<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;

use Inertia\Inertia;
use Illuminate\Support\Str;

use App\Models\Project;
use App\Models\User;
use App\Models\Source;
use App\Models\QR;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Http\Controllers\Projects\MainController as ProjectController;
use App\Http\Controllers\Projects\AnalyticsController as AnalyticsController;
use App\Http\Controllers\Projects\I18NController as I18NController;
use App\Http\Controllers\Projects\QuizController as QuizController;
use App\Http\Controllers\Projects\QrController as QrController;
use App\Http\Controllers\Projects\PrizesController as PrizesController;
use App\Http\Controllers\Projects\SharingController as SharingController;
use App\Http\Controllers\Projects\SourcesController as SourcesController;
use App\Http\Controllers\Projects\ArticlesController as ArticlesController;

use App\Http\Controllers\Dashboard\MainController as DashboardController;
use App\Http\Controllers\Sharing\MainController as SharingMainController;
use App\Http\Controllers\Go\MainController as GoMainController;

use App\Http\Controllers\RegisterController;
use App\Http\Controllers\CustomLoginController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;

use App\Http\Controllers\Api\MainController as ApiMainController;
use App\Http\Controllers\Projects\AlphaController;
use App\Models\AlphaNumCode;
use App\Models\Leaderboard;
use App\Models\Participant;
use App\Models\Contact;
use BaconQrCode\Encoder\QrCode;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use GeoIp2\Database\Reader;

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

Route::get('/changetable', function () {
    #AlphaNumCode::truncate();
    Schema::table('articles', function (Blueprint $table) {
        $table->string('country')->nullable()->after('source_value');
        $table->string('language')->nullable()->after('country');
    });
});

Route::group(['domain' => env('DOMAIN_RAUCH')], function(){
    Route::get('/{id}', [GoMainController::class, 'redirect'])->name('rauch.redirect');    
});

Route::group(['domain' => env('DOMAIN_KDD')], function(){
    Route::get('/{id}', [GoMainController::class, 'redirect'])->name('kdd.redirect');    
});

Route::group(['domain' => env('DOMAIN_GO')], function(){
    Route::get('/{id}', [GoMainController::class, 'redirect'])->name('go.redirect');    
});

Route::group(['domain' => env('DOMAIN_SHARE')], function(){
    Route::get('/{project}/{selfie}', [SharingMainController::class, 'index'])->name('sharing.index');    
});

 function extractSurname($email, $firstName)
{
    // Split the email address at '@', and convert it to lowercase
    $username = strtolower(explode('@', $email)[0]);

    // Split the username at '.', or '_' and take the first or second part based on matching the first name
    $delimiter = strpos($username, '.') !== false ? '.' : (strpos($username, '_') !== false ? '_' : '');
    if (!empty($delimiter)) {
        $usernameParts = explode($delimiter, $username);
        if (count($usernameParts) > 1) {
            if ($usernameParts[0] == strtolower($firstName)) {
                $username = $usernameParts[1];
            } else {
                $username = $usernameParts[0];
            }
        } else {
            $username = $usernameParts[0];
        }
    } else {
        $username = $username;
    }

    // Remove the first name from the username, if it exists there
    $username = str_replace(strtolower($firstName), '', $username);

    // Remove special characters and numbers
    $username = preg_replace("/[^a-zA-Z]/", "", $username);

    // Trim whitespace and capitalize the first letter
    $username = trim($username);
    $username = ucfirst($username);

    // If the resulting string is empty, return a random Austrian/German surname
    if (empty($username)) {
        //$surnames = ['Müller', 'Schmidt', 'Schneider', 'Fischer', 'Weber', 'Meyer', 'Wagner', 'Becker'];
        //$username = $surnames[array_rand($surnames)];
        $username = "";
    }

    return $username;
}




function isJson($string) {
    if(!is_string($string)) return false;
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}

Route::get('/surnames2', function(){
    echo extractSurname('alhaas54@gmail.com', 'Lina');
});

Route::get('/surnames', function(){
    Participant::whereProjectId(26)->chunk(400, function ($participants) {
        foreach ($participants as $participant) {
            if(!$participant->profile) continue;
            $profile = ($participant->profile);
            if(!isset($profile->name)) continue;
            $profile->surname = extractSurname(trim($profile->email), trim($profile->name));
            $participant->profile = json_encode($profile);
            $participant->save();
        }
    });
});

Route::get('/geoip', function(){
    //$reader = new Reader(storage_path('app/geolite/GeoLite2-City.mmdb'));
    //$record = $reader->city(request()->ip());
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

        /* Reset Data */
        Route::post('/reset/all', [ProjectController::class, 'resetData'])->name('projects.reset.data');

        /* Bearer Token */
        Route::post('/view/token', [ProjectController::class, 'updateToken'])->name('projects.token.update');

        /* Analytics */
        Route::get('/view/analytics', [AnalyticsController::class, 'view'])->name('projects.analytics.view');
        Route::post('/view/analytics', [AnalyticsController::class, 'store'])->name('projects.analytics.store');
        Route::post('/view/analytics/plausible', [AnalyticsController::class, 'plausible'])->name('projects.analytics.plausible');

        /* i18n */
        Route::get('/view/i18n', [I18NController::class, 'view'])->name('projects.i18n.view');
        Route::post('/view/i18n', [I18NController::class, 'store'])->name('projects.i18n.store');

        /* Quiz */
        Route::get('/view/quiz', [QuizController::class, 'view'])->name('projects.quiz.view');
        Route::post('/view/quiz', [QuizController::class, 'store'])->name('projects.quiz.store');
        Route::post('/view/quiz/actions', [QuizController::class, 'actions'])->name('projects.quiz.actions');
    
        /* QR */
        Route::get('/view/qr', [QrController::class, 'view'])->name('projects.qr.view');
        Route::post('/view/qr', [QrController::class, 'store'])->name('projects.qr.store');
        Route::post('/view/qrbulk', [QrController::class, 'bulkStore'])->name('projects.qr.bulkStore');
        Route::post('/view/qr/details/upload', [QrController::class, 'detailsUpload'])->name('projects.qr.details.upload');
        Route::post('/view/qr/details/add', [QrController::class, 'detailsAdd'])->name('projects.qr.details.add');
        Route::post('/view/qr/details/save', [QrController::class, 'detailsSave'])->name('projects.qr.details.save');
        Route::post('/check/qr/limit', [QrController::class, 'checkLimit'])->name('projects.qr.limit');

        /* Alpha Num */
        Route::get('/view/alphanum', [AlphaController::class, 'view'])->name('projects.alphanum.view');
        Route::post('/view/alphanum', [AlphaController::class, 'store'])->name('projects.alphanum.store');

        /* Prizes */
        Route::get('/view/prizes', [PrizesController::class, 'view'])->name('projects.prizes.view');
        Route::post('/view/prizes', [PrizesController::class, 'store'])->name('projects.prizes.store');

        /* Articles */
        Route::get('/view/articles', [ArticlesController::class, 'view'])->name('projects.articles.view');
        Route::post('/view/articles', [ArticlesController::class, 'store'])->name('projects.articles.store');
        Route::post('/view/articles/upload', [ArticlesController::class, 'upload'])->name('projects.articles.upload');
        Route::post('/view/actions', [ArticlesController::class, 'actions'])->name('projects.articles.actions');

        /* Sharing */
        Route::get('/view/sharing', [SharingController::class, 'view'])->name('projects.sharing.view');
        Route::post('/view/sharing', [SharingController::class, 'store'])->name('projects.sharing.store');

        /* Sources */
        Route::get('/view/sources', [SourcesController::class, 'view'])->name('projects.sources.view');
        Route::post('/view/sources', [SourcesController::class, 'store'])->name('projects.sources.store');
        Route::post('/view/sources/toggle', [SourcesController::class, 'toggle'])->name('projects.sources.toggle');
        
    });

    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    Route::get('/contact/{id}', [ContactController::class, 'view'])->name('contact.view');
    Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    Route::get('/contact-list', [ContactController::class, 'list'])->name('contact.list');
    Route::post('/contact-manage-list', [ContactController::class, 'manageList'])->name('contact.manage.list');

    Route::get('/user-list', [UserController::class, 'list'])->name('user.list');
    Route::post('/user-manage-list', [UserController::class, 'manageList'])->name('user.manage.list');
    Route::put('/user/can-subscription/{id}', [UserController::class, 'canSubscription'])->name('user.can.subscription');

});


Route::get('/feed', function () {


return 1;
    
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

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register');
Route::post('/login', [CustomLoginController::class, 'checkUser'])->name('login');

Route::get('/verified/{id}', [RegisterController::class, 'verified'])->name('verified');

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
