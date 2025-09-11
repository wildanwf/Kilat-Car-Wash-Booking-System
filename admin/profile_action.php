<?php

include('../class/Appointment.php');

$object = new Appointment;

if($_POST["action"] == 'employee_profile')
{
	sleep(2);

	$error = '';

	$success = '';

	$employee_profile_image = '';

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
				// ':employee_degree'				=>	$object->clean_input($_POST["employee_degree"]),
				':employee_expert_in'				=>	$object->clean_input($_POST["employee_expert_in"])
			);

			$object->query = "
			UPDATE employee_table  
			SET employee_email_address = :employee_email_address, 
			employee_password = :employee_password, 
			employee_name = :employee_name, 
			employee_profile_image = :employee_profile_image, 
			employee_phone_no = :employee_phone_no, 
			employee_address = :employee_address, 
			employee_date_of_birth = :employee_date_of_birth, 
			
			employee_expert_in = :employee_expert_in 
			WHERE employee_id = '".$_POST['hidden_id']."'
			";
			$object->execute($data);

			$success = '<div class="alert alert-success">Employee Updated</div>';
		}			
	}

	$output = array(
		'error'					=>	$error,
		'success'				=>	$success,
		'employee_email_address'	=>	$_POST["employee_email_address"],
		'employee_password'		=>	$_POST["employee_password"],
		'employee_name'			=>	$_POST["employee_name"],
		'employee_profile_image'	=>	$employee_profile_image,
		'employee_phone_no'		=>	$_POST["employee_phone_no"],
		'employee_address'		=>	$_POST["employee_address"],
		'employee_date_of_birth'	=>	$_POST["employee_date_of_birth"],
		'employee_expert_in'		=>	$_POST["employee_expert_in"],
	);

	echo json_encode($output);
}

if($_POST["action"] == 'admin_profile')
{
	sleep(2);

	$error = '';

	$success = '';

	$branch_logo = $_POST['hidden_branch_logo'];

	if($_FILES['branch_logo']['name'] != '')
	{
		$allowed_file_format = array("jpg", "png");

	    $file_extension = pathinfo($_FILES["branch_logo"]["name"], PATHINFO_EXTENSION);

	    if(!in_array($file_extension, $allowed_file_format))
		{
		    $error = "<div class='alert alert-danger'>Upload valiid file. jpg, png</div>";
		}
		else if (($_FILES["branch_logo"]["size"] > 2000000))
		{
		   $error = "<div class='alert alert-danger'>File size exceeds 2MB</div>";
	    }
		else
		{
		    $new_name = rand() . '.' . $file_extension;

			$destination = '../images/' . $new_name;

			move_uploaded_file($_FILES['branch_logo']['tmp_name'], $destination);

			$branch_logo = $destination;
		}
	}

	if($error == '')
	{
		$data = array(
			':admin_email_address'			=>	$object->clean_input($_POST["admin_email_address"]),
			':admin_password'				=>	$_POST["admin_password"],
			':admin_name'					=>	$object->clean_input($_POST["admin_name"]),
			':branch_name'				=>	$object->clean_input($_POST["branch_name"]),
			':branch_address'				=>	$object->clean_input($_POST["branch_address"]),
			':branch_contact_no'			=>	$object->clean_input($_POST["branch_contact_no"]),
			':branch_logo'				=>	$branch_logo
		);

		$object->query = "
		UPDATE admin_table  
		SET admin_email_address = :admin_email_address, 
		admin_password = :admin_password, 
		admin_name = :admin_name, 
		branch_name = :branch_name, 
		branch_address = :branch_address, 
		branch_contact_no = :branch_contact_no, 
		branch_logo = :branch_logo 
		WHERE admin_id = '".$_SESSION["admin_id"]."'
		";
		$object->execute($data);

		$success = '<div class="alert alert-success">Admin Data Updated</div>';

		$output = array(
			'error'					=>	$error,
			'success'				=>	$success,
			'admin_email_address'	=>	$_POST["admin_email_address"],
			'admin_password'		=>	$_POST["admin_password"],
			'admin_name'			=>	$_POST["admin_name"], 
			'branch_name'			=>	$_POST["branch_name"],
			'branch_address'		=>	$_POST["branch_address"],
			'branch_contact_no'	=>	$_POST["branch_contact_no"],
			'branch_logo'			=>	$branch_logo
		);

		echo json_encode($output);
	}
	else
	{
		$output = array(
			'error'					=>	$error,
			'success'				=>	$success
		);
		echo json_encode($output);
	}
}

?>
