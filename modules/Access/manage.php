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

// ajax handlers of IP bans management
$template->addJavaScript('access');

// edition form
$form = $template->createComponent('AdminForm');
$form['action'] = '/admin/module=Access&command=insert';
$form['submit'] = $language['main.admin.InsertSubmit'];
$form['id'] = 'accessForm';

// composes group labels
$groups = array(-1 => $language['Modules.Access.Level-1'], 0 => $language['Modules.Access.Level0'], 3 => $language['Modules.Access.Level3']);

foreach( $ots->createObject('Groups_List') as $group)
{
    $groups[ $group->getAccess() ] = $group->getName();
}

// reads modules list
$modules = Toolbox::subDirs($config['directories.modules'], true);

// handles module commands list
$js = $template->createComponent('RAW');
$js['content'] = '<script type="text/javascript">

function moduleCommands(newModule)
{
    switch(newModule)
    {';

// all modules list
foreach($modules as $module)
{
    $js['content'] .= 'case "' . $module . '":
            commandsList = new Array(';

    // reads module commands
    $commands = new DirectoryIterator($config['directories.modules'] . $module);
    foreach($commands as $command)
    {
        if( !$commands->isDot() && $command != 'index.php')
        {
            $js['content'] .= '"' . preg_replace('/\.php$/', '', $command) . '", ';
        }
    }

    $js['content'] .= '"*");
            break;';
}

$js['content'] .= '}

    return commandsList;
}

</script>';

// form fields
$form->addField('access[module]', ComponentAdminForm::FieldSelect, $language['Modules.Access.Module'], array('options' => array_combine($modules, $modules) ) );
$form->addField('access[command]', ComponentAdminForm::FieldSelect, $language['Modules.Access.Command'], array('options' => array() ) );
$form->addField('access[content]', ComponentAdminForm::FieldSelect, $language['Modules.Access.Content'], array('options' => $groups) );

// restrictions list
$list = $template->createComponent('TableList');
$list['id'] = 'accesssTable';
$list['caption'] = $language['Modules.Access.Caption'];
$list->addField('name', $language['Modules.Access.Name']);
$list->addField('content', $language['Modules.Access.Content']);
$list->addAction('remove', $language['main.admin.DeleteSubmit']);
$list->module = 'Access';
$list->idPrefix = 'accessID_';

$restrictions = array();

foreach( $db->query('SELECT `id`, `name`, `content` FROM [access]') as $access)
{
    $access['content'] = isset($groups[ $access['content'] ]) ? $groups[ $access['content'] ] : $access['content'];

    // saves row in results
    $restrictions[] = $access;
}

$list['list'] = $restrictions;

?>
