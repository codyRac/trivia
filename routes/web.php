<?php

use App\Http\Controllers\{ProfileController, WelcomeController,
    TriviaController, ServiceController, CreditController,
     MusicController, EmojiController};
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
Route::get('/holding', [WelcomeController::class, 'holding'])->name('holding');

Route::get('/trivia',[TriviaController::class, 'index'])->name('trivia');
Route::post('/trivia_answer',[TriviaController::class, 'answered'])->name('answered');
Route::post('/use-credits',[CreditController::class, 'useCredits'])->name('useCredits');

Route::get('/music',[MusicController::class, 'index'])->name('music');
Route::post('/music/rate',[MusicController::class, 'rate'])->name('music.rate');

Route::get('/emoji', [EmojiController::class, 'index'])->name('emoji');
Route::post('/emoji/answer', [EmojiController::class, 'answer'])->name('emoji.answer');

Route::get('/redeem',[ServiceController::class, 'redeem'])->name('redeem');
Route::post('/fav',[ServiceController::class, 'fav'])->name('fav');


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
