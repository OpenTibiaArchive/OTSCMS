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
    POT guilds membership requests driver.
*/

class RequestsDriver implements IOTS_GuildAction
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

        $this->guild->setRequestsDriver($this);
    }

    // returns all invited players to current guild
    public function listRequests()
    {
        $invites = array();
        $ots = POT::getInstance();

        foreach( $this->db->query('SELECT `name` FROM [requests] WHERE `content` = ' . $this->guild->getId() ) as $invite)
        {
            $player = $ots->createObject('Player');
            $player->load($invite['name']);
            $invites[] = $player;
        }

        return $invites;
    }

    // invites player to current guild
    public function addRequest(OTS_Player $player)
    {
        $this->db->query('INSERT INTO [requests] (`name`, `content`) VALUES (' . $player->getId() . ', ' . $this->guild->getId() . ')');
    }

    // un-invites player
    public function deleteRequest(OTS_Player $player)
    {
        $this->db->query('DELETE FROM [requests] WHERE `name` = ' . $player->getId() . ' AND `content` = ' . $this->guild->getId() );
    }

    // commits invitation
    public function submitRequest(OTS_Player $player)
    {
        $rank = null;

        // finds normal member rank
        foreach( $this->guild->getGuildRanks() as $guildRank)
        {
            if( $guildRank->getLevel() == 1)
            {
                $rank = $guildRank;
                break;
            }
        }

        $player->setRank($rank);
        $player->save();

        // clears invitation
        $this->deleteRequest($player);
    }
}

?>
