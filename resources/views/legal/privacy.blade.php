@extends('legal.layout')

@section('title', 'Data Privacy Policy')
@section('page_title', 'Data Privacy Policy & NPC Compliance')
@section('doc_heading', 'Data Privacy Notice (Republic Act No. 10173)')

@section('toc')
<ul class="space-y-2 text-xs font-bold text-[#4B5563] list-none p-0 m-0">
    <li><a href="#pic-identity" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 1. Personal Information Controller (PIC)</a></li>
    <li><a href="#data-inventory" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 2. Categories of Data Collected</a></li>
    <li><a href="#processing-purposes" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 3. Purposes of Processing</a></li>
    <li><a href="#lawful-basis" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 4. Lawful Basis & Minors\' Consent</a></li>
    <li><a href="#ai-third-parties" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 5. Third-Party & AI Proxy Safeguards</a></li>
    <li><a href="#security-measures" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 6. Technical & Physical Security</a></li>
    <li><a href="#retention-disposal" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 7. Retention, Rollover & Disposal</a></li>
    <li><a href="#data-subject-rights" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 8. Rights of Data Subjects</a></li>
    <li><a href="#breach-management" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 9. Incident & Breach Management</a></li>
    <li><a href="#dpo-contact" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 10. Inquiries & DPO Contact</a></li>
</ul>
@endsection

@section('document_body')

<div class="space-y-6">

    <div class="bg-blue-50 border-l-4 border-[#3B82F6] p-4 rounded-r-xl">
        <div class="flex items-start gap-3">
            <i class="fa fa-circle-info text-[#3B82F6] text-lg mt-0.5 shrink-0"></i>
            <div class="text-xs text-[#1E2657] leading-relaxed">
                <strong>Statement of Compliance:</strong> Dream Achievers Learning Center (DALC) is committed to upholding the privacy rights of all individuals, especially minor children with special educational needs. This policy complies with <strong>Republic Act No. 10173 (Data Privacy Act of 2012)</strong>, its Implementing Rules and Regulations, and relevant directives of the <strong>National Privacy Commission (NPC)</strong>.
            </div>
        </div>
    </div>

    <section id="pic-identity">
        <h2><i class="fa fa-building-shield text-[#1E2657]"></i> 1. Personal Information Controller (PIC)</h2>
        <p>
            Under RA 10173, the Personal Information Controller (PIC) responsible for your personal data is:
        </p>
        <div class="bg-[#F8FAFC] border border-black/10 rounded-xl p-4 text-xs space-y-1">
            <div><strong class="text-[#1E2657]">Institution:</strong> Dream Achievers Learning Center (DALC)</div>
            <div><strong class="text-[#1E2657]">Operational Scope:</strong> Special Education (SPED) Learning Center</div>
            <div><strong class="text-[#1E2657]">Data Protection Officer (DPO):</strong> Compliance & Privacy Unit (<a href="mailto:dpo@dalc.edu.ph" class="text-[#4F6AF5] underline font-bold">dpo@dalc.edu.ph</a>)</div>
            <div><strong class="text-[#1E2657]">Administrative Contact:</strong> 0936-952-3782 (Mondays–Fridays, 8:00 AM – 5:00 PM)</div>
        </div>
    </section>

    <section id="data-inventory">
        <h2><i class="fa fa-list-check text-[#4F6AF5]"></i> 2. Categories of Data Collected & Processed</h2>
        <p>
            To deliver specialized educational programming, DALC processes the following categories of information:
        </p>

        <h3>A. Student Information (Minors & Special Learners)</h3>
        <ul>
            <li><strong>Demographic Identifiers:</strong> First name, last name, date of birth, age, gender, assigned classroom or developmental group.</li>
            <li><strong>Government-Issued Identifier:</strong> 12-digit <strong>Learner Reference Number (LRN)</strong> mandated by the Department of Education (DepEd).</li>
            <li><strong>Sensitive Personal Information (SPI) - Health & Medical:</strong> Formal clinical diagnoses (e.g., Autism Spectrum Disorder, ADHD, Down Syndrome, Cerebral Palsy, Speech/Language Delay), pediatrician evaluations, psychological assessments, therapy records (OT, PT, ST), and allergy/dietary notes.</li>
            <li><strong>Pedagogical & Behavioral Data:</strong> Present Levels of Performance (PLOP), Individualized Education Program (IEP) SMART objectives, daily observation logs, sensory coping strategies, milestone progress percentages, and quarterly narrative evaluations.</li>
            <li><strong>Photographic Media:</strong> Pedagogical photographs taken during classroom learning sessions, fine/gross motor exercises, and social interaction activities.</li>
        </ul>

        <h3>B. Parent / Guardian Information</h3>
        <ul>
            <li>Full legal name, relationship to student, active contact number, email address, residential address, emergency contact coordinates.</li>
            <li>System access credentials (username, Bcrypt-hashed password, encrypted TOTP two-factor authentication secret).</li>
            <li>Discussion logs, home follow-up comments, and audit timestamps.</li>
        </ul>

        <h3>C. Educator & Administrative Staff Information</h3>
        <ul>
            <li>Full name, PRC professional license number, designated role, administrative module permissions, audit timestamps, and uploaded digital signature stamps for official report verification.</li>
        </ul>
    </section>

    <section id="processing-purposes">
        <h2><i class="fa fa-bullseye text-[#10B981]"></i> 3. Purposes of Processing</h2>
        <p>
            Your information is processed strictly for legitimate educational, clinical, and administrative purposes:
        </p>
        <ol>
            <li>Drafting, tailoring, benchmarking, and tracking Individualized Education Programs (IEPs).</li>
            <li>Documenting daily classroom behavioral adaptations, milestone developments, and sensory accommodations.</li>
            <li>Authoring, reviewing, digitally signing, and releasing quarterly developmental progress reports.</li>
            <li>Facilitating secure, contextual home-school communication between teachers and parents.</li>
            <li>Fulfilling institutional compliance and statutory reporting under DepEd guidelines and RA 11650.</li>
            <li>Maintaining an immutable, auditable historical academic archive upon end-of-year rollover.</li>
        </ol>
        <p>
            <strong>NO COMMERCIAL SHARING:</strong> DALC never sells, leases, trades, or commercially exploits personal or sensitive data under any circumstance.
        </p>
    </section>

    <section id="lawful-basis">
        <h2><i class="fa fa-scale-balanced text-[#C4922E]"></i> 4. Lawful Basis & Minors' Consent</h2>
        <p>
            Processing is conducted under the lawful grounds established in Sections 12 and 13 of RA 10173:
        </p>
        <ul>
            <li><strong>Parental / Guardian Consent:</strong> As students are minors, processing of their Sensitive Personal Information (SPI) is predicated upon express, written or verified digital parental consent provided during enrollment.</li>
            <li><strong>Contractual & Educational Obligation:</strong> Processing is required to perform the educational services for which the student is enrolled.</li>
            <li><strong>Protection of Vital Health Interests:</strong> Maintaining accurate medical history and emergency contacts is critical for safeguarding the physical safety of learners on campus.</li>
        </ul>
    </section>

    <section id="ai-third-parties">
        <h2><i class="fa fa-network-wired text-[#3B82F6]"></i> 5. Third-Party Technical Processors & AI Safeguards</h2>
        <p>
            DALC engages trusted technical service providers to maintain the System's high security and pedagogical capabilities. Every provider operates under strict confidentiality obligations:
        </p>
        <ul>
            <li><strong>Groq Cloud AI Inference Engine (`openai/gpt-oss-20b`):</strong> Used by certified educators for clinical observation rewrites, IEP goal drafting, and progress narrative compilation.
                <div class="mt-2 p-3 bg-amber-50 rounded-lg border border-amber-200 text-xs">
                    <strong>Zero PII Exposure Guardrail:</strong> AI prompts pass through a secure server-side Laravel proxy. The proxy scrubs all identifying details—including the student's full name, LRN, guardian details, and address—before sending text to the LLM. Only first name and developmental context are forwarded. Groq does not use DALC system prompts to train foundational public models.
                </div>
            </li>
            <li><strong>Secure Cloud Hosting & Database Infrastructure:</strong> High-security server infrastructure configured with strict SSL/TLS cryptographic protocols, daily encrypted backups, and isolated private storage volumes.</li>
        </ul>
    </section>

    <section id="security-measures">
        <h2><i class="fa fa-lock text-[#10B981]"></i> 6. Technical, Physical & Organizational Security Measures</h2>
        <p>
            DALC enforces multi-layered technical controls in accordance with NPC Circular 16-01:
        </p>
        <ul>
            <li><strong>3-Gate Security Perimeter:</strong> Credential validation, forced first-login password initialization, and mandatory TOTP 2FA.</li>
            <li><strong>Private File Vault:</strong> Confidential student diagnostic files reside on non-public disks (`storage/app/`) and are accessible solely through backend authorization (`SecureFileController`).</li>
            <li><strong>Encryption at Rest and in Transit:</strong> Passwords hashed with Bcrypt (work factor 12); TOTP 2FA secrets encrypted at rest via AES-256-CBC; all network transit enforced through HTTPS/TLS.</li>
            <li><strong>Cryptographic Digital Signatures:</strong> Formal progress reports and IEPs generate unique SHA/MD5 audit hashes validating the exact timestamp, evaluator license, and approval record.</li>
            <li><strong>Role-Based Access Control (RBAC):</strong> Strict zero-trust separation preventing cross-student access between guardians and unauthorized educators.</li>
        </ul>
    </section>

    <section id="retention-disposal">
        <h2><i class="fa fa-clock-rotate-left text-[#C4922E]"></i> 7. Data Retention, Archival & Secure Disposal</h2>
        <p>
            <strong>Active Enrollment:</strong> Student data is actively processed throughout the student's enrollment period.
        </p>
        <p>
            <strong>Annual Rollover & Master Archive:</strong> At the close of each academic school year, student records are bundled into immutable JSON snapshots and comprehensive PDF master archives. Archived records are quarantined in a restricted archive repository accessible only to Super Admins and authorized archival delegates.
        </p>
        <p>
            <strong>Retention Period & Disposal:</strong> Under DepEd standards and relevant Philippine statutes of limitations, educational and diagnostic records are retained for a minimum of <strong>five (5) to ten (10) years</strong> post-graduation or transfer. Upon the expiration of the retention timeline, records are permanently shredded from digital storage using cryptographically secure disk overwrite protocols.
        </p>
    </section>

    <section id="data-subject-rights">
        <h2><i class="fa fa-user-check text-[#4F6AF5]"></i> 8. Rights of the Data Subject under RA 10173</h2>
        <p>
            As a parent, guardian, or data subject, you are entitled to the full suite of rights granted by Philippine law:
        </p>
        <ul>
            <li><strong>Right to be Informed:</strong> To know whether your or your child's data is being collected and processed.</li>
            <li><strong>Right to Access:</strong> To obtain reasonable access to your child's complete educational, behavioral, and diagnostic portfolio upon written request.</li>
            <li><strong>Right to Rectification:</strong> To request correction of inaccurate, outdated, or incomplete records.</li>
            <li><strong>Right to Erasure or Blocking:</strong> To request the suspension, withdrawal, or removal of personal data that is unlawfully processed or no longer necessary for educational purposes (subject to statutory retention mandates).</li>
            <li><strong>Right to Damages:</strong> To be indemnified for any damages sustained due to inaccurate, incomplete, outdated, false, or unlawfully obtained personal data.</li>
            <li><strong>Right to Data Portability:</strong> To receive your child's records in a standardized, machine-readable format (such as certified PDF and JSON export).</li>
            <li><strong>Right to Lodge a Complaint:</strong> If you believe your privacy rights have been violated, you have the right to file a complaint directly with the <strong>National Privacy Commission (NPC)</strong> at <a href="https://privacy.gov.ph" target="_blank" class="text-[#4F6AF5] underline font-bold">privacy.gov.ph</a>.</li>
        </ul>
    </section>

    <section id="breach-management">
        <h2><i class="fa fa-triangle-exclamation text-rose-500"></i> 9. Security Incident & Breach Notification Protocol</h2>
        <p>
            In the improbable event of a confirmed security incident or unauthorized disclosure involving sensitive personal data:
        </p>
        <ol>
            <li>DALC's Incident Response Team will immediately isolate the affected accounts or server nodes.</li>
            <li>Pursuant to Section 20 of RA 10173 and NPC Circular 16-03, DALC will notify both the <strong>National Privacy Commission</strong> and the affected parents/guardians within <strong>seventy-two (72) hours</strong> of breach assessment.</li>
            <li>The notice will detail the nature of the breach, the affected data fields, measures taken to mitigate damage, and recommended precautions for the data subject.</li>
        </ol>
    </section>

    <section id="dpo-contact">
        <h2><i class="fa fa-envelope-open-text text-[#1E2657]"></i> 10. Inquiries & Data Protection Officer Contact</h2>
        <p>
            For privacy inquiries, rights assertion, or data updates, please contact our institutional Data Protection Officer:
        </p>
        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 text-xs space-y-1">
            <div><strong class="text-[#1E2657]">Attention:</strong> Data Protection Officer (DPO)</div>
            <div><strong class="text-[#1E2657]">Office:</strong> Dream Achievers Learning Center — Compliance Unit</div>
            <div><strong class="text-[#1E2657]">Direct Email:</strong> <a href="mailto:dpo@dalc.edu.ph" class="text-[#4F6AF5] underline font-bold">dpo@dalc.edu.ph</a></div>
            <div><strong class="text-[#1E2657]">Campus Hotline:</strong> 0936-952-3782</div>
            <div><strong class="text-[#1E2657]">Mailing Address:</strong> Dream Achievers Learning Center, Philippines</div>
        </div>
    </section>

</div>

@endsection
