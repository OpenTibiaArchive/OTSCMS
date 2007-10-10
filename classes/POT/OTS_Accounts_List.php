<?php

/**#@+
 * @version 0.0.1
 */

/**
 * @package POT
 * @version 0.0.3
 * @author Wrzasq <wrzasq@gmail.com>
 * @copyright 2007 (C) by Wrzasq
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 */

/**
 * List of accounts.
 * 
 * @package POT
 * @version 0.0.3
 */
class OTS_Accounts_List implements IOTS_DAO, Iterator, Countable
{
/**
 * Database connection.
 * 
 * @var IOTS_DB
 */
    private $db;

/**
 * Limit for SELECT query.
 * 
 * @var int
 */
    private $limit = false;

/**
 * Offset for SELECT query.
 * 
 * @var int
 */
    private $offset = false;

/**
 * Query results.
 * 
 * @var array
 */
    private $rows;

/**
 * Sets database connection handler.
 * 
 * @param IOTS_DB $db Database connection object.
 */
    public function __construct(IOTS_DB $db)
    {
        $this->db = $db;
    }

/**
 * Magic PHP5 method.
 * 
 * Allows object serialisation.
 * 
 * @return array List of properties that should be saved.
 * @internal Magic PHP5 method.
 */
    public function __sleep()
    {
        return array('limit', 'offset');
    }

/**
 * Magic PHP5 method.
 * 
 * Allows object unserialisation.
 * 
 * @internal Magic PHP5 method.
 */
    public function __wakeup()
    {
        $this->db = POT::getInstance()->getDBHandle();
    }

/**
 * Sets LIMIT.
 * 
 * @param int|bool Limit for SELECT (false to reset).
 */
    public function setLimit($limit = false)
    {
        if( is_int($limit) )
        {
            $this->limit = $limit;
        }
        else
        {
            $this->limit = false;
        }
    }

/**
 * Sets OFFSET.
 * 
 * @param int|bool Offset for SELECT (false to reset).
 */
    public function setOffset($offset = false)
    {
        if( is_int($offset) )
        {
            $this->offset = $offset;
        }
        else
        {
            $this->offset = false;
        }
    }

/**
 * Deletes account.
 * 
 * @version 0.0.3
 * @param OTS_Account $account Account to be deleted.
 */
    public function deleteAccount(OTS_Account $account)
    {
        $this->db->SQLquery('DELETE FROM ' . $this->db->tableName('account') . ' WHERE ' . $this->db->fieldName('id') . ' = ' . $account->getId() );
    }

/**
 * Returns current row.
 * 
 * @return OTS_Account Current account.
 */
    public function current()
    {
        $id = current($this->rows);

        $account = POT::getInstance()->createObject('Account');
        $account->load($id['id']);
        return $account;
    }

/**
 * Moves to next row.
 */
    public function next()
    {
        next($this->rows);
    }

/**
 * Current cursor position.
 * 
 * @return mixed Array key.
 */
    public function key()
    {
        return key($this->rows);
    }

/**
 * Checks if there are any rows left.
 * 
 * @return bool Does next row exist.
 */
    public function valid()
    {
        return key($this->rows) !== null;
    }

/**
 * Select accounts from database.
 */
    public function rewind()
    {
        $this->rows = $this->db->SQLquery('SELECT ' . $this->db->fieldName('id') . ' FROM ' . $this->db->tableName('accounts') . $this->db->limit($this->limit, $this->offset) )->fetchAll();
    }

/**
 * Returns number of accounts on list in current criterium.
 * 
 * @return int Number of accounts.
 */
    public function count()
    {
        $count = $this->db->SQLquery('SELECT COUNT(' . $this->db->fieldName('id') . ') AS ' . $this->db->fieldName('count') . ' FROM ' . $this->db->tableName('accounts') . $this->db->limit($this->limit, $this->offset) )->fetch();
        return $count['count'];
    }
}

/**#@-*/

?>
