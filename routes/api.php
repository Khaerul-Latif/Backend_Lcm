x1<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProspectParentController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\PaymentSpController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\WhatsAppController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\HandleSubmitController;
use App\Http\Controllers\HandleSubmitSignUpController;




/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('login', [AuthController::class, 'login']);
Route::post('/api-keys', [ApiKeyController::class, 'create'])->middleware('auth:sanctum');


Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RoleController::class);
    Route::apiResource('menus', MenuController::class);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::apiResource('prospect', ProspectParentController::class);
Route::apiResource('citys', CityController::class);
Route::apiResource('programs', ProgramController::class);
Route::apiResource('payment-sps', PaymentSpController::class);
Route::apiResource('messages', MessageController::class);
Route::apiResource('invites', InviteController::class);
Route::post('create-invoice', [PaymentSpController::class, 'createInvoice'])->name('api.create-invoice');
Route::post('xendit-callback', [PaymentSpController::class, 'handleXenditCallback']);
Route::post('check-invitional-code', [InviteController::class, 'checkInvitationalCode'])->name('api.check-invitional-code');
Route::post('handle-signup', [HandleSubmitSignUpController::class, 'handleSignUp']);
Route::post('send-message', [WhatsAppController::class, 'sendMessage']);
Route::post('form-submit', [HandleSubmitController::class, 'handleFormSubmission']);
Route::get('messages', [MessageController::class, 'index'])->name('api.messages.index');
