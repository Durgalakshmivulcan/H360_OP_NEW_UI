<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';
?>

<table class="table" id="tableExport1" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th>S.No</th>
            <?php if ($SessionUserId == "1") { ?>
                <th>Organization Name</th>
            <?php } ?>
            <th>Package Name</th>
            <th>Test Names</th>
            <th>Price</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody class="text-center">
        <?php
        if ($SessionUserId == "1" && $SessionRoleId == "1") {
            $getAdminTestGroup = mysqli_query($conn, "SELECT * FROM test_group WHERE status='1' ORDER BY test_group_id DESC") or die(mysqli_error($conn));
        } else {
            $getAdminTestGroup = mysqli_query($conn, "SELECT * FROM test_group WHERE status='1' AND org_id='$SessionOrgId' ORDER BY test_group_id DESC") or die(mysqli_error($conn));
        }

        $i = 1;
        $alreadyDisplayed = [];

        while ($resAdminTestGroup = mysqli_fetch_object($getAdminTestGroup)) {
            $test_json = $resAdminTestGroup->test_id;
            $test_group_name = $resAdminTestGroup->test_group_name;
            $org_id = $resAdminTestGroup->org_id;
            $test_group_id = $resAdminTestGroup->test_group_id;
            $investigationData = [];

            if (!empty($test_json) && substr(trim($test_json), 0, 1) == "[") {
                $decodedTests = json_decode($test_json, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $investigationData = $decodedTests;
                }
            }

            // Check if package name already displayed
            if (in_array($test_group_name, $alreadyDisplayed)) {
                continue;
            }
            $alreadyDisplayed[] = $test_group_name;

            if (!empty($investigationData)) {
                $testNames = [];
                $price = $investigationData[0]['price'] ?? '';

                foreach ($investigationData as $inv) {
                    $testNames[] = $inv['investigation'];
                }

                // Escape everything properly
                $safe_test_group_name = htmlspecialchars($test_group_name, ENT_QUOTES);
                $safe_test_names = htmlspecialchars(implode(", ", $testNames), ENT_QUOTES);
                $safe_price = htmlspecialchars($price, ENT_QUOTES);
                $safe_org_id = htmlspecialchars($org_id, ENT_QUOTES);

                ?>
                <tr>
                    <td> <?= $i++; ?> </td>
                    <?php if ($SessionUserId == "1") { ?>
                        <td> <?= getUserNameByOrgId($conn, $org_id) ?> </td>
                    <?php } ?>
                    <td> <?= $safe_test_group_name ?> </td>
                    <td> <?= $safe_test_names ?> </td>
                    <td> <?= $safe_price ?> </td>
                    <td class="text-center">
                        <?php if (userCan('edit', 'testGroup.php')) { /* FIX_B_1810 */ ?><a href="#" class="has-icon editTestGroupBtn me-3"
                           data-testgroupid="<?= $test_group_id ?>"
                           data-testgroupname="<?= $safe_test_group_name ?>"
                           data-testnames="<?= $safe_test_names ?>"
                           data-testgroupprice="<?= $safe_price ?>"
                           data-orgid="<?= $safe_org_id ?>">
                            <i class="fa fa-edit fa-lg"></i>
                        </a><?php } ?>
                        <?php if (userCan('delete', 'testGroup.php')) { /* FIX_B_1810 */ ?><a class="has-icon text-danger"
                           onclick="deleteTestGroup('<?= $test_group_id ?>', '<?= $safe_test_group_name ?>')">
                            <i class="fa fa-trash fa-lg"></i>
                        </a><?php } ?>
                    </td>
                </tr>
                <?php
            } else {
                ?>
                <tr>
                    <td colspan="6">No Investigation Tests Found</td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>
<script>
   
   $(document).on('click', '.editTestGroupBtn', function () {

        $("#test_group_id").val('');
        $("#test_group_name").val('');
        $("#test_name").val('');  
        $("#test_group_price").val('');
        $("#organizations").val('');
        GetAutoTest()
        var test_group_id = $(this).data('testgroupid');
        var test_group_name = $(this).data('testgroupname');
        var test_names = $(this).data('testnames');
        var test_group_price = $(this).data('testgroupprice');
        var organizations = $(this).data('orgid');

        $("#test_group_id").val(test_group_id);
        $("#test_group_name").val(test_group_name);
        $("#test_group_price").val(test_group_price);
        $("#organizations").val(organizations);

        var testNamesArray = test_names.split(',').map(name => name.trim());
        // Tests.clearStore();

        if (typeof Tests !== 'undefined') {
            // Tests.clearChoices(); // completely reset the dropdown

            const newChoices = testNamesArray.map(name => ({
                value: name,
                label: name,
                selected: true
            }));

            Tests.setChoices(newChoices, 'value', 'label', false); // add and select
        }
    });


</script>