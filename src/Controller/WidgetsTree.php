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
use Gm\Panel\Controller\TreeController;

/**
 * Контроллер узлов дерева модулей.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Workspace\Controller
 * @since 1.0
 */
class WidgetsTree extends TreeController
{
    /**
     * {@inheritdoc}
     */
    protected string $defaultModel = 'WidgetsTree';

    /**
     * {@inheritdoc}
     */
    public function translateAction(mixed $params, string $default = null): ?string
    {
        switch ($this->actionName) {
            // вывод записи по указанному идентификатору
            case 'data':
                if ($params->queryId) {
                    if ($params->queryId === '["root"]')
                        return $this->t('view all tree nodes');
                    else
                        return Gm::t(BACKEND, '{data tree action}', [$params->queryId]);
                };

            default:
                return parent::translateAction(
                    $params,
                    $default ?: Gm::t(BACKEND, '{' . $this->actionName . ' tree action}')
                );
        }
    }
}
