<?php


use App\Http\Controllers\Auth\SocialiteLoginController;
use App\Http\Controllers\MessagesController;
use Illuminate\Support\Facades\Route;
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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('login/{provider}', [SocialiteLoginController::class, 'redirectToProvider']);
Route::get('login/{provider}/callback', [SocialiteLoginController::class, 'handleProviderCallback']);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


//Messages Routes

Route::post('threads/{thread}',[MessagesController::class,'store'])->middleware('auth');
Route::get('threads/{thread}',[MessagesController::class,'show'])->middleware('auth');


Route::prefix('messages')->group(function () {
    Route::get('/', [MessagesController::class,'index']);
    Route::get('create', [MessagesController::class,'create']);
    Route::post('/', [MessagesController::class,'store']);
    Route::get('{id}', [MessagesController::class,'show']);
    Route::put('{id}',  [MessagesController::class,'update']);
});

