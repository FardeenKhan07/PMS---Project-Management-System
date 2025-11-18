<?php include '../includes/db.php'; ?>

<!-- New Employee Modal -->
<div id="new_employee" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog"> 
    <div class="modal-content modal-md">  
      <div class="modal-header"> 
        <h4 class="modal-title" id='head'>
          <i class=""></i> New Employee
        </h4> 
      </div> 
      <form method="POST" id='emp_form'>
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group" id="form-login">
              <div class="col-sm-12">
                <div id="retCode2">
                  <div class="alert alert-success" id="suc_msg">
                    <h4><i class="fa fa-check"></i> Data successfully added.</h4>
                  </div>
                  <div class="alert alert-danger" id="err_msg">
                    <h4><i class="fa fa-times"></i> Data failed to add.</h4>
                  </div>
                </div>
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Last Name:</label>
              <div class="col-sm-8">
                <input class="form-control" style="text-transform:capitalize" name="lname" type="text" required>
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">First Name:</label>
              <div class="col-sm-8">
                <input class="form-control" style="text-transform:capitalize" name="fname" type="text" required>
              </div>
            </div> 

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Middle Name:</label>
              <div class="col-sm-8">
                <input class="form-control" style="text-transform:capitalize" name="mname" type="text">
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Birthday:</label>
              <div class="col-sm-8">
                <input class="form-control" name="bday" type="date" required>
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Gender:</label>
              <div class="col-sm-4">
                <select class="form-control" name="gender" required>
                  <option value=""></option>
                  <option value="Female">Female</option>
                  <option value="Male">Male</option>
                </select>
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Address:</label>
              <div class="col-sm-8">
                <textarea class="form-control" rows="2" name="address" required></textarea> 
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Contact no.:</label>
              <div class="col-sm-5">
                <input class="form-control text-right" name="cn" type="text" maxlength="11" required>
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Status:</label>
              <div class="col-sm-8">
                <select class="form-control" name="status" required>
                  <option value=""></option>
                  <option value="Single">Single</option>
                  <option value="Married">Married</option>
                  <option value="Widow">Widow</option>
                </select>
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Position:</label>
              <div class="col-sm-8">
                <select class="form-control" name="position" required>
                  <option value=""></option>
                  <?php
                    $pos_query = mysqli_query($conn,"SELECT * FROM position ORDER BY position ASC");
                    while($pos_row = mysqli_fetch_assoc($pos_query)){
                      echo '<option style="text-transform:capitalize" value="'.htmlspecialchars($pos_row['pid']).'">'.htmlspecialchars($pos_row['position']).'</option>';
                    }
                  ?>
                </select>
              </div>
            </div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info btn-sm" id="btn_sub">Save</button>
          <button data-dismiss="modal" class="btn btn-info btn-sm"><i class="glyphicon glyphicon-close"></i>Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- New Project Modal (UPDATED: IT project types) -->
<div id="new_project" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
  <div class="modal-dialog"> 
    <div class="modal-content modal-md">  
      <div class="modal-header"> 
        <h4 class="modal-title" id='head'><i class=""></i> New Project</h4> 
      </div> 
      <form method="POST" id='proj_form' >
        <div class="modal-body">
          <div class="form-horizontal">
            <div class="form-group" id="form-login">
              <div class="col-sm-12">
                <div id="retCode2">
                  <div class="alert alert-success" id="suc_msg2">
                    <h4><i class="fa fa-check"></i> Data successfully added.</h4>
                  </div>
                  <div class="alert alert-danger" id="err_msg2">
                    <h4><i class="fa fa-times"></i> Data failed to add.</h4>
                  </div>
                </div>
              </div>
            </div>

            <?php
            // IT project types — edit labels here if you wish
            $project_types = [
              1 => 'Web Application Development',
              2 => 'ERP / SAP Implementation',
              3 => 'Data Analytics Dashboard',
              4 => 'Mobile App Development',
              5 => 'Cloud Migration / Infrastructure',
              6 => 'Networking & Server Setup',
              7 => 'Cybersecurity Assessment',
              8 => 'Inventory Management System',
              9 => 'AI/ML System',
              10 => 'Database Optimization'
            ];
            ?>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Proposed Project:</label>
              <div class="col-sm-8">
                <select name="p_type" id="p_type" class="form-control input-sm" style="text-transform:capitalize" autocomplete="off" onchange="div_field()" required>
                  <option value="">-- Select Project Type --</option>
                  <?php foreach($project_types as $k => $label): ?>
                    <option value="<?php echo htmlspecialchars($k) ?>"><?php echo htmlspecialchars($label) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Project Name:</label>
              <div class="col-sm-8">
                <input class="form-control" style="text-transform:capitalize" name="pname" type="text" required>
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Location:</label>
              <div class="col-sm-8">
                <textarea class="form-control" style="text-transform:capitalize" name="location" required></textarea>
              </div>
            </div> 

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Cost:</label>
              <div class="col-sm-6">
                <input class="form-control" style="text-align:right" id="cc" name="cost" type="text" placeholder="Php.">
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Starting Date:</label>
              <div class="col-sm-8">
                <input class="form-control" name="sdate" type="date" required>
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Deadline:</label>
              <div class="col-sm-8">
                <input class="form-control" name="deadline" type="date" required>
              </div>
            </div>

            <div class="form-group" id="form-login">
              <label class="col-sm-4 control-label">Foreman:</label>
              <div class="col-sm-8">
                <select class="form-control" id="tid" name="tid" type="text" onchange="mem_list()" required>
                  <option value="">-- Select Foreman --</option>
                  <?php
                    $pos_query = mysqli_query($conn,"SELECT *, CONCAT(lastname,', ',firstname,' ',midname) as name FROM project_team LEFT JOIN employee ON project_team.eid = employee.eid WHERE pio='1' ORDER BY name ASC");
                    while($pos_row = mysqli_fetch_assoc($pos_query)){
                      echo '<option style="text-transform:capitalize" value="'.htmlspecialchars($pos_row['tid']).'">'.htmlspecialchars($pos_row['name']).'</option>';
                    }
                  ?>
                </select>
              </div>
            </div>

            <div id="mem-field"></div>
            <div id="div-field"></div>

          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-info btn-sm" id="btn_sub"><i class="fa fa-save"></i> Save</button>
          <button data-dismiss="modal" class="btn btn-info btn-sm"><i class="glyphicon glyphicon-close"></i>Close</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  jQuery(document).ready(function(){
      $('#suc_msg2').hide();
      $('#err_msg2').hide();
  });

  function div_field(){
    var id = $('#p_type').val();
    $.ajax({
      url: "div_field.php?id="+id,
      success: function(html){
        $('#div-field').html(html);
      }
    });
  }
  function mem_list(){
    var id = $('#tid').val();
    $.ajax({
      url: "mem_list.php?id="+id,
      success: function(html){
        $('#mem-field').html(html);
      }
    });
  }
</script>
