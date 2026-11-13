<?php include __DIR__ . '/../header.php'; ?>

<main class="level-select-page arcade-page">
    <div class="level-header">
        <a href="<?= BASE_URL ?>/home" class="btn-circle btn-back" title="Voltar">
            <i class="bi bi-reply-fill"></i>
        </a>

        <h1 class="page-title"><?= htmlspecialchars($arcadeTitle) ?></h1>

        <a href="<?= BASE_URL ?>/settings" class="btn-circle btn-settings" title="Configurações">
            <i class="bi bi-gear-fill"></i>
        </a>
    </div>

    <?php
    $games = [
        ['slug' => 'chuva',   'title' => 'chuva de contas', 'symbol' => '?',    'bg_color' => '#D1F875'],
        ['slug' => 'sudoku',  'title' => 'sudoku',          'symbol' => '#',    'bg_color' => '#D176F1'],
        ['slug' => 'memoria', 'title' => 'memória',         'symbol' => '[ ]',  'bg_color' => '#A5F0FA'],
        ['slug' => 'balanca', 'title' => 'balança',         'symbol' => '=',    'bg_color' => '#F8B17A'],
    ];
    ?>

    <div class="levels-grid">
        <?php foreach ($games as $game): ?>
            <div class="level-card-wrapper">
                <a href="<?= BASE_URL ?>/game/arcade/<?= $game['slug'] ?>"
                   class="level-card"
                   style="background-color: <?= $game['bg_color'] ?>;"
                   onclick="soundManager.playClickSound()">
                    <span class="level-title" style="text-align:center"><?= htmlspecialchars($game['title']) ?></span>
                    <span class="level-symbol"><?= htmlspecialchars($game['symbol']) ?></span>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<?php include __DIR__ . '/../footer.php'; ?>
