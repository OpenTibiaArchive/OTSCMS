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
    Poll option row object.
*/

class CMS_Option extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
        $load = $this->db->prepare('SELECT `id`, `name`, `poll` FROM [options] WHERE `id` = :id');
        $load->execute( array(':id' => $id) );
        $this->data = $load->fetch();
    }

    // saves current record
    public function save()
    {
        // checks if id is set
        if( isset($this->data['id']) )
        {
            $save = $this->db->prepare('UPDATE [options] SET `name` = :name, `poll` = :poll WHERE `id` = :id');
            $save->execute( array(':name' => $this->data['name'], ':poll' => $this->data['poll'], ':id' => $this->data['id']) );
        }
        // if no then inserts it as new row
        else
        {
            $save = $this->db->prepare('INSERT INTO [options] (`name`, `poll`) VALUES (:name, :poll)');
            $save->execute( array(':name' => $this->data['name'], ':poll' => $this->data['poll']) );
            $this->data['id'] = $this->db->lastInsertId();
        }
    }
}

?>
