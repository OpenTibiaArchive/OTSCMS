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
    OTserv account row object.
*/

class OTS_Account extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
        // can load both by id and name
        $load = $this->db->prepare('SELECT `id`, `password`, `email`, `blocked`, `premdays`, `signature`, `avatar`, `website` FROM {accounts} WHERE `id` = :id');
        $load->execute( array(':id' => $id) );
        $this->data = $load->fetch();
    }

    // saves current record
    public function save()
    {
        // checks if id is set
        if( isset($this->data['id']) )
        {
            $save = $this->db->prepare('UPDATE {accounts} SET `password` = :password, `email` = :email, `blocked` = :blocked, `premdays` = :premdays, `signature` = :signature, `avatar` = :avatar, `website` = :website WHERE `id` = :id');
            $save->execute( array(':password' => $this->data['password'], ':email' => $this->data['email'], ':blocked' => $this->data['blocked'], ':premdays' => $this->data['premdays'], ':signature' => $this->data['signature'], ':avatar' => $this->data['avatar'], ':website' => $this->data['website'], ':id' => $this->data['id']) );
        }
        // if no then inserts it as new row
        else
        {
            $save = $this->db->prepare('INSERT INTO {accounts} (`password`, `email`, `blocked`, `premdays`, `signature`, `avatar`, `website`) VALUES (:password, :email, :blocked, :premdays, :signature, :avatar, :website)');
            $save->execute( array(':password' => $this->data['password'], ':email' => $this->data['email'], ':blocked' => $this->data['blocked'], ':premdays' => $this->data['premdays'], ':signature' => $this->data['signature'], ':avatar' => $this->data['avatar'], ':website' => $this->data['website']) );
            $this->data['id'] = $this->db->lastInsertId();
        }
    }

    // creates new account with defined id
    public function create($id)
    {
        $this->db->query('INSERT INTO {accounts} (`id`, `password`, `email`, `blocked`, `premdays`, `signature`, `avatar`, `website`) VALUES (' . $id . ', \'\', \'\', 0, 0, \'\', \'\', \'\')');
        $this->data['id'] = $id;
    }
}

?>
