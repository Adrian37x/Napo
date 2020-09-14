<?php
	
	require_once("../classes/main.php");

	// gets current user and important data
	$currentUser = User::get_current();
	$appointments = Appointment::get_all_by_userId($currentUser->id);

	if (post("acceptAppointmentId")) {
		if (Appointment::accept(post("acceptAppointmentId"))) {
			redirect("/napo/appointments");
		}
	}

	if (post("declineAppointmentId")) {
		if (Appointment::decline(post("declineAppointmentId"))) {
			redirect("/napo/appointments");
		}
	}
?>

<div class="m-4">
	<h3 class="border-bottom">Appointments</h3>
	<?php
		if (count($appointments)) {
	?>
		<table class="table">
			<thead class="thead-light">
				<tr>
					<th>With</th>
					<th>Role</th>
					<th>Subject</th>
					<th>Date</th>
					<th>Start</th>
					<th>End</th>
					<th>Status</th>
				</tr>
			</thead>
			<tbody>
	<?php
			foreach ($appointments as $appointment) {
				$user = User::get_by_id($appointment->teacherId == $currentUser->id ? $appointment->studentId : $appointment->teacherId);
				$status = Status::get_by_id($appointment->statusId);
	?>
			<tr class="<?= $status->type == "Accepted" ? 'table-success' : ($status->type == "Declined" ? 'table-danger' : '') ?>">
				<td><?= $user->firstname ?> <?= $user->lastname ?></td>
				<td><?= $appointment->teacherId == $currentUser->id ? "Teacher" : "Student" ?></td>
				<td><?= $appointment->subject ?></td>
				<td><?= $appointment->date == date('Y-m-d') ? "Today" : date("m.d.Y", strtotime($appointment->date)) ?></td>
				<td><?= $appointment->start ?></td>
				<td><?= $appointment->end ?></td>
				<td>
					<?php
						if ($status->type == "Open") {
							if ($appointment->teacherId == $currentUser->id) {
						?>
							<div class="row">
								<div class="col-3">
									<form method="post">
										<input type="hidden" name="acceptAppointmentId" value="<?= $appointment->id ?>">
										<button type="submit" class="btn btn-sm btn-success mx-1"><li class="fas fa-check"></li> Accept</button>
									</form>
								</div>
								<div class="col">
									<form method="post">
										<input type="hidden" name="declineAppointmentId" value="<?= $appointment->id ?>">
										<button type="submit" class="btn btn-sm btn-danger mx-1"><li class="fas fa-times"></li> Decline</button>
									</form>
								</div>
							</div>
						<?php
							} else {
						?>
							<h5><span class="badge badge-primary">Open</span></h5>
						<?php
							}
						} else if ($status->type == "Accepted") {
					?>
						<h5><span class="badge badge-success">Accepted</span></h5>
					<?php
						} else if ($status->type == "Declined") {
					?>
						<h5><span class="badge badge-danger">Declined</span></h5>
					<?php
						}
					?>
				</td>
			</tr>	
	<?php
			}
	?>
			</tbody>
		</table>
	<?php
		} else {
	?>
		<p>You don't have any appointments!</p>
	<?php
		}
	?>
</div>
