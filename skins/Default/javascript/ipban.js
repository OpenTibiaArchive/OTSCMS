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
    IP management AJAX wrapper.
*/

IPBanAJAX.prototype = new XMLHttpRequest;

function IPBanAJAX()
{
    // last action
    this.lastInsert = false;

    // recreates bans list
    this.callback = function()
    {
        // if it was form submit then reset form
        if(this.lastInsert)
        {
            IPBanForm.reset();
        }

        tableList = document.getElementById("ipbansTable").tBodies[0];

        // clears list
        while(tableList.rows.length > 0)
        {
            tableList.deleteRow(0);
        }

        // reads items list
        newList = this.root.getElementByTagName("list").getElementsByTagName("field");

        // reloads list
        for(i = 0; i < newList.length; i++)
        {
            // loads item
            item = newList[i];
            ip = item.getTagValue("ip");
            mask = item.getTagValue("mask");
            tag = document.importNode( item.getElementByTagName("a"), true);

            row = tableList.insertRow(-1);

            // ip number field
            cell = row.insertCell(-1);
            cell.appendChild( document.createTextNode(ip) );

            // mask field
            cell = row.insertCell(-1);
            cell.appendChild( document.createTextNode(mask) );

            // creates new link as tag variable is now not treated as link
            a = document.createElement("a");
            a.href = tag.getAttribute("href");
            a.onclick = new Function( tag.getAttribute("onclick") );

            // copies all children
            for(j = 0; j < tag.childNodes.length; j++)
            {
                if(tag.childNodes[j].nodeType == 3)
                {
                    a.appendChild( document.createTextNode(tag.childNodes[j].nodeValue) );
                    break;
                }
            }

            cell = row.insertCell(-1);
            cell.appendChild(a);
        }

        // everything's fine
        return false;
    }

    // calls IP ban deletion
    this.remove = function(IP, Mask)
    {
        // sets last action flag
        this.lastInsert = false;

        // executes AJAX request
        return this.request("GET", "IPBan", "remove", "ipban[ip]=" + IP +  "&ipban[mask]=" + Mask);
    }

    // calls IP baning
    this.insert = function()
    {
        // sets last action flag
        this.lastInsert = true;

        // executes AJAX request
        return this.request("POST", "IPBan", "insert", dumpForm(IPBanForm) );
    }
}

// IPBanAJAX default instance
pageIPBan = new IPBanAJAX();

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    IPBanForm = document.getElementById("ipbanForm");

    // insertion form template
    IPBanForm.onsubmit = new Function("return pageIPBan.insert();");
};
