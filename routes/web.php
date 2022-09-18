<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia;
use Illuminate\Support\Str;

use App\Models\Project;
use App\Models\User;
use App\Models\Source;
use App\Models\QR;

use App\Http\Controllers\Projects\MainController as ProjectController;
use App\Http\Controllers\Projects\AnalyticsController as AnalyticsController;
use App\Http\Controllers\Projects\I18NController as I18NController;
use App\Http\Controllers\Projects\QuizController as QuizController;
use App\Http\Controllers\Projects\QrController as QrController;
use App\Http\Controllers\Projects\PrizesController as PrizesController;
use App\Http\Controllers\Projects\SharingController as SharingController;
use App\Http\Controllers\Projects\SourcesController as SourcesController;

use App\Http\Controllers\Dashboard\MainController as DashboardController;
use App\Http\Controllers\Sharing\MainController as SharingMainController;
use App\Http\Controllers\Go\MainController as GoMainController;

use App\Http\Controllers\Api\MainController as ApiMainController;
use BaconQrCode\Encoder\QrCode;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

#Route::mailPreview();

Route::group(['domain' => env('DOMAIN_KDD')], function(){
    Route::get('/{id}', [GoMainController::class, 'redirect'])->name('kdd.redirect');    
});

Route::group(['domain' => env('DOMAIN_GO')], function(){
    Route::get('/{id}', [GoMainController::class, 'redirect'])->name('go.redirect');    
});

Route::group(['domain' => env('DOMAIN_SHARE')], function(){
    Route::get('/{project}/{selfie}', [SharingMainController::class, 'index'])->name('sharing.index');    
});

Route::group(['domain' => env('DOMAIN_APP'), 'middleware' => 'auth'], function(){
        
    Route::any('/', function(){
        $customController = current_project()->controller ? 'Projects\\'.current_project()->controller : 'MainController';
        return App::make('App\Http\Controllers\Dashboard\\'.$customController)->index();
    })->name('dashboard');
    
    Route::any('/analytics', function(){
        $customController = current_project()->controller ? 'Projects\\'.current_project()->controller : 'MainController';
        return App::make('App\Http\Controllers\Dashboard\\'.$customController)->analytics();
    })->name('analytics');

    Route::get('/prizes', function(){
        $customController = current_project()->controller ? 'Projects\\'.current_project()->controller : 'MainController';
        return App::make('App\Http\Controllers\Dashboard\\'.$customController)->prizes();
    })->name('prizes');

    Route::get('/charts/{type}', function($type){
        $customController = current_project()->controller ? 'Projects\\'.current_project()->controller : 'MainController';
        return App::make('App\Http\Controllers\Dashboard\\'.$customController)->charts($type);
    })->name('charts');

    Route::get('/exports', function(){
        $customController = current_project()->controller ? 'Projects\\'.current_project()->controller : 'MainController';
        return App::make('App\Http\Controllers\Dashboard\\'.$customController)->exports();
    })->name('exports');

    Route::get('/export/emails', function(){
        $customController = current_project()->controller ? 'Projects\\'.current_project()->controller : 'MainController';
        return App::make('App\Http\Controllers\Dashboard\\'.$customController)->exportEmails();
    })->name('export.emails');

    Route::get('/export/daily', function(){
        $customController = current_project()->controller ? 'Projects\\'.current_project()->controller : 'MainController';
        return App::make('App\Http\Controllers\Dashboard\\'.$customController)->exportDaily();
    })->name('export.daily');

    Route::get('/export/leaderboard', function(){
        $customController = current_project()->controller ? 'Projects\\'.current_project()->controller : 'MainController';
        return App::make('App\Http\Controllers\Dashboard\\'.$customController)->exportLeaderboard();
    })->name('export.leaderboard');

    Route::get('/insights', function(){
        $customController = current_project()->controller ? 'Projects\\'.current_project()->controller : 'MainController';
        return App::make('App\Http\Controllers\Dashboard\\'.$customController)->insights();
    })->name('insights');

    Route::post('/prizes/winner', [DashboardController::class, 'prizeWinner'])->name('prizes.winner');
    Route::post('/prizes/winner/delete', [DashboardController::class, 'prizeWinnerDelete'])->name('prizes.winner.delete');

    Route::post('/participant/update', function(){
        $customController = current_project()->controller ? 'Projects\\'.current_project()->controller : 'MainController';
        return App::make('App\Http\Controllers\Dashboard\\'.$customController)->participantUpdate();
    })->name('participant.update');

    Route::any('/notes', [DashboardController::class, 'notes'])->name('notes');
    Route::any('/debug', [DashboardController::class, 'debug'])->name('debug');

    Route::get('/activity', [DashboardController::class, 'activity'])->name('activity');

    Route::post('/modules', [App\Http\Controllers\Modules\MainController::class, 'update'])->name('modules.update');

    Route::prefix('projects')->group(function(){

        Route::get('/create', [ProjectController::class, 'create'])->name('projects.create');
        Route::post('/store', [ProjectController::class, 'store'])->name('projects.store');
        Route::put('/details', [ProjectController::class, 'updateDetails'])->name('projects.details.update');
        Route::put('/current', [ProjectController::class, 'current'])->name('projects.current.update');
        Route::get('/view', [ProjectController::class, 'view'])->name('projects.view');

        Route::get('/logs', [ProjectController::class, 'viewLogs'])->name('projects.logs');

        /* Project Members */
        Route::post('/members/add', [ProjectController::class, 'addMember'])->name('projects.members.add');
        Route::post('/members/role', [ProjectController::class, 'editMemberRole'])->name('projects.members.role');

        /* Live / Test */
        Route::post('/view/live', [ProjectController::class, 'toggleLive'])->name('projects.live.toggle');

        /* Bearer Token */
        Route::post('/view/token', [ProjectController::class, 'updateToken'])->name('projects.token.update');

        /* Analytics */
        Route::get('/view/analytics', [AnalyticsController::class, 'view'])->name('projects.analytics.view');
        Route::post('/view/analytics', [AnalyticsController::class, 'store'])->name('projects.analytics.store');

        /* i18n */
        Route::get('/view/i18n', [I18NController::class, 'view'])->name('projects.i18n.view');
        Route::post('/view/i18n', [I18NController::class, 'store'])->name('projects.i18n.store');

        /* Quiz */
        Route::get('/view/quiz', [QuizController::class, 'view'])->name('projects.quiz.view');
        Route::post('/view/quiz', [QuizController::class, 'store'])->name('projects.quiz.store');
        Route::post('/view/actions', [QuizController::class, 'actions'])->name('projects.quiz.actions');
    
        /* QR */
        Route::get('/view/qr', [QrController::class, 'view'])->name('projects.qr.view');
        Route::post('/view/qr', [QrController::class, 'store'])->name('projects.qr.store');
        Route::post('/view/qr/details/upload', [QrController::class, 'detailsUpload'])->name('projects.qr.details.upload');
        Route::post('/view/qr/details/add', [QrController::class, 'detailsAdd'])->name('projects.qr.details.add');
        Route::post('/view/qr/details/save', [QrController::class, 'detailsSave'])->name('projects.qr.details.save');
    
        /* Prizes */
        Route::get('/view/prizes', [PrizesController::class, 'view'])->name('projects.prizes.view');
        Route::post('/view/prizes', [PrizesController::class, 'store'])->name('projects.prizes.store');

        /* Sharing */
        Route::get('/view/sharing', [SharingController::class, 'view'])->name('projects.sharing.view');
        Route::post('/view/sharing', [SharingController::class, 'store'])->name('projects.sharing.store');

        /* Sources */
        Route::get('/view/sources', [SourcesController::class, 'view'])->name('projects.sources.view');
        Route::post('/view/sources', [SourcesController::class, 'store'])->name('projects.sources.store');
        Route::post('/view/sources/toggle', [SourcesController::class, 'toggle'])->name('projects.sources.toggle');
        
    });

});


Route::get('/feed', function () {



    
    /*
    $p = new Project;
    $p->user_id = 1;
    $p->name = 'KDD';
    $p->ucode = Str::random(8);
    $p->api_token = Hash::make(Str::random(60));
    $p->save();

    $u = User::find(1);
    $u->is_admin = true;
    $u->current_project_id = 1;
    $u->save();

    $s = new Source;
    $s->title = 'Connected Packaging';
    $s->save();
    
    $s = new Source;
    $s->title = 'Social Media';
    $s->save();

    $s = new Source;
    $s->title = 'Ads';
    $s->save();

    $s = new Source;
    $s->title = 'Direct';
    $s->save();
    */
});



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
