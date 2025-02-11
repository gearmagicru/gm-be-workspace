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
use Gm\Helper\Browser;

/**
 * Виджета для формирования интерфейса общей информации о пользователе в панели пуска.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Workspace\Widget
 * @since 1.0
 */
class Common extends Widget
{
    /**
     * {@inheritdoc}
     */
    public string $viewFile = '/widgets/common';

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
    public string $title = 'Common';

    /**
     * {@inheritdoc}
     */
    public string $role = 'menuitem';

    /**
     * {@inheritdoc}
     */
    public function getParams(): array
    {
        if (!Gm::hasUserIdentity()) {
            return [];
        }

        /** @var \Gm\Panel\User\UserIdentity $identity */
        $identity = Gm::userIdentity();
        /** @var \Gm\Panel\User\UserRoles $useRoles */
        $useRoles = $identity->getRoles()->all();
        /** @var \Gm\Panel\User\UserProfile $profile */
        $profile = $identity->getProfile();

        // доступные группы пользователей
        $roles = [];
        foreach ($useRoles as $id => $role) {
            $roles[] = $role['name'] . ' (' . $role['shortname'] . ')';
        }

        // имя
        if ($profile->name)
            $name = $profile->name;
        else {
            $name = [];
            if ($profile->secondName)
                $name[] = $profile->secondName;
            if ($profile->firstName)
                $name[] = $profile->firstName;
            if ($profile->patronymicName)
                $name[] = $profile->patronymicName;
            $name = implode(' ', $name);
        }

        return [
            'profileName' => $name,
            'username'    => $identity->getUsername(),
            'visitedDate' => Gm::$app->formatter->toDateTime($identity->visitedDate),
            'browser'     => Browser::browserName(),
            'os'          => Browser::platformName(),
            'ipAddress'   => Gm::$app->request->getUserIp(),
            'roles'       => implode(', ', $roles)
        ];
    }
}
