<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ItemController;

Route::apiResource('items', ItemController::class)->only([
    'index', 'store', 'update', 'destroy', 'show'
]);
