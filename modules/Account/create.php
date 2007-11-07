<?php
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

$email = InputData::read('email');

// validates e-mail
if( !preg_match('/^[a-z][\w\.+-]*[a-z0-9]@[a-z0-9][\w\.+-]*\.[a-z][a-z\.]*[a-z]$/i', $email) )
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Account.PleaseEMail'];
    OTSCMS::call('Account', 'signup');
    return;
}

$account = $ots->createObject('Account');

// checks if this e-mail was already used
$account->find($email);
if( $account->isLoaded() )
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Account.AlreadyUsed'];
    OTSCMS::call('Account', 'signup');
    return;
}

// generates random account number
try
{
    $number = $account->create($config['system.min_number'], $config['system.max_number']);
}
catch(Exception $e)
{
    // no free numbers
    if( $e->getMessage() == 'No free account number are available.')
    {
        throw new HandledException('OutOfNumbers');
    }
    // we don't know what is it at the moment
    else
    {
        throw $e;
    }
}

// generates random password
$password = substr( md5( uniqid( rand(), true) ), 1, 8);

// sets all info
$account->unblock();
$account->setPassword($config['system.use_md5'] ? md5($password) : $password);
$account->setEMail($email);
$account->save();

$root = XMLToolbox::createDocumentFragment();
$span = XMLToolbox::createElement('span');
$span->setAttribute('class', 'accountNumber');
$span->addContent($number);
$root->addContents($language['Modules.Account.Created_Number'] . ': ', $span);

// created account number info
$message = $template->createComponent('Message');
$message['message'] = $root;

// check if administrator enabled sending mail
if($config['system.use_mail'])
{
    // tries to send mail with password
    try
    {
        Mail::send($email, $language['Modules.Account.SignupMail_Title'], $language['Modules.Account.SignupMail_Content'] . ': '.$password);
        $message['place'] = $language['Modules.Account.SignupMail_Sent'];
    }
    // if failed then tell user about it
    catch(MailException $error)
    {
        $message['place'] = $language['Modules.Account.SignupMail_Error'];
    }
}
// else jsut display password
else
{
    $root = XMLToolbox::createDocumentFragment();
    $span = XMLToolbox::createElement('span');
    $span->setAttribute('class', 'accountNumber');
    $span->addContent($password);
    $root->addContents($language['Modules.Account.SignupMail_Content'] . ': ', $span);
    $message['place'] = $root;
}

?>
