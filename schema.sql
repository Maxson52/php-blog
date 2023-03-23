CREATE TABLE `users`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` ENUM('user', 'mod', 'admin') NOT NULL DEFAULT 'user'
);

CREATE TABLE `posts`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `content` TEXT NOT NULL,
    `category` ENUM('recipe', 'review', 'advice'),
    `created_at` DATETIME NULL,
    `author_id` INT NOT NULL
);

CREATE TABLE `comments`(
    `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `content` TEXT NOT NULL,
    `created_at` DATETIME NULL,
    `author_id` INT NOT NULL,
    `post_id` INT NOT NULL
);


ALTER TABLE
    `comments` ADD CONSTRAINT `comments_post_id_foreign` FOREIGN KEY(`post_id`) REFERENCES `posts`(`id`);
ALTER TABLE
    `posts` ADD CONSTRAINT `posts_author_id_foreign` FOREIGN KEY(`author_id`) REFERENCES `users`(`id`);
ALTER TABLE
    `comments` ADD CONSTRAINT `comments_author_id_foreign` FOREIGN KEY(`author_id`) REFERENCES `users`(`id`);