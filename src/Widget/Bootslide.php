<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Workspace\Widget;

/**
 * Виджет для формирования интерфейса панели пуска.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Workspace\Widget
 * @since 1.0
 */
class Bootslide extends Widget
{
    /**
     * {@inheritdoc}
     */
    public string $viewFile = '/widgets/bootslide';

    /**
     * {@inheritdoc}
     */
    public array $viewOptions = [
        'useLocalize'   => true, // локализация для имён шаблонов
        'forceLocalize' => true, // имя шаблона соответствует выбранному языку
    ];

    /**
     * Имена виджетов (для формирования вкладок панели пуска).
     * 
     * @var array
     */
    protected array $items = ['Common', 'Account', 'Settings', 'Help'];

    /**
     * {@inheritdoc}
     */
    public function getParams(): array
    {
        $widgets = [];
        foreach ($this->items as $name) {
            $className = __NAMESPACE__ . NS . $name;
            /** @var string $content Контент виджета */
            $content = $className::widget();
            /** @var Widget $widget Созданный виджет */
            $widget = $className::getWidget();
            $widgets[] = [
                'name'      => strtolower($name),
                'title'     => $widget->title,
                'role'      => $widget->role,
                'data-role' => $widget->dataRole,
                'content'   => $widget->role == 'menuitem' ? $content : ''
            ];
        }
        return [
            'widgets' => $widgets
        ];
    }
}
