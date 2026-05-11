<?php
	require_once __DIR__ . '/_auth.php';
	require_admin();
	require_once __DIR__ . '/_db.php';

	$link = gen_link();
	$message = null;

	function fetch_reservations($link) {
		$result = mysqli_query(
			$link,
			"SELECT
				reservations.id,
				reservations.created_at,
				reservations.seats,
				reservations.comment,
				visitors.first_name AS visitor_first_name,
				visitors.last_name AS visitor_last_name,
				visitors.mail AS visitor_mail,
				visitors.tel AS visitor_tel,
				concerts.name AS concert_name,
				concerts.planned_at,
				concerts.ends_at,
				concerts.price_customer,
				scenes.name AS scene_name,
				scenes.capacity
			FROM reservations
			INNER JOIN concerts ON concerts.id = reservations.concert
			INNER JOIN scenes ON scenes.id = concerts.scene
			INNER JOIN visitors ON visitors.id = reservations.owner
			WHERE reservations.concert IS NOT NULL
			ORDER BY concerts.planned_at ASC, reservations.created_at DESC"
		);

		if (!$result) {
			return [];
		}

		$reservations = [];
		while ($reservation = mysqli_fetch_assoc($result)) {
			$reservations[] = $reservation;
		}

		return $reservations;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
		$id = isset($_POST['cancel_id']) ? $_POST['cancel_id'] : '';

		if ($id === '') {
			$message = 'ID invalide.';
		} else {
			$id = mysqli_real_escape_string($link, $id);
			if (mysqli_query($link, "DELETE FROM reservations WHERE id = '$id' LIMIT 1")) {
				$message = 'Reservation supprimee.';
			} else {
				$message = 'Erreur: ' . mysqli_error($link);
			}
		}
	}

	$reservations = fetch_reservations($link);
	$total_seats = 0;
	$total_revenue = 0;
	foreach ($reservations as $reservation) {
		$seats = (int) $reservation['seats'];
		$price = (int) $reservation['price_customer'];
		$total_seats += $seats;
		$total_revenue += ($seats * $price);
	}
?>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DRUMM'Show - Admin Reservations</title>

		<link rel="stylesheet" href="/assets/css/main.css" type="text/css" />
		<link rel="stylesheet" href="/assets/css/admin.css" type="text/css" />
	</head>
	<body>
		<nav class="navbar admin-nav">
			<a class="navlink" href="/admin/index.php?<?php echo admin_params(); ?>">
				<span>Dashboard</span>
			</a>
			<a class="navlink" href="/admin/artists.php?<?php echo admin_params(); ?>">
				<span>Artistes</span>
			</a>
			<a class="navlink" href="/admin/concerts.php?<?php echo admin_params(); ?>">
				<span>Concerts</span>
			</a>
			<a class="navlink active" href="/admin/reservations.php?<?php echo admin_params(); ?>">
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
			<h1>Reservations</h1>
			<p>Suivi des places reservees et des achats.</p>
		</header>
		<main>
			<section class="admin-section">
				<?php if ($message) { ?>
					<p class="admin-message"><?php echo escape_html($message); ?></p>
				<?php } ?>
				<p class="admin-small">Total places: <?php echo escape_html($total_seats); ?> - Recette estimee: <?php echo escape_html($total_revenue . ' EUR'); ?></p>

				<?php if (!$reservations) { ?>
					<p>Aucune reservation pour le moment.</p>
				<?php } else { ?>
					<table class="admin-table">
						<thead>
							<tr>
								<th>Concert</th>
								<th>Visiteur</th>
								<th>Contact</th>
								<th>Scene</th>
								<th>Horaire</th>
								<th>Places</th>
								<th>Total</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($reservations as $reservation) {
								$visitor_name = $reservation['visitor_first_name'] . ' ' . $reservation['visitor_last_name'];
								$contact = $reservation['visitor_mail'];
								if ($reservation['visitor_tel']) {
									$contact .= ' - ' . $reservation['visitor_tel'];
								}
								$planned = $reservation['planned_at'] ? date('d/m/Y H:i', strtotime($reservation['planned_at'])) : '';
								$ends = $reservation['ends_at'] ? date('H:i', strtotime($reservation['ends_at'])) : '';
								$total_price = (int) $reservation['seats'] * (int) $reservation['price_customer'];
								$total_label = $total_price . ' EUR';
							?>
							<tr>
								<td>
									<?php echo escape_html($reservation['concert_name'] ?: 'Concert'); ?>
									<?php if ($reservation['comment']) { ?>
										<div class="admin-small"><?php echo escape_html($reservation['comment']); ?></div>
									<?php } ?>
								</td>
								<td><?php echo escape_html($visitor_name); ?></td>
								<td><?php echo escape_html($contact); ?></td>
								<td><?php echo escape_html($reservation['scene_name']); ?> (<?php echo escape_html($reservation['capacity']); ?>)</td>
								<td><?php echo escape_html($planned); ?> - <?php echo escape_html($ends); ?></td>
								<td><?php echo escape_html($reservation['seats']); ?></td>
								<td><?php echo escape_html($total_label); ?></td>
								<td>
									<form class="admin-inline" method="POST" action="/admin/reservations.php?<?php echo admin_params(); ?>" onsubmit="return confirm('Supprimer cette reservation ?');">
										<input type="hidden" name="cancel_id" value="<?php echo escape_html($reservation['id']); ?>" />
										<input type="submit" value="Supprimer" />
									</form>
								</td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
				<?php } ?>
			</section>
		</main>
	</body>
</html>
