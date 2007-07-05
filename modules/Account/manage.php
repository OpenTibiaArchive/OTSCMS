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

$template->addJavaScript('account');

$accounts = array();
$md5 = $config['system.use_md5'];

// reads accounts
foreach( $db->query('SELECT `id`, `email`, `blocked`, `premdays`, `password` FROM {accounts} ORDER BY `id`') as $account)
{
    $row = array('id' => $account['id'], 'email' => $account['email'], 'blocked' => $account['blocked'] ? $language['Modules.Account.Blocked'] : $language['Modules.Account.Unblocked'], 'premdays' => $account['premdays']);

    // if MD5 is disabled we can display passwords
    if(!$md5)
    {
        $row['password'] = $account['password'];
    }

    $accounts[] = $row;
}

// list table
$list = $template->createComponent('TableList');
$list->addField('id', $language['Modules.Account.AccountNumber']);

if(!$md5)
{
    $list->addField('password', $language['Modules.Account.Password']);
}

$list->addField('email', $language['Modules.Account.EMail']);
$list->addField('blocked', $language['Modules.Account.Status']);
$list->addField('premdays', $language['Modules.Account.Premium']);
$list->addAction('remove', $language['main.admin.DeleteSubmit']);
$list->addAction('edit', $language['main.admin.EditSubmit']);
$list->addAction('block', $language['Modules.Account.BlockSubmit']);
$list->addAction('unblock', $language['Modules.Account.UnblockSubmit']);
$list->module = 'Account';
$list->idPrefix = 'accountID_';

$list['list'] = $accounts;

?>