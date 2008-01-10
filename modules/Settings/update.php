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

$update = $db->prepare('UPDATE [settings] SET `content` = :content WHERE `name` = :name');

// updates all settings
$settings = InputData::read('settings');
foreach($settings as $setting => $content)
{
    $update->execute( array(':content' => $content, ':name' => $setting) );
}

// displays the text so user will know that settings has been updated
// it redirects to settigns page so he/she couldn't spot it
$message = $template->createComponent('Message');
$message['message'] = $language['Modules.Settings.SettingsUpdated'];

OTSCMS::call('Settings', 'manage');

?>
