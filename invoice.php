<?php
// invoice.php
include('database_connection.php');

$statement = $connect->prepare("
   SELECT * FROM table_invoice ORDER BY invoice_id DESC
");

$statement->execute();

$all_result = $statement->fetchAll();
$total_rows = $statement->rowCount();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Jquery JS Datatables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">

    <title>Invoice System</title>
</head>

<body>
    <div class="container-fluid">
        <!-- ---php code --- -->
        <?php
        if (isset($_GET["add"])) {
        ?>
            <div class="container mx-auto" style="max-width:800px">
                <h4 class="fw-bold text-center my-4">Create Records</h4>
                <hr class="my-4" style="border: 1px solid #16a34a;">
                <form method="post" id="invoice_form">
                    <div class="form-group row my-3">
                        <label for="customer_name" class="col-sm-2 col-form-label fw-bold">Name</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="customer_name" name="customer_name" placeholder="Customer Name">
                        </div>
                    </div>
                    <div class="form-group row my-3">
                        <label for="customer_contact" class="col-sm-2 col-form-label fw-bold">Contact</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="customer_contact" name="customer_contact" placeholder="Customer Contact Number">
                        </div>
                    </div>
                    <div class="form-group row my-3">
                        <label for="customer_address" class="col-sm-2 col-form-label fw-bold">Address</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="customer_address" name="customer_address" placeholder="Customer Address">
                        </div>
                    </div>

                    <hr class="my-4" style="border: 1px solid #16a34a;">

                    <div class="form-group row my-3">
                        <label for="service_date" class="col-sm-2 col-form-label fw-bold">Service Date</label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" data-date-format="mm/dd/yyyy" id="service_date" name="service_date" placeholder="Service Date">
                        </div>
                    </div>


                    <fieldset class="form-group">
                        <div class="row my-3">
                            <legend class="col-form-label fw-bold col-sm-2 pt-0">Service</legend>
                            <div class="col-sm-10">
                                <div class="form-check form-check-inline">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service" id="service1" value="option1" checked>
                                        <label class="form-check-label" for="service1">
                                            Cultivation
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service" id="service2" value="option2">
                                        <label class="form-check-label" for="service2">
                                            Seeding
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check form-check-inline">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service" id="service3" value="option3">
                                        <label class="form-check-label" for="service3">
                                            Plough
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service" id="service4" value="option3">
                                        <label class="form-check-label" for="service4">
                                            Threser
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check form-check-inline">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service" id="service5" value="option3">
                                        <label class="form-check-label" for="service5">
                                            Trolley
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service" id="service6" value="option3">
                                        <label class="form-check-label" for="service6">
                                            Tirri Plough
                                        </label>
                                    </div>
                                </div>
                                <div class="form-check form-check-inline">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="service" id="service7" value="option3">
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
                            <input type="text" class="form-control" id="area_covered" name="area_covered" placeholder="Area Covered in acre">
                        </div>
                    </div>
                    <div class="form-group row my-3">
                        <label for="diesel_cost" class="col-sm-2 col-form-label fw-bold">Diesel Cost</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="diesel_cost" name="diesel_cost" placeholder="Rs">
                        </div>
                    </div>

                    <hr class="my-4" style="border: 1px solid #16a34a;">

                    <div class="form-group row my-3">
                        <label for="total_payment" class="col-sm-2 col-form-label fw-bold">Total Payment</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="total_payment" name="total_payment" placeholder="Rs">
                        </div>
                    </div>

                    <fieldset class="form-group ">
                        <div class="row my-3">
                            <legend class="col-form-label fw-bold col-sm-2 pt-0">Diesel Payment</legend>
                            <div class="col-sm-10">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="diesel_payment_status" id="diesel_payment_status1" value="option3">
                                    <label class="form-check-label" for="diesel_payment_status1">
                                        Paid
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="diesel_payment_status" id="diesel_payment_status2" value="option3">
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
                            <input type="text" class="form-control" id="remaining_payment" name="remaining_payment" placeholder="Rs">
                        </div>
                    </div>

                    <fieldset class="form-group ">
                        <div class="row my-3">
                            <legend class="col-form-label fw-bold col-sm-2 pt-0">Billing Status</legend>
                            <div class="col-sm-10">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="billing_status" id="billing_status1" value="option3">
                                    <label class="form-check-label" for="billing_status1">
                                        Incomplete
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="billing_status" id="billing_status2" value="option3">
                                    <label class="form-check-label" for="billing_status2">
                                        Partial
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="billing_status" id="billing_status3" value="option3">
                                    <label class="form-check-label" for="billing_status3">
                                        Complete
                                    </label>
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <hr style="border: 1px solid  #16a34a;">

                    <!-- invoice table  -->
                    <div class="container my-4 mx-auto" style="width: 800px;">

                        <h5 class="text-center fw-semibold" style="color :  #16a34a">INVOICE TABLE</h5>
                        <table class="table my-5" id="invoice-records-table">
                            <thead>
                                <tr>
                                    <th scope="col">Sr No</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Description</th>
                                    <th scope="col">Paid</th>
                                    <th scope="col">Remaining</th>
                                </tr>
                            </thead>
                            <tbody>
                                <div class="container my-4 mx-auto" style="width: 800px;">
                                for()
                                    <tr>
                                        <td>1</td>
                                        <td>25/06/2023</td>
                                        <td>Diesel Payment</td>
                                        <td>2000</td>
                                        <td>4000</td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Paid</b></td>
                                        <td><span id="final_total_paid" name="final_total_paid"></span></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="hidden" name="total_description" id="total_description" value="1">
                                            <input type="submit" name="create_invoice" id="create_invoice" class="btn btn-warning" value="Invoice">
                                        </td>
                                    </tr>
                            </tbody>
                        </table>

                        <hr style="border: 1px solid  #16a34a;">

                        <!-- add invoce data  -->
                        <div class="container my-4 mx-auto" style="width: 600px;">
                            <form>
                                <div class="form-group row my-2">
                                    <label for="InvoiceDescription" class="col-sm-2 col-form-label  col-form-label-sm fw-bold">Description</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-sm" id="invoice_description1" name="invoice_description[]" placeholder="Description for Billing">
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label for="invoiceDate" class="col-sm-2 col-form-label  col-form-label-sm fw-bold">Date</label>
                                    <div class="col-sm-10">
                                        <input type="date" class="form-control form-control-sm" id="invoice_date1" name="invoice_date[]" data-srno="1" placeholder="Paid on">
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label for="invoicePaidAmount" class="col-sm-2 col-form-label  col-form-label-sm fw-bold">Paid</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-sm number-only" id="invoice_paid_amount1" name="invoice_paid_amount[]" data-srno="1" placeholder="Amount Paid">
                                    </div>
                                </div>
                                <div class="form-group row my-2">
                                    <label for="invoiceRemainingAmount" class="col-sm-2 col-form-label  col-form-label-sm fw-bold">Remaining</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control form-control-sm number-only" id="invoice_remaining_amount1" name="invoice_remaining_amount[]" data-srno="1" placeholder="Amount Remaining">
                                    </div>
                                </div>
                                <div class="form-group row my-3">
                                    <div class="col-sm-10">
                                        <button type="submit" name="add_row" id="add_row" class="btn btn-dark">Add</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <hr style="border: 1px solid  #16a34a;">

                    <!-- Final Submit button -->
                    <div class="form-group row my-3 justify-content-center text-center">
                        <div class="col-sm-10 ">
                            <button type="submit" class="btn btn-success">Create</button>
                        </div>
                    </div>
                </form>
            </div>

            <script>
                $(document).ready(function() {
                    var final_total_paid = $('#final_total_paid').text();
                    var count = 1;
                    console.log(count)
                    $(document).on('click', '#add_row', function() {
                        count = count + 1;
                        $('#total_description').val(count);
                        var html_code = '';
                        html_code += '<tr id="row_id_' + count + '">';
                        html_code += '<td><span id="sr_no">' + count + '</span></td>';
                        html_code += '<td name="invoice_description[]" id="invoice_description' + invoice_description1 + '"></td>';
                        html_code += '<td><input type="text" name="invoice_date[]" id="invoice_date' + count + '" class="form-control"/></td>';
                        html_code += '<td><input type="text" name="invoice_paid_amount[]" id="invoice_paid_amount' + count + '" data-srno="' + count + '" class="form-control number-only"/></td>';
                        html_code += '<td><input type="text" name="invoice_remaining_amount[]" id="invoice_description' + count + '" data-srno="' + count + '" class="form-control number-only"/></td>';
                        html_code += '<td><button type="button" name="remove_row" id=" ' + count + '" class="btn btn-danger btn-xs remove_row">X</button></td></tr>';

                        // html_code += '<td><input type="text" name="invoice_description[]" id="invoice_description' + count + '" class="form-control"/></td>';
                        // html_code += '<td><input type="text" name="invoice_date[]" id="invoice_date' + count + '" class="form-control"/></td>';
                        // html_code += '<td><input type="text" name="invoice_paid_amount[]" id="invoice_paid_amount' + count + '" data-srno="' + count + '" class="form-control number-only"/></td>';
                        // html_code += '<td><input type="text" name="invoice_remaining_amount[]" id="invoice_description' + count + '" data-srno="' + count + '" class="form-control number-only"/></td>';
                        // html_code += '<td><button type="button" name="remove_row" id=" ' + count + '" class="btn btn-danger btn-xs remove_row">X</button></td></tr>';
                        console.log(html_code)
                        $('#invoice_records_table').append(html_code);
                    });

                })
            </script>
        <?php
        } else {


        ?>
            <!-- --------------- -->
            <h3 class="text-center my-4 fw-bold">Invoices and Records of Vrushabh Coperation</h3>
            <hr class="my-4">
            <div class="my-5">
                <a href="invoice.php?add=1" class="btn btn-success">Create</a>
            </div>
            <table id="data-table" class="table table-bordered table-striped py-3">
                <thead>
                    <tr>
                        <th>Invoice No.</th>
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
                    foreach ($all_result as $row) {
                        echo '
                      <tr>
                        <td>' . $row["invoice_no"] . '</td> 
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
                        </tr>
                    ';
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
