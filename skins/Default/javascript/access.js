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
    Access restrictions management AJAX wrapper.
*/

AccessAJAX.prototype = new XMLHttpRequest;

function AccessAJAX()
{
    // last action
    this.lastInsert = false;
    this.lastData = false;

    // recreates bans list
    this.callback = function()
    {
        // calls event handler
        return this.lastInsert ? this.onInsert() : this.onRemove();
    }

    // calls IP ban deletion
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastInsert = false;
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Access", "remove", "id=" + ID);
    }

    // calls IP baning
    this.insert = function()
    {
        // sets last action flag
        this.lastInsert = true;

        // executes AJAX request
        return this.request("POST", "Access", "insert", dumpForm(AccessForm) );
    }

    // event handlers

    this.onRemove = function()
    {
        // just removes row
        deleteNode("accessID_" + this.lastData);
        return false;
    }

    this.onInsert = function()
    {
        // resets form
        AccessForm.reset();

        // reads items list
        newList = this.root.getElementByTagName("list").getElementsByTagName("field");

        // searches for new row
        for(i = 0; i < newList.length; i++)
        {
            ID = newList[i].getTagValue("id");

            // if row doesnt exists then we've found newly inserted access right
            if( !document.getElementById("accessID_" + ID) )
            {
                // loads item
                item = newList[i];
                Name = item.getTagValue("name");
                Content = item.getTagValue("content");

                row = document.getElementById("accesssTable").tBodies[0].insertRow(-1);

                // module field
                cell = row.insertCell(-1);
                cell.appendChild( document.createTextNode(Name) );

                // group field
                cell = row.insertCell(-1);
                cell.appendChild( document.createTextNode(Content) );

                // delete action link
                a = document.createElement("a");
                a.href = "/admin/module=Access&command=remove&id=" + ID;
                a.onclick = new Function("if( confirm(Language[0]) ) { return pageAccess.remove(" + ID + "); } else { return false; }");
                a.appendChild( document.createTextNode(Language[2]) );

                cell = row.insertCell(-1);
                cell.appendChild(a);

                break;
            }
        }
    }
}

// AccessAJAX default instance
pageAccess = new AccessAJAX();

// handles module change
function changeModule()
{
    // loads new module commands
    newCommands = moduleCommands(this.value);

    commandSelector = document.getElementById("commandSelect");
    commandSelector.length = newCommands.length;

    // inserts new options
    for(i = 0; i < newCommands.length; i++)
    {
        commandSelector[i].text = newCommands[i];
        commandSelector[i].value = newCommands[i];
    }
}

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    AccessForm = document.getElementById("accessForm");

    // insertion form template
    AccessForm.onsubmit = new Function("return pageAccess.insert();");

    // finds module and command fields
    for(i = 0; i < AccessForm.elements.length; i++)
    {
        if(AccessForm.elements[i].name == "access[module]")
        {
            AccessForm.elements[i].id = "moduleSelect";
        }
        else if(AccessForm.elements[i].name == "access[command]")
        {
            AccessForm.elements[i].id = "commandSelect";
        }
    }

    // sets module change handler
    document.getElementById("moduleSelect").onchange = changeModule;
};
