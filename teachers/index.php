<?php
	
	require_once("../classes/main.php");

	// gets current user and important data
	$currentUser = User::get_current();
	$users = [];
	$selectedUser = null;
	$schedules = [];
	$reviews = [];

	// alerts
	$endNotAfterStartAlert = false;
	$appointmentInThePastAlert = false;
	$notInScheduleAlert = false;

	// get users with search param
	if (get("name")) {
		$names = explode(' ', get("name"));
		if (count($names) == 2) {
			$users = User::search_by_name($currentUser->id, $names[0], $names[1]);
		} else if (count($names) == 1) {
			$users = User::search_by_name($currentUser->id, $names[0], $names[0]);
		}
	} else {
		$users = User::get_all($currentUser->id);
	}

	// get schedules and reviews of selected user
	if (get("teacherId")) {
		$selectedUser = User::get_by_id(get("teacherId"));
		$schedules = Schedule::get_by_userId($selectedUser->id);
		$reviews = Review::get_all_by_ownerId($selectedUser->id);
	}

	// create an appointment
	if (post("date") && post("start") && post("end") && post("subject")) {
		// if the appointment would be in the past
		if (post("date") == date('Y-m-d') && strtotime(post("start")) > date('H-i')) {
			$appointmentInThePastAlert = true;

		} else if (strtotime(post("end")) <= strtotime(post("start"))) { // if the appointment timespan isn't correct
			$endNotAfterStartAlert = true;

		} else if (!Appointment::check_overlap_schedule($selectedUser->id, date('l', strtotime(post("date"))), post("start"), post("end"))) { // if timespan isn't set in any schedule of the selected user
			$notInScheduleAlert = true;

		} else {
			Appointment::add(post("date"), post("start"), post("end"), post("subject"), $selectedUser->id, $currentUser->id);
			redirect("/napo/teachers?name=" . get("name") . "&teacherId=" . get("teacherId"));
		}
	}

	// create review
	if (post("rating") && post("comment")) {
		Review::add(post("rating"), post("comment"), $currentUser->id, $selectedUser->id);
		redirect("/napo/teachers?name=" . get("name") . "&teacherId=" . get("teacherId"));
	}

?>

<div class="m-4">
	<h3 class="border-bottom">Teachers</h3>

	<div class="row">
		<div class="col-3">
			<div class="row">
				<div class="col-12">
					<form method="get" class="row">
						<div class="col-9">
							<input class="form-control" type="text" name="name" placeholder="Search" value="<?= get("name") ?>">
							<input type="hidden" name="teacherId" value="<?= get("teacherId") ?>">
						</div>
						<div class="col-3">
							<button class="btn btn-success float-right" type="submit">Search</button>
						</div>
					</form>
				</div>
			</div>
			<div class="row">
				<div class="col-12">
					<div class="list-group">
						<?php
							foreach ($users as $user) {
						?>
							<form method="get" class="my-0">
								<input type="hidden" name="name" value="<?= get("name") ?>">
								<input type="hidden" name="teacherId" value="<?= $user->id ?>">
								<?php 
									if ($selectedUser && $user->id == $selectedUser->id) {
								?>
									<button type="submit" class="list-group-item list-group-item-action active"><?= $user->firstname ?> <?= $user->lastname ?></button>
								<?php
									} else {
								?>
									<button type="submit" class="list-group-item list-group-item-action"><?= $user->firstname ?> <?= $user->lastname ?></button>
								<?php
									}
								?>
							</form>
						<?php
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<div class="col-9">
			<?php
				if ($selectedUser) {
			?>
				<div class="card">
					<div class="card-body">
						<div class="row mb-4">
							<div class="col-12">
								<h4><?= $selectedUser->firstname ?> <?= $selectedUser->lastname ?></h4>
							</div>
						</div>
						<div class="row">
							<div class="col-6">
								<div class="row border-bottom mr-1">
									<div class="mb-2 col-12">
										<h5><li class="fas fa-id-card"></li> Contact</h5>
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label">Handy</label>
									<div class="col-sm-10">
										<input type="text" readonly class="form-control-plaintext" value="<?= $selectedUser->handy ?>">
									</div>
								</div>
								<div class="form-group row">
									<label class="col-sm-2 col-form-label">Email</label>
									<div class="col-sm-10">
										<input type="text" readonly class="form-control-plaintext" value="<?= $selectedUser->email ?>">
									</div>
								</div>
							</div>
							<div class="col-6">
								<div class="row ml-1 my-1">
									<div class="col-12">
										<h5><li class="fas fa-clock"></li> Schedules</h5>
									</div>
								</div>

								<div class="row">
									<div class="col-12">
										<table class="table table-striped">
											<thead>
												<tr>
													<th>Day</th>
													<th>Start</th>
													<th>End</th>
												</tr>
											</thead>
											<tbody>
												<?php 
													foreach ($schedules as $schedule) {
												?>
													<tr>
														<td><?= Weekday::get_by_id($schedule->weekdayId)->name ?></td>
														<td><?= date('H:i', strtotime($schedule->start)) ?></td>
														<td><?= date('H:i', strtotime($schedule->end)) ?></td>
													</tr>
												<?php
													} 
												?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-6">
								<div class="row border-bottom mr-1 my-2">
									<div class="col-12">
										<h5><li class="fas fa-star"></li> Reviews</h5>
									</div>
								</div>
								<div class="row mx-1 my-2">
									<div class="col-9">
										<?php 
											// round to the nearest 0.5
											$averageRating = round(Review::get_average_rating($selectedUser->id) * 2) / 2 
										?>
										<h5>Average Rating <?= $averageRating ?></h5>
									</div>
									<div class="col-3">
										<button class="btn btn-sm btn-primary float-right" type="button" data-toggle="collapse" data-target="#createReview">
											<li class="fas fa-plus"></li> Create
										</button>
									</div>
								</div>
								<div class="collapse" id="createReview">
									<div class="card card-body">
										<form class="m-2" method="post">
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Rating</label>
												<div class="col-sm-10">
													<input type="number" min="1" max="5" step="0.5" class="form-control" name="rating" required>
												</div>
											</div>
											<div class="form-group row">
												<label class="col-sm-2 col-form-label">Comment</label>
												<div class="col-sm-10">
													<input type="textarea" max="255" class="form-control" name="comment" placeholder="Comment" required>
												</div>
											</div>
											<button type="submit" class="btn btn-sm btn-success float-right">Create</button>
										</form>
									</div>
								</div>
								<div class="list-group">
									<?php
										foreach ($reviews as $review) {
											$creator = User::get_by_id($review->creatorId)
									?>
										<div class="list-group-item">
											<h5><?= $creator->firstname ?> <?= $creator->lastname ?> 
												<span class="float-right"><?= $review->rating ?></span>
											</h5>
											<p><?= $review->comment ?></p>
										</div>
									<?php
										}
									?>
								</div>
							</div>
							<div class="col-6">
								<?php
									// if selected user has schedules
									if (count($schedules)) {
								?>
									<div class="row border-bottom ml-1">
										<div class="col-12 my-2">
											<h5><li class="fas fa-calendar-check"></li> Make appointment</h5>
										</div>
									</div>
									<?php
										if ($endNotAfterStartAlert) {
									?>
										<div class="alert alert-danger alert-dismissible fade show">
											The end must be after the start of the appointment
											<button type="button" class="close" data-dismiss="alert">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
									<?php
										}
									?>
									<?php
										if ($appointmentInThePastAlert) {
									?>
										<div class="alert alert-danger alert-dismissible fade show">
											Appointment is in the past
											<button type="button" class="close" data-dismiss="alert">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
									<?php
										}
									?>
									<?php
										if ($notInScheduleAlert) {
									?>
										<div class="alert alert-danger alert-dismissible fade show">
											Appointment must be in one of the schedule timespans
											<button type="button" class="close" data-dismiss="alert">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
									<?php
										}
									?>
									<form class="m-2" method="post">
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Date</label>
											<div class="col-sm-10">
												<input type="date" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" class="form-control" name="date" required>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Start</label>
											<div class="col-sm-10">
												<input type="text" class="form-control" name="start" placeholder="09:30" pattern="^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$" required>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">End</label>
											<div class="col-sm-10">
												<input type="text" class="form-control" name="end" placeholder="11:45" pattern="^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$" required>
											</div>
										</div>
										<div class="form-group row">
											<label class="col-sm-2 col-form-label">Subject</label>
											<div class="col-sm-10">
												<input type="text" class="form-control" name="subject" placeholder="French" pattern=".{2,}" required>
											</div>
										</div>
										<button type="submit" class="btn btn-sm btn-success float-right">Make appointment</button>
									</form>
								<?php
									}
								?>
							</div>
						</div>
					</div>
				</div>
			<?php
				}
			?>
		</div>
	</div>
</div>