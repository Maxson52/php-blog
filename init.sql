-- Create users
INSERT INTO `users` (`name`, `email`, `password`, `role`)
VALUES
    ('John', 'john@john.com', '$2y$10$jfDRQiUCi0Hx2myHryYWa.bSWSiVjmoj2m5NhI8v9x8DTurZeyS8.', 'user'),
    ('Jane', 'janedoe@jane.com', '$2y$10$jfDRQiUCi0Hx2myHryYWa.bSWSiVjmoj2m5NhI8v9x8DTurZeyS8.', 'user'),
    ('Max', 'max@max.com', '$2y$10$jfDRQiUCi0Hx2myHryYWa.bSWSiVjmoj2m5NhI8v9x8DTurZeyS8.', 'admin');

-- Create categories
INSERT INTO `categories` (`name`)
VALUES
    ('Recipe'),
    ('Review'),
    ('Tips & Tricks'),
    ('Other')
    ;

-- Create posts
INSERT INTO `posts` (`title`, `content`, `category_id`, `author_id`)
VALUES
    ('Classic Chocolate Chip Cookies', "
If you're looking for a timeless cookie recipe that everyone will love, you can't go wrong with classic chocolate chip cookies. Here's how to make them:

Ingredients:

<ul>
<li>2 1/4 cups all-purpose flour</li>
<li>1 tsp baking soda</li>
<li>1 tsp salt</li>
<li>1 cup unsalted butter, at room temperature</li>
<li>3/4 cup granulated sugar</li>
<li>3/4 cup brown sugar</li>
<li>2 large eggs</li>
<li>2 tsp vanilla extract</li>
<li>2 cups semisweet chocolate chips</li>
</ul>

Instructions:

<ol>
<li>Preheat your oven to 375°F (190°C). Line a baking sheet with parchment paper.</li>
<li>In a medium bowl, whisk together the flour, baking soda, and salt.</li>
<li>In a large bowl, beat the butter, granulated sugar, and brown sugar until creamy. Add the eggs and vanilla extract, and beat until well combined.</li>
<li>Add the flour mixture to the butter mixture, and stir until just combined. Fold in the chocolate chips.</li>
<li>Drop tablespoonfuls of dough onto the prepared baking sheet, spacing them about 2 inches apart.</li>
<li>Bake for 10-12 minutes, or until the cookies are golden brown. Allow to cool for a few minutes on the baking sheet, then transfer to a wire rack to cool completely.</li>
<li>Enjoy!</li>
</ol>
    ", 1, 1),
    ('The Perfect Omlette', "
Omelettes are a great way to start your day, and they're also a great way to use up leftover ingredients. Here's how to make a perfect omelette:

Ingredients:

<ul>
<li>2 large eggs</li>
<li>1 tbsp milk</li>
<li>1/2 tsp salt</li>
<li>1/4 tsp pepper</li>
<li>1 tbsp butter</li>
<li>1/2 cup shredded cheese</li>
<li>1/2 cup chopped vegetables</li>
</ul>

Instructions:

<ol>
<li>Whisk together the eggs, milk, salt, and pepper in a small bowl.</li>
<li>Heat the butter in a small nonstick skillet over medium heat. Pour in the egg mixture, and cook, stirring occasionally, until the eggs are set.</li>
<li>Sprinkle the cheese and vegetables over the top of the omelette, and fold in half. Cook for 1-2 minutes, or until the cheese is melted.</li>
<li>Enjoy!</li>
</ol>
    ", 1, 2),
    ('Bananas Are AMAZING', '<p>I think bananas are by far the best fruit there is because of their delicous taste, and they go good in baked goods, and they go good in ice cream. Moreover, bananas are the preferred food of everyones fav animal - MONKEYS', 2, 1);

-- Create comments
INSERT INTO `comments` (`content`, `author_id`, `post_id`)
VALUES
    ('Great article!', 1, 1),
    ('I disagree with your opinion.', 2, 2),
    ('I loved this one!', 1, 3);
