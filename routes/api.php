<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['domain' => env('DOMAIN_API'), 'prefix' => 'api'], function(){

    Route::get('/alphanum/{project}/view', [App\Http\Controllers\Api\MainController::class, 'alphaNumView'])->name('alphanum.external.views');
    Route::get('/qrcodes/{project}/view', [App\Http\Controllers\Api\MainController::class, 'qrcodesViews'])->name('qrcodes.external.views');

    Route::any('/geo/ip', [App\Http\Controllers\Api\MainController::class, 'geo'])->name('api.qr.geo');
    
    Route::post('/qr/details', [App\Http\Controllers\Api\MainController::class, 'qrDetails'])->name('api.qr.details');
    Route::post('/qr/burn', [App\Http\Controllers\Api\MainController::class, 'qrBurn'])->name('api.qr.burn');

    Route::post('/alphanum/details', [App\Http\Controllers\Api\MainController::class, 'alphanumDetails'])->name('api.alphanum.details');
    Route::post('/alphanum/burn', [App\Http\Controllers\Api\MainController::class, 'alphanumBurn'])->name('api.alphanum.burn');

    Route::post('/selfie/submit', [App\Http\Controllers\Api\MainController::class, 'selfieSubmit'])->name('api.selfie.submit');

    Route::post('/leaderboard/get', [App\Http\Controllers\Api\MainController::class, 'leaderboardGet'])->name('api.leaderboard.get');
    Route::post('/leaderboard/submit', [App\Http\Controllers\Api\MainController::class, 'leaderboardSubmit'])->name('api.leaderboard.submit');

    Route::post('/quiz/log', [App\Http\Controllers\Api\MainController::class, 'quizLog'])->name('api.quiz.log');
    Route::post('/quiz/random', [App\Http\Controllers\Api\MainController::class, 'quizRandom'])->name('api.quiz.random');
    Route::post('/quiz/all', [App\Http\Controllers\Api\MainController::class, 'quizAll'])->name('api.quiz.all');
    Route::post('/quiz/next', [App\Http\Controllers\Api\MainController::class, 'quizNext'])->name('api.quiz.next');

    Route::post('/log/action', [App\Http\Controllers\Api\MainController::class, 'logAction'])->name('api.log.action');
    Route::post('/source/track', [App\Http\Controllers\Api\MainController::class, 'trackSource'])->name('api.source.track');

    Route::post('/auth/update', [App\Http\Controllers\Api\MainController::class, 'authUpdate'])->name('api.auth.update');
    Route::post('/auth/register', [App\Http\Controllers\Api\MainController::class, 'authRegister'])->name('api.auth.register');
    Route::post('/auth/login', [App\Http\Controllers\Api\MainController::class, 'authLogin'])->name('api.auth.login');
    Route::post('/auth/loginOrNew', [App\Http\Controllers\Api\MainController::class, 'authLoginOrNew'])->name('api.auth.loginornew');
    Route::post('/auth/resetPassword', [App\Http\Controllers\Api\MainController::class, 'authResetPassword'])->name('api.auth.resetpassword');

    Route::post('/articles/view', [App\Http\Controllers\Api\MainController::class, 'articlesView'])->name('api.articles.view');
    Route::post('/articles/all', [App\Http\Controllers\Api\MainController::class, 'articlesAll'])->name('api.articles.all');

    Route::post('/crm/contact', [App\Http\Controllers\Api\MainController::class, 'crmContact'])->name('api.crm.lead');

});


