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
    FCKEditor binding for OTSCMS.
*/

class ComponentFCK extends TemplateComponent
{
    // displays component
    public function display()
    {
        $language = OTSCMS::getResource('Language');

        // creates new FCK editor instance
        $fck = new FCKeditor('site[content]');
        // sets it up
        $fck->Value = $this['content'];
        $fck->Height = 300;
        $fck->Config['DocType'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $fck->Config['FormatOutput'] = true;
        $fck->Config['FormatSource'] = true;

        // returns HTML
        return '<form id="siteForm" action="/admin/module=Sites&amp;command=' . ( isset($this['id']) ? 'update&amp;id=' . $this['id'] : 'insert') . '" method="post">
<label for="siteName">' . htmlspecialchars($language['Modules.Sites.Name']) . ': </label><input type="text" name="site[name]" id="siteName" value="' . htmlspecialchars($this['name']) . '" /><br />
' . $fck->CreateHtml() . '
<br />
<label for="siteHome">' . htmlspecialchars($language['Modules.Sites.IsHome']) . ': </label><input type="checkbox" value="1" name="site[is_home]" id="siteHome"' . ($this['is_home'] ? ' checked="checked"' : '') . ' />
<br />
<input type="submit" value="' . htmlspecialchars($language['main.admin.UpdateSubmit']) . '" />
</form>';
    }
}

OTSCMS::setDriver('FCKeditor', '../fckeditor/fckeditor_php5');

?>
