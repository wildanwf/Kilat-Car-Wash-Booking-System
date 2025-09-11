<?php

//download.php

include('class/Appointment.php');

$object = new Appointment;

require_once('class/pdf.php');

if(isset($_GET["id"]))
{
	$html = '<table border="0" cellpadding="5" cellspacing="5" width="100%">';

	$object->query = "
	SELECT branch_name, branch_address, branch_contact_no, branch_logo 
	FROM admin_table
	";

	$branch_data = $object->get_result();

	foreach($branch_data as $branch_row)
	{
		$html .= '<tr><td align="center">';
		if($branch_row['branch_logo'] != '')
		{
			$html .= '<img src="'.substr($branch_row['branch_logo'], 3).'" /><br />';
		}
		$html .= '<h2 align="center">'.$branch_row['branch_name'].'</h2>
		<p align="center">'.$branch_row['branch_address'].'</p>
		<p align="center"><b>Contact No. - </b>'.$branch_row['branch_contact_no'].'</p></td></tr>
		';
	}

	$html .= "
	<tr><td><hr /></td></tr>
	<tr><td>
	";

	$object->query = "
	SELECT * FROM appointment_table 
	WHERE appointment_id = '".$_GET["id"]."'
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
		
		$html .= '
		<h4 align="center">Vehicle Owmer Details</h4>
		<table border="0" cellpadding="5" cellspacing="5" width="100%">';

		foreach($customer_data as $customer_row)
		{
			$html .= '<tr><th width="50%" align="right">Customer Name</th><td>'.$customer_row["customer_first_name"].' '.$customer_row["customer_last_name"].'</td></tr>
			<tr><th width="50%" align="right">Contact No.</th><td>'.$customer_row["customer_phone_no"].'</td></tr>
			<tr><th width="50%" align="right">Address</th><td>'.$customer_row["customer_address"].'</td></tr>';
		}

		$html .= '</table><br /><hr />
		<h4 align="center">Appointment Details</h4>
		<table border="0" cellpadding="5" cellspacing="5" width="100%">
			<tr>
				<th width="50%" align="right">Appointment No.</th>
				<td>'.$appointment_row["appointment_number"].'</td>
			</tr>
		';
		foreach($employee_schedule_data as $employee_schedule_row)
		{
			$html .= '
			<tr>
				<th width="50%" align="right">employee Name</th>
				<td>'.$employee_schedule_row["employee_name"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Appointment Date</th>
				<td>'.$employee_schedule_row["employee_schedule_date"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Appointment Day</th>
				<td>'.$employee_schedule_row["employee_schedule_day"].'</td>
			</tr>
				
			';
		}

		$html .= '
			<tr>
				<th width="50%" align="right">Appointment Time</th>
				<td>'.$appointment_row["appointment_time"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Reason for Appointment</th>
				<td>'.$appointment_row["reason_for_appointment"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">Customer come into Hostpital</th>
				<td>'.$appointment_row["customer_come_into_branch"].'</td>
			</tr>
			<tr>
				<th width="50%" align="right">employee Comment</th>
				<td>'.$appointment_row["employee_comment"].'</td>
			</tr>
		</table>
			';
	}

	$html .= '
			</td>
		</tr>
	</table>';

	echo $html;

	$pdf = new Pdf();

	$pdf->loadHtml($html, 'UTF-8');
	$pdf->render();
	ob_end_clean();
	//$pdf->stream($_GET["id"] . '.pdf', array( 'Attachment'=>1 ));
	$pdf->stream($_GET["id"] . '.pdf', array( 'Attachment'=>false ));
	exit(0);

}

?>