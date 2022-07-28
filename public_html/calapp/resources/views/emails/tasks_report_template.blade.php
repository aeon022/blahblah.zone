<!DOCTYPE html>
<html>
	<head>
		<title>Task Status</title>
		<style type="text/css">
	        .center {
	            margin: auto;
	            border: 3px solid green;
	            padding: 10px;
	        }
	        table {
			  border-collapse: collapse;
			}

			table, td, th {
			  border: 1px solid black;
			}
	    </style>
	</head>
	<body>
		<div class="center">
			<h2 style="text-align: center;color: green">Daily Task Reports</h2>
			<div>
				<table width="100%">
					<thead>
						<tr>
							<th>TaskID</th>
							<th>Task Name</th>
							<th>Project Name</th>
							<th>Assign User</th>
							<th>Status</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Hours</th>
						</tr>
					</thead>
					<tbody>
						<?php if(!empty($tasks)) { ?>
							<?php foreach ($tasks as $task): ?>
								<tr>
									<td>{{$task->generated_id}}</td>
									<td>{{$task->name}}</td>
									<td>{{$task->project1['project_name']}}</td>

									<td>{{$task->assignUser['firstname']}} {{$task->assignUser['lastname']}}</td>
									<td>
										<?php if($task->status == 1) { ?>
											<label class="label label-default">Open</label>
										<?php } ?>
										<?php if($task->status == 2) { ?>
											<label class="label label-warning">In Progress</label>
										<?php } ?>
										<?php if($task->status == 6) { ?>
											<label class="label label-danger">Completed</label>
										<?php } ?>
									</td>
									<td>{{$task->task_start_date}}</td>
									<td>{{$task->task_end_date}}</td>
									<td>{{$task->estimated_hours}}</td>
								</tr>
							<?php endforeach ?>
						<?php } else { ?>
							<tr>
								<td colspan="7">
									There are no any tasks.
								</td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</body>
</html>