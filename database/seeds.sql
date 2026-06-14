USE artovia_db;

-- Step 1: Temporarily turn off foreign key safety checks
SET FOREIGN_KEY_CHECKS = 0;

-- Step 2: Empty all data from every table (new table included)
TRUNCATE TABLE account_status_tbl;
TRUNCATE TABLE account_tbl;
TRUNCATE TABLE administrator_tbl;
TRUNCATE TABLE artist_tbl;
TRUNCATE TABLE artworks_tbl;
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
TRUNCATE TABLE review_tbl;
TRUNCATE TABLE role_tbl;
TRUNCATE TABLE status_tbl;
TRUNCATE TABLE transaction_tbl;
TRUNCATE TABLE user_payment_method_tbl;
TRUNCATE TABLE user_tbl;

-- Step 3: Turn safety checks back on
SET FOREIGN_KEY_CHECKS = 1;

-- ==========================================
-- 1. LOOKUP / REFERENCE TABLES
-- ==========================================

INSERT INTO account_status_tbl (account_status_id, status_name) VALUES
(1, 'Active'), (2, 'Banned'), (3, 'Suspended');

INSERT INTO role_tbl (role_id, role_name) VALUES
(1, 'User'), (2, 'Artist'), (3, 'Administrator');

INSERT INTO status_tbl (status_id, status_name) VALUES
(1, 'Active'), (2, 'Pending'), (3, 'Accepted'), (4, 'Rejected'),
(5, 'In Progress'), (6, 'Completed'), (7, 'Cancelled'), (8, 'Read'),
(9, 'Unread'), (10, 'Paid');

-- Maya is now included alongside GCash, PayPal, Credit Card, Bank Transfer
INSERT INTO payment_method_tbl (payment_method_id, payment_method_name) VALUES
(1, 'GCash'),
(2, 'Maya'),
(3, 'PayPal'),
(4, 'Credit Card'),
(5, 'Bank Transfer');

INSERT INTO image_type_tbl (image_type_id, image_type_name) VALUES
(1, 'Profile'), (2, 'Artwork'), (3, 'Commission'), (4, 'Reference');

INSERT INTO category_tbl (category_id, category_name) VALUES
(1, 'Anime'), (2, 'Chibi'), (3, 'Pixel Art'), (4, 'Watercolor'),
(5, 'Fantasy'), (6, 'Logo Design'), (7, 'Portrait'), (8, 'Character Design');

-- ==========================================
-- 2. ACCOUNT TABLE (105 Rows)
-- ==========================================
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
(2,1,'artist_40','$2y$10$hash','Ezra',NULL,'Blackwood','ezra.art@mail.com','09170000090'),
(2,1,'artist_41','$2y$10$hash','Violet',NULL,'Castillo','violet.art@mail.com','09170000091'),
(2,1,'artist_42','$2y$10$hash','Finn',NULL,'Sullivan','finn.art@mail.com','09170000092'),
(2,1,'artist_43','$2y$10$hash','Hazel',NULL,'Reyes','hazel.art@mail.com','09170000093'),
(2,1,'artist_44','$2y$10$hash','Zion',NULL,'Murray','zion.art@mail.com','09170000094'),
(2,1,'artist_45','$2y$10$hash','Poppy',NULL,'West','poppy.art@mail.com','09170000095'),
(2,1,'artist_46','$2y$10$hash','Axel',NULL,'Jordan','axel.art@mail.com','09170000096'),
(2,1,'artist_47','$2y$10$hash','Daisy',NULL,'Lane','daisy.art@mail.com','09170000097'),
(2,1,'artist_48','$2y$10$hash','Ryder',NULL,'Grant','ryder.art@mail.com','09170000098'),
(2,1,'artist_49','$2y$10$hash','Willow',NULL,'Webb','willow.art@mail.com','09170000099'),
(2,1,'artist_50','$2y$10$hash','Caden',NULL,'Stone','caden.art@mail.com','09170000100'),
(3,1,'admin_1','$2y$10$hash','Admin',NULL,'One','admin1@artovia.com','09170000101'),
(3,1,'admin_2','$2y$10$hash','Admin',NULL,'Two','admin2@artovia.com','09170000102'),
(3,1,'admin_3','$2y$10$hash','Admin',NULL,'Three','admin3@artovia.com','09170000103'),
(3,1,'admin_4','$2y$10$hash','Admin',NULL,'Four','admin4@artovia.com','09170000104'),
(3,1,'admin_5','$2y$10$hash','Admin',NULL,'Five','admin5@artovia.com','09170000105');

-- ==========================================
-- 3. USER TABLE (50 Rows) — no card_number column
-- ==========================================
INSERT INTO user_tbl (account_id) VALUES
(1),(2),(3),(4),(5),(6),(7),(8),(9),(10),
(11),(12),(13),(14),(15),(16),(17),(18),(19),(20),
(21),(22),(23),(24),(25),(26),(27),(28),(29),(30),
(31),(32),(33),(34),(35),(36),(37),(38),(39),(40),
(41),(42),(43),(44),(45),(46),(47),(48),(49),(50);

-- ==========================================
-- 4. ARTIST TABLE (50 Rows)
-- ==========================================
INSERT INTO artist_tbl (account_id, starting_rate, is_available) VALUES
(51,250.00,TRUE),(52,300.00,TRUE),(53,350.00,TRUE),(54,400.00,TRUE),(55,450.00,TRUE),
(56,500.00,TRUE),(57,550.00,TRUE),(58,600.00,TRUE),(59,650.00,TRUE),(60,700.00,TRUE),
(61,750.00,TRUE),(62,800.00,TRUE),(63,850.00,TRUE),(64,900.00,TRUE),(65,950.00,TRUE),
(66,1000.00,TRUE),(67,1050.00,TRUE),(68,1100.00,TRUE),(69,1150.00,TRUE),(70,1200.00,TRUE),
(71,1250.00,TRUE),(72,1300.00,TRUE),(73,1350.00,TRUE),(74,1400.00,TRUE),(75,1450.00,TRUE),
(76,1500.00,TRUE),(77,1550.00,TRUE),(78,1600.00,TRUE),(79,1650.00,TRUE),(80,1700.00,TRUE),
(81,1750.00,TRUE),(82,1800.00,TRUE),(83,1850.00,TRUE),(84,1900.00,TRUE),(85,1950.00,TRUE),
(86,2000.00,TRUE),(87,2050.00,TRUE),(88,2100.00,TRUE),(89,2150.00,TRUE),(90,2200.00,TRUE),
(91,2250.00,TRUE),(92,2300.00,TRUE),(93,2350.00,TRUE),(94,2400.00,TRUE),(95,2450.00,TRUE),
(96,2500.00,TRUE),(97,2550.00,TRUE),(98,2600.00,TRUE),(99,2650.00,TRUE),(100,3000.00,TRUE);

-- ==========================================
-- 5. USER PAYMENT METHODS (50 Rows)
-- Replaces card_number in user_tbl.
-- Cycles through all 5 payment methods.
-- ==========================================
INSERT INTO user_payment_method_tbl
    (user_id, payment_method_id, mobile_number, email_address, card_number, card_expiry, bank_name, account_number, is_default)
VALUES
-- GCash (method 1)
(1, 1,'09170000001',NULL,NULL,NULL,NULL,NULL,TRUE),
(6, 1,'09170000006',NULL,NULL,NULL,NULL,NULL,TRUE),
(11,1,'09170000011',NULL,NULL,NULL,NULL,NULL,TRUE),
(16,1,'09170000016',NULL,NULL,NULL,NULL,NULL,TRUE),
(21,1,'09170000021',NULL,NULL,NULL,NULL,NULL,TRUE),
(26,1,'09170000026',NULL,NULL,NULL,NULL,NULL,TRUE),
(31,1,'09170000031',NULL,NULL,NULL,NULL,NULL,TRUE),
(36,1,'09170000036',NULL,NULL,NULL,NULL,NULL,TRUE),
(41,1,'09170000041',NULL,NULL,NULL,NULL,NULL,TRUE),
(46,1,'09170000046',NULL,NULL,NULL,NULL,NULL,TRUE),
-- Maya (method 2)
(2, 2,'09170000002',NULL,NULL,NULL,NULL,NULL,TRUE),
(7, 2,'09170000007',NULL,NULL,NULL,NULL,NULL,TRUE),
(12,2,'09170000012',NULL,NULL,NULL,NULL,NULL,TRUE),
(17,2,'09170000017',NULL,NULL,NULL,NULL,NULL,TRUE),
(22,2,'09170000022',NULL,NULL,NULL,NULL,NULL,TRUE),
(27,2,'09170000027',NULL,NULL,NULL,NULL,NULL,TRUE),
(32,2,'09170000032',NULL,NULL,NULL,NULL,NULL,TRUE),
(37,2,'09170000037',NULL,NULL,NULL,NULL,NULL,TRUE),
(42,2,'09170000042',NULL,NULL,NULL,NULL,NULL,TRUE),
(47,2,'09170000047',NULL,NULL,NULL,NULL,NULL,TRUE),
-- PayPal (method 3)
(3, 3,NULL,'noah.w@mail.com',NULL,NULL,NULL,NULL,TRUE),
(8, 3,NULL,'charlotte.d@mail.com',NULL,NULL,NULL,NULL,TRUE),
(13,3,NULL,'ben.g@mail.com',NULL,NULL,NULL,NULL,TRUE),
(18,3,NULL,'harper.m@mail.com',NULL,NULL,NULL,NULL,TRUE),
(23,3,NULL,'henry.t@mail.com',NULL,NULL,NULL,NULL,TRUE),
(28,3,NULL,'avery.r@mail.com',NULL,NULL,NULL,NULL,TRUE),
(33,3,NULL,'sam.a@mail.com',NULL,NULL,NULL,NULL,TRUE),
(38,3,NULL,'penelope.n@mail.com',NULL,NULL,NULL,NULL,TRUE),
(43,3,NULL,'matthew.n@mail.com',NULL,NULL,NULL,NULL,TRUE),
(48,3,NULL,'audrey.m@mail.com',NULL,NULL,NULL,NULL,TRUE),
-- Credit Card (method 4)
(4, 4,NULL,NULL,'4111111111111111','12/27',NULL,NULL,TRUE),
(9, 4,NULL,NULL,'4111111111112222','11/26',NULL,NULL,TRUE),
(14,4,NULL,NULL,'4111111111113333','10/25',NULL,NULL,TRUE),
(19,4,NULL,NULL,'4111111111114444','09/28',NULL,NULL,TRUE),
(24,4,NULL,NULL,'4111111111115555','08/27',NULL,NULL,TRUE),
(29,4,NULL,NULL,'4111111111116666','07/26',NULL,NULL,TRUE),
(34,4,NULL,NULL,'4111111111117777','06/25',NULL,NULL,TRUE),
(39,4,NULL,NULL,'4111111111118888','05/28',NULL,NULL,TRUE),
(44,4,NULL,NULL,'4111111111119999','04/27',NULL,NULL,TRUE),
(49,4,NULL,NULL,'4111111111110000','03/26',NULL,NULL,TRUE),
-- Bank Transfer (method 5)
(5, 5,NULL,NULL,NULL,NULL,'BDO','00100200300',TRUE),
(10,5,NULL,NULL,NULL,NULL,'BPI','00100200301',TRUE),
(15,5,NULL,NULL,NULL,NULL,'Metrobank','00100200302',TRUE),
(20,5,NULL,NULL,NULL,NULL,'UnionBank','00100200303',TRUE),
(25,5,NULL,NULL,NULL,NULL,'Landbank','00100200304',TRUE),
(30,5,NULL,NULL,NULL,NULL,'BDO','00100200305',TRUE),
(35,5,NULL,NULL,NULL,NULL,'BPI','00100200306',TRUE),
(40,5,NULL,NULL,NULL,NULL,'Metrobank','00100200307',TRUE),
(45,5,NULL,NULL,NULL,NULL,'UnionBank','00100200308',TRUE),
(50,5,NULL,NULL,NULL,NULL,'Landbank','00100200309',TRUE);

-- ==========================================
-- 6. ADMINISTRATOR TABLE
-- ==========================================
INSERT INTO administrator_tbl (account_id) VALUES
(101),(102),(103),(104),(105);

-- ==========================================
-- 7. HIRED ARTIST TABLE (100 Rows)
-- ==========================================
INSERT INTO hired_artist_tbl (artist_id, user_id, hire_date, status_id) VALUES
(1,1,'2024-01-01',1),(2,1,'2024-01-02',1),(3,1,'2024-01-03',1),(4,1,'2024-01-04',1),(5,1,'2024-01-05',1),
(6,1,'2024-01-06',1),(7,1,'2024-01-07',1),(8,1,'2024-01-08',1),(9,1,'2024-01-09',1),(10,1,'2024-01-10',1),
(11,1,'2024-01-11',1),(12,1,'2024-01-12',1),(13,1,'2024-01-13',1),(14,1,'2024-01-14',1),(15,1,'2024-01-15',1),
(16,1,'2024-01-16',1),(17,1,'2024-01-17',1),(18,1,'2024-01-18',1),(19,1,'2024-01-19',1),(20,1,'2024-01-20',1),
(21,1,'2024-01-21',1),(22,1,'2024-01-22',1),(23,1,'2024-01-23',1),(24,1,'2024-01-24',1),(25,1,'2024-01-25',1),
(26,1,'2024-01-26',1),(27,1,'2024-01-27',1),(28,1,'2024-01-28',1),(29,1,'2024-01-29',1),(30,1,'2024-01-30',1),
(31,1,'2024-01-31',1),(32,1,'2024-02-01',1),(33,1,'2024-02-02',1),(34,1,'2024-02-03',1),(35,1,'2024-02-04',1),
(36,1,'2024-02-05',1),(37,1,'2024-02-06',1),(38,1,'2024-02-07',1),(39,1,'2024-02-08',1),(40,1,'2024-02-09',1),
(41,1,'2024-02-10',1),(42,1,'2024-02-11',1),(43,1,'2024-02-12',1),(44,1,'2024-02-13',1),(45,1,'2024-02-14',1),
(46,1,'2024-02-15',1),(47,1,'2024-02-16',1),(48,1,'2024-02-17',1),(49,1,'2024-02-18',1),(50,1,'2024-02-19',1),
(1,2,'2024-02-20',1),(2,2,'2024-02-21',1),(3,2,'2024-02-22',1),(4,2,'2024-02-23',1),(5,2,'2024-02-24',1),
(6,2,'2024-02-25',1),(7,2,'2024-02-26',1),(8,2,'2024-02-27',1),(9,2,'2024-02-28',1),(10,2,'2024-02-29',1),
(11,2,'2024-03-01',1),(12,2,'2024-03-02',1),(13,2,'2024-03-03',1),(14,2,'2024-03-04',1),(15,2,'2024-03-05',1),
(16,2,'2024-03-06',1),(17,2,'2024-03-07',1),(18,2,'2024-03-08',1),(19,2,'2024-03-09',1),(20,2,'2024-03-10',1),
(21,2,'2024-03-11',1),(22,2,'2024-03-12',1),(23,2,'2024-03-13',1),(24,2,'2024-03-14',1),(25,2,'2024-03-15',1),
(26,2,'2024-03-16',1),(27,2,'2024-03-17',1),(28,2,'2024-03-18',1),(29,2,'2024-03-19',1),(30,2,'2024-03-20',1),
(31,2,'2024-03-21',1),(32,2,'2024-03-22',1),(33,2,'2024-03-23',1),(34,2,'2024-03-24',1),(35,2,'2024-03-25',1),
(36,2,'2024-03-26',1),(37,2,'2024-03-27',1),(38,2,'2024-03-28',1),(39,2,'2024-03-29',1),(40,2,'2024-03-30',1),
(41,2,'2024-03-31',1),(42,2,'2024-04-01',1),(43,2,'2024-04-02',1),(44,2,'2024-04-03',1),(45,2,'2024-04-04',1),
(46,2,'2024-04-05',1),(47,2,'2024-04-06',1),(48,2,'2024-04-07',1),(49,2,'2024-04-08',1),(50,2,'2024-04-09',1);

-- ==========================================
-- 8. COMMISSIONS TABLE (100 Rows)
-- ==========================================
INSERT INTO commission_tbl (user_id, artist_id, description, status_id, price) VALUES
(1,1,'Anime illustration',2,500.00),(2,2,'Chibi icon profile',2,350.00),(3,3,'Fantasy novel cover art',5,1500.00),(4,4,'VTuber structural model 2D',6,2500.00),(5,5,'Landscape concept painting',2,800.00),
(6,6,'Twitch sub badges graphic',2,300.00),(7,7,'Mecha design high detail',5,1800.00),(8,8,'Game background vector',6,900.00),(9,9,'Comic book page ink',2,1200.00),(10,10,'Pixel art sprite sheets',2,400.00),
(11,11,'Vector minimalist design logo',2,250.00),(12,12,'D&D character full body art',5,1100.00),(13,13,'Cyberpunk themed wallpaper',6,1300.00),(14,14,'Fursona dynamic pose ref',2,700.00),(15,15,'Children book illustration page',2,600.00),
(16,16,'Dark fantasy armor setup design',2,1600.00),(17,17,'Pop art style custom print',5,450.00),(18,18,'Lo-fi aesthetic animation loop',6,2000.00),(19,19,'Steam profile header graphic',2,200.00),(20,20,'Tattoo pattern floral sleeve',2,850.00),
(21,21,'Retro synthwave album poster',2,650.00),(22,22,'Custom emoji set Discord pack',5,350.00),(23,23,'Manga style spread backdrop',6,1400.00),(24,24,'Water color family portrait print',2,1050.00),(25,25,'Caricature funny drawing group',2,500.00),
(26,26,'Gothic portrait canvas mock',2,950.00),(27,27,'Chibi matching couple icons',5,400.00),(28,28,'Sci-fi starship model sheet',6,1750.00),(29,29,'Oil painting landscape simulation',2,1250.00),(30,30,'Line art structural blueprint aesthetic',2,550.00),
(31,31,'Anime style thumbnail splash',2,600.00),(32,32,'Cute animal sticker designs pack',5,300.00),(33,33,'High fantasy spell card framework',6,1150.00),(34,34,'Live2D rigging prep model design',2,3000.00),(35,35,'Environment concept matte painting',2,1400.00),
(36,36,'Esports team mascot visual icon',2,750.00),(37,37,'Steampunk character outfit ref sheet',5,1350.00),(38,38,'Isometric room vector model illustration',6,950.00),(39,39,'Webtoon panel storyboard draft',2,800.00),(40,40,'Game item inventory UI icons asset',2,600.00),
(41,41,'Minimal line art home decor design',2,300.00),(42,42,'RPG character custom token frame art',5,1000.00),(43,43,'Neon cyberpunk street view overlay',6,1650.00),(44,44,'Mythological creature beast painting',2,1900.00),(45,45,'Cute food items vector pattern design',2,450.00),
(46,46,'Surrealist psychological painting print',2,1450.00),(47,47,'Corporate presentation custom flat vector',5,500.00),(48,48,'Animated pixel intro screen setup',6,2200.00),(49,49,'YouTube stream overlay graphics package',2,850.00),(50,50,'Traditional style dynamic sketch portrait',2,400.00),
(1,1,'Vibrant graffiti lettering sketch canvas',2,550.00),(2,2,'Kawaii style magical girl illustration',5,700.00),(3,3,'Epic boss monster conceptual design',6,2100.00),(4,4,'Cute twitch emotes package of 6 items',2,450.00),(5,5,'Historical knight battle armor render',2,1600.00),
(6,6,'Calm starry night sky scenic background',2,850.00),(7,7,'Futuristic cyberpunk motorcycle sheet',5,1250.00),(8,8,'Chibi style fantasy party group photo',6,1800.00),(9,9,'Abstract geometric art layout design',2,600.00),(10,10,'Dark watercolor gothic mansion visual',2,1100.00),
(11,11,'Elegant botanical print design',2,550.00),(12,12,'Heroic RPG character full render',5,700.00),(13,13,'Vaporwave neon city wallpaper',6,2100.00),(14,14,'Dragon rider dynamic portrait',2,450.00),(15,15,'Storybook animal characters page',2,1600.00),
(16,16,'Medieval paladin armor concept',2,850.00),(17,17,'Retro pop album cover mock',5,1250.00),(18,18,'Chill lo-fi rain loop animation',6,1800.00),(19,19,'Stream alert overlay graphics',2,600.00),(20,20,'Floral tattoo arm sleeve design',2,1100.00),
(21,21,'Synthwave sunset poster print',2,650.00),(22,22,'Custom twitch emote set pack',5,350.00),(23,23,'Post-apocalyptic ruins backdrop',6,1700.00),(24,24,'Realism oil portrait commission',2,1500.00),(25,25,'Caricature birthday sketch group',2,550.00),
(26,26,'Gothic witch portrait painting',2,650.00),(27,27,'Couple chibi avatar matching set',5,1200.00),(28,28,'Sci-fi command bridge layout',6,2000.00),(29,29,'Autumn forest impressionism art',2,1150.00),(30,30,'Technical blueprint drafting sheet',2,1850.00),
(31,31,'Action manga cover splash art',2,1400.00),(32,32,'Merchandise turnaround flat vector',5,500.00),(33,33,'Fantasy continent map design',6,1600.00),(34,34,'Vtuber layered costume design',2,2200.00),(35,35,'Atmospheric sci-fi desert art',2,1750.00),
(36,36,'Vintage arcade neon lettering',2,600.00),(37,37,'Steampunk airship concept sheet',5,1450.00),(38,38,'Cozy isometric room illustration',6,1250.00),(39,39,'Webtoon panel storyboard series',2,900.00),(40,40,'Fantasy UI inventory icons set',2,1100.00),
(41,41,'Minimal moon constellation art',2,350.00),(42,42,'Tabletop RPG token frame pack',5,850.00),(43,43,'Cyberpunk neon alleyway scene',6,1500.00),(44,44,'Griffin creature portrait render',2,1300.00),(45,45,'Seamless tile pattern background',2,500.00),
(46,46,'Surrealist dreamscape oil print',2,1650.00),(47,47,'Clean corporate infographic set',5,700.00),(48,48,'Pixel campfire ambient loop art',6,1400.00),(49,49,'Full streaming overlay frame pack',2,1200.00),(50,50,'Chalkboard chalk art typography',2,450.00);

-- ==========================================
-- 9. COMMISSION REQUEST TABLE (100 Rows)
-- ==========================================
INSERT INTO commission_request_tbl (commission_id, artist_id, message, status_id) VALUES
(1,1,'I can deliver this within 3 days easily!',2),(2,2,'I love drawing cute stuff, hope we work together.',2),(3,3,'High fantasy settings are exactly my style.',3),(4,4,'Expert in 2D models, checking in.',2),(5,5,'Can handle landscape challenges nicely.',2),
(6,6,'Can provide variations on the twitch icons.',2),(7,7,'Experienced in industrial mecha designs.',2),(8,8,'Vector files will be fully clean layered.',2),(9,9,'Will deliver raw high-res dynamic ink layout.',2),(10,10,'Love working on pixel sheets!',2),
(11,11,'Minimalist vector brands are my specialty.',2),(12,12,'D&D characters have rich detail in my style.',3),(13,13,'Cyberpunk tech palettes are my comfort zone.',3),(14,14,'Fursona reference sheets done perfectly.',2),(15,15,'Children illustrations are gentle and warm.',2),
(16,16,'Dark theme armor is my primary design track.',2),(17,17,'Pop art and vivid prints are totally my thing.',3),(18,18,'Lo-fi loops are a favorite project category.',3),(19,19,'Profile header banners are fast turnarounds.',2),(20,20,'Floral pattern sleeves are fun sleeve designs.',2),
(21,21,'Synthwave retro aesthetics are a passion project.',2),(22,22,'Tiny custom emote packs are my specialty.',3),(23,23,'Manga spreads in large canvas sizes are available.',3),(24,24,'Watercolor portrait styles blend beautifully.',2),(25,25,'Caricatures are my strongest comedic art style.',2),
(26,26,'Gothic portraits suit dark academia aesthetics.',2),(27,27,'Couple chibi sets are charming match projects.',3),(28,28,'Sci-fi ship sheets are technically precise.',3),(29,29,'Landscape oil simulation blends naturally.',2),(30,30,'Blueprint line structures are my drafting forte.',2),
(31,31,'Anime thumbnails splash pages pop on screen.',2),(32,32,'Animal sticker vectors come fully optimized.',3),(33,33,'Fantasy spellcard frameworks are structured well.',3),(34,34,'Live2D rigging prep models are my specialty niche.',2),(35,35,'Environmental concept painting is a proud skill.',2),
(36,36,'Mascot visuals translate well to merch formats.',2),(37,37,'Steampunk outfit sheets have rich accessory details.',3),(38,38,'Isometric layouts look clean and layered.',3),(39,39,'Webtoon storyboard drafts flow at a good pace.',2),(40,40,'Game UI icon sets are clean and reusable.',2),
(41,41,'Minimal line art is sleek for home use.',2),(42,42,'Custom token frame art is tabletop optimized.',3),(43,43,'Cyberpunk overlays are high contrast neon.',3),(44,44,'Creature paintings are dynamic and layered.',2),(45,45,'Pattern vectors tile seamlessly at any size.',2),
(46,46,'Surrealist prints blend color beautifully.',2),(47,47,'Flat vector graphics are clean for decks.',3),(48,48,'Pixel animated screens are smooth and loopable.',3),(49,49,'Stream overlay packages include all frame types.',2),(50,50,'Sketch portrait dynamism fits this style well.',2),
(51,1,'Graffiti sketches have good street texture.',2),(52,2,'Magical girl art is a frequent request type.',3),(53,3,'Boss monster concepts are rendered in high detail.',3),(54,4,'Twitch emote packages come in multiple formats.',2),(55,5,'Knight armor render has historical accuracy.',2),
(56,6,'Night sky paintings are calming pieces.',2),(57,7,'Motorcycle tech sheets are layered and clean.',3),(58,8,'Group chibi pieces have balanced composition.',3),(59,9,'Geometric abstract layouts are balanced designs.',2),(60,10,'Gothic mansion paint sets match the dark style.',2),
(61,11,'Botanical print assets are elegant line work.',2),(62,12,'RPG character renders have high detail.',3),(63,13,'Vaporwave grid prints are retro and vivid.',3),(64,14,'Dragon rider portraits are dynamic and large.',2),(65,15,'Forest animal covers are warm and inviting.',2),
(66,16,'Paladin armor shield concepts are polished.',2),(67,17,'Pop banner templates are flexible formats.',3),(68,18,'Lo-fi animation loops are relaxing mood pieces.',3),(69,19,'Stream alert graphics are modular and clean.',2),(70,20,'Fine line tattoo outlines are stencil-ready.',2),
(71,21,'Cell-shaded anime galleries are my signature.',2),(72,22,'Isometric food vendor frames are charming.',3),(73,23,'Industrial ruin environments are dramatic art.',3),(74,24,'Impasto portrait renders have rich textures.',2),(75,25,'Expression sheet grids are funny sequences.',2),
(76,26,'Familiar spirit concepts glow with color.',2),(77,27,'Community crowd illustrations are warm scenes.',3),(78,28,'Cockpit dashboard overlays are technically precise.',3),(79,29,'Woodland path portfolios have great warmth.',2),(80,30,'Architectural cross-section works are intricate.',2),
(81,31,'Action panels have explosive dynamic layout.',2),(82,32,'Merchandise vector turnarounds are precise.',3),(83,33,'Fantasy terrain maps have stylized depth.',3),(84,34,'VTuber wardrobe layers are modular assets.',2),(85,35,'Desert sci-fi art has great atmosphere.',2),
(86,36,'Neon arcade lettering has a strong glow effect.',2),(87,37,'Airship concepts have steampunk detail.',3),(88,38,'Cozy isometric rooms have warm interiors.',3),(89,39,'Panel storyboard series has strong flow.',2),(90,40,'Fantasy UI sets are symmetrical and ornate.',2),
(91,41,'Moon constellation art is delicate and clean.',2),(92,42,'Tabletop token borders print clearly.',3),(93,43,'Cyberpunk alleyway scenes glow with neon.',3),(94,44,'Griffin portraits have epic creature energy.',2),(95,45,'Seamless pattern tiles repeat perfectly.',2),
(96,46,'Dreamscape oil prints have surreal depth.',2),(97,47,'Corporate infographic sets are readable.',3),(98,48,'Pixel campfire loops have ambient warmth.',3),(99,49,'Streaming overlay frame packs are complete.',2),(100,50,'Chalk art typography mimics real texture.',2);

-- ==========================================
-- 10. TRANSACTION TABLE (100 Rows)
-- status_id 6 = Completed
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
-- 11. PAYMENT TABLE (100 Rows)
-- Cycles through payment_method_ids 1-5 (now includes Maya)
-- status_id 10 = Paid
-- ==========================================
INSERT INTO payment_tbl (transaction_id, payment_method_id, amount, status_id) VALUES
(1,1,500.00,10),(2,2,350.00,10),(3,3,1500.00,10),(4,4,2500.00,10),(5,5,800.00,10),(6,1,300.00,10),(7,2,1800.00,10),(8,3,900.00,10),(9,4,1200.00,10),(10,5,400.00,10),
(11,1,250.00,10),(12,2,1100.00,10),(13,3,1300.00,10),(14,4,700.00,10),(15,5,600.00,10),(16,1,1600.00,10),(17,2,450.00,10),(18,3,2000.00,10),(19,4,200.00,10),(20,5,850.00,10),
(21,1,650.00,10),(22,2,350.00,10),(23,3,1400.00,10),(24,4,1050.00,10),(25,5,500.00,10),(26,1,950.00,10),(27,2,400.00,10),(28,3,1750.00,10),(29,4,1250.00,10),(30,5,550.00,10),
(31,1,600.00,10),(32,2,300.00,10),(33,3,1150.00,10),(34,4,3000.00,10),(35,5,1400.00,10),(36,1,750.00,10),(37,2,1350.00,10),(38,3,950.00,10),(39,4,800.00,10),(40,5,600.00,10),
(41,1,300.00,10),(42,2,1000.00,10),(43,3,1650.00,10),(44,4,1900.00,10),(45,5,450.00,10),(46,1,1450.00,10),(47,2,500.00,10),(48,3,2200.00,10),(49,4,850.00,10),(50,5,400.00,10),
(51,1,550.00,10),(52,2,700.00,10),(53,3,2100.00,10),(54,4,450.00,10),(55,5,1600.00,10),(56,1,850.00,10),(57,2,1250.00,10),(58,3,1800.00,10),(59,4,600.00,10),(60,5,1100.00,10),
(61,1,250.00,10),(62,2,1300.00,10),(63,3,900.00,10),(64,4,2400.00,10),(65,5,750.00,10),(66,1,800.00,10),(67,2,500.00,10),(68,3,1350.00,10),(69,4,300.00,10),(70,5,400.00,10),
(71,1,850.00,10),(72,2,650.00,10),(73,3,1700.00,10),(74,4,1500.00,10),(75,5,550.00,10),(76,1,650.00,10),(77,2,1200.00,10),(78,3,2000.00,10),(79,4,1150.00,10),(80,5,1850.00,10),
(81,1,1400.00,10),(82,2,500.00,10),(83,3,1600.00,10),(84,4,2200.00,10),(85,5,1750.00,10),(86,1,600.00,10),(87,2,1450.00,10),(88,3,1250.00,10),(89,4,900.00,10),(90,5,1100.00,10),
(91,1,350.00,10),(92,2,850.00,10),(93,3,1500.00,10),(94,4,1300.00,10),(95,5,500.00,10),(96,1,1650.00,10),(97,2,700.00,10),(98,3,1400.00,10),(99,4,1200.00,10),(100,5,450.00,10);

-- ==========================================
-- 12. FAVORITE TABLE (100 Rows)
-- ==========================================
INSERT INTO favorite_tbl (account_id, user_id, artist_id) VALUES
(1,1,1),(2,2,2),(3,3,3),(4,4,4),(5,5,5),(6,6,6),(7,7,7),(8,8,8),(9,9,9),(10,10,10),
(11,11,11),(12,12,12),(13,13,13),(14,14,14),(15,15,15),(16,16,16),(17,17,17),(18,18,18),(19,19,19),(20,20,20),
(21,21,21),(22,22,22),(23,23,23),(24,24,24),(25,25,25),(26,26,26),(27,27,27),(28,28,28),(29,29,29),(30,30,30),
(31,31,31),(32,32,32),(33,33,33),(34,34,34),(35,35,35),(36,36,36),(37,37,37),(38,38,38),(39,39,39),(40,40,40),
(41,41,41),(42,42,42),(43,43,43),(44,44,44),(45,45,45),(46,46,46),(47,47,47),(48,48,48),(49,49,49),(50,50,50),
(1,1,2),(2,2,3),(3,3,4),(4,4,5),(5,5,6),(6,6,7),(7,7,8),(8,8,9),(9,9,10),(10,10,11),
(11,11,12),(12,12,13),(13,13,14),(14,14,15),(15,15,16),(16,16,17),(17,17,18),(18,18,19),(19,19,20),(20,20,21),
(21,21,22),(22,22,23),(23,23,24),(24,24,25),(25,25,26),(26,26,27),(27,27,28),(28,28,29),(29,29,30),(30,30,31),
(31,31,32),(32,32,33),(33,33,34),(34,34,35),(35,35,36),(36,36,37),(37,37,38),(38,38,39),(39,39,40),(40,40,41),
(41,41,42),(42,42,43),(43,43,44),(44,44,45),(45,45,46),(46,46,47),(47,47,48),(48,48,49),(49,49,50),(50,50,1);

-- ==========================================
-- 13. CONVERSATIONS & MESSAGES (abridged — same as original)
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
-- 14. SAMPLE REVIEWS (25 Rows — one per completed commission)
-- commission_ids 3,4,8,13,18,23 etc. have status Completed (6)
-- ==========================================
INSERT INTO review_tbl (artist_id, reviewer_account_id, commission_id, rating, comment) VALUES
(3,3,3,5,'Absolutely stunning fantasy cover, exceeded all expectations!'),(4,4,4,5,'Perfect VTuber model, very professional delivery.'),(8,8,8,4,'Clean vector background, minor revision was handled fast.'),(13,13,13,5,'Cyberpunk wallpaper was exactly the vibe I asked for.'),(18,18,18,5,'Lo-fi loop is so relaxing, exactly what I needed.'),
(23,23,23,4,'Great manga spread, colors were vibrant and dynamic.'),(28,28,28,5,'Starship sheet was technically detailed and impressive.'),(33,33,33,4,'Fantasy spell card had beautiful ornamental borders.'),(38,38,38,5,'Isometric room illustration was cozy and layered perfectly.'),(43,43,43,5,'Cyberpunk overlay popped with great neon contrast.'),
(48,48,48,5,'Pixel animated screen was smooth and charming.'),(1,1,51,4,'Graffiti lettering canvas had great street art texture.'),(2,2,52,5,'Magical girl illustration was sparkly and well-detailed.'),(3,3,53,5,'Boss monster concept was dramatic and high-quality.'),(8,8,58,4,'Chibi group was cute with excellent composition.'),
(13,13,63,5,'Vaporwave wallpaper had perfect retro grid aesthetics.'),(18,18,68,5,'Lo-fi rain loop was incredibly soothing.'),(23,23,73,4,'Post-apocalyptic ruins were atmospheric and gritty.'),(28,28,78,5,'Cockpit layout had intricate and believable detail.'),(33,33,83,4,'Fantasy continent map had beautiful stylized terrain.'),
(38,38,88,5,'Cozy isometric room had perfect warm lighting.'),(43,43,93,5,'Cyberpunk alleyway glowed with great neon depth.'),(48,48,98,5,'Pixel campfire loop was ambient and relaxing.'),(4,4,54,4,'Twitch emote pack came in all needed formats.'),(8,58,59,3,'Decent background but took longer than expected.');

-- ==========================================
-- 15. ADMIN USER (overwrite-safe)
-- ==========================================
DELETE FROM account_tbl WHERE username = 'admin';

INSERT INTO account_tbl (username, password_hash, role_id, account_status_id, first_name, last_name, email)
VALUES ('admin', 'password', 3, 1, 'System', 'Admin', 'admin@artovia.com');

INSERT INTO administrator_tbl (account_id)
VALUES (LAST_INSERT_ID());