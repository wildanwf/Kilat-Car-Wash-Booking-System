<?php

//product_action.php

include('../class/Appointment.php');

$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$order_column = array('employee_id', 'appointment_number', 'employee_name');

		$output = array();

		$main_query = "
        SELECT * FROM appointment_table  
        INNER JOIN employee_table 
        ON employee_table.employee_id = appointment_table.employee_id 
        INNER JOIN employee_schedule_table 
        ON employee_schedule_table.employee_schedule_id = appointment_table.employee_schedule_id 
        INNER JOIN customer_table 
        ON customer_table.customer_id = appointment_table.customer_id 
        ";

		$search_query = '';

		if(isset($_POST["search"]["value"]))
		{
			$search_query .= 'OR appointment_number LIKE "%'.$_POST["search"]["value"].'%" ';
			$search_query .= 'OR employee_name LIKE "%'.$_POST["search"]["value"].'%" ';
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY appointment_table.appointment_id DESC ';
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
			$sub_array[] = html_entity_decode($row["appointment_number"]);
			$sub_array[] = $object->cur . $row["employee_name"];
			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="'.$row["appointment_id"].'"><i class="fas fa-edit"></i></button>
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

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM appointment_table 
		WHERE appointment_id = '".$_POST["appointment_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['employee_id'] = $row['employee_id'];
			$data['appointment_number'] = $row['appointment_number'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

			$data = array(
				':employee_id'	=>	$_POST["employee_id"]
			);

			$object->query = "
			UPDATE appointment_table 
			SET employee_id = :employee_id
			WHERE appointment_id = '".$_POST['hidden_id']."'
			";

			$object->execute($data);

			$success = '<div class="alert alert-success">Product Updated</div>';
		

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}
}

?>