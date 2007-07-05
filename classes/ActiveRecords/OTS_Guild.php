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
    OTserv guild row object.
*/

class OTS_Guild extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
        // can load both by id and name
        $load = $this->db->prepare('SELECT `id`, `name`, `ownerid`, `creationdata` FROM {guilds} WHERE `id` = :id');
        $load->execute( array(':id' => $id) );
        $this->data = $load->fetch();
    }

    // saves current record
    public function save()
    {
        // checks if id is set
        if( isset($this->data['id']) )
        {
            $save = $this->db->prepare('UPDATE {guilds} SET `name` = :name, `ownerid` = :ownerid, `creationdata` = :creationdata WHERE `id` = :id');
            $save->execute( array(':name' => $this->data['name'], ':ownerid' => $this->data['ownerid'], ':creationdata' => $this->data['creationdata'], ':id' => $this->data['id']) );
        }
        // if no then inserts it as new row
        else
        {
            $save = $this->db->prepare('INSERT INTO {guilds} (`name`, `ownerid`, `creationdata`) VALUES (:name, :ownerid, :creationdata)');
            $save->execute( array(':name' => $this->data['name'], ':ownerid' => $this->data['ownerid'], ':creationdata' => $this->data['creationdata']) );
            $this->data['id'] = $this->db->lastInsertId();
        }
    }
}

?>
