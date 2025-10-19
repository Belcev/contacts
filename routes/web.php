<?php

declare(strict_types=1);

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImportController;

Route::redirect('/', '/contacts');

Route::get('/contacts/import', [ImportController::class, 'create'])->name('contacts.import');
Route::post('/contacts/import', [ImportController::class, 'store'])->name('contacts.import.store');

Route::resource('contacts', ContactController::class);
Route::post('/contacts/purge', [ContactController::class, 'purge'])->name('contacts.purge');
