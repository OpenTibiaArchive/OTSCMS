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

$ots = POT::getInstance();

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
$player = $ots->createObject('Player');
$player->find($character['name']);

if( $player->isLoaded() )
{
    throw new HandledException('NameUsed');
}

$account = $ots->createObject('Account');
$account->load(User::$number);
$group = $ots->createObject('Group');
$group->load($system['default_group']);

// composes new player record
$player = $ots->createObject('Player');
$player->setName($character['name']);
$player->setAccount($account);
$player->setGroup($group);
$player->setSex($character['sex']);
$player->setVocation($character['vocation']);
$player->setConditions(0);
$player->setRankId(0);
$player->setLookAddons(0);
$player->setTownId($system['rook']['enabled'] ? $system['rook']['id'] : $character['town']);

// prepared query for reading profile
$read = $db->prepare('SELECT `id`, `name`, `skill0`, `skill1`, `skill2`, `skill3`, `skill4`, `skill5`, `skill6`, `health`, `healthmax`, `direction`, `experience`, `lookbody`, `lookfeet`, `lookhead`, `looklegs`, `looktype`, `maglevel`, `mana`, `manamax`, `manaspent`, `soul`, `cap`, `food`, `loss_experience`, `loss_mana`, `loss_skills` FROM [profiles] WHERE `name` = :name');

// reads deafult character profile
$profile = new CMS_Profile('*.*');
$sex = $player->getSex();
$vocation = $player->getVocation();

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
$gender = new CMS_Profile($sex . '.*');

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
$profession = new CMS_Profile('*.' . $vocation);

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
$detail = new CMS_Profile($sex . '.' . $vocation);

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

// fixes NULLs
foreach($profile as $key => $value)
{
    if( is_null($value) )
    {
        $profile[$key] = 0;
    }
}

// continues player data inserting from profile
$player->setExperience($profile['experience']);
$player->setLevel($level);
$player->setMagLevel($profile['maglevel']);
$player->setHealth($profile['health']);
$player->setHealthMax($profile['healthmax']);
$player->setMana($profile['mana']);
$player->setManaMax($profile['manamax']);
$player->setManaSpent($profile['manaspent']);
$player->setSoul($profile['soul']);
$player->setDirection($profile['direction']);
$player->setLookBody($profile['lookbody']);
$player->setLookFeet($profile['lookfeet']);
$player->setLookHead($profile['lookhead']);
$player->setLookLegs($profile['looklegs']);
$player->setLookType($profile['looktype']);
$player->setCap($profile['cap']);
$player->setLossExperience($profile['loss_experience']);
$player->setLossMana($profile['loss_mana']);
$player->setLossSkills($profile['loss_skills']);

// skill vlaues
$player->setSkill(POT::SKILL_FIST, $profile['skill0']);
$player->setSkill(POT::SKILL_CLUB, $profile['skill1']);
$player->setSkill(POT::SKILL_SWORD, $profile['skill2']);
$player->setSkill(POT::SKILL_AXE, $profile['skill3']);
$player->setSkill(POT::SKILL_DISTANCE, $profile['skill4']);
$player->setSkill(POT::SKILL_SHIELDING, $profile['skill5']);
$player->setSkill(POT::SKILL_FISHING, $profile['skill6']);

// saves record
$player->save();

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

$insert = $db->prepare('INSERT INTO {player_items} (`player_id`, `sid`, `pid`, `itemtype`, `count`) VALUES (' . $player->getId() . ', :sid, :pid, :itemtype, :count)');
$pids = array(0 => 1, 1 => 2, 2 => 3, 3 => 4, 4 => 5, 5 => 6, 6 => 7, 7 => 8, 8 => 9, 9 => 10);
$sid = 10;

// normal items
foreach($items as $item)
{
    $insert->execute( array(':sid' => ++$sid, ':pid' => $pids[ $item['slot'] ], ':itemtype' => $item['content'], ':count' => $item['count']) );
    $pids[ $item['id'] ] = $sid;
}

$insert = $db->prepare('INSERT INTO {player_depotitems} (`depotid`, `sid`, `pid`, `itemtype`, `count`) VALUES (' . $player->getId() . ', :sid, :pid, :itemtype, :count)');
$pids = array();
$sid = 201 + $system['depots']['count'];

// depot lockers and chests
for($i = 1; $i <= $system['depots']['count']; $i++)
{
    $insert->execute( array(':sid' => 100 + $i, ':pid' => $i, ':itemtype' => $system['depots']['item'], ':count' => 0) );
    $insert->execute( array(':sid' => ++$sid, ':pid' => 100 + $i, ':itemtype' => $system['depots']['chest'], ':count' => 0) );
    $pids[100 + $i] = $sid;
}

// depots contents
foreach($depots as $item)
{
    $insert->execute( array(':sid' => ++$sid, ':pid' => $pids[ $item['slot'] ], ':itemtype' => $item['content'], ':count' => $item['count']) );
    $pids[ $item['id'] ] = $sid;
}

// there is nothing to display
// redirects internaly to management page
OTSCMS::call('Account', 'account');

?>
