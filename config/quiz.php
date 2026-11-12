<?php

// Configuração do quiz (contas) de cada nível. A chave é o id do nível.
//
//   op      '+', '-', '*' ou '/'
//   count   quantas contas até concluir o nível
//   min/max menor e maior número usados nas contas
//   mascot  arquivo em public/assets/img/vn/chars/ que aparece (metade, no canto) com o balão de fala
//           ('' = só o balão, sem imagem).
//           {mood} vira 'happy' (acertou) ou 'sad' (errou): 'luke_{mood}.png' usa luke_happy.png / luke_sad.png.
//           {avatar} vira 1 ou 2 conforme o avatar da conta.
return [
    1 => ['op' => '+', 'count' => 10, 'min' => 1, 'max' => 12, 'mascot' => 'luke_{mood}.png'],
    2 => ['op' => '-', 'count' => 10, 'min' => 1, 'max' => 15, 'mascot' => 'luna_{mood}.png'],
    3 => ['op' => '*', 'count' => 10, 'min' => 1, 'max' => 10, 'mascot' => 'tofu_{mood}.png'],
    4 => ['op' => '/', 'count' => 10, 'min' => 1, 'max' => 10, 'mascot' => 'mia_{mood}.png'],

    // Mensagem exibida ao concluir o jogo (1ª vez que todos os níveis ficam concluídos),
    // depois da cena 'game_final' (se existir). Aceita {nome} e {masculino|feminino}.
    'finale' => [
        'title'  => 'parabéns!',
        'text'   => "você concluiu sua jornada matemática e aprendeu as quatro operações fundamentais: adição, subtração, multiplicação e divisão. continue praticando e descubra quantas coisas novas você ainda pode aprender!\n\nmodo infinito desbloqueado!",
        'button' => 'ir para a tela inicial',
    ],

    // usado por qualquer nível que não esteja listado acima
    'default' => ['op' => '+', 'count' => 10, 'min' => 1, 'max' => 10, 'mascot' => ''],
];