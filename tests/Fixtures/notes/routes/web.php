<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => 'notes index')->name('index');
