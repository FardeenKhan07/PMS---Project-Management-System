<div class="panel panel-default">
	<div class="panel-body">

<table id="teams" class="table table-bordered">
	<thead>
		<tr>
			<th class="col-md-2 text-center">FOREMAN</th>
			<th class="col-md-2 text-center">MEMBER/s</th>
			<th class="col-md-1 text-center">Status</th>
			<th width="10%" class="text-center"></th>
		</tr>
	</thead>
	<tbody>
		<?php
		include "../includes/db.php";
		$query = mysqli_query($conn,"SELECT *,Concat(firstname,', ',lastname,' ',midname) as name FROM  project_team left join employee on project_team.eid = employee.eid ");
		while($row= mysqli_fetch_assoc($query)){ ?>
			<tr>
				<td><?php echo htmlspecialchars(ucwords((string)$row['name'])) ?></td>
				<td>
					<?php
					// members - safe loop
					$query2 = mysqli_query($conn,"SELECT *,Concat(firstname,', ',lastname,' ',midname) as name FROM  team_member left join employee on team_member.eid = employee.eid where tid = '".(isset($row['tid']) ? $row['tid'] : '')."' ");
					while($row2= mysqli_fetch_assoc($query2)){
					    echo '- ' . htmlspecialchars(ucwords((string)$row2['name'])) . "<br>";
					} ?>
				</td>
				<td><center>
					<?php
					if(isset($row['pio']) && $row['pio'] == '1'){
						echo '<a href="../forms/update_forms.php?action=team_stats&io=0&id='.htmlspecialchars($row['tid']).'" class="btn btn-sm btn-success">Active</a>';
					}else{
						echo '<a href="../forms/update_forms.php?action=team_stats&io=1&id='.htmlspecialchars($row['tid']).'" class="btn btn-sm btn-danger">Inactive</a>';
					}
					 ?>
					</center>
				</td>
				<td><center><a type="button" onclick="update_team(<?php echo htmlspecialchars($row['tid']); ?>)"><i class="fa fa-edit"></i> Update</a></center></td>
			</tr>

		<?php } ?>
	</tbody>
</table>
	</div>
</div>
<script type="text/javascript">
        $(function() {
            $("#teams").dataTable(
        { "aaSorting": [[ 0, "asc" ]] }
      );
        });
    </script>
    <script>
    	function update_team(i){
    		$.ajax({
    			url:"update_team.php?id="+i,
    			success: function(html){
    				$('#team').html(html);
    			}
    		});
    	}
    </script>
