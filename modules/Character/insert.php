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

// checks characters limit
$count = $db->query('SELECT COUNT(`id`) AS `count` FROM {players} WHERE `account_id` = ' . User::$number)->fetch();
if($count['count'] >= $config['system.account_limit'])
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Character.Limit'];
    return;
}

// deletes item recursively
function dropItem(&$array, $slot)
{
    // simply recursive iteration
    foreach($array as $key => $items)
    {
        // found
        if($key == $slot)
        {
            unset($array[$key]);

            // also removes it recursively
            foreach($items as $item)
            {
                dropItem($array, $item['id']);
            }
        }
    }
}

// pre-loads HTTP data
$character = InputData::read('character');
$system = $config['system'];

// checks if the name is correct
if( !preg_match('/^[A-Z][A-Za-z ]{' . ($system['nick_length'] - 1) . ',}$/', $character['name']) || preg_match('/^(gm|god) /i', $character['name']) )
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Character.WrongName'];
    return;
}

// checks if character exists
$count = $db->prepare('SELECT COUNT(`id`) AS `count` FROM {players} WHERE `name` = :name');
$count->execute( array(':name' => $character['name']) );
$count = $count->fetch();

if($count['count'])
{
    throw new HandledException('NameUsed');
}

// composes new player record
$player = new OTS_Player();
$player['name'] = $character['name'];
$player['account_id'] = User::$number;
$player['group_id'] = $system['default_group'];
$player['sex'] = $character['sex'];
$player['vocation'] = $character['vocation'];
$player['comment'] = '';
$player['posx'] = 0;
$player['posy'] = 0;
$player['posz'] = 0;
$player['lastlogin'] = 0;
$player['lastip'] = 0;
$player['save'] = 1;
$player['conditions'] = 0;
$player['redskulltime'] = 0;
$player['redskull'] = 0;
$player['guildnick'] = '';
$player['rank_id'] = 0;
$player['lookaddons'] = 0;
$player['town_id'] = $system['rook']['enabled'] ? $system['rook']['id'] : $character['town'];

// prepared query for reading profile
$read = $db->prepare('SELECT `id`, `name`, `skill0`, `skill1`, `skill2`, `skill3`, `skill4`, `skill5`, `skill6`, `health`, `healthmax`, `direction`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `cap`, `food` FROM [profiles] WHERE `name` = :name');

// reads deafult character profile
$read->execute( array(':name' => '*.*') );
$profile = $read->fetch();

// reads default equipment
$list = array();
if( is_array($profile) )
{
    foreach( $db->query('SELECT `id`, `content`, `slot`, `count` FROM [containers] WHERE `profile` = ' . $profile['id'] . ' ORDER BY `id`') as $item)
    {
        $list[ $item['slot'] ][] = $item;
    }
}

// reads profile for gender
$read->execute( array(':name' => $player['sex'] . '.*') );
$gender = $read->fetch();

// overwrites default profile settings
if( is_array($gender) )
{
    foreach($gender as $key => $value)
    {
        if($value != '')
        {
            $profile[$key] = $value;
        }
    }
}

// reads gender equipment
if($gender['id'])
{
    foreach( $db->query('SELECT `id`, `content`, `slot`, `count` FROM [containers] WHERE `profile` = ' . $gender['id'] . ' ORDER BY `id`') as $item)
    {
        // body slots can contain only one item - need to override it
        if($item['slot'] < 10)
        {
            dropItem($list, $item['slot']);
        }

        $list[ $item['slot'] ][] = $item;
    }
}

// reads profile for vocation
$read->execute( array(':name' => '*.' . $player['vocation']) );
$profession = $read->fetch();

// overwrites profile settings
if( is_array($profession) )
{
    foreach($profession as $key => $value)
    {
        if($value != '')
        {
            $profile[$key] = $value;
        }
    }
}

// reads vocation equipment
if($profession['id'])
{
    foreach( $db->query('SELECT `id`, `content`, `slot`, `count` FROM [containers] WHERE `profile` = ' . $profession['id'] . ' ORDER BY `id`') as $item)
    {
        // body slots can contain only one item - need to override it
        if($item['slot'] < 10)
        {
            dropItem($list, $item['slot']);
        }

        $list[ $item['slot'] ][] = $item;
    }
}

// reads detailed profile
$read->execute( array(':name' => $player['sex'] . '.' . $player['vocation']) );
$detail = $read->fetch();

// creates final profile
if( is_array($detail) )
{
    foreach($detail as $key => $value)
    {
        if($value != '')
        {
            $profile[$key] = $value;
        }
    }
}

// reads detailed profile equipment
if($detail['id'])
{
    foreach( $db->query('SELECT `id`, `content`, `slot`, `count` FROM [containers] WHERE `profile` = ' . $detail['id'] . ' ORDER BY `id`') as $item)
    {
        // body slots can contain only one item - need to override it
        if($item['slot'] < 10)
        {
            dropItem($list, $item['slot']);
        }

        $list[ $item['slot'] ][] = $item;
    }
}

// finds experience level based on points
$level = 1;
while(50 / 3 * pow($level + 1, 3) - 100 * pow($level + 1, 2) + (850 / 3) * ($level + 1) - 200 <= (int) $profile['experience'])
{
    $level++;
}

// continues player data inserting from profile
$player['experience'] = $profile['experience'];
$player['level'] = $level;
$player['maglevel'] = $profile['maglevel'];
$player['health'] = $profile['health'];
$player['healthmax'] = $profile['healthmax'];
$player['mana'] = $profile['mana'];
$player['manamax'] = $profile['manamax'];
$player['manaspent'] = $profile['manaspent'];
$player['soul'] = $profile['soul'];
$player['direction'] = $profile['direction'];
$player['lookbody'] = $profile['lookbody'];
$player['lookfeet'] = $profile['lookfeet'];
$player['lookhead'] = $profile['lookhead'];
$player['looklegs'] = $profile['looklegs'];
$player['looktype'] = $profile['looktype'];
$player['cap'] = $profile['cap'];

// saves record
$player->save();

$skill = $db->prepare('UPDATE {player_skills} SET `value` = :value WHERE `player_id` = ' . $player['id'] . ' AND `skillid` = :skillid');

// skill vlaues
$skill->execute( array(':value' => (int) $profile['skill0'], ':skillid' => 0) );
$skill->execute( array(':value' => (int) $profile['skill1'], ':skillid' => 1) );
$skill->execute( array(':value' => (int) $profile['skill2'], ':skillid' => 2) );
$skill->execute( array(':value' => (int) $profile['skill3'], ':skillid' => 3) );
$skill->execute( array(':value' => (int) $profile['skill4'], ':skillid' => 4) );
$skill->execute( array(':value' => (int) $profile['skill5'], ':skillid' => 5) );
$skill->execute( array(':value' => (int) $profile['skill6'], ':skillid' => 6) );

// sorts list of items
$items = array();
$depots = array();
$types = array();

foreach($list as $container)
{
    foreach($container as $item)
    {
        // depot item
        if( floor($item['slot'] / 100) == 1)
        {
            $depots[ $item['id'] ] = $item;
            $types[ $item['id'] ] = &$depots;
        }
        // body slot
        elseif($item['slot'] < 10)
        {
            $items[ $item['id'] ] = $item;
            $types[ $item['id'] ] = &$items;
        }
        // container
        else
        {
            $types[ $item['slot'] ][ $item['id'] ] = $item;
            $types[ $item['id'] ] = &$types[ $item['slot'] ];
        }
    }
}

// so we are sure we wont insert item to empty container
ksort($items);
ksort($depots);

$insert = $db->prepare('INSERT INTO {player_items} (`player_id`, `sid`, `pid`, `itemtype`, `count`) VALUES (' . $player['id'] . ', :sid, :pid, :itemtype, :count)');
$pids = array(0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9, 9 => 10);
$sid = 10;

// normal items
foreach($items as $item)
{
    $insert->execute( array(':sid' => ++$sid, ':pid' => $pids[ $item['slot'] ], ':itemtype' => $item['content'], ':count' => $item['count']) );
    $pids[ $item['id'] ] = $sid;
}

$insert = $db->prepare('INSERT INTO {player_depotitems} (`player_id`, `depotid`, `sid`, `pid`, `itemtype`, `count`) VALUES (' . $player['id'] . ', :depotid, :sid, :pid, :itemtype, :count)');
$pids = array();
$sid = 201 + $system['depots']['count'];

// depot lockers and chests
for($i = 1; $i <= $system['depots']['count']; $i++)
{
    $insert->execute( array(':depotid' => $i, ':sid' => 100 + $i, ':pid' => 0, ':itemtype' => $system['depots']['item'], ':count' => 0) );
    $insert->execute( array(':depotid' => $i, ':sid' => 200 + $i, ':pid' => 100 + $i, ':itemtype' => $system['depots']['chest'], ':count' => 0) );
    $pids[100 + $i] = array('pid' => 200 + $i, 'depot' => $i);
}

// depots contents
foreach($depots as $item)
{
    $insert->execute( array(':depotid' => $pids[ $item['slot'] ]['depot'], ':sid' => ++$sid, ':pid' => $pids[ $item['slot'] ]['pid'], ':itemtype' => $item['content'], ':count' => $item['count']) );
    $pids[ $item['id'] ] = array('pid' => $sid, 'depot' => $pids[ $item['slot'] ]['depot']);
}

// there is nothing to display
// redirects internaly to management page
OTSCMS::call('Account', 'account');

?>
