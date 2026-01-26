<?php

namespace App\Controllers;

use App\Services\WelcomeService;

class HomeController
{
    public function index()
    {
        //$service = new WelcomeService();
        //echo $service->getMessage();
        echo '<h1>Welcome to Vanilla PHP App</h1>';
        echo '<a href="/users">Users</a>';
    }
}   