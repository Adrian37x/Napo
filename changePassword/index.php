<?php
	
    require_once("../classes/main.php");
    
    // current user
    $currentUser = User::get_current();

	// alerts
	$wrongPasswordAlert = false;

	// change password
	if (post("newPassword") && post("oldPassword")) {
		// if password and email is correct
		if (User::change_password($currentUser->id, post("newPassword"), post("oldPassword"))) {
            $wrongPasswordAlert = false;
            User::logout();
			redirect("/napo/login");
		} else {
			$wrongPasswordAlert = true;
		}
	}

?>

<?php
	if ($wrongPasswordAlert) {
?>
	<div class="alert alert-danger alert-dismissible fade show">
		Incorrect old password
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
			<h3>Change password</h3>
			<form method="post">
				<div class="form-group">
					<label for="exampleInputEmail1">Old password</label>
					<input type="password" class="form-control" placeholder="*********" name="oldPassword" required>
				</div>
				<div class="form-group">
					<label for="exampleInputPassword1">New password</label>
					<input type="password" class="form-control" placeholder="*********" name="newPassword"  pattern=".{6,}" required>
				</div>
				<div class="form-group row justify-content-end">
					<a class="btn btn-warning m-1" href="/napo/profile">Cancel</a>
				    <button type="submit" class="btn btn-success m-1">Change password</button>
				</div>
			</form>
		</div>
	</div>
</div>