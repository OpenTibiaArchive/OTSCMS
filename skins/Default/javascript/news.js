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
    News management AJAX wrapper.
*/

NewsAJAX.prototype = new XMLHttpRequest;

function NewsAJAX()
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

    // deletes news
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastAction = "remove";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "News", "remove", "id=" + ID);
    }

    // adds new news to list
    this.insert = function()
    {
        // sets last action flag
        this.lastAction = "insert";

        // executes AJAX request
        return this.request("POST", "News", "insert", dumpForm(NewsForm) );
    }

    // loads link info
    this.edit = function(ID)
    {
        // sets last action flag
        this.lastAction = "edit";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "News", "edit", "id=" + ID);
    }

    // updates existing link
    this.update = function(ID)
    {
        // sets last action flag
        this.lastAction = "update";
        this.lastData = ID;

        // executes AJAX request
        return this.request("POST", "News", "update", "id=" + ID + "&" + dumpForm(NewsFormEdit) );
    }

    // callback handlers

    this.onRemove = function()
    {
        // deletes news from list
        deleteNode("newsID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onInsert = function()
    {
        // resets form
        NewsForm.reset();

        // reads items list
        newList = this.root.getElementByTagName("list").getElementsByTagName("field");

        // searches for new row
        for(i = 0; i < newList.length; i++)
        {
            ID = newList[i].getTagValue("id");

            // if row doesnt exists then we've found newly inserted news
            if( !document.getElementById("newsID_" + ID) )
            {
                // loads item
                item = newList[i];
                Name = item.getTagValue("name");
                DateTime = item.getTagValue("date_time");

                // new news row
                row = document.getElementById("newssTable").tBodies[0].insertRow(-1);
                row.id = "news_ID" + ID;

                // title field
                cell = row.insertCell(-1);
                cell.appendChild( document.createTextNode(Name) );

                // posting date
                cell = row.insertCell(-1);
                cell.appendChild( document.createTextNode(DateTime) );

                // delete action link
                deleteLink = document.createElement("a");
                deleteLink.href = "/admin/module=News&command=remove&id=" + ID;
                deleteLink.onclick = new Function("if( confirm(Language[0]) ) { return pageNews.remove(" + ID + "); } else { return false; }");
                deleteLink.appendChild( document.createTextNode(Language[2]) );

                // edit link
                editLink = document.createElement("a");
                editLink.href = "/admin/module=News&command=edit&id=" + ID;
                editLink.onclick = new Function("return pageNews.edit(" + ID + ");");
                editLink.appendChild( document.createTextNode(Language[1]) );

                cell = row.insertCell(-1);
                cell.appendChild(deleteLink);
                cell.appendChild( document.createTextNode(" | ") );
                cell.appendChild(editLink);

                break;
            }
        }

        return false;
    }

    this.onEdit = function()
    {
        // prepares edition form
        NewsFormEdit.action = "/admin/module=News&command=update&id=" + this.lastData;
        NewsFormEdit.onsubmit = new Function("return pageNews.update(" + this.lastData + ");");

        oldNews = this.root.getElementByTagName("data");
        oldNames = oldNews.getElementByTagName("name");
        oldContents = oldNews.getElementByTagName("content");

        nameMatch = /news\[name\]\[(.*?)\]/;
        contentMatch = /news\[content\]\[(.*?)\]/;

        // sets form fields values
        for(i = 0; i < NewsFormEdit.elements.length; i++)
        {
            Element = NewsFormEdit.elements[i];

            if(Language = nameMatch.exec(Element.name) )
            {
                Element.value = oldNames.getTagValue(Language[1]);
            }
            else if(Language = contentMatch.exec(Element.name) )
            {
                Element.value = oldContents.getTagValue(Language[1]);
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
        AJAXMain.appendChild(NewsFormEdit);
        document.getElementById("ajaxWrapper").show("block");

        // everything's fine
        return false;
    }

    this.onUpdate = function()
    {
        // hides edit form and clears AJAX layer
        document.getElementById("ajaxWrapper").hide();
        deleteNode(NewsFormEdit);

        // finds new title
        newList = this.root.getElementByTagName("list").getElementsByTagName("field");

        // searches for new row
        for(i = 0; i < newList.length; i++)
        {
            // if row doesnt exists then we've found newly inserted news
            if( newList[i].getTagValue("id") == this.lastData)
            {
                // loads item
                Name = newList[i].getTagValue("name");
                break;
            }
        }

        // finds link on website
        News = document.getElementById("newsID_" + this.lastData).cells[0];

        // updates link label on website
        for(i = 0; i < News.childNodes.length; i++)
        {
            if(News.childNodes[i].nodeType == 3)
            {
                News.childNodes[i].nodeValue = Name;
                break;
            }
        }

        // everything's fine
        return false;
    }
}

// NewsAJAX default instance
pageNews = new NewsAJAX();

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    NewsForm = document.getElementById("newsForm");

    // insertion form template
    NewsForm.onsubmit = new Function("return pageNews.insert();");

    NewsFormEdit = NewsForm.cloneNode(true);
    NewsFormEdit.id = "newsEdit";

    // submit button text
    for(i = 0; i < NewsFormEdit.elements.length; i++)
    {
        if(NewsFormEdit.elements[i].type == "submit")
        {
            NewsFormEdit.elements[i].value = Language[3];
            break;
        }
    }
};
