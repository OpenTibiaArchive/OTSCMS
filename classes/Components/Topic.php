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
            $a->setAttribute('href', 'character.php?name=' . $post['poster']);
            $a->nodeValue = $post['poster'];
            $td->appendChild($a);
            $td->appendChild( XMLToolbox::createElement('br') );

            // avatar if exists
            if($post['avatar'])
            {
                $img = XMLToolbox::createElement('img');
                $img->setAttribute('src', $post['avatar']);
                $img->setAttribute('alt', $post['poster']);
                $td->appendChild($img);
                $td->appendChild( XMLToolbox::createElement('br') );
            }

            // posts count and posting date
            $select->execute( array(':poster' => $post['author']) );
            $count = $select->fetch();

            $td->appendChild( XMLToolbox::createTextNode($language['Modules.Topic.PostsCount'] . ': ' . $count['count']) );
            $td->appendChild( XMLToolbox::createElement('br') );
            $td->appendChild( XMLToolbox::createTextNode( date($config['site.date_format'], $post['date_time']) ) );

            $row->appendChild($td);

            // post content
            $td = XMLToolbox::createElement('td');

            $td->appendChild( XMLToolbox::inparse( BBParser::parse($post['content'] . "\n" . '____________________' . "\n" . $post['signature']) ) );
            $row->appendChild($td);
            $tbody->appendChild($row);

            // post options bar
            $row = XMLToolbox::createElement('tr');
            $td = XMLToolbox::createElement('td');
            $td->setAttribute('class', 'postBar');

            // admin options
            if($this->admin)
            {
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', 'forum.php?module=Topic&command=remove&id=' . $post['id']);
                $a->setAttribute('onclick', 'return confirm(Language[0]);');
                $a->nodeValue = $language['main.admin.DeleteSubmit'];
                $td->appendChild($a);
                $td->appendChild( XMLToolbox::createTextNode(' | ') );
            }

            // normal user options
            if($this->user)
            {
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', 'forum.php?module=Topic&command=new&topicid=' . $this['id'] . '&quoteid=' . $post['id']);
                $a->nodeValue = $language['Modules.Topic.QuoteSubmit'];
                $td->appendChild($a);
                $td->appendChild( XMLToolbox::createTextNode(' | ') );

                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', 'priv.php?command=new&to=' . $post['poster']);
                $a->nodeValue = $language['Modules.Account.PMSubmit'];
                $td->appendChild($a);
            }

            $row->appendChild($td);
            $tbody->appendChild($row);
        }

        $table->setAttribute('class', 'listTable');
        $table->appendChild($tbody);

        $root->appendChild($table);

        // admin options
        if($this->admin)
        {
            $remove = XMLToolbox::createElement('a');
            $remove->setAttribute('href', 'forum.php?module=Topic&command=remove&id=' . $this['id']);
            $remove->setAttribute('onclick', 'return confirm(Language[0]);');
            $remove->nodeValue = $language['main.admin.DeleteSubmit'];

            $openClose = XMLToolbox::createElement('a');
            $openClose->setAttribute('href', 'forum.php?module=Topic&command=' . ($this['closed'] ? 'open' : 'close') . '&id=' . $this['id']);
            $openClose->nodeValue = $this['closed'] ? $language['Modules.Forum.OpenSubmit'] : $language['Modules.Forum.CloseSubmit'];

            $pinUnpin = XMLToolbox::createElement('a');
            $pinUnpin->setAttribute('href', 'forum.php?module=Topic&command=' . ($this['pinned'] ? 'unpin' : 'pin') . '&id=' . $this['id']);
            $pinUnpin->nodeValue = $this['pinned'] ? $language['Modules.Forum.UnpinSubmit'] : $language['Modules.Forum.PinSubmit'];

            // appends links and separators to root element
            $root->appendChild($remove);
            $root->appendChild( XMLToolbox::createTextNode(' | ') );
            $root->appendChild($openClose);
            $root->appendChild( XMLToolbox::createTextNode(' | ') );
            $root->appendChild($pinUnpin);
        }

        // outputs table
        return XMLToolbox::saveXML($root);
    }

    // admin backend

    public $admin = false;
    public $user = false;
}

?>
