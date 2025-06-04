<?php

/**
 * gollem smartmobile view.
 *
 * Copyright 2012-2023 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @author   Heinz Schweiger <heinz@htl-steyr.ac.at>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Gollem
 */




require_once __DIR__ . '/lib/Application.php';
Horde_Registry::appInit('gollem');

$ob = new Gollem_Smartmobile($injector->getInstance('Horde_Variables'));

$page_output->header(array(
    'title' => _("Mobile"),
    'view' => $registry::VIEW_SMARTMOBILE
));

$ob->render();

$page_output->footer();
