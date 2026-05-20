<?php
require_once("../../config/functions.php");
// FIX_B_2252b: specialization gate — gynaecologist users blocked from
// general-prescription endpoints (mirrors prescription.php page gate).
// SA + non-doctor users are bypassed inside userMaySeeBySpecialization().
if (function_exists('requireSpecializationFor')) requireSpecializationFor('prescription.php', 'ajax');


function encryptData($data, $key) {
    $ivLength = openssl_cipher_iv_length('aes-256-cbc');
    $iv = openssl_random_pseudo_bytes($ivLength);
    $encrypted = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);
    return base64_encode($encrypted . '::' . $iv);
}

$encryptionKey = "YourSecretKey123!";

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionRoleId = $_SESSION['role_id'] ?? '';
$SessionOrgId = $_SESSION['org_id'] ?? '';

if ($SessionUserId == "1") {
    $query = "SELECT 
                p.prescription_id,
                p.patient_vitals,
                p.org_id,
                p.patient_uid,
                p.age AS prescription_age,
                p.gender,
                p.create_date_time,
                p.status,
                ao.appoint_register_id,
                ao.appoint_id
              FROM prescripition p 
              LEFT JOIN appointment_online ao 
                ON p.patient_vitals = ao.appoint_register_id 
               AND p.org_id = ao.org_id 
               LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
              WHERE p.status = '1' AND d.security_id = $SessionUserId
                AND DATE(p.create_date_time) = CURDATE() 
              ORDER BY p.prescription_id DESC, ao.appoint_id DESC";
} else {
    $query = "SELECT 
                p.prescription_id,
                p.patient_vitals,
                p.org_id,
                p.patient_uid,
                p.age AS prescription_age,
                p.gender,
                p.create_date_time,
                p.status,
                ao.appoint_register_id,
                ao.appoint_id
              FROM prescripition p 
              LEFT JOIN appointment_online ao 
                ON p.patient_vitals = ao.appoint_register_id 
               AND ao.org_id = '$SessionOrgId'
                LEFT JOIN doctors d ON ao.doctor_name = d.doc_id
              WHERE p.status = '1' 
                AND p.org_id = '$SessionOrgId' 
                AND d.security_id = $SessionUserId
                AND DATE(p.create_date_time) = CURDATE() 
              ORDER BY p.prescription_id DESC, ao.appoint_id DESC";
}
$getAdminMenus = mysqli_query($conn, $query) or die(mysqli_error($conn));
?>

<!-- Share Popup -->
<div class="overlay" id="overlay"></div>
<div class="popup" id="popup">
    <div class="popup-content">
        <h3>Share Prescription</h3>
        <p>Enter phone number with country code (e.g. 919876543210)</p>
    </div>
    <div class="share-options">
        <input type="text" id="whatsappNumber" placeholder="e.g. 919876543210" 
               style="padding:8px; border:1px solid #ccc; border-radius:5px; width:100%;" 
               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 12)" 
               maxlength="12" />
        
        <button class="share-btn whatsapp-btn" onclick="shareToWhatsApp()">
            <i class="fab fa-whatsapp"></i> Send via WhatsApp
        </button>

        <button class="share-btn view-btn" onclick="openPatientView()">
            <i class="fas fa-external-link-alt"></i> Open View
        </button>
    </div>
    <button class="close-btn" onclick="closePopup()">Close</button>
</div>

<!-- Prescription Table -->
<table class="table" id="tableExportP" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th>S.No</th>
            <th>Prescription</th>
            <th>Share</th>
            <th>Action</th>
            <?php if ($SessionUserId == "1") echo "<th>Organization</th>"; ?>
            <th>Patient Name</th>
            <th>Patient Id</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Date & Time</th>
        </tr>
    </thead>
    <tbody class="text-center">
        <?php
        $i = 1;
        while ($res = mysqli_fetch_object($getAdminMenus)) {
            $itemid=$res->prescription_id;
            $orgidview=$res->org_id;
            $encryptedItemId = urlencode(encryptData($res->prescription_id, $encryptionKey));
            $encryptedOrgId = urlencode(encryptData($res->org_id, $encryptionKey));

            $patientViewUrl = "patientview.php?ItemId=$encryptedItemId&OrgID=$encryptedOrgId";
            $prescriptionUrl = "patientPrescription.php?ItemId=$itemid&OrgID=$orgidview";
        ?>
        <tr>
            <td><?= $i++; ?></td>
            <td>
                <a class="btn btn-primary" href="<?= htmlspecialchars($prescriptionUrl) ?>" target="_blank">View</a>
            </td>
            <td>
                <button style="color:#20d220;background:none;border:none;" onclick="showShareOptions('<?= htmlspecialchars($patientViewUrl) ?>')">
                    <i class="fa-solid fa-share-from-square"></i>
                </button>
            </td>
            <td>
                <a class="text-danger" style="cursor:pointer;" onclick="deleteP('<?= htmlspecialchars($res->prescription_id) ?>', '<?= htmlspecialchars($res->patientName) ?>')">
                    <i class="fa fa-trash fa-lg"></i>
                </a>
            </td>
            <?php if ($SessionUserId == "1") { ?>
                <td><?= htmlspecialchars(getUserNameByOrgId($conn, $res->org_id)) ?></td>
            <?php } ?>
            <td><?= htmlspecialchars(getAppointmentById($conn, $res->patient_uid, $res->org_id)) ?></td>
            <!-- <td>
                <span class="clickable-col text-black" 
                    data-patient-uid="<?= htmlspecialchars($res->appoint_id) ?>"
                    style="cursor:pointer;">
                    <?= htmlspecialchars($res->patient_uid) ?>
                </span>
            </td> -->
            <td>
                <a href="AllPatients.php?appoint_id=<?= urlencode($res->appoint_id) ?>"
                class="text-black"
                style="cursor:pointer; text-decoration:none;">
                    <?= htmlspecialchars($res->patient_uid) ?>
                </a>
            </td>
            <td><?= htmlspecialchars($res->prescription_age) ?></td>
            <td><?= htmlspecialchars($res->gender) ?></td>
            <td><?= htmlspecialchars($res->create_date_time) ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<style>
.overlay {
    display: none; position: fixed; top: 0; left: 0; width: 100%;
    height: 100%; background: rgba(0,0,0,0.5); z-index: 999;
}
.popup {
    display: none; position: fixed; top: 50%; left: 50%;
    transform: translate(-50%, -50%);
    background: white; padding: 20px; border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.2); z-index: 1000;
    width: 350px; max-width: 90%;
    text-align: center;
}
.popup-content { margin-bottom: 15px; }
.share-options {
    display: flex; flex-direction: column; gap: 10px; margin-top: 15px;
}
.share-btn {
    padding: 10px; border: none; border-radius: 5px;
    cursor: pointer; font-size: 14px;
    display: flex; align-items: center; justify-content: center; gap: 8px;
}
.whatsapp-btn { background-color: #25D366; color: white; }
.view-btn { background-color: #6c757d; color: white; }
.close-btn {
    margin-top: 15px; padding: 8px;
    background: #f1f1f1; color: #333; border: none;
    border-radius: 5px; width: 100%;
}
</style>

<script>
// document.querySelectorAll('.clickable-col').forEach(el => {
//     el.addEventListener('click', () => {
//         const uid = el.getAttribute('data-patient-uid');
//         window.location.href = `AllPatients.php?appoint_id=${encodeURIComponent(uid)}`;
//     });
// });


let currentShareUrl = '';

function showShareOptions(url) {
    currentShareUrl = url;
    document.getElementById('popup').style.display = 'block';
    document.getElementById('overlay').style.display = 'block';
    document.getElementById('whatsappNumber').value = '';
    document.getElementById('whatsappNumber').focus();
}

function shareToWhatsApp() {
    const phone = document.getElementById('whatsappNumber').value.trim();
    const origin = window.location.origin;
    const fullUrl = origin + '/' + currentShareUrl.replace(/^\//, '');
    
    if (!phone || !/^[0-9]{10,12}$/.test(phone)) {
        alert('Please enter a valid 10-12 digit phone number with country code');
        return;
    }

    const tempLink = document.createElement('a');
    tempLink.href = `https://web.whatsapp.com/send?phone=${phone}&text=Please check your prescription: ${encodeURIComponent(fullUrl)}`;
    tempLink.target = '_blank';
    
    const desktopWindow = window.open(tempLink.href, '_blank');
    
    setTimeout(() => {
        if (!desktopWindow || desktopWindow.closed || typeof desktopWindow.closed == 'undefined') {
            window.open(`https://api.whatsapp.com/send?phone=${phone}&text=${encodeURIComponent("Please check your prescription: " + fullUrl)}`, '_blank');
        }
    }, 500);
    
    closePopup();
}

function openPatientView() {
    window.open(currentShareUrl, '_blank');
    closePopup();
}

function closePopup() {
    document.getElementById('popup').style.display = 'none';
    document.getElementById('overlay').style.display = 'none';
}
</script>