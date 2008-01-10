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
    AJAX statistics browser.
*/

StatisticsAJAX.prototype = new XMLHttpRequest;

function StatisticsAJAX()
{
    // last action
    this.lastInsert = false;

    // recreates bans list
    this.callback = function()
    {
        // clears list - leaves headers row
        while(StatisticsTable.rows.length > 1)
        {
            StatisticsTable.deleteRow(1);
        }

        // reads items list
        newList = this.root.getElementByTagName("scores").getElementsByTagName("field");

        // reloads list
        for(i = 0; i < newList.length; i++)
        {
            // loads item
            item = newList[i];
            Rank = item.getAttribute("name");
            Name = item.getTagValue("name");
            Value = item.getTagValue("value");

            // only in experience statistics
            Level = item.getElementByTagName("level");
            Level = Level ? Level.getAttribute("value") : false;

            row = StatisticsTable.insertRow(-1);

            // rank field
            cell = row.insertCell(-1);
            cell.appendChild( document.createTextNode(Rank) );

            // character link
            a = document.createElement("a");
            a.href = "/characters/" + Name;
            a.appendChild( document.createTextNode(Name) );

            // character name
            cell = row.insertCell(-1);
            cell.appendChild(a);

            // points field
            cell = row.insertCell(-1);
            cell.appendChild( document.createTextNode(Value) );

            // level field
            if(Level)
            {
                cell = row.insertCell(-1);
                cell.appendChild( document.createTextNode(Level) );
            }

            // updates links
            Page = this.root.getTagValue("page");
            List = this.root.getTagValue("list");

            Left = this.root.getElementByTagName("left");

            // checks if this link should be displayed
            if( Left.getTagValue("show") )
            {
                old = LinkLeft.getElementsByTagName("a");

                // if link doesn't exists then create new one
                if(old.length > 0)
                {
                    a = old.item(0);
                }
                else
                {
                    a = document.createElement("a");
                    LinkLeft.appendChild(a);
                }

                while(a.childNodes.length > 0)
                {
                    deleteNode(a.childNodes[0]);
                }

                // prepares new link
                a.href = "/statistics/" + List + "/page" + (Page - 1);
                a.onclick = new Function("return pageStatistics.move(\"" + List + "\", " + (Page - 1) + ");");
                a.appendChild( document.createTextNode(Language[4] + " " + Left.getTagValue("from") + " - " + Left.getTagValue("to") ) );
            }
            else
            {
                old = LinkLeft.getElementsByTagName("a");

                // if it exists, delete it as it shouldn't be displayed
                if(old.length > 0)
                {
                    deleteNode( old.item(0) );
                }
            }

            Right = this.root.getElementByTagName("right");

            // checks if this link should be displayed
            if( Right.getTagValue("show") )
            {
                old = LinkRight.getElementsByTagName("a");

                // if link doesn't exists then create new one
                if(old.length > 0)
                {
                    a = old.item(0);
                }
                else
                {
                    a = document.createElement("a");
                    LinkRight.appendChild(a);
                }

                while(a.childNodes.length > 0)
                {
                    deleteNode(a.childNodes[0]);
                }

                // prepares new link
                a.href = "/statistics/" + List + "/page" + (Page + 1);
                a.onclick = new Function("return pageStatistics.move(\"" + List + "\", " + (Page + 1) + ");");
                a.appendChild( document.createTextNode(Language[4] + " " + Right.getTagValue("from") + " - " + Right.getTagValue("to") ) );
            }
            else
            {
                old = RightLeft.getElementsByTagName("a");

                // if it exists, delete it as it shouldn't be displayed
                if(old.length > 0)
                {
                    deleteNode( old.item(0) );
                }
            }
        }

        // everything's fine
        return false;
    }

    // calls IP ban deletion
    this.move = function(list, page)
    {
        // executes AJAX request
        return this.request("GET", "Statistics", "highscores", "list=" + list + "&page=" + page);
    }
}

// StatisticsAJAX default instance
pageStatistics = new StatisticsAJAX();

// more javascript initializations
pageLoaded = function()
{
    // sets pager variable
    StatisticsTable = document.getElementById("statisticsPager").tBodies[0];
    LinkLeft = document.getElementById("pagerLeft");
    LinkRight = document.getElementById("pagerRight");
};
