<?php

return [

    'characters' => '1234567890',

    'default'   => [
        'length'    => 4,
        'width'     => 120,
        'height'    => 36,
        'quality'   => 90,
        'math'      => false,
    ],

    'flat'   => [
        'length'    => 4,
        'width'     => 160,
        'height'    => 46,
        'quality'   => 90,
        'lines'     => 1,
        'bgImage'   => false,
        'bgColor'   => '#ACC6AA',
        'fontColors'=> ['#2c3e50'],
        'contrast'  => 0,
    ],

    'mini'   => [
        'length'    => 3,
        'width'     => 60,
        'height'    => 32,
    ],

    'inverse'   => [
        'length'    => 5,
        'width'     => 120,
        'height'    => 36,
        'quality'   => 90,
        'sensitive' => true,
        'angle'     => 12,
        'sharpen'   => 10,
        'blur'      => 2,
        'invert'    => true,
        'contrast'  => -5,
    ]

];
