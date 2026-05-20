<?php
require_once("../../config/functions.php");

// FIX_B_026: require login and pull SessionOrgId from session.
require_once(__DIR__ . '/../../include/auth_guard.php');
requireLogin();
$SessionOrgId = $_SESSION['org_id'] ?? '';

$id = (int)($_POST['echo_report_id'] ?? 0);
if (!$id) { exit('Invalid request.'); }

$qry = mysqli_query($conn, "SELECT * FROM echo_reports WHERE echo_report_id='$id' AND status='1' AND org_id='$SessionOrgId' LIMIT 1");
$r   = mysqli_fetch_assoc($qry);
if (!$r) { exit('Record not found.'); }

// Org / logo
$orgId  = $r['org_id'];
$orgQry = mysqli_query($conn, "SELECT * FROM organization WHERE org_id='$orgId' AND status='1' LIMIT 1");
$org    = mysqli_fetch_assoc($orgQry);

$_host    = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$_appRoot = rtrim(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
$baseUrl  = $_host . $_appRoot;

$uploadDir  = __DIR__ . '/../../organisation_images/';
$logoFile   = $org['logo'] ?? '';
$logoSrc    = (!empty($logoFile) && file_exists($uploadDir . $logoFile))
    ? $baseUrl . '/organisation_images/' . $logoFile
    : $baseUrl . '/assets/img/h360.png';

// Format date
$reportDateDisplay = !empty($r['report_date']) ? date('d/m/Y', strtotime($r['report_date'])) : date('d/m/Y');

// Helpers
function val($v, $suffix = '') {
    return (!empty(trim($v ?? ''))) ? htmlspecialchars(trim($v)) . $suffix : '-';
}
function valRaw($v) {
    return trim($v ?? '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>2D Echo Report – <?= htmlspecialchars($r['patient_name']) ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #fff; font-family: "Times New Roman", Times, serif; font-size: 13px; color: #000; }

        .print-btn { position: sticky; top: 8px; z-index: 100; text-align: right; padding: 6px 16px; background: #fff; }
        .print-btn button { padding: 5px 18px; background: #198754; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; }

        @media print { .print-btn { display: none !important; } }

        .page {
            width: 21cm;
            min-height: 29.7cm;
            margin: 0 auto;
            padding: 1.4cm 1.6cm 1.8cm;
        }

        /* ---- Header (logo + org) ---- */
        .header { display: flex; align-items: flex-start; margin-bottom: 10px; }
        .header-org { flex: 1; }
        .header-org h3 { font-size: 15px; margin-bottom: 3px; }
        .header-org p  { font-size: 12px; color: #444; }
        .header-logo   { text-align: right; }
        .header-logo img { max-width: 160px; max-height: 100px; }

        /* ---- Report title ---- */
        .report-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 10px 0 6px;
            text-decoration: underline;
        }

        /* ---- Patient info grid ---- */
        .patient-info { margin-bottom: 4px; }
        .pi-row { display: flex; justify-content: space-between; margin-bottom: 3px; font-size: 13px; }
        .pi-left  { flex: 1; }
        .pi-right { min-width: 240px; text-align: right; }
        .pi-label { font-weight: bold; }

        /* ---- Divider ---- */
        .divider { border: none; border-top: 1.5px solid #000; margin: 6px 0 8px; }

        /* ---- Report fields ---- */
        .field-row {
            display: flex;
            align-items: baseline;
            margin-bottom: 4px;
            font-size: 13px;
        }
        .f-label {
            width: 200px;
            min-width: 200px;
            font-weight: normal;
        }
        .f-colon { width: 20px; text-align: center; }
        .f-value { flex: 1; }

        /* Rows that have a right-side value on same line */
        .field-row-dual { display: flex; align-items: baseline; margin-bottom: 4px; font-size: 13px; }
        .field-row-dual .f-label { width: 200px; min-width: 200px; }
        .field-row-dual .f-colon { width: 20px; text-align: center; }
        .field-row-dual .f-value-left  { flex: 1; }
        .field-row-dual .f-right-block { display: flex; align-items: baseline; gap: 6px; }
        .f-right-label { font-weight: normal; }
        .f-unit { color: #333; margin-left: 2px; font-size: 12px; }

        /* LV sub-rows (indented, two-column layout matching document) */
        .lv-block { padding-left: 220px; margin-bottom: 4px; }
        .lv-row   { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 3px; font-size: 13px; }
        .lv-left  { display: flex; align-items: baseline; gap: 6px; min-width: 200px; }
        .lv-right { display: flex; align-items: baseline; gap: 6px; min-width: 220px; justify-content: flex-start; }
        .lv-lbl   { min-width: 72px; }

        /* Doppler */
        .doppler-row { padding-left: 30%; margin-bottom: 6px; font-size: 13px; }

        /* Conclusion */
        .conclusion-block { margin-top: 6px; }
        .conclusion-label { font-weight: bold; font-size: 13px; margin-bottom: 4px; }
        .conclusion-text { font-size: 13px; white-space: pre-wrap; line-height: 1.6; }

        /* Signature */
        .signature { text-align: right; margin-top: 40px; font-size: 13px; }
        .signature .doc-name { font-weight: bold; }

        .spacer { margin-bottom: 6px; }
    </style>
</head>
<body>

<div class="print-btn no-print">
    <button onclick="window.print()">Print</button>
</div>

<div class="page">

    <!-- Organisation header -->
    <div class="header">
        <div class="header-org">
            <h3><?= htmlspecialchars($org['organization_name'] ?? '') ?></h3>
            <p><?= htmlspecialchars($org['address'] ?? '') ?></p>
        </div>
        <div class="header-logo">
            <img src="<?= $logoSrc ?>" alt="Logo">
        </div>
    </div>

    <!-- Report title -->
    <div class="report-title">2D ECHOCARDIOGRAPHIC REPORT</div>

    <!-- Patient info -->
    <div class="patient-info">
        <div class="pi-row">
            <div class="pi-left"><span class="pi-label">NAME &nbsp;&nbsp;&nbsp;:</span> <strong><?= htmlspecialchars(strtoupper($r['patient_name'])) ?></strong></div>
            <div class="pi-right"><span class="pi-label">Date :</span> <strong><?= $reportDateDisplay ?></strong></div>
        </div>
        <div class="pi-row">
            <div class="pi-left"><span class="pi-label">Age / Sex :</span> <strong><?= htmlspecialchars($r['age']) ?>/<?= htmlspecialchars(strtoupper(substr($r['gender'] ?? '', 0, 1))) ?></strong></div>
            <div class="pi-right"><span class="pi-label">Ref By :</span> <strong><?= htmlspecialchars(strtoupper($r['ref_by'] ?? '')) ?></strong></div>
        </div>
        <div class="pi-row">
            <div class="pi-left"><span class="pi-label">Indication :</span> <strong><?= htmlspecialchars(strtoupper($r['indication'] ?? '')) ?></strong></div>
        </div>
    </div>

    <hr class="divider">

    <!-- Valves -->
    <div class="field-row">
        <div class="f-label">MITRAL VALVE</div>
        <div class="f-colon">:</div>
        <div class="f-value"><?= htmlspecialchars($r['mitral_valve'] ?? 'Normal') ?></div>
    </div>
    <div class="spacer"></div>

    <div class="field-row">
        <div class="f-label">AORTIC VALVE</div>
        <div class="f-colon">:</div>
        <div class="f-value"><?= htmlspecialchars($r['aortic_valve'] ?? 'Normal') ?></div>
    </div>
    <div class="field-row">
        <div class="f-label">TRICUSPID VALVE</div>
        <div class="f-colon">:</div>
        <div class="f-value"><?= htmlspecialchars($r['tricuspid_valve'] ?? 'Normal') ?></div>
    </div>
    <div class="field-row">
        <div class="f-label">PULMONARY VALVE</div>
        <div class="f-colon">:</div>
        <div class="f-value"><?= htmlspecialchars($r['pulmonary_valve'] ?? 'Normal') ?></div>
    </div>
    <div class="field-row">
        <div class="f-label">LEFT ATRIUM</div>
        <div class="f-colon">:</div>
        <div class="f-value"><?= htmlspecialchars($r['left_atrium'] ?? '') ?> cm</div>
    </div>

    <!-- Left Ventricle -->
    <div class="field-row">
        <div class="f-label">LEFT VENTRICLE</div>
        <div class="f-colon">:</div>
        <div class="f-value"></div>
    </div>
    <div class="lv-block">
        <!-- Row 1: LVID(d) left  |  IVS right -->
        <div class="lv-row">
            <div class="lv-left">
                <span class="lv-lbl">LVID (d) :</span>
                <strong><?= htmlspecialchars($r['lvid_d'] ?? '') ?></strong>&nbsp;cm
            </div>
            <div class="lv-right">
                <span class="lv-lbl">IVS &nbsp;&nbsp;&nbsp;:</span>
                <strong><?= htmlspecialchars($r['ivs_thickness'] ?? '') ?></strong>&nbsp;cm
            </div>
        </div>
        <!-- Row 2: LVID(s) left  |  PWD right -->
        <div class="lv-row">
            <div class="lv-left">
                <span class="lv-lbl">LVID (s) :</span>
                <strong><?= htmlspecialchars($r['lvid_s'] ?? '') ?></strong>&nbsp;cm
            </div>
            <div class="lv-right">
                <span class="lv-lbl">PWD &nbsp;&nbsp;:</span>
                <strong><?= htmlspecialchars($r['pwd'] ?? '') ?></strong>&nbsp;cm
            </div>
        </div>
        <!-- Row 3: EF left  (no right column) -->
        <div class="lv-row">
            <div class="lv-left">
                <strong class="lv-lbl">EF &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:</strong>
                <strong><?= htmlspecialchars($r['ef'] ?? '') ?></strong>&nbsp;%
            </div>
            <div class="lv-right"></div>
        </div>
    </div>

    <div class="field-row">
        <div class="f-label">LV RWMA</div>
        <div class="f-colon">:</div>
        <div class="f-value"><?= htmlspecialchars($r['lv_rwma'] ?? 'NO RWMA') ?></div>
    </div>
    <div class="spacer"></div>

    <!-- Right side -->
    <div class="field-row">
        <div class="f-label">RIGHT ATRIUM</div>
        <div class="f-colon">:</div>
        <div class="f-value"><?= htmlspecialchars($r['right_atrium'] ?? 'Normal') ?></div>
    </div>
    <div class="field-row">
        <div class="f-label">RIGHT VENTRICLE</div>
        <div class="f-colon">:</div>
        <div class="f-value">
            <?= htmlspecialchars($r['right_ventricle'] ?? 'Normal') ?>
            <?php if (!empty(trim($r['tapse'] ?? ''))): ?>
                , TAPSE = <strong><?= htmlspecialchars($r['tapse']) ?> cm</strong>
            <?php endif; ?>
        </div>
    </div>

    <!-- Aorta + AJV -->
    <div class="field-row-dual">
        <div class="f-label">AORTA</div>
        <div class="f-colon">:</div>
        <div class="f-value-left"><?= htmlspecialchars($r['aorta'] ?? '') ?> cm</div>
        <div class="f-right-block">
            <span class="f-right-label">AJV :</span>
            <strong><?= htmlspecialchars($r['ajv'] ?? '') ?></strong>&nbsp;m/sec
        </div>
    </div>

    <!-- Pulmonary Artery + PJV -->
    <div class="field-row-dual">
        <div class="f-label">PULMONARY ARTERY</div>
        <div class="f-colon">:</div>
        <div class="f-value-left"><?= htmlspecialchars($r['pulmonary_artery'] ?? 'Normal') ?></div>
        <div class="f-right-block">
            <span class="f-right-label">PJV &nbsp;:</span>
            <strong><?= htmlspecialchars($r['pjv'] ?? '') ?></strong>&nbsp;m/sec
        </div>
    </div>

    <!-- IVS + Mitral Flow -->
    <div class="field-row-dual">
        <div class="f-label">IVS</div>
        <div class="f-colon">:</div>
        <div class="f-value-left"><?= htmlspecialchars($r['ivs_status'] ?? 'Intact') ?></div>
        <?php if (!empty(trim($r['mitral_flow'] ?? ''))): ?>
        <div class="f-right-block">
            <span class="f-right-label">MITRAL FLOW :</span>
            <?= htmlspecialchars($r['mitral_flow']) ?>
        </div>
        <?php endif; ?>
    </div>

    <div class="field-row">
        <div class="f-label">IAS</div>
        <div class="f-colon">:</div>
        <div class="f-value"><?= htmlspecialchars($r['ias_status'] ?? 'Intact') ?></div>
    </div>
    <div class="field-row">
        <div class="f-label">IVC/SVC/CS</div>
        <div class="f-colon">:</div>
        <div class="f-value"><?= htmlspecialchars($r['ivc_svc_cs'] ?? 'Normal') ?></div>
    </div>
    <div class="field-row">
        <div class="f-label">PERICARDIUM</div>
        <div class="f-colon">:</div>
        <div class="f-value"><?= htmlspecialchars($r['pericardium'] ?? 'No PE') ?></div>
    </div>

    <!-- Doppler -->
    <div class="field-row">
        <div class="f-label">DOPPLER STUDY</div>
        <div class="f-colon">:</div>
        <div class="f-value"></div>
    </div>
    <div class="doppler-row">
        <strong>MR</strong> &ndash; <?= htmlspecialchars($r['doppler_mr'] ?? 'NO') ?> &nbsp;&nbsp;&nbsp;
        <strong>AR</strong> &ndash; <?= htmlspecialchars($r['doppler_ar'] ?? 'NO') ?> &nbsp;&nbsp;&nbsp;
        <strong>TR</strong> &ndash; <?= htmlspecialchars($r['doppler_tr'] ?? 'NO') ?> &nbsp;&nbsp;&nbsp;
        <strong>PR</strong> &ndash; <?= htmlspecialchars($r['doppler_pr'] ?? 'NO') ?>
    </div>

    <!-- Conclusion -->
    <?php if (!empty(trim($r['conclusion'] ?? ''))): ?>
    <div class="spacer"></div>
    <div class="conclusion-block">
        <div class="conclusion-label">CONCLUSION :</div>
        <div class="conclusion-text"><strong><?= htmlspecialchars(trim($r['conclusion'])) ?></strong></div>
    </div>
    <?php endif; ?>

    <!-- Doctor signature -->
    <div class="signature">
        <?php if (!empty(trim($r['doctor_name'] ?? ''))): ?>
            <div class="doc-name">Dr <?= htmlspecialchars(strtoupper(trim($r['doctor_name']))) ?></div>
        <?php endif; ?>
        <?php if (!empty(trim($r['doctor_credentials'] ?? ''))): ?>
            <div><strong><?= htmlspecialchars(trim($r['doctor_credentials'])) ?></strong></div>
        <?php endif; ?>
    </div>

</div><!-- .page -->

</body>
</html>
