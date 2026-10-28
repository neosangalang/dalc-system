# 🛡️ SECURITY.md

> **Security Guide**  
> *Security practices, 3-gate authentication, and guidelines for keeping DALC and student records safe.*

---

## 01 Purpose

🎯 **Protecting Confidential Special Education (SPED) Data**  
This document outlines the security architecture, authentication policies, and data protection practices for the **Dream Achievers Learning Center (DALC)** system. It applies to all contributors, system administrators, and developers.

- [x] Protect confidential student diagnostic, behavioral, and medical records
- [x] Enforce zero-trust role isolation between Admins, Teachers, and Guardians
- [x] Guarantee end-to-end accountability for all student assessments and reports

---

## 02 Core Principles

💡 **Security by Design, Not as an Afterthought**

- [x] **Principle of Least Privilege**: Users access only their assigned students and specific permitted modules
- [x] **Defense in Depth**: Multi-layer security perimeter (Credentials → Force Password → Mandatory MFA)
- [x] **No Public Sensitive Storage**: All medical, psychological, and log media reside in private disks
- [x] **Server-Side Authorization**: Every file request and state change is verified on the backend

---

## 03 Authentication & 3-Gate Perimeter

🚪 **Every User Must Clear Three Sequential Security Gates**

### Gate 1: Primary Credentials
- [x] Dual-identifier login supporting either `username` or `email`
- [x] Secure password hashing using Bcrypt (work factor 12)
- [x] IP-based rate limiting against brute-force attacks via Laravel Breeze

### Gate 2: Mandatory First-Login Password Change
- [x] Admin-created accounts start with `password_changed_at = NULL`
- [x] `ForcePasswordChange` middleware intercepts all requests until a new password is set
- [x] Enforces strong passwords (min 8 chars, uppercase, lowercase, number, symbol)

### Gate 3: Two-Factor Authentication (TOTP / Google Authenticator)
- [x] RFC 6238-compliant TOTP generated via `pragmarx/google2fa`
- [x] Pure SVG QR code rendering via `bacon/bacon-qr-code` (zero third-party dependencies)
- [x] `EnsureMfaIsVerified` middleware blocks unauthorized portal access
- [x] Encrypted 30-day remembered device cookie (`mfa_remember_{user_id}`) with HMAC signatures that auto-revoke on password changes

---

## 04 Role-Based Access Control (RBAC)

👤 **Strict Portal Isolation via `RoleMiddleware`**

- [x] **Admin (`role:admin`)**: Institutional management, user accounts, calendar sync, report approvals, and annual archival
- [x] **Teacher (`role:teacher`)**: Daily observational logging, IEP goal tracking, report authoring, and file uploads
- [x] **Guardian (`role:guardian`)**: Read-only tracking for their enrolled children, IEP progress, formal PDF downloads, and home learning advice
- [x] Unauthorized cross-role navigation immediately terminates with `403 Forbidden`

---

## 05 Granular Module Permissions

🎛️ **Sub-Admin Delegation Without Promoting to Full Admin**

Super Admins can grant specific operational permissions to trusted teachers via boolean flags:

- [x] `can_edit_students`: Allows modifying assigned student demographics
- [x] `can_manage_credentials`: Allows resetting passwords and credentials
- [x] `can_manage_calendar`: Allows configuring School Years and Academic Quarters
- [x] `can_create_profiles`: Allows enrolling new students and uploading intake documents
- [x] `can_archive_students`: Allows running annual rollover and viewing historical snapshots
- [x] `can_approve_reports`: Allows reviewing and approving clinical progress reports
- [x] Protected by `CheckModulePermission` middleware; Super Admins automatically bypass checks

---

## 06 Secure File Vault

🔒 **Authorized File Streaming via `SecureFileController`**

Confidential student files and medical diagnostics are never placed in `public/storage`.

- [x] Files reside in private local storage (`storage/app/`)
- [x] Access checked strictly on every download request:
  - Admins can access all records
  - Teachers can access records only for their assigned students (`teacher_id == user.id`)
  - Guardians can access records only for their enrolled child (`guardian_id == user.id`)
- [x] Direct streaming with dynamic MIME headers via `Storage::disk('local')->response()`
- [x] Backward-compatible fallback for legacy uploads

---

## 07 Data Protection & Cryptography

🔐 **Data Protection at Rest and in Transit**

- [x] **Encrypted 2FA Secrets**: `two_factor_secret` encrypted at rest using AES-256-CBC via `encrypted` Eloquent cast
- [x] **Password Protection**: Passwords hashed with Bcrypt via `hashed` cast
- [x] **CSRF Defense**: All state-changing verbs (`POST`, `PUT`, `PATCH`, `DELETE`) require `@csrf` tokens
- [x] **SQL Injection Prevention**: 100% parameter-bound queries via Eloquent ORM
- [x] **Session Hijacking Mitigation**: `AuthenticateSession` middleware terminates all sessions upon password changes
- [x] **Cookie Security**: `HttpOnly`, `SameSite=Lax`, and `Secure` flags on all auth cookies

---

## 08 Incident & Security Reporting

🚨 **Vulnerability & Incident Handling Guidelines**

- [x] Never commit `.env` or application secrets to version control
- [x] Rotate `GROQ_API_KEY` and `APP_KEY` immediately if exposed
- [x] Report any unauthorized access or security concerns directly to system administration
- [x] Test security gates locally:
  - Test `/security-setup` with new account
  - Test `/mfa-challenge` in incognito browser
  - Verify `403 Forbidden` on unauthorized file vault URLs

---

## 09 Legal & Privacy Compliance Architecture

🏛️ **Statutory Alignment with Philippine Data Privacy & Special Education Laws**

- [x] **Republic Act No. 10173 (Data Privacy Act of 2012)**:
  - Formal Privacy Policy available at `/privacy` with designated DPO contact (`dpo@dalc.edu.ph`).
  - Mandatory parental consent validation for all minor student records and diagnostic assessments.
  - 72-hour security incident notification protocol to National Privacy Commission (NPC).
- [x] **Institutional Terms of Use**:
  - Live agreement at `/terms` establishing zero-trust credential handling and barring unauthorized re-publication.
  - Enforced acceptance checkbox on Gate 2 first-login setup (`/security-setup`).
- [x] **AI SPED Assistant Ethics & Clinical Disclaimer**:
  - Documented guidelines at `/policies/ai-ethics`.
  - Zero-PII server-side proxy stripping student surnames and LRN before forwarding context to Groq Cloud.
  - Human-in-the-loop guarantee requiring educator verification prior to committing drafts.
- [x] **Parental Consent & Media Release Policy**:
  - Live policy at `/policies/parental-consent` regulating classroom observation photography and prohibiting peer photo sharing on public social networks.
- [x] **Document Legal Watermarking**:
  - Mandatory statutory confidentiality notice rendered across all generated PDFs (Daily Logs, IEPs, Progress Reports, Master Archives).
