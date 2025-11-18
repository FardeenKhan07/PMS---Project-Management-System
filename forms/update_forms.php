<?php
// forms/update_forms.php
session_start();
include '../includes/db.php';
$action = isset($_GET['action']) ? $_GET['action'] : '';

function _post($k, $default = '') {
    return isset($_POST[$k]) ? trim($_POST[$k]) : $default;
}
function escape($conn, $v) { return mysqli_real_escape_string($conn, $v); }

/* ---------- USER (update) ---------- */
if ($action === 'user') {
    $uid = (int)_post('uid');
    $user = escape($conn, _post('user'));
    $pass = escape($conn, _post('pass'));
    $u_type = (int)_post('u_type');
    $status = (int)_post('status');

    $sql = "UPDATE users SET username = '$user', password = '$pass', user_type = '$u_type', io = '$status' WHERE uid = '$uid' LIMIT 1";
    if (mysqli_query($conn, $sql)) {
        echo '<div class="alert alert-success" id="msg2"><i class="fa fa-check"></i> Data successfully updated. </div><script>$("#msg2").show("SlideDown");</script>';
    } else {
        echo "<script>alert('updating data failed: ".htmlspecialchars(mysqli_error($conn))."');</script>";
    }
    exit;
}

/* ---------- USER2 (change password with check) ---------- */
if ($action === 'user2') {
    $uid = (int)_post('uid');
    $query20 = mysqli_query($conn, "SELECT * FROM users WHERE uid ='$uid' LIMIT 1");
    $row20 = mysqli_fetch_assoc($query20);
    $cpass = _post('current');
    $user = escape($conn, _post('user'));
    $npass = escape($conn, _post('npass'));

    if ($row20 && $cpass === $row20['password']) {
        if (mysqli_query($conn, "UPDATE users SET username = '$user', password = '$npass' WHERE uid = '$uid' LIMIT 1")) {
            echo '<script>$("#msg20").show("SlideDown"); $("#msg21").hide(); var delay = 2000; setTimeout(function(){ window.location.reload(); }, delay);</script>';
        } else {
            echo "<script>alert('updating data failed: ".htmlspecialchars(mysqli_error($conn))."');</script>";
        }
    } else {
        echo '<script>$("#msg21").show("SlideDown");</script>';
    }
    exit;
}

/* ---------- POSITION (update) ---------- */
if ($action === 'position') {
    $id = (int)_post('id');
    $pos = escape($conn, _post('pos'));
    $dr = escape($conn, _post('dr'));
    if (mysqli_query($conn, "UPDATE position SET daily_rate = '$dr' , position = '$pos' WHERE pid = '$id'")) {
        echo '<h4 class="alert alert-success"><i class="fa fa-fw fa-check"></i>  Data Successfully Updated.</h4>';
    } else {
        echo "<script>alert('Updating data failed: ".htmlspecialchars(mysqli_error($conn))."');</script>";
    }
    exit;
}

/* ---------- DIVISION (update) ---------- */
if ($action === 'division') {
    $id = (int)_post('id');
    $division = _post('division');
    $p_type_raw = isset($_POST['p_type']) ? $_POST['p_type'] : null;
    if ($id <= 0) { echo '<div class="alert alert-danger"><i class="fa fa-times"></i> Invalid division id.</div>'; exit; }
    if ($division === '') { echo '<div class="alert alert-danger"><i class="fa fa-times"></i> Division name is required.</div>'; exit; }

    $division_safe = escape($conn, $division);
    $p_type_value_sql = ($p_type_raw === null || $p_type_raw === '') ? 'NULL' : (int)$p_type_raw;
    $sql = "UPDATE project_division SET division = '{$division_safe}', project_type = {$p_type_value_sql} WHERE pd_id = {$id} LIMIT 1";
    if (mysqli_query($conn, $sql)) {
        include '../includes/msg_box.php';
        echo '<script>$("#msg").html("Data successfully updated.")</script>';
    } else {
        echo "<script>alert('Saving data failed: ".htmlspecialchars(mysqli_error($conn))."');</script>";
    }
    exit;
}

/* ---------- CHANGE PIC (employee) ---------- */
if ($action === 'change_pic') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) exit;
    if (!empty($_FILES['file']['name'])) {
        $rd2 = mt_rand(1000, 9999);
        $filename = basename($_FILES['file']['name']);
        $file = $rd2 . "_" . preg_replace('/[^A-Za-z0-9\-\_\.]/','_', $filename);
        if (move_uploaded_file($_FILES['file']['tmp_name'], '../images/'.$file)) {
            $query = mysqli_query($conn, "UPDATE employee SET e_pic = '$file' WHERE eid = '$id'");
            if ($query) echo '<script> location.replace(document.referrer);</script>';
        }
    }
    exit;
}

/* ---------- CHANGE PIC2 (project) ---------- */
if ($action === 'change_pic2') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id <= 0) exit;
    if (!empty($_FILES['file']['name'])) {
        $rd2 = mt_rand(1000, 9999);
        $filename = basename($_FILES['file']['name']);
        $file = $rd2 . "_" . preg_replace('/[^A-Za-z0-9\-\_\.]/','_', $filename);
        if (move_uploaded_file($_FILES['file']['tmp_name'], '../images/'.$file)) {
            $query = mysqli_query($conn, "UPDATE projects SET site_pic = '$file' WHERE project_id = '$id'");
            if ($query) echo '<script> location.replace(document.referrer);</script>';
        }
    }
    exit;
}

/* ---------- EMPLOYEE (update) ---------- */
if ($action === 'employee') {
    $id = (int)_post('id');
    $fname  = escape($conn, _post('fname'));
    $lname  = escape($conn, _post('lname'));
    $mname  = escape($conn, _post('mname'));
    $address= escape($conn, _post('address'));
    $gender = escape($conn, _post('gender'));
    $bday   = escape($conn, _post('bday'));
    $cn     = escape($conn, _post('cn'));
    $position = (int)_post('position');
    $status = escape($conn, _post('status'));
    $ps = (int)_post('ps');

    $sql = "UPDATE employee SET lastname = '$lname', firstname = '$fname', midname = '$mname', bday = '$bday',
            contact_no = '$cn', address = '$address', pid = '$position', status = '$status', gender = '$gender', io = '$ps' WHERE eid = '$id'";
    if (mysqli_query($conn, $sql)) {
        echo '<script>$("#suc_msg1").show("slidedown"); var delay = 1500; setTimeout(function(){ window.location = "index.php?page=employee_profile&id='. $id.'"; }, delay);</script>';
    } else {
        echo '<script>$("#err_msg1").show("slidedown");</script>';
    }
    exit;
}

/* ---------- PROJECT (update) ---------- */
if ($action === 'project') {
    $id = (int)_post('id');
    $pname = escape($conn, _post('pname'));
    $location = escape($conn, _post('location'));
    $cost = escape($conn, _post('cost'));
    $deadline = escape($conn, _post('deadline'));
    $sdate = escape($conn, _post('sdate'));
    $tid = (int)_post('tid');
    $p_type_raw = isset($_POST['p_type']) ? $_POST['p_type'] : null;
    $stats = (int)_post('stats');

    $p_type_value = ($p_type_raw === null || $p_type_raw === '') ? 'NULL' : (int)$p_type_raw;

    $sql = "UPDATE projects SET project = '$pname', location = '$location', overall_cost = '$cost', deadline = '$deadline',
            start_date = '$sdate', tid = '$tid', io = '$stats', proposed_project = {$p_type_value} WHERE project_id = '$id' LIMIT 1";
    if (mysqli_query($conn, $sql)) {
        echo '<script>$("#suc_msg2").show("slidedown"); var delay = 1500; setTimeout(function(){ window.location = "index.php?page=project_detail&id='. $id.'&stats='.$stats.'"; }, delay);</script>';
    } else {
        echo '<div class="alert alert-danger">DB Error: '.htmlspecialchars(mysqli_error($conn)).'</div>';
    }
    exit;
}

/* ---------- PROGRESS (update) ---------- */
if ($action === 'progress') {
    $id = (int)_post('id');
    $div = (int)_post('div');
    $prog = (int)_post('prog');
    if ($id <= 0) { echo '<div class="alert alert-danger">Invalid progress id.</div>'; exit; }
    $sql = "UPDATE project_progress SET pp_id = '$div', progress = '$prog' WHERE prog_id = '$id' LIMIT 1";
    if (mysqli_query($conn, $sql)) {
        echo '<script>location.replace(document.referrer);</script>';
    } else {
        echo '<div class="alert alert-danger">DB Error: '.htmlspecialchars(mysqli_error($conn)).'</div>';
    }
    exit;
}

/* ---------- TEAM (update) ---------- */
if ($action === 'team') {
    $id = (int)_post('id');
    $fid = (int)_post('fid');
    $q1 = mysqli_query($conn, "UPDATE project_team SET eid = $fid WHERE tid = '$id' LIMIT 1");
    if (isset($_POST['mid']) && is_array($_POST['mid'])) {
        foreach ($_POST['mid'] as $mid) {
            $mid_i = (int)$mid;
            mysqli_query($conn, "INSERT INTO team_member (tid,eid) VALUES('$id','$mid_i')");
        }
    }
    if ($q1) echo "true";
    exit;
}

/* ---------- TEAM_STATS (toggle) ---------- */
if ($action === 'team_stats') {
    $io = isset($_GET['io']) ? (int)$_GET['io'] : 0;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    if ($id > 0) {
        $update = mysqli_query($conn, "UPDATE project_team SET pio = '$io' WHERE tid = '$id' LIMIT 1");
        if ($update) echo '<script>location.replace(document.referrer);</script>';
    }
    exit;
}

/* ---------- ATTENDANCE (update actions) ---------- */
if ($action === 'attendance') {
    foreach ($_GET as $var => $value) $$var = $value;
    if ($task == 'out') {
        mysqli_query($conn, "UPDATE attendance set time_out = now() where eid ='$id' and date_today = '$d' ");
    } elseif ($task == 'odel') {
        mysqli_query($conn, "UPDATE attendance set time_out = '' where eid ='$id' and date_today = '$d' ");
    }
    echo '<script>window.location.reload();</script>';
    exit;
}

echo '<div class="alert alert-danger">Invalid action.</div>';
exit;
?>
