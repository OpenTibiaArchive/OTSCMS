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

// checks characters limit
$account = new OTS_Account(User::$number);
if( count($account) >= $config['system.account_limit'])
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Character.Limit'];
    return;
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
$player = new OTS_Player($character['name']);

if($player->loaded)
{
    throw new HandledException('NameUsed');
}

$group = new OTS_Group( (int) $system['default_group']);

// composes new player record
$player = new OTS_Player();
$player->name = $character['name'];
$player->account = $account;
$player->group = $group;
$player->sex = $character['sex'];
$player->vocation = $character['vocation'];
$player->conditions = '';
$player->setRank();
$player->lookAddons = 0;
$player->townId = $system['rook']['enabled'] ? $system['rook']['id'] : $character['town'];

// reads deafult character profile
$profile = new CMS_Profile('*.*');
$sex = $player->sex;
$vocation = $player->vocation;

$list = array();

// depot lockers and chests
for($i = 1; $i <= $system['depots']['count']; $i++)
{
    $chest = new OTS_Container($system['depots']['chest']);

    $locker = new OTS_Container($system['depots']['item']);
    $locker->addItem($chest);

    $list[100 + $i] = $locker;
}

// reads default equipment
if( is_array($profile) )
{
    foreach( $db->query('SELECT `id`, `content`, `slot`, `count` FROM [containers] WHERE `profile` = ' . $profile['id'] . ' ORDER BY `id`') as $item)
    {
        $container = new OTS_Container($item['content']);
        $container->count = $item['count'];

        // appends to parent container
        if( isset($list[ $item['slot'] ]) )
        {
            $list[ $item['slot'] ]->addItem($container);
        }

        $list[ $item['id'] ] = $container;
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
            unset($list[ $item['slot'] ]);
        }

        $container = new OTS_Container($item['content']);
        $container->count = $item['count'];

        // appends to parent container
        if( isset($list[ $item['slot'] ]) )
        {
            $list[ $item['slot'] ]->addItem($container);
        }

        $list[ $item['id'] ] = $container;
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
            unset($list[ $item['slot'] ]);
        }

        $container = new OTS_Container($item['content']);
        $container->count = $item['count'];

        // appends to parent container
        if( isset($list[ $item['slot'] ]) )
        {
            $list[ $item['slot'] ]->addItem($container);
        }

        $list[ $item['id'] ] = $container;
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
            unset($list[ $item['slot'] ]);
        }

        $container = new OTS_Container($item['content']);
        $container->count = $item['count'];

        // appends to parent container
        if( isset($list[ $item['slot'] ]) )
        {
            $list[ $item['slot'] ]->addItem($container);
        }

        $list[ $item['id'] ] = $container;
    }
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
$player->experience = $profile['experience'];
$player->level = OTS_Toolbox::levelForExperience($profile['experience']);
$player->magLevel = $profile['maglevel'];
$player->health = $profile['health'];
$player->healthMax = $profile['healthmax'];
$player->mana = $profile['mana'];
$player->manaMax = $profile['manamax'];
$player->manaSpent = $profile['manaspent'];
$player->soul = $profile['soul'];
$player->direction = $profile['direction'];
$player->lookBody = $profile['lookbody'];
$player->lookFeet = $profile['lookfeet'];
$player->lookHead = $profile['lookhead'];
$player->lookLegs = $profile['looklegs'];
$player->lookType = $profile['looktype'];
$player->cap = $profile['cap'];
$player->lossExperience = $profile['loss_experience'];
$player->lossMana = $profile['loss_mana'];
$player->lossSkills = $profile['loss_skills'];
$player->balance = $profile['balance'];

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

foreach($list as $slot => $item)
{
    // depot item
    if( floor($slot / 100) == 1)
    {
        $player->setDepot($slot - 100, $item);
    }
    // body slot
    elseif($slot < 10)
    {
        $player->setSlot($slot + 1, $item);
    }
}

// there is nothing to display
// redirects internaly to management page
OTSCMS::call('Account', 'account');

?>
