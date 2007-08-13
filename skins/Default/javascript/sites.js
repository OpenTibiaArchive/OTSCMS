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
    Sites management AJAX wrapper.
*/

SitesAJAX.prototype = new XMLHttpRequest;

function SitesAJAX()
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

    // deletes poll
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastAction = "remove";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Sites", "remove", "id=" + ID);
    }

    // adds new poll to list
    this.insert = function()
    {
        // we have to find a way to bind FCKEditor to it
        return true;

        // sets last action flag
        this.lastAction = "insert";

        // executes AJAX request
        return this.request("POST", "Sites", "insert", dumpForm(SitesForm) );
    }

    // loads polls info
    this.edit = function(ID)
    {
        // we have to find a way to bind FCKEditor to it
        return true;

        // sets last action flag
        this.lastAction = "edit";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Sites", "edit", "id=" + ID);
    }

    // updates existing poll
    this.update = function(ID)
    {
        // we have to find a way to bind FCKEditor to it
        return true;

        // sets last action flag
        this.lastAction = "update";
        this.lastData = ID;

        // executes AJAX request
        return this.request("POST", "Sites", "update", "id=" + ID + "&" + dumpForm(SitesFormEdit) );
    }

    // callback handlers

    this.onRemove = function()
    {
        // deletes poll from list
        deleteNode("siteID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onInsert = function()
    {
        // searches for poll info
        newSite = this.root.getElementByTagName("list").getElementsByTagName("field");

        // finds new poll
        for(i = 0; i < newSite.length; i++)
        {
            ID = newSite[i].getAttribute("name");

            // checks if given poll exists already
            if( !document.getElementById("siteID_" + ID) )
            {
                // if no then we've found new ID
                Name = newSite[i].getAttribute("value");
                break;
            }
        }

        // creates new link on website
        newSite = document.createElement("li");
        newSite.id = "siteID_" + ID;

        a = document.createElement("a");
        a.href = "/guides/" + ID;
        a.appendChild( document.createTextNode(Name) );
        newSite.appendChild(a);

        // management links

        a = document.createElement("a");
        a.onclick = new Function("return pageSites.edit(" + ID + ");");
        a.href = "/admin/module=Sites&command=edit&id=" + ID;

        img = document.createElement("img");
        img.alt = Language[1];
        img.src = BaseHref + "images/edit.gif";

        a.appendChild(img);
        newSite.appendChild(a);

        a = document.createElement("a");
        a.onclick = new Function("if( confirm(Language[0]) ) { return pageSites.remove(" + ID + "); } else { return false; }");
        a.href = "/admin/module=Sites&command=remove&id=" + ID;

        img = document.createElement("img");
        img.alt = Language[2];
        img.src = BaseHref + "images/delete.gif";

        a.appendChild(img);
        newSite.appendChild(a);

        document.getElementById("pageSites").appendChild(newSite);

        // resets form
        SitesForm.reset();

        // everything's fine
        return false;
    }

    this.onEdit = function()
    {
        // prepares edition form
        SitesFormEdit.action = "/admin/module=Sites&command=update&id=" + this.lastData;
        SitesFormEdit.onsubmit = new Function("return pageSites.update(" + this.lastData + ");");

        oldSite = this.root.getElementByTagName("data");

        // sets form fields values
        for(i = 0; i < SitesFormEdit.elements.length; i++)
        {
            Element = SitesFormEdit.elements[i];

            switch(Element.name)
            {
                case "site[name]":
                    Element.value = oldSite.getTagValue("name");
                    break;

                case "site[content]":
                    Element.value = oldSite.getTagValue("content");
                    break;

                case "site[is_home]":
                    Element.checked = parseInt( oldSite.getTagValue("is_home") ) == 1;
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
        AJAXMain.appendChild(SitesFormEdit);
        document.getElementById("ajaxWrapper").show("block");

        // everything's fine
        return false;
    }

    this.onUpdate = function()
    {
        // hides edit form and clears AJAX layer
        document.getElementById("ajaxWrapper").hide();
        deleteNode(SitesFormEdit);

        // finds link on website
        Sites = document.getElementById("siteID_" + this.lastData + "_a");

        // searches for new poll title
        for(i = 0; i < SitesFormEdit.elements.length; i++)
        {
            Element = SitesFormEdit.elements[i];

            // 've it
            if(Element.name == "site[name]")
            {
                Name = Element.value;
                break;
            }
        }

        // updates link label on website
        for(i = 0; i < Sites.childNodes.length; i++)
        {
            if(Sites.childNodes[i].nodeType == 3)
            {
                Sites.childNodes[i].nodeValue = Name;
                break;
            }
        }

        // everything's fine
        return false;
    }
}

// SitesAJAX default instance
pageSites = new SitesAJAX();

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    SitesForm = document.getElementById("siteForm");

    // insertion form template
    SitesForm.onsubmit = new Function("return pageSites.insert();");

    SitesFormEdit = SitesForm.cloneNode(true);
    SitesFormEdit.id = "siteEdit";

    // submit button text
    for(i = 0; i < SitesFormEdit.elements.length; i++)
    {
        if(SitesFormEdit.elements[i].type == "submit")
        {
            SitesFormEdit.elements[i].value = Language[3];
            break;
        }
    }
};
