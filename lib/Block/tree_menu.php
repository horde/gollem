<?php

$block_name = _("Menu List");
$block_type = 'tree';

/**
 * Gollem tree block.
 *
 * $Horde: gollem/lib/Block/tree_menu.php,v 1.13 2009/06/10 03:46:18 chuck Exp $
 *
 * Copyright 2005-2009 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (GPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/gpl.html.
 *
 * @author  Michael Slusarz <slusarz@horde.org>
 * @package Gollem
 */
class Horde_Block_gollem_tree_menu extends Horde_Block {

    var $_app = 'gollem';

    function _buildTree(&$tree, $indent = 0, $parent = null)
    {
        $GLOBALS['gollem_authentication'] = 'none';
        require_once dirname(__FILE__) . '/../base.php';

        return;

        // TODO

        $icondir = $GLOBALS['registry']->getImageDir();
        $login_url = Horde::applicationUrl('login.php');

        foreach ($GLOBALS['gollem_backends'] as $key => $val) {
            if (Gollem::checkPermissions('backend', PERMS_SHOW, $key)) {
                $tree->addNode($parent . $key,
                               $parent,
                               $val['name'],
                               $indent + 1,
                               false,
                               array('icon' => 'gollem.png',
                                     'icondir' => $icondir,
                                     'url' => Horde_Util::addParameter($login_url, array('backend_key' => $key, 'change_backend' => 1))));
            }
        }
    }

}
