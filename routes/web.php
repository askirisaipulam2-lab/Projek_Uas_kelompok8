<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\LandingPage;
use App\Livewire\BlogShow;

// Route::get('/', function () {
//     return redirect('/admin');
// });

Route::get('/', LandingPage::class)->name('landing-page');
Route::get('/blog/{slug}', BlogShow::class)->name('blog.show');