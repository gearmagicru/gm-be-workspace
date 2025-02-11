<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Workspace\Controller;

use Gm\Panel\Http\Response;
use Gm\Panel\Controller\BaseController;
use Gm\Backend\Workspace\Widget\WelcomeWindow;

/**
 * Контроллер отображения приветствия при первом запуске рабочего пространства.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Workspace\Controller
 * @since 1.0
 */
class Welcome extends BaseController
{
    /**
     * Создаёт виджет окна.
     * 
     * @return WelcomeWindow
     */
    public function createWidget(): WelcomeWindow
    {
        $window = new WelcomeWindow();
        return $window;
    }

     /**
     * Действие "index" выводит интерфейс окна приветствия.
     * 
     * @return Response
     */
    public function indexAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        /** @var WelcomeWindow $widget */
        $widget = $this->getWidget();
        // если была ошибка при формировании виджета
        if ($widget === false) {
            return $response;
        }

        $response
            ->setContent($widget->run())
            ->meta
                ->addWidget($widget);
        return $response;
    }
}
