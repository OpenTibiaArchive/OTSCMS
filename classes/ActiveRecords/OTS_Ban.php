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
    OTserv ban row object.
*/

class OTS_Ban extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
    }

    // saves current record
    public function save()
    {
        // inserts it as new row
        $save = $this->db->prepare('INSERT INTO {bans} (`type`, `ip`, `mask`, `player`, `account`, `time`) VALUES (:type, :ip, :mask, :player, :account, :time)');
        $save->execute( array(':type' => $this->data['type'], ':ip' => $this->data['ip'], ':mask' => $this->data['mask'], ':player' => $this->data['player'], ':account' => $this->data['account'], ':time' => $this->data['time']) );
    }
}

?>
