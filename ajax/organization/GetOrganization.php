<?php
// FIX_B_1501: previously had NO authentication gate. Anonymous clients could
// fetch the entire organization list (name, contact, email, GST, TAN, geo).
// Now: session-gated. Only authenticated users may render the table.
require_once("../../config/functions.php");
$SessionUserId = $_SESSION['security_id'] ?? '';
if ($SessionUserId === '') {
    http_response_code(403);
    echo '<table class="table"><tr><td>Forbidden</td></tr></table>';
    exit;
}
?>
<table class="table" id="tableExport1" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th> S.No </th>
            <th> Organization Name </th>
            <th> Contact </th>
            <th> Email </th>
            <!-- <th > Description </th> -->
            <th> GST Number </th>
            <th> TAN Number </th>
            <th> Longitude </th>
            <th> Latitude </th>
            <!-- <th> Address </th> -->
            <th> Action </th>
        </tr>
    </thead>
    <tbody class="text-center">
<?php
$getAdminOrganization = mysqli_query($conn, "SELECT * FROM organization WHERE status='1' ORDER BY org_id DESC") or die(mysqli_error($conn));
$i = 1;
while($resAdminOrganization = mysqli_fetch_object($getAdminOrganization)){
?>
    <tr>
        <td> <?=$i++;?> </td>
        <td> <?=$resAdminOrganization->organization_name?> </td>
        <td> <?=$resAdminOrganization->contact?> </td>
        <td> <?=$resAdminOrganization->email?> </td>
        <!-- <td> <?=$resAdminOrganization->description?> </td> -->
        <td> <?=$resAdminOrganization->gstNumber?> </td>
        <td> <?=$resAdminOrganization->tanNumber?> </td>
        <td> <?=$resAdminOrganization->longitude?> </td>
        <td> <?=$resAdminOrganization->latitude?> </td>
        <!-- <td> <?=$resAdminOrganization->address?> </td> -->
        <td class="text-center">
            <a class="has-icon " style="cursor:pointer;margin-right: 20px;"
             onclick='editOrganization(`<?=$resAdminOrganization->org_id?>`, `<?=$resAdminOrganization->organization_name ?>`,`<?=$resAdminOrganization->contact?>`,`<?=$resAdminOrganization->email?>`,
                `<?=$resAdminOrganization->description ?>`, `<?=$resAdminOrganization->gstNumber ?>`, `<?=$resAdminOrganization->tanNumber ?>`, `<?=$resAdminOrganization->longitude ?>`, `<?=$resAdminOrganization->latitude ?>`, `<?=$resAdminOrganization->address ?>`, `<?=$resAdminOrganization->user_limit?>`,`<?=$resAdminOrganization->opipaccess?>`)'> 
                <i class="fa fa-edit fa-lg"></i>
            </a>
            <a class="has-icon text-danger" style="cursor:pointer;" onclick="deleteOrganization('<?=$resAdminOrganization->org_id?>', '<?=$resAdminOrganization->organization_name ?>')"> 
                <i class="fa fa-trash fa-lg"></i>
            </a>
        </td>
    </tr>
<?php } ?>

    </tbody>                            
</table>



