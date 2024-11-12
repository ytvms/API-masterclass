<?php

use Illuminate\Support\Facades\Route;
use App\Models\Ticket;
use App\Http\Controllers\Api\AuthController;

// http://localhost:8000/api/
// universal resource locator
// tickets
// users

Route::post('/api/login', [AuthController::class, 'login']);
Route::post('/api/register', [AuthController::class, 'register']);

Route::get('/api/tickets', function() {
    return Ticket::all();
});
