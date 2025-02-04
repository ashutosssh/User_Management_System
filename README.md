# User Management System

A secure web application for user management with authentication and CRUD operations.

## Features
- User registration with validation
- Secure login with session management
- Profile management (view/update)
- Account deletion
- CSRF protection
- Password hashing
- Client-side & server-side validation
- Rate limiting
- Responsive design

## Requirements
- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)

## Installation
1. Import the database schema:
   ```bash
   mysql -u root -p < users.sql
2. Configure database credentials in db.php
3. Set proper file permissions:
    chmod -R 755 ./
4. Configure web server to serve the application

## Security Features

- Prepared statements for SQL injection prevention

- Password hashing with bcrypt

- CSRF tokens for all forms

- Session security headers

- Rate limiting on login attempts

- Content Security Policy

- Input validation & sanitization

- Secure cookie settings

## File Structure
user-management-system/
├── templates/
│   ├── footer.php
│   ├── header.php
├── uploads/                 # Stores uploaded profile pictures
├── db.php                   # Database connection
├── error_handler.php        # Error management logic
├── dashboard.php            # User dashboard
├── delete_account.php       # Delete user account logic
├── goodbye.php              # Goodbye page after account deletion
├── index.php                # Homepage
├── login.php                # User login page
├── logout.php               # Logout script
├── register.php             # User registration page
├── update_profile.php       # Profile update page
├── upload_profile_picture.php # Upload logic for profile pictures
├── styles.css               # Main stylesheet
├── script.js                # JavaScript logic
├── validation.js            # Form validation
└── README.md                # Documentation

## How to Use

   - Register:
        Fill out the registration form to create an account.
   - Log In:
        Use your credentials to access the dashboard.
    -Update Profile:
        Modify profile information or upload a picture.
    -Delete Account:
        Remove your account permanently if needed.
## Future Enhancements

   - Add email verification for registration.
   - Include a role-based access control system (e.g., admin panel).
   - Integrate a password recovery system.

## License
This project is licensed under the MIT License.

## Author

Developed by Ashutosh Rijal.
Feel free to reach out at ashutoshrizzal124@gmail.com for feedback or questions.
