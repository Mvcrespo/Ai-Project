<?php

use App\Http\Controllers\GenreController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\TheaterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SeatController;
use App\Http\Controllers\CartController;

// Definir a rota inicial para redirecionar para movies.high
Route::get('/', [MovieController::class, 'high'])->name('movies.high');


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
Route::post('users/{user}/block', [UserController::class, 'block'])->name('users.block');
Route::post('users/{user}/unblock', [UserController::class, 'unblock'])->name('users.unblock');
Route::delete('users/{user}/destroy', [UserController::class, 'destroy'])->name('users.destroy');

Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');


Route::resource('seats', SeatController::class);
Route::get('/theaters/{theater}/seats/{screening}', [SeatController::class, 'show']);
Route::get('/seats/{seat}/ticket-details', [SeatController::class, 'ticketDetails']);



Route::get('/cart', [CartController::class, 'show'])->name('cart.show');
Route::post('/cart/add/{ticket}', [CartController::class, 'addToCart'])->name('cart.add');
Route::delete('/cart/remove/{ticket}', [CartController::class, 'removeFromCart'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'destroy'])->name('cart.clear');
Route::post('/cart/confirm', [CartController::class, 'confirm'])->name('cart.confirm');
Route::get('/cart/total', [CartController::class, 'getCartTotal'])->name('cart.total');
