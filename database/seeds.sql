USE artovia_db;

-- Step 1: Temporarily turn off foreign key safety checks
SET FOREIGN_KEY_CHECKS = 0;

-- Step 2: Empty all data from every table
TRUNCATE TABLE account_status_tbl;
TRUNCATE TABLE account_tbl;
TRUNCATE TABLE administrator_tbl;
TRUNCATE TABLE artist_tbl;
TRUNCATE TABLE category_tbl;
TRUNCATE TABLE commission_request_tbl;
TRUNCATE TABLE commission_tbl;
TRUNCATE TABLE conversation_tbl;
TRUNCATE TABLE favorite_tbl;
TRUNCATE TABLE hired_artist_tbl;
TRUNCATE TABLE image_tbl;
TRUNCATE TABLE image_type_tbl;
TRUNCATE TABLE message_tbl;
TRUNCATE TABLE payment_method_tbl;
TRUNCATE TABLE payment_tbl;
TRUNCATE TABLE portfolio_image_tbl;
TRUNCATE TABLE portfolio_tbl;
TRUNCATE TABLE role_tbl;
TRUNCATE TABLE status_tbl;
TRUNCATE TABLE transaction_tbl;
TRUNCATE TABLE user_tbl;

-- Step 3: Turn safety checks back on
SET FOREIGN_KEY_CHECKS = 1;


INSERT INTO account_status_tbl (account_status_id, status_name) VALUES
(1, 'Active'), (2, 'Banned'), (3, 'Suspended');

INSERT INTO role_tbl (role_id, role_name) VALUES
(1, 'User'), (2, 'Artist'), (3, 'Administrator');

INSERT INTO status_tbl (status_id, status_name) VALUES
(1, 'Active'), (2, 'Pending'), (3, 'Accepted'), (4, 'Rejected'), 
(5, 'In Progress'), (6, 'Completed'), (7, 'Cancelled'), (8, 'Read'), 
(9, 'Unread'), (10, 'Paid');

INSERT INTO payment_method_tbl (payment_method_id, payment_method_name) VALUES
(1, 'GCash'), (2, 'PayPal'), (3, 'Credit Card'), (4, 'Bank Transfer');

INSERT INTO image_type_tbl (image_type_id, image_type_name) VALUES
(1, 'Profile'), (2, 'Artwork'), (3, 'Commission'), (4, 'Reference');

INSERT INTO category_tbl (category_id, category_name) VALUES
(1, 'Anime'), (2, 'Chibi'), (3, 'Pixel Art'), (4, 'Watercolor'), 
(5, 'Fantasy'), (6, 'Logo Design'), (7, 'Portrait'), (8, 'Character Design');


-- ==========================================================
-- STEP 3: MAIN SYSTEM TABLES (Dependent on Lookups)
-- ==========================================================

-- Account Table (105 Rows)
INSERT INTO account_tbl (role_id, account_status_id, username, password_hash, first_name, middle_name, last_name, email, phone) VALUES
(1,1,'user_1','$2y$10$hash','Liam','Noah','Smith','liam.smith@mail.com','09170000001'),
(1,1,'user_2','$2y$10$hash','Olivia','Emma','Johnson','olivia.j@mail.com','09170000002'),
(1,1,'user_3','$2y$10$hash','Noah','Oliver','Williams','noah.w@mail.com','09170000003'),
(1,1,'user_4','$2y$10$hash','Emma','Ava','Brown','emma.b@mail.com','09170000004'),
(1,1,'user_5','$2y$10$hash','Oliver','Elijah','Jones','oliver.j@mail.com','09170000005'),
(1,1,'user_6','$2y$10$hash','Ava','Charlotte','Garcia','ava.g@mail.com','09170000006'),
(1,1,'user_7','$2y$10$hash','Elijah','Sophia','Miller','elijah.m@mail.com','09170000007'),
(1,1,'user_8','$2y$10$hash','Charlotte','Amelia','Davis','charlotte.d@mail.com','09170000008'),
(1,1,'user_9','$2y$10$hash','William','James','Rodriguez','william.r@mail.com','09170000009'),
(1,1,'user_10','$2y$10$hash','Sophia','Isabella','Martinez','sophia.m@mail.com','09170000010'),
(1,1,'user_11','$2y$10$hash','James','Benjamin','Hernandez','james.h@mail.com','09170000011'),
(1,1,'user_12','$2y$10$hash','Isabella','Lucas','Lopez','isabella.l@mail.com','09170000012'),
(1,1,'user_13','$2y$10$hash','Benjamin','Henry','Gonzalez','ben.g@mail.com','09170000013'),
(1,1,'user_14','$2y$10$hash','Mia','Mia','Wilson','mia.w@mail.com','09170000014'),
(1,1,'user_15','$2y$10$hash','Lucas','Alexander','Anderson','lucas.a@mail.com','09170000015'),
(1,1,'user_16','$2y$10$hash','Evelyn','Harper','Thomas','evelyn.t@mail.com','09170000016'),
(1,1,'user_17','$2y$10$hash','Alexander','Michael','Taylor','alex.t@mail.com','09170000017'),
(1,1,'user_18','$2y$10$hash','Harper','Evelyn','Moore','harper.m@mail.com','09170000018'),
(1,1,'user_19','$2y$10$hash','Michael','Daniel','Jackson','michael.j@mail.com','09170000019'),
(1,1,'user_20','$2y$10$hash','Camila','Abigail','Martin','camila.m@mail.com','09170000020'),
(1,1,'user_21','$2y$10$hash','Daniel','Henry','Lee','daniel.l@mail.com','09170000021'),
(1,1,'user_22','$2y$10$hash','Gianna','Emily','Perez','gianna.p@mail.com','09170000022'),
(1,1,'user_23','$2y$10$hash','Henry','Jackson','Thompson','henry.t@mail.com','09170000023'),
(1,1,'user_24','$2y$10$hash','Luna','Madison','White','luna.w@mail.com','09170000024'),
(1,1,'user_25','$2y$10$hash','Sebastian','Sebastian','Harris','sebastian.h@mail.com','09170000025'),
(1,1,'user_26','$2y$10$hash','Ella','Aria','Sanchez','ella.s@mail.com','09170000026'),
(1,1,'user_27','$2y$10$hash','Jack','Owen','Clark','jack.clark@mail.com','09170000027'),
(1,1,'user_28','$2y$10$hash','Avery','Chloe','Ramirez','avery.r@mail.com','09170000028'),
(1,1,'user_29','$2y$10$hash','Owen','Theodore','Lewis','owen.l@mail.com','09170000029'),
(1,1,'user_30','$2y$10$hash','Millie','Layla','Robinson','millie.r@mail.com','09170000030'),
(1,1,'user_31','$2y$10$hash','Theodore','Samuel','Walker','theo.w@mail.com','09170000031'),
(1,1,'user_32','$2y$10$hash','Scarlett','Riley','Young','scarlett.y@mail.com','09170000032'),
(1,1,'user_33','$2y$10$hash','Samuel','Joseph','Allen','sam.a@mail.com','09170000033'),
(1,1,'user_34','$2y$10$hash','Eleanor','Zoey','King','eleanor.k@mail.com','09170000034'),
(1,1,'user_35','$2y$10$hash','David','John','Wright','david.w@mail.com','09170000035'),
(1,1,'user_36','$2y$10$hash','Madison','Lily','Scott','madison.s@mail.com','09170000036'),
(1,1,'user_37','$2y$10$hash','Joseph','David','Torres','joseph.t@mail.com','09170000037'),
(1,1,'user_38','$2y$10$hash','Penelope','Hannah','Nguyen','penelope.n@mail.com','09170000038'),
(1,1,'user_39','$2y$10$hash','Carter','Wyatt','Hill','carter.h@mail.com','09170000039'),
(1,1,'user_40','$2y$10$hash','Grace','Lillian','Flores','grace.f@mail.com','09170000040'),
(1,1,'user_41','$2y$10$hash','Wyatt','Matthew','Green','wyatt.g@mail.com','09170000041'),
(1,1,'user_42','$2y$10$hash','Chloe','Addison','Adams','chloe.a@mail.com','09170000042'),
(1,1,'user_43','$2y$10$hash','Matthew','Luke','Nelson','matthew.n@mail.com','09170000043'),
(1,1,'user_44','$2y$10$hash','Camila','Camila','Baker','cam.b@mail.com','09170000044'),
(1,1,'user_45','$2y$10$hash','Luke','Asher','Hall','luke.h@mail.com','09170000045'),
(1,1,'user_46','$2y$10$hash','Aria','Natalie','Rivera','aria.r@mail.com','09170000046'),
(1,1,'user_47','$2y$10$hash','Asher','Carter','Campbell','asher.c@mail.com','09170000047'),
(1,1,'user_48','$2y$10$hash','Audrey','Savannah','Mitchell','audrey.m@mail.com','09170000048'),
(1,1,'user_49','$2y$10$hash','Gabriel','Julian','Carter','gabriel.c@mail.com','09170000049'),
(1,1,'user_50','$2y$10$hash','Bella','Brooklyn','Roberts','bella.r@mail.com','09170000050'),
(2,1,'artist_1','$2y$10$hash','Jayden',NULL,'Gomez','jayden.art@mail.com','09170000051'),
(2,1,'artist_2','$2y$10$hash','Skylar',NULL,'Phillips','skylar.art@mail.com','09170000052'),
(2,1,'artist_3','$2y$10$hash','Dylan',NULL,'Evans','dylan.art@mail.com','09170000053'),
(2,1,'artist_4','$2y$10$hash','Genesis',NULL,'Turner','genesis.art@mail.com','09170000054'),
(2,1,'artist_5','$2y$10$hash','Grayson',NULL,'Diaz','grayson.art@mail.com','09170000055'),
(2,1,'artist_6','$2y$10$hash','Zoe',NULL,'Cruz','zoe.art@mail.com','09170000056'),
(2,1,'artist_7','$2y$10$hash','Isaac',NULL,'Parker','isaac.art@mail.com','09170000057'),
(2,1,'artist_8','$2y$10$hash','Stella',NULL,'Mendoza','stella.art@mail.com','09170000058'),
(2,1,'artist_9','$2y$10$hash','Anthony',NULL,'Edwards','anthony.art@mail.com','09170000059'),
(2,1,'artist_10','$2y$10$hash','Maya',NULL,'Ortiz','maya.art@mail.com','09170000060'),
(2,1,'artist_11','$2y$10$hash','Gracie',NULL,'Morris','gracie.art@mail.com','09170000061'),
(2,1,'artist_12','$2y$10$hash','Lincoln',NULL,'Rodriguez','lincoln.art@mail.com','09170000062'),
(2,1,'artist_13','$2y$10$hash','Nova',NULL,'Snyder','nova.art@mail.com','09170000063'),
(2,1,'artist_14','$2y$10$hash','Hudson',NULL,'Kennedy','hudson.art@mail.com','09170000064'),
(2,1,'artist_15','$2y$10$hash','Paisley',NULL,'Warren','paisley.art@mail.com','09170000065'),
(2,1,'artist_16','$2y$10$hash','Samuel',NULL,'Dixon','samuel.art@mail.com','09170000066'),
(2,1,'artist_17','$2y$10$hash','Savannah',NULL,'Rios','savannah.art@mail.com','09170000067'),
(2,1,'artist_18','$2y$10$hash','Nolan',NULL,'Rogers','nolan.art@mail.com','09170000068'),
(2,1,'artist_19','$2y$10$hash','Elena',NULL,'Elliott','elena.art@mail.com','09170000069'),
(2,1,'artist_20','$2y$10$hash','Wesley',NULL,'Kim','wesley.art@mail.com','09170000070'),
(2,1,'artist_21','$2y$10$hash','Naomi',NULL,'Payne','naomi.art@mail.com','09170000071'),
(2,1,'artist_22','$2y$10$hash','Ian',NULL,'Foster','ian.art@mail.com','09170000072'),
(2,1,'artist_23','$2y$10$hash','Clara',NULL,'Sanders','clara.art@mail.com','09170000073'),
(2,1,'artist_24','$2y$10$hash','Christian',NULL,'Ross','christian.art@mail.com','09170000074'),
(2,1,'artist_25','$2y$10$hash','Aurora',NULL,'Bryant','aurora.art@mail.com','09170000075'),
(2,1,'artist_26','$2y$10$hash','Ryan',NULL,'Powell','ryan.art@mail.com','09170000076'),
(2,1,'artist_27','$2y$10$hash','Ariana',NULL,'Long','ariana.art@mail.com','09170000077'),
(2,1,'artist_28','$2y$10$hash','Leonardo',NULL,'Perry','leo.art@mail.com','09170000078'),
(2,1,'artist_29','$2y$10$hash','Madelyn',NULL,'Butler','madelyn.art@mail.com','09170000079'),
(2,1,'artist_30','$2y$10$hash','Waylon',NULL,'Barnes','waylon.art@mail.com','09170000080'),
(2,1,'artist_31','$2y$10$hash','Alice',NULL,'Fisher','alice.art@mail.com','09170000081'),
(2,1,'artist_32','$2y$10$hash','Marcus',NULL,'Henderson','marcus.art@mail.com','09170000082'),
(2,1,'artist_33','$2y$10$hash','Aubrey',NULL,'Coleman','aubrey.art@mail.com','09170000083'),
(2,1,'artist_34','$2y$10$hash','Miles',NULL,'Simmons','miles.art@mail.com','09170000084'),
(2,1,'artist_35','$2y$10$hash','Ivy',NULL,'Patterson','ivy.art@mail.com','09170000085'),
(2,1,'artist_36','$2y$10$hash','Liam',NULL,'Hughes','liamh.art@mail.com','09170000086'),
(2,1,'artist_37','$2y$10$hash','Piper',NULL,'Washington','piper.art@mail.com','09170000087'),
(2,1,'artist_38','$2y$10$hash','Colton',NULL,'Menezes','colton.art@mail.com','09170000088'),
(2,1,'artist_39','$2y$10$hash','Sadie',NULL,'Greenaway','sadie.art@mail.com','09170000089'),
(2,1,'artist_40','$2y$10$hash','Ezekiel',NULL,'Melton','ezekiel.art@mail.com','09170000090'),
(2,1,'artist_41','$2y$10$hash','Lydia',NULL,'Glover','lydia.art@mail.com','09170000091'),
(2,1,'artist_42','$2y$10$hash','John',NULL,'Blankenship','johnb.art@mail.com','09170000092'),
(2,1,'artist_43','$2y$10$hash','Sara',NULL,'Gentry','sara.art@mail.com','09170000093'),
(2,1,'artist_44','$2y$10$hash','Jeremiah',NULL,'Holloway','jeremiah.art@mail.com','09170000094'),
(2,1,'artist_45','$2y$10$hash','Julia',NULL,'Lutz','julia.art@mail.com','09170000095'),
(2,1,'artist_46','$2y$10$hash','Emerson',NULL,'Clay','emerson.art@mail.com','09170000096'),
(2,1,'artist_47','$2y$10$hash','Quinn',NULL,'Kemp','quinn.art@mail.com','09170000097'),
(2,1,'artist_48','$2y$10$hash','Josiah',NULL,'Finley','josiah.art@mail.com','09170000098'),
(2,1,'artist_49','$2y$10$hash','Reeve',NULL,'Rhodes','reeve.art@mail.com','09170000099'),
(2,1,'artist_50','$2y$10$hash','Zuri',NULL,'Browning','zuri.art@mail.com','09170000100'),
(3,1,'admin_1','$2y$10$hash','Arthur',NULL,'Pendragon','arthur.admin@mail.com','09170000101'),
(3,1,'admin_2','$2y$10$hash','Merlin',NULL,'Ambrosius','merlin.admin@mail.com','09170000102'),
(3,1,'admin_3','$2y$10$hash','Guinevere',NULL,'DeGalle','guin.admin@mail.com','09170000103'),
(3,1,'admin_4','$2y$10$hash','Lancelot',NULL,'DuLac','lance.admin@mail.com','09170000104'),
(3,1,'admin_5','$2y$10$hash','Gawain',NULL,'Orkney','gawain.admin@mail.com','09170000105');

-- User Profile Table
INSERT INTO user_tbl (account_id, card_number) VALUES
(1,'4111111111111111'),(2,'4222222222222222'),(3,'4333333333333333'),(4,'4444444444444444'),(5,'4555555555555555'),
(6,'4666666666666666'),(7,'4777777777777777'),(8,'4888888888888888'),(9,'4999999999999999'),(10,'5111111111111111'),
(11,'5222222222222222'),(12,'5333333333333333'),(13,'5444444444444444'),(14,'5555555555555555'),(15,'5666666666666666'),
(16,'5777777777777777'),(17,'5888888888888888'),(18,'5999999999999999'),(19,'6111111111111111'),(20,'6222222222222222'),
(21,NULL),(22,NULL),(23,NULL),(24,NULL),(25,NULL),(26,NULL),(27,NULL),(28,NULL),(29,NULL),(30,NULL),
(31,'3111111111111111'),(32,'3222222222222222'),(33,'3333333333333333'),(34,'3444444444444444'),(35,'3555555555555555'),
(36,'3666666666666666'),(37,'3777777777777777'),(38,'3888888888888888'),(39,'3999999999999999'),(40,'7111111111111111'),
(41,'7222222222222222'),(42,'7333333333333333'),(43,'7444444444444444'),(44,'7555555555555555'),(45,'7666666666666666'),
(46,'7777777777777777'),(47,'7888888888888888'),(48,'7999999999999999'),(49,'8111111111111111'),(50,'8222222222222222');

-- Artist Profile Table
INSERT INTO artist_tbl (account_id, starting_rate, is_available) VALUES
(51,500.00,TRUE),(52,550.00,TRUE),(53,600.00,TRUE),(54,650.00,TRUE),(55,700.00,TRUE),
(56,750.00,TRUE),(57,800.00,TRUE),(58,850.00,TRUE),(59,900.00,TRUE),(60,950.00,TRUE),
(61,1000.00,TRUE),(62,1050.00,TRUE),(63,1100.00,TRUE),(64,1150.00,TRUE),(65,1200.00,TRUE),
(66,1250.00,TRUE),(67,1300.00,TRUE),(68,1350.00,TRUE),(69,1400.00,TRUE),(70,1450.00,TRUE),
(71,1500.00,TRUE),(72,1550.00,TRUE),(73,1600.00,TRUE),(74,1650.00,TRUE),(75,1700.00,TRUE),
(76,1750.00,TRUE),(77,1800.00,TRUE),(78,1850.00,TRUE),(79,1900.00,TRUE),(80,1950.00,TRUE),
(81,2000.00,TRUE),(82,2050.00,TRUE),(83,2100.00,TRUE),(84,2150.00,TRUE),(85,2200.00,TRUE),
(86,2250.00,TRUE),(87,2300.00,TRUE),(88,2350.00,TRUE),(89,2400.00,TRUE),(90,2450.00,TRUE),
(91,2500.00,TRUE),(92,2550.00,TRUE),(93,2600.00,TRUE),(94,2650.00,TRUE),(95,2700.00,TRUE),
(96,2750.00,TRUE),(97,2800.00,TRUE),(98,2850.00,TRUE),(99,2900.00,TRUE),(100,3000.00,TRUE);

-- Administrator Table
INSERT INTO administrator_tbl (account_id) VALUES
(101),(102),(103),(104),(105);

-- Hired Artist Table
INSERT INTO hired_artist_tbl (artist_id, admin_id, hire_date, status_id) VALUES
(1,1,'2024-01-01',1),(2,1,'2024-01-02',1),(3,1,'2024-01-03',1),(4,1,'2024-01-04',1),(5,1,'2024-01-05',1),
(6,1,'2024-01-06',1),(7,1,'2024-01-07',1),(8,1,'2024-01-08',1),(9,1,'2024-01-09',1),(10,1,'2024-01-10',1),
(11,1,'2024-01-11',1),(12,1,'2024-01-12',1),(13,1,'2024-01-13',1),(14,1,'2024-01-14',1),(15,1,'2024-01-15',1),
(16,1,'2024-01-16',1),(17,1,'2024-01-17',1),(18,1,'2024-01-18',1),(19,1,'2024-01-19',1),(20,1,'2024-01-20',1),
(21,1,'2024-01-21',1),(22,1,'2024-01-22',1),(23,1,'2024-01-23',1),(24,1,'2024-01-24',1),(25,1,'2024-01-25',1),
(26,1,'2024-01-26',1),(27,1,'2024-01-27',1),(28,1,'2024-01-28',1),(29,1,'2024-01-29',1),(30,1,'2024-01-30',1),
(31,1,'2024-02-01',1),(32,1,'2024-02-02',1),(33,1,'2024-02-03',1),(34,1,'2024-02-04',1),(35,1,'2024-02-05',1),
(36,1,'2024-02-06',1),(37,1,'2024-02-07',1),(38,1,'2024-02-08',1),(39,1,'2024-02-09',1),(40,1,'2024-02-10',1),
(41,1,'2024-02-11',1),(42,1,'2024-02-12',1),(43,1,'2024-02-13',1),(44,1,'2024-02-14',1),(45,1,'2024-02-15',1),
(46,1,'2024-02-16',1),(47,1,'2024-02-17',1),(48,1,'2024-02-18',1),(49,1,'2024-02-19',1),(50,1,'2024-02-20',1),
(51,1,'2024-02-21',1),(52,1,'2024-02-22',1),(53,1,'2024-02-23',1),(54,1,'2024-02-24',1),(55,1,'2024-02-25',1),
(56,1,'2024-02-26',1),(57,1,'2024-02-27',1),(58,1,'2024-02-28',1),(59,1,'2024-03-01',1),(60,1,'2024-03-02',1),
(61,1,'2024-03-03',1),(62,1,'2024-03-04',1),(63,1,'2024-03-05',1),(64,1,'2024-03-06',1),(65,1,'2024-03-07',1),
(66,1,'2024-03-08',1),(67,1,'2024-03-09',1),(68,1,'2024-03-10',1),(69,1,'2024-03-11',1),(70,1,'2024-03-12',1),
(71,1,'2024-03-13',1),(72,1,'2024-03-14',1),(73,1,'2024-03-15',1),(74,1,'2024-03-16',1),(75,1,'2024-03-17',1),
(76,1,'2024-03-18',1),(77,1,'2024-03-19',1),(78,1,'2024-03-20',1),(79,1,'2024-03-21',1),(80,1,'2024-03-22',1),
(81,1,'2024-03-23',1),(82,1,'2024-03-24',1),(83,1,'2024-03-25',1),(84,1,'2024-03-26',1),(85,1,'2024-03-27',1),
(86,1,'2024-03-28',1),(87,1,'2024-03-29',1),(88,1,'2024-03-30',1),(89,1,'2024-03-31',1),(90,1,'2024-04-01',1),
(91,1,'2024-04-02',1),(92,1,'2024-04-03',1),(93,1,'2024-04-04',1),(94,1,'2024-04-05',1),(95,1,'2024-04-06',1),
(96,1,'2024-04-07',1),(97,1,'2024-04-08',1),(98,1,'2024-04-09',1),(99,1,'2024-04-10',1),(100,1,'2024-04-11',1);

-- Commissions Table
INSERT INTO commission_tbl (user_id, artist_id, category_id, description, status_id, price) VALUES
(1,1,1,'Anime illustration',2,500.00),(2,2,2,'Chibi icon profile',2,350.00),(3,3,3,'Fantasy novel cover art',5,1500.00),(4,4,4,'VTuber structural model 2D',6,2500.00),(5,5,5,'Landscape concept painting',2,800.00),
(6,6,1,'Twitch sub badges graphic',2,300.00),(7,7,2,'Mecha design high detail',5,1800.00),(8,8,3,'Game background vector',6,900.00),(9,9,4,'Comic book page ink',2,1200.00),(10,10,5,'Pixel art sprite sheets',2,400.00),
(11,11,1,'Vector minimalist design logo',2,250.00),(12,12,2,'D&D character full body art',5,1100.00),(13,13,3,'Cyberpunk themed wallpaper',6,1300.00),(14,14,4,'Fursona dynamic pose ref',2,700.00),(15,15,5,'Children book illustration page',2,600.00),
(16,16,1,'Dark fantasy armor setup design',2,1600.00),(17,17,2,'Pop art style custom print',5,450.00),(18,18,3,'Lo-fi aesthetic animation loop',6,2000.00),(19,19,4,'Steam profile header graphic',2,200.00),(20,20,5,'Tattoo pattern floral sleeve',2,850.00),
(21,21,1,'Retro synthwave album poster',2,650.00),(22,22,2,'Custom emoji set Discord pack',5,350.00),(23,23,3,'Manga style spread backdrop',6,1400.00),(24,24,4,'Water color family portrait print',2,1050.00),(25,25,5,'Caricature funny drawing group',2,500.00),
(26,26,1,'Gothic portrait canvas mock',2,950.00),(27,27,2,'Chibi matching couple icons',5,400.00),(28,28,3,'Sci-fi starship model sheet',6,1750.00),(29,29,4,'Oil painting landscape simulation',2,1250.00),(30,30,5,'Line art structural blueprint aesthetic',2,550.00),
(31,31,1,'Anime style thumbnail splash',2,600.00),(32,32,2,'Cute animal sticker designs pack',5,300.00),(33,33,3,'High fantasy spell card framework',6,1150.00),(34,34,4,'Live2D rigging prep model design',2,3000.00),(35,35,5,'Environment concept matte painting',2,1400.00),
(36,36,1,'Esports team mascot visual icon',2,750.00),(37,37,2,'Steampunk character outfit ref sheet',5,1350.00),(38,38,3,'Isometric room vector model illustration',6,950.00),(39,39,4,'Webtoon panel storyboard draft',2,800.00),(40,40,5,'Game item inventory UI icons asset',2,600.00),
(41,41,1,'Minimal line art home decor design',2,300.00),(42,42,2,'RPG character custom token frame art',5,1000.00),(43,43,3,'Neon cyberpunk street view overlay',6,1650.00),(44,44,4,'Mythological creature beast painting',2,1900.00),(45,45,5,'Cute food items vector pattern design',2,450.00),
(46,46,1,'Surrealist psychological painting print',2,1450.00),(47,47,2,'Corporate presentation custom flat vector',5,500.00),(48,48,3,'Animated pixel intro screen setup',6,2200.00),(49,49,4,'YouTube stream overlay graphics package',2,850.00),(50,50,5,'Traditional style dynamic sketch portrait',2,400.00),
(1,51,1,'Vibrant graffiti lettering sketch canvas',2,550.00),(2,52,2,'Kawaii style magical girl illustration',5,700.00),(3,53,3,'Epic boss monster conceptual design',6,2100.00),(4,54,4,'Cute twitch emotes package of 6 items',2,450.00),(5,55,5,'Historical knight battle armor render',2,1600.00),
(6,56,1,'Calm starry night sky scenic background',2,850.00),(7,57,2,'Futuristic cyberpunk motorcycle sheet',5,1250.00),(8,58,3,'Chibi style fantasy party group photo',6,1800.00),(9,59,4,'Abstract geometric art layout design',2,600.00),(10,60,5,'Dark watercolor gothic mansion visual',2,1100.00);
-- ==========================================
-- 8. COMMISSION REQUEST TABLE (100 Rows)
-- Connects to the created commission IDs (1 to 100)
-- ==========================================
INSERT INTO commission_request_tbl (commission_id, artist_id, message, status_id) VALUES
(1,1,'I can deliver this within 3 days easily!',2),(2,2,'I love drawing cute stuff, hope we work together.',2),(3,3,'High fantasy settings are exactly my style.',3),(4,4,'Expert in 2D models, checking in.',2),(5,5,'Can handle landscape challenges nicely.',2),
(6,6,'Can provide variations on the twitch icons.',2),(7,7,'Experienced in industrial mecha designs.',2),(8,8,'Vector files will be fully clean layered.',2),(9,9,'Will deliver raw high-res dynamic ink layout.',2),(10,10,'Love working on pixel sheets!',2),
(11,11,'Can make it super modern minimal style.',2),(12,12,'Have custom template assets for D&D races.',2),(13,13,'Cyberpunk theme expert ready here.',2),(14,14,'Will create accurate ref sheet panels.',2),(15,15,'Whimsical kid story style is my primary focus.',2),(16,16,'Armor sets design is my daily jam.',2),(17,17,'Pop art layouts can match photo matches.',2),(18,18,'Lo-fi loop sample ready on short notice.',2),(19,19,'Perfect sizing for steam layouts verified.',2),(20,20,'Clean lines for direct stencil work.',2),
(21,21,'Can incorporate cool neon effects palette.',2),(22,22,'Fast emote revisions available if needed.',2),(23,23,'Manga inks look authentic via my brushes.',2),(24,24,'Traditional watercolor mimic preset look.',2),(25,25,'Fun exaggerations ready for the caricature.',2),(26,26,'Moody tones will suit this perfectly.',2),(27,27,'Matching icons will be perfectly cohesive.',2),(28,28,'Sci-fi structural sheets look hyper technical.',2),(29,29,'Vibrant texture brushstrokes ready.',2),(30,30,'Clean blueprints alignment mapping guaranteed.',2),
(31,31,'Clickable high energy thumbnail dynamic style.',2),(32,32,'Die-cut safe printing file layouts preset.',2),(33,33,'Custom text boxes ready for card gaming.',2),(34,34,'Cutting layout files prepared for live2D cut.',2),(35,35,'Can do matte painting style scale renders.',2),(36,36,'Vector based sharp scalable mascot branding.',2),(37,37,'Gear motifs detailing will look amazing.',2),(38,38,'Isometric alignment grid setup is clean.',2),(39,39,'Pacing storyboard flow optimized well.',2),(40,40,'Clean gaming asset icons separate sheets.',2),
(41,41,'Delicate thin lines work standard approach.',2),(42,42,'Print ready tokens dimensions template checked.',2),(43,43,'Glowing signboards look authentic in dark context.',2),(44,44,'Anatomical mythical creature mashup specialist.',2),(45,45,'Perfect seamless looping grid export package.',2),(46,46,'Deep psychological subtexts painting concept.',2),(47,47,'Clean svg vectors flat color palette layout.',2),(48,48,'Frame by frame neat pixel loops creation setup.',2),(49,49,'Full set template streams layout ready.',2),(50,50,'Raw expressive sketch style presentation layout.',2),
(51,51,'Vibrant wall design patterns look.',2),(52,52,'Bright sparkling magical girl style design.',2),(53,53,'Intimidating layout scale monster sheet.',2),(54,54,'Custom text elements pack options.',2),(55,55,'Historical armor accurate plating reference.',2),(56,56,'Soft ambient gradient sky tones ready.',2),(57,57,'Detailed engine parts look highly industrial.',2),(58,58,'Everyone will have unique clear faces layout.',2),(59,59,'Geometric abstract balance design style.',2),(60,60,'Spooky vintage oil layout render aesthetic.',2),
(61,61,'Elegant botanical minimal vector format style.',2),(62,62,'Will paint customized armor details.',2),(63,63,'Cool retro neon color balance design mapping.',2),(64,64,'Dynamic dynamic lighting breath effects design.',2),(65,65,'Cheerful colors perfect for children appeal.',2),(66,66,'Shining metallic textures render beautifully.',2),(67,67,'Fits perfectly for current platform standards.',2),(68,68,'Classic game retro style sprite setup layout.',2),(69,69,'Elegant decorative border ornaments pack.',2),(70,70,'Clean layout lines optimized for tattoo ink.',2),
(71,71,'Grain filter look 90s style layout authentic.',2),(72,72,'Isometric cute stall setup map view.',2),(73,73,'Grungy ruinous landscape lighting concept.',2),(74,74,'Rich impasto digital paint brush layers.',2),(75,75,'Hilarious expressions mapping grid option.',2),(76,76,'Mystical glowing accents familiar designs.',2),(77,77,'Warm community vibe group sketch design.',2),(78,78,'Complex cockpit command board map view details.',2),(79,79,'Cozy lighting golden hour brush style.',2),(80,80,'Accurate scaling cut-outs isometric look.',2),
(81,81,'Impact lines explosive panel actions layouts.',2),(82,82,'Turnaround blueprints modeling safe formats.',2),(83,83,'Beautiful cloudscapes backgrounds painting focus.',2),(84,84,'Layers cleanly sliced named for immediate use.',2),(85,85,'Cinematic scale atmospheric haze rendering.',2),(86,86,'Bold vector graphics look old school fun.',2),(87,87,'Steampunk pipe grids layout aesthetic look.',2),(88,88,'Warm lighting effects cozy background setting.',2),(89,89,'Eye catching cover splash page elements pack.',2),(90,90,'Polished fantasy elements assets package UI.',2),
(91,91,'Intricate moon patterns delicate lines focus.',2),(92,92,'Standard cards measurements safe bleed templates.',2),(93,93,'Rainy reflections puddles dynamic lighting setup.',2),(94,94,'Dynamic wing feather painting details clean.',2),(95,95,'High resolution transparent backing patterns.',2),(96,96,'Trippy optical illusion landscape painting look.',2),(97,97,'Clean presentation data icons flat layout.',2),(98,98,'Cozy pixel fireplace embers loop layout asset.',2),(99,99,'Clean camera bounding frame overlay panels.',2),(100,100,'Charming handwritten style chalkboard vector fonts.',2);

-- ==========================================
-- 9. TRANSACTION TABLE (100 Rows)
-- Links directly to commissions (1 to 100)
-- ==========================================
INSERT INTO transaction_tbl (commission_id, total_amount, status_id) VALUES
(1,500.00,6),(2,350.00,6),(3,1500.00,6),(4,2500.00,6),(5,800.00,6),(6,300.00,6),(7,1800.00,6),(8,900.00,6),(9,1200.00,6),(10,400.00,6),
(11,250.00,6),(12,1100.00,6),(13,1300.00,6),(14,700.00,6),(15,600.00,6),(16,1600.00,6),(17,450.00,6),(18,2000.00,6),(19,200.00,6),(20,850.00,6),
(21,650.00,6),(22,350.00,6),(23,1400.00,6),(24,1050.00,6),(25,500.00,6),(26,950.00,6),(27,400.00,6),(28,1750.00,6),(29,1250.00,6),(30,550.00,6),
(31,600.00,6),(32,300.00,6),(33,1150.00,6),(34,3000.00,6),(35,1400.00,6),(36,750.00,6),(37,1350.00,6),(38,950.00,6),(39,800.00,6),(40,600.00,6),
(41,300.00,6),(42,1000.00,6),(43,1650.00,6),(44,1900.00,6),(45,450.00,6),(46,1450.00,6),(47,500.00,6),(48,2200.00,6),(49,850.00,6),(50,400.00,6),
(51,550.00,6),(52,700.00,6),(53,2100.00,6),(54,450.00,6),(55,1600.00,6),(56,850.00,6),(57,1250.00,6),(58,1800.00,6),(59,600.00,6),(60,1100.00,6),
(61,250.00,6),(62,1300.00,6),(63,900.00,6),(64,2400.00,6),(65,750.00,6),(66,800.00,6),(67,500.00,6),(68,1350.00,6),(69,300.00,6),(70,400.00,6),
(71,850.00,6),(72,650.00,6),(73,1700.00,6),(74,1500.00,6),(75,550.00,6),(76,650.00,6),(77,1200.00,6),(78,2000.00,6),(79,1150.00,6),(80,1850.00,6),
(81,1400.00,6),(82,500.00,6),(83,1600.00,6),(84,2200.00,6),(85,1750.00,6),(86,600.00,6),(87,1450.00,6),(88,1250.00,6),(89,900.00,6),(90,1100.00,6),
(91,350.00,6),(92,850.00,6),(93,1500.00,6),(94,1300.00,6),(95,500.00,6),(96,1650.00,6),(97,700.00,6),(98,1400.00,6),(99,1200.00,6),(100,450.00,6);

-- ==========================================
-- 10. PAYMENT TABLE (100 Rows)
-- Connects sequentially to transactions (1 to 100)
-- ==========================================
INSERT INTO payment_tbl (transaction_id, payment_method_id, amount, status_id) VALUES
(1,1,500.00,10),(2,1,350.00,10),(3,2,1500.00,10),(4,2,2500.00,10),(5,1,800.00,10),(6,1,300.00,10),(7,2,1800.00,10),(8,2,900.00,10),(9,1,1200.00,10),(10,1,400.00,10),
(11,1,250.00,10),(12,2,1100.00,10),(13,1,1300.00,10),(14,1,700.00,10),(15,2,600.00,10),(16,2,1600.00,10),(17,1,450.00,10),(18,1,2000.00,10),(19,2,200.00,10),(20,2,850.00,10),
(21,1,650.00,10),(22,1,350.00,10),(23,2,1400.00,10),(24,2,1050.00,10),(25,1,500.00,10),(26,1,950.00,10),(27,2,400.00,10),(28,2,1750.00,10),(29,1,1250.00,10),(30,1,550.00,10),
(31,1,600.00,10),(32,2,300.00,10),(33,1,1150.00,10),(34,1,3000.00,10),(35,2,1400.00,10),(36,2,750.00,10),(37,1,1350.00,10),(38,1,950.00,10),(39,2,800.00,10),(40,2,600.00,10),
(41,1,300.00,10),(42,1,1000.00,10),(43,2,1650.00,10),(44,2,1900.00,10),(45,1,450.00,10),(46,1,1450.00,10),(47,2,500.00,10),(48,2,2200.00,10),(49,1,850.00,10),(50,1,400.00,10),
(51,1,550.00,10),(52,1,700.00,10),(53,2,2100.00,10),(54,2,450.00,10),(55,1,1600.00,10),(56,1,850.00,10),(57,2,1250.00,10),(58,2,1800.00,10),(59,1,600.00,10),(60,1,1100.00,10),
(61,1,250.00,10),(62,2,1300.00,10),(63,1,900.00,10),(64,1,2400.00,10),(65,2,750.00,10),(66,2,800.00,10),(67,1,500.00,10),(68,1,1350.00,10),(69,2,300.00,10),(70,2,400.00,10),
(71,1,850.00,10),(72,1,650.00,10),(73,2,1700.00,10),(74,2,1500.00,10),(75,1,550.00,10),(76,1,650.00,10),(77,2,1200.00,10),(78,2,2000.00,10),(79,1,1150.00,10),(80,1,1850.00,10),
(81,1,1400.00,10),(82,2,500.00,10),(83,1,1600.00,10),(84,1,2200.00,10),(85,2,1750.00,10),(86,2,750.00,10),(87,1,1450.00,10),(88,1,1250.00,10),(89,2,900.00,10),(90,2,1100.00,10),
(91,1,350.00,10),(92,1,850.00,10),(93,2,1500.00,10),(94,2,1300.00,10),(95,1,500.00,10),(96,1,1650.00,10),(97,2,700.00,10),(98,2,1400.00,10),(99,1,1200.00,10),(100,1,450.00,10);

-- ==========================================
-- 11. FAVORITE TABLE (100 Rows)
-- Links varying user IDs to distinct artist IDs 
-- ==========================================
INSERT INTO favorite_tbl (user_id, artist_id) VALUES
(1,1),(2,2),(3,3),(4,4),(5,5),(6,6),(7,7),(8,8),(9,9),(10,10),
(11,11),(12,12),(13,13),(14,14),(15,15),(16,16),(17,17),(18,18),(19,19),(20,20),
(21,21),(22,22),(23,23),(24,24),(25,25),(26,26),(27,27),(28,28),(29,29),(30,30),
(31,31),(32,32),(33,33),(34,34),(35,35),(36,36),(37,37),(38,38),(39,39),(40,40),
(41,41),(42,42),(43,43),(44,44),(45,45),(46,46),(47,47),(48,48),(49,49),(50,50),
(1,51),(2,52),(3,53),(4,54),(5,55),(6,56),(7,57),(8,58),(9,59),(10,60),
(11,61),(12,62),(13,63),(14,64),(15,65),(16,66),(17,67),(18,68),(19,69),(20,70),
(21,71),(22,72),(23,73),(24,74),(25,75),(26,76),(27,77),(28,78),(29,79),(30,80),
(31,81),(32,82),(33,83),(34,84),(35,85),(36,86),(37,87),(38,88),(39,89),(40,90),
(41,91),(42,92),(43,93),(44,94),(45,95),(46,96),(47,97),(48,98),(49,99),(50,100);

-- ==========================================
-- 12. CONVERSATION TABLE (100 Rows)
-- ==========================================
INSERT INTO conversation_tbl VALUES
(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),
(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),
(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),
(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),
(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),
(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),
(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),
(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),
(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),
(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW()),(NULL,NOW());

-- ==========================================
-- 13. MESSAGE TABLE (100 Rows)
-- Dynamic dialogue flow between account pairs across active conversations (1 to 100)
-- ==========================================
INSERT INTO message_tbl (sender_account_id, receiver_account_id, message_content, status_id, conversation_id) VALUES
(1,51,'Hey! Are you open for commissions?',8,1),(51,1,'Yes I am! What are you looking to get?',8,1),
(2,52,'Hello, can you design a character for me?',8,2),(52,2,'Sure thing! Tell me about your ideas.',8,2),
(3,53,'Hi there, what are your current rates?',8,3),(53,3,'My prices start at $600 for full renders.',8,3),
(4,54,'Can you deliver by next Friday afternoon?',8,4),(54,4,'Yes, that fits my calendar window perfectly.',8,4),
(5,55,'Sent over the visual references over email.',8,5),(55,5,'Awesome, reviewing them right now!',8,5),
(6,56,'Are you comfortable sketching animal companions?',8,6),(56,6,'Yes, love working on fantasy pets.',8,6),
(7,57,'I need a high-res layout version for prints.',8,7),(57,7,'No problem, I always deliver 300 DPI files.',8,7),
(8,58,'Can we do a dynamic group composition pose?',8,8),(58,8,'Sure, that sounds super fun to plan out!',8,8),
(9,59,'Do you accept custom commercial deals?',8,9),(59,9,'Yes, commercial rights add an extra fee.',8,9),
(10,60,'Looking for clean pixel sprite loops.',8,10),(60,10,'Got it, how many frames do you expect?',8,10),
(11,61,'Is minimalist layout design within your lane?',8,11),(61,11,'Absolutely, less is more for my aesthetic.',8,11),
(12,62,'Hey, looking to update my old character reference.',8,12),(62,12,'Send the old version over, let\'s upgrade it.',8,12),
(13,63,'Do you offer complex mechanical options?',8,13),(63,13,'Yes, mecha and cyber styles are open.',8,13),
(14,64,'Can you write text in custom hand fonts?',8,14),(64,14,'Yes, all done manually with my design tablet.',8,14),
(15,65,'Hey there! Love your kids book styles.',8,15),(65,15,'Thank you so much! Let\'s build a concept page.',8,15),
(16,66,'Can we structure an update plan timeline?',8,16),(66,16,'I will send sketches every three days.',8,16),
(17,67,'Interested in your retro pop setups.',8,17),(67,17,'Great, send a snapshot photo to transform!',8,17),
(18,68,'What format do animations ship out in?',8,18),(68,18,'I package them as high-quality GIF or MP4.',8,18),
(19,69,'Can you create clean graphics overlay setups?',8,19),(69,19,'Yes, measured to standard streaming proportions.',8,19),
(20,70,'Is your line art adaptable for real tattooing?',8,20),(70,70,'Yes, very clean contours for easy stencil transfers.',8,20),
(21,71,'Hey! Open for retro aesthetic anime requests?',8,21),(71,21,'Yes, love that 90s vintage shading style!',8,21),
(22,72,'Looking to get 4 separate emotes items.',8,22),(72,22,'Cool, tell me what expressions you want.',8,22),
(23,73,'Do you paint apocalyptic dark settings?',8,23),(73,23,'Yes, moody atmospheric environments are my main craft.',8,23),
(24,74,'Is your portrait brushwork realistic?',8,24),(74,24,'Yes, digital oil mimic look with high details.',8,24),
(25,75,'Want a silly gift sketch for a classmate.',8,25),(75,25,'Caricatures are super fun, hit me with details!',8,25),
(26,76,'Can you draw a gothic theme design?',8,26),(76,26,'Absolutely, dark palettes work very nicely.',8,26),
(27,77,'Hey, streaming squad portrait project possible?',8,27),(77,27,'Yes, multi character layouts are open right now.',8,27),
(28,78,'Need a detailed spaceship control center layout.',8,28),(78,28,'Sounds cool, I can design a complex UI bridge layout.',8,28),
(29,79,'Do you work with rich autumn colors palettes?',8,29),(79,29,'My favorite seasonal spectrum to paint with!',8,29),
(30,80,'Can you format structural layout views clearly?',8,30),(80,30,'Yes, architectural drafting looks are clean.',8,30),
(31,81,'Hey, dynamic action manga cover setup wanted.',8,31),(81,31,'Awesome, let\'s create an explosive action frame!',8,31),
(32,82,'Need flat layouts for toy production samples.',8,32),(82,32,'I will output precise turnarounds vectors for you.',8,32),
(33,83,'Can you build a fantasy overland map concept?',8,33),(83,33,'Yes, perfect project for my stylized terrain brushes.',8,33),
(34,84,'Looking for dynamic virtual idol wardrobe options.',8,34),(84,34,'Can construct modular layered source assets easily.',8,34),
(35,85,'Do you draw cinematic desert landscape styles?',8,35),(85,35,'Yes, heavy atmosphere and depth focus included.',8,35),
(36,86,'Need an old arcade text graphic.',8,36),(86,36,'Can treat typography with vintage neon glow.',8,36),
(37,87,'Are airship fantasy designs open?',8,37),(87,37,'Yes, steampunk aesthetics are fully available.',8,37),
(38,88,'Looking for a cozy interior design layout.',8,38),(88,38,'Isometric room designs are perfect for that vibe.',8,38),
(39,89,'Do you draft comic panel story flow?',8,39),(89,39,'Yes, storyboard sketching layout is step one.',8,39),
(40,90,'Need borders frames for fantasy inventories.',8,40),(90,40,'Will format them symmetrically as distinct pieces.',8,40),
(41,91,'Hey, small minimalist moon sketch wanted.',8,41),(91,41,'Clean and simple line work ready to roll.',8,41),
(42,92,'Need card patterns for card games.',8,42),(92,42,'Will design matching templates with safe print margins.',8,42),
(43,93,'Can you paint neon glowing alleyways?',8,43),(93,43,'Cyberpunk neon lighting styles are my main thing.',8,43),
(44,94,'Looking for a griffin character setup portrait.',8,44),(94,44,'Creatures and beast details look epic in my style.',8,44),
(45,95,'Need seamless tiles patterns backgrounds.',8,45),(95,45,'Will structure them to repeat smoothly without seams.',8,45),
(46,96,'Do you paint dreamlike surrealist shapes?',8,46),(96,46,'Abstract layouts are perfect for creative exploration.',8,46),
(47,97,'Need clean flat data infographic blocks.',8,47),(97,47,'Will output clean vector boxes easy to read.',8,47),
(48,98,'Pixel campfire loop for stream holding screens?',8,48),(48,98,'Can make it super relaxing with ambient lighting.',8,48),
(49,99,'Do you bundle streaming frames layouts packs?',8,49),(99,49,'Yes, webcam frame, chat area, and alerts included.',8,49),
(50,100,'Need hand-drawn chalkboard style look.',8,50),(100,50,'Will mimic texture chalk effects cleanly on dark boards.',8,50);

-- ==========================================
-- 14. PORTFOLIO TABLE (100 Rows)
-- Covers variations for the 100 artist IDs
-- ==========================================
INSERT INTO portfolio_tbl (artist_id, title, description) VALUES
(1,'Cyber Dreams','Neon glow urban illustrations'),(2,'Cute Critters','Chibi and kawaii wildlife art packs'),(3,'Mythic Worlds','High fantasy environmental concepts'),(4,'Virtual Avatars','2D character layouts for streamers'),(5,'Silent Horizons','Calming minimalist watercolor prints'),
(6,'Pixel Arcade','Retro game loops and custom assets'),(7,'Iron Core','Mecha armor and tech machinery sheets'),(8,'Vector Spaces','Clean geometry patterns and designs'),(9,'Ink and Shadow','Dark horror comic line creations'),(10,'Chibi Kingdom','Super stylized matching profile pictures'),
(11,'Flora and Form','Botanical line art and custom prints'),(12,'Heroic Legends','Custom roleplaying character templates'),(13,'Neon Grid','Vaporwave cityscapes and design prints'),(14,'Beast Ref Sheets','Detailed animal character dynamic sheets'),(15,'Bedtime Stories','Friendly artwork for kids publications'),
(16,'Steel Protectors','Fantasy medieval armory concept guides'),(17,'Retro Pop Icons','Vibrant comic poster style creations'),(18,'Lo-Fi Nostalgia','Relaxing animated scene loops background'),(19,'Streamer Hub','Custom broadcast overlay visual components'),(20,'Ink Needle Stencils','Crisp high contrast body art outlines'),
(21,'Classic Anime Vibe','Nostalgic cell shaded digital paintings'),(22,'Chat Emote Vault','Expressive tiny reaction stickers vectors'),(23,'Wasteland Ruins','Grim post-apocalyptic base landscapes'),(24,'Digital Canvas Oil','Realism focus human portrait renders'),(25,'Toon Caricatures','Exaggerated funny cartoon friend group art'),
(26,'Witchcraft Moods','Gothic magical theme designs elements'),(27,'Duo Icons Pack','Sweet couple themed matching avatars'),(28,'Starship Voyager','Sci-fi technical spaceships deck layout'),(29,'Golden Seasons','Impressionism forest pathway paint sets'),(30,'Blueprints Dynamic','Technical line drafting design schematics'),
(31,'Shonen Clash','Action packed comic panel graphics'),(32,'Plushie blueprints','Turnaround schematics for merchandise creation'),(33,'Mystic Cartography','Fantasy adventure topography continent map designs'),(34,'Idol Wardrobe Packs','Layered costume designs for virtual personas'),(35,'Dune Chronicles','Atmospheric sci-fi desert ruins exploration art'),
(36,'Arcade Lettering','Vintage neon script font graphic design packages'),(37,'Steam and Gears','Steampunk engineering aesthetic fantasy prints'),(38,'Isometric Comfort','Cozy low poly room design indoor layouts'),(39,'Webtoon Flow Sheets','Pacing layout composition panel drafts series'),(40,'Fantasy UI Vault','Ornate inventory screens design element graphics'),
(41,'Minimal Cosmos','Delicate space constellation line art collection'),(42,'Token Borders Frame','Tabletop RPG printable token border designs'),(43,'Rainy Streets Aesthetic','Moody rainy cyberpunk store front layouts'),(44,'Mythical Bestiary','High detail griffin and dragon portrait series'),(45,'Seamless Patterns Tile','Cute repeated graphic background texture elements'),
(46,'Surreal Spaces','Abstract dreamscape concepts oil texture brush style'),(47,'Flat Data Infographics','Corporate presentation clean workflow diagrams'),(48,'Pixel Campfire Cozy','Charming old school pixel loop animations portfolio'),(49,'Broadcast Overlays Pack','Complete streaming stream view frames layouts set'),(50,'Chalk Board Signs','Charming restaurant display mock typography items'),
(51,'Graffiti Legends','Street art lettering wall designs canvas styles'),(52,'Magical Girls Realm','Glittering celestial anime style character packs'),(53,'Kaiju Core Renders','Monstrous titans dynamic scale fight concepts'),(54,'Twitch Emote Palace','Expressive custom face text reactions pack'),(55,'Chivalry Armor Guides','Historically accurate medieval knight render packs'),
(56,'Starry Sky Scenic','Soft celestial atmosphere background paint compilations'),(57,'Engine Mechanics Grid','Intricate mechanical bike parts blueprint rendering'),(58,'Party Group Memories','Fun multiple character party campaign layouts'),(59,'Abstract Balance Line','Geometric shapes modern balanced living posters'),(60,'Spooky Mansions Oil','Haunted vintage estate gothic color sets'),
(61,'Green Leaf Botanicals','Elegant clean plant vector printable art assets'),(62,'Fiery Tieflings Pack','Rich deep skin tones detailed character showcases'),(63,'Vaporwave Grid Sunset','Retro sunset wireframe mesh landscape illustrations'),(64,'Dragon Breath Encounters','Epic scale dragon combat fantasy dynamic paintings'),(65,'Friendly Forest Animals','Warm charming smiling critter cover prints compilation'),
(66,'Holy Shields armory','Polished reflections metallic weapon concept sets'),(67,'Social Banner Templates','Stylized profile layout graphic elements updates'),(68,'Platformer Sprites 8bit','Retro modular arcade levels obstacle sprite tiles'),(69,'Ornate Calligraphy Mats','Elegant swirl frame typography borders text accents'),(70,'Delicate Fine Outlines','Clean minimalistic geometry tattoo ready stencils'),
(71,'Cell Shading 1990','Grain textured old school anime character galleries'),(72,'Food Vendor Isometrics','Charming visual snack bars layout miniature frames'),(73,'Ruinous Horizons','Grim industrial ash landscape environment visuals'),(74,'Rich Impasto Portraits','Thick brush stroke style premium facial renders'),(75,'Expression Sheets Grid','Hilarious sequence frames cartoon face variables'),(76,'Familiar Spirits Spark','Mystical glowing animal familiars concept collection'),(77,'Community Gathering Art','Warm interactive crowd scene illustrations showcase'),(78,'Cockpit Command Decks','Highly detailed sci-fi ship dashboard overlay art'),(79,'Golden Hour Woodlands','Warm light leaking forest path scene portfolios'),(80,'Cross Section Blueprints','Intricate sliced architectural interior view works'),
(81,'Explosive Shonen Panels','High contrast speed line action comic pages'),(82,'Merchandise Ready Specs','Vector paths optimized directly for vinyl stamping'),(83,'Cloud Realms Scenic','Vast blue sky floating island landscape paintings'),(84,'Layer Sliced Models','Rigging ready virtual avatar separate entity slices'),(85,'Haze and Stardust','Cinematic galactic fog space explorer concepts'),(86,'Bold Comic Branding','Pop text vintage logos high visibility items'),(87,'Pipe Network Schematics','Steampunk copper valve boiler interface graphics'),(88,'Warm Library Nooks','Bookshelves and fireplaces relaxing room captures'),(89,'Promo Splash Banners','Webcomic cover display introductory visual plates'),(90,'Menu Borders Jewel','Polished crystal element item frames inventory UI'),
(91,'Zodiac Lines Mystic','Gold on black celestial chart design prints'),(92,'Card Deck Backing Layout','Symmetric print safe playing cards vector mockups'),(93,'Wet Asphalt Cityscapes','Reflective neon puddles dark alley color captures'),(94,'Feather and Wing Studies','Detailed wings anatomy drawing for bird creatures'),(95,'Pastel Texture Seamless','Soft repetitive surface elements textile patterns pack'),(96,'Trippy Mindscapes Oil','Vibrant color distortion abstract dream visuals'),(97,'Vector Charts Layout','Clean corporate graphic module presentations design'),(98,'Cozy Pixel Embers','Pixel fire particle loop screens custom works'),(99,'Camera Framing Boxes','Twitch streamer modular gaming border packages'),(100,'Cafe Script Blackboard','Charming handwritten food chalk art typography set');

-- ==========================================
-- 15. PORTFOLIO IMAGE TABLE (100 Rows)
-- Maps seamlessly to the portfolio IDs (1 to 100)
-- ==========================================
INSERT INTO portfolio_image_tbl (portfolio_id, image_url) VALUES
(1,'https://example.com/p1_img1.jpg'),(2,'https://example.com/p2_img1.jpg'),(3,'https://example.com/p3_img1.jpg'),(4,'https://example.com/p4_img1.jpg'),(5,'https://example.com/p5_img1.jpg'),
(6,'https://example.com/p6_img1.jpg'),(7,'https://example.com/p7_img1.jpg'),(8,'https://example.com/p8_img1.jpg'),(9,'https://example.com/p9_img1.jpg'),(10,'https://example.com/p10_img1.jpg'),
(11,'https://example.com/p11_img1.jpg'),(12,'https://example.com/p12_img1.jpg'),(13,'https://example.com/p13_img1.jpg'),(14,'https://example.com/p14_img1.jpg'),(15,'https://example.com/p15_img1.jpg'),
(16,'https://example.com/p16_img1.jpg'),(17,'https://example.com/p17_img1.jpg'),(18,'https://example.com/p18_img1.jpg'),(19,'https://example.com/p19_img1.jpg'),(20,'https://example.com/p20_img1.jpg'),
(21,'https://example.com/p21_img1.jpg'),(22,'https://example.com/p22_img1.jpg'),(23,'https://example.com/p23_img1.jpg'),(24,'https://example.com/p24_img1.jpg'),(25,'https://example.com/p25_img1.jpg'),
(26,'https://example.com/p26_img1.jpg'),(27,'https://example.com/p27_img1.jpg'),(28,'https://example.com/p28_img1.jpg'),(29,'https://example.com/p29_img1.jpg'),(30,'https://example.com/p30_img1.jpg'),
(31,'https://example.com/p31_img1.jpg'),(32,'https://example.com/p32_img1.jpg'),(33,'https://example.com/p33_img1.jpg'),(34,'https://example.com/p34_img1.jpg'),(35,'https://example.com/p35_img1.jpg'),
(36,'https://example.com/p36_img1.jpg'),(37,'https://example.com/p37_img1.jpg'),(38,'https://example.com/p38_img1.jpg'),(39,'https://example.com/p39_img1.jpg'),(40,'https://example.com/p40_img1.jpg'),
(41,'https://example.com/p41_img1.jpg'),(42,'https://example.com/p42_img1.jpg'),(43,'https://example.com/p43_img1.jpg'),(44,'https://example.com/p44_img1.jpg'),(45,'https://example.com/p45_img1.jpg'),
(46,'https://example.com/p46_img1.jpg'),(47,'https://example.com/p47_img1.jpg'),(48,'https://example.com/p48_img1.jpg'),(49,'https://example.com/p49_img1.jpg'),(50,'https://example.com/p50_img1.jpg'),
(51,'https://example.com/p51_img1.jpg'),(52,'https://example.com/p52_img1.jpg'),(53,'https://example.com/p53_img1.jpg'),(54,'https://example.com/p54_img1.jpg'),(55,'https://example.com/p55_img1.jpg'),
(56,'https://example.com/p56_img1.jpg'),(57,'https://example.com/p57_img1.jpg'),(58,'https://example.com/p58_img1.jpg'),(59,'https://example.com/p59_img1.jpg'),(60,'https://example.com/p60_img1.jpg'),
(61,'https://example.com/p61_img1.jpg'),(62,'https://example.com/p62_img1.jpg'),(63,'https://example.com/p63_img1.jpg'),(64,'https://example.com/p64_img1.jpg'),(65,'https://example.com/p65_img1.jpg'),
(66,'https://example.com/p66_img1.jpg'),(67,'https://example.com/p67_img1.jpg'),(68,'https://example.com/p68_img1.jpg'),(69,'https://example.com/p69_img1.jpg'),(70,'https://example.com/p70_img1.jpg'),
(71,'https://example.com/p71_img1.jpg'),(72,'https://example.com/p72_img1.jpg'),(73,'https://example.com/p73_img1.jpg'),(74,'https://example.com/p74_img1.jpg'),(75,'https://example.com/p75_img1.jpg'),
(76,'https://example.com/p76_img1.jpg'),(77,'https://example.com/p77_img1.jpg'),(78,'https://example.com/p78_img1.jpg'),(79,'https://example.com/p79_img1.jpg'),(80,'https://example.com/p80_img1.jpg'),
(81,'https://example.com/p81_img1.jpg'),(82,'https://example.com/p82_img1.jpg'),(83,'https://example.com/p83_img1.jpg'),(84,'https://example.com/p84_img1.jpg'),(85,'https://example.com/p85_img1.jpg'),
(86,'https://example.com/p86_img1.jpg'),(87,'https://example.com/p87_img1.jpg'),(88,'https://example.com/p88_img1.jpg'),(89,'https://example.com/p89_img1.jpg'),(90,'https://example.com/p90_img1.jpg'),
(91,'https://example.com/p91_img1.jpg'),(92,'https://example.com/p92_img1.jpg'),(93,'https://example.com/p93_img1.jpg'),(94,'https://example.com/p94_img1.jpg'),(95,'https://example.com/p95_img1.jpg'),
(96,'https://example.com/p96_img1.jpg'),(97,'https://example.com/p97_img1.jpg'),(98,'https://example.com/p98_img1.jpg'),(99,'https://example.com/p99_img1.jpg'),(100,'https://example.com/p100_img1.jpg');

-- ==========================================
-- 16. IMAGE TABLE (100 Rows)
-- Alternates tracking references across system entities safely
-- ==========================================
INSERT INTO image_tbl (image_url, image_type_id, user_id, artist_id, commission_id) VALUES
('https://example.com/sys-img-1.jpg',1,1,NULL,NULL),('https://example.com/sys-img-2.jpg',1,2,NULL,NULL),('https://example.com/sys-img-3.jpg',1,3,NULL,NULL),('https://example.com/sys-img-4.jpg',1,4,NULL,NULL),('https://example.com/sys-img-5.jpg',1,5,NULL,NULL),
('https://example.com/sys-img-6.jpg',1,6,NULL,NULL),('https://example.com/sys-img-7.jpg',1,7,NULL,NULL),('https://example.com/sys-img-8.jpg',1,8,NULL,NULL),('https://example.com/sys-img-9.jpg',1,9,NULL,NULL),('https://example.com/sys-img-10.jpg',1,10,NULL,NULL),
('https://example.com/sys-img-11.jpg',1,11,NULL,NULL),('https://example.com/sys-img-12.jpg',1,12,NULL,NULL),('https://example.com/sys-img-13.jpg',1,13,NULL,NULL),('https://example.com/sys-img-14.jpg',1,14,NULL,NULL),('https://example.com/sys-img-15.jpg',1,15,NULL,NULL),
('https://example.com/sys-img-16.jpg',1,16,NULL,NULL),('https://example.com/sys-img-17.jpg',1,17,NULL,NULL),('https://example.com/sys-img-18.jpg',1,18,NULL,NULL),('https://example.com/sys-img-19.jpg',1,19,NULL,NULL),('https://example.com/sys-img-20.jpg',1,20,NULL,NULL),
('https://example.com/sys-img-21.jpg',1,21,NULL,NULL),('https://example.com/sys-img-22.jpg',1,22,NULL,NULL),('https://example.com/sys-img-23.jpg',1,23,NULL,NULL),('https://example.com/sys-img-24.jpg',1,24,NULL,NULL),('https://example.com/sys-img-25.jpg',1,25,NULL,NULL),
('https://example.com/sys-img-26.jpg',2,NULL,1,NULL),('https://example.com/sys-img-27.jpg',2,NULL,2,NULL),('https://example.com/sys-img-28.jpg',2,NULL,3,NULL),('https://example.com/sys-img-29.jpg',2,NULL,4,NULL),('https://example.com/sys-img-30.jpg',2,NULL,5,NULL),
('https://example.com/sys-img-31.jpg',2,NULL,6,NULL),('https://example.com/sys-img-32.jpg',2,NULL,7,NULL),('https://example.com/sys-img-33.jpg',2,NULL,8,NULL),('https://example.com/sys-img-34.jpg',2,NULL,9,NULL),('https://example.com/sys-img-35.jpg',2,NULL,10,NULL),
('https://example.com/sys-img-36.jpg',2,NULL,11,NULL),('https://example.com/sys-img-37.jpg',2,NULL,12,NULL),('https://example.com/sys-img-38.jpg',2,NULL,13,NULL),('https://example.com/sys-img-39.jpg',2,NULL,14,NULL),('https://example.com/sys-img-40.jpg',2,NULL,15,NULL),
('https://example.com/sys-img-41.jpg',2,NULL,16,NULL),('https://example.com/sys-img-42.jpg',2,NULL,17,NULL),('https://example.com/sys-img-43.jpg',2,NULL,18,NULL),('https://example.com/sys-img-44.jpg',2,NULL,19,NULL),('https://example.com/sys-img-45.jpg',2,NULL,20,NULL),
('https://example.com/sys-img-46.jpg',2,NULL,21,NULL),('https://example.com/sys-img-47.jpg',2,NULL,22,NULL),('https://example.com/sys-img-48.jpg',2,NULL,23,NULL),('https://example.com/sys-img-49.jpg',2,NULL,24,NULL),('https://example.com/sys-img-50.jpg',2,NULL,25,NULL),
('https://example.com/sys-img-51.jpg',3,NULL,NULL,1),('https://example.com/sys-img-52.jpg',3,NULL,NULL,2),('https://example.com/sys-img-53.jpg',3,NULL,NULL,3),('https://example.com/sys-img-54.jpg',3,NULL,NULL,4),('https://example.com/sys-img-55.jpg',3,NULL,NULL,5),
('https://example.com/sys-img-56.jpg',3,NULL,NULL,6),('https://example.com/sys-img-57.jpg',3,NULL,NULL,7),('https://example.com/sys-img-58.jpg',3,NULL,NULL,8),('https://example.com/sys-img-59.jpg',3,NULL,NULL,9),('https://example.com/sys-img-60.jpg',3,NULL,NULL,10),
('https://example.com/sys-img-61.jpg',3,NULL,NULL,11),('https://example.com/sys-img-62.jpg',3,NULL,NULL,12),('https://example.com/sys-img-63.jpg',3,NULL,NULL,13),('https://example.com/sys-img-64.jpg',3,NULL,NULL,14),('https://example.com/sys-img-65.jpg',3,NULL,NULL,15),
('https://example.com/sys-img-66.jpg',3,NULL,NULL,16),('https://example.com/sys-img-67.jpg',3,NULL,NULL,17),('https://example.com/sys-img-68.jpg',3,NULL,NULL,18),('https://example.com/sys-img-69.jpg',3,NULL,NULL,19),('https://example.com/sys-img-70.jpg',3,NULL,NULL,20),
('https://example.com/sys-img-71.jpg',3,NULL,NULL,21),('https://example.com/sys-img-72.jpg',3,NULL,NULL,22),('https://example.com/sys-img-73.jpg',3,NULL,NULL,23),('https://example.com/sys-img-74.jpg',3,NULL,NULL,24),('https://example.com/sys-img-75.jpg',3,NULL,NULL,25),
('https://example.com/sys-img-76.jpg',4,1,NULL,1),('https://example.com/sys-img-77.jpg',4,2,NULL,2),('https://example.com/sys-img-78.jpg',4,3,NULL,3),('https://example.com/sys-img-79.jpg',4,4,NULL,4),('https://example.com/sys-img-80.jpg',4,5,NULL,5),
('https://example.com/sys-img-81.jpg',4,6,NULL,6),('https://example.com/sys-img-82.jpg',4,7,NULL,7),('https://example.com/sys-img-83.jpg',4,8,NULL,8),('https://example.com/sys-img-84.jpg',4,9,NULL,9),('https://example.com/sys-img-85.jpg',4,10,NULL,10),
('https://example.com/sys-img-86.jpg',4,11,NULL,11),('https://example.com/sys-img-87.jpg',4,12,NULL,12),('https://example.com/sys-img-88.jpg',4,13,NULL,13),('https://example.com/sys-img-89.jpg',4,14,NULL,14),('https://example.com/sys-img-90.jpg',4,15,NULL,15),
('https://example.com/sys-img-91.jpg',4,16,NULL,16),('https://example.com/sys-img-92.jpg',4,17,NULL,17),('https://example.com/sys-img-93.jpg',4,18,NULL,18),('https://example.com/sys-img-94.jpg',4,19,NULL,19),('https://example.com/sys-img-95.jpg',4,20,NULL,20),
('https://example.com/sys-img-96.jpg',4,21,NULL,21),('https://example.com/sys-img-97.jpg',4,22,NULL,22),('https://example.com/sys-img-98.jpg',4,23,NULL,23),('https://example.com/sys-img-99.jpg',4,24,NULL,24),('https://example.com/sys-img-100.jpg',4,25,NULL,25);