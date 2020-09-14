<?php
	
	require_once("../classes/main.php");

	// alerts
	$emailUsedAlert = false;

	// register
	if (post("firstname") && post("lastname") && post("handy") && post("email") && post("password")) {
		// if email doesn't already exists
		if (User::register(post("firstname"), post("lastname"), post("handy"), post("email"), post("password"))) {
			$emailUsedAlert = false;
			redirect("/napo/profile");
		} else {
			$emailUsedAlert = true;
		}
	}

?>

<?php
	if ($emailUsedAlert) {
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

<div class="m-4">
	<div class="row justify-content-center">
		<div class="col-5">
			<h3>Register</h3>
			<form method="post">
				<div class="form-group">
					<label>Firstname</label>
					<input type="text" class="form-control" name="firstname" placeholder="Ben" pattern=".{2,50}" required>
				</div>
				<div class="form-group">
					<label>Lastname</label>
					<input type="text" class="form-control" name="lastname" placeholder="Dover" pattern=".{2,50}" required>
				</div>
				<div class="form-group">
					<label>Handy</label>
					<input type="text" class="form-control" name="handy" placeholder="079 000 00 00" pattern=".{3} .{3} .{2} .{2}" required>
				</div>
				<div class="form-group">
					<label>Email address</label>
					<input type="email" class="form-control" name="email"  placeholder="ben.dover@gmail.com" required>
				</div>
				<div class="form-group">
					<label>Password</label>
					<input type="password" class="form-control" placeholder="*******" name="password" pattern=".{6,}" required>
				</div>
				<button type="submit" class="btn btn-primary">Register</button>
			</form>
		</div>
	</div>
</div>