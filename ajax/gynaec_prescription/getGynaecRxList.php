<?php
require_once("../../config/functions.php");
// FIX_B_2252: specialization gate — only doctors whose doctors.doctor_specialization
// is in menus.restricted_to_specializations for gynaec_prescription.php may hit
// these AJAX endpoints. SA + non-doctor users (admin/receptionist/etc.) are
// bypassed inside userMaySeeBySpecialization(). 403 JSON for AJAX mode.
if (function_exists('requireSpecializationFor')) requireSpecializationFor('gynaec_prescription.php', 'ajax');


$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id']      ?? '';
$orgId         = mysqli_real_escape_string($conn, $_POST['org_id'] ?? $SessionOrgId);

if ($SessionUserId == "1") {
    $qry = mysqli_query($conn,
        "SELECT g.*, o.organization_name
         FROM gynaec_prescriptions g
         LEFT JOIN organization o ON g.org_id = o.org_id
         WHERE g.status = '1'
           AND DATE(COALESCE(g.rx_date, g.created_at)) = CURDATE()
         ORDER BY g.gynaec_rx_id DESC") or die(mysqli_error($conn));
} else {
    $qry = mysqli_query($conn,
        "SELECT g.*
         FROM gynaec_prescriptions g
         WHERE g.status = '1' AND g.org_id = '$orgId'
           AND DATE(COALESCE(g.rx_date, g.created_at)) = CURDATE()
         ORDER BY g.gynaec_rx_id DESC") or die(mysqli_error($conn));
}

$rows = [];
while ($r = mysqli_fetch_assoc($qry)) $rows[] = $r;
$colspan = ($SessionUserId == "1") ? 12 : 11;
?>

<!-- Share Popup -->
<div class="overlay" id="grxOverlay"></div>
<div class="popup" id="grxPopup">
    <div class="popup-content">
        <h3>Share Gynaec Prescription</h3>
        <p>Enter phone number with country code (e.g. 919876543210)</p>
    </div>
    <div class="share-options">
        <input type="text" id="grxWhatsappNumber" placeholder="e.g. 919876543210"
               style="padding:8px;border:1px solid #ccc;border-radius:5px;width:100%;"
               oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,12)"
               maxlength="12"/>
        <button class="share-btn whatsapp-btn" onclick="grxShareToWhatsApp()">
            <i class="fab fa-whatsapp"></i> Send via WhatsApp
        </button>
        <button class="share-btn view-btn" onclick="grxOpenShare()">
            <i class="fas fa-external-link-alt"></i> Open View
        </button>
    </div>
    <button class="close-btn" onclick="grxClosePopup()">Close</button>
</div>

<!-- Gynaec Prescription History Table -->
<div class="table-responsive">
<table class="table" id="gynaecRxTable" style="width:100%;">
    <thead class="text-center">
        <tr>
            <th>S.No</th>
            <th>Prescription</th>
            <th>Share</th>
            <th>Action</th>
            <?php if ($SessionUserId == "1"): ?><th>Organization</th><?php endif; ?>
            <th>Patient Name</th>
            <th>Patient ID</th>
            <th>Mobile</th>
            <th>Age</th>
            <th>Gender</th>
            <th>Final Diagnosis</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody class="text-center">
        <?php if (empty($rows)): ?>
            <tr><td colspan="<?= $colspan ?>" class="text-muted">No records found.</td></tr>
        <?php else: $i = 1; foreach ($rows as $r): ?>
        <tr>
            <td><?= $i++ ?></td>
            <td>
                <button class="btn btn-primary btn-sm" onclick="viewGynaecRx(<?= (int)$r['gynaec_rx_id'] ?>)">
                    <i class="fas fa-eye"></i> View
                </button>
            </td>
            <td>
                <button style="color:#20d220;background:none;border:none;font-size:1.3em;"
                    onclick="grxShowShare(<?= (int)$r['gynaec_rx_id'] ?>, '<?= addslashes(htmlspecialchars($r['patient_name'])) ?>')">
                    <i class="fa-solid fa-share-from-square"></i>
                </button>
            </td>
            <td>
                <button class="btn btn-sm btn-danger delete-gynaec-btn" data-id="<?= (int)$r['gynaec_rx_id'] ?>" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
            <?php if ($SessionUserId == "1"): ?>
            <td><?= htmlspecialchars($r['organization_name'] ?? '') ?></td>
            <?php endif; ?>
            <td><?= htmlspecialchars($r['patient_name']) ?></td>
            <td><?= htmlspecialchars($r['patient_id'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['mobile'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['age'] ?? '-') ?></td>
            <td><?= htmlspecialchars($r['gender'] ?? '-') ?></td>
            <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;"
                title="<?= htmlspecialchars($r['final_diagnosis'] ?? '') ?>">
                <?= htmlspecialchars(mb_strimwidth($r['final_diagnosis'] ?? '-', 0, 40, '…')) ?>
            </td>
            <td><?= $r['rx_date'] ? date('d/m/Y', strtotime($r['rx_date'])) : ($r['created_at'] ? date('d/m/Y', strtotime($r['created_at'])) : '-') ?></td>
        </tr>
        <?php endforeach; endif; ?>
    </tbody>
</table>
</div>

<style>
#grxOverlay {
    display:none;position:fixed;top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,.5);z-index:1040;
}
#grxPopup {
    display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);
    background:#fff;padding:20px;border-radius:10px;
    box-shadow:0 0 10px rgba(0,0,0,.2);z-index:1050;
    width:350px;max-width:90%;text-align:center;
}
#grxPopup .popup-content { margin-bottom:15px; }
#grxPopup .share-options { display:flex;flex-direction:column;gap:10px;margin-top:15px; }
#grxPopup .share-btn { padding:10px;border:none;border-radius:5px;cursor:pointer;font-size:14px;
    display:flex;align-items:center;justify-content:center;gap:8px; }
#grxPopup .whatsapp-btn { background:#25D366;color:#fff; }
#grxPopup .view-btn { background:#6c757d;color:#fff; }
#grxPopup .close-btn { margin-top:15px;padding:8px;background:#f1f1f1;color:#333;
    border:none;border-radius:5px;width:100%;cursor:pointer; }
</style>

<script>
var _grxCurrentShareId = null;

function grxShowShare(rxId, patientName) {
    _grxCurrentShareId = rxId;
    document.getElementById('grxWhatsappNumber').value = '';
    document.getElementById('grxPopup').style.display  = 'block';
    document.getElementById('grxOverlay').style.display = 'block';
    document.getElementById('grxWhatsappNumber').focus();
}

function grxShareToWhatsApp() {
    var phone = document.getElementById('grxWhatsappNumber').value.trim();
    if (!phone || !/^[0-9]{10,12}$/.test(phone)) {
        alert('Please enter a valid 10-12 digit phone number with country code');
        return;
    }
    var origin  = window.location.origin;
    var baseDir = '<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') ?>';
    var viewUrl = origin + baseDir + '/ajax/gynaec_prescription/viewGynaecRx.php';
    var msg     = 'Please check your Gynaec Prescription at: ' + viewUrl + ' (Rx ID: ' + _grxCurrentShareId + ')';
    var waUrl   = 'https://web.whatsapp.com/send?phone=' + phone + '&text=' + encodeURIComponent(msg);
    var win     = window.open(waUrl, '_blank');
    setTimeout(function(){
        if (!win || win.closed) {
            window.open('https://api.whatsapp.com/send?phone='+phone+'&text='+encodeURIComponent(msg), '_blank');
        }
    }, 500);
    grxClosePopup();
}

function grxOpenShare() {
    if (_grxCurrentShareId) viewGynaecRx(_grxCurrentShareId);
    grxClosePopup();
}

function grxClosePopup() {
    document.getElementById('grxPopup').style.display  = 'none';
    document.getElementById('grxOverlay').style.display = 'none';
}
</script>
