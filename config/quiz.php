<?php

$arcadeTitle = 'Modo arcade';

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
        'text'   => "você concluiu sua jornada matemática e aprendeu as quatro operações fundamentais: adição, subtração, multiplicação e divisão. continue praticando e descubra quantas coisas novas você ainda pode aprender!\n\n" . mb_strtolower($arcadeTitle) . " desbloqueado!",
        'button' => 'ir para a tela inicial',
    ],

    'infinite' => [
        'title'         => 'chuva de contas',
        'time'          => 60,
        'lives'         => 3,
        'ops'           => ['+', '-', '*', '/'],
        'min'           => 1,
        'max'           => 10,
        'maxStep'       => 2,
        'levelEvery'    => 3,
        'maxLevel'      => 8,
        'factorCap'     => 12,
        'points'        => 10,
        'speed'         => 150,
        'speedMax'      => 330,
        'speedStep'     => 8,
        'spawn'         => 1.2,
        'spawnMin'      => 0.7,
        'spawnStep'     => 0.04,
        'correctAfterMin' => 1,
        'correctAfterMax' => 2,
    ],

    'arcade' => [
        'title'   => $arcadeTitle,
        'sudoku'  => [
            'time'           => 60,
            'blanks'         => 8,
            'cellPoints'     => 5,
            'bonusBase'      => 50,
            'bonusPerSecond' => 2,
        ],
        'memory'  => [
            'time'          => 30,
            'ops'           => ['+', '-', '*', '/'],
            'revealSeconds' => 5,
            'revealMin'     => 2.5,
            'revealStep'    => 0.3,
            'pairPoints'    => 10,
            'roundBonus'    => 20,
        ],
        'balance' => [
            'time'     => 60,
            'ops'      => ['+', '-', '*', '/'],
            'blocks'   => 5,
            'maxBlock' => 14,
            'points'   => 10,
        ],
    ],

    // usado por qualquer nível que não esteja listado acima
    'default' => ['op' => '+', 'count' => 10, 'min' => 1, 'max' => 10, 'mascot' => ''],
];