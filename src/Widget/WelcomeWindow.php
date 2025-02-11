<?php
/**
 * Виджет модуля веб-приложения GearMagic.
 * 
 * @link https://gearmagic.ru
 * @copyright Copyright (c) 2015 Веб-студия GearMagic
 * @license https://gearmagic.ru/license/
 */

namespace Gm\Backend\Workspace\Widget;

use Gm;
use Gm\Helper\Html;
use Gm\Panel\Widget\Window;

/**
 * Виджет для формирования интерфейса окна приветствия.
 * 
 * @author Anton Tivonenko <anton.tivonenko@gmail.com>
 * @package Gm\Backend\Workspace\Widget
 * @since 1.0
 */
class WelcomeWindow extends Window
{
    /**
     * {@inheritdoc}
     */
    protected function init(): void
    {
        parent::init();

        // свойства окна (Ext.window.Window ExtJS)
        $this->ui          = 'light';
        $this->layout      = 'fit';
        $this->width       = 800;
        $this->height      = '70%';
        $this->resizable   = true;
        $this->maximizable = true;
        $this->title       = $this->makeTitle();
    }

    /**
     * Возвращает заголовок окна.
     * 
     * @return string
     */
    protected function makeTitle(): string
    {
        $name = Gm::$app->config->edition['name'] ?? '';
        $number = Gm::$app->config->edition['number'] ?? '';

        return $this->creator->t(
            'Welcome to "{0}"',
            [Gm::t('app', $name) . ($number ? ' - ' . $number : '') ]
        );
    }

    /**
     * Возвращает URL-адрес справочной документации.
     * 
     * @return string
     */
    public function getDocsUrl(): string
    {
        /** @var array|null $edition */
        $edition = Gm::$app->config->edition;
        if (empty($edition['code']) || empty($edition['number'])) return '';

        $url = rtrim(Gm::$app->config->docsServer['url'] ?? '', '/');
        if (empty($url)) return '';

        return $url . '/embed/welcome.html';
        // return $url . '/' . strtolower($edition['number']) . '-' . str_replace('.', '_', $edition['number']) . '/welcome.html';
    }

    /**
     * {@inheritdoc}
     */
    public function beforeRender(): bool
    {
        $this->html = Html::iframe([
            'class'       => 'g-frame_fit',
            'frameborder' => '0',
            'src'         => $this->getDocsUrl()
        ]);
        return true;
    }
}
