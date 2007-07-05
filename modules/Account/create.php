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

// checks if this e-mail was already used
$used = $db->prepare('SELECT COUNT(`id`) AS `count` FROM {accounts} WHERE `email` = :email');
$used->execute( array(':email' => $email) );
$used = $used->fetch();
if($used['count'])
{
    $message = $template->createComponent('Message');
    $message['message'] = $language['Modules.Account.AlreadyUsed'];
    OTSCMS::call('Account', 'signup');
    return;
}

$min = $config['system.min_number'];
$max = $config['system.max_number'];

// generates random account number
$random = rand($min, $max);
$number = $random;

// reads already existing accounts
$exist = Toolbox::dumpRecords( $db->query('SELECT `id` AS `key`, 1 AS `value` FROM {accounts}') );

// finds unused number
while(true)
{
    // unused - found
    if( !isset($exist[$number]) )
    {
        break;
    }

    // used - next one
    $number++;

    // we need to re-set
    if($number > $max)
    {
        $number = $min;
    }

    // we checked all possibilities
    if($number == $random)
    {
        throw new HandledException('OutOfNumbers');
    }
}

// generates random password
$password = substr( md5( uniqid( rand(), true) ), 1, 8);

// creates new account
$account = new OTS_Account();
$account->create($number);

// sets all info
$account['password'] = $config['system.use_md5'] ? md5($password) : $password;
$account['email'] = $email;
$account['blocked'] = 0;
$account['premdays'] = 0;
$account['signature'] = '';
$account['website'] = '';
$account['avatar'] = '';
$account->save();

$root = XMLToolbox::createDocumentFragment();
$span = XMLToolbox::createElement('span');
$span->setAttribute('class', 'accountNumber');
$span->nodeValue = $number;
$root->appendChild( XMLToolbox::createTextNode($language['Modules.Account.Created_Number'] . ': ') );
$root->appendChild($span);

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
    $span->nodeValue = $password;
    $root->appendChild( XMLToolbox::createTextNode($language['Modules.Account.SignupMail_Content'] . ': ') );
    $root->appendChild($span);
    $message['place'] = $root;
}

?>
