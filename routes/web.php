<?php

use Illuminate\Support\Facades\Route;

// Import Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\QuarterlyCalendarController;
use App\Http\Controllers\Admin\ReportApprovalController;
use App\Http\Controllers\Admin\ArchiveController;
use App\Http\Controllers\Admin\AcademicQuarterController;

// Import Teacher Controllers
use App\Http\Controllers\Teacher\TeacherController;
use App\Http\Controllers\Teacher\TeacherStudentController;
use App\Http\Controllers\Teacher\DailyLogController;
use App\Http\Controllers\Teacher\IepGoalController;
use App\Http\Controllers\Teacher\FileController;
use App\Http\Controllers\Teacher\ReportController;

// Import Guardian Controllers
use App\Http\Controllers\Guardian\GuardianController;
use App\Http\Controllers\Guardian\FeedbackController;

// Import Shared Controllers
use App\Http\Controllers\CommentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SecureFileController;

// --- FIXED: Moved root logic to HomeController ---
Route::get('/', [HomeController::class, 'index'])->name('home');

// ==========================================
// SECURITY, PROFILE & SHARED FEATURES (GLOBAL)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/security-setup', [\App\Http\Controllers\Auth\PasswordSetupController::class, 'index'])->name('security.setup');
    Route::post('/security-setup', [\App\Http\Controllers\Auth\PasswordSetupController::class, 'store'])->name('security.setup.store');
    Route::get('/mfa-setup', [\App\Http\Controllers\Auth\MfaSetupController::class, 'index'])->name('security.mfa.setup');
    Route::post('/mfa-setup', [\App\Http\Controllers\Auth\MfaSetupController::class, 'verify'])->name('security.mfa.verify');
    Route::get('/mfa-challenge', [\App\Http\Controllers\Auth\MfaChallengeController::class, 'index'])->name('security.mfa.challenge');
    Route::post('/mfa-challenge', [\App\Http\Controllers\Auth\MfaChallengeController::class, 'verify'])->name('security.mfa.verify-login');

    // --- FIXED: Moved profile views to ProfileController ---
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/settings', [ProfileController::class, 'security'])->name('profile.security');

    Route::post('/comments', [CommentController::class, 'store'])->name('comments.store');
    
    // --- FIXED: Moved secure photo logic to SecureFileController ---
    Route::get('/secure-log-photo/{id}', [SecureFileController::class, 'show'])->name('secure.log-photo');
});

/*
|--------------------------------------------------------------------------
| STRICTLY ADMIN ONLY ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin', \App\Http\Middleware\ForcePasswordChange::class, \App\Http\Middleware\EnsureMfaIsVerified::class])
    ->prefix('admin')
    ->name('admin.') 
    ->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| SHARED ADMIN & GRANULAR TEACHER ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', \App\Http\Middleware\ForcePasswordChange::class, \App\Http\Middleware\EnsureMfaIsVerified::class])
    ->prefix('admin')
    ->name('admin.') 
    ->group(function () {

    Route::middleware([\App\Http\Middleware\CheckModulePermission::class . ':can_approve_reports'])->group(function() {
        Route::get('/report-approval', [ReportApprovalController::class, 'index'])->name('report-approval.index');
        Route::post('/report-approval/{id}/status', [ReportApprovalController::class, 'updateStatus'])->name('report-approval.update-status');
    });

    Route::middleware([\App\Http\Middleware\CheckModulePermission::class . ':can_create_profiles'])->group(function() {
        Route::resource('students', AdminStudentController::class);
    });

    Route::middleware([\App\Http\Middleware\CheckModulePermission::class . ':can_archive_students'])->group(function() {
        Route::get('/archive', [ArchiveController::class, 'index'])->name('archive.index');
        Route::post('/archive/run', [ArchiveController::class, 'runArchive'])->name('archive.run');
        Route::get('/archive/{id}/pdf', [ArchiveController::class, 'masterPdf'])->name('archive.pdf');
        Route::post('/archive/execute-rollover', [ArchiveController::class, 'archiveQuarterlyReports'])->name('archive.reports');
    });

    Route::middleware([\App\Http\Middleware\CheckModulePermission::class . ':can_manage_calendar'])->group(function() {
        Route::get('/academic-quarters', [AcademicQuarterController::class, 'index'])->name('quarters.index');
        Route::post('/academic-quarters', [AcademicQuarterController::class, 'updateAll'])->name('quarters.update');
    });

    Route::middleware([\App\Http\Middleware\CheckModulePermission::class . ':can_manage_credentials'])->group(function() {
        Route::resource('accounts', AccountController::class);
        Route::patch('/accounts/{account}/toggle-status', [AccountController::class, 'toggleStatus'])->name('accounts.toggle');
        Route::patch('/accounts/{account}/toggle-edit', [AccountController::class, 'toggleEditPermission'])->name('accounts.toggle-edit');
        Route::patch('/accounts/{id}/permissions', [AccountController::class, 'updatePermissions'])->name('accounts.permissions');
        Route::put('/accounts/{id}/credentials', [AccountController::class, 'updateCredentials'])->name('accounts.update-credentials');
    });

});

/*
|--------------------------------------------------------------------------
| Teacher Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:teacher', \App\Http\Middleware\ForcePasswordChange::class, \App\Http\Middleware\EnsureMfaIsVerified::class])
    ->prefix('teacher')
    ->name('teacher.') 
    ->group(function () {
    
    Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
    
    Route::post('/daily-logs/generate-ai', [DailyLogController::class, 'generateAiReport'])->name('daily-logs.generate-ai');
    Route::post('/iep-goals/generate-ai', [IepGoalController::class, 'generateAi'])->name('iep-goals.generate-ai');
    Route::post('/reports/generate-ai', [ReportController::class, 'generateAiReport'])->name('reports.generate-ai');

    // --- FIXED: Commented out to prevent conflict with Route::resource('reports') ---
    // Route::post('/reports/store', [ReportController::class, 'store'])->name('reports.store');
    
    Route::get('/reports/{id}/pdf', [ReportController::class, 'downloadPdf'])->name('reports.pdf');
    Route::get('/students/{student}/iep-pdf', [IepGoalController::class, 'downloadIepPdf'])->name('iep-goals.pdf');

    Route::resource('students', TeacherStudentController::class);
    Route::resource('files', FileController::class);
    Route::resource('reports', ReportController::class);
    Route::resource('iep-goals', IepGoalController::class);
    
    Route::get('/daily-logs', [DailyLogController::class, 'index'])->name('daily-logs.index');
    Route::post('/daily-logs', [DailyLogController::class, 'store'])->name('daily-logs.store');
    Route::delete('/daily-logs/{id}', [DailyLogController::class, 'destroy'])->name('daily-logs.destroy');
    Route::post('/daily-logs/generate-recommendations', [DailyLogController::class, 'generateAiRecommendations'])->name('daily-logs.generate-recommendations');
});

/*
|--------------------------------------------------------------------------
| Guardian Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:guardian', \App\Http\Middleware\ForcePasswordChange::class, \App\Http\Middleware\EnsureMfaIsVerified::class])
    ->prefix('guardian')
    ->name('guardian.')
    ->group(function () {
        
    Route::get('/dashboard', [GuardianController::class, 'dashboard'])->name('dashboard');
    
    Route::get('/goals', [GuardianController::class, 'goals'])->name('goals');
    Route::get('/goals/download-iep/{studentId}', [GuardianController::class, 'downloadIepPdf'])->name('goals.download-iep');
    
    Route::get('/reports', [GuardianController::class, 'reports'])->name('reports');
    Route::get('/reports/{id}/pdf', [GuardianController::class, 'downloadReportPdf'])->name('reports.pdf');
    
    Route::get('/recommendations', [GuardianController::class, 'recommendations'])->name('recommendations');
    Route::get('/notifications', [GuardianController::class, 'notifications'])->name('notifications');

    // This is the fixed route!
    Route::get('/switch-child/{id}', [GuardianController::class, 'switchChild'])->name('switch-child');
});

require __DIR__.'/auth.php';