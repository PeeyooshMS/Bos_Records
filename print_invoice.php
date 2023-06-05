<?php
if(isset($_GET["pdf"]) && isset($_GET["id"]))
{
    require_once 'pdf.php';
    include('database_connection.php');

    $output = '';
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
    foreach($result as $row )
    {
        $output .= '
        <table width="100%" cellpadding="5" border="0px" cellspacing="0">
        <tr>
          <td colspan="2" align="center" style="font-size: 18px; color:#16a34a">
            <b>Invoice</b>
          </td>
        </tr>
        <tr>
          <td colspan="2">
              <table width="100%" margin="2" cellpadding="5">
                  <tr>
                      <td width="65%">
                          To,<br/>
                          <b>Receiver (Bill to)</b><br>
                          <b>Name</b> : '.$row["customer_name"].'<br>
                          <b>Contact No.</b> : '.$row["customer_contact"].'<br>
                          <b>Address</b> : '.$row["customer_address"].'<br>
                          <br>
                          
                      </td>
                      <td width="35%">
                          <b>Invoice No.</b> : '.$row["invoice_no"].'<br>
                          <b>Service</b> : '.$row["service"].'<br>
                          <b>Service Date</b> : '.$row["service_date"].'<br>
                          <b>Area Coverd</b> : '.$row["area_covered"].'<br>
                          <b>Service Cost</b> : '.$row["service_cost"].'<br>
                          <br>    
                      </td>
                  </tr>
              </table>
              <br>
              <table width="100%" cellpadding="5" cellspacing="0">
                  <tr>
                      <th>Sr No.</th>
                      <th>Date</th>
                      <th>Description</th>
                      <th>Amount Paid</th>
                      <th>Remaining Amount</th>
                  </tr>';
                  $statement = $connect->prepare(
                      "SELECT * FROM table_invoice_records
                      WHERE invoice_id = :invoice_id"
                  );
                  $statement->execute(array(
                      ':invoice_id' => $_GET["id"]
                  ));
                  $item_result = $statement->fetchAll();
                  $count = 0;
                  foreach($item_result as $sub_row)
                  {
                      $count++;
                      $output .='
                       <tr>
                          <td>'.$count.'</td>
                          <td>'.$sub_row["invoice_date"].'</td>
                          <td>'.$sub_row["invoice_description"].'</td>
                          <td>'.$sub_row["invoice_paid_amount"].'</td>
                          <td>'.$sub_row["invoice_remaining_amount"].'</td>
                       </tr>
                      ';
                  }
                  $output .= '
                      <tr>
                          <td align="right" colspan="8"><b>Total</b></td>
                          <td align="right"><b>'.$row["total_payment"].'</b></td>
                      </tr>
                  ';
                  $output .= '
              </table>
          </td> 
        </tr>
      </table>';
    }

    // Create a new Dompdf instance
    $pdf = new pdf();

    // HTML content
    $html = '
    <html>
    <head>
        <style>
            /* Add your CSS styles here */
            table {
                width: 100%;
                border-collapse: collapse;
            }
            th, td {
                border: 1px solid black;
                padding: 5px;
            }
        </style>
    </head>
    <body>
        '.$output.'
    </body>
    </html>';

    // Load HTML content
    $pdf->loadHtml($html);

    // Set paper size and orientation (optional)
    $pdf->setPaper('A4', 'portrait');

    // Render the PDF
    $pdf->render();

    // Output the PDF to the browser
    $pdf->stream("invoice.pdf");
}
?>
