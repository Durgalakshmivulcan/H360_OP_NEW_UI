<?php require_once('ajax/header.php'); ?>
<?php
// FIX_B_1850: per-action RBAC gate. Receptionist/Pharmacist/etc. are bounced
// back to dashboard; SA (security_id=1 OR role_id=1) is auto-allowed.
requireCan('view', basename(__FILE__));
?>

<?php
/* B-2100: Roles page redesigned to a 2-column "Sovereign Institutional"
   permission matrix. Backend POST contract preserved (`menu_id[]`,
   `perm_<action>_<id>`, hidden `#access<id>`); existing JS handlers
   `savemenus`, `editroles`, `selecteall`, `unselect`, `deleteroles` retained.
   Markup classes `.perm-view/.perm-add/.perm-edit/.perm-delete` kept so the
   B-1800-1 regression spec stays green. */
?>

<div class="main-content roles-redesign" data-page="roles">
  <section class="section">
    <ul class="breadcrumb breadcrumb-style">
      <li class="breadcrumb-item"><h4 class="page-title m-b-0">Access Control</h4></li>
      <li class="breadcrumb-item active">Roles</li>
    </ul>

    <div class="rr-grid">
      <!-- ============================== LEFT: ROLES LIST ============================== -->
      <aside class="rr-roles-pane">
        <div class="rr-card rr-roles-card">
          <div class="rr-card-head">
            <span class="rr-eyebrow">Directory</span>
            <h5 class="rr-card-title">Roles</h5>
          </div>
          <div class="rr-roles-toolbar">
            <button type="button" class="rr-btn rr-btn-primary rr-btn-block" id="rrAddRoleBtn">
              <i class="fa fa-plus"></i> Add New Role
            </button>
            <div class="rr-search">
              <i class="fa fa-search"></i>
              <input type="text" id="rrRoleFilter" placeholder="Filter roles…" autocomplete="off">
            </div>
          </div>
          <div class="rr-roles-list-wrap">
            <div id="roles" class="rr-roles-list">
              <div class="rr-loading">Loading roles…</div>
            </div>
            <!-- Hidden DataTable mount-point kept to preserve existing JS -->
            <div id="tableExport_wrapper" style="display:none;"></div>
          </div>
        </div>
      </aside>

      <!-- ============================== RIGHT: PERMISSION MATRIX ============================== -->
      <main class="rr-form-pane">
        <form method="POST" action="" id="rolesformid" autocomplete="off">

          <!-- Sticky top action bar -->
          <div class="rr-sticky-bar rr-sticky-top">
            <div class="rr-bar-left">
              <span class="rr-eyebrow" id="rrModeLabel">Creating new role</span>
              <div class="rr-name-field">
                <input type="hidden" name="role_id" id="role_id" value="">
                <input class="rr-input" name="role_name" id="role_name" placeholder="Role name (e.g. Pharmacist)" required>
              </div>
            </div>
            <div class="rr-bar-right">
              <span class="rr-dirty-dot" id="rrDirtyDot" title="Unsaved changes" aria-hidden="true"></span>
              <button type="button" class="rr-btn rr-btn-ghost" id="rrCancelBtn">Cancel</button>
              <?php if (userCan('add', basename(__FILE__)) || userCan('edit', basename(__FILE__))) { ?>
              <button type="button" class="rr-btn rr-btn-primary" name="savemenus" id="savemenus">
                <i class="fa fa-save"></i> Save Role
              </button>
              <?php } ?>
            </div>
          </div>

          <!-- Permission matrix body -->
          <div class="rr-matrix" id="getmenu">
            <?php
            // Compose menus query identical to original (SA gets all, others gated by role_menus).
            if ($SessionUserId == "1") {
              $qryGetRoleMenus = mysqli_query($conn, "SELECT * FROM menus WHERE status='1' AND menu_type='p' ORDER BY menu_order")
                or die(mysqli_error($conn));
              $childWhereExtra = "";
            } else {
              $allowedMenusQuery = mysqli_query($conn, "SELECT menu_id FROM role_menus WHERE role_id = '$SessionRoleId'")
                or die(mysqli_error($conn));
              $allowedMenuIds = [];
              while ($row = mysqli_fetch_assoc($allowedMenusQuery)) { $allowedMenuIds[] = $row['menu_id']; }
              $menuIdsStr = implode(',', array_map('intval', $allowedMenuIds));
              if ($menuIdsStr === '') { $menuIdsStr = '0'; }
              $qryGetRoleMenus = mysqli_query($conn,
                "SELECT * FROM menus WHERE status='1' AND menu_type='p' AND menu_id IN ($menuIdsStr) ORDER BY menu_order"
              ) or die(mysqli_error($conn));
              $childWhereExtra = " AND menu_id IN ($menuIdsStr) ";
            }

            $parentIdx = 0;
            while ($resGetRoleMenus = mysqli_fetch_object($qryGetRoleMenus)):
              $pid = (int)$resGetRoleMenus->menu_id;
              $childSql = "SELECT * FROM menus WHERE status='1' AND menu_type='s' "
                        . (isset($childWhereExtra) ? $childWhereExtra : "")
                        . " AND parent_id='$pid' ORDER BY menu_order";
              $qryGetRoleSubMenus = mysqli_query($conn, $childSql) or die(mysqli_error($conn));
              $childCount = mysqli_num_rows($qryGetRoleSubMenus);
              $expanded = ($parentIdx < 2); // first two open by default
              $parentIdx++;
            ?>
              <section class="rr-pcard <?= $expanded ? 'is-open' : '' ?>" data-parent-id="<?= $pid ?>">
                <header class="rr-pcard-head">
                  <label class="rr-pcheck">
                    <input type="checkbox" name="menu_id[]" id="menu<?= $pid ?>"
                           value="<?= $pid ?>" onclick='selecteall(<?= $pid ?>)'>
                    <span class="rr-pcheck-box" aria-hidden="true"></span>
                    <span class="rr-pcheck-name"><?= htmlspecialchars($resGetRoleMenus->menu_name) ?></span>
                  </label>
                  <div class="rr-pcard-meta">
                    <span class="rr-count" data-count="<?= $childCount ?>"><?= $childCount ?> sub-menu<?= $childCount === 1 ? '' : 's' ?></span>
                    <button type="button" class="rr-mini" data-bulk="all" data-parent="<?= $pid ?>" title="Grant all permissions">All</button>
                    <button type="button" class="rr-mini" data-bulk="none" data-parent="<?= $pid ?>" title="Clear all permissions">None</button>
                    <button type="button" class="rr-toggle" aria-expanded="<?= $expanded ? 'true' : 'false' ?>" aria-controls="rrBody<?= $pid ?>" title="Collapse / expand">
                      <i class="fa fa-chevron-down"></i>
                    </button>
                  </div>
                </header>

                <div class="rr-pcard-body" id="rrBody<?= $pid ?>">
                  <?php if ($childCount === 0): ?>
                    <div class="rr-empty">No sub-menus configured for this section.</div>
                  <?php else: ?>
                    <table class="rr-perm-table">
                      <thead>
                        <tr>
                          <th class="rr-th-name">Sub-menu</th>
                          <th class="rr-th-act">View</th>
                          <th class="rr-th-act">Add</th>
                          <th class="rr-th-act">Edit</th>
                          <th class="rr-th-act">Delete</th>
                          <th class="rr-th-bulk" aria-label="Row actions"></th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php while ($resGetRoleSubMenus = mysqli_fetch_object($qryGetRoleSubMenus)):
                        $cid = (int)$resGetRoleSubMenus->menu_id;
                      ?>
                        <tr class="rr-prow" data-child-id="<?= $cid ?>" data-parent-id="<?= $pid ?>">
                          <td class="rr-td-name">
                            <label class="rr-subcheck">
                              <input type="checkbox"
                                     class="form-check-input state p-success submenu<?= $pid ?> rr-sub-input"
                                     name="menu_id[]"
                                     id="menu<?= $cid ?>"
                                     value="<?= $cid ?>"
                                     onclick="unselect(<?= $pid ?>)">
                              <span class="rr-subcheck-box" aria-hidden="true"></span>
                              <span class="rr-subcheck-name"><?= htmlspecialchars($resGetRoleSubMenus->menu_name) ?></span>
                            </label>
                          </td>
                          <?php foreach (['view','add','edit','delete'] as $act): ?>
                            <td class="rr-td-act" data-label="<?= ucfirst($act) ?>">
                              <label class="rr-actcheck" title="<?= ucfirst($act) ?>">
                                <input type="checkbox"
                                       class="form-check-input perm-<?= $act ?> rr-act-input"
                                       id="perm_<?= $act ?>_<?= $cid ?>"
                                       data-action="<?= $act ?>"
                                       data-menu-id="<?= $cid ?>"
                                       value="1" checked>
                                <span class="rr-actcheck-box" aria-hidden="true"></span>
                                <span class="sr-only"><?= ucfirst($act) ?></span>
                              </label>
                            </td>
                          <?php endforeach; ?>
                          <td class="rr-td-bulk">
                            <button type="button" class="rr-row-mini" data-row-bulk="all" data-child="<?= $cid ?>" title="Grant all 4 permissions">All</button>
                            <button type="button" class="rr-row-mini" data-row-bulk="none" data-child="<?= $cid ?>" title="Clear all 4 permissions">None</button>
                          </td>
                          <input type="hidden" id="access<?= $cid ?>" value="1">
                        </tr>
                      <?php endwhile; ?>
                      </tbody>
                    </table>
                  <?php endif; ?>
                </div>
              </section>
            <?php endwhile; ?>

            <div id="message" class="message"></div>
          </div>

          <!-- Sticky bottom save bar (mirrors top for long forms) -->
          <div class="rr-sticky-bar rr-sticky-bottom">
            <span class="rr-bar-hint" id="rrFootHint">Tip: click a parent header to collapse a section.</span>
            <div class="rr-bar-right">
              <button type="button" class="rr-btn rr-btn-ghost" id="rrCancelBtn2">Cancel</button>
              <?php if (userCan('add', basename(__FILE__)) || userCan('edit', basename(__FILE__))) { ?>
              <button type="button" class="rr-btn rr-btn-primary" id="savemenus2">
                <i class="fa fa-save"></i> Save Role
              </button>
              <?php } ?>
            </div>
          </div>

        </form>
      </main>
    </div>

    <form action="" method="POST" id="deleterolesFormid">
      <input type="hidden" name="deleterolesid" id="deleterolesid" value="">
    </form>
  </section>
</div>

<?php require_once("ajax/footer.php"); ?>

<script>
  /* ===================== Roles list bootstrap ===================== */
  var org_id = '<?=$SessionOrgId ?>';

  $(document).ready(function() {
    Getroles();
    if (<?php echo isset($_SESSION['display_message']) ? 'true' : 'false'; ?>) {
      $('#message').show();
      setTimeout(function() { $('#message').hide(); }, 5000);
      <?php unset($_SESSION['']); ?>;
    }
  });

  function Getroles() {
    $.ajax({
      url: 'ajax/accesscontrol/roles/getroles.php',
      type: 'GET',
      success: function(data) {
        if (!data) return;
        // Re-shape the legacy table (#tableroles1) into a clean side-rail list.
        var $tmp = $('<div>').html(data);
        var rows = [];
        $tmp.find('#tableroles1 tbody tr').each(function() {
          var $tds = $(this).find('td');
          var sno = $.trim($tds.eq(0).text());
          var name = $.trim($tds.eq(1).text());
          var actionHtml = $tds.eq(2).html() || '';
          rows.push({ sno: sno, name: name, action: actionHtml });
        });
        var html = '';
        if (rows.length === 0) {
          html = '<div class="rr-empty">No roles defined yet.</div>';
        } else {
          rows.forEach(function(r) {
            html += '<div class="rr-role-row" data-name="' + (r.name || '').toLowerCase().replace(/"/g,'&quot;') + '">';
            html +=   '<span class="rr-role-num">' + r.sno + '</span>';
            html +=   '<span class="rr-role-name">' + r.name + '</span>';
            html +=   '<span class="rr-role-actions">' + r.action + '</span>';
            html += '</div>';
          });
        }
        $("#roles").html(html);
      },
      error: function(err) { console.log(err); }
    });
  }

  /* ===================== Filter (client-side) ===================== */
  $(document).on('input', '#rrRoleFilter', function() {
    var q = $(this).val().toLowerCase().trim();
    $('#roles .rr-role-row').each(function() {
      var n = $(this).attr('data-name') || '';
      $(this).toggle(q === '' || n.indexOf(q) !== -1);
    });
  });

  /* ===================== Save handler (preserved POST contract) ===================== */
  function rrSubmit(e) {
    if (e && e.preventDefault) e.preventDefault();
    var role_id   = $("#role_id").val();
    var role_name = $("#role_name").val();

    if (!role_name || !role_name.trim()) {
      swal('', 'Role Name Required', 'warning');
      return false;
    }
    var selectedMenus = [];
    $('input[name="menu_id[]"]:checked').each(function() {
      selectedMenus.push($(this).val().toString().trim());
    });
    if (selectedMenus.length === 0) {
      swal('', 'Please select at least one menu', 'warning');
      return false;
    }
    var menu_access = {};
    var permissions = {};
    selectedMenus.forEach(function(menuId) {
      var perms = [];
      ['view','add','edit','delete'].forEach(function(action) {
        if ($('#perm_' + action + '_' + menuId).is(':checked')) perms.push(action);
      });
      // Parent menus have no permission checkboxes in the UI — default to 'view'
      // so the sidebar FIND_IN_SET('view', permissions) gate passes.
      if (perms.length === 0 && $('#perm_view_' + menuId).length === 0) {
        perms = ['view'];
      }
      permissions[menuId] = perms.join(',');
      var hidden = $('#access' + menuId);
      var hasView = perms.indexOf('view') !== -1;
      if (hidden.length) hidden.val(hasView ? '1' : '0');
      menu_access[menuId] = hasView ? '1' : '0';
    });
    $.ajax({
      url: 'ajax/accesscontrol/roles/insertupdate.php',
      type: 'POST',
      data: { role_id: role_id, role_name: role_name, menu_id: selectedMenus, menu_access: menu_access, permissions: permissions },
      success: function(data) {
        if (data == 1)      swal('', 'Added Successfully', 'success').then(function(){ location.reload(); });
        else if (data == 2) swal('', 'Updated Successfully', 'success').then(function(){ location.reload(); });
        else if (data == 3) swal('', role_name + " already exists", 'warning');
        $("#rolesformid")[0].reset();
      },
      error: function(err) { console.log(err); }
    });
  }
  $(document).on('click', '#savemenus, #savemenus2', rrSubmit);

  /* ===================== editroles — preserved signature ===================== */
  function editroles(role_id, role_name, menus_ids_array, menu_access_array, permissions_map) {
    window.scrollTo(0, 0);
    $("#role_id").val(role_id);
    $("#role_name").val(role_name);
    $('#rrModeLabel').text('Editing: ' + role_name);
    $('input[type="checkbox"][name="menu_id[]"]').prop('checked', false);
    $('.rr-act-input').prop('checked', false);

    if (Array.isArray(menus_ids_array)) {
      menus_ids_array.forEach(function(menuID, index) {
        $("#menu" + menuID).prop("checked", true);
        if (permissions_map && permissions_map[menuID] !== undefined) {
          var perms = String(permissions_map[menuID] || '').split(',');
          ['view','add','edit','delete'].forEach(function(action) {
            $('#perm_' + action + '_' + menuID).prop('checked', perms.indexOf(action) !== -1);
          });
          var hidden = $("#access" + menuID);
          if (hidden.length) hidden.val(perms.indexOf('view') !== -1 ? '1' : '0');
        } else if (menu_access_array && menu_access_array[index] !== undefined) {
          var hasAccess = (menu_access_array[index] == '1');
          ['view','add','edit','delete'].forEach(function(action) {
            $('#perm_' + action + '_' + menuID).prop('checked', hasAccess);
          });
          var hidden = $("#access" + menuID);
          if (hidden.length) hidden.val(hasAccess ? '1' : '0');
        }
      });
    }
    // Highlight active role row in the side list
    $('#roles .rr-role-row').removeClass('is-active');
    $('#roles .rr-role-row').filter(function() {
      return $(this).find('.rr-role-name').text().trim() === String(role_name).trim();
    }).addClass('is-active');
    rrUpdateAllParentStates();
    rrFlashShimmer();
    rrMarkClean();
  }

  function deleteroles(role_id, role_name) {
    swal({ title: "Are you sure?", text: "Do you want to delete Role Record!", icon: "warning", buttons: true, dangerMode: true })
    .then(function(willDelete) {
      if (!willDelete) return;
      $.ajax({
        url: 'ajax/accesscontrol/roles/rolesdelete.php', type: 'POST', data: { role_id: role_id },
        success: function(data) {
          if (data == 1) { swal('Role Deleted Successfully', 'success'); Getroles(); }
          else { swal("error", "Error occured. please try again"); }
        },
        error: function(err) { console.log(err); }
      });
      $('#deleterolesid').val(role_id);
      swal('', ' Deleted Successfully', 'success').then(function() { $('#deleterolesFormid').submit(); });
    });
  }

  /* ===================== Parent select-all / sub-up-propagate ===================== */
  function selecteall(parentMenuId) {
    var parentCheckbox = document.getElementById('menu' + parentMenuId);
    var submenuCheckboxes = document.querySelectorAll('.submenu' + parentMenuId);
    for (var i = 0; i < submenuCheckboxes.length; i++) {
      submenuCheckboxes[i].checked = parentCheckbox.checked;
    }
    rrUpdateParentRowState(parentMenuId);
    rrMarkDirty();
  }
  function unselect(parentMenuId) {
    var parentCheckbox = document.getElementById('menu' + parentMenuId);
    var submenuCheckboxes = document.querySelectorAll('.submenu' + parentMenuId);
    var anyChecked = false;
    for (var i = 0; i < submenuCheckboxes.length; i++) { if (submenuCheckboxes[i].checked) { anyChecked = true; break; } }
    parentCheckbox.checked = anyChecked;
    rrUpdateParentRowState(parentMenuId);
    rrMarkDirty();
  }

  /* ===================== Collapsible parent cards ===================== */
  $(document).on('click', '.rr-toggle, .rr-pcard-head .rr-pcheck-name', function(e) {
    if ($(e.target).is('input,label,.rr-pcheck-box')) return;
    var $card = $(this).closest('.rr-pcard');
    var open = $card.toggleClass('is-open').hasClass('is-open');
    $card.find('.rr-toggle').attr('aria-expanded', open ? 'true' : 'false');
  });

  /* ===================== Bulk parent-card actions ===================== */
  $(document).on('click', '.rr-mini', function(e) {
    e.stopPropagation();
    var pid = $(this).data('parent');
    var bulk = $(this).data('bulk');
    var $card = $('.rr-pcard[data-parent-id="' + pid + '"]');
    var on = (bulk === 'all');
    $card.find('.rr-sub-input').prop('checked', on);
    $card.find('.rr-act-input').prop('checked', on);
    $('#menu' + pid).prop('checked', on);
    rrUpdateParentRowState(pid);
    rrMarkDirty();
  });

  /* ===================== Bulk row-level actions ===================== */
  $(document).on('click', '.rr-row-mini', function(e) {
    e.stopPropagation();
    var cid = $(this).data('child');
    var on = ($(this).data('row-bulk') === 'all');
    $('#menu' + cid).prop('checked', on);
    ['view','add','edit','delete'].forEach(function(action) {
      $('#perm_' + action + '_' + cid).prop('checked', on);
    });
    var pid = $('.rr-prow[data-child-id="' + cid + '"]').data('parent-id');
    if (pid) { unselect(pid); }
    rrMarkDirty();
  });

  /* ===================== Add new role / Cancel ===================== */
  $(document).on('click', '#rrAddRoleBtn', function() {
    $("#rolesformid")[0].reset();
    $("#role_id").val('');
    $("#role_name").val('').focus();
    $('#rrModeLabel').text('Creating new role');
    $('input[type="checkbox"][name="menu_id[]"]').prop('checked', false);
    $('.rr-act-input').prop('checked', false);
    $('#roles .rr-role-row').removeClass('is-active');
    rrFlashShimmer();
    rrMarkClean();
  });
  $(document).on('click', '#rrCancelBtn, #rrCancelBtn2', function() {
    $('#rrAddRoleBtn').trigger('click');
  });

  /* ===================== Dirty-state tracking ===================== */
  function rrMarkDirty()  { $('body').addClass('rr-dirty'); }
  function rrMarkClean()  { $('body').removeClass('rr-dirty'); }
  $(document).on('change', '#rolesformid input', rrMarkDirty);

  /* ===================== Sub→parent state sync on load ===================== */
  function rrUpdateParentRowState(pid) {
    var $card = $('.rr-pcard[data-parent-id="' + pid + '"]');
    var total = $card.find('.rr-sub-input').length;
    var on = $card.find('.rr-sub-input:checked').length;
    $card.toggleClass('is-partial', on > 0 && on < total)
         .toggleClass('is-full', on > 0 && on === total);
  }
  function rrUpdateAllParentStates() {
    $('.rr-pcard').each(function() { rrUpdateParentRowState($(this).data('parent-id')); });
  }

  /* ===================== Brief shimmer when form repopulates ===================== */
  function rrFlashShimmer() {
    var $m = $('.rr-matrix');
    $m.addClass('rr-shimmer');
    setTimeout(function() { $m.removeClass('rr-shimmer'); }, 200);
  }

  /* ===================== Role-name validation (preserved) ===================== */
  $(function() {
    $("#role_name").on('keypress', function(e) {
      var keyCode = e.keyCode || e.which;
      var regex = /^[A-Za-z 0-9 ]+$/;
      return regex.test(String.fromCharCode(keyCode));
    });
    $("#role_name").on('keyup', function() {
      var v = $(this).val();
      if (!v.trim()) $(this).val('');
    });
  });
</script>
