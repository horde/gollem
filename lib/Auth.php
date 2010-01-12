<?php
/**
 * The Gollem_Auth:: class provides a Gollem implementation of the Horde
 * authentication system.
 *
 * Required parameters: None
 * Optional parameters: None
 *
 * Copyright 2004-2010 The Horde Project (http://www.horde.org/)
 *
 * See the enclosed file COPYING for license information (LGPL). If you
 * did not receive this file, see http://www.fsf.org/copyleft/lgpl.html.
 *
 * @author  Michael Slusarz <slusarz@curecanti.org>
 * @package Gollem
 */
class Gollem_Auth
{
    /**
     * Find out if a set of login credentials are valid, and if
     * requested, mark the user as logged in in the current session.
     *
     * @param string $userID      The userID to check.
     * @param array $credentials  The credentials to check.
     * @param boolean $login      Whether to log the user in. If false, we'll
     *                            only test the credentials and won't modify
     *                            the current session.
     *
     * @return boolean  Whether or not the credentials are valid.
     */
    public function authenticate($userID = null, $credentials = array(),
                                 $login = false)
    {
        // Check for for hordeauth.
        if (empty($_SESSION['gollem']['backend_key'])) {
            if (Gollem::canAutoLogin()) {
                $backend_key = Gollem::getPreferredBackend();

                $ptr = &$GLOBALS['gollem_backends'][$backend_key];
                if (!empty($ptr['hordeauth'])) {
                    $user = Gollem::getAutologinID($backend_key);
                    $pass = Horde_Auth::getCredential('password');

                    if (Gollem_Session::createSession($backend_key, $user, $pass)) {
                        $entry = sprintf('Login success for %s [%s] to {%s}',
                                         $user, $_SERVER['REMOTE_ADDR'],
                                         $backend_key);
                        Horde::logMessage($entry, __FILE__, __LINE__,
                                          PEAR_LOG_NOTICE);
                        return true;
                    }
                }
            }
        }

        if (empty($userID) &&
            !empty($GLOBALS['gollem_be']['params']['username'])) {
            $userID = $GLOBALS['gollem_be']['params']['username'];
        }

        if (empty($credentials) &&
            !empty($GLOBALS['gollem_be']['params']['password'])) {
            $credentials = array('password' => Horde_Secret::read(Horde_Secret::getKey('gollem'), $GLOBALS['gollem_be']['params']['password']));
        }

        $login = ($login && (Horde_Auth::getProvider() == 'gollem'));

        return parent::authenticate($userID, $credentials, $login);
    }

    /**
     * Find out if a set of login credentials are valid.
     *
     * @param string $userID      The userID to check.
     * @param array $credentials  An array of login credentials.
     *
     * @throw Horde_Exception
     */
    protected function _authenticate($userID, $credentials)
    {
        if (!(isset($_SESSION['gollem']) && is_array($_SESSION['gollem']))) {
            if (isset($GLOBALS['prefs'])) {
                $GLOBALS['prefs']->cleanup(true);
            }
            throw new Horde_Exception('', Horde_Auth::REASON_SESSION);
        }

        // Allocate a global VFS object
        $GLOBALS['gollem_vfs'] = Gollem::getVFSOb($_SESSION['gollem']['backend_key']);
        if (is_a($GLOBALS['gollem_vfs'], 'PEAR_Error')) {
            Horde::fatal($GLOBALS['gollem_vfs']);
        }

        $valid = $GLOBALS['gollem_vfs']->checkCredentials();
        if ($valid instanceof PEAR_Error) {
            $msg = $valid->getMessage();
            if (empty($msg)) {
                throw new Horde_Exception('', Horde_Auth::REASON_FAILED);
            }

            throw new Horde_Exception($msg);
        }
    }

    /**
     * Check Gollem authentication and change to the currently active
     * directory. Redirects to login page on authentication/session failure.
     *
     * @param string $mode       The authentication mode we are using.
     * @param boolean $redirect  Redirect to the logout page if authentication
     *                           is unsuccessful?
     *
     * @return boolean  True on success, false on failure.
     */
    static public function checkAuthentication($mode = null, $redirect = true)
    {
        $auth_gollem = new Gollem_Auth();
        $reason = $auth_gollem->authenticate();

        if ($reason !== true) {
            if ($redirect) {
                if ($mode = 'selectlist') {
                    $url = Horde_Util::addParameter($GLOBALS['registry']->get('webroot', 'gollem') . '/login.php', 'selectlist_login', 1, false);
                } else {
                    $url = Horde_Auth::addLogoutParameters(self::logoutUrl());
                }
                $url = Horde_Util::addParameter($url, 'url', Horde::selfUrl(true, true, true), false);
                header('Location: ' . $url);
                exit;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Can we log in without a login screen for the requested backend key?
     *
     * @param string $key     The backend key to check. Defaults to
     *                        self::getPreferredBackend().
     * @param boolean $force  If true, check the backend key even if there is
     *                        more than one backend.
     *
     * @return boolean  True if autologin possible, false if not.
     */
    static public function canAutoLogin($key = null, $force = false)
    {
        $auto_server = self::getPreferredBackend();
        if ($key === null) {
            $key = $auto_server;
        }

        return (((count($auto_server) == 1) || $force) &&
                Horde_Auth::getAuth() &&
                empty($GLOBALS['gollem_backends'][$key]['loginparams']) &&
                !empty($GLOBALS['gollem_backends'][$key]['hordeauth']));
    }

    /**
     * Take information posted from a login attempt and try setting up
     * an initial Gollem session. Handle Horde authentication, if
     * required, and only do enough work to see if the user can log
     * in. This function should only be called once, when the user first logs
     * into Gollem.
     *
     * Creates the $gollem session variable with the following entries:
     * 'backend_key' --  The current backend
     * 'be_list'     --  The cached list of available backends
     * 'selectlist'  --  Stores file selections from the API call
     *
     * Each backend is stored by its name in the 'backends' array.  Each
     * backend contains the following entries:
     * 'attributes'  --  See config/backends.php
     * 'autologin'   --  Whether this backend supports autologin
     * 'clipboard'   --  The clipboard for the current backend
     * 'createhome'  --  See config/backends.php
     * 'dir'         --  The current directory
     * 'driver'      --  See config/backends.php
     * 'filter'      --  See config/backends.php
     * 'home'        --  The user's home directory
     * 'hordeauth'   --  See config/backends.php
     * 'hostspec'    --  See config/backends.php
     * 'label'       --  The label to use
     * 'name'        --  See config/backends.php
     * 'params'      --  See config/backends.php
     * 'preferred'   --  See config/backends.php
     * 'root'        --  The root directory
     *
     * @param string $key   The backend key to initialize.
     * @param string $user  The username to use for authentication.
     * @param string $pass  The password to use for authentication.
     * @param array $args   Any additional parameters the backend needs.
     *
     * @return boolean  True on success, false on failure.
     */
    function createSession($key, $user = null, $pass = null, $args = array())
    {
        global $conf;

        /* Make sure we have a key and that it is valid. */
        if (empty($key) || (substr($key, 0, 1) == '_')) {
            return false;
        }

        /* We might need to override some of the defaults with
         * environment-wide settings. Do NOT use the global $backends
         * variable as it may not exist. */
        require GOLLEM_BASE . '/config/backends.php';
        if (empty($backends[$key])) {
            $entry = sprintf('Invalid server key from client [%s]', $_SERVER['REMOTE_ADDR']);
            Horde::logMessage($entry, __FILE__, __LINE__, PEAR_LOG_INFO);
            return false;
        }

        /* Create gollem session object if it doesn't already exist. */
        if (!isset($_SESSION['gollem'])) {
            $_SESSION['gollem'] = array();
            $_SESSION['gollem']['backends'] = array();
            $_SESSION['gollem']['selectlist'] = array();
        }
        $_SESSION['gollem']['backends'][$key] = $backends[$key];
        $GLOBALS['gollem_be'] = &$_SESSION['gollem']['backends'][$key];
        $ptr = &$_SESSION['gollem']['backends'][$key];
        $ptr['params'] = array_merge($ptr['params'], $args);

        /* Set the current backend as active. */
        $_SESSION['gollem']['backend_key'] = $key;

        /* Set username now. Don't set the current username if the backend
         * already has a username defined. */
        if (empty($ptr['params']['username'])) {
            $ptr['params']['username'] = ($user === null) ? Horde_Auth::getBareAuth() : $user;
        }

        /* Set password now. The password should always be encrypted within
         * the session. */
        if (!empty($ptr['params']['password'])) {
            $pass = $ptr['params']['password'];
        }
        if ($pass === null) {
            $ptr['params']['password'] = null;
        } else {
            $ptr['params']['password'] = Horde_Secret::write(Horde_Secret::getKey('gollem'), $pass);
        }

        /* Try to authenticate with the given information. */
        $auth_gollem = new Gollem_Auth();
        if ($auth_gollem->authenticate(null, null, true) !== true) {
            unset($_SESSION['gollem']['backends'][$key]);
            $_SESSION['gollem']['backend_key'] = null;
            return false;
        }

        // Make sure we have a 'root' parameter.
        if (empty($ptr['root'])) {
            $ptr['root'] = '/';
        }
        $ptr['root'] = Horde_Util::realPath($ptr['root']);

        // Make sure we have a 'home' parameter.
        if (empty($ptr['home'])) {
            $ptr['home'] = (!empty($ptr['params']['home'])) ? $ptr['params']['home'] : $GLOBALS['gollem_vfs']->getCurrentDirectory();
            if (empty($ptr['home'])) {
                $ptr['home'] = $ptr['root'];
            }
        }

        // Make sure the home parameter lives under root if it is a relative
        // directory.
        if (strpos($ptr['home'], '/') !== 0) {
            $ptr['home'] = $ptr['root'] . '/' . $ptr['home'];
        }
        $ptr['home'] = Horde_Util::realPath($ptr['home']);
        $ptr['dir'] = $ptr['home'];

        // Verify that home is below root.
        if (!Gollem::verifyDir($ptr['home'])) {
            $error_msg = 'Backend Configuration Error: Home directory not below root.';
            $auth_gollem->gollemSetAuthErrorMsg($error_msg);
            Horde::logMessage(PEAR::raiseError($error_msg), __FILE__, __LINE__, PEAR_LOG_ERR);
            unset($_SESSION['gollem']['backends'][$key]);
            $_SESSION['gollem']['backend_key'] = null;
            return false;
        }

        /* Create the home directory if it doesn't already exist. */
        if (($ptr['home'] != '/') && !empty($ptr['createhome'])) {
            $pos = strrpos($ptr['home'], '/');
            $cr_dir = substr($ptr['home'], 0, $pos);
            $cr_file = substr($ptr['home'], $pos + 1);
            if (!$GLOBALS['gollem_vfs']->exists($cr_dir, $cr_file)) {
                $res = Gollem::createFolder($cr_dir, $cr_file);
                if (is_a($res, 'PEAR_Error')) {
                    $error_msg = 'Backend Configuration Error: Could not create home directory ' . $ptr['home'] . '.';
                    $auth_gollem->gollemSetAuthErrorMsg($error_msg);
                    Horde::logMessage(PEAR::raiseError($error_msg), __FILE__, __LINE__, PEAR_LOG_ERR);
                    unset($_SESSION['gollem']['backends'][$key]);
                    $_SESSION['gollem']['backend_key'] = null;
                    return false;
                }
            }
        }

        /* Does this driver support autologin? */
        $ptr['autologin'] = Gollem::canAutoLogin(true);

        /* Cache the backend_list in the session. */
        if (empty($_SESSION['gollem']['be_list'])) {
            Gollem::loadBackendList();
            $_SESSION['gollem']['be_list'] = $GLOBALS['gollem_backends'];
        }

        /* Initialize clipboard. */
        if (!isset($_SESSION['gollem']['clipboard'])) {
            $_SESSION['gollem']['clipboard'] = array();
        }

        /* Call Gollem::changeDir() to make sure the label is set. */
        Gollem::changeDir();

        return true;
    }

    /**
     * Change the currently active backend.
     *
     * @param string $key  The ID of the backend to set as active.
     */
    function changeBackend($key)
    {
        $_SESSION['gollem']['backend_key'] = $key;
        $GLOBALS['gollem_be'] = &$_SESSION['gollem']['backends'][$key];
    }

}
