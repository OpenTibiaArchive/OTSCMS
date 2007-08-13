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

/*
    Posting options.
*/

class ComponentForumBar extends TemplateComponent
{
    // displays component
    public function display()
    {
        // translation
        $language = OTSCMS::getResource('Language');
        $config = OTSCMS::getResource('Config');

        $root = XMLToolbox::createElement('div');
        $root->setAttribute('class', 'forumBar');

        // new thread link
        $a = XMLToolbox::createElement('a');
        $img = XMLToolbox::createElement('img');
        $img->setAttribute('alt', $language['Components.ForumBar.newThread']);
        $img->setAttribute('src', $this->this['baseHref'] . $config['site.language'] . '/forum_newThread.png');
        $a->setAttribute('href', '/forum/' . $this['boardid'] . '/reply');
        $a->addContent($img);
        $root->addContent($a);

        // post reply link
        if( isset($this['topicid']) )
        {
            $a = XMLToolbox::createElement('a');
            $img = XMLToolbox::createElement('img');
            $img->setAttribute('alt', $language['Components.ForumBar.newPost']);
            $img->setAttribute('src', $this->this['baseHref'] . $config['site.language'] . '/forum_newPost.png');
            $a->setAttribute('href', '/posts/' . $this['topicid'] . '/reply');
            $a->addContent($img);
            $root->addContent($a);
        }

        // outputs pagination bar
        return XMLToolbox::saveXML($root);
    }
}

?>
