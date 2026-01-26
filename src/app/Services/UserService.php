<?php

namespace App\Services;

#require_one '../'

class UserService
{
    private $file;

    public function __construct()
    {
        // Use BASE_PATH defined in bootstrap.php, or fallback
        $base = defined('BASE_PATH') ? BASE_PATH : __DIR__ . '/../..';
        $this->file = $base . '/users.json';
        
        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([]));
        }
    }

    public function getAll()
    {
        $content = file_get_contents($this->file);
        return json_decode($content, true) ?? [];
    }

    public function getById($id)
    {
        $users = $this->getAll();
        foreach ($users as $user) {
            if ($user['id'] == $id) {
                return $user;
            }
        }
        return null;
    }

    public function create($data)
    {
        $users = $this->getAll();
        // Simple auto-increment logic
        $id = count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1;
        
        $newUser = [
            'id' => $id,
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? ''
        ];
        
        $users[] = $newUser;
        $this->save($users);
    }

    public function update($id, $data)
    {
        $users = $this->getAll();
        foreach ($users as &$user) {
            if ($user['id'] == $id) {
                $user['name'] = $data['name'];
                $user['email'] = $data['email'];
                break;
            }
        }
        $this->save($users);
    }

    public function delete($id)
    {
        $users = $this->getAll();
        $users = array_filter($users, function ($user) use ($id) {
            return $user['id'] != $id;
        });
        $this->save(array_values($users));
    }

    private function save($users)
    {
        file_put_contents($this->file, json_encode($users, JSON_PRETTY_PRINT));
    }
}
