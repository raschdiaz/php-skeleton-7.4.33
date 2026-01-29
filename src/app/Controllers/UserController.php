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
            //header("HTTP/1.0 404 Not Found");
            //echo 'User not found. <a href="/users">Go back</a>';
            $this->utils->redirect('/users');
            return;
        }

        $this->utils->render('users/edit', ['user' => $user]);
    }

    // END-POINTS

    public function post()
    {
        header('Content-Type: application/json');
        try {

            $request = $_POST;
            // Validate required fields
            $requiredFields = ['name', 'email'];
            $validateRequiredFields = $this->utils->validateRequiredFields($request, $requiredFields);
            if (!empty($validateRequiredFields)) {
                header("HTTP/1.0 400 Bad Request");
                echo json_encode(['error' => 'Missing required fields: ' . implode(', ', $validateRequiredFields)]);
                return;
            }
            echo json_encode($this->userService->post($request));
            return;
        } catch (\Throwable $th) {
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode(['error' => 'An error occurred: ' . $th->getMessage()]);
        }
    }

    public function getById()
    {
        header('Content-Type: application/json');
        try {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $id = substr($uri, strrpos($uri, '/') + 1);
            $user = $this->userService->getById($id);
            if (!$user) {
                header("HTTP/1.0 404 Not Found");
                echo json_encode(['error' => 'User not found']);
                return;
            }
            echo json_encode($user);
            return;
        } catch (\Throwable $th) {
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode(['error' => 'An error occurred: ' . $th->getMessage()]);
        }
    }

    public function put()
    {
        header('Content-Type: application/json');
        try {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $id = substr($uri, strrpos($uri, '/') + 1);
            $request = $_POST;
            // Validate required fields
            $requiredFields = ['name', 'email'];
            $validateRequiredFields = $this->utils->validateRequiredFields($request, $requiredFields);
            if (!empty($validateRequiredFields)) {
                header("HTTP/1.0 400 Bad Request");
                echo json_encode(['error' => 'Missing required fields: ' . implode(', ', $validateRequiredFields)]);
                return;
            }
            echo json_encode($this->userService->put($id, $request));
            return;
        } catch (\Throwable $th) {
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode(['error' => 'An error occurred: ' . $th->getMessage()]);
        }
    }

    public function delete()
    {
        header('Content-Type: application/json');
        try {
            $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $id = substr($uri, strrpos($uri, '/') + 1);
            $this->userService->delete($id);
            header("HTTP/1.0 204 No Content");
            return;
        } catch (\Throwable $th) {
            header("HTTP/1.0 500 Internal Server Error");
            echo json_encode(['error' => 'An error occurred: ' . $th->getMessage()]);
        }
    }
    
}
