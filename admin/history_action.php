<?php

//appointment_action.php
session_start();                      // ← make sure you start the session

include('../class/Appointment.php');

header('Content-Type: application/json; charset=utf-8');
// optionally turn off direct error output:
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);


$object = new Appointment;


if(isset($_POST["action"]))
{
	if($_POST["action"] == 'fetch')
	{
		$customer_id=$_POST['id'];
		$output = array();
	
		if($_SESSION['type'] == 'Admin')
		{
			$order_column = array('appointment_table.appointment_number', 'customer_table.customer_first_name', 'employee_schedule_table.employee_schedule_date', 'appointment_table.appointment_time', 'employee_schedule_table.employee_schedule_day', 'appointment_table.status', 'vehicle_table.vehicle_name', 'employee_table.employee_name');

			$main_query = "
			SELECT * FROM appointment_table 
			INNER JOIN employee_schedule_table 
			ON employee_schedule_table.employee_schedule_id = appointment_table.employee_schedule_id 
			INNER JOIN customer_table 
			ON customer_table.customer_id = appointment_table.customer_id 
			INNER JOIN vehicle_table 
			ON vehicle_table.vehicle_id = appointment_table.vehicle_id 
			INNER JOIN employee_table 
			ON employee_table.employee_id = appointment_table.employee_id 
			";

			$search_query = ' WHERE appointment_table.customer_id = '.$customer_id; 
		}
		else
		{
			
			$order_column = array('appointment_table.appointment_number', 'customer_table.customer_first_name', 'employee_schedule_table.employee_schedule_date', 'appointment_table.appointment_time', 'employee_schedule_table.employee_schedule_day', 'appointment_table.status', 'vehicle_table.vehicle_name', 'employee_table.employee_name');

			$main_query = "
			SELECT * FROM appointment_table 
			INNER JOIN employee_schedule_table 
			ON employee_schedule_table.employee_schedule_id = appointment_table.employee_schedule_id 
			INNER JOIN customer_table 
			ON customer_table.customer_id = appointment_table.customer_id 
			INNER JOIN vehicle_table 
			ON vehicle_table.vehicle_id = appointment_table.vehicle_id 
			INNER JOIN employee_table 
			ON employee_table.employee_id = appointment_table.employee_id 
			";

			$search_query = ' WHERE appointment_table.customer_id = '.$customer_id; 

		}

		if(isset($_POST["order"]))
		{
			$order_query = ' ORDER BY '.$order_column[$_POST['order']['0']['column']].' '.$_POST['order']['0']['dir'].' ';
		}
		else
		{
			$order_query = ' ORDER BY appointment_table.appointment_id DESC ';
		}


		$object->query = $main_query . $search_query . $order_query;
		// echo $object->query; return;

		$object->execute();

		$filtered_rows = $object->row_count();


		$result = $object->get_result();

		$object->query = $main_query . $search_query;

		$object->execute();

		$total_rows = $object->row_count();

		$data = array();

		foreach($result as $row)
		{
			$sub_array = array();

			$sub_array[] = $row["appointment_number"];

			$sub_array[] = $row["customer_first_name"] . ' ' . $row["customer_last_name"];

			$sub_array[] = $row["vehicle_brand"];

				$sub_array[] = $row["employee_name"];

				$sub_array[] = $row["employee_schedule_date"];

			$sub_array[] = $row["appointment_time"];

			$status = '';

			if($row["status"] == 'Booked')
			{
				$status = '<span class="badge badge-warning">' . $row["status"] . '</span>';
			}

			if($row["status"] == 'In Process')
			{
				$status = '<span class="badge badge-primary">' . $row["status"] . '</span>';
			}

			if($row["status"] == 'Completed')
			{
				$status = '<span class="badge badge-success">' . $row["status"] . '</span>';
			}

			if($row["status"] == 'Cancelled')
			{
				$status = '<span class="badge badge-danger">' . $row["status"] . '</span>';
			}
			
			$sub_array[] = $status;

			$sub_array[] = '
			<div align="center">
			<a href="http://localhost/kilatcarwash/download.php?id='.$row["appointment_id"].'" class="btn btn-danger btn-sm" target="_blank"><i class="fas fa-file-pdf"></i> PDF</a>
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

		$appointment_data = $object->get_result();

		foreach($appointment_data as $appointment_row)
		{

			$object->query = "
			SELECT * FROM customer_table 
			WHERE customer_id = '".$appointment_row["customer_id"]."'
			";

			$customer_data = $object->get_result();

			$object->query = "
			SELECT * FROM employee_schedule_table 
			INNER JOIN employee_table 
			ON employee_table.employee_id = employee_schedule_table.employee_id 
			WHERE employee_schedule_table.employee_schedule_id = '".$appointment_row["employee_schedule_id"]."'
			";

			$employee_schedule_data = $object->get_result();

			$object->query = "
			SELECT * FROM vehicle_table 
			WHERE customer_id = '".$appointment_row["customer_id"]."'
			";

			$vehicle_data = $object->get_result();

			$html = '
			<h4 class="text-center">vehicle Owner Details</h4>
			<table class="table">
			';

			foreach($customer_data as $customer_row)
			{
				$html .= '
				<tr>
					<th width="40%" class="text-right">Name</th>
					<td>'.$customer_row["customer_first_name"].' '.$customer_row["customer_last_name"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Contact No.</th>
					<td>'.$customer_row["customer_phone_no"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Address</th>
					<td>'.$customer_row["customer_address"].'</td>
				</tr>
				';
			}

			$html .= '
			</table>
			<hr />

			<h4 class="text-center">vehicle Details</h4>
			<table class="table">
			';
			foreach($vehicle_data as $vehicle_row)
			{
				$html .= '
				<tr>
					<th width="40%" class="text-right">vehicle Name</th>
					<td>'.$vehicle_row["vehicle_name"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">vehicle Age</th>
					<td>'.$vehicle_row["vehicle_age"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">vehicle Species</th>
					<td>'.$vehicle_row["vehicle_species"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">vehicle Gender</th>
					<td>'.$vehicle_row["vehicle_gender"].'</td>
				</tr>
				
				';
			}

			$html .= '
			</table>
			<hr />

			<h4 class="text-center">Appointment Details</h4>
			<table class="table">
				<tr>
					<th width="40%" class="text-right">Appointment No.</th>
					<td>'.$appointment_row["appointment_number"].'</td>
				</tr>
			';
			foreach($employee_schedule_data as $employee_schedule_row)
			{
				$html .= '
				<tr>
					<th width="40%" class="text-right">Employee Name</th>
					<td>'.$employee_schedule_row["employee_name"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Appointment Date</th>
					<td>'.$employee_schedule_row["employee_schedule_date"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Appointment Day</th>
					<td>'.$employee_schedule_row["employee_schedule_day"].'</td>
				</tr>
				
				';
			}

			$html .= '
				<tr>
					<th width="40%" class="text-right">Appointment Time</th>
					<td>'.$appointment_row["appointment_time"].'</td>
				</tr>
				<tr>
					<th width="40%" class="text-right">Reason for Appointment</th>
					<td>'.$appointment_row["reason_for_appointment"].'</td>
				</tr>
			';

			if($appointment_row["status"] != 'Cancel')
			{
				if($_SESSION['type'] != 'Admin')
				{
					if($appointment_row['customer_come_into_branch'] == 'Yes')
					{
						if($appointment_row["status"] == 'Completed')
						{
							$html .= '
								<tr>
									<th width="40%" class="text-right">customer attended appointment</th>
									<td>Yes</td>
								</tr>
								<tr>
									<th width="40%" class="text-right">Employee Comment</th>
									<td>'.$appointment_row["employee_comment"].'</td>
								</tr>
							';
						}
						else
						{
							$html .= '
								<tr>
									<th width="40%" class="text-right">customer attended appointment</th>
									<td>
										<select name="customer_come_into_branch" id="customer_come_into_branch" class="form-control" required>
											<option value="">Select</option>
											<option value="No" selected>No</option>
											<option value="Yes" selected>Yes</option>
										</select>
									</td>
								</tr
							';
						}
					}
					else
					{
						$html .= '
							<tr>
								<th width="40%" class="text-right">customer attended appointment</th>
								<td>
									<select name="customer_come_into_branch" id="customer_come_into_branch" class="form-control" required>
										<option value="">Select</option>
										<option value="No">No</option>
										<option value="Yes">Yes</option>
									</select>
								</td>
							</tr
						';
					}
				}

				if($_SESSION['type'] == 'employee')
				{
					if($appointment_row["customer_come_into_branch"] == 'Yes')
					{
						if($appointment_row["status"] == 'Completed')
						{
							$html .= '
								<tr>
									<th width="40%" class="text-right">employee Comment</th>
									<td>
										<textarea name="employee_comment" id="employee_comment" class="form-control" rows="8" required>'.$appointment_row["employee_comment"].'</textarea>
									</td>
								</tr
							';
						}
						else
						{
							$html .= '
								<tr>
									<th width="40%" class="text-right">employee Comment</th>
									<td>
										<textarea name="employee_comment" id="employee_comment" class="form-control" rows="8" required></textarea>
									</td>
								</tr
							';
						}
					}
				}
			
			}

			$html .= '
			</table>
			';
		}

		echo $html;
	}

	if($_POST['action'] == 'change_appointment_status')
	{
		if($_SESSION['type'] == 'employee')
		{
			$data = array(
				':status'							=>	'In Process',
				':customer_come_into_branch'		=>	'Yes',
				':appointment_id'					=>	$_POST['hidden_appointment_id']
			);

			$object->query = "
			UPDATE appointment_table 
			SET status = :status, 
			customer_come_into_branch = :customer_come_into_branch 
			WHERE appointment_id = :appointment_id
			";

			$object->execute($data);

			echo '<div class="alert alert-success">Appointment Status change to In Process</div>';
		}

		if($_SESSION['type'] == 'employee')
		{
			if(isset($_POST['employee_comment']))
			{
				$data = array(
					':status'							=>	'Completed',
					':employee_comment'					=>	$_POST['employee_comment'],
					':appointment_id'					=>	$_POST['hidden_appointment_id']
				);

				$object->query = "
				UPDATE appointment_table 
				SET status = :status, 
				employee_comment = :employee_comment 
				WHERE appointment_id = :appointment_id
				";

				$object->execute($data);

				echo '<div class="alert alert-success">Appointment Completed</div>';
			}
		}
	}
	

	if($_POST["action"] == 'delete')
	{
		$object->query = "
		DELETE FROM employee_schedule_table 
		WHERE employee_schedule_id = '".$_POST["id"]."'
		";

		$object->execute();

		echo '<div class="alert alert-success">employee Schedule has been Deleted</div>';
	}
}
?>
