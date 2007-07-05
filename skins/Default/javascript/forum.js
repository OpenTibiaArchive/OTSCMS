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
    Boards management AJAX wrapper.
*/

ForumAJAX.prototype = new XMLHttpRequest;

function ForumAJAX()
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

            case "delete":
                return this.onDelete();
                break;
        }

        // everything's fine
        return false;
    }

    // deletes forum
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastAction = "remove";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Forum", "remove", "id=" + ID);
    }

    // adds new board to list
    this.insert = function()
    {
        // sets last action flag
        this.lastAction = "insert";

        // executes AJAX request
        return this.request("POST", "Forum", "insert", dumpForm(BoardsForm) );
    }

    // loads board info
    this.edit = function(ID)
    {
        // sets last action flag
        this.lastAction = "edit";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Forum", "edit", "id=" + ID);
    }

    // updates existing board info
    this.update = function(ID)
    {
        // sets last action flag
        this.lastAction = "update";
        this.lastData = ID;

        // executes AJAX request
        return this.request("POST", "Forum", "update", "id=" + ID + "&" + dumpForm(BoardsFormEdit) );
    }

    // deletes thread
    this.Delete = function(ID)
    {
        // sets last action flag
        this.lastAction = "delete";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Topic", "remove", "id=" + ID);
    }

    // callback handlers

    this.onRemove = function()
    {
        // deletes poll from list
        deleteNode("boardID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onInsert = function()
    {
        tableList = document.getElementById("boardsTable").tBodies[0];

        // reads items list
        newBoard = this.root.getElementByTagName("list").getElementsByTagName("field");

        // finds new board
        for(i = 0; i < newBoard.length; i++)
        {
            ID = newBoard[i].getTagValue("id");

            // checks if given board exists already
            if( !document.getElementById("boardID_" + ID) )
            {
                // if no then we've found new ID
                Name = newBoard[i].getElementByTagName("name");
                break;
            }
        }

        row = tableList.insertRow(-1);

        // link to forum
        cell = row.insertCell(-1);

        // copies link from packet
        for(i = 0; i < Name.childNodes.length; i++)
        {
            cell.appendChild( document.importNode(Name.childNodes[i], true) );
        }

        // posts count
        cell = row.insertCell(-1);
        cell.appendChild( document.createTextNode("0") );

        // topics count
        cell = row.insertCell(-1);
        cell.appendChild( document.createTextNode("0") );

        // last post
        cell = row.insertCell(-1);
        cell.appendChild( document.createTextNode(Language[7]) );

        // board management
        cell = row.insertCell(-1);
        a = document.createElement("a");
        a.onclick = new Function("return pageForum.edit(" + ID + ");");
        a.href = "admin.php?module=Forum&command=edit&id=" + ID;
        a.appendChild( document.createTextNode(Language[1]) );
        cell.appendChild(a);

        cell.appendChild( document.createTextNode(" | ") );

        a = document.createElement("a");
        a.onclick = new Function("if( confirm(Language[0]) ) { return pageForum.remove(" + ID + "); } else { return false; }");
        a.href = "admin.php?module=Forum&command=remove&id=" + ID;
        a.appendChild( document.createTextNode(Language[2]) );
        cell.appendChild(a);

        BoardsForm.reset();

        // everything's fine
        return false;
    }

    this.onEdit = function()
    {
        // prepares edition form
        BoardsFormEdit.action = "admin.php?module=Forum&command=update&id=" + this.lastData;
        BoardsFormEdit.onsubmit = new Function("return pageForum.update(" + this.lastData + ");");

        oldBoard = this.root.getElementByTagName("data");

        // sets form fields values
        for(i = 0; i < BoardsFormEdit.elements.length; i++)
        {
            Element = BoardsFormEdit.elements[i];

            switch(Element.name)
            {
                case "board[name]":
                    Element.value = oldBoard.getTagValue("name");
                    break;

                case "board[content]":
                    Element.value = oldBoard.getTagValue("content");
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
        AJAXMain.appendChild(BoardsFormEdit);
        document.getElementById("ajaxWrapper").show("block");

        // everything's fine
        return false;
    }

    this.onUpdate = function()
    {
        // hides edit form and clears AJAX layer
        document.getElementById("ajaxWrapper").hide();
        deleteNode(BoardsFormEdit);

        // finds link on website
        Board = document.getElementById("boardID_" + this.lastData + "_a");

        // searches for new poll title
        for(i = 0; i < BoardsFormEdit.elements.length; i++)
        {
            Element = BoardsFormEdit.elements[i];

            switch(Element.name)
            {
                case "board[name]":
                    Name = Element.value;
                    break;

                case "board[content]":
                    Content = Element.value;
                    break;
            }
        }

        // updates link label on website
        A = Board.getElementByTagName("a");
        for(i = 0; i < A.childNodes.length; i++)
        {
            if(A.childNodes[i].nodeType == 3)
            {
                A.childNodes[i].nodeValue = Name;
                break;
            }
        }

        for(i = 0; i < Board.childNodes.length; i++)
        {
            if(Board.childNodes[i].nodeType == 3)
            {
                Board.childNodes[i].nodeValue = Content;
                break;
            }
        }

        // everything's fine
        return false;
    }

    this.onDelete = function()
    {
        // deletes topic row from table
        deleteNode("postID_" + this.lastData);

        // everything's fine
        return false;
    }
}

// ForumAJAX default instance
pageForum = new ForumAJAX();

// more javascript initializations
pageLoaded = function()
{
    // sets form variable
    BoardsForm = document.getElementById("forumForm");

    // edition form
    BoardsFormEdit = BoardsForm.cloneNode(true);
    BoardsFormEdit.id = "forumEdit";

    // edition form preparations
    for(i = 0; i < BoardsFormEdit.elements.length; i++)
    {
        if(BoardsFormEdit.elements[i].type == "submit")
        {
            BoardsFormEdit.elements[i].value = Language[3];
        }
    }
};
