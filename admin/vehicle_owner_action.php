<?php

//customer_action.php

include('../class/Appointment.php');

$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('customer_name');

		$output = array();

		$main_query = "
		SELECT * FROM customer_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE customer_email_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR customer_first_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR customer_last_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR customer_phone_no LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR customer_address LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR customer_gender LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR customer_id LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY customer_id DESC ';
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
			$sub_array[] = $row["customer_id"];
			$sub_array[] = $row["customer_email_address"];
			$sub_array[] = $row["customer_first_name"];
			$sub_array[] = $row["customer_last_name"];
			$sub_array[] = $row["customer_phone_no"];
			
			$sub_array[] = '
			<div align="center">
			<button type="button" name="view_button" class="btn btn-info btn-circle btn-sm view_button" data-toggle="tooltip" data-placement="top" title="View account"  data-id="'.$row["customer_id"].'"><i class="fas fa-eye"></i></button>
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-toggle="tooltip" data-placement="top" title="Edit account"  data-id="'.$row["customer_id"].'"><i class="fas fa-edit"></i></button>
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-toggle="tooltip" data-placement="top" title="Delete account"  data-id="'.$row["customer_id"].'"><i class="fas fa-times"></i></button>
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
			':customer_email_address'	=>	$_POST["customer_email_address"]
		);

		$object->query = "
		SELECT * FROM customer_table 
		WHERE customer_email_address = :customer_email_address
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
		}
		else
		{

			if($error == '')
			{
				$data = array(
					':customer_email_address'		=>	$object->clean_input($_POST["customer_email_address"]),
					//':customer_password'				=>	$_POST["customer_password"],
					':customer_first_name'			=>	$object->clean_input($_POST["customer_first_name"]),
					':customer_last_name'			=>	$object->clean_input($_POST["customer_last_name"]),
					':customer_phone_no'				=>	$object->clean_input($_POST["customer_phone_no"]),
					':customer_address'				=>	$object->clean_input($_POST["customer_address"]),
					':customer_gender'				=>	$object->clean_input($_POST["customer_gender"]),
					':customer_added_on'				=>	$object->now
				);

				$object->query = "
				INSERT INTO customer_table 
				(customer_email_address, customer_password, customer_first_name, customer_last_name, customer_phone_no, customer_address, customer_gender, customer_added_on) 
				VALUES (:customer_email_address, :customer_password, :customer_first_name, :customer_last_name, :customer_phone_no, :customer_address, :customer_gender, :customer_added_on)
				";

				$object->execute($data);

				$success = '<div class="alert alert-success">Vehicle Owner Account Added</div>';
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
		SELECT * FROM customer_table 
		WHERE customer_id = '".$_POST["customer_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['customer_email_address'] = $row['customer_email_address'];
			//$data['customer_password'] = $row['customer_password'];
			$data['customer_first_name'] = $row['customer_first_name'];
			$data['customer_last_name'] = $row['customer_last_name'];
			$data['customer_phone_no'] = $row['customer_phone_no'];
			$data['customer_address'] = $row['customer_address'];
			$data['customer_gender'] = $row['customer_gender'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':customer_email_address'	=>	$_POST["customer_email_address"],
			':customer_id'			=>	$_POST['hidden_id']
		);

		$object->query = "
		SELECT * FROM customer_table 
		WHERE customer_email_address = :customer_email_address 
		AND customer_id != :customer_id
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Email Address Already Exists</div>';
		}
		else
		{

			if($error == '')
			{
				$data = array(
					':customer_email_address'		=>	$object->clean_input($_POST["customer_email_address"]),
					//':customer_password'				=>	$_POST["customer_password"],
					':customer_first_name'			=>	$object->clean_input($_POST["customer_first_name"]),
					':customer_last_name'			=>	$object->clean_input($_POST["customer_last_name"]),
					':customer_phone_no'				=>	$object->clean_input($_POST["customer_phone_no"]),
					':customer_address'				=>	$object->clean_input($_POST["customer_address"]),
					':customer_gender'				=>	$object->clean_input($_POST["customer_gender"]),
				);

				$object->query = "
				UPDATE customer_table  
				SET customer_email_address = :customer_email_address, 
	
				customer_first_name = :customer_first_name, 
				customer_last_name = :customer_last_name, 
				customer_phone_no = :customer_phone_no, 
				customer_address = :customer_address, 
				customer_gender = :customer_gender
				WHERE customer_id = '".$_POST['hidden_id']."'
				";

				

				$object->execute($data);

				$success = '<div class="alert alert-success">Vehicle Owner Account Updated</div>';
			}			
		}

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	// if($_POST["action"] == 'change_status')
	// {
	// 	$data = array(
	// 		':customer_status'		=>	$_POST['next_status']
	// 	);

	// 	$object->query = "
	// 	UPDATE customer_table 
	// 	SET customer_status = :customer_status 
	// 	WHERE customer_id = '".$_POST["id"]."'
	// 	";

	// 	$object->execute($data);

	// 	echo '<div class="alert alert-success">Class Status change to '.$_POST['next_status'].'</div>';
	// }

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM customer_table 
		WHERE customer_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Vehicle Owner Account Deleted</div>';
	}
}

?>