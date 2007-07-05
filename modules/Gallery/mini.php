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

// loads given image from database
$gallery = new CMS_Gallery( (int) InputData::read('id') );

// checks if the id was correct
if(!$gallery['name'])
{
    OTSCMS::call('Gallery', 'list');
    return;
}

// blob
if($gallery['binary'])
{
    $image = imagecreatefromstring( base64_decode($gallery['file']) );
}
// link
else
{
    $image = imagecreatefromstring( file_get_contents($gallery['file']) );
}

// creates mini image
$config = $config['gallery'];
$mini = imagecreatetruecolor($config['mini_x'], $config['mini_y']);
imagecopyresized($mini, $image,  0, 0, 0, 0, $config['mini_x'], $config['mini_y'], imagesx($image), imagesy($image) );

header('Content-Type: image/png');
imagepng($mini);
exit;

?>
