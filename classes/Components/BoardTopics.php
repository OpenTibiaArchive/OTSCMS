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
    Forum threads list.
*/

class ComponentBoardTopics extends TemplateComponent
{
    // displays component
    public function display()
    {
        // translation
        $language = OTSCMS::getResource('Language');
        $config = OTSCMS::getResource('Config');

        // list table
        $table = XMLToolbox::createElement('table');
        $table->setAttribute('class', 'listTable');
        $table->setAttribute('id', 'boardTopics');

        // headers
        $thead = XMLToolbox::createElement('thead');
        $tr = XMLToolbox::createElement('tr');

        $th = XMLToolbox::createElement('th');
        $th->nodeValue = $language['Modules.Forum.Topic'];
        $tr->appendChild($th);

        $th = XMLToolbox::createElement('th');
        $th->nodeValue = $language['Modules.Forum.Author'];
        $tr->appendChild($th);

        $th = XMLToolbox::createElement('th');
        $th->nodeValue = $language['Modules.Forum.Replies'];
        $tr->appendChild($th);

        $th = XMLToolbox::createElement('th');
        $th->nodeValue = $language['Modules.Forum.LastPost'];
        $tr->appendChild($th);

        if($this->admin)
        {
            $th = XMLToolbox::createElement('th');
            $th->nodeValue = $language['main.admin.Actions'];
            $tr->appendChild($th);
        }

        $thead->appendChild($tr);
        $table->appendChild($thead);

        $tbody = XMLToolbox::createElement('tbody');

        $limit = $config['forum.limit'];

        // table content
        foreach( OTSCMS::getResource('DB')->query('SELECT `id`, `name`, `date_time`, `closed`, `pinned`, `poster` FROM [posts_with_authors] WHERE `istopic` = 1 AND `upperid` = ' . $this['id'] . ' ORDER BY `pinned` DESC, `date_time` DESC LIMIT ' . $limit . ' OFFSET ' . ($limit * ($this['page'] - 1))) as $topic)
        {
            // table row
            $row = XMLToolbox::createElement('tr');
            $row->setAttribute('id', 'postID_' . $topic['id']);

            // topic title
            $td = XMLToolbox::createElement('td');

            // pinned
            if($topic['pinned'])
            {
                $td->appendChild( XMLToolbox::createTextNode('[' . $language['Modules.Forum.PINNED'] . '] ') );
            }

            // topic link
            $a = XMLToolbox::createElement('a');
            $a->setAttribute('href', 'forum.php?module=Topic&command=view&id=' . $topic['id']);
            $a->nodeValue = $topic['name'];

            // topic closed
            if($topic['closed'])
            {
                $a->setAttribute('class', 'closed');
            }

            $td->appendChild($a);
            $row->appendChild($td);

            // author
            $td = XMLToolbox::createElement('td');
            $a = XMLToolbox::createElement('a');
            $a->setAttribute('href', 'character.php?name=' . urlencode($topic['poster']) );
            $a->appendChild( XMLToolbox::createTextNode($topic['poster']) );
            $td->appendChild($a);
            $row->appendChild($td);

            // replies count
            $td = XMLToolbox::createElement('td');
            $td->nodeValue = ForumToolbox::countReplies($topic['id']);
            $row->appendChild($td);

            $lastPost = ForumToolbox::getLastTopicPost($topic['id']);

            // last post
            $td = XMLToolbox::createElement('td');

            if($lastPost)
            {
                $a = XMLToolbox::createElement('a');
                $img = XMLToolbox::createElement('img');
                $a->setAttribute('href', 'forum.php?module=Topic&command=view&id=' . $topic['id']);
                $img->setAttribute('src', $this->owner->getSkinPath() . 'images/arrow.png');
                $img->setAttribute('alt', $language['Modules.Forum.LastPost']);
                $a->appendChild($img);
                $td->appendChild($a);
                $td->appendChild( XMLToolbox::createTextNode( date( $config['site.date_format'], $lastPost['date_time']) ) );
                $td->appendChild( XMLToolbox::createElement('br') );
                $td->appendChild( XMLToolbox::createTextNode($language['Modules.Forum.by'] . ' ') );

                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', 'character.php?name=' . urlencode($lastPost['poster']) );
                $a->nodeValue = $lastPost['poster'];
                $td->appendChild($a);
            }
            // no replies
            else
            {
                $td->nodeValue = $language['Modules.Forum.NoPosts'];
            }

            $row->appendChild($td);

            // actions
            if($this->admin)
            {
                $td = XMLToolbox::createElement('td');

                // remove topic
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', 'admin.php?module=Topic&command=remove&id=' . $topic['id']);
                $a->setAttribute('onclick', 'if( confirm(Language[0]) ) { return pageForum.Delete(' . $topic['id'] . '); } else { return false; }');
                $a->nodeValue = $language['main.admin.DeleteSubmit'];
                $td->appendChild($a);

                $td->appendChild( XMLToolbox::createTextNode(' | ') );

                // pin/unpin
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', 'admin.php?module=Topic&command=' . ($topic['pinned'] ? 'unpin' : 'pin') . '&id=' . $topic['id']);
                $a->nodeValue = $language['Modules.Forum.' . ($topic['pinned'] ? 'Unpin' : 'Pin') . 'Submit'];
                $td->appendChild($a);

                $td->appendChild( XMLToolbox::createTextNode(' | ') );

                // open/close
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', 'admin.php?module=Topic&command=' . ($topic['closed'] ? 'open' : 'close') . '&id=' . $topic['id']);
                $a->nodeValue = $language['Modules.Forum.' . ($topic['closed'] ? 'Open' : 'Close') . 'Submit'];
                $td->appendChild($a);

                $row->appendChild($td);
            }

            $tbody->appendChild($row);
        }

        $table->appendChild($tbody);

        // outputs table
        return XMLToolbox::saveXML($table);
    }

    // admin backend
    public $admin = false;
}

?>
