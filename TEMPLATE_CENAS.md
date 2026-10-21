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
