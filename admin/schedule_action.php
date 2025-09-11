<?php

//employee_schedule_action.php

include('../class/Appointment.php');

$object = new Appointment;

if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$output = array();

		if($_SESSION['type'] == 'Admin')
		{
			$order_column = array('employee_table.employee_name', 'schedule_table.employee_schedule_date', 'schedule_table.employee_schedule_day', 'schedule_table.employee_schedule_start_time', 'schedule_table.employee_schedule_end_time');
			$main_query = "
			SELECT * FROM schedule_table 
			INNER JOIN employee_table 
			ON employee_table.employee_id = schedule_table.employee_id 
			";

			$search_query = '';

			if(isset($_POST["search"]["value"]))
			{
				$search_query .= 'WHERE employee_table.employee_name LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR schedule_table.employee_schedule_date LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR schedule_table.employee_schedule_day LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR schedule_table.employee_schedule_start_time LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR schedule_table.employee_schedule_end_time LIKE "%'.$_POST["search"]["value"].'%" ';
			}
		}
		else
		{
			$order_column = array('employee_schedule_date', 'employee_schedule_day', 'employee_schedule_start_time', 'employee_schedule_end_time');
			$main_query = "
			SELECT * FROM schedule_table 
			";

			$search_query = '
			WHERE employee_id = "'.$_SESSION["admin_id"].'" AND 
			';

			if(isset($_POST["search"]["value"]))
			{
				$search_query .= '(employee_schedule_date LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR employee_schedule_day LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR employee_schedule_start_time LIKE "%'.$_POST["search"]["value"].'%" ';
				$search_query .= 'OR employee_schedule_end_time LIKE "%'.$_POST["search"]["value"].'%" ';
			}
		}

		if(isset($_POST["order"]))
		{
			$order_query = 'ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = 'ORDER BY schedule_table.schedule_id DESC ';
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
			
			$sub_array[] = $row["employee_schedule_date"];

			$sub_array[] = $row["employee_schedule_day"];

			$sub_array[] = $row["employee_schedule_start_time"];

			$sub_array[] = $row["employee_schedule_end_time"];

            if($_SESSION['type'] == 'Admin')
			{
				$sub_array[] = html_entity_decode($row["employee_name"]);
			}

			$status = '';
			if($_SESSION['type'] == 'Admin')
			{
				if($row["employee_status"] == 'Active')
				{
					$status = '<span name="nostatus_button" class="badge badge-primary" data-toggle="tooltip" data-placement="top" title="Sync with Employee account status" data-id="'.$row["schedule_id"].'" data-status="'.$row["employee_schedule_status"].'">Active</span>';
				}
				else
				{
					$status = '<span name="nostatus_button" class="badge badge-danger" data-toggle="tooltip" data-placement="top" title="Sync with Employee account status" data-id="'.$row["schedule_id"].'" data-status="'.$row["employee_schedule_status"].'">Inactive</span>';
				}
				
			}

			$sub_array[] = $status;

			$sub_array[] = '
			<div align="center">
			<button type="button" name="edit_button" class="btn btn-warning btn-circle btn-sm edit_button" data-toggle="tooltip" data-placement="top" title="Edit schedule" data-id="'.$row["schedule_id"].'"><i class="fas fa-edit"></i></button>
			&nbsp;
			<button type="button" name="delete_button" class="btn btn-danger btn-circle btn-sm delete_button" data-toggle="tooltip" data-placement="top" title="Delete schedule" data-id="'.$row["schedule_id"].'"><i class="fas fa-times"></i></button>
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

		$employee_id = '';

		if($_SESSION['type'] == 'Admin')
		{
			$employee_id = $_POST["employee_id"];
		}

		if($_SESSION['type'] == 'employee')
		{
			$employee_id = $_SESSION['admin_id'];
		}

		$data = array(
			':employee_id'					=>	$employee_id,
			':employee_schedule_date'			=>	$_POST["employee_schedule_date"],
			':employee_schedule_day'			=>	date('l', strtotime($_POST["employee_schedule_date"])),
			':employee_schedule_start_time'	=>	$_POST["employee_schedule_start_time"],
			':employee_schedule_end_time'		=>	$_POST["employee_schedule_end_time"]
		);

		$object->query = "
		INSERT INTO schedule_table 
		(employee_id, employee_schedule_date, employee_schedule_day, employee_schedule_start_time, employee_schedule_end_time) 
		VALUES (:employee_id, :employee_schedule_date, :employee_schedule_day, :employee_schedule_start_time, :employee_schedule_end_time)
		";

		$object->execute($data);

		$success = '<div class="alert alert-success">Employee Schedule Added Successfully</div>';

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'fetch_single')
	{
		$object->query = "
		SELECT * FROM schedule_table 
		WHERE schedule_id = '".$_POST["schedule_id"]."'
		";

		$result = $object->get_result();

		$data = array();

		foreach($result as $row)
		{
			$data['employee_id'] = $row['employee_id'];
			$data['employee_schedule_date'] = $row['employee_schedule_date'];
			$data['employee_schedule_start_time'] = $row['employee_schedule_start_time'];
			$data['employee_schedule_end_time'] = $row['employee_schedule_end_time'];
		}

		echo json_encode($data);
	}

	if($_POST["action"] == 'Edit')
	{
		$error = '';

		$success = '';

		$employee_id = '';

		if($_SESSION['type'] == 'Admin')
		{
			$employee_id = $_POST["employee_id"];
		}

		if($_SESSION['type'] == 'employee')
		{
			$employee_id = $_SESSION['admin_id'];
		}

		$data = array(
			':employee_id'					=>	$employee_id,
			':employee_schedule_date'			=>	$_POST["employee_schedule_date"],
			':employee_schedule_start_time'	=>	$_POST["employee_schedule_start_time"],
			':employee_schedule_end_time'		=>	$_POST["employee_schedule_end_time"]
		);

		$object->query = "
		UPDATE schedule_table 
		SET employee_id = :employee_id, 
		employee_schedule_date = :employee_schedule_date, 
		employee_schedule_start_time = :employee_schedule_start_time, 
		employee_schedule_end_time = :employee_schedule_end_time 
		WHERE schedule_id = '".$_POST['hidden_id']."'
		";

		$object->execute($data);

		$success = '<div class="alert alert-success">Employee Schedule Updated Successfully Updated</div>';

		$output = array(
			'error'		=>	$error,
			'success'	=>	$success
		);

		echo json_encode($output);

	}

	if($_POST["action"] == 'change_status')
	{
		$data = array(
			':employee_schedule_status'		=>	$_POST['next_status']
		);

		$object->query = "
		UPDATE schedule_table 
		SET employee_schedule_status = :employee_schedule_status 
		WHERE schedule_id = '".$_POST["id"]."'
		";

		$object->execute($data);

		echo '<div class="alert alert-success">Employee Schedule Status change to '.$_POST['next_status'].'</div>';
	}

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM schedule_table 
		WHERE schedule_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">Employee Schedule has been Deleted</div>';
	}
}

?>