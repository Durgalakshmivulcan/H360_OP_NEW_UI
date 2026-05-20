<?php
require_once("../../config/functions.php");
// FIX_B_2252: specialization gate — only doctors whose doctors.doctor_specialization
// is in menus.restricted_to_specializations for gynaec_prescription.php may hit
// these AJAX endpoints. SA + non-doctor users (admin/receptionist/etc.) are
// bypassed inside userMaySeeBySpecialization(). 403 JSON for AJAX mode.
if (function_exists('requireSpecializationFor')) requireSpecializationFor('gynaec_prescription.php', 'ajax');


// FIX_B_027: require login + load SessionOrgId.
require_once(__DIR__ . '/../../include/auth_guard.php');
requireLogin();
$SessionOrgId = $_SESSION['org_id'] ?? '';

$id = (int)($_POST['gynaec_rx_id'] ?? 0);
if (!$id) { exit('Invalid request.'); }

$qry = mysqli_query($conn, "SELECT * FROM gynaec_prescriptions WHERE gynaec_rx_id='$id' AND status='1' AND org_id='$SessionOrgId' LIMIT 1");
$r   = mysqli_fetch_assoc($qry);
if (!$r) { exit('Record not found.'); }

$meds = json_decode($r['medicines_json']      ?? '[]', true) ?: [];
$invs = json_decode($r['investigations_json'] ?? '[]', true) ?: [];

$orgId  = $r['org_id'];
$orgQry = mysqli_query($conn, "SELECT * FROM organization WHERE org_id='$orgId' AND status='1' LIMIT 1");
$org    = mysqli_fetch_assoc($orgQry);

// Bill size settings
$getSizes = mysqli_query($conn, "SELECT * FROM bill_sizes WHERE status='1' AND pagetype='2' AND org_id='$orgId'");
$resData  = mysqli_fetch_object($getSizes);
$top    = !empty($resData->top)    ? $resData->top    : '150px';
$bottom = !empty($resData->bottom) ? $resData->bottom : '30px';
$getSingleSize  = mysqli_query($conn, "SELECT w_size FROM pagessize WHERE status='1' AND size_id='".(int)($resData->sizes ?? 0)."'");
$resSingleData  = mysqli_fetch_object($getSingleSize);
$pageWidth = !empty($resSingleData->w_size) ? $resSingleData->w_size : '21cm';

// Doctor / signature
$sigQry  = mysqli_query($conn, "SELECT doctor_name, departments FROM doctors WHERE status='1' AND org_id='$orgId' LIMIT 1");
$docRow  = mysqli_fetch_object($sigQry);
$sigImgQry = mysqli_query($conn, "SELECT signature_url FROM security WHERE status='1' AND org_id='$orgId' LIMIT 1");
$sigImg    = mysqli_fetch_object($sigImgQry);
$depQry  = mysqli_query($conn, "SELECT departmentName FROM department WHERE status='1' AND dept_id='".(int)($docRow->departments ?? 0)."' LIMIT 1");
$depRow  = mysqli_fetch_object($depQry);

function h($v){ return htmlspecialchars(trim($v ?? ''), ENT_QUOTES); }
function fdate($d){ return (!empty($d) && $d !== '0000-00-00') ? date('d/m/Y', strtotime($d)) : ''; }
function showField($label, $value, $upper = false){
    $v = trim($value ?? '');
    if ($v === '') return;
    if ($upper) $v = strtoupper($v);
    echo '<p style="margin-bottom:0px;"><span class="section-title">'.htmlspecialchars($label).' :</span></p>';
    echo '<div class="border-box"><p style="font-size:13px;margin-bottom:0;white-space:pre-wrap;">'.nl2br(htmlspecialchars($v)).'</p></div>';
}

// Patient name display
$patientName = strtoupper(trim($r['patient_name'] ?? ''));
$nameParts   = explode(' ', ucwords(strtolower($patientName)));
$firstLine   = implode(' ', array_slice($nameParts, 0, 2));
$secondLine  = implode(' ', array_slice($nameParts, 2));
$hasMeds     = !empty($meds);
$hasInvs     = !empty($invs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Gynaec Prescription – <?= h($r['patient_name']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: white; font-family: Arial, serif; font-size: 12px; }

page {
    background: white;
    display: block;
    margin: 0 auto;
    margin-bottom: 0.5cm;
    position: relative;
    font-family: Arial, serif;
    font-size: 12px;
}
page[size="A4"] {
    width: <?= $pageWidth ?>;
    min-height: 29.2cm;
    margin-top: <?= $top ?>;
    margin-bottom: <?= $bottom ?>;
}

.info-box { border: 1px solid #ddd; display: flex; flex-wrap: wrap; margin-bottom: 10px; }
.col-half { width: 50%; padding: 5px 10px; position: relative; }
.col-half:not(:last-child)::after {
    content: ""; position: absolute; right: 15px; top: 0;
    height: 100%; width: 2px; background-color: #ddd;
}
.info-row { display: flex; align-items: center; }
.info-label { color: #3462ca; min-width: 130px; font-size: 18px; }
.info-value { flex: 1; }

.section-title { font-weight: bold; font-size: 15px; }

.border-box  { border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; }
.border-boxes{ border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; }

.vitals-table { width: 100%; border-collapse: collapse; }
.vitals-table th, .vitals-table td { border: 1px solid #ccc; padding: 5px; text-align: left; }
.vitals-table th { font-weight: bold; font-size: 14px; text-align: center; background-color: #ddd; }

table { width: 100%; border-collapse: collapse; border-spacing: 0; margin-bottom: 20px; }

.print-btn { text-align: right; padding: 8px 16px; }
.print-btn button { padding: 5px 18px; background: #198754; color: #fff; border: none; border-radius: 4px; cursor: pointer; }

@media print {
    .print-btn { display: none !important; }
    @page:first { margin-top: 0mm; margin-left: 10mm; margin-right: 10mm; margin-bottom: 25mm; }
    @page { margin-top: 60mm; margin-left: 10mm; margin-right: 10mm; margin-bottom: 15mm; }
    table, tr, td { page-break-inside: avoid !important; }
}
</style>
</head>
<body>

<div class="print-btn">
    <button onclick="window.print()">Print / Save PDF</button>
</div>

<page size="A4" id="A4">

    <!-- Name / Date / Age-Gender row -->
    <div class="d-flex flex-row align-items-start flex-wrap" style="font-size:17px;">

        <div class="d-flex mt-2 align-items-center" style="flex-basis:24%;">
            <div class="form-group w-100">
                <label class="mb-0 ms-3" style="font-size:17px;font-weight:bold;">Date :</label>
                <span><?= fdate($r['rx_date']) ?></span>
            </div>
        </div>

        <div class="d-flex mt-2 align-items-start" style="flex-basis:40%;min-width:0;">
            <div class="form-group w-100 ms-2" style="min-width:0;">
                <div style="font-size:17px;font-weight:bold;display:inline-block;vertical-align:top;">Name :</div>
                <div style="display:inline-block;font-size:17px;">
                    <?= h($firstLine) ?><br>
                    <span><?= h($secondLine) ?></span>
                </div>
            </div>
        </div>

        <div class="d-flex mt-2 align-items-center" style="flex-basis:36%;">
            <div class="form-group w-100">
                <label class="mb-0 ms-5" style="font-size:17px;font-weight:bold;">Gender / Age :</label>
                <?php
                    $g = strtolower(trim($r['gender'] ?? ''));
                    $gl = $g === 'female' ? 'F' : ($g === 'male' ? 'M' : 'O');
                    echo h($gl.' / '.($r['age'] ?? '')).' Y';
                ?>
            </div>
        </div>

    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card shadow-none" style="border:none !important;">
          <div class="card-body">
            <div class="row">
              <div class="form-group col-lg-12">

                <!-- Info Box -->
                <div class="info-box">
                    <div class="col-half">
                        <div class="info-row">
                            <div style="font-size:17px;font-weight:bold;">Mobile</div>
                            <div style="min-width:10px;font-size:17px;font-weight:bold;">:</div>
                            <h6 style="margin-bottom:0;font-size:17px;"><?= h($r['mobile']) ?></h6>
                        </div>
                        <div class="info-row">
                            <div style="font-size:17px;font-weight:bold;">Organisation</div>
                            <div style="min-width:10px;font-size:17px;font-weight:bold;">:</div>
                            <h6 style="margin-top:5px;margin-bottom:0;font-size:17px;"><?= h(strtoupper($org['organization_name'] ?? '')) ?></h6>
                        </div>
                        <?php if (!empty(trim($r['ref_by'] ?? ''))): ?>
                        <div class="info-row">
                            <div style="font-size:17px;font-weight:bold;">Ref By</div>
                            <div style="min-width:10px;font-size:17px;font-weight:bold;">:</div>
                            <h6 style="margin-top:5px;margin-bottom:0;font-size:17px;"><?= h(strtoupper($r['ref_by'])) ?></h6>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="col-half">
                        <div class="info-row">
                            <div style="font-size:17px;font-weight:bold;">UMR No</div>
                            <div style="min-width:10px;font-size:17px;font-weight:bold;">:</div>
                            <h6 style="margin-top:5px;margin-bottom:0;font-size:17px;"><?= h(strtoupper($r['patient_id'] ?? '')) ?></h6>
                        </div>
                        <div class="info-row">
                            <div style="font-size:17px;font-weight:bold;">Appointment No</div>
                            <div style="min-width:10px;font-size:17px;font-weight:bold;">:</div>
                            <h6 style="margin-top:5px;margin-bottom:0;font-size:17px;"><?= h(strtoupper($r['appointment_id'] ?? '')) ?></h6>
                        </div>
                        <?php if (!empty(trim($r['review_after'] ?? ''))): ?>
                        <div class="info-row">
                            <div style="font-size:17px;font-weight:bold;">Review After</div>
                            <div style="min-width:10px;font-size:17px;font-weight:bold;">:</div>
                            <h6 style="margin-top:5px;margin-bottom:0;font-size:17px;"><?= h($r['review_after']) ?></h6>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Clinical Details -->
                <?php
                showField('Chief Complaints / HPI', $r['chief_complaints'], true);
                showField('Final Diagnosis',         $r['final_diagnosis'],  true);
                ?>
                <?php
                $hasClinOther = !empty(trim($r['gynaec_history']??'')) || !empty(trim($r['obstetric_history']??''))
                             || !empty(trim($r['family_history']??''))  || !empty(trim($r['personal_history']??''))
                             || !empty(trim($r['general_examination']??'')) || !empty(trim($r['previous_investigations']??''));
                if ($hasClinOther): ?>
                <div style="display:flex;gap:20px;flex-wrap:wrap;">
                    <?php if (!empty(trim($r['gynaec_history']??''))): ?>
                    <div style="flex:1;min-width:200px;">
                        <p style="margin-bottom:0;"><span class="section-title">Gynaec History :</span></p>
                        <div class="border-box"><p style="font-size:13px;margin-bottom:0;white-space:pre-wrap;"><?= nl2br(h(strtoupper($r['gynaec_history']))) ?></p></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty(trim($r['obstetric_history']??''))): ?>
                    <div style="flex:1;min-width:200px;">
                        <p style="margin-bottom:0;"><span class="section-title">Obstetric History :</span></p>
                        <div class="border-box"><p style="font-size:13px;margin-bottom:0;white-space:pre-wrap;"><?= nl2br(h(strtoupper($r['obstetric_history']))) ?></p></div>
                    </div>
                    <?php endif; ?>
                </div>
                <div style="display:flex;gap:20px;flex-wrap:wrap;">
                    <?php if (!empty(trim($r['family_history']??''))): ?>
                    <div style="flex:1;min-width:200px;">
                        <p style="margin-bottom:0;"><span class="section-title">Family History :</span></p>
                        <div class="border-box"><p style="font-size:13px;margin-bottom:0;white-space:pre-wrap;"><?= nl2br(h(strtoupper($r['family_history']))) ?></p></div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty(trim($r['personal_history']??''))): ?>
                    <div style="flex:1;min-width:200px;">
                        <p style="margin-bottom:0;"><span class="section-title">Personal History :</span></p>
                        <div class="border-box"><p style="font-size:13px;margin-bottom:0;white-space:pre-wrap;"><?= nl2br(h(strtoupper($r['personal_history']))) ?></p></div>
                    </div>
                    <?php endif; ?>
                </div>
                <?php showField('General Examination',     $r['general_examination'],    true); ?>
                <?php showField('Previous Investigations', $r['previous_investigations'], true); ?>
                <?php endif; ?>

                <!-- Menstrual & Obstetric -->
                <?php
                $lmpStr = fdate($r['lmp']); $eddStr = fdate($r['edd']);
                $hasMen = !empty(trim($r['menstrual_history']??'')) || !empty($lmpStr)
                       || !empty(trim($r['pmc']??''))               || !empty($eddStr)
                       || !empty(trim($r['risk_factors']??''));
                if ($hasMen): ?>
                <p style="margin-bottom:0;"><span class="section-title">Menstrual &amp; Basic Details :</span></p>
                <div class="border-box">
                    <?php if (!empty(trim($r['menstrual_history']??''))): ?>
                    <div style="font-size:13px;margin-bottom:4px;"><strong>Menstrual History :</strong> <?= nl2br(h($r['menstrual_history'])) ?></div>
                    <?php endif; ?>
                    <div style="display:flex;gap:30px;flex-wrap:wrap;font-size:13px;">
                        <?php if (!empty($lmpStr)): ?><div><strong>LMP :</strong> <?= h($lmpStr) ?></div><?php endif; ?>
                        <?php if (!empty(trim($r['pmc']??''))): ?><div><strong>PMC :</strong> <?= h($r['pmc']) ?></div><?php endif; ?>
                        <?php if (!empty($eddStr)): ?><div><strong>EDD :</strong> <?= h($eddStr) ?></div><?php endif; ?>
                    </div>
                    <?php if (!empty(trim($r['risk_factors']??''))): ?>
                    <div style="font-size:13px;margin-top:4px;"><strong>Risk Factors :</strong> <?= nl2br(h($r['risk_factors'])) ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Scan Details -->
                <?php
                $scanDate = fdate($r['scan_date']);
                $hasScan  = !empty(trim($r['scan_type']??''))     || !empty($scanDate)
                         || !empty(trim($r['scan_findings']??'')) || !empty(trim($r['scan_remarks']??''));
                if ($hasScan): ?>
                <p style="margin-bottom:0;"><span class="section-title">Scan Details :</span></p>
                <div class="border-box">
                    <div style="display:flex;gap:30px;flex-wrap:wrap;font-size:13px;">
                        <?php if (!empty(trim($r['scan_type']??''))): ?><div><strong>Type :</strong> <?= h($r['scan_type']) ?></div><?php endif; ?>
                        <?php if (!empty($scanDate)): ?><div><strong>Date :</strong> <?= h($scanDate) ?></div><?php endif; ?>
                    </div>
                    <?php if (!empty(trim($r['scan_findings']??''))): ?>
                    <div style="font-size:13px;margin-top:4px;"><strong>Findings :</strong> <?= nl2br(h($r['scan_findings'])) ?></div>
                    <?php endif; ?>
                    <?php if (!empty(trim($r['scan_remarks']??''))): ?>
                    <div style="font-size:13px;margin-top:4px;"><strong>Remarks :</strong> <?= nl2br(h($r['scan_remarks'])) ?></div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Medicines -->
                <?php if ($hasMeds): ?>
                <div class="section-title">Medicine:</div>
                <div class="form-group col-lg-12">
                    <table class="vitals-table">
                        <thead>
                            <tr>
                                <?php
                                $first = $meds[0];
                                $medName    = $first['drugName']    ?? $first['medicine_name'] ?? '';
                                $medType    = $first['typeText']    ?? $first['medicine_type'] ?? '';
                                $medUnit    = $first['unitText']    ?? $first['unit']          ?? '';
                                $medDosage  = $first['dosageText']  ?? $first['dose']          ?? '';
                                $medTime    = $first['timeText']    ?? $first['time']          ?? '';
                                $medWhen    = $first['whenText']    ?? $first['frequency']     ?? '';
                                $medDurVal  = $first['duration_value'] ?? '';
                                $medDur     = $first['duration']    ?? '';
                                $medRoute   = $first['route']       ?? '';
                                $medNotes   = $first['notes']       ?? $first['instructions']  ?? '';
                                if (!empty($medName) || !empty($medType) || !empty($medUnit)) echo '<th>Medicine</th>';
                                if (!empty($medDosage) || !empty($medTime)) echo '<th>Dosage</th>';
                                if (!empty($medWhen))   echo '<th>In-take-period</th>';
                                if (!empty($medDurVal) || !empty($medDur)) echo '<th>Duration</th>';
                                if (!empty($medRoute))  echo '<th>Route</th>';
                                if (!empty($medNotes))  echo '<th>Instructions</th>';
                                ?>
                            </tr>
                        </thead>
                        <tbody style="font-weight:500;font-size:13px;">
                        <?php foreach ($meds as $m):
                            $mn   = $m['drugName']      ?? $m['medicine_name'] ?? '';
                            $mt   = $m['typeText']       ?? $m['medicine_type'] ?? '';
                            $mu   = $m['unitText']       ?? $m['unit']          ?? '';
                            $mdos = $m['dosageText']     ?? $m['dose']          ?? '';
                            $mtim = $m['timeText']       ?? $m['time']          ?? '';
                            $mwh  = $m['whenText']       ?? $m['frequency']     ?? '';
                            $mdv  = $m['duration_value'] ?? '';
                            $mdu  = $m['duration']       ?? '';
                            $mro  = $m['route']          ?? '';
                            $mno  = $m['notes']          ?? $m['instructions']  ?? '';
                        ?>
                        <tr>
                            <?php if (!empty($medName) || !empty($medType) || !empty($medUnit)): ?>
                            <td>
                                <?php if (!empty($mt) || !empty($mn)): ?>
                                <div><?= h($mt) ?><?= (!empty($mt) && !empty($mn)) ? ' - ' : '' ?><?= h($mn) ?></div>
                                <?php endif; ?>
                                <?php if (!empty($mu)): ?>
                                <div><span style="font-size:13px;">Unit/Vol.</span> - <span style="font-size:13px;"><?= h($mu) ?></span></div>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                            <?php if (!empty($medDosage) || !empty($medTime)): ?>
                            <td>
                                <?= !empty($mdos) ? h($mdos).'<br>' : '' ?>
                                <?php if (!empty($mtim)):
                                    $timeparts = array_filter(array_map('trim', explode('-', $mtim)), fn($v) => $v !== '0');
                                    echo '<span style="font-size:12px;">('.h(implode(' - ',$timeparts)).')</span>';
                                endif; ?>
                            </td>
                            <?php endif; ?>
                            <?php if (!empty($medWhen)): ?><td><?= h($mwh) ?></td><?php endif; ?>
                            <?php if (!empty($medDurVal) || !empty($medDur)): ?><td><?= h($mdv) ?> <?= h($mdu) ?></td><?php endif; ?>
                            <?php if (!empty($medRoute)): ?><td><?= h($mro) ?></td><?php endif; ?>
                            <?php if (!empty($medNotes)): ?><td><?= h($mno) ?></td><?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <!-- Investigations -->
                <?php if ($hasInvs): ?>
                <div class="section-title mt-3">Tests:</div>
                <div class="form-group col-lg-12">
                    <table class="vitals-table">
                        <thead>
                            <tr>
                                <th>S.No</th>
                                <th>Investigation</th>
                                <th>Instructions</th>
                            </tr>
                        </thead>
                        <tbody style="font-weight:500;font-size:13px;">
                        <?php $sno = 1; foreach ($invs as $inv): ?>
                        <tr>
                            <td><?= $sno++ ?></td>
                            <td><?= h($inv['investigation_name'] ?? $inv['investigation'] ?? '') ?></td>
                            <td><?= h($inv['instructions'] ?? $inv['instruction'] ?? '') ?></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>

                <!-- Treatment & Plan -->
                <?php
                showField('Plan',         $r['plan'],         true);
                showField('Advice',       $r['advice'],       true);
                showField('Review Notes', $r['review_notes'], true);
                ?>

                <!-- Doctor Signature -->
                <div style="text-align:right;margin-top:50px;padding-right:30px;">
                    <?php
                    $sig_url = $sigImg->signature_url ?? '';
                    if (!empty($sig_url)) {
                        echo "<img src='../../signature/".h($sig_url)."' alt='Signature' style='max-width:300px;max-height:100px;'>";
                    }
                    $doctorName = !empty(trim($r['doctor_name'] ?? '')) ? $r['doctor_name']
                                : (!empty($docRow->doctor_name) ? $docRow->doctor_name : '');
                    $doctorCred = trim($r['doctor_credentials'] ?? '');
                    $deptName   = $depRow->departmentName ?? '';
                    ?>
                    <?php if (!empty($doctorName)): ?>
                    <div class="info-label" style="font-weight:bold;font-size:16px;"><?= h(strtoupper($doctorName)) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($doctorCred)): ?>
                    <div style="font-weight:bold;font-size:14px;"><?= h($doctorCred) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($deptName)): ?>
                    <div class="info-label" style="font-weight:bold;font-size:16px;"><?= h($deptName) ?></div>
                    <?php endif; ?>
                </div>

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

</page>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script>
const mainContent = document.querySelector('#A4');
function getURLParam(p){ return new URLSearchParams(window.location.search).get(p); }
let topMargin = parseInt(getURLParam('topMargin')) || 100;
mainContent.style.marginTop = topMargin + 'px';
</script>
</body>
</html>
