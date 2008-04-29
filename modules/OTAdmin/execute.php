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

// loads server
$otadmin = new CMS_OTAdmin( (int) InputData::read('id') );

// backs up current log
$log = Session::read('log');

OTSCMS::call('OTAdmin', 'panel');

// server command signature
$log .= $otadmin['name'] . '@' . $otadmin['content'] . ':' . $otadmin['port'] . '$ ';

// executes command
try
{
    // connects to server
    $admin = new OTS_Admin($otadmin['content'], $otadmin['port']);

    // logs in
    if($admin->requiresLogin)
    {
        $admin->login($otadmin['password']);
    }

    try
    {
        $method = InputData::read('method');

        switch($method)
        {
            // special ping command
            case 'ping':
                $log .= 'ping() > ';
                $log .= $admin->ping();
                break;

            case OTS_Admin::COMMAND_BROADCAST:
                $param = InputData::read('param');
                $log .= 'broadcast(' . $param . ') > ';
                $admin->broadcast($param);
                $log .= $language['Modules.OTAdmin.StatOK'];
                break;

            case OTS_Admin::COMMAND_CLOSE_SERVER:
                $log .= 'close() > ';
                $admin->close();
                $log .= $language['Modules.OTAdmin.StatOK'];
                break;

            case OTS_Admin::COMMAND_PAY_HOUSES:
                $log .= 'payHouses() > ';
                $admin->payHouses();
                $log .= $language['Modules.OTAdmin.StatOK'];
                break;

            case OTS_Admin::COMMAND_SHUTDOWN_SERVER:
                $log .= 'shutdown() > ';
                $admin->shutdown();
                $log .= $language['Modules.OTAdmin.StatOK'];
                break;

            case OTS_Admin::COMMAND_KICK:
                $param = InputData::read('param');
                $log .= 'kick(' . $param . ') > ';
                $admin->kick($param);
                $log .= $language['Modules.OTAdmin.StatOK'];
                break;

            default:
                $log .= $language['Modules.OTAdmin.UnknownCommadn'] . ': ' . $method;
        }
    }
    catch(E_OTS_OutOfBuffer $error)
    {
        $log .= $language['Modules.OTAdmin.StatFail'] . ': E_OTS_OutOfBuffer';
    }
    catch(E_OTS_ErrorCode $error)
    {
        $log .= $language['Modules.OTAdmin.StatFail'] . ': ' . $error->getCode();
    }
}
catch(E_OTS_OutOfBuffer $error)
{
    $log .= $language['Modules.Online.offline'];
}
catch(E_OTS_ErrorCode $error)
{
    $log .= $language['Modules.OTAdmin.StatLoginFail'];
}

// puts new log
Session::write('log', $log . "\n");

?>
