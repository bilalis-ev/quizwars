<?php

session_start();
require_once __DIR__ . '/../php/db.php';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect the inputs
    $selectedThemeIds = $_POST['themes'] ?? [];
    $categoryNames = $_POST['cat_names'] ?? [];

    // We need to fetch the Theme Names too (for display on the card)
    // Let's do a quick lookup
    $placeholders = implode(',', array_fill(0, count($selectedThemeIds), '?'));
    $stmt = $pdo->prepare("SELECT theme_id, name FROM themes WHERE theme_id IN ($placeholders)");
    $stmt->execute(array_values($selectedThemeIds));
    $themeDetails = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // [id => name]

    $gameConfig = [];

    // Combine everything into a clean list for the Play page
    foreach ($selectedThemeIds as $catId => $themeId) {
        $gameConfig[] = [
            'cat_name' => $categoryNames[$catId], // e.g., "History"
            'theme_name' => $themeDetails[$themeId], // e.g., "Game of Thrones"
            'theme_id' => $themeId // e.g., 1 (We need this for the API!)
        ];
    }

    $_SESSION['game_config'] = $gameConfig;
    header('Location: quiz.php');
    exit;
}
