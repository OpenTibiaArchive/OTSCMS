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
    $character = $ots->createObject('Player');
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
    $cache = new OTBMCache($db);
    $otbm = new OTS_OTBMFile();
    $otbm->setCacheDriver($cache);
    $otbm->loadFile($config['directories.data'] . 'world/' . $config['system.map']);

    // character info table
    $table = $template->createComponent('TableData');
    $table['caption'] = $language['Modules.Character.CharacterData'];

    $data = array($language['Modules.Character.Name'] => $character->getName(), $language['Modules.Character.Gender'] => $language['main.gender' . $character->getSex() ], $language['Modules.Character.Vocation'] => $language['main.vocation' . $character->getVocation() ], $language['Modules.Character.Experience'] => $character->getExperience(), $language['Modules.Character.Level'] => $character->getLevel(), $language['Modules.Character.MagicLevel'] => $character->getMagLevel(),
    $language['Modules.Character.City'] => $otbm->getTownName( $character->getTownId() ) );

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
    $rank = $character->getRank();
    if( isset($rank) )
    {
        // for guilds link
        $root = XMLToolbox::createDocumentFragment();
        $a = XMLToolbox::createElement('a');

        $guild = $rank->getGuild();

        $a->setAttribute('href', '/guilds/' . $guild->getId() );
        $a->addContent( $guild->getName() );

        $root->addContents( $rank->getName() . ' ' . $language['Modules.Character.InGuild'] . ' ', $a);

        $data[ $language['Modules.Character.Guild'] ] = $root;
    }

    // last login time
    $data[ $language['Modules.Character.LastLogin'] ] = date($config['site.date_format'], $character->getLastLogin() );

    // forum profile part
    $account = $character->getAccount();
    $signature = $account->getCustomField('signature');
    $avatar = $account->getCustomField('avatar');
    $website = $account->getCustomField('website');

    // parses BB code and then loads it into XML tree
    if( !empty($signature) )
    {
        $data[ $language['Modules.Account.Signature'] ] = XMLToolbox::inparse( BBParser::parse($signature) );
    }

    if( !empty($avatar) )
    {
        // avatar image
        $img = XMLToolbox::createElement('img');
        $img->setAttribute('alt', $avatar);
        $img->setAttribute('src', $avatar);
        $data[ $language['Modules.Account.Avatar'] ] = $img;
    }

    if( !empty($website) )
    {
        // website link
        $a = XMLToolbox::createElement('a');
        $a->setAttribute('href', $website);
        $a->addContent($website);
        $data[ $language['Modules.Account.Website'] ] = $a;
    }

    // character comment
    $comment = $character->getCustomField('comment');
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
    foreach( $account->getPlayersList() as $other)
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
