@extends('legal.layout')

@section('title', 'Terms of Use')
@section('page_title', 'Terms of Use & System Regulations')
@section('doc_heading', 'Terms of Use Agreement')

@section('toc')
<ul class="space-y-2 text-xs font-bold text-[#4B5563] list-none p-0 m-0">
    <li><a href="#acceptance" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 1. Acceptance & Scope</a></li>
    <li><a href="#accounts" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 2. User Accounts & 3-Gate MFA</a></li>
    <li><a href="#roles" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 3. User Roles & Access Rights</a></li>
    <li><a href="#conduct" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 4. Acceptable Use & Prohibitions</a></li>
    <li><a href="#sped-records" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 5. SPED Records & File Vault</a></li>
    <li><a href="#discussions" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 6. Communication Etiquette</a></li>
    <li><a href="#disclaimers" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 7. Medical & Emergency Disclaimer</a></li>
    <li><a href="#intellectual-property" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 8. Intellectual Property</a></li>
    <li><a href="#termination" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 9. Termination of Access</a></li>
    <li><a href="#governing-law" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 10. Governing Law & Jurisdiction</a></li>
</ul>
@endsection

@section('document_body')

<div class="space-y-6">

    <section id="acceptance">
        <h2><i class="fa fa-check-circle text-[#10B981]"></i> 1. Acceptance of Terms & Institutional Scope</h2>
        <p>
            Welcome to the <strong>Dream Achievers Learning Center (DALC) System</strong>. By accessing, logging into, or using this web application (the "System"), you ("User", "Teacher", "Guardian", or "Administrator") acknowledge that you have read, understood, and agree to be legally bound by these Terms of Use, along with our <a href="{{ route('legal.privacy') }}" class="text-[#4F6AF5] font-bold underline">Privacy Policy</a>, <a href="{{ route('legal.ai-ethics') }}" class="text-[#4F6AF5] font-bold underline">AI SPED Assistant Ethics Policy</a>, and <a href="{{ route('legal.parental-consent') }}" class="text-[#4F6AF5] font-bold underline">Parental Consent & Media Release Agreement</a>.
        </p>
        <p>
            The System is a specialized private platform created solely to facilitate <strong>Special Education (SPED) administration</strong>, Individualized Education Program (IEP) tracking, clinical observation logging, and secure communication between Dream Achievers Learning Center educators and registered parents/guardians.
        </p>
    </section>

    <section id="accounts">
        <h2><i class="fa fa-shield-halved text-[#4F6AF5]"></i> 2. User Accounts, Security & Mandatory 3-Gate Perimeter</h2>
        <p>
            To protect minors' highly sensitive personal and developmental data, every account is subject to DALC's mandatory <strong>3-Gate Security Perimeter</strong>:
        </p>
        <ul>
            <li><strong>Gate 1 — Primary Authentication:</strong> Users must log in using their verified username/email and strong cryptographic password.</li>
            <li><strong>Gate 2 — Mandatory Password Initialization:</strong> Accounts provisioned with temporary default credentials must immediately update to a private, high-entropy password (minimum 8 characters with uppercase, lowercase, numbers, and symbols) prior to gaining system access.</li>
            <li><strong>Gate 3 — Mandatory Two-Factor Authentication (TOTP 2FA):</strong> All users must link an RFC 6238-compliant authenticator app (such as Google Authenticator or Microsoft Authenticator). Access to student records without completing MFA verification is strictly barred.</li>
        </ul>
        <p>
            <strong>Account Responsibility:</strong> You are solely responsible for maintaining the confidentiality of your credentials. Account sharing—whether between family members, colleagues, or third parties—is strictly prohibited. Any activity originating from your authenticated session is deemed your direct responsibility. You must report any suspected credential theft or unauthorized device access immediately to the DALC Administration.
        </p>
    </section>

    <section id="roles">
        <h2><i class="fa fa-users text-[#C4922E]"></i> 3. Role-Based Access Control (RBAC) & Scopes</h2>
        <p>
            Access within the System is partitioned strictly according to user roles:
        </p>
        <ul>
            <li><strong>Administrators:</strong> Institutional governance, user provisioning, academic quarter and calendar scheduling, master student archival, and official report approvals.</li>
            <li><strong>Teachers & SPED Specialists:</strong> Daily pedagogical observation logging, IEP SMART goal authoring, quarterly progress report draft generation, student file vault uploads, and parent collaboration for assigned learners.</li>
            <li><strong>Guardians:</strong> Read-only access strictly restricted to their enrolled children's profiles, observation feeds, progress reports, IEP milestone tracking, and teacher-parent discussion threads.</li>
        </ul>
        <p>
            Attempting to bypass role restrictions, manipulate URLs, query non-assigned student identifiers, or tamper with API parameters constitutes a material security violation resulting in immediate account revocation and potential legal reporting under the <em>Cybercrime Prevention Act of 2012 (RA 10175)</em>.
        </p>
    </section>

    <section id="conduct">
        <h2><i class="fa fa-ban text-rose-500"></i> 4. Acceptable Use & Prohibited Activities</h2>
        <p>
            Users agree to use the DALC System strictly for legitimate educational, developmental, and home-school coordination purposes. You expressly agree <strong>NOT</strong> to:
        </p>
        <ol>
            <li>Capture screenshots, download, copy, or redistribute photos, medical evaluations, diagnostic assessments, or observation notes of other learners who may appear in announcements or group activities.</li>
            <li>Publish or post student records, behavioral challenges, or IEP benchmarks onto public social media platforms (Facebook, Instagram, TikTok, etc.).</li>
            <li>Use automated bots, web scrapers, spiders, or extraction scripts to harvest student or teacher data.</li>
            <li>Attempt to reverse-engineer, decompile, or tamper with the System's source code, security gates, or encryption keys.</li>
            <li>Upload malicious payloads, viruses, executable binaries, or contaminated files into the Secure File Vault or comment threads.</li>
            <li>Harass, intimidate, defame, or use inappropriate language toward school personnel, therapists, or other guardians in discussion channels.</li>
        </ol>
    </section>

    <section id="sped-records">
        <h2><i class="fa fa-folder-closed text-[#3B82F6]"></i> 5. Confidential SPED Records & Private File Vault</h2>
        <p>
            All diagnostic documents, neurodevelopmental assessments, medical certifications, and photographic logs stored in the DALC File Vault reside on encrypted, private server disks (`storage/app/`) and are streamed dynamically via the authenticated `SecureFileController`.
        </p>
        <p>
            Users acknowledge that these documents contain <strong>Sensitive Personal Information (SPI)</strong> governed by the Philippine <em>Data Privacy Act of 2012 (Republic Act No. 10173)</em>. Authorized downloads are meant solely for the personal archival of the student's legal guardians and pedagogical planning by assigned school staff.
        </p>
    </section>

    <section id="discussions">
        <h2><i class="fa fa-comments text-[#10B981]"></i> 6. Home-School Discussion Channels & Communication Etiquette</h2>
        <p>
            The polymorphic comment threads attached to Daily Logs, IEP Goals, Progress Reports, and Announcements are dedicated channels for collaborative educational support.
        </p>
        <ul>
            <li>Communications must be constructive, child-centered, respectful, and focused on learning objectives and coping strategies.</li>
            <li>Teachers and school personnel will review and respond to communications during official school operating hours (Mondays to Fridays, 8:00 AM to 5:00 PM).</li>
            <li>Discussion threads are subject to administrative auditing for child protection and quality assurance purposes pursuant to <em>DepEd Child Protection Policy (DepEd Order No. 40, s. 2012)</em>.</li>
        </ul>
    </section>

    <section id="disclaimers">
        <h2><i class="fa fa-triangle-exclamation text-amber-500"></i> 7. Medical, Clinical & Emergency Disclaimer</h2>
        <p>
            <strong>NON-EMERGENCY NOTICE:</strong> The DALC System is NOT an emergency response platform. In the event of acute medical emergencies, psychiatric crises, or urgent physical safety issues, contact local emergency services (911) or bring the individual to the nearest hospital emergency room immediately.
        </p>
        <p>
            <strong>CLINICAL PROGRESS DISCLAIMER:</strong> All IEP goals, developmental benchmarks, and progress narratives recorded within the System reflect pedagogical assessments conducted in an educational setting. They do not constitute formal medical diagnoses, psychiatric prescriptions, or guarantees of developmental timelines. Every child progresses at their individual pace.
        </p>
    </section>

    <section id="intellectual-property">
        <h2><i class="fa fa-copyright text-[#4F6AF5]"></i> 8. Intellectual Property Rights</h2>
        <p>
            All software code, user interface designs, logos, custom SPED rubrics, curriculum templates, and proprietary IEP methodologies developed by or for Dream Achievers Learning Center are the exclusive intellectual property of Dream Achievers Learning Center. Unauthorized commercial exploitation, white-labeling, or reproduction is strictly prohibited.
        </p>
    </section>

    <section id="termination">
        <h2><i class="fa fa-user-xmark text-rose-600"></i> 9. Account Suspension & Offboarding</h2>
        <p>
            DALC reserves the right to suspend or terminate portal access immediately, without prior notice, upon:
        </p>
        <ul>
            <li>Material breach of these Terms of Use or the Student Code of Conduct.</li>
            <li>Compromised account security or suspected malicious activity.</li>
            <li>Official withdrawal or graduation of the student from Dream Achievers Learning Center.</li>
            <li>Separation or resignation of staff/educators from the institution.</li>
        </ul>
        <p>
            Upon account deactivation, historical records remain protected within the institutional master archive in accordance with educational record-keeping retention guidelines.
        </p>
    </section>

    <section id="governing-law">
        <h2><i class="fa fa-gavel text-[#1E2657]"></i> 10. Governing Law, Dispute Resolution & Jurisdiction</h2>
        <p>
            These Terms of Use shall be governed by and construed in accordance with the laws of the <strong>Republic of the Philippines</strong>, including but not limited to <em>Republic Act No. 10173 (Data Privacy Act of 2012)</em>, <em>Republic Act No. 10175 (Cybercrime Prevention Act of 2012)</em>, <em>Republic Act No. 11650 (Inclusive Education Act)</em>, and relevant Department of Education (DepEd) regulations.
        </p>
        <p>
            Any dispute arising out of or related to the use of this System shall first be addressed through good-faith administrative dialog with the DALC Board of Trustees. If unresolved, disputes shall be submitted to the exclusive jurisdiction of the competent courts of the Philippines.
        </p>
    </section>

</div>

@endsection
