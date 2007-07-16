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

// checks PHP version
$php = explode('.', PHP_VERSION);
if($php[0] < 5 || $php[1] < 2)
{
    die('<b>! Critical: you have to install PHP version <i>5.2</i> or newer.</b>');
}

// checks if mgaic quotes are turned off
if( get_magic_quotes_gpc() )
{
    die('<b>! Critical: turn off <i>magic_quotes_gpc</i> in your PHP configuration.</b>');
}

// checks if register globals is disabled
if( ini_get('register_globals') )
{
    die('<b>! Critical: turn off <i>register_globals</i> in your PHP configuration.</b>');
}

// checks if SPL is enabled
if( !extension_loaded('SPL') )
{
    die('<b>! Critical: you have to install <i>SPL</i> (Standard PHP Library) extension.</b>');
}

// checks if PDO is enabled
if( !extension_loaded('PDO') )
{
    die('<b>! Critical: you have to install <i>PDO</i> (PHP Data Objects) extension.</b>');
}

// checks if GD is enabled
if( !extension_loaded('gd') )
{
    die('<b>! Critical: you have to install <i>GD</i> (imaging library) extension.</b>');
}

// checks if PCRE is enabled
if( !extension_loaded('pcre') )
{
    die('<b>! Critical: you have to install <i>PCRE</i> (Perl Compatible Regular Expression) extension.</b>');
}

if( !file_exists('config.php') )
{
    // we cann't do anything without configuration
    die('<b>! Critical: could not load configuration file.</b>');
}

// checks if system has already been installed
if( md5_file('config.php') != '88b5cf84c95b683c0a5ebe07407b81ef')
{
    die('<b>! Critical: you already installed system once - in order to reinstall you need to copy clean <i>config.php</i> file from <i>OTSCMS Lite</i> package.</b>');
}

// checking if config.php file is writable
if( !is_writable('config.php') )
{
    die('<b>! Critical: configuration file (<i>config.php</i>) is not writable. Please change it\'s permissions.</b>');
}

// this constant lets script to include config.php
// after making it has protection frum running
define('OTSCMS_INSTALL', '1');

// loads basic configuration from config.php
include('config.php');

$command = $_REQUEST['command'];

// installation output will look more... readable ;-)
echo '<tt>'."\n";

echo '+ Configuration file loaded<br/>'."\n";

// checks if user posted a configuraiton command
if( isset($command) && $command == 'install')
{
    // creates index.php files inside all sub directories
    function indexDirectory($destination)
    {
        // full index.php file path
        $file = $destination.'index.php';

        // saves index.php file, but not everwrites existing one
        if( !file_exists($file) )
        {
            // saves file
            echo '@ Creating : '.$file.'<br/>'."\n";
            file_put_contents($file, '<?php

// redirects to upper-directory

header(\'Location: ../\');

?>');
        }
        else
        {
            echo '@ '.$file.' already exists<br/>'."\n";
        }

        // indexes also all sub-directories
        $dir = opendir($destination);
        while($current = readdir($dir) )
        {
            // checks if those are directories, but not symbols of upper directory
            if( is_dir($destination.$current) && $current != '.' && $current != '..')
            {
                // if it's a directory then create index.php file there too
                indexDirectory($destination.$current.'/');
            }
        }
    }

    // deletes a directory reccurently
    function deleteDirectory($source)
    {
        echo '@ Deleting: '.$source.'<br/>'."\n";

        // copies all source directory files and sub-directories
        $dir = opendir($source);
        while($new = readdir($dir) )
        {
            // checks if those are not symbols of upper directory
            if($new != '.' && $new != '..')
            {
                // if it's a directory the remove it reccurently too
                if( is_dir($source.$new) )
                {
                    if( !deleteDirectory($source.$new.'/') )
                    {
                        echo '- Could not delete directory: '.$source.$new.'/<br/>'."\n";
                        return false;
                    }
                }
                // otherwise jsut copy a file
                else
                {
                    if( !@unlink($source.$new) )
                    {
                        echo '- Could not delete file: '.$source.$new.'<br/>'."\n";
                        return false;
                    }
                    echo '@ Deleted file: '.$source.$new.'<br/>'."\n";
                }
            }
        }

        // creates destination directory
        rmdir($source);
        echo '@ Deleted directory: '.$source.'<br/>'."\n";

        // returns that directory coping succeeded
        return true;
    }

    // pre-loads HTTP data
    $db = $_POST['db'];

    // checks if PDO driver is installed
    if( !extension_loaded( strtolower('pdo_' . $db['type']) ) )
    {
        die('You have to install ' . $db['type'] . ' driver for PDO.');
    }

    // install scheme
    $install = file_get_contents('Install/' . $db['type'] . '.sql');

    // adds OTSCMS classes directory to includes direcotry
    set_include_path( get_include_path() . PATH_SEPARATOR . $config['directories']['classes']);

    // loads database handler file
    $config['db'] = $db;
    require_once('OTSCMS.php');
    $sql = new SQL($db['host'], $db['user'], $db['password'], $db['database'], $db['cms_prefix'], $db['ots_prefix']);

    // current query
    $step = 0;
    $last = 0;

    // installs database scheme
    for($i = 0; $i < strlen($install); $i++)
    {
        // one level deeper
        if( strtoupper( substr($install, $i, 5) ) == 'BEGIN')
        {
            $step++;
            $i += 4;
        }

        // one level finished
        if( strtoupper( substr($install, $i, 4) ) == 'END;')
        {
            $step--;
            $i += 2;
        }

        // checks if current query is finished
        if(!$step && $install[$i] == ';')
        {
            // reads last query and moves pointer after it
            $query = trim( substr($install, $last, $i - $last) );
            $last = $i + 1;

            try
            {
                $sql->exec($query);
            }
            catch(PDOException $e)
            {
                // ignore DROP queries and ALTER queries on existing collumns
                if( strtoupper( substr($query, 0, 4) ) != 'DROP' && !( strtoupper( substr($query, 0, 5) ) == 'ALTER' && stripos( $e->getMessage(), 'duplicate column name') !== false))
                {
                    echo '- Error during executing SQL query: ' . $query . ' returned ' . $e . '<br/>'."\n";
                }
            }
        }
    }

    echo '+ Database scheme installed<br/>'."\n";

    try
    {
        // inserts database startup content

        $sql->beginTransaction();

        $insert = $sql->prepare('INSERT INTO [settings] (`name`, `content`) VALUES (:name, :content)');

        $insert->execute( array(':name' => 'version', ':content' => '3.1.0') );
        $insert->execute( array(':name' => 'directories.languages', ':content' => $config['directories']['languages']) );
        $insert->execute( array(':name' => 'directories.modules', ':content' => $config['directories']['modules']) );
        $insert->execute( array(':name' => 'directories.skins', ':content' => $config['directories']['skins']) );
        $insert->execute( array(':name' => 'directories.images', ':content' => $config['directories']['images']) );

        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'directories.data\', \'/path/to/your/otserv/data/\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'cookies.prefix\', \'otscms_\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'cookies.path\', \'/\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'cookies.domain\', \'.example.com\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'cookies.expire\', \'2592000\')');

        $insert->execute( array(':name' => 'system.md5', ':content' => (int) $_POST['uses_md5']) );

        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.use_mail\', \'0\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.nick_length\', \'3\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.default_group\', \'1\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.min_number\', \'0\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.max_number\', \'999999\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.account_limit\', \'5\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.map\', \'map.otbm\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.rook.enabled\', \'0\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.rook.id\', \'0\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.depots.count\', \'2\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.depots.item\', \'2590\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'system.depots.chest\', \'2594\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'statistics.page\', \'30\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'site.language\', \'english\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'site.skin\', \'Default\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'site.title\', \'Powered by OTSCMS 3 - http://www.otscms.com/\')');

        $insert->execute( array(':name' => 'site.home', ':content' => '<p class="justified">
Describe of your site can goes here. You can edit this text in administration panel.
</p>

<p class="justified">
There are many people who contributed with our team in developing this project.
</p>

<p class="justified">
Mainly great thans to <b>Foziw</b> for the domain, <b>Invincible AznX</b> for hosting and <b>Yorick</b> for forum!
</p>

<h3>OTSCMS Dev-Team members</h3>

<ul>
<li><b>Wrzasq</b> - Project headmaster, engine, porting and developing, <i>Default</i> skin, website.</li>
</ul>

<h3>OTSCMS version 3 developement</h3>

<ul>
<li><b>Foziw</b> - Project supporter, beta tester, bug finder.</li>
<li><b>Invincible Aznx</b> - Hosting (<a href="http://www.invinciblehost.com/">http://www.invinciblehost.com/</a>).</li>
<li><b>Brave</b> - Helps with "<i>Players Online</i>" script.</li>
<li><b>Chester</b> - Website design.</li>
<li><b>Yorick</b> - Advertising, forum.</li>
<li><b>LooSik</b> - Helps with design.</li>
<li><b>Katten</b> - Code for XML output formating.</li>
<li><b>Ada GT</b> - For help in hard moment by reminding rule "if you don\'t understant, then fuck it" :P.</li>
<li><b>Reks</b> - Beta tester.</li>
<li><b>Winghawk</b> - Beta tester.</li>
<li><b>Slash X</b> - Beta tester.</li>
<li><b>Empty Flask</b> - Beta tester.</li>
<li><b>Rupo</b> - Beta tester.</li>
<li><b>Master-m</b> - Beta tester.</li>
<li><b>Mistik</b> - Beta tester.</li>
<li><b>Morpheus</b> - Beta tester.</li>
<li><b>Critias</b> - Beta tester.</li>
</ul>

<h3>OTSCMS version 2 developement</h3>

<ul>
<li><b>Jascman</b> - Some bugfixes.</li>
<li><b>Gabbe</b> - Helps with fixing bugs.</li>
<li><b>GregStar</b> - Critical bug reported and helped fixing it.</li>
</ul>

<h3>OTSCMS version 1 developement</h3>

<ul>
<li><b>Ashganek</b> - Old forum.</li>
<li><b>GriZzmO</b> - Helps with MySQL driver for OTServ database.</li>
<li><b>Erl</b> - Dutch translation.</li>
<li><b>Nuker</b> - Dutch translation.</li>
<li><b>Crismac</b> - Spanish (Chile) translation.</li>
<li><b>Tiago Martines</b> - Portuguese (Brazil) translation.</li>
<li><b>K-Zodron</b> - Swedish translation.</li>
<li><b>Rasmus</b> - English translation fixes.</li>
<li><b>Escman</b> - Portuguese translation fixes.</li>
<li><b>Anarkus Furi</b> - Some portuguese translation fixes.</li>
<li><b>Mroczny_Mis</b> - Technical consultation.</li>
<li><b>FF Wrapzone</b> - "<i>Default</i>" skin (downloaded from <a href="http://www.freelayouts.com/">http://www.freelayouts.com/</a>).</li>
<li><b>Bryan007</b> - Remote OTSCMS tester.</li>
<li><b>Jason</b> - <i>GenX</i> theme.</li>
<li><b>Mick</b> - Helps with MySQL driver for OTServ database, translation fixes.</li>
<li><b>Adki</b> - Designer, port of <a href="http://webmark.shost.pl/">WebMark</a> <i>Multi CSS</i> skin.</li>
<li><b>Katten</b> - Some bugfixes.</li>
<li><b>Anjek</b> - Portuguese (Brazil) translation.</li>
</ul>

<h3>OTSCMS version 0 developement</h3>

<ul>
<li><b>Maniacx86</b> - some bugfixes.</li>
<li><b>Heepee</b> - some minor bugfixes.</li>
<li><b>Sufixx</b> - tester, bug finder.</li>
<li><b>Tibiaboy15</b> - old translation into Dutch.</li>
<li><b>Rozek</b> - technical consultation.</li>
</ul>') );

        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'site.date_format\', \'j F Y G:i\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'site.news_limit\', \'5\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'gallery.mini_x\', \'100\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'gallery.mini_y\', \'100\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'forum.limit\', \'20\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'forum.avatar.max_x\', \'80\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'forum.avatar.max_y\', \'80\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'mail.from\', \'you@example.com\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'mail.smtp.host\', \'localhost\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'mail.smtp.port\', \'25\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'mail.smtp.use_auth\', \'0\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'mail.smtp.user\', \'\')');
        $sql->exec('INSERT INTO [settings] (`name`, `content`) VALUES (\'mail.smtp.password\', \'\')');

        $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'OTSCMS\', \'http://www.otscms.com/\')');
        $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'OTFans\', \'http://www.otfans.net/\')');
        $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'ToDlaCiebie.net\', \'http://www.wrzasq.com/\')');
        $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'OTServ AAC\', \'http://www.otserv-aac.info/\')');
        $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'Open Tibia Server List\', \'http://www.otservlist.org/\')');
        $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'OTServ News\', \'http://www.otservnews.org/\')');
        $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'Tibia Fans\', \'http://www.tibiafans.com/\')');
        $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'Invincible Hosting\', \'http://www.invinciblehosting.com/\')');
        $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'Save OTServ\', \'http://www.wrzasq.com/otserv/\')');

        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Access.*\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Account.*\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Account.account\', -1)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Account.change\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Account.create\', -1)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Account.forgot\', -1)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Account.login\', -1)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Account.password\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Account.remind\', -1)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Account.save\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Account.signup\', -1)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Account.suspend\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Character.*\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Character.change\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Character.create\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Character.delete\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Character.display\', -1)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Character.insert\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Character.save\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Download.*\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Download.download\', -1)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Download.list\', -1)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Forum.*\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Forum.board\', -1)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Gallery.edit\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Gallery.insert\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Gallery.remove\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Gallery.update\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Guilds.*\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Guilds.display\', -1)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Guilds.list\', -1)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'IPBan.*\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Links.*\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Logger.*\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'News.edit\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'News.insert\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'News.manage\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'News.remove\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'News.update\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Online.*\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Options.*\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'PM.*\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'PM.manage\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'PM.remove\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Poll.edit\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Poll.insert\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Poll.remove\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Poll.update\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Poll.vote\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Post.*\', 3)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Post.new\', 0)');
        $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Post.view\', -1)');

        $sql->exec('INSERT INTO [download] (`name`, `content`, `binary`, `file`) VALUES (\'OTSCMS Lite\', \'Latest <span style="font-weight: bold;">OTSCMS Lite</span>.<br />
<br />
Please visit <a href="http://www.otscms.com/">http://www.otscms.com/</a>.\', 0, \'http://www.otscms.com/latest.php/lite\')');
        $sql->exec('INSERT INTO [download] (`name`, `content`, `binary`, `file`) VALUES (\'OTSCMS Easy\', \'Latest <span style="font-weight: bold;">OTSCMS Easy</span>.<br />
<br />
Please visit <a href="http://www.otscms.com/">http://www.otscms.com/</a>.\', 0, \'http://www.otscms.com/latest.php/easy\')');

        $sql->exec('INSERT INTO [profiles] (`name`, `health`, `healthmax`, `direction`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `cap`) VALUES (\'*.*\', 250, 250, 0, 0, 30, 50, 20, 40, 0, 0, 0, 0, 0, 220)');
        $sql->exec('INSERT INTO [profiles] (`name`, `looktype`) VALUES (\'0.*\', 136)');
        $sql->exec('INSERT INTO [profiles] (`name`, `looktype`) VALUES (\'1.*\', 128)');

        $sql->commit();

        echo '+ Database content inserted<br/>'."\n";

        // deletes Install directory if user wanted to do so
        if($_POST['del_install'])
        {
            if( deleteDirectory('Install/') )
            {
                echo '> Install directory deleted<br/>'."\n";
            }
            else
            {
                echo '- Could not delete install directory<br/>'."\n";
            }
        }

        // creating index.php files in all sub-directories
        echo '> Creating index.php files<br/>'."\n";
        indexDirectory('./');
        echo '+ Directories indexed<br/>'."\n";

        // saving configuretion file
        file_put_contents('config.php', '<?php
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

$config[\'directories\'][\'classes\'] = \''.$config['directories']['classes'].'\';

$config[\'db\'][\'type\'] = \''.$db['type'].'\';
$config[\'db\'][\'host\'] = \''.$db['host'].'\';
$config[\'db\'][\'user\'] = \''.$db['user'].'\';
$config[\'db\'][\'password\'] = \''.$db['password'].'\';
$config[\'db\'][\'database\'] = \''.$db['database'].'\';
$config[\'db\'][\'cms_prefix\'] = \''.$db['cms_prefix'].'\';
$config[\'db\'][\'ots_prefix\'] = \''.$db['ots_prefix'].'\';

?>
');
        echo '> Configuration saved in config.php<br/>'."\n";
    }
    catch(Exception $e)
    {
        $sql->rollback();
        echo '<b>! CRITICAL: ' . $e->getMessage() . '</b>' . "\n";
    }
}
// else displays installation panel
else
{
?>
<form action="install.php" method="post">
    <table style="width: 100%;">
        <tr>
            <td colspan="2" style="width: 100%; text-align: center; font-weight: bold; text-decoration: underline;">
                OTServ database connection settings.
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">
                OTServ database connection type: 
            </td>
            <td style="width: 50%; text-align: left;">
                <label><input type="radio" name="db[type]" value="MySQL" selected="selected"/>MySQL</label><br/>
                <label><input type="radio" name="db[type]" value="SQLite"/>SQLite</label><br/>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">
                OTServ host: 
            </td>
            <td style="width: 50%; text-align: left;">
                <input type="text" name="db[host]" value="localhost"/>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 100%; text-align: lustify;">
                <span style="font-weight: bold;">MySQL</span>: MySQL server.<br/>
                <span style="font-weight: bold;">SQLite</span>: Path to database file.<br/>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">
                Database user (<span style="font-weight: bold;">MySQL</span> only): 
            </td>
            <td style="width: 50%; text-align: left;">
                <input type="text" name="db[user]"/>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">
                Database password (<span style="font-weight: bold;">MySQL</span> only): 
            </td>
            <td style="width: 50%; text-align: left;">
                <input type="text" name="db[password]"/>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">
                Database name (<span style="font-weight: bold;">MySQL</span> only): 
            </td>
            <td style="width: 50%; text-align: left;">
                <input type="text" name="db[database]" value="otserv"/>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">
                Use MD5 for passwords: 
            </td>
            <td style="width: 50%; text-align: left;">
                <label><input type="radio" name="uses_md5" value="0" checked="checked"/>Disable</label>
                <label><input type="radio" name="uses_md5" value="1"/>Enable</label><br/>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 100%; text-align: lustify;">
                MD5 is hashing algorithm that makes password safer. This is not an OPTION - it must correspond with your OTServ configuration!
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 100%; text-align: center;">
                <input type="submit" value="Install"/>
                <hr />
            </td>
        </tr>
<?php

if( !isset($_GET['advanced']) )
{
?>
        <tr id="advanced_button">
            <td colspan="2" style="width: 100%; text-align: center;">
                <a href="install.php?advanced=1" onclick="var advanced_settings = document.getElementById('advanced_settings').style; advanced_settings.display = 'table'; advanced_settings.visibility = 'visible'; this.parentNode.removeChild(this); return false;">Advanced options</a>
            </td>
        </tr>
<?php
}

?>
    </table>
    <table id="advanced_settings" style="width: 100%;<?php if( !isset($_GET['advanced']) ) { echo ' display: none; visibility: hidden;'; } ?>">
        <tr>
            <td style="width: 50%; text-align: right;">
                Should install directory be deleted after installation: 
            </td>
            <td style="width: 50%; text-align: left;">
                <label><input type="checkbox" name="del_install"/>Delete</label>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">
                Prefix for <span style="font-weight: bold;">OTSCMS</span> tables (we recommend to leave it as it is): 
            </td>
            <td style="width: 50%; text-align: left;">
                <input type="text" name="db[cms_prefix]" value="otscms_"/>
            </td>
        </tr>
        <tr>
            <td style="width: 50%; text-align: right;">
                Prefix for <span style="font-weight: bold;">OTServ</span> tables (we recommend to leave it as it is - type only if you use one! Leave empty if you don't know what it is!): 
            </td>
            <td style="width: 50%; text-align: left;">
                <input type="text" name="db[ots_prefix]" value=""/>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="width: 100%; text-align: center;">
                <input type="submit" value="Install"/>
            </td>
        </tr>
    </table>
    <input type="hidden" name="command" value="install"/>
</form>
<?php
}

echo '</tt>';

?>
