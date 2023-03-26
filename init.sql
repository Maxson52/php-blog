-- Create users
INSERT INTO `users` (`name`, `email`, `password`, `role`)
VALUES
    ('John', 'john@john.com', '$2y$10$jfDRQiUCi0Hx2myHryYWa.bSWSiVjmoj2m5NhI8v9x8DTurZeyS8.', 'user'),
    ('Jane', 'janedoe@jane.com', '$2y$10$jfDRQiUCi0Hx2myHryYWa.bSWSiVjmoj2m5NhI8v9x8DTurZeyS8.', 'mod'),
    ('Max', 'max@max.com', '$2y$10$jfDRQiUCi0Hx2myHryYWa.bSWSiVjmoj2m5NhI8v9x8DTurZeyS8.', 'admin');

-- Create categories
INSERT INTO `categories` (`name`)
VALUES
    ('Technology'),
    ('Sports'),
    ('Entertainment');

-- Create posts
INSERT INTO `posts` (`title`, `content`, `category_id`, `author_id`)
VALUES
    ('New Technology Trends', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 1, 1),
    ('Sports News', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 2, 2),
    ('Movie Reviews', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 3, 1);

-- Create comments
INSERT INTO `comments` (`content`, `author_id`, `post_id`)
VALUES
    ('Great article!', 1, 1),
    ('I disagree with your opinion.', 2, 2),
    ('I loved this movie!', 1, 3);
