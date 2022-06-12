<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/v1/events', [EventController::class, 'index'])->name('events.index'); // Return All Event API
Route::get('/v1/events/active-events', [EventController::class, 'active_events'])->name('active-events');   // Return active Events API
Route::post('/user/login', [AuthController::class, 'login'])->name('auth.login'); // Return with auth Key
Route::post('/user/register', [AuthController::class, 'register'])->name('auth.register'); // Register User and return with auth Key

Route::group(['middleware' => ['auth:sanctum']], function ($router) {
    $router->post('/user/logout', [AuthController::class, 'logout'])->name('auth.logout'); // Return Logout User Message
});

Route::group(['middleware' => ['auth:sanctum'], 'prefix' => 'v1/events'], function ($router) {
    $router->get('/{id}', [EventController::class, 'fetch_event'])->name('fetch_event');   // Get one event API  
    $router->post('/', [EventController::class, 'generate_event'])->name('generate_event');   // Create an event API  
    $router->put('/{id}', [EventController::class, 'update_event'])->name('update_event');   // Create event if not exist, else update the event API  
    $router->patch('/{id}', [EventController::class, 'update_event'])->name('update_event');   // Partially update event API  
    $router->delete('/{id}', [EventController::class, 'delete_event'])->name('delete_event');   // Soft delete an event API  
});
