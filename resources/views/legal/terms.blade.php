@extends('legal.layout')

@section('title', 'Terms and Conditions')
@section('page_title', 'Terms and Conditions of IEP Management and Generation System')
@section('doc_heading', 'Terms and Conditions Agreement')

@section('toc')
<ul class="space-y-2 text-xs font-bold text-[#4B5563] list-none p-0 m-0">
    <li class="text-[10px] uppercase tracking-wider text-[#9CA3AF] font-black pt-1">Institutional Scope</li>
    <li><a href="#preamble" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> Acceptance & Purpose</a></li>

    <li class="text-[10px] uppercase tracking-wider text-[#3B82F6] font-black pt-3">For Guardian / Parent Users</li>
    <li><a href="#parent-security" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 1. Account Responsibility & Security</a></li>
    <li><a href="#parent-confidentiality" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 2. Confidentiality of Learner Data</a></li>
    <li><a href="#parent-media" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 3. Protection of Visual Media</a></li>
    <li><a href="#parent-feedback" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 4. Collaborative Feedback Conduct</a></li>
    <li><a href="#parent-availability" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 5. Non-Liability & Service Availability</a></li>

    <li class="text-[10px] uppercase tracking-wider text-[#10B981] font-black pt-3">For School Users (Staff)</li>
    <li><a href="#school-rbac" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 1. User Accountability & RBAC</a></li>
    <li><a href="#school-dpa" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 2. Data Privacy Act (R.A. 10173)</a></li>
    <li><a href="#school-human-in-the-loop" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 3. Mandatory Human-in-the-Loop</a></li>
    <li><a href="#school-stamp" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 4. Report Approval & Digital Stamp</a></li>
    <li><a href="#school-scope" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 5. Scope & Functional Boundaries</a></li>
    <li><a href="#school-archiving" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 6. Retention, MFA Archiving & Termination</a></li>

    <li class="text-[10px] uppercase tracking-wider text-[#9CA3AF] font-black pt-3">Legal Governance</li>
    <li><a href="#governing-law" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> Statutory Governance & Jurisdiction</a></li>
</ul>
@endsection

@section('document_body')

<div class="space-y-8">

    <!-- PREAMBLE & INSTITUTIONAL SCOPE -->
    <section id="preamble">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-[#3B82F6] p-4 rounded-r-xl">
            <h2 class="text-base font-extrabold text-[#1E2657] m-0 flex items-center gap-2 border-b-0 pb-0">
                <i class="fa fa-school text-[#3B82F6]"></i> Institutional Scope & Acceptance
            </h2>
            <p class="text-xs text-[#1E2657] leading-relaxed mt-2 mb-0">
                Welcome to the <strong>Web-Based AI-Assisted Individualized Education Plan (IEP) Management and Generation System</strong> for <strong>Dream Achievers Learning Center PH</strong>. This system is a specialized private platform engineered to streamline Special Education (SPED) administration, student developmental tracking, baseline assessments, and home-school coordination.
            </p>
            <p class="text-xs text-[#4B5563] leading-relaxed mt-1.5 mb-0">
                By accessing, logging in, or operating the system, all users agree to comply with the terms set forth herein according to their assigned system classification.
            </p>
        </div>
    </section>

    <!-- ===================================================================== -->
    <!-- SECTION 1: FOR GUARDIAN / PARENT USERS                                -->
    <!-- ===================================================================== -->
    <div class="border-t-2 border-[#3B82F6]/20 pt-6">
        <div class="flex items-center gap-2 mb-3">
            <span class="px-3 py-1 rounded-lg text-xs font-black bg-blue-100 text-[#1E2657] uppercase tracking-wider">
                <i class="fa fa-user-group text-[#3B82F6] mr-1"></i> Part I
            </span>
            <h2 class="text-lg font-black text-[#1E2657] m-0 border-b-0 p-0">For Guardian / Parent Users</h2>
        </div>
        <p class="text-xs text-[#4B5563] leading-relaxed mb-6">
            By using the Web-Based AI-Assisted Individualized Education Plan Management and Generation System for Dream Achievers Learning Center PH, designed to grant authorized guardians access to student developmental milestones, Individualized Education Plans (IEPs), daily logs, and home interventions, you agree to the following Terms and Conditions. Please read them carefully to ensure you understand your rights, responsibilities, and data protections.
        </p>

        <div class="space-y-6">
            <section id="parent-security" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1E2657] flex items-center gap-2 m-0 pb-2 border-b border-slate-100">
                    <i class="fa fa-key text-[#C4922E]"></i> 1. Account Responsibility and Security
                </h3>
                <p class="text-xs text-[#374151] mt-2.5 leading-relaxed">
                    You are responsible for maintaining the strict confidentiality of your login credentials (username and temporary/permanent passwords). You agree to comply with the mandatory initial password change upon your first login. To safeguard minor learners' records, credential sharing across individuals or third parties is strictly prohibited. Any unauthorized access, suspected compromise, or misuse of your account must be reported to the school administration immediately.
                </p>
            </section>

            <section id="parent-confidentiality" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1E2657] flex items-center gap-2 m-0 pb-2 border-b border-slate-100">
                    <i class="fa fa-shield-cat text-[#3B82F6]"></i> 2. Confidentiality of Sensitive Learner Data
                </h3>
                <p class="text-xs text-[#374151] mt-2.5 leading-relaxed">
                    Through the Parent Portal, you are granted secure access to sensitive developmental, behavioral, and academic records regarding your child. You agree not to disclose, publicly disseminate, re-upload, or share downloadable PDF reports, clinical baselines, or associated learning objectives without appropriate written authorization from Dream Achievers Learning Center PH.
                </p>
            </section>

            <section id="parent-media" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1E2657] flex items-center gap-2 m-0 pb-2 border-b border-slate-100">
                    <i class="fa fa-camera-retro text-[#10B981]"></i> 3. Privacy and Protection of Visual Media
                </h3>
                <p class="text-xs text-[#374151] mt-2.5 leading-relaxed">
                    Classroom activity photos uploaded by teachers to document learning sessions are strictly protected and session-authenticated through secure system tokens. You agree that any images displaying your child or fellow learners are strictly for personal developmental monitoring only. Unauthorized downloading, posting, or republishing of these photos on public social media platforms (e.g., Facebook, Instagram, TikTok) or external communication channels is strictly prohibited to protect the privacy and safety of all minors.
                </p>
            </section>

            <section id="parent-feedback" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1E2657] flex items-center gap-2 m-0 pb-2 border-b border-slate-100">
                    <i class="fa fa-comments text-[#8B5CF6]"></i> 4. Collaborative Feedback Conduct
                </h3>
                <p class="text-xs text-[#374151] mt-2.5 leading-relaxed">
                    The platform provides a direct, two-way Collaborative Feedback thread attached to daily logs, IEP objectives, quarterly progress reports, and School Announcements. You agree to utilize this communication channel in a professional, constructive, and respectful manner to facilitate coordinated learning between the classroom and home. Communications are monitored for child welfare and institutional decorum pursuant to the DepEd Child Protection Policy.
                </p>
            </section>

            <section id="parent-liability" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1E2657] flex items-center gap-2 m-0 pb-2 border-b border-slate-100">
                    <i class="fa fa-triangle-exclamation text-amber-500"></i> 5. Non-Liability and Service Availability
                </h3>
                <p class="text-xs text-[#374151] mt-2.5 leading-relaxed">
                    The IEP Management System operates as an online, web-dependent platform requiring a stable internet connection. Dream Achievers Learning Center PH and the system developers are not liable for temporary service interruptions or network-related delays in notifications or report updates.
                </p>
                <div class="mt-2.5 p-3 bg-amber-50 rounded-xl border border-amber-200 text-[11px] text-amber-900 leading-relaxed font-semibold">
                    <i class="fa fa-circle-exclamation mr-1 text-amber-600"></i> <strong>Medical Non-Emergency Notice:</strong> This platform is strictly an educational tool and does not provide clinical emergency dispatch. For acute medical, psychiatric, or physical safety emergencies, please contact emergency hotlines (911) or medical practitioners immediately.
                </div>
            </section>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- SECTION 2: FOR SCHOOL USERS (ADMINISTRATORS AND SPED TEACHERS)        -->
    <!-- ===================================================================== -->
    <div class="border-t-2 border-[#10B981]/20 pt-6">
        <div class="flex items-center gap-2 mb-3">
            <span class="px-3 py-1 rounded-lg text-xs font-black bg-emerald-100 text-[#065F46] uppercase tracking-wider">
                <i class="fa fa-chalkboard-user text-[#10B981] mr-1"></i> Part II
            </span>
            <h2 class="text-lg font-black text-[#1E2657] m-0 border-b-0 p-0">For School Users (Administrators and SPED Teachers)</h2>
        </div>
        <p class="text-xs text-[#4B5563] leading-relaxed mb-6">
            The Web-Based AI-Assisted Individualized Education Plan Management and Generation System provides school personnel with a centralized workspace to manage student profiles, structure daily classroom observations, formulate measurable learning goals, and streamline periodic reporting. By accessing and operating the system, you agree to the following Terms and Conditions:
        </p>

        <div class="space-y-6">
            <section id="school-rbac" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1E2657] flex items-center gap-2 m-0 pb-2 border-b border-slate-100">
                    <i class="fa fa-user-lock text-[#4F6AF5]"></i> 1. User Accountability and Role-Based Access
                </h3>
                <p class="text-xs text-[#374151] mt-2.5 leading-relaxed">
                    School personnel (Administrators and SPED Teachers) are responsible for maintaining the confidentiality of their login credentials and for all actions performed under their accounts. System access is strictly governed by Role-Based Access Control (RBAC). Personnel must operate exclusively within their assigned access levels and are strictly prohibited from attempting to bypass system restrictions, view unauthorized student data, or alter account permissions beyond their designated responsibilities.
                </p>
            </section>

            <section id="school-dpa" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1E2657] flex items-center gap-2 m-0 pb-2 border-b border-slate-100">
                    <i class="fa fa-scale-balanced text-[#C4922E]"></i> 2. Data Privacy Act Compliance (R.A. 10173)
                </h3>
                <p class="text-xs text-[#374151] mt-2.5 leading-relaxed">
                    School users handle highly sensitive student information, including diagnostic profiles (e.g., Autism Spectrum Disorder, ADHD, Down Syndrome), baseline evaluations, and pediatric records. Personnel agree to comply strictly with the Philippine Data Privacy Act of 2012, ensuring student files and encrypted database records remain confidential and accessed solely on a legitimate, need-to-know pedagogical basis.
                </p>
            </section>

            <section id="school-human-in-the-loop" class="bg-white p-5 rounded-2xl border-2 border-emerald-500/30 bg-emerald-50/20 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#065F46] flex items-center gap-2 m-0 pb-2 border-b border-emerald-100">
                    <i class="fa fa-robot text-[#10B981]"></i> 3. Mandatory Human-in-the-Loop Validation
                </h3>
                <p class="text-xs text-[#374151] mt-2.5 leading-relaxed">
                    All artificial intelligence integrations—including the <strong>AI Daily Log Standardizer</strong>, <strong>SMART Goal Generator</strong>, <strong>AI Progress Summarizer</strong>, and <strong>Home Recommendation Assistant</strong>—serve strictly as drafting aids. SPED Teachers and Administrators agree that all AI outputs must undergo human verification, pedagogical review, and manual editing prior to publishing daily reports or periodic summaries. Personnel remain fully accountable for the educational appropriateness and accuracy of finalized records.
                </p>
            </section>

            <section id="school-stamp" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1E2657] flex items-center gap-2 m-0 pb-2 border-b border-slate-100">
                    <i class="fa fa-stamp text-[#3B82F6]"></i> 4. Report Approval and Digital Stamp Authorization
                </h3>
                <p class="text-xs text-[#374151] mt-2.5 leading-relaxed">
                    The partner institution authorizes the adoption and electronic application of an official digital stamp and administrator signature to authenticate school records generated by the system. School Administrators must formally review and approve all periodic summaries before they are published to the guardian portal. Approving a document automatically appends the Administrator’s digital stamp and signature, confirming the report's official institutional status.
                </p>
            </section>

            <section id="school-scope" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1E2657] flex items-center gap-2 m-0 pb-2 border-b border-slate-100">
                    <i class="fa fa-compass-drafting text-rose-500"></i> 5. Scope and Functional Boundaries
                </h3>
                <p class="text-xs text-[#374151] mt-2.5 leading-relaxed">
                    Personnel acknowledge that the system is strictly limited to IEP-related behavioral, academic, and developmental documentation. It must not be utilized for standardized diagnostic clinical scoring, external medical monitoring, tuition/billing, or general school admissions outside the defined scope of Dream Achievers Learning Center PH.
                </p>
            </section>

            <section id="school-archiving" class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <h3 class="text-sm font-extrabold text-[#1E2657] flex items-center gap-2 m-0 pb-2 border-b border-slate-100">
                    <i class="fa fa-box-archive text-[#1E2657]"></i> 6. Record Retention, Archiving, and Account Termination
                </h3>
                <p class="text-xs text-[#374151] mt-2.5 leading-relaxed">
                    Administrators agree to follow standardized archiving protocols, including executing Multi-Factor Authentication (MFA) prior to initiating Year-End Rollovers and capturing student snapshots. Dream Achievers Learning Center PH reserves the right to suspend or revoke system access for any user who violates data handling policies, misuses AI generation utilities, or breaches student record confidentiality.
                </p>
            </section>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- SECTION 3: LEGAL GOVERNING LAW & JURISDICTION                         -->
    <!-- ===================================================================== -->
    <section id="governing-law" class="border-t-2 border-slate-200 pt-6">
        <h2 class="text-sm font-extrabold text-[#1E2657] flex items-center gap-2 m-0 pb-2 border-b-0">
            <i class="fa fa-gavel text-[#1E2657]"></i> Statutory Governance, Child Protection & Dispute Resolution
        </h2>
        <p class="text-xs text-[#4B5563] leading-relaxed mt-2">
            These Terms and Conditions shall be governed by and construed in accordance with the laws of the <strong>Republic of the Philippines</strong>, including <em>Republic Act No. 10173 (Data Privacy Act of 2012)</em>, <em>Republic Act No. 10175 (Cybercrime Prevention Act of 2012)</em>, <em>Republic Act No. 11650 (Instituting a Policy of Inclusion and Services for Learners with Disabilities)</em>, and the <em>Department of Education Child Protection Policy (DepEd Order No. 40, s. 2012)</em>.
        </p>
        <p class="text-xs text-[#4B5563] leading-relaxed mt-2 mb-0">
            Any disputes arising from or related to the operation of the system shall first be submitted to administrative dialog with the Dream Achievers Learning Center Administration. If unresolved, disputes shall fall under the jurisdiction of the competent courts of the Philippines.
        </p>
    </section>

</div>

@endsection
