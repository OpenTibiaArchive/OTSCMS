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
    Character management AJAX wrapper.
*/

CharacterAJAX.prototype = new XMLHttpRequest;

function CharacterAJAX()
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

            case "set":
                return this.onSet();
                break;

            case "pop":
                return this.onPop();
                break;

            case "push":
                return this.onPush();
                break;
        }

        // everything's fine
        return false;
    }

    // deletes character from account management page
    this.Delete = function(ID)
    {
        this.lastAction = "remove";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Character", "delete", "id=" + ID);
    }

    // deletes poll
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastAction = "remove";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Character", "remove", "id=" + ID);
    }

    // saves new settings
    this.set = function()
    {
        // sets last action flag
        this.lastAction = "set";

        // executes AJAX request
        return this.request("POST", "Character", "set", "id=" + profileID + "&" + dumpForm(ProfileForm) );
    }

    // only for component compatibility
    this.edit = function(ID)
    {
        return true;
    }

    // deletes item from profile
    this.pop = function(ID)
    {
        // sets last action flag
        this.lastAction = "pop";
        this.lastData = ID;

        // executes AJAX request
        return this.request("POST", "Character", "push", "id=" + ID);
    }

    // appends new item to profile
    this.push = function()
    {
        // sets last action flag
        this.lastAction = "push";

        // executes AJAX request
        return this.request("GET", "Character", "pop", "id=" + profileID + "&" + dumpForm(ContainerForm) );
    }

    // callback handlers

    this.onRemove = function()
    {
        // deletes character from list
        deleteNode("characterID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onSet = function()
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

        // everything went fine
        return false;
    }

    this.onPop = function()
    {
        deleteNode("containerID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onPush = function()
    {
        // clears insertion form
        ContainerForm.reset();

        // reads items list
        newList = this.root.getElementByTagName("list").getElementsByTagName("field");

        // searches for new row
        for(i = 0; i < newList.length; i++)
        {
            ID = newList[i].getTagValue("id");

            // if row doesnt exists then we've found newly inserted access right
            if( !document.getElementById("containerID_" + ID) )
            {
                // item label
                cell = row.insertCell(-1);
                cell.appendChild( document.createTextNode( newList[i].getTagValue("name") ) );

                // delete action link
                a = document.createElement("a");
                a.href = "admin.php?module=Character&command=pop&id=" + ID;
                a.onclick = new Function("if( confirm(Language[0]) ) { return pageCharacter.pop(" + ID + "); } else { return false; }");
                a.appendChild( document.createTextNode(Language[2]) );

                cell = row.insertCell(-1);
                cell.appendChild(a);

                break;
            }
        }

        // everything's fine
        return false;
    }
}

// CharacterAJAX default instance
pageCharacter = new CharacterAJAX();

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    ProfileForm = document.getElementById("profileForm");

    // insertion form template
    if(ProfileForm)
    {
        ProfileForm.onsubmit = new Function("return pageCharacter.set();");

        // another management elements
        ContainersTable = document.getElementById("containersList");

        ContainerForm = document.getElementById("containerForm");
        ContainerForm.onsubmit = new Function("return pageCharacter.push();");
    }
};
