<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    dd('asdf');
});

Route::get('/frontend/{vue_capture?}', function () {
    return view('layouts.app');
})->where('vue_capture', '[\/\w\.-]*');
