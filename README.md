# Food recipe Database

The Food Recipe Database is a web-based application developed using PHP, MySQL, HTML, CSS, JavaScript, and Ajax.  
It allows users to browse and search food recipes online, while administrators can manage recipes and ingredients.



## Login Credentials

### Admin Account
- Username: admin
- Password: admin123
- Role: Admin

### User Account
- Username: user
- Password: user123
- Role: User



## Setup Instructions

1. Install **Xampp**  on your system.
2. Start **Apache** and **MySQL** services.
3. Copy the project folder **food_recipe_database** into:
    C:\xampp\htdocs\FullStack\
4. Open your browser and go to **phpMyAdmin**.
5. Create a new database named:
    food_recipe_database
6. Import the provided SQL file into the database.
7. Open a browser and visit:
    http://localhost/Sem3/FullStack/food_recipe_database.


## Features Implemented

### User Features
- User registration and login
- View list of food recipes
- View detailed recipe information
- Search recipes by name
- Search recipes by ingredients
- Responsive user interface

### Admin Features
- Admin login authentication
- Add new recipes
- Edit existing recipes
- Delete recipes
- Upload recipe images
- Manage ingredients
- View all recipes


# Security Features

- **SQL Injection Protection** using prepared statements (PDO / MySQLi)
- **XSS Protection** using output escaping
- **Session-based authentication**
- **Role-based access control** (Admin/User)

## Ajax Implementation

- Live ingredient search using Ajax
- Recipe suggestions without page reload
- Faster and smoother user experience

## Responsive Design

- Mobile-friendly layout
- CSS media queries for responsiveness


## Known Issues

- Passwords are stored without hashing
- No password reset functionality
- Limited validation on image uploads
- UI can be further improved for smaller screens

## Technologies Used

- PHP
- MySQL
- HTML
- CSS
- JavaScript
- Ajax
- XAMPP
- Git & GitHub


## Author

- **Name:** Supriya Paudel  
- **Project Type:** Academic / Learning Project  














