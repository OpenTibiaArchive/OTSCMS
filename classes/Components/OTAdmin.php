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
    OTAdmin console.
*/

class ComponentOTAdmin extends TemplateComponent
{
    // server object
    private $otadmin;

    // sets OTAdmin record
    public function setOTAdmin(CMS_OTAdmin $otadmin)
    {
        $this->otadmin = $otadmin;
    }

    // displays component
    public function display()
    {
        // global resources
        $language = OTSCMS::getResource('Language');

        // panel container
        $panel = XMLToolbox::createDocumentFragment();

        // textarea for log
        $textarea = XMLToolbox::createElement('textarea');
        $textarea->setAttribute('readonly', 'readonly');
        $textarea->setAttribute('rows', '10');
        $textarea->setAttribute('cols', '60');

        // puts log lines
        foreach( explode("\n", Session::read('log') ) as $line)
        {
            $textarea->addContent($line . "\n");
        }

        // puts server signature
        $textarea->addContent($this->otadmin['name'] . '@' . $this->otadmin['content'] . ':' . $this->otadmin['port'] . '$ ');
        $panel->addContent($textarea);

        // command forms
        $form = XMLToolbox::createElement('form');
        $form->setAttribute('action', 'admin/module=OTAdmin&command=execute&method=ping&id=' . $this->otadmin['id']);
        $form->setAttribute('method', 'post');
        $panel->addContent($form);

        $submit = XMLToolbox::createElement('input');
        $submit->setAttribute('type', 'submit');
        $submit->setAttribute('value', $language['Modules.OTAdmin.Commands.ping']);
        $form->addContent($submit);

        $form = XMLToolbox::createElement('form');
        $form->setAttribute('action', 'admin/module=OTAdmin&command=execute&method=' . OTS_Admin::COMMAND_BROADCAST . '&id=' . $this->otadmin['id']);
        $form->setAttribute('method', 'post');
        $panel->addContent($form);

        $submit = XMLToolbox::createElement('input');
        $submit->setAttribute('type', 'submit');
        $submit->setAttribute('value', $language['Modules.OTAdmin.Commands.broadcast']);
        $form->addContent($submit);

        $input = XMLToolbox::createElement('input');
        $input->setAttribute('type', 'text');
        $input->setAttribute('name', 'param');
        $form->addContent($input);

        $form = XMLToolbox::createElement('form');
        $form->setAttribute('action', 'admin/module=OTAdmin&command=execute&method=' . OTS_Admin::COMMAND_KICK . '&id=' . $this->otadmin['id']);
        $form->setAttribute('method', 'post');
        $panel->addContent($form);

        $submit = XMLToolbox::createElement('input');
        $submit->setAttribute('type', 'submit');
        $submit->setAttribute('value', $language['Modules.OTAdmin.Commands.kick']);
        $form->addContent($submit);

        $input = XMLToolbox::createElement('input');
        $input->setAttribute('type', 'text');
        $input->setAttribute('name', 'param');
        $form->addContent($input);

        $form = XMLToolbox::createElement('form');
        $form->setAttribute('action', 'admin/module=OTAdmin&command=execute&method=' . OTS_Admin::COMMAND_CLOSE_SERVER . '&id=' . $this->otadmin['id']);
        $form->setAttribute('method', 'post');
        $panel->addContent($form);

        $submit = XMLToolbox::createElement('input');
        $submit->setAttribute('type', 'submit');
        $submit->setAttribute('value', $language['Modules.OTAdmin.Commands.close']);
        $form->addContent($submit);

        $form = XMLToolbox::createElement('form');
        $form->setAttribute('action', 'admin/module=OTAdmin&command=execute&method=' . OTS_Admin::COMMAND_PAY_HOUSES . '&id=' . $this->otadmin['id']);
        $form->setAttribute('method', 'post');
        $panel->addContent($form);

        $submit = XMLToolbox::createElement('input');
        $submit->setAttribute('type', 'submit');
        $submit->setAttribute('value', $language['Modules.OTAdmin.Commands.payHouses']);
        $form->addContent($submit);

        $form = XMLToolbox::createElement('form');
        $form->setAttribute('action', 'admin/module=OTAdmin&command=execute&method=' . OTS_Admin::COMMAND_SHUTDOWN_SERVER . '&id=' . $this->otadmin['id']);
        $form->setAttribute('method', 'post');
        $panel->addContent($form);

        $submit = XMLToolbox::createElement('input');
        $submit->setAttribute('type', 'submit');
        $submit->setAttribute('value', $language['Modules.OTAdmin.Commands.shutdown']);
        $form->addContent($submit);

        // outputs message block
        return XMLToolbox::saveXML($panel);
    }
}

?>
