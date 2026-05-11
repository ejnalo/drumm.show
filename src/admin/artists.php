<?php
	require_once __DIR__ . '/_auth.php';
	require_admin();
	require_once __DIR__ . '/_db.php';

	$link = gen_link();
	$message = null;
	$edit_artist = null;
	$default_avatar = 'https://api.beam.ejnalo.me/users/beam/avatar.png';

	function fetch_artists($link) {
		$result = mysqli_query($link, "SELECT * FROM artists ORDER BY COALESCE(nickname, CONCAT(first_name, ' ', last_name)) ASC");

		if (!$result) {
			return [];
		}

		$artists = [];
		while ($artist = mysqli_fetch_assoc($result)) {
			$artists[] = $artist;
		}

		return $artists;
	}

	function fetch_artist($link, $id) {
		$id = mysqli_real_escape_string($link, $id);
		$result = mysqli_query($link, "SELECT * FROM artists WHERE id = '$id' LIMIT 1");

		if (!$result) {
			return null;
		}

		return mysqli_fetch_assoc($result);
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$action = $_POST['action'] ?? '';

		if ($action === 'create') {
			$first_name = clean_value($link, 'first_name');
			$last_name = clean_value($link, 'last_name');
			$email = clean_value($link, 'email', false, '');
			$nickname = clean_value($link, 'nickname', false, '');
			$avatar_url = clean_value($link, 'avatar_url', false, '');
			$bio = clean_value($link, 'bio', false, '');
			$style = clean_value($link, 'style', false, '');
			$spotify = clean_value($link, 'spotify', false, '');
			$youtube = clean_value($link, 'youtube', false, '');
			$deezer = clean_value($link, 'deezer', false, '');
			$soundcloud = clean_value($link, 'soundcloud', false, '');
			$bandlab = clean_value($link, 'bandlab', false, '');
			$beam = clean_value($link, 'beam', false, '');
			$instagram = clean_value($link, 'instagram', false, '');

			if (!$first_name || !$last_name) {
				$message = 'Le prenom et le nom sont obligatoires.';
			} else {
				if ($avatar_url === '') {
					$avatar_url = mysqli_real_escape_string($link, $default_avatar);
				}

				$query = "INSERT INTO artists (first_name, last_name, email, nickname, avatar_url, bio, style, spotify, youtube, deezer, soundcloud, bandlab, beam, instagram)
					VALUES ('$first_name', '$last_name', " . sql_optional($email) . ", " . sql_optional($nickname) . ", '$avatar_url', " . sql_optional($bio) . ", " . sql_optional($style) . ", " . sql_optional($spotify) . ", " . sql_optional($youtube) . ", " . sql_optional($deezer) . ", " . sql_optional($soundcloud) . ", " . sql_optional($bandlab) . ", " . sql_optional($beam) . ", " . sql_optional($instagram) . ")";

				if (mysqli_query($link, $query)) {
					$message = 'Artiste cree.';
				} else {
					$message = 'Erreur: ' . mysqli_error($link);
				}
			}
		} elseif ($action === 'update') {
			$id = isset($_POST['id']) ? $_POST['id'] : '';
			$first_name = clean_value($link, 'first_name');
			$last_name = clean_value($link, 'last_name');
			$email = clean_value($link, 'email', false, '');
			$nickname = clean_value($link, 'nickname', false, '');
			$avatar_url = clean_value($link, 'avatar_url', false, '');
			$bio = clean_value($link, 'bio', false, '');
			$style = clean_value($link, 'style', false, '');
			$spotify = clean_value($link, 'spotify', false, '');
			$youtube = clean_value($link, 'youtube', false, '');
			$deezer = clean_value($link, 'deezer', false, '');
			$soundcloud = clean_value($link, 'soundcloud', false, '');
			$bandlab = clean_value($link, 'bandlab', false, '');
			$beam = clean_value($link, 'beam', false, '');
			$instagram = clean_value($link, 'instagram', false, '');

			if ($id === '') {
				$message = 'ID invalide.';
			} elseif (!$first_name || !$last_name) {
				$message = 'Le prenom et le nom sont obligatoires.';
			} else {
				$id = mysqli_real_escape_string($link, $id);
				if ($avatar_url === '') {
					$avatar_url = mysqli_real_escape_string($link, $default_avatar);
				}

				$query = "UPDATE artists
					SET first_name = '$first_name',
						last_name = '$last_name',
						email = " . sql_optional($email) . ",
						nickname = " . sql_optional($nickname) . ",
						avatar_url = '$avatar_url',
						bio = " . sql_optional($bio) . ",
						style = " . sql_optional($style) . ",
						spotify = " . sql_optional($spotify) . ",
						youtube = " . sql_optional($youtube) . ",
						deezer = " . sql_optional($deezer) . ",
						soundcloud = " . sql_optional($soundcloud) . ",
						bandlab = " . sql_optional($bandlab) . ",
						beam = " . sql_optional($beam) . ",
						instagram = " . sql_optional($instagram) . "
					WHERE id = '$id'
					LIMIT 1";

				if (mysqli_query($link, $query)) {
					$message = 'Artiste mis a jour.';
				} else {
					$message = 'Erreur: ' . mysqli_error($link);
				}
			}
		} elseif ($action === 'delete') {
			$id = isset($_POST['id']) ? $_POST['id'] : '';

			if ($id === '') {
				$message = 'ID invalide.';
			} else {
				$id = mysqli_real_escape_string($link, $id);
				if (mysqli_query($link, "DELETE FROM artists WHERE id = '$id' LIMIT 1")) {
				$message = 'Artiste supprime.';
				} else {
					$message = 'Erreur: ' . mysqli_error($link);
				}
			}
		}
	}

	$edit_id = isset($_GET['id']) ? $_GET['id'] : '';
	if ($edit_id !== '') {
		$edit_artist = fetch_artist($link, $edit_id);

		if (!$edit_artist) {
			$message = 'Artiste introuvable.';
		}
	}

	$artists = fetch_artists($link);
	$form_title = $edit_artist ? 'Modifier un artiste' : 'Ajouter un artiste';
	$form_action = $edit_artist ? 'update' : 'create';
	$submit_label = $edit_artist ? 'Mettre a jour' : 'Ajouter';
	$avatar_value = $edit_artist ? $edit_artist['avatar_url'] : $default_avatar;
?>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>DRUMM'Show - Admin Artistes</title>

		<link rel="stylesheet" href="/assets/css/main.css" type="text/css" />
		<link rel="stylesheet" href="/assets/css/admin.css" type="text/css" />
	</head>
	<body>
		<nav class="navbar admin-nav">
			<a class="navlink" href="/admin/index.php?<?php echo admin_params(); ?>">
				<span>Dashboard</span>
			</a>
			<a class="navlink active" href="/admin/artists.php?<?php echo admin_params(); ?>">
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
			<h1>Artistes</h1>
			<p>Gerer les profils et les liens sociaux.</p>
		</header>
		<main>
			<section class="admin-section">
				<?php if ($message) { ?>
					<p class="admin-message"><?php echo escape_html($message); ?></p>
				<?php } ?>

				<h2><?php echo $form_title; ?></h2>
				<form class="admin-form" method="POST" action="/admin/artists.php?<?php echo admin_params(); ?>">
					<input type="hidden" name="action" value="<?php echo $form_action; ?>" />
					<?php if ($edit_artist) { ?>
						<input type="hidden" name="id" value="<?php echo escape_html($edit_artist['id']); ?>" />
					<?php } ?>
					<div class="form-row">
						<label for="first_name">
							<span>Prenom*</span>
							<input type="text" name="first_name" id="first_name" value="<?php echo escape_html($edit_artist['first_name'] ?? ''); ?>" required />
						</label>
						<label for="last_name">
							<span>Nom*</span>
							<input type="text" name="last_name" id="last_name" value="<?php echo escape_html($edit_artist['last_name'] ?? ''); ?>" required />
						</label>
						<label for="email">
							<span>Email</span>
							<input type="email" name="email" id="email" value="<?php echo escape_html($edit_artist['email'] ?? ''); ?>" />
						</label>
					</div>
					<div class="form-row">
						<label for="nickname">
							<span>Nom de scene</span>
							<input type="text" name="nickname" id="nickname" value="<?php echo escape_html($edit_artist['nickname'] ?? ''); ?>" />
						</label>
						<label for="avatar_url">
							<span>Avatar URL</span>
							<input type="url" name="avatar_url" id="avatar_url" value="<?php echo escape_html($avatar_value); ?>" />
						</label>
						<label for="style">
							<span>Style</span>
							<input type="text" name="style" id="style" value="<?php echo escape_html($edit_artist['style'] ?? ''); ?>" />
						</label>
					</div>
					<label for="bio">
						<span>Bio</span>
						<textarea name="bio" id="bio"><?php echo escape_html($edit_artist['bio'] ?? ''); ?></textarea>
					</label>
					<h3>Reseaux sociaux</h3>
					<div class="form-row">
						<label for="spotify">
							<span>Spotify</span>
							<input type="text" name="spotify" id="spotify" value="<?php echo escape_html($edit_artist['spotify'] ?? ''); ?>" />
						</label>
						<label for="youtube">
							<span>YouTube</span>
							<input type="text" name="youtube" id="youtube" value="<?php echo escape_html($edit_artist['youtube'] ?? ''); ?>" />
						</label>
						<label for="deezer">
							<span>Deezer</span>
							<input type="text" name="deezer" id="deezer" value="<?php echo escape_html($edit_artist['deezer'] ?? ''); ?>" />
						</label>
					</div>
					<div class="form-row">
						<label for="soundcloud">
							<span>SoundCloud</span>
							<input type="text" name="soundcloud" id="soundcloud" value="<?php echo escape_html($edit_artist['soundcloud'] ?? ''); ?>" />
						</label>
						<label for="bandlab">
							<span>Bandlab</span>
							<input type="text" name="bandlab" id="bandlab" value="<?php echo escape_html($edit_artist['bandlab'] ?? ''); ?>" />
						</label>
						<label for="beam">
							<span>Beam</span>
							<input type="text" name="beam" id="beam" value="<?php echo escape_html($edit_artist['beam'] ?? ''); ?>" />
						</label>
					</div>
					<div class="form-row">
						<label for="instagram">
							<span>Instagram</span>
							<input type="text" name="instagram" id="instagram" value="<?php echo escape_html($edit_artist['instagram'] ?? ''); ?>" />
						</label>
					</div>
					<div class="admin-actions">
						<input type="submit" value="<?php echo $submit_label; ?>" />
						<?php if ($edit_artist) { ?>
							<a class="button btn-secondary" href="/admin/artists.php?<?php echo admin_params(); ?>">Annuler</a>
						<?php } ?>
					</div>
				</form>

				<h2>Liste des artistes</h2>
				<?php if (!$artists) { ?>
					<p>Aucun artiste pour le moment.</p>
				<?php } else { ?>
					<table class="admin-table">
						<thead>
							<tr>
								<th>Artiste</th>
								<th>Contact</th>
								<th>Style</th>
								<th>Ajoute le</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($artists as $artist) {
								$display_name = $artist['nickname'] ? $artist['nickname'] : $artist['first_name'] . ' ' . $artist['last_name'];
								$created_at = $artist['created_at'] ? date('d/m/Y', strtotime($artist['created_at'])) : '';
							?>
							<tr>
								<td><?php echo escape_html($display_name); ?></td>
								<td><?php echo escape_html($artist['email'] ?? ''); ?></td>
								<td><?php echo escape_html($artist['style'] ?? ''); ?></td>
								<td><?php echo escape_html($created_at); ?></td>
								<td>
									<div class="admin-actions">
										<a class="button" href="/admin/artists.php?id=<?php echo escape_html($artist['id']); ?>&<?php echo admin_params(); ?>">Editer</a>
										<form class="admin-inline" method="POST" action="/admin/artists.php?<?php echo admin_params(); ?>" onsubmit="return confirm('Supprimer cet artiste ?');">
											<input type="hidden" name="action" value="delete" />
											<input type="hidden" name="id" value="<?php echo escape_html($artist['id']); ?>" />
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
