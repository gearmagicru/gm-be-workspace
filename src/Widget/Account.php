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
 * Виджета для формирования интерфейса аккаунта пользователя в панели пуска.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Workspace\Widget
 * @since 1.0
 */
class Account extends Widget
{
    /**
     * {@inheritdoc}
     */
    public string $viewFile = '/widgets/account';

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
    public string $title = 'Account';

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
        /** @var \Gm\Panel\User\UserRoles $identityRoles */
        $identityRoles = $identity->getRoles()->all();
        /** @var \Gm\Panel\User\UserProfile $profile */
        $profile = $identity->getProfile();
        // доступные группы пользователей
        $roles = [];
        foreach($identityRoles as $id => $role) {
            $roles[] = $role['name'] . ' (' . $role['shortname'] . ')';
        }
        // обращение
        if ($profile->callName)
            $name = $profile->callName;
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
            'username'    => $identity->getUsername(),
            'userId'      => $identity->getId(),
            'profileId'   => $identity->getProfile()->id,
            'roles'       => implode(', ', $roles),
            'name'        => $name,
            'picture'     => $profile->getPicture(),
            'gender'      => $profile->gender,
            'dateOfBirth' => $profile->dateOfBirth,
            // контактные данные
            'phone'       => $profile->phone,
            'email'       => $profile->email,
            'contactExists' => $profile->phone || $profile->email
        ];
    }
}
