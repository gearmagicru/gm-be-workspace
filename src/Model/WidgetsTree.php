<?php
/**
 * Этот файл является частью модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Workspace\Model;

use Gm;
use Gm\Helper\Str;
use Gm\Panel\Data\Model\NodesModel;

/**
 * Модель данных дерева виджетов (модулей и их расширений) панели навигации.
 * 
 * Дерево виджетов формируется на основе элементов панели разделов.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Workspace\Model
 * @since 1.0
 */
class WidgetsTree extends NodesModel
{
    /**
     * Количество символов в надписи элемента навигации.
     * 
     * @var int
     */
    public int $textLength = 27;

    /**
     * {@inheritdoc}
     */
    public function getIdentifier(): mixed
    {
        if (!isset($this->nodeId)) {
            $nodeId = Gm::$app->request->getQuery('node');
            if ($nodeId)
                $this->nodeId = explode('-', $nodeId);
            else
                $this->nodeId = [];
        }
        return $this->nodeId;
    }

    /**
     * Возвращает корневые элементы дерева (элементы панели разделов не имеющих 
     * родителей).
     * 
     * @return array
     */
    public function getRootNodes(): array
    {
        $nodes  = [];
        /** @var \Gm\Backend\Partitionbar\Module|null $modulePbar */
        $modulePbar = Gm::$app->modules->get('gm.be.partitionbar');

        /** @var \Gm\Backend\Partitionbar\Model\Partitionbar $pbar Панель разделов */
        $pbar = $modulePbar->getModel('Partitionbar');
        if ($pbar) {
            /** @var array $childs Доступные пользователю корневые эелементы панели разделов */
            $childs = $pbar->getRoot(true);
            if ($childs) {
                foreach ($childs as $child) {
                    $nodes[] = [
                        'id'   => 'partition-' . $child['id'],
                        'text' => Str::ellipsis($modulePbar->tH($child['name']), 0, $this->textLength),
                        'leaf' => false
                    ];
                }
            }
        }
        return $nodes;
    }


    /**
     * Возвращает дочернии элементы дерева по указанному идентификатору родителя.
     * 
     * Дочерними элементами могут быть модули, расширения или другие элементы панели разделов.
     * 
     * @param int|null $parentId Идентификатор родителя.
     * 
     * @return array Дочернии элементы дерева. Если идентификатор родителя не указан, то 
     *     реузльтат - пустой массив.
     */
    public function getChildNodes(?int $parentId): array
    {
        $result = [];
        if (empty($parentId)) {
            return $result;
        }
        /** @var \Gm\Backend\Partitionbar\Module|null $modulePbar */
        $modulePbar = Gm::$app->modules->get('gm.be.partitionbar');

        /** @var \Gm\Backend\Partitionbar\Model\Partitionbar $pbar Панель разделов */
        $pbar = $modulePbar->getModel('Partitionbar');
        /** @var array $childs Доступные пользователю дочернии эелементы панели разделов */
        $childs = $pbar->getChildren($parentId, true);
        if ($childs) {
            foreach ($childs as $child) {
                $result[] = [
                    'id'   => 'partition-' . $child['id'],
                    'text' => Str::ellipsis($modulePbar->tH($child['name']), 0, $this->textLength),
                    'leaf' => false
                ];
            }
        }

        // все доступные модули сгруппированные по идентификаторам элементов панели разделов 
        $modules = Gm::tempGet('pbarModules', function () use ($pbar) {
            return $pbar->getModules(true, true); 
        });
        // если есть модули для указанного родителя
        if (isset($modules[$parentId])) {
            // добавляем модули к выводу
            foreach ($modules[$parentId] as $module) {
                // если модуль имеет расширения
                if ($module['expandable']) {
                    /** @var \Gm\Panel\Extension\ExtensionNavigation $navigation Панель навигации расширения */
                    $navigation = Gm::$app->modules->getObject('Workspace\Navigation', $module['id'], ['id' => $module['id']]);
                    // если расширение имеет панель навигации
                    if ($navigation !== null) {
                        $result[] = [
                            'id'          => $module['id'],
                            'text'        => Str::ellipsis($module['name'], 0, $this->textLength),
                            'description' => $module['description'],
                            'icon'        => $module['smallIcon'],
                            'leaf'        => false
                        ];
                    }
                } else {
                    $result[] = [
                        'text'        => Str::ellipsis($module['name'], 0, $this->textLength),
                        'description' => $module['description'],
                        'icon'        => $module['smallIcon'],
                        'widgetUrl'   => '@backend/'. $module['route'],
                        'leaf'        => true
                    ];
                }
            }
        }

        // все доступные расширения модулей сгруппированные по идентификаторам элементов панели разделов 
        $extensions = Gm::tempGet('pbarExtensions', function () use ($pbar) {
            return $pbar->getExtensions(true, true); 
        });
        // если есть расширения для указанного родителя
        if (isset($extensions[$parentId])) {
            // добавляем расширения к выводу
            foreach ($extensions[$parentId] as $extension) {
                $result[] = [
                    'text'        => Str::ellipsis($extension['name'], 0, $this->textLength),
                    'description' => $extension['description'],
                    'icon'        => $extension['smallIcon'],
                    'widgetUrl'   => Gm::alias('@backend', '/'. $extension['baseRoute']),
                    'leaf'        => true
                ];
            }
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getNodes(): array
    {
        $node = $this->getIdentifier();
        if ($node) {
            // имя узла
            $name = $node[0] ?? null;
            // идентификатор раскрываемого узла
            $id = isset($node[1]) ? (int) $node[1] : null;
            if ($name) {
                $nodeId = Gm::$app->request->getQuery('node');
                /** @var \Gm\Session\Container $storage */
                $storage = $this->module->getStorage();
                // кэшируем результат
                if ($storage->nodes === null) {
                    $storage->nodes = [];
                }
                if (isset($storage->nodes[$nodeId])) {
                }

                // если имя узла - имя модуля, значит модуль имеет расширение
                if (strpos($name, '.') !== false) {
                    /** @var \Gm\Panel\Extension\ExtensionNavigation $navigation Панель навигации */
                    $navigation = Gm::$app->modules->getObject(
                        'Workspace\Navigation', $name, ['id' => $name, 'textLength' => $this->textLength]
                    );
                    $nodes = $navigation ? $navigation->getNodes(false) : [];
                } else {
                    $method = 'get' . $name . 'Nodes' ;
                    if (method_exists($this, $method)) {
                        $nodes = $this->{$method}($id);
                    } else 
                        $nodes = $this->getChildNodes($id);
                }
                return $storage->nodes[$nodeId] = $nodes;
            }
        }
        return [];
    }
}
