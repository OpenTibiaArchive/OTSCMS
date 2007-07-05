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
    Images management AJAX wrapper.
*/

GalleryAJAX.prototype = new XMLHttpRequest;

function GalleryAJAX()
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

    // deletes image
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastRemove = true;
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Gallery", "remove", "id=" + ID);
    }

    // loads image info
    this.edit = function(ID)
    {
        // sets last action flag
        this.lastRemove = false;
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Gallery", "edit", "id=" + ID);
    }

    // callback handlers

    this.onRemove = function()
    {
        // deletes image from website
        deleteNode("galleryID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onEdit = function()
    {
        // prepares edition form
        GallerysFormEdit.action = "admin.php?module=Gallery&command=update&id=" + this.lastData;
        GallerysFormEdit.onsubmit = document.getElementById("ajaxWrapper").show;

        oldGallery = this.root.getElementByTagName("data");

        // sets form fields values
        for(i = 0; i < GallerysFormEdit.elements.length; i++)
        {
            Element = GallerysFormEdit.elements[i];

            switch(Element.name)
            {
                case "gallery[name]":
                    Element.value = oldGallery.getTagValue("name");
                    break;

                case "gallery[content]":
                    Element.value = oldGallery.getTagValue("content");
                    break;

                case "gallery[binary]":
                    Element.checked = oldGallery.getTagValue("binary") == Element.value;
                    break;

                // only text links will have values assigned
                case "gallery[file]":
                    if( !oldGallery.getTagValue("binary") )
                    {
                        Element.value = oldGallery.getTagValue("file");
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
        AJAXMain.appendChild(GallerysFormEdit);
        document.getElementById("ajaxWrapper").show("block");

        // everything's fine
        return false;
    }
}

// GalleryAJAX default instance
pageGallery = new GalleryAJAX();

// switches file mode
function switchFileMode(Field, binary)
{
    Field.type = binary ? "file" : "text";
}

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    GallerysForm = document.getElementById("galleryForm");

    // no administration panel available
    if(!GallerysForm)
    {
        return;
    }

    GallerysForm.enctype = "multipart/form-data";

    // some form preparations
    for(i = 0; i < GallerysForm.length; i++)
    {
        // file field reference
        if(GallerysForm.elements[i].name == "gallery[file]")
        {
            FileField = GallerysForm.elements[i];
        }

        // file mode switch
        if(GallerysForm.elements[i].name == "gallery[binary]")
        {
            GallerysForm.elements[i].onclick = new Function("switchFileMode(FileField, " + GallerysForm.elements[i].value + ");");
        }
    }

    // edition form
    GallerysFormEdit = GallerysForm.cloneNode(true);
    GallerysFormEdit.id = "galleryEdit";

    // edition form preparations
    for(i = 0; i < GallerysFormEdit.elements.length; i++)
    {
        if(GallerysFormEdit.elements[i].type == "submit")
        {
            GallerysFormEdit.elements[i].value = Language[3];
        }

        // file field reference
        if(GallerysFormEdit.elements[i].name == "gallery[file]")
        {
            FileFieldEdit = GallerysFormEdit.elements[i];
        }

        // file mode switch
        if(GallerysFormEdit.elements[i].name == "gallery[binary]")
        {
            GallerysFormEdit.elements[i].onclick = new Function("switchFileMode(FileFieldEdit, " + GallerysFormEdit.elements[i].value + ");");
        }
    }
};
