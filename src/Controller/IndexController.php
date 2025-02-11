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
use Gm\Exception;
use Gm\Helper\Json;
use Gm\Helper\Url;
use Gm\Helper\Str;
use Gm\Mvc\Controller\Controller;

/**
 * Контроллер рабочего пространства.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Workspace\Controller
 * @since 1.0
 */
class IndexController extends Controller
{
    /**
     * Добавляет панель разделов.
     * 
     * @param array $params 
     * 
     * @return void
     */
    public function addPartitionbarView(array &$params): void
    {
        $partitionbar = Gm::getModule('gm.be.partitionbar');
        if ($partitionbar !== null) {
            /** @var \Gm\Backend\Partitionbar\Model\Workspace\Panel $panel */
            $panel = $partitionbar->getModel('Workspace\Panel');
            $params['partitionbar'] = $panel !== null ? $panel->getSettings() : 'null';
        } else
            $params['partitionbar'] = 'null';
    }

    /**
     * Добавляет панель уведомлений.
     * 
     * @param array $params 
     * 
     * @return void
     */
    public function addTraybarView(array &$params): void
    {
        $traybar = Gm::getModule('gm.be.traybar', [], false);
        if ($traybar !== null) {
            /** @var \Gm\Backend\Traybar\Model\Workspace\Panel  */
            $panel = $traybar->getModel('Workspace\Panel');
            $params['traybar'] = $panel !== null ? $panel->getSettings() : 'null';
        } else
            $params['traybar'] = 'null';
    }

    /**
     * Добавляет панель главного меню.
     * 
     * @param array $params 
     * 
     * @return void
     */
    public function addMenuView(array &$params): void
    {
        $menu = Gm::getModule('gm.be.menu', [], false);
        if ($menu !== null) {
            /** @var \Gm\Backend\Menu\Model\Workspace\Panel  */
            $panel = $menu->getModel('Workspace\Panel');
            $params['menu'] = $panel !== null ? $panel->getSettings() : 'null';
        } else
            $params['menu'] = 'null';
    }

    /**
     * Добавляет панель навигации.
     * 
     * @param array $params 
     * 
     * @return void
     */
    public function addNavigatorView(array &$params): void
    {
        // настройка панели навигации
        $workspace = Gm::$app->unifiedConfig->get('workspace');
        $params['navigator'] = Json::encode([
            'position'    => $workspace['navigatorPosition'] ?? 'east',
            'split'       => $workspace['navigatorSplit'] ?? true,
            'collapsible' => $workspace['navigatorCollapsible'] ?? true,
            'width'       => isset($workspace['navigatorWidth']) ? (int) $workspace['navigatorWidth'] : 350,
            'hidden'      => isset($workspace['navigatorVisible']) ? !$workspace['navigatorVisible'] : false
        ]);
    }

    /**
     * Добавляет настройки автозапуска модулей и их расширений.
     * 
     * @param array $params 
     * 
     * @return void
     */
    public function addAutorunView(array &$params): void
    {
        /** @var \Gm\Backend\Config\Autorun\Model\Autorun $autorun */
        $autorun = Gm::$app->extensions->getModel('Autorun', 'gm.be.config.autorun');
        if ($autorun) {
            /** @var array $routes маршруты модулей и их расширений */
            $routes = $autorun->getRoutes();
            $params['autorunRoutes'] = $routes ? implode(',', $routes) : '';
        }
    }

    /**
     * Добавляет в автозапуск окно приветствия.
     * 
     * @param array $params 
     * 
     * @return void
     */
    public function addWelcomeView(array &$params): void
    {
        /** @var null|array $workspace Настройка приветствия */
        $welcome = Gm::$app->unifiedConfig->welcome;
        // если окно уже выводилось
        $shown = $welcome ? ($welcome['show'] ?? false) : false;
        if (!$shown) {
            $welcome['show'] = true;
            Gm::$app->unifiedConfig->welcome = $welcome;
            Gm::$app->unifiedConfig->save();

            /** @var string $route Маршрут запуска окна */
            $route = 'workspace/welcome';
            // если маршруты автозапуска уже указаны
            if (isset($params['autorunRoutes']))
                $params['autorunRoutes'] = $params['autorunRoutes']  . ",'$route'";
            else
                $params['autorunRoutes'] = "'$route'";
        }
    }

    /**
     * Добавляет параметры локализации.
     * 
     * @param array $params 
     * 
     * @return void
     */
    public function addLanguage(array &$params): void
    {
        /** @var \Gm\View\ClientScript $script */
        $script = Gm::$app->page->script;
        /** @var \Gm\Language\Language $language */
        $language = Gm::$app->language;
        /** @var string $path Локальный путь к файлам локализации Панели */
        $path = '/vendors/gm/panel/locale/';
        // 
        if (!$script->exists($path . $language->locale))
            $locale = $language->alternative;
        else
            $locale = $language->locale;

        $params['locale'] = [
            'language'    => $language->slug,
            'path'        => Url::toPublished($path . $locale),
            'isDefault'   => Str::boolToStr($language->isDefault()),
            'isPosPrefix' => Str::boolToStr($language->isPosPrefix),
            'isPosSuffix' => Str::boolToStr(!$language->isPosPrefix),
            'slugParam'   => $language->slugParam
        ];
    }

    /**
     * Добавляет параметр шорткодов для подстановки в редактор.
     * 
     * @param array $params 
     * 
     * @return void
     */
    public function addEditorShortcodes(array &$params): void
    {
        $items = [];

        /** @var \Gm\ModuleManager\ModuleRegistry $registry */
        $registry = Gm::$app->modules->getRegistry();
        /** @var array $modules */
        $modules = $registry->getListInfo(true, false, 'rowId', ['icon' => true, 'install' => true]);
        foreach ($modules as $rowId => $module) {
            if ($module['enabled'] && $module['visible']) {
                $shortcodes = $module['install']['editor']['shortcodes'] ?? [];
                if ($shortcodes) {
                    foreach ($shortcodes as $tagName => $shortcode) {
                        if (isset($shortcode['icon']))
                            $icon = MODULE_BASE_URL . $module['path']. '/assets' . $shortcode['icon'];        
                        else
                            $icon = $module['smallIcon'];
                        $items[] = [
                            'type' => 'module',
                            'id'   => $rowId,
                            'name' => $shortcode['name'], // имя тега шорткода 'foobar'
                            'tag'  => $shortcode['tag'] ?? null, // тег шорткода '[foobar][/foobar]'
                            'text' => (empty($shortcode['text']) ? $module['name'] : $shortcode['text']),
                            'icon' => $icon
                        ];
                    }
                }
            }
        }

        /** @var \Gm\ExtensionManager\ExtensionRegistry $registry */
        $registry = Gm::$app->extensions->getRegistry();
        /** @var array $extensions */
        $extensions = $registry->getListInfo(true, false, 'rowId', ['icon' => true, 'install' => true]);
        foreach ($extensions as $rowId => $extension) {
            if ($extension['enabled']) {
                $shortcodes = $extension['install']['editor']['shortcodes'] ?? [];
                if ($shortcodes) {
                    foreach ($shortcodes as $tagName => $shortcode) {
                        if (isset($shortcode['icon']))
                            $icon = MODULE_BASE_URL . $extension['path']. '/assets' . $shortcode['icon'];        
                        else
                            $icon = $extension['smallIcon'];
                        $items[] = [
                            'type' => 'extension',
                            'id'   => $rowId,
                            'name' => $shortcode['name'], // имя тега шорткода 'foobar'
                            'tag'  => $shortcode['tag'] ?? null, // тег шорткода '[foobar][/foobar]'
                            'text' => (empty($shortcode['text']) ? $extension['name'] : $shortcode['text']),
                            'icon' => $icon
                        ];
                    }
                }
            }
        }

        /** @var \Gm\WidgetManager\WidgetRegistry $registry */
        $registry = Gm::$app->widgets->getRegistry();
        /** @var array $widgets */
        $widgets = $registry->getListInfo(true, false, 'rowId', ['icon' => true, 'install' => true]);

        $namePrefix = $this->t('widget') . ' ';
        foreach ($widgets as $rowId => $widget) {
            if ($widget['enabled']) {
                $shortcodes = $widget['install']['editor']['shortcodes'] ?? [];
                if ($shortcodes) {
                    foreach ($shortcodes as $tagName => $shortcode) {
                        if (isset($shortcode['icon']))
                            $icon = MODULE_BASE_URL . $widget['path']. '/assets' . $shortcode['icon'];        
                        else
                            $icon = $widget['smallIcon'];
                        $items[] = [
                            'type' => 'widget',
                            'id'   => $rowId,
                            'name' => $shortcode['name'], // имя тега шорткода 'foobar'
                            'tag'  => $shortcode['tag'] ?? null, // тег шорткода '[foobar][/foobar]'
                            'text' => $namePrefix . (empty($shortcode['text']) ? $widget['name'] : $shortcode['text']),
                            'icon' => $icon
                        ];
                    }
                }
            }
        }
        $params['editorShortcodes'] = json_encode($items, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
    }

    /**
     * Действие "index" выводит интерфейс рабочего пространства.
     * 
     * @return string
     */
    public function indexAction(): string
    {
        // заголовок вкладки браузера (атрибут тега "title")
        Gm::$app->page->title = $this->t('Panel control');

        $params = [
            // заголовок вкладки браузера (Gm.settings.panel.name)
            'name' => $this->t('Panel control'),
            // направление текста в виджетах справа налево (Gm.settings.locale.rtl)
            'rtl' => false,
            // подключить стили темы (Gm.settings.theme.includeCSS)
            'includeCSS' => true,
            // заменть скрипты темы если она унаследована (Gm.settings.theme.hasOverrides)
            'hasOverrides' => true,
            // версия приложения (Gm.settings.versionName)
            'version' => Gm::$app->version
        ];
        $this->addLanguage($params);
        // интерфейс панели разделов (Gm.settings.panel.partitionbar)
        $this->addPartitionbarView($params);
        // интерфейс панели уведомлений (Gm.settings.panel.traybar)
        $this->addTraybarView($params);
        // интерфейс панели меню (Gm.settings.panel.menu)
        $this->addMenuView($params);
        // интерфейс панели навигации (Gm.settings.panel.navigator)
        $this->addNavigatorView($params);
        // настройки автозапуска (Gm.settings.autorun)
        $this->addAutorunView($params);
        // шорткоды реадактора (Gm.settings.editorShortcodes)
        $this->addEditorShortcodes($params);
        // интерфейс окна приветствия
        $this->addWelcomeView($params);
        // если были ошибки при преобразовании JSON-представления
        if ($error = Json::error()) {
            throw new Exception\JsonFormatException($error);
        }

        // вызывает событие перед выводом параметров в шаблон workspace
        Gm::$app->doEvent($this->module->id . ':onRender', [&$params]);

        return $this->renderLayout('workspace', $params);
    }
}
