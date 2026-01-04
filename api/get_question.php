<?php

// api/get_question.php
declare(strict_types=1);
require_once __DIR__ . '/../php/db.php';

header('Content-Type: application/json');

try {
    $pdo = db();

    // Inputs
    $themeId = $_GET['theme_id'] ?? 0;
    $levelNum = $_GET['level'] ?? 1;
    $userId = $_SESSION['user_id'] ?? null;


    // 1. Map UI Level (1, 2, 3) to DB Difficulty ('Easy', 'Medium', 'Hard')
    $difficultyMap = [
        '1' => 'Easy',
        '2' => 'Medium',
        '3' => 'Hard'
    ];
    $dbDifficulty = $difficultyMap[$levelNum] ?? 'Easy';

    // 2. Fetch Question AND the Correct Answer
    // We join the answers table to get the text of the correct answer
    $sql = "SELECT q.question_id, q.question_text, a.answer_text
            FROM questions q JOIN answers a ON q.question_id = a.question_id
            WHERE q.theme_id = ? AND q.difficulty = ? AND a.is_correct = 1";

    $params = [$theme_id, $dbDifficulty];

    if ($userId) {
        $sql .= " AND q.question_id NOT IN (
                    SELECT question_id
                    FROM user_question_history
                    WHERE user_id = ?)";
        $params[] = $user_id;
    }

    $sql .= " ORDER BY RAND() LIMIT 1";

    $stm = $pdo->prepare($sql);
    $stm->execute([$themeId, $dbDifficulty]);
    $data = $stm->fetch(PDO::FETCH_ASSOC);

    if ($data) {
        if ($user_id) {
            try {
                $insertStm = $pdo->prepare("INSERT INTO user_question_history (user_id, question_id) VALUES (?, ?)");
                $insertStm->execute([$userId, $data['question_id']]);
            } catch (Exception $e) {

            }
        }

        echo json_encode([
            'success' => true,
            'question' => $data['question_text'],
            'answer' => $data['answer_text']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => "You have completed all questions in this category!"
        ]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
}
