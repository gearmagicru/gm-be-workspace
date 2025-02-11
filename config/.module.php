<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * Файл конфигурации модуля.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'translator' => [
        'locale'   => 'auto',
        'patterns' => [
            'text' => [
                'basePath'  => __DIR__ . '/../lang',
                'pattern'   => 'text-%s.php'
            ]
        ],
        'autoload' => ['text'],
        'external' => [BACKEND]
    ],

    'accessRules' => [
        // для авторизованных пользователей Панели управления
        [
            'allow',
            'controllers' => [
                'info'        => 'Info',
                'Index'       => ['index'],
                'Signout'     => ['index'],
                'Welcome'     => ['index'],
                'WidgetsTree' => ['data']
            ],
            'users' => ['@backend']
        ],
        [ // для всех остальных, доступа нет
            'deny'
        ]
    ],

    'tokenAccessRules' => [
        'csrf' => '*'
    ],

    'viewManager' => [
        'id'          => 'gm-workspace-{name}',
        'useTheme'    => true,
        'useLocalize' => true,
        'viewMap'     => [
            // информации о модуле
            'info' => [
                'viewFile'      => '//backend/module-info.phtml', 
                'forceLocalize' => true
            ],
            'workspace' => '/layouts/workspace.phtml' // макет страницы рабочего пространства
        ]
    ]
];
