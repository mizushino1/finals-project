CREATE DATABASE IF NOT EXISTS artovia_db;
USE artovia_db;

-- =====================================
-- ROLE TABLE
-- =====================================
CREATE TABLE role_tbl (
    role_id   INT AUTO_INCREMENT PRIMARY KEY,
    role_name VARCHAR(50) NOT NULL UNIQUE
);

-- =====================================
-- ACCOUNT STATUS LOOKUP
-- =====================================
CREATE TABLE account_status_tbl (
    account_status_id INT AUTO_INCREMENT PRIMARY KEY,
    status_name       VARCHAR(20) NOT NULL UNIQUE
);

-- =====================================
-- ACCOUNT TABLE
-- =====================================
CREATE TABLE account_tbl (
    account_id        INT AUTO_INCREMENT PRIMARY KEY,
    role_id           INT NOT NULL,
    account_status_id INT NOT NULL,
    username          VARCHAR(100) NOT NULL UNIQUE,
    password_hash     VARCHAR(255) NOT NULL,
    first_name        VARCHAR(100) NOT NULL,
    middle_name       VARCHAR(100),
    last_name         VARCHAR(100) NOT NULL,
    email             VARCHAR(255) NOT NULL UNIQUE,
    phone             VARCHAR(20),
    creation_date     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (role_id)           REFERENCES role_tbl(role_id),
    FOREIGN KEY (account_status_id) REFERENCES account_status_tbl(account_status_id)
);

-- =====================================
-- USER PROFILE
-- card_number moved to user_payment_method_tbl
-- =====================================
CREATE TABLE user_tbl (
    user_id    INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL UNIQUE,

    FOREIGN KEY (account_id) REFERENCES account_tbl(account_id) ON DELETE CASCADE
);

-- =====================================
-- ARTIST PROFILE
-- =====================================
CREATE TABLE artist_tbl (
    artist_id          INT AUTO_INCREMENT PRIMARY KEY,
    account_id         INT NOT NULL UNIQUE,
    starting_rate      DECIMAL(10,2) NOT NULL,
    is_available       BOOLEAN NOT NULL DEFAULT TRUE,
    artist_description TEXT NULL,

    FOREIGN KEY (account_id) REFERENCES account_tbl(account_id) ON DELETE CASCADE
);

-- =====================================
-- ADMIN PROFILE
-- =====================================
CREATE TABLE administrator_tbl (
    admin_id   INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL UNIQUE,

    FOREIGN KEY (account_id) REFERENCES account_tbl(account_id) ON DELETE CASCADE
);

-- =====================================
-- STATUS LOOKUP
-- =====================================
CREATE TABLE status_tbl (
    status_id   INT AUTO_INCREMENT PRIMARY KEY,
    status_name VARCHAR(50) NOT NULL UNIQUE
);

-- =====================================
-- PAYMENT METHOD LOOKUP
-- Lookup table — lists every available gateway/type
-- =====================================
CREATE TABLE payment_method_tbl (
    payment_method_id   INT AUTO_INCREMENT PRIMARY KEY,
    payment_method_name VARCHAR(50) NOT NULL UNIQUE  -- e.g. GCash, Maya, PayPal, Credit Card, Bank Transfer
);

-- =====================================
-- USER PAYMENT METHODS
-- Stores per-user saved payment credentials.
-- card_number was formerly in user_tbl — it now lives here
-- alongside every other method so one user can have many.
-- =====================================
CREATE TABLE user_payment_method_tbl (
    user_payment_method_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id                INT NOT NULL,
    payment_method_id      INT NOT NULL,

    -- Shared / generic credential fields.
    -- Only the columns relevant to the chosen method will be populated.
    mobile_number   VARCHAR(20)  NULL,   -- GCash / Maya
    email_address   VARCHAR(255) NULL,   -- PayPal
    card_number     VARCHAR(20)  NULL,   -- Credit / Debit Card (masked: last 4 only in prod)
    card_expiry     VARCHAR(7)   NULL,   -- MM/YY
    bank_name       VARCHAR(100) NULL,   -- Bank Transfer
    account_number  VARCHAR(50)  NULL,   -- Bank Transfer

    is_default  BOOLEAN  NOT NULL DEFAULT FALSE,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)           REFERENCES user_tbl(user_id)                ON DELETE CASCADE,
    FOREIGN KEY (payment_method_id) REFERENCES payment_method_tbl(payment_method_id)
);

-- =====================================
-- IMAGE TYPE LOOKUP
-- =====================================
CREATE TABLE image_type_tbl (
    image_type_id   INT AUTO_INCREMENT PRIMARY KEY,
    image_type_name VARCHAR(50) NOT NULL UNIQUE
);

-- =====================================
-- CATEGORY LOOKUP
-- =====================================
CREATE TABLE category_tbl (
    category_id   INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

-- =====================================
-- HIRED ARTISTS
-- =====================================
CREATE TABLE hired_artist_tbl (
    hire_id   INT AUTO_INCREMENT PRIMARY KEY,
    artist_id INT NOT NULL,
    user_id   INT NOT NULL,
    hire_date DATE NOT NULL,
    status_id INT NOT NULL,

    FOREIGN KEY (artist_id) REFERENCES artist_tbl(artist_id),
    FOREIGN KEY (user_id)   REFERENCES user_tbl(user_id),
    FOREIGN KEY (status_id) REFERENCES status_tbl(status_id)
);

-- =====================================
-- COMMISSIONS
-- =====================================
CREATE TABLE commission_tbl (
    commission_id   INT AUTO_INCREMENT PRIMARY KEY,
    user_id         INT NOT NULL,
    artist_id       INT NULL,
    category_id     INT NULL,
    description     TEXT,
    status_id       INT NOT NULL,
    commission_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    price           DECIMAL(10,2) NOT NULL,
    updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id)     REFERENCES user_tbl(user_id),
    FOREIGN KEY (artist_id)   REFERENCES artist_tbl(artist_id),
    FOREIGN KEY (status_id)   REFERENCES status_tbl(status_id),
    FOREIGN KEY (category_id) REFERENCES category_tbl(category_id)
);

-- =====================================
-- COMMISSION REQUESTS
-- =====================================
CREATE TABLE commission_request_tbl (
    request_id    INT AUTO_INCREMENT PRIMARY KEY,
    commission_id INT NOT NULL,
    artist_id     INT NOT NULL,
    message       TEXT,
    status_id     INT NOT NULL,
    requested_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY uq_artist_commission (commission_id, artist_id),

    FOREIGN KEY (commission_id) REFERENCES commission_tbl(commission_id),
    FOREIGN KEY (artist_id)     REFERENCES artist_tbl(artist_id),
    FOREIGN KEY (status_id)     REFERENCES status_tbl(status_id)
);

-- =====================================
-- TRANSACTIONS
-- =====================================
CREATE TABLE transaction_tbl (
    transaction_id   INT AUTO_INCREMENT PRIMARY KEY,
    commission_id    INT NOT NULL,
    total_amount     DECIMAL(10,2) NOT NULL,
    transaction_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status_id        INT NOT NULL,
    updated_at       DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (commission_id) REFERENCES commission_tbl(commission_id),
    FOREIGN KEY (status_id)     REFERENCES status_tbl(status_id)
);

-- =====================================
-- PAYMENTS
-- References payment_method_tbl (lookup) by ID.
-- =====================================
CREATE TABLE payment_tbl (
    payment_id        INT AUTO_INCREMENT PRIMARY KEY,
    transaction_id    INT NOT NULL,
    payment_method_id INT NOT NULL,   -- FK to payment_method_tbl
    amount            DECIMAL(10,2) NOT NULL,
    status_id         INT NOT NULL,
    payment_date      DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (transaction_id)    REFERENCES transaction_tbl(transaction_id),
    FOREIGN KEY (payment_method_id) REFERENCES payment_method_tbl(payment_method_id),
    FOREIGN KEY (status_id)         REFERENCES status_tbl(status_id)
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

    UNIQUE KEY uq_favorite (account_id, user_id, artist_id),

    FOREIGN KEY (account_id) REFERENCES account_tbl(account_id),
    FOREIGN KEY (user_id)    REFERENCES user_tbl(user_id),
    FOREIGN KEY (artist_id)  REFERENCES artist_tbl(artist_id)
);

-- =====================================
-- CONVERSATIONS
-- =====================================
CREATE TABLE conversation_tbl (
    conversation_id INT AUTO_INCREMENT PRIMARY KEY,
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================
-- MESSAGES
-- =====================================
CREATE TABLE message_tbl (
    message_id          INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id     INT NULL,
    sender_account_id   INT NOT NULL,
    receiver_account_id INT NOT NULL,
    message_content     TEXT NOT NULL,
    sent_at             DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status_id           INT NOT NULL,

    FOREIGN KEY (conversation_id)     REFERENCES conversation_tbl(conversation_id),
    FOREIGN KEY (sender_account_id)   REFERENCES account_tbl(account_id),
    FOREIGN KEY (receiver_account_id) REFERENCES account_tbl(account_id),
    FOREIGN KEY (status_id)           REFERENCES status_tbl(status_id)
);

-- =====================================
-- IMAGES
-- =====================================
CREATE TABLE image_tbl (
    image_id      INT AUTO_INCREMENT PRIMARY KEY,
    image_url     VARCHAR(2048) NOT NULL,
    image_type_id INT NOT NULL,
    user_id       INT NULL,
    artist_id     INT NULL,
    commission_id INT NULL,
    uploaded_at   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (image_type_id) REFERENCES image_type_tbl(image_type_id),
    FOREIGN KEY (user_id)       REFERENCES user_tbl(user_id)       ON DELETE CASCADE,
    FOREIGN KEY (artist_id)     REFERENCES artist_tbl(artist_id)   ON DELETE CASCADE,
    FOREIGN KEY (commission_id) REFERENCES commission_tbl(commission_id) ON DELETE CASCADE
);

-- =====================================
-- ARTWORKS
-- =====================================
CREATE TABLE artworks_tbl (
    artwork_id  INT AUTO_INCREMENT PRIMARY KEY,
    image_id    INT NOT NULL UNIQUE,
    title       VARCHAR(255) NOT NULL DEFAULT 'Untitled',
    description TEXT NULL,
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (image_id) REFERENCES image_tbl(image_id) ON DELETE CASCADE
);

-- =====================================
-- PORTFOLIO
-- =====================================
CREATE TABLE portfolio_tbl (
    portfolio_id INT AUTO_INCREMENT PRIMARY KEY,
    artist_id    INT NOT NULL,
    title        VARCHAR(255) NOT NULL,
    description  TEXT,
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (artist_id) REFERENCES artist_tbl(artist_id) ON DELETE CASCADE
);

CREATE TABLE portfolio_image_tbl (
    portfolio_image_id INT AUTO_INCREMENT PRIMARY KEY,
    portfolio_id       INT NOT NULL,
    image_url          VARCHAR(2048) NOT NULL,

    FOREIGN KEY (portfolio_id) REFERENCES portfolio_tbl(portfolio_id) ON DELETE CASCADE
);

-- =====================================
-- REVIEWS
-- One review per completed commission (enforced by UNIQUE on commission_id).
-- =====================================
CREATE TABLE review_tbl (
    review_id           INT AUTO_INCREMENT PRIMARY KEY,
    artist_id           INT NOT NULL,
    reviewer_account_id INT NOT NULL,
    commission_id       INT NULL,
    rating              TINYINT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment             TEXT,
    created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY uq_review_commission (commission_id),

    FOREIGN KEY (artist_id)           REFERENCES artist_tbl(artist_id)   ON DELETE CASCADE,
    FOREIGN KEY (reviewer_account_id) REFERENCES account_tbl(account_id) ON DELETE CASCADE,
    FOREIGN KEY (commission_id)       REFERENCES commission_tbl(commission_id) ON DELETE SET NULL
);