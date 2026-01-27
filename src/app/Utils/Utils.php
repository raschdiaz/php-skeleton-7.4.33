<?php

namespace App\Utils;

class Utils
{
    // Simple function to escape output for XSS protection
    private function escape($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'escape'], $data);
        }
        if (is_string($data)) {
            return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
        }
        return $data;
    }

    // Render a view with data
    public function render($view, $data = [])
    {
        $data = $this->escape($data);
        // Makes variables available in the view file (e.g., $users, $user)
        extract($data);
        $viewPath = __DIR__ . "/../../resources/views/{$view}.php";
        require_once $viewPath;
    }

}