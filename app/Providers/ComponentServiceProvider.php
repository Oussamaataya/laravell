<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\ChatButton;

class ComponentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::component('chat-button', ChatButton::class);
    }

    public function register()
    {
        //
    }
}