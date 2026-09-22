    <div>
        <a href="<?= BASE_URL ?>/home">
            <button type="button">&hookleftarrow; Voltar</button>
        </a>
    </div>

    <br>

    <main>
        <section>
            <h2>configurações de som</h2>

            <div>
                <label for="bg-music">🎵 Música:</label>
                <input type="range" id="bg-music" name="bg_music" min="0" max="100" value="50">
            </div>

            <br>

            <div>
                <label for="sound-fx">🔊 Efeitos:</label>
                <input type="range" id="sound-fx" name="sound_fx" min="0" max="100" value="50">
            </div>
        </section>

        <br><br>

        <div>
            <a href="<?= BASE_URL ?>/developers">
                <button type="button">desenvolvedores &#9432;</button>
            </a>
        </div>
    </main>

</body>
</html>