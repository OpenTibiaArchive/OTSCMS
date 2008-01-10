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
    AJAX wrapper for massive spawn change.
*/

MSPAJAX.prototype = new XMLHttpRequest;

function MSPAJAX()
{
    // recreates bans list
    this.callback = function()
    {
        // outputs final message
        alert( this.root.getElementByTagName("message").getAttribute("value") );

        // clears form
        MSPForm.reset();

        // everything's fine
        return false;
    }

    // calls MSP
    this.run = function()
    {
        // executes AJAX request
        return this.request("POST", "MSP", "change", dumpForm(MSPForm) );
    }
}

// MSPAJAX default instance
pageMSP = new MSPAJAX();

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    MSPForm = document.getElementById("mspForm");

    // insertion form template
    MSPForm.onsubmit = new Function("return pageMSP.run();");
};
