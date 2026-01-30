<?php

namespace App\Controllers;

use App\Services\UserService;
use App\Utils\Utils;
use App\Utils\JsonResponse;

class UserController
{
    private $userService;
    private $utils;
    private $jsonResponse;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->utils = new Utils();
        $this->jsonResponse = new JsonResponse();
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

    public function edit($id)
    {
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
        try {
            $request = $_POST;
            if (!$this->validate($request)) {
                return;
            }
            $this->jsonResponse->response($this->userService->post($request), JsonResponse::HTTP_OK);
            return;
        } catch (\Throwable $th) {
            $this->jsonResponse->response(['error' => 'An error occurred: ' . $th->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }
    }

    public function getById($id)
    {
        try {
            $user = $this->userService->getById($id);
            if (!$user) {
                $this->jsonResponse->response(['error' => 'User not found'], JsonResponse::HTTP_NOT_FOUND);
                return;
            }
            $this->jsonResponse->response($user, JsonResponse::HTTP_OK);
            return;
        } catch (\Throwable $th) {
            $this->jsonResponse->response(['error' => 'An error occurred: ' . $th->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }
    }

    public function put($id)
    {
        try {
            $request = $_POST;
            if (!$this->validate($request)) {
                return;
            }
            $updatedUser = $this->userService->put($id, $request);

            if (!$updatedUser) {
                $this->jsonResponse->response(['error' => 'User not found to update'], JsonResponse::HTTP_NOT_FOUND);
                return;
            }
            $this->jsonResponse->response($updatedUser, JsonResponse::HTTP_OK);
            return;
        } catch (\Throwable $th) {
            $this->jsonResponse->response(['error' => 'An error occurred: ' . $th->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }
    }

    public function delete($id)
    {
        try {
            $this->userService->delete($id);
            $this->jsonResponse->response(null, JsonResponse::HTTP_NO_CONTENT);
            return;
        } catch (\Throwable $th) {
            $this->jsonResponse->response(['error' => 'An error occurred: ' . $th->getMessage()], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
            return;
        }
    }

    private function validate($data)
    {
        $requiredFields = ['name', 'email'];
        $missingFields = $this->utils->validateRequiredFields($data, $requiredFields);
        if (!empty($missingFields)) {
            $this->jsonResponse->response(['error' => 'Missing required fields: ' . implode(', ', $missingFields)], JsonResponse::HTTP_BAD_REQUEST);
            return false;
        }
        return true;
    }
    
}
