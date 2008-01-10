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

DownloadAJAX.prototype = new XMLHttpRequest;

function DownloadAJAX()
{
    // last action
    this.lastRemove = false;
    this.lastData = false;

    // handles respond
    this.callback = function()
    {
        // calls given event handler
        if(this.lastRemove)
        {
            return this.onRemove();
        }
        else
        {
            return this.onEdit();
        }

        // everything's fine
        return false;
    }

    // deletes file
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastRemove = true;
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Download", "remove", "id=" + ID);
    }

    // loads file info
    this.edit = function(ID)
    {
        // sets last action flag
        this.lastRemove = false;
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Download", "edit", "id=" + ID);
    }

    // callback handlers

    this.onRemove = function()
    {
        // deletes file from website
        deleteNode("downloadID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onEdit = function()
    {
        // prepares edition form
        DownloadsFormEdit.action = "/admin/module=Download&command=update&id=" + this.lastData;
        DownloadsFormEdit.onsubmit = document.getElementById("ajaxWrapper").show;

        oldDownload = this.root.getElementByTagName("data");

        // sets form fields values
        for(i = 0; i < DownloadsFormEdit.elements.length; i++)
        {
            Element = DownloadsFormEdit.elements[i];

            switch(Element.name)
            {
                case "download[name]":
                    Element.value = oldDownload.getTagValue("name");
                    break;

                case "download[content]":
                    Element.value = oldDownload.getTagValue("content");
                    break;

                case "download[binary]":
                    Element.checked = oldDownload.getTagValue("binary") == Element.value;
                    break;

                // only text links will have values assigned
                case "download[file]":
                    if( !oldDownload.getTagValue("binary") )
                    {
                        Element.value = oldDownload.getTagValue("file");
                    }
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
        AJAXMain.appendChild(DownloadsFormEdit);
        document.getElementById("ajaxWrapper").show("block");

        // everything's fine
        return false;
    }
}

// DownloadAJAX default instance
pageDownload = new DownloadAJAX();

// switches file mode
function switchFileMode(Field, binary)
{
    Field.type = binary ? "file" : "text";
}

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    DownloadsForm = document.getElementById("downloadForm");

    // no administration panel available
    if(!DownloadsForm)
    {
        return;
    }

    DownloadsForm.enctype = "multipart/form-data";

    // some form preparations
    for(i = 0; i < DownloadsForm.length; i++)
    {
        // file field reference
        if(DownloadsForm.elements[i].name == "download[file]")
        {
            FileField = DownloadsForm.elements[i];
        }

        // file mode switch
        if(DownloadsForm.elements[i].name == "download[binary]")
        {
            DownloadsForm.elements[i].onclick = new Function("switchFileMode(FileField, " + DownloadsForm.elements[i].value + ");");
        }
    }

    // edition form
    DownloadsFormEdit = DownloadsForm.cloneNode(true);
    DownloadsFormEdit.id = "downloadEdit";

    // edition form preparations
    for(i = 0; i < DownloadsFormEdit.elements.length; i++)
    {
        if(DownloadsFormEdit.elements[i].type == "submit")
        {
            DownloadsFormEdit.elements[i].value = Language[3];
        }

        // file field reference
        if(DownloadsFormEdit.elements[i].name == "download[file]")
        {
            FileFieldEdit = DownloadsFormEdit.elements[i];
        }

        // file mode switch
        if(DownloadsFormEdit.elements[i].name == "download[binary]")
        {
            DownloadsFormEdit.elements[i].onclick = new Function("switchFileMode(FileFieldEdit, " + DownloadsFormEdit.elements[i].value + ");");
        }
    }
};
