# G2Travel - Tour & Hotel Booking System

G2Travel is a comprehensive web-based platform designed to facilitate seamless booking experiences for travelers. It allows users to browse and book tours and hotel rooms while providing a robust admin panel for managing bookings, users, and content.

##  Features

### User Module
*   **Browse & Search**: Explore available tours and hotel rooms with detailed descriptions, features, and facilities.
*   **Booking System**: Secure booking flow with date selection and immediate confirmation logic.
*   **User Accounts**: Register and login to manage personal profiles and view booking history.
*   **Reviews & Ratings**: Leave feeback on booked rooms/tours.
*   **Contact & Inquiries**: Direct messaging system to contact site administrators.
*   **Responsive Design**: Fully responsive interface built with Bootstrap 5.

### Admin Module
*   **Dashboard**: Real-time analytics showing total bookings, revenue, and user queries.
*   **Booking Management**: View, approve, or cancel bookings. Handle refunds and payment statuses.
*   **Room/Tour Management**: Add, edit, or remove tours and rooms. Manage associated images, facilities, and features.
*   **User Management**: Monitor registered users and handle account verification statuses.
*   **Settings**: Configure site details (Title, About Us) and toggle "Shutdown" mode.
*   **carousel Management**: Update homepage slider images dynamically.

##  Tech Stack

*   **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
*   **Backend**: PHP (Native)
*   **Database**: MySQL
*   **Server**: Apache (via XAMPP/WAMP) or PHP Built-in Server
*   **Payment Integration**: Simulated payment processing flow.

---

##  Project Structure

```
G2Travel/
├── admin/                 # Admin panel module
│   ├── ajax/              # AJAX handlers for admin actions
│   ├── inc/               # Admin-specific includes (db_config, essentials)
│   ├── scripts/           # Admin-specific JS
│   └── ...php             # Admin pages (dashboard, bookings, rooms, etc.)
├── inc/                   # Shared includes (header, footer, links)
├── images/                # Static assets (uploads, site images)
├── css/                   # Global styles
├── vietchill.sql          # Database schema import file
├── index.php              # Homepage
├── rooms.php              # Rooms listing
├── pay_now.php            # Payment processing logic
└── ...                    # Other user-facing pages
```

---

##  Installation & Setup

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/trungdatrung/G2Travel.git
    cd G2Travel
    ```

2.  **Database Setup**
    *   Create a new MySQL database named `g2travel`.
    *   Import the provided SQL file: `vietchill.sql` into the database.

3.  **Configuration**
    *   Open `admin/inc/db_config.php`.
    *   Update the database credentials if necessary:
        ```php
        $hname = 'localhost';
        $uname = 'root';      // Your DB Username
        $pass = '';           // Your DB Password
        $db = 'g2travel';
        ```

4.  **Run the Application**
    *   You can use XAMPP/WAMP or the PHP built-in server.
    *   **Using PHP built-in server**:
        ```bash
        php -S localhost:8000
        ```
    *   Access the site at `http://localhost:8000`.

---

##  Default Credentials

### Admin Account
*   **URL**: `/admin/index.php`
*   **Username**: `holden`
*   **Password**: `12345`

### Test User Account
*   **Username**: `Trung` (or register a new user)
*   **Password**: `12345`

---

##  Security Features
*   **CSRF Protection**: All forms are protected against Cross-Site Request Forgery.
*   **Password Hashing**: User and Admin passwords are hashed using Bcrypt.
*   **Input Sanitization**: All user inputs are sanitized to prevent SQL Injection and XSS attacks.