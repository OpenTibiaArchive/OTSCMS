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
    Profile item.
*/

class CMS_Container extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
        $load = $this->db->prepare('SELECT `id`, `content`, `slot`, `count`, `profile` FROM [containers] WHERE `id` = :id');
        $load->execute( array(':id' => $id) );
        $this->data = $load->fetch();
    }

    // saves current record
    public function save()
    {
        // checks if id is set
        if( isset($this->data['id']) )
        {
            $save = $this->db->prepare('UPDATE [containers] SET `content` = :content, `slot` = :slot, `count` = :count, `profile` = :profile WHERE `id` = :id');
            $save->execute( array(':content' => $this->data['content'], ':slot' => $this->data['slot'], ':count' => $this->data['count'], ':profile' => $this->data['profile'], ':id' => $this->data['id']) );
        }
        // if no then inserts it as new row
        else
        {
            $save = $this->db->prepare('INSERT INTO [containers] (`content`, `slot`, `count`, `profile`) VALUES (:content, :slot, :count, :profile)');
            $save->execute( array(':content' => $this->data['content'], ':slot' => $this->data['slot'], ':count' => $this->data['count'], ':profile' => $this->data['profile']) );
            $this->data['id'] = $this->db->lastInsertId();
        }
    }

    // creates new container with defined id
    public function create($id, $profile)
    {
        $this->db->query('INSERT INTO [containers] (`id`, `profile`) VALUES (' . $id . ', ' . $profile . ')');
        $this->data['id'] = $number;
        $this->data['profile'] = $profile;
    }
}

?>
