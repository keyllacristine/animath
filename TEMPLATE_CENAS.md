# Template de cenas — AniMath VN

Guia rápido para criar e editar cenas direto no SQL (tabela `vn_scenes`).

## Criar uma cena nova

```sql
INSERT INTO vn_scenes (slug, level_id, title, data) VALUES (
    'level_2_intro',        -- nome único da cena (letras, números, _ e -)
    2,                      -- id do nível (ou NULL se for uma cena avulsa)
    'Introducao - Subtracao',
    '{"title":"Introdução – Subtração","lines":[

        {"speaker":"Mel","text":"Oi de novo! Hoje vamos subtrair.",
         "bg":"rio.png",
         "chars":[{"id":"mel","src":"mel_feliz.png","pos":"center","show":true}]},

        {"speaker":"Mel","text":"Subtrair é tirar coisas de um grupo."},

        {"speaker":"Bia","text":"Tipo comer uma maçã?",
         "chars":[{"id":"mel","pos":"left"},
                  {"id":"bia","src":"bia_normal.png","pos":"right","show":true}]},

        {"speaker":"Mel","text":"Isso! Se eu tenho 5 e tiro 2, fico com 3.",
         "chars":[{"id":"mel","src":"mel_animada.png"}]},

        {"speaker":"","text":"(texto sem nome de personagem, tipo narração)"},

        {"speaker":"Mel","text":"Vamos praticar!",
         "chars":[{"id":"bia","show":false}]}

    ]}'
);
```

## Editar uma cena que já existe

```sql
UPDATE vn_scenes
SET data = '{"title":"Intro","lines":[ ...suas falas aqui... ]}'
WHERE slug = 'level_2_intro';
```

## Ver o JSON de uma cena (para copiar de base)

```sql
SELECT data FROM vn_scenes WHERE slug = 'level_1_intro';
```

## Referência de cada fala

| campo | o que faz | obrigatório? |
|---|---|---|
| `speaker` | nome na etiqueta. Use `""` para narração (a etiqueta some) | não |
| `text` | texto do diálogo. Use `\n` para quebrar linha | sim |
| `bg` | troca o fundo (arquivo em `img/vn/bg/`). Se omitir, mantém o atual | não |
| `chars` | lista de comandos de personagem | não |
| `part` | use `2` na fala em que a EXPLICAÇÃO começa (ver "Reentrada rápida") | não |

### Itens de `chars`

| campo | valores | observação |
|---|---|---|
| `id` | qualquer nome (`"mel"`) | identifica o personagem na cena. Obrigatório |
| `src` | arquivo em `img/vn/chars/` | trocar o `src` muda a expressão |
| `pos` | `"left"`, `"center"`, `"right"` | se omitir, mantém a posição |
| `show` | `true` ou `false` | `false` esconde o personagem |

O estado persiste de fala em fala: fundo e personagens continuam na tela até você mudá-los.

## Cuidados no SQL

- O JSON fica entre aspas simples. Se o texto tiver apóstrofo (como em d'água), escreva dois: `''`.
- Aspas duplas dentro do texto precisam de barra: `\"`. Para quebra de linha, no MySQL escreva `\\n`.
- JSON não aceita vírgula sobrando depois da última fala nem comentários. Um erro faz a cena abrir vazia.
- Use o mesmo `id` de personagem do começo ao fim, porque é por ele que a engine sabe quem mover ou esconder.
- Os nomes de arquivo (`rio.png`, `mel_feliz.png`) precisam existir em `bg/` ou `chars/`, exatamente iguais, inclusive maiúsculas.
- Para validar o JSON antes de colar, use um validador online (jsonlint.com) ou abra a cena pelo editor ✎.

## Pastas das imagens

- Fundos: `public/assets/img/vn/bg/`
- Personagens (PNG): `public/assets/img/vn/chars/`

## Dica

Para não escrever JSON na mão, crie a cena pelo editor (digite o slug novo e clique em "Abrir / criar"), salve, e depois use o `SELECT` acima para copiar o resultado como base.


## Avatar e pronomes (muda conforme a conta do jogador)

Funciona em `speaker`, `text`, `bg` e `src`. O texto salvo no JSON não muda, só o que aparece na tela.

| escreva | aparece |
|---|---|
| `av{avatar}.png` | `av1.png` (avatar 1) ou `av2.png` (avatar 2) |
| `{nome}` | nome da conta |
| `{pronto\|pronta}` | 1ª opção para menino, 2ª para menina |
| `verdadeir{o\|a}`, `{ele\|ela}`, `{menino\|menina}` | mesma regra, vale para qualquer palavra |

(No SQL/JSON escreva `{pronto|pronta}` normal; a barra invertida acima é só da tabela do Markdown.)

- Qual avatar é menino/menina fica em `config/avatars.php` (hoje: 1 = menino, 2 = menina).
- Para ter uma expressão por avatar, nomeie os arquivos com o número, ex.: `mel_feliz.png` (fixo) e `heroi_{avatar}_feliz.png` → `heroi_1_feliz.png` / `heroi_2_feliz.png`.
- Os PNGs com esses nomes precisam existir em `img/vn/chars/`.
- O mascote do quiz usa o mesmo recurso (`config/quiz.php` → `mascot`).

## Reentrada rápida (parte 2)

Cada nível tem UMA cena (`level_N_intro`). Marque com `"part":2` a primeira fala da explicação (no editor ✎: caixinha "começa a parte 2").
- Primeira vez: a cena toca inteira.
- Depois que o jogador concluiu TODOS os níveis: ao entrar em qualquer nível a cena começa na fala marcada, e aparece o botão "pular para as contas".
- Se a cena não tiver nenhuma fala com `part:2`, ela toca inteira também na reentrada.

## Cena final

- `game_final` (opcional) toca uma única vez, quando o jogador conclui o último nível pela primeira vez.
- Depois dela aparece a mensagem final de `config/quiz.php` → `finale`, com o botão para a tela inicial.
- Sem a cena `game_final`, vai direto para a mensagem.

## Roteiro do jogo (como as cenas estão organizadas)

Rode `database/vn.sql` (cria a tabela) e depois `database/vn_roteiro.sql` (cria/atualiza as 9 cenas do roteiro; pode rodar de novo quando mudar o roteiro).

| slug | quando toca |
|---|---|
| `intro` | só na 1ª vez no nível 1 (enquanto o nível 1 não foi concluído), antes de `level_1_intro` |
| `level_N_intro` | ao entrar no nível N. Nos níveis 2–4, a fala com `part:2` é o início da explicação |
| (quiz) | contas do nível |
| `level_N_outro` | depois do quiz (níveis 1–3), antes de voltar à tela de níveis. Não toca na reentrada |
| `game_final` | uma vez, ao concluir o último nível pela 1ª vez. Depois vem a mensagem final |

Posições de personagem: `far-left`, `left`, `mid-left`, `center`, `mid-right`, `right`, `far-right` (use as `far-`/`mid-` em cenas com 4–5 personagens).

Lista de fundos e sprites que o roteiro usa: `ROTEIRO_ASSETS.md`.

## Jogador em primeira pessoa (POV)

O jogador não aparece como sprite: as falas dele saem com o nome da conta (`{nome}`) e sem imagem, e a narração usa "você". O avatar aparece só no fundo da home, no perfil, no mascote do quiz e na ilustração final (`ilustracao_final_{avatar}.png`, que vira `_1` ou `_2`).

## Pop-up didático (passos + armação de contas)

Janelinha em HTML/CSS que aparece durante as falas, revelada passo a passo. Campo `popup` na fala; o pop-up **persiste** de fala em fala. Uma fala só com `{"step": 3}` avança o mesmo pop-up, `{"type": ...}` troca, e `false` fecha. No editor ✎ use "Pop-up didático" → "(inserir exemplo…)".

| tipo | exemplo | passos |
|---|---|---|
| objetos `+` | `{"type":"items","op":"+","icon":"🌸","groups":[2,3],"step":1}` | 1 grupo A, 2 grupo B, 3 resultado |
| objetos `-` | `{"type":"items","op":"-","icon":"🌸","total":5,"remove":2}` | 1 todos, 2 riscar, 3 resultado |
| objetos `*` | `{"type":"items","op":"*","icon":"🍄","times":3,"per":2}` | 1..N grupos, N+1 soma, N+2 vezes |
| objetos `/` | `{"type":"items","op":"/","icon":"🐟","total":6,"parts":3}` | 1 monte, 2 pratos, 3 divide, 4 conta |
| sinal | `{"type":"equation","text":"3 × 2 = 6","mark":"×","caption":"..."}` | — |
| **armação** | `{"type":"column","op":"+","a":47,"b":38,"step":0}` | 0 conta montada, depois 1 coluna por passo |
| imagem/GIF | `{"type":"image","src":"arquivo.gif"}` | pasta `img/vn/popup/` |

Armação: `+` com "vai um" · `-` com "empresta" (inclusive 300 − 47) · `*` com um dígito (vai um) ou vários (produtos parciais) · `/` com a chave. `a` e `b` são inteiros (`a ≥ b` na subtração). Se o `step` passar do último, mostra o último. `icon` aceita emoji ou, com `src`, um arquivo de `chars/`.

Com o pop-up aberto os personagens vão para as laterais: use no máximo 2 por cena nessas falas.

Exemplos prontos de armação: `database/vn_armacao.sql` (cenas `armacao_adicao`, `_subtracao`, `_multiplicacao`, `_divisao`; chame com `vn.insertScene('armacao_adicao')`).
