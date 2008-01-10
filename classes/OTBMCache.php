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

/*
    Cache for OTBM spawns.
*/

class OTBMCache implements IOTS_FileCache
{
    private $db;

    // attaches database handler
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function readCache($md5)
    {
        // reads cache for given key
        $select = $this->db->prepare('SELECT `id`, `name`, `content`, `parent`, `previous` FROM [cache] WHERE `key` = :key');
        $select->execute( array(':key' => $md5) );

        $cache = array();

        // reads all nodes
        foreach($select as $record)
        {
            $cache[ $record['id'] ] = $record;
        }

        // no cache
        if( empty($cache) )
        {
            return null;
        }

        // default root node
        $root = new OTS_FileNode();
        $root->setType(OTS_OTBMFile::OTBM_NODE_ROOTV1);

        $nodes = array();

        // creates nodes
        foreach($cache as $id => $record)
        {
            $node = new OTS_FileNode();
            $node->setType($record['name']);
            $node->setBuffer($record['content']);

            $nodes[$id] = $node;

            // this is the root one
            if(!($record['parent'] || $record['previous']))
            {
                $root = $node;
            }
        }

        // composes tree
        foreach($nodes as $id => $node)
        {
            $record = $cache[$id];

            // sets child relative
            if($record['parent'])
            {
                $nodes[ $record['parent'] ]->setChild($node);
            }

            // sets sibling relative
            if($record['previous'])
            {
                $nodes[ $record['previous'] ]->setNext($node);
            }
        }

        return $root;
    }

    public function writeCache($md5, OTS_FileNode $root, $parent = 0)
    {
        // IDs counter
        static $i = 0;
        static $insert = null;
        static $types = array(OTS_OTBMFile::OTBM_NODE_ROOTV1, OTS_OTBMFile::OTBM_NODE_MAP_DATA, OTS_OTBMFile::OTBM_NODE_TOWNS, OTS_OTBMFile::OTBM_NODE_TOWN);

        // initial loop info
        $i++;
        $previous = 0;

        // prepares query
        if( !isset($insert) )
        {
            $insert = $this->db->prepare('INSERT INTO [cache] (`key`, `id`, `name`, `content`, `parent`, `previous`) VALUES (:key, :id, :name, :content, :parent, :previous)');
        }

        // saves nodes tree
        while($root)
        {
            // checks if we have to save this node - skip not used by OTSCMS
            if( in_array( $root->getType(), $types) )
            {
                $insert->execute( array(':key' => $md5, ':id' => $i, ':name' => $root->getType(), ':content' => $root->getBuffer(), ':parent' => $parent, ':previous' => $previous) );

                $child = $root->getChild();

                // saves child node
                if( isset($child) )
                {
                    $this->writeCache($md5, $child, $i);
                }

                // sets values for next node
                $parent = 0;
                $previous = $i;
                $i++;
            }

            $root = $root->getNext();
        }
    }
}

?>
