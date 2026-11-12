USE animath;

-- Exemplos de ARMAÇÃO DE CONTAS (pop-up coluna a coluna). Não entram no fluxo dos níveis:
-- chame quando quiser, em qualquer lugar:  vn.insertScene('armacao_adicao')   (ou _subtracao, _multiplicacao, _divisao)
-- Pode rodar de novo: ON DUPLICATE KEY UPDATE.

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

-- ============================================================
-- armacao_subtracao
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('armacao_subtracao', NULL, 'Armação — subtração', '{"title": "Armação — subtração", "lines": [
    {"id": "armacao_su_1", "speaker": "Luna", "text": "Na subtração também armamos a conta. Quem tem que ficar em cima é o número maior.", "bg": "parque.png", "chars": [{"id": "luna", "src": "luna_teaching.png", "pos": "center", "show": true}], "popup": {"type": "column", "op": "-", "a": 52, "b": 27, "step": 0, "title": "armação"}},
    {"id": "armacao_su_2", "speaker": "Luna", "text": "Unidades: 2 − 7 não dá. Então \\"emprestamos\\" uma dezena: o 5 vira 4 e o 2 vira 12.", "popup": {"step": 1}},
    {"id": "armacao_su_3", "speaker": "Luna", "text": "Agora dá: 12 − 7 = 5.", "popup": {"step": 1}},
    {"id": "armacao_su_4", "speaker": "Luna", "text": "Dezenas: 4 − 2 = 2.", "popup": {"step": 2}},
    {"id": "armacao_su_5", "speaker": "Luna", "text": "Então 52 − 27 = 25.", "popup": {"step": 2}}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);

-- ============================================================
-- armacao_multiplicacao
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('armacao_multiplicacao', NULL, 'Armação — multiplicação', '{"title": "Armação — multiplicação", "lines": [
    {"id": "armacao_mu_1", "speaker": "Tofu", "text": "Vamos armar 24 × 3. O número de baixo multiplica cada algarismo de cima, começando pela direita.", "bg": "floresta.png", "chars": [{"id": "tofu", "src": "tofu_teaching.png", "pos": "center", "show": true}], "popup": {"type": "column", "op": "*", "a": 24, "b": 3, "step": 0, "title": "armação"}},
    {"id": "armacao_mu_2", "speaker": "Tofu", "text": "3 × 4 = 12. Escrevemos o 2 e o 1 \\"vai\\" para a próxima coluna.", "popup": {"step": 1}},
    {"id": "armacao_mu_3", "speaker": "Tofu", "text": "3 × 2 = 6, mais o 1 que veio, dá 7.", "popup": {"step": 2}},
    {"id": "armacao_mu_4", "speaker": "Tofu", "text": "Então 24 × 3 = 72.", "popup": {"step": 2}}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);

-- ============================================================
-- armacao_divisao
-- ============================================================
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES ('armacao_divisao', NULL, 'Armação — divisão', '{"title": "Armação — divisão", "lines": [
    {"id": "armacao_di_1", "speaker": "Mia", "text": "Na divisão usamos a chave: o 78 fica dentro e o 3 do lado de fora.", "bg": "lago.png", "chars": [{"id": "mia", "src": "mia_teaching.png", "pos": "center", "show": true}], "popup": {"type": "column", "op": "/", "a": 78, "b": 3, "step": 0, "title": "armação"}},
    {"id": "armacao_di_2", "speaker": "Mia", "text": "Quantas vezes o 3 cabe no 7? Duas vezes, porque 2 × 3 = 6. Escrevemos o 2 no resultado.", "popup": {"step": 1}},
    {"id": "armacao_di_3", "speaker": "Mia", "text": "7 − 6 = 1. Agora \\"descemos\\" o 8 e ficamos com 18.", "popup": {"step": 1}},
    {"id": "armacao_di_4", "speaker": "Mia", "text": "Quantas vezes o 3 cabe no 18? Seis vezes: 6 × 3 = 18, e não sobra nada.", "popup": {"step": 2}},
    {"id": "armacao_di_5", "speaker": "Mia", "text": "Então 78 ÷ 3 = 26.", "popup": {"step": 2}}
]}')
ON DUPLICATE KEY UPDATE level_id = VALUES(level_id), title = VALUES(title), data = VALUES(data);
