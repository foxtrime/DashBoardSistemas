<?php

return [
    'role_structure' => [
        'DSV' => [
            'users'             => 'ca,ra,ua,da',
            'veiculos'          => 'ca,ra,ua,da',
            'postos'            => 'ca,ra,ua,da',
            'bases'             => 'ca,ra,ua,da',
            'locadoras'         => 'ca,ra,ua,da',
            'abastecimentos'    => 'ca,ra,ua,da',
            'secretarias'       => 'ca,ra,ua,da',
            'bdts'              => 'ca,ra,ua,da',
            'viagens'           => 'ca,ra,ua,da',
            'distribui_viagem'  => 'ca,ra,ua,da',
            'relatorios'        => 'ra',
        ],

        'ADMINISTRADOR' => [
            'users'             => 'ca,ra,ua,da',
            'veiculos'          => 'ca,ra,ua,da',
            'postos'            => 'ca,ra,ua,da',
            'bases'             => 'ca,ra,ua,da',
            'locadoras'         => 'ca,ra,ua,da',
            'abastecimentos'    => 'ca,ra,ua,da',
            'secretarias'       => 'ca,ra,ua,da',
            'bdts'              => 'ca,ra,ua,da',
            'viagens'           => 'ca,ra,ua,da',
            'distribui_viagem'  => 'ca,ra,ua,da',
            'relatorios'        => 'ra',
        ],
 
        'GOVERNO' => [
            'users'             => 'ra',
            'veiculos'          => 'ra',
            'postos'            => 'ra',
            'bases'             => 'ra',
            'locadoras'         => 'ra',
            'abastecimentos'    => 'ra',
            'secretarias'       => 'ra',
            'bdts'              => 'ra',
            'viagens'           => 'ra',
            'relatorios'        => 'ra',
        ],

        'GERENTE_FROTA' => [
            //'users'             => 'cs,rs,us,ds',
            'users'             => 'ro',
            'veiculos'          => 'ca,ra,ua,da',
            'postos'            => 'ca,ra,ua,da',
            'bases'             => 'ra',
            'locadoras'         => 'ra',
            'abastecimentos'    => 'ca,ra,ua,da',
            'secretarias'       => 'ra',
            'bdts'              => 'ca,ra,ua,da',
            'viagens'           => 'ca,ra,ua,da',
            'distribui_viagem'  => 'ca,ra,ua,da',
            'relatorios'        => 'ra',
        ],

        'RESP_ABASTECIMENTO' => [
            //'users'             => 'cs,rs,us,ds',
            //'veiculos'          => 'cs,rs,us,ds',
            'users'             => 'ro',
            'veiculos'          => 'ra',
            'postos'            => 'ca,ra,ua,da',
            'bases'             => 'ra',
            'locadoras'         => 'ra',
            'abastecimentos'    => 'ca,ra,ua,da',
            'secretarias'       => 'ra',
            'bdts'              => 'cs,rs,us,ds',
            'relatorios'        => 'ra',
        ],

        'RESP_BDT' => [
            //'users'             => 'cs,rs,us,ds',
            'users'             => 'ro',
            'veiculos'          => 'ra',
            //'postos'            => 'ra',
            'bases'             => 'ra',
            'locadoras'         => 'ra',
            //'abastecimentos'    => 'cs,rs,us,ds',
            'secretarias'       => 'ra',
            'bdts'              => 'ca,ra,ua,da',
            'relatorios'        => 'ra',
        ],

        'RESP_FROTA' => [
            'users'             => 'ro',
            'veiculos'          => 'ca,ra,ua,da',
            'postos'            => 'ra',
            'bases'             => 'ra',
            'locadoras'         => 'ca,ra,ua,da',
            'abastecimentos'    => 'ra',
            'secretarias'       => 'rs',
            'bdts'              => 'ca,ra,ua,da',
            'relatorios'        => 'rs',
        ],

        'SECRETARIO' => [
            'users'             => 'cs,rs,us,ds',
            'veiculos'          => 'cs,rs,us,ds',
            'postos'            => 'ca,ra,ua,da',
            'bases'             => 'ra',
            'locadoras'         => 'ca,ra,ua,da',
            'abastecimentos'    => 'cs,rs,us,ds',
            'secretarias'       => 'rs,us',
            'bdts'              => 'cs,rs,us,ds',
            'viagens'           => 'cs,rs,us,ds',
            'relatorios'        => 'rs',
        ],

        'FUNCIONARIO' => [
            'users'             => 'cs,rs,us,ds',
            'veiculos'          => 'cs,rs,us,ds',
            'postos'            => 'ra',
            'bases'             => 'ra',
            'locadoras'         => 'ra',
            'abastecimentos'    => 'cs,rs,us,ds',
            'secretarias'       => 'rs',
            'bdts'              => 'cs,rs,us,ds',
            'viagens'           => 'ro',
            'relatorios'        => 'rs',
        ],
        
        'FISCAL' => [
            'users'             => 'ro',
            'veiculos'          => 'rs',
            'postos'            => 'ra',
            'bases'             => 'ra',
            'locadoras'         => 'ra',
            //'abastecimentos'    => 'r',
            'secretarias'       => 'rs',
            'bdts'              => 'co,ro,uo,do',
            'relatorios'        => 'ro',
        ],

        'MOTORISTA' => [
            'users'             => 'ro',
            'veiculos'          => 'ro',
            'postos'            => 'ra',
            'bases'             => 'ra',
            'locadoras'         => 'ra',
            'abastecimentos'    => 'ro',
            'secretarias'       => 'rs',
            'bdts'              => 'ro',
            'viagens'           => 'ro',
            'relatorios'        => 'ro',
        ],

        'RESP_AGENDAMENTO' => [
            'users'             => 'ro',
            'viagens'           => 'cs,rs,us,ds',
            'relatorios'        => 'ro',
        ],

       
    
    ],

/*     'permission_structure' => [
        'cru_user' => [
            'profile' => 'c,r,u'
        ],
    ],
 */ 
    'permissions_map' => [
        'c' => 'create',
        'r' => 'read',
        'u' => 'update',
        'd' => 'delete',

        'co' => 'create_own',
        'ro' => 'read_own',
        'uo' => 'update_own',
        'do' => 'delete_own',

        'cs' => 'create_sec',
        'rs' => 'read_sec',
        'us' => 'update_sec',
        'ds' => 'delete_sec',
 
        'ca' => 'create_all',
        'ra' => 'read_all',
        'ua' => 'update_all',
        'da' => 'delete_all'
    ]
];
