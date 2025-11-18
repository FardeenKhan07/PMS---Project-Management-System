<div class="col-md-12"><br>
<hr style="border-bottom:1px solid grey"></hr>
</div>
<style>
	.control-label {
		text-transform:capitalize;
	}
</style>
<?php
include '../includes/db.php';

// sanitize id
$id = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : '';

// fetch project + foreman info
$emp_query = mysqli_query($conn,
    "SELECT *, CONCAT(lastname,', ',firstname, ' ',midname) as name, projects.io as stats
     FROM projects
     LEFT JOIN project_team ON projects.tid = project_team.tid
     LEFT JOIN employee ON project_team.eid = employee.eid
     WHERE project_id = '$id' LIMIT 1"
);
$row = mysqli_fetch_assoc($emp_query);
?>
</div>

<div class="col-md-12">
<div class="panel panel-default">
<div class="panel-body">
	<div class="col-md-4">
	<center><img src="../images/<?php echo htmlspecialchars($row['site_pic'] ?? '') ?>" width="200px" height="230px"></center>
	<br>
	<div class="row">
		<div class="col-sm-4 text-right"><label class="control-label" style="font-size:15px !important">Project Name:</label></div>
		<div class="col-sm-8 text-left"><label class="control-label" style="font-size:18px !important"><?php echo htmlspecialchars($row['project'] ?? '') ?></label></div>
	</div>
	<div class="row">
		<div class="col-sm-4 text-right"><label class="control-label">Start Date:</label></div>
		<div class="col-sm-8 text-left"><label class="control-label"><?php echo !empty($row['start_date']) ? date("F d, Y",strtotime($row['start_date'])) : '' ?></label></div>
	</div>
	<div class="row">
		<div class="col-sm-4 text-right"><label class="control-label">Deadline:</label></div>
		<div class="col-sm-8 text-left"><label class="control-label"><?php echo !empty($row['deadline']) ? date("F d, Y",strtotime($row['deadline'])) : '' ?></label></div>
	</div>
	<div class="row">
		<div class="col-sm-4 text-right"><label class="control-label">Location:</label></div>
		<div class="col-sm-8 text-left"><label class="control-label"><?php echo htmlspecialchars($row['location'] ?? '') ?></label></div>
	</div>

	<div class="row">
		<div class="col-sm-4 text-right"><label class="control-label">Project Cost:</label></div>
		<div class="col-sm-8 text-left"><label class="control-label"><?php echo  (isset($row['overall_cost']) ? htmlspecialchars($row['overall_cost']) . ' Php.' : '') ?></label></div>
	</div>
	
	<div class="row">
		<div class="col-sm-4 text-right"><label class="control-label">Foreman:</label></div>
		<div class="col-sm-8 text-left"><label class="control-label"><?php echo htmlspecialchars($row['name'] ?? '') ?></label></div>
	</div>
	<div class="row">
		<div class="col-sm-4 text-right"><label class="control-label">Project type:</label></div>
		<div class="col-sm-8 text-left"><label class="control-label">
		<?php 
		// If you changed project types to IT labels elsewhere, update this mapping accordingly
		$ptype = $row['proposed_project'] ?? '';
		if($ptype == '1'){
			echo 'Building';
		}elseif($ptype == '2'){
			echo 'House';
		}elseif($ptype == '3'){
			echo 'Highways';
		}elseif($ptype == '4'){
			echo 'Grandstand';
		}elseif($ptype == '5'){
			echo 'Covered Court';
		}	
		?></label></div>
		
	</div>
	
		<div class="row">
		<div class="col-sm-4 text-right"><label class="control-label">Project Status:</label></div>
		<div class="col-sm-8 text-left"><label class="control-label"><?php  if(($row['stats'] ?? '') == '1'){ echo 'On going';}elseif(($row['stats'] ?? '') == '2'){ echo 'Finished'; }elseif(($row['stats'] ?? '') == '3'){ echo 'Canceled'; } ?></label></div>
	</div>
	
<br>
<br>
<br>
<center>
	<h4><b>-- New Progress</b></h4>
</center>

	<form action="../forms/add_forms.php?action=progress" method="POST" enctype="multipart/form-data" >
	<div class="row">
		
			<div class="col-sm-4 text-right"><label for="" class="control-label">Division:</label></div>
			<div class="col-sm-8">
				<select name="div" id="div" class="form-control" required>
					<option value="" selected disabled>SELECT DIVISION</option>
					<?php 
					$div= mysqli_query($conn,"SELECT * FROM project_partition NATURAL JOIN project_division WHERE project_id = '$id' ORDER BY division ");
					while($div_row = mysqli_fetch_assoc($div)){
						$test = mysqli_query($conn,"SELECT SUM(progress) as prog FROM project_progress WHERE pp_id = '".mysqli_real_escape_string($conn,$div_row['pp_id'])."' ");
						$test_row= mysqli_fetch_assoc($test);
						$prog = isset($test_row['prog']) ? (int)$test_row['prog'] : 0;
						if($prog < 100 ){ 
					?>
						<option value="<?php echo htmlspecialchars($div_row['pp_id']) ?>"><?php echo ucwords(htmlspecialchars($div_row['division'])) ?></option>
					<?php }} ?>
				</select>
			</div>
		
	</div>
	<br>
	<div class="row">
		 <input type="hidden" name="id" value="<?php echo htmlspecialchars($id) ?>">
		<div class="col-sm-4 text-right"><label for="" class="control-label">Progress:</label></div>
		<div class="col-sm-7"><input type="text" class="form-control text-right"  max="100" onkeyup="validate_prog()" onkeydown="validate_prog()" name="prog" id="prog" required/></div>
		<div class="col-sm-1 text-left"><label for="" class="control-label">%</label></div>
	</div>
	<div class="row">
		<div class="col-sm-4"></div>
		<div class="col-sm-8" id="validation"></div>
	</div>
	<br>
	<div class="row">
		<div class="col-sm-4 text-right"><label for="" class="control-label">Image:</label></div>
		<div class="col-sm-8"><input type="file" name="image" class="form-control" required/></div>
	</div>
	<br>
	<div class="row">
		<div class="col-sm-4"></div>
		<div class="col-sm-8 col-sm-offset-4">
			<button id="btn_save" class="btn btn-sm btn-info"><i class="fa fa-save"></i> Save</button>
			<button type="reset" class="btn btn-sm btn-info"><i class="fa fa-reset"></i> Cancel</button>
		</div>
	</div>
	</form>
	</div>
	<br>
	<br>
	<br>
	<div class="col-md-8">
		<div class="row">
		<div class="col-sm-12" id="progress2">
			<center><h5><b>-- Project Progress --</b></h5></center>

<?php
// Build chart data for partitions safely

// initialize array collector
$array = [];

// fetch partitions/divisions
$partitions = $conn->query("SELECT * FROM project_partition NATURAL JOIN project_division WHERE project_id = '$id' ORDER BY division");

if ($partitions && $partitions->num_rows > 0) {
    while ($progress = $partitions->fetch_assoc()) {

        $name = $progress['division'];
        $pid = $progress['pp_id'];

        // fetch total progress for this partition
        $prog3 = $conn->query("SELECT SUM(progress) as total_prog FROM project_progress WHERE pp_id = '".mysqli_real_escape_string($conn,$pid)."' ");
        $row_prog = $prog3->fetch_assoc();

        // ensure count is defined (int)
        $count = isset($row_prog['total_prog']) ? (int)$row_prog['total_prog'] : 0;

        // choose color based on progress
        if ($count <= 50) {
            $color = 'rgba(251, 159, 118, 0.53)';
        } else {
            $color = 'rgba(120, 151, 239, 0.53)';
        }

        // Add this entry to the array (use project id as key to keep grouping)
        $array[$id][] = '{"progress":"'. $count . '","name":"' . addslashes(ucfirst($name)) . '","color":"' . $color . '"}';
    }
} else {
    // no partitions: keep array empty so chart shows only Total later
    $array[$id] = [];
}

// compute overall total safely
$prog2 = $conn->query("SELECT SUM(progress) as total FROM project_progress NATURAL JOIN project_partition WHERE project_id = '$id' ");
$progress2 = $prog2 ? $prog2->fetch_assoc() : null;

// protect against division by zero
$part_count = ($partitions ? (int)$partitions->num_rows : 0);
if ($part_count > 0 && isset($progress2['total'])) {
    $total = $progress2['total'] / $part_count;
    $tots = number_format($total, 0);
} else {
    $tots = 0;
}

// Total color and data element (no leading comma)
$colors = 'rgba(0, 241, 5, 0.39)';
$data_total = '{"progress":"'. $tots .'","name":"Total","color":"'. $colors .'"}';

// implode partition data if exists
$data_parts = '';
if (!empty($array[$id])) {
    $data_parts = implode(',', $array[$id]);
}

// build final data string for JS dataProvider
if ($data_parts !== '') {
    $final_data_provider = $data_parts . ',' . $data_total;
} else {
    $final_data_provider = $data_total;
}

$conn->close();
?>

            <div class="chartdiv" id="chartdiv<?php echo htmlspecialchars($id) ?>" style="width:100% ; height:40%;"></div>

<script>
 jQuery(document).ready(function(){
chart.exportConfig = {
    menuItems: [{
        icon: '../am_chart/images/export.png',
        format: 'png',
        onclick: function(a) {
            var output = a.output({
                format: 'png',
                output: 'datastring'
            }, function(data) {
                console.log(data)
            });
        }
    }]
}
}); 

var chart = AmCharts.makeChart("chartdiv<?php echo htmlspecialchars($id) ?>", {
    "type": "serial",
    "theme": "none",
    "pathToImages":"http://localhost/new_admin/am_chart/images/export.png",
    "dataProvider": [<?php echo $final_data_provider ?>],
    "title":"Project Progress",
    "valueAxes": [{
        "axisAlpha": 0,
        "position": "left",
        "title": "Progress (%)",
    }],
    "startDuration": 1,
    "graphs": [{
        "balloonText": "<b style='text-transform:capitalize'>[[name]]: [[value]]%</b>",
        "colorField": "color",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "type": "column",
        "valueField": "progress",
        "labelText":"[[progress]]%",
        "labelPosition":"inside",
    }],
    "chartCursor": {
        "categoryBalloonEnabled": false,
        "cursorAlpha": 0,
        "zoomable": false
    },
    "categoryField": "name",
    "categoryAxis": {
        "text-transform":"capitalize",
        "gridPosition": "start",
        "labelRotation": 50,
        "title":"Divisions"
    },
});
</script>

		</div>
	</div>
	

<br>


	<table class="table table-bordered">
		<thead>
			<tr>
				<th class="col-sm-2 text-center"></th>
				<th class="col-sm-2 text-center">Division</th>
				<th class="text-center" style="width:1% !important">Progress</th>
				<th class="col-sm-1 text-center">Date Updated</th>
				<th class="col-sm-1 text-center">Action</th>
			</tr>
		</thead>
		<tbody>
			<?php
			 include '../includes/db.php';
			 $query = mysqli_query($conn,"SELECT * FROM project_partition NATURAL JOIN project_progress LEFT JOIN project_division ON project_partition.pd_id = project_division.pd_id WHERE project_id = '$id' ORDER BY date_added DESC ");
			 if(mysqli_num_rows($query) > 0){
				while($row2 = mysqli_fetch_assoc($query)){
			 ?>
			 <tr>
			 	<td><center><img src="../images/<?php echo htmlspecialchars($row2['partition_img']) ?>" width="100px" height="100px" ></center></td>
			 	<td><?php echo ucwords(htmlspecialchars($row2['division'])) ?></td>
			 	<td><?php echo htmlspecialchars($row2['progress']).'%' ?></td>
			 	<td><?php echo !empty($row2['date_added']) ? date("F d, Y",strtotime($row2['date_added'])) : '' ?></td>
			 	<td><center><a href="#progess<?php echo htmlspecialchars($row2['prog_id']) ?>" data-toggle="modal"><i class="fa fa-pencil"> </i> Edit</a></center></td>
			 </tr>
			 <?php include '../includes/update_modals.php' ?>
			<?php }}else{ ?>
			
					<tr>
						<td colspan='5'><center>No Data yet.</center></td>
					</tr>
			<?php } ?>
		</tbody>
	</table>
</div>
</div>
</div>
</div>


 <script>
	function validate_prog(){
		var id = $('#div').val();
		var prog = $('#prog').val();

		$.ajax({
			url: "validate_progress.php?id="+id+"&prog="+prog,
			success:function(html){
				$('#validation').html(html);
			}
		});
	}
</script>
