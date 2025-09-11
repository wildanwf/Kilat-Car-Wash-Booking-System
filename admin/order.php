<?php

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
                    <h1 class="h3 mb-4 text-gray-800">Product Dispenser Management</h1>

                    <div class="row">
                        <div class="col col-sm-4">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">Appointment Number</div>
                                <div class="card-body" id="status">
                                <?php
                                $object->query = "
                                SELECT * FROM appointment_table 
                                WHERE status = 'In Process' 
                                ORDER BY appointment_id ASC
                                ";
                                $table_result = $object->get_result();
                                foreach($table_result as $table)
                                {
                                    echo '
                                    <button type="button" name="table_button" id="table_'.$table["appointment_id"].'" class="btn btn-secondary table_button" data-index="'.$table["appointment_id"].'">'.$table["appointment_number"].'<br />'.$table["table_capacity"].' Person</button>
                                    ';
                                }
                                ?>
                                </div>
                            </div>
                        </div>
                        <div class="col col-sm-8">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">Items</div>
                                <div class="card-body">
                                    <div class="table-responsive" id="order_status">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                include('footer.php');
                ?>

<div id="orderModal" class="modal fade">
    <div class="modal-dialog">
        <form method="post" id="order_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">Add Item</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <span id="form_message"></span>
                    
                    <div class="form-group">
                        <label>Product Name</label>
                        <select name="product_name" id="product_name" class="form-control" required>
                            <?php
                            $object->query = "
                            SELECT * FROM product_table
                            ";
                            $result = $object->get_result();
                            $html = '<option value="">Select Product</option>';
                            foreach($result as $row)
                            {
                                $html .= '<option value="'.$row["product_name"].'" data-price="'.$row["product_price"].'">'.$row["product_name"].'</option>';
                            }
                            echo $html;
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Quantity</label>
                        <select name="product_quantity" id="product_quantity" class="form-control" required>
                            <?php
                            for($i = 1; $i < 25; $i++)
                            {
                                echo '<option value="'.$i.'">'.$i.'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="hidden_appointment_id" id="hidden_appointment_id" />
                    <input type="hidden" name="hidden_order_id" id="hidden_order_id" />
                    <input type="hidden" name="hidden_product_rate" id="hidden_product_rate" />
                    <input type="hidden" name="hidden_appointment_number" id="hidden_appointment_number" />
                    <input type="hidden" name="action" id="action" value="Add" />
                    <input type="submit" name="submit" id="submit_button" class="btn btn-success" value="Add" />
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>

$(document).ready(function(){

    reset_status();

    setInterval(function(){
        reset_status();
    }, 10000);

    function reset_status()
    {
        $.ajax({
            url:"order_action.php",
            method:"POST",
            data:{action:'reset'},
            success:function(data){
                $('#status').html(data);
            }
        });
    }

    function fetch_order_data(order_id)
    {
        $.ajax({
            url:"order_action.php",
            method:"POST",
            data:{action:'fetch_order', order_id:order_id},
            success:function(data)
            {
                $('#order_status').html(data);
            }
        });
    }


    $(document).on('change', '#product_name', function(){
        var rate = $('#product_name').find(':selected').data('price');
        $('#hidden_product_rate').val(rate);
    });

    var button_id = $(this).attr('id');

    $(document).on('click', '.table_button', function(){        
        var appointment_id = $(this).data('index');
        $('#hidden_appointment_id').val(appointment_id);
        $('#hidden_appointment_number').val($(this).data('appointment_number'));
        $('#orderModal').modal('show');
        $('#order_form')[0].reset();
        $('#order_form').parsley().reset();
        $('#submit_button').attr('disabled', false);
        $('#submit_button').val('Add');
        var order_id = $(this).data('order_id');
        $('#hidden_order_id').val(order_id);
        fetch_order_data(order_id);
    });

    $('#product_form').parsley();

    $('#order_form').on('submit', function(event){
        event.preventDefault();
        if($('#order_form').parsley().isValid())
        {
            $.ajax({
                url:"order_action.php",
                method:"POST",
                data:$(this).serialize(),
                beforeSend:function(){
                    $('#submit_button').attr('disabled', 'disabled');
                    $('#submit_button').val('Wait...');
                },
                success:function(data)
                {
                    $('#submit_button').attr('disabled', false);
                    $('#submit_button').val('Add');
                    $('#'+button_id).addClass('btn-primary');
                    $('#'+button_id).removeClass('btn-secondary');
                    $('#order_form')[0].reset();
                    $('#orderModal').modal('hide');
                    fetch_order_data(data);
                }
            }); 
        }
    });

    $(document).ready(function() {
    $("body").tooltip({ selector: '[data-toggle=tooltip]',placement: 'top' });
});

    $(document).on('change', '.product_quantity', function(){
        var quantity = $(this).val();
        var item_id = $(this).data('item_id');
        var order_id = $(this).data('order_id');
        var rate = $(this).data('rate');
        $.ajax({
            url:"order_action.php",
            method:"POST",
            data:{order_id:order_id, item_id:item_id, quantity:quantity, rate:rate, action:'change_quantity'},
            success:function(data)
            {
                fetch_order_data(order_id);
            }
        });
    });

    $(document).on('click', '.remove_item', function(){
        if(confirm("Are you sure you want to remove it?"))
        {
            var item_id = $(this).data('item_id');
            var order_id = $(this).data('order_id');
            $.ajax({
                url:"order_action.php",
                method:"POST",
                data:{order_id:order_id, item_id:item_id, action:'remove_item'},
                success:function(data)
                {
                    fetch_order_data(order_id);
                }
            });
        }
    });

});

</script>