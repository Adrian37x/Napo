<?php
	
	require_once("../classes/main.php");

	// alerts
	$incorrectAlert = false;

	// login
	if (post("email") && post("password")) {
		// if password and email is correct
		if (User::login(post("email"), post("password"))) {
			$incorrectAlert = false;
			redirect("/napo/profile");
		} else {
			$incorrectAlert = true;
		}
	}

?>

<?php
	if ($incorrectAlert) {
?>
	<div class="alert alert-danger alert-dismissible fade show">
		Either the password or the email is incorrect
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
			<h3>Login</h3>
			<form method="post">
				<div class="form-group">
					<label for="exampleInputEmail1">Email address</label>
					<input type="email" class="form-control" placeholder="ben.dover@gmail.com" name="email" required>
				</div>
				<div class="form-group">
					<label for="exampleInputPassword1">Password</label>
					<input type="password" class="form-control" placeholder="*********" name="password" required>
				</div>
				<div class="form-group">
					<a href="/napo/register">Create a new account</a>
				</div>
				<button type="submit" class="btn btn-primary">Login</button>
			</form>
		</div>
	</div>
</div>