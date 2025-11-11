# CIABOC - Case Traking System

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=for-the-badge&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-15-336791?style=for-the-badge&logo=postgresql)
![License](https://img.shields.io/badge/license-MIT-blue.svg?style=for-the-badge)


A feature-rich web application built with **Laravel 11** to provide seamless payment experiences. It uses **PostgreSQL** as its database and includes powerful third-party libraries for enhanced functionality.

- **Project Start Date:** 2025-11-11

## 🛠️ Tech Stack

-   **Backend:** Laravel 12, PHP 8.2+
-   **Database:** PostgreSQL
-   **Frontend:** React, Vite
-   **DevOps:** Git, Composer

---

## 🚀 Getting Started

Follow these instructions to get a copy of the project up and running on your local machine for development and testing purposes.

### Prerequisites

Make sure you have the following software installed on your system:
-   PHP >= 8.4
-   Composer
-   Node.js & npm
-   A local PostgreSQL server

### Installation Guide

1.  **Clone the Repository**
    Open your terminal and clone the project repository.

    ```bash
    git clone [https://github.com/sachin56/Payment-App.git](https://github.com/sachin56/Payment-App.git)
    cd payment-app
    ```

2.  **Install PHP Dependencies**
    Install all the required backend packages using Composer.

    ```bash
    composer install
    ```

3.  **Create Environment File**
    Copy the example environment file and generate a unique application key.

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Configure Your Database**
    Open the `.env` file you just created and update the database connection details to match your local PostgreSQL setup. You will need to create a new database named `payment_app_db`.

    ```env
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432 # The default port for PostgreSQL is 5432
    DB_DATABASE=payment_app_db
    DB_USERNAME=postgres
    DB_PASSWORD=your_secret_password # <-- IMPORTANT: Change this to your actual password
    ```

5.  **Run Database Migrations**
    This command will create all the necessary tables in your database.

    ```bash
    php artisan migrate
    ```

6.  **Install Frontend Dependencies**
    Install the necessary Node.js packages.

    ```bash
    npm install
    ```

7.  **Compile Frontend Assets**
    Compile the assets for development.

    ```bash
    npm run dev
    ```

8.  **Run the Application**
    You're all set! Start the Laravel development server.

    ```bash
    php artisan serve
    ```

    Your application will now be running at **[http://127.0.0.1:8000](http://127.0.0.1:8000)**.

9.  **Run the Queue**
    You're all set! Start the Laravel Queue.

    ```bash
    php artisan queue:work 
    ```

    Your application will now run queue.

10.  **Run the Shedule**
    You're all set! Start the Laravel Queue.

    ```bash
    php artisan daily:payout 
    ```

    Your application will now run Shedule

---

## 🧪 Screen Shots

### Upload Screen
### Upload Screen
[View Upload Screen](https://prnt.sc/wiu-PNZh-RnJ)

### Job Function Screen
[View Job Function Screen](https://prnt.sc/SypudGSCCV_h)



