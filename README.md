

# Getting Started with the Laravel News Aggregator Project

This project is a Laravel-based application designed to fetch, display, and manage article data from the [Laravel News Blog](https://laravel-news.com/blog). It is built with modern web development principles, incorporating key design methodologies such as Object-Oriented Programming (OOP) and the SOLID principles to ensure scalability, maintainability, and clean code practices. Combining robust backend functionality with a user-friendly interface, this project aims to make web scraping and article management seamless and efficient for developers and users alike.

## Prerequisites

Before getting started, ensure your system is equipped with the following software and tools:

- **PHP**: Version 8.1 or higher is required to support Laravel 11.x and its advanced features.
- **Laravel**: The framework required for this project, version 11.x.
- **Composer**: The latest version is necessary for managing PHP packages and dependencies efficiently.
- **MySQL**: Version 8.0 or higher to handle database operations, ensuring robust data management capabilities.
- **Node.js**: Version 16.x or newer to support modern JavaScript tooling for frontend development.
- **npm**: The latest version for managing and building frontend dependencies effectively.
- **Git**: The latest version to clone the project repository and manage version control.

## Setup Instructions

Follow these detailed steps to set up the project locally:

1. **Clone the Repository**

   Begin by cloning the repository to your local machine:

   ```bash
   git clone <repository_url>
   cd <repository_name>
   ```

2. **Install Dependencies**

   Install all necessary dependencies for both backend and frontend:

   ```bash
   composer install
   npm install
   ```

3. **Configure Environment Variables**

   Configure the application environment by creating and editing a `.env` file:

   ```bash
   cp .env.example .env
   ```

   Update the `.env` file with your database and application settings. For instance:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=laravel_news
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Generate Application Key**

   Create a unique application encryption key for securing sensitive data:

   ```bash
   php artisan key:generate
   ```

5. **Run Database Migrations**

   Set up the database by running migrations to create the necessary tables:

   ```bash
   php artisan migrate
   ```


6. **Start the Development Server**

   Launch the Laravel development server and access the application locally:

   ```bash
   php artisan serve
   ```

   By default, the server will be accessible at `http://127.0.0.1:8000`.


## Application Features

The Laravel News Aggregator Project is designed to fetch and display articles tagged as "news" from Laravel News within the past four months. Key features include:

- **Article Display**:
  - Articles are presented in a user-friendly table format.
  - Fields include:
    - Publication date formatted as `d/m/Y`
    - Title displayed as a clickable link to the original article
    - Author name
    - Tags listed as a comma-separated string

- **Sorting Options**:
  - Default sorting is by author name (alphabetically).
  - Manual sorting is available for title and publication date.

- **Database Storage**:
  - Articles are fetched once and stored in the database for efficient retrieval.
  - Page reloads display cached data from the database, reducing load times.

- **Article Update Command**:
  - Update the database with the latest articles using:

   ```bash
   php artisan articles:fetch
   ```

## Additional Commands

To maintain application performance and resolve common issues, the following commands may be helpful:

- **Clear Application Cache**:

   ```bash
   php artisan cache:clear
   ```

- **Restart Queues**:

   ```bash
   php artisan queue:restart
   ```

- **Optimize the Application**:

   ```bash
   php artisan optimize
   ```

## Notes and Recommendations

- **User Interface**:
  - The interface is functional and adaptive, Bootstrap was used to improve aesthetics and ease of use.
- **Code Quality and Best Practices**:
  - The project adheres to Laravel’s best practices, emphasizing modular architecture, clean code, and SOLID principles.
- **Expandability**:
  - The codebase is designed for future enhancements, such as advanced filtering options, additional sorting mechanisms, or more sophisticated UI elements.


## Troubleshooting

1. **Permission Issues**:

   Ensure appropriate permissions for `storage` and `bootstrap/cache` directories:

   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

2. **Database Connectivity**:

   Verify database credentials in the `.env` file if migrations or database operations fail. Ensure the database server is running and accessible.


3. **Frontend Dependency Issues**:

   Ensure frontend dependencies are up to date by running:

   ```bash
   npm install
   ```

---

For further guidance, refer to the [official Laravel documentation](https://laravel.com/docs), which provides comprehensive insights into Laravel’s features, tools, and best practices.

