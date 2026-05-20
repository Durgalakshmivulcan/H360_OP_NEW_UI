<?php
// FIX_B_164: dead admin demo page with SQLi surface against phantom tables.
// Documented for deletion; tombstoned so accidental callers fail visibly.
http_response_code(410);
header('Content-Type: text/plain; charset=utf-8');
echo "Gone: dsample.php removed (B-164, dead SQLi-prone admin demo).\n";
exit;
