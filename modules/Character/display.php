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
    $character = new OTS_Player($name);

    // checks if player exists
    if(!$character['id'])
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

    $data = array($language['Modules.Character.Name'] => $character['name'], $language['Modules.Character.Gender'] => $language['main.gender' . $character['sex'] ], $language['Modules.Character.Vocation'] => $language['main.vocation' . $character['vocation'] ], $language['Modules.Character.Experience'] => $character['experience'], $language['Modules.Character.Level'] => $character['level'], $language['Modules.Character.MagicLevel'] => $character['maglevel'],
    $language['Modules.Character.City'] => $spawns[ $character['id_town'] ]);

    // house
    $house = $db->query('SELECT `id` FROM {houses} WHERE `owner` = ' . $character['id'])->fetch();

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
    if($character['rank_id'])
    {
        // for guilds link
        $root = XMLToolbox::createDocumentFragment();
        $a = XMLToolbox::createElement('a');

        $guild = $db->query('SELECT {guilds}.`id` AS `id`, {guilds}.`name` AS `name`, {guild_ranks}.`name` AS `rank` FROM {guilds}, {guild_ranks} WHERE {guilds}.`id` = {guild_ranks}.`guild_id` AND {guild_ranks}.`id` = ' . $character['rank_id'])->fetch();

        $a->setAttribute('href', 'guild.php?command=display&id=' . $guild['id']);
        $a->addContent($guild['name']);

        $root->addContents($guild['rank'] . ' ' . $language['Modules.Character.InGuild'] . ' ', $a);

        $data[ $language['Modules.Character.Guild'] ] = $root;
    }

    // last login time
    $data[ $language['Modules.Character.LastLogin'] ] = date($config['site.date_format'], $character['lastlogin']);

    // forum profile part
    $profile = new OTS_Account($character['account_id']);

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
    if($character['comment'])
    {
        $data[ $language['Modules.Character.Comment'] ] = $character['comment'];
    }

    $table['data'] = $data;

    // PM link
    $link = $template->createComponent('Links');
    $link['links'] = array( array('link' => 'priv.php?command=new&to=' . urlencode($character['name']), 'label' => $language['Modules.Account.PMSubmit']) );

    // other characters list
    $others = Toolbox::dumpRecords( $db->query('SELECT `name` AS `key`, `name` AS `value` FROM {players} WHERE `account_id` = ' . $character['account_id'] . ' AND `id` != ' . $character['id']) );

    if( !empty($others) )
    {
        $list = $template->createComponent('ItemsList');
        $list['link'] = 'character.php?name=';
        $list['list'] = $others;
        $list['header'] = $language['Modules.Character.OtherCharacter'];
    }
}

// character search form
$form = $template->createComponent('Signup');
$form['action'] = 'character.php';
$form['text'] = $language['Modules.Character.TypeName'];
$form['name'] = 'name';
$form['submit'] = $language['Modules.Character.DisplaySubmit'];
$form['onsubmit'] = 'return true;';

?>
