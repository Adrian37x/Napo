<?php
	
	require_once("../classes/main.php");

	// gets current user and important data
	$currentUser = User::get_current();
	$schedules = Schedule::get_by_userId($currentUser->id);
	$reviews = Review::get_all_by_ownerId($currentUser->id);

	// alerts
	$emailAlreadyExistsAlert = false;
	$startEndDiffAlert = false;
	$overlapOtherScheduleAlert = false;

	// copied profile for edit
	$editProfile = null;

	// copies profile if is edit
	if (post("editProfile")) {
		$editProfile = clone $currentUser;
	}

	// saves the edited user
	if (post("firstname") && post("lastname") && post("handy") && post("email")) {
		// if email doesn't already exists
		if (User::save($currentUser->id, post("firstname"), post("lastname"), post("handy"), post("email"), $currentUser->email)) {
			$emailAlreadyExistsAlert = false;
			redirect("/napo/profile");
		} else {
			$emailAlreadyExistsAlert = true;
		}
	}

	// validates and creates a schedule
	if (post("weekday") && post("start") && post("end")) {
		// if start time is later than end time
		if (Schedule::check_timediff(post("start"), post("end"))) {
			$startEndDiffAlert = false;

			// if schedule overlaps with an already existing one
			if (Schedule::add(session("user_id"), post("weekday"), post("start"), post("end"))) {
				$overlapOtherScheduleAlert = false;
				redirect("/napo/profile");
			} else {
				$overlapOtherScheduleAlert = true;
			}
			
		} else {
			$startEndDiffAlert = true;
		}
	}

	// deletes a schedule by id
	if (post("deleteSchedule")) {
		Schedule::delete(post("deleteSchedule"));
		redirect("/napo/profile");
	}
?>

<div class="m-4">
	<h3 class="border-bottom">Profile</h3>

	<div class="row">
		<div class="col-6">
			<div class="row m-1">
				<div class="col-8">
					<h5><li class="fas fa-id-card"></li> Infos</h5>
				</div>
				<div class="col-4">
					<?php 
						if (!$editProfile) {
					?>
						<form method="post">
							<input type="hidden" name="editProfile" value="edit">
							<button class="btn btn-sm btn-success float-right">
								Edit
							</button>
						</form>
					<?php
						}
					?>
				</div>
			</div>

			<?php
				if ($emailAlreadyExistsAlert) {
			?>
				<div class="alert alert-danger alert-dismissible fade show">
					This email is already used
					<button type="button" class="close" data-dismiss="alert">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			<?php
				}
			?>

			<div class="card">
				<div class="card-body">
					<?php 
						if (!$editProfile) {
					?>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">Firstname</label>
							<div class="col-sm-10">
								<input type="text" readonly class="form-control-plaintext" value="<?= $currentUser->firstname ?>">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">Lastname</label>
							<div class="col-sm-10">
								<input type="text" readonly class="form-control-plaintext" value="<?= $currentUser->lastname ?>">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">Handy</label>
							<div class="col-sm-10">
								<input type="text" readonly class="form-control-plaintext" value="<?= $currentUser->handy ?>">
							</div>
						</div>
						<div class="form-group row">
							<label class="col-sm-2 col-form-label">Email</label>
							<div class="col-sm-10">
								<input type="text" readonly class="form-control-plaintext" value="<?= $currentUser->email ?>">
							</div>
						</div>
					<?php
						} else {
					?>
						<form method="post">
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Firstname</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="firstname" value="<?= $editProfile->firstname ?>" pattern=".{2,50}" required>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Lastname</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="lastname" value="<?= $editProfile->lastname ?>" pattern=".{2,50}" required>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Handy</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" name="handy" value="<?= $editProfile->handy ?>" pattern=".{3} .{3} .{2} .{2}" required>
								</div>
							</div>
							<div class="form-group row">
								<label class="col-sm-2 col-form-label">Email</label>
								<div class="col-sm-10">
									<input type="email" class="form-control" name="email" value="<?= $editProfile->email ?>" pattern=".{2,50}" required>
								</div>
							</div>
							<button type="submit" class="btn btn-sm btn-success float-right">Save</button>
						</form>
					<?php
						}
					?>
					<a href="/napo/changePassword" class="btn btn-sm btn-danger">Change password</a>
				</div>
			</div>
		</div>
		<div class="col-6">
			<h5><li class="fas fa-clock"></li> Schedules</h5>
			
			<?php
				if ($startEndDiffAlert) {
			?>
				<div class="alert alert-danger alert-dismissible fade show">
					The end time must be after the start time
					<button type="button" class="close" data-dismiss="alert">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			<?php
				}
			?>

			<?php
				if ($overlapOtherScheduleAlert) {
			?>
				<div class="alert alert-danger alert-dismissible fade show">
					This schedule overlaps an already existing one
					<button type="button" class="close" data-dismiss="alert">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
			<?php
				}
			?>

			<table class="table table-striped">
				<thead>
					<tr>
						<th>Day</th>
						<th>Start</th>
						<th>End</th>
						<th></th>
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
							<td>
								<form method="post">
									<input type="hidden" name="deleteSchedule" value="<?= $schedule->id ?>" />
									<button type="submit" class="btn btn-sm btn-danger float-right">
										<li class="fas fa-minus"></li>
									</button>
								</form>
							</td>
						</tr>
					<?php
						} 
					?>
					<tr>
						<form method="post">
							<td>
								<select class="form-control" name="weekday" required>
									<?php 
										foreach (Weekday::get_all() as $weekday) {
										?>
											<option value="<?= $weekday->id ?>"><?= $weekday->name ?></option>
										<?php
										}
									?>
								</select>
							</td>
							<td>
								<input class="form-control" type="text" name="start" pattern="^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$" placeholder="17:15" required>
							</td>
							<td>
								<input class="form-control" type="text" name="end" pattern="^(0[0-9]|1[0-9]|2[0-3]|[0-9]):[0-5][0-9]$" placeholder="19:40" required>
							</td>
							<td>
								<button type="submit" class="btn btn-sm btn-success float-right mr-2">
									<li class="fas fa-plus"></li>
								</button>
							</td>
						</form>
					</tr>
				</tbody>
			</table>
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
						$averageRating = round(Review::get_average_rating($currentUser->id) * 2) / 2 
					?>
					<h5>Average Rating <?= $averageRating ?></h5>
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
	</div>
</div>