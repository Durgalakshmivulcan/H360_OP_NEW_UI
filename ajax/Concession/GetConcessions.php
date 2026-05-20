<?php
require_once("../../config/functions.php");

    // $SessionUserId = $_SESSION['security_id'] ?? '';
    // $SessionRoleId = $_SESSION['role_id'] ?? '';
    $SessionOrgId = $_SESSION['org_id'] ?? '';

?>

<table class="table" id="concessionid" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th>S.No</th>
            <?php
                if($SessionUserId=="1"){
            ?>
            <th>Organization Name</th>
            <?php
                }
            ?>
            <th>Concession Name</th>
            <th>Concession Type</th>
            <th>Value</th>
            <th class="action">Action</th>
        </tr>
    </thead>
    <tbody class="text-center">
    <?php
        if ($SessionUserId == "1" && $SessionRoleId == "1") {
            $getConcessions = mysqli_query($conn, "
                SELECT * 
                FROM concessions 
                WHERE status='1'
                ORDER BY concession_id DESC
            ") or die(mysqli_error($conn));
        } else {
            $getConcessions = mysqli_query($conn, "
                SELECT *
                FROM concessions 
                WHERE status='1' AND org_id='$SessionOrgId'
                ORDER BY concession_id DESC
            ") or die(mysqli_error($conn));
        }

        $i = 1;
        while ($res = mysqli_fetch_assoc($getConcessions)) {
    ?>
        <tr>
            <td><?= $i++; ?></td>

            <?php if ($SessionUserId == "1" && $SessionRoleId == "1") { ?>
                <td><?= getUserNameByOrgId($conn, $res['org_id']); ?></td>
            <?php } ?>

            <td><?= htmlspecialchars($res['concession_name']); ?></td>
            <td><?= htmlspecialchars($res['concession_type']); ?></td>
            <td><?= htmlspecialchars($res['concession_value']); ?></td>
            
            <td class="text-center">
                <?php if (userCan('edit', 'concession.php')) { /* FIX_B_1810 */ ?><a class="has-icon me-3" style="cursor:pointer;"
                    onclick="editConcession(
                        '<?= $res['concession_id']; ?>', 
                        '<?= addslashes($res['concession_name']); ?>', 
                        '<?= $res['concession_type']; ?>', 
                        '<?= $res['concession_value']; ?>', 
                        '<?= $res['org_id']; ?>'
                    )">
                    <i class="fa fa-edit fa-lg"></i>
                    </a><?php } ?>
                    <?php if (userCan('delete', 'concession.php')) { /* FIX_B_1810 */ ?><a class="has-icon text-danger" style="cursor:pointer;"
                        onclick="deleteConcession('<?= $res['concession_id']; ?>')">
                        <i class="fa fa-trash fa-lg"></i> 
                    </a><?php } ?>
                </td>
            </tr>
        <?php } ?>
    </tbody>

</table>
