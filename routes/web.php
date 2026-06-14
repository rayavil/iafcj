<?php

use App\Http\Controllers\RegistroVisitaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [RegistroVisitaController::class, 'create'])->name('registro');
Route::post('/', [RegistroVisitaController::class, 'store'])->name('registro.store');
