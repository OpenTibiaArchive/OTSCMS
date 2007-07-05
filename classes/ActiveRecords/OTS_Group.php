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
    OTserv group row object.
*/

class OTS_Group extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
        // can load both by id and name
        $load = $this->db->prepare('SELECT `id`, `name`, `flags`, `access`, `maxdepotitems`, `maxviplist` FROM {groups} WHERE `id` = :id');
        $load->execute( array(':id' => $id) );
        $this->data = $load->fetch();
    }

    // saves current record
    public function save()
    {
        // checks if id is set
        if( isset($this->data['id']) )
        {
            $save = $this->db->prepare('UPDATE {groups} SET `name` = :name, `flags` = :flags, `access` = :access, `maxdepotitems` = :maxdepotitems, `maxviplist` = :maxviplist WHERE `id` = :id');
            $save->execute( array(':name' => $this->data['name'], ':flags' => $this->data['flags'], ':access' => $this->data['access'], ':maxdepotitems' => $this->data['maxdepotitems'], ':maxviplist' => $this->data['maxviplist'], ':id' => $this->data['id']) );
        }
        // if no then inserts it as new row
        else
        {
            $save = $this->db->prepare('INSERT INTO {groups} (`name`, `flags`, `access`, `maxdepotitems`, `maxviplist`) VALUES (:name, :flags, :access, :maxdepotitems, :maxviplist)');
            $save->execute( array(':name' => $this->data['name'], ':flags' => $this->data['flags'], ':access' => $this->data['access'], ':maxdepotitems' => $this->data['maxdepotitems'], ':maxviplist' => $this->data['maxviplist']) );
            $this->data['id'] = $this->db->lastInsertId();
        }
    }
}

?>
