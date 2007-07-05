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

var userAgent = navigator.userAgent.toLowerCase();
var is_ie = userAgent.indexOf("msie") != -1 && userAgent.indexOf("opera") == -1 && userAgent.indexOf("safari") == -1 && navigator.vendor != "Apple Computer, Inc." && userAgent.indexOf("webtv") == -1;

var text = "";

var AddTxt = "";

function getActiveText()
{
    BBEditor.focus();
    if (!is_ie || (is_ie && !document.selection))
    {
        return false;
    }

    var sel = document.selection;
    var rng = sel.createRange();
    rng.colapse;
    if (rng != null && sel.type == "text")
    {
        text = rng.text;
    }
    if (BBEditor.createTextRange)
    {
        BBEditor.caretPos = rng.duplicate();
    }
    return true;
}

function AddText(NewCode)
{
    if (typeof(BBEditor.createTextRange) != "undefined" && BBEditor.caretPos)
    {
        var caretPos = BBEditor.caretPos;
        caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == " " ? NewCode + " " : NewCode;
    }
    else
    {
        BBEditor.value += NewCode;
    }
    BBEditor.focus();
    getActiveText();
    AddTxt = "";
}

function bbcode(bbcode, optionvalue)
{
    if (typeof(BBEditor) != "object" && typeof(must_click_content) != "undefined")
    {
        alert(must_click_content);
        return false;
    }

    if (optionvalue)
    {
        optioncompiled = "=" + optionvalue;
    }
    else
    {
        optioncompiled = "";
    }

    getActiveText();

    if (text)
    {
        if (text.substring(0, bbcode.length + 2 ) == "[" + bbcode + "]" && text.substring(text.length - bbcode.length - 3, text.length) == "[/" + bbcode + "]")
        {
            AddTxt = text.substring(bbcode.length + 2, text.length - bbcode.length - 3);
        }
        else
        {
            AddTxt = "[" + bbcode + optioncompiled + "]" + text + "[/" + bbcode + "]";
        }
        AddText(AddTxt);
    }
    else if (BBEditor.selectionEnd && (BBEditor.selectionEnd - BBEditor.selectionStart > 0))
    {
        var start_selection = BBEditor.selectionStart;
        var end_selection = BBEditor.selectionEnd;
        if (end_selection <= 2)
        {
            end_selection = BBEditor.textLength;
        }

        var start = BBEditor.value.substring(0, start_selection);
        var middle = BBEditor.value.substring(start_selection, end_selection);
        var end = BBEditor.value.substring(end_selection, BBEditor.textLength);

        if (middle.substring(0, bbcode.length + 2 ) == "[" + bbcode + "]" && middle.substring(middle.length - bbcode.length - 3, middle.length) == "[/" + bbcode + "]")
        {
            middle = middle.substring(bbcode.length + 2, middle.length - bbcode.length - 3);
        }
        else
        {
            middle = "[" + bbcode + optioncompiled + "]" + middle + "[/" + bbcode + "]";
        }

        BBEditor.value = start + middle + end;
    }
    else
    {
            var inserttext = prompt(Language[8] + "\n[" + bbcode + "]xxx[/" + bbcode + "]", '');
            if ( inserttext != null && inserttext.trim() )
            {
                AddTxt = "[" + bbcode + optioncompiled + "]" + inserttext + "[/" + bbcode + "] ";
            }
            AddText(AddTxt);
    }
    BBEditor.focus();
}

function fontformat(thevalue, thetype)
{
    getActiveText();

    if (text)
    {
        AddTxt = "[" + thetype + "=" + thevalue + "]" + text + "[/" + thetype + "]";
        AddText(AddTxt);
    }
    else if (BBEditor.selectionEnd && (BBEditor.selectionEnd - BBEditor.selectionStart > 0))
    {
        var start_selection = BBEditor.selectionStart;
        var end_selection = BBEditor.selectionEnd;
        if (end_selection <= 2)
        {
            end_selection = BBEditor.textLength;
        }

        var start = BBEditor.value.substring(0, start_selection);
        var middle = BBEditor.value.substring(start_selection, end_selection);
        var end = BBEditor.value.substring(end_selection, BBEditor.textLength);

        middle = "[" + thetype + "=" + thevalue + "]" + middle + "[/" + thetype + "]";

        BBEditor.value = start + middle + end;
    }
    else
    {
        AddText("[" + thetype + "=" + thevalue + "][/" + thetype + "]");
    }

    document.getElementById("fontselect").selectedIndex = 0;
    document.getElementById("sizeselect").selectedIndex = 0;
    document.getElementById("colorselect").selectedIndex = 0;
    BBEditor.focus();
    return false;
}

function namedlink(thetype)
{
    var extraspace = "";

    getActiveText();
    var dtext = "";
    if (text)
    {
        dtext = text;
    }
    else
    {
        extraspace = " ";
    }
    linktext = prompt(Language[9], dtext);
    var prompttext, prompt_contents;
    if (thetype == "URL")
    {
        prompt_text = Language[10];
        prompt_contents = "http://";
    }
    else
    {
        prompt_text = Language[11];
        prompt_contents = "";
    }
    var linkurl = prompt(prompt_text, prompt_contents);
    if (listentry != null && listentry.trim() )
    {
        if ((linktext != null) && (linktext != ""))
        {
            AddTxt = "[" + thetype + "=" + linkurl + "]" + linktext + "[/" + thetype + "]" + extraspace;
        }
        else
        {
            AddTxt = "[" + thetype + "=" + linkurl + "]" + linkurl + "[/" + thetype + "]" + extraspace;
        }
        AddText(AddTxt);
    }
}

function dolist(type)
{
    if(type)
    {
        var listtype = prompt(Language[12], "");
    }

    if(type)
    {
        if (listtype == "a" || listtype == "A" || listtype == "1" || listtype == "I" || listtype == "i")
        {
            thelist = "[list=" + listtype + "]\n";
        }
        else
        {
            thelist = "[list:o]\n";
        }
    }
    else
    {
        thelist = "[list:u]\n";
    }

    do
    {
        listentry = prompt(Language[13], "");
        if (listentry != null && listentry.trim() )
        {
            thelist = thelist + "[*]" + listentry + "\n";
        }
    }
    while (listentry != null && listentry.trim() );

    if(type)
    {
        AddTxt = thelist + "[/list:o]";
    }
    else
    {
        AddTxt = thelist + "[/list:u]";
    }
    if (!text)
    {
        AddTxt = AddTxt + " ";
    }
    AddText(AddTxt);
}

var linkurl = "http://";

// loads bbeditor form
pageLoaded = function()
{
    BBEditor = document.getElementById("bbeditor");

    // finds content field
    for(i = 0; i < BBEditor.elements.length; i++)
    {
        if(BBEditor.elements[i].name == "bb[content]")
        {
            BBEditor = BBEditor.elements[i];
            break;
        }
    }
}
