<?php

//employee.php

include('../class/Appointment.php');

$object = new Appointment;

if(!$object->is_login())
{
    header("location:".$object->base_url."admin");
}

if($_SESSION['type'] != 'Admin')
{
    header("location:".$object->base_url."");
}

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Employees Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Employees List</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_employee" id="add_employee" class="btn btn-success btn-circle btn-sm" data-toggle="tooltip" data-placement="top" title="Register account"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="employee_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Email Address</th>
                                            <th>Name</th>
                                            <th>Contact No.</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                <?php
                include('footer.php');
                ?>

<div id="employeeModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="employee_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Employees</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
		          	<div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Email Address <span class="text-danger">*</span></label>
                                <input type="text" name="employee_email_address" id="employee_email_address" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" name="employee_password" id="employee_password" class="form-control" required  data-parsley-trigger="keyup" />
                            </div>
		          		</div>
		          	</div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" name="employee_name" id="employee_name" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Contact No. <span class="text-danger">*</span></label>
                                <input type="text" name="employee_phone_no" id="employee_phone_no" class="form-control" required  data-parsley-trigger="keyup" />
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Address <span class="text-danger">*</span></label>
                                <input type="text" name="employee_address" id="employee_address" class="form-control" required  data-parsley-trigger="keyup"/>
                            </div>
                            <div class="col-md-6">
                                <label>Date of birth</label>
                                <input type="text" name="employee_date_of_birth" id="employee_date_of_birth" readonly class="form-control" />
                            </div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label>Image <span class="text-danger"></span></label>
                        <br />
                        <input type="file" name="employee_profile_image" id="employee_profile_image" />
                        <div id="uploaded_image"></div>
                        <input type="hidden" name="hidden_employee_profile_image" id="hidden_employee_profile_image" />
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

<div id="viewModal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="modal_title">View Employee Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="employee_details">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){

	var dataTable = $('#employee_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"kilat_employee_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[0, 1, 2, 4, 5],
				"orderable":false,
			},
		],
	});

    $('#employee_date_of_birth').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true
    });

	$('#add_employee').click(function(){
		
		$('#employee_form')[0].reset();

		$('#employee_form').parsley().reset();

    	$('#modal_title').text('Add Employee');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#employeeModal').modal('show');

    	$('#form_message').html('');

	});

	$('#employee_form').parsley();

	$('#employee_form').on('submit', function(event){
		event.preventDefault();
		if($('#employee_form').parsley().isValid())
		{		
			$.ajax({
				url:"kilat_employee_action.php",
				method:"POST",
				data: new FormData(this),
				dataType:'json',
                contentType: false,
                cache: false,
                processData:false,
				beforeSend:function()
				{
					$('#submit_button').attr('disabled', 'disabled');
					$('#submit_button').val('wait...');
				},
				success:function(data)
				{
					$('#submit_button').attr('disabled', false);
					if(data.error != '')
					{
						$('#form_message').html(data.error);
						$('#submit_button').val('Add');
					}
					else
					{
						$('#employeeModal').modal('hide');
						$('#message').html(data.success);
						dataTable.ajax.reload();

						setTimeout(function(){

				            $('#message').html('');

				        }, 5000);
					}
				}
			})
		}
	});

	$(function () {
  $('[data-toggle="tooltip"]').tooltip()
})

	$(document).on('click', '.edit_button', function(){

		var employee_id = $(this).data('id');

		$('#employee_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"kilat_employee_action.php",

	      	method:"POST",

	      	data:{employee_id:employee_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{

	        	$('#employee_email_address').val(data.employee_email_address);
                $('#employee_password').val(data.employee_password);
                $('#employee_name').val(data.employee_name);
                $('#uploaded_image').html('<img src="'+data.employee_profile_image+'" class="img-fluid img-thumbnail" width="150" />')
                $('#hidden_employee_profile_image').val(data.employee_profile_image);
                $('#employee_phone_no').val(data.employee_phone_no);
                $('#employee_address').val(data.employee_address);
                $('#employee_date_of_birth').val(data.employee_date_of_birth);
                $('#employee_expert_in').val(data.employee_expert_in);

	        	$('#modal_title').text('Edit Employee');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#employeeModal').modal('show');

	        	$('#hidden_id').val(employee_id);

	      	}

	    })

	});

	$(document).on('click', '.status_button', function(){
		var id = $(this).data('id');
    	var status = $(this).data('status');
		var next_status = 'Active';
		if(status == 'Active')
		{
			next_status = 'Inactive';
		}
		if(confirm("Are you sure you want to "+next_status+" it?"))
    	{

      		$.ajax({

        		url:"kilat_employee_action.php",

        		method:"POST",

        		data:{id:id, action:'change_status', status:status, next_status:next_status},

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

    $(document).on('click', '.view_button', function(){
        var employee_id = $(this).data('id');

        $.ajax({

            url:"kilat_employee_action.php",

            method:"POST",

            data:{employee_id:employee_id, action:'fetch_single'},

            dataType:'JSON',

            success:function(data)
            {
                var html = '<div class="table-responsive">';
                html += '<table class="table">';

                html += '<tr><td colspan="2" class="text-center"><img src="'+data.employee_profile_image+'" class="img-fluid img-thumbnail" width="150" /></td></tr>';

                html += '<tr><th width="40%" class="text-right">Email Address</th><td width="60%">'+data.employee_email_address+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Name</th><td width="60%">'+data.employee_name+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Contact No.</th><td width="60%">'+data.employee_phone_no+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Address</th><td width="60%">'+data.employee_address+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Date of birth</th><td width="60%">'+data.employee_date_of_birth+'</td></tr>';

                html += '</table></div>';

                $('#viewModal').modal('show');

                $('#employee_details').html(html);

            }

        })
    });

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"kilat_employee_action.php",

        		method:"POST",

        		data:{id:id, action:'delete'},

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



});
</script>