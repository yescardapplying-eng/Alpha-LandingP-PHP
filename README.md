# Stock Market Assessment Portal — PHP & MySQL Backend

A high-performance, progressive quiz assessment portal built with **PHP 8.3**, **MySQL (TiDB Cloud Serverless)**, and vanilla **HTML / CSS / JavaScript**.

---

## Key Features

1. **Real-time Question & Answer Click Tracking (`/api/track-click`)**:
   - Every time a user clicks on an option card (e.g. *Step 1: Challenge*, *Step 3: Trading Segment*, *Step 4: Trading Capital*), the question and answer are immediately stored into MySQL (`assessment_interactions` and `leads` tables).
2. **Progressive Step Saving on Continue (`/api/track-step`)**:
   - When the user clicks "Continue", inputs are validated on the server side and stored in the database, with step progression recorded.
3. **Final Submission (`/api/submit-lead`)**:
   - On the final step (Step 5: WhatsApp & State), clicking "Submit" finalizes the record with `status = 'completed'` and `completed_at` timestamp.
4. **Complete Audit Trail**:
   - All user clicks (`option_click`, `continue_click`, `submit_click`) are logged with timestamps, IP address, user agent, question title, and selected response.
5. **TiDB Cloud / MySQL Serverless Compatibility**:
   - Secure TLS/SSL connection with CA certificate verification.

---

## Directory Structure

```
d:\Alpha-LandingP-PHP\
├── .env                         # Database credentials (ignored in git)
├── .env.example                 # Environment template
├── .gitignore                   # Excludes .env, logs, system files
├── cacert.pem                   # CA certificate bundle for SSL connections
├── config/
│   └── database.php             # PDO MySQL connection with SSL & .env loader
├── database/
│   ├── schema.sql               # MySQL DDL for leads & assessment_interactions
│   └── migrate.php              # Automated database table migration script
├── helpers/
│   ├── response.php             # JSON helper, security headers, IP & UserAgent
│   └── validation.php           # Field validations, mobile normalization, state list
├── api/
│   ├── track-click.php          # Real-time click tracking on option select
│   ├── track-step.php           # Progressive saving on Continue button click
│   ├── submit-lead.php          # Final submission on Submit button click
│   └── get-lead.php             # Retrieve lead & interaction logs by session ID
├── assets/
│   ├── css/style.css            # Stylesheet
│   └── js/app.js                # Frontend controller with real-time tracking
├── index.html                   # Assessment questionnaire UI
├── router.php                   # Built-in PHP server router
├── test_backend.php             # Automated integration test suite
└── README.md                    # Documentation
```

---

## Database Tables

### 1. `leads`
Stores aggregated session data and answers:
- `id` (BIGINT AUTO_INCREMENT PRIMARY KEY)
- `session_id` (VARCHAR UNIQUE)
- `lead_reference` (VARCHAR UNIQUE)
- `status` ('partial', 'completed')
- `current_step` (INT)
- `challenge` (VARCHAR)
- `full_name` (VARCHAR)
- `mobile_number` (VARCHAR)
- `trading_segment` (VARCHAR)
- `trading_capital` (VARCHAR)
- `whatsapp_number` (VARCHAR)
- `state` (VARCHAR)
- `utm_source`, `utm_medium`, `utm_campaign`, `utm_content`, `utm_term`, `fbclid`
- `ip_address`, `user_agent`
- `created_at`, `updated_at`, `completed_at`

### 2. `assessment_interactions`
Logs every user click action with question and answer:
- `id` (BIGINT AUTO_INCREMENT PRIMARY KEY)
- `session_id` (VARCHAR)
- `step_number` (INT)
- `question` (VARCHAR)
- `selected_answer` (TEXT)
- `field_name` (VARCHAR)
- `action_type` ('option_click', 'continue_click', 'submit_click')
- `ip_address`, `user_agent`
- `created_at` (DATETIME)

---

## Running Locally

### 1. Run Database Migrations
```bash
php database/migrate.php
```

### 2. Run Backend Tests
```bash
php test_backend.php
```

### 3. Start Local Development Server
```bash
php -S localhost:8000 router.php
```
Open [http://localhost:8000](http://localhost:8000) in your browser.
