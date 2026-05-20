<?php
// FIX_B_023: orphan SSE producer removed (cross-tenant PHI leak, no consumer).
// Original file streamed appointment_online rows without auth or tenant scope.
// Audit grep across the repo found zero EventSource subscribers to this path.
// We tombstone it instead of git-rm so accidental re-introduction fails loudly.
http_response_code(410);
header('Content-Type: text/plain; charset=utf-8');
echo "Gone: ajax/AppointmentBooking/sse.php was removed (B-023, cross-tenant leak).\n";
exit;
