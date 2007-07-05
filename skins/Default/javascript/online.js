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
    Servers list management AJAX wrapper.
*/

OnlineAJAX.prototype = new XMLHttpRequest;

function OnlineAJAX()
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

            case "edit":
                return this.onEdit();
                break;

            case "update":
                return this.onUpdate();
                break;
        }

        // everything's fine
        return false;
    }

    // deletes server
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastAction = "remove";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Online", "remove", "id=" + ID);
    }

    // adds new server
    this.insert = function()
    {
        // sets last action flag
        this.lastAction = "insert";

        // executes AJAX request
        return this.request("POST", "Online", "insert", dumpForm(OnlinesForm) );
    }

    // loads server info
    this.edit = function(ID)
    {
        // sets last action flag
        this.lastAction = "edit";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Online", "edit", "id=" + ID);
    }

    // updates existing server
    this.update = function(ID)
    {
        // sets last action flag
        this.lastAction = "update";
        this.lastData = ID;

        // executes AJAX request
        return this.request("POST", "Online", "update", "id=" + ID + "&" + dumpForm(OnlinesFormEdit) );
    }

    // callback handlers

    this.onRemove = function()
    {
        // deletes server from website
        deleteNode("onlineID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onInsert = function()
    {
        // searches for server info
        newOnline = this.root.getElementByTagName("onlines").getElementsByTagName("field");

        // finds new link
        for(i = 0; i < newOnline.length; i++)
        {
            ID = newOnline[i].getAttribute("name");

            // checks if given server exists already
            if( !document.getElementById("linkID_" + ID) )
            {
                // if no then we've found new ID
                Name = newOnline[i].getTagValue("name");
                Content = newOnline[i].getTagValue("content");
                break;
            }
        }

        // creates new server on website
        newOnline = document.createElement("div");
        newOnline.id = "onlineID_" + ID;

        // statistics field
        stats = document.createElement("div");
        stats.id = "onlineStatsID_" + ID;
        stats.className = "floatRight";
        stats.appendChild( document.createTextNode(Content) );
        newOnline.appendChild(stats);
        newOnline.appendChild( document.createTextNode(Name) );

        // management links

        newOnline.appendChild( document.createElement("br") );

        a = document.createElement("a");
        a.onclick = new Function("return pageOnline.edit(" + ID + ");");
        a.href = "admin.php?module=Online&command=edit&id=" + ID;

        img = document.createElement("img");
        img.alt = Language[1];
        img.src = BaseHref + "images/edit.gif";

        a.appendChild(img);
        newOnline.appendChild(a);

        a = document.createElement("a");
        a.onclick = new Function("if( confirm(Language[0]) ) { return pageOnline.remove(" + ID + "); } else { return false; }");
        a.href = "admin.php?module=Online&command=remove&id=" + ID;

        img = document.createElement("img");
        img.alt = Language[2];
        img.src = BaseHref + "images/delete.gif";

        a.appendChild(img);
        newOnline.appendChild(a);

        document.getElementById("pageOnlines").appendChild(newOnline);

        // resets form
        OnlinesForm.reset();

        // everything's fine
        return false;
    }

    this.onEdit = function()
    {
        // prepares edition form
        OnlinesFormEdit.action = "admin.php?module=Online&command=update&id=" + this.lastData;
        OnlinesFormEdit.onsubmit = new Function("return pageOnline.update(" + this.lastData + ");");

        oldOnline = this.root.getElementByTagName("data");

        // sets form fields values
        for(i = 0; i < OnlinesFormEdit.elements.length; i++)
        {
            Element = OnlinesFormEdit.elements[i];

            switch(Element.name)
            {
                case "link[name]":
                    Element.value = oldOnline.getTagValue("name");
                    break;

                case "link[content]":
                    Element.value = oldOnline.getTagValue("content");
                    break;

                case "link[port]":
                    Element.value = oldOnline.getTagValue("port");
                    break;
            }
        }

        // clears form layer
        for(i = AJAXMain.childNodes.length - 1; i > -1; i--)
        {
            if(AJAXMain.childNodes[i].nodeType == 1 && AJAXMain.childNodes[i].id != "ajaxClose")
            {
                deleteNode(AJAXMain.childNodes[i]);
            }
        }

        // makes form layer visible
        AJAXMain.appendChild(OnlinesFormEdit);
        document.getElementById("ajaxWrapper").show("block");

        // everything's fine
        return false;
    }

    this.onUpdate = function()
    {
        // hides edit form and clears AJAX layer
        document.getElementById("ajaxWrapper").hide();
        deleteNode(OnlinesFormEdit);

        // searches for server info
        newOnline = this.root.getElementByTagName("onlines").getElementsByTagName("field");

        // finds current link
        for(i = 0; i < newOnline.length; i++)
        {
            // checks if ID matches
            if( newOnline[i].getAttribute("name") == this.lastData)
            {
                // 've found
                newOnline = newOnline[i];
                break;
            }
        }

        // server stats
        webOnline = document.getElementById("onlineStatsID_" + this.lastData);
        for(i = 0; i < webOnline.childNodes.length; i++)
        {
            if(webOnline.childNodes[i].nodeType == 3)
            {
                webOnline.childNodes[i].nodeValue = newOnline.getTagValue("content");
                break;
            }
        }

        // finds server on website
        webOnline = document.getElementById("onlineID_" + this.lastData);

        for(i = 0; i < webOnline.childNodes.length; i++)
        {
            // 've found it
            if(webOnline.childNodes[i].nodeType == 3 && webOnline.childNodes[i].nodeValue.trim() )
            {
                webOnline.childNodes[i].nodeValue = newOnline.getTagValue("name");
                break;
            }
        }

        // everything's fine
        return false;
    }
}

// OnlineAJAX default instance
pageOnline = new OnlineAJAX();
