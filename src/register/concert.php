<?php
	function gen_link() {
		global $DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME;

		$DB_HOST = 'db';
		$DB_USER = 'root';
		$DB_PASSWORD = 'root';
		$DB_NAME = 'test';

		return mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
	}

	function fetch_artists() {
		$link = gen_link();
		$result = mysqli_query($link, "SELECT id, first_name, last_name, nickname FROM artists ORDER BY COALESCE(nickname, CONCAT(first_name, ' ', last_name)) ASC");

		if (!$result) {
			return [];
		}

		$artists = [];
		while ($artist = mysqli_fetch_assoc($result)) {
			$artists[] = $artist;
		}

		return $artists;
	}

	function fetch_scenes() {
		$link = gen_link();
		$result = mysqli_query($link, "SELECT id, name, capacity, price_solo, price_group FROM scenes ORDER BY name ASC");

		if (!$result) {
			return [];
		}

		$scenes = [];
		while ($scene = mysqli_fetch_assoc($result)) {
			$scenes[] = $scene;
		}

		return $scenes;
	}

	$artists = fetch_artists();
	$scenes = fetch_scenes();
?>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DRUMM'Show - Réserver un horaire de concert</title>

		<link rel="stylesheet" href="/assets/css/main.css" type="text/css" />

		<script>
			function formatCurrency(value) {
				return new Intl.NumberFormat('fr-FR', {
					style: 'currency',
					currency: 'EUR',
					maximumFractionDigits: 2,
				}).format(value);
			}

			function formatDuration(minutes) {
				const hours = Math.floor(minutes / 60);
				const remainingMinutes = Math.round(minutes % 60);

				if (!minutes || minutes <= 0) {
					return '0 min';
				}

				if (hours === 0) {
					return remainingMinutes + ' min';
				}

				if (remainingMinutes === 0) {
					return hours + ' h';
				}

				return hours + ' h ' + remainingMinutes + ' min';
			}

			function updatePriceEstimate() {
				const sceneSelect = document.querySelector('#scene');
				const formationSelect = document.querySelector('#formation');
				const plannedAt = document.querySelector('#planned_at');
				const endsAt = document.querySelector('#ends_at');
				const priceValue = document.querySelector('#priceValue');
				const priceDetails = document.querySelector('#priceDetails');

				if (!sceneSelect || !formationSelect || !plannedAt || !endsAt || !priceValue || !priceDetails) {
					return;
				}

				const selectedScene = sceneSelect.selectedOptions[0];
				const formation = formationSelect.value;
				const start = new Date(plannedAt.value);
				const end = new Date(endsAt.value);
				const soloRate = Number(selectedScene?.dataset.priceSolo || 0);
				const groupRate = Number(selectedScene?.dataset.priceGroup || soloRate);
				const hourlyRate = formation === 'group' ? groupRate : soloRate;

				if (!sceneSelect.value || !formationSelect.value || Number.isNaN(start.getTime()) || Number.isNaN(end.getTime())) {
					priceValue.textContent = 'Choisis une salle, une formation et des horaires pour voir le prix.';
					priceDetails.textContent = '';
					return;
				}

				const durationMinutes = (end.getTime() - start.getTime()) / 60000;
				if (durationMinutes <= 0) {
					priceValue.textContent = 'La fin doit être après le début.';
					priceDetails.textContent = '';
					return;
				}

				const durationHours = durationMinutes / 60;
				const estimatedPrice = durationHours * hourlyRate;

				priceValue.textContent = formatCurrency(estimatedPrice);
				priceDetails.textContent = formatDuration(durationMinutes) + ' × ' + formatCurrency(hourlyRate) + ' / h' + ' pour une ' + (formation === 'group' ? 'formation groupe' : 'formation solo');
			}

			window.addEventListener('DOMContentLoaded', function () {
				['change', 'input'].forEach(function (eventName) {
					document.querySelector('#scene')?.addEventListener(eventName, updatePriceEstimate);
					document.querySelector('#formation')?.addEventListener(eventName, updatePriceEstimate);
					document.querySelector('#planned_at')?.addEventListener(eventName, updatePriceEstimate);
					document.querySelector('#ends_at')?.addEventListener(eventName, updatePriceEstimate);
				});

				updatePriceEstimate();
			});
		</script>
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
			<a class="navlink active" href="/register/concert.php">
				<span>Horaire concert</span>
			</a>
		</nav>
		<header>
			<h1>Réserver un horaire de concert</h1>
			<p>Choisis l'artiste, la scène et le créneau du concert à programmer.</p>
		</header>
		<form action="/callback/concert.php" method="POST">
			<main>
				<section>
					<h2>Créneau du concert</h2>
					<div class="form-row">
						<label for="artist">
							<span>Artiste*</span>
							<select name="artist" required>
								<option value="">Choisis un artiste</option>
								<?php foreach ($artists as $artist) {
									$label = $artist['nickname'] ? $artist['nickname'] : $artist['first_name'] . ' ' . $artist['last_name'];
									echo '<option value="' . $artist['id'] . '">' . $label . '</option>';
								} ?>
							</select>
						</label>
						<label for="name">
							<span>Nom du concert*</span>
							<input type="text" name="name" placeholder="Nuit électrique" required />
						</label>
					</div>
					<div class="form-row">
						<label for="formation">
							<span>Formation*</span>
							<select name="formation" id="formation" required>
								<option value="">Choisis une formation</option>
								<option value="solo">Solo</option>
								<option value="group">Groupe</option>
							</select>
						</label>
						<label for="scene">
							<span>Scène*</span>
							<select name="scene" id="scene" required>
								<option value="">Choisis une scène</option>
								<?php foreach ($scenes as $scene) {
									$soloRate = $scene['price_solo'] ?? 0;
									$groupRate = $scene['price_group'] !== null ? $scene['price_group'] : $soloRate;
									echo '<option value="' . $scene['id'] . '" data-price-solo="' . $soloRate . '" data-price-group="' . $groupRate . '">' . $scene['name'] . ' (' . $scene['capacity'] . ' places, solo ' . $soloRate . ' €/h, groupe ' . $groupRate . ' €/h)</option>';
								} ?>
							</select>
						</label>
						<label for="planned_at">
							<span>Début*</span>
								<input type="datetime-local" name="planned_at" id="planned_at" required />
						</label>
					</div>
					<div class="form-row">
						<label for="ends_at">
							<span>Fin prévue*</span>
								<input type="datetime-local" name="ends_at" id="ends_at" required />
						</label>
						<label for="description">
							<span>Description (optionnelle)</span>
							<textarea name="description" placeholder="Quelques mots sur le set"></textarea>
						</label>
					</div>
						<div class="price-preview" aria-live="polite">
							<p class="price-preview-label">Prix estimé</p>
							<p class="price-preview-value" id="priceValue">Choisis une scène et des horaires pour voir le prix.</p>
							<p class="price-preview-details" id="priceDetails"></p>
						</div>
					<input type="submit" value="Réserver l'horaire" />
				</section>
			</main>
		</form>
	</body>
</html>
