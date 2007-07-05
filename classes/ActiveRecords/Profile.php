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
    Character profile.
*/

class CMS_Profile extends ActiveRecord
{
    // loads record by given ID
    public function load($id)
    {
        if( preg_match('/^[0-9]+$/', $id) )
        {
            $load = $this->db->prepare('SELECT `id`, `name`, `skill0`, `skill1`, `skill2`, `skill3`, `skill4`, `skill5`, `skill6`, `health`, `healthmax`, `direction`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `cap`, `food` FROM [profiles] WHERE `id` = :id');
            $load->execute( array(':id' => $id) );
            $this->data = $load->fetch();
        }
        // loads by name
        else
        {
            $load = $this->db->prepare('SELECT `id`, `name`, `skill0`, `skill1`, `skill2`, `skill3`, `skill4`, `skill5`, `skill6`, `health`, `healthmax`, `direction`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `cap`, `food` FROM [profiles] WHERE `name` = :id');
            $load->execute( array(':id' => $id) );
            $this->data = $load->fetch();
        }
    }

    // saves current record
    public function save()
    {
        // checks if id is set
        if( isset($this->data['id']) )
        {
            $save = $this->db->prepare('UPDATE [profiles] SET `name` = :name, `skill0` = :skill0, `skill1` = :skill1, `skill2` = :skill2, `skill3` = :skill3, `skill4` = :skill4, `skill5` = :skill5, `skill6` = :skill6, `health` = :health, `healthmax` = :healthmax, `direction` = :direction, `experience` = :experience, `lookbody` = :lookbody, `lookfeet` = :lookfeet, `lookhead` = :lookhead, `looklegs` = :looklegs, `looktype` = :looktype, `maglevel` = :maglevel, `mana` = :mana, `manamax` = :manamax, `manaspent` = :manaspent, `soul` = :soul, `cap` = :cap, `food` = :food WHERE `id` = :id');
            $save->execute( array(':name' => $this->data['name'], ':skill0' => $this->data['skill0'], ':skill1' => $this->data['skill1'], ':skill2' => $this->data['skill2'], ':skill3' => $this->data['skill3'], ':skill4' => $this->data['skill4'], ':skill5' => $this->data['skill5'], ':skill6' => $this->data['skill6'], ':health' => $this->data['health'], ':healthmax' => $this->data['healthmax'], ':direction' => $this->data['direction'], ':experience' => $this->data['experience'], ':lookbody' => $this->data['lookbody'], ':lookfeet' => $this->data['lookfeet'], ':lookhead' => $this->data['lookhead'], ':looklegs' => $this->data['looklegs'], ':looktype' => $this->data['looktype'], ':maglevel' => $this->data['maglevel'], ':mana' => $this->data['mana'], ':manamax' => $this->data['manamax'], ':manaspent' => $this->data['manaspent'], ':soul' => $this->data['soul'], ':cap' => $this->data['cap'], ':food' => $this->data['food'], ':id' => $this->data['id']) );
        }
        // if no then inserts it as new row
        else
        {
            $save = $this->db->prepare('INSERT INTO [profiles] (`name`, `skill0`, `skill1`, `skill2`, `skill3`, `skill4`, `skill5`, `skill6`, `health`, `healthmax`, `direction`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `cap`, `food`) VALUES (:name, :skill0, :skill1, :skill2, :skill3, :skill4, :skill5, :skill6, :health, :healthmax, :direction, :experience, :lookbody, :lookfeet, :lookhead, :looklegs, :looktype, :maglevel, :mana, :manamax, :manaspent, :soul, :cap, :food)');
            $save->execute( array(':name' => $this->data['name'], ':skill0' => $this->data['skill0'], ':skill1' => $this->data['skill1'], ':skill2' => $this->data['skill2'], ':skill3' => $this->data['skill3'], ':skill4' => $this->data['skill4'], ':skill5' => $this->data['skill5'], ':skill6' => $this->data['skill6'], ':health' => $this->data['health'], ':healthmax' => $this->data['healthmax'], ':direction' => $this->data['direction'], ':experience' => $this->data['experience'], ':lookbody' => $this->data['lookbody'], ':lookfeet' => $this->data['lookfeet'], ':lookhead' => $this->data['lookhead'], ':looklegs' => $this->data['looklegs'], ':looktype' => $this->data['looktype'], ':maglevel' => $this->data['maglevel'], ':mana' => $this->data['mana'], ':manamax' => $this->data['manamax'], ':manaspent' => $this->data['manaspent'], ':soul' => $this->data['soul'], ':cap' => $this->data['cap'], ':food' => $this->data['food']) );
            $this->data['id'] = $this->db->lastInsertId();
        }
    }
}

?>
