<?php
include('../class/Appointment.php');
$object = new Appointment;

if (isset($_POST["action"])) {

    // if ($_POST["action"] == 'fetch') {
    //     $order_column = array('vehicle_id', 'vehicle_brand', 'vehicle_year', 'vehicle_model', 'comments', 'customer_id');
    //     $output = array();

    //     $query = "SELECT * FROM vehicle_table";
    //     $search_query = "";

    //     if (!empty($_POST["search"]["value"])) {
    //         $search_value = $_POST["search"]["value"];
    //         $search_query .= ' WHERE vehicle_brand LIKE "%' . $search_value . '%" ';
    //         $search_query .= ' OR vehicle_year LIKE "%' . $search_value . '%" ';
    //         $search_query .= ' OR vehicle_model LIKE "%' . $search_value . '%" ';
    //         $search_query .= ' OR comments LIKE "%' . $search_value . '%" ';
    //         $search_query .= ' OR customer_id LIKE "%' . $search_value . '%" ';
    //     }

    //     $order_query = isset($_POST["order"])
    //         ? ' ORDER BY ' . $order_column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir']
    //         : ' ORDER BY vehicle_id DESC ';

    //     $limit_query = $_POST["length"] != -1
    //         ? ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length']
    //         : '';

    //     $object->query = $query . $search_query . $order_query;
    //     $object->execute();
    //     $filtered_rows = $object->row_count();

    //     $object->query .= $limit_query;
    //     $result = $object->get_result();

    //     $object->query = $query . $search_query;
    //     $object->execute();
    //     $total_rows = $object->row_count();

    //     $data = array();
    //     foreach ($result as $row) {
    //         $sub_array = array();
    //         $sub_array[] = $row["vehicle_id"];
    //         $sub_array[] = $row["vehicle_brand"];
    //         $sub_array[] = $row["vehicle_year"];
    //         $sub_array[] = $row["vehicle_model"];
    //         $sub_array[] = $row["comments"];
    //         $sub_array[] = $row["customer_id"];
    //         $sub_array[] = '
    //         <div align="center">
    //             <button type="button" class="btn btn-info btn-circle btn-sm view_button" data-id="' . $row["vehicle_id"] . '"><i class="fas fa-eye"></i></button>
    //             <button type="button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="' . $row["vehicle_id"] . '"><i class="fas fa-edit"></i></button>
    //             <button type="button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="' . $row["vehicle_id"] . '"><i class="fas fa-trash"></i></button>
    //         </div>';
    //         $data[] = $sub_array;
    //     }

    //     echo json_encode(array(
    //         "draw" => intval($_POST["draw"]),
    //         "recordsTotal" => $total_rows,
    //         "recordsFiltered" => $filtered_rows,
    //         "data" => $data
    //     ));
    // }

    if ($_POST["action"] == 'fetch') {
    $order_column = array('v.vehicle_id', 'v.vehicle_brand', 'v.vehicle_year', 'v.vehicle_model', 'v.comments', 'c.customer_first_name');

    $output = array();

    $query = "
        SELECT v.*, c.customer_first_name, c.customer_last_name 
        FROM vehicle_table v 
        LEFT JOIN customer_table c 
        ON v.customer_id = c.customer_id
    ";

    $search_query = "";

    if (!empty($_POST["search"]["value"])) {
        $search_value = $_POST["search"]["value"];
        $search_query .= ' WHERE v.vehicle_brand LIKE "%' . $search_value . '%" ';
        $search_query .= ' OR v.vehicle_year LIKE "%' . $search_value . '%" ';
        $search_query .= ' OR v.vehicle_model LIKE "%' . $search_value . '%" ';
        $search_query .= ' OR v.comments LIKE "%' . $search_value . '%" ';
        $search_query .= ' OR c.customer_first_name LIKE "%' . $search_value . '%" ';
        $search_query .= ' OR c.customer_last_name LIKE "%' . $search_value . '%" ';
    }

    $order_query = isset($_POST["order"])
        ? ' ORDER BY ' . $order_column[$_POST['order']['0']['column']] . ' ' . $_POST['order']['0']['dir']
        : ' ORDER BY v.vehicle_id DESC ';

    $limit_query = $_POST["length"] != -1
        ? ' LIMIT ' . $_POST['start'] . ', ' . $_POST['length']
        : '';

    // Main query for filtered data
    $object->query = $query . $search_query . $order_query;
    $object->execute();
    $filtered_rows = $object->row_count();

    // Fetch paginated result
    $object->query .= $limit_query;
    $result = $object->get_result();

    // Get total rows (without limit)
    $object->query = $query . $search_query;
    $object->execute();
    $total_rows = $object->row_count();

    $data = array();
    foreach ($result as $row) {
        $sub_array = array();
        $sub_array[] = $row["vehicle_id"];
        $sub_array[] = $row["vehicle_brand"];
        $sub_array[] = $row["vehicle_year"];
        $sub_array[] = $row["vehicle_model"];
        $sub_array[] = $row["comments"];
        $sub_array[] = $row["customer_first_name"] . ' ' . $row["customer_last_name"]; // ✅ Full name
        $sub_array[] = '
        <div align="center">
            <button type="button" class="btn btn-info btn-circle btn-sm view_button" data-id="' . $row["vehicle_id"] . '"><i class="fas fa-eye"></i></button>
            <button type="button" class="btn btn-warning btn-circle btn-sm edit_button" data-id="' . $row["vehicle_id"] . '"><i class="fas fa-edit"></i></button>
            <button type="button" class="btn btn-danger btn-circle btn-sm delete_button" data-id="' . $row["vehicle_id"] . '"><i class="fas fa-trash"></i></button>
        </div>';
        $data[] = $sub_array;
    }

    echo json_encode(array(
        "draw" => intval($_POST["draw"]),
        "recordsTotal" => $total_rows,
        "recordsFiltered" => $filtered_rows,
        "data" => $data
    ));
}


    if ($_POST["action"] == 'Add') {
        $data = array(
            ':vehicle_brand' => $object->clean_input($_POST["vehicle_brand"]),
            ':vehicle_year' => $object->clean_input($_POST["vehicle_year"]),
            ':vehicle_model' => $object->clean_input($_POST["vehicle_model"]),
            ':comments' => $object->clean_input($_POST["comments"]),
            ':customer_id' => $object->clean_input($_POST["customer_id"]),
            ':vehicle_added_on' => $object->now
        );

        $object->query = "
            INSERT INTO vehicle_table (vehicle_brand, vehicle_year, vehicle_model, comments, customer_id, vehicle_added_on) 
            VALUES (:vehicle_brand, :vehicle_year, :vehicle_model, :comments, :customer_id, :vehicle_added_on)
        ";
        $object->execute($data);

        echo json_encode(['error' => '', 'success' => '<div class="alert alert-success">Vehicle Added</div>']);
    }

    // if ($_POST["action"] == 'fetch_single') {
    //     $object->query = "SELECT * FROM vehicle_table WHERE vehicle_id = '" . $_POST["vehicle_id"] . "'";
    //     $result = $object->get_result();

    //     foreach ($result as $row) {
    //         echo json_encode(array(
    //             'vehicle_brand' => $row['vehicle_brand'],
    //             'vehicle_year' => $row['vehicle_year'],
    //             'vehicle_model' => $row['vehicle_model'],
    //             'comments' => $row['comments'],
    //             'customer_id' => $row['customer_id']
    //         ));
    //     }
    // }

    if ($_POST["action"] == 'fetch_single') {
    $object->query = "
        SELECT v.*, c.customer_first_name, c.customer_last_name
        FROM vehicle_table v
        LEFT JOIN customer_table c ON v.customer_id = c.customer_id
        WHERE v.vehicle_id = '".$_POST["vehicle_id"]."'
    ";

    $result = $object->get_result();

    $data = array();
    foreach ($result as $row) {
        $data['vehicle_brand'] = $row['vehicle_brand'];
        $data['vehicle_year'] = $row['vehicle_year'];
        $data['vehicle_model'] = $row['vehicle_model'];
        $data['comments'] = $row['comments'];
        $data['customer_name'] = $row['customer_first_name'] . ' ' . $row['customer_last_name']; // full name
    }

    echo json_encode($data);
}


    if ($_POST["action"] == 'Edit') {
        $data = array(
            ':vehicle_brand' => $object->clean_input($_POST["vehicle_brand"]),
            ':vehicle_year' => $object->clean_input($_POST["vehicle_year"]),
            ':vehicle_model' => $object->clean_input($_POST["vehicle_model"]),
            ':comments' => $object->clean_input($_POST["comments"]),
            ':customer_id' => $object->clean_input($_POST["customer_id"])
        );

        $object->query = "
            UPDATE vehicle_table SET 
            vehicle_brand = :vehicle_brand,
            vehicle_year = :vehicle_year,
            vehicle_model = :vehicle_model,
            comments = :comments,
            customer_id = :customer_id
            WHERE vehicle_id = '" . $_POST["hidden_id"] . "'
        ";
        $object->execute($data);

        echo json_encode(['error' => '', 'success' => '<div class="alert alert-success">Vehicle Updated</div>']);
    }

    if ($_POST["action"] == 'delete') {
        $object->query = "DELETE FROM vehicle_table WHERE vehicle_id = '" . $_POST["id"] . "'";
        $object->execute();
        echo '<div class="alert alert-success">Vehicle Deleted</div>';
    }
}
?>
