<?php
include('../class/Appointment.php');
$object = new Appointment;

if(!$object->is_login()) {
    header("location:".$object->base_url."admin");
}

if($_SESSION['type'] != 'Admin') {
    header("location:".$object->base_url."");
}

$object->query = "SELECT * FROM customer_table ORDER BY customer_first_name ASC";
$vehicle_owner_result = $object->get_result();

include('header.php');
?>

<h1 class="h3 mb-4 text-gray-800">Vehicle Management</h1>
<span id="message"></span>
<div class="card shadow mb-4">
  <div class="card-header py-3">
    <div class="row">
      <div class="col"><h6 class="m-0 font-weight-bold text-primary">Vehicle List</h6></div>
      <div class="col" align="right">
        <button type="button" name="add_customer" id="add_customer" class="btn btn-success btn-circle btn-sm" data-toggle="tooltip" title="Add Vehicle">
          <i class="fas fa-plus"></i>
        </button>
      </div>
    </div>
  </div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered" id="customer_table" width="100%" cellspacing="0">
        <thead>
          <tr>
            <th>ID</th>
            <th>Car Brand</th>
            <th>Car Year</th>
            <th>Car Model</th>
            <th>Comments</th>
            <th>Vehicle Owner</th>  <!-- changed here -->
            <th>Action</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Add/Edit Modal -->
<div id="customerModal" class="modal fade">
  <div class="modal-dialog">
    <form method="post" id="customer_form">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title" id="modal_title">Add Vehicle</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <span id="form_message"></span>
          <div class="form-group">
            <label>Car Brand</label>
            <select name="vehicle_brand" id="vehicle_brand" class="form-control" required>
              <option value="">Select</option>
              <option value="Perodua">Perodua</option>
              <option value="Toyota">Toyota</option>
              <option value="Proton">Proton</option>
              <option value="Honda">Honda</option>
              <option value="BYD">BYD</option>
              <option value="Kia">Kia</option>
              <option value="BMW">BMW</option>
              <option value="Volkswagen">Volkswagen</option>
              <option value="Subaru">Subaru</option>
              <option value="Haval">Haval</option>
            </select>
          </div>
          <div class="form-group">
            <label>Car Year</label>
            <input type="text" name="vehicle_year" id="vehicle_year" class="form-control" required readonly>
          </div>
          <div class="form-group">
            <label>Car Model</label>
            <input type="text" name="vehicle_model" id="vehicle_model" class="form-control" required>
          </div>
          <div class="form-group">
            <label>Comments</label>
            <input type="text" name="comments" id="comments" class="form-control">
          </div>
          <div class="form-group">
            <label>Vehicle Owner</label>
            <select name="customer_id" id="customer_id" class="form-control" required>
              <option value="">Select vehicle owner</option>
              <?php foreach($vehicle_owner_result as $row): ?>
                <option value="<?= $row['customer_id'] ?>"><?= $row['customer_first_name'].' '.$row['customer_last_name'] ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <input type="hidden" name="hidden_id" id="hidden_id" />
          <input type="hidden" name="action" id="action" value="Add" />
          <input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="modal fade">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title">View Vehicle Details</h4>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>
      <div class="modal-body" id="customer_details"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- JS scripts -->
<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/datatables/jquery.dataTables.min.js"></script>
<script src="../vendor/datatables/dataTables.bootstrap4.min.js"></script>
<script src="../vendor/datepicker/bootstrap-datepicker.js"></script>

<script>
$(document).ready(function(){

  $('#vehicle_year').datepicker({
    format: "yyyy",
    viewMode: "years",
    minViewMode: "years",
    autoclose: true
  });

  var dataTable = $('#customer_table').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax": {
      url: "vehicle_action.php",
      type: "POST",
      data: { action: 'fetch' }
    },
    "columnDefs": [
      { "orderable": false, "targets": [6] }
    ],
  });

  $('#add_customer').on('click', function() {
    $('#customer_form')[0].reset();
    $('#form_message').html('');
    $('#action').val('Add');
    $('#submit_button').val('Add');
    $('#modal_title').text('Add Vehicle');
    $('#customerModal').modal('show');
  });

  $('#customer_form').on('submit', function(e){
    e.preventDefault();
    $.ajax({
      url: "vehicle_action.php",
      method: "POST",
      data: new FormData(this),
      contentType: false,
      processData: false,
      dataType: 'json',
      beforeSend: function() {
        $('#submit_button').attr('disabled', true).val('Wait...');
      },
      success: function(data) {
        $('#submit_button').attr('disabled', false).val($('#action').val());
        if(data.error != '') {
          $('#form_message').html(data.error);
        } else {
          $('#customerModal').modal('hide');
          $('#message').html(data.success);
          dataTable.ajax.reload();
          setTimeout(() => $('#message').html(''), 4000);
        }
      }
    });
  });

$(document).on('click', '.view_button', function() {
  var vehicle_id = $(this).data('id');
  $.ajax({
    url: "vehicle_action.php",
    method: "POST",
    data: { vehicle_id: vehicle_id, action: 'fetch_single' },
    dataType: 'json',
    success: function(data) {
      var html = '<table class="table">';
      html += '<tr><th>Car Brand</th><td>' + data.vehicle_brand + '</td></tr>';
      html += '<tr><th>Car Year</th><td>' + data.vehicle_year + '</td></tr>';
      html += '<tr><th>Car Model</th><td>' + data.vehicle_model + '</td></tr>';
      html += '<tr><th>Comments</th><td>' + data.comments + '</td></tr>';
      html += '<tr><th>Owner</th><td>' + data.customer_name + '</td></tr>';
      html += '</table>';
      $('#customer_details').html(html);
      $('#viewModal').modal('show');
    }
  });
});


  // EDIT
  $(document).on('click', '.edit_button', function() {
    var vehicle_id = $(this).data('id');
    $.ajax({
      url: "vehicle_action.php",
      method: "POST",
      data: { vehicle_id: vehicle_id, action: 'fetch_single' },
      dataType: 'json',
      success: function(data) {
        $('#vehicle_brand').val(data.vehicle_brand);
        $('#vehicle_year').val(data.vehicle_year);
        $('#vehicle_model').val(data.vehicle_model);
        $('#comments').val(data.comments);
        $('#customer_id').val(data.customer_id);
        $('#hidden_id').val(vehicle_id);
        $('#modal_title').text('Edit Vehicle');
        $('#action').val('Edit');
        $('#submit_button').val('Edit');
        $('#customerModal').modal('show');
      }
    });
  });

  // DELETE
  $(document).on('click', '.delete_button', function() {
    var id = $(this).data('id');
    if(confirm("Are you sure you want to delete this vehicle?")) {
      $.ajax({
        url: "vehicle_action.php",
        method: "POST",
        data: { id: id, action: 'delete' },
        success: function(data) {
          $('#message').html(data);
          dataTable.ajax.reload();
          setTimeout(() => $('#message').html(''), 4000);
        }
      });
    }
  });

});
</script>

<?php include('footer.php'); ?>
