📚 Book Management System
A full-featured book management web application built with Laravel and Vue.js (Inertia.js). This application allows users to manage their book collection with a modern, responsive interface.

✨ Features
Current Features
✅ User Authentication - Register, Login, Logout using Laravel Breeze

✅ CRUD Operations - Create, Read, Update, Delete books

✅ Dashboard - View statistics, recent books, and quick actions

✅ Search Functionality - Search books by title or author

✅ Responsive Design - Works on desktop, tablet, and mobile

✅ Modern UI - Clean interface with Tailwind CSS

✅ Flash Messages - Success and error notifications

Technologies Used
Backend: Laravel 13.19.0 (PHP 8.4.23)

Frontend: Vue.js 3, Inertia.js

Database: MySQL

Styling: Tailwind CSS

Authentication: Laravel Breeze

📋 Table of Contents
Installation

Configuration

Database Setup

Running the Application

Application Structure

Features Overview

Screenshots

API Routes

Troubleshooting

Contributing

License

🚀 Installation
Prerequisites
Make sure you have the following installed:

PHP >= 8.4.23

Composer (PHP package manager)

MySQL (Database)

Node.js >= 18.x (For frontend assets)

NPM or Yarn (Package manager)

Step 1: Clone the Repository
bash
git clone https://github.com/eyuashu06/book-management.git
cd book-management
Step 2: Install PHP Dependencies
bash
composer install
Step 3: Install Frontend Dependencies
bash
npm install
Step 4: Environment Configuration
Copy the example environment file:

bash
cp .env.example .env
Generate an application key:

bash
php artisan key:generate
Step 5: Configure Database
Open .env and update your database credentials:

env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=book_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
Step 6: Run Migrations
bash
php artisan migrate
Step 7: Seed the Database
bash
php artisan db:seed
This will create sample books in your database.

Step 8: Build Frontend Assets
bash
npm run build
⚙️ Configuration
Environment Variables
Here are the key environment variables you might need to customize:

env
# Application
APP_NAME=BookManagement
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=book_management
DB_USERNAME=book_user
DB_PASSWORD=password123

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Mail (for password reset)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
📦 Database Setup
Create Database
bash
# Login to MySQL
mysql -u root -p

# Create database
CREATE DATABASE book_management;

# Create user (optional)
CREATE USER 'book_user'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON book_management.* TO 'book_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
Run Migrations
bash
# Run all migrations
php artisan migrate

# Reset and run migrations
php artisan migrate:fresh

# Reset and seed
php artisan migrate:fresh --seed
🎯 Running the Application
Development Mode
Terminal 1 - Laravel Backend:

bash
php artisan serve
Terminal 2 - Frontend Development:

bash
npm run dev
Now visit: http://localhost:8000

Production Mode
bash
# Build assets
npm run build

# Optimize for production
php artisan optimize

# Start server
php artisan serve --host=0.0.0.0 --port=8000
📁 Application Structure
text
book-management/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── BookController.php      # Book CRUD operations
│   │   │   ├── DashboardController.php # Dashboard logic
│   │   │   └── ProfileController.php   # User profile management
│   │   └── Middleware/
│   └── Models/
│       ├── Book.php                    # Book model
│       └── User.php                    # User model
├── database/
│   ├── factories/
│   │   └── BookFactory.php            # Fake book data generator
│   ├── migrations/
│   │   └── *_create_books_table.php   # Database schema
│   └── seeders/
│       ├── DatabaseSeeder.php
│       └── BookSeeder.php              # Sample book data
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Books/
│   │   │   │   ├── Index.vue          # List all books
│   │   │   │   ├── Create.vue         # Add new book
│   │   │   │   ├── Edit.vue           # Edit book
│   │   │   │   └── Show.vue           # View single book
│   │   │   └── Dashboard.vue          # Dashboard view
│   │   └── Layouts/
│   │       └── AuthenticatedLayout.vue # Main layout
│   └── views/                         # Blade templates (legacy)
├── routes/
│   ├── web.php                        # Web routes
│   └── auth.php                       # Auth routes
├── public/
│   └── index.php                      # Entry point
├── .env                               # Environment config
├── composer.json                      # PHP dependencies
├── package.json                       # Node dependencies
└── README.md                          # This file
🎨 Features Overview
1. Authentication
Login Page:

Email/Password login

Remember me option

Password reset

Register Page:

Name, Email, Password

Password confirmation

Validation rules

Dashboard:

User-specific greeting

Book statistics

Recent books list

Quick action buttons

2. Book Management
Create Book:

Title, Author, ISBN (required)

Published Year (1900 - current year)

Description (optional)

Validation errors displayed

Read Books:

Grid view of all books

Search by title or author

View book details

Responsive layout

Update Book:

Edit any book field

Pre-filled form

Validation

Flash messages

Delete Book:

Confirmation dialog

Soft delete option

3. Search Functionality
Real-time search (with Inertia.js)

Search by title and author

Instant results

Case-insensitive

4. Dashboard
Statistics:

Total books

Total authors

Years covered

Recent Books:

List of 5 most recent books

Quick access

Quick Actions:

Add new book

View all books

📸 Screenshots
Home Page
https://via.placeholder.com/600x400?text=Book+List

Dashboard
https://via.placeholder.com/600x400?text=Dashboard

Add Book Form
https://via.placeholder.com/600x400?text=Add+Book

Edit Book Form
https://via.placeholder.com/600x400?text=Edit+Book

🔧 Troubleshooting
Common Issues and Solutions
1. "Table 'books' doesn't exist"
Solution:

bash
php artisan migrate
2. "Access denied for user"
Solution:
Check your .env file credentials:

env
DB_USERNAME=correct_username
DB_PASSWORD=correct_password
Then clear config:

bash
php artisan config:clear
3. "SQLSTATE[HY000] [1045]"
Solution:
Reset MySQL password:

bash
mysql -u root -p
ALTER USER 'book_user'@'localhost' IDENTIFIED BY 'newpassword';
FLUSH PRIVILEGES;
4. "Vite manifest not found"
Solution:

bash
npm run build
5. "Route not defined"
Solution:

bash
php artisan route:clear
php artisan route:cache
6. "View not found"
Solution:

bash
php artisan view:clear
🧪 Testing
Run Tests
bash
# Run all tests
php artisan test

# Run specific test
php artisan test --filter=BookTest
Test Routes
bash
# List all routes
php artisan route:list

# Test a specific route
php artisan route:list | grep books
📚 API Routes
Public Routes (No Auth Required)
Method	URL	Description
GET	/	View all books
GET	/books/{id}	View single book
Protected Routes (Auth Required)
Method	URL	Description
GET	/dashboard	Dashboard view
GET	/books/create	Show create form
POST	/books	Store new book
GET	/books/{id}/edit	Show edit form
PUT	/books/{id}	Update book
DELETE	/books/{id}	Delete book
🛠️ Development Commands
bash
# Start Laravel development server
php artisan serve

# Start Vite development server
npm run dev

# Build for production
npm run build

# Clear all caches
php artisan optimize:clear

# Run migrations
php artisan migrate

# Reset and seed database
php artisan migrate:fresh --seed

# Generate new model with migration
php artisan make:model Book -m

# Generate new controller
php artisan make:controller BookController --resource

# Generate new seeder
php artisan make:seeder BookSeeder

# Open Tinker
php artisan tinker
🤝 Contributing
Contributions are welcome! Here's how:

Fork the repository

Create a feature branch (git checkout -b feature/AmazingFeature)

Commit your changes (git commit -m 'Add some AmazingFeature')

Push to the branch (git push origin feature/AmazingFeature)

Open a Pull Request

Coding Standards
Follow PSR-12 coding standards

Write meaningful commit messages

Add tests for new features

Update documentation

📝 License
This project is open-sourced software licensed under the MIT license.

👥 Authors
Your Name - github.com/eyuashu06

🙏 Acknowledgments
Laravel - The PHP framework

Vue.js - The progressive JavaScript framework

Inertia.js - The glue between Laravel and Vue

Tailwind CSS - Utility-first CSS framework

📞 Contact
Email: eashenafi82@gmail.com.com

GitHub: github.com/eyuashu06

LinkedIn: linkedin.com/in/eyuel-ashenafi

🎯 Roadmap
Upcoming Features
User-specific books (each user manages their own)

Book categories/genres

Book rating system

Comments and reviews

Export books to CSV/PDF

Bulk import from CSV

Book cover image upload

Advanced search filters

Reading status (Reading, Completed, Plan to read)

Social sharing

⭐ If you found this project helpful, please give it a star on GitHub!

Made with ❤️ using Laravel and Vue.js