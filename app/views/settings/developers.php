<?php include __DIR__ . '/../header.php'; ?>

<main class="developers-page">
    <!-- Botão Voltar -->
    <div class="top-bar">
        <a href="<?= BASE_URL ?>/settings" class="btn-circle btn-back" title="Voltar">
            <i class="bi bi-reply-fill"></i>
        </a>
    </div>

    <?php 
    $developers = [
        ['img' => 'dev1.png', 'name' => 'keylla', 'role' => "arte/conceito \nprogramação"],
        ['img' => 'dev2.png', 'name' => 'luiza', 'role' => "programação \ngame-design"],
        ['img' => 'dev3.png', 'name' => 'rodrigo', 'role' => "documentação \npesquisa"],
        ['img' => 'dev4.png', 'name' => 'julia', 'role' => "documentação no\npesquisa"],
        ['img' => 'dev5.png', 'name' => 'laura', 'role' => "documentação\nbanco de dados"],
        ['img' => 'dev6.png', 'name' => 'evilly', 'role' => "documentação \nbanco de dados"],
    ];
    ?>
    <!-- Grid de Desenvolvedores (3 colunas x 2 linhas) -->
    <div class="devs-grid">
        <?php foreach ($developers as $dev): ?>
            <article class="dev-card">
                <div class="dev-avatar">
                    <img src="<?= BASE_URL ?>/assets/img/devs/<?= htmlspecialchars($dev['img']) ?>" alt="<?= htmlspecialchars($dev['name']) ?>">
                </div>
                <h3 class="dev-name"><?= htmlspecialchars($dev['name']) ?></h3>
                <p class="dev-role"><?= nl2br(htmlspecialchars($dev['role'])) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</main>

<?php include __DIR__ . '/../footer.php'; ?>