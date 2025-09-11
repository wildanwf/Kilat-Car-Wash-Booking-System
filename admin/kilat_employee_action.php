<?php

//employee_action.php

include('../class/Appointment.php');

$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('employee_name', 'employee_status');

		$output = array();

		$main_query = "
		SELECT * FROM employee_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE employee_email_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR employee_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR employee_phone_no LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR employee_date_of_birth LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR employee_status LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY employee_id DESC ';
		}

		$limit_query = '';

		if($_POST["length"] != -1)
		{
			$limit_query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}

		$object->query = $main_query . $search_query . $order_query;

		$object->execute();

		$filtered_rows = $object->row_count();

		$object->query .= $limit_query;

		$result = $object->get_result();

		$object->query = $main_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();
			$sub_array[] = '<img src="'.$row["employee_profile_image"].'" class="img-thumbnail" width="75" />';
			$sub_array[] = $row["employee_email_address"];
			$sub_array[] = $row["employee_name"];
			$sub_array[] = $row["employee_phone_no"];
			$status = '';
			if($row["employee_status"] == 'Active')
			{
				$status = '<button type="button" name="status_button" class="btn btn-primary btn-sm status_button" data-toggle="tooltip" data-placement="top" title="Click to Deactivate account" data-id="'.$row["employee_id"].'" data-status="'.$row["employee_status"].'">Active</button>';
			}
			else
			{
				$status = '<button type="button" name="status_button" class="btn btn-danger btn-sm status_button" data-toggle="tooltip" data-placement="top" title="Click to Activate account"data-id="'.$row["employee_id"].'" data-status="'.$row["employee_status"].'">Inactive</button>';
			}
			$sub_array[] = $status;
			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-toggle="tooltip" data-placement="top" title="View account" data-id="'.$row["employee_id"].'" ><i class="fas fa-eye"></i></button>
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-toggle="tooltip" data-placement="top" title="Edit account" data-id="'.$row["employee_id"].'"><i class="fas fa-edit"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-toggle="tooltip" data-placement="top" title="Delete account" data-id="'.$row["employee_id"].'"><i class="fas fa-times"></i></button>
			</div>
			';
			$data[] = $sub_array;
		}

		$output = array(
			"draw"    			=> 	intval($_POST["draw"]),
			"recordsTotal"  	=>  $total_rows,
			"recordsFiltered" 	=> 	$filtered_rows,
			"data"    			=> 	$data
		);
			
		echo json_encode($output);

	}

	if($_POST["action"] == 'Add')
	{
		$error = '';

		$success = '';

		$data = array(
			':employee_email_address'	=>	$_POST["employee_email_address"]
		);

		$object->query = "
		SELECT * FROM employee_table 
		WHERE employee_email_address = :employee_email_address
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
		}
		else
		{
			$employee_profile_image = '';
			if($_FILES['employee_profile_image']['name'] != '')
			{
				$allowed_file_format = array("jpg", "png");

	    		$file_extension = pathinfo($_FILES["employee_profile_image"]["name"], PATHINFO_EXTENSION);

	    		if(!in_array($file_extension, $allowed_file_format))
			    {
			        $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
			    }
			    else if (($_FILES["employee_profile_image"]["size"] > 2000000))
			    {
			       $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
			    }
			    else
			    {
			    	$new_name = rand() . '.' . $file_extension;

					$destination = '../images/' . $new_name;

					move_uploaded_file($_FILES['employee_profile_image']['tmp_name'], $destination);

					$employee_profile_image = $destination;
			    }
			}
			else
			{
				$character = $_POST["employee_name"][0];
				$path = "../images/". time() . ".png";
				$image = imagecreate(200, 200);
				$red = rand(0, 255);
				$green = rand(0, 255);
				$blue = rand(0, 255);
			    imagecolorallocate($image, 230, 230, 230);  
			    $textcolor = imagecolorallocate($image, $red, $green, $blue);
			    imagettftext($image, 100, 0, 55, 150, $textcolor, '../font/arial.ttf', $character);
			    imagepng($image, $path);
			    imagedestroy($image);
			    $employee_profile_image = $path;
			}

			if($error == '')
			{
				$data = array(
					':employee_email_address'			=>	$object->clean_input($_POST["employee_email_address"]),
					':employee_password'				=>	$_POST["employee_password"],
					':employee_name'					=>	$object->clean_input($_POST["employee_name"]),
					':employee_profile_image'			=>	$employee_profile_image,
					':employee_phone_no'				=>	$object->clean_input($_POST["employee_phone_no"]),
					':employee_address'				=>	$object->clean_input($_POST["employee_address"]),
					':employee_date_of_birth'			=>	$object->clean_input($_POST["employee_date_of_birth"]),
					':employee_status'				=>	'Active',
					':employee_added_on'				=>	$object->now
				);

				$object->query = "
				INSERT INTO employee_table 
				(employee_email_address,employee_password, employee_name, employee_profile_image, employee_phone_no, employee_address, employee_date_of_birth, employee_status, employee_added_on) 
				VALUES (:employee_email_address,SHA1(:employee_password), :employee_name, :employee_profile_image, :employee_phone_no, :employee_address, :employee_date_of_birth, :employee_status, :employee_added_on)
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Employee Account Added</div>';
			}
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM employee_table 
		WHERE employee_id = '".$_POST["employee_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['employee_email_address'] = $row['employee_email_address'];
			$data['employee_password'] = $row['employee_password'];
			$data['employee_name'] = $row['employee_name'];
			$data['employee_name'] = $row['employee_name'];
			$data['employee_profile_image'] = $row['employee_profile_image'];
			$data['employee_phone_no'] = $row['employee_phone_no'];
			$data['employee_address'] = $row['employee_address'];
			$data['employee_date_of_birth'] = $row['employee_date_of_birth'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':employee_email_address'	=>	$_POST["employee_email_address"],
			':employee_id'			=>	$_POST['hidden_id']
		);

		$object->query = "
		SELECT * FROM employee_table 
		WHERE employee_email_address = :employee_email_address 
		AND employee_id != :employee_id
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
		}
		else
		{
			$employee_profile_image = $_POST["hidden_employee_profile_image"];

			if($_FILES['employee_profile_image']['name'] != '')
			{
				$allowed_file_format = array("jpg", "png");

	    		$file_extension = pathinfo($_FILES["employee_profile_image"]["name"], PATHINFO_EXTENSION);

	    		if(!in_array($file_extension, $allowed_file_format))
			    {
			        $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
			    }
			    else if (($_FILES["employee_profile_image"]["size"] > 2000000))
			    {
			       $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
			    }
			    else
			    {
			    	$new_name = rand() . '.' . $file_extension;

					$destination = '../images/' . $new_name;

					move_uploaded_file($_FILES['employee_profile_image']['tmp_name'], $destination);

					$employee_profile_image = $destination;
			    }
			}

			if($error == '')
			{
				$data = array(
					':employee_email_address'			=>	$object->clean_input($_POST["employee_email_address"]),
					':employee_password'				=>	$_POST["employee_password"],
					':employee_name'					=>	$object->clean_input($_POST["employee_name"]),
					':employee_profile_image'			=>	$employee_profile_image,
					':employee_phone_no'				=>	$object->clean_input($_POST["employee_phone_no"]),
					':employee_address'				=>	$object->clean_input($_POST["employee_address"]),
					':employee_date_of_birth'			=>	$object->clean_input($_POST["employee_date_of_birth"]),
				);

				$object->query = "
				UPDATE employee_table  
				SET employee_email_address = :employee_email_address, 
				employee_password = :employee_password, 
				employee_name = :employee_name, 
				employee_profile_image = :employee_profile_image, 
				employee_phone_no = :employee_phone_no, 
				employee_address = :employee_address, 
				employee_date_of_birth = :employee_date_of_birth
				WHERE employee_id = '".$_POST['hidden_id']."'
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Employee Account Updated</div>';
			}			
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		$data = array(
			':employee_status'		=>	$_POST['next_status']
		);

		$object->query = "
		UPDATE employee_table 
		SET employee_status = :employee_status 
		WHERE employee_id = '".$_POST["id"]."'
		";

		$object->execute($data);

		echo '<div class="alert alert-success">Employee Account change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM employee_table 
		WHERE employee_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Employee Account Deleted</div>';
	}
}

?>