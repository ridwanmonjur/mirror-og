USE splash;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `email_verified_token`, `email_verified_expires_at`, `password`, `country_code`, `mobile_no`, `role`, `status`, `remember_token`, `created_at`, `updated_at`, `google_id`) VALUES
(1, 'Admin', 'admin@example.com', NULL, '08XknKWJLm8nUlEK11oYtie7bnRV7tKA4nLhA9yt4ZyaWmrFeVh00H0eGnzFpR78', NULL, '$2y$10$3b8hd9PNVg/ikULcrGgg/enkqjdqBSbZmgzwpxBzM7Ps2fW3SEkCa', NULL, NULL, 'ADMIN', NULL, NULL, '2023-11-05 20:36:17', '2023-11-06 18:14:34', NULL),
(2, 'o_zach', 'o_zach@gmail.com', '2023-11-05 20:44:48', NULL, NULL, '$2y$10$sDcA25DcYz.IpB3/TKxRP.kbnDc0LzSe..pGFc3g1FNIICGSmSsLi', NULL, NULL, 'ORGANIZER', NULL, NULL, '2023-11-05 20:44:22', '2023-11-05 20:44:48', NULL),
(3, 'Oceans Gaming', 'oceans@gmail.com', '2023-11-06 07:42:46', NULL, NULL, '$2y$10$0li6SVWriI9KTW.4LazL0eCR6q4TfBvbmWoqCH3rcAwEz3lEUyWgq', NULL, NULL, 'ORGANIZER', NULL, NULL, '2023-11-06 07:42:08', '2023-11-06 07:42:46', NULL),
(4, 'o_leigh', 'o_leigh@gmail.com', '2023-11-06 17:46:09', NULL, NULL, '$2y$10$WUWJt5tJ4M6Domo78yjYXuQlJAEr3DHJVM0LMN8lcW1HgZkpnVbmW', NULL, NULL, 'ORGANIZER', NULL, NULL, '2023-11-06 17:45:20', '2023-11-06 17:46:09', NULL),
(5, 'ridwanmonjur', 'ridwanmonjur@gmail.com', '2023-11-06 17:46:09', 'ExkEClSE2Gm2geLOK2zMXGYb54J1THPiaY3MplANxan7pXKgRENwLzkxg9o5Lt3v', NULL, '$2y$10$lH28CRN3B5YizMg/H4L1J.Xu1v6B2.6A3pN/9tVUG/xR9nh.ibht6', NULL, NULL, 'ORGANIZER', NULL, NULL, '2023-11-10 10:02:24', '2023-11-10 10:02:24', NULL),
(6, 'p_leigh', 'p_leigh@gmail.com', '2023-11-06 17:46:09', 'ExkEClSE2Gm2geLOK2zMXGYb54J1THPiaY3MplANxan7pXKgRENwLzkxg9o5Lt3v', NULL, '$2y$10$lH28CRN3B5YizMg/H4L1J.Xu1v6B2.6A3pN/9tVUG/xR9nh.ibht6', NULL, NULL, 'PARTICIPANT', NULL, NULL, '2023-11-10 10:02:24', '2023-11-10 10:02:24', NULL),
(7, 'p_zach', 'p_zach@gmail.com', '2023-11-06 17:46:09', 'ExkEClSE2Gm2geLOK2zMXGYb54J1THPiaY3MplANxan7pXKgRENwLzkxg9o5Lt3v', NULL, '$2y$10$lH28CRN3B5YizMg/H4L1J.Xu1v6B2.6A3pN/9tVUG/xR9nh.ibht6', NULL, NULL, 'PARTICIPANT', NULL, NULL, '2023-11-10 10:02:24', '2023-11-10 10:02:24', NULL),
(8, 'p_ridwan', 'p_ridwan@gmail.com', '2023-11-06 17:46:09', 'ExkEClSE2Gm2geLOK2zMXGYb54J1THPiaY3MplANxan7pXKgRENwLzkxg9o5Lt3v', NULL, '$2y$10$lH28CRN3B5YizMg/H4L1J.Xu1v6B2.6A3pN/9tVUG/xR9nh.ibht6', NULL, NULL, 'PARTICIPANT', NULL, NULL, '2023-11-10 10:02:24', '2023-11-10 10:02:24', NULL),
(9, 'participant4', 'participant4@gmail.com', '2023-11-06 17:46:09', 'ExkEClSE2Gm2geLOK2zMXGYb54J1THPiaY3MplANxan7pXKgRENwLzkxg9o5Lt3v', NULL, '$2y$10$lH28CRN3B5YizMg/H4L1J.Xu1v6B2.6A3pN/9tVUG/xR9nh.ibht6', NULL, NULL, 'PARTICIPANT', NULL, NULL, '2023-11-10 10:02:24', '2023-11-10 10:02:24', NULL),
(10, 'participant5', 'participant5@gmail.com', '2023-11-06 17:46:09', 'ExkEClSE2Gm2geLOK2zMXGYb54J1THPiaY3MplANxan7pXKgRENwLzkxg9o5Lt3v', NULL, '$2y$10$lH28CRN3B5YizMg/H4L1J.Xu1v6B2.6A3pN/9tVUG/xR9nh.ibht6', NULL, NULL, 'PARTICIPANT', NULL, NULL, '2023-11-10 10:02:24', '2023-11-10 10:02:24', NULL),
(11, 'participant6', 'participant6@gmail.com', '2023-11-06 17:46:09', 'ExkEClSE2Gm2geLOK2zMXGYb54J1THPiaY3MplANxan7pXKgRENwLzkxg9o5Lt3v', NULL, '$2y$10$lH28CRN3B5YizMg/H4L1J.Xu1v6B2.6A3pN/9tVUG/xR9nh.ibht6', NULL, NULL, 'PARTICIPANT', NULL, NULL, '2023-11-10 10:02:24', '2023-11-10 10:02:24', NULL),
(12, 'participant7', 'participant7@gmail.com', '2023-11-06 17:46:09', 'ExkEClSE2Gm2geLOK2zMXGYb54J1THPiaY3MplANxan7pXKgRENwLzkxg9o5Lt3v', NULL, '$2y$10$lH28CRN3B5YizMg/H4L1J.Xu1v6B2.6A3pN/9tVUG/xR9nh.ibht6', NULL, NULL, 'PARTICIPANT', NULL, NULL, '2023-11-10 10:02:24', '2023-11-10 10:02:24', NULL),
(13, 'participant8', 'participant8@gmail.com', '2023-11-06 17:46:09', 'ExkEClSE2Gm2geLOK2zMXGYb54J1THPiaY3MplANxan7pXKgRENwLzkxg9o5Lt3v', NULL, '$2y$10$lH28CRN3B5YizMg/H4L1J.Xu1v6B2.6A3pN/9tVUG/xR9nh.ibht6', NULL, NULL, 'PARTICIPANT', NULL, NULL, '2023-11-10 10:02:24', '2023-11-10 10:02:24', NULL),
(14, 'participant9', 'participant9@gmail.com', '2023-11-06 17:46:09', 'ExkEClSE2Gm2geLOK2zMXGYb54J1THPiaY3MplANxan7pXKgRENwLzkxg9o5Lt3v', NULL, '$2y$10$lH28CRN3B5YizMg/H4L1J.Xu1v6B2.6A3pN/9tVUG/xR9nh.ibht6', NULL, NULL, 'PARTICIPANT', NULL, NULL, '2023-11-10 10:02:24', '2023-11-10 10:02:24', NULL),
(15, 'participant10', 'participant10@gmail.com', '2023-11-06 17:46:09', 'ExkEClSE2Gm2geLOK2zMXGYb54J1THPiaY3MplANxan7pXKgRENwLzkxg9o5Lt3v', NULL, '$2y$10$lH28CRN3B5YizMg/H4L1J.Xu1v6B2.6A3pN/9tVUG/xR9nh.ibht6', NULL, NULL, 'PARTICIPANT', NULL, NULL, '2023-11-10 10:02:24', '2023-11-10 10:02:24', NULL);

INSERT INTO participants (avatar,created_at,updated_at,user_id) VALUES
	 (NULL,NULL,NULL,6),
	 (NULL,NULL,NULL,7),
	 (NULL,NULL,NULL,8),
	 (NULL,NULL,NULL,9),
	 (NULL,NULL,NULL,10),
	 (NULL,NULL,NULL,11),
	 (NULL,NULL,NULL,12),
	 (NULL,NULL,NULL,13),
	 (NULL,NULL,NULL,14),
	 (NULL,NULL,NULL,15);



INSERT INTO `organizers` (`id`, `companyName`, `companyDescription`, `created_at`, `updated_at`, `user_id`) VALUES
(1, 'Splash Test', 'Good Company', '2023-11-05 20:44:22', '2023-11-05 20:44:22', 1),
(2, 'Oceans Gaming Co', 'Cool Water Gaming', '2023-11-06 07:42:08', '2023-11-06 07:42:08', 1),
(3, 'Human', 'Good', '2023-11-06 17:45:20', '2023-11-06 17:45:20', 1),
(4, 'ridwanmonjur@gmail.com', 'ridwanmonjur@gmail.com', '2023-11-10 10:02:24', '2023-11-10 10:02:24', 1);


INSERT INTO `event_categories` (`id`, `gameTitle`, `gameIcon`, `eventDefinitions`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Dota 2', 'download.jpg', "Dota 2 is a 2013 multiplayer online battle arena video game by Valve. The game is a sequel to Defense of the Ancients, a community-created mod for Blizzard Entertainment\'s Warcraft III: Reign of Chaos.", NULL, '2023-11-05 14:39:09', '2023-12-19 04:46:06'),
(2, 'Dota ', 'images/event_details/dota.png', 'dota doing good', NULL, '2023-11-05 21:06:58', '2023-11-05 21:06:58');


INSERT INTO `event_tier` (`id`, `eventTier`, `tierIcon`, `tierTeamSlot`, `tierPrizePool`, `tierEntryFee`, `user_id`) VALUES
(1, 'Starfish', 'images/event_details/starfish.png', '16', '5000', '10', NULL),
(2, 'Turtle', 'images/event_details/turtle.png', '32', '10000', '20', NULL),
(3, 'Dolphin', 'images/event_details/dolphin.png', '64', '15000', '30', NULL);

INSERT INTO `event_type` (`id`, `eventType`, `eventDefinitions`) VALUES
(1, 'Tournament', "You\'ll play a series of contests between a number of competitors that will result in teams getting disqualified based on results and points, till you get the final prize."),
(2, 'League', 'The winner is decided by the highest win/ loss/ draw ratio among the participants.');

INSERT INTO `organizer_create_event_discounts` (`name`,`coupon`,`type`,`amount`, `startDate`, `endDate`, `startTime`, `endTime`, `isEnforced`) VALUES
	 ('JAN_50','50_PERCENT','percent',50.0,'2024-01-13','2027-01-13','20:57:00','20:57:00',1),
	 ('JAN_25','25_PERCENT','percent',25.0,'2024-01-13','2027-01-13','20:57:00','20:57:00',1),
	 ('JAN_250RM','JAN_250RM','sum',250.0,'2024-01-13','2027-01-13','20:57:00','20:57:00',1),
	 ('JAN_1000RM','JAN_1000RM','sum',1000.0,'2024-01-13','2027-01-13','20:57:00','20:57:00',1),
	 ('JAN_EXPIRED','JAN_EXPIRED','sum',1000.0,'2024-01-13','2024-01-15','20:57:00','20:57:00',1),
	 ('JAN_NOT_ENFORCED','JAN_NOT_ENFORCED','sum',1000.0,'2024-01-13','2027-01-13','20:57:00','20:57:00',0),
	 ('JAN_NOT_STARTED','JAN_NOT_STARTED','sum',1000.0,'2026-01-13','2027-01-13','20:57:00','20:57:00',0);


INSERT INTO `awards` (`title`,`image`,`description`,`created_at`,`updated_at`) VALUES
	 ('Championship','images/awards/trophy1.png','This is an amazing tropy','2023-11-05 14:39:09','2023-11-05 14:39:09'),
	 ('Trophy','images/awards/trophy2.png','This is an amazing tropy','2023-11-05 14:39:09','2023-11-05 14:39:09'),
	 ('Award','images/awards/trophy3.png','This is an amazing tropy','2023-11-05 14:39:09','2023-11-05 14:39:09'),
	 ('Prize','images/awards/trophy4.png','This is an amazing tropy','2023-11-05 14:39:09','2023-11-05 14:39:09'),
	 ('Crest','images/awards/trophy5.png','This is an amazing tropy','2023-11-05 14:39:09','2023-11-05 14:39:09');


INSERT INTO `games` (`name`,`image`) VALUES
	 ('Dota ','images/games/dota.png'),
	 ('Dota 2','images/games/dota2.png'),
	 ('CS Go','images/games/csgo.jpg'),
	 ('Apex','images/games/apex.png'),
	 ('League of Legends','images/games/lol.jpeg'),
	 ('Valorant','images/games/valorant.jpeg');