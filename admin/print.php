<?php

//print.php


include('../class/Appointment.php');

$object = new Appointment;

require_once('C:\xampp3\htdocs\kilatcarwash\class\pdf.php');

$file_name = '';

if(isset($_GET["order_id"]))
{
	$output = '
	<table width="100%" border="0" cellpadding="5" cellspacing="5" style="font-family:Arial, san-sarif">';

	$object->query = "
		SELECT * FROM admin_table
	";

	$admin_data = $object->get_result();

	foreach($admin_data as $row)
	{
		$output .= '
		<tr>
			<td align="center">
				<b style="font-size:32px">'.$row["branch_name"].'</b>
				<br />
				<span style="font-size:16px;">'.$row["branch_address"].'</span>
				<br />
				<span style="font-size:16px;"><b>Contact No. - </b>'.$row["branch_contact_no"].'</span>
				<br />
				<span style="font-size:16px;"><b>Email - </b>'.$row["admin_email_address"].'</span>
				<br /><br />
			</td>
		</tr>
		';
	}

	$object->query = "
		SELECT * FROM order_table 
		WHERE order_id = '".$_GET["order_id"]."'
	";

	$order_result = $object->get_result();

	foreach($order_result as $order)
	{
		$file_name = $order["order_number"] . '.pdf';
		$output .= '
		<tr>
			<td>
				<table width="100%" border="0" cellpadding="5" cellspacing="5">
					<tr>
						<td width="30%"><b>Bill No:- </b>'.$order["order_number"].'</td>
						<td width="30%"><b>Appointment No:- </b>'.$order["order_table"].'</td>
						<td width="20%" align="right"><b>Date:- </b>'.$order["order_date"].'</td>
						<td width="20%" align="right"><b>Time:- </b>'.$order["order_time"].'</td>
					</tr>
				</table>
			</td>
		</tr>
		';

		$object->query = "
			SELECT * FROM order_item_table 
			WHERE order_id = '".$_GET["order_id"]."' 
			ORDER BY order_item_id ASC
		";

		$order_item_result = $object->get_result();

		$output .= '
			<tr>
				<td>
					<table width="100%" border="1" cellpadding="10" cellspacing="0">
						<tr>
							<th width="10%">Sr#</th>
							<th width="45%">Item</th>
							<th width="10%">Qty.</th>
							<th width="20%">Price</th>
							<th width="15%">Amount</th>
						</tr>';
		$count = 0;
		foreach($order_item_result as $item)
		{
			$count++;
			$output .= '
						<tr>
							<td>'.$count.'</td>
							<td>'.$item["product_name"].'</td>
							<td>'.$item["product_quantity"].'</td>
							<td>'.$object->cur . $item["product_rate"].'</td>
							<td>'.$object->cur . $item["product_amount"].'</td>
						</tr>
			';
		}

		$object->query = "
		SELECT * FROM order_tax_table 
		WHERE order_id = '".$_GET["order_id"]."'
		";

		$tax_result = $object->execute();

		$total_tax_row = $object->row_count();

		$rowspan = 2 + $total_tax_row;

		$tax_result = $object->statement_result();

		$output .= '
						<tr>
							<td rowspan="'.$rowspan.'" colspan="3">
							
							</td>
							<td align="right"><b>Net amount</b></td>
							<td>'.$object->cur . $order["order_gross_amount"].'</td>
						</tr>
		';

		foreach($tax_result as $tax)
		{
			$output .= '
						<tr>
							<td align="right"><b>'.$tax["order_tax_name"].' ('.$tax["order_tax_percentage"].'%)</b></td>
							<td>'.$object->cur . $tax["order_tax_amount"].'</td>
						</tr>
			';
		}

		$output .= '
						<tr>
							<td align="right"><b>Gross total</b></td>
							<td>'.$object->cur . $order["order_net_amount"].'</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td align="center">Thank you, Please come again</td>
			</tr>
			';
	}



	$output .= '</table>';

	$pdf = new Pdf();

	$pdf->loadHtml($output, 'UTF-8');
	$pdf->render();
	$pdf->stream($file_name, array( 'Attachment'=>0 ));
	exit(0);

}

?>