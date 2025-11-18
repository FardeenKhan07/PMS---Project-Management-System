<?php
// pages/project_division.php
// Adjust path if this file lives elsewhere
include '../includes/db.php';
?>
<div class="col-md-12">
	<h4>List of Project Divisions</h4>
<hr style="border-bottom:1px solid black"></hr>
</div>

<div class="col-lg-8">
	<div class="panel panel-default">
        <div class="panel-heading">
          <h3 class="panel-title"><strong></strong></h3>
        </div>
        <div class="panel-body">
       <table id="position" class="table table-bordered table-condensed">
    <thead>
      <tr id="heads">
        <th class="col-md-1 text-center"></th>
        <th class="col-md-2 text-center">Divisions</th>
        <th class="col-md-2 text-center">Project type</th>
        <th class="col-md-1 text-center"></th>
      </tr>
    </thead>
    <tbody>
    <?php
      // join with project_types to get label
      $query = mysqli_query($conn, "SELECT pd.*, pt.label AS project_type_label FROM project_division pd LEFT JOIN project_types pt ON pd.project_type = pt.id ORDER BY pd.division");
      $i = 1;
      while ($row = mysqli_fetch_assoc($query)) {
        $id = $row['pd_id'];
    ?>
      <tr>
        <td class="text-center"><?php echo $i++; ?></td>
        <td class="text-center"><?php echo htmlspecialchars($row['division']); ?>
          <input type="hidden" id="div<?php echo $id ?>" value="<?php echo htmlspecialchars($row['division']); ?>"></td>
        <td class="text-center">
          <?php
            if (isset($row['project_type_label']) && $row['project_type_label'] !== '') {
              echo htmlspecialchars($row['project_type_label']);
            } elseif ($row['project_type'] === '0' || $row['project_type'] === 0 || $row['project_type'] === null) {
              echo 'All';
            } else {
              echo 'Unknown';
            }
          ?>
          <input type="hidden" id="typ<?php echo $id ?>" value="<?php echo htmlspecialchars($row['project_type']); ?>">
        </td>
        <td class="text-center"><center><a onclick="update_div(<?php echo $id ?>)"><i class="fa fa-pencil"></i> edit</a></center></td>
       </tr>
      <?php } ?>
    </tbody>
  </table>
		</div>
	</div>
</div>

<div class="col-md-4">
	<a class="col-sm-12 btn btn-md btn-info" id="add_div" onclick="add_form()"><center><i class="fa fa-plus"></i> Add</center></a>
	<br>
<div id="add_form">
	<h4 id="frm-title">New Division</h4>
	<hr style="border-bottom:1px solid grey"></hr>

	<form method="POST" id="pos_form">
		<div class="form-horizontal">
			<div class="form-group">
				<div class="col-sm-4"><label class="control-label" for="pos">Division:</label></div>
				<div class="col-sm-7">
					<input type="text" class="form-control input-sm" style="text-transform:capitalize" autocomplete="off" name="division" id="pos" required/>
				</div>
			</div>

            <div class="form-group">
				<div class="col-sm-4"><label class="control-label" for="p_type">Project Type:</label></div>
				<div class="col-sm-7">
					<select class="form-control input-sm" style="text-transform:capitalize" autocomplete="off" name="p_type" id="p_type" required>
						<option value="">-- Select Project Type --</option>
						<?php
						// populate options from project_types table (Option A seeded above)
						$types_q = mysqli_query($conn, "SELECT id, label FROM project_types ORDER BY label ASC");
						while ($type = mysqli_fetch_assoc($types_q)) {
							echo '<option value="'.htmlspecialchars($type['id']).'">'.htmlspecialchars($type['label']).'</option>';
						}
						// include 'All' choice (value 0)
						echo '<option value="0">All</option>';
						?>
					</select>
				</div>
			</div>

		</div>
		<input type="hidden" name="id" id="id">
		<hr style="border-bottom:1px solid grey"></hr>

		<div class="form-horizontal">
			<div class="form-group">
				<div class="col-sm-12">
					<center>
					<button class="col-sm-4 btn btn-info btn-sm" id="save_pos">Save</button>
					<div class="col-sm-4"></div>
					<a class="col-sm-4 btn btn-info btn-sm" onclick="can_pos()">Cancel</a>
					</center>
				</div>
			</div>
		</div>
		<hr style="border-bottom:1px solid grey"></hr>

	</form>

</div>
</div>
<div id="retCode1"></div>

<script>
	jQuery(document).ready(function(){
		jQuery("#pos_form").submit(function(e){
			e.preventDefault();
			var formData = jQuery(this).serialize();
			var id = $('#id').val();
			if(id == ''){
				var action = '../forms/add_forms.php?action=division' ;
			}else{
				var action = '../forms/update_forms.php?action=division' ;
			}
			$.ajax({
				type: "POST",
				url: action,
				data: formData,
				success: function(html){
					$('#retCode1').html(html);
					var delay = 1500;
					setTimeout(function(){ window.location = 'index.php?page=division'; }, delay);
				}
			});
			return false;
		});
	});
</script>

<script>
	$(document).ready(function(){
		$('#add_form').hide();
	});
	function add_form(){
		$('#add_div').hide();
		$('#add_form').show('SlideDown');
	}
	function can_pos(){
		$('#add_form').hide('slideUp');
		$('#add_div').show('SlideDown');
		$('#pos').val('');
		$('#id').val('');
		$('#p_type').val('');
		$('#p_type option:first').prop('selected', true);
	}
	function update_div(i){
		var div = $('#div'+i).val();
		var typ = $('#typ'+i).val();
		$('#pos').val(div);
		$('#p_type').val(typ);
		$('#id').val(i);
		$('#frm-title').html('Update Division.');
		$('#add_div').hide();
		$('#add_form').show('SlideDown');
	}
</script>

<script type="text/javascript">
$(function() {
    $("#position").dataTable({ "aaSorting": [[ 2, "asc" ]] });
});
</script>
