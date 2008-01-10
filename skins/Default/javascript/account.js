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
    Files management AJAX wrapper.
*/

AccountAJAX.prototype = new XMLHttpRequest;

function AccountAJAX()
{
    // last action
    this.lastAction = false;
    this.lastData = false;

    // handles respond
    this.callback = function()
    {
        // calls given event handler
        switch(this.lastAction)
        {
            case "remove":
                return this.onRemove();
                break;

            case "block":
                return this.onBlock();
                break;

            case "unblock":
                return this.onUnblock();
                break;
        }

        // everything's fine
        return false;
    }

    // deletes account
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastAction = "remove";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Account", "remove", "id=" + ID);
    }

    // blocks account
    this.block = function(ID)
    {
        // sets last action flag
        this.lastAction = "block";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Account", "block", "id=" + ID);
    }

    // unblocks account
    this.unblock = function(ID)
    {
        // sets last action flag
        this.lastAction = "unblock";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Account", "unblock", "id=" + ID);
    }

    // for compatibility with PHP components and possibly further use
    this.edit = function(ID)
    {
        return true;
    }

    // callback handlers

    this.onRemove = function()
    {
        // deletes file from website
        deleteNode("accountID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onBlock = function()
    {
        // sets label in table cell
        deleteNode("accountID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onUnblock = function()
    {
        // sets label in table cell
        deleteNode("accountID_" + this.lastData);

        // everything's fine
        return false;
    }
}

// AccountAJAX default instance
pageAccount = new AccountAJAX();

// checks if email address typed in given field is valid
function checkmail(field)
{
    // yes it is
    if( /^\w+[\+\.\w-]*@([\w-]+\.)*\w+[\w-]*\.([a-z]{2,4}|\d+)$/i.test(field.value) )
    {
        return true;
    }
    else
    {
        alert(Language[14]);
        field.select();
        return false;
    }
}
