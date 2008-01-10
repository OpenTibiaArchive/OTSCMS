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
    OTSCMS XMLHttpRequest extensions
*/

XMLHttpRequest.prototype.root = false;

// requests executer
XMLHttpRequest.prototype.request = function(requestMethod, requestModule, requestCommand, requestData)
{
    try
    {
        // prepares URL
        if(requestMethod == "GET" && requestData)
        {
            requestCommand += "&" + requestData;
            requestData = null;
        }

        // opens connection
        this.open(requestMethod, "ajax.php?module=" + requestModule + "&command=" + requestCommand, false);

        // POST method header
        if(requestMethod == "POST")
        {
            this.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        }

        // executes query
        this.send(requestData);

        // standard callback way
        if(this.status == 200)
        {
            this.root = this.responseXML.documentElement;

            if( this.root.getAttribute("module") == "error")
            {
                // HandledException wrapper
                message = this.root.getElementsByTagName("message");

                // if it wasnt HandledException error
                if(message.length == 0)
                {
                    return true;
                }

                alert( message[0].getAttribute("value") + "\n\n" + this.root.getTagValue("debug") );
                return false;
            }

            // handles message
            return this.callback();
        }
    }
    catch(e)
    {
        return true;
    }
};

// callback handler
XMLHttpRequest.prototype.callback = function()
{
};

// dumps form fields as HTTP packet
function dumpForm(formObject)
{
    // form fields
    formElements = formObject.elements;
    packet = "";

    // appending all form fields
    for(i = 0; i < formElements.length; i++)
    {
        // checkboxes and radios are appended only if are checked
        if(formElements[i].name.length > 0 && ((formElements[i].type != "checkbox" && formElements[i].type != "radio") || formElements[i].checked))
        {
            if(i > 0)
            {
                // apresant as separator
                packet += "&";
            }

            // escapes HTTP-nonsuitable characters to not break packet
            packet += formElements[i].name + "=" + escape(formElements[i].value);
        }
    }

    return packet;
}
