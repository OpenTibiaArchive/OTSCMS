<?php
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

// loads HTTP data in correct order
$account = new OTS_Account( (int) InputData::read('number') );

$message = $template->createComponent('Message');

// checks if this e-mail was used for given account
if($account->eMail != trim( InputData::read('email') ) )
{
    $message['message'] = $language['Modules.Account.RemindMail_MissMatch'];
}
else
{
    // we need to re-create MD5 and SHA1 password
    switch($config['system.passwords'])
    {
        case 'md5':
            $password = substr( md5( uniqid( rand(), true) ), 1, 8);
            $account->password = md5($password);
            $account->save();
            break;

        case 'sha1':
            $password = substr( md5( uniqid( rand(), true) ), 1, 8);
            $account->password = sha1($password);
            $account->save();
            break;

        case 'plain':
            $password = $account->password;
            break;
    }

    // sends password
    if($config['system.use_mail'])
    {
        try
        {
            Mail::send($account->eMail, $language['Modules.Account.RemindMail_Title'], $language['Modules.Account.SignupMail_Content'] . ': '.$password);
            $message['place'] = $language['Modules.Account.SignupMail_Sent'];
        }
        // if failed then tell user about it
        catch(MailException $error)
        {
            $message['place'] = $language['Modules.Account.SignupMail_Error'];
        }
    }
    // displays it
    else
    {
        $root = XMLToolbox::createDocumentFragment();
        $span = XMLToolbox::createElement('span');
        $span->setAttribute('class', 'accountNumber');
        $span->addContent($password);
        $root->addContents($language['Modules.Account.SignupMail_Content'] . ': ', $span);
        $message['place'] = $root;
    }
}

?>
