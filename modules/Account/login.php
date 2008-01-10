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

// login form
$form = $template->createComponent('AdminForm');
$form['action'] = '/account/';
$form['submit'] = $language['Modules.Account.Login'];

// cookie checkbox
$cookie = XMLToolbox::createElement('label');
$input = XMLToolbox::createElement('input');
$input->setAttribute('type', 'checkbox');
$input->setAttribute('name', 'userusecookielogin');
$input->setAttribute('value', '1');
$cookie->addContents($input, ' ' . $language['Modules.Account.Cookies']);

// form fields
$form->addField('useraccount', ComponentAdminForm::FieldPassword, $language['Modules.Account.Login_Number']);
$form->addField('userpassword', ComponentAdminForm::FieldPassword, $language['Modules.Account.Login_Password']);
$form->addField('', ComponentAdminForm::FieldSeparator, $cookie);

?>
