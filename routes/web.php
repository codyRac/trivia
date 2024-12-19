<?php

use App\Http\Controllers\{ProfileController, WelcomeController, TriviaController, ServiceController, CreditController};
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});


Route::post('/start', [WelcomeController::class, 'start'])->name('start');
Route::get('/trivia',[TriviaController::class, 'index'])->name('trivia');
Route::post('/trivia_answer',[TriviaController::class, 'answered'])->name('answered');
Route::post('/use-credits',[CreditController::class, 'useCredits'])->name('useCredits');


Route::get('/redeem',[ServiceController::class, 'redeem'])->name('redeem');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
