create database artovia_db;

use artovia_db;

create table user_tbl (
    account_id int not null auto_increment,
    role varchar(50) not null,
    username varchar(100) not null,
    password varchar(255) not null,
    acc_creation_date date not null,
    last_name varchar(100) not null,
    first_name varchar(100) not null,
    account_status varchar(10) not null,
    card_number varchar(20) null,
    middle_name varchar(100) null,
    constraint pk_user primary key (account_id)
);

create table artist_tbl (
    artist_id int not null auto_increment,
    role varchar(50) not null,
    username varchar(100) not null,
    password varchar(255) not null,
    acc_creation_date date not null,
    last_name varchar(100) not null,
    first_name varchar(100) not null,
    card_number varchar(20) null,
    account_status varchar(10) not null,
    start_at decimal(10,2) not null,
    constraint pk_artist primary key (artist_id)
);

create table administrator (
    admin_id int not null auto_increment,
    username varchar(100) not null,
    password varchar(255) not null,
    role varchar(50) not null,
    constraint pk_administrator primary key (admin_id)
);

create table hired_artist (
    hire_id int not null auto_increment,
    artist_id int,
    admin_id int,
    hire_date date not null,
    status varchar(50) not null,
    constraint pk_hired_artist primary key (hire_id),
    constraint fk_hired_artist_artist foreign key (artist_id) references artist_tbl(artist_id),
    constraint fk_hired_artist_admin foreign key (admin_id) references administrator(admin_id)
);

create table commission_tbl (
    commission_id int not null auto_increment,
    user_id int,
    artist_id int,
    description text null,
    status varchar(50) not null,
    commission_date date not null,
    price decimal(10,2) not null,
    constraint pk_commission primary key (commission_id),
    constraint fk_commission_user foreign key (user_id) references user_tbl(account_id),
    constraint fk_commission_artist foreign key (artist_id) references artist_tbl(artist_id)
);

create table transaction_tbl (
    transaction_id int not null auto_increment,
    commission_id int,
    user_id int,
    artist_id int,
    total_amount decimal(10,2) not null,
    transaction_date date not null,
    status varchar(50) not null,
    constraint pk_transaction primary key (transaction_id),
    constraint fk_transaction_commission foreign key (commission_id) references commission_tbl(commission_id),
    constraint fk_transaction_user foreign key (user_id) references user_tbl(account_id),
    constraint fk_transaction_artist foreign key (artist_id) references artist_tbl(artist_id)
);

create table payment_tbl (
    payment_id int not null auto_increment,
    transaction_id int,
    amount decimal(10,2) not null,
    payment_method varchar(50) not null,
    status varchar(50) not null,
    payment_date date not null,
    constraint pk_payment primary key (payment_id),
    constraint fk_payment_transaction foreign key (transaction_id) references transaction_tbl(transaction_id)
);

create table favorites_table (
    favorite_id int not null auto_increment,
    user_id int,
    artist_id int,
    date_added date not null,
    constraint pk_favorites primary key (favorite_id),
    constraint fk_favorites_user foreign key (user_id) references user_tbl(account_id),
    constraint fk_favorites_artist foreign key (artist_id) references artist_tbl(artist_id)
);

create table message_box (
    message_id int not null auto_increment,
    sender_id int,
    receiver_id int,
    message_content text not null,
    sent_at datetime not null,
    status varchar(20) not null,
    constraint pk_message_box primary key (message_id),
    constraint fk_message_sender foreign key (sender_id) references user_tbl(account_id),
    constraint fk_message_receiver foreign key (receiver_id) references artist_tbl(artist_id)
);

-- new addition, query this bayorn --

CREATE TABLE commission_request_tbl (
    request_id     INT NOT NULL AUTO_INCREMENT,
    commission_id  INT NOT NULL,
    artist_id      INT NOT NULL,
    message        TEXT NULL,
    status         VARCHAR(50) NOT NULL DEFAULT 'pending',
    requested_at   DATE NOT NULL,
    CONSTRAINT pk_commission_request PRIMARY KEY (request_id),
    CONSTRAINT fk_cr_commission FOREIGN KEY (commission_id) REFERENCES commission_tbl(commission_id),
    CONSTRAINT fk_cr_artist     FOREIGN KEY (artist_id)     REFERENCES artist_tbl(artist_id)
);


ALTER TABLE commission_tbl DROP FOREIGN KEY fk_commission_artist;
ALTER TABLE commission_tbl MODIFY artist_id INT NULL DEFAULT NULL;

--query this -charles babbage--
alter table user_tbl add Email varchar(255);
alter table artist_tbl add Email varchar(255);
alter table administrator add Email varchar(255);
alter table administrator add Phone varchar(20);

--image table ni jay-r--
create table image_tbl (
    image_id INT NOT NULL AUTO_INCREMENT,
    image_url VARCHAR(2048) NOT NULL,               
    image_type VARCHAR(50) NOT NULL,               
    user_id INT NULL,                              
    artist_id INT NULL,                             
    commission_id INT NULL,                 
    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_image PRIMARY KEY (image_id),
    CONSTRAINT fk_image_user FOREIGN KEY (user_id) REFERENCES user_tbl(account_id) ON DELETE CASCADE,
    CONSTRAINT fk_image_artist FOREIGN KEY (artist_id) REFERENCES artist_tbl(artist_id) ON DELETE CASCADE,
    CONSTRAINT fk_image_commission FOREIGN KEY (commission_id) REFERENCES commission_tbl(commission_id) ON DELETE CASCADE
);