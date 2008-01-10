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
            $a->setAttribute('href', 'statistics/' . $this['list'] . '/page' . ($this['page'] - 1) );
            $a->setAttribute('onclick', 'return pageStatistics.move(\'' . $this['list'] . '\', ' . ($this['page'] - 1) . ');');
            $a->addContent($language['Modules.Statistics.HighscoresRanks'] . ' ' . $this['left']['from'] . ' - ' . $this['left']['to']);
            $div->addContent($a);
        }
        else
        {
            $div->addContent(' ');
        }

        $root->addContent($div);

        $div = XMLToolbox::createElement('div');
        $div->setAttribute('id', 'pagerRight');

        // right link
        if($this['right']['show'])
        {
            $a = XMLToolbox::createElement('a');
            $a->setAttribute('href', 'statistics/' . $this['list'] . '/page' . ($this['page'] + 1) );
            $a->setAttribute('onclick', 'return pageStatistics.move(\'' . $this['list'] . '\', ' . ($this['page'] + 1) . ');');
            $a->addContent($language['Modules.Statistics.HighscoresRanks'] . ' ' . $this['right']['from'] . ' - ' . $this['right']['to']);
            $div->addContent($a);
        }
        else
        {
            $div->addContent(' ');
        }

        $root->addContent($div);

        $table = XMLToolbox::createElement('table');
        $tbody = XMLToolbox::createElement('tbody');

        $tr = XMLToolbox::createElement('tr');

        $th = XMLToolbox::createElement('th');
        $th->addContent($language['Modules.Statistics.HighscoresRank']);
        $tr->addContent($th);

        $th = XMLToolbox::createElement('th');
        $th->addContent($language['Modules.Character.Name']);
        $tr->addContent($th);

        $th = XMLToolbox::createElement('th');
        $th->addContent($language['Modules.Statistics.HighscoresScore']);
        $tr->addContent($th);

        // only in experience list
        if($this['list'] == 'experience')
        {
            $th = XMLToolbox::createElement('th');
            $th->addContent($language['Modules.Character.Level']);
            $tr->addContent($th);
        }

        $tbody->addContent($tr);

        // form fields
        foreach($this['scores'] as $rank => $score)
        {
            // table row
            $row = XMLToolbox::createElement('tr');

            // lp. cell
            $td = XMLToolbox::createElement('td');
            $td->addContent($rank);
            $row->addContent($td);

            $td = XMLToolbox::createElement('td');
            $a = XMLToolbox::createElement('a');
            $a->setAttribute('href', 'characters/' . $score['name']);
            $a->addContent($score['name']);
            $td->addContent($a);
            $row->addContent($td);

            $td = XMLToolbox::createElement('td');
            $td->addContent($score['value']);
            $row->addContent($td);

            if($this['list'] == 'experience')
            {
                $td = XMLToolbox::createElement('td');
                $td->addContent($score['level']);
                $row->addContent($td);
            }

            $tbody->addContent($row);
        }

        $table->setAttribute('id', 'statisticsPager');
        $table->addContent($tbody);
        $root->addContent($table);

        // outputs only table element
        return XMLToolbox::saveXML($root);
    }
}

?>
