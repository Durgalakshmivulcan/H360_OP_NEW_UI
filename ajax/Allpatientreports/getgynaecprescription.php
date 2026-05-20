<?php
require_once("../../config/functions.php");

$SessionUserId = $_SESSION['security_id'] ?? '';
$SessionOrgId  = $_SESSION['org_id'] ?? '';

$rxId  = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$orgId = isset($_POST['org_id']) && !empty($_POST['org_id']) ? $_POST['org_id'] : $SessionOrgId;

if (!$rxId) {
    echo "<p class='text-danger'>Invalid request.</p>";
    exit;
}

// FIX_B_1903: doctor-scope filter via join to appointment_online.doctor_name
$docScope = currentDoctorScopeSql('ao.doctor_name');
$query = mysqli_query($conn,
    "SELECT g.*, o.organization_name
     FROM gynaec_prescriptions g
     LEFT JOIN organization o ON g.org_id = o.org_id
     LEFT JOIN appointment_online ao ON ao.appoint_register_id = g.appointment_id
     WHERE g.gynaec_rx_id = '$rxId' AND g.status='1' $docScope
     LIMIT 1") or die(mysqli_error($conn));

$rx = mysqli_fetch_assoc($query);

if (!$rx) {
    echo "<p class='text-danger'>Prescription not found.</p>";
    exit;
}

$medicines      = json_decode($rx['medicines_json']     ?? '[]', true) ?: [];
$investigations = json_decode($rx['investigations_json'] ?? '[]', true) ?: [];
$hasMeds        = !empty($medicines);
$hasInv         = !empty($investigations);
?>

<style>
  .head  { font-weight:800!important; }
  .info  { font-weight:600!important; }
  .section-title { font-weight:bold; font-size:15px; margin-top:10px; }
  .border-box { border:1px solid #ccc; padding:8px; border-radius:6px; background:#f9f9f9; }
  .vitals-table-container { overflow-x:auto; -webkit-overflow-scrolling:touch; margin-top:5px; margin-bottom:10px; }
  .vitals-table { width:100%; border-collapse:collapse; }
  .vitals-table th, .vitals-table td { border:1px solid #ccc; padding:6px 8px; text-align:left; font-size:14px; }
</style>

<div class="card">
  <div class="card-body">

    <div class="row mb-2">
      <div class="col-md-10 col-sm-12">
        <span class="badge bg-danger" style="font-size:0.9em;">Gynaec Prescription</span>
      </div>
      <div class="col-md-2 col-sm-12 text-end">
        <a class="btn btn-primary btn-sm"
           href="javascript:void(0);"
           onclick="printGynaecFromModal(<?= (int)$rx['gynaec_rx_id'] ?>)">
          <i class="fa fa-print"></i> Print
        </a>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 col-sm-12">
        <div class="info"><span class="head">Name : </span><?= strtoupper(htmlspecialchars($rx['patient_name'] ?? 'N/A')) ?></div>
      </div>
      <div class="col-md-4 col-sm-12">
        <div class="info"><span class="head">Gender / Age : </span><?= strtoupper(htmlspecialchars($rx['gender'] ?? 'N/A')) ?> / <?= htmlspecialchars($rx['age'] ?? 'N/A') ?> Y</div>
      </div>
      <div class="col-md-4 col-sm-12">
        <div class="info"><span class="head">Date : </span><?= htmlspecialchars($rx['rx_date'] ?? 'N/A') ?></div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 col-sm-12">
        <div class="info"><span class="head">Mobile : </span><?= htmlspecialchars($rx['mobile'] ?? 'N/A') ?></div>
      </div>
      <div class="col-md-4 col-sm-12">
        <div class="info"><span class="head">Appointment ID : </span><?= htmlspecialchars($rx['appointment_id'] ?? 'N/A') ?></div>
      </div>
      <div class="col-md-4 col-sm-12">
        <div class="info"><span class="head">Organization : </span><?= strtoupper(htmlspecialchars($rx['organization_name'] ?? 'N/A')) ?></div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-4 col-sm-12">
        <div class="info"><span class="head">UMR No : </span><?= htmlspecialchars($rx['patient_id'] ?? 'N/A') ?></div>
      </div>
    </div>

    <?php
    $assessmentFields = [
        'final_diagnosis'         => 'Final Diagnosis',
        'chief_complaints'        => 'Chief Complaints',
        'gynaec_history'          => 'Gynaec History',
        'obstetric_history'       => 'Obstetric History',
        'family_history'          => 'Family History',
        'personal_history'        => 'Personal History',
        'general_examination'     => 'General Examination',
        'previous_investigations' => 'Previous Investigations',
    ];
    foreach ($assessmentFields as $col => $label):
        if (empty($rx[$col])) continue;
    ?>
    <div>
      <p style="margin-bottom:0;"><span class="section-title"><?= $label ?> :</span></p>
      <div class="border-box">
        <p style="font-size:15px;margin-bottom:0;"><?= nl2br(htmlspecialchars($rx[$col])) ?></p>
      </div>
    </div>
    <?php endforeach; ?>

    <?php if ($hasMeds): ?>
    <div class="section-title">Medicines :</div>
    <div class="vitals-table-container">
      <table class="vitals-table">
        <thead style="background-color:#ddd;">
          <tr>
            <th>Medicine Name</th><th>Type</th><th>Dose</th><th>Frequency</th><th>Duration</th><th>Instructions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($medicines as $m): ?>
          <tr>
            <td><?= htmlspecialchars($m['drugName']     ?? $m['medicine_name'] ?? '') ?></td>
            <td><?= htmlspecialchars($m['type']         ?? $m['type_text']     ?? '') ?></td>
            <td><?= htmlspecialchars($m['dose']         ?? '') ?></td>
            <td><?= htmlspecialchars($m['frequency']    ?? $m['timeText']      ?? '') ?></td>
            <td><?= htmlspecialchars($m['duration']     ?? '') ?></td>
            <td><?= htmlspecialchars($m['instructions'] ?? $m['whenText']      ?? '') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

    <?php if ($hasInv): ?>
    <div class="section-title">Investigations :</div>
    <div class="vitals-table-container">
      <table class="vitals-table">
        <thead style="background-color:#ddd;">
          <tr>
            <th>Investigation</th><th>Instruction</th><th>Price</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($investigations as $inv): ?>
          <tr>
            <td><?= htmlspecialchars($inv['investigation_name'] ?? $inv['investigation'] ?? '') ?></td>
            <td><?= htmlspecialchars($inv['instruction'] ?? '') ?></td>
            <td><?= htmlspecialchars($inv['price'] ?? '') ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php endif; ?>

    <?php if (!empty($rx['reviewafterdate'])): ?>
    <div>
      <p style="margin-bottom:0;"><span class="section-title">Review After :</span></p>
      <div class="border-box">
        <p style="font-size:15px;margin-bottom:0;"><?= htmlspecialchars($rx['reviewafterdate']) ?></p>
      </div>
    </div>
    <?php endif; ?>

  </div>
</div>

<script>
function printGynaecFromModal(rxId) {
    var baseUrl = '<?= (isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']==='on'?'https':'http')
        .'://'.$_SERVER['HTTP_HOST']
        .rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))),'/')
        .'/ajax/gynaec_prescription/viewGynaecRx.php' ?>';
    var f = document.createElement('form');
    f.method = 'POST'; f.action = baseUrl; f.target = '_blank';
    var inp = document.createElement('input');
    inp.type = 'hidden'; inp.name = 'gynaec_rx_id'; inp.value = rxId;
    f.appendChild(inp); document.body.appendChild(f); f.submit(); document.body.removeChild(f);
}
</script>
