CREATE TABLE `users`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(33) NOT NULL,
    `email` VARCHAR(33) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('deleted', 'user', 'admin') NOT NULL DEFAULT 'user'
);

CREATE TABLE `posts`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(33) NOT NULL,
    `content` TEXT NOT NULL,
    `visible` BOOLEAN NOT NULL DEFAULT TRUE,
    `category_id` INT,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `author_id` INT NOT NULL
);

CREATE TABLE `comments`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `content` TEXT NOT NULL,
    `visible` BOOLEAN NOT NULL DEFAULT TRUE,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `author_id` INT NOT NULL,
    `post_id` INT NOT NULL
);

CREATE TABLE `categories`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(33) NOT NULL,
    `visible` BOOLEAN NOT NULL DEFAULT TRUE
);


ALTER TABLE
    `comments` ADD CONSTRAINT `comments_post_id_foreign` FOREIGN KEY(`post_id`) REFERENCES `posts`(`id`);
ALTER TABLE
    `posts` ADD CONSTRAINT `posts_author_id_foreign` FOREIGN KEY(`author_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `comments` ADD CONSTRAINT `comments_author_id_foreign` FOREIGN KEY(`author_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `posts` ADD CONSTRAINT `posts_category_id_foreign` FOREIGN KEY(`category_id`) REFERENCES `categories`(`id`);