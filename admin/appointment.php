<?php

//employee.php

include('../class/Appointment.php');

$object = new Appointment;

if(!$object->is_login())
{
    header("location:".$object->base_url."admin");
}

include('header.php');


$object->query = "
SELECT * FROM employee_table 
WHERE employee_status = 'Active' 
ORDER BY employee_name ASC
";

$employee_result = $object->get_result();

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Booked Appointments Management</h1>

                   <button id="notifyTelegram" class="btn btn-dark">Notify Customer (Telegram)</button>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('#notifyTelegram').on('click', function () {
    $.ajax({
      url: '../send_telegram.php', // relative path from /admin to project root
      method: 'GET',
      success: function (response) {
        alert("Telegram message sent:\n" + response);
      },
      error: function (xhr, status, error) {
        alert("Failed to send Telegram message.\n" + error);
      }
    });
  });
</script>


                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col-sm-6">
                            		<h6 class="m-0 font-weight-bold text-primary">Booked Appointments List</h6>
                            	</div>
                            	<div class="col-sm-6" align="right">
                                    <div class="row">
                                        <div class="col-md-9">
                                            <div class="row input-daterange">
                                                <div class="col-md-6">
                                                    <input type="text" name="start_date" id="start_date" class="form-control form-control-sm" readonly />
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="text" name="end_date" id="end_date" class="form-control form-control-sm" readonly />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="row">
                                                <button type="button" name="search" id="search" value="Search" class="btn btn-info btn-sm"><i class="fas fa-search"></i></button>
                                                &nbsp;<button type="button" name="refresh" id="refresh" class="btn btn-secondary btn-sm"><i class="fas fa-sync-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered" id="appointment_table">
                                    <thead>
                                        <tr>
                                            <th>Appointment No.</th>
                                            <th>Vehicle Owner</th>
                                            <?php
                                            if($_SESSION['type'] != 'Admin')
                                            {
                                            ?>
                                            <th>Car Brand</th>
                                            <?php
                                            }
                                            ?>

                                            <?php
                                            if($_SESSION['type'] == 'Admin')
                                            {
                                            ?>
                                            <th>Employee</th>
                                            <?php
                                            }
                                            ?>
                                            <th>Appointment Date</th>
                                            <th>Appointment Time</th>
                                            <?php
                                            if($_SESSION['type'] != 'Admin')
                                            {
                                            ?>
                                            <th>Appointment Day</th>
                                            <?php
                                            }
                                            ?>
                                            <th>Appointment Status</th>
                                            <th>Bay Status</th>
                                            <th>View</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                include('footer.php');
                ?>

<div id="viewModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="edit_appointment_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">View Booked Appointment Details</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                
                    <div id="appointment_details"></div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="hidden_appointment_id" id="hidden_appointment_id" />
                    <input type="hidden" name="action1" value="change_appointment_status" />
                    <input type="hidden" name="action" value="Edit" />
                    <!-- <input type="submit" name="save_appointment" id="save_appointment" class="btn btn-primary" value="Save" /> -->
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="viewModal1" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">View Vehicle Owner Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="customer_details">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="productModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="product_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Appointment Status</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="status" class="form-control" required data-parsley-trigger="change">
                            <option value="">Select</option>
                            <option value="In Process">In Process</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
		          	
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_id" id="hidden_id" />
          			<input type="hidden" name="action" id="action" value="Add" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
          			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        		</div>
      		</div>
    	</form>
  	</div>
</div>

<div id="viewModal1" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">View Vehicle Owner Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="customer_details">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div id="productModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="product_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Appointment Status</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
                    <div class="form-group">
                        <label>Employee</label>
                        <select name="employee_id" id="employee_id" class="form-control" required data-parsley-trigger="change">
                            <option value="">Select Employee</option>
                            <?php
                            foreach($employee_result as $employee)
                            {
                                echo '<option value="'.$employee["employee_id"].'">'.$employee["employee_name"].'</option>';
                            }
                            ?>
                        </select>
                    </div>
		          	
        		</div>
        		<div class="modal-footer">
          			<input type="hidden" name="hidden_id" id="hidden_id" />
          			<input type="hidden" name="action" id="action" value="Add" />
          			<input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
          			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        		</div>
      		</div>
    	</form>
  	</div>
</div>

<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.0/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/js/tempusdominus-bootstrap-4.min.js" integrity="sha512-k6/Bkb8Fxf/c1Tkyl39yJwcOZ1P4cRrJu77p83zJjN2Z55prbFHxPs9vN7q3l3+tSMGPDdoH51AEU8Vgo1cgAA==" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.39.0/css/tempusdominus-bootstrap-4.min.css" integrity="sha512-3JRrEUwaCkFUBLK1N8HehwQgu8e23jTH4np5NHOmQOobuC4ROQxFwFgBLTnhcnQRMs84muMh0PnnwXlPq5MGjg==" crossorigin="anonymous" />



<script>

    

$(document).ready(function(){

    fetch_data('no');

    function fetch_data(is_date_search, start_date='', end_date='')
    {
        var dataTable = $('#appointment_table').DataTable({
            "processing" : true,
            "serverSide" : true,
            "order" : [],
            "ajax" : {
                url:"appointment_action.php",
                type:"POST",
                data:{
                    is_date_search:is_date_search, start_date:start_date, end_date:end_date, action:'fetch'
                }
            },
            "columnDefs":[
                {
                    <?php
                    if($_SESSION['type'] == 'Admin')
                    {
                    ?>
                    "targets":[8],
                    <?php
                    }
                    else
                    {
                    ?>
                    "targets":[8],
                    <?php
                    }
                    ?>
                    "orderable":false,
                },
            ],
        });

        $('#product_form').parsley();

// $('#product_form').on('submit', function(event){
//     event.preventDefault();
//     if($('#product_form').parsley().isValid())
//     {		
//         $.ajax({
//             url:"appointment_action.php",
//             method:"POST",
//             data:$(this).serialize(),
//             dataType:'json',
//             beforeSend:function()
//             {
//                 $('#submit_button').attr('disabled', 'disabled');
//                 $('#submit_button').val('wait...');
//             },
//             success:function(data)
//             {
//                 $('#submit_button').attr('disabled', false);
//                 if(data.error != '')
//                 {
//                     $('#form_message').html(data.error);
//                     $('#submit_button').val('Add');
//                 }
//                 else
//                 {
//                     $('#productModal').modal('hide');
//                     $('#message').html(data.success);
//                     dataTable.ajax.reload();

//                     setTimeout(function(){

//                         $('#message').html('');

//                     }, 5000);
//                 }
//             }
//         })
//     }
// });

$(function(){

  // 1) click “edit” in your table
  $(document).on('click', '.edit_button', function(){
    var appointment_id = $(this).data('id');

    // reset & set up modal
    $('#product_form')[0].reset();
    $('#form_message').empty();
    $('#modal_title').text('Change Appointment Status');
    $('#submit_button').text('Update');
    $('#action').val('fetch_single5');   // first step: fetch

    // fetch current status
    $.ajax({
      url: 'appointment_action.php',
      method: 'POST',
      data: {
        action: 'fetch_single5',
        appointment_id: appointment_id
      },
      dataType: 'json',
      success: function(data){
        $('#status').val(data.status);
        $('#hidden_id').val(appointment_id);
        $('#action').val('Edit5');        // next step: when you submit, do Edit5
        $('#productModal').modal('show');
      },
      error: function(xhr){
        console.error('Fetch error:', xhr.responseText);
      }
    });
  });

  // 2) submit form to update status
  $('#product_form').on('submit', function(e){
    e.preventDefault();
    if (!$('#product_form').parsley().isValid()) return;

    $.ajax({
      url: 'appointment_action.php',
      method: 'POST',
      data: $(this).serialize(),
      dataType: 'html',
      beforeSend: function(){
        $('#submit_button').prop('disabled', true).text('Please wait...');
      },
      success: function(html){
        $('#submit_button').prop('disabled', false).text('Update');
        $('#form_message').html(html);
        $('#productModal').modal('hide');
        $('#message').html(html);
        dataTable.ajax.reload();          // your datatable
        setTimeout(function(){ $('#message').empty(); }, 5000);
      },
      error: function(xhr){
        console.error('Update error:', xhr.responseText);
        $('#submit_button').prop('disabled', false).text('Update');
        $('#form_message').html('<div class="alert alert-danger">Server error</div>');
      }
    });
  });

});


$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})


$(document).on('click', '.charge_button', function(){

var appointment_id = $(this).data('id');

$('#product_form').parsley().reset();

$('#form_message').html('');

$.ajax({

      url:"appointment_action.php",

      method:"POST",

      data:{appointment_id:appointment_id, action:'fetch_single5'},

      dataType:'JSON',

      success:function(data)
      {

        $('#status').val(data.status);

        $('#modal_title').text('Edit Data');

        $('#action').val('Edit5');

        $('#submit_button').val('Edit');

        $('#productModal').modal('show');

        $('#hidden_id').val(appointment_id);

      }

})

});

$(document).on('click', '.edit_button', function(){

var appointment_id = $(this).data('id');

$('#product_form').parsley().reset();

$('#form_message').html('');

$.ajax({

      url:"appointment_action.php",

      method:"POST",

      data:{appointment_id:appointment_id, action:'fetch_single4'},

      dataType:'JSON',

      success:function(data)
      {

        $('#employee_id').val(data.employee_id);

        $('#appointment_number').val(data.appointment_number);

        $('#modal_title').text('Edit Data');

        $('#action').val('Edit1');

        $('#submit_button').val('Edit');

        $('#productModal').modal('show');

        $('#hidden_id').val(appointment_id);

      }

})

});

        $(document).on('click', '.cancel_button', function(){

var id = $(this).data('id');

if(confirm("Are you sure you want to cancel this apppointment ?"))
{

  $.ajax({

    url:"appointment_action.php",

    method:"POST",

    data:{id:id, action:'cancel'},

    success:function(data)
    {

          $('#message').html(data);

          dataTable.ajax.reload();

          setTimeout(function(){

            $('#message').html('');

          }, 5000);

    }

  })

}
});

    }

    $(document).ready(function() {
    $("body").tooltip({ selector: '[data-toggle=tooltip]',placement: 'top' });
});


    // $(document).on('click', '.view_button', function(){

    //     var appointment_id = $(this).data('id');

    //     $.ajax({

    //         url:"appointment_action.php",

    //         method:"POST",

    //         data:{appointment_id:appointment_id, action:'fetch_single'},

    //         success:function(data)
    //         {
    //             $('#viewModal').modal('show');

    //             $('#appointment_details').html(data);

    //             $('#hidden_appointment_id').val(appointment_id);

    //         }

    //     })
    // });

    $(document).on('click', '.file_button', function(){
        var customer_id = $(this).data('id');

        $.ajax({

            url:"appointment_action.php",

            method:"POST",

            data:{customer_id:customer_id, action:'fetch_single1'},

            dataType:'JSON',
            
        success:function(data)
        {

            var html = '<div class="table-responsive">';
                html += '<table class="table">';

                html += '<tr><td colspan="2" class="text-center"><img src="'+data.customer_profile_image+'" class="img-fluid img-thumbnail" width="150" /></td></tr>';

                html += '<tr><th width="40%" class="text-right">customer Email Address</th><td width="60%">'+data.customer_email_address+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">customer Password</th><td width="60%">'+data.customer_password+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">customer First Name</th><td width="60%">'+data.customer_first_name+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">customer Last Name</th><td width="60%">'+data.customer_last_name+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">customer Phone No.</th><td width="60%">'+data.customer_phone_no+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">customer Address</th><td width="60%">'+data.customer_address+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Date of birth</th><td width="60%">'+data.customer_date_of_birth+'</td></tr>';

                html += '</table></div>';

                $('#viewModal1').modal('show');

                $('#customer_details').html(html);

        }

    })
});



    $('.input-daterange').datepicker({
        todayBtn:'linked',
        format: "yyyy-mm-dd",
        autoclose: true
    });

    $('#search').click(function(){
        var start_date = $('#start_date').val();
        var end_date = $('#end_date').val();
        if(start_date != '' && end_date !='')
        {
            $('#appointment_table').DataTable().destroy();
            fetch_data('yes', start_date, end_date);
        }
        else
        {
            alert("Both Date is Required");
        }
    });

    $('#refresh').click(function(){
        $('#appointment_table').DataTable().destroy();
        fetch_data('no');
    });

    $('#edit_appointment_form').parsley();

    $('#edit_appointment_form').on('submit', function(event){
        event.preventDefault();
        if($('#edit_appointment_form').parsley().isValid())
        {       
            $.ajax({
                url:"appointment_action.php",
                method:"POST",
                data: $(this).serialize(),
                beforeSend:function()
                {
                    $('#save_appointment').attr('disabled', 'disabled');
                    $('#save_appointment').val('wait...');
                },
                success:function(data)
                {
                    $('#save_appointment').attr('disabled', false);
                    $('#save_appointment').val('Save');
                    $('#viewModal').modal('hide');
                    $('#message').html(data);
                    $('#appointment_table').DataTable().destroy();
                    fetch_data('no');
                    setTimeout(function(){
                        $('#message').html('');
                    }, 5000);
                }
            })
        }
    });

});

</script>

<script>
  $(function(){
    $('#appointment_table').on('click', '.view_button', function(){
      var appointment_id = $(this).data('id');
      $.ajax({
        url: 'appointment_action.php',
        method: 'POST',
        data: { appointment_id: appointment_id, action: 'fetch_single' },
        dataType: 'html'
      })
      .done(function(html){
        $('#viewModal')
          .modal('show')
          .find('#appointment_details').html(html);
        $('#hidden_appointment_id').val(appointment_id);
      })
      .fail(function(xhr, status, err){
        console.error('AJAX error:', err, xhr.responseText);
        alert('Could not load details—check console for errors.');
      });
    });
  });
</script>
</body>
</html>

