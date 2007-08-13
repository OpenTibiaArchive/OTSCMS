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
        $db = OTSCMS::getResource('DB');

        // list table
        $table = XMLToolbox::createElement('table');
        $table->setAttribute('class', 'listTable');
        $table->setAttribute('id', 'boardTopics');

        // headers
        $thead = XMLToolbox::createElement('thead');
        $tr = XMLToolbox::createElement('tr');

        $th = XMLToolbox::createElement('th');
        $th->addContent($language['Modules.Forum.Topic']);
        $tr->addContent($th);

        $th = XMLToolbox::createElement('th');
        $th->addContent($language['Modules.Forum.Author']);
        $tr->addContent($th);

        $th = XMLToolbox::createElement('th');
        $th->addContent($language['Modules.Forum.Replies']);
        $tr->addContent($th);

        $th = XMLToolbox::createElement('th');
        $th->addContent($language['Modules.Forum.LastPost']);
        $tr->addContent($th);

        if($this->admin)
        {
            $th = XMLToolbox::createElement('th');
            $th->addContent($language['main.admin.Actions']);
            $tr->addContent($th);
        }

        $thead->addContent($tr);
        $table->addContent($thead);

        $tbody = XMLToolbox::createElement('tbody');

        $limit = $config['forum.limit'];

        // table content
        foreach( $db->query('SELECT `id`, `name`, `date_time`, `closed`, `pinned`, `poster` FROM [posts_with_authors] WHERE `istopic` = 1 AND `upperid` = ' . $this['id'] . ' ORDER BY `pinned` DESC, `date_time` DESC LIMIT ' . $limit . ' OFFSET ' . ($limit * ($this['page'] - 1))) as $topic)
        {
            // table row
            $row = XMLToolbox::createElement('tr');
            $row->setAttribute('id', 'postID_' . $topic['id']);

            // topic title
            $td = XMLToolbox::createElement('td');

            // pinned
            if($topic['pinned'])
            {
                $td->addContent('[' . $language['Modules.Forum.PINNED'] . '] ');
            }

            // topic link
            $a = XMLToolbox::createElement('a');
            $a->setAttribute('href', '/posts/' . $topic['id']);
            $a->addContent($topic['name']);

            // topic closed
            if($topic['closed'])
            {
                $a->setAttribute('class', 'closed');
            }

            $td->addContent($a);
            $row->addContent($td);

            // author
            $td = XMLToolbox::createElement('td');
            $a = XMLToolbox::createElement('a');
            $a->setAttribute('href', '/characters/' . urlencode($topic['poster']) );
            $a->addContent($topic['poster']);
            $td->addContent($a);
            $row->addContent($td);

            // replies count
            $count = $db->query('SELECT COUNT(`id`) AS `count` FROM [posts] WHERE `istopic` = 0 AND `upperid` = ' . $topic['id'])->fetch();
            $td = XMLToolbox::createElement('td');
            $td->addContent($count['count']);
            $row->addContent($td);

            $lastPost = ForumToolbox::getLastTopicPost($topic['id']);

            // last post
            $td = XMLToolbox::createElement('td');

            if($lastPost)
            {
                $a = XMLToolbox::createElement('a');
                $img = XMLToolbox::createElement('img');
                $a->setAttribute('href', '/posts/' . $topic['id']);
                $img->setAttribute('src', $this->this['baseHref'] . 'images/arrow.png');
                $img->setAttribute('alt', $language['Modules.Forum.LastPost']);
                $a->addContent($img);
                $td->addContents($a, date( $config['site.date_format'], $lastPost['date_time']), XMLToolbox::createElement('br'), $language['Modules.Forum.by'] . ' ');

                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', '/characters/' . urlencode($lastPost['poster']) );
                $a->addContent($lastPost['poster']);
                $td->addContent($a);
            }
            // no replies
            else
            {
                $td->addContent($language['Modules.Forum.NoPosts']);
            }

            $row->addContent($td);

            // actions
            if($this->admin)
            {
                $td = XMLToolbox::createElement('td');

                // remove topic
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', '/admin/module=Topic&command=remove&id=' . $topic['id']);
                $a->setAttribute('onclick', 'if( confirm(Language[0]) ) { return pageForum.Delete(' . $topic['id'] . '); } else { return false; }');
                $a->addContent($language['main.admin.DeleteSubmit']);
                $td->addContents($a, ' | ');

                // pin/unpin
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', '/admin/module=Topic&command=' . ($topic['pinned'] ? 'unpin' : 'pin') . '&id=' . $topic['id']);
                $a->addContent($language['Modules.Forum.' . ($topic['pinned'] ? 'Unpin' : 'Pin') . 'Submit']);
                $td->addContents($a, ' | ');

                // open/close
                $a = XMLToolbox::createElement('a');
                $a->setAttribute('href', '/admin/module=Topic&command=' . ($topic['closed'] ? 'open' : 'close') . '&id=' . $topic['id']);
                $a->addContent($language['Modules.Forum.' . ($topic['closed'] ? 'Open' : 'Close') . 'Submit']);
                $td->addContent($a);

                $row->addContent($td);
            }

            $tbody->addContent($row);
        }

        $table->addContent($tbody);

        // outputs table
        return XMLToolbox::saveXML($table);
    }

    // admin backend
    public $admin = false;
}

?>
