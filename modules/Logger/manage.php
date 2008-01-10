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

// AJAX wrap
$template->addJavaScript('logger');

// errors list
$list = $template->createComponent('TableList');
$list['id'] = 'logsTable';
$list['caption'] = $language['Modules.Logger.Caption'];
$list->addField('name', $language['Modules.Logger.Name']);
$list->addField('content', $language['Modules.Logger.Content']);
$list->addField('date_time', $language['Modules.Logger.DateTime']);

$logs = array();

// loads error logs
foreach( $db->query('SELECT `name`, `content`, `date_time` FROM [logs]') as $log)
{
    $logs[] = array('name' => $log['name'], 'content' => long2ip($log['content']), 'date_time' => date( $config['site.date_format'], $log['date_time']) );
}

$list['list'] = $logs;

// clear logs link
$a = XMLToolbox::createElement('a');
$a->setAttribute('href', '/admin/module=Logger&command=clean');
$a->setAttribute('onclick', 'if( confirm(\'' . $language['Modules.Logger.ConfirmClean'] . '\') ) { return pageLogger.run(); } else { return false; }');
$a->addContent($language['Modules.Logger.CleanSubmit']);

// puts link into template
$clear = $template->createComponent('Message');
$clear['message'] = $a;

?>
