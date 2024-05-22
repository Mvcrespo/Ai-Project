<?php

use App\Http\Controllers\GenreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;



// Grupo de rotas que requerem autenticação
Route::middleware('auth')->group(function () {
    Route::get('/password', [ProfileController::class, 'editPassword'])->name('profile.edit.password');
});

// Rota para o dashboard
Route::view('/dashboard', 'dashboard')->name('dashboard');

// Autenticação padrão do Laravel
require __DIR__ . '/auth.php';

// Recursos de genres (gêneros)
Route::resource('genres', GenreController::class);


// Rota para a página inicial que lista os filmes
Route::get('/', [MovieController::class, 'index'])->name('movies.index');
// Rotas para os filmes
Route::get('/movies/all', [MovieController::class, 'allmovies'])->name('movies.allmovies');
Route::get('/movies/search', [MovieController::class, 'search'])->name('movies.search');
Route::get('/movies/{id}', [MovieController::class, 'show'])->name('movies.show');

Route::get('/movies/highlighted', [MovieController::class, 'highlighted'])->name('movies.highlighted');
Route::get('/movies/highlighted/search', [MovieController::class, 'highlightedSearch'])->name('movies.highlighted_search');


Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
