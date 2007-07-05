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
    Poll voting and management AJAX wrapper.
*/

PollAJAX.prototype = new XMLHttpRequest;

function PollAJAX()
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
            case "vote":
                return this.onVote();
                break;

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

    // sends vote
    this.vote = function()
    {
        this.lastAction = "vote";

        // executes AJAX request
        return this.request("GET", "Poll", "vote", dumpForm(VoteForm) );
    }

    // deletes poll
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastAction = "remove";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Poll", "remove", "id=" + ID);
    }

    // adds new poll to list
    this.insert = function()
    {
        // sets last action flag
        this.lastAction = "insert";

        // executes AJAX request
        return this.request("POST", "Poll", "insert", dumpForm(PollsForm) );
    }

    // loads polls info
    this.edit = function(ID)
    {
        // sets last action flag
        this.lastAction = "edit";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Poll", "edit", "id=" + ID);
    }

    // updates existing poll
    this.update = function(ID)
    {
        // sets last action flag
        this.lastAction = "update";
        this.lastData = ID;

        // executes AJAX request
        return this.request("POST", "Poll", "update", "id=" + ID + "&" + dumpForm(PollsFormEdit) );
    }

    // callback handlers

    this.onVote = function()
    {
        // drops voting items
        deleteNode("pollSubmit");
        deleteNode(VoteForm);

        // loads options results
        newOptions = this.root.getElementByTagName("options").getElementsByTagName("field");

        for(i = 0; i < newOptions.length; i++)
        {
            newOption = newOptions[i];
            td = document.getElementById("pollOption_" + newOption.getTagValue("id") );

            // drops input box
            deleteNode( td.getElementByTagName("input") );

            // adds votes count
            td.appendChild( document.createTextNode( newOption.getTagValue("count") ) );
        }

        // appends table without voting form
        VoteRoot.appendChild(VoteTable);

        // everything's fine
        return false;
    }

    this.onRemove = function()
    {
        // deletes poll from list
        deleteNode("pollID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onInsert = function()
    {
        // searches for poll info
        newPoll = this.root.getElementByTagName("list").getElementsByTagName("field");

        // finds new poll
        for(i = 0; i < newPoll.length; i++)
        {
            ID = newPoll[i].getAttribute("name");

            // checks if given poll exists already
            if( !document.getElementById("pollID_" + ID) )
            {
                // if no then we've found new ID
                Name = newPoll[i].getAttribute("value");
                break;
            }
        }

        // creates new link on website
        newPoll = document.createElement("li");
        newPoll.id = "pollID_" + ID;

        a = document.createElement("a");
        a.href = "poll.php?command=display&id=" + ID;
        a.appendChild( document.createTextNode(Name) );
        newPoll.appendChild(a);

        // management links

        a = document.createElement("a");
        a.onclick = new Function("return pagePoll.edit(" + ID + ");");
        a.href = "admin.php?module=Poll&command=edit&id=" + ID;

        img = document.createElement("img");
        img.alt = Language[1];
        img.src = BaseHref + "images/edit.gif";

        a.appendChild(img);
        newPoll.appendChild(a);

        a = document.createElement("a");
        a.onclick = new Function("if( confirm(Language[0]) ) { return pagePoll.remove(" + ID + "); } else { return false; }");
        a.href = "admin.php?module=Poll&command=remove&id=" + ID;

        img = document.createElement("img");
        img.alt = Language[2];
        img.src = BaseHref + "images/delete.gif";

        a.appendChild(img);
        newPoll.appendChild(a);

        document.getElementById("pagePolls").appendChild(newPoll);

        // resets form
        PollsForm.reset();

        // everything's fine
        return false;
    }

    this.onEdit = function()
    {
        // prepares edition form
        PollsFormEdit.action = "admin.php?module=Poll&command=update&id=" + this.lastData;
        PollsFormEdit.onsubmit = new Function("return pagePoll.update(" + this.lastData + ");");

        oldPoll = this.root.getElementByTagName("data");

        // deletes options rows
        while(PollsFormTable.rows.length > 3)
        {
            PollsFormTable.deleteRow(3);
        }

        // sets form fields values
        for(i = 0; i < PollsFormEdit.elements.length; i++)
        {
            Element = PollsFormEdit.elements[i];

            switch(Element.name)
            {
                case "poll[name]":
                    Element.value = oldPoll.getTagValue("name");
                    break;

                case "poll[content]":
                    Element.value = oldPoll.getTagValue("content");
                    break;
            }
        }

        // adds option rows
        Options = this.root.getElementByTagName("options").getElementsByTagName("field");

        for(i = 0; i < Options.length; i++)
        {
            Option = Options[i];

            newRow = PollsFormTable.insertRow(-1);
            newRow.id = "optionID_" + Option.getAttribute("name");

            Input = document.createElement("input");
            Input.id = "optionValue_" + Option.getAttribute("name");
            Input.type = "text";
            Input.value = Option.getAttribute("value");

            newCell = newRow.insertCell(-1);
            newCell.appendChild(Input);

            optionsCell = newRow.insertCell(-1);

            Update = document.createElement("input");
            Update.type = "button";
            Update.value = Language[3];
            Update.onclick = new Function("return pageOptions.update(" + Option.getAttribute("name") + ");");
            optionsCell.appendChild(Update);

            Remove = document.createElement("input");
            Remove.type = "button";
            Remove.value = Language[2];
            Remove.onclick = new Function("if( confirm(Language[0]) ) { return pageOptions.remove(" + Option.getAttribute("name") + "); } else { return false; }");
            optionsCell.appendChild(Remove);
        }

        newRow = PollsFormTable.insertRow(-1);

        NewOptionField = document.createElement("input");
        NewOptionField.id = "optionNew";
        NewOptionField.type = "text";

        newCell = newRow.insertCell(-1);
        newCell.appendChild(NewOptionField);

        Insert = document.createElement("input");
        Insert.type = "button";
        Insert.value = Language[6];
        Insert.onclick = new Function("return pageOptions.insert(" + this.lastData + ");");
        optionsCell = newRow.insertCell(-1);
        optionsCell.appendChild(Insert);

        // clears form layer
        for(i = AJAXMain.childNodes.length - 1; i > -1; i--)
        {
            if(AJAXMain.childNodes[i].nodeType == 1 && AJAXMain.childNodes[i].id != "ajaxClose")
            {
                deleteNode(AJAXMain.childNodes[i]);
            }
        }

        // makes form layer visible
        AJAXMain.appendChild(PollsFormEdit);
        document.getElementById("ajaxWrapper").show("block");

        // everything's fine
        return false;
    }

    this.onUpdate = function()
    {
        // hides edit form and clears AJAX layer
        document.getElementById("ajaxWrapper").hide();
        deleteNode(PollsFormEdit);

        // finds link on website
        Poll = document.getElementById("pollID_" + this.lastData + "_a");

        // searches for new poll title
        for(i = 0; i < PollsFormEdit.elements.length; i++)
        {
            Element = PollsFormEdit.elements[i];

            // 've it
            if(Element.name == "poll[name]")
            {
                Name = Element.value;
                break;
            }
        }

        // updates link label on website
        for(i = 0; i < Poll.childNodes.length; i++)
        {
            if(Poll.childNodes[i].nodeType == 3)
            {
                Poll.childNodes[i].nodeValue = Name;
                break;
            }
        }

        // everything's fine
        return false;
    }
}

// PollAJAX default instance
pagePoll = new PollAJAX();

/*
    Poll options management AJAX wrapper.
*/

OptionsAJAX.prototype = new XMLHttpRequest;

function OptionsAJAX()
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

            case "update":
                return this.onUpdate();
                break;
        }

        // everything's fine
        return false;
    }

    // deletes option from poll
    this.remove = function(ID)
    {
        // sets last action flag
        this.lastAction = "remove";
        this.lastData = ID;

        // executes AJAX request
        return this.request("GET", "Options", "remove", "id=" + ID);
    }

    // adds new option to poll
    this.insert = function(ID)
    {
        // sets last action flag
        this.lastAction = "insert";

        // executes AJAX request
        return this.request("POST", "Options", "insert", "option[poll]=" + ID + "&option[name]=" + NewOptionField.value);
    }

    // updates existing option
    this.update = function(ID)
    {
        // sets last action flag
        this.lastAction = "update";
        this.lastData = ID;

        // executes AJAX request
        return this.request("POST", "Options", "update", "id=" + ID + "&option[name]=" + document.getElementById("optionValue_" + this.lastData).value );
    }

    // callback handlers

    this.onRemove = function()
    {
        // deletes link from website
        deleteNode("optionID_" + this.lastData);

        // everything's fine
        return false;
    }

    this.onInsert = function()
    {
        // clears form for further use
        NewOptionField.value = "";

        // searches for option info
        newOption = this.root.getElementByTagName("options").getElementsByTagName("field");

        // finds new option
        for(i = 0; i < newOption.length; i++)
        {
            ID = newOption[i].getAttribute("name");

            // checks if given option exists already
            if( !document.getElementById("optionID_" + ID) )
            {
                // if no then we've found new ID
                Name = newOption[i].getAttribute("value");
                break;
            }
        }

        // creates new option row in table
        newOption = PollsFormTable.insertRow(PollsFormTable.rows.length - 1);
        newOption.id = "optionID_" + ID;

        // edition and controll buttons cells
        Input = document.createElement("input");
        Input.type = "text";
        Input.id = "optionValue_" + ID;
        Input.value = Name;
        editCell = newOption.insertCell(-1);
        editCell.appendChild(Input);

        optionsCell = newOption.insertCell(-1);

        Update = document.createElement("input");
        Update.type = "button";
        Update.value = Language[3];
        Update.onclick = new Function("return pageOptions.update(" + ID + ");");
        optionsCell.appendChild(Update);

        Remove = document.createElement("input");
        Remove.type = "button";
        Remove.value = Language[2];
        Remove.onclick = new Function("if( confirm(Language[0]) ) { return pageOptions.remove(" + ID + "); } else { return false; }");
        optionsCell.appendChild(Remove);

        // everything's fine
        return false;
    }

    this.onUpdate = function()
    {
        // just to notice that update is done
        alert(Language[5]);

        // everything's fine
        return false;
    }
}

// OptionsAJAX default instance
pageOptions = new OptionsAJAX();

// more javascript initializations
pageLoaded = function()
{
    // voting page references
    VoteRoot = document.getElementById("pollRoot");

    if(VoteRoot)
    {
        VoteForm = document.getElementById("pollForm");
        VoteTable = document.getElementById("pollTable");

        // voting handler
        if(VoteForm)
        {
            VoteForm.onsubmit = new Function("return pagePoll.vote();");
        }
    }

    // sets form variable
    PollsForm = document.getElementById("pollForm");

    // insertion form template
    if(PollsForm)
    {
        PollsForm.onsubmit = new Function("return pagePoll.insert();");

        PollsFormEdit = PollsForm.cloneNode(true);
        PollsFormEdit.id = "pollEdit";
        PollsFormTable = PollsFormEdit.getElementByTagName("table").tBodies[0];

        // submit button text
        for(i = 0; i < PollsFormEdit.elements.length; i++)
        {
            if(PollsFormEdit.elements[i].type == "submit")
            {
                PollsFormEdit.elements[i].value = Language[3];
                break;
            }
        }
    }
};
