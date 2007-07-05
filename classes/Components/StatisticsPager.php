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
    Statistics browser.
*/

class ComponentStatisticsPager extends TemplateComponent
{
    // displays component
    public function display()
    {
        // translation
        $language = OTSCMS::getResource('Language');

        $root = XMLToolbox::createDocumentFragment();

        // page links

        $div = XMLToolbox::createElement('div');
        $div->setAttribute('id', 'pagerLeft');

        // left link
        if($this['left']['show'])
        {
            $a = XMLToolbox::createElement('a');
            $a->setAttribute('href', 'statistics.php?command=highscores&list=' . $this['list'] . '&page=' . ($this['page'] - 1) );
            $a->setAttribute('onclick', 'return pageStatistics.move(\'' . $this['list'] . '\', ' . ($this['page'] - 1) . ');');
            $a->nodeValue = $language['Modules.Statistics.HighscoresRanks'] . ' ' . $this['left']['from'] . ' - ' . $this['left']['to'];
            $div->appendChild($a);
        }
        else
        {
            $div->appendChild( XMLToolbox::createTextNode(' ') );
        }

        $root->appendChild($div);

        $div = XMLToolbox::createElement('div');
        $div->setAttribute('id', 'pagerRight');

        // right link
        if($this['right']['show'])
        {
            $a = XMLToolbox::createElement('a');
            $a->setAttribute('href', 'statistics.php?command=highscores&list=' . $this['list'] . '&page=' . ($this['page'] + 1) );
            $a->setAttribute('onclick', 'return pageStatistics.move(\'' . $this['list'] . '\', ' . ($this['page'] + 1) . ');');
            $a->nodeValue = $language['Modules.Statistics.HighscoresRanks'] . ' ' . $this['right']['from'] . ' - ' . $this['right']['to'];
            $div->appendChild($a);
        }
        else
        {
            $div->appendChild( XMLToolbox::createTextNode(' ') );
        }

        $root->appendChild($div);

        $table = XMLToolbox::createElement('table');
        $tbody = XMLToolbox::createElement('tbody');

        $tr = XMLToolbox::createElement('tr');

        $th = XMLToolbox::createElement('th');
        $th->nodeValue = $language['Modules.Statistics.HighscoresRank'];
        $tr->appendChild($th);

        $th = XMLToolbox::createElement('th');
        $th->nodeValue = $language['Modules.Character.Name'];
        $tr->appendChild($th);

        $th = XMLToolbox::createElement('th');
        $th->nodeValue = $language['Modules.Statistics.HighscoresScore'];
        $tr->appendChild($th);

        // only in experience list
        if($this['list'] == 'experience')
        {
            $th = XMLToolbox::createElement('th');
            $th->nodeValue = $language['Modules.Character.Level'];
            $tr->appendChild($th);
        }

        $tbody->appendChild($tr);

        // form fields
        foreach($this['scores'] as $rank => $score)
        {
            // table row
            $row = XMLToolbox::createElement('tr');

            // lp. cell
            $td = XMLToolbox::createElement('td');
            $td->nodeValue = $rank;
            $row->appendChild($td);

            $td = XMLToolbox::createElement('td');
            $a = XMLToolbox::createElement('a');
            $a->setAttribute('href', 'character.php?name=' . $score['name']);
            $a->nodeValue = $score['name'];
            $td->appendChild($a);
            $row->appendChild($td);

            $td = XMLToolbox::createElement('td');
            $td->nodeValue = $score['value'];
            $row->appendChild($td);

            if($this['list'] == 'experience')
            {
                $td = XMLToolbox::createElement('td');
                $td->nodeValue = $score['level'];
                $row->appendChild($td);
            }

            $tbody->appendChild($row);
        }

        $table->setAttribute('id', 'statisticsPager');
        $table->appendChild($tbody);
        $root->appendChild($table);

        // outputs only table element
        echo XMLToolbox::saveXML($root);
    }
}

?>
