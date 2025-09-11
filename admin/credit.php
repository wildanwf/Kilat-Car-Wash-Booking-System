<?php

include('../class/Appointment.php');

$object = new Appointment;

if(!$object->is_login())
{
    header("location:".$object->base_url."");
}

if($_SESSION['type'] != 'Admin')
{
    header("location:".$object->base_url."");
}


$object->query = "
SELECT * FROM admin_table
WHERE admin_id = '".$_SESSION["admin_id"]."'
";

$result = $object->get_result();

include('header.php');

?>

                    <!-- Page Heading -->
                    <h1 class="h3 mb-4 text-gray-800">Kilat Car Wash</h1>

                    <!-- DataTales Example -->
                    
                    <form method="post" id="profile_form" enctype="multipart/form-data">
                        <div class="row"><div class="col-md-8"><span id="message"></span><div class="card shadow mb-4">
                            <div class="card-header py-3">
                                <div class="row">
                                    <div class="col">
                                        <h6 class="m-0 font-weight-bold text-primary">Credits / Acknowledgments</h6>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="card-body">
                            <p>
                                Thank you to <a href="https://www.webslesson.info/p/contact-us.html"><b>Weblessons</b></a> for providing 
                                helpful discussions and tutorials in programming. Webslesson is a web development programming 
                                blog make programming tutorials and web development tutorials.
                            </p>
                            <p>
                                We would also like to acknowledge <a href="https://uideck.com"><b>UIDeck</b></a>. UIDeck provides Free and Premium HTML Landing Page Templates,
                                 Bootstrap Themes, React Templates, Tailwind Templates, HTML Site Templates and UI Kits.
                            </p>
                            </div>
                        </div></div></div>
                    </form>
                <?php
                include('footer.php');
                ?>

<script>
$(document).ready(function(){

    <?php
    foreach($result as $row)
    {
    ?>
    $('#admin_email_address').val("<?php echo $row['admin_email_address']; ?>");
    $('#admin_password').val("<?php echo $row['admin_password']; ?>");
    $('#admin_name').val("<?php echo $row['admin_name']; ?>");
    $('#branch_name').val("<?php echo $row['branch_name']; ?>");
    $('#branch_address').val("<?php echo $row['branch_address']; ?>");
    $('#branch_contact_no').val("<?php echo $row['branch_contact_no']; ?>");
    <?php
        if($row['branch_logo'] != '')
        {
    ?>
    $("#uploaded_branch_logo").html("<img src='<?php echo $row["branch_logo"]; ?>' class='img-thumbnail' width='100' /><input type='hidden' name='hidden_branch_logo' value='<?php echo $row['branch_logo']; ?>' />");

    <?php
        }
        else
        {
    ?>
    $("#uploaded_branch_logo").html("<input type='hidden' name='hidden_branch_logo' value='' />");
    <?php
        }
    }
    ?>

    $('#profile_form').parsley();

	$('#profile_form').on('submit', function(event){
		event.preventDefault();
		if($('#profile_form').parsley().isValid())
		{		
			$.ajax({
				url:"credit_action.php",
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

                    if(data.error != '')
                    {
                        $('#message').html(data.error);
                    }
                    else
                    {

                        $('#admin_email_address').val(data.admin_email_address);
                        $('#admin_password').val(data.admin_password);
                        $('#admin_name').val(data.admin_name);

                        $('#branch_name').val(data.branch_name);
                        $('#branch_address').val(data.branch_address);
                        $('#branch_contact_no').val(data.branch_contact_no);

                        if(data.branch_logo != '')
                        {
                            $("#uploaded_branch_logo").html("<img src='"+data.branch_logo+"' class='img-thumbnail' width='100' /><input type='hidden' name='hidden_branch_logo' value='"+data.branch_logo+"'");
                        }
                        else
                        {
                            $("#uploaded_branch_logo").html("<input type='hidden' name='hidden_branch_logo' value='"+data.branch_logo+"'");
                        }

                        $('#message').html(data.success);

    					setTimeout(function(){

    				        $('#message').html('');

    				    }, 5000);
                    }
				}
			})
		}
	});

});
</script>