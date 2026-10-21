<?php

// Configuração do quiz (contas) de cada nível. A chave é o id do nível.
//
//   op      '+', '-', '*' ou '/'
//   count   quantas contas até concluir o nível
//   min/max menor e maior número usados nas contas
//   mascot  arquivo em public/assets/img/vn/chars/ que aparece com o balão de fala
//           ('' = só o balão, sem imagem)
return [
    1 => ['op' => '+', 'count' => 10, 'min' => 1, 'max' => 12, 'mascot' => 'av1.png'],
    2 => ['op' => '-', 'count' => 10, 'min' => 1, 'max' => 15, 'mascot' => 'av1.png'],
    3 => ['op' => '*', 'count' => 10, 'min' => 1, 'max' => 10, 'mascot' => 'av1.png'],
    4 => ['op' => '/', 'count' => 10, 'min' => 1, 'max' => 10, 'mascot' => 'av1.png'],

    // usado por qualquer nível que não esteja listado acima
    'default' => ['op' => '+', 'count' => 10, 'min' => 1, 'max' => 10, 'mascot' => ''],
];
