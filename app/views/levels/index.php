<?php include __DIR__ . '/../header.php'; ?>

<main class="level-select-page">
    <!-- Cabeçalho -->
    <div class="level-header">
        <a href="<?= BASE_URL ?>/home" class="btn-circle btn-back" title="Voltar">
            <span class="icon-back">↩</span>
        </a>
        
        <h1 class="page-title">Níveis</h1>
        
        <a href="<?= BASE_URL ?>/settings" class="btn-circle btn-settings" title="Configurações">
            <span class="icon-gear">⚙</span>
        </a>
    </div>

    <?php 
    // Configurações visuais dos 4 níveis
    $levelConfig = [
        1 => [
            'title' => 'adição',
            'symbol' => '+',
            'lineart' => BASE_URL . '/assets/img/casa_lineart.png',
            'bg_color' => '#E2FCDA'
        ],
        2 => [
            'title' => 'subtração',
            'symbol' => '−',
            'lineart' => BASE_URL . '/assets/img/arvore_lineart.png',
            'bg_color' => '#F0DAFB'
        ],
        3 => [
            'title' => 'multiplicação',
            'symbol' => '×',
            'lineart' => BASE_URL . '/assets/img/pinheiros_lineart.png',
            'bg_color' => '#B7F9F7'
        ],
        4 => [
            'title' => 'divisão',
            'symbol' => '÷',
            'lineart' => BASE_URL . '/assets/img/rio_lineart.png',
            'bg_color' => '#FFE5B4'
        ]
    ];

    $totalLevels = count($levels) > 0 ? count($levels) : 4;
    $completedCount = 0;
    ?>

    <!-- Grid de Níveis -->
    <div class="levels-grid">
        <?php 
        $previousCompleted = true; // 1º nível desbloqueado por padrão

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

            // Bloqueia se o nível anterior não foi concluído
            $isLocked = !$previousCompleted;
            $previousCompleted = $isCompleted;
        ?>
            <div class="level-card-wrapper">
                <a href="<?= !$isLocked ? BASE_URL . '/game?id=' . $levelId : 'javascript:void(0)' ?>" 
                   class="level-card <?= $isLocked ? 'locked' : '' ?>"
                   style="background-color: <?= htmlspecialchars($config['bg_color']) ?>;">
                    
                    <?php if (!$isLocked): ?>
                        <!-- Só exibe as informações se o nível estiver DESBLOQUEADO -->
                        <span class="level-title"><?= htmlspecialchars($config['title']) ?></span>
                        <span class="level-symbol"><?= htmlspecialchars($config['symbol']) ?></span>
                        
                        <?php if (!empty($config['lineart'])): ?>
                            <img src="<?= htmlspecialchars($config['lineart']) ?>" 
                                 alt="<?= htmlspecialchars($config['title']) ?>" 
                                 class="level-lineart-img">
                        <?php endif; ?>

                    <?php else: ?>
                        <!-- Se estiver BLOQUEADO, mostra apenas o overlay com o cadeado -->
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
    ?>

    <!-- Barra de Progresso -->
    <div class="progress-section">
        <p class="progress-label">Progresso: <strong><?= $overallProgress ?>%</strong></p>
        <div class="progress-bar-container">
            <div class="progress-bar-fill" style="width: <?= $overallProgress ?>%;"></div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../footer.php'; ?>