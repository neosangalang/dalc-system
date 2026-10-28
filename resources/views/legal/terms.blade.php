@extends('legal.layout')

@section('title', 'Terms and Conditions')
@section('page_title', 'Terms and Conditions of IEP Management and Generation System')
@section('doc_heading', 'TERMS AND CONDITIONS OF IEP MANAGEMENT AND GENERATION SYSTEM')

@section('toc')
<ul class="space-y-2 text-xs font-bold text-[#4B5563] list-none p-0 m-0">
    <li class="text-[10px] uppercase tracking-wider text-[#3B82F6] font-black pt-1">For Guardian / Parent Users</li>
    <li><a href="#parent-security" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> Account Responsibility & Security</a></li>
    <li><a href="#parent-confidentiality" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> Confidentiality of Learner Data</a></li>
    <li><a href="#parent-media" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> Privacy & Protection of Visual Media</a></li>
    <li><a href="#parent-feedback" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> Collaborative Feedback Conduct</a></li>
    <li><a href="#parent-availability" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> Non-Liability & Service Availability</a></li>

    <li class="text-[10px] uppercase tracking-wider text-[#10B981] font-black pt-3">For School Users (Staff)</li>
    <li><a href="#school-rbac" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> User Accountability & Role-Based Access</a></li>
    <li><a href="#school-dpa" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> Data Privacy Act Compliance (R.A. 10173)</a></li>
    <li><a href="#school-human-in-the-loop" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> Mandatory Human-in-the-Loop Validation</a></li>
    <li><a href="#school-stamp" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> Report Approval & Digital Stamp</a></li>
    <li><a href="#school-scope" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> Scope & Functional Boundaries</a></li>
    <li><a href="#school-archiving" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> Record Retention, Archiving & Termination</a></li>
</ul>
@endsection

@section('document_body')

<div class="space-y-8">

    <!-- ===================================================================== -->
    <!-- SECTION 1: FOR GUARDIAN / PARENT USERS                                -->
    <!-- ===================================================================== -->
    <div>
        <div class="flex items-center gap-2 mb-3">
            <span class="px-3 py-1 rounded-lg text-xs font-black bg-blue-100 text-[#1E2657] uppercase tracking-wider">
                <i class="fa fa-user-group text-[#3B82F6] mr-1"></i> Part I
            </span>
            <h2 class="text-base sm:text-lg font-black text-[#1E2657] m-0 border-b-0 p-0 uppercase tracking-wide">
                FOR GUARDIAN / PARENT USERS:
            </h2>
        </div>
        <div class="bg-blue-50/60 border-l-4 border-[#3B82F6] p-4 rounded-r-xl mb-6">
            <p class="text-xs sm:text-sm text-[#1E2657] leading-relaxed m-0 font-medium">
                By using the <strong>Web-Based AI-Assisted Individualized Education Plan Management and Generation System for Dreams Achievers Learning Center PH</strong>, designed to grant authorized guardians access to student developmental milestones, Individualized Education Plans (IEPs), daily logs, and home interventions, you agree to the following Terms and Conditions. Please read them carefully to ensure you understand your rights, responsibilities, and data protections.
            </p>
        </div>

        <div class="space-y-4">
            <!-- 1. Account Responsibility and Security -->
            <section id="parent-security" class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:border-[#3B82F6]/40 transition-colors">
                <h3 class="text-sm sm:text-base font-extrabold text-[#1E2657] flex items-center gap-2.5 m-0 pb-2.5 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-[#C4922E] flex items-center justify-center text-xs shrink-0 font-black">1</span>
                    <i class="fa fa-key text-[#C4922E] text-xs"></i>
                    <span>Account Responsibility and Security</span>
                </h3>
                <p class="text-xs sm:text-[13px] text-[#374151] mt-3 leading-relaxed m-0">
                    You are responsible for maintaining the strict confidentiality of your login credentials (username and temporary/permanent passwords). You agree to comply with the mandatory initial password change upon your first login. Any unauthorized access, suspected compromise, or misuse of your account must be reported to the school administration immediately.
                </p>
            </section>

            <!-- 2. Confidentiality of Sensitive Learner Data -->
            <section id="parent-confidentiality" class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:border-[#3B82F6]/40 transition-colors">
                <h3 class="text-sm sm:text-base font-extrabold text-[#1E2657] flex items-center gap-2.5 m-0 pb-2.5 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-blue-100 text-[#3B82F6] flex items-center justify-center text-xs shrink-0 font-black">2</span>
                    <i class="fa fa-shield-halved text-[#3B82F6] text-xs"></i>
                    <span>Confidentiality of Sensitive Learner Data</span>
                </h3>
                <p class="text-xs sm:text-[13px] text-[#374151] mt-3 leading-relaxed m-0">
                    Through the Parent Portal, you are granted secure access to sensitive developmental, behavioral, and academic records regarding your child. You agree not to disclose, publicly disseminate, re-upload, or share downloadable PDF reports, or associated learning objectives without appropriate authorization.
                </p>
            </section>

            <!-- 3. Privacy and Protection of Visual Media -->
            <section id="parent-media" class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:border-[#3B82F6]/40 transition-colors">
                <h3 class="text-sm sm:text-base font-extrabold text-[#1E2657] flex items-center gap-2.5 m-0 pb-2.5 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-[#10B981] flex items-center justify-center text-xs shrink-0 font-black">3</span>
                    <i class="fa fa-camera-retro text-[#10B981] text-xs"></i>
                    <span>Privacy and Protection of Visual Media</span>
                </h3>
                <p class="text-xs sm:text-[13px] text-[#374151] mt-3 leading-relaxed m-0">
                    Classroom activity photos uploaded by teachers to document learning sessions are strictly protected and session-authenticated. You agree that any images displaying your child or fellow learners are strictly for personal monitoring only. Unauthorized downloading, posting, or republishing of these photos on public social media platforms or external channels is strictly prohibited.
                </p>
            </section>

            <!-- 4. Collaborative Feedback Conduct -->
            <section id="parent-feedback" class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:border-[#3B82F6]/40 transition-colors">
                <h3 class="text-sm sm:text-base font-extrabold text-[#1E2657] flex items-center gap-2.5 m-0 pb-2.5 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-purple-100 text-[#8B5CF6] flex items-center justify-center text-xs shrink-0 font-black">4</span>
                    <i class="fa fa-comments text-[#8B5CF6] text-xs"></i>
                    <span>Collaborative Feedback Conduct</span>
                </h3>
                <p class="text-xs sm:text-[13px] text-[#374151] mt-3 leading-relaxed m-0">
                    The platform provides a direct, two-way Collaborative Feedback thread attached to reports, IEP objectives, and School Announcements. You agree to utilize this communication channel in a professional, constructive, and respectful manner to facilitate coordinated learning between the classroom and home.
                </p>
            </section>

            <!-- 5. Non-Liability and Service Availability -->
            <section id="parent-availability" class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:border-[#3B82F6]/40 transition-colors">
                <h3 class="text-sm sm:text-base font-extrabold text-[#1E2657] flex items-center gap-2.5 m-0 pb-2.5 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center text-xs shrink-0 font-black">5</span>
                    <i class="fa fa-wifi text-amber-500 text-xs"></i>
                    <span>Non-Liability and Service Availability</span>
                </h3>
                <p class="text-xs sm:text-[13px] text-[#374151] mt-3 leading-relaxed m-0">
                    The IEP Management System operates as an online, web-dependent platform requiring a stable internet connection. Dreams Achievers Learning Center PH and the system developers are not liable for temporary service interruptions, network-related delays in notifications or report updates.
                </p>
            </section>
        </div>
    </div>

    <!-- ===================================================================== -->
    <!-- SECTION 2: FOR SCHOOL USERS (ADMINISTRATORS AND SPED TEACHERS)        -->
    <!-- ===================================================================== -->
    <div class="border-t-2 border-slate-200/80 pt-8">
        <div class="flex items-center gap-2 mb-3">
            <span class="px-3 py-1 rounded-lg text-xs font-black bg-emerald-100 text-[#065F46] uppercase tracking-wider">
                <i class="fa fa-chalkboard-user text-[#10B981] mr-1"></i> Part II
            </span>
            <h2 class="text-base sm:text-lg font-black text-[#1E2657] m-0 border-b-0 p-0 uppercase tracking-wide">
                FOR SCHOOL USERS (ADMINISTRATORS AND SPED TEACHERS):
            </h2>
        </div>
        <div class="bg-emerald-50/60 border-l-4 border-[#10B981] p-4 rounded-r-xl mb-6">
            <p class="text-xs sm:text-sm text-[#1E2657] leading-relaxed m-0 font-medium">
                The <strong>Web-Based AI-Assisted Individualized Education Plan Management and Generation System</strong> provides school personnel with a centralized workspace to manage student profiles, structure daily classroom observations, formulate measurable learning goals, and streamline periodic reporting. By accessing and operating the system, you agree to the following Terms and Conditions:
            </p>
        </div>

        <div class="space-y-4">
            <!-- 1. User Accountability and Role-Based Access -->
            <section id="school-rbac" class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:border-[#10B981]/40 transition-colors">
                <h3 class="text-sm sm:text-base font-extrabold text-[#1E2657] flex items-center gap-2.5 m-0 pb-2.5 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-blue-100 text-[#4F6AF5] flex items-center justify-center text-xs shrink-0 font-black">1</span>
                    <i class="fa fa-user-lock text-[#4F6AF5] text-xs"></i>
                    <span>User Accountability and Role-Based Access</span>
                </h3>
                <p class="text-xs sm:text-[13px] text-[#374151] mt-3 leading-relaxed m-0">
                    School personnel (Administrators and SPED Teachers) are responsible for maintaining the confidentiality of their login credentials and for all actions performed under their accounts. System access is strictly governed by Role-Based Access Control (RBAC). Personnel must operate exclusively within their assigned access levels and are strictly prohibited from attempting to bypass system restrictions, view unauthorized student data, or alter account permissions beyond their designated responsibilities.
                </p>
            </section>

            <!-- 2. Data Privacy Act Compliance (R.A. 10173) -->
            <section id="school-dpa" class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:border-[#10B981]/40 transition-colors">
                <h3 class="text-sm sm:text-base font-extrabold text-[#1E2657] flex items-center gap-2.5 m-0 pb-2.5 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-[#C4922E] flex items-center justify-center text-xs shrink-0 font-black">2</span>
                    <i class="fa fa-scale-balanced text-[#C4922E] text-xs"></i>
                    <span>Data Privacy Act Compliance (R.A. 10173)</span>
                </h3>
                <p class="text-xs sm:text-[13px] text-[#374151] mt-3 leading-relaxed m-0">
                    School users handle highly sensitive student information, including diagnostic profiles (e.g., Autism Spectrum Disorder, ADHD), and baseline evaluations. Personnel agree to comply strictly with the Philippine Data Privacy Act of 2012, ensuring student files and encrypted database records remain confidential and accessed solely on a legitimate, need-to-know pedagogical basis.
                </p>
            </section>

            <!-- 3. Mandatory Human-in-the-Loop Validation -->
            <section id="school-human-in-the-loop" class="bg-white p-5 rounded-2xl border-2 border-emerald-500/30 bg-emerald-50/20 shadow-sm">
                <h3 class="text-sm sm:text-base font-extrabold text-[#065F46] flex items-center gap-2.5 m-0 pb-2.5 border-b border-emerald-100">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-[#10B981] flex items-center justify-center text-xs shrink-0 font-black">3</span>
                    <i class="fa fa-robot text-[#10B981] text-xs"></i>
                    <span>Mandatory Human-in-the-Loop Validation</span>
                </h3>
                <p class="text-xs sm:text-[13px] text-[#374151] mt-3 leading-relaxed m-0">
                    All artificial intelligence integrations—including the AI Daily Log Standardizer, SMART Goal Generator, AI Progress Summarizer, and Home Recommendation Assistant—serve strictly as drafting aids. SPED Teachers and Administrators agree that all AI outputs must undergo human verification, pedagogical review, and manual editing prior to publishing daily reports or periodic summaries. Personnel remain fully accountable for the educational appropriateness and accuracy of finalized records.
                </p>
            </section>

            <!-- 4. Report Approval and Digital Stamp Authorization -->
            <section id="school-stamp" class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:border-[#10B981]/40 transition-colors">
                <h3 class="text-sm sm:text-base font-extrabold text-[#1E2657] flex items-center gap-2.5 m-0 pb-2.5 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-blue-100 text-[#3B82F6] flex items-center justify-center text-xs shrink-0 font-black">4</span>
                    <i class="fa fa-stamp text-[#3B82F6] text-xs"></i>
                    <span>Report Approval and Digital Stamp Authorization</span>
                </h3>
                <p class="text-xs sm:text-[13px] text-[#374151] mt-3 leading-relaxed m-0">
                    The partner institution complies with the adoption of an authorized digital stamp to authenticate school records generated by the system. School Administrators must formally review and approve all periodic summaries before they are published to the guardian portal. Approving a document automatically appends the Administrator’s digital stamp and signature, confirming the report's official status.
                </p>
            </section>

            <!-- 5. Scope and Functional Boundaries -->
            <section id="school-scope" class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:border-[#10B981]/40 transition-colors">
                <h3 class="text-sm sm:text-base font-extrabold text-[#1E2657] flex items-center gap-2.5 m-0 pb-2.5 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-rose-100 text-rose-500 flex items-center justify-center text-xs shrink-0 font-black">5</span>
                    <i class="fa fa-compass-drafting text-rose-500 text-xs"></i>
                    <span>Scope and Functional Boundaries</span>
                </h3>
                <p class="text-xs sm:text-[13px] text-[#374151] mt-3 leading-relaxed m-0">
                    Personnel acknowledge that the system is strictly limited to IEP-related behavioral, academic, and developmental documentation. It must not be utilized for standardized diagnostic clinical scoring, external medical monitoring, tuition/billing, or general school admissions outside the defined scope of Dreams Achievers Learning Center PH.
                </p>
            </section>

            <!-- 6. Record Retention, Archiving, and Account Termination -->
            <section id="school-archiving" class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-sm hover:border-[#10B981]/40 transition-colors">
                <h3 class="text-sm sm:text-base font-extrabold text-[#1E2657] flex items-center gap-2.5 m-0 pb-2.5 border-b border-slate-100">
                    <span class="w-7 h-7 rounded-lg bg-slate-200 text-[#1E2657] flex items-center justify-center text-xs shrink-0 font-black">6</span>
                    <i class="fa fa-box-archive text-[#1E2657] text-xs"></i>
                    <span>Record Retention, Archiving, and Account Termination</span>
                </h3>
                <p class="text-xs sm:text-[13px] text-[#374151] mt-3 leading-relaxed m-0">
                    Administrators agree to follow standardized archiving protocols, including executing Multi-Factor Authentication (MFA) prior to initiating Year-End Rollovers and capturing student snapshots. Dreams Achievers Learning Center PH reserves the right to suspend or revoke system access for any user who violates data handling policies, misuses AI generation utilities, or breaches student record confidentiality.
                </p>
            </section>
        </div>
    </div>

</div>

@endsection
