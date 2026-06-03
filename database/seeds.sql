USE artovia_db;

-- ══════════════════════════════════════
-- USERS
-- ══════════════════════════════════════
INSERT INTO user_tbl (role, username, password, acc_creation_date, last_name, first_name, middle_name, account_status, card_number) VALUES
('user', 'john_doe',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-01-15', 'Doe',       'John',    'Michael',  'active',   '4111111111111111'),
('user', 'jane_smith',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-02-20', 'Smith',     'Jane',    'Anne',     'active',   '4222222222222222'),
('user', 'carlos_reyes', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-03-05', 'Reyes',     'Carlos',  'Luis',     'active',   NULL),
('user', 'mia_santos',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-03-18', 'Santos',    'Mia',     NULL,       'active',   '4333333333333333'),
('user', 'leo_cruz',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-04-01', 'Cruz',      'Leo',     'Ramon',    'active',   NULL),
('user', 'nina_garcia',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-04-22', 'Garcia',    'Nina',    'Rose',     'active',   '4444444444444444'),
('user', 'mark_tan',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-05-10', 'Tan',       'Mark',    NULL,       'active',   NULL),
('user', 'sofia_lim',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-05-30', 'Lim',       'Sofia',   'Mae',      'active',   '4555555555555555'),
('user', 'ryan_uy',      '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-06-14', 'Uy',        'Ryan',    NULL,       'active',   NULL),
('user', 'banned_user',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-06-20', 'Banned',    'Banned',  NULL,       'banned',   NULL);

-- ══════════════════════════════════════
-- ARTISTS
-- ══════════════════════════════════════
INSERT INTO artist_tbl (role, username, password, acc_creation_date, last_name, first_name, account_status, start_at, card_number) VALUES
('artist', 'benten_art',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-11-01', 'Benten',    'Ben',      'active',  800.00, '5111111111111111'),
('artist', 'luna_draws',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-11-15', 'Luna',      'Luna',     'active',  500.00, NULL),
('artist', 'inkmaster_jay', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-12-01', 'Umandap',   'Jay-R',    'active',  1200.00,'5222222222222222'),
('artist', 'chibi_queen',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2023-12-20', 'Reyes',     'Angela',   'active',  350.00, NULL),
('artist', 'sketch_pro',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-01-10', 'Pascual',   'Miguel',   'active',  650.00, '5333333333333333'),
('artist', 'pixel_pete',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-01-25', 'Dela Cruz', 'Peter',    'active',  450.00, NULL),
('artist', 'watercolor_ysa','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-02-08', 'Santiago',  'Ysabel',   'active',  900.00, '5444444444444444'),
('artist', 'dark_inker',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-02-28', 'Morales',   'Diego',    'active',  750.00, NULL),
('artist', 'pastel_mae',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-03-15', 'Flores',    'Mae',      'active',  300.00, NULL),
('artist', 'banned_artist', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2024-03-30', 'Banned',    'Banned',   'banned',  500.00, NULL);

-- ══════════════════════════════════════
-- ADMINS
-- ══════════════════════════════════════
INSERT INTO administrator (username, password, role) VALUES
('superadmin',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('admin_greg',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'),
('admin_ash',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- ══════════════════════════════════════
-- HIRED ARTISTS (admin approved artists)
-- ══════════════════════════════════════
INSERT INTO hired_artist (artist_id, admin_id, hire_date, status) VALUES
(1, 1, '2023-11-05',  'active'),
(2, 1, '2023-11-20',  'active'),
(3, 2, '2023-12-05',  'active'),
(4, 2, '2023-12-25',  'active'),
(5, 1, '2024-01-15',  'active'),
(6, 3, '2024-01-30',  'active'),
(7, 1, '2024-02-12',  'active'),
(8, 2, '2024-03-02',  'active'),
(9, 3, '2024-03-20',  'active'),
(10, 1,'2024-04-05',  'banned');

-- ══════════════════════════════════════
-- COMMISSIONS
-- (artist_id NULL = open, waiting for artist requests)
-- (artist_id SET  = in_progress/completed)
-- ══════════════════════════════════════
INSERT INTO commission_tbl (user_id, artist_id, description, status, commission_date, price) VALUES
-- Open posts (no artist yet)
(1,  NULL, 'I want a full body anime character of my OC. Dark fantasy style, female warrior.',              'open',        '2024-07-01', 800.00),
(2,  NULL, 'Need a chibi version of my cat. Orange tabby, cute pose.',                                      'open',        '2024-07-03', 350.00),
(3,  NULL, 'Portrait of me and my partner in watercolor style for our anniversary.',                        'open',        '2024-07-05', 950.00),
(4,  NULL, 'Logo design for my small bakery. Pastel colors, cute bread mascot.',                            'open',        '2024-07-06', 500.00),
(5,  NULL, 'Fantasy map illustration for my DnD campaign. Detailed, old parchment style.',                 'open',        '2024-07-08', 1200.00),
(6,  NULL, 'Pixel art character sprite for my indie game. 64x64, idle and walk animation.',                'open',        '2024-07-10', 600.00),

-- In progress (artist assigned)
(7,  1,    'Bust portrait of my anime OC. Male, silver hair, red eyes, school uniform.',                   'in_progress', '2024-06-15', 800.00),
(8,  3,    'Full illustration of a dragon perched on a mountain. Epic fantasy.',                            'in_progress', '2024-06-18', 1200.00),
(9,  5,    'Couple portrait in semi-realistic style. Reference photos will be provided.',                   'in_progress', '2024-06-20', 650.00),
(1,  7,    'Detailed background painting of a Japanese street at night with rain.',                        'in_progress', '2024-06-25', 900.00),

-- Completed
(2,  2,    'Simple line art of my dog. Black labrador, sitting pose.',                                     'completed',   '2024-05-10', 500.00),
(3,  4,    'Chibi versions of my 3 OCs for a sticker pack.',                                               'completed',   '2024-05-15', 350.00),
(4,  6,    'Pixel art icon for my Twitch channel. Cat with headphones.',                                   'completed',   '2024-05-20', 450.00),
(5,  9,    'Pastel illustration of my OC for a phone wallpaper.',                                          'completed',   '2024-05-25', 300.00),
(6,  8,    'Dark fantasy warrior character, full body with armor details.',                                'completed',   '2024-06-01', 750.00),

-- Cancelled
(7,  NULL, 'Abstract art piece for my bedroom wall. Blue and gold tones.',                                 'cancelled',   '2024-06-05', 400.00),
(8,  NULL, 'Cartoon style family portrait. 4 members.',                                                    'cancelled',   '2024-06-08', 550.00);

-- ══════════════════════════════════════
-- COMMISSION REQUESTS
-- (artists bidding on open posts)
-- ══════════════════════════════════════
INSERT INTO commission_request_tbl (commission_id, artist_id, message, status, requested_at) VALUES
-- Requests on commission 1 (open - anime OC)
(1, 1, 'I specialize in dark fantasy anime art. Check my portfolio!',           'pending',  '2024-07-02'),
(1, 3, 'I can do this! I have experience with fantasy character design.',        'pending',  '2024-07-02'),
(1, 8, 'Dark themes are my specialty. Would love to take this commission.',      'pending',  '2024-07-03'),

-- Requests on commission 2 (open - chibi cat)
(2, 4, 'Chibi is literally my specialty! I can make it super cute.',             'pending',  '2024-07-04'),
(2, 9, 'I love drawing pets in cute styles! Would be happy to help.',            'pending',  '2024-07-04'),

-- Requests on commission 3 (open - watercolor portrait)
(3, 7, 'Watercolor portraits are my favorite. I can deliver in 5 days.',         'pending',  '2024-07-06'),
(3, 2, 'I have done many couple portraits. Very comfortable with this style.',   'pending',  '2024-07-06'),

-- Requests on commission 5 (open - DnD map)
(5, 3, 'I have illustrated several fantasy maps before. Very detailed work.',    'pending',  '2024-07-09'),
(5, 5, 'Map illustration is something I really enjoy. Happy to take this.',      'pending',  '2024-07-09'),

-- Accepted requests (for in_progress commissions)
(7, 1, 'Anime busts are my strong suit. I can finish in 3 days.',                'accepted', '2024-06-16'),
(8, 3, 'Epic fantasy is my genre. This will look amazing.',                      'accepted', '2024-06-19'),
(9, 5, 'Semi-realistic portraits are what I do best.',                           'accepted', '2024-06-21'),
(10,7, 'I love painting atmospheric night scenes. This suits me perfectly.',     'accepted', '2024-06-26');

-- ══════════════════════════════════════
-- TRANSACTIONS
-- ══════════════════════════════════════
INSERT INTO transaction_tbl (commission_id, user_id, artist_id, total_amount, transaction_date, status) VALUES
(11, 2, 2, 500.00,  '2024-05-28', 'completed'),
(12, 3, 4, 350.00,  '2024-06-01', 'completed'),
(13, 4, 6, 450.00,  '2024-06-05', 'completed'),
(14, 5, 9, 300.00,  '2024-06-10', 'completed'),
(15, 6, 8, 750.00,  '2024-06-18', 'completed');

-- ══════════════════════════════════════
-- PAYMENTS
-- ══════════════════════════════════════
INSERT INTO payment_tbl (transaction_id, amount, payment_method, status, payment_date) VALUES
(1, 500.00, 'card',   'completed', '2024-05-28'),
(2, 350.00, 'gcash',  'completed', '2024-06-01'),
(3, 450.00, 'card',   'completed', '2024-06-05'),
(4, 300.00, 'gcash',  'completed', '2024-06-10'),
(5, 750.00, 'card',   'completed', '2024-06-18');

-- ══════════════════════════════════════
-- FAVORITES
-- ══════════════════════════════════════
INSERT INTO favorites_table (user_id, artist_id, date_added) VALUES
(1, 1, '2024-07-01'),
(1, 3, '2024-07-01'),
(2, 2, '2024-06-15'),
(2, 4, '2024-06-16'),
(3, 7, '2024-06-20'),
(4, 9, '2024-06-25'),
(5, 1, '2024-07-02'),
(5, 5, '2024-07-03'),
(6, 8, '2024-07-05'),
(7, 3, '2024-07-06'),
(8, 6, '2024-07-08'),
(9, 2, '2024-07-09');

-- ══════════════════════════════════════
-- MESSAGES
-- (sender = user, receiver = artist per your schema)
-- ══════════════════════════════════════
INSERT INTO message_box (sender_id, receiver_id, message_content, sent_at, status) VALUES
(1, 1, 'Hi! I love your work. Can you do dark fantasy?',                          '2024-07-01 09:00:00', 'read'),
(1, 1, 'Yes I specialize in dark fantasy! Send me your references.',              '2024-07-01 09:05:00', 'read'),
(1, 1, 'Here are my reference images. Let me know your availability.',            '2024-07-01 09:10:00', 'read'),
(2, 2, 'Hello! I saw your chibi work. How long does it usually take?',            '2024-06-15 10:00:00', 'read'),
(2, 2, 'Usually 2-3 days for a simple chibi. Complex ones take up to 5 days.',   '2024-06-15 10:15:00', 'read'),
(3, 7, 'Are you available for a couple portrait this week?',                      '2024-06-20 14:00:00', 'read'),
(3, 7, 'Yes I am available! What style are you looking for?',                     '2024-06-20 14:10:00', 'read'),
(3, 7, 'Watercolor style please. I will send references soon.',                   '2024-06-20 14:15:00', 'read'),
(4, 9, 'I need a logo for my bakery. Can you do pastel illustrations?',           '2024-06-25 11:00:00', 'read'),
(4, 9, 'Absolutely! Pastel is one of my favorite styles.',                        '2024-06-25 11:20:00', 'read'),
(5, 3, 'Can you do a detailed fantasy map? I have a rough sketch.',               '2024-07-08 16:00:00', 'unread'),
(6, 6, 'I need pixel art for my game. Are you familiar with sprite sheets?',     '2024-07-10 13:00:00', 'unread'),
(7, 1, 'Hey, I just requested your commission. Hope to work with you!',           '2024-06-16 08:00:00', 'read'),
(8, 3, 'Your dragon illustration style is perfect for what I need.',              '2024-06-19 09:00:00', 'read'),
(9, 5, 'Looking forward to working with you on our couple portrait.',             '2024-06-21 10:00:00', 'unread');