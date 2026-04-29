CREATE TABLE scenes (
  id varchar(32) NOT NULL,
  name varchar(255) NOT NULL, -- Nom complet de la salle
  capacity int NOT NULL, -- Capacité de la salle
  outdoor tinyint(1) NOT NULL DEFAULT 0, -- Salle en extérieur
  price_solo int NOT NULL DEFAULT 0, -- Prix horaire pour un soliste
  price_group int DEFAULT NULL, -- Prix horaire pour un groupe
  PRIMARY KEY (id)
);

CREATE TABLE artists (
  id int unsigned NOT NULL AUTO_INCREMENT,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  first_name varchar(128) NOT NULL, -- Nom de l'artiste
  last_name varchar(128) NOT NULL, -- Prénom de l'artiste
  email varchar(128) DEFAULT NULL, -- Email de l'artiste
  nickname varchar(128) DEFAULT NULL, -- Nom de scène
  avatar_url varchar(128) NOT NULL DEFAULT 'https://api.beam.ejnalo.me/users/beam/avatar.png', -- Photo
  bio varchar(255) DEFAULT NULL, -- Description
  style varchar(255) DEFAULT NULL, -- Styles
  spotify varchar(64) DEFAULT NULL,
  youtube varchar(64) DEFAULT NULL,
  deezer varchar(64) DEFAULT NULL,
  soundcloud varchar(64) DEFAULT NULL,
  bandlab varchar(64) DEFAULT NULL,
  beam varchar(64) DEFAULT NULL,
  instagram varchar(64) DEFAULT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE concerts (
  id int unsigned NOT NULL AUTO_INCREMENT,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date de réservation
  planned_at timestamp NOT NULL, -- Date de concert
  ends_at timestamp NOT NULL, -- Fin du concert
  name varchar(64) DEFAULT NULL, -- Titre
  description varchar(255) DEFAULT NULL, -- Description
  scene varchar(32) NOT NULL, -- Scène
  artist int unsigned NOT NULL, -- Artiste
  PRIMARY KEY (id),
  KEY concerts_artists_relation (artist),
  KEY concerts_scene_relation (scene),
  CONSTRAINT concerts_artists_relation FOREIGN KEY (artist) REFERENCES artists(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT concerts_scene_relation FOREIGN KEY (scene) REFERENCES scenes(id) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE visitors (
  id int unsigned NOT NULL AUTO_INCREMENT,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date d'enregistrement
  first_name varchar(128) NOT NULL, -- Prénom
  last_name varchar(128) NOT NULL, -- Nom
  age int NOT NULL,
  status varchar(32) NOT NULL DEFAULT 'normal', -- Statut
  student_number varchar(16) DEFAULT NULL, -- N° étudiant
  mail varchar(255) NOT NULL, -- Email
  tel varchar(32) DEFAULT NULL, -- Téléphone
  address varchar(255) DEFAULT NULL, -- Adresse
  PRIMARY KEY (id),
  UNIQUE KEY mail_unique (mail),
  UNIQUE KEY tel_unique (tel)
);

CREATE TABLE reservations (
  id int unsigned NOT NULL AUTO_INCREMENT,
  created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, -- Date de réservation
  day date NOT NULL DEFAULT '1970-01-01', -- Jour
  afternoon tinyint(1) NOT NULL DEFAULT 0, -- Après-midi
  evening tinyint(1) NOT NULL DEFAULT 0, -- Soir
  vip tinyint(1) NOT NULL DEFAULT 0, -- VIP
  owner int unsigned NOT NULL, -- Proprio
  PRIMARY KEY (id),
  KEY reservations_owners (owner),
  CONSTRAINT reservations_owners FOREIGN KEY (owner) REFERENCES visitors(id) ON DELETE CASCADE ON UPDATE CASCADE
);
