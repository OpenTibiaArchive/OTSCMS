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

// loads name from URL
$name = InputData::read('name');

// checks if the name is valid OTServ character name
if( !preg_match('/^[a-z ]+$/i', $name) )
{
    unset($name);
}

// checks if there is any name to look for
if( isset($name) )
{
    // gets character informations from database
    $character = POT::getInstance()->createObject('Player');
    $character->find($name);

    // checks if player exists
    if( !$character->isLoaded() )
    {
        $message = $template->createComponent('Message');
        $message['message'] = $language['Modules.Character.NoCharacterText'];
        InputData::write('name', null);
        OTSCMS::call('Character', 'display');
        return;
    }

    // cities names
    $spawns = new SpawnsReader($config['directories.data'] . 'world/' . $config['system.map']);

    // character info table
    $table = $template->createComponent('TableData');
    $table['caption'] = $language['Modules.Character.CharacterData'];

    $data = array($language['Modules.Character.Name'] => $character->getName(), $language['Modules.Character.Gender'] => $language['main.gender' . $character->getSex() ], $language['Modules.Character.Vocation'] => $language['main.vocation' . $character->getVocation() ], $language['Modules.Character.Experience'] => $character->getExperience(), $language['Modules.Character.Level'] => $character->getLevel(), $language['Modules.Character.MagicLevel'] => $character->getMagLevel(),
    $language['Modules.Character.City'] => $spawns[ $character->getTownId() ]);

    // house
    $house = $db->query('SELECT `id` FROM {houses} WHERE `owner` = ' . $character->getId() )->fetch();

    if( !empty($house) )
    {
        $xml = new DOMDocument();
        $xml->load($config['directories.data'] . 'world/' . preg_replace('/\.otbm$/', '-house.xml', $config['system.map']) );

        foreach( $xml->getElementsByTagName('house') as $element)
        {
            if( $element->getAttribute('houseid') == $house['id'])
            {
                $data[ $language['Modules.Character.House'] ] = $element->getAttribute('name');

                break;
            }
        }
    }

    // reads guild information if there is any
    $rank = $character->getRankId();
    if($rank)
    {
        // for guilds link
        $root = XMLToolbox::createDocumentFragment();
        $a = XMLToolbox::createElement('a');

        $guild = $db->query('SELECT {guilds}.`id` AS `id`, {guilds}.`name` AS `name`, {guild_ranks}.`name` AS `rank` FROM {guilds}, {guild_ranks} WHERE {guilds}.`id` = {guild_ranks}.`guild_id` AND {guild_ranks}.`id` = ' . $rank)->fetch();

        $a->setAttribute('href', '/guilds/' . $guild['id']);
        $a->addContent($guild['name']);

        $root->addContents($guild['rank'] . ' ' . $language['Modules.Character.InGuild'] . ' ', $a);

        $data[ $language['Modules.Character.Guild'] ] = $root;
    }

    // last login time
    $data[ $language['Modules.Character.LastLogin'] ] = date($config['site.date_format'], $character->getLastLogin() );

    // forum profile part
    $account = $character->getAccount();
    $profile = $db->query('SELECT `signature`, `avatar`, `website` FROM {accounts} WHERE `id` = ' . $account->getId() )->fetch();

    // parses BB code and then loads it into XML tree
    if( !empty($profile['signature']) )
    {
        $data[ $language['Modules.Account.Signature'] ] = XMLToolbox::inparse( BBParser::parse($profile['signature']) );
    }

    if( !empty($profile['avatar']) )
    {
        // avatar image
        $img = XMLToolbox::createElement('img');
        $img->setAttribute('alt', $profile['avatar']);
        $img->setAttribute('src', $profile['avatar']);
        $data[ $language['Modules.Account.Avatar'] ] = $img;
    }

    if( !empty($profile['website']) )
    {
        // website link
        $a = XMLToolbox::createElement('a');
        $a->setAttribute('href', $profile['website']);
        $a->addContent($profile['website']);
        $data[ $language['Modules.Account.Website'] ] = $a;
    }

    // character comment
    $comment = $db->query('SELECT `comment` FROM {players} WHERE `id` = ' . $character->getId() )->fetch();
    if( !empty($comment['comment']) )
    {
        $data[ $language['Modules.Character.Comment'] ] = $comment['comment'];
    }

    $table['data'] = $data;

    // PM link
    $link = $template->createComponent('Links');
    $link['links'] = array( array('link' => '/characters/' . urlencode( $character->getName() ) . '/message', 'label' => $language['Modules.Account.PMSubmit']) );

    // other characters list
    $others = array();
    foreach( $account->getPlayers() as $other)
    {
        // checks if it's not current player
        if( $character->getId() != $other->getId() )
        {
            $others[ $other->getName() ] = $other->getName();
        }
    }

    if( !empty($others) )
    {
        $list = $template->createComponent('ItemsList');
        $list['link'] = '/characters/';
        $list['list'] = $others;
        $list['header'] = $language['Modules.Character.OtherCharacter'];
    }
}

// character search form
$form = $template->createComponent('Signup');
$form['action'] = '/characters/';
$form['text'] = $language['Modules.Character.TypeName'];
$form['name'] = 'name';
$form['submit'] = $language['Modules.Character.DisplaySubmit'];
$form['onsubmit'] = 'return true;';

?>
