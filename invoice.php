<?php
// invoice.php
include('database_connection.php');
$statement = $connect->prepare("
    SELECT * FROM table_invoice ORDER BY invoice_id DESC
");

$statement->execute();

$all_result = $statement->fetchAll();
$total_rows = $statement->rowCount();

if (isset($_POST["create_invoice"])) {
    $statement = $connect->prepare("
        INSERT INTO table_invoice (
            customer_name, customer_contact, customer_address, service_date, service,
            area_covered, diesel_cost, total_payment, diesel_payment_status, remaining_payment,
            billing_status
        )
        VALUES (
            :customer_name, :customer_contact, :customer_address, :service_date, :service,
            :area_covered, :diesel_cost, :total_payment, :diesel_payment_status, :remaining_payment,
            :billing_status
        )
    ");

    $statement->execute(array(
        // ':invoice_no' => $invoiceNumber,
        ':customer_name' => trim($_POST["customer_name"]),
        ':customer_contact' => trim($_POST["customer_contact"]),
        ':customer_address' => trim($_POST["customer_address"]),
        ':service_date' => trim($_POST["service_date"]),
        ':service' => trim($_POST["service"]),
        ':area_covered' => trim($_POST["area_covered"]),
        ':diesel_cost' => trim($_POST["diesel_cost"]),
        ':total_payment' => trim($_POST["total_payment"]),
        ':diesel_payment_status' => trim($_POST["diesel_payment_status"]),
        ':remaining_payment' => trim($_POST["remaining_payment"]),
        ':billing_status' => trim($_POST["billing_status"]),
    ));

    $invoice_id = $connect->lastInsertId();

    for ($count = 0; $count < $_POST["total_item"]; $count++) {
        $invoice_date = floatval(trim($_POST['invoice_date'][$count]));
        $invoice_description = trim($_POST['invoice_description'][$count]);
        $invoice_paid_amount = floatval(trim($_POST['invoice_paid_amount'][$count]));
        $invoice_remaining_amount = floatval(trim($_POST['invoice_remaining_amount'][$count]));
        $final_total_paid = $invoice_paid_amount - $invoice_remaining_amount;

        $statement = $connect->prepare("
            INSERT INTO table_invoice_records (
                invoice_id, invoice_date, invoice_description, invoice_paid_amount,
                invoice_remaining_amount, final_total_paid
            )
            VALUES (
                :invoice_id, :invoice_date, :invoice_description, :invoice_paid_amount,
                :invoice_remaining_amount, :final_total_paid
            )
        ");

        $statement->execute(array(
            ':invoice_id' => $invoice_id,
            ':invoice_date' => $invoice_date,
            ':invoice_description' => $invoice_description,
            ':invoice_paid_amount' => $invoice_paid_amount,
            ':invoice_remaining_amount' => $invoice_remaining_amount,
            ':final_total_paid' => $final_total_paid,
        ));
    }

    $statement = $connect->prepare("
        UPDATE table_invoice_records SET final_total_paid = :final_total_paid WHERE invoice_id = :invoice_id
    ");

    $statement->execute(array(
        ':final_total_paid' => trim($_POST["final_total_paid"]),
        ':invoice_id' => $invoice_id,
    ));

    header("location: invoice.php");
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <title>Invoice System</title>
</head>

<body>

    <!-- -------- -------- Navbar comes here---------------- -->

    <!-- -------------------------- ---------------------------->

    <div class="container-fluid">
        <!-- ---php code --- -->
        <?php
        if (isset($_GET["add"])) {
        ?>

            <!-- FORM HERE  -->
            <div class="container">
                <form method="post" id="invoice_form">
                    <div class="table-responsive small ">
                        <table class="table table-bordered mx-auto" style="width: 800px;">
                            <tr>
                                <td colspan="2" class="text-center justify-content-center">
                                    <h3 class="fw-bold my-3" style="color: #16a34a;">Create Invoice</h3>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div class="container-fluid mx-auto" style="width:800px;">
                                        <div class="form-group row my-3">
                                            <label for="customer_name" class="col-sm-2 col-form-label fw-bold">Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="customer_name" name="customer_name" placeholder="Customer Name">
                                            </div>
                                        </div>
                                        <div class="form-group row my-3">
                                            <label for="customer_contact" class="col-sm-2 col-form-label fw-bold">Contact</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="customer_contact" name="customer_contact" placeholder="Customer Contact Number">
                                            </div>
                                        </div>
                                        <div class="form-group row my-3">
                                            <label for="customer_address" class="col-sm-2 col-form-label fw-bold">Address</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="customer_address" name="customer_address" placeholder="Customer Address">
                                            </div>
                                        </div>

                                        <hr class="my-4" style="border: 1px solid #16a34a;">

                                        <div class="form-group row my-3">
                                            <label for="service_date" class="col-sm-2 col-form-label fw-bold">Service Date</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control form-control-sm" data-date-format="mm/dd/yyyy" id="service_date" name="service_date" placeholder="Service Date">
                                            </div>
                                        </div>


                                        <fieldset class="form-group">
                                            <div class="row my-3">
                                                <legend class="col-form-label fw-bold col-sm-2 pt-0">Service</legend>
                                                <div class="col-sm-10">
                                                    <div class="form-check form-check-inline">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="service" id="service1" value="Cultivation" checked>
                                                            <label class="form-check-label" for="service1">
                                                                Cultivation
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="service" id="service2" value="Seeding">
                                                            <label class="form-check-label" for="service2">
                                                                Seeding
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="service" id="service3" value="Plough">
                                                            <label class="form-check-label" for="service3">
                                                                Plough
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="service" id="service4" value="Threser">
                                                            <label class="form-check-label" for="service4">
                                                                Threser
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="service" id="service5" value="Trolley">
                                                            <label class="form-check-label" for="service5">
                                                                Trolley
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="service" id="service6" value="Tirri Plough">
                                                            <label class="form-check-label" for="service6">
                                                                Tirri Plough
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" name="service" id="service7" value="Panja Plough">
                                                            <label class="form-check-label" for="service7">
                                                                Panja Plough
                                                            </label>
                                                        </div>
                                                        <div class="form-check" disabled>
                                                            <input class="form-check-input" type="checkbox" name="service" id="service8" value="option3" disabled>
                                                            <label class="form-check-label" for="service8">
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <div class="form-group row my-3">
                                            <label for="area_covered" class="col-sm-2 col-form-label fw-bold">Area (in acre)</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="area_covered" name="area_covered" placeholder="Area Covered in acre">
                                            </div>
                                        </div>
                                        <div class="form-group row my-3">
                                            <label for="diesel_cost" class="col-sm-2 col-form-label fw-bold">Diesel Cost</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="diesel_cost" name="diesel_cost" placeholder="Rs">
                                            </div>
                                        </div>

                                        <hr class="my-4" style="border: 1px solid #16a34a;">

                                        <div class="form-group row my-3">
                                            <label for="total_payment" class="col-sm-2 col-form-label fw-bold">Total Payment</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="total_payment" name="total_payment" placeholder="Rs">
                                            </div>
                                        </div>

                                        <fieldset class="form-group ">
                                            <div class="row my-3">
                                                <legend class="col-form-label fw-bold col-sm-2 pt-0">Diesel Payment</legend>
                                                <div class="col-sm-10">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="diesel_payment_status" id="diesel_payment_status1" value="Paid">
                                                        <label class="form-check-label" for="diesel_payment_status1">
                                                            Paid
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="diesel_payment_status" id="diesel_payment_status2" value="Not Paid">
                                                        <label class="form-check-label" for="diesel_payment_status2">
                                                            Not Paid
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>


                                        <div class="form-group row my-3">
                                            <label for="remaining_payment" class="col-sm-2 col-form-label fw-bold">Remaining Payment</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="remaining_payment" name="remaining_payment" placeholder="Rs">
                                            </div>
                                        </div>

                                        <fieldset class="form-group ">
                                            <div class="row my-3">
                                                <legend class="col-form-label fw-bold col-sm-2 pt-0">Billing Status</legend>
                                                <div class="col-sm-10">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="billing_status" id="billing_status1" value="Incomplete">
                                                        <label class="form-check-label" for="billing_status1">
                                                            Incomplete
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="billing_status" id="billing_status2" value="Partial">
                                                        <label class="form-check-label" for="billing_status2">
                                                            Partial
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="billing_status" id="billing_status3" value="Complete">
                                                        <label class="form-check-label" for="billing_status3">
                                                            Complete
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                        <hr class="my-4" style="border: 1px solid #16a34a;">
                                    </div>

                                    <h5 class="text-center fw-semibold" style="color :  #16a34a">INVOICE TABLE</h5>
                                    <table class="table table-bordered mx-auto my-4" id="invoice_records_table" style="width:800px;">
                                        <tr>
                                            <th>Sno</th>
                                            <th>Date</th>
                                            <th>Description</th>
                                            <th>Paid</th>
                                            <th>Remaining</th>
                                        </tr>
                                        <tr>
                                            <td><span id="sr_no">1</span></td>
                                            <td>
                                                <input type="date" class="form-control form-control-sm text-center invoice_date" id="invoice_date1" name="invoice_date[]" data-srno="1" placeholder="Paid on">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm invoice_description" id="invoice_description1" name="invoice_description[]" data-srno="1" placeholder="Billing Description">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm number-only invoice_paid_amount" id="invoice_paid_amount1" name="invoice_paid_amount[]" data-srno="1" placeholder="Amount Paid">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm number-only invoice_remaining_amount" id="invoice_remaining_amount1" name="invoice_remaining_amount[]" data-srno="1" placeholder="Amount Remaining">
                                            </td>
                                        </tr>
                                    </table>
                                    <div class="mx-3 text-end">
                                        <button type="submit" name="add_row" id="add_row" class="btn btn-dark btn-xs ">+</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-end"><b>Total Paid</b></td>
                                <td><b><span id="final_total_paid" name="final_total_paid"></span></b></td>
                            </tr>
                            <tr>
                                <td colspan="2"></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="text-center">
                                    <input type="hidden" name="total_item" id="total_item" value="1">
                                    <input type="submit" name="create_invoice" id="create_invoice" class="btn btn-success" value="Create">
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>
            </div>



            <!-- Javascript here  -->
            <!-- Add a row and delete a row in the form -->
            <script>
                $(document).ready(function() {
                    var count = 1;

                    // Add row when "add_row" button is clicked
                    $(document).on('click', '#add_row', function(e) {
                        e.preventDefault(); // Prevent form submission

                        count++;

                        var html = `<tr class="invoice_row">
                   <td><span class="sr_no">${count}</span></td>
                   <td><input type="date" class="form-control form-control-sm text-center invoice_date" id="invoice_date${count}" name="invoice_date[]" data-srno="${count}" placeholder="Paid on"></td>
                   <td><input type="text" class="form-control form-control-sm invoice_description" id="invoice_description${count}" name="invoice_description[]" data-srno="${count}" placeholder="Billing Description"></td>
                   <td><input type="text" class="form-control form-control-sm number-only invoice_paid_amount" id="invoice_paid_amount${count}" name="invoice_paid_amount[]" data-srno="${count}" placeholder="Amount Paid"></td>
                   <td><input type="text" class="form-control form-control-sm number-only invoice_remaining_amount" id="invoice_remaining_amount${count}" name="invoice_remaining_amount[]" data-srno="${count}" placeholder="Amount Remaining"></td>
                   <td><button type="button" class="btn btn-xs remove_row text-center fw-bold" style="color:red;">X</button></td>
               </tr>`;

                        $('#invoice_records_table').append(html);
                    });

                    // Remove row
                    $(document).on('click', '.remove_row', function() {
                        $(this).closest('.invoice_row').remove();
                        updateTotalPaidAmount();
                    });

                    // Update total paid amount when the paid amount is changed
                    $(document).on('input', '.invoice_paid_amount', function() {
                        updateTotalPaidAmount();
                    });

                    // Function to update the total paid amount
                    function updateTotalPaidAmount() {
                        var totalPaid = 0;
                        $('.invoice_paid_amount').each(function() {
                            var paidAmount = parseFloat($(this).val()) || 0;
                            totalPaid += paidAmount;
                        });
                        $('#final_total_paid').text(totalPaid);
                    }

                    $('#create_invoice').click(function() {
                        if ($.trim($('#customer_name').val()).length == 0) {
                            alert("Please Enter Customer Name");
                            return false;
                        }

                        if ($.trim($('#service_date').val()).length == 0) {
                            alert("Please Enter Service date");
                            return false;
                        }

                        // if($.trim($('#customer_name').val()).length==0){
                        //     alert("Please Enter Customer Name");
                        //     return false;
                        // }

                        // for (var no = 1; no <= count; no++) {
                           

                        //     if ($.trim($('#invoice_description' + no).val()).length == 0) {
                        //         alert("Please enter description");
                        //         $('#invoice_description' + no).focus();
                        //         return false;
                        //     }

                        //     if ($.trim($('#invoice_paid_amount' + no).val()).length == 0) {
                        //         alert("Please enter Paid amount");
                        //         $('#invoice_paid_amount' + no).focus();
                        //         return false;
                        //     }

                        // }

                        $('#invoice_form').submit();

                    });
                });
            </script>





        <?php
        } else {


        ?>
            <!-- --------------- -->
            <h3 class="text-center my-4 fw-bold" style="color:#16a34a">Records of Invoices</h3>
            <hr class="my-4">
            <div class="my-5 text-end mx-3">
                <a href="invoice.php?add=1" class="btn btn-success">Create</a>
            </div>
            <table id="data-table sm" class="table table-bordered table-striped py-3">
                <thead>
                    <tr>
                        <th>Sr No.</th>
                        <th>Customer Name</th>
                        <th>Contact No</th>
                        <th>Address</th>
                        <th>Service Date</th>
                        <th>Service</th>
                        <th>Area Covered</th>
                        <th>Diesel cost</th>
                        <th>Diesel Payment</th>
                        <th>Total Payment</th>
                        <th>Remaining Payment</th>
                        <th>Billing Status</th>
                        <th>PDF</th>
                        <th>Edit</th>
                    </tr>
                </thead>

                <?php
               
               if ($total_rows > 0) {
                $invoiceNumber = 1; // Initialize the invoice number
            
                foreach ($all_result as $row) {
                    echo '<tr>
                        <td>' . $invoiceNumber . '</td> 
                        <td>' . $row["customer_name"] . '</td> 
                        <td>' . $row["customer_contact"] . '</td> 
                        <td>' . $row["customer_address"] . '</td> 
                        <td>' . date('d/m/Y', strtotime($row["service_date"])) . '</td>    
                        <td>' . $row["service"] . '</td> 
                        <td>' . $row["area_covered"] . '</td> 
                        <td>' . $row["diesel_cost"] . '</td>  
                        <td>' . $row["diesel_payment_status"] . '</td> 
                        <td>' . $row["total_payment"] . '</td> 
                        <td>' . $row["remaining_payment"] . '</td> 
                        <td>' . $row["billing_status"] . '</td> 
                        <td><a href="print_invoice.php?pdf=1&id=' . $row["invoice_id"] . '">PDF</a></td> 
                        <td><a href="invoice.php?update=1&id=' . $row["invoice_id"] . '">Edit</a></td> 
                    </tr>';
            
                    $invoiceNumber++; // Increment the invoice number
                }
            }
            
                ?>

            </table>
        <?php
        }
        ?>
    </div>


    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#data-table').DataTable();

        });
    </script>


</body>

</html>