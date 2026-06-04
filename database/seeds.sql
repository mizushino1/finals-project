INSERT INTO role_tbl (role_name)
VALUES
('User'),
('Artist'),
('Administrator');

INSERT INTO account_status_tbl (status_name)
VALUES
('Active'),
('Banned'),
('Suspended');

INSERT INTO account_tbl
(
    role_id,
    account_status_id,
    username,
    password_hash,
    first_name,
    middle_name,
    last_name,
    email,
    phone
)
VALUES
-- Users
(1,1,'john_doe','$2y$10$hash','John','Michael','Doe','john@email.com','09171234567'),
(1,1,'jane_smith','$2y$10$hash','Jane','Anne','Smith','jane@email.com','09171234568'),
(1,1,'carlos_reyes','$2y$10$hash','Carlos','Luis','Reyes','carlos@email.com','09171234569'),

-- Artists
(2,1,'benten_art','$2y$10$hash','Ben',NULL,'Benten','ben@email.com','09171234570'),
(2,1,'luna_draws','$2y$10$hash','Luna',NULL,'Santos','luna@email.com','09171234571'),
(2,1,'inkmaster_jay','$2y$10$hash','Jay-R',NULL,'Umandap','jay@email.com','09171234572'),

-- Admins
(3,1,'superadmin','$2y$10$hash','Super',NULL,'Admin','admin@email.com','09171234573');

INSERT INTO user_tbl
(account_id, card_number)
VALUES
(1,'4111111111111111'),
(2,'4222222222222222'),
(3,NULL);

INSERT INTO artist_tbl
(account_id, starting_rate, is_available)
VALUES
(4,800.00,TRUE),
(5,500.00,TRUE),
(6,1200.00,TRUE);

INSERT INTO administrator_tbl
(account_id)
VALUES
(7);

INSERT INTO hired_artist_tbl
(
artist_id,
admin_id,
hire_date,
status_id
)
VALUES
(1,1,'2024-01-01',1),
(2,1,'2024-01-15',1),
(3,1,'2024-02-01',1);

INSERT INTO category_tbl
(category_name)
VALUES
('Anime'),
('Chibi'),
('Pixel Art'),
('Watercolor'),
('Fantasy'),
('Logo Design'),
('Portrait'),
('Character Design');

INSERT INTO commission_tbl
(
user_id,
artist_id,
category_id,
description,
status_id,
price
)
VALUES
(1,NULL,1,'Full body anime character commission',2,800.00),
(2,NULL,2,'Cute chibi cat illustration',2,350.00),
(3,1,8,'Character design for fantasy game',5,1200.00),
(1,2,7,'Couple portrait commission',6,500.00);

INSERT INTO commission_request_tbl
(
commission_id,
artist_id,
message,
status_id
)
VALUES
(1,1,'I specialize in anime art.',2),
(1,3,'Fantasy anime is my specialty.',2),
(2,2,'I can make this super cute.',2),
(3,1,'I would love to work on this.',3);

INSERT INTO transaction_tbl
(
commission_id,
total_amount,
status_id
)
VALUES
(4,500.00,6);

INSERT INTO payment_tbl
(
transaction_id,
payment_method_id,
amount,
status_id
)
VALUES
(1,1,500.00,10);

INSERT INTO favorite_tbl
(
user_id,
artist_id
)
VALUES
(1,1),
(1,2),
(2,1),
(3,3);

INSERT INTO conversation_tbl
VALUES
(NULL,NOW()),
(NULL,NOW()),
(NULL,NOW());

INSERT INTO message_tbl
(
sender_account_id,
receiver_account_id,
message_content,
status_id,
conversation_id
)
VALUES
(1,4,'Hi! Are you available for a commission?',8,1),
(4,1,'Yes, I am available.',8,1),

(2,5,'Can you draw my cat?',8,2),
(5,2,'Of course!',8,2),

(3,6,'Interested in a fantasy character design.',9,3);

INSERT INTO portfolio_tbl
(
artist_id,
title,
description
)
VALUES
(1,'Fantasy Collection','Dark fantasy themed illustrations'),
(2,'Chibi Collection','Cute chibi artwork'),
(3,'Character Designs','Game and anime character designs');

INSERT INTO portfolio_image_tbl
(
portfolio_id,
image_url
)
VALUES
(1,'https://example.com/fantasy1.jpg'),
(1,'https://example.com/fantasy2.jpg'),

(2,'https://example.com/chibi1.jpg'),

(3,'https://example.com/design1.jpg'),
(3,'https://example.com/design2.jpg');

INSERT INTO image_tbl
(
image_url,
image_type_id,
user_id,
artist_id,
commission_id
)
VALUES
('https://example.com/profile-user1.jpg',1,1,NULL,NULL),
('https://example.com/profile-artist1.jpg',1,NULL,1,NULL),
('https://example.com/commission1.jpg',3,NULL,1,3),
('https://example.com/reference1.jpg',4,1,NULL,1);

