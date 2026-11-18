# Student Project Management System (SPMS)
PHP + MySQL + HTML/CSS/JS project.

## Run in 4 steps (XAMPP + VS Code)
1. Copy the `spms` folder into `C:\xampp\htdocs\`
2. Open XAMPP Control Panel -> Start **Apache** and **MySQL**
3. Open http://localhost/phpmyadmin -> **Import** -> choose `spms/database.sql` -> Go
4. Open http://localhost/spms/

## Logins
- Admin (guide): `admin@spms.com` / `admin123` (created automatically)
- Student: click "Create an account" on the login page

## Features
- Secure login/registration (password hashing, sessions, CSRF tokens, prepared statements)
- Student: create/edit/delete own projects, track status & progress, see guide feedback and grade
- Admin: view all projects, search/filter, give feedback and grade, manage students
- Dashboard with animated, filterable stats, status breakdown, portfolio progress and a personalized workspace summary
- Live student-directory search, remembered light/dark theme, and password visibility controls
- Keyboard shortcut Ctrl+K (or Cmd+K on Mac) to focus directory search; Esc clears it
- Responsive UI (works on mobile)

## Files
- config.php - DB connection + helper functions (edit DB password here if needed)
- index.php / register.php / logout.php - authentication
- dashboard.php, projects.php, project_form.php, project_view.php, project_delete.php, students.php
- includes/header.php, includes/footer.php - shared layout
- assets/style.css, assets/app.js - design and interactions
- database.sql - tables
