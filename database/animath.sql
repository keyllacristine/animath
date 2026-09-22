CREATE DATABASE animath
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE animath;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar TINYINT NOT NULL DEFAULT 1,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user'
);

CREATE TABLE levels (
    id INT AUTO_INCREMENT PRIMARY KEY
);

CREATE TABLE user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    level_id INT NOT NULL,
    completed BOOLEAN NOT NULL DEFAULT FALSE,
    hits INT NOT NULL DEFAULT 0,
    erros INT NOT NULL DEFAULT 0,

    UNIQUE (user_id, level_id),

    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    FOREIGN KEY (level_id)
        REFERENCES levels(id)
        ON DELETE CASCADE
);

INSERT INTO levels (id) VALUES
(1),
(2),
(3),
(4);

select * from users;