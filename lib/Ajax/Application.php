<?php

/**
 * Copyright 2012-2025 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL). If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author   Heinz Schweiger <heinz@htl-steyr.ac.at>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Gollem
 */

/**
 * Defines the AJAX interface for Gollem.
 *
 * @author   Heinz Schweiger <heinz@htl-steyr.ac.at>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Gollem
 */
class Gollem_Ajax_Application extends Horde_Core_Ajax_Application
{
    /**
     */
    protected function _init()
    {
        global $registry;

        switch ($registry->getView()) {
            case $registry::VIEW_BASIC:
            case $registry::VIEW_DYNAMIC:
                break;

            case $registry::VIEW_SMARTMOBILE:
                $this->addHandler('Gollem_Ajax_Application_Smartmobile');
                break;
        }
    }
}
