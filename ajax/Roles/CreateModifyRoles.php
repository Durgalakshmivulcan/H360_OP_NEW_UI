<?php
// FIX_B_066: 0-byte abandoned endpoint. Active writer is
// ajax/accesscontrol/roles/insertupdate.php. Tombstone to fail loudly.
http_response_code(410);
header('Content-Type: text/plain; charset=utf-8');
echo "Gone: ajax/Roles/CreateModifyRoles.php removed (B-066). Use ajax/accesscontrol/roles/insertupdate.php.\n";
exit;
