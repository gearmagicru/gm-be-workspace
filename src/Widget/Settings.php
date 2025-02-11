<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Workspace\Widget;

use Gm;

/**
 * Виджет вывода элементов конфигурации (пункт "Конфигурация") панели пуска.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Workspace\Widget
 * @since 1.0
 */
class Settings extends Widget
{
    /**
     * {@inheritdoc}
     */
    public string $viewFile = '/widgets/settings';

    /**
     * {@inheritdoc}
     */
    public array $viewOptions = [
        'useLocalize'   => true, // локализация для имён шаблонов
        'forceLocalize' => true, // имя шаблона соответствует выбранному языку
    ];

    /**
     * {@inheritdoc}
     */
    public string $title = 'Settings';

    /**
     * {@inheritdoc}
     */
    public string $role = 'menuitem';

    /**
     * {@inheritdoc}
     */
    public string $dataRole = 'data-role';

    /**
     * {@inheritdoc} 
     */
    public function getParams(): array
    {
        /** @var \Gm\Backend\Partitionbar\Module $module */
        $module = Gm::createModule('gm.be.partitionbar');
        if ($module) {
            /** @var \Gm\Backend\Partitionbar\Workspace\Bootslide $bootslide */
            $bootslide = $module->getModel('Workspace\Bootslide');
            return [
                'items' => $bootslide->getItems()
            ];
        }
        return [];
    }
}
