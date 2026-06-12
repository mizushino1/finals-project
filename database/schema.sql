CREATE DATABASE artovia_db;
USE artovia_db;

-- =====================================
-- ROLE TABLE
-- =====================================

CREATE TABLE role_tbl (
    role_id INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- =====================================
-- ACCOUNT TABLE
-- =====================================

CREATE TABLE account_tbl (
    account_id INT AUTO_INCREMENT PRIMARY KEY,
    role_id INT NOT NULL,
    username VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    middle_name VARCHAR(100),
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    phone VARCHAR(20),
    account_status VARCHAR(20) NOT NULL DEFAULT 'Active',
    creation_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (role_id)
        REFERENCES role_tbl(role_id)
);

-- =====================================
-- USER PROFILE
-- =====================================

CREATE TABLE user_tbl (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL UNIQUE,
    card_number VARCHAR(20),

    FOREIGN KEY (account_id)
        REFERENCES account_tbl(account_id)
        ON DELETE CASCADE
);

-- =====================================
-- ARTIST PROFILE
-- =====================================

CREATE TABLE artist_tbl (
    artist_id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL UNIQUE,
    starting_rate DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (account_id)
        REFERENCES account_tbl(account_id)
        ON DELETE CASCADE
);

-- =====================================
-- ADMIN PROFILE
-- =====================================

CREATE TABLE administrator_tbl (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL UNIQUE,

    FOREIGN KEY (account_id)
        REFERENCES account_tbl(account_id)
        ON DELETE CASCADE
);

-- =====================================
-- STATUS LOOKUP
-- =====================================

CREATE TABLE status_tbl (
    status_id INT AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(50) NOT NULL UNIQUE
);

-- =====================================
-- PAYMENT METHOD LOOKUP
-- =====================================

CREATE TABLE payment_method_tbl (
    payment_method_id INT AUTO_INCREMENT PRIMARY KEY,
    payment_method_name VARCHAR(50) NOT NULL UNIQUE
);

-- =====================================
-- HIRED ARTISTS
-- =====================================

CREATE TABLE hired_artist_tbl (
    hire_id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT NOT NULL,
    user_id INT NOT NULL,
    hire_date DATE NOT NULL,
    status_id INT NOT NULL,

    FOREIGN KEY (artist_id)
        REFERENCES artist_tbl(artist_id),

    FOREIGN KEY (user_id)
        REFERENCES user_tbl(user_id),

    FOREIGN KEY (status_id)
        REFERENCES status_tbl(status_id)
);

-- =====================================
-- COMMISSIONS
-- =====================================

CREATE TABLE commission_tbl (
    commission_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    artist_id INT NULL,
    description TEXT,
    status_id INT NOT NULL,
    commission_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    price DECIMAL(10,2) NOT NULL,

    FOREIGN KEY (user_id)
        REFERENCES user_tbl(user_id),

    FOREIGN KEY (artist_id)
        REFERENCES artist_tbl(artist_id),

    FOREIGN KEY (status_id)
        REFERENCES status_tbl(status_id)
);

-- =====================================
-- COMMISSION REQUESTS
-- =====================================

CREATE TABLE commission_request_tbl (
    request_id INT AUTO_INCREMENT PRIMARY KEY,
    commission_id INT NOT NULL,
    artist_id INT NOT NULL,
    message TEXT,
    status_id INT NOT NULL,
    requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (commission_id)
        REFERENCES commission_tbl(commission_id),

    FOREIGN KEY (artist_id)
        REFERENCES artist_tbl(artist_id),

    FOREIGN KEY (status_id)
        REFERENCES status_tbl(status_id)
);

-- =====================================
-- TRANSACTIONS
-- =====================================

CREATE TABLE transaction_tbl (
    transaction_id INT AUTO_INCREMENT PRIMARY KEY,
    commission_id INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    transaction_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status_id INT NOT NULL,

    FOREIGN KEY (commission_id)
        REFERENCES commission_tbl(commission_id),

    FOREIGN KEY (status_id)
        REFERENCES status_tbl(status_id)
);

-- =====================================
-- PAYMENTS
-- =====================================

CREATE TABLE payment_tbl (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id INT NOT NULL,
    payment_method_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    status_id INT NOT NULL,
    payment_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (transaction_id)
        REFERENCES transaction_tbl(transaction_id),

    FOREIGN KEY (payment_method_id)
        REFERENCES payment_method_tbl(payment_method_id),

    FOREIGN KEY (status_id)
        REFERENCES status_tbl(status_id)
);

-- =====================================
-- FAVORITES
-- =====================================

CREATE TABLE favorite_tbl (
    favorite_id INT AUTO_INCREMENT PRIMARY KEY,
    account_id  INT NOT NULL,
    user_id     INT NULL,
    artist_id   INT NULL,
    date_added  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (account_id) REFERENCES account_tbl(account_id),
    FOREIGN KEY (user_id)    REFERENCES user_tbl(user_id),
    FOREIGN KEY (artist_id)  REFERENCES artist_tbl(artist_id),

    UNIQUE(account_id, user_id, artist_id)
);

-- =====================================
-- MESSAGES
-- =====================================

CREATE TABLE message_tbl (
    message_id INT AUTO_INCREMENT PRIMARY KEY,

    sender_account_id INT NOT NULL,
    receiver_account_id INT NOT NULL,

    message_content TEXT NOT NULL,

    sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    status_id INT NOT NULL,

    FOREIGN KEY (sender_account_id)
        REFERENCES account_tbl(account_id),

    FOREIGN KEY (receiver_account_id)
        REFERENCES account_tbl(account_id),

    FOREIGN KEY (status_id)
        REFERENCES status_tbl(status_id)
);

-- =====================================
-- IMAGES
-- =====================================

CREATE TABLE image_tbl (
    image_id INT AUTO_INCREMENT PRIMARY KEY,

    image_url VARCHAR(2048) NOT NULL,
    image_type VARCHAR(50) NOT NULL,

    user_id INT NULL,
    artist_id INT NULL,
    commission_id INT NULL,

    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)
        REFERENCES user_tbl(user_id)
        ON DELETE CASCADE,

    FOREIGN KEY (artist_id)
        REFERENCES artist_tbl(artist_id)
        ON DELETE CASCADE,

    FOREIGN KEY (commission_id)
        REFERENCES commission_tbl(commission_id)
        ON DELETE CASCADE
);


CREATE TABLE account_status_tbl (
    account_status_id INT AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(20) NOT NULL UNIQUE
);

INSERT INTO account_status_tbl(status_name)
VALUES
('Active'),
('Banned'),
('Suspended');

ALTER TABLE account_tbl
ADD account_status_id INT NOT NULL AFTER role_id;

ALTER TABLE account_tbl
ADD CONSTRAINT fk_account_status
FOREIGN KEY (account_status_id)
REFERENCES account_status_tbl(account_status_id);

ALTER TABLE account_tbl
DROP COLUMN account_status;

CREATE TABLE image_type_tbl (
    image_type_id INT AUTO_INCREMENT PRIMARY KEY,
    image_type_name VARCHAR(50) NOT NULL UNIQUE
);

INSERT INTO image_type_tbl(image_type_name)
VALUES
('Profile'),
('Artwork'),
('Commission'),
('Reference');

ALTER TABLE image_tbl
ADD image_type_id INT NOT NULL;

ALTER TABLE image_tbl
ADD CONSTRAINT fk_image_type
FOREIGN KEY (image_type_id)
REFERENCES image_type_tbl(image_type_id);

ALTER TABLE image_tbl
DROP COLUMN image_type;

CREATE TABLE category_tbl (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

INSERT INTO category_tbl(category_name)
VALUES
('Anime'),
('Chibi'),
('Pixel Art'),
('Watercolor'),
('Fantasy'),
('Logo Design'),
('Portrait'),
('Character Design');

ALTER TABLE commission_tbl
ADD category_id INT NULL;

ALTER TABLE commission_tbl
ADD CONSTRAINT fk_commission_category
FOREIGN KEY (category_id)
REFERENCES category_tbl(category_id);

ALTER TABLE artist_tbl
ADD is_available BOOLEAN NOT NULL DEFAULT TRUE;

ALTER TABLE account_tbl
ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE commission_tbl
ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE transaction_tbl
ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE payment_tbl
ADD updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
ON UPDATE CURRENT_TIMESTAMP;

ALTER TABLE commission_request_tbl
ADD CONSTRAINT uq_artist_commission
UNIQUE (commission_id, artist_id);

CREATE TABLE conversation_tbl (
    conversation_id INT AUTO_INCREMENT PRIMARY KEY,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE message_tbl
ADD conversation_id INT NULL;

ALTER TABLE message_tbl
ADD CONSTRAINT fk_message_conversation
FOREIGN KEY (conversation_id)
REFERENCES conversation_tbl(conversation_id);

CREATE TABLE portfolio_tbl (
    portfolio_id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (artist_id)
    REFERENCES artist_tbl(artist_id)
    ON DELETE CASCADE
);

CREATE TABLE portfolio_image_tbl (
    portfolio_image_id INT AUTO_INCREMENT PRIMARY KEY,
    portfolio_id INT NOT NULL,
    image_url VARCHAR(2048) NOT NULL,

    FOREIGN KEY (portfolio_id)
    REFERENCES portfolio_tbl(portfolio_id)
    ON DELETE CASCADE
);

INSERT INTO status_tbl(status_name)
VALUES
('Active'),
('Pending'),
('Accepted'),
('Rejected'),
('In Progress'),
('Completed'),
('Cancelled'),
('Read'),
('Unread'),
('Paid');


INSERT INTO payment_method_tbl(payment_method_name)
VALUES
('GCash'),
('PayPal'),
('Credit Card'),
('Bank Transfer');

ALTER TABLE artist_tbl
ADD artist_description TEXT NULL AFTER is_available;

ALTER TABLE artist_tbl ADD COLUMN description TEXT;

-- =====================================
-- REVIEWS TABLE
-- =====================================

CREATE TABLE review_tbl (
    review_id           INT AUTO_INCREMENT PRIMARY KEY,
    artist_id           INT NOT NULL,
    reviewer_account_id INT NOT NULL,
    commission_id       INT NULL,           -- optional: tie review to a specific commission
    rating              TINYINT NOT NULL    -- 1–5
                        CHECK (rating BETWEEN 1 AND 5),
    comment             TEXT,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP
                        ON UPDATE CURRENT_TIMESTAMP,

    -- One review per reviewer per artist (or per commission if you prefer)
    UNIQUE KEY uq_review_artist_reviewer (artist_id, reviewer_account_id),

    FOREIGN KEY (artist_id)
        REFERENCES artist_tbl(artist_id)
        ON DELETE CASCADE,

    FOREIGN KEY (reviewer_account_id)
        REFERENCES account_tbl(account_id)
        ON DELETE CASCADE,

    FOREIGN KEY (commission_id)
        REFERENCES commission_tbl(commission_id)
        ON DELETE SET NULL
);

CREATE TABLE artworks_tbl (
    artwork_id   INT AUTO_INCREMENT PRIMARY KEY,
    image_id     INT NOT NULL UNIQUE,
    title        VARCHAR(255) NOT NULL DEFAULT 'Untitled',
    description  TEXT NULL,
    created_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (image_id)
        REFERENCES image_tbl(image_id)
        ON DELETE CASCADE
);