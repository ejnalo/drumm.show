<?php
	function gen_link() {
		global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME;

		$DB_HOST = 'db';
		$DB_USER = 'root';
		$DB_PASSWORD = 'root';
		$DB_NAME = 'test';

		return mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
	}

	function cancel_reservation($link, $reservationId) {
		$reservationId = mysqli_real_escape_string($link, $reservationId);
		mysqli_query($link, "DELETE FROM reservations WHERE id = '$reservationId' LIMIT 1") or die(mysqli_error($link));
	}

	function fetch_reservations() {
		$link = gen_link();
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

	$link = gen_link();
	$message = null;

	if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_id'])) {
		cancel_reservation($link, $_POST['cancel_id']);
		$message = 'La place a été résiliée.';
	}

	$reservations = fetch_reservations();
?>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DRUMM'Show - Places achetées</title>

		<link rel="stylesheet" href="/assets/css/main.css" type="text/css" />
	</head>
	<body>
		<nav class="navbar">
			<a class="navlink" href="/">
				<span>Accueil</span>
			</a>
			<a class="navlink" href="/programme.php">
				<span>Programme</span>
			</a>
			<a class="navlink" href="/reservation.php">
				<span>Réserver un concert</span>
			</a>
			<a class="navlink active" href="/cart.php">
				<span>Places achetées</span>
			</a>
		</nav>
		<main>
			<section>
				<?php if ($message) { ?>
					<p><?php echo $message; ?></p>
				<?php } ?>
				<?php if (!$reservations) { ?>
					<p>Aucune place achetée pour le moment.</p>
				<?php } else { ?>
					<ul>
						<?php foreach ($reservations as $reservation) { ?>
							<li class="concert">
								<div>
									<h3><?php echo $reservation['concert_name'] ?: 'Concert'; ?></h3>
									<p><strong>Visiteur:</strong> <?php echo $reservation['visitor_first_name'] . ' ' . $reservation['visitor_last_name']; ?></p>
									<p><strong>Contact:</strong> <?php echo $reservation['visitor_mail']; ?><?php echo $reservation['visitor_tel'] ? ' - ' . $reservation['visitor_tel'] : ''; ?></p>
									<p><strong>Scène:</strong> <?php echo $reservation['scene_name']; ?> (<?php echo $reservation['capacity']; ?> places)</p>
									<p><strong>Horaire:</strong> <?php echo date('d/m/Y H:i', strtotime($reservation['planned_at'])); ?> - <?php echo date('H:i', strtotime($reservation['ends_at'])); ?></p>
									<p><strong>Places:</strong> <?php echo $reservation['seats']; ?></p>
								</div>
								<div>
									<form method="POST" action="/cart.php">
										<input type="hidden" name="cancel_id" value="<?php echo $reservation['id']; ?>" />
										<input type="submit" value="Résilier" />
									</form>
								</div>
							</li>
						<?php } ?>
					</ul>
				<?php } ?>
			</section>
		</main>
	</body>
</html>
