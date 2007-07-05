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
    OTserv guild rank row object.
*/

class OTS_GuildRank extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
        // can load both by id and name
        $load = $this->db->prepare('SELECT `id`, `guild_id`, `name`, `level` FROM {guild_ranks} WHERE `id` = :id');
        $load->execute( array(':id' => $id) );
        $this->data = $load->fetch();
    }

    // saves current record
    public function save()
    {
        // checks if id is set
        if( isset($this->data['id']) )
        {
            $save = $this->db->prepare('UPDATE {guild_ranks} SET `guild_id` = :guild_id, `name` = :name, `level` = :level WHERE `id` = :id');
            $save->execute( array(':guild_id' => $this->data['guild_id'], ':name' => $this->data['name'], ':level' => $this->data['level'], ':id' => $this->data['id']) );
        }
        // if no then inserts it as new row
        else
        {
            $save = $this->db->prepare('INSERT INTO {guild_ranks} (`guild_id`, `name`, `level`) VALUES (:guild_id, :name, :level)');
            $save->execute( array(':guild_id' => $this->data['guild_id'], ':name' => $this->data['name'], ':level' => $this->data['level']) );
            $this->data['id'] = $this->db->lastInsertId();
        }
    }
}

?>
