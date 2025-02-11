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
 * Виджет для формирования интерфейса вкладок в панели пуска.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Workspace\Widget
 * @since 1.0
 */
class Widget extends \Gm\View\Widget
{
    /**
     * Загаловок пункта меню.
     * 
     * @var string
     */
    public string $title = '';

    /**
     * Роль пункта меню.
     * 
     * @var string
     */
    public string $role = '';

    /**
     * Роль пункта меню.
     * 
     * @var string
     */
    public string $dataRole = '';

    /**
     * {@inheritdoc}
     */
    public function run(): mixed
    {
        $params = $this->getParams();
        $params['empty'] = empty($params);
        $params['title'] = $this->title;
        $params['role'] = $this->title;
        return $this->render($this->viewFile, $params);
    }

    /**
     * Возвращает параметры виджета выводимых в шаблон.
     * 
     * @return array
     */
    public function getParams(): array
    {
        return [];
    }
}
