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
    Rewritten URL object.
*/

class CMS_URL extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
        $load = $this->db->prepare('SELECT `name`, `content`, `order` FROM [urls] WHERE :id REGEXP `name` ORDER BY `order`');
        $load->execute( array(':id' => $id) );
        $this->data = $load->fetch();
    }

    // saves current record
    public function save()
    {
        // inserts it as new row
        $save = $this->db->prepare('INSERT INTO [urls] (`name`, `content`, `order`) VALUES (:name, :content, :order)');
        $save->execute( array(':name' => $this->data['name'], ':content' => $this->data['content'], ':order' => $this->data['order']) );
        $this->data['id'] = $this->db->lastInsertId();
    }
}

?>
