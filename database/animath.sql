drop database if exists animath;
CREATE DATABASE animath
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE animath;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    avatar TINYINT NOT NULL DEFAULT 1,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user'
);

CREATE TABLE levels (
    id INT AUTO_INCREMENT PRIMARY KEY
);

CREATE TABLE user_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    level_id INT NOT NULL,
    completed BOOLEAN NOT NULL DEFAULT FALSE,
    hits INT NOT NULL DEFAULT 0,
    erros INT NOT NULL DEFAULT 0,

    UNIQUE (user_id, level_id),

    FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    FOREIGN KEY (level_id)
        REFERENCES levels(id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS vn_scenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(80) NOT NULL UNIQUE,
    level_id INT NULL,
    title VARCHAR(120) NOT NULL DEFAULT '',
    data LONGTEXT NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO levels (id) VALUES
(1),
(2),
(3),
(4);

-- ============================================================
-- intro  (Introdução — Uma pausa nos estudos)
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('intro', 1, 'Introdução — Uma pausa nos estudos', '{"title": "Introdução — Uma pausa nos estudos", "lines": [
    {"id": "intro_1", "speaker": "", "text": "Você está sentad{o|a} à mesa, fazendo a lição de casa. Há um caderno aberto com algumas contas de matemática. Você tenta resolver um exercício, mas não consegue chegar a uma resposta.", "bg": "quarto_pov.png"},
    {"id": "intro_2", "speaker": "{nome}", "text": "Ahhh... eu não consigo entender essas contas!"},
    {"id": "intro_3", "speaker": "{nome}", "text": "Já tentei fazer de vários jeitos, mas continuo sem saber o que fazer..."},
    {"id": "intro_4", "speaker": "", "text": "Você olha para a porta do quarto.", "bg": "quarto_porta.png"},
    {"id": "intro_5", "speaker": "{nome}", "text": "Mãe? Você pode me ajudar com a lição?"},
    {"id": "intro_6", "speaker": "", "text": "A mãe está ocupada com suas tarefas domésticas.", "bg": "cozinha.png"},
    {"id": "intro_7", "speaker": "Mãe", "text": "Oi, meu bem! Aconteceu alguma coisa?", "chars": [{"id": "mae", "src": "mae_happy.png", "pos": "center", "show": true}]},
    {"id": "intro_8", "speaker": "{nome}", "text": "Eu estou tentando fazer a lição de matemática, mas tem umas contas que eu não consigo resolver."},
    {"id": "intro_9", "speaker": "Mãe", "text": "Entendi, quer{ido|ida}. Agora estou ocupada e não consigo parar para te ajudar.", "chars": [{"id": "mae", "src": "mae_neutral.png"}]},
    {"id": "intro_10", "speaker": "{nome}", "text": "Ah... tudo bem."},
    {"id": "intro_11", "speaker": "Mãe", "text": "Que tal ir brincar um pouquinho no quintal? Às vezes, fazer uma pausa ajuda a gente a organizar os pensamentos.", "chars": [{"id": "mae", "src": "mae_encouraging.png"}]},
    {"id": "intro_12", "speaker": "Mãe", "text": "Depois você pode voltar à lição com a cabeça mais tranquila. Não precisa desistir só porque está difícil!", "chars": [{"id": "mae", "src": "mae_encouraging.png"}]},
    {"id": "intro_13", "speaker": "{nome}", "text": "É... acho que vou fazer isso. {Obrigado|Obrigada}, mãe!"},
    {"id": "intro_14", "speaker": "", "text": "Você sai de casa e vai para o quintal.", "bg": "quintal.png", "chars": [{"id": "mae", "show": false}]},
    {"id": "intro_15", "speaker": "Luke", "text": "{nome}! Oie!", "chars": [{"id": "luke", "src": "luke_happy.png", "pos": "center", "show": true}]},
    {"id": "intro_16", "speaker": "{nome}", "text": "Oi, Luke! Eu estava tentando fazer minha lição de matemática, mas não consegui resolver algumas contas."},
    {"id": "intro_17", "speaker": "Luke", "text": "Ah, sério? Matemática pode parecer complicada quando a gente ainda não entendeu como alguma coisa funciona.", "chars": [{"id": "luke", "src": "luke_neutral.png"}]},
    {"id": "intro_18", "speaker": "{nome}", "text": "Sim… Eu olho para as contas e nem sei por onde começar."},
    {"id": "intro_19", "speaker": "Luke", "text": "Mas… eu sei adição!", "chars": [{"id": "luke", "src": "luke_excited.png"}]},
    {"id": "intro_20", "speaker": "{nome}", "text": "Adição? Acho que eu preciso aprender isso…"},
    {"id": "intro_21", "speaker": "Luke", "text": "Eu posso te explicar!", "chars": [{"id": "luke", "src": "luke_happy.png"}]},
    {"id": "intro_22", "speaker": "Luke", "text": "Eu aprendi de um jeito bem fácil. Como com flores.", "chars": [{"id": "luke", "src": "luke_neutral.png"}]},
    {"id": "intro_23", "speaker": "", "text": "Você olha para as flores do quintal."},
    {"id": "intro_24", "speaker": "{nome}", "text": "Hmmm...."},
    {"id": "intro_25", "speaker": "Luke", "text": "Então vem comigo! Vamos descobrir juntos!", "chars": [{"id": "luke", "src": "luke_happy.png"}]}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);

-- ============================================================
-- level_1_intro  (Nível 1 — Adição)
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('level_1_intro', 1, 'Nível 1 — Adição', '{"title": "Nível 1 — Adição", "lines": [
    {"id": "level_1_intr_1", "speaker": "Luke", "text": "Primeiro, vamos imaginar que temos duas flores aqui no quintal.", "bg": "quintal.png", "chars": [{"id": "luke", "src": "luke_teaching.png", "pos": "center", "show": true}], "popup": {"type": "items", "op": "+", "src": "flor.png", "groups": [2, 3], "step": 1}},
    {"id": "level_1_intr_2", "speaker": "Luke", "text": "Agora, imagine que eu plante mais três flores ao lado delas.", "chars": [{"id": "luke", "src": "luke_neutral.png"}], "popup": {"step": 2}},
    {"id": "level_1_intr_3", "speaker": "{nome}", "text": "Então temos cinco flores!"},
    {"id": "level_1_intr_4", "speaker": "Luke", "text": "Isso mesmo! Você juntou duas flores com outras três e descobriu quantas temos ao todo.", "chars": [{"id": "luke", "src": "luke_neutral.png"}], "popup": {"step": 3}},
    {"id": "level_1_intr_5", "speaker": "Luke", "text": "É isso que a adição faz: ela junta quantidades para descobrir o total.", "chars": [{"id": "luke", "src": "luke_neutral.png"}]},
    {"id": "level_1_intr_6", "speaker": "{nome}", "text": "Então adicionar é juntar coisas?"},
    {"id": "level_1_intr_7", "speaker": "Luke", "text": "Exatamente! O sinal de adição é este aqui: +.", "chars": [{"id": "luke", "src": "luke_happy.png"}], "popup": {"type": "equation", "text": "2 + 3 = 5", "mark": "+", "caption": "sinal de adição"}},
    {"id": "level_1_intr_8", "speaker": "Luke", "text": "Quando vemos uma conta como 2 + 3, podemos pensar em duas flores mais três flores.", "chars": [{"id": "luke", "src": "luke_teaching.png"}], "popup": {"type": "items", "op": "+", "src": "flor.png", "groups": [2, 3], "step": 3}},
    {"id": "level_1_intr_9", "speaker": "{nome}", "text": "E o resultado é 5!"},
    {"id": "level_1_intr_10", "speaker": "Luke", "text": "Isso! E o sinal de igual, =, mostra que o número que vem depois é o resultado da conta.", "chars": [{"id": "luke", "src": "luke_neutral.png"}], "popup": {"type": "equation", "text": "2 + 3 = 5", "mark": "=", "caption": "sinal de igual"}},
    {"id": "level_1_intr_11", "speaker": "{nome}", "text": "Acho que estou começando a entender!"},
    {"id": "level_1_intr_12", "speaker": "Luke", "text": "Agora vamos praticar com algumas continhas. Eu digo uma conta e você me diz o resultado, tá bom?", "chars": [{"id": "luke", "src": "luke_neutral.png"}], "popup": false}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);

-- ============================================================
-- level_1_outro  (Nível 1 — Uma nova dúvida)
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('level_1_outro', 1, 'Nível 1 — Uma nova dúvida', '{"title": "Nível 1 — Uma nova dúvida", "lines": [
    {"id": "level_1_outr_1", "speaker": "", "text": "Depois de terminar a atividade, você olha para Luke.", "bg": "quintal.png"},
    {"id": "level_1_outr_2", "speaker": "{nome}", "text": "Luke, acho que entendi a adição! Mas e quando a gente precisa tirar uma quantidade de outra?", "chars": [{"id": "luke", "src": "luke_neutral.png", "pos": "center", "show": true}]},
    {"id": "level_1_outr_3", "speaker": "{nome}", "text": "Por exemplo, se algumas flores forem retiradas do jardim, como descubro quantas sobraram?"},
    {"id": "level_1_outr_4", "speaker": "Luke", "text": "Hmmm... boa pergunta!", "chars": [{"id": "luke", "src": "luke_confused.png"}]},
    {"id": "level_1_outr_5", "speaker": "{nome}", "text": "Você não sabe?"},
    {"id": "level_1_outr_6", "speaker": "Luke", "text": "Ah, eu… esqueci.", "chars": [{"id": "luke", "src": "luke_awkward.png"}]},
    {"id": "level_1_outr_7", "speaker": "", "text": "Você e Luke ficam em silêncio, se olhando.", "chars": [{"id": "luke", "src": "luke_deadpan.png"}]},
    {"id": "level_1_outr_8", "speaker": "Luke", "text": "Maaas… A Luna se lembra! Ela está no parque e entende bastante disso.", "chars": [{"id": "luke", "src": "luke_neutral.png"}]},
    {"id": "level_1_outr_9", "speaker": "{nome}", "text": "Então vamos até lá!"},
    {"id": "level_1_outr_10", "speaker": "Luke", "text": "Vamos! Tenho certeza de que ela pode nos ensinar.", "chars": [{"id": "luke", "src": "luke_happy.png"}]}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);

-- ============================================================
-- level_2_intro  (Nível 2 — Subtração)
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('level_2_intro', 2, 'Nível 2 — Subtração', '{"title": "Nível 2 — Subtração", "lines": [
    {"id": "level_2_intr_1", "speaker": "", "text": "Você e Luke chegam ao parque e encontram Luna.", "bg": "parque.png"},
    {"id": "level_2_intr_2", "speaker": "Luke", "text": "Luna!", "chars": [{"id": "luke", "src": "luke_neutral.png", "pos": "mid-left", "show": true}]},
    {"id": "level_2_intr_3", "speaker": "Luna", "text": "!", "chars": [{"id": "luna", "src": "luna_neutral.png", "pos": "mid-right", "show": true}]},
    {"id": "level_2_intr_4", "speaker": "Luna", "text": "Ai, oi, Luke. Oi, {nome}.", "chars": [{"id": "luna", "src": "luna_neutral.png"}]},
    {"id": "level_2_intr_5", "speaker": "{nome}", "text": "O Luke me ensinou adição usando as flores do quintal, mas agora quero aprender subtração."},
    {"id": "level_2_intr_6", "speaker": "Luke", "text": "Eu também preciso de uma ajudinha com essa parte.", "chars": [{"id": "luke", "src": "luke_awkward.png"}]},
    {"id": "level_2_intr_7", "speaker": "Luna", "text": "Hm.", "chars": [{"id": "luna", "src": "luna_unimpressed.png"}]},
    {"id": "level_2_intr_8", "speaker": "Luna", "text": "Tá, eu ensino vocês.", "chars": [{"id": "luna", "src": "luna_neutral.png"}]},
    {"id": "level_2_intr_9", "speaker": "Luna", "text": "Imaginem que temos cinco flores aqui.", "part": 2, "chars": [{"id": "luna", "src": "luna_teaching.png"}], "popup": {"type": "items", "op": "-", "src": "flor.png", "total": 5, "remove": 2, "step": 1}},
    {"id": "level_2_intr_10", "speaker": "Luna", "text": "Se retirarmos duas delas, quantas flores vão continuar aqui?", "chars": [{"id": "luna", "src": "luna_teaching.png"}], "popup": {"step": 2}},
    {"id": "level_2_intr_11", "speaker": "{nome}", "text": "Três flores!"},
    {"id": "level_2_intr_12", "speaker": "Luna", "text": "Isso. Nós começamos com cinco e retiramos duas. Por isso, sobraram três.", "chars": [{"id": "luna", "src": "luna_teaching.png"}], "popup": {"step": 3}},
    {"id": "level_2_intr_13", "speaker": "Luke", "text": "Então a subtração serve para descobrir quanto sobra quando tiramos uma quantidade de outra?", "chars": [{"id": "luke", "src": "luke_thinking.png"}]},
    {"id": "level_2_intr_14", "speaker": "Luna", "text": "Sim. Também podemos usá-la para descobrir a diferença entre duas quantidades.", "chars": [{"id": "luna", "src": "luna_teaching.png"}]},
    {"id": "level_2_intr_15", "speaker": "{nome}", "text": "E qual é o sinal da subtração?"},
    {"id": "level_2_intr_16", "speaker": "Luna", "text": "É este aqui: −.", "chars": [{"id": "luna", "src": "luna_neutral.png"}], "popup": {"type": "equation", "text": "5 − 2 = 3", "mark": "−", "caption": "sinal de subtração"}},
    {"id": "level_2_intr_17", "speaker": "Luna", "text": "Por exemplo, 5 − 2 = 3. Começamos com cinco, retiramos duas e ficamos com três. É simples.", "chars": [{"id": "luna", "src": "luna_teaching.png"}], "popup": {"type": "items", "op": "-", "src": "flor.png", "total": 5, "remove": 2, "step": 3}},
    {"id": "level_2_intr_18", "speaker": "Luke", "text": "É mesmo!", "chars": [{"id": "luke", "src": "luke_neutral.png"}]},
    {"id": "level_2_intr_19", "speaker": "Luna", "text": "E mesmo assim você esqueceu!", "chars": [{"id": "luna", "src": "luna_teasing.png"}]},
    {"id": "level_2_intr_20", "speaker": "Luke", "text": ":(", "chars": [{"id": "luke", "src": "luke_sad.png"}]},
    {"id": "level_2_intr_21", "speaker": "{nome}", "text": "Podemos praticar um pouco?"},
    {"id": "level_2_intr_22", "speaker": "Luna", "text": "Claro! Quero ver vocês usando o que acabaram de aprender.", "chars": [{"id": "luna", "src": "luna_happy.png"}], "popup": false}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);

-- ============================================================
-- level_2_outro  (Nível 2 — Mais uma operação!)
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('level_2_outro', 2, 'Nível 2 — Mais uma operação!', '{"title": "Nível 2 — Mais uma operação!", "lines": [
    {"id": "level_2_outr_1", "speaker": "{nome}", "text": "Luna, agora eu entendi a subtração! Mas tem outra coisa que apareceu na minha lição...", "bg": "parque.png", "chars": [{"id": "luke", "src": "luke_neutral.png", "pos": "mid-left", "show": true}, {"id": "luna", "src": "luna_neutral.png", "pos": "mid-right", "show": true}]},
    {"id": "level_2_outr_2", "speaker": "{nome}", "text": "Como funciona a multiplicação?"},
    {"id": "level_2_outr_3", "speaker": "Luna", "text": "Multiplicação? Ah, eu até sei explicar, mas estou com tanto sono que….", "chars": [{"id": "luna", "src": "luna_sleepy.png"}]},
    {"id": "level_2_outr_4", "speaker": "Luna", "text": "ZZZZzzZzz…", "chars": [{"id": "luna", "src": "luna_sleepy.png"}]},
    {"id": "level_2_outr_5", "speaker": "Luke", "text": "…Ah.", "chars": [{"id": "luke", "src": "luke_deadpan.png"}]},
    {"id": "level_2_outr_6", "speaker": "Luna", "text": "O Tofu sabe, ele está na floresta… Eu vou… logo em seguida…", "chars": [{"id": "luna", "src": "luna_sleepy.png"}]},
    {"id": "level_2_outr_7", "speaker": "{nome}", "text": "Certo! Vamos procurar o Tofu, Luke."},
    {"id": "level_2_outr_8", "speaker": "Luna", "text": "Boa sorte, pessoal! E tomem cuidado para não se perderem pelo caminho!", "chars": [{"id": "luna", "src": "luna_sleepy.png"}]}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);

-- ============================================================
-- level_3_intro  (Nível 3 — Multiplicação)
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('level_3_intro', 3, 'Nível 3 — Multiplicação', '{"title": "Nível 3 — Multiplicação", "lines": [
    {"id": "level_3_intr_1", "speaker": "", "text": "Você e Luke chegam à floresta e encontram Tofu.", "bg": "floresta.png"},
    {"id": "level_3_intr_2", "speaker": "Tofu", "text": "…", "chars": [{"id": "luke", "src": "luke_neutral.png", "pos": "mid-left", "show": true}, {"id": "tofu", "src": "tofu_neutral.png", "pos": "mid-right", "show": true}]},
    {"id": "level_3_intr_3", "speaker": "Luke", "text": "Oi, Tofu!", "chars": [{"id": "luke", "src": "luke_neutral.png"}]},
    {"id": "level_3_intr_4", "speaker": "Tofu", "text": "Luke, {nome}, oi!!", "chars": [{"id": "tofu", "src": "tofu_happy.png"}]},
    {"id": "level_3_intr_5", "speaker": "{nome}", "text": "Viemos porque a Luna disse que você sabe multiplicação."},
    {"id": "level_3_intr_6", "speaker": "Tofu", "text": "Multiplicação? Eu… sei um pouco.", "chars": [{"id": "tofu", "src": "tofu_awkward.png"}]},
    {"id": "level_3_intr_7", "speaker": "Tofu", "text": "Mas ela também não sabe?", "chars": [{"id": "tofu", "src": "tofu_confused.png"}]},
    {"id": "level_3_intr_8", "speaker": "Luke", "text": "Sabe, mas ela já falou o suficiente comigo hoje.", "chars": [{"id": "luke", "src": "luke_deadpan.png"}]},
    {"id": "level_3_intr_9", "speaker": "{nome}", "text": "E… ela acabou dormindo logo depois de explicar subtração."},
    {"id": "level_3_intr_10", "speaker": "Tofu", "text": "Okay!", "chars": [{"id": "tofu", "src": "tofu_happy.png"}]},
    {"id": "level_3_intr_11", "speaker": "Tofu", "text": "Imaginem que temos três grupos de cogumelos.", "part": 2, "chars": [{"id": "tofu", "src": "tofu_teaching.png"}], "popup": {"type": "items", "op": "*", "src": "cogumelo.png", "times": 3, "per": 2, "step": 3}},
    {"id": "level_3_intr_12", "speaker": "Tofu", "text": "Cada grupo tem dois cogumelos. Em vez de contar tudo separadamente, podemos pensar em quantidades iguais.", "chars": [{"id": "tofu", "src": "tofu_teaching.png"}]},
    {"id": "level_3_intr_13", "speaker": "{nome}", "text": "Dois cogumelos no primeiro grupo, dois no segundo e dois no terceiro..."},
    {"id": "level_3_intr_14", "speaker": "Luke", "text": "Então temos seis cogumelos!", "chars": [{"id": "luke", "src": "luke_happy.png"}]},
    {"id": "level_3_intr_15", "speaker": "Tofu", "text": "Exatamente! Podemos escrever isso como 2 + 2 + 2 = 6.", "chars": [{"id": "tofu", "src": "tofu_teaching.png"}], "popup": {"step": 4}},
    {"id": "level_3_intr_16", "speaker": "Tofu", "text": "Mas também podemos usar a multiplicação: 3 × 2 = 6.", "chars": [{"id": "tofu", "src": "tofu_teaching.png"}], "popup": {"step": 5}},
    {"id": "level_3_intr_17", "speaker": "{nome}", "text": "Então a multiplicação pode representar a soma de grupos com a mesma quantidade?"},
    {"id": "level_3_intr_18", "speaker": "Tofu", "text": "Isso mesmo! Ela nos ajuda a descobrir o total de vários grupos iguais.", "chars": [{"id": "tofu", "src": "tofu_teaching.png"}]},
    {"id": "level_3_intr_19", "speaker": "Tofu", "text": "E o sinal da multiplicação é este: ×.", "chars": [{"id": "tofu", "src": "tofu_teaching.png"}], "popup": {"type": "equation", "text": "3 × 2 = 6", "mark": "×", "caption": "sinal da multiplicação"}},
    {"id": "level_3_intr_20", "speaker": "Tofu", "text": "Muito bem! Por exemplo, 4 × 2 significa quatro grupos de dois, que totalizam oito.", "chars": [{"id": "tofu", "src": "tofu_teaching.png"}], "popup": {"type": "items", "op": "*", "src": "cogumelo.png", "times": 4, "per": 2, "step": 6}},
    {"id": "level_3_intr_21", "speaker": "{nome}", "text": "Agora entendi! Multiplicar é uma maneira prática de somar quantidades iguais!"},
    {"id": "level_3_intr_22", "speaker": "Tofu", "text": "Sim! Perfeitamente.", "chars": [{"id": "tofu", "src": "tofu_happy.png"}], "popup": false}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);

-- ============================================================
-- level_3_outro  (Nível 3 — A última operação)
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('level_3_outro', 3, 'Nível 3 — A última operação', '{"title": "Nível 3 — A última operação", "lines": [
    {"id": "level_3_outr_1", "speaker": "{nome}", "text": "Tofu, acho que entendi a multiplicação!", "bg": "floresta.png", "chars": [{"id": "luke", "src": "luke_neutral.png", "pos": "mid-left", "show": true}, {"id": "tofu", "src": "tofu_neutral.png", "pos": "mid-right", "show": true}]},
    {"id": "level_3_outr_2", "speaker": "{nome}", "text": "Mas ainda falta uma coisa na minha lição: a divisão."},
    {"id": "level_3_outr_3", "speaker": "Tofu", "text": "Ah, a divisão! Conheço alguém que entende muito disso.", "chars": [{"id": "tofu", "src": "tofu_neutral.png"}]},
    {"id": "level_3_outr_4", "speaker": "Luke", "text": "Quem?", "chars": [{"id": "luke", "src": "luke_confused.png"}]},
    {"id": "level_3_outr_5", "speaker": "", "text": "Luna aparece de repente.", "chars": [{"id": "luke", "pos": "left"}, {"id": "tofu", "pos": "center"}, {"id": "luna", "pos": "right"}]},
    {"id": "level_3_outr_6", "speaker": "Luna", "text": "A Mia.", "chars": [{"id": "luke", "src": "luke_scared.png"}, {"id": "tofu", "src": "tofu_scared.png"}, {"id": "luna", "src": "luna_neutral.png", "pos": "right", "show": true}]},
    {"id": "level_3_outr_7", "speaker": "Tofu e Luke", "text": "AAAHH!!"},
    {"id": "level_3_outr_8", "speaker": "{nome}", "text": "Oi, Luna."},
    {"id": "level_3_outr_9", "speaker": "Luna", "text": "Oi. Eu disse que ia alcançar vocês.", "chars": [{"id": "luna", "src": "luna_neutral.png"}]},
    {"id": "level_3_outr_10", "speaker": "Luke", "text": "Disse?", "chars": [{"id": "luke", "src": "luke_confused.png"}]},
    {"id": "level_3_outr_11", "speaker": "Tofu", "text": "A Mia está no lago, vamos lá. Também preciso revisar a divisão…", "chars": [{"id": "tofu", "src": "tofu_neutral.png"}]}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);

-- ============================================================
-- level_4_intro  (Nível 4 — Divisão)
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('level_4_intro', 4, 'Nível 4 — Divisão', '{"title": "Nível 4 — Divisão", "lines": [
    {"id": "level_4_intr_1", "speaker": "Mia", "text": "Ah, oi gente!! Que bom ter vocês aqui.", "bg": "lago.png", "chars": [{"id": "luke", "src": "luke_neutral.png", "pos": "far-left", "show": true}, {"id": "tofu", "src": "tofu_neutral.png", "pos": "right", "show": true}, {"id": "mia", "src": "mia_happy.png", "pos": "mid-left", "show": true}]},
    {"id": "level_4_intr_2", "speaker": "Tofu", "text": "Mia, oi! Você é boa em divisão, não é?", "chars": [{"id": "tofu", "src": "tofu_neutral.png"}]},
    {"id": "level_4_intr_3", "speaker": "Mia", "text": "Si-", "chars": [{"id": "mia", "src": "mia_happy.png"}]},
    {"id": "level_4_intr_4", "speaker": "Luke", "text": "Já aprendemos adição, subtração e multiplicação.", "chars": [{"id": "luke", "src": "luke_neutral.png"}]},
    {"id": "level_4_intr_5", "speaker": "Mia", "text": "Sério?", "chars": [{"id": "mia", "src": "mia_surprised.png"}]},
    {"id": "level_4_intr_6", "speaker": "{nome}", "text": "Agora só falta entender a divisão."},
    {"id": "level_4_intr_7", "speaker": "Mia", "text": "Ok, ok…! Claro!", "chars": [{"id": "mia", "src": "mia_neutral.png"}]},
    {"id": "level_4_intr_8", "speaker": "Mia", "text": "Olhem ali no lago. Temos seis peixes e queremos distribuí-los igualmente entre três.", "part": 2, "chars": [{"id": "mia", "src": "mia_teaching.png"}], "popup": {"type": "items", "op": "/", "src": "peixe.png", "total": 6, "parts": 3, "step": 2}},
    {"id": "level_4_intr_9", "speaker": "{nome}", "text": "Então cada espaço precisa receber a mesma quantidade de peixes?"},
    {"id": "level_4_intr_10", "speaker": "Mia", "text": "Exatamente! Na divisão, podemos repartir uma quantidade em partes iguais.", "chars": [{"id": "mia", "src": "mia_teaching.png"}]},
    {"id": "level_4_intr_11", "speaker": "Mia", "text": "Vamos distribuir peixes para cada pessoa e continuar até ficar igual.", "chars": [{"id": "mia", "src": "mia_neutral.png"}]},
    {"id": "level_4_intr_12", "speaker": "", "text": "Os peixes se dispersam.", "popup": {"step": 3}},
    {"id": "level_4_intr_13", "speaker": "{nome}", "text": "Cada lugar ficou com dois peixes!"},
    {"id": "level_4_intr_14", "speaker": "Mia", "text": "Isso mesmo! Começamos com seis peixes e dividimos igualmente entre três pessoas.", "chars": [{"id": "mia", "src": "mia_neutral.png"}]},
    {"id": "level_4_intr_15", "speaker": "Mia", "text": "Podemos escrever assim: 6 ÷ 3 = 2.", "chars": [{"id": "mia", "src": "mia_teaching.png"}], "popup": {"step": 4}},
    {"id": "level_4_intr_16", "speaker": "Luke", "text": "Então cada lugar recebeu dois peixes!", "chars": [{"id": "luke", "src": "luke_neutral.png"}]},
    {"id": "level_4_intr_17", "speaker": "Tofu", "text": "E o sinal da divisão é este: ÷.", "chars": [{"id": "tofu", "src": "tofu_neutral.png"}], "popup": {"type": "equation", "text": "6 ÷ 3 = 2", "mark": "÷", "caption": "sinal da divisão"}},
    {"id": "level_4_intr_18", "speaker": "{nome}", "text": "Entendi! A divisão pode servir para repartir uma quantidade igualmente!"},
    {"id": "level_4_intr_19", "speaker": "Mia", "text": "Perfeitamente! E também podemos usá-la para descobrir quantos grupos de determinado tamanho cabem em uma quantidade.", "chars": [{"id": "mia", "src": "mia_teaching.png"}]},
    {"id": "level_4_intr_20", "speaker": "Mia", "text": "Agora vamos praticar para ter certeza de que você entendeu.", "chars": [{"id": "mia", "src": "mia_teaching.png"}], "popup": false}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);

-- ============================================================
-- game_final  (Cena final — Tudo o que aprendemos)
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('game_final', NULL, 'Cena final — Tudo o que aprendemos', '{"title": "Cena final — Tudo o que aprendemos", "lines": [
    {"id": "game_final_1", "speaker": "", "text": "Após concluir a última atividade, os quatro personagens se reúnem perto do lago.", "bg": "lago.png"},
    {"id": "game_final_2", "speaker": "Mia", "text": "Nossa… você aprendeu bastante durante nossa jornada. Que tudo!", "chars": [{"id": "luke", "src": "luke_neutral.png", "pos": "far-left", "show": true}, {"id": "luna", "src": "luna_happy.png", "pos": "mid-left", "show": true}, {"id": "tofu", "src": "tofu_neutral.png", "pos": "mid-right", "show": true}, {"id": "mia", "src": "mia_proud.png", "pos": "far-right", "show": true}]},
    {"id": "game_final_3", "speaker": "{nome}", "text": "Aprendi mesmo! No começo, eu nem sabia como resolver as contas da minha lição…"},
    {"id": "game_final_4", "speaker": "Luke", "text": "E agora você já conhece a adição!", "chars": [{"id": "luke", "src": "luke_happy.png"}]},
    {"id": "game_final_5", "speaker": "{nome}", "text": "Na adição, juntamos quantidades para descobrir o total."},
    {"id": "game_final_6", "speaker": "Luna", "text": "E também aprendeu subtração.", "chars": [{"id": "luna", "src": "luna_teaching.png"}]},
    {"id": "game_final_7", "speaker": "{nome}", "text": "Na subtração, podemos retirar uma quantidade de outra para descobrir quanto sobra ou a diferença entre elas."},
    {"id": "game_final_8", "speaker": "Tofu", "text": "Não podemos esquecer da multiplicação…", "chars": [{"id": "tofu", "src": "tofu_neutral.png"}]},
    {"id": "game_final_9", "speaker": "{nome}", "text": "A multiplicação pode representar a soma de grupos com quantidades iguais."},
    {"id": "game_final_10", "speaker": "Mia", "text": "E, finalmente, a divisão, né?", "chars": [{"id": "mia", "src": "mia_teaching.png"}]},
    {"id": "game_final_11", "speaker": "{nome}", "text": "A divisão pode repartir uma quantidade em partes iguais ou ajudar a descobrir quantos grupos cabem nela."},
    {"id": "game_final_12", "speaker": "Luke", "text": "Você se lembra de como estava se sentindo antes de vir para o quintal?", "chars": [{"id": "luke", "src": "luke_neutral.png"}]},
    {"id": "game_final_13", "speaker": "{nome}", "text": "Eu estava frustrad{o|a} porque não conseguia resolver a lição de casa."},
    {"id": "game_final_14", "speaker": "Luna", "text": "Eu também estaria.", "chars": [{"id": "luna", "src": "luna_teaching.png"}]},
    {"id": "game_final_15", "speaker": "Tofu", "text": "Mas você pediu ajuda e conseguiu entender tudo.", "chars": [{"id": "tofu", "src": "tofu_neutral.png"}]},
    {"id": "game_final_16", "speaker": "Mia", "text": "Verdade, Tofu.", "chars": [{"id": "mia", "src": "mia_happy.png"}]},
    {"id": "game_final_17", "speaker": "Tofu", "text": "… (sorrindo)", "chars": [{"id": "tofu", "src": "tofu_happy.png"}]},
    {"id": "game_final_18", "speaker": "Mia", "text": "Você arrasou, {nome}!", "chars": [{"id": "mia", "src": "mia_excited.png"}]},
    {"id": "game_final_19", "speaker": "{nome}", "text": "Agora me sinto muito mais prepar{ado|ada} para voltar à minha lição… Está ficando tarde."},
    {"id": "game_final_20", "speaker": "Luke", "text": "Então vamos logo, antes que escureça!", "chars": [{"id": "luke", "src": "luke_happy.png"}]},
    {"id": "game_final_21", "speaker": "{nome}", "text": "Sim… obrigad{o|a}, pessoal!"},
    {"id": "game_final_22", "speaker": "Todos", "text": "De nada!!"},
    {"id": "game_final_23", "speaker": "", "text": "Os personagens se despedem enquanto você se prepara para voltar para casa.", "bg": "ilustracao_final_{avatar}.png", "chars": [{"id": "luke", "show": false}, {"id": "luna", "show": false}, {"id": "tofu", "show": false}, {"id": "mia", "show": false}]}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);

-- ============================================================
-- armacao_adicao
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('armacao_adicao', NULL, 'Armação — adição', '{"title": "Armação — adição", "lines": [
    {"id": "armacao_ad_1", "speaker": "Luke", "text": "Quando os números ficam grandes, a gente arma a conta: um embaixo do outro, unidade com unidade e dezena com dezena.", "bg": "quintal.png", "chars": [{"id": "luke", "src": "luke_teaching.png", "pos": "center", "show": true}], "popup": {"type": "column", "op": "+", "a": 47, "b": 38, "step": 0, "title": "armação"}},
    {"id": "armacao_ad_2", "speaker": "Luke", "text": "Começamos pela direita, pelas unidades: 7 + 8 = 15.", "popup": {"step": 1}},
    {"id": "armacao_ad_3", "speaker": "Luke", "text": "O 15 tem dois algarismos. Escrevemos o 5 embaixo e o outro 1 \\"vai\\" para a próxima coluna.", "popup": {"step": 1}},
    {"id": "armacao_ad_4", "speaker": "Luke", "text": "Agora as dezenas: 4 + 3, mais o 1 que veio, dá 8.", "popup": {"step": 2}},
    {"id": "armacao_ad_5", "speaker": "Luke", "text": "Pronto! 47 + 38 = 85.", "popup": {"step": 2}}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);


INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('armacao_subtracao', NULL, 'Armação — subtração', '{"title": "Armação — subtração", "lines": [
    {"id": "armacao_su_1", "speaker": "Luna", "text": "Na subtração também armamos a conta. Quem tem que ficar em cima é o número maior.", "bg": "parque.png", "chars": [{"id": "luna", "src": "luna_teaching.png", "pos": "center", "show": true}], "popup": {"type": "column", "op": "-", "a": 52, "b": 27, "step": 0, "title": "armação"}},
    {"id": "armacao_su_2", "speaker": "Luna", "text": "Unidades: 2 − 7 não dá. Então \\"emprestamos\\" uma dezena: o 5 vira 4 e o 2 vira 12.", "popup": {"step": 1}},
    {"id": "armacao_su_3", "speaker": "Luna", "text": "Agora dá: 12 − 7 = 5.", "popup": {"step": 1}},
    {"id": "armacao_su_4", "speaker": "Luna", "text": "Dezenas: 4 − 2 = 2.", "popup": {"step": 2}},
    {"id": "armacao_su_5", "speaker": "Luna", "text": "Então 52 − 27 = 25.", "popup": {"step": 2}}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);


INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('armacao_multiplicacao', NULL, 'Armação — multiplicação', '{"title": "Armação — multiplicação", "lines": [
    {"id": "armacao_mu_1", "speaker": "Tofu", "text": "Vamos armar 24 × 3. O número de baixo multiplica cada algarismo de cima, começando pela direita.", "bg": "floresta.png", "chars": [{"id": "tofu", "src": "tofu_teaching.png", "pos": "center", "show": true}], "popup": {"type": "column", "op": "*", "a": 24, "b": 3, "step": 0, "title": "armação"}},
    {"id": "armacao_mu_2", "speaker": "Tofu", "text": "3 × 4 = 12. Escrevemos o 2 e o 1 \\"vai\\" para a próxima coluna.", "popup": {"step": 1}},
    {"id": "armacao_mu_3", "speaker": "Tofu", "text": "3 × 2 = 6, mais o 1 que veio, dá 7.", "popup": {"step": 2}},
    {"id": "armacao_mu_4", "speaker": "Tofu", "text": "Então 24 × 3 = 72.", "popup": {"step": 2}}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);


INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('armacao_divisao', NULL, 'Armação — divisão', '{"title": "Armação — divisão", "lines": [
    {"id": "armacao_di_1", "speaker": "Mia", "text": "Na divisão usamos a chave: o 78 fica dentro e o 3 do lado de fora.", "bg": "lago.png", "chars": [{"id": "mia", "src": "mia_teaching.png", "pos": "center", "show": true}], "popup": {"type": "column", "op": "/", "a": 78, "b": 3, "step": 0, "title": "armação"}},
    {"id": "armacao_di_2", "speaker": "Mia", "text": "Quantas vezes o 3 cabe no 7? Duas vezes, porque 2 × 3 = 6. Escrevemos o 2 no resultado.", "popup": {"step": 1}},
    {"id": "armacao_di_3", "speaker": "Mia", "text": "7 − 6 = 1. Agora \\"descemos\\" o 8 e ficamos com 18.", "popup": {"step": 1}},
    {"id": "armacao_di_4", "speaker": "Mia", "text": "Quantas vezes o 3 cabe no 18? Seis vezes: 6 × 3 = 18, e não sobra nada.", "popup": {"step": 2}},
    {"id": "armacao_di_5", "speaker": "Mia", "text": "Então 78 ÷ 3 = 26.", "popup": {"step": 2}}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);

select * from users;

update users set role = "admin" where id = 1;