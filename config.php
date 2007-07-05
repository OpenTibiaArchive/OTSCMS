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
// this is a temporary state before installation will be completed
if( !defined('OTSCMS_INSTALL') )
{
    header('Location: install.php');
    die('OTSCMS not installed. Please run install.php.');
}

$config['directories']['classes'] = 'classes/';
$config['directories']['languages'] = 'languages/';
$config['directories']['modules'] = 'modules/';
$config['directories']['skins'] = 'skins/';
$config['directories']['images'] = 'images/';

?>