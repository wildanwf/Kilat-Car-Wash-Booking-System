<?php

//service_action.php

include('../class/Appointment.php');

$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('service_name');

		$output = array();

		$main_query = "
		SELECT * FROM service_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE service_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR service_purpose LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR service_price LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY service_id DESC ';
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
			$sub_array[] = '<img src="'.$row["service_profile_image"].'" class="img-thumbnail" width="75" />';
			$sub_array[] = $row["service_name"];
			$sub_array[] = $row["service_purpose"];
			$sub_array[] = $row["service_price"];
			
			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["service_id"].'"><i class="fas fa-eye"></i></button>
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["service_id"].'"><i class="fas fa-edit"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["service_id"].'"><i class="fas fa-times"></i></button>
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

			$service_profile_image = '';
			if($_FILES['service_profile_image']['name'] != '')
			{
				$allowed_file_format = array("jpg", "png");

	    		$file_extension = pathinfo($_FILES["service_profile_image"]["name"], PATHINFO_EXTENSION);

	    		if(!in_array($file_extension, $allowed_file_format))
			    {
			        $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
			    }
			    else if (($_FILES["service_profile_image"]["size"] > 2000000))
			    {
			       $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
			    }
			    else
			    {
			    	$new_name = rand() . '.' . $file_extension;

					$destination = '../images/' . $new_name;

					move_uploaded_file($_FILES['service_profile_image']['tmp_name'], $destination);

					$service_profile_image = $destination;
			    }
			}
			else
			{
				$character = $_POST["service_name"][0];
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
			    $service_profile_image = $path;
			}

			if($error == '')
			{
				$data = array(
					':service_name'		        =>	$object->clean_input($_POST["service_name"]),
					':service_purpose'			=>	$_POST["service_purpose"],
					':service_price'			=>	$object->clean_input($_POST["service_price"]),
					':service_profile_image'    =>	$service_profile_image,
					':service_added_on'			=>	$object->now
				);

				$object->query = "
				INSERT INTO service_table 
				(service_name, service_purpose, service_price, service_profile_image, service_added_on) 
				VALUES (:service_name, :service_purpose, :service_price, :service_profile_image,  :service_added_on)
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Service Added</div>';
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
		SELECT * FROM service_table 
		WHERE service_id = '".$_POST["service_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['service_name'] = $row['service_name'];
			$data['service_purpose'] = $row['service_purpose'];
			$data['service_price'] = $row['service_price'];
			$data['service_profile_image'] = $row['service_profile_image'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

			$service_profile_image = $_POST["hidden_service_profile_image"];

			if($_FILES['service_profile_image']['name'] != '')
			{
				$allowed_file_format = array("jpg", "png");

	    		$file_extension = pathinfo($_FILES["service_profile_image"]["name"], PATHINFO_EXTENSION);

	    		if(!in_array($file_extension, $allowed_file_format))
			    {
			        $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
			    }
			    else if (($_FILES["service_profile_image"]["size"] > 2000000))
			    {
			       $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
			    }
			    else
			    {
			    	$new_name = rand() . '.' . $file_extension;

					$destination = '../images/' . $new_name;

					move_uploaded_file($_FILES['service_profile_image']['tmp_name'], $destination);

					$service_profile_image = $destination;
			    }
			}

			if($error == '')
			{
				$data = array(
					':service_name'		        =>	$object->clean_input($_POST["service_name"]),
					':service_purpose'			=>	$_POST["service_purpose"],
					':service_price'			=>	$object->clean_input($_POST["service_price"]),
					':service_profile_image'    =>	$service_profile_image,
				);

				$object->query = "
				UPDATE service_table  
				SET service_name = :service_name, 
				service_purpose = :service_purpose, 
				service_price = :service_price, 
				service_profile_image = :service_profile_image, 
				WHERE service_id = '".$_POST['hidden_id']."'
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Service Updated</div>';
			}			
		

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM service_table 
		WHERE service_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Service Deleted</div>';
	}
}

?>