# 🏛️ ARCHITECTURE.md

> **Architecture Guide**  
> *System structure, MVC patterns, database relationships, and request lifecycle for DALC.*

---

## 01 Technology Stack

💻 **Modern, Robust, High-Performance Foundation**

- [x] **Backend Framework**: Laravel 12.x (PHP 8.2+)
- [x] **Frontend Engine**: Blade templates, Tailwind CSS (CDN), Alpine.js 3.x
- [x] **Database Engine**: SQLite (Development / Testing) / MySQL or PostgreSQL (Production)
- [x] **Document Engine**: DomPDF (`barryvdh/laravel-dompdf`) for formal IEP and Progress Report PDFs
- [x] **Image Processing**: `intervention/image` for digital signatures and uploaded media
- [x] **AI Provider**: Groq Cloud API (`openai/gpt-oss-20b`)

---

## 02 Directory Organization

📂 **Core Structure of `dalc-system/`**

- [x] `app/Http/Controllers/Admin/`: Dashboard, Accounts, Students, Approvals, Archival, Calendar
- [x] `app/Http/Controllers/Teacher/`: Daily Logs, IEP Goals, Clinical Reports, Student Roster
- [x] `app/Http/Controllers/Guardian/`: Child Switcher, Daily Log Archive, Reports, IEP Milestones
- [x] `app/Http/Controllers/SecureFileController.php`: Private disk file vault
- [x] `app/Http/Controllers/AiAssistantController.php`: Groq Cloud AI proxy
- [x] `app/Http/Middleware/`: Custom 3-gate authentication & granular permission filters
- [x] `app/Models/`: 11 Eloquent models with typed casts and relationships
- [x] `routes/web.php`: Role-isolated route groupings

---

## 03 MVC Request Flow

🔄 **Lifecycle of an Incoming Request**

- [x] **Step 1 — Routing**: `routes/web.php` maps endpoint to controller
- [x] **Step 2 — Security Middleware**: AuthenticateSession → Role → ForcePassword → MFA → Permissions
- [x] **Step 3 — Controller Logic**: Verifies request data, calls business logic, interacts with models
- [x] **Step 4 — Data Layer**: Eloquent models query DB with strict parameter bindings
- [x] **Step 5 — Presentation**: Returns Blade view with Alpine.js or streams PDF/Vault binary

---

## 04 Database & Eloquent Models

🗄️ **The 11 Core Eloquent Models**

- [x] `User`: Admins, Teachers, Guardians; stores credentials, 2FA secret, and permission flags
- [x] `Student`: Learner info, diagnosis, class, teacher/guardian foreign keys
- [x] `DailyLog`: Classroom observation notes, photos, AI rewrites, and home recommendations
- [x] `IepGoal`: Domain, PLOP baseline, SMART target, and percentage progress (0–100%)
- [x] `Report`: Quarterly/Annual narrative reports with approval status and admin signature
- [x] `StudentDocument`: Vaulted intake forms, medical evaluations, and psychological records
- [x] `ArchivedStudent`: Permanent historical snapshots with frozen JSON data
- [x] `SchoolYear`: Active calendar year anchor enforcing single-active-year invariant
- [x] `AcademicQuarter`: School quarters with automatic date-based activation
- [x] `Comment`: Polymorphic discussion model attached to logs, goals, reports, and notices
- [x] `Announcement`: School-wide announcements with broadcast alerts

---

## 05 Route Architecture & Portals

🛣️ **Strictly Segmented Route Groups**

- [x] **Global & Security Setup**: `/security-setup`, `/mfa-setup`, `/mfa-challenge`, `/profile`
- [x] **Secure File Vault**: `/secure-log-photo/{id}`, `/secure-document/{id}`, `/secure-medical-document/{id}`
- [x] **Admin Portal (`/admin/*`)**: Strictly gated by `role:admin`
- [x] **Delegated Modules**: Gated by `CheckModulePermission` flags (`can_approve_reports`, `can_manage_calendar`, etc.)
- [x] **Teacher Portal (`/teacher/*`)**: Strictly gated by `role:teacher`
- [x] **Guardian Portal (`/guardian/*`)**: Strictly gated by `role:guardian`

---

## 06 Middleware Pipeline

🧱 **Sequential Gatekeepers**

- [x] `\Illuminate\Session\Middleware\AuthenticateSession`: Kills hijacked sessions on password change
- [x] `RoleMiddleware`: Ensures user role matches route prefix (`admin`, `teacher`, `guardian`)
- [x] `ForcePasswordChange`: Forces private password setup if `password_changed_at` is `NULL`
- [x] `EnsureMfaIsVerified`: Validates TOTP code or verifies 30-day remembered device cookie
- [x] `CheckModulePermission`: Checks individual boolean switches for delegated teacher access

---

## 07 Academic Calendar Engine

📅 **Multi-Year Term Synchronization**

- [x] `SchoolYear` model enforces a single active academic year at any given time
- [x] `AcademicQuarter` implements `syncActiveStatus()`:
  - Deactivates quarters outside the active school year
  - Automatically turns on the current quarter matching today's calendar date
  - Syncs seamlessly whenever an administrator switches the active school year

---

## 08 Annual Archival & Rollover Engine

📦 **End-of-Year Student Record Freeze**

- [x] Executed via `/admin/archive` or command line during academic year transitions
- [x] Freezes student demographics, IEP goals, and clinical progress into immutable JSON snapshots
- [x] Retains historical documents and re-enrolls active students into the new school year
- [x] Compiles a complete, printable Master Archive PDF via DomPDF
