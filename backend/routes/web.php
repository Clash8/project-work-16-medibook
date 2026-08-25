<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotte web
|--------------------------------------------------------------------------
| L'applicazione è headless: l'interfaccia utente è servita dal progetto
| React contenuto in ../frontend. Le sole rotte web esposte riguardano la
| documentazione interattiva delle API (Swagger UI).
*/

Route::get('/', fn () => redirect('/api/documentation'));

Route::get('/api/documentation', fn () => view('documentazione'))->name('api.documentation');
