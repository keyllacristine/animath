<?php include __DIR__ . '/../header.php'; ?>

<main class="page-shell">
    <div class="page-header">
        <a href="<?= BASE_URL ?>/home" class="btn-circle btn-back" title="Voltar">
                        <i class="bi bi-reply-fill"></i>
        </a>
        
        <h1 class="page-title">Níveis</h1>
        
        <button type="button" class="btn-circle btn-settings" title="Configurações" aria-label="Abrir configurações" onclick="window.openSettings()">
            <i class="bi bi-gear-fill"></i>
        </button>
    </div>

    <?php 
    $levelConfig = [
        1 => [
            'title' => 'adição',
            'symbol' => '+',
            'lineart' => BASE_URL . '/assets/img/casa_lineart.png',
            'bg_color' => '#D1F875'
        ],
        2 => [
            'title' => 'subtração',
            'symbol' => '−',
            'lineart' => BASE_URL . '/assets/img/arvore_lineart.png',
            'bg_color' => '#D176F1'
        ],
        3 => [
            'title' => 'multiplicação',
            'symbol' => '×',
            'lineart' => BASE_URL . '/assets/img/pinheiros_lineart.png',
            'bg_color' => '#A5F0FA'
        ],
        4 => [
            'title' => 'divisão',
            'symbol' => '÷',
            'lineart' => BASE_URL . '/assets/img/rio_lineart.png',
            'bg_color' => '#F8B17A'
        ]
    ];

    $totalLevels = count($levels) > 0 ? count($levels) : 4;
    $completedCount = 0;
    ?>

    <div class="page-content">
    <div class="levels-grid">
        <?php 
        $previousCompleted = true; 

        foreach ($levels as $index => $level):
            $levelId = (int)$level['id'];
            $config = $levelConfig[$levelId] ?? [
                'title' => 'Nível ' . $levelId,
                'symbol' => '?',
                'lineart' => '',
                'bg_color' => '#F8F3EE'
            ];

            $progress = $userProgress[$levelId] ?? null;
            $isCompleted = $progress && !empty($progress['completed']);
            
            if ($isCompleted) {
                $completedCount++;
            }

            $isLocked = !$previousCompleted;
            $previousCompleted = $isCompleted;
        ?>
            <div class="level-card-wrapper">
                <a href="<?= !$isLocked ? BASE_URL . '/game?id=' . $levelId : 'javascript:void(0)' ?>" 
                   class="level-card <?= $isLocked ? 'locked' : '' ?>"
                   style="background-color: <?= htmlspecialchars($config['bg_color']) ?>;">
                    
                    <?php if (!$isLocked): ?>
                        <span class="level-title"><?= htmlspecialchars($config['title']) ?></span>
                        <span class="level-symbol"><?= htmlspecialchars($config['symbol']) ?></span>
                        
                        <?php if (!empty($config['lineart'])): ?>
                            <img src="<?= htmlspecialchars($config['lineart']) ?>" 
                                 alt="<?= htmlspecialchars($config['title']) ?>" 
                                 class="level-lineart-img">
                        <?php endif; ?>

                    <?php else: ?>
                        <div class="lock-overlay">
                            <i class="bi bi-lock-fill"></i>
                        </div>
                    <?php endif; ?>

                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <?php 
    $overallProgress = round(($completedCount / $totalLevels) * 100);
    $gameDone = $completedCount >= $totalLevels;   
    ?>

    <div class="progress-section">
        <?php if ($gameDone): ?>
            <p class="progress-label">Progresso: <strong>concluído <i class="bi bi-check-circle-fill"></i></strong></p>
        <?php else: ?>
            <p class="progress-label">Progresso: <strong><?= $overallProgress ?>%</strong></p>
        <?php endif; ?>
        <div class="progress-bar-container">
            <div class="progress-bar-fill <?= $gameDone ? 'done' : '' ?>" style="width: <?= $overallProgress ?>%;"></div>
        </div>
    </div>
    </div>
</main>

<?php include __DIR__ . '/../settings/_overlay.php'; ?>
<?php include __DIR__ . '/../footer.php'; ?>