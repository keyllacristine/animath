    <div>
        <a href="<?= BASE_URL ?>/home">
            <button type="button">&hookleftarrow; Voltar</button>
        </a>
    </div>

    <br>

    <main>
        
        <section>
            <header>
                <h2>seu perfil 👤</h2>
            </header>

            <form action="<?= BASE_URL ?>/profile" method="POST">
                
                <div>
                    <label for="username">nome:</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($userData['username'] ?? $_SESSION['username']) ?>" required>
                    <button type="submit" title="Salvar alterações">✏️</button>
                </div>

                <br>

                <fieldset>
                    <legend>Avatar</legend>

                    <label>
                        <input type="radio" name="avatar" value="1" <?= ($userData['avatar'] == 1) ? 'checked' : '' ?>>
                        <img src="<?= BASE_URL ?>/assets/images/avatar1.png" alt="Avatar Coelho 1" width="80">
                    </label>

                    <label>
                        <input type="radio" name="avatar" value="2" <?= ($userData['avatar'] == 2) ? 'checked' : '' ?>>
                        <img src="<?= BASE_URL ?>/assets/images/avatar2.png" alt="Avatar Coelho 2" width="80">
                    </label>
                </fieldset>

                <br>

                <button type="submit">Salvar Perfil</button>
            </form>

            <br><br>

            <div>
                <a href="<?= BASE_URL ?>/logout">
                    <button type="button">sair da conta 🚪</button>
                </a>
            </div>
        </section>

        <br><br>

        <section>
            <header>
                <h2>seu desempenho 📊</h2>
            </header>

            <div>
                <h3>acertos &#10004;</h3>
                <p><strong><?= $totalHits ?></strong></p>
            </div>

            <br>

            <div>
                <h3>erros &#10006;</h3>
                <p><strong><?= $totalErrors ?></strong></p>
            </div>
        </section>

    </main>

</body>
</html>