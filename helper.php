<?php

use JetBrains\PhpStorm\NoReturn;

#[NoReturn] function dd(...$data) {
    echo '<pre>';
    var_dump(count($data) === 1 ? $data[0] : $data);
    echo '</pre>';
    die();
}

function response(array $data, int $code = 200): bool
{
    http_response_code($code);
    echo json_encode($data);
    return true;
}

function base_path($path): string
{
    return __DIR__ . $path;
}

function setSuccess(string $message): void
{
    Session::set('success', $message);
}

function setError(string $message = 'Something went wrong. Please try again'): void
{
    Session::set('error', $message);
}