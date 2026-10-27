# Dream Achievers Learning Center (DALC) System

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpinedotjs&logoColor=white)](https://alpinejs.dev)
[![Groq AI](https://img.shields.io/badge/Groq_Cloud-AI_Engine-F55036?style=for-the-badge)](https://groq.com)
[![Security](https://img.shields.io/badge/Security-3--Gate_MFA-10B981?style=for-the-badge)](#-security-architecture)

A specialized enterprise web application engineered for **Special Education (SPED)** administration, individualized student development tracking, and secure home-school collaboration at **Dream Achievers Learning Center (DALC)**.

---

## 🌟 Key Features

- **🛡️ 3-Gate Security Perimeter**: Enforces mandatory first-login password updates, TOTP 2FA (Google Authenticator) with 30-day remembered devices, and strict Role-Based Access Control (RBAC).
- **🔑 Granular Module Permissions**: Super Admins can delegate administrative switches (`can_edit_students`, `can_manage_credentials`, `can_manage_calendar`, `can_create_profiles`, `can_archive_students`, `can_approve_reports`) to select educators.
- **🤖 AI-Powered SPED Assistant**: Groq Cloud integration (`openai/gpt-oss-20b`) providing clinical rewrites for daily observation logs, SMART IEP goal drafting, quarterly progress narrative generation, and parent home recommendations.
- **🎯 Individualized Education Programs (IEP)**: Track multi-domain objectives, Present Levels of Performance (PLOP) baselines, and milestone percentage progress bars.
- **🔒 Secure File Vault**: Non-public private storage disk (`storage/app/`) protecting confidential medical histories, diagnostic assessments, and student media with per-request authorization checks.
- **📦 Annual Archive & Rollover Engine**: End-of-year immutable JSON snapshots of student records, historical document retention, and DomPDF master archive compilation.
- **💬 Polymorphic Discussions**: Contextual, real-time message threads between teachers and guardians attached directly to daily logs, IEP goals, progress reports, and announcements.
- **📱 Mobile-First Responsive Design**: Optimized for mobile and tablet touchscreens with tactile feedback (`touch-press`), iOS 16px auto-zoom prevention, and role-specific 5-tab bottom navigation bars.

---

## 👥 User Portals

| Role | Access Scope & Key Workflows |
|---|---|
| **Admin (`admin`)** | Institutional governance, user lifecycle management, granular permission assignment, academic calendar & quarter configuration, clinical report approvals, and annual student archival/rollovers. |
| **Teacher (`teacher`)** | Pedagogical observation logging with photo attachments, IEP SMART goal authoring, AI-assisted progress report generation, student file vault uploads, and parent collaboration. |
| **Guardian (`guardian`)** | Multi-child switching, real-time daily log monitoring, IEP milestone tracking, formal report/IEP PDF downloads, and accessing AI home learning activities. |

---

## 🛠️ Technology Stack

- **Backend**: Laravel 12.x running on PHP 8.2+
- **Frontend**: Laravel Blade, Tailwind CSS (CDN), Alpine.js 3.x
- **Database**: SQLite (Development / Testing) / MySQL or PostgreSQL (Production)
- **AI Inference**: Groq Cloud API (`openai/gpt-oss-20b`) via server-side proxy
- **2FA / TOTP**: `pragmarx/google2fa` & `bacon/bacon-qr-code` (pure SVG generation)
- **Document Processing**: `barryvdh/laravel-dompdf` & `intervention/image`
- **Authorization**: `spatie/laravel-permission` + custom role & module permission middleware

---

## 🚀 Quick Start Guide

### Prerequisites
- **PHP**: >= 8.2 with extensions: `pdo_sqlite`, `curl`, `mbstring`, `openssl`, `gd`, `fileinfo`
- **Composer**: >= 2.x
- **Web Server**: Laragon, XAMPP, or built-in PHP CLI server

### Installation Steps

1. **Clone the repository**:
   ```bash
   git clone <repository-url> dalc-system
   cd dalc-system
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment File**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Configure your Database & AI Service** in `.env`:
   ```ini
   DB_CONNECTION=sqlite
   # Make sure database/database.sqlite exists (or touch database/database.sqlite)

   GROQ_API_KEY="gsk_your_groq_api_key_here"
   ```

5. **Run Migrations & Seeders**:
   ```bash
   php artisan migrate --seed
   ```

6. **Serve the Application**:
   ```bash
   php artisan serve
   ```
   Open your browser and navigate to `http://127.0.0.1:8000`.

---

## 🔐 Security Architecture

DALC safeguards confidential Special Education data through layered access controls:

1. **Gate 1 — Credential Verification**: Bcrypt password validation with rate-limited login attempts.
2. **Gate 2 — Forced Password Update**: Any new account with a `NULL` `password_changed_at` timestamp is quarantined until a private, complex password is set.
3. **Gate 3 — Mandatory 2FA**: Every user must configure TOTP via Google Authenticator or Microsoft Authenticator before accessing student records. Includes a secure 30-day remembered device cookie (`mfa_remember_{user_id}`) with HMAC signatures that auto-revoke on password changes.
4. **Secure File Vault**: All medical reports, observation images, and IEP attachments are stored on private disks and streamed only after verifying `teacher_id` or `guardian_id` ownership.

---

## 📚 Documentation Guides

The documentation is organized into modular guides in the [`docs/`](docs/) directory:

- 🛡️ [**docs/SECURITY.md**](docs/SECURITY.md) — 3-gate authentication, 2FA/TOTP, RBAC, file vault, and cryptographic policies.
- 🎨 [**docs/DESIGN_SYSTEM.md**](docs/DESIGN_SYSTEM.md) — Brand identity, Navy & Gold color tokens, typography scale, components, and mobile UX.
- 🏛️ [**docs/ARCHITECTURE.md**](docs/ARCHITECTURE.md) — System structure, MVC lifecycle, 11 Eloquent models, routes, and academic rollover.
- 🤖 [**docs/AGENTS.md**](docs/AGENTS.md) — Groq Cloud LLM proxy, daily log rewriting, IEP goal generation, and report synthesis.
- 📖 [**docs/README.md**](docs/README.md) — Documentation index & quick guide navigation.
- 📜 [**docs/SYSTEM_DOCUMENTATION.md**](docs/SYSTEM_DOCUMENTATION.md) — Full authoritative technical and architectural reference.

---

## 📄 License & Attribution

Internal system developed for **Dream Achievers Learning Center (DALC)**. All rights reserved.
