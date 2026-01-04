<?php

// absolute paths
define('PATH_ROOT', realpath(__DIR__ . '/..'));
define('PATH_PHP', PATH_ROOT . '/php');
define('PATH_ASSETS', PATH_ROOT . '/assets');
define('PATH_DB', PATH_ROOT . '/db');

// http or https
$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_ROOT'] == 443);
$scheme = $https ? 'https' : 'http';

// user-entered domain name
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// page and root urls
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '\//') . '/';
$URL_PAGES = $scriptDir;
$URL_ROOT = rtrim(dirname($URL_PAGES), '\//') . '/';

// helpers
function url(string $path = ''): string
{
    global $URL_ROOT;
    return $URL_ROOT . ltrim($path, '/');
}

function pages(string $path = ''): string
{
    global $URL_PAGES;
    return $URL_PAGES . ltrim($path, '/');
}

function asset(string $path = ''): string
{
    return url('assets/' . ltrim($path, '/'));
}
