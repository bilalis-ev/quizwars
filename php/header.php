<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Play ready-made quizzes, build custom quizzes, or suggest new categories on QuizWars.">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel = "stylesheet" href = "../assets/css/style.css?v=3">

    <title><?= htmlspecialchars($page_title ?? 'Quizwars', ENT_QUOTES, 'UTF-8') ?></title>
</head>
<body>
    <header>    
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">            
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php"><img src="../assets/logo1.png" alt="Quizwars" height="40"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="browse.php">Browse</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="quiz.php">Play</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="create.php">Custom</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="leaderboard.php">Leaderboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="suggest.php">Suggestions</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="account.php">Account</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="signup.php">Signup</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
<main>