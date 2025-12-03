<?php

use App\Http\Controllers\TaskController;

Route::get('/', [TaskController::class, 'index']);
Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');

// FITUR EDIT
Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tasks.edit');
Route::put('/tasks/{task}/edit', [TaskController::class, 'editUpdate'])->name('tasks.editUpdate');

Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

// reorder (ajax)
Route::post('/tasks/reorder', [TaskController::class, 'reorder'])->name('tasks.reorder');
