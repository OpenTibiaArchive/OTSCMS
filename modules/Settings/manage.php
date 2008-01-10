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

$template->addJavaScript('settings');

// control panel
$form = $template->createComponent('AdminForm');
$form['action'] = 'admin/module=Settings&command=update';
$form['submit'] = $language['Modules.Settings.UpdateSubmit'];
$form['id'] = 'settingsForm';

// direcotries paths
$part = $config['directories'];
$form->addField('', ComponentAdminForm::FieldSeparator, $language['Modules.Settings.PartDirecotries']);
$form->addField('settings[directories.languages]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingDirectoriesLanguages'], $part['languages']);
$form->addField('settings[directories.modules]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingDirectoriesModules'], $part['modules']);
$form->addField('settings[directories.skins]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingDirectoriesSkins'], $part['skins']);
$form->addField('settings[directories.images]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingDirectoriesImages'], $part['images']);
$form->addField('settings[directories.data]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingDirectoriesData'], $part['data']);

// cookies configurations
$part = $config['cookies'];
$form->addField('', ComponentAdminForm::FieldSeparator, $language['Modules.Settings.PartCookies']);
$form->addField('settings[cookies.prefix]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingCookiesPrefix'], $part['prefix']);
$form->addField('settings[cookies.path]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingCookiesPath'], $part['path']);
$form->addField('settings[cookies.domain]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingCookiesDomain'], $part['domain']);

// time options
$time = array();
$times = $language['Components.AdminForm'];

// select options
foreach( array(60, 1800, 3600, 7200, 14400, 43200, 86400, 172800, 259200, 604800, 2592000, 5270400, 10540800, 15811200, 31536000) as $value)
{
    $time[$value] = $times['Time' . $value];
}

$form->addField('settings[cookies.expire]', ComponentAdminForm::FieldSelect, $language['Modules.Settings.SettingCookiesExpire'], array('options' => $time, 'selected' => $part['expire']) );

$groups = array();

// groups list
foreach( new OTS_Groups_List() as $group)
{
    $groups[$group->id] = $group->name;
}

// otserv settings
$part = $config['system'];
$form->addField('', ComponentAdminForm::FieldSeparator, $language['Modules.Settings.PartSystem']);
$form->addField('settings[system.md5]', ComponentAdminForm::FieldIsEnabled, $language['Modules.Settings.SettingSystemMD5'], $part['md5']);
$form->addField('settings[system.use_mail]', ComponentAdminForm::FieldIsEnabled, $language['Modules.Settings.SettingSystemUseMail'], $part['use_mail']);
$form->addField('settings[system.nick_length]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingSystemNickLength'], $part['nick_length']);
$form->addField('settings[system.default_group]', ComponentAdminForm::FieldSelect, $language['Modules.Settings.SettingSystemDefaultGroup'], array('options' => $groups, 'selected' => $part['default_group']) );
$form->addField('settings[system.min_number]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingSystemMinNumber'], $part['min_number']);
$form->addField('settings[system.max_number]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingSystemMaxNumber'], $part['max_number']);
$form->addField('settings[system.account_limit]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingSystemAccountLimit'], $part['account_limit']);
$form->addField('settings[system.map]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingSystemMap'], $part['map']);
$form->addField('settings[system.rook.enabled]', ComponentAdminForm::FieldIsEnabled, $language['Modules.Settings.SettingSystemRookEnabled'], $part['rook']['enabled']);
$form->addField('settings[system.rook.id]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingSystemRookID'], $part['rook']['id']);
$form->addField('settings[system.depots.count]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingSystemDepotsCount'], $part['depots']['count']);
$form->addField('settings[system.depots.item]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingSystemDepotsItem'], $part['depots']['item']);
$form->addField('settings[system.depots.chest]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingSystemDepotsChest'], $part['depots']['chest']);

// statistics settings
$part = $config['statistics'];
$form->addField('', ComponentAdminForm::FieldSeparator, $language['Modules.Settings.PartStatistics']);
$form->addField('settings[statistics.page]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingStatisticsPage'], $part['page']);

// site settings and default values
$part = $config['site'];
$form->addField('', ComponentAdminForm::FieldSeparator, $language['Modules.Settings.PartSite']);
$form->addField('settings[site.language]', ComponentAdminForm::FieldSelect, $language['Modules.Settings.SettingSiteLanguage'], array('options' => array_combine($template['languages'], $template['languages']), 'selected' => $part['language']) );
$form->addField('settings[site.skin]', ComponentAdminForm::FieldSelect, $language['Modules.Settings.SettingSiteSkin'], array('options' => array_combine($template['skins'], $template['skins']), 'selected' => $part['skin']) );
$form->addField('settings[site.title]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingSiteTitle'], $part['title']);
$form->addField('settings[site.news_limit]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingSiteNewsLimit'], $part['news_limit']);

// gallery settings
$part = $config['gallery'];
$form->addField('', ComponentAdminForm::FieldSeparator, $language['Modules.Settings.PartGallery']);
$form->addField('settings[gallery.mini_x]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingGalleryMiniX'], $part['mini_x']);
$form->addField('settings[gallery.mini_y]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingGalleryMiniY'], $part['mini_y']);

// forum configuration
$part = $config['forum'];
$form->addField('', ComponentAdminForm::FieldSeparator, $language['Modules.Settings.PartForum']);
$form->addField('settings[forum.limit]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingForumLimit'], $part['limit']);
$form->addField('settings[forum.avatar.max_x]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingForumAvatarMaxX'], $part['avatar']['max_x']);
$form->addField('settings[forum.avatar.max_y]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingForumAvatarMaxY'], $part['avatar']['max_y']);

// mail configuration
$part = $config['mail'];
$form->addField('', ComponentAdminForm::FieldSeparator, $language['Modules.Settings.PartMail']);
$form->addField('settings[mail.from]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingMailFrom'], $part['from']);
$form->addField('settings[mail.smtp.host]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingMailSMTPHost'], $part['smtp']['host']);
$form->addField('settings[mail.smtp.port]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingMailSMTPPort'], $part['smtp']['port']);
$form->addField('settings[mail.smtp.use_auth]', ComponentAdminForm::FieldIsEnabled, $language['Modules.Settings.SettingMailSMTPUseAuth'], $part['smtp']['use_auth']);
$form->addField('settings[mail.smtp.user]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingMailSMTPUser'], $part['smtp']['user']);
$form->addField('settings[mail.smtp.password]', ComponentAdminForm::FieldText, $language['Modules.Settings.SettingMailSMTPPassword'], $part['smtp']['password']);

?>
