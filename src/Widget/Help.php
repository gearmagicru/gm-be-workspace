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
 * Виджета для формирования интерфейса руководства помощи в панели пуска.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Workspace\Widget
 * @since 1.0
 */
class Help extends Widget
{
    /**
     * {@inheritdoc}
     */
    public string $viewFile = '/widgets/help';

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
    public string $title = 'Help';

    /**
     * {@inheritdoc}
     */
    public string $role = 'menuitem';

    /**
     * {@inheritdoc}
     */
    public function getParams(): array
    {
        $guide = Gm::getModule('gm.be.guide', [], false);
        if ($guide !== null) {
            $settings = $guide->getSettings();
            if ($settings !== null) {
                return [
                    'help' => $settings->getAll()
                ];
            }
        }
        return [];
    }
}
