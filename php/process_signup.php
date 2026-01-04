<?php

declare(strict_types=1);
session_start();

// only post acceptance
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed');
}

$csrf = $_POST['csrf'] ?? '';

if (!hash_equals($_SESSION['csrf_token'] ?? '', $csrf)) {
    http_response_code(419);
    exit('Invalid or missing csrf token');
}

// input collection and trimming
$email = trim($_POST['email'] ?? '');
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';

require_once __DIR__ . '/db.php';
$pdo = db();

$errors = [];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email';
    $_SESSION['signup_errors'] = $errors;
    $_SESSION['signup_old'] = ['email' => $email];
    header('Location: /quizwars/pages/signup.php');
    exit;
} else {
    $stm = $pdo->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
    $stm->execute([$email]);
    if ($stm->fetch()) {
        $errors[] = 'Email already used';
        header('Location: /quizwars/pages/signup.php');
        exit;
    }
}

if (!preg_match('/^[a-zA-Z0-9_.]{3,20}$/', $username)) {
    $errors[] = 'Invalid username';
    header('Location: /quizwars/pages/signup.php');
    exit;
} else {
    $stm = $pdo->prepare('SELECT user_id FROM users WHERE username = ? LIMIT 1');
    $stm->execute([$username]);

    if ($stm->fetch()) {
        $errors[] = "Username already used";
        header('Location: /quizwars/pages/signup.php');
        exit;
    }
}

$len = strlen($password);

if ($len < 8 || $len > 32) {
    $errors[] = 'Password must be 8-32 characters long';
}
if (!preg_match('/[A-Z]/', $password)) {
    $errors[] = 'Password must contain at least one uppercase letter';
}
if (!preg_match('/[a-z]/', $password)) {
    $errors[] = 'Password must contain at least one lowercase letter';
}
if (!preg_match('/[0-9]/', $password)) {
    $errors[] = 'Password must contain at least one number';
}
if (!preg_match('/[^A-Za-z0-9]/', $password)) {
    $errors[] = 'Password must contain a special character';
}
if (preg_match('/\s/', $password)) {
    $errors[] = 'Password must not contain spaces';
}
if ($errors) {
    $_SESSION['signup_errors'] = $errors;
    header('Location: /quizwars/pages/signup.php');
    exit;
}

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stm = $pdo->prepare('INSERT INTO users (email, username, password) VALUES (?, ?, ?)');
    $stm->execute([$email, $username, $hash]);

    header('Location: /quizwars/pages/login.php');
    exit;
} catch (Throwable $e) {
    if ($e instanceof PDOException && $e->getCode() === '23000') {
        header('Content-Type: text/plain; charset=utf-8');
        echo "Signup failed:\n- Username or email is already taken.";
        exit;
    }

    http_response_code(500);
    exit('Unexpected error. Please try again.');
}
