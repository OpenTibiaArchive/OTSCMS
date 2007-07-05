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
    Links management AJAX wrapper.
*/

LinksAJAX.prototype = new XMLHttpRequest;

function LinksAJAX()
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

    // deletes link
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastAction = "remove";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Links", "remove", "id=" + ID);
    }

    // adds new link
    this.insert = function()
    {
        // sets last action flag
        this.lastAction = "insert";

        // executes AJAX request
        return this.request("POST", "Links", "insert", dumpForm(LinksForm) );
    }

    // loads link info
    this.edit = function(ID)
    {
        // sets last action flag
        this.lastAction = "edit";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Links", "edit", "id=" + ID);
    }

    // updates existing link
    this.update = function(ID)
    {
        // sets last action flag
        this.lastAction = "update";
        this.lastData = ID;

        // executes AJAX request
        return this.request("POST", "Links", "update", "id=" + ID + "&" + dumpForm(LinksFormEdit) );
    }

    // callback handlers

    this.onRemove = function()
    {
        // deletes link from website
        deleteNode("linkID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onInsert = function()
    {
        // searches for link info
        newLink = this.root.getElementByTagName("links").getElementsByTagName("field");

        // finds new link
        for(i = 0; i < newLink.length; i++)
        {
            ID = newLink[i].getAttribute("name");

            // checks if given link exists already
            if( !document.getElementById("linkID_" + ID) )
            {
                // if no then we've found new ID
                Name = newLink[i].getTagValue("name");
                Content = newLink[i].getTagValue("content");
                break;
            }
        }

        // creates new link on website
        newLink = document.createElement("div");
        newLink.id = "linkID_" + ID;
        newLink.className = "adminFrontend";

        a = document.createElement("a");
        a.className = "outLink";
        a.onclick = outLink;
        a.href = Content;
        a.appendChild( document.createTextNode(Name) );
        newLink.appendChild(a);

        // management links

        a = document.createElement("a");
        a.onclick = new Function("return pageLinks.edit(" + ID + ");");
        a.href = "admin.php?module=Links&command=edit&id=" + ID;

        img = document.createElement("img");
        img.alt = Language[1];
        img.src = BaseHref + "images/edit.gif";

        a.appendChild(img);
        newLink.appendChild(a);

        a = document.createElement("a");
        a.onclick = new Function("if( confirm(Language[0]) ) { return pageLinks.remove(" + ID + "); } else { return false; }");
        a.href = "admin.php?module=Links&command=remove&id=" + ID;

        img = document.createElement("img");
        img.alt = Language[2];
        img.src = BaseHref + "images/delete.gif";

        a.appendChild(img);
        newLink.appendChild(a);

        document.getElementById("pageLinks").appendChild(newLink);

        // resets form
        LinksForm.reset();

        // everything's fine
        return false;
    }

    this.onEdit = function()
    {
        // prepares edition form
        LinksFormEdit.action = "admin.php?module=Links&command=update&id=" + this.lastData;
        LinksFormEdit.onsubmit = new Function("return pageLinks.update(" + this.lastData + ");");

        oldLink = this.root.getElementByTagName("data");

        // sets form fields values
        for(i = 0; i < LinksFormEdit.elements.length; i++)
        {
            Element = LinksFormEdit.elements[i];

            switch(Element.name)
            {
                case "link[name]":
                    Element.value = oldLink.getTagValue("name");
                    break;

                case "link[content]":
                    Element.value = oldLink.getTagValue("content");
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
        AJAXMain.appendChild(LinksFormEdit);
        document.getElementById("ajaxWrapper").show("block");

        // everything's fine
        return false;
    }

    this.onUpdate = function()
    {
        // hides edit form and clears AJAX layer
        document.getElementById("ajaxWrapper").hide();
        deleteNode(LinksFormEdit);

        // finds link on website
        Links = document.getElementById("linkID_" + this.lastData).getElementsByTagName("a");

        for(i = 0; i < Links.length; i++)
        {
            // 've found it
            if(Links[i].className == "outLink")
            {
                webLink = Links[i];
                break;
            }
        }

        // updates link on website
        for(i = 0; i < LinksFormEdit.elements.length; i++)
        {
            Element = LinksFormEdit.elements[i];

            switch(Element.name)
            {
                case "link[name]":
                    for(j = 0; j < webLink.childNodes.length; j++)
                    {
                        if(webLink.childNodes[j].nodeType == 3)
                        {
                            webLink.childNodes[j].nodeValue = Element.value;
                            break;
                        }
                    }
                    break;

                case "link[content]":
                    webLink.href = Element.value;
                    break;
            }
        }

        // everything's fine
        return false;
    }
}

// LinksAJAX default instance
pageLinks = new LinksAJAX();
