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
    POT guilds invites driver.
*/

class InvitesDriver implements IOTS_GuildAction
{
    // assigned guild
    private $guild;
    // database
    private $db;

    // initializes driver
    public function __construct(OTS_Guild $guild)
    {
        $this->guild = $guild;
        $this->db = OTSCMS::getResource('DB');

        $this->guild->setInvitesDriver($this);
    }

    // returns all invited players to current guild
    public function listRequests()
    {
        $invites = array();

        foreach( $this->db->query('SELECT `name` FROM [invites] WHERE `content` = ' . $this->guild->id) as $invite)
        {
            $player = new OTS_Player();
            $player->load($invite['name']);
            $invites[] = $player;
        }

        return $invites;
    }

    // invites player to current guild
    public function addRequest(OTS_Player $player)
    {
        $this->db->query('INSERT INTO [invites] (`name`, `content`) VALUES (' . $player->id . ', ' . $this->guild->id . ')');
    }

    // un-invites player
    public function deleteRequest(OTS_Player $player)
    {
        $this->db->query('DELETE FROM [invites] WHERE `name` = ' . $player->id . ' AND `content` = ' . $this->guild->id);
    }

    // commits invitation
    public function submitRequest(OTS_Player $player)
    {
        $rank = null;

        // finds normal member rank
        foreach($this->guild as $guildRank)
        {
            if($guildRank->level == 1)
            {
                $rank = $guildRank;
                break;
            }
        }

        $player->rank = $rank;
        $player->save();

        // clears invitation
        $this->deleteRequest($player);
    }
}

?>
