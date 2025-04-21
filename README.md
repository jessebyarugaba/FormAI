# AI Form Builder (PHP)

A web application built with PHP that allows users to generate dynamic web forms using natural language descriptions powered by an AI model (via OpenRouter). Generated forms can be saved, shared via unique links, and user submissions can be collected and stored in a database.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT) 

## Features

* **AI-Powered Form Generation:** Describe the form you need in plain English (e.g., "Create a contact form with name, email, and message fields").
* **Dynamic Field Types:** Supports various HTML input types including text, email, textarea, select dropdowns, radio buttons, and checkboxes based on AI interpretation.
* **Customizable AI Provider:** Uses OpenRouter by default, allowing access to various underlying AI models (like Gemini, Claude, Llama, GPT models). Configurable via API endpoint and key.
* **Form Persistence:** Save generated form structures to a database.
* **Shareable Links:** Each saved form gets a unique URL for easy sharing.
* **Submission Collection:** Collect answers submitted through the shared forms and store them in the database.
* **Secure Configuration:** Uses `.env` files for managing sensitive API keys and database credentials.

## Technology Stack

* **Backend:** PHP (Requires version 7.4+ or 8.0+)
* **Database:** MySQL / MariaDB
* **Frontend:** HTML, CSS, Vanilla JavaScript (for minor enhancements like copy link)
* **Dependencies:**
    * [Composer](https://getcomposer.org/) for package management
    * [GuzzleHttp](https://github.com/guzzle/guzzle) for making HTTP requests to the AI API
    * [vlucas/phpdotenv](https://github.com/vlucas/phpdotenv) for loading environment variables

## Prerequisites

Before you begin, ensure you have the following installed:

* PHP (Version 7.4 or higher recommended)
* Composer
* A web server (like Apache or Nginx)
* MySQL or MariaDB database server
* An API Key from [OpenRouter.ai](https://openrouter.ai/) (or another compatible AI service endpoint)

## Installation & Setup

1.  **Clone the Repository:**
    ```bash
    git clone [https://github.com/](https://github.com/)jessebyarugaba/FormAI.git
    cd ai-form-builder-php
    ```

2.  **Install Dependencies:**
    ```bash
    composer install
    ```

3.  **Configure Environment Variables:**
    * Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    * Edit the `.env` file with your specific details:
        * `OPENROUTER_API_KEY`: Your API key from OpenRouter.
        * `OPENROUTER_API_ENDPOINT`: The API endpoint (defaults to OpenRouter chat completions).
        * `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`: Your database connection details.
        * `APP_NAME`, `APP_URL`: Optional details used in API request headers and link generation.
    * **Important:** Never commit your `.env` file to version control! The `.gitignore` file should already be configured to prevent this.

4.  **Database Setup:**
    * Create a database using your preferred MySQL/MariaDB client (e.g., phpMyAdmin, command line).
    * Import the database schema. You can use the SQL commands below or potentially place them in a `.sql` file and import it.

    <details>
    <summary>Click to view Database Schema (SQL)</summary>

    ```sql
    CREATE TABLE forms (
        id INT AUTO_INCREMENT PRIMARY KEY,
        unique_id VARCHAR(16) NOT NULL UNIQUE,
        title VARCHAR(255) NULL,
        form_definition JSON NOT NULL, -- Or TEXT if JSON type is not supported
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE INDEX idx_unique_id ON forms(unique_id);

    CREATE TABLE submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        form_id INT NOT NULL,
        submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        submitter_ip VARCHAR(45) NULL,
        FOREIGN KEY (form_id) REFERENCES forms(id) ON DELETE CASCADE
    );

    CREATE TABLE answers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        submission_id INT NOT NULL,
        field_name VARCHAR(255) NOT NULL,
        field_value TEXT NULL,
        FOREIGN KEY (submission_id) REFERENCES submissions(id) ON DELETE CASCADE
    );

    CREATE INDEX idx_submission_id ON answers(submission_id);
    ```
    </details>

5.  **Web Server Configuration:**
    * Configure your web server (Apache Virtual Host or Nginx server block) to point the document root to the project's public directory (which is the root directory in this setup, containing `index.php`).
    * Ensure the web server has write permissions for logging errors if needed, and potentially for file uploads if you add that feature later.
    * Enable URL rewriting if you plan to implement cleaner URLs later (e.g., Apache's `mod_rewrite`).

## Usage

1.  **Access the Application:** Open your web browser and navigate to the URL where you set up the project (e.g., `http://localhost/ai-form-builder-php/` or your configured virtual host).
2.  **Describe Your Form:** Enter a natural language description of the form you want in the text area provided on `index.php`.
3.  **Generate:** Click the "Generate Form" button. The application will contact the AI API.
4.  **Preview:** A preview of the generated form will be displayed based on the AI's JSON output. The raw JSON response from the AI might also be shown for debugging.
5.  **Save:** If you are satisfied with the preview, enter an optional title and click "Save Form".
6.  **Share:** The application will save the form structure to the database and provide you with a unique, shareable link (e.g., `.../view_form.php?id=xxxxxxxx`). Copy this link.
7.  **Fill:** Anyone with the link can visit it to view and fill out the generated form.
8.  **Submit:** When a user submits the form, their answers are saved to the `submissions` and `answers` tables in the database.
9.  **(Optional) View Submissions:** You would currently need to access the database directly (e.g., via phpMyAdmin) to view submitted answers. A future enhancement could be adding an interface to view submissions.

## Contributing

Contributions are welcome! If you'd like to contribute, please feel free to fork the repository, make your changes, and submit a pull request. For major changes, please open an issue first to discuss what you would like to change.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contact

[Jesse Byarugaba / Morzepay]
Project Link: [https://github.com/jessebyarugaba/FormAI.git](https://github.com/jessebyarugaba/FormAI)