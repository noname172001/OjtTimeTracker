CREATE TABLE admin (
    admin_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_password VARCHAR(50) NOT NULL 
);


CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_email VARCHAR(100) NOT NULL UNIQUE,
    user_password VARCHAR(50) NOT NULL,
    user_location VARCHAR(100),
    user_school VARCHAR(100),
    user_total_hours_required INT NOT NULL
);


CREATE TABLE user_logs (
    id_logs INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    log_date DATE NOT NULL,
    time_in TIME,
    time_out TIME,
    total_number_of_hours_a_day DECIMAL(5, 2),
    hours_completed DECIMAL(10, 2),
    remaining_hours DECIMAL(10, 2),
    total_number_of_days INT,

    CONSTRAINT fk_user 
        FOREIGN KEY (user_id) 
        REFERENCES users(user_id) 
        ON DELETE CASCADE
);


ALTER TABLE admin
ADD admin_email VARCHAR(100) NOT NULL;


INSERT INTO admin (admin_email, admin_password)
VALUES (
    "admin",
    "1234"
  );


ALTER TABLE users DROP INDEX user_email;


INSERT INTO users (user_name, user_email, user_password, user_location, user_school, user_total_hours_required)
VALUES (
    "Jean Claudette Pena",
    "jean.pena@omegahms.com",
    "jeanpena123",
    "Cebu",
    "Cebu Technological University",
    "729"
);


ALTER TABLE user DROP INDEX user_email;

INSERT INTO users (user_name, user_email, user_password, user_location, user_school, user_total_hours_required)
VALUES (
    "Aubrey",
    "aubrey.lariosa@omegahms.com",
    "aubrey123",
    "Cebu",
    "Cebu Technological University",
    "729"
);

CREATE TABLE log_in (
"log_id" INT AUTO_INCREMENT PRIMARY KEY,
"user_emai;" INT NOT NULL,
"user_password"
);

ALTER TABLE users MODIFY user_password VARCHAR(255);

-----------------------------------------------------------------------------------------

DROP TABLE IF EXISTS log_in;
DROP TABLE IF EXISTS user_logs;
DROP TABLE IF EXISTS admin;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS login;
DROP TABLE IF EXISTS timelog;


CREATE TABLE users (
    user_id       INT AUTO_INCREMENT PRIMARY KEY,
    first_name    VARCHAR(100),
    middle_name   VARCHAR(100),
    last_name     VARCHAR(100),
    school        VARCHAR(150),
    total_no_of_hrs_required INT,
    location      VARCHAR(100),
    address       VARCHAR(255),
    mobile_no     VARCHAR(20)
);


CREATE TABLE login (
    login_id  INT AUTO_INCREMENT PRIMARY KEY,
    user_id   INT NOT NULL,
    email     VARCHAR(100) NOT NULL UNIQUE,
    password  VARCHAR(255) NOT NULL,
    role      ENUM('admin', 'intern') NOT NULL,

    CONSTRAINT fk_login_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
);


CREATE TABLE timelog (
    timelog_id  INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    curr_date   DATE NOT NULL,
    time_in     TIME,
    time_out    TIME,
    no_of_hrs_day DECIMAL(5, 2),

    CONSTRAINT fk_timelog_user
        FOREIGN KEY (user_id)
        REFERENCES users(user_id)
        ON DELETE CASCADE
);


INSERT INTO users (first_name, school, location, address)
VALUES ('Admin', 'Omega Healthcare', 'Cebu', 'Philippines');

INSERT INTO login (user_id, email, password, role)
VALUES (LAST_INSERT_ID(), 'admin@ojttimetracker.com', 'admin1234', 'admin');

