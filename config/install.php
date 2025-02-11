<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

return [
    'use'         => BACKEND,
    'id'          => 'gm.be.workspace',
    'name'        => 'Workspace',
    'description' => 'A set of panels in different areas of the screen for the implementation of applied tasks',
    'namespace'   => 'Gm\Backend\Workspace',
    'path'        => '/gm/gm.be.workspace',
    'route'       => 'workspace',
    'routes'      => [
        [
            'type'    => 'crudSegments',
            'options' => [
                'module'      => 'gm.be.workspace',
                'route'       => 'workspace',
                'prefix'      => BACKEND
            ]
        ],
    ],
    'locales'     => ['ru_RU', 'en_GB'],
    'permissions' => [],
    'events'      => [],
    'required'    => [
        ['php', 'version' => '8.2'],
        ['app', 'code' => 'GM MS'],
        ['app', 'code' => 'GM CMS'],
        ['app', 'code' => 'GM CRM'],
    ]
];
