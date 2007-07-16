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
    OTserv player row object.
*/

class OTS_Player extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
        // can load both by id and name
        if( is_numeric($id) )
        {
            $load = $this->db->prepare('SELECT `id`, `name`, `account_id`, `group_id`, `sex`, `vocation`, `experience`, `level`, `maglevel`, `health`, `healthmax`, `mana`, `manamax`, `manaspent`, `soul`, `direction`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `lookaddons`, `posx`, `posy`, `posz`, `cap`, `lastlogin`, `lastip`, `save`, `conditions`, `redskulltime`, `redskull`, `guildnick`, `rank_id`, `town_id`, `loss_experience`, `loss_mana`, `loss_skills`, `comment` FROM {players} WHERE `id` = :id');
        }
        else
        {
            $load = $this->db->prepare('SELECT `id`, `name`, `account_id`, `group_id`, `sex`, `vocation`, `experience`, `level`, `maglevel`, `health`, `healthmax`, `mana`, `manamax`, `manaspent`, `soul`, `direction`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `lookaddons`, `posx`, `posy`, `posz`, `cap`, `lastlogin`, `lastip`, `save`, `conditions`, `redskulltime`, `redskull`, `guildnick`, `rank_id`, `town_id`, `loss_experience`, `loss_mana`, `loss_skills`, `comment` FROM {players} WHERE `name` = :id');
        }
        $load->execute( array(':id' => $id) );
        $this->data = $load->fetch();
    }

    // saves current record
    public function save()
    {
        // checks if id is set
        if( isset($this->data['id']) )
        {
            $save = $this->db->prepare('UPDATE {players} SET `name` = :name, `account_id` = :account_id, `group_id` = :group_id, `sex` = :sex, `vocation` = :vocation, `experience` = :experience, `level` = :level, `maglevel` = :maglevel, `health` = :health, `healthmax` = :healthmax, `mana` = :mana, `manamax` = :manamax, `manaspent` = :manaspent, `soul` = :soul, `direction` = :direction, `lookbody` = :lookbody, `lookfeet` = :lookfeet, `lookhead` = :lookhead, `looklegs` = :looklegs, `looktype` = :looktype, `lookaddons` = :lookaddons, `posx` = :posx, `posy` = :posy, `posz` = :posz, `cap` = :cap, `lastlogin` = :lastlogin, `lastip` = :lastip, `save` = :save, `conditions` = :conditions, `redskulltime` = :redskulltime, `redskull` = :redskull, `guildnick` = :guildnick, `rank_id` = :rank_id, `town_id` = :town_id, `loss_experience` = :loss_experience, `loss_mana` = :loss_mana, `loss_skills` = :loss_skills, `comment` = :comment WHERE `id` = :id');
            $save->execute( array(':name' => $this->data['name'], ':account_id' => $this->data['account_id'], ':group_id' => $this->data['group_id'], ':sex' => $this->data['sex'], ':vocation' => $this->data['vocation'], ':experience' => $this->data['experience'], ':level' => $this->data['level'], ':maglevel' => $this->data['maglevel'], ':health' => $this->data['health'], ':healthmax' => $this->data['healthmax'], ':mana' => $this->data['mana'], ':manamax' => $this->data['manamax'], ':manaspent' => $this->data['manaspent'], ':soul' => $this->data['soul'], ':direction' => $this->data['direction'], ':lookbody' => $this->data['lookbody'], ':lookfeet' => $this->data['lookfeet'], ':lookhead' => $this->data['lookhead'], ':looklegs' => $this->data['looklegs'], ':looktype' => $this->data['looktype'], ':lookaddons' => $this->data['lookaddons'], ':posx' => $this->data['posx'], ':posy' => $this->data['posy'], ':posz' => $this->data['posz'], ':cap' => $this->data['cap'], ':lastlogin' => $this->data['lastlogin'], ':lastip' => $this->data['lastip'], ':save' => $this->data['save'], ':conditions' => $this->data['conditions'], ':redskulltime' => $this->data['redskulltime'], ':redskull' => $this->data['redskull'], ':guildnick' => $this->data['guildnick'], ':rank_id' => $this->data['rank_id'], ':town_id' => $this->data['town_id'], ':loss_experience' => $this->data['loss_experience'], ':loss_mana' => $this->data['loss_mana'], ':loss_skills' => $this->data['loss_skills'], ':comment' => $this->data['comment'], ':id' => $this->data['id']) );
        }
        // if no then inserts it as new row
        else
        {
            $save = $this->db->prepare('INSERT INTO {players} (`name`, `account_id`, `group_id`, `sex`, `vocation`, `experience`, `level`, `maglevel`, `health`, `healthmax`, `mana`, `manamax`, `manaspent`, `soul`, `direction`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `lookaddons`, `posx`, `posy`, `posz`, `cap`, `lastlogin`, `lastip`, `save`, `conditions`, `redskulltime`, `redskull`, `guildnick`, `rank_id`, `town_id`, `loss_experience`, `loss_mana`, `loss_skills`, `comment`) VALUES (:name, :account_id, :group_id, :sex, :vocation, :experience, :level, :maglevel, :health, :healthmax, :mana, :manamax, :manaspent, :soul, :direction, :lookbody, :lookfeet, :lookhead, :looklegs, :looktype, :lookaddons, :posx, :posy, :posz, :cap, :lastlogin, :lastip, :save, :conditions, :redskulltime, :redskull, :guildnick, :rank_id, :town_id, :loss_experience, :loss_mana, :loss_skills, :comment)');
            $save->execute( array(':name' => $this->data['name'], ':account_id' => $this->data['account_id'], ':group_id' => $this->data['group_id'], ':sex' => $this->data['sex'], ':vocation' => $this->data['vocation'], ':experience' => $this->data['experience'], ':level' => $this->data['level'], ':maglevel' => $this->data['maglevel'], ':health' => $this->data['health'], ':healthmax' => $this->data['healthmax'], ':mana' => $this->data['mana'], ':manamax' => $this->data['manamax'], ':manaspent' => $this->data['manaspent'], ':soul' => $this->data['soul'], ':direction' => $this->data['direction'], ':lookbody' => $this->data['lookbody'], ':lookfeet' => $this->data['lookfeet'], ':lookhead' => $this->data['lookhead'], ':looklegs' => $this->data['looklegs'], ':looktype' => $this->data['looktype'], ':lookaddons' => $this->data['lookaddons'], ':posx' => $this->data['posx'], ':posy' => $this->data['posy'], ':posz' => $this->data['posz'], ':cap' => $this->data['cap'], ':lastlogin' => $this->data['lastlogin'], ':lastip' => $this->data['lastip'], ':save' => $this->data['save'], ':conditions' => $this->data['conditions'], ':redskulltime' => $this->data['redskulltime'], ':redskull' => $this->data['redskull'], ':guildnick' => $this->data['guildnick'], ':rank_id' => $this->data['rank_id'], ':town_id' => $this->data['town_id'], ':loss_experience' => $this->data['loss_experience'], ':loss_mana' => $this->data['loss_mana'], ':loss_skills' => $this->data['loss_skills'], ':comment' => $this->data['comment']) );
            $this->data['id'] = $this->db->lastInsertId();
        }
    }
}

?>
