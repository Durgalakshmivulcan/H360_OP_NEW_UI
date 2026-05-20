<?php
require_once("../../config/functions.php");

$getAdminInvoice = mysqli_query($conn, "SELECT * FROM invoice WHERE status='1' ORDER BY invoice_id DESC") or die(mysqli_error($conn));
$i = 1;
while($resAdminInvoice = mysqli_fetch_object($getAdminInvoice)){
?>
    <tr>
        <td> <?=$i++;?> </td>
        <td> <?=$resAdminInvoice->doctor_name?> </td>
        <td> <?=$resAdminInvoice->mobile_number ?> </td>
        <td> <?=$resAdminInvoice->invoice_date?> </td>
        <td> <?=$resAdminInvoice->MR_No?> </td>
        <td> <?=$resAdminInvoice->receipt_no?> </td>
        <td> <?=$resAdminInvoice->patient_name?> </td>
        <td> <?=$resAdminInvoice->age?> </td>
        <td> <?=$resAdminInvoice->p_number?> </td>
        <td> <?=$resAdminInvoice->gender?> </td>
        <td> <?=$resAdminInvoice->test_name?> </td>
        <td> <?=$resAdminInvoice->charge?> </td>
        <td> <?=$resAdminInvoice->sub_total?> </td>
        <td> <?=$resAdminInvoice->paid?> </td>
        <td> <?=$resAdminInvoice->due?> </td>
        <td> <?=$resAdminInvoice->final_total?> </td>
        <!-- <td> <?=getUserNameById($conn, $resAdminInvoice->created_by)?> </td> -->
        <td class="text-center">
            <ul class="navbar-nav">
                <li class="dropdown">
                    <a href="#" data-bs-toggle="dropdown" class="nav-link" style="color:black">
                        <i class="fa fa-ellipsis-v"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-left">
                        <a href="#" class="dropdown-item has-icon" onclick='editInvoice(`<?=$resAdminInvoice->invoice_id?>`, `<?=$resAdminInvoice->doctor_name ?>`, `<?=$resAdminInvoice->mobile_number ?>`, `<?=$resAdminInvoice->invoice_date?>`, `<?=$resAdminInvoice->MR_No ?>`, `<?=$resAdminInvoice->receipt_no ?>`, `<?=$resAdminInvoice->patient_name ?>`, `<?=$resAdminInvoice->age ?>`, `<?=$resAdminInvoice->p_number ?>`, `<?=$resAdminInvoice->gender ?>`, `<?=$resAdminInvoice->test_name ?>`, `<?=$resAdminInvoice->charge ?>`, `<?=$resAdminInvoice->sub_total?>`, `<?=$resAdminInvoice->paid ?>`, `<?=$resAdminInvoice->due ?>`, `<?=$resAdminInvoice->final_total ?>`)'> <i class="fa fa-edit"></i> Update</a>
                        <a class="dropdown-item has-icon" style="cursor:pointer;" onclick="deleteInvoice('<?=$resAdminInvoice->invoice_id?>', '<?=$resAdminInvoice->doctor_name ?>')"> <i class="fa fa-trash"></i> Delete</a>
                    </div>
                </li>
            </ul>
        </td>
    </tr>
<?php } ?>







