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
    Private messages deletion.
*/

PMAJAX.prototype = new XMLHttpRequest;

function PMAJAX()
{
    this.lastData = false;

    this.callback = function()
    {
        // deletes pm entry from website
        deleteNode("pmID_" + this.lastData);

        // everything's fine
        return false;
    }

    // deletes private message
    this.remove = function(ID)
    {
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "PM", "remove", "id=" + ID);
    }

    this.Delete = function(ID)
    {
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "PM", "delete", "id=" + ID);
    }
}

// PMAJAX default instance
pagePM = new PMAJAX();
