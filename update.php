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

// loads system configuration
include('config.php');

// loads system core
include($config['directories']['classes'] . 'OTSCMS.php');

$config = OTSCMS::getResource('Config');

// database connection
$db = $config['db'];
$db = new SQL($db['host'], $db['user'], $db['password'], $db['database'], $db['cms_prefix'], $db['ots_prefix']);
OTSCMS::setResource('DB', $db);

// loads system configuration
foreach( $db->query('SELECT `name`, `content` FROM [settings]') as $setting)
{
    $config[ $setting['name'] ] = $setting['content'];
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

// interface objects
$language = new OTSTranslation($config['directories.languages'] . $config['site.language'] . '/');
OTSCMS::setResource('Language', $language);
$template = new OTSTemplate($config['directories.skins'] . $config['site.skin'] . '/');
OTSCMS::setResource('Template', $template);

$template['languages'] = array();
$template['skins'] = array();
$template['links'] = array();
$template['onlines'] = array();

// execute update
switch($config['version'])
{
    // only 3.0.0 didn't have version config
    default:
        // usefull trick from http://www.perturb.org/display/entry/645/ to evaluate ALTER TABLE DROP on SQLite
        $db->exec('CREATE TEMPORARY TABLE [settings_temporary] (`name` VARCHAR(255), `content` TEXT, UNIQUE(`name`) )');
        $db->exec('INSERT INTO [settings_temporary] SELECT `name`, `content` FROM [settings]');
        $db->exec('DROP TABLE [settings]');
        $db->exec('CREATE TABLE [settings] (`name` VARCHAR(255), `content` TEXT, UNIQUE(`name`) )');
        $db->exec('INSERT INTO [settings] SELECT `name`, `content` FROM [settings_temporary]');
        $db->exec('DROP TABLE [settings_temporary]');

        $query = $db->prepare('INSERT INTO [settings] (`name`, `content`) VALUES (:name, :content)');
        $query->execute( array(':name' => 'version', ':content' => '3.0.1') );
        $query->execute( array(':name' => 'mail.from', ':content' => 'you@example.com') );
        $query->execute( array(':name' => 'mail.smtp.host', ':content' => 'localhost') );
        $query->execute( array(':name' => 'mail.smtp.port', ':content' => '25') );
        $query->execute( array(':name' => 'mail.smtp.use_auth', ':content' => '0') );
        $query->execute( array(':name' => 'mail.smtp.user', ':content' => '') );
        $query->execute( array(':name' => 'mail.smtp.password', ':content' => '') );
        $query->execute( array(':name' => 'site.home', ':content' => file_get_contents('__home') ) );
        unlink($config['directories.modules'] . 'Account/password.php');
        unlink('__home');

    // 3.0.1 and up
    case '3.0.1':
        // session now use same prefix as cookies
        $db->exec('DELETE FROM [settings] WHERE `name` = \'session.prefix\'');

        // updates system version
        $db->exec('UPDATE [settings] SET `content` = \'3.0.2\' WHERE `name` = \'version\'');

        // deletes User module
        $dir = new DirectoryIterator($config['directories.modules'] . 'User');
        foreach($dir as $file)
        {
            // finding all subdirectories except . and .. symbols
            if( !$dir->isDot() )
            {
                unlink($config['directories.modules'] . 'User/' . $dir->getFilename() );
            }
        }

        rmdir($config['directories.modules'] . 'User');
        $db->exec('DELETE FROM [access] WHERE `name` LIKE \'User.%\'');

        foreach( Toolbox::subDirs($config['directories.languages']) as $language)
        {
            unlink($config['directories.languages'] . '/' . $langauge . '/Modules/User.php');
        }

        // new Account module command
        $db->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Account.save\', 0)');

    // 3.0.2 and up
    case '3.0.2':
        // updates system version
        $db->exec('UPDATE [settings] SET `content` = \'3.0.3\' WHERE `name` = \'version\'');

    // 3.0.3 and up
    case '3.0.3':
        // updates system version
        $db->exec('UPDATE [settings] SET `content` = \'3.0.4\' WHERE `name` = \'version\'');

        // characters limit
        $db->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.account_limit\', \'5\')');

    // 3.0.4 and up
    case '3.0.4':
        // updates system version
        $db->exec('UPDATE [settings] SET `content` = \'3.0.5\' WHERE `name` = \'version\'');

    // 3.0.5 and up
    case '3.0.5':
        // updates system version
        $db->exec('UPDATE [settings] SET `content` = \'3.1.0\' WHERE `name` = \'version\'');

        // changes directories paths
        $query = $db->prepare('UPDATE [settings] SET `content` = :content WHERE `name` = :name');
        $query->execute( array(':name' => 'directories.languages', ':content' => 'languages/') );
        $query->execute( array(':name' => 'directories.modules', ':content' => 'modules/') );
        $query->execute( array(':name' => 'directories.skins', ':content' => 'skins/') );
        $query->execute( array(':name' => 'directories.images', ':content' => 'images/') );

        // rename directories
        rename('lng', 'languages');
        rename('mod', 'modules');
        rename('lay', 'skins');
        rename('img', 'images');
        rename('cls', 'classes');

        // overwrite configuration file
        file_put_contents('config.php', str_replace($config['directories.classes'], 'classes/', file_get_contents('config.php') ) );

        // runtime configuration change

        $config['directories.classes'] = 'classes/';
        $config['directories.skins'] = 'skins/';
        $config['directories.languages'] = 'languages/';

        OTSCMS::init( array('version' => $config['version'], 'directories' => $config['directories'], 'system' => $config['system'], 'site' => $config['site']) );

        $config = OTSCMS::getResource('Config');

        $language = new OTSTranslation($config['directories.languages'] . $config['site.language'] . '/');
        OTSCMS::setResource('Language', $language);
        $template = new OTSTemplate($config['directories.skins'] . $config['site.skin'] . '/');
        OTSCMS::setResource('Template', $template);

        $template['languages'] = array();
        $template['skins'] = array();
        $template['links'] = array();
        $template['onlines'] = array();
}

$raw = $template->createComponent('RAW');
$raw['content'] = '<p>System updated.</p>';

// outputs update results
$template->display('Install', 'update');

?>
