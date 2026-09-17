<?php
 include_once __DIR__ . "/header.php";
?>
<body>
  <div class="screen">
    <div class="grid">

      <div class="panel">
        <h1>vamos criar<br>sua conta!</h1>
        <p class="hint">caso precise, peça ajuda para um adulto</p>

        <div class="field">
          <label for="nome">seu nome</label>
          <input id="nome" type="text" placeholder="">
        </div>
        <div class="field">
          <label for="senha">sua senha</label>
          <input id="senha" type="password" placeholder="">
        </div>
      </div>

      <div class="panel panel--right">
        <h2>escolha um avatar</h2>
        <div class="avatars">
          <button class="avatar" aria-label="Avatar 1">
            <div class="critter">
                <img src="../ASSETS/CHARACTERS/BOY/ele.png" width=100>
            </div>
          </button>
          <button class="avatar" aria-label="Avatar 2">
            <div class="critter">
                <img src="../ASSETS/CHARACTERS/GIRL/la.png" width=100>
            </div>
          </button>
        </div>

        <button class="am-btn">
          Vamos aprender!
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </button>
      </div>

    </div>
  </div>
</body>
</html>