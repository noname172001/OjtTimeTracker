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

INSERT INTO timelog (user_id, curr_date, time_in, time_out, no_of_hrs_day)
VALUES ("3", '2024-06-01', '08:00:00', '17:00:00', "9.00");

INSERT INTO timelog (user_id, curr_date, time_in, time_out, no_of_hrs_day)
VALUES ("3", '2025-05-05', '07:00:00', '17:00:00', "9.00");

