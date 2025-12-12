# G2TRAVEL - TRAVEL & TOUR BOOKING WEBSITE (Enhanced)

## Abstract
**G2Travel** is a robust web application designed for booking travel tours and packages. It features a comprehensive **Admin Panel** for management and a user-friendly frontend for customers. This project demonstrates advanced PHP/MySQL development, including secure authentication, AJAX-driven interfaces, and file management.

## Table of Contents
- [Features](#features)
- [Prerequisites](#prerequisites)
- [Installation & Setup](#installation--setup)
- [Usage Guide](#usage-guide)
- [Project Structure](#project-structure)
- [Recent Updates & Fixes](#recent-updates--fixes)
- [Technologies](#technologies)

## Features

### Admin Panel
- **Dashboard**: Real-time overview of bookings, user queries, and analytics.
- **Room/Tour Management**: content-rich editor for adding tour packages, features, facilities, and images.
  - *New*: Updated thumbnail management and "Image" column for quick status checks.
- **Booking Management**: 
  - Manage new bookings (Assign Room, Cancel).
  - **Booking Records**: View history and **Download Receipts** (HTML/PDF-ready view).
- **User Management**: View active users, toggle status, or remove accounts.
- **Carousel & Team**: Drag-and-drop style image uploads for the homepage slider and "About Us" team section.
- **Settings**: Configure site title, contact info, shutdown mode, and more.
- **AJAX Interactions**: All major actions (Delete, Update, Active/Inactive) happen instantly without page reloads.

### User Side
- **Responsive Design**: Fully mobile-compatible layout using Bootstrap 5.
- **Tour Booking**: Filter by date, guest count, and facilities.
- **User Account**: Profile management, booking history, and review system.
- **Real-time Reviews**: Users can rate and review tours they have booked.
- **Multilingual Support**: The public interface is fully translated to English (No hardcoded Vietnamese text).

## Prerequisites
- **PHP** (Version 7.4 or 8.x recommended)
- **MySQL/MariaDB**
- **Web Server** (Apache via XAMPP/MAMP or PHP Built-in Server)

## Installation & Setup

### Option A: Using XAMPP (Standard)
1.  **Download** and install XAMPP.
2.  **Copy** the `G2Travel` folder to `C:\xampp\htdocs\` (Windows) or `/Applications/XAMPP/htdocs/` (Mac).
3.  **Start** Apache and MySQL from the XAMPP Control Panel.
4.  **Database Setup**:
    - Go to `http://localhost/phpmyadmin`.
    - Create a database named `vietchill` (or matching the one in `admin/inc/db_config.php`).
    - Import `vietchill.sql`.
5.  **Run**: Open `http://localhost/G2Travel` in your browser.

### Option B: Using PHP Built-in Server (Lightweight)
1.  **Open Terminal** in the project root directory.
2.  **Start Server**: Run the command:
    ```bash
    php -S localhost:8000
    ```
3.  **Access**: Open `http://localhost:8000` in your browser.
    *Note: Ensure your `admin/inc/db_config.php` points to a running MySQL instance.*

## Usage Guide

### Admin Credentials
To access the backend management system, navigate to `/admin` (e.g., `http://localhost:8000/admin`).
- **Username**: `holden`
- **Password**: `12345`

### Generating Receipts
In the **Booking Records** section of the Admin Panel, click "Download PDF". This opens a printable HTML receipt for the selected booking.

## Project Structure
- `admin/`: Backend logic, AJAX handlers, and UI panels.
- `inc/`: Shared components (Header, Footer, Database Config).
- `images/`: Stores uploaded assets (Rooms, Carousel, Users).
- `ajax/`: Frontend AJAX handlers for booking and searching.
- `*.php`: Frontend pages (index, rooms, about, etc.).

## Recent Updates & Fixes
- **Image Resolution Limit**: Upload limit increased from **2MB to 10MB** for high-quality photos.
- **Realtime UI Updates**: Fixed AJAX bugs where buttons (Active/Inactive, Delete) required a page reload to show changes. They now update instantly.
- **Carousel Stability**: Swapped unreliable `unpkg` CDN links for `jsdelivr`, fixing broken/stacking sliders.
- **Localization**: Removed leftover Vietnamese text hardcoded in `ajax/rooms.php` and `room_details.php`.
- **Layout Fixes**: Corrected overlay glitches on the Homepage Booking Form.

## Technologies
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla + AJAX), Bootstrap 5.
- **Backend**: PHP (Native).
- **Database**: MySQL.
- **Libraries**: 
  - **SwiperJS** (via JSDelivr CDN) for carousels.
  - **Bootstrap Icons** for UI elements.