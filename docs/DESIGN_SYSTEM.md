# 🎨 DESIGN_SYSTEM.md

> **Design System Guide**  
> *Brand identity, color tokens, typography, component library, and mobile UX standards for DALC.*

---

## 01 Brand Identity

🏛️ **Warm Academic Excellence & Clinical Dignity**  
The DALC interface is designed to evoke trust, authority, and warmth—balancing professional clinical record-keeping with an encouraging educational environment for parents and special education teachers.

- [x] Primary Identity: Deep Navy `#1E2657` (Trust & Authority)
- [x] Accent Highlights: Academic Gold `#C4922E` (Achievement & Attention)
- [x] Canvas Tone: Warm Paper Gradient `#F4F1EA` to `#EDF0F8` (Comfortable, low-eyestrain reading)

---

## 02 Color Palette Tokens

🌈 **Curated System Colors**

### Brand Core
- [x] **Primary Navy**: `#1E2657` — Top header, primary buttons, table headers
- [x] **Darker Navy**: `#12163A` — Gradient depth, card headers, active navigation
- [x] **Accent Gold**: `#C4922E` — Star badges, achievement indicators, highlight actions

### Canvas & Backgrounds
- [x] **Background Gradient**: `bg-gradient-to-br from-[#F4F1EA] via-[#EDF0F8] to-[#F4F1EA] bg-fixed`
- [x] **Card Glass Surface**: `bg-white/85 backdrop-blur-md border border-[#1E2657]/10`

### Functional & Status Colors
- [x] **Blue** (`#3B82F6`): Information, active tabs, primary navigation
- [x] **Emerald** (`#10B981`): Success, completed milestones, daily logs
- [x] **Purple** (`#8B5CF6`): IEP goals, objective domains, clinical categories
- [x] **Amber** (`#F59E0B`): Pending approvals, warning alerts, progress reviews
- [x] **Rose** (`#F43F5E`): Danger actions, deletions, revision notices

---

## 03 Typography

✍️ **Clear, Accessible Multi-Font Architecture**

- [x] **Primary UI Typeface**: `'Nunito', sans-serif`
  - Weights: `400` (Regular), `600` (Semi-Bold), `700` (Bold), `800` (Extra-Bold), `900` (Black)
  - Applied to: Navigation, buttons, headings, card titles, badges
- [x] **Content & Document Typeface**: `'Inter', sans-serif`
  - Weights: `400`, `500`, `600`
  - Applied to: Observational notes, IEP descriptions, clinical reports
- [x] **Text Contrast Hierarchy**:
  - Headings / Strong: `#2C2F45`
  - Body Content: `#4B5563`
  - Muted Metadata: `#6B7086`
  - Subtle Borders: `#9CA0AF` / `#E5E7EB`

---

## 04 Component Library

🧩 **Standardized Blade & Tailwind UI Elements**

- [x] **Primary Button**: `bg-[#1E2657] hover:bg-[#12163A] text-white font-bold rounded-xl shadow-md active:scale-95 touch-press`
- [x] **Accent Gold Button**: `bg-[#C4922E] hover:bg-[#b08124] text-white font-bold rounded-xl shadow-md active:scale-95`
- [x] **Secondary Outline**: `bg-white/80 border border-[#1E2657]/20 text-[#1E2657] font-semibold rounded-xl hover:bg-white`
- [x] **Danger Button**: `bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl active:scale-95`
- [x] **Glass Surface Card**: Rounded 2xl card with 10% navy border and subtle shadow
- [x] **Status Badges**: Pill-shaped badges with soft tinted background (e.g. `bg-emerald-50 text-emerald-700 border-emerald-200`)

---

## 05 Polymorphic Comment Component

💬 **Contextual Home-School Communication**

The comment thread component (`resources/views/components/comments-thread.blade.php`) integrates seamlessly with any model:

- [x] Polymorphic attachment to `DailyLog`, `IepGoal`, `Report`, and `Announcement`
- [x] Distinct visual badges for **Teacher** vs **Guardian** comments
- [x] Dynamic unread indicators for new feedback
- [x] In-place comment deletion controls for authors
- [x] Smooth Alpine.js reply toggles without page reloads

---

## 06 Layout & Navigation Architecture

🧭 **Responsive Page Shell (`app.blade.php`)**

- [x] **Sticky Glassmorphic Header**: `h-16 bg-white/80 backdrop-blur-lg border-b border-[#1E2657]/20`
- [x] **Real-Time Notification Bell**: Unread counter with instant flyout preview drawer
- [x] **Smart Role Dispatcher**: Header logo routes users directly to their designated dashboard
- [x] **Unsaved Changes Guard**: Built-in listener alerts educators if they navigate away with unsaved AI drafts
- [x] **Responsive Container**: `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8`

---

## 07 Mobile-First & Touch Standards

📱 **Built for Phones & Tablets**

- [x] **iOS Auto-Zoom Prevention**: Enforces `font-size: 16px !important;` on all input fields on `< 640px` screens
- [x] **Touch-Press Micro-Animations**: `.touch-press:active { transform: scale(0.97); }` gives tactile feedback
- [x] **5-Tab Role-Aware Bottom Bar**:
  - **Teacher**: Home, Logging, IEP, Reports, Students
  - **Admin**: Home, Students, Accounts, Approvals, Alerts
  - **Guardian**: Home, Goals, Advice, Reports, Alerts
- [x] **Safe-Area Insets**: Proper `pb-20` spacing to ensure mobile home indicators never obscure buttons

---

## 08 Iconography & Visual Feedback

⭐ **Font Awesome 6.5.0 Integration**

- [x] Consistent icon set for high legibility
- [x] Milestone progress indicators with visual percentage bars
- [x] Empty state placeholders with friendly illustrative icons
- [x] Color-coded notification icons (Info Blue, Success Emerald, Alert Amber)
