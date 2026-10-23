<?php include __DIR__ . '/../header.php'; ?>

<main class="developers-page">
    <!-- Botão Voltar -->
    <div class="top-bar">
        <a href="<?= BASE_URL ?>/settings" class="btn-circle btn-back" title="Voltar">
            <i class="bi bi-reply-fill"></i>
        </a>
    </div>

    <?php 
    // Array com a lista de desenvolvedores
    $developers = [
        ['img' => 'dev1.png', 'name' => 'nome', 'role' => "papel no\ndesenvolvimento"],
        ['img' => 'dev2.png', 'name' => 'nome', 'role' => "papel no\ndesenvolvimento"],
        ['img' => 'dev3.png', 'name' => 'nome', 'role' => "papel no\ndesenvolvimento"],
        ['img' => 'dev4.png', 'name' => 'nome', 'role' => "papel no\ndesenvolvimento"],
        ['img' => 'dev5.png', 'name' => 'nome', 'role' => "papel no\ndesenvolvimento"],
        ['img' => 'dev6.png', 'name' => 'nome', 'role' => "papel no\ndesenvolvimento"],
    ];
    ?>

    <!-- Grid de Desenvolvedores (3 colunas x 2 linhas) -->
    <div class="devs-grid">
        <?php foreach ($developers as $dev): ?>
            <article class="dev-card">
                <div class="dev-avatar">
                    <img src="<?= BASE_URL ?>/assets/images/<?= htmlspecialchars($dev['img']) ?>" alt="<?= htmlspecialchars($dev['name']) ?>">
                </div>
                <h3 class="dev-name"><?= htmlspecialchars($dev['name']) ?></h3>
                <p class="dev-role"><?= nl2br(htmlspecialchars($dev['role'])) ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</main>

<?php include __DIR__ . '/../footer.php'; ?>