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
    Image file object.
*/

class CMS_Gallery extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
        $load = $this->db->prepare('SELECT `id`, `name`, `content`, `binary`, `file` FROM [gallery] WHERE `id` = :id');
        $load->execute( array(':id' => $id) );
        $this->data = $load->fetch();
    }

    // saves current record
    public function save()
    {
        // checks if id is set
        if( isset($this->data['id']) )
        {
            $save = $this->db->prepare('UPDATE [gallery] SET `name` = :name, `content` = :content, `binary` = :binary, `file` = :file WHERE `id` = :id');
            $save->execute( array(':name' => $this->data['name'], ':content' => $this->data['content'], ':binary' => $this->data['binary'], ':file' => $this->data['file'], ':id' => $this->data['id']) );
        }
        // if no then inserts it as new row
        else
        {
            $save = $this->db->prepare('INSERT INTO [gallery] (`name`, `content`, `binary`, `file`) VALUES (:name, :content, :binary, :file)');
            $save->execute( array(':name' => $this->data['name'], ':content' => $this->data['content'], ':binary' => $this->data['binary'], ':file' => $this->data['file']) );
            $this->data['id'] = $this->db->lastInsertId();
        }
    }
}

?>
