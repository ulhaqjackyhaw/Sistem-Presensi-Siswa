<?php

use App\Http\Controllers\PrestasiController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\DBBackupController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ViolationPointController;
use App\Http\Controllers\HourController;
use App\Http\Controllers\AchievementPointController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\ClassScheduleController;
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\StudentClassAssignmentController;
use App\Http\Controllers\TeachingAssignmentController;
use App\Http\Controllers\StudentAttendanceController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AchievementValidationController;
use App\Http\Controllers\ViolationValidationController;
use App\Http\Controllers\AttendanceHistoryController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherScheduleController;
use App\Http\Controllers\HomeController;

// Redirect root to login
Route::permanentRedirect('/', '/login');

// Authentication routes
Auth::routes();

// Routes accessible to all authenticated users
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // User profile
    Route::resource('profil', ProfilController::class)->except('destroy');

    // Student detail view (accessible by multiple roles)
    Route::get('/student/{student}', [StudentController::class, 'showDetail'])->name('student.detail');
});

// =========================================================
// Admin Sistem Routes
// =========================================================
Route::middleware(['auth', 'role:superadmin'])->group(function () {
    // User management
    Route::resource('manage-user', UserController::class);

    // Role & Permission management
    Route::resource('manage-role', RoleController::class);
    Route::resource('manage-menu', MenuController::class);
    Route::resource('manage-permission', PermissionController::class)->only('store', 'destroy');

    // Database backup
    Route::get('dbbackup', [DBBackupController::class, 'DBDataBackup']);
});

// =========================================================
// Admin Sekolah Routes
// =========================================================
Route::middleware(['auth', 'role:Admin Sekolah'])->group(function () {
    // Curriculum management
    Route::resource('manage-curriculums', CurriculumController::class);
    Route::resource('manage-academic-years', AcademicYearController::class);

    // Class management
    Route::resource('manage-classes', SchoolClassController::class);
    Route::post('/classes/assign-homeroom', [SchoolClassController::class, 'assignHomeroom'])->name('classes.assignHomeroom');

    // Subject management
    Route::resource('manage-subjects', SubjectController::class);
    Route::resource('manage-subject', SubjectController::class);
    Route::get('manage-subject/schedule-names', [SubjectController::class, 'getScheduleNames'])->name('manage-subject.schedule-names');
    Route::get('subjects/schedule-names', [SubjectController::class, 'getScheduleNames'])->name('subjects.schedule-names');

    // Teacher management
    Route::resource('manage-teachers', TeacherController::class);
    Route::post('manage-teachers/import', [TeacherController::class, 'import'])->name('manage-teachers.import');
    Route::get('manage-teachers/template/download', [TeacherController::class, 'downloadTemplate'])->name('manage-teachers.template');
    Route::post('manage-teachers/{nip}/jadikan-guru-bk', [TeacherController::class, 'jadikanGuruBk'])->name('manage-teachers.jadikan-guru-bk');

    // Student management
    Route::resource('manage-students', StudentController::class);
    Route::post('manage-students/import', [StudentController::class, 'import'])->name('manage-students.import');
    Route::get('manage-students/template/download', [StudentController::class, 'downloadTemplate'])->name('manage-students.template');

    // Assignment management
    Route::resource('manage-teacher-subject-assignments', TeachingAssignmentController::class)
        ->parameters(['manage-teacher-subject-assignments' => 'teacherAssignment']);
    Route::resource('manage-student-class-assignments', StudentClassAssignmentController::class)
        ->parameters(['manage-student-class-assignments' => 'studentAssignment']);
    Route::get('/manage-student-class-assignments/create/for-class/{class_id}', [StudentClassAssignmentController::class, 'createForClass'])
        ->name('manage-student-class-assignments.create-for-class');

    // Schedule management
    Route::resource('manage-hours', HourController::class);
    Route::resource('manage-schedules', ClassScheduleController::class);
    Route::get('manage-schedules/{manage_schedule}/export-pdf', [ClassScheduleController::class, 'exportPdf'])->name('manage-schedules.export-pdf');

    // Attendance management
    Route::get('manage-attendances/class/{class_id}', [AttendanceController::class, 'showByClass'])->name('manage-attendances.show-by-class');
    Route::post('manage-attendances/update-status', [AttendanceController::class, 'updateStatus'])->name('manage-attendances.update-status');
    Route::resource('manage-attendances', AttendanceController::class);
    Route::resource('manage-attendances-history', AttendanceHistoryController::class)->only(['index', 'show']);
    Route::get('attendances/history-detail', [AttendanceHistoryController::class, 'detail'])->name('attendances.history-detail');
});

// =========================================================
// Guru BK Routes
// =========================================================
Route::middleware(['auth', 'role:Guru BK'])->group(function () {
    // Achievement management
    Route::resource('achievement-management', AchievementPointController::class);
    Route::get('/achievements/all-students', [AchievementController::class, 'allStudents'])->name('achievements.all_students');
    Route::resource('achievement-validations', AchievementValidationController::class)->only(['index', 'show']);
    Route::post('achievement-validations/{achievement}/validate', [AchievementValidationController::class, 'validateAchievement'])
        ->name('achievement-validations.validate');
    Route::get('achievement-validations/{achievement}/edit-validation', [AchievementValidationController::class, 'editValidation'])
        ->name('achievement-validations.editValidation');
    Route::put('achievement-validations/{achievement}/update-validation', [AchievementValidationController::class, 'updateValidation'])
        ->name('achievement-validations.updateValidation');

    // Violation management
    Route::resource('violation-management', ViolationPointController::class);
    Route::resource('kelola-pelanggaran', ViolationPointController::class);
    Route::get('/violations/all-students', [ViolationController::class, 'allStudents'])->name('violations.all_students');
    Route::resource('violation-validations', ViolationValidationController::class)->only(['index', 'show']);
    Route::post('violations/{violation}/validate', [ViolationValidationController::class, 'validateViolation'])->name('violations.validate');
    Route::get('violation-validations/{violation}/edit-validation', [ViolationValidationController::class, 'editValidation'])
        ->name('violation-validations.editValidation');
    Route::put('violation-validations/{violation}/update-validation', [ViolationValidationController::class, 'updateValidation'])
        ->name('violation-validations.updateValidation');
});

// =========================================================
// Guru Routes
// =========================================================
Route::middleware(['auth', 'role:Guru'])->group(function () {
    // Teacher schedule
    Route::get('/teacher-schedule', [TeacherScheduleController::class, 'index'])->name('teacher-schedule.index');

    // Achievement reporting
    Route::resource('achievements', AchievementController::class);
    Route::post('achievements/{achievement}/validate', [AchievementController::class, 'validateAchievement'])->name('achievements.validate');

    // Violation reporting
    Route::resource('violations', ViolationController::class);

    // Autocomplete for forms
    Route::get('/autocomplete/siswa', [AchievementController::class, 'autocompleteSiswa'])->name('autocomplete.siswa');
    Route::get('/autocomplete/violation-points', [ViolationPointController::class, 'autocomplete'])->name('autocomplete.violation-points');
    Route::get('/autocomplete/classes', [ViolationController::class, 'autocompleteClass'])->name('autocomplete.classes');
    Route::get('/autocomplete/siswa-by-class', [ViolationController::class, 'autocompleteStudentByClass'])->name('autocomplete.siswa-by-class');
});

// =========================================================
// Siswa Routes
// =========================================================
Route::middleware(['auth', 'role:Siswa'])->group(function() {
    // Student attendance view
    Route::get('/manage-attendance-students', [StudentAttendanceController::class, 'index']);
    Route::get('/student/attendance', [StudentAttendanceController::class, 'index'])->name('student.attendance');

    // Achievement & violation points for students
    Route::get('/poin-prestasi', [HomeController::class, 'studentAchievements'])->name('student.achievements');
    Route::get('/poin-pelanggaran', [HomeController::class, 'studentViolations'])->name('student.violations');
});
