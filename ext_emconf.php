<?php


$EM_CONF[$_EXTKEY] = [
    'title' => 'FE Debug/Info output',
    'description' => 'The plugin shows system variables and other debug data which might be interesting for frontend debugging and development. (old stuff from 2004 but might still be helpful)',
    'category' => 'plugin',
    'author' => 'René Fritz',
    'author_email' => 'r.fritz@colorcube.de',
    'author_company' => 'Colorcube',
    'version' => '1.1.0',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'constraints' => [
        'depends' => [
            'typo3' => '6.2.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'Colorcube\\CcFeinfo\\' => 'Classes'
        ]
    ]
];