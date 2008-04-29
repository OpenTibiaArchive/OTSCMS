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
    OTAdmin management AJAX wrapper.
*/

OTAdminAJAX.prototype = new XMLHttpRequest;

function OTAdminAJAX()
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

            case "insert":
                return this.onInsert();
                break;
        }

        // everything's fine
        return false;
    }

    // method stub for compatibility with layout components
    this.panel = function(ID)
    {
        // redirects browser
        return true;
    }

    // deletes connection
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastAction = "remove";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "OTAdmin", "remove", "id=" + ID);
    }

    // adds connection to list
    this.insert = function()
    {
        // sets last action flag
        this.lastAction = "insert";

        // executes AJAX request
        return this.request("POST", "OTAdmin", "insert", dumpForm(OTAdminForm) );
    }

    // callback handlers

    this.onRemove = function()
    {
        // deletes connection from list
        deleteNode("otadminID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onInsert = function()
    {
        // resets form
        OTAdminForm.reset();

        // reads items list
        newList = this.root.getElementByTagName("list").getElementsByTagName("field");

        // searches for new row
        for(i = 0; i < newList.length; i++)
        {
            ID = newList[i].getTagValue("id");

            // if row doesnt exists then we've found newly inserted OTAdmin connection
            if( !document.getElementById("otadminID_" + ID) )
            {
                // loads item
                item = newList[i];
                Name = item.getTagValue("name");
                Content = item.getTagValue("content");
                Port = item.getTagValue("port");
                Password = item.getTagValue("password");

                row = document.getElementById("otadminsTable").tBodies[0].insertRow(-1);

                // server label field
                cell = row.insertCell(-1);
                cell.appendChild( document.createTextNode(Name) );

                // server address field
                cell = row.insertCell(-1);
                cell.appendChild( document.createTextNode(Content + ":" + Port) );

                // does server uses password
                cell = row.insertCell(-1);
                cell.appendChild( document.createTextNode( Password.length > 0 ? Language[15] : Language[16]) );

                // delete action link
                cell = row.insertCell(-1);
                a = document.createElement("a");
                a.href = "/admin/module=OTAdmin&command=remove&id=" + ID;
                a.onclick = new Function("if( confirm(Language[0]) ) { return pageOTAdmin.remove(" + ID + "); } else { return false; }");
                a.appendChild( document.createTextNode(Language[2]) );
                cell.appendChild(a);

                cell.appendChild( document.createTextNode(" | ") );

                cell = row.insertCell(-1);
                a = document.createElement("a");
                a.href = "/admin/module=OTAdmin&command=panel&id=" + ID;
                a.appendChild( document.createTextNode(Language[17]) );
                cell.appendChild(a);
                break;
            }
        }

        // everything's fine
        return false;
    }
}

// OTAdminAJAX default instance
pageOTAdmin = new OTAdminAJAX();

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    OTAdminForm = document.getElementById("otadminForm");

    // insertion form template
    OTAdminForm.onsubmit = new Function("return pageOTAdmin.insert();");
};
