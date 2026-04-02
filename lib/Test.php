<?php

/**
 * This class provides the Gollem configuration for the test script.
 *
 * Copyright 2010-2026 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (GPL). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl.
 *
 * @author  Michael Slusarz <slusarz@horde.org>
 * @package Gollem
 * @coversNothing
 */
class Gollem_Test extends Horde_Test
{
    /**
     * The module list
     *
     * @var array
     */
    protected $_moduleList = [
        'ftp' => [
            'descrip' => 'FTP Support',
            'error' => 'You need FTP support compiled into PHP if you plan to use the FTP VFS driver (see config/backends.php).',
        ],
        'ssh2' => [
            'descrip' => 'SSH2 Support',
            'error' => 'You need the SSH2 PECL module if you plan to use the SSH2 VFS driver (see config/backends.php).',
        ],
    ];

    /**
     * PHP settings list.
     *
     * @var array
     */
    protected $_settingsList = [];

    /**
     * PEAR modules list.
     *
     * @var array
     */
    protected $_pearList = [];

    /**
     * Inter-Horde application dependencies.
     *
     * @var array
     */
    protected $_appList = [];

    /**
     */
    public function __construct()
    {
        parent::__construct();

        $this->_fileList += [
            'config/backends.php' => null,
            'config/prefs.php' => null,
        ];
    }

    /**
     * Any application specific tests that need to be done.
     *
     * @return string  HTML output.
     */
    public function appTests() {}

}
