USE animath;

-- Cada linha = uma cena de visual novel (ex.: introdução do nível 1, dica de adição, etc.)
-- O conteúdo (falas, fundos, personagens) fica em JSON na coluna `data`,
-- então dá pra mudar tudo sem alterar o banco.
CREATE TABLE IF NOT EXISTS vn_scenes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    slug VARCHAR(80) NOT NULL UNIQUE,
    level_id INT NULL,
    title VARCHAR(120) NOT NULL DEFAULT '',
    data LONGTEXT NOT NULL,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Cena de exemplo (usa as imagens de teste de public/assets/img/vn/)
INSERT IGNORE INTO vn_scenes (slug, level_id, title, data) VALUES (
    'level_1_intro',
    1,
    'Introducao - Adicao',
    '{"title":"Introdução – Adição","lines":[
        {"id":"l1","speaker":"Mel","text":"AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA","bg":"login.png","chars":[{"id":"mel","src":"av1.png","pos":"center","show":true}]},
        {"id":"l2", "speaker":"tuntuntun","text":"cuidado","chars":[{"id":mel", "src":"tun.png","pos":"center","show":"true"}]},
        {"id":"l3","speaker":"Mel","text":"Somar é juntar coisas. Se eu tenho 2 maçãs e ganho mais 3, fico com quantas?"},
        {"id":"l5","speaker":"Bia","text":"Cinco! 2 + 3 = 5!","chars":[{"id":"mel","pos":"left"},{"id":"bia","src":"av2.png","pos":"right","show":true}]},
        {"id":"l6","speaker":"Mel","text":"Isso! Vamos praticar?","bg":"logado.png","chars":[{"id":"bia","show":false}]}
    ]}'
);

-- Exemplo de cena para ser "inserida" no meio do jogo (ex.: dica após errar)
INSERT IGNORE INTO vn_scenes (slug, level_id, title, data) VALUES (
    'dica_adicao_1',
    1,
    'Dica de adicao',
    '{"title":"Dica","lines":[
        {"id":"d1","speaker":"Mel","text":"Calma! Tente contar nos dedos: comece pelo maior número e some o outro.","bg":"login.png","chars":[{"id":"mel","src":"av1.png","pos":"center","show":true}]},
        {"id":"d2","speaker":"Mel","text":"Você consegue! Vamos tentar de novo?"}
    ]}'
);

select * from vn_scenes;