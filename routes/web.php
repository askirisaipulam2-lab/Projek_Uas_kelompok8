<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\LandingPage;
use App\Livewire\BlogShow;
use App\Livewire\SiberangComponent;

// Route::get('/', function () {
//     return redirect('/admin');
// });

Route::get('/', SiberangComponent::class);
Route::get('/', LandingPage::class)->name('landing-page');
Route::get('/blog/{slug}', BlogShow::class)->name('blog.show');