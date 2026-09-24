<?php include __DIR__ . '/../header.php' ?>    
    
    <div>
        <a href="<?= BASE_URL ?>/home">
            <button type="button">&hookleftarrow; Voltar</button>
        </a>
    </div>

    <br>

    <main class="container profile">
        <section class="profile">
            <header>
                <h2>seu perfil </h2>
            </header>

            <form action="<?= BASE_URL ?>/profile" method="POST">
                <div>
                    <label for="username">nome:</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($userData['username'] ?? $_SESSION['username']) ?>" required>
                    <button type="submit" title="Salvar alterações">✏️</button>
                </div>
                <fieldset class="avatar">
                    <legend>Avatar</legend>

                    <label>
                        <input type="radio" name="avatar" value="1" <?= ($userData['avatar'] == 1) ? 'checked' : '' ?>>
                        <img src="<?= BASE_URL ?>/assets/img/av1.png" alt="Avatar Coelho Coelho com Ursinho" width="100">
                        </label>

                    <label>
                        <input type="radio" name="avatar" value="2" <?= ($userData['avatar'] == 2) ? 'checked' : '' ?>>
                        <img src="<?= BASE_URL ?>/assets/img/av2.png" alt="Avatar Coelho com Laço" width="115">
                        </label>
                </fieldset>
                <button type="submit">Salvar Perfil</button>
            </form>
            <div>
                <a href="<?= BASE_URL ?>/logout">
                    <button type="button">sair da conta </button>
                </a>
            </div>
        </section>

        <section class="profile">
            <header>
                <h2>seu desempenho 📊</h2>
            </header>
            <div>
                <h3>acertos &#10004;</h3>
                <p><strong><?= $totalHits ?></strong></p>
            </div>
            <div>
                <h3>erros &#10006;</h3>
                <p><strong><?= $totalErrors ?></strong></p>
            </div>
        </section>

    </main>

<?php include __DIR__ . '/../footer.php'?>