@extends('legal.layout')

@section('title', 'Parental Consent & Media Policy')
@section('page_title', 'Parental Consent & Media Release Policy')
@section('doc_heading', 'Parental / Guardian Consent & Media Release Agreement')

@section('toc')
<ul class="space-y-2 text-xs font-bold text-[#4B5563] list-none p-0 m-0">
    <li><a href="#digital-records" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 1. Digital Educational Record-Keeping</a></li>
    <li><a href="#medical-diagnostic" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 2. Medical & Diagnostic Data Sharing</a></li>
    <li><a href="#photo-release" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 3. Classroom Photography & Media Release</a></li>
    <li><a href="#social-media-ban" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 4. Anti-Peer Exposure & Social Media</a></li>
    <li><a href="#emergency-treatment" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 5. Emergency Medical Authorization</a></li>
    <li><a href="#withdrawal" class="hover:text-[#4F6AF5] transition-colors flex items-center gap-1.5"><i class="fa fa-angle-right text-[#C4922E]"></i> 6. Consent Modification & Withdrawal</a></li>
</ul>
@endsection

@section('document_body')

<div class="space-y-6">

    <div class="bg-amber-50 border-l-4 border-[#C4922E] p-4 rounded-r-xl">
        <div class="flex items-start gap-3">
            <i class="fa fa-child-reaching text-[#C4922E] text-lg mt-0.5 shrink-0"></i>
            <div class="text-xs text-[#1E2657] leading-relaxed">
                <strong>Parent / Guardian Partnership:</strong> At Dream Achievers Learning Center, we believe that effective Special Education rests upon open, transparent, and legally safeguarded home-school cooperation. This agreement defines how your child's milestones, media, and assessments are handled.
            </div>
        </div>
    </div>

    <section id="digital-records">
        <h2><i class="fa fa-id-card-clip text-[#4F6AF5]"></i> 1. Consent for Digital Educational Record-Keeping</h2>
        <p>
            By enrolling your child at Dream Achievers Learning Center and establishing an authorized Guardian account in the DALC System, you grant permission for the institution to create, maintain, and process a comprehensive digital developmental profile for your enrolled child. This includes:
        </p>
        <ul>
            <li>Learner demographics, birth dates, and Department of Education (DepEd) Learner Reference Number (LRN).</li>
            <li>Present Levels of Performance (PLOP) baselines, IEP milestone percentages, and quarterly evaluation rubrics.</li>
            <li>Daily classroom observation logs, behavioral coping notes, and attendance markers.</li>
            <li>Direct home-school discussion threads and notifications regarding your child's progress.</li>
        </ul>
    </section>

    <section id="medical-diagnostic">
        <h2><i class="fa fa-notes-medical text-[#3B82F6]"></i> 2. Medical, Diagnostic & Assessment Documentation</h2>
        <p>
            You authorize DALC administration and assigned SPED teachers to store, review, and reference confidential medical documents (developmental pediatrician evaluations, hearing/vision screening, psychological reports, and allied therapy notes) uploaded to the Secure File Vault.
        </p>
        <p>
            You acknowledge that these documents are accessed solely by authorized educators and therapists directly involved in your child's individualized curriculum planning.
        </p>
    </section>

    <section id="photo-release">
        <h2><i class="fa fa-camera text-[#10B981]"></i> 3. Pedagogical Photography & Media Release</h2>
        <p>
            Special Education relies significantly on visual evidence to track fine-motor grip, posture, sensory tolerance, task completion, and social peer interactions. You consent to DALC educators capturing photographs and short video clips of your child during:
        </p>
        <ul>
            <li>Structured classroom learning activities, sensory circuit sessions, and motor therapies.</li>
            <li>Campus social events, seasonal programs, and developmental celebration days.</li>
        </ul>
        <p>
            <strong>Media Vault Security:</strong> All pedagogical photos attached to daily logs reside on private, access-controlled storage disks and are streamed dynamically only to your authenticated guardian portal.
        </p>
    </section>

    <section id="social-media-ban">
        <h2><i class="fa fa-shield-virus text-rose-500"></i> 4. Strict Anti-Peer Exposure & Social Media Restriction</h2>
        <div class="bg-rose-50 border border-rose-200 rounded-xl p-4 text-xs text-rose-900 leading-relaxed font-medium">
            <p class="m-0 mb-2">
                <strong>CRITICAL PRIVACY RULE FOR ALL PARENTS & GUARDIANS:</strong>
            </p>
            <p class="m-0 mb-2">
                While you are free to download and privately treasure photos that exclusively depict your own child, <strong>you are strictly prohibited from downloading, screenshotting, or publicly posting any photos, videos, or announcements that depict OTHER enrolled children</strong> onto social media (Facebook, Instagram, TikTok, YouTube, WhatsApp groups, etc.).
            </p>
            <p class="m-0">
                Violating another child's right to privacy constitutes a direct breach of this agreement and is actionable under Republic Act No. 10173 and Republic Act No. 7610 (Child Protection Act).
            </p>
        </div>
    </section>

    <section id="emergency-treatment">
        <h2><i class="fa fa-kit-medical text-[#C4922E]"></i> 5. Emergency Contact & Medical Authorization</h2>
        <p>
            Guardians must keep active, verified contact numbers registered in their profile. In the event of a sudden health or behavioral emergency on campus, DALC staff will immediately initiate contact through the registered guardian phone number.
        </p>
        <p>
            If guardians or emergency alternates cannot be reached within a reasonable timeframe, you authorize DALC administration to administer basic pediatric first aid and, if urgently necessary, transport the child to the nearest accredited medical center for immediate evaluation.
        </p>
    </section>

    <section id="withdrawal">
        <h2><i class="fa fa-pen-to-square text-[#1E2657]"></i> 6. Modification & Withdrawal of Consent</h2>
        <p>
            You have the right to request modifications to your media permissions (such as opting out of specific group photo publications) by submitting a written notice to the DALC Data Protection Officer (<a href="mailto:dpo@dalc.edu.ph" class="text-[#4F6AF5] underline font-bold">dpo@dalc.edu.ph</a>).
        </p>
        <p>
            Please note that core educational record-keeping (IEP goals, attendance, and official report cards) is mandatory for the continuation of enrollment and cannot be withdrawn while remaining an actively enrolled student.
        </p>
    </section>

</div>

@endsection
