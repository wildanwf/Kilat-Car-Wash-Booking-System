<?php

//service.php

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
                    <h1 class="h3 mb-4 text-gray-800">Services Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Services List</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_service" id="add_service" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="service_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Purpose</th>
                                            <th>Price</th>
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

<div id="serviceModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="service_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Service</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>

        		<div class="modal-body">
        			<span id="form_message"></span>
		          	<div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" name="service_name" id="service_name" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Purpose <span class="text-danger">*</span></label>
                                <input type="text" name="service_purpose" id="service_purpose" class="form-control" required  data-parsley-trigger="keyup" />
                            </div>
		          		</div>
		          	</div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Price<span class="text-danger">*</span></label>
                                <input type="text" name="service_price" id="service_price" class="form-control" required data-parsley-trigger="keyup" />
                            </div>  
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Image <span class="text-danger"></span></label>
                        <br />
                        <input type="file" name="service_profile_image" id="service_profile_image" />
                        <div id="uploaded_image"></div>
                        <input type="hidden" name="hidden_service_profile_image" id="hidden_service_profile_image" />
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
                <h4 class="modal-title" id="modal_title">View Service Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="service_details">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){

	var dataTable = $('#service_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"service_action.php",
			type:"POST",
			data:{action:'fetch'}
		},
		"columnDefs":[
			{
				"targets":[0, 1, 2, 3, 4],
				"orderable":false,
			},
		],
	});

	$('#add_service').click(function(){
		
		$('#service_form')[0].reset();

		$('#service_form').parsley().reset();

    	$('#modal_title').text('Add Service');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#serviceModal').modal('show');

    	$('#form_message').html('');

	});

	$('#service_form').parsley();

	$('#service_form').on('submit', function(event){
		event.preventDefault();
		if($('#service_form').parsley().isValid())
		{		
			$.ajax({
				url:"service_action.php",
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
						$('#serviceModal').modal('hide');
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

	$(document).on('click', '.edit_button', function(){

		var service_id = $(this).data('id');

		$('#service_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"service_action.php",

	      	method:"POST",

	      	data:{service_id:service_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{

	        	$('#service_name').val(data.service_name);
                $('#service_purpose').val(data.service_purpose);
                $('#service_price').val(data.service_price);
                $('#uploaded_image').html('<img src="'+data.service_profile_image+'" class="img-fluid img-thumbnail" width="150" />')
                $('#hidden_service_profile_image').val(data.service_profile_image);

	        	$('#modal_title').text('Edit Service');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#serviceModal').modal('show');

	        	$('#hidden_id').val(service_id);

	      	}

	    })

	});

    $(document).on('click', '.view_button', function(){
        var service_id = $(this).data('id');

        $.ajax({

            url:"service_action.php",

            method:"POST",

            data:{service_id:service_id, action:'fetch_single'},

            dataType:'JSON',

            success:function(data)
            {
                var html = '<div class="table-responsive">';
                html += '<table class="table">';

                html += '<tr><td colspan="2" class="text-center"><img src="'+data.service_profile_image+'" class="img-fluid img-thumbnail" width="150" /></td></tr>';

                html += '<tr><th width="40%" class="text-right">Name</th><td width="60%">'+data.service_name+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Purpose</th><td width="60%">'+data.service_purpose+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Price</th><td width="60%">'+data.service_price+'</td></tr>';

                html += '</table></div>';

                $('#viewModal').modal('show');

                $('#service_details').html(html);

            }

        })
    });

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"service_action.php",

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