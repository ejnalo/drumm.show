-- --------------------------------------------------------
-- Table : artists
-- --------------------------------------------------------

CREATE TABLE `artists` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `name` VARCHAR(128) NOT NULL COMMENT 'Nom de l''artiste',
  `last_name` VARCHAR(128) NOT NULL COMMENT 'Nom de famille de l''artiste',
  `nickname` VARCHAR(128) DEFAULT NULL COMMENT 'Nom de scène',
  `bio` VARCHAR(255) DEFAULT NULL COMMENT 'Description de l''artiste',
  `avatar_url` VARCHAR(128) DEFAULT 'https://api.beam.ejnalo.me/users/beam/avatar.png'
    COMMENT 'Lien de la photo de l''artiste',
  `style` VARCHAR(255) DEFAULT NULL COMMENT 'Styles de l''artiste',
  PRIMARY KEY (`id`)
);


-- --------------------------------------------------------
-- Table : scenes
-- --------------------------------------------------------

CREATE TABLE `scenes` (
  `id` VARCHAR(32) NOT NULL,
  `name` VARCHAR(255)
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_0900_ai_ci
    NOT NULL COMMENT 'Nom complet de la salle',
  `capacity` INT NOT NULL COMMENT 'Capacité de la salle',
  `outdoor` BOOLEAN NOT NULL DEFAULT FALSE
    COMMENT 'Salle en extérieur',
  `price_solo` INT NOT NULL DEFAULT '0'
    COMMENT 'Prix horaire pour un soliste',
  `price_group` INT DEFAULT NULL
    COMMENT 'Prix horaire pour un groupe',
  PRIMARY KEY (`id`)
);


-- --------------------------------------------------------
-- Table : concerts
-- --------------------------------------------------------

CREATE TABLE `concerts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    COMMENT 'Date de réservation du concert',
  `planned_at` TIMESTAMP NOT NULL COMMENT 'Date de concert',
  `ends_at` TIMESTAMP NOT NULL COMMENT 'Date de fin du concert',
  `scene` VARCHAR(32)
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_0900_ai_ci
    NOT NULL COMMENT 'Scène où aura lieu le concert',
  `artist` INT UNSIGNED NOT NULL COMMENT 'Artiste qui performe',

  PRIMARY KEY (`id`),

  KEY `concerts_artists_relation` (`artist`),
  KEY `concerts_scene_relation` (`scene`),

  CONSTRAINT `concerts_artists_relation`
    FOREIGN KEY (`artist`)
    REFERENCES `artists` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,

  CONSTRAINT `concerts_scene_relation`
    FOREIGN KEY (`scene`)
    REFERENCES `scenes` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE

);


-- --------------------------------------------------------
-- Table : visitors
-- --------------------------------------------------------

CREATE TABLE `visitors` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    COMMENT 'Date d''enregistrement du client',

  `first_name` VARCHAR(128) NOT NULL
    COMMENT 'Prénom du client',

  `last_name` VARCHAR(128) NOT NULL
    COMMENT 'Nom de famille du client',

  `age` INT NOT NULL,

  `status` VARCHAR(32) DEFAULT 'normal'
    COMMENT 'Statut du client (normal, étudiant...)',

  `student_number` VARCHAR(16) DEFAULT NULL
    COMMENT 'N° étudiant',

  `mail` VARCHAR(255) UNIQUE NOT NULL
    COMMENT 'Adresse mail du client',

  `tel` VARCHAR(32) UNIQUE DEFAULT NULL
    COMMENT 'N° de tel du client',

  `address` VARCHAR(255) DEFAULT NULL
    COMMENT 'Adresse du client',

  PRIMARY KEY (`id`)
);


-- --------------------------------------------------------
-- Table : reservations
-- --------------------------------------------------------

CREATE TABLE `reservations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    COMMENT 'Date de réservation',

  `day` DATE NOT NULL
    COMMENT 'Jour de réservation',

  `morning` TINYINT(1) NOT NULL DEFAULT 0
    COMMENT 'Actif le matin',

  `afternoon` TINYINT(1) NOT NULL DEFAULT 0
    COMMENT 'Actif l''après-midi',

  `vip` TINYINT(1) NOT NULL DEFAULT 0
    COMMENT 'True',

  `owner` INT UNSIGNED NOT NULL
    COMMENT 'Proprio',

  PRIMARY KEY (`id`),

  KEY `reservations_owners` (`owner`),

  CONSTRAINT `reservations_owners`
    FOREIGN KEY (`owner`)
    REFERENCES `visitors` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);
