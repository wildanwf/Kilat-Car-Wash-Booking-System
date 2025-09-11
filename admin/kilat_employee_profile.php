<?php

include('../class/Appointment.php');

$object = new Appointment;

if(!$object->is_login())
{
    header("location:".$object->base_url."");
}

if($_SESSION['type'] != 'employee')
{
    header("location:".$object->base_url."");
}

$object->query = "
    SELECT * FROM employee_table
    WHERE employee_id = '".$_SESSION["admin_id"]."'
    ";

$result = $object->get_result();

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Profile</h1>

                    <!-- DataTales Example -->
                    
                    <form method="post" id="profile_form" enctype="multipart/form-data">
                        <div class="row"><div class="col-md-10"><span id="message"></span><div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="m-0 font-weight-bold text-primary">Profile</h6>
                                    </div>
                                    <div clas="col" align="right">
                                        <input type="hidden" name="action" value="employee_profile" />
                                        <input type="hidden" name="hidden_id" id="hidden_id" />
                                        <button type="submit" name="edit_button" id="edit_button" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i> Edit</button>
                                        &nbsp;&nbsp;
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!--<div class="row">
                                    <div class="col-md-6">!-->
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
                                                    <label>Phone No. <span class="text-danger">*</span></label>
                                                    <input type="text" name="employee_phone_no" id="employee_phone_no" class="form-control" required  data-parsley-trigger="keyup" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <label>Address </label>
                                                    <input type="text" name="employee_address" id="employee_address" class="form-control" />
                                                </div>
                                                <div class="col-md-6">
                                                    <label>Date of birth</label>
                                                    <input type="text" name="employee_date_of_birth" id="employee_date_of_birth" readonly class="form-control" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <!-- <div class="col-md-6">
                                                    <label>employee Degree <span class="text-danger">*</span></label>
                                                    <input type="text" name="employee_degree" id="employee_degree" class="form-control" required data-parsley-trigger="keyup" />
                                                </div> -->
                                                <div class="col-md-6">
                                                    <label>Speciality <span class="text-danger">*</span></label>
                                                    <input type="text" name="employee_expert_in" id="employee_expert_in" class="form-control" required  data-parsley-trigger="keyup" />
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
                                    <!--</div>
                                </div>!-->
                            </div>
                        </div></div></div>
                    </form>
                <?php
                include('footer.php');
                ?>

<script>
$(document).ready(function(){

    $('#employee_date_of_birth').datepicker({
        format: "yyyy-mm-dd",
        autoclose: true
    });

    <?php
    foreach($result as $row)
    {
    ?>
    $('#hidden_id').val("<?php echo $row['employee_id']; ?>");
    $('#employee_email_address').val("<?php echo $row['employee_email_address']; ?>");
    $('#employee_password').val("<?php echo $row['employee_password']; ?>");
    $('#employee_name').val("<?php echo $row['employee_name']; ?>");
    $('#employee_phone_no').val("<?php echo $row['employee_phone_no']; ?>");
    $('#employee_address').val("<?php echo $row['employee_address']; ?>");
    $('#employee_date_of_birth').val("<?php echo $row['employee_date_of_birth']; ?>");
    
    $('#employee_expert_in').val("<?php echo $row['employee_expert_in']; ?>");
    
    $('#uploaded_image').html('<img src="<?php echo $row["employee_profile_image"]; ?>" class="img-thumbnail" width="100" /><input type="hidden" name="hidden_employee_profile_image" value="<?php echo $row["employee_profile_image"]; ?>" />');

    $('#hidden_employee_profile_image').val("<?php echo $row['employee_profile_image']; ?>");
    <?php
    }
    ?>

    $('#employee_profile_image').change(function(){
        var extension = $('#employee_profile_image').val().split('.').pop().toLowerCase();
        if(extension != '')
        {
            if(jQuery.inArray(extension, ['png','jpg']) == -1)
            {
                alert("Invalid Image File");
                $('#employee_profile_image').val('');
                return false;
            }
        }
    });

    $('#profile_form').parsley();

	$('#profile_form').on('submit', function(event){
		event.preventDefault();
		if($('#profile_form').parsley().isValid())
		{		
			$.ajax({
				url:"kilat_employee_profile_action.php",
				method:"POST",
				data:new FormData(this),
                dataType:'json',
                contentType:false,
                processData:false,
				beforeSend:function()
				{
					$('#edit_button').attr('disabled', 'disabled');
					$('#edit_button').html('wait...');
				},
				success:function(data)
				{
					$('#edit_button').attr('disabled', false);
                    $('#edit_button').html('<i class="fas fa-edit"></i> Edit');

                    $('#employee_email_address').val(data.employee_email_address);
                    $('#employee_password').val(data.employee_password);
                    $('#employee_name').val(data.employee_name);
                    $('#employee_phone_no').val(data.employee_phone_no);
                    $('#employee_address').text(data.employee_address);
                    $('#employee_date_of_birth').text(data.employee_date_of_birth);
                    // $('#employee_degree').text(data.employee_degree);
                    $('#employee_expert_in').text(data.employee_expert_in);
                    if(data.employee_profile_image != '')
                    {
                        $('#uploaded_image').html('<img src="'+data.employee_profile_image+'" class="img-thumbnail" width="100" />');

                        $('#user_profile_image').attr('src', data.employee_profile_image);
                    }

                    $('#hidden_employee_profile_image').val(data.employee_profile_image);
						
                    $('#message').html(data.success);

					setTimeout(function(){

				        $('#message').html('');

				    }, 5000);
				}
			})
		}
	});

});
</script>
