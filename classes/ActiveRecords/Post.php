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
    Forum post.
*/

class CMS_Post extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
        $load = $this->db->prepare('SELECT `id`, `name`, `istopic`, `upperid`, `closed`, `pinned`, `content`, `poster`, `date_time` FROM [posts] WHERE `id` = :id');
        $load->execute( array(':id' => $id) );
        $this->data = $load->fetch();
    }

    // saves current record
    public function save()
    {
        // checks if id is set
        if( isset($this->data['id']) )
        {
            $save = $this->db->prepare('UPDATE [posts] SET `name` = :name, `istopic` = :istopic, `upperid` = :upperid, `closed` = :closed, `pinned` = :pinned, `content` = :content, `poster` = :poster, `date_time` = :date_time WHERE `id` = :id');
            $save->execute( array(':name' => $this->data['name'], ':istopic' => $this->data['istopic'], ':upperid' => $this->data['upperid'], ':closed' => $this->data['closed'], ':pinned' => $this->data['pinned'], ':content' => $this->data['content'], ':poster' => $this->data['poster'], ':date_time' => $this->data['date_time'], ':id' => $this->data['id']) );
        }
        // if no then inserts it as new row
        else
        {
            $save = $this->db->prepare('INSERT INTO [posts] (`name`, `istopic`, `upperid`, `closed`, `pinned`, `content`, `poster`, `date_time`) VALUES (:name, :istopic, :upperid, :closed, :pinned, :content, :poster, :date_time)');
            $save->execute( array(':name' => $this->data['name'], ':istopic' => $this->data['istopic'], ':upperid' => $this->data['upperid'], ':closed' => $this->data['closed'], ':pinned' => $this->data['pinned'], ':content' => $this->data['content'], ':poster' => $this->data['poster'], ':date_time' => $this->data['date_time']) );
            $this->data['id'] = $this->db->lastInsertId();
        }
    }
}

?>
