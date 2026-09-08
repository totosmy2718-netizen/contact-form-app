<?php
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TagController;

Route::get('/', [ContactController::class, 'index']);

Route::post('contacts/confirm', [ContactController::class, 'confirm']);

Route::post('/contacts', [ContactController::class, 'store']);

Route::get('/thanks', [ContactController::class, 'thanks']);

Route::middleware('auth')->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);

    // お問い合わせ詳細画面
    Route::get('/admin/contacts/{contact}', [AdminController::class, 'show']);

    // お問い合わせ削除
    Route::delete('/admin/contacts/{contact}', [AdminController::class, 'destroy']);

    // タグ新規登録
    Route::post('/admin/tags', [TagController::class, 'store']);

    // タグ編集画面
    Route::get('/admin/tags/{tag}/edit', [TagController::class, 'edit']);

    // タグ更新
    Route::put('/admin/tags/{tag}', [TagController::class, 'update']);

    // タグ削除
    Route::delete('/admin/tags/{tag}', [TagController::class, 'destroy']);
});