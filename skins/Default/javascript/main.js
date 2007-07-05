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

// adds trim() method to String prototype to strip white characters from start and end
String.prototype.trim = function()
{
    return this.replace(/^\s+|\s+$/g, "");
};

// hide(), show() and isVisible() methods for HTML tags
Element.prototype.hide = function()
{
    this.style.display = "none";
    this.style.visibility = "hidden";
};

Element.prototype.show = function(mode)
{
    this.style.display = mode;
    this.style.visibility = "visible";
};

Element.prototype.isVisible = function()
{
    return this.style.visibility == "visible";
};

// fetching single element by tag name routines
Element.prototype.getElementByTagName = function(tag)
{
    return this.getElementsByTagName(tag).item(0);
};

Element.prototype.getTagValue = function(tag)
{
    return this.getElementByTagName(tag).getAttribute("value");
};

// opens link in new windows
function outLink()
{
    window.open(this.href);
    return false;
}

// removes node from document
function deleteNode(node)
{
    // if it's string then drop node with given ID
    if( typeof(node) == "string")
    {
        deleteNode( document.getElementById(node) );
    }
    // otherwise just drop it
    else
    {
        node.parentNode.removeChild(node);
    }
}

// applies some improvements to website interface and behavior
window.onload = function()
{
    allLinks = document.getElementsByTagName("a");

    // all links with class="outLink" will be opened in new window
    for(i = 0; i < allLinks.length; i++)
    {
        if(allLinks[i].className == "outLink")
        {
            allLinks[i].onclick = outLink;
        }
    }

    // sets links form variable
    LinksForm = document.getElementById("linkForm");

    if(LinksForm)
    {
        // insertion form handle
        LinksForm.onsubmit = new Function("return pageLinks.insert();");

        // creates links edition form
        LinksFormEdit = LinksForm.cloneNode(true);
        LinksFormEdit.id = "linkEdit";

        // submit button text
        for(i = 0; i < LinksFormEdit.elements.length; i++)
        {
            if(LinksFormEdit.elements[i].type == "submit")
            {
                LinksFormEdit.elements[i].value = Language[3];
                break;
            }
        }
    }

    // sets servers form variable
    OnlinesForm = document.getElementById("onlineForm");

    if(OnlinesForm)
    {
        // insertion form handle
        OnlinesForm.onsubmit = new Function("return pageOnline.insert();");

        // creates server edition form
        OnlinesFormEdit = OnlinesForm.cloneNode(true);
        OnlinesFormEdit.id = "onlineEdit";

        // submit button text
        for(i = 0; i < OnlinesFormEdit.elements.length; i++)
        {
            if(OnlinesFormEdit.elements[i].type == "submit")
            {
                OnlinesFormEdit.elements[i].value = Language[3];
                break;
            }
        }
    }

    // AJAX layer
    AJAXMain = document.getElementById("ajaxMain");

    // calls custom page preparetions
    if( typeof(pageLoaded) == "function")
    {
        pageLoaded();
    }
};

// module menus routines

toogleOnOff = false;

// shows menu right under nav link
function showMenu(id)
{
    Menu = document.getElementById(id);

    LinkButton = document.getElementById(id + "_Btn");

    Menu.style.left = (LinkButton.offsetLeft) + "px";
    Menu.style.top = (LinkButton.offsetTop + LinkButton.offsetHeight) + "px";
    Menu.show("block");
}

// switches menu visibility
function toogleMenu(id)
{
    toogleOnOff = toogleOnOff ? false : true;

    if(toogleOnOff)
    {
        showMenu(id);
    }
    else
    {
        document.getElementById(id).hide();
    }
}

// shows new menu
function changeMenu(id)
{
    // menu visibility is off
    if(!toogleOnOff)
    {
        return;
    }

    // turns off all other visible menus
    if( document.getElementById("menu_Home").isVisible() )
    {
        document.getElementById("menu_Home").hide();
    }

    if( document.getElementById("menu_Community").isVisible() )
    {
        document.getElementById("menu_Community").hide();
    }

    if( document.getElementById("menu_Forum").isVisible() )
    {
        document.getElementById("menu_Forum").hide();
    }

    if( document.getElementById("menu_Library").isVisible() )
    {
        document.getElementById("menu_Library").hide();
    }

    if( document.getElementById("menu_Statistics").isVisible() )
    {
        document.getElementById("menu_Statistics").hide();
    }

    showMenu(id);
}
