<?php

/**#@+
 * @version 0.0.1
 */

/**
 * This file contains main toolkit class. Please read README file for quick startup guide and/or tutorials for more info.
 * 
 * @package POT
 * @version 0.0.3+SVN
 * @author Wrzasq <wrzasq@gmail.com>
 * @copyright 2007 (C) by Wrzasq
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 */

/**
 * Main POT class.
 * 
 * @package POT
 * @version 0.0.3+SVN
 */
class POT
{
/**
 * MySQL driver.
 */
    const DB_MYSQL = 1;
/**
 * SQLite driver.
 */
    const DB_SQLITE = 2;
/**
 * PostgreSQL driver.
 * 
 * @version 0.0.3+SVN
 * @since 0.0.3+SVN
 */
    const DB_PGSQL = 3;
/**
 * ODBC driver.
 * 
 * @version 0.0.3+SVN
 * @since 0.0.3+SVN
 */
    const DB_ODBC = 4;

/**
 * Female gender.
 */
    const SEX_FEMALE = 0;
/**
 * Male gender.
 */
    const SEX_MALE = 1;

/**
 * None vocation.
 */
    const VOCATION_NONE = 0;
/**
 * Sorcerer.
 */
    const VOCATION_SORCERER = 1;
/**
 * Druid.
 */
    const VOCATION_DRUID = 2;
/**
 * Paladin.
 */
    const VOCATION_PALADIN = 3;
/**
 * Knight.
 */
    const VOCATION_KNIGHT = 4;

/**
 * North.
 */
    const DIRECTION_NORTH = 0;
/**
 * East.
 */
    const DIRECTION_EAST = 1;
/**
 * South.
 */
    const DIRECTION_SOUTH = 2;
/**
 * West.
 */
    const DIRECTION_WEST = 3;

/**
 * Fist fighting.
 * 
 * @version 0.0.2
 * @since 0.0.2
 */
    const SKILL_FIST = 0;
/**
 * Club fighting.
 * 
 * @version 0.0.2
 * @since 0.0.2
 */
    const SKILL_CLUB = 1;
/**
 * Sword fighting.
 * 
 * @version 0.0.2
 * @since 0.0.2
 */
    const SKILL_SWORD = 2;
/**
 * Axe fighting.
 * 
 * @version 0.0.2
 * @since 0.0.2
 */
    const SKILL_AXE = 3;
/**
 * Distance fighting.
 * 
 * @version 0.0.2
 * @since 0.0.2
 */
    const SKILL_DISTANCE = 4;
/**
 * Shielding.
 * 
 * @version 0.0.2
 * @since 0.0.2
 */
    const SKILL_SHIELDING = 5;
/**
 * Fishing.
 * 
 * @version 0.0.2
 * @since 0.0.2
 */
    const SKILL_FISHING = 6;

/**
 * Head slot.
 * 
 * @version 0.0.3
 * @since 0.0.3
 */
    const SLOT_HEAD = 1;
/**
 * Necklace slot.
 * 
 * @version 0.0.3
 * @since 0.0.3
 */
    const SLOT_NECKLACE = 2;
/**
 * Backpack slot.
 * 
 * @version 0.0.3
 * @since 0.0.3
 */
    const SLOT_BACKPACK = 3;
/**
 * Armor slot.
 * 
 * @version 0.0.3
 * @since 0.0.3
 */
    const SLOT_ARMOR = 4;
/**
 * Right hand slot.
 * 
 * @version 0.0.3
 * @since 0.0.3
 */
    const SLOT_RIGHT = 5;
/**
 * Left hand slot.
 * 
 * @version 0.0.3
 * @since 0.0.3
 */
    const SLOT_LEFT = 6;
/**
 * Legs slot.
 * 
 * @version 0.0.3
 * @since 0.0.3
 */
    const SLOT_LEGS = 7;
/**
 * Boots slot.
 * 
 * @version 0.0.3
 * @since 0.0.3
 */
    const SLOT_FEET = 8;
/**
 * Ring slot.
 * 
 * @version 0.0.3
 * @since 0.0.3
 */
    const SLOT_RING = 9;
/**
 * Ammunition slot.
 * 
 * @version 0.0.3
 * @since 0.0.3
 */
    const SLOT_AMMO = 10;

/**
 * First depot item sid.
 * 
 * @version 0.0.3+SVN
 * @since 0.0.3+SVN
 */
    const DEPOT_SID_FIRST = 100;

/**
 * Singleton.
 * 
 * @return POT Global POD class instance.
 */
    public static function getInstance()
    {
        static $instance;

        // creates new instance
        if( !isset($instance) )
        {
            $instance = new self;
        }

        return $instance;
    }

/**
 * POT classes directory.
 * 
 * Directory path to POT files.
 * 
 * @var string
 * @see POT::setPOTPath()
 */
    private $path = '';

/**
 * Set POT directory.
 * 
 * Use this method if you keep your POT package in different directory then this file.
 * 
 * @param string $path POT files path.
 * @example examples/fakeroot.php fakeroot.php
 */
    public function setPOTPath($path)
    {
        $this->path = str_replace('\\', '/', $path);

        // appends ending slash to directory path
        if( substr($this->path, -1) != '/')
        {
            $this->path .= '/';
        }
    }

/**
 * Class initialization tools.
 * 
 * Never create instance of this class by yourself! Use POT::getInstance()!
 * 
 * @version 0.0.3
 * @see POT::getInstance()
 * @internal
 */
    private function __construct()
    {
        // default POT directory
        $this->path = dirname(__FILE__) . '/';
        // registers POT autoload mechanism
        spl_autoload_register( array($this, 'loadClass') );
    }

/**
 * Loads POT class file.
 * 
 * Runtime class loading on demand - usefull for __autoload() function.
 * 
 * <p>
 * Note: Since 0.0.2 version this function is suitable for spl_autoload_register().
 * </p>
 * 
 * <p>
 * Note: Since 0.0.3 version this function handles also exceptions.
 * </p>
 * 
 * @version 0.0.3
 * @param string $class Class name.
 * @example examples/autoload.php autoload.php
 */
    public function loadClass($class)
    {
        if( preg_match('/^(I|E_)?OTS_/', $class) > 0)
        {
            include_once($this->path . $class . '.php');
        }
    }

/**
 * Database connection.
 * 
 * OTServ database connection object.
 * 
 * @var IOTS_DB
 */
    private $db;

/**
 * Connects to database.
 * 
 * Creates OTServ database connection object.
 * 
 * <p>
 * First parameter is one of database driver constants values. Currently MySQL, SQLite, PostgreSQL and ODBC drivers are supported.<br />
 * This parameter can be null, then you have to specify <var>'driver'</var> parameter.<br />
 * Such way is comfortable to store entire database configuration in one array and possibly runtime evaluation and/or configuration file saving.<br />
 * </p>
 * 
 * <p>
 * For parameters list see driver documentation. Common parameters for all drivers are:
 * </p>
 * 
 * - <var>driver</var> - optional, specifies driver, aplies when <var>$driver</var> method parameter is <i>null</i>
 * - <var>prefix</var> - optional, prefix for database tables, use if you have more then one OTServ installed on one database.
 * 
 * @version 0.0.3+SVN
 * @param int|null $driver Database driver type.
 * @param array $params Connection info.
 * @throws Exception When driver is not supported.
 * @example examples/connect.php connect.php
 */
    public function connect($driver, $params)
    {
        // $params['driver'] option instead of $driver
        if( !isset($driver) )
        {
            if( isset($params['driver']) )
            {
                $driver = $params['driver'];
            }
            else
            {
                throw new Exception('You must specify database driver to connect with.');
            }
        }
        unset($params['driver']);

        // switch() structure provides us further flexibility
        switch($driver)
        {
            // MySQL database
            case self::DB_MYSQL:
                $this->db = new OTS_DB_MySQL($params);
                break;

            // SQLite database
            case self::DB_SQLITE:
                $this->db = new OTS_DB_SQLite($params);
                break;

            // SQLite database
            case self::DB_PGSQL:
                $this->db = new OTS_DB_PostgreSQL($params);
                break;

            // SQLite database
            case self::DB_ODBC:
                $this->db = new OTS_DB_ODBC($params);
                break;

            // unsupported driver
            default:
                throw new Exception('Driver \'' . $driver . '\' is not supported.');
        }
    }

/**
 * Creates OTServ DAO class instance.
 * 
 * @param string $class Class name.
 * @return IOTS_DAO OTServ database object.
 */
    public function createObject($class)
    {
        $class = 'OTS_' . $class;
        return new $class($this->db);
    }

/**
 * Queries server status.
 * 
 * Sends 'info' packet to OTS server and return output.
 * 
 * @version 0.0.2
 * @since 0.0.2
 * @param string $server Server IP/domain.
 * @param int $port OTServ port.
 * @return OTS_InfoRespond|bool Respond content document (false when server is offline).
 * @example examples/info.php
 */
    public function serverStatus($server, $port)
    {
        // connects to server
        // gives maximum 5 seconds to connect
        $socket = fsockopen($server, $port, $error, $message, 5);

        // if connected then checking statistics
        if($socket)
        {
            // sets 5 second timeout for reading and writing
            stream_set_timeout($socket, 5);

            // sends packet with request
            // 06 - length of packet, 255, 255 is the comamnd identifier, 'info' is a request
            fwrite($socket, chr(6).chr(0).chr(255).chr(255).'info');

            // reads respond
            $data = stream_get_contents($socket);

            // closing connection to current server
            fclose($socket);

            // sometimes server returns empty info
            if( empty($data) )
            {
                // returns offline state
                return false;
            }

            // loads respond XML
            $info = new OTS_InfoRespond();
            $info->loadXML($data);
            return $info;
        }
        // returns offline state
        else
        {
            return false;
        }
    }

/**
 * Returns database connection handle.
 * 
 * <p>
 * At all you shouldn't use this method and work with database using POT classes, but it may be sometime necessary to use direct database access (mainly until POT won't provide many important features).
 * </p>
 * 
 * <p>
 * It is also important as serialised objects after unserialisation needs to be re-initialised with database connection.
 * </p>
 * 
 * @version 0.0.3+SVN
 * @since 0.0.3+SVN
 * @return IOTS_DB Database connection handle.
 * @internal You should not call this function in your external code without real need.
 */
    public function getDBHandle()
    {
        return $this->db;
    }
}

/**#@-*/

?>
