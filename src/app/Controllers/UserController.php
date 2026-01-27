<?php

namespace App\Controllers;

use App\Services\UserService;
use App\Utils\Utils;

class UserController
{
    private $userService;
    private $utils;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->utils = new Utils();
    }

    // VIEWS

    public function index()
    {
        $users = $this->userService->getAll();
        $this->utils->render('users/index', ['users' => $users]);
    }

    public function create()
    {
        $this->utils->render('users/create');
    }

    public function edit()
    {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $id = substr($uri, strrpos($uri, '/') + 1);
        $user = $this->userService->getById($id);

        if (!$user) {
            header("HTTP/1.0 404 Not Found");
            echo 'User not found. <a href="/users">Go back</a>';
            return;
        }

        $this->utils->render('users/edit', ['user' => $user]);
    }

    // END-POINTS

    public function store()
    {
        $this->userService->create($_POST);
        $this->redirect('/users');
    }

    public function update()
    {
        $id = $_POST['id'];
        $this->userService->update($id, $_POST);
        $this->redirect('/users');
    }

    public function delete()
    {
        $id = $_POST['id'];
        $this->userService->delete($id);
        $this->redirect('/users');
    }

    private function redirect($url)
    {
        // JS redirect is safer here because Swoole output buffering might interfere with headers
        echo "<script>window.location.href = '$url';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=$url'></noscript>";
    }

    
}
