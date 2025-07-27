# YoPrint Laravel CSV Product Importer

This is a Laravel application designed to handle the asynchronous upload and processing of product data from CSV files, as outlined in the YoPrint Laravel Coding Project Specification.

## Project Overview

The application provides a user interface for uploading CSV files containing product information. These files are then processed in the background to create or update product records in the database. The system is designed to be idempotent, meaning it can handle re-uploads of the same file without creating duplicate entries, and it can also update existing records based on a unique key.

## Key Features

- **CSV File Upload:** A simple interface for users to upload product data.
- **Real-time Status Updates:** The UI displays a list of recent uploads and their processing status (e.g., Pending, Processing, Completed), which updates in real-time.
- **Background Job Processing:** CSV files are processed asynchronously using Laravel's queue system to ensure the application remains responsive.
- **Idempotent & Upsert Functionality:** The import logic handles both the creation of new products and the updating of existing ones based on a `UNIQUE_KEY`.
- **Database Schema:** The application uses a custom schema to store product information and track file upload history.

## Getting Started

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js & NPM
- A database (SQLite is used by default)
- A queue driver (Redis is recommended)

### Installation & Setup

1.  **Clone the repository:**
    ```bash
    git clone <your-repository-url>
    cd yoprint-app
    ```

2.  **Install dependencies:**
    ```bash
    composer install
    npm install
    ```

3.  **Environment Configuration:**
    - Copy the `.env.example` file to `.env`:
      ```bash
      cp .env.example .env
      ```
    - Generate a new application key:
      ```bash
      php artisan key:generate
      ```
    - Configure your database and queue connections in the `.env` file. For a quick start, you can use `sqlite` and `redis`.

4.  **Database Setup:**
    - Create an empty `database.sqlite` file in the `database/` directory.
    - Run the database migrations to create the necessary tables:
      ```bash
      php artisan migrate
      ```

5.  **Compile Frontend Assets:**
    ```bash
    npm run dev
    ```

6.  **Run the Queue Worker:**
    - The queue worker is essential for processing the uploaded files.
    ```bash
    php artisan queue:work
    ```

7.  **Start the Development Server:**
    - In a separate terminal, start the Laravel server:
    ```bash
    php artisan serve
    ```

## How to Use

1.  Open your browser and navigate to the application's URL (typically `http://127.0.0.1:8000`).
2.  Use the form to select and upload a product CSV file (e.g., `yoprint_test_import.csv`).
3.  The file upload will appear in the "Recent Uploads" list. The status will update in real-time as the file is processed by the queue worker.
4.  You can upload the `yoprint_test_updated.csv` file to test the update (upsert) functionality.