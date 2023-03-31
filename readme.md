# 🍔 Fry Me to the Moon 🚀

Welcome to Fry Me to the Moon, a food blog written in PHP where you can find delicious recipes from around the world.

## Features

- ✍️ Write your own articles using a rich text editor.
- 🔍 Search for specific articles by title, content, or author.
- 🍲 Sort recipes by various categories, such as recipes or tips and tricks.
- 🔒 Admin editing to manage and update recipes.
- 📊 Admin statistics about frequency of posts and comments.

## Development

1. Clone the repository: `git clone https://github.com/maxson52/php-blog.git`
2. Import `schema.sql` into MySQL database
3. Start the Tailwind compiler: `npm run dev`

## Vulnerabilities

- ⚠️ Despite using `mysqli_real_escape_string`, Fry Me to the Moon is still vulnerable to SQL injections.
- ⚠️ The site is also vulnerable to session hijacking (no session validation, expiration, etc.).
