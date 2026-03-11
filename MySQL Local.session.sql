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