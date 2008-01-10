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
    AJAX wrapper for control panel.
*/

SettingsAJAX.prototype = new XMLHttpRequest;

function SettingsAJAX()
{
    // recreates bans list
    this.callback = function()
    {
        // outputs final message
        alert( this.root.getTagValue("message") );

        // everything's fine
        return false;
    }

    // calls MSP
    this.run = function()
    {
        // executes AJAX request
        return this.request("POST", "Settings", "update", dumpForm(SettingsForm) );
    }
}

// SettingsAJAX default instance
pageSettings = new SettingsAJAX();

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    SettingsForm = document.getElementById("settingsForm");

    // insertion form template
    SettingsForm.onsubmit = new Function("return pageSettings.run();");
};
