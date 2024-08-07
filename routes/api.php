<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CommentaireController;
use App\Http\Controllers\HistoriqueController;
use App\Http\Controllers\PrioriteController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TypeTicketController;
use App\Http\Controllers\UserController;
use App\Mail\TestEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('/send-test', function (){
//     // $toemail = 'vmagnouwai@gmail.com';
//     $message = 'Test Email';

//     Mail::to('vmagnouwai@gmail.com')->send(new TestEmail($message));;
//     return 'Test email sent';
// });

Route::middleware(['auth:sanctum', 'role:Admin'])->group(function () {


});
Route::get('/clients', [ClientController::class, 'index']);
Route::post('/clients', [ClientController::class, 'store']);
Route::put('/updateclt/{id}', [ClientController::class, 'update']);
Route::delete('/deleteclt/{id}', [ClientController::class, 'destroy']);
Route::middleware('auth:sanctum')->get('/client-profile', [ClientController::class, 'getProfile']);
Route::post('/updateclient/profile', [ClientController::class, 'updateProfile']);

Route::post('/login', [AuthController::class, 'login']);
Route::get('/roles', [RoleController::class, 'index']);
Route::post('/reset-password', [ClientController::class, 'reset']);
Route::post('/send-activation/{id}', [ClientController::class, 'send']);

Route::get('/users', [UserController::class, 'index']);
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::put('/update/{id}', [UserController::class, 'update']);
Route::delete('/delete/{id}', [UserController::class, 'destroy']);
Route::middleware('auth:sanctum')->get('/user-profile', [UserController::class, 'getUserprofile']);
Route::post('/updateUser/profile',[UserController::class, 'updateProfile']);

Route::get('/services', [ServiceController::class, 'index']);
Route::post('/services', [ServiceController::class, 'store']);
Route::get('/services/{id}', [ServiceController::class, 'show']);
Route::put('/update/{id}', [ServiceController::class, 'update']);
Route::delete('/delete/{id}', [ServiceController::class, 'destroy']);

Route::get('/types', [TypeTicketController::class, 'index']);
Route::post('/types', [TypeTicketController::class, 'store']);
Route::get('/types/{id}', [TypeTicketController::class, 'show']);
Route::put('/updatetype/{id}', [TypeTicketController::class, 'update']);
Route::delete('/deletetype/{id}', [TypeTicketController::class, 'destroy']);

Route::get('/priorites', [PrioriteController::class, 'index']);
Route::post('/priorites', [PrioriteController::class, 'store']);
Route::get('/priorites/{id}', [PrioriteController::class, 'show']);
Route::put('/updatepriorite/{id}', [PrioriteController::class, 'update']);
Route::delete('/deletepriorite/{id}', [PrioriteController::class, 'destroy']);

Route::middleware(['auth:sanctum', 'role:Admin,Client'])->group(function () {

});
Route::get('/tickets', [TicketController::class, 'index']);
Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets/{id}', [TicketController::class, 'show']);
Route::put('/updateticket/{id}', [TicketController::class, 'update']);
Route::delete('/deleteticket/{id}', [TicketController::class, 'destroy']);
Route::get('tickets/service/{servicename}', [TicketController::class, 'getTicketsByservice']);
Route::post('/tickets/send-email', [TicketController::class, 'email']);


Route::get('/commentaires', [CommentaireController::class, 'index']);
Route::post('/commentaires', [CommentaireController::class, 'store']);
Route::get('/comment/{id}', [PrioriteController::class, 'show']);
Route::put('/updatecomment/{id}', [CommentaireController::class, 'update']);
Route::delete('/deletecomment/{id}', [CommentaireController::class, 'destroy']);

Route::get('/historiques', [HistoriqueController::class, 'index']);
Route::post('/historiques', [HistoriqueController::class, 'store']);
Route::get('/hist/{id}', [PrioriteController::class, 'show']);
Route::put('/updatehist/{id}', [HistoriqueController::class, 'update']);
Route::delete('/deletehist/{id}', [HistoriqueController::class, 'destroy']);

Route::get('/agents', [UserController::class, 'getAgents']);
Route::post('/tickets/{id}/assign', [TicketController::class, 'assign']);
Route::get('/tickets/agent/{agentId}', [TicketController::class, 'getticketsByAgent']);
Route::get('/tickets/status/{status}', [TicketController::class, 'getTicketByStatus']);
Route::put('/tickets/update-status/{id}', [TicketController::class, 'updateStatus']);
Route::post('/tickets/send-resolution/{id}', [TicketController::class, 'sendResolution']);
Route::post('/ticket/{ticketId}/rapport', [TicketController::class, 'generateReport']);
Route::get('/tickets/client/{clientId}', [TicketController::class, 'getTicketByClient']);
Route::get('/ticket/{ticketId}/comments', [TicketController::class, 'getComments']);
Route::get('/ticket/{ticketId}/rapport', [TicketController::class, 'getTicketRapport']);
Route::get('/ticket/{agentId}/dashboard', [TicketController::class, 'getdashboard']);
