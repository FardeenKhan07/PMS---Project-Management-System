<?php
// forms/add_forms.php
include '../includes/db.php';
$action = isset($_GET['action']) ? $_GET['action'] : '';

/* Helper */
function _post($k, $default = '') {
    return isset($_POST[$k]) ? trim($_POST[$k]) : $default;
}
function escape($conn, $v) { return mysqli_real_escape_string($conn, $v); }

/* ---------- USER (add) ---------- */
if ($action === 'user') {
    $eid = (int)_post('eid');
    $user = escape($conn, _post('user'));
    $pass = escape($conn, _post('pass'));
    $u_type = (int)_post('u_type');

    if ($eid > 0 && $user !== '' && $pass !== '') {
        $sql = "INSERT INTO users (eid,username,password,user_type,io) VALUES ('$eid','$user','$pass','$u_type','1')";
        if (mysqli_query($conn, $sql)) {
            echo '<script>$("#msg").show("SlideDown");</script>';
        } else {
            echo "<script>alert('Saving data failed: ".htmlspecialchars(mysqli_error($conn))."');</script>";
        }
    } else {
        echo "<script>alert('Missing required fields.')</script>";
    }
    exit;
}

/* ---------- POSITION (add) ---------- */
if ($action === 'position') {
    $pos = escape($conn, _post('position'));
    $dr = escape($conn, _post('dr'));
    if ($pos !== '') {
        if (mysqli_query($conn, "INSERT INTO position (position,daily_rate) VALUES('$pos','$dr')")) {
            include '../includes/msg_box.php';
        } else {
            echo "<script>alert('Saving data failed: ".htmlspecialchars(mysqli_error($conn))."');</script>";
        }
    } else {
        echo "<script>alert('Position name required.')</script>";
    }
    exit;
}

/* ---------- ATTENDANCE (add-like actions via GET) ---------- */
if ($action === 'attendance') {
    foreach($_GET as $var=>$value) $$var = $value;
    if (isset($task) && isset($id) && isset($d)) {
        if($task == 'in'){
            mysqli_query($conn,"INSERT INTO attendance (eid,time_in,date_today) VALUES('".(int)$id."',now(),'".escape($conn,$d)."') ");
        } elseif($task == 'del'){
            mysqli_query($conn,"DELETE from attendance where eid ='".(int)$id."' and date_today = '".escape($conn,$d)."' ");
        } elseif($task == 'out'){
            mysqli_query($conn,"UPDATE attendance set time_out = now() where eid ='".(int)$id."' and date_today = '".escape($conn,$d)."' ");
        } elseif($task == 'out_reset'){
            mysqli_query($conn,"UPDATE attendance set time_out = '' where eid ='".(int)$id."' and date_today = '".escape($conn,$d)."' ");
        }
        echo '<script>window.location.reload();</script>';
    } else {
        echo "<script>alert('Invalid attendance request.')</script>";
    }
    exit;
}

/* ---------- DIVISION (add) ---------- */
if ($action === 'division') {
    $division = _post('division');
    $p_type_raw = isset($_POST['p_type']) ? $_POST['p_type'] : null;

    if ($division === '') {
        echo '<div class="alert alert-danger"><i class="fa fa-times"></i> Division name is required.</div>';
        exit;
    }
    $division_safe = escape($conn, $division);
    if ($p_type_raw === null || $p_type_raw === '') {
        $p_type_value_sql = 'NULL';
    } else {
        $p_type_value_sql = (int)$p_type_raw;
    }
    $sql = "INSERT INTO project_division (division, project_type) VALUES ('{$division_safe}', {$p_type_value_sql})";
    if (mysqli_query($conn, $sql)) {
        include '../includes/msg_box.php';
    } else {
        echo '<div class="alert alert-danger"><i class="fa fa-times"></i> DB Error: ' . htmlspecialchars(mysqli_error($conn)) . '</div>';
    }
    exit;
}

/* ---------- EMPLOYEE (add) ---------- */
if ($action === 'employee') {
    $fname  = escape($conn, _post('fname'));
    $lname  = escape($conn, _post('lname'));
    $mname  = escape($conn, _post('mname'));
    $address= escape($conn, _post('address'));
    $gender = escape($conn, _post('gender'));
    $bday   = escape($conn, _post('bday'));
    $cn     = escape($conn, _post('cn'));
    $position = (int)_post('position');
    $status = escape($conn, _post('status'));

    $file = "no_image.jpg";
    // generate ecode
    $e_query = mysqli_query($conn, "SELECT ecode FROM employee ORDER BY eid DESC LIMIT 1");
    $e_row = mysqli_fetch_assoc($e_query);
    if ($e_row && !empty($e_row['ecode']) && is_numeric($e_row['ecode'])) {
        $ecode = (int)$e_row['ecode'] + 1;
    } else {
        $ecode = 1001;
    }
    $sql = "INSERT INTO employee (lastname,firstname,midname,bday,contact_no,address,pid,status,gender,ecode,e_pic,io,date_added)
            VALUES('{$lname}','{$fname}','{$mname}','{$bday}','{$cn}','{$address}','{$position}','{$status}','{$gender}','{$ecode}','{$file}','1',NOW())";
    if (mysqli_query($conn, $sql)) {
        $last_id = mysqli_insert_id($conn);
        echo '<script>$("#suc_msg").show("slidedown"); var delay = 1500; setTimeout(function(){ window.location = "index.php?page=employee_profile&id='. $last_id.'&dattyp=new"; }, delay);</script>';
    } else {
        echo '<script>$("#err_msg").show("slidedown");</script>';
    }
    exit;
}

/* ---------- PROJECT (add) ---------- */
if ($action === 'project') {
    $pname = escape($conn, _post('pname'));
    $location = escape($conn, _post('location'));
    $cost = escape($conn, _post('cost'));
    $deadline = escape($conn, _post('deadline'));
    $sdate = escape($conn, _post('sdate'));
    $tid = isset($_POST['tid']) ? (int)$_POST['tid'] : 0;
    $p_type = isset($_POST['p_type']) && $_POST['p_type'] !== '' ? (int)$_POST['p_type'] : 'NULL';
    $file = "no_image.jpg";

    $sql = "INSERT INTO projects (project,location,overall_cost,start_date,deadline,site_pic,tid,date_added,io,proposed_project)
            VALUES('{$pname}','{$location}','{$cost}','{$sdate}','{$deadline}','{$file}','{$tid}',NOW(),'1', {$p_type})";
    if (mysqli_query($conn, $sql)) {
        $last_id = mysqli_insert_id($conn);
        $query2_ok = true;
        if (isset($_POST['divs']) && is_array($_POST['divs'])) {
            foreach ($_POST['divs'] as $pd) {
                $pd_safe = (int)$pd;
                if (!mysqli_query($conn, "INSERT INTO project_partition (project_id,pd_id) VALUES('$last_id','$pd_safe')")) {
                    $query2_ok = false;
                }
            }
        }
        if ($query2_ok) {
            echo '<script>$("#suc_msg2").show("slidedown"); var delay = 1500; setTimeout(function(){ window.location = "index.php?page=project_detail&id='. $last_id.'&dattyp=new"; }, delay);</script>';
        } else {
            echo '<div class="alert alert-warning">Project saved but partitions failed to save.</div>';
        }
    } else {
        echo '<div class="alert alert-danger">DB Error: '.htmlspecialchars(mysqli_error($conn)).'</div>';
    }
    exit;
}

/* ---------- TEAM (add) ---------- */
if ($action === 'team') {
    $fid = (int)_post('fid');
    $q1 = mysqli_query($conn,"INSERT INTO project_team (eid,date_added,pio) VALUES('$fid',NOW(),'1')");
    if ($q1) {
        $id = mysqli_insert_id($conn);
        if (isset($_POST['mid']) && is_array($_POST['mid'])) {
            foreach ($_POST['mid'] as $mid) {
                $mid_i = (int)$mid;
                mysqli_query($conn, "INSERT INTO team_member (tid,eid) VALUES('$id','$mid_i')");
            }
        }
        echo "true";
    } else {
        echo "false";
    }
    exit;
}

/* ---------- PROGRESS (add) ---------- */
if ($action === 'progress') {
    $div = isset($_POST['div']) ? (int)$_POST['div'] : 0;
    $prog = isset($_POST['prog']) ? (int)$_POST['prog'] : 0;
    $file = null;

    if (!empty($_FILES['image']['name'])) {
        $rd2 = mt_rand(1000, 9999);
        $filename = basename($_FILES['image']['name']);
        $file_clean = $rd2 . "_" . preg_replace('/[^A-Za-z0-9\-\_\.]/','_', $filename);
        $target = '../images/' . $file_clean;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $file = $file_clean;
        }
    }
    $file_db = $file ? $file : 'no_image.jpg';
    $sql = "INSERT INTO project_progress (pp_id,progress,date_added,partition_img) VALUES ('$div','$prog',NOW(),'$file_db')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>location.replace(document.referrer);</script>";
    } else {
        echo '<div class="alert alert-danger">DB Error: '.htmlspecialchars(mysqli_error($conn)).'</div>';
    }
    exit;
}

/* ---------- FALLBACK ---------- */
echo '<div class="alert alert-danger">Invalid action.</div>';
exit;
?>
