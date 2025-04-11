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
 * Defines AJAX actions used in the Gollem smartmobile view.
 *
 * @author   Heinz Schweiger <heinz@htl-steyr.ac.at>
 * @category Horde
 * @license  http://www.horde.org/licenses/apache ASL
 * @package  Ingo
 */
class Gollem_Ajax_Application_Smartmobile extends Horde_Core_Ajax_Application_Handler
{
    /**
     * AJAX action: Get folder data.
     *
     * Variables used:
     *   - backend_key: (string) Key of backend
     *   - dir: (string) Directory to list
     *
     * @return object  An object with the following properties:
     *   - backendName: (string) Backend name.
     *   - folderlist: (array) Folders to display.
     *                 'l' => (string) label.
     *                 'e' => (array) entries.
     *                        'n' => name of subfolder.
     *                        'u' => url to change into subfolder.
     *   - filelist: (array) Files in folder.
     *               'l' => (string) label.
     *               'e' => (array) entries.
     *                         'n' => name of file.
     *                         'i' => icon image of filetype.
     *                         'u' => url to download file.
     *                         'd' => unix timestamp of filedate.
     */
    public function smartmobileFolderContent()
    {
        global $injector;

        $backend_key = $this->vars->get('backend_key');
        $dir = $this->vars->get('dir');

        if ($dir) Gollem::$backend['dir'] = $dir;
        Gollem::changeDir();

        $GLOBALS['prefs']->setValue('sortby', Gollem::SORT_NAME);
        $list = Gollem::listFolder(Gollem::$backend['dir']);

        $out = new stdClass();
        $out->backendName = Gollem::$backend['name'];

        $out->folderlist = array(
            'l' => _("Subfolders of ") . Gollem::$backend['dir'],
            'e' => array()
        );

        $out->filelist = array(
            'l' => _("Files of ") . Gollem::$backend['dir'],
            'e' => array()
        );

        $folder_url = new Horde_Core_Smartmobile_Url();
        $folder_url->setAnchor('folder');
        $folder_url->add('backend_key', $backend_key);

        $view_url = Horde::url('view.php');

        foreach ($list as $val) {
            switch ($val['type']) {
                case '**dir':
                    $out->folderlist['e'][] = array(
                        'n' => $val['name'],
                        'u' => strval($folder_url->copy()->setRaw(true)->add(array('dir' => Gollem::subdirectory(Gollem::$backend['dir'], $val['name'])
                        )))
                    );
                    break;

                default:
                    if (empty($icon_cache[$val['type']])) {
                        $icon_cache[$val['type']] = Horde::img($injector->getInstance('Horde_Core_Factory_MimeViewer')->getIcon(Horde_Mime_Magic::extToMime($val['type'])), '', '', '');
                    }
                    $icon = $icon_cache[$val['type']];

                    // Try a view link.
                    $url = $view_url->copy()->add(array(
                        'type' => $val['type'],
                        'file' => $val['name'],
                        'dir' => Gollem::$backend['dir'],
                        'driver' => Gollem::$backend['driver']
                    ));
                    $out->filelist['e'][] = array(
                        'n' => $val['name'] . ' (' . Gollem::formatFileSize($val['size']) . ')',
                        'i' => $icon,
                        'u' => strval($url->setRaw(true)),
                        'd' => strftime($GLOBALS['prefs']->getValue('date_format_mini'), $val['date'])
                    );
                    break;
            }
        }

        return $out;
    }

}
