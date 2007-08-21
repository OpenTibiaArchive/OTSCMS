<?php
/*
    This file is part of OTSCMS (http://www.otscms.com/) project.

    Copyright (C) 2005 - 2007 Wrzasq (wrzasq@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
    Main class which handles entire system.
*/

class OTSCMS
{
    // initializes system with given configuration table
    public static function init($config)
    {
        // registers __autoload() handler
        spl_autoload_register( array('OTSCMS', '__autoload') );

        // creates configuration resource
        $config = new DataContainer($config);
        self::setResource('Config', $config);

        // sets default critical exteptions handler
        set_exception_handler( array('OTSCMS', 'exceptionHandler') );

        // sets database driver
        self::setDriver('SQL', $config['db.type']);

        // POT inclusion
        self::setDriver('POT', 'POT/OTS');
    }

    // handles critical exceptions
    public static function exceptionHandler($exception)
    {
        // fatal error, at all only fatals should be catched so far
        // other exceptions will be catched inside try statement and will be displayed in user-friendly site
        die('<pre style="font-weight: bold;">FATAL ERROR: ' . $exception->getMessage() . '</pre>');
    }

    private static $handlers = array();

    // allows adding custom class-families loading handlers
    public static function addAutoloadDriver(AutoloadDriver $driver)
    {
        self::$handlers[] = $driver;
    }

    private static $drivers = array();

    public static function setDriver($class, $driver)
    {
        self::$drivers[$class] = $driver;
    }

    private static $resources = array();

    // stores all system objects in array to give access to it blobaly
    public static function getResource($index)
    {
        return self::$resources[$index];
    }

    public static function setResource($index, &$resource)
    {
        self::$resources[$index] = &$resource;
    }

    // class-oriented __autoload - uses class methods
    public static function __autoload($class)
    {
        $config = self::getResource('Config');

        // default driver
        $file = $class;

        // defined class driver
        if(self::$drivers[$class])
        {
            $file = self::$drivers[$class];
        }
        else
        {
            // checks all drivers handlers if this class matches any of them
            foreach(self::$handlers as $handler)
            {
                if( $handler->match($class) )
                {
                    $file = $handler->$class;
                }
            }
        }

        $file .= '.php';

        // to prevent from ugly error messages from PHP
        if( file_exists($config['directories.classes'] . $file) )
        {
            // loads class driver
            include($config['directories.classes'] . $file);
        }
    }

    // calls single module
    public static function call($module, $command)
    {
        // sets local references to resources
        $db = self::getResource('DB');
        $config = self::getResource('Config');
        $template = self::getResource('Template');
        $language = self::getResource('Language');
        $ots = POT::getInstance();

        // sets displaying configuration
        $config['display'] = array('module' => $module, 'command' => $command);

        // checks if there are access restrictions for given command of module
        $require = new CMS_Access($module . '.' . $command);

        // if no then checks if there are restriction for whole module
        if( empty($require['name']) )
        {
            $require = new CMS_Access($module . '.*');
        }

        // if access restriction founded then checks them
        // otherwise sets required access level to -1 (free accesssable)
        $require = $require['name'] ? $require['content'] : -1;

        // checks access rights
        if( !User::hasAccess($require) )
        {
            throw new NoAccessException();
        }

        // checks if module exists
        if( !file_exists($config['directories.modules'] . $module . '/' . $command . '.php') )
        {
            Toolbox::redirect('/');
        }

        // runs module
        include($config['directories.modules'] . $module . '/' . $command . '.php');
    }

    // runs system
    public static function run()
    {
        // allows to use session and cookies calls without care of output
        ob_start();

        // local reference to system configuration
        $config = self::getResource('Config');

        // database connection
        $db = $config['db'];

        // POT initialization
        $driver = array('MySQL' => POT::DB_MYSQL, 'SQLite' => POT::DB_SQLITE);
        $db['driver'] = $driver[ $db['type'] ];
        $db['prefix'] = $db['ots_prefix'];
        POT::getInstance()->connect(null, $db);

        $db = new SQL($db['host'], $db['user'], $db['password'], $db['database'], $db['cms_prefix'], $db['ots_prefix']);
        self::setResource('DB', $db);

        // loads system configuration
        foreach( $db->query('SELECT `name`, `content` FROM [settings]') as $setting)
        {
            $config[ $setting['name'] ] = $setting['content'];
        }

        // sets current language
        if( InputData::read('language') )
        {
            // on _GET and/or _POST method (support url link and form sending)
            // sets cookie with recived language
            Cookies::write('language', InputData::read('language') );
            // sets session for language
            Session::write('language', InputData::read('language') );
            // sets user configuration
            $config['site.language'] = InputData::read('language');
        }
        elseif( Session::read('language') )
        {
            // if no new language has been send tries to set it up from current session
            $config['site.language'] = Session::read('language');
        }
        elseif( Cookies::read('language') )
        {
            // if there is no session then tries to find cookie on user's computer
            $config['site.language'] = Cookies::read('language');
        }

        // sets current skin
        if( InputData::read('skin') )
        {
            // on _GET and/or _POST method (support url link and form sending)
            // sets cookie with recived skin
            Cookies::write('skin', InputData::read('skin') );
            // sets session for skin
            Session::write('skin', InputData::read('skin') );
            // sets user configuration
            $config['site.skin'] = InputData::read('skin');
        }
        elseif( Session::read('skin') )
        {
            // if no new skin has been send tries to set it up from current session
            $config['site.skin'] = Session::read('skin');
        }
        elseif( Cookies::read('skin') )
        {
            // if there is no session then tries to find cookie on user's computer
            $config['site.skin'] = Cookies::read('skin');
        }

        // handling logging out users
        if( InputData::read('userlogout') )
        {
            Session::unRegister('useraccount', 'userpassword');
            // clears cookies - doesn't matter if user used them
            Cookies::unRegister('useraccount', 'userpassword');
        }

        // handling user session registering
        if( InputData::read('useraccount') && InputData::read('userpassword') )
        {
            Session::write('useraccount', trim( InputData::read('useraccount') ) );
            Session::write('userpassword', $config['system.md5'] ? md5( InputData::read('userpassword') ) : InputData::read('userpassword') );

            // checks if user wants to use cookies for login
            if( InputData::read('usercookies') )
            {
                Cookies::write('useraccount', Session::read('useraccount') );
                Cookies::write('userpassword', Session::read('userpassword') );
            }
        }
        // if no login request then try to re-login from cookies
        elseif( Cookies::read('useraccount') && Cookies::read('userpassword') && !( Session::read('useraccount') && Session::read('userpassword') ))
        {
            Session::write('useraccount', Cookies::read('useraccount') );
            Session::write('userpassword', Cookies::read('userpassword') );
        }

        // interface objects
        $language = new OTSTranslation($config['directories.languages'] . $config['site.language'] . '/');
        self::setResource('Language', $language);
        $template = new OTSTemplate($config['directories.skins'] . $config['site.skin'] . '/');
        self::setResource('Template', $template);

        // main JavaScript interface
        $template->addJavaScript('main');
        $template->addJavaScript('ajax');

        // always used
        $template->addJavaScript('links');
        $template->addJavaScript('online');

        // some basic template varaibles

        // finding all languages that are installed and supported by current template
        $template['languages'] = Toolbox::subDirs($config['directories.languages']);
        // finding all templates
        $template['skins'] = Toolbox::subDirs($config['directories.skins']);

        $temp = array();

        // reads servers online statistics
        foreach( $db->query('SELECT `id`, `name`, `content`, `port`, `maximum` FROM [online]') as $server)
        {
            // saves server record
            $temp[ $server['id'] ] = array('name' => htmlspecialchars($server['name']), 'content' => Toolbox::getPlayersCount( new CMS_Online($server) ) );
        }

        // assingns results to template
        $template['onlines'] = $temp;

        $temp = array();

        // reads links to display
        // we have to make some changes to make sure it will be correctly displayed and won't crash template
        foreach( $db->query('SELECT `id`, `name`, `content` FROM [links]') as $link)
        {
            $temp[ $link['id'] ] = array('content' => $link['content'], 'name' => $link['name']);
        }

        // assigns links to template
        $template['links'] = $temp;

        try
        {
            // checks IP banishment
            $ban = $db->query('SELECT COUNT(`type`) AS `count` FROM {bans} WHERE `ip` & `mask` = ' . Toolbox::ip2long($_SERVER['REMOTE_ADDR']) . ' & `mask` AND (`time` > ' . time() . ' OR `time` = 0) AND `type` = 1')->fetch();

            if($ban['count'] > 0)
            {
                // to skip to the end
                throw new HandledException('IPBan');
            }

            // handling user logging in
            if( Session::read('useraccount') && Session::read('userpassword') )
            {
                // tries to login user
                try
                {
                    // checks account ban
                    $ban = $db->query('SELECT COUNT(`type`) AS `count` FROM {bans} WHERE `account` = ' . (int) $session->useraccount . ' AND (`time` > ' . time() . ' OR `time` = 0) AND `type` = 3')->fetch();

                    if($ban['count'] > 0)
                    {
                        throw new HandledException('AccountBlocked');
                    }

                    User::login( Session::read('useraccount'), Session::read('userpassword') );
                }
                catch(Exception $error)
                {
                    // clears session as it could cause this error every time
                    Session::unRegister('useraccount', 'userpassword');
                    // clears cookies - doesn't matter if user used them as we dont know that
                    Cookies::unRegister('useraccount', 'userpassword');

                    // throw it away
                    throw $error;
                }
            }

            // loads user's forum stuff
            if(User::$logged)
            {
                $unread = $db->query('SELECT COUNT([pms].`id`) AS `count` FROM [pms], {players} WHERE [pms].`to` = {players}.`id` AND [pms].`read` = 0 AND {players}.`account_id` = ' . User::$number)->fetch();
                $template['unread'] = $unread['count'];
            }

            // loads startup module
            self::call( InputData::read('module'), InputData::read('command') );
        }
        // access deny exception
        catch(NoAccessException $error)
        {
            // not-logged users may just need to log-in to receive access
            if(!User::$logged)
            {
                Toolbox::redirect('/account');
            }

            // logs exception
            $log = new CMS_Log();
            $log['name'] = '#NoAccess';
            $log['content'] = Toolbox::ip2long($_SERVER['REMOTE_ADDR']);
            $log['date_time'] = time();
            $log->save();

            $message = $template->createComponent('Message');
            $message['message'] = $language['Modules.error.NoAccess'];

            $config['display'] = array('module' => 'error', 'command' => 'NoAccess');
        }
        // system took care about exception already, don't need to worry about it
        catch(HandledException $error)
        {
            // logs exception
            $log = new CMS_Log();
            $log['name'] = $error->label;
            $log['content'] = Toolbox::ip2long($_SERVER['REMOTE_ADDR']);
            $log['date_time'] = time();
            $log->save();

            $message = $template->createComponent('Message');
            $message['message'] = $language['Modules.error.' . $error->label];

            $config['display'] = array('module' => 'error', 'command' => $error->label);
        }
        // SQL debug
        catch(PDOException $error)
        {
            $message = $template->createComponent('Message');
            $message['message'] = $language['Templates.sqlDebug.text'];
            $message['debug'] = $error->errorInfo[2];
            $message['place'] = $language['Templates.error.file'] . ' ' . $error->getFile() . ' ' . $language['Templates.error.line'] . ' ' . $error->getLine();

            $config['display'] = array('module' => 'error', 'command' => 'PDOException');
        }
        // general error
        catch(Exception $error)
        {
            $message = $template->createComponent('Message');
            $message['message'] = $language['Templates.error.text'] . ': ' . $error->getMessage() . ' (' . get_class($error) . ').';
            $message['place'] = $language['Templates.error.file'] . ' ' . $error->getFile() . ' ' . $language['Templates.error.line'] . ' ' . $error->getLine();

            $config['display'] = array('module' => 'error', 'command' => get_class($error) );
        }

        // outputs webste
        $template->display($config['display.module'], $config['display.command']);

        ob_end_flush();
    }
}

/*
    Class for handling dot-separated paths in data collections.
*/

class DataContainer implements ArrayAccess
{
    // data in container
    protected $data = array();

    // by default it will create empty container
    public function __construct($data = array() )
    {
        $this->data = $data;
    }

    // reads value
    public function __get($key)
    {
        // with references we won't have E_NOTICE
        $data = &$this->data;

        // reads each key step-by-step
        // for example root.foo.bar into root -> foo -> bar
        foreach( explode('.', $key) as $arg)
        {
            // we need to use new variable to save reference
            // simple $data = &$data[$arg] would break entire table
            $temp = &$data;
            unset($data);

            $data = &$temp[$arg];
            unset($temp);
        }

        // returns final field - note that it still can be a table
        // for example if there is root.foo.bar then root.foo would return a table with bar field
        return $data;
    }

    // writes value - similar to __get(), but here in final we sets value instead of returning it
    public function __set($key, $value)
    {
        $data = &$this->data;

        foreach( explode('.', $key) as $arg)
        {
            $temp = &$data;
            unset($data);

            $data = &$temp[$arg];
            unset($temp);
        }

        $data = $value;
    }

    // isset() and unset() wrappers
    public function __isset($key)
    {
        $data = $this->data;

        foreach( explode('.', $key) as $arg)
        {
            // checks if current path exists
            if( !isset($data[$arg]) )
            {
                return false;
            }

            // moves to next step
            $data = $data[$arg];
        }

        return true;
    }

    public function __unset($key)
    {
        $data = &$this->data;

        foreach( explode('.', $name) as $arg)
        {
            // saves previous step and moves to next one
            unset($prev);
            $prev = &$data;
            $data = &$prev[$arg];
        }

        // clears entry
        unset($prev[$arg]);
    }

    // ArrayAccess interface

    public function offsetExists($name)
    {
        return $this->__isset($name);
    }

    public function offsetGet($name)
    {
        return $this->__get($name);
    }

    public function offsetSet($name, $value)
    {
        $this->__set($name, $value);
    }

    public function offsetUnset($name)
    {
        $this->__unset($name);
    }
}

// initializes system with default configuration
OTSCMS::init($config);

?>
