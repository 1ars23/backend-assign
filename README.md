# Backend Assignment

## Overview

This project is a Laravel-based backend application designed to manage projects, users, and timesheets. The application allows users to log their timesheets, assign them to projects, and manage user and project data.

## Prerequisites

Before you begin, ensure you have the following installed:

-   PHP (>= 8.0)
-   Composer
-   MySQL or another database supported by Laravel
-   Node.js (optional, for front-end development)

## Steps to Run the Laravel Project

1. **Clone the Repository**

    ```bash
    git clone https://github.com/1ars23/backend-assign.git

    cd backend-assignment
    ```

2. **Install Composer Dependencies**
   Make sure you have Composer installed. Then run:

    ```bash
    composer install
    ```

3. **Set Up the Environment File**
   Copy the `.env.example` file to `.env` and update the necessary configurations (database, app URL, etc.):

    ```bash
    cp .env.example .env
    ```

4. **Generate the Application Key**
   Run the following command to generate the application key:

    ```bash
    php artisan key:generate
    ```

5. **Run Migrations**
   To set up the database tables, run:

    ```bash
    php artisan migrate
    ```

6. **Seed the Database (Optional)**
   If you have seeders set up and want to populate your database with sample data, run:

    ```bash
    php artisan db:seed
    ```

7. **Serve the Application**
   You can serve the application using the built-in PHP server:

    ```bash
    php artisan serve
    ```

    The application will be accessible at `http://localhost:8000`.

## Demo Credentials

You can use these credentials for login after seed:

```bash
jane.smith@example.com
Secret123!@#
```

## API Swagger Documentation

API documentation is available via Swagger. You can access it by navigating to:

```
http://localhost:8000/api/docs
```

Ensure that you have configured Swagger correctly in your application to generate the documentation.

## Additional Information

-   For any issues, please refer to the [issues section](https://github.com/1ars23/backend-assign/issues) of the repository.
-   Contributions are welcome! Please open an issue or submit a pull request.
