<?php
// invoice.php
include('database_connection.php');
$statement = $connect->prepare("
    SELECT * FROM table_invoice ORDER BY invoice_id DESC
");

$statement->execute();

$all_result = $statement->fetchAll();
$total_rows = $statement->rowCount();

$timestamp = time(); // Get current timestamp
$random_number = rand(10000, 99999); // Generate random 6-digit number
$invoice_no = $timestamp . $random_number; // Combine timestamp and random number
$invoice_no = substr($invoice_no, -6); // Extract the last 6 digits


if (isset($_POST["create_invoice"])) {
   
    $statement = $connect->prepare("
        INSERT INTO table_invoice (
            invoice_no, customer_name, customer_contact, customer_address, service_date, service,
            area_covered, service_cost, total_payment, remaining_payment, billing_status
        )
        VALUES (
            :invoice_no, :customer_name, :customer_contact, :customer_address, :service_date, :service,
            :area_covered, :service_cost, :total_payment, :remaining_payment, :billing_status
        )
    ");

    $statement->execute(array(
        ':invoice_no' => $invoice_no,
        ':customer_name' => trim($_POST["customer_name"]),
        ':customer_contact' => trim($_POST["customer_contact"]),
        ':customer_address' => trim($_POST["customer_address"]),
        ':service_date' => trim($_POST["service_date"]),
        ':service' => trim($_POST["service"]),
        ':area_covered' => trim($_POST["area_covered"]),
        ':service_cost' => trim($_POST["service_cost"]),
        ':total_payment' =>  trim($_POST["total_payment"]),
        ':remaining_payment' =>  trim($_POST["remaining_payment"]),
        ':billing_status' => trim($_POST["billing_status"]),
    ));

    $statement = $connect->query("SELECT LAST_INSERT_ID()");
    $invoice_id = $statement->fetchColumn();

    for ($count = 0; $count < $_POST["total_item"]; $count++) {

        $total_payment = $total_payment + floatval(trim($_POST["invoice_paid_amount"][$count]));
        //$remaining_payment = $remaining_payment - floatval(trim($_POST["invoice_remaining_amount"][$count]));

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
            ':invoice_date' => trim($_POST['invoice_date'][$count]),
            ':invoice_description' =>  trim($_POST['invoice_description'][$count]),
            ':invoice_paid_amount' => trim($_POST['invoice_paid_amount'][$count]),
            ':invoice_remaining_amount' => trim($_POST['invoice_remaining_amount'][$count]),
            ':final_total_paid' =>  $total_payment,
        ));
    }

    // $statement = $connect->prepare("
    //     UPDATE table_invoice_records 
    //     SET 
    //     invoice_paid_amount = :invoice_paid_amount,
    //     invoice_remaining_amount = :invoice_remaining_amount,
    //     final_total_paid = :final_total_paid WHERE invoice_id = :invoice_id
    // ");

    // $statement->execute(array(
    //     ':invoice_paid_amount' => $total_payment,
    //     ':invoice_remaining_amount' => $remaining_payment,
    //     ':final_total_paid' => $total_payment,
    //     ':invoice_id' => $invoice_id,
    // ));

    header("location: invoice.php");
}


// Code to Edit form 

if (isset($_POST["update_invoice"])) {
    $invoice_id = $_POST["invoice_id"];

    $statement = $connect->prepare("DELETE FROM table_invoice_records WHERE invoice_id = :invoice_id");
    $statement->execute(array(':invoice_id' => $invoice_id));

    $total_payment = 0;
    for ($count = 0; $count <= $_POST["total_item"]; $count++) {
        $total_payment = $total_payment + floatval(trim($_POST["invoice_paid_amount"][$count]));

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

        $statement->execute(
            array(
                ':invoice_id' => $invoice_id,
                ':invoice_date' => trim($_POST["invoice_date"][$count]),
                ':invoice_description' => trim($_POST["invoice_description"][$count]),
                ':invoice_paid_amount' => trim($_POST["invoice_paid_amount"][$count]),
                ':invoice_remaining_amount' => trim($_POST["invoice_remaining_amount"][$count]),
                ':final_total_paid' => $total_payment,
            )
        );
    }

    $statement = $connect->prepare(
        "
        UPDATE table_invoice 
        SET invoice_no = :invoice_no,
            customer_name = :customer_name,
            customer_contact = :customer_contact,
            customer_address = :customer_address,
            service_date = :service_date,
            service = :service,
            area_covered = :area_covered,
            service_cost = :service_cost,
            total_payment = :total_payment,
            remaining_payment = :remaining_payment,
            billing_status = :billing_status
        WHERE invoice_id = :invoice_id"
    );

    $statement->execute(
        array(
            ':invoice_no' => $invoice_no,
            ':customer_name' => trim($_POST["customer_name"]),
            ':customer_contact' => trim($_POST["customer_contact"]),
            ':customer_address' => trim($_POST["customer_address"]),
            ':service_date' => trim($_POST["service_date"]),
            ':service' => trim($_POST["service"]),
            ':area_covered' => trim($_POST["area_covered"]),
            ':service_cost' => trim($_POST["service_cost"]),
            ':total_payment' => $total_payment,
            ':remaining_payment' => trim($_POST["remaining_payment"]),
            ':billing_status' => trim($_POST["billing_status"]),
            ':invoice_id' => $invoice_id
        )
    );

    header("location:invoice.php");
}

// Code to detele the record 
if(isset($_GET["delete"])&& isset($_GET["id"]))
{
    $statement = $connect-> prepare("DELETE FROM table_invoice WHERE invoice_id = :id");
    $statement->execute(
        array(
            ':id' => $_GET["id"]
        )
    );

    $statement = $connect-> prepare("DELETE FROM table_invoice_records WHERE invoice_id = :id");
    $statement->execute(
        array(
            ':id' => $_GET["id"]
        )
    );

    header("location:invoice.php");
    
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

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>



    <title>Invoice System</title>
</head>

<body>

    <div class="container-fluid">
        <!-- ---php code --- -->
        <?php
        if (isset($_GET["add"])) {
        ?>
            <!-- FORM HERE  -->
            <div class="container">
                <form method="post" id="invoice_form">
                    <div class="table-responsive small">
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
                                            <label for="customer_name" class="col-sm col-form-label fw-bold">Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="customer_name" name="customer_name" placeholder="Customer Name">
                                            </div>
                                        </div>
                                        <div class="form-group row my-3">
                                            <label for="customer_contact" class="col-sm col-form-label fw-bold">Contact</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="customer_contact" name="customer_contact" placeholder="Customer Contact Number">
                                            </div>
                                        </div>
                                        <div class="form-group row my-3">
                                            <label for="customer_address" class="col-sm col-form-label fw-bold">Address</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="customer_address" name="customer_address" placeholder="Customer Address">
                                            </div>
                                        </div>
                                        <hr class="my-4" style="border: 1px solid #16a34a;">

                                        <div class="form-group row my-3">
                                            <label for="service_date" class="col-sm col-form-label fw-bold">Service Date</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control form-control-sm" data-date-format="mm/dd/yyyy" id="service_date" name="service_date" placeholder="Service Date">
                                            </div>
                                        </div>

                                        <fieldset class="form-group ">
                                            <div class="row my-3">
                                                <legend class="col-form-label fw-bold col-sm pt-0">Services</legend>
                                                <div class="col-sm-10">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="service" id="service1" value="Cultivation">
                                                        <label class="form-check-label" for="service1">
                                                            Cultivation
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="service" id="service2" value="Seeding">
                                                        <label class="form-check-label" for="service2">
                                                            Seeding
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="service" id="service3" value="Plough">
                                                        <label class="form-check-label" for="service3">
                                                            Plough
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>

                                        <div class="form-group row my-3">
                                            <label for="area_covered" class="col-sm col-form-label fw-bold">Area (in acre)</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="area_covered" name="area_covered" placeholder="Area Covered in acre">
                                            </div>
                                        </div>
                                        <div class="form-group row my-3">
                                            <label for="service_cost" class="col-sm col-form-label fw-bold">service Cost</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="service_cost" name="service_cost" placeholder="Rs">
                                            </div>
                                        </div>



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
                                                <input type="text" class="form-control form-control-sm number-only invoice_paid_amount" id="invoice_paid_amount<?php echo $m; ?>" name="invoice_paid_amount[]" data-srno="1" placeholder="Amount Paid">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control form-control-sm number-only invoice_remaining_amount" id="invoice_remaining_amount<?php echo $m; ?>" name="invoice_remaining_amount[]" data-srno="1" placeholder="Amount Remaining">
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

                                <td colspan="2">
                                    <hr class="my-4" style="border: 1px solid #16a34a;">
                                    <div class="form-group row my-3">
                                        <label for="total_payment" class="col-sm col-form-label fw-bold">Total Payment</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control form-control-sm" id="total_payment" name="total_payment" placeholder="Rs">
                                        </div>
                                    </div>

                                    <div class="form-group row my-3">
                                        <label for="remaining_payment" class="col-sm col-form-label fw-bold">Remaining Payment</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control form-control-sm" id="remaining_payment" name="remaining_payment" placeholder="Rs">
                                        </div>
                                    </div>

                                    <fieldset class="form-group ">
                                        <div class="row my-3">
                                            <legend class="col-form-label fw-bold col-sm pt-0">Billing Status</legend>
                                            <div class="col-sm-10">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="billing_status" id="billing_status" value="Complete">
                                                    <label class="form-check-label" for="billing_status1">
                                                        Complete
                                                    </label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="billing_status" id="billing_status" value="Active">
                                                    <label class="form-check-label" for="billing_status2">
                                                        Active
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </fieldset>

                                </td>
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

            <!-- <script>
                $(document).ready(function() {
                    var final_total_paid = $('#final_total_paid').text();
                    var count = 1;
                    // Add row when "add_row" button is clicked
                    $(document).on('click', '#add_row', function(e) {
                        e.preventDefault(); // Prevent form submission

                        count = count + 1;
                        $('#total_item').val(count);

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
                    $(document).on('click', '.remove_row', function() {
                        var row_id = $(this).data('rowid');
                        console.log(row_id);

                        var total_payment = $('#invoice_paid_amount' + row_id).val();
                        var final_amount = parseFloat($('#final_total_paid').text());
                        var result_amount = final_amount - parseFloat(total_payment);

                        $('#final_total_paid').text(result_amount.toFixed(2));
                        $('#row_' + row_id).remove();
                        count = count - 1;
                        $('#total_item').val(count);
                    });
                });
            </script> -->

            <script>
                $(document).ready(function() {
                    var final_total_paid = $('#final_total_paid').text();
                    var count = 1;
                    var totalPaidAmount = 0;

                    // Add row when "add_row" button is clicked
                    $(document).on('click', '#add_row', function(e) {
                        e.preventDefault(); // Prevent form submission

                        count++;
                        $('#total_item').val(count);

                        var html = `<tr class="invoice_row" id="row_${count}">
                                      <td><span class="sr_no">${count}</span></td>
                                      <td><input type="date" class="form-control form-control-sm text-center invoice_date" id="invoice_date${count}" name="invoice_date[]" data-srno="${count}" placeholder="Paid on"></td>
                                      <td><input type="text" class="form-control form-control-sm invoice_description" id="invoice_description${count}" name="invoice_description[]" data-srno="${count}" placeholder="Billing Description"></td>
                                      <td><input type="text" class="form-control form-control-sm number-only invoice_paid_amount" id="invoice_paid_amount${count}" name="invoice_paid_amount[]" data-srno="${count}" placeholder="Amount Paid"></td>
                                      <td><input type="text" class="form-control form-control-sm number-only invoice_remaining_amount" id="invoice_remaining_amount${count}" name="invoice_remaining_amount[]" data-srno="${count}" placeholder="Amount Remaining"></td>
                                      <td><button type="button" class="btn btn-xs remove_row text-center fw-bold" data-rowid="${count}" style="color:red;">X</button></td>
                                    </tr>`;

                        $('#invoice_records_table').append(html);
                    });

                    // Remove row
                    $(document).on('click', '.remove_row', function() {
                        var row_id = $(this).data('rowid');
                        var paidAmount = parseFloat($('#invoice_paid_amount' + row_id).val());
                        if (!isNaN(paidAmount)) {
                            totalPaidAmount -= paidAmount;
                        }
                        $('#final_total_paid').text(totalPaidAmount.toFixed(2));

                        $('#row_' + row_id).remove();
                        count--;
                        $('#total_item').val(count);
                    });

                    // Update total paid amount when the paid amount field changes
                    $(document).on('input', '.invoice_paid_amount', function() {
                        totalPaidAmount = 0;
                        $('.invoice_paid_amount').each(function() {
                            var paidAmount = parseFloat($(this).val());
                            if (!isNaN(paidAmount)) {
                                totalPaidAmount += paidAmount;
                            }
                        });
                        $('#final_total_paid').text(totalPaidAmount.toFixed(2));
                    });

                    $('#create_invoice').click(function() {
                        $('#invoice_form').submit();
                    });
                });
            </script>

            <?php
        }
        // Code to edit page is Here
        elseif (isset($_GET["update"]) && isset($_GET["id"])) {
            $statement = $connect->prepare("
            SELECT * FROM table_invoice 
                    WHERE invoice_id = :invoice_id
                    LIMIT 1
            ");

            $statement->execute(
                array(
                    ':invoice_id' => $_GET["id"]
                )
            );

            $result = $statement->fetchAll();
            foreach ($result as $row) {
            ?>
                <script>
                    $(document).ready(function() {
                        $('#invoice_no').val("<?php echo $row['invoice_no']; ?>");
                        $('#customer_name').val("<?php echo $row['customer_name']; ?>");
                        $('#customer_contact').val("<?php echo $row['customer_contact']; ?>");
                        $('#customer_address').val("<?php echo $row['customer_address']; ?>");
                        $('#service_date').val("<?php echo $row['service_date']; ?>");
                        $('#service').val("<?php echo $row['service']; ?>");
                        $('#area_covered').val("<?php echo $row['area_covered']; ?>");
                        $('#service_cost').val("<?php echo $row['service_cost']; ?>");
                        $('#total_payment').val("<?php echo $row['total_payment']; ?>");
                        $('#remaining_payment').val("<?php echo $row['remaining_payment']; ?>");
                        $('#billing_status').val("<?php echo $row['billing_status']; ?>");
                    });
                </script>

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
                                                <label for="customer_name" class="col-sm col-form-label fw-bold">Name</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control form-control-sm" id="customer_name" name="customer_name" placeholder="Customer Name">
                                                </div>
                                            </div>
                                            <div class="form-group row my-3">
                                                <label for="customer_contact" class="col-sm col-form-label fw-bold">Contact</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control form-control-sm" id="customer_contact" name="customer_contact" placeholder="Customer Contact Number">
                                                </div>
                                            </div>
                                            <div class="form-group row my-3">
                                                <label for="customer_address" class="col-sm col-form-label fw-bold">Address</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control form-control-sm" id="customer_address" name="customer_address" placeholder="Customer Address">
                                                </div>
                                            </div>

                                            <hr class="my-4" style="border: 1px solid #16a34a;">

                                            <div class="form-group row my-3">
                                                <label for="service_date" class="col-sm col-form-label fw-bold">Service Date</label>
                                                <div class="col-sm-10">
                                                    <input type="date" class="form-control form-control-sm" data-date-format="mm/dd/yyyy" id="service_date" name="service_date" placeholder="Service Date">
                                                </div>
                                            </div>


                                            <fieldset class="form-group">
                                                <div class="row my-3">
                                                    <legend class="col-form-label fw-bold col-sm pt-0">Service</legend>
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
                                                        </div>
                                                    </div>
                                                </div>
                                            </fieldset>

                                            <div class="form-group row my-3">
                                                <label for="area_covered" class="col-sm col-form-label fw-bold">Area (in acre)</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control form-control-sm" id="area_covered" name="area_covered" placeholder="Area Covered in acre">
                                                </div>
                                            </div>
                                            <div class="form-group row my-3">
                                                <label for="service_cost" class="col-sm col-form-label fw-bold">Diesel Cost</label>
                                                <div class="col-sm-10">
                                                    <input type="text" class="form-control form-control-sm" id="service_cost" name="service_cost" placeholder="Rs">
                                                </div>
                                            </div>


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

                                            <!-- Edited Form Table -->
                                            <?php
                                            $statement = $connect->prepare("
                                            SELECT * FROM table_invoice_records 
                                            WHERE invoice_id = :invoice_id
                                            ");

                                            $statement->execute(
                                                array(
                                                    ':invoice_id' => $_GET["id"]
                                                )
                                            );

                                            $item_result = $statement->fetchAll();
                                            $m = 0;
                                            foreach ($item_result as $sub_row) {
                                                $m = $m + 1;
                                            ?>
                                                <tr>
                                                    <td><span id="sr_no"><?php echo $m; ?></span></td>
                                                    <td>
                                                        <input type="date" class="form-control form-control-sm text-center invoice_date" id="invoice_date<?php echo $m; ?>" name="invoice_date[]" value="<?php echo $sub_row["invoice_date"]; ?>" placeholder="Paid on">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm invoice_description" id="invoice_description<?php echo $m; ?>" name="invoice_description[]" value="<?php echo $sub_row["invoice_description"]; ?>" placeholder="Billing Description">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm number-only invoice_paid_amount" id="invoice_paid_amount<?php echo $m; ?>" name="invoice_paid_amount[]" data-srno="<?php echo $m; ?>" value="<?php echo $sub_row["invoice_paid_amount"]; ?>" placeholder="Amount Paid">
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control form-control-sm number-only invoice_remaining_amount" id="invoice_remaining_amount<?php echo $m; ?>" name="invoice_remaining_amount[]" data-srno="<?php echo $m; ?>" value="<?php echo $sub_row["invoice_remaining_amount"]; ?>" placeholder="Amount Remaining">
                                                    </td>
                                                    <td></td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </table>

                                        <div class="mx-3 text-end">
                                            <button type="submit" name="add_row" id="add_row" class="btn btn-dark btn-xs ">+</button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-end"><b>Total Paid</b></td>
                                    <td><b><span id="final_total_paid"><?php echo $row["total_payment"] ?></span></b></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <hr class="my-4" style="border: 1px solid #16a34a;">

                                        <div class="form-group row my-3">
                                            <label for="total_payment" class="col-sm col-form-label fw-bold">Total Payment</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="total_payment" name="total_payment" placeholder="Rs">
                                            </div>
                                        </div>


                                        <div class="form-group row my-3">
                                            <label for="remaining_payment" class="col-sm col-form-label fw-bold">Remaining Payment</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control form-control-sm" id="remaining_payment" name="remaining_payment" placeholder="Rs">
                                            </div>
                                        </div>

                                        <fieldset class="form-group ">
                                            <div class="row my-3">
                                                <legend class="col-form-label fw-bold col-sm pt-0">Billing Status</legend>
                                                <div class="col-sm-10">
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="billing_status" id="billing_status1" value="Complete">
                                                        <label class="form-check-label" for="billing_status1">
                                                            Complete
                                                        </label>
                                                    </div>
                                                    <div class="form-check form-check-inline">
                                                        <input class="form-check-input" type="radio" name="billing_status" id="billing_status2" value="Active">
                                                        <label class="form-check-label" for="billing_status2">
                                                            Active
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center">
                                        <input type="hidden" name="total_item" id="total_item" value="<?php echo $m; ?>">
                                        <input type="hidden" name="invoice_id" id="invoice_id" value="<?php echo $row["invoice_id"] ?>">
                                        <input type="submit" name="update_invoice" id="create_invoice" class="btn btn-success" value="Create">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </form>
                </div>

                <!-- Javascript here  -->
                <!-- Add a row and delete a row in the EDIT form -->
                <script>
                    $(document).ready(function() {
                        var count = "<?php echo $m; ?>";

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

                            $('#invoice_form').submit();

                        });
                    });
                </script>

            <?php
            }
        } else {

            ?>
            <!-- Home Page  -->
            <h3 class="text-center my-4 fw-bold" style="color:#16a34a">Records of Invoices</h3>
            <hr class="my-4">
            <div class="my-5 text-end mx-3">
                <a href="invoice.php?add=1" class="btn" style="background-color: #16a34a; color:#f8fafc;">create</a>
            </div>

            <!-- Data Table of Invoices and Records   -->
            <table id="data-table" class="table table-bordered table-striped py-3 mx-auto sm">
                <thead>
                    <tr>
                        <th>Invoice No.</th>
                        <th>Customer Name</th>
                        <th>Contact No</th>
                        <th>Address</th>
                        <th>Service Date</th>
                        <th>Service</th>
                        <th>Service Cost</th>
                        <th>Total Payment</th>
                        <th>Remaining Payment</th>
                        <th>Billing Status</th>
                        <th>PDF</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>

                <?php

                if ($total_rows > 0) {
                    foreach ($all_result as $row) {

                        echo '<tr>           
                            <td>' . $row["invoice_no"] . '</td> 
                            <td>' . $row["customer_name"] . '</td> 
                            <td>' . $row["customer_contact"] . '</td> 
                            <td>' . $row["customer_address"] . '</td> 
                            <td>' . date('d/m/Y', strtotime($row["service_date"])) . '</td>    
                            <td>' . $row["service"] . '</td> 
                            <td>' . $row["service_cost"] . '</td> 
                            <td>' . $row["total_payment"] . '</td> 
                            <td>' . $row["remaining_payment"] . '</td> 
                            <td>' . $row["billing_status"] . '</td> 
                            <td><a href="print_invoice.php?pdf=1&id=' . $row["invoice_id"] . '">PDF</a></td> 
                            <td>
                            <a href="invoice.php?update=1&id=' . $row["invoice_id"] . '">
                               Edit
                            </a>
                            </td> 
                            <td class="text-center">
                            <a href="#" id="' . $row["invoice_id"] . '" class="delete fw-bold " style="color:red; text-decoration:none;">
                              X
                            </a>
                            </td> 
                         </tr>';
                    }
                }
                ?>
            </table>
        <?php
        }
        ?>
    </div>

    <!-- dataTable -->
    <script type="text/javascript">
        $(document).ready(function() {
            var table = $('#data-table').DataTable();

            $(document).on('click','.delete', function(){
                var id = $(this).attr('id');
                console.log(id);
                if(confirm("Are you sure you want to delete this ?"))
                {
                    window.location.href ="invoice.php?delete=1&id=" + id;
                }
                else 
                {
                    return false;
                }

            });
        });
    </script>




</body>

</html>