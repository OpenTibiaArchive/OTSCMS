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
    Topic page.
*/

class ComponentTopic extends TemplateComponent
{
    // displays component
    public function display()
    {
        // compositing reosurces
        $language = OTSCMS::getResource('Language');
        $db = OTSCMS::getResource('DB');
        $config = OTSCMS::getResource('Config');
        $root = XMLToolbox::createDocumentFragment();

        // list table
        $table = XMLToolbox::createElement('table');
        $tbody = XMLToolbox::createElement('tbody');

        $limit = $config['forum.limit'];
        $select = $db->prepare('SELECT COUNT(`id`) AS `count` FROM [posts] WHERE `poster` = :poster');

        // table content
        foreach( $db->query('SELECT `id`, `content`, `date_time`, `author`, `poster`, `avatar`, `signature` FROM [posts_with_authors] WHERE (`istopic` = 0 AND `upperid` = ' . $this['id'] . ') OR `id` = ' . $this['id'] . ' ORDER BY `date_time` LIMIT ' . $limit . ' OFFSET ' . ($limit * ($this['page'] - 1))) as $post)
        {
            // table row
            $row = XMLToolbox::createElement('tr');

            // user info cell
            $td = XMLToolbox::createElement('td');
            $td->setAttribute('rowspan', '2');
            $td->setAttribute('class', 'postAuthor');

            $a = XMLToolbox::createElement('a');
            $a->setAttribute('href', '/characters/' . $post['poster']);
            $a->addContent($post['poster']);
            $td->addContents($a, XMLToolbox::createElement('br') );

            // avatar if exists
            if($post['avatar'])
            {
                $img = XMLToolbox::createElement('img');
                $img->setAttribute('src', $post['avatar']);
                $img->setAttribute('alt', $post['poster']);
                $td->addContents($img, XMLToolbox::createElement('br') );
            }

            // posts count and posting date
            $select->execute( array(':poster' => $post['author']) );
            $count = $select->fetch();

            $td->addContents($language['Modules.Topic.PostsCount'] . ': ' . $count['count'], XMLToolbox::createElement('br'), date($config['site.date_format'], $post['date_time']) );

            // post content
            $content = XMLToolbox::createElement('td');
            $content->addContent( XMLToolbox::inparse( BBParser::parse($post['content'] . "\n" . '____________________' . "\n" . $post['signature']) ) );
            $row->addContents($td, $content);
            $tbody->addContent($row);

            // post options bar
            $row = XMLToolbox::createElement('tr');
            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'postBar');

            // admin options
            if($this->admin)
            {
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', '/admin/module=Topic&command=remove&id=' . $post['id']);
                $a->setAttribute('onclick', 'return confirm(Language[0]);');
                $a->addContent($language['main.admin.DeleteSubmit']);
                $td->addContents($a, ' | ');
            }

            // normal user options
            if($this->user)
            {
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', '/posts/' . $this['id'] . '/quote/' . $post['id']);
                $a->addContent($language['Modules.Topic.QuoteSubmit']);
                $td->addContents($a, ' | ');

                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', '/characters/' . urlencode($post['poster']) . '/message');
                $a->addContent($language['Modules.Account.PMSubmit']);
                $td->addContent($a);
            }

            $row->addContent($td);
            $tbody->addContent($row);
        }

        $table->setAttribute('class', 'listTable');
        $table->addContent($tbody);

        $root->addContent($table);

        // admin options
        if($this->admin)
        {
            $remove = XMLToolbox::createElement('a');
            $remove->setAttribute('href', '/admin/module=Topic&command=remove&id=' . $this['id']);
            $remove->setAttribute('onclick', 'return confirm(Language[0]);');
            $remove->addContent($language['main.admin.DeleteSubmit']);

            $openClose = XMLToolbox::createElement('a');
            $openClose->setAttribute('href', '/admin/module=Topic&command=' . ($this['closed'] ? 'open' : 'close') . '&id=' . $this['id']);
            $openClose->addContent($this['closed'] ? $language['Modules.Forum.OpenSubmit'] : $language['Modules.Forum.CloseSubmit']);

            $pinUnpin = XMLToolbox::createElement('a');
            $pinUnpin->setAttribute('href', '/admin/module=Topic&command=' . ($this['pinned'] ? 'unpin' : 'pin') . '&id=' . $this['id']);
            $pinUnpin->addContent($this['pinned'] ? $language['Modules.Forum.UnpinSubmit'] : $language['Modules.Forum.PinSubmit']);

            // appends links and separators to root element
            $root->addContents($remove, ' | ', $openClose, ' | ', $pinUnpin);
        }

        // outputs table
        return XMLToolbox::saveXML($root);
    }

    // admin backend

    public $admin = false;
    public $user = false;
}

?>
