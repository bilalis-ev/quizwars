<?php
require_once __DIR__ . '\..\php\db.php';
$pdo = db();

// 1. Fetch all Categories
$stmt = $pdo->query("SELECT * FROM categories ORDER BY category_id");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2. Fetch all Themes
$stmt = $pdo->query("SELECT * FROM themes");
$allThemes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper: Group themes by category_id for easier display
$themesByCat = [];
foreach ($allThemes as $t) {
    $themesByCat[$t['category_id']][] = $t;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Build Your Quiz</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #1a1a1a; color: white; padding: 40px; }
        .selection-card {
            background: #333; border: 1px solid #555; padding: 20px;
            margin-bottom: 20px; border-radius: 10px;
        }
        select { background: #222; color: white; border: 1px solid #C44DFF; padding: 10px; width: 100%; }
    </style>
</head>
<body>
    <h1 class="text-center mb-5">Configure Your 10 Categories</h1>

    <form action="setup_game.php" method="POST">
        <div class="row">
            <?php foreach ($categories as $cat): ?>
                <div class="col-md-4"> <div class="selection-card">
                        <h3 style="color: #C44DFF;"><?= htmlspecialchars($cat['name']) ?></h3>
                        <p>Select a Theme:</p>
                        
                        <select name="themes[<?= $cat['category_id'] ?>]" required>
                            <option value="" disabled selected>Choose...</option>
                            
                            <?php if (isset($themesByCat[$cat['category_id']])): ?>
                                <?php foreach ($themesByCat[$cat['category_id']] as $theme): ?>
                                    <option value="<?= $theme['theme_id'] ?>">
                                        <?= htmlspecialchars($theme['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option disabled>No themes found</option>
                            <?php endif; ?>
                            
                        </select>
                        
                        <input type="hidden" name="cat_names[<?= $cat['category_id'] ?>]" value="<?= htmlspecialchars($cat['name']) ?>">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success btn-lg">START WAR</button>
        </div>
    </form>
</body>
</html>