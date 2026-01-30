<?php

namespace App\Utils;

class JsonResponse
{
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_NO_CONTENT = 204;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_NOT_FOUND = 404;
    const HTTP_INTERNAL_SERVER_ERROR = 500;

    private static $statusTexts = [
        200 => 'OK',
        201 => 'Created',
        204 => 'No Content',
        400 => 'Bad Request',
        404 => 'Not Found',
        500 => 'Internal Server Error',
    ];

    private $headers = [];
    private $status = "HTTP/1.0 200 OK";

    public function __construct()
    {
        $this->headers['Content-Type'] = 'application/json';
    }

    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    public function setStatus($status)
    {
        if (isset(self::$statusTexts[$status])) {
            $this->status = "HTTP/1.0 {$status} " . self::$statusTexts[$status];
        } else {
            $this->status = $status;
        }
        syslog(LOG_INFO, "Response API status: " . $this->status);
    }

    public function sendHeaders()
    {
        header($this->status);
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }
    }

    public function response($data = null, $status = null)
    {
        if ($status !== null) {
            $this->setStatus($status);
        }
        $this->sendHeaders();
        if ($data !== null) {
            echo json_encode($data);
            syslog(LOG_INFO, "Response API data: " . json_encode($data));
        }
    }
}
