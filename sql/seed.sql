
INSERT INTO scenes (id, name, capacity, outdoor, price_solo, price_group) VALUES
('ozzy_osbourne', 'Ozzy Osbourne', 60, 0, 5, 20),
('john_lennon', 'John Lennon', 140, 1, 30, 60),
('duke_ellington', 'Duke Ellington', 300, 1, 50, 80),
('michael_jackson', 'Michael Jackson', 500, 0, 70, 100);

INSERT INTO artists (id, first_name, last_name, nickname, style, avatar_url) VALUES
(1, 'Aexelo', 'Aexelo', 'Æxelo', 'rap', '/assets/demo/exelo.jpg'),
(2, 'Ejnalo', 'Ejnalo', 'Ejnalo', 'rap, classique, rock, jazz', '/assets/demo/ejnalo.webp'),
(3, 'Bastoon', 'Bastoon', 'Bastoon', 'techno', '/assets/demo/bastoon.webp'),
(4, 'The', 'Grunpy Guinea Pigs', 'The Grunpy Guinea Pigs', 'rock', '/assets/demo/ggp.jpg');

INSERT INTO concerts (id, planned_at, ends_at, name, description, price_customer, scene, artist) VALUES
(1, '2026-04-27 15:00:00', '2026-04-27 15:45:00', 'Rimes incendiaires', 'Rap brut et flows serres', 12, 'ozzy_osbourne', 1),
(2, '2026-04-27 16:15:00', '2026-04-27 16:50:00', 'Techno ignition', 'Kick rapide et basses lourdes', 25, 'john_lennon', 3),
(3, '2026-04-27 17:10:00', '2026-04-27 17:55:00', 'Rock cage', 'Guitares rugueuses et hooks', 30, 'duke_ellington', 4),
(4, '2026-04-27 19:00:00', '2026-04-27 19:40:00', 'Crossfade', 'Rap et jazz en fusion', 30, 'john_lennon', 2),
(5, '2026-04-27 20:30:00', '2026-04-27 21:30:00', 'Night blitz', 'Rap sur scene premium', 80, 'michael_jackson', 1),
(6, '2026-04-27 23:00:00', '2026-04-28 00:00:00', 'Midnight pulse', 'Techno hyper energie', 90, 'michael_jackson', 3),

(7, '2026-04-28 15:00:00', '2026-04-28 15:30:00', 'Kickstart', 'Techno courte et intense', 12, 'ozzy_osbourne', 3),
(8, '2026-04-28 16:00:00', '2026-04-28 16:45:00', 'Chromatique', 'Rap et classique en miroir', 30, 'duke_ellington', 2),
(9, '2026-04-28 17:15:00', '2026-04-28 18:00:00', 'Rap en fusion', 'Flow acide et refrains courts', 25, 'john_lennon', 1),
(10, '2026-04-28 19:00:00', '2026-04-28 19:45:00', 'Guinea Riot', 'Rock brut et refrains larges', 35, 'duke_ellington', 4),
(11, '2026-04-28 20:30:00', '2026-04-28 21:15:00', 'Velvet drive', 'Set premium tres attendu', 80, 'michael_jackson', 2),
(12, '2026-04-28 23:00:00', '2026-04-29 00:00:00', 'Midnight roars', 'Rock scene premium', 85, 'michael_jackson', 4),

(13, '2026-04-29 15:00:00', '2026-04-29 15:40:00', 'Piggy Groove', 'Rock nerveux et sec', 12, 'ozzy_osbourne', 4),
(14, '2026-04-29 16:10:00', '2026-04-29 16:55:00', 'Warehouse spark', 'Techno groove et kicks', 25, 'john_lennon', 3),
(15, '2026-04-29 17:20:00', '2026-04-29 18:05:00', 'Rimes sur cuivre', 'Rap dense et textures', 30, 'duke_ellington', 1),
(16, '2026-04-29 19:00:00', '2026-04-29 19:35:00', 'Horizon line', 'Rap et rock melanges', 30, 'john_lennon', 2),
(17, '2026-04-29 20:20:00', '2026-04-29 21:20:00', 'Laser storm', 'Techno premium au max', 80, 'michael_jackson', 3),
(18, '2026-04-29 23:00:00', '2026-04-30 00:00:00', 'Nightflow', 'Rap tardif et incisif', 90, 'michael_jackson', 1),

(19, '2026-04-30 15:00:00', '2026-04-30 15:50:00', 'Mosaik', 'Rap, jazz et rock', 12, 'ozzy_osbourne', 2),
(20, '2026-04-30 16:20:00', '2026-04-30 17:05:00', 'Pulse Drive', 'Techno rapide et dense', 30, 'duke_ellington', 3),
(21, '2026-04-30 17:30:00', '2026-04-30 18:15:00', 'Guinea Parade', 'Rock rugueux en plein air', 25, 'john_lennon', 4),
(22, '2026-04-30 19:00:00', '2026-04-30 19:50:00', 'Verse flare', 'Rap et textures live', 35, 'duke_ellington', 1),
(23, '2026-04-30 20:30:00', '2026-04-30 21:20:00', 'Stadium roar', 'Rock premium sur grande scene', 80, 'michael_jackson', 4),
(24, '2026-04-30 23:00:00', '2026-05-01 00:00:00', 'Midnight suite', 'Set premium hybride', 85, 'michael_jackson', 2),

(25, '2026-05-01 15:00:00', '2026-05-01 15:45:00', 'Flowline', 'Rap accrocheur et direct', 12, 'ozzy_osbourne', 1),
(26, '2026-05-01 16:10:00', '2026-05-01 16:55:00', 'Fusion classica', 'Rap et classique en tension', 25, 'john_lennon', 2),
(27, '2026-05-01 17:20:00', '2026-05-01 18:00:00', 'Electro rush', 'Techno frappe et hooks', 30, 'duke_ellington', 3),
(28, '2026-05-01 19:00:00', '2026-05-01 19:40:00', 'Piggy Riot', 'Rock et refrains solides', 30, 'john_lennon', 4),
(29, '2026-05-01 20:30:00', '2026-05-01 21:30:00', 'Premium blaze', 'Rap premium et ambiance', 80, 'michael_jackson', 1),
(30, '2026-05-01 23:00:00', '2026-05-02 00:00:00', 'Neon crash', 'Techno final explosive', 90, 'michael_jackson', 3);
