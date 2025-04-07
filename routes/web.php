<?php

use App\Events\UserStatusUpdated;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::post('/send-otp', [OtpController::class, 'sendOtp'])->name('send.otp');
Route::post('/verify-otp', [OtpController::class, 'verifyOtp'])->name('verify.otp');
Route::middleware(['auth'])->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat');
    Route::post('/group-member', [ChatController::class, 'memberGroup'])->name('group-member');
    Route::post('/send-message', [ChatController::class, 'sendMessage'])->name('send.message');
    Route::post('/mark-messages-as-read', [ChatController::class, 'markMessagesAsRead']);
    Route::post('/fetch-messages', [ChatController::class, 'fetchMessages'])->name('fetch.messages');

    Route::post('/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::delete('delete-group/{id}', [GroupController::class, 'destroy'])->name('group.delete');


    Route::get('/dashboard', function () {
        return view('dashboard');
    })->middleware('verified')->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/update-profile-img/{userid}', [ProfileController::class, 'updateProfileimg'])
        ->name('update.profile-img');

});

Route::get('/keep-alive', function () {
    if (Auth::check()) {
        $user = Auth::user();
        $user->update(['last_seen' => now()]);
        Cache::put('user-online-' . $user->id, true, now()->addMinutes(5));

        broadcast(new UserStatusUpdated($user));
    }
    return response()->json(['status' => 'refreshed']);
});


require __DIR__ . '/auth.php';
