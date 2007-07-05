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

/*
    Class for sending emails that allows using user authentication over SMTP.
*/

class Mail
{
    // connection settings
    private static $from;
    private static $host;
    private static $port;
    private static $useAuth;
    private static $user;
    private static $password;

    // sets connection handling settings
    public static function init($from, $smtpConfig, $useAuth)
    {
        self::$from = $from;
        self::$host = $smtpConfig['host'];
        self::$port = $smtpConfig['port'];
        self::$useAuth = $useAuth;
        self::$user = $smtpConfig['user'];
        self::$password = $smtpConfig['password'];
    }

    // sends e-mail
    public static function send($to, $subject, $content)
    {
        $config = OTSCMS::getResource('Config');

        // composes message headers
        $header = 'Date: ' . date('r') . "\r\n";
        $header .= 'To: ' . $to . "\r\n";
        $header .= 'Subject: ' . $subject . "\r\n";
        $header .= 'Return-Path: ' . trim(self::$from) . "\r\n";
        $header .= 'From: ' . self::$from . "\r\n";
        $header .= 'Message-ID: <' . md5( uniqid( time() ) ) . '@' . $_SERVER['SERVER_NAME'] . '>' . "\r\n";
        $header .= 'X-Mailer: OTSCMS/' . $config['version'] . "\r\n";
        $header .= 'MIME-Version: 1.0' . "\r\n";
        $header .= 'Content-Transfer-Encoding: 8bit' . "\r\n";
        $header .= 'Content-Type: text/plain; charset="utf-8"' . "\r\n";

        // message content that should fit email line limit
        $content = str_replace("\n", "\r\n", str_replace("\r", "\n", str_replace("\r\n", "\n", wordwrap($content, 70) ) ) );

        // appends empty line at the end if there isnt already
        if( substr($content, -2) != "\r\n")
        {
            $content .= "\r\n";
        }

        // connects to server with 10 seconds timeout
        $socket = fsockopen(self::$host, self::$port, $errno, $error, 10);

        // checks connection
        if( empty($socket) )
        {
            throw new MailException($error, $errno);
        }

        // skips welcome message
        self::read($socket);

        // timeout for socket operation
        stream_set_timeout($socket, 10, 0);

        // sends EHLO before HELO (RFC 2821)
        try
        {
            fputs($socket, 'EHLO ' . $_SERVER['SERVER_NAME'] . "\r\n");

            $reply = self::read($socket);
            $code = substr($reply, 0, 3);

            if($code != 250)
            {
                throw new MailException( substr($reply, 4), $code);
            }
        }
        catch(MailException $error)
        {
            fputs($socket, 'HELO ' . $_SERVER['SERVER_NAME'] . "\r\n");

            $reply = self::read($socket);
            $code = substr($reply, 0, 3);

            if($code != 250) {
                throw new MailException( substr($reply, 4), $code);
            }
        }

        try
        {
            // authentication required
            if(self::$useAuth)
            {
                fputs($socket,'AUTH LOGIN' . "\r\n");

                $reply = self::read($socket);
                $code = substr($reply, 0, 3);

                if($code != 334)
                {
                    throw new MailException( substr($reply, 4), $code);
                }

                // user login
                fputs($socket, base64_encode(self::$user) . "\r\n");

                $reply = self::read($socket);
                $code = substr($reply, 0, 3);

                if($code != 334)
                {
                    throw new MailException( substr($reply, 4), $code);
                }

                // password to confirm
                fputs($socket, base64_encode(self::$password) . "\r\n");

                $reply = self::read($socket);
                $code = substr($reply, 0, 3);

                if($code != 235)
                {
                    throw new MailException( substr($reply, 4), $code);
                }
            }

            // starts new mail
            fputs($socket, 'MAIL FROM:<' . self::$from . '>' . "\r\n");

            $reply = self::read($socket);
            $code = substr($reply, 0, 3);

            if($code != 250)
            {
                throw new MailException( substr($reply, 4), $code);
            }

            // adds recipient
            fputs($socket, 'RCPT TO:<' . $to . '>' . "\r\n");

            $reply = self::read($socket);
            $code = substr($reply, 0, 3);

            if($code != 250 && $code != 251)
            {
                throw new MailException( substr($reply, 4), $code);
            }

            // initializes data transfer
            fputs($socket, 'DATA' . "\r\n");

            $reply = self::read($socket);
            $code = substr($reply, 0, 3);

            if($code != 354)
            {
                throw new MailException( substr($reply, 4), $code);
            }

            // splits data into single lines
            $lines = explode("\n", str_replace("\r", "\n", str_replace("\r\n", "\n", $header . "\r\n" . "\r\n" . $content) ) );

            $field = substr($lines[0], 0, strpos($lines[0], ':') );
            $inside = false;
            if(! ( empty($field) || strstr($field, ' ') ))
            {
                $inside = true;
            }

            // truncates each line to it's maximum length
            foreach($lines as $line)
            {
                $output = array();
                if( empty($line) && $inside)
                {
                    $inside = false;
                }

                // checks if line is longer then maximum possible
                while(strlen($line) > 998)
                {
                    // finds first space that would be used to split words
                    $pos = strrpos( substr($line, 0, 998), ' ');

                    // if not found then need to use entire line and split a word
                    if(!$pos)
                    {
                        $pos = 997;
                    }

                    // adds line to buffer and shortents current one
                    $output[] = substr($line, 0, $pos);
                    $line = substr($line, $pos + 1);

                    // we need to add TAB to make header continued
                    if($inside)
                    {
                        $line = "\t" . $line;
                    }
                }

                $output[] = $line;

                // sends buffer to server
                foreach($output as $line)
                {
                    // dot in SMTP is used to indicates data buffer end so we need to escape it
                    if( substr($line, 0, 1) == '.')
                    {
                        $line = '.' . $line;
                    }

                    // sends single line
                    fputs($socket, $line . "\r\n");
                }
            }

            // sends data end marker - empty line with only dot
            fputs($socket, "\r\n" . '.' . "\r\n");

            $reply = self::read($socket);
            $code = substr($reply, 0, 3);

            if($code != 250)
            {
                throw new MailException( substr($reply, 4), $code);
            }
        }
        // resets transfer on failure and closes connection
        catch(MailException $error)
        {
            fputs($socket, 'RSET' . "\r\n");
            fputs($socket, 'QUIT' . "\r\n");
            fclose($socket);
            throw $error;
        }

        // closes transaction and connection
        fputs($socket, 'QUIT' . "\r\n");
        fclose($socket);
    }

    // reads socket buffer
    private static function read(&$socket)
    {
        $data = '';

        // reads single line
        while($temp = fgets($socket) )
        {
            $data .= $temp;

            // space tells that this is last line
            if($temp[3] == ' ')
            {
                return $data;
            }
        }
    }
}

// startup initialization
$config = OTSCMS::getResource('Config');
$mail = $config['mail'];
Mail::init($mail['from'], $mail['smtp'], $mail['smtp']['use_auth']);

?>
