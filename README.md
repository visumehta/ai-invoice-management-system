# AI Invoice Management System

An AI-powered invoice management application that extracts invoice information from PDF files and organizes invoice records for easy searching and filtering.

## Features

- Upload PDF invoices for AI-powered data extraction.
- Extract invoice details and store them in a MySQL database.
- Support marketplace identification and normalization.
- Filter invoices by marketplace and month.
- Search invoice records.
- Bulk invoice upload.
- Retry failed invoice processing.
- View and refresh invoice records.

## Tech Stack

- **Frontend:** Angular, TypeScript, HTML, SCSS
- **Backend:** Core PHP
- **Database:** MySQL
- **AI Integration:** Google Gemini API
- **Local Development:** WAMP Server

## Project Structure

```text
AI-invoice-management-system/
├── backend/
│   ├── api/
│   └── config/
├── frontend/
│   ├── src/
│   ├── angular.json
│   └── package.json
└── .gitignore
```

## Getting Started

### Prerequisites

- WAMP Server
- PHP with cURL enabled
- MySQL
- Node.js and npm
- Angular CLI

### 1. Clone the Repository

```bash
git clone https://github.com/visumehta/ai-invoice-management-system.git
```

### 2. Configure the Backend

- Place the project inside your WAMP `www` directory.
- Create a MySQL database named `ai_invoice_db`.
- Configure the database connection in `backend/config/database.php`.
- Create a `.env` file in the project root and add your Gemini API key:

```text
GEMINI_API_KEY=your_actual_api_key
```

Never commit your actual API key or other secrets to GitHub.

### 3. Run the Frontend

Open a terminal in the `frontend` directory and run:

```bash
npm install
ng serve
```

Open `http://localhost:4200` in your browser.

## Security Notes

- Keep API keys and credentials private.
- The `.env` file is excluded from Git tracking.
- Configure database credentials appropriately for your environment.

## Author

**Vaishali Mehta**

GitHub: [visumehta](https://github.com/visumehta)
