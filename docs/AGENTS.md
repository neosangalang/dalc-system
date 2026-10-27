# 🤖 AGENTS.md

> **AI Agent Guide**  
> *Groq Cloud LLM integration, prompt engineering patterns, and clinical SPED assistive tools for DALC.*

---

## 01 Architecture & Proxy Pattern

🛡️ **Zero-Exposure Server-Side Proxy**  
Client browsers never communicate with the AI provider directly. All AI prompts pass through authenticated Laravel backend controllers, keeping the API key secure and preventing prompt tampering.

- [x] Client submits raw teacher notes or IEP criteria to Laravel controller
- [x] Backend validates user authentication and student ownership
- [x] Controller loads `GROQ_API_KEY` securely from server environment (`config('services.groq.api_key')`)
- [x] Dispatches request via `Http::timeout(60)->post('https://api.groq.com/openai/v1/chat/completions')`
- [x] Sanitizes response, strips markdown conversational filler, and returns clean text to UI

---

## 02 Model & Configuration

⚡ **High-Throughput, Low-Latency Special Education Inference**

- [x] **Model**: `openai/gpt-oss-20b` (hosted via Groq Cloud)
- [x] **Inference Speed**: ~300–500 tokens/sec for near-instant results
- [x] **Tuned Temperature Profiles**:
  - `0.5`: Deterministic, objective tone for official clinical progress reports and observation rewrites
  - `0.7`: Balanced creativity for IEP SMART goal synthesis and engaging home activity ideas

---

## 03 Daily Log Rewriting

📝 **Informal Notes → Professional Clinical Observations**

- [x] **Routes**: `POST /ai/rewrite-log` & `POST /teacher/daily-logs/generate-ai`
- [x] **Controllers**: `AiAssistantController` / `DailyLogController`
- [x] **Function**: Converts raw, rushed teacher notes into clinical, strengths-focused SPED documentation
- [x] **Output Quality**:
  - Eliminates emotional bias and negative phrasing
  - Emphasizes coping strategies and sensory accommodations used
  - Produces clean, structured paragraphs ready for official student records

---

## 04 IEP SMART Goal Generation

🎯 **Domain & PLOP → Measurable Target Objectives**

- [x] **Route**: `POST /teacher/iep-goals/generate-ai`
- [x] **Controller**: `IepGoalController`
- [x] **Inputs**: Developmental Domain (e.g., Communication, Cognitive, Motor) + Present Level of Performance (PLOP)
- [x] **Enforced JSON Output**:
  - Measurable 36-week annual goal with baseline criteria
  - 3 sequential quarterly benchmark objectives
  - 2 specific classroom interventions and accommodations

---

## 05 Comprehensive Progress Report Synthesis

📊 **Quarterly / Annual Narrative Evaluation Generator**

- [x] **Route**: `POST /teacher/reports/generate-ai`
- [x] **Controller**: `ReportController`
- [x] **Context Assembly**:
  - Gathers all student Daily Logs for the selected academic quarter
  - Injects active IEP goals and current completion percentages
  - Injects teacher clinical observations
- [x] **Generated Report Structure**:
  - Section 1: Executive Developmental Summary
  - Section 2: Cognitive & Academic Milestones
  - Section 3: Social, Behavioral & Sensory Progress
  - Section 4: IEP Goal Progress Analysis
  - Section 5: Recommendations for Next Academic Term

---

## 06 Home Learning Recommendations

🏠 **Parent-Friendly Practical Reinforcement Activities**

- [x] **Route**: `POST /teacher/daily-logs/generate-recommendations`
- [x] **Controller**: `DailyLogController`
- [x] **Function**: Analyzes child's daily classroom challenges and successes to suggest 2 actionable home activities
- [x] **Tone**: Warm, encouraging, empathetic, and free of medical jargon
- [x] **Safety**: Activities use everyday household items without causing parental stress

---

## 07 Prompt Engineering & Guardrails

🛡️ **Safety, Privacy & Clinical Precision**

- [x] **System Persona Framing**: Prompts instruct the LLM to act as an expert pediatric SPED diagnostician
- [x] **PII Protection**: Only student first name, age, and clinical notes are shared; LRN, full names, and contact details are omitted
- [x] **Automated Regex Post-Processing**: Filters out conversational prefixes (e.g. *"Here is your rewritten log:"*) so only clean content enters text fields
- [x] **Human-in-the-Loop Review**: AI outputs populate editable draft textareas; nothing is saved to the database without teacher approval

---

## 08 Error Handling & Fallbacks

🛑 **Graceful Degradation**

- [x] Pre-checks `GROQ_API_KEY` before dispatching requests; notifies admin if unconfigured
- [x] Catches HTTP timeouts and rate limits (`429`), returning structured, friendly error messages
- [x] Validates JSON output structure on IEP generation with fallback parsing
- [x] Front-end unsaved-changes listener warns teachers if they attempt to navigate away from uncommitted AI drafts
