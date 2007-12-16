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

$gender = InputData::read('gender');
$vocation = InputData::read('vocation');

// loads profile
$profile = new CMS_Profile($gender . '.' . $vocation);

// if there is no profile loaded then create new
if(!$profile['id'])
{
    $profile['name'] = $gender . '.' . $vocation;
    $profile->save();
}

// human-readable profile name
if( is_numeric($gender) )
{
    $gender = $language['main.gender' . $gender];
}

if( is_numeric($vocation) )
{
    $vocation = $language['main.vocation' . $vocation];
}

$name = $gender . '.' . $vocation;

// for ajax routines
$js = $template->createComponent('RAW');
$js['content'] = '<script type="text/javascript">

profileID = ' . $profile['id'] . ';

</script>';

// settings form
$form = $template->createComponent('AdminForm');
$form['action'] = '/admin/module=Character&command=set&id=' . $profile['id'];
$form['submit'] = $language['main.admin.UpdateSubmit'];
$form['id'] = 'profileForm';

// profile settings
$form->addField('', ComponentAdminForm::FieldLabel, $language['Modules.Character.FieldName'], $name);

for($i = 0; $i < 7; $i++)
{
    $form->addField('profile[skill' . $i .']', ComponentAdminForm::FieldText, $language['Modules.Character.FieldSkill' . $i], $profile['skill' . $i]);
}

$form->addField('profile[experience]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldExperience'], $profile['experience']);
$form->addField('profile[maglevel]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldMagicLevel'], $profile['maglevel']);
$form->addField('profile[mana]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldMana'], $profile['mana']);
$form->addField('profile[manamax]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldManaMax'], $profile['manamax']);
$form->addField('profile[manaspent]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldManaSpent'], $profile['manaspent']);
$form->addField('profile[soul]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldSoul'], $profile['soul']);
$form->addField('profile[health]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldHealth'], $profile['health']);
$form->addField('profile[healthmax]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldHealthMax'], $profile['healthmax']);
$form->addField('profile[direction]', ComponentAdminForm::FieldSelect, $language['Modules.Character.FieldDirection'], array('options' => array('' => $language['Modules.Character.ValueInherit'], 0 => $language['Modules.Character.DirN'], 1 => $language['Modules.Character.DirE'], 2 => $language['Modules.Character.DirS'], 3 => $language['Modules.Character.DirW']), 'selected' => $profile['direction']) );
$form->addField('profile[looktype]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldLookType'], $profile['looktype']);
$form->addField('profile[lookhead]', ComponentAdminForm::FieldColors, $language['Modules.Character.FieldLookHead'], $profile['lookhead']);
$form->addField('profile[lookbody]', ComponentAdminForm::FieldColors, $language['Modules.Character.FieldLookBody'], $profile['lookbody']);
$form->addField('profile[looklegs]', ComponentAdminForm::FieldColors, $language['Modules.Character.FieldLookLegs'], $profile['looklegs']);
$form->addField('profile[lookfeet]', ComponentAdminForm::FieldColors, $language['Modules.Character.FieldLookFeet'], $profile['lookfeet']);
$form->addField('profile[cap]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldCap'], $profile['cap']);
$form->addField('profile[food]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldFood'], $profile['food']);
$form->addField('profile[loss_experience]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldLossExperience'], $profile['loss_experience']);
$form->addField('profile[loss_mana]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldLossMana'], $profile['loss_mana']);
$form->addField('profile[loss_skills]', ComponentAdminForm::FieldText, $language['Modules.Character.FieldLossSkills'], $profile['loss_skills']);

// info about inheritance
$message = $template->createComponent('Message');
$message['message'] = $language['Modules.Character.SettingsHelp'];

// loads items.xml file
$cache = new ItemsCache($db);
$reader = new OTS_ItemsList();
$reader->setCacheDriver($cache);
$reader->loadItems($config['directories.data'] . 'items');

// creates items array
$items = array();

foreach($reader as $item)
{
    $items[ $item->getId() ] = $item->getName();
}

// current items list
$list = $template->createComponent('TableList');
$list['id'] = 'containersList';
$list['caption'] = $language['Modules.Character.EditContainers'];
$list->addField('name', $language['Modules.Character.ContainerName']);
$list->module = 'Character';
$list->idPrefix = 'containerID_';
$list->addAction('pop', $language['Modules.Character.PopSubmit']);

// slots names
$slots = array();

// body slots
for($i = 0; $i < 10; $i++)
{
    $slots[$i] = $language['Modules.Character.FieldItem' . $i];
}

// appends depots slots
for($i = 1; $i <= $config['system.depots.count']; $i++)
{
    $slots[100 + $i] = 'Depot #' . $i;
}

$containers = array();

// loads already existing items and containers
foreach( $db->query('SELECT `id`, `content`, `slot`, `count` FROM [containers] WHERE `profile` = ' . $profile['id']) as $container)
{
    // loads item
    $container['name'] = $reader->getItemType($container['content'])->getName();

    // checks if this item can be a container
    if( $reader->getItemType($container['content'])->getGroup() == OTS_ItemType::ITEM_GROUP_CONTAINER)
    {
        $slots[ $container['id'] ] = $language['Modules.Character.ContainerType'] . ' #' . $container['id'] . ' (' . $container['name'] . ')';
    }

    // composes container display name
    $containers[] = array('id' => $container['id'], 'name' => $container['name'] . ($container['count'] ? ' x ' . $countainer['count'] : '') . ' (' . $slots[ $container['slot'] ] . ')');

    // slots 0 to 9 can contain only one item
    if($container['slot'] < 10)
    {
        unset($slots[ $container['slot'] ]);
    }
}

$list['list'] = $containers;

// new item insertion form
$new = $template->createComponent('AdminForm');
$new['action'] = '/admin/module=Character&command=push&id=' . $profile['id'];
$new['submit'] = $language['main.admin.InsertSubmit'];
$new['id'] = 'containerForm';
$new->addField('container[content]', ComponentAdminForm::FieldSelect, $language['Modules.Character.ContainerName'], array('options' => $items) );
$new->addField('container[slot]', ComponentAdminForm::FieldSelect, $language['Modules.Character.ContainerSlot'], array('options' => $slots) );
$new->addField('container[count]', ComponentAdminForm::FieldText, $language['Modules.Character.ContainerCount']);

?>
