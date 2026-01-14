<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'public.home');
Route::view('/admin-test', 'admin.dashboard');
