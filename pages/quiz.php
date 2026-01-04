<?php session_start(); ?>

<!DOCTYPE html>
<html lang="el">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QuizWars: Play</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="../assets/css/style1.css?v=7">
</head>
<body>
<main class="container-fluid">
  <video autoplay muted loop id="bg-video">
    <source src="../assets/space.mp4" type="video/mp4">
  </video>

  <div class="quizwars">
    <a href="index.php" class="logo-link">
      <img src="../assets/logo1.png" alt="Quiz Logo" class="logo">
    </a>

    <div class="categories">
    <?php
    $gameConfig = $_SESSION['game_config'] ?? [];

// Optional: Map specific category names to your CSS colors
$colors = [
    'History' => 'brown', 'Geography' => 'blue', 'Handler ID' => 'red',
    'Logo ID' => 'pink', 'Guess the Song' => 'grey', 'Top 5' => 'green',
    'Movie ID' => 'purple', 'Pokemon ID' => 'gold', 'Photo ID' => 'lightblue',
    'Dribbler ID' => 'orange'
];
?>

    <?php foreach ($gameConfig as $card): ?>
        <?php $colorClass = $colors[$card['cat_name']] ?? 'purple'; ?>
        
        <div class="category <?= $colorClass ?>" 
             data-theme-id="<?= $card['theme_id'] ?>" 
             data-theme-name="<?= htmlspecialchars($card['theme_name']) ?>">
             
            <h2><?= htmlspecialchars($card['cat_name']) ?></h2>
            <p style="font-size: 0.8rem; margin-top:-10px; opacity:0.8; color:#eee;">
                <?= htmlspecialchars($card['theme_name']) ?>
            </p>
            
            <div class="levels"><span>1</span><span>2</span><span>3</span></div>
        </div>
    <?php endforeach; ?>
    </div>
      
    <div class="teams">
      <div class="team team-left">
        <div class="helpers">
          <span class="helper fifty-helper" title="50/50">50/50</span>
          <span class="helper phone-helper" title="Τηλεφώνημα σε φίλο">
            <i class="fa-solid fa-phone"></i>
          </span>
          <span class="helper" title="Διπλή Ευκαιρία">x2</span>
        </div>
        <div class="info">
          <h3>ΑΧΑΣΤΟΙ</h3>
          <div class="controls">
            <button class="minus">−</button>
            <div class="score">0</div>
            <button class="plus">+</button>
          </div>
        </div>
      </div>

      <div class="team team-right">
        <div class="info">
          <h3>NO BALLS BOYS</h3>
          <div class="controls">
            <button class="minus">−</button>
            <div class="score">0</div>
            <button class="plus">+</button>
          </div>
        </div>
        <div class="helpers">
          <span class="helper fifty-helper" title="50/50">50/50</span>
          <span class="helper phone-helper" title="Τηλεφώνημα σε φίλο">
            <i class="fa-solid fa-phone"></i>
          </span>
          <span class="helper double-helper" title="Διπλή Ευκαιρία">x2</span>
        </div>
      </div>
    </div>
  </div>

  <div id="categoryOverlay" class="overlay">
    <div class="overlay-content"></div>
  </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
  //scoreboard
  let pointsToAdd = 0;

  const teams = document.querySelectorAll('.team');

  teams.forEach(team => {
    const minusBtn = team.querySelector('.minus');
    const plusBtn = team.querySelector('.plus');
    const scoreDisplay = team.querySelector('.score');

    minusBtn.addEventListener('click', () => {
      let current_score = parseInt(scoreDisplay.innerText);
      current_score -= pointsToAdd;
      scoreDisplay.innerText = current_score;
    })

    plusBtn.addEventListener('click', () => {
      let current_score = parseInt(scoreDisplay.innerText);
      current_score += pointsToAdd;
      scoreDisplay.innerText = current_score;
    })
  })

  const levels = document.querySelectorAll('.levels span');
  const overlay = document.getElementById('categoryOverlay');
  const overlayContent = document.querySelector('.overlay-content');
  const playedLevels = new Set();
  let lastRect = null;

  // === 1. CLICK ON THE MAIN GRID (Animation Start) ===
  levels.forEach(level => {
    level.addEventListener('click', e => {
      const category = e.target.closest('.category');
      
      // Get Data from the Original Card
      const themeId = category.getAttribute('data-theme-id');
      const catName = category.querySelector('h2').innerText;
      const themeName = category.getAttribute('data-theme-name');
      const levelNum = e.target.innerText;
      const levelKey = `${themeId}-${levelNum}`;

      pointsToAdd = parseInt(levelNum);

      // Prevent playing the same level twice
      if (playedLevels.has(levelKey)) return;

      // --- Animation Setup ---
      const rect = category.getBoundingClientRect();
      lastRect = rect;

      // Create the "Clone" (The card that flies to the center)
      const clone = category.cloneNode(true);
      overlayContent.innerHTML = '';
      overlayContent.appendChild(clone);
      overlay.classList.add('active');

      // Set initial position (on top of original)
      overlayContent.style.left = rect.left + 'px';
      overlayContent.style.top = rect.top + 'px';
      overlayContent.style.width = rect.width + 'px';
      overlayContent.style.height = rect.height + 'px';
      overlayContent.style.transform = 'scale(1)';
      overlayContent.style.transition = 'all 0.6s cubic-bezier(0.22, 1, 0.36, 1)';

      // Move to Center
      requestAnimationFrame(() => {
        const cx = window.innerWidth / 2 - rect.width / 2;
        const cy = window.innerHeight / 2 - rect.height / 2;
        overlayContent.style.left = cx + 'px';
        overlayContent.style.top = cy + 'px';
        overlayContent.style.width = rect.width * 1.2 + 'px';
        overlayContent.style.height = rect.height * 1.2 + 'px';
        overlayContent.style.transform = 'scale(1.2)';
      });

      // === 2. AFTER ANIMATION (Activate the Cloned Card) ===
      setTimeout(() => {
        // Gray out the background levels
        document.querySelectorAll('.levels span').forEach(sp => {
           const pCat = sp.closest('.category');
           const key = `${pCat.getAttribute('data-theme-id')}-${sp.innerText}`;
           if (key !== levelKey && !playedLevels.has(key)) sp.classList.add('disabled');
        });

        // Find the specific button INSIDE THE CLONE
        const cloneLevels = clone.querySelectorAll('.levels span');
        
        cloneLevels.forEach(sp => {
          // We only activate the button matching the level we clicked (1, 2, or 3)
          if (sp.innerText === levelNum) {
            
            // --- ATTACH THE FETCH LISTENER HERE ---
            sp.addEventListener('click', () => {
              
              // Get color for styling (optional)
              let categoryColor = '#C44DFF'; // Default Purple
              // You can expand this color map if you want specific colors
              const colorMap = { 'History': '#DE7135', 'Geography': '#227EBD' }; 
              if (colorMap[catName]) categoryColor = colorMap[catName];

              // A. Show Loading Spinner
              overlayContent.innerHTML = `
                <div class="question-box">
                  <h2 style="color:${categoryColor};">${catName}</h2>
                  <p style="opacity:0.8; font-size:0.9rem;">${themeName} - Level ${levelNum}</p>
                  <div class="spinner-border text-light" role="status" style="margin: 20px;">
                     <span class="visually-hidden">Loading...</span>
                  </div>
                </div>
              `;

              // B. Call the Database (API)
              // We use the themeId we grabbed earlier
              fetch(`../api/get_question.php?theme_id=${themeId}&level=${levelNum}`)
                .then(response => response.json())
                .then(data => {
                  if (data.success) {
                    // C. Show Question
                    overlayContent.innerHTML = `
                      <div class="question-box">
                        <h2 style="color:${categoryColor};">${catName}</h2>
                        <p style="opacity:0.8; margin-top:-10px;">${themeName} - Level ${levelNum}</p>
                        
                        <p class="q-text" style="font-size: 1.4rem; margin: 20px 0;">${data.question}</p>
                        
                        <div id="answer-area" style="display:none; margin-top:20px; border-top:1px solid #555; padding-top:15px; color:#fff;">
                           <strong style="color:${categoryColor}">Answer:</strong> ${data.answer}
                        </div>

                        <div style="margin-top: 30px;">
                           <button id="reveal-btn" class="btn btn-light btn-sm" style="color:${categoryColor}; font-weight:bold;">Show Answer</button>
                        </div>

                        <button class="back-arrow" style="background:${categoryColor};color:white; position:absolute; bottom:20px; right:20px; border:none; width:40px; height:40px; border-radius:50%; font-size:1.2rem;">&#8592;</button>
                      </div>
                    `;

                    // "Show Answer" Click
                    document.getElementById('reveal-btn').addEventListener('click', function() {
                      document.getElementById('answer-area').style.display = 'block';
                      this.style.display = 'none';
                    });

                  } else {
                    // Database Error (No question found)
                    overlayContent.innerHTML = `
                      <div class="question-box">
                        <h2>Error</h2>
                        <p>${data.message}</p>
                        <button class="back-arrow" style="background:${categoryColor};color:white;">&#8592;</button>
                      </div>`;
                  }

                  // D. Back Arrow Logic (Close Window)
                  const backArrow = overlayContent.querySelector('.back-arrow');
                  backArrow.onclick = () => {
                    // Reverse Animation
                    overlayContent.style.transition = 'all 0.6s cubic-bezier(0.65, 0, 0.35, 1)';
                    overlayContent.style.left = lastRect.left + 'px';
                    overlayContent.style.top = lastRect.top + 'px';
                    overlayContent.style.width = lastRect.width + 'px';
                    overlayContent.style.height = lastRect.height + 'px';
                    overlayContent.style.transform = 'scale(1)';

                    setTimeout(() => {
                      overlay.classList.remove('active');
                      playedLevels.add(levelKey);
                      
                      // Mark used levels on main board
                      document.querySelectorAll('.levels span').forEach(sp2 => {
                        const pCat2 = sp2.closest('.category');
                        const key2 = `${pCat2.getAttribute('data-theme-id')}-${sp2.innerText}`;
                        if (playedLevels.has(key2)) sp2.classList.add('used');
                        sp2.classList.remove('disabled');
                      });
                    }, 600);
                  };
                })
                .catch(err => {
                   console.error('Fetch error:', err);
                   overlayContent.innerHTML = `<div class="question-box"><p>Connection Error.</p></div>`;
                });
            }); // End Fetch Listener
          }
        });
      }, 600); // End Timeout
    });
  });

  // Close overlay on outside click
  overlay.addEventListener('click', e => {
    if (e.target === overlay) overlay.classList.remove('active');
  });
});
</script>
</body>
</html>