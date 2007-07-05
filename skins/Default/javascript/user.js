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
    Profile edition handler.
*/

UserAJAX.prototype = new XMLHttpRequest;

function UserAJAX()
{
    // displays result messages
    this.callback = function()
    {
        Messages = this.root.getElementsByTagName("message");
        Message = "";
        Next = false;

        // show result messages
        for(i = 0; i < Messages.length; i++)
        {
            Message += (Next ? "\n\n" : "") + Messages[i].getAttribute("value");
            Next = true;
        }

        alert(Message);

        // everything's fine
        return false;
    }

    // calls profile update
    this.run = function()
    {
        // executes AJAX request
        return this.request("POST", "Account", "save", dumpForm(UserForm) );
    }
}

// UserAJAX default instance
pageUser = new UserAJAX();

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    UserForm = document.getElementById("userForm");

    // insertion form template
    UserForm.onsubmit = new Function("return pageUser.run();");
};
