<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BlockerController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (AUTH)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/', [AuthController::class, 'showLogin'])->name('login');

    // Login dilimit pakai rate limiter "login"
    Route::post('/', [AuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.post');

    // Register juga pakai limiter yang sama (bisa dipisah kalau mau)
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware('throttle:login')
        ->name('register.post');
});

// Logout juga dilimit global untuk user yang sudah login
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth', 'throttle:authenticated'])
    ->name('logout');

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (AUTHENTICATED USERS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'throttle:authenticated'])->group(function () {

    // Dashboard utama (redirect sesuai role)
    Route::get('/dashboard', [ProjectController::class, 'index'])->name('dashboard');
    Route::get('/me', [ProfileController::class, 'show'])->name('profile.show');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {

        // Project Management
        Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

        // Project Members
        Route::post('/projects/{project}/members', [ProjectMemberController::class, 'addMember'])
            ->name('projects.members.add');
        Route::post('/projects/{project}/members/add', [ProjectMemberController::class, 'add'])
            ->name('projects.members.add.ajax');
        Route::put('/projects/{project}/members/{member}', [ProjectMemberController::class, 'updateUser'])
            ->name('projects.members.update');
        Route::delete('/projects/{project}/members/{member}', [ProjectMemberController::class, 'deleteMember'])
            ->name('projects.members.delete');

        // Monitoring Routes - DIPINDAHKAN KE DALAM GROUP ADMIN
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/', [MonitoringController::class, 'index'])->name('index');
            Route::get('/project/{project}', [MonitoringController::class, 'show'])->name('show');
            Route::get('/project/{project}/board/{board}', [MonitoringController::class, 'board'])->name('board');
            Route::get('/project/{project}/card/{card}', [MonitoringController::class, 'card'])->name('card');
        });

        // Cards (view by board)
        Route::get('/boards/{board}/cards', [CardController::class, 'index'])->name('cards.index');

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index'); // user management
            Route::post('/', [UserController::class, 'store'])->name('store'); // create user
            Route::get('/active', [UserController::class, 'indexActive'])->name('active');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::post('/{user}/approve', [UserController::class, 'approve'])->name('approve');
            Route::delete('/{user}/reject', [UserController::class, 'reject'])->name('reject');
            Route::post('/{user}/update-role', [UserController::class, 'updateRole'])->name('updateRole');
            Route::get('/{user}/projects', [UserController::class, 'getUserProjects'])->name('getUserProjects');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });

        // Report Generation
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::post('/project', [ReportController::class, 'generateProjectReport'])->name('project');
            Route::post('/team', [ReportController::class, 'generateTeamReport'])->name('team');
            Route::post('/general', [ReportController::class, 'generateGeneralReport'])->name('general');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | TEAM LEAD ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:team_lead')->prefix('teamlead')->name('teamlead.')->group(function () {

        // Dashboard & Projects
        Route::get('/dashboard', [ProjectController::class, 'teamLeadDashboard'])->name('dashboard');
        Route::get('/projects/{project}', [ProjectController::class, 'teamLeadShow'])->name('projects.show');
        Route::post('/projects/{project}/complete', [ProjectController::class, 'complete'])->name('projects.complete');
        Route::get('/myteam', [ProjectMemberController::class, 'myTeam'])->name('myteam');

        // Cards Management
        Route::post('/cards/{card}/approve', [CardController::class, 'approve'])->name('cards.approve');
        Route::post('/cards/{card}/reject', [CardController::class, 'reject'])->name('cards.reject');

        Route::prefix('boards/{board}/cards')->name('cards.')->group(function () {
            Route::get('/', [CardController::class, 'index'])->name('index');
            Route::get('/create', [CardController::class, 'create'])->name('create');
            Route::post('/', [CardController::class, 'store'])->name('store');
            Route::get('/{card}/edit', [CardController::class, 'edit'])->name('edit');
            Route::put('/{card}', [CardController::class, 'update'])->name('update');
            Route::delete('/{card}', [CardController::class, 'destroy'])->name('destroy');
        });

        // Subtask Review (Approve/Reject)
        Route::post('/subtasks/{subtask}/approve', [SubtaskController::class, 'approve'])->name('subtasks.approve');
        Route::post('/subtasks/{subtask}/reject', [SubtaskController::class, 'reject'])->name('subtasks.reject');

        // Blocker Management
        Route::prefix('blocker')->name('blocker.')->group(function () {
            Route::get('/', [BlockerController::class, 'teamLeadIndex'])->name('index');
            Route::get('/{blocker}/edit', [BlockerController::class, 'edit'])->name('edit')->whereNumber('blocker');
            Route::put('/{blocker}', [BlockerController::class, 'update'])->name('update')->whereNumber('blocker');
            Route::post('/{blocker}/assign', [BlockerController::class, 'assign'])->name('assign')->whereNumber('blocker');
            Route::post('/{blocker}/resolve', [BlockerController::class, 'resolve'])->name('resolve')->whereNumber('blocker');
            Route::post('/{blocker}/reject', [BlockerController::class, 'reject'])->name('reject')->whereNumber('blocker');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | DEVELOPER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:developer')->prefix('developer')->name('developer.')->group(function () {
        Route::get('/dashboard', [ProjectController::class, 'developerDashboard'])->name('dashboard');
        Route::get('/myteam', [ProjectMemberController::class, 'developerTeam'])->name('myteam');
        Route::get('/cards/{card}', [CardController::class, 'showDeveloper'])->name('cards.show');
    });

    /*
    |--------------------------------------------------------------------------
    | DESIGNER ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:designer')->prefix('designer')->name('designer.')->group(function () {
        Route::get('/dashboard', [ProjectController::class, 'designerDashboard'])->name('dashboard');
        Route::get('/myteam', [ProjectMemberController::class, 'designerTeam'])->name('myteam');
        Route::get('/cards/{card}', [CardController::class, 'showDesigner'])->name('cards.show');
    });

    /*
    |--------------------------------------------------------------------------
    | DEVELOPER & DESIGNER SHARED ROUTES (SUBTASKS & BLOCKERS)
    |--------------------------------------------------------------------------
    */
    Route::middleware('role:developer,designer')->group(function () {
        // Subtask Management
        Route::get('/cards/{card}/subtasks/create', [SubtaskController::class, 'create'])->name('subtasks.create');
        Route::post('/cards/{card}/subtasks', [SubtaskController::class, 'store'])->name('subtasks.store');
        Route::post('/subtasks/{subtask}/start', [SubtaskController::class, 'start'])->name('subtasks.start');
        Route::post('/subtasks/{subtask}/complete', [SubtaskController::class, 'complete'])->name('subtasks.complete');

        // Blocker Management
        Route::prefix('blocker')->name('blocker.')->group(function () {
            Route::get('/', [BlockerController::class, 'index'])->name('index');
            Route::get('/create', [BlockerController::class, 'create'])->name('create');
            Route::post('/', [BlockerController::class, 'store'])->name('store');
            Route::get('/{blocker}', [BlockerController::class, 'show'])->name('show')->whereNumber('blocker');
            Route::get('/subtask/{subtask}/entries', [BlockerController::class, 'subtaskEntries'])->name('subtask.entries');
            Route::post('/subtask/{subtask}/entries', [BlockerController::class, 'storeSubtaskBlocker'])->name('subtask.store');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | COMMENT ROUTES (ALL AUTHENTICATED USERS)
    |--------------------------------------------------------------------------
    */
    Route::middleware('throttle:comments')->group(function () {

        // AJAX Comments Routes - UNTUK SEMUA USER TERAUTENTIKASI
        Route::post('/comments/ajax-project/{projectId}', [CommentController::class, 'ajaxStoreProject'])->name('comments.ajax.project');
        Route::post('/comments/ajax-card/{cardId}', [CommentController::class, 'ajaxStoreCard'])->name('comments.ajax.card');
        Route::post('/comments/ajax-subtask/{subtaskId}', [CommentController::class, 'ajaxStore'])->name('comments.ajax.subtask');

        // Route untuk mendapatkan komentar subtask
        Route::get('/comments/subtask/{subtaskId}', [CommentController::class, 'getSubtaskComments'])->name('comments.subtask');

        // Get Comments Routes
        Route::get('/comments/card/{cardId}', [CommentController::class, 'getCardComments'])->name('comments.card');
        Route::get('/comments/project/{projectId}', [CommentController::class, 'getProjectComments'])->name('comments.project');
    });

    // Fetch members (admin/projects)
    Route::get('/projects/{project}/members', [ProjectMemberController::class, 'fetchMembers'])
        ->name('admin.projects.members.fetch');
});

Route::fallback(function () {
    return response()->view('errors.404', [], 404);
});
