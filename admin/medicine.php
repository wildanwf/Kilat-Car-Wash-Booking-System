<?php

//medicine.php

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
                    <h1 class="h3 mb-4 text-gray-800">Medicines Management</h1>

                    <!-- DataTales Example -->
                    <span id="message"></span>
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                        	<div class="row">
                            	<div class="col">
                            		<h6 class="m-0 font-weight-bold text-primary">Medicines List</h6>
                            	</div>
                            	<div class="col" align="right">
                            		<button type="button" name="add_med" id="add_med" class="btn btn-success btn-circle btn-sm"><i class="fas fa-plus"></i></button>
                            	</div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="med_table" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Name</th>
                                            <th>Purpose</th>
                                            <th>Price</th>
                                            <th>Per</th>
                                            <th>Unit</th>
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

<div id="medModal" class="modal fade">
  	<div class="modal-dialog">
    	<form method="post" id="med_form">
      		<div class="modal-content">
        		<div class="modal-header">
          			<h4 class="modal-title" id="modal_title">Add Medicine</h4>
          			<button type="button" class="close" data-dismiss="modal">&times;</button>
        		</div>
        		<div class="modal-body">
        			<span id="form_message"></span>
		          	<div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Name <span class="text-danger">*</span></label>
                                <input type="text" name="med_name" id="med_name" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Purpose <span class="text-danger">*</span></label>
                                <input type="text" name="med_purpose" id="med_purpose" class="form-control" required  data-parsley-trigger="keyup" />
                            </div>
		          		</div>
		          	</div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Price<span class="text-danger">*</span></label>
                                <input type="text" name="med_price" id="med_price" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                            <div class="col-md-6">
                                <label>Per<span class="text-danger">*</span></label>
                                <input type="text" name="med_price_per" id="med_price_per" class="form-control" required data-parsley-trigger="keyup" />
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Unit<span class="text-danger"></span></label>
                                <!-- <input type="text" name="patient_gender" id="patient_gender" class="form-control" required  data-parsley-trigger="keyup" /> -->
                                <select id="med_price_per_unit" name="med_price_per_unit" class="form-control" required  data-parsley-trigger="keyup">
                                    <option value="Liter">l</option>
                                    <option value="Milliliter">ml</option>
                                    <option value="Kilogram">kg</option>
                                    <option value="Milligram">mg</option>
                                </select>
                            </div>
                        </div>
                    </div> 
                    <div class="form-group">
                        <label>Image <span class="text-danger"></span></label>
                        <br />
                        <input type="file" name="med_profile_image" id="med_profile_image" />
                        <div id="uploaded_image"></div>
                        <input type="hidden" name="hidden_med_profile_image" id="hidden_med_profile_image" />
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
                <h4 class="modal-title" id="modal_title">View Medicine Details</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="med_details">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){

	var dataTable = $('#med_table').DataTable({
		"processing" : true,
		"serverSide" : true,
		"order" : [],
		"ajax" : {
			url:"medicine_action.php",
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

	$('#add_med').click(function(){
		
		$('#med_form')[0].reset();

		$('#med_form').parsley().reset();

    	$('#modal_title').text('Add Medicine');

    	$('#action').val('Add');

    	$('#submit_button').val('Add');

    	$('#medModal').modal('show');

    	$('#form_message').html('');

	});

	$('#med_form').parsley();

	$('#med_form').on('submit', function(event){
		event.preventDefault();
		if($('#med_form').parsley().isValid())
		{		
			$.ajax({
				url:"medicine_action.php",
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
						$('#medModal').modal('hide');
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

		var med_id = $(this).data('id');

		$('#med_form').parsley().reset();

		$('#form_message').html('');

		$.ajax({

	      	url:"medicine_action.php",

	      	method:"POST",

	      	data:{med_id:med_id, action:'fetch_single'},

	      	dataType:'JSON',

	      	success:function(data)
	      	{

	        	$('#med_name').val(data.med_name);
                $('#med_purpose').val(data.med_purpose);
                $('#med_price').val(data.med_price);
                $('#med_price_per').val(data.med_price_per);
                $('#uploaded_image').html('<img src="'+data.med_profile_image+'" class="img-fluid img-thumbnail" width="150" />')
                $('#hidden_med_profile_image').val(data.med_profile_image);
                $('#med_price_per_unit').val(data.med_price_per_unit);

	        	$('#modal_title').text('Edit Medicine');

	        	$('#action').val('Edit');

	        	$('#submit_button').val('Edit');

	        	$('#medModal').modal('show');

	        	$('#hidden_id').val(med_id);

	      	}

	    })

	});

    $(document).on('click', '.view_button', function(){
        var med_id = $(this).data('id');

        $.ajax({

            url:"medicine_action.php",

            method:"POST",

            data:{med_id:med_id, action:'fetch_single'},

            dataType:'JSON',

            success:function(data)
            {
                var html = '<div class="table-responsive">';
                html += '<table class="table">';

                html += '<tr><td colspan="2" class="text-center"><img src="'+data.med_profile_image+'" class="img-fluid img-thumbnail" width="150" /></td></tr>';

                html += '<tr><th width="40%" class="text-right">Name</th><td width="60%">'+data.med_name+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Purpose</th><td width="60%">'+data.med_purpose+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Price</th><td width="60%">'+data.med_price+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Per</th><td width="60%">'+data.med_price_per+'</td></tr>';

                html += '<tr><th width="40%" class="text-right">Unit</th><td width="60%">'+data.med_price_per_unit+'</td></tr>';

                html += '</table></div>';

                $('#viewModal').modal('show');

                $('#med_details').html(html);

            }

        })
    });

	$(document).on('click', '.delete_button', function(){

    	var id = $(this).data('id');

    	if(confirm("Are you sure you want to remove it?"))
    	{

      		$.ajax({

        		url:"medicine_action.php",

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