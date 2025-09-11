<?php

//medicine_action.php

include('../class/Appointment.php');

$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('med_name');

		$output = array();

		$main_query = "
		SELECT * FROM medicine_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE med_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR med_purpose LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR med_price LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR med_price_per LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR med_price_per_unit LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY med_id DESC ';
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
			$sub_array[] = '<img src="'.$row["med_profile_image"].'" class="img-thumbnail" width="75" />';
			$sub_array[] = $row["med_name"];
			$sub_array[] = $row["med_purpose"];
			$sub_array[] = $row["med_price"];
			$sub_array[] = $row["med_price_per"];
            $sub_array[] = $row["med_price_per_unit"];
			
			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-id="'.$row["med_id"].'"><i class="fas fa-eye"></i></button>
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["med_id"].'"><i class="fas fa-edit"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["med_id"].'"><i class="fas fa-times"></i></button>
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

		
			$med_profile_image = '';
			if($_FILES['med_profile_image']['name'] != '')
			{
				$allowed_file_format = array("jpg", "png");

	    		$file_extension = pathinfo($_FILES["med_profile_image"]["name"], PATHINFO_EXTENSION);

	    		if(!in_array($file_extension, $allowed_file_format))
			    {
			        $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
			    }
			    else if (($_FILES["med_profile_image"]["size"] > 2000000))
			    {
			       $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
			    }
			    else
			    {
			    	$new_name = rand() . '.' . $file_extension;

					$destination = '../images/' . $new_name;

					move_uploaded_file($_FILES['med_profile_image']['tmp_name'], $destination);

					$med_profile_image = $destination;
			    }
			}
			else
			{
				$character = $_POST["med_name"][0];
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
			    $med_profile_image = $path;
			}

			if($error == '')
			{
				$data = array(
					':med_name'		        =>	$object->clean_input($_POST["med_name"]),
					':med_purpose'			=>	$_POST["med_purpose"],
					':med_price'		    =>	$object->clean_input($_POST["med_price"]),
					':med_price_per'		=>	$object->clean_input($_POST["med_price_per"]),
					':med_profile_image'	=>	$med_profile_image,
					':med_price_per_unit'   =>	$object->clean_input($_POST["med_price_per_unit"]),
					':med_added_on'			=>	$object->now
				);

				$object->query = "
				INSERT INTO medicine_table 
				(med_name, med_purpose, med_price, med_price_per, med_profile_image, med_price_per_unit, med_added_on) 
				VALUES (:med_name, :med_purpose, :med_price, :med_price_per, :med_profile_image, :med_price_per_unit, :med_added_on)
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Medicine Added</div>';
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
		SELECT * FROM medicine_table 
		WHERE med_id = '".$_POST["med_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['med_name'] = $row['med_name'];
			$data['med_purpose'] = $row['med_purpose'];
			$data['med_price'] = $row['med_price'];
			$data['med_price_per'] = $row['med_price_per'];
			$data['med_profile_image'] = $row['med_profile_image'];
			$data['med_price_per_unit'] = $row['med_price_per_unit'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';
		
			// $med_profile_image = $_POST["hidden_med_profile_image"];

			// if($_FILES['med_profile_image']['name'] != '')
			// {
			// 	$allowed_file_format = array("jpg", "png");

	    	// 	$file_extension = pathinfo($_FILES["med_profile_image"]["name"], PATHINFO_EXTENSION);

	    	// 	if(!in_array($file_extension, $allowed_file_format))
			//     {
			//         $error = "<div class='alert alert-danger'>Upload valid file. jpg, png</div>";
			//     }
			//     else if (($_FILES["med_profile_image"]["size"] > 2000000))
			//     {
			//        $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
			//     }
			//     else
			//     {
			//     	$new_name = rand() . '.' . $file_extension;

			// 		$destination = '../images/' . $new_name;

			// 		move_uploaded_file($_FILES['med_profile_image']['tmp_name'], $destination);

			// 		$med_profile_image = $destination;
			//     }
			// }

			if($error == '')
			{
				$data = array(
					':med_name'		        =>	$object->clean_input($_POST["med_name"]),
					':med_purpose'			=>	$object->clean_input($_POST["med_purpose"]),
					':med_price'			=>	$object->clean_input($_POST["med_price"]),
                    ':med_price_per'		=>	$object->clean_input($_POST["med_price_per"]),
					':med_profile_image'    =>	$med_profile_image,
					':med_price_per_unit'	=>	$object->clean_input($_POST["med_price_per_unit"]),
				);

				$object->query = "
				UPDATE medicine_table  
				SET med_name = :med_name, 
				med_purpose = :med_purpose, 
				med_price = :med_price, 
                med_price_per = :med_price_per, 
				med_profile_image = :med_profile_image, 
				med_price_per_unit = :med_price_per_unit, 
				WHERE med_id = '".$_POST['hidden_id']."'
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Medicine Updated</div>';
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
		DELETE FROM medicine_table 
		WHERE med_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Medicine Deleted</div>';
	}
}

?>