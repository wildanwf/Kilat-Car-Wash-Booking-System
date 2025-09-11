<?php

//product_action.php

include('../class/Appointment.php');
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// header('Content-Type: application/json'); // Ensure correct response type


$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('product_direction', 'product_name', 'product_price', 'product_purpose', 'product_prescription');

		$output = array();

		$main_query = "
		SELECT * FROM product_table ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'WHERE product_direction LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR product_name LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR product_price LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR product_purpose LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR product_prescription LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY product_id DESC ';
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
			$sub_array[] = html_entity_decode($row["product_name"]);
			$sub_array[] = html_entity_decode($row["product_purpose"]);
			$sub_array[] = html_entity_decode($row["product_prescription"]);
			$sub_array[] = $row["product_direction"];
			$sub_array[] = $object->cur . $row["product_price"];
			$sub_array[] = '<img src="'.$row["product_image"].'" class="img-thumbnail" width="75" />';
			
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["product_id"].'"><i class="fas fa-edit"></i></button>
			<br />
			<br />
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="'.$row["product_id"].'"><i class="fas fa-times"></i></button>
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
			':product_direction'	=>	$_POST["product_direction"],
			':product_name'		=>	$_POST["product_name"]
		);

		$object->query = "
		SELECT * FROM product_table 
		WHERE product_direction = :product_direction 
		AND product_name = :product_name
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Product Already Exists</div>';
		}
		else
		{
			$data = array(
				':product_direction'	=>	$_POST["product_direction"],
				':product_name'		=>	$object->clean_input($_POST["product_name"]),
				':product_purpose'		=>	$object->clean_input($_POST["product_purpose"]),
				':product_prescription'		=>	$object->clean_input($_POST["product_prescription"]),
				':product_price'	=>	$object->clean_input($_POST["product_price"]),
			);

			$object->query = "
			INSERT INTO product_table 
			(product_direction, product_name, product_purpose, product_prescription, product_price) 
			VALUES (:product_direction, :product_name, :product_purpose, :product_prescription, :product_price)
			";

			$object->execute($data);

			$success = '<div class="alert alert-success">Product Added</div>';
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
		SELECT * FROM product_table 
		WHERE product_id = '".$_POST["product_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['product_direction'] = $row['product_direction'];
			$data['product_name'] = $row['product_name'];
			$data['product_purpose'] = $row['product_purpose'];
			$data['product_prescription'] = $row['product_prescription'];
			$data['product_price'] = $row['product_price'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$data = array(
			':product_direction'	=>	$_POST["product_direction"],
			':product_name'		=>	$_POST["product_name"],
			':product_id'		=>	$_POST['hidden_id'],
			':product_image'			=>	$product_image,
		);

		$object->query = "
		SELECT * FROM product_table 
		WHERE product_direction = :product_direction 
		AND product_name = :product_name
		AND product_id != :product_id
		";

		$object->execute($data);

		if($object->row_count() > 0)
		{
			$error = '<div class="alert alert-danger">Product Already Exists</div>';
		}
		else
		{
			$data = array(
				':product_direction'	=>	$_POST["product_direction"],
				':product_name'		=>	$object->clean_input($_POST["product_name"]),
				':product_purpose'		=>	$object->clean_input($_POST["product_purpose"]),
				':product_prescription'		=>	$object->clean_input($_POST["product_prescription"]),
				':product_price'	=>	$object->clean_input($_POST["product_price"])
			);

			$object->query = "
			UPDATE product_table 
			SET product_direction = :product_direction, 
			product_name = :product_name, 
			product_purpose = :product_purpose, 
			product_prescription = :product_prescription, 
			product_price = :product_price
			WHERE product_id = '".$_POST['hidden_id']."'
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

	// if($_POST["action"] == 'change_status')
	// {
	// 	$data = array(
	// 		':product_status'		=>	$_POST['next_status']
	// 	);

	// 	$object->query = "
	// 	UPDATE product_table 
	// 	SET product_status = :product_status 
	// 	WHERE product_id = '".$_POST["id"]."'
	// 	";

	// 	$object->execute($data);

	// 	echo '<div class="alert alert-success">Product Status change to '.$_POST['next_status'].'</div>';
	// }

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM product_table 
		WHERE product_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Medicine Deleted</div>';
	}
}

?>