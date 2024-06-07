<?php

use App\Http\Controllers\GenreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\TheaterController;
use App\Http\Controllers\UserController;


// Definir a rota inicial para redirecionar para movies.high
Route::get('/', [MovieController::class, 'high'])->name('movies.high');


// Grupo de rotas que requerem autenticação
Route::middleware('auth')->group(function () {
    Route::get('/password', [ProfileController::class, 'editPassword'])->name('profile.edit.password');
});
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Autenticação padrão do Laravel
require __DIR__ . '/auth.php';

// Recursos de genres (gêneros)
Route::resource('genres', GenreController::class);

// Recursos de theaters
Route::resource('theaters', TheaterController::class);

// Recursos de movies
Route::resource('movies', MovieController::class);
// Rotas para os filmes highlighted
Route::get('/highlighted', [MovieController::class, 'highlighted'])->name('movies.highlighted');
Route::get('/highlighted/search', [MovieController::class, 'highlightedSearch'])->name('movies.highlighted_search');
Route::get('/high_movie/{id}', [MovieController::class, 'high_show'])->name('movies.high_show');


Route::resource('users', UserController::class);

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');

