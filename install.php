<?php
/*
    This file is part of OTSCMS (http://www.otscms.com/) project.

    Copyright (C) 2005 - 2008 Wrzasq (wrzasq@gmail.com)

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

header('Content-Type: text/html; charset=utf-8');
$version = '3.1.3';

// to make sure OTSCMS will run correctly on various PHP configurations
include('compat.php');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>OTSCMS installation</title>
        <style type="text/css">
body
{
    font-family: "Verdana", "Tahoma", sans-serif;
}

#wrapper
{
    font-size: 12px;
    background-color: #CCCCCC;
    width: 95%;
    padding: 5px;
    margin: 20px;
    border: 1px solid #000000;
}

.hint
{
    font-style: italic;
    font-size: 8px;
    text-align: justify;
    margin: 5px;
    text-indent: 15px;
}

#pageFooter
{
    font-size: 14px;
    width: 100%;
    text-align: center;
}

.critical
{
    color: #FF0000;
}

.critical p
{
    text-indent: 30px;
    text-align: justify;
}

.critical p:first-letter
{
    size: 20px;
    font-weight: bold;
}

pre
{
    color: #000000;
    border: 1px solid #000000;
    background-color: #FFFFFF;
    padding: 3px;
    width: 90%;
    margin: 3px;
}

.code
{
    font-family: monospace;
}

table
{
    width: 100%;
}

.formLeft
{
    width: 50%;
    text-align: right;
}

.formRight
{
    width: 50%;
    text-align: left;
}

td[colspan]
{
    width: 100%;
    text-align: center;
}

.bold
{
    font-weight: bold;
}
        </style>
    </head>
    <body>
<div id="wrapper">
<?php

// by default server is fine
$matches = true;

$phpIni = false;
$winOS = strtolower( substr(PHP_OS, 0, 3) ) == 'win';

// displays requirement
function requiredFeature($label, $text)
{
    echo '<div class="critical">
    <h3>' . $label . '</h3>
    <p>' . $text . '</p>
</div>';
}

// creates index.php files inside all sub directories
function indexDirectory($destination)
{
    // full index.php file path
    $file = $destination . 'index.php';

    // saves index.php file, but not everwrites existing one
    if( !file_exists($file) )
    {
        // saves file
        echo '@ Creating : ' . $file . '<br />'."\n";
        file_put_contents($file, '<?php

// redirects to upper-directory

header(\'Location: ../\');

?>');
    }
    else
    {
        echo '@ ' . $file . ' already exists<br />' . "\n";
    }

    // indexes also all sub-directories
    $dir = opendir($destination);
    while($current = readdir($dir) )
    {
        // checks if those are directories, but not symbols of upper directory
        if( is_dir($destination . $current) && $current != '.' && $current != '..' && is_writeable($destination . $current) )
        {
            // if it's a directory then create index.php file there too
            indexDirectory($destination . $current . '/');
        }
    }
}

// deletes a directory reccurently
function deleteDirectory($source)
{
    echo '@ Deleting: ' . $source . '<br />' . "\n";

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
                if( !deleteDirectory($source . $new . '/') )
                {
                    echo '- Could not delete directory: ' . $source . $new . '/<br />' . "\n";
                    return false;
                }
            }
            // otherwise jsut copy a file
            else
            {
                if( !@unlink($source . $new) )
                {
                    echo '- Could not delete file: ' . $source . $new . '<br />' . "\n";
                    return false;
                }
                echo '@ Deleted file: ' . $source . $new . '<br />' . "\n";
            }
        }
    }

    // creates destination directory
    rmdir($source);
    echo '@ Deleted directory: ' . $source . '<br />' . "\n";

    // returns that directory coping succeeded
    return true;
}

// checks PHP version
if( version_compare(PHP_VERSION, '5.2.0', '<') )
{
    $matches = false;
    requiredFeature('Outdated PHP version', 'PHP version installed on your server is ' . PHP_VERSION . '. OTSCMS requires version at least 5.2. You can download latest PHP version from <a href="http://www.php.net/">php.net</a>.');
}

// checks if SPL is enabled
if( !extension_loaded('SPL') )
{
    // filename depended on platform
    $file = $winOS ? 'php_spl.dll' : 'spl.so';

    $matches = false;
    requiredFeature('Missing SPL extension', 'OTSCMS requires SPL (Standard PHP Library) PHP extension for running. You must enable it in your <span class="code">php.ini</span> file by adding following line:</p>
<pre class="code">extension=' . $file . '</pre>
    <p>It\'s probably out there already, but commented by semicolon (<span class="code">;</span>). After enabling this extension, save <span class="code">php.ini</span> file and restart server.' . ($phpIni ? '' : '</p>
    <p>Make sure you are editing correct <span class="code">php.ini</span> file as in some server packages there can be more then one files. Correct one is usualy the one that is in Apache\'s <span class="code">bin/</span> directory (where Apache executables are placed).'));
    $phpIni = true;
}

// checks if PDO is enabled
if( !extension_loaded('PDO') )
{
    // filename depended on platform
    $file = $winOS ? 'php_pdo.dll' : 'pdo.so';

    $matches = false;
    requiredFeature('Missing PDO extension', 'OTSCMS requires PDO (PHP Data Objects) PHP extension for running. You must enable it in your <span class="code">php.ini</span> file by adding following line:</p>
<pre class="code">extension=' . $file . '</pre>
    <p>It\'s probably out there already, but commented by semicolon (<span class="code">;</span>). After enabling this extension, save <span class="code">php.ini</span> file and restart server.</p>
    <p>Also PDO drivers for your database type are required so if you know which one you will use, enable it too (OTSCMS will tell you to do so later if you won\'t do it now).' . ($phpIni ? '' : '</p>
    <p>Make sure you are editing correct <span class="code">php.ini</span> file as in some server packages there can be more then one files. Correct one is usualy the one that is in Apache\'s <span class="code">bin/</span> directory (where Apache executables are placed).'));
    $phpIni = true;
}

// checks if GD is enabled
if( !extension_loaded('gd') )
{
    // filename depended on platform
    $file = $winOS ? 'php_gd2.dll' : 'gd.so';

    requiredFeature('Missing GD extension', 'OTSCMS uses PHP GD extension for Gallery module. If you want to use this module, uou must enable GD in your <span class="code">php.ini</span> file by adding following line:</p>
<pre class="code">extension=' . $file . '</pre>
    <p>It\'s probably out there already, but commented by semicolon (<span class="code">;</span>). After enabling this extension, save <span class="code">php.ini</span> file and restart server.' . ($phpIni ? '' : '</p>
    <p>Make sure you are editing correct <span class="code">php.ini</span> file as in some server packages there can be more then one files. Correct one is usualy the one that is in Apache\'s <span class="code">bin/</span> directory (where Apache executables are placed).'));
    $phpIni = true;
}

// checks if PCRE is enabled
if( !extension_loaded('pcre') )
{
    // filename depended on platform
    $file = $winOS ? 'php_pcre.dll' : 'pcre.so';

    $matches = false;
    requiredFeature('Missing PCRE extension', 'OTSCMS requires PCRE (Perl Compatible Regular Expressions) PHP extension for running. You must enable it in your <span class="code">php.ini</span> file by adding following line:</p>
<pre class="code">extension=' . $file . '</pre>
    <p>It\'s probably out there already, but commented by semicolon (<span class="code">;</span>). After enabling this extension, save <span class="code">php.ini</span> file and restart server.' . ($phpIni ? '' : '</p>
    <p>Make sure you are editing correct <span class="code">php.ini</span> file as in some server packages there can be more then one files. Correct one is usualy the one that is in Apache\'s <span class="code">bin/</span> directory (where Apache executables are placed).'));
    $phpIni = true;
}

// checks if mod_rewrite is enabled
if( function_exists('apache_get_modules') )
{
    if( !in_array('mod_rewrite', apache_get_modules() ) )
    {
        $matches = false;
        requiredFeature('Missing mod_rewrite module', 'OTSCMS requires mod_rewrite Apache module for running. You must enable it in your <span class="code">httpd.conf</span> file by adding following line:</p>
<pre class="code">LoadModule rewrite_module modules/mod_rewrite.so</pre>
    <p>It\'s probably out there already, but commented by hash (<span class="code">#</span>). After enabling this extension, save <span class="code">httpd.conf</span> file and restart server.');
    }
}

// checking if config.php file is writable
if(!(( file_exists('config.php') && is_writable('config.php') ) || ( !file_exists('config.php') && is_writeable('.') ) ))
{
    $matches = false;
    requiredFeature('<span class="code">config.php</span> is not writeable', 'OTSCMS can\'t write to configuration file. Setup will save startup database connection configuration in that file. Note that usualy user under which OTSCMS runs is the user under which HTTP server is running and usualy it\'s not the same as you, or your FTP user. To make it writeable for OTSCMS execute following command in your command line:</p>
<pre class="code">touch config.php && chmod 777 config.php</pre>
    <p>If you are running on Windows then check file properties.');
}

// we can proceed to installation steps
if($matches)
{
    $command = '';

    // reads command
    if( isset($_REQUEST['command']) )
    {
        $command = $_REQUEST['command'];
    }

    switch($command)
    {
        // detailed database configuration
        case 'database':
            $db = array();

            // loads data from config.lua
            if( !empty($_FILES['lua']['name']) )
            {
                preg_match_all('/^([a-z0-9_]+) *= *"?(.*?)"?$/mi', file_get_contents($_FILES['lua']['tmp_name']), $lua);
                $lua = array_combine($lua[1], $lua[2]);

                // loads certain info

                if( isset($lua['sql_type']) )
                {
                    switch($lua['sql_type'])
                    {
                        case 'mysql':
                            $db['type'] = 'MySQL';
                            break;

                        case 'sqlite':
                            $db['type'] = 'SQLite';
                            break;

                        case 'pgsql':
                            $db['type'] = 'PostgreSQL';
                            break;
                    }
                }

                if( isset($lua['sql_host']) )
                {
                    $db['host'] = $lua['sql_host'];
                }

                if( isset($lua['sql_user']) )
                {
                    $db['user'] = $lua['sql_user'];
                }

                if( isset($lua['sql_port']) )
                {
                    $db['port'] = $lua['sql_port'];
                }

                if( isset($lua['sql_pass']) )
                {
                    $db['password'] = $lua['sql_pass'];
                }

                if( isset($lua['sql_db']) )
                {
                    $db['database'] = $lua['sql_db'];
                }

                if( isset($lua['passwordtype']) )
                {
                    $password_type = $lua['passwordtype'];
                }

                if( isset($lua['map'], $lua['mapkind']) && $lua['mapkind'] == 'OTBM')
                {
                    $mapfile = basename($lua['map']);
                }

                $online = array();

                if( isset($lua['worldname']) )
                {
                    $online['name'] = $lua['worldname'];
                }

                if( isset($lua['ip']) )
                {
                    $online['content'] = $lua['ip'];
                }

                if( isset($lua['port']) )
                {
                    $online['port'] = $lua['port'];
                }
            }

            // overwrites settings by user choice
            if( isset($_REQUEST['db']) )
            {
                $db['type'] = $_REQUEST['db']['type'];
            }

            // checks if database type is supported
            if( !in_array($db['type'], array('MySQL', 'SQLite', 'PostgreSQL') ) )
            {
                requiredFeature('Unsupported database type', 'OTSCMS supports MySQL, SQLite and PostrgeSQL databases. Your database type <span class="code">' . $db['type'] . '</span> is not supported, sorry. Make sure you have selected driver from list, or contact OTSCMS developers on <a href="http://otfans.net/forumdisplay.php?f=112">OTSCMS forum</a> posting your request to support this database type.');
            }
            // checks PDO driver
            elseif( !extension_loaded( strtolower('pdo_' . $db['type']) ) )
            {
                // filename depended on platform
                $file = $winOS ? 'php_pdo_' . $db['type'] . '.dll' : 'pdo_' . $db['type'] . '.so';

                requiredFeature('Missing PDO driver', 'PDO driver for your database type is not loaded. PDO itself is only data access interface, it requires drivers to connect to certain type of database. You must enable it in your <span class="code">php.ini</span> file by adding following line:</p>
<pre class="code">extension=' . $file . '</pre>
    <p>It\'s probably out there already, but commented by semicolon (<span class="code">;</span>). After enabling this extension, save <span class="code">php.ini</span> file and restart server.' . ($phpIni ? '' : '</p>
    <p>Make sure you are editing correct <span class="code">php.ini</span> file as in some server packages there can be more then one files. Correct one is usualy the one that is in Apache\'s <span class="code">bin/</span> directory (where Apache executables are placed).'));
            }
            // continues
            else
            {
?>
<form action="install.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="command" value="account" />
<input type="hidden" name="db[type]" value="<?php echo $db['type']; ?>" />
<?php if( isset($online['name']) ): ?>
<input type="hidden" name="online[name]" value="<?php echo $online['name']; ?>" />
<?php endif; ?>
<?php if( isset($online['content']) ): ?>
<input type="hidden" name="online[content]" value="<?php echo $online['content']; ?>" />
<?php endif; ?>
<?php if( isset($online['port']) ): ?>
<input type="hidden" name="online[port]" value="<?php echo $online['port']; ?>" />
<?php endif; ?>
<table>
<tbody>
<?php switch($db['type']): ?>
<?php case 'MySQL': ?>
    <tr>
        <td class="formLeft">MySQL server:</td>
        <td class="formRight">
            <input type="text" name="db[host]" value="<?php echo isset($db['host']) ? $db['host'] . ( isset($db['port']) && $db['port'] != '3306' ? ':' . $db['port'] : '') : 'localhost'; ?>" />
        </td>
    </tr>
    <tr>
        <td class="formLeft">MySQL user:</td>
        <td class="formRight">
            <input type="text" name="db[user]" value="<?php echo isset($db['user']) ? $db['user'] : 'root'; ?>" />
        </td>
    </tr>
    <tr>
        <td class="formLeft">Password:</td>
        <td class="formRight">
            <input type="text" name="db[password]" value="<?php echo isset($db['password']) ? $db['password'] : ''; ?>" />
        </td>
    </tr>
    <tr>
        <td class="formLeft">Database name:</td>
        <td class="formRight">
            <input type="text" name="db[database]" value="<?php echo isset($db['database']) ? $db['database'] : 'otserv'; ?>" />
        </td>
    </tr>
<?php break; ?>
<?php case 'SQLite': ?>
    <tr>
        <td class="formLeft">Path to database file:</td>
        <td class="formRight">
            <input type="text" name="db[host]" value="/path/to/otserv/<?php echo isset($db['db']) ? $db['db'] : 'db.s3db'; ?>" />
        </td>
    </tr>
<input type="hidden" name="db[user]" value="" />
<input type="hidden" name="db[password]" value="" />
<input type="hidden" name="db[database]" value="" />
<?php break; ?>
<?php case 'PostgreSQL': ?>
    <tr>
        <td class="formLeft">PostgreSQL server:</td>
        <td class="formRight">
            <input type="text" name="db[host]" value="<?php echo isset($db['host']) ? $db['host'] . ( isset($db['port']) && $db['port'] != '5432' ? ':' . $db['port'] : '') : 'localhost'; ?>" />
        </td>
    </tr>
    <tr>
        <td class="formLeft">PostgreSQL user:</td>
        <td class="formRight">
            <input type="text" name="db[user]" value="<?php echo isset($db['user']) ? $db['user'] : ''; ?>" />
        </td>
    </tr>
    <tr>
        <td class="formLeft">Password:</td>
        <td class="formRight">
            <input type="text" name="db[password]" value="<?php echo isset($db['password']) ? $db['password'] : ''; ?>" />
        </td>
    </tr>
    <tr>
        <td class="formLeft">Database name:</td>
        <td class="formRight">
            <input type="text" name="db[database]" value="<?php echo isset($db['database']) ? $db['database'] : 'otserv'; ?>" />
        </td>
    </tr>
<?php break; ?>
<?php endswitch; ?>
    <tr>
        <td class="formLeft">Password saving mechanism:
<p class="hint">This is not an OPTION - it must correspond with your OTServ setting!</p>
        </td>
        <td class="formRight">
            <label><input type="radio" name="password_type" value="plain"<?php echo !isset($password_type) || $password_type == 'plain' ? ' checked="checked"': ''; ?> />Plain (no hashing)</label><br />
            <label><input type="radio" name="password_type" value="md5"<?php echo isset($password_type) && $password_type == 'md5' ? ' checked="checked"': ''; ?> />MD5 hash</label><br />
            <label><input type="radio" name="password_type" value="sha1"<?php echo isset($password_type) && $password_type == 'sha1' ? ' checked="checked"': ''; ?> />SHA1 hash</label><br />
        </td>
    </tr>
    <tr>
        <td class="formLeft">data/ directory path:</td>
        <td class="formRight">
            <input type="text" name="data_directory" value="/path/to/data/" />
        </td>
    </tr>
    <tr>
        <td class="formLeft">Map filename:</td>
        <td class="formRight">
            <input type="text" name="mapfile" value="<?php echo isset($mapfile) ? $mapfile : 'map.otbm'; ?>" />
        </td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit" value="Proceed" /></td>
    </tr>
<?php

if( !isset($_GET['advanced']) )
{
?>
    <tr id="advanced_button">
        <td colspan="2">
            <a href="install.php?advanced" onclick="var advanced_settings = document.getElementById('advanced_settings').style; advanced_settings.display = 'table'; advanced_settings.visibility = 'visible'; this.parentNode.removeChild(this); return false;">Advanced options</a>
        </td>
    </tr>
<?php
}

?>
</tbody>
</table>

<table id="advanced_settings"<?php if( !isset($_GET['advanced']) ) { echo ' style="display: none; visibility: hidden;"'; } ?>>
<tbody>
    <tr>
        <td class="formLeft">Should install directory be deleted after installation:</td>
        <td class="formRight">
            <label><input type="checkbox" name="del_install" />Delete</label>
        </td>
    </tr>
    <tr>
        <td class="formLeft">Prefix for <span style="font-weight: bold;">OTSCMS</span> tables (we recommend to leave it as it is):</td>
        <td class="formRight">
            <input type="text" name="db[cms_prefix]" value="otscms_" />
        </td>
    </tr>
    <tr>
        <td class="formLeft">Prefix for <span style="font-weight: bold;">OTServ</span> tables (we recommend to leave it as it is - type only if you use one! Leave empty if you don't know what it is!):</td>
        <td class="formRight">
            <input type="text" name="db[ots_prefix]" value="" />
        </td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit" value="Proceed" /></td>
    </tr>
</tbody>
</table>
</form>
<?php
            }

            break;

        // detailed installation info
        case 'account':
            // pre-loads HTTP data
            $db = $_POST['db'];

            // install scheme
            if( !file_exists('Install/' . $db['type'] . '.sql') )
            {
                requiredFeature('Missing installation schema', 'Installation schema file is missing. Please copy installation schemas (<span class="code">Install/</span> directory) from OTSCMS Lite package to your OTSCMS directory.');
                break;
            }

            // fix for POT
            if($db['type'] == 'SQLite')
            {
                $db['database'] = $db['host'];
            }

            // loads database handler file
            $config['db'] = $db;
            $config['directories']['classes'] = 'classes/';
            require_once('classes/OTSCMS.php');
            POT::getInstance();

            try
            {
                $sql = new SQL($db['host'], $db['user'], $db['password'], $db['database'], $db['cms_prefix'], $db['ots_prefix']);
            }
            catch(PDOException $e)
            {
                requiredFeature('Database connection error', 'Could not connect to database. PDO returned following message:</p>
<pre class="code">' . $e->getMessage() . '</pre>
    <p>You can continue installation on your own risk, but we recommend you to check database information.');
            }

?>
<form action="install.php" method="post">
<input type="hidden" name="command" value="install" />
<input type="hidden" name="db[type]" value="<?php echo $db['type']; ?>" />
<input type="hidden" name="db[host]" value="<?php echo $db['host']; ?>" />
<input type="hidden" name="db[user]" value="<?php echo $db['user']; ?>" />
<input type="hidden" name="db[password]" value="<?php echo $db['password']; ?>" />
<input type="hidden" name="db[database]" value="<?php echo $db['database']; ?>" />
<input type="hidden" name="db[cms_prefix]" value="<?php echo $db['cms_prefix']; ?>" />
<input type="hidden" name="db[ots_prefix]" value="<?php echo $db['ots_prefix']; ?>" />
<input type="hidden" name="mapfile" value="<?php echo $_POST['mapfile']; ?>" />
<input type="hidden" name="data_directory" value="<?php echo $_POST['data_directory']; ?>" />
<input type="hidden" name="del_install" value="<?php echo isset($_POST['del_install']) && $_POST['del_install'] ? '1' : '0'; ?>" />
<input type="hidden" name="password_type" value="<?php echo $_POST['password_type']; ?>" />
<?php if( isset($_POST['online']['name']) ): ?>
<input type="hidden" name="online[name]" value="<?php echo $_POST['online']['name']; ?>" />
<?php endif; ?>
<?php if( isset($_POST['online']['content']) ): ?>
<input type="hidden" name="online[content]" value="<?php echo $_POST['online']['content']; ?>" />
<?php endif; ?>
<?php if( isset($_POST['online']['port']) ): ?>
<input type="hidden" name="online[port]" value="<?php echo $_POST['online']['port']; ?>" />
<?php endif; ?>
<table>
<tbody>
    <tr>
        <td class="formLeft">Create new GameMaster account:
<p class="hint">This is usefull when you have empty database as this will allow you to log into OTSCMS administration panel. You don't need it if you install OTSCMS on already running OTServ database with existing GameMaster players.</p>
        </td>
        <td class="formRight">
            <input type="checkbox" name="create_gm" value="1" checked="checked" onclick="document.getElementById('gm_account').disabled = !this.checked; document.getElementById('gm_password').disabled = !this.checked; document.getElementById('gm_name').disabled = !this.checked;" />
        </td>
    </tr>
    <tr>
        <td class="formLeft">Account number:</td>
        <td class="formRight">
            <input type="text" name="gm_account" id="gm_account" value="111111" />
        </td>
    </tr>
    <tr>
        <td class="formLeft">Password:</p>
        </td>
        <td class="formRight">
            <input type="text" name="gm_password" id="gm_password" value="tibia" />
        </td>
    </tr>
    <tr>
        <td class="formLeft">Nick:</p>
        </td>
        <td class="formRight">
            <input type="text" name="gm_name" id="gm_name" value="OTSCMS" />
        </td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit" value="Install" /></td>
    </tr>
</table>
</form>
<?php
            break;

        // final installation
        case 'install':
            echo '<pre class="code">';

            // pre-loads HTTP data
            $db = $_POST['db'];

            // install scheme
            $install = file_get_contents('Install/' . $db['type'] . '.sql');

            // loads database handler file
            $config['db'] = $db;
            $config['directories']['classes'] = 'classes/';
            require_once('classes/OTSCMS.php');
            POT::getInstance();
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
                            echo '<span class="critical,bold">- Error during executing SQL query:<br />
&nbsp;&nbsp;&nbsp;&nbsp;' . $query . '<br />
returned:<br />
&nbsp;&nbsp;&nbsp;&nbsp;' . $e . '</span></pre>' . "\n";
                            break 2;
                        }
                    }
                }
            }

            echo '<span class="bold">+ Database scheme installed</span><br />' . "\n";

            try
            {
                // inserts database startup content

                $sql->beginTransaction();

                // startup GM account
                if( isset($_POST['create_gm']) && $_POST['create_gm'])
                {
                    // POT initialization
                    $driver = array('MySQL' => POT::DB_MYSQL, 'SQLite' => POT::DB_SQLITE, 'PostgreSQL' => POT::DB_PGSQL);
                    $db['driver'] = $driver[ $db['type'] ];
                    $db['prefix'] = $db['ots_prefix'];
                    $ots = POT::getInstance();
                    $ots->connect(null, $db);

                    // generates password hash
                    switch($_POST['password_type'])
                    {
                        default:
                            $_POST['password_type'] = 'plain';

                        case 'plain':
                            $password = $_POST['gm_password'];
                            break;

                        case 'md5':
                            $password = md5($_POST['gm_password']);
                            break;

                        case 'sha1':
                            $password = sha1($_POST['gm_password']);
                            break;
                    }

                    // GM account
                    $account = new OTS_Account();
                    $account->create($_POST['gm_account'], $_POST['gm_account']);
                    $account->blocked = false;
                    $account->password = $_POST['gm_password'];
                    $account->save();

                    echo '+ GM account created<br />' . "\n";

                    // searches for GM group
                    $filter = new OTS_SQLFilter();
                    $filter->compareField('access', 3, OTS_SQLFilter::OPERATOR_NLOWER);

                    // loads user group
                    $list = new OTS_Groups_List();
                    $list->filter = $filter;

                    // GM gorup
                    if( count($list) > 0)
                    {
                        $list->rewind();
                        $group = $list->current();
                    }
                    // creates new group
                    else
                    {
                        $group = new OTS_Group();
                        $group->name = 'OTSCMS';
                        $group->access = 3;
                        $group->maxDepotItems = 1000;
                        $group->maxVIPList = 50;
                        $group->save();
                        echo 'GameMasters group not found, created new one<br />' . "\n";
                    }

                    // creates GM character
                    $player = new OTS_Player();
                    $player->account = $account;
                    $player->group = $group;
                    $player->name = $_POST['gm_name'];
                    $player->setRank();
                    $player->townId = 1;
                    $player->conditions = '';
                    $player->save();

                    echo '+ GM character created<br />' . "\n";
                }

                // default OTS server for online statistics
                if( isset($_POST['online']['name'], $_POST['online']['content'], $_POST['online']['port']) )
                {
                    $online = $_POST['online'];
                    $query = $sql->prepare('INSERT INTO [online] (`name`, `content`, `port`) VALUES (:name, :content, :port)');
                    $query->execute( array(':name' => $online['name'], ':content' => $online['content'], ':port' => $online['port']) );
                }

                // home website
                $query = $sql->prepare('INSERT INTO [sites] (`name`, `content`, `is_home`) VALUES (:name, :content, 1)');

                $query->execute( array(':name' => 'OTSCMS credits', ':content' => '<p class="justified">
Describe of your site can goes here. You can edit this text in administration panel.
</p>

<p class="justified">
<b>OTSCMS</b> uses <a href="http://otserv-aac.info/">PHP OTServ Toolkit</a>. While writing extensions for <b>OTSCMS</b> you can use any code written with <b>POT</b>!
</p>

<p class="justified">
There are many people who contributed with our team in developing this project.
</p>

<p class="justified">
Mainly great thans to <b>Foziw</b> for the domain and <b>Yorick</b> for forum!
</p>

<h3>OTSCMS Dev-Team members</h3>

<ul>
<li><b>Wrzasq</b> - Project headmaster, engine, porting and developing, <i>Default</i> skin, website.</li>
</ul>

<h3>OTSCMS version 3 developement</h3>

<ul>
<li><b>Foziw</b> - Project supporter, beta tester, bug finder.</li>
<li><b>Invincible Aznx</b> - Hosting (<a href="http://www.invinciblehost.com/" class="outLink">http://www.invinciblehost.com/</a>).</li>
<li><b>Brave</b> - Helps with "<i>Players Online</i>" script.</li>
<li><b>Chester</b> - Website design.</li>
<li><b>Yorick</b> - Advertising, forum.</li>
<li><b>LooSik</b> - Helps with design.</li>
<li><b>Katten</b> - Code for XML output formating.</li>
<li><b>Ada GT</b> - For help in hard moment by reminding rule "if you don\'t understant, then fuck it" :P.</li>
<li><b>Joddo</b> - Help, POT bugfix.</li>
<li><b>Reks</b> - Beta tester.</li>
<li><b>Winghawk</b> - Beta tester.</li>
<li><b>Slash X</b> - Beta tester.</li>
<li><b>Empty Flask</b> - Beta tester.</li>
<li><b>Rupo</b> - Beta tester.</li>
<li><b>Master-m</b> - Beta tester.</li>
<li><b>Mistik</b> - Beta tester.</li>
<li><b>Morpheus</b> - Beta tester.</li>
<li><b>Critias</b> - Beta tester.</li>
<li><b>FCKEditor</b> - WYSIWYG controll (<a href="http://www.fckeditor.net/" class="outLink">FCK Editor</a>).</li>
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
<li><b>FF Wrapzone</b> - "<i>Default</i>" skin (downloaded from <a href="http://www.freelayouts.com/" class="outLink">http://www.freelayouts.com/</a>).</li>
<li><b>Bryan007</b> - Remote OTSCMS tester.</li>
<li><b>Jason</b> - <i>GenX</i> theme.</li>
<li><b>Mick</b> - Helps with MySQL driver for OTServ database, translation fixes.</li>
<li><b>Adki</b> - Designer, port of <a href="http://webmark.shost.pl/" class="outLink">WebMark</a> <i>Multi CSS</i> skin.</li>
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

                $query = $sql->prepare('INSERT INTO [settings] (`name`, `content`) VALUES (:name, :content)');

                $query->execute( array(':name' => 'version', ':content' => $version) );
                $query->execute( array(':name' => 'directories.languages', ':content' => 'languages/') );
                $query->execute( array(':name' => 'directories.modules', ':content' => 'modules/') );
                $query->execute( array(':name' => 'directories.skins', ':content' => 'skins/') );
                $query->execute( array(':name' => 'directories.images', ':content' => 'images/') );
                $query->execute( array(':name' => 'directories.data', ':content' => $_POST['data_directory']) );
                $query->execute( array(':name' => 'cookies.prefix', ':content' => 'otscms_') );
                $query->execute( array(':name' => 'cookies.path', ':content' => '/') );
                $query->execute( array(':name' => 'cookies.domain', ':content' => '.example.com') );
                $query->execute( array(':name' => 'cookies.expire', ':content' => 2592000) );
                $query->execute( array(':name' => 'system.passwords', ':content' => $_POST['password_type']) );
                $query->execute( array(':name' => 'system.use_mail', ':content' => false) );
                $query->execute( array(':name' => 'system.nick_length', ':content' => 3) );
                $query->execute( array(':name' => 'system.default_group', ':content' => 1) );
                $query->execute( array(':name' => 'system.min_number', ':content' => 0) );
                $query->execute( array(':name' => 'system.max_number', ':content' => 999999) );
                $query->execute( array(':name' => 'system.account_limit', ':content' => 5) );
                $query->execute( array(':name' => 'system.map', ':content' => $_POST['mapfile']) );
                $query->execute( array(':name' => 'system.rook.enabled', ':content' => false) );
                $query->execute( array(':name' => 'system.rook.id', ':content' => 0) );
                $query->execute( array(':name' => 'system.depots.count', ':content' => 2) );
                $query->execute( array(':name' => 'system.depots.item', ':content' => 2590) );
                $query->execute( array(':name' => 'system.depots.chest', ':content' => 2594) );
                $query->execute( array(':name' => 'statistics.page', ':content' => 30) );
                $query->execute( array(':name' => 'site.language', ':content' => 'english') );
                $query->execute( array(':name' => 'site.skin', ':content' => 'Default') );
                $query->execute( array(':name' => 'site.title', ':content' => 'Powered by OTSCMS 3 - http://www.otscms.com/') );
                $query->execute( array(':name' => 'site.date_format', ':content' => 'j F Y G:i') );
                $query->execute( array(':name' => 'site.news_limit', ':content' => 5) );
                $query->execute( array(':name' => 'gallery.mini_x', ':content' => 100) );
                $query->execute( array(':name' => 'gallery.mini_y', ':content' => 100) );
                $query->execute( array(':name' => 'forum.limit', ':content' => 20) );
                $query->execute( array(':name' => 'forum.avatar.max_x', ':content' => 80) );
                $query->execute( array(':name' => 'forum.avatar.max_y', ':content' => 80) );
                $query->execute( array(':name' => 'mail.from', ':content' => 'you@example.com') );
                $query->execute( array(':name' => 'mail.smtp.host', ':content' => 'localhost') );
                $query->execute( array(':name' => 'mail.smtp.port', ':content' => 25) );
                $query->execute( array(':name' => 'mail.smtp.use_auth', ':content' => false) );
                $query->execute( array(':name' => 'mail.smtp.user', ':content' => '') );
                $query->execute( array(':name' => 'mail.smtp.password', ':content' => '') );

                $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'OTSCMS\', \'http://otscms.com/\')');
                $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'OTFans\', \'http://otfans.net/\')');
                $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'PHP OTServ Toolkit\', \'http://otserv-aac.info/\')');
                $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'Open Tibia Server List\', \'http://www.otservlist.org/\')');
                $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'OTServ News\', \'http://www.otservnews.org/\')');
                $sql->exec('INSERT INTO [links] (`name`, `content`) VALUES (\'OTWiki\', \'http://otserv.org/\')');

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
                $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'OTAdmin.*\', 3)');
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
                $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Sites.*\', 3)');
                $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Sites.display\', -1)');
                $sql->exec('INSERT INTO [access] (`name`, `content`) VALUES (\'Sites.list\', -1)');

                $sql->exec('INSERT INTO [download] (`name`, `content`, `binary`, `file`) VALUES (\'OTSCMS Lite\', \'Latest <span style="font-weight: bold;">OTSCMS Lite</span>.<br />
<br />
Please visit <a href="http://otscms.com/">http://www.otscms.com/</a>.\', 0, \'http://otscms.com/latest.php/lite\')');

                $sql->exec('INSERT INTO [profiles] (`name`, `health`, `healthmax`, `direction`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `cap`) VALUES (\'*.*\', 250, 250, 0, 0, 30, 50, 20, 40, 0, 0, 0, 0, 0, 220)');
                $sql->exec('INSERT INTO [profiles] (`name`, `looktype`) VALUES (\'' . POT::SEX_FEMALE . '.*\', 136)');
                $sql->exec('INSERT INTO [profiles] (`name`, `looktype`) VALUES (\'' . POT::SEX_MALE . '.*\', 128)');

                $query = $sql->prepare('INSERT INTO [urls] (`name`, `content`, `order`) VALUES (:name, :content, :order)');

                $query->execute( array(':name' => '^.*$', ':content' => 'module=News&command=home', ':order' => 100) );
                $query->execute( array(':name' => '^admin/?(.*)$', ':content' => '$1', ':order' => 10) );
                $query->execute( array(':name' => '^logout/?$', ':content' => 'module=Accoubr />nt&command=account&userlogout=1', ':order' => 50) );
                $query->execute( array(':name' => '^vote/?$', ':content' => 'module=Poll&command=vote', ':order' => 50) );
                $query->execute( array(':name' => '^library/?$', ':content' => 'module=Library&command=main', ':order' => 50) );
                $query->execute( array(':name' => '^download/?$', ':content' => 'module=Download&command=list', ':order' => 50) );
                $query->execute( array(':name' => '^gallery/?$', ':content' => 'module=Gallery&command=list', ':order' => 50) );
                $query->execute( array(':name' => '^signup/?$', ':content' => 'module=Account&command=signup', ':order' => 50) );
                $query->execute( array(':name' => '^signup/submit$', ':content' => 'module=Account&command=create', ':order' => 20) );
                $query->execute( array(':name' => '^password/forgot$', ':content' => 'module=Account&command=forgot', ':order' => 20) );
                $query->execute( array(':name' => '^password/remind$', ':content' => 'module=Account&command=remind', ':order' => 20) );
                $query->execute( array(':name' => '^password/change$', ':content' => 'module=Account&command=change', ':order' => 20) );
                $query->execute( array(':name' => '^account/?$', ':content' => 'module=Account&command=account', ':order' => 50) );
                $query->execute( array(':name' => '^account/save$', ':content' => 'module=Account&command=save', ':order' => 20) );
                $query->execute( array(':name' => '^account/suspend$', ':content' => 'module=Account&command=suspend', ':order' => 20) );
                $query->execute( array(':name' => '^archive/?$', ':content' => 'module=News&command=archive', ':order' => 50) );
                $query->execute( array(':name' => '^news/?$', ':content' => 'module=News&command=list', ':order' => 50) );
                $query->execute( array(':name' => '^news/([0-9]+)$', ':content' => 'module=News&command=display&id=$1', ':order' => 30) );
                $query->execute( array(':name' => '^spells/?$', ':content' => 'module=Library&command=spells', ':order' => 50) );
                $query->execute( array(':name' => '^spells/instants/(.+)$', ':content' => 'module=Library&command=spell&name=$1&type=' . OTS_SpellsList::SPELL_INSTANT, ':order' => 20) );
                $query->execute( array(':name' => '^spells/runes/(.+)$', ':content' => 'module=Library&command=spell&name=$1&type=' . OTS_SpellsList::SPELL_RUNE, ':order' => 20) );
                $query->execute( array(':name' => '^spells/conjures/(.+)$', ':content' => 'module=Library&command=spell&name=$1&type=' . OTS_SpellsList::SPELL_CONJURE, ':order' => 20) );
                $query->execute( array(':name' => '^monsters/?$', ':content' => 'module=Library&command=monsters', ':order' => 50) );
                $query->execute( array(':name' => '^monsters/(.+)$', ':content' => 'module=Library&command=monster&name=$1', ':order' => 40) );
                $query->execute( array(':name' => '^characters/?$', ':content' => 'module=Character&command=display', ':order' => 50) );
                $query->execute( array(':name' => '^characters/([0-9]+)/delete$', ':content' => 'module=Character&command=delete&id=$1', ':order' => 10) );
                $query->execute( array(':name' => '^characters/([0-9]+)/change$', ':content' => 'module=Character&command=change&id=$1', ':order' => 10) );
                $query->execute( array(':name' => '^characters/([0-9]+)/save$', ':content' => 'module=Character&command=save&id=$1', ':order' => 10) );
                $query->execute( array(':name' => '^characters/create$', ':content' => 'module=Character&command=create', ':order' => 20) );
                $query->execute( array(':name' => '^characters/insert$', ':content' => 'module=Character&command=insert', ':order' => 20) );
                $query->execute( array(':name' => '^characters/(.+)$', ':content' => 'module=Character&command=display&name=$1', ':order' => 40) );
                $query->execute( array(':name' => '^characters/(.+)/message$', ':content' => 'module=PM&command=new&to=$1', ':order' => 30) );
                $query->execute( array(':name' => '^statistics/?$', ':content' => 'module=Statistics&command=census', ':order' => 50) );
                $query->execute( array(':name' => '^statistics/(.*)$', ':content' => 'module=Statistics&command=highscores&list=$1', ':order' => 40) );
                $query->execute( array(':name' => '^statistics/(.*)/page([0-9]+)$', ':content' => 'module=Statistics&command=highscores&list=$1&page=$2', ':order' => 30) );
                $query->execute( array(':name' => '^guild/quit$', ':content' => 'module=Guilds&command=quit', ':order' => 20) );
                $query->execute( array(':name' => '^guild/add$', ':content' => 'module=Guilds&command=add', ':order' => 20) );
                $query->execute( array(':name' => '^guild/insert$', ':content' => 'module=Guilds&command=insert', ':order' => 20) );
                $query->execute( array(':name' => '^guild/create$', ':content' => 'module=Guilds&command=create', ':order' => 20) );
                $query->execute( array(':name' => '^guilds/?$', ':content' => 'module=Guilds&command=list', ':order' => 50) );
                $query->execute( array(':name' => '^guilds/([0-9]+)$', ':content' => 'module=Guilds&command=display&id=$1', ':order' => 30) );
                $query->execute( array(':name' => '^guides/?$', ':content' => 'module=Sites&command=list', ':order' => 50) );
                $query->execute( array(':name' => '^guides/([0-9]+)$', ':content' => 'module=Sites&command=display&id=$1', ':order' => 40) );
                $query->execute( array(':name' => '^forum/?$', ':content' => 'module=Forum&command=board&id=0', ':order' => 50) );
                $query->execute( array(':name' => '^forum/([0-9]+)$', ':content' => 'module=Forum&command=board&id=$1', ':order' => 40) );
                $query->execute( array(':name' => '^forum/([0-9]+)/page([0-9]+)$', ':content' => 'module=Forum&command=board&id=$1&page=$2', ':order' => 30) );
                $query->execute( array(':name' => '^forum/([0-9]+)/reply$', ':content' => 'module=Topic&command=new&boardid=$1', ':order' => 20) );
                $query->execute( array(':name' => '^posts/([0-9]+)$', ':content' => 'module=Topic&command=view&id=$1', ':order' => 40) );
                $query->execute( array(':name' => '^posts/([0-9]+)/page([0-9]+)$', ':content' => 'module=Topic&command=view&id=$1&page=$2', ':order' => 30) );
                $query->execute( array(':name' => '^posts/([0-9]+)/reply$', ':content' => 'module=Topic&command=new&topicid=$1', ':order' => 20) );
                $query->execute( array(':name' => '^posts/([0-9]+)/quote/([0-9]+)$', ':content' => 'module=Topic&command=new&topicid=$1&quoteid=$2', ':order' => 10) );
                $query->execute( array(':name' => '^poll/?$', ':content' => 'module=Poll&command=latest', ':order' => 60) );
                $query->execute( array(':name' => '^polls/?$', ':content' => 'module=Poll&command=list', ':order' => 50) );
                $query->execute( array(':name' => '^polls/([0-9]+)$', ':content' => 'module=Poll&command=display&id=$1', ':order' => 40) );
                $query->execute( array(':name' => '^inbox/?$', ':content' => 'module=PM&command=inbox', ':order' => 50) );
                $query->execute( array(':name' => '^outbox/?$', ':content' => 'module=PM&command=sent', ':order' => 50) );
                $query->execute( array(':name' => '^message/([0-9]+)$', ':content' => 'module=PM&command=display&id=$1', ':order' => 30) );
                $query->execute( array(':name' => '^message/([0-9]+)/delete$', ':content' => 'module=PM&command=delete&id=$1', ':order' => 20) );
                $query->execute( array(':name' => '^message/([0-9]+)/reply$', ':content' => 'module=PM&command=reply&id=$1', ':order' => 20) );
                $query->execute( array(':name' => '^message/([0-9]+)/forward$', ':content' => 'module=PM&command=fw&id=$1', ':order' => 20) );
                $query->execute( array(':name' => '^message/new$', ':content' => 'module=PM&command=new', ':order' => 20) );
                $query->execute( array(':name' => '^send/?$', ':content' => 'module=PM&command=send', ':order' => 50) );

                $sql->commit();

                echo '<span class="bold">+ Database content inserted</span><br />' . "\n";

                // deletes Install directory if user wanted to do so
                if($_POST['del_install'])
                {
                    if( deleteDirectory('Install/') )
                    {
                        echo '<span class="bold">+ Install directory deleted</span><br />' . "\n";
                    }
                    else
                    {
                        echo '<span class="critical">- Could not delete install directory</span><br />' . "\n";
                    }
                }

                // creating index.php files in all accessible sub-directories
                echo 'Creating index.php files...<br />' . "\n";
                indexDirectory('images/');
                indexDirectory('skins/');
                indexDirectory('fckeditor/');
                echo '<span class="bold">+ Directories indexed</span><br />' . "\n";

                // saving configuretion file
                file_put_contents('config.php', '<?php
/*
    This file is part of OTSCMS (http://www.otscms.com/) project.

    Copyright (C) 2005 - 2008 Wrzasq (wrzasq@gmail.com)

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

include(\'compat.php\');

$config[\'directories\'][\'classes\'] = \'classes/\';

$config[\'db\'][\'type\'] = \'' . $db['type'] . '\';
$config[\'db\'][\'host\'] = \'' . $db['host'] . '\';
$config[\'db\'][\'user\'] = \'' . $db['user'] . '\';
$config[\'db\'][\'password\'] = \'' . $db['password'] . '\';
$config[\'db\'][\'database\'] = \'' . $db['database'] . '\';
$config[\'db\'][\'cms_prefix\'] = \'' . $db['cms_prefix'] . '\';
$config[\'db\'][\'ots_prefix\'] = \'' . $db['ots_prefix'] . '\';

?>
');
                echo '<span class="bold">+ Configuration saved in config.php</span><br />' . "\n";

                // saving friendly-URLs wrapper
                $_SERVER['REQUEST_URI'] = dirname($_SERVER['REQUEST_URI']);
                file_put_contents('.htaccess', '<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase ' . $_SERVER['REQUEST_URI'] . '/
RewriteCond %{REQUEST_URI} !^' . $_SERVER['REQUEST_URI'] . '/ajax.php
RewriteCond %{REQUEST_URI} !^' . $_SERVER['REQUEST_URI'] . '/index.php
RewriteCond %{REQUEST_URI} !^' . $_SERVER['REQUEST_URI'] . '/install.php
RewriteCond %{REQUEST_URI} !^' . $_SERVER['REQUEST_URI'] . '/update.php
RewriteCond %{REQUEST_URI} !^' . $_SERVER['REQUEST_URI'] . '/images/
RewriteCond %{REQUEST_URI} !^' . $_SERVER['REQUEST_URI'] . '/skins/
RewriteCond %{REQUEST_URI} !^' . $_SERVER['REQUEST_URI'] . '/fckeditor/
RewriteRule ^(.*)$ index.php?run=$1 [QSA,L]
</IfModule>
');
                echo '<span class="bold">+ Configuration saved in config.php</span><br />' . "\n";

                requiredFeature('Cache generation', 'OTSCMS uses <span class="code">data/</span> directory resources including <span class="bold">OTBM</span> and items files. Loading them takes long time, so before first run you man need to change PHP configuration to turn off memory limit (set <span class="code">memory_limit = 4096M</span> in <span class="code">php.ini</span> file - it doesn\'t matter if you have so much RAM) and execution time limit (<span class="code">max_execution_time = 0</span>).</p>
                <p>This is only for first run as OTSCMS needs to generate cache for those files. After that you should switch your PHP configuration back to previous (backup your <span class="code">php.ini</span> file). Note that cache will need to be re-created after each items or OTBM file change.');
            }
            catch(Exception $e)
            {
                $sql->rollback();
                echo '<span class="critical,bold">! CRITICAL: ' . $e->getMessage() . '</span>' . "\n";
            }
            break;

        // first screen
        default:

            // checks if it is possible to handle mod_rewrite as Apache module
            if( !function_exists('apache_get_modules') )
            {
                requiredFeature('Not running on Apache', 'It seems that you are not running OTSCMS on Apache server. OTSCMS itself is not using any Apache-specyfic functions, but it uses "fiendly-URLs" with <span class="code">mod_rewrite</span>. Installer will generate <span class="code">.htaccess</span> file which will contain URLs rewriting rules. You have to configure your server to handle them somehow (it depends on server which you are using). For example for Microsoft IIS there are <span class="code">mod_rewrite</span> replacements called <a href="http://www.isapirewrite.com/">ISAPI_Rewrite</a> or <a href="http://www.qwerksoft.com/products/iisrewrite/">IIS Rewrite Engine</a>.');
            }
?>
<form action="install.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="command" value="database" />
<table>
<tbody>
    <tr>
        <td class="formLeft">Choose database type...</td>
        <td class="formRight">
            <label><input type="radio" name="db[type]" value="MySQL" selected="selected" />MySQL</label><br />
            <label><input type="radio" name="db[type]" value="SQLite" />SQLite</label><br />
            <label><input type="radio" name="db[type]" value="PostgreSQL" />PostgreSQL</label><br />
        </td>
    </tr>
    <tr>
        <td class="formLeft">...or submit your <span class="code">config.lua</span> file for auto-detection.</td>
        <td class="formRight">
            <input type="file" name="lua" />
        </td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit" value="Proceed" /></td>
    </tr>
</tbody>
</table>
</form>
<?php
    }
}

?>
</div>
            <div id="pageFooter">
                Powered by <a href="http://otscms.com/">OTSCMS</a> v <?php echo $version; ?>; Copyright &copy; 2005 - 2008 by <a href="http://www.wrzasq.com/" class="outLink">Wrzasq</a>.<br />
<a href="http://otserv-aac.info/">
    <img alt="This site was smoked" src="http://otserv-aac.info/pot.png"/>
</a>
            </div>
    </body>
</html>
