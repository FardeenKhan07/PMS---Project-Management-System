<div class="col-md-12">
	<h4>User List</h4>

<hr style="border-bottom:1px solid black"></hr>
</div>
<?php
// safe default for io
$io = isset($_GET['io']) ? $_GET['io'] : '1';
?>
<div class="col-lg-7">
	<div class="panel panel-default">
        <div class="panel-heading">
        <?php if($io == '1'){ $btn_class1 = 'class="btn btn-md btn-success"';} else{ $btn_class1 = 'class="btn btn-md btn-default"';} ?>
        <?php if($io == '2'){ $btn_class = 'class="btn btn-md btn-success"';} else{ $btn_class = 'class="btn btn-md btn-default"';} ?>
          <a href="index.php?page=user_list&io=1" <?php echo $btn_class1 ?> > Active</a>
          <a href="index.php?page=user_list&io=2" <?php echo $btn_class ?> > Inactive</a>

        </div>
        <div class="panel-body">

       <table id="emp" class="table table-bordered table-condensed">
    <thead>
      <tr id="heads">
        <th class="col-md-3 text-center">Name</th>
        <th class="col-md-2 text-center">User Type</th>
        <th class="col-md-1 text-center"></th>
      </tr>
    </thead>
    <tbody>
    <?php
    include '../includes/db.php';

    // Use the safe $io variable. Select employee name as emp_name.
    $sql = "SELECT users.*, employee.*, 
            CONCAT(COALESCE(employee.lastname,''),', ',COALESCE(employee.firstname,''),' ',COALESCE(employee.midname,'')) AS emp_name
            FROM users
            LEFT JOIN employee ON users.eid = employee.eid
            WHERE users.io = '".mysqli_real_escape_string($conn, $io)."' 
              AND users.eid != '".mysqli_real_escape_string($conn, $_SESSION['ID'])."' ";
    $query = mysqli_query($conn, $sql);
    while($row = mysqli_fetch_assoc($query)) {
         $id = isset($row['eid']) ? $row['eid'] : '';
         $id2 = isset($row['uid']) ? $row['uid'] : '';

         // safe year generation (employee or user date_added - use whichever exists)
         $dateAdded = $row['date_added'] ?? '';
         $yearPart = !empty($dateAdded) ? date("Y", strtotime($dateAdded)) : '----';
         $ecodePart = isset($row['ecode']) ? (string)$row['ecode'] : '';
         $eco = $yearPart . $ecodePart;

         // Prepare display name:
         // 1. normalize emp_name (remove extra spaces)
         $empNameRaw = $row['emp_name'] ?? '';
         $empNameTrimmed = trim(preg_replace('/\s+/', ' ', trim($empNameRaw)));
         // Remove leading/trailing commas and spaces (cases like ", John" or ",")
         $empNameClean = trim($empNameTrimmed, " ,");

         // 2. Fallback options from users table
         $fallback = $row['user'] ?? $row['username'] ?? $row['uid'] ?? 'Unknown';

         // Final display name decision
         $displayName = !empty($empNameClean) ? ucwords($empNameClean) : $fallback;
    ?>
      <tr>

        <td style="text-transform:capitalize"><?php echo htmlspecialchars($displayName) ?></td>
        <td style="text-transform:capitalize" class="text-center"><?php  if(isset($row['user_type']) && $row['user_type'] == '1'){ echo 'Administrator';}else{ echo 'Staff';} ?></td>
        <td style="text-transform:capitalize" class="text-center"><center><a onclick="edit_user('<?php echo htmlspecialchars($id2) ?>')" ><i class="fa fa-edit"></i> Edit</a></center></td>
       </tr>

      <?php
    }
      ?>
    </tbody>
  </table>
		</div>
	</div>
</div>
<div class="col-md-5">

	<a class="col-sm-7 col-sm-offset-3 btn btn-md btn-info" onclick="new_user()" id="new_user"><center><i class="fa fa-plus"></i> New User</center></a>
  <div id="user">
 <center><h4>New User</h4></center>

  <hr style="border-bottom:1px solid grey"></hr>
  <div class="alert alert-success" id="msg"><i class="fa fa-check"></i> Data successfully added. </div>
    <form id="user_form" method="POST">
    <div class="form-group">
    <div class="col-sm-4 text-right"><label for="emp">Employee:</label></div>
    <div class="col-sm-8">
    <select name="eid" id="emp" class="form-control chosen-select" data-placeholder="Select Employee">
    <option value=""></option>
      <?php
    include '../includes/db.php';

    // safe query for employees
      $query2=  mysqli_query($conn, "SELECT *,CONCAT(COALESCE(lastname,''),', ',COALESCE(firstname,''),' ',COALESCE(midname,'')) as name FROM employee where io = '1' and eid != '".mysqli_real_escape_string($conn, $_SESSION['ID'])."' order by name ");
         while($row2 = mysqli_fetch_assoc($query2)) {
            $row2_name = ucwords((string)$row2['name']);
            $row2_year = !empty($row2['date_added']) ? date("Y", strtotime($row2['date_added'])) : '----';
            $row2_ecode = isset($row2['ecode']) ? (string)$row2['ecode'] : '';
    ?>
      <option value="<?php echo htmlspecialchars($row2['eid']) ?>"><?php echo htmlspecialchars($row2_year . $row2_ecode . ' | ' . $row2_name) ?></option>
      <?php } ?>
    </select>
    </div>
    </div>
<br>
<br>
     <div class="form-group">
    <div class="col-sm-4 text-right"><label for="us">Username:</label></div>
    <div class="col-sm-8">
   <input type="text" class="form-control input-sm" id="us" name="user">
    </div>
    </div>
<br>
<br>
    <div class="form-group">
    <div class="col-sm-4 text-right"><label for="pass">Password:</label></div>
    <div class="col-sm-8">
   <input type="password" class="form-control input-sm" id="pass" name="pass">
    </div>
    </div>
<br>
<br>
    <div class="form-group">
    <div class="col-sm-4 text-right"><label for="u_type">User Type:</label></div>
    <div class="col-sm-8">
   <select type="text" class="form-control input-sm" id="u_type" name="u_type">
   <option ></option>
   <option value="1">Administrator</option>
   <option value="2">Staff</option>
   </select>
    </div>
    </div>
<br>
    <hr style="border-bottom:1px solid grey"></hr>

    <div class="form-horizontal">
      <div class="form-group">
        <div class="col-sm-12">
          <center>
          <div class="col-sm-4"></div>
          <button class="col-sm-2 btn btn-info btn-sm" id="save_pos">Save</button>
          <div class="col-sm-2"></div>
          <a class="col-sm-2 btn btn-info btn-sm" onclick="window.location.reload()">Cancel</a>
          </center>
        </div>
      </div>
    </div>
    <hr style="border-bottom:1px solid grey"></hr>
    </form>
  </div>
</div>
<div id="retCode1">
</div>
 <?php   include '../includes/add_modal.php'; ?>

<script>
	jQuery(document).ready(function(){
    $('#user').hide();
		$('#msg').hide();
						jQuery("#user_form").submit(function(e){
								e.preventDefault();
								var formData = jQuery(this).serialize();
								$.ajax({
									type: "POST",
									url: "../forms/add_forms.php?action=user",
									data: formData,
									success: function(html){
										$('#retCode1').append(html);
									var delay = 2000;
										setTimeout(function(){	location.replace(document.referrer);   }, delay);

									}
								});
									return false;
						});
						});
  function new_user(){
    $('#new_user').hide('slideUp');
    $('#user').show('slideUp');
  }
  function edit_user(i){
   $.ajax({
    url:"edit_user.php?uid="+i,
    success: function(html){
      $('#user').html(html);
      $('#new_user').hide();
      $('#user').show('SlideDown');

    }
   });
  }
</script>

<script type="text/javascript">
        $(function() {
            $("#emp").dataTable(
        { "aaSorting": [[ 0, "asc" ]] }
      );
        });
    </script>
<script type="text/javascript">
    var config = {
      '.chosen-select'           : {},
      '.chosen-select-deselect'  : {allow_single_deselect:true},
      '.chosen-select-no-single' : {disable_search_threshold:10},
      '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
      '.chosen-select-width'     : {width:"95%"}
    }
    for (var selector in config) {
      $(selector).chosen(config[selector]);
    }
  </script>
