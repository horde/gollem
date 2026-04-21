<?php

use Horde\Util\Util;

/**
 * Gollem edit script.
 *
 * Copyright 2006-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (GPL). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl.
 *
 * @author   Jan Schneider <jan@horde.org>
 * @category Horde
 * @license  http://www.horde.org/licenses/gpl GPL
 * @package  Gollem
 */

require_once __DIR__ . '/lib/Application.php';
Horde_Registry::appInit('gollem');

$vars = Horde_Variables::getDefaultVariables();

if ($vars->driver != Gollem::$backend['driver']) {
    echo Horde::wrapInlineScript(['window.close();']);
    exit;
}

/* Run through action handlers. */
switch ($vars->actionID) {
    case 'save_file':
        try {
            $injector
                ->getInstance('Gollem_Vfs')
                ->writeData($vars->dir, $vars->file, $vars->content);
            $message = sprintf(_("%s successfully saved."), $vars->file);
        } catch (Horde_Vfs_Exception $e) {
            $message = sprintf(_("Access denied to %s"), $vars->file);
        }
        echo Horde::wrapInlineScript([
            'alert(' . Horde_Serialize::serialize($message, Horde_Serialize::JSON) . ')',
        ]);
        break;

    case 'edit_file':
        try {
            $data = $injector
                ->getInstance('Gollem_Vfs')
                ->read($vars->dir, $vars->file);
        } catch (Horde_Vfs_Exception $e) {
            echo Horde::wrapInlineScript([
                'alert(' . Horde_Serialize::serialize(sprintf(_("Access denied to %s"), $vars->file), Horde_Serialize::JSON) . ')',
            ]);
            break;
        }

        $mime_type = Horde_Mime_Magic::extToMIME($vars->type);
        if (strpos($mime_type, 'text/') !== 0) {
            break;
        }

        if ($mime_type == 'text/html') {
            $injector->getInstance('Horde_Editor')->initialize(['id' => 'content']);
        }

        $view = $injector->createInstance('Horde_View');
        $view->self_url = Horde::url('edit.php');
        $view->forminput = Util::formInput();
        $view->vars = $vars;
        $view->data = $data;

        $page_output->addScriptFile('edit.js');
        $page_output->topbar = $page_output->sidebar = false;

        $page_output->header([
            'title' => $title,
        ]);
        $notification->notify(['listeners' => 'status']);
        echo $view->render('edit');
        $page_output->footer();
        exit;
}

echo Horde::wrapInlineScript(['window.close()']);
