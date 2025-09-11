<?php

//customer.php

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
                    <h1 class="h3 mb-4 text-gray-800">Vehicle Owners Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Vehicle Owners List</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_customer" id="add_customer" class="btn btn-success btn-circle btn-sm" data-toggle="tooltip" data-placement="top" title="Register account"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="customer_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID Number</th>
                                            <th>Email Address</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Contact</th>
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

<div id="customerModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="customer_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Vehicle Owner</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
		          	<div class="form-group">
                        <div class="row">
                            <div class="col-md-12">
                                <label>Email Address <span class="text-danger">*</span></label>
                                <input type="text" name="customer_email_address" id="customer_email_address" class="form-control" required data-parsley-type="email" data-parsley-trigger="keyup" />
                            </div>
                            <!-- <div class="col-md-6">
                                <label>Password <span class="text-danger">*</span></label>
                                <input type="password" name="customer_password" id="customer_password" class="form-control" required  data-parsley-trigger="keyup" />
                            </div> -->
		          		</div>
		          	</div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>First Name<span class="text-danger">*</span></label>
                                <input type="text" name="customer_first_name" id="customer_first_name" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Last Name<span class="text-danger">*</span></label>
                                <input type="text" name="customer_last_name" id="customer_last_name" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Phone No. <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone_no" id="customer_phone_no" class="form-control" required  data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Gender <span class="text-danger"></span></label>
                                <!-- <input type="text" name="customer_gender" id="customer_gender" class="form-control" required  data-parsley-trigger="keyup" /> -->
                                <select id="customer_gender" name="customer_gender" class="form-control" required  data-parsley-trigger="keyup">
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                </select>
                            </div>
                        </div>
                    </div> 

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Address </label>
                                <input type="text" name="customer_address" id="customer_address" class="form-control" />
                            </div>
                        </div>
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

<script>
$(document).ready(function(){

	var dataTable = $('#customer_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"vehicle_owner_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[0, 1, 2, 3, 4, 5],
				"orderable":false,
			},
		],
	});

    $('#customer_date_of_birth').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true
    });

	$('#add_customer').click(function(){
		
		$('#customer_form')[0].reset();

		$('#customer_form').parsley().reset();

    	$('#modal_title').text('Add Vehicle Owner');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#customerModal').modal('show');

    	$('#form_message').html('');

	});

	$('#customer_form').parsley();

	$('#customer_form').on('submit', function(event){
		event.preventDefault();
		if($('#customer_form').parsley().isValid())
		{		
			$.ajax({
				url:"vehicle_owner_action.php",
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
						$('#customerModal').modal('hide');
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

    $(document).ready(function() {
    $("body").tooltip({ selector: '[data-toggle=tooltip]',placement: 'top' });
});


	$(document).on('click', '.edit_button', function(){

		var customer_id = $(this).data('id');

		$('#customer_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"vehicle_owner_action.php",

	      	method:"POST",

	      	data:{customer_id:customer_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{

	        	$('#customer_email_address').val(data.customer_email_address);
                //$('#customer_password').val(data.customer_password);
                $('#customer_first_name').val(data.customer_first_name);
                $('#customer_last_name').val(data.customer_last_name);
                $('#customer_phone_no').val(data.customer_phone_no);
                $('#customer_address').val(data.customer_address);
                $('#customer_gender').val(data.customer_gender);

	        	$('#modal_title').text('Edit Vehicle Owner');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#customerModal').modal('show');

	        	$('#hidden_id').val(customer_id);

	      	}

	    })

	});

	// $(document).on('click', '.status_button', function(){
	// 	var id = $(this).data('id');
    // 	var status = $(this).data('status');
	// 	var next_status = 'Active';
	// 	if(status == 'Active')
	// 	{
	// 		next_status = 'Inactive';
	// 	}
	// 	if(confirm("Are you sure you want to "+next_status+" it?"))
    // 	{

    //   		$.ajax({

    //     		url:"customer_action.php",

    //     		method:"POST",

    //     		data:{id:id, action:'change_status', status:status, next_status:next_status},

    //     		success:function(data)
    //     		{

    //       			$('#message').html(data);

    //       			dataTable.ajax.reload();

    //       			setTimeout(function(){

    //         			$('#message').html('');

    //       			}, 5000);

    //     		}

    //   		})

    // 	}
	// });

    $(document).on('click', '.view_button', function(){
        var customer_id = $(this).data('id');

        $.ajax({

            url:"vehicle_owner_action.php",

            method:"POST",

            data:{customer_id:customer_id, action:'fetch_single'},

            dataType:'JSON',

            success:function(data)
            {
                var html = '<div class="table-responsive">';
                html += '<table class="table">';

                html += '<tr><th width="40%" class="text-right">Email Address</th><td width="60%">'+data.customer_email_address+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">First Name</th><td width="60%">'+data.customer_first_name+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Last Name</th><td width="60%">'+data.customer_last_name+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Phone No.</th><td width="60%">'+data.customer_phone_no+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Address</th><td width="60%">'+data.customer_address+'</td></tr>';

                html += '</table></div>';

                $('#viewModal').modal('show');

                $('#customer_details').html(html);

            }

        })
    });

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"vehicle_owner_action.php",

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