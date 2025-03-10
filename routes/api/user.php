<?php

use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::resource('users', UserController::class , ['except' => ['create', 'edit']]);