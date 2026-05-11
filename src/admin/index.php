<?php
	require_once __DIR__ . '/_auth.php';
	require_admin();
	require_once __DIR__ . '/_db.php';

	$link = gen_link();

	function fetch_count($link, $table) {
		$result = mysqli_query($link, "SELECT COUNT(*) AS count FROM $table");

		if (!$result) {
			return 0;
		}

		$row = mysqli_fetch_assoc($result);

		return (int) ($row['count'] ?? 0);
	}

	$counts = [
		'scenes' => fetch_count($link, 'scenes'),
		'artists' => fetch_count($link, 'artists'),
		'concerts' => fetch_count($link, 'concerts'),
		'reservations' => fetch_count($link, 'reservations'),
		'visitors' => fetch_count($link, 'visitors'),
	];
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
			<a class="navlink active" href="/admin/index.php?<?php echo admin_params(); ?>">
				<span>Dashboard</span>
			</a>
			<a class="navlink" href="/admin/artists.php?<?php echo admin_params(); ?>">
				<span>Artistes</span>
			</a>
			<a class="navlink" href="/admin/concerts.php?<?php echo admin_params(); ?>">
				<span>Concerts</span>
			</a>
			<a class="navlink" href="/admin/reservations.php?<?php echo admin_params(); ?>">
				<span>Reservations</span>
			</a>
			<a class="navlink" href="/admin/visitors.php?<?php echo admin_params(); ?>">
				<span>Visiteurs</span>
			</a>
			<a class="navlink" href="/admin/logout.php">
				<span>Deconnexion</span>
			</a>
		</nav>
		<header>
			<h1>Panel admin</h1>
			<p>Suivi rapide et acces aux modules.</p>
		</header>
		<main>
			<section class="admin-section">
				<div class="admin-grid">
					<div class="admin-card">
						<div class="bigint"><?php echo $counts['artists']; ?></div>
						<p class="subtitle">Artistes</p>
					</div>
					<div class="admin-card">
						<div class="bigint"><?php echo $counts['concerts']; ?></div>
						<p class="subtitle">Concerts</p>
					</div>
					<div class="admin-card">
						<div class="bigint"><?php echo $counts['reservations']; ?></div>
						<p class="subtitle">Reservations</p>
					</div>
					<div class="admin-card">
						<div class="bigint"><?php echo $counts['visitors']; ?></div>
						<p class="subtitle">Visiteurs</p>
					</div>
					<div class="admin-card">
						<div class="bigint"><?php echo $counts['scenes']; ?></div>
						<p class="subtitle">Scenes</p>
					</div>
				</div>
				<div class="admin-actions">
					<a class="button" href="/admin/artists.php?<?php echo admin_params(); ?>">Gerer les artistes</a>
					<a class="button" href="/admin/concerts.php?<?php echo admin_params(); ?>">Gerer les concerts</a>
					<a class="button" href="/admin/reservations.php?<?php echo admin_params(); ?>">Voir les reservations</a>
					<a class="button" href="/admin/visitors.php?<?php echo admin_params(); ?>">Gerer les visiteurs</a>
				</div>
			</section>
		</main>
	</body>
</html>
