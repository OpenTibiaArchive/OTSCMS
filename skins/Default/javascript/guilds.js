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
    Guilds management AJAX wrapper.
*/

GuildsAJAX.prototype = new XMLHttpRequest;

function GuildsAJAX()
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
            case "delete":
                return this.onDelete();
                break;

            case "new":
                return this.onNew();
                break;

            case "kick":
                return this.onKick();
                break;

            case "accept":
            case "reject":
                return this.onAccept();
                break;
        }

        // everything's fine
        return false;
    }

    // adds new news to list
    this.Delete = function(ID)
    {
        // sets last action flag
        this.lastAction = "delete";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Guilds", "delete", "id=" + ID);
    }

    // loads link info
    this.New = function()
    {
        // sets last action flag
        this.lastAction = "new";

        // executes AJAX request
        return this.request("POST", "Guilds", "new", "rank[guild_id]=" + GuildID + "&" + dumpForm(RanksForm) );
    }

    // kicks member
    this.kick = function(ID)
    {
        // sets last action flag
        this.lastAction = "kick";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Guilds", "kick", "id=" + ID);
    }

    // accepts membership request
    this.accept = function(ID)
    {
        // sets last action flag
        this.lastAction = "accept";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Guilds", "accept", "id=" + ID);
    }

    // rejects membership request
    this.reject = function(ID)
    {
        // sets last action flag
        this.lastAction = "reject";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Guilds", "reject", "id=" + ID);
    }

    // callback handlers

    this.onDelete = function()
    {
        // deletes news from list
        deleteNode("rankID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onKick = function()
    {
        // deletes member from list
        deleteNode("memberID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onAccept = function()
    {
        // deletes member from list
        deleteNode("requestID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onNew = function()
    {
        // resets form
        RanksForm.reset();

        // reads items list
        newList = this.root.getElementByTagName("list").getElementsByTagName("field");

        // searches for new row
        for(i = 0; i < newList.length; i++)
        {
            ID = newList[i].getTagValue("id");

            // if row doesnt exists then we've found newly inserted rank
            if( !document.getElementById("rankID_" + ID) )
            {
                // loads item
                Name = newList[i].getTagValue("name");

                // rank name field
                cell = row.insertCell(-1);
                cell.appendChild( document.createTextNode(Name) );

                // delete action link
                deleteLink = document.createElement("a");
                deleteLink.href = "/admin/module=Guilds&command=delete&id=" + ID;
                deleteLink.onclick = new Function("if( confirm(Language[0]) ) { return pageGuilds.Delete(" + ID + "); } else { return false; }");
                deleteLink.appendChild( document.createTextNode(Language[2]) );

                cell = row.insertCell(-1);
                cell.appendChild(deleteLink);

                break;
            }
        }
    }
}

// GuildsAJAX default instance
pageGuilds = new GuildsAJAX();

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    RanksForm = document.getElementById("ranksForm");

    if(RanksForm)
    {
        // insertion form template
        RanksForm.onsubmit = new Function("return pageGuilds.New();");
    }
};
