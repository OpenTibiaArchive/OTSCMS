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
    Cache for items.otb and items.xml spawns.
*/

class ItemsCache implements IOTS_ItemsCache
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

        $nodes = array();

        // creates nodes
        foreach($cache as $id => $record)
        {
            $node = new OTS_FileNode();
            $node->type = $record['name'];
            $node->buffer = $record['content'];

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
                $nodes[ $record['parent'] ]->child = $node;
            }

            // sets sibling relative
            if($record['previous'])
            {
                $nodes[ $record['previous'] ]->next = $node;
            }
        }

        return $root;
    }

    public function writeCache($md5, OTS_FileNode $root, $parent = 0)
    {
        // IDs counter
        static $i = 0;
        static $insert = null;
        static $types = array(OTS_ItemType::ITEM_GROUP_NONE, OTS_ItemType::ITEM_GROUP_CONTAINER, OTS_ItemType::ITEM_GROUP_WEAPON, OTS_ItemType::ITEM_GROUP_AMMUNITION, OTS_ItemType::ITEM_GROUP_ARMOR, OTS_ItemType::ITEM_GROUP_RUNE, OTS_ItemType::ITEM_GROUP_WRITEABLE, OTS_ItemType::ITEM_GROUP_KEY);

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
            if( in_array($root->type, $types) )
            {
                $insert->execute( array(':key' => $md5, ':id' => $i, ':name' => $root->type, ':content' => $root->buffer, ':parent' => $parent, ':previous' => $previous) );

                $child = $root->child;

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

            $root = $root->next;
        }
    }

    public function readItems($md5)
    {
        $items = array();

        // loads all cached types
        $cache = $this->db->prepare('SELECT `id`, `name`, `group`, `type` FROM [items] WHERE `key` = :key');
        $cache->execute( array(':key' => $md5) );

        foreach($cache as $item)
        {
            // composes type info from cache
            $type = new OTS_ItemType($item['id']);
            $type->name = $item['name'];
            $type->type = $item['type'];
            $type->group = $item['group'];
            $items[ $item['id'] ] = $type;
        }

        return $items;
    }

    public function writeItems($md5, $items)
    {
        // prepares statement for saving items
        $cache = $this->db->prepare('INSERT INTO [items] (`key`, `id`, `name`, `group`, `type`) VALUES (:key, :id, :name, :group, :type)');

        foreach($items as $item)
        {
            // leave only those which we need
            if($item->pickupable && strlen($item->name) > 0)
            {
                // saves item type
                $cache->execute( array(':key' => $md5, ':id' => $item->id, ':name' => $item->name, ':group' => $item->group, ':type' => $item->type) );
            }
        }
    }
}

?>
