<?php

	require_once("init.php");

?>

<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" />
	<title>Napo</title>

	<!-- BOOTSTRAP CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    
    <!-- FONTAWESOME -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/js/all.min.js"></script>
</head>
<body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
		<a class="navbar-brand" href="/napo">
			<img src="http://<?= $_SERVER['SERVER_NAME'] ?>:<?= $_SERVER['SERVER_PORT'] ?>/napo/assets/logo.png" height="35">
		</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>

		<div class="collapse navbar-collapse" id="navbarSupportedContent">
			<div class="navbar-nav mr-auto">
				<div class="nav-item">
					<a class="nav-link" href="/napo">Home</a>
				</div>
				<?php 
					if (User::get_current()) {
				?>
					<div class="nav-item">
						<a class="nav-link" href="/napo/profile">Profile</a>
					</div>
					<div class="nav-item">
						<a class="nav-link" href="/napo/appointments">Appointments</a>
					</div>
					<div class="nav-item">
						<a class="nav-link" href="/napo/teachers">Teachers</a>
					</div>
				<?php
					}
				?>
			</div>
			<?php 
				if (User::get_current()) {
					?>
						<div class="nav-item dropdown">
							<a class="nav-link" href="#" data-toggle="dropdown">
								<li class="fas fa-user fa-lg"></li>
							</a>
							<div class="dropdown-menu dropdown-menu-right">
								<a class="dropdown-item" href="/napo/login">Change account</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item text-danger" href="/napo/logout">Logout</a>
							</div>
						</div>
					<?php
				} else {
					?>
						<div class="nav-item">
							<a class="nav-link" href="/napo/login">Login</a>
						</div>
					<?php
				}
			?>
		</div>
	</nav>

	<!-- Bootstrap JS -->
	<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>