<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Workspace\Controller;

use Gm;
use Gm\Helper\Url;
use Gm\Panel\Http\Response;
use Gm\Panel\Controller\BaseController;

/**
 * Контроллер рабочего пространства.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Workspace\Controller
 * @since 1.0
 */
class Signout extends BaseController
{
    /**
     * {@inheritdoc}
     */
    public function translateAction(mixed $params, string $default = null): ?string
    {
        switch ($this->actionName) {
            // выход из панели управления
            case '':
                return $this->t('sign out of user account');

            default:
                return parent::translateAction($params, $default);
        }
    }

    /**
     * Действие "index" удаляет сессию пользователя.
     * 
     * @return Response
     */
    public function indexAction(): Response
    {
        /** @var Response $response */
        $response = $this->getResponse();

        // проверка, если пользователь имеет сессию
        if (Gm::hasUserIdentity()) {
            //Gm::$app->audit->getDefaultRow(); // запомнить информацию о пользователе
            Gm::$app->session->destroy();
        }
        // перенаправить на страницу авторизации
        $response
            ->meta->command('redirect', Url::toBackend(''));
        return $response;
    }
}
