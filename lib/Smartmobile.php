<?php

/**
 * Copyright 2012-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author   Heinz Schweiger <heinz@htl-steyr.ac.at>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Gollem
 */

use Horde\Util\Variables;

/**
 * Base class for smartmobile view pages.
 *
 * @author   Heinz Schweiger <heinz@htl-steyr.ac.at>
 * @category Horde
 * @copyright 2012-2025 Horde LLC
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Gollem
 */
class Gollem_Smartmobile
{
    /**
     * @var Horde_Variables
     */
    public $vars;

    /**
     * @var Horde_View
     */
    public $view;

    /**
     */
    public function __construct(Variables|Horde_Variables $vars)
    {
        global $notification, $page_output;

        $this->vars = $vars;

        $this->view = new Horde_View([
            'templatePath' => GOLLEM_TEMPLATES . '/smartmobile',
        ]);
        $this->view->addHelper('Horde_Core_Smartmobile_View_Helper');
        $this->view->addHelper('Text');

        $this->_initPages();
        $this->_addBaseVars();

        $page_output->addScriptFile('smartmobile.js');

        $notification->notify(['listeners' => 'status']);
    }

    /**
     */
    public function render()
    {
        echo $this->view->render('backends');
        echo $this->view->render('folder');
    }

    /**
     */
    protected function _initPages()
    {
        global $injector, $session;

        $this->view->list = [];

        foreach (Gollem_Auth::getBackend() as $key => $val) {
            $url = new Horde_Core_Smartmobile_Url();
            $url->setAnchor('folder');
            $url->add('backend_key', $key);
            $this->view->list[] = [
                'img' =>  Horde_Themes_Image::tag('gollem.png', ['attr' => ['class' => 'ui-li-icon']]),
                'name' => $val['name'],
                'url' => $url,

            ];
        }
    }


    /**
     * Add base javascript variables to the page.
     */
    protected function _addBaseVars()
    {
        global $page_output;

        $code = [
            'text' => [
                'no_descrip' => _("No Description"),
            ],
        ];

        $page_output->addInlineJsVars([
            'var Ingo' => $code,
        ], ['top' => true]);
    }
}
