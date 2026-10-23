<?php

// Gênero de cada avatar (coluna users.avatar). Usado para trocar os pronomes
// nos diálogos: {menino|menina}, {o|a}, {ele|ela}...  ('m' = 1ª opção, 'f' = 2ª opção)
// Se o avatar 1 for a menina, é só inverter aqui.
return [
    1 => ['gender' => 'm'],
    2 => ['gender' => 'f'],
];
