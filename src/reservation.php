<?php
	function gen_link() {
		global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME;

		$DB_HOST = 'db';
		$DB_USER = 'root';
		$DB_PASSWORD = 'root';
		$DB_NAME = 'test';

		return mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
	}

	function fetch_concerts() {
		$link = gen_link();
		$result = mysqli_query(
			$link,
			"SELECT concerts.id, concerts.name, concerts.planned_at, concerts.ends_at, concerts.price_customer, artists.nickname, artists.first_name, artists.last_name, scenes.name AS scene_name
			FROM concerts
			INNER JOIN artists ON artists.id = concerts.artist
			INNER JOIN scenes ON scenes.id = concerts.scene
			ORDER BY concerts.planned_at ASC"
		);

		if (!$result) {
			return [];
		}

		$concerts = [];
		while ($concert = mysqli_fetch_assoc($result)) {
			$concerts[] = $concert;
		}

		return $concerts;
	}

	$concerts = fetch_concerts();
	$selected_concert_id = filter_input(INPUT_GET, 'concert', FILTER_VALIDATE_INT);
	if ($selected_concert_id !== null && $selected_concert_id !== false && $selected_concert_id <= 0) {
		$selected_concert_id = null;
	}
?>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DRUMM'Show - Réserver un concert</title>

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
			<a class="navlink active" href="/reservation.php">
				<span>Réserver un concert</span>
			</a>
			<a class="navlink" href="/register/artist.php">
				<span>Inscription artiste</span>
			</a>
		</nav>
		<header>
			<h1>Réserver un concert</h1>
			<p>Choisis le concert que tu veux voir et réserve directement ta place.</p>
		</header>
		<script>
			function formatCurrency(value) {
				return new Intl.NumberFormat('fr-FR', {
					style: 'currency',
					currency: 'EUR',
					maximumFractionDigits: 2,
				}).format(value);
			}

			function updateCustomerPrice() {
				const concertSelect = document.querySelector('#concert');
				const seatsInput = document.querySelector('#seats');
				const priceValue = document.querySelector('#customerPriceValue');
				const priceDetails = document.querySelector('#customerPriceDetails');

				if (!concertSelect || !seatsInput || !priceValue || !priceDetails) {
					return;
				}

				const selectedConcert = concertSelect.selectedOptions[0];
				const ticketPrice = Number(selectedConcert?.dataset.priceCustomer || 0);
				const seats = Number(seatsInput.value || 0);

				if (!concertSelect.value || !ticketPrice || seats <= 0) {
					priceValue.textContent = 'Choisis un concert pour voir le prix.';
					priceDetails.textContent = '';
					return;
				}

				priceValue.textContent = formatCurrency(ticketPrice * seats);
				priceDetails.textContent = formatCurrency(ticketPrice) + ' par place × ' + seats + ' place' + (seats > 1 ? 's' : '');
			}

			window.addEventListener('DOMContentLoaded', function () {
				document.querySelector('#concert')?.addEventListener('change', updateCustomerPrice);
				document.querySelector('#seats')?.addEventListener('input', updateCustomerPrice);
				updateCustomerPrice();
			});
		</script>
		<form action="/callback/reserver.php" method="POST">
			<main>
				<section>
					<h2>Coordonnées</h2>
					<div class="form-row">
						<label for="first_name">
							<span>Prénom*</span>
							<input type="text" name="first_name" placeholder="John" required />
						</label>
						<label for="last_name">
							<span>Nom*</span>
							<input type="text" name="last_name" placeholder="DOE" required />
						</label>
						<label for="age">
							<span>Âge*</span>
							<input type="number" name="age" min="0" placeholder="19" required />
						</label>
					</div>
					<div class="form-row">
						<label for="mail">
							<span>Adresse mail*</span>
							<input type="email" name="mail" placeholder="exemple@drum.ms" required />
						</label>
						<label for="tel">
							<span>Téléphone (optionnel)</span>
							<input type="tel" name="tel" placeholder="+33 1 23 45 67 89" />
						</label>
					</div>
					<h2>Réservation</h2>
					<div class="form-row">
						<label for="concert">
							<span>Concert*</span>
							<select name="concert" id="concert" required>
								<option value="">Choisis un concert</option>
								<?php foreach ($concerts as $concert) {
									$artistLabel = $concert['nickname'] ? $concert['nickname'] : $concert['first_name'] . ' ' . $concert['last_name'];
									$label = date('d/m/Y H:i', strtotime($concert['planned_at'])) . ' - ' . $artistLabel . ' - ' . $concert['name'] . ' (' . $concert['scene_name'] . ')';
									$selected = ($selected_concert_id && (int) $concert['id'] === (int) $selected_concert_id) ? ' selected' : '';
									echo '<option value="' . $concert['id'] . '" data-price-customer="' . $concert['price_customer'] . '"' . $selected . '>' . $label . '</option>';
								} ?>
							</select>
						</label>
						<label for="seats">
							<span>Nombre de places*</span>
							<input type="number" name="seats" id="seats" min="1" value="1" required />
						</label>
					</div>
					<div class="price-preview" aria-live="polite">
						<p class="price-preview-label">Prix estimé</p>
						<p class="price-preview-value" id="customerPriceValue">Choisis un concert pour voir le prix.</p>
						<p class="price-preview-details" id="customerPriceDetails"></p>
					</div>
					<label for="comment">
						<span>Message (optionnel)</span>
						<textarea name="comment" placeholder="Besoin d'un accès particulier ?"></textarea>
					</label>
					<input type="submit" value="Réserver le concert" />
				</section>
			</main>
		</form>
	</body>
</html>
