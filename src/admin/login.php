<?php
	require_once __DIR__ . '/_auth.php';

	if (is_admin_logged_in()) {
		header('Location: /admin/index.php');
		exit;
	}

	$message = null;

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$username = isset($_POST['username']) ? $_POST['username'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';

		if ($username === $ADMIN_USERNAME && $password === $ADMIN_PASSWORD) {
			header('Location: /admin/index.php?admin_user=' . $username . '&admin_pass=' . $password);
			exit;
		}

		$message = 'Identifiants invalides.';
	}
?>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DRUMM'Show - Admin</title>

		<link rel="stylesheet" href="/assets/css/main.css" type="text/css" />
		<link rel="stylesheet" href="/assets/css/admin.css" type="text/css" />
	</head>
	<body>
		<nav class="navbar admin-nav">
			<a class="navlink" href="/">
				<span>Retour au site</span>
			</a>
		</nav>
		<header>
			<h1>Connexion admin</h1>
			<p>Acces reserve au staff.</p>
		</header>
		<main>
			<section>
				<?php if ($message) { ?>
					<p class="admin-message"><?php echo $message; ?></p>
				<?php } ?>
				<form class="admin-form" action="/admin/login.php" method="POST">
					<label for="username">
						<span>Nom d'utilisateur</span>
						<input type="text" name="username" id="username" required />
					</label>
					<label for="password">
						<span>Mot de passe</span>
						<input type="password" name="password" id="password" required />
					</label>
					<input type="submit" value="Se connecter" />
				</form>
			</section>
		</main>
	</body>
</html>
