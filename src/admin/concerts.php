<?php
	require_once __DIR__ . '/_auth.php';
	require_admin();
	require_once __DIR__ . '/_db.php';

	$link = gen_link();
	$message = null;
	$edit_concert = null;

	function fetch_artists($link) {
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

	function fetch_scenes($link) {
		$result = mysqli_query($link, "SELECT id, name, capacity FROM scenes ORDER BY name ASC");

		if (!$result) {
			return [];
		}

		$scenes = [];
		while ($scene = mysqli_fetch_assoc($result)) {
			$scenes[] = $scene;
		}

		return $scenes;
	}

	function fetch_concerts($link) {
		$result = mysqli_query(
			$link,
			"SELECT concerts.*, artists.nickname, artists.first_name, artists.last_name, scenes.name AS scene_name
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

	function fetch_concert($link, $id) {
		$id = mysqli_real_escape_string($link, $id);
		$result = mysqli_query(
			$link,
			"SELECT concerts.*, artists.nickname, artists.first_name, artists.last_name, scenes.name AS scene_name
			FROM concerts
			INNER JOIN artists ON artists.id = concerts.artist
			INNER JOIN scenes ON scenes.id = concerts.scene
			WHERE concerts.id = '$id'
			LIMIT 1"
		);

		if (!$result) {
			return null;
		}

		return mysqli_fetch_assoc($result);
	}

	function clean_datetime($link, $key) {
		if (!isset($_POST[$key])) {
			return null;
		}

		$value = trim($_POST[$key]);
		if ($value === '') {
			return null;
		}

		$value = str_replace('T', ' ', $value);
		$value = mysqli_real_escape_string($link, $value);

		return $value . ':00';
	}

	function format_datetime_local($value) {
		if (!$value) {
			return '';
		}

		$timestamp = strtotime($value);
		if (!$timestamp) {
			return '';
		}

		return date('Y-m-d', $timestamp) . 'T' . date('H:i', $timestamp);
	}

	function record_exists($link, $table, $column, $value) {
		$result = mysqli_query($link, "SELECT $column FROM $table WHERE $column = '$value' LIMIT 1");

		return $result && mysqli_num_rows($result) > 0;
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$action = $_POST['action'] ?? '';

		if ($action === 'create' || $action === 'update') {
			$artist = clean_value($link, 'artist');
			$scene = clean_value($link, 'scene');
			$name = clean_value($link, 'name');
			$description = clean_value($link, 'description', false, '');
			$planned_at = clean_datetime($link, 'planned_at');
			$ends_at = clean_datetime($link, 'ends_at');
			$price_customer = clean_value($link, 'price_customer', false, '0');

			if (!$artist || !$scene || !$name || !$planned_at || !$ends_at) {
				$message = 'Tous les champs obligatoires doivent etre remplis.';
			} elseif (strtotime($ends_at) <= strtotime($planned_at)) {
				$message = 'La fin doit etre apres le debut.';
			} elseif (!record_exists($link, 'artists', 'id', $artist)) {
				$message = 'Artiste invalide.';
			} elseif (!record_exists($link, 'scenes', 'id', $scene)) {
				$message = 'Scene invalide.';
			} else {
				if ($action === 'create') {
					$query = "INSERT INTO concerts (planned_at, ends_at, name, description, price_customer, scene, artist)
						VALUES ('$planned_at', '$ends_at', '$name', " . sql_optional($description) . ", '$price_customer', '$scene', '$artist')";

					if (mysqli_query($link, $query)) {
						$message = 'Concert cree.';
					} else {
						$message = 'Erreur: ' . mysqli_error($link);
					}
				} else {
					$id = isset($_POST['id']) ? $_POST['id'] : '';

					if ($id === '') {
						$message = 'ID invalide.';
					} else {
						$id = mysqli_real_escape_string($link, $id);
						$query = "UPDATE concerts
							SET planned_at = '$planned_at',
								ends_at = '$ends_at',
								name = '$name',
								description = " . sql_optional($description) . ",
								price_customer = '$price_customer',
								scene = '$scene',
								artist = '$artist'
							WHERE id = '$id'
							LIMIT 1";

						if (mysqli_query($link, $query)) {
							$message = 'Concert mis a jour.';
						} else {
							$message = 'Erreur: ' . mysqli_error($link);
						}
					}
				}
			}
		} elseif ($action === 'delete') {
			$id = isset($_POST['id']) ? $_POST['id'] : '';

			if ($id === '') {
				$message = 'ID invalide.';
			} else {
				$id = mysqli_real_escape_string($link, $id);
				if (mysqli_query($link, "DELETE FROM concerts WHERE id = '$id' LIMIT 1")) {
					$message = 'Concert supprime.';
				} else {
					$message = 'Erreur: ' . mysqli_error($link);
				}
			}
		}
	}

	$edit_id = isset($_GET['id']) ? $_GET['id'] : '';
	if ($edit_id !== '') {
		$edit_concert = fetch_concert($link, $edit_id);

		if (!$edit_concert) {
			$message = 'Concert introuvable.';
		}
	}

	$artists = fetch_artists($link);
	$scenes = fetch_scenes($link);
	$concerts = fetch_concerts($link);
	$form_title = $edit_concert ? 'Modifier un concert' : 'Ajouter un concert';
	$form_action = $edit_concert ? 'update' : 'create';
	$submit_label = $edit_concert ? 'Mettre a jour' : 'Ajouter';
?>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DRUMM'Show - Admin Concerts</title>

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
			<a class="navlink active" href="/admin/concerts.php?<?php echo admin_params(); ?>">
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
			<h1>Concerts</h1>
			<p>Creer, modifier et supprimer les concerts.</p>
		</header>
		<main>
			<section class="admin-section">
				<?php if ($message) { ?>
					<p class="admin-message"><?php echo escape_html($message); ?></p>
				<?php } ?>

				<h2><?php echo $form_title; ?></h2>
				<form class="admin-form" method="POST" action="/admin/concerts.php?<?php echo admin_params(); ?>">
					<input type="hidden" name="action" value="<?php echo $form_action; ?>" />
					<?php if ($edit_concert) { ?>
						<input type="hidden" name="id" value="<?php echo escape_html($edit_concert['id']); ?>" />
					<?php } ?>
					<div class="form-row">
						<label for="artist">
							<span>Artiste*</span>
							<select name="artist" id="artist" required>
								<option value="">Choisir un artiste</option>
								<?php foreach ($artists as $artist) {
									$label = $artist['nickname'] ? $artist['nickname'] : $artist['first_name'] . ' ' . $artist['last_name'];
									$selected = $edit_concert && (int) $edit_concert['artist'] === (int) $artist['id'] ? ' selected' : '';
									echo '<option value="' . escape_html($artist['id']) . '"' . $selected . '>' . escape_html($label) . '</option>';
								} ?>
							</select>
						</label>
						<label for="scene">
							<span>Scene*</span>
							<select name="scene" id="scene" required>
								<option value="">Choisir une scene</option>
								<?php foreach ($scenes as $scene) {
									$selected = $edit_concert && $edit_concert['scene'] === $scene['id'] ? ' selected' : '';
									echo '<option value="' . escape_html($scene['id']) . '"' . $selected . '>' . escape_html($scene['name']) . ' (' . escape_html($scene['capacity']) . ' places)</option>';
								} ?>
							</select>
						</label>
						<label for="price_customer">
							<span>Prix client</span>
							<input type="number" name="price_customer" id="price_customer" min="0" value="<?php echo escape_html($edit_concert['price_customer'] ?? 0); ?>" />
						</label>
					</div>
					<div class="form-row">
						<label for="name">
							<span>Nom*</span>
							<input type="text" name="name" id="name" value="<?php echo escape_html($edit_concert['name'] ?? ''); ?>" required />
						</label>
						<label for="planned_at">
							<span>Debut*</span>
							<input type="datetime-local" name="planned_at" id="planned_at" value="<?php echo escape_html(format_datetime_local($edit_concert['planned_at'] ?? '')); ?>" required />
						</label>
						<label for="ends_at">
							<span>Fin*</span>
							<input type="datetime-local" name="ends_at" id="ends_at" value="<?php echo escape_html(format_datetime_local($edit_concert['ends_at'] ?? '')); ?>" required />
						</label>
					</div>
					<label for="description">
						<span>Description</span>
						<textarea name="description" id="description"><?php echo escape_html($edit_concert['description'] ?? ''); ?></textarea>
					</label>
					<div class="admin-actions">
						<input type="submit" value="<?php echo $submit_label; ?>" />
						<?php if ($edit_concert) { ?>
							<a class="button btn-secondary" href="/admin/concerts.php?<?php echo admin_params(); ?>">Annuler</a>
						<?php } ?>
					</div>
				</form>

				<h2>Liste des concerts</h2>
				<?php if (!$concerts) { ?>
					<p>Aucun concert pour le moment.</p>
				<?php } else { ?>
					<table class="admin-table">
						<thead>
							<tr>
								<th>Concert</th>
								<th>Artiste</th>
								<th>Scene</th>
								<th>Debut</th>
								<th>Fin</th>
								<th>Prix</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($concerts as $concert) {
								$artist_label = $concert['nickname'] ? $concert['nickname'] : $concert['first_name'] . ' ' . $concert['last_name'];
								$planned = $concert['planned_at'] ? date('d/m/Y H:i', strtotime($concert['planned_at'])) : '';
								$ends = $concert['ends_at'] ? date('d/m/Y H:i', strtotime($concert['ends_at'])) : '';
								$price = $concert['price_customer'] . ' EUR';
							?>
							<tr>
								<td><?php echo escape_html($concert['name'] ?? 'Concert'); ?></td>
								<td><?php echo escape_html($artist_label); ?></td>
								<td><?php echo escape_html($concert['scene_name']); ?></td>
								<td><?php echo escape_html($planned); ?></td>
								<td><?php echo escape_html($ends); ?></td>
								<td><?php echo escape_html($price); ?></td>
								<td>
									<div class="admin-actions">
										<a class="button" href="/admin/concerts.php?id=<?php echo escape_html($concert['id']); ?>&<?php echo admin_params(); ?>">Editer</a>
										<form class="admin-inline" method="POST" action="/admin/concerts.php?<?php echo admin_params(); ?>" onsubmit="return confirm('Supprimer ce concert ?');">
											<input type="hidden" name="action" value="delete" />
											<input type="hidden" name="id" value="<?php echo escape_html($concert['id']); ?>" />
											<input type="submit" value="Supprimer" />
										</form>
									</div>
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
