<?php

/**
 * Gollem clipboard script.
 *
 * Copyright 2005-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (GPL). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl.
 *
 * @author   Michael Slusarz <slusarz@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/gpl GPL
 * @package  Gollem
 */

require_once __DIR__ . '/lib/Application.php';
Horde_Registry::appInit('gollem');

$vars = Horde_Variables::getDefaultVariables();

/* Set up the template object. */
$template = $injector->createInstance('Horde_View');
$template->cancelbutton = _("Cancel");
$template->clearbutton = _("Clear");
$template->pastebutton = _("Paste");
/**
 * ARCHITECTURE VIOLATION: Using deprecated Horde::img()
 * @deprecated Use Horde_Themes_Image::tag() instead
 * @see Horde_Deprecated::img()
 */
$template->cutgraphic = Horde::img('cut.png', _("Cut"));
/**
 * ARCHITECTURE VIOLATION: Using deprecated Horde::img()
 * @deprecated Use Horde_Themes_Image::tag() instead
 * @see Horde_Deprecated::img()
 */
$template->copygraphic = Horde::img('copy.png', _("Copy"));
$template->currdir = Gollem::getDisplayPath($vars->dir);
$template->dir = $vars->dir;
$template->manager_url = Horde::url('manager.php');

$entry = [];
foreach ($session->get('gollem', 'clipboard') as $key => $val) {
    $entry[] = [
        'copy' => ($val['action'] == 'copy'),
        'cut' => ($val['action'] == 'cut'),
        'id' => $key,
        'name' => $val['display'],
    ];
}
$template->entries = $entry;

$page_output->addScriptFile('clipboard.js');
$page_output->addScriptFile('tables.js', 'horde');
$page_output->addInlineJsVars([
    'GollemClipboard.selectall' => _("Select All"),
    'GollemClipboard.selectnone' => _("Select None"),
]);

$page_output->header([
    'title' => _("Clipboard"),
]);
$notification->notify(['listeners' => 'status']);
echo $template->render('clipboard');
$page_output->footer();
