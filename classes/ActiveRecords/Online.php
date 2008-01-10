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
    OTS server row object.
*/

class CMS_Online extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
        $load = $this->db->prepare('SELECT `id`, `name`, `content`, `port`, `maximum` FROM [online] WHERE `id` = :id');
        $load->execute( array(':id' => $id) );
        $this->data = $load->fetch();
    }

    // saves current record
    public function save()
    {
        // checks if id is set
        if( isset($this->data['id']) )
        {
            $save = $this->db->prepare('UPDATE [online] SET `name` = :name, `content` = :content, `port` = :port, `maximum` = :maximum WHERE `id` = :id');
            $save->execute( array(':name' => $this->data['name'], ':content' => $this->data['content'], ':port' => $this->data['port'], ':maximum' => $this->data['maximum'], ':id' => $this->data['id']) );
        }
        // if no then inserts it as new row
        else
        {
            $save = $this->db->prepare('INSERT INTO [online] (`name`, `content`, `port`, `maximum`) VALUES (:name, :content, :port, :maximum)');
            $save->execute( array(':name' => $this->data['name'], ':content' => $this->data['content'], ':port' => $this->data['port'], ':maximum' => $this->data['maximum']) );
            $this->data['id'] = $this->db->lastInsertId();
        }
    }
}

?>
