<?php

// Der Username wird in der $_SESSION['user'] Variable gespeichert. Es muss der passende $_SESSION['pass']
// eintrag vorhanden sein, der jedesmal beim initialisieren der Hauptklasse überprüft wird.
// Die Uservariable wird gelöscht wenn die gespeicherte md5 Summe des Passwortes in der Sessionvariable nicht
// zum User passt.

$module_name = 'user';

class user
{
    var $logged_in = FALSE; // der default Status beim initialisieren

    function check_login()
    {
        if (!empty($_SESSION['user']))
        {
            $sql = "SELECT user_pass FROM jarty_user WHERE user_nick='".$nick."'";
            $res = mysql_query($sql);

            if (@mysql_num_rows($res) == 1)
            {
                $row = mysql_fetch_row($res);
                if ($row[0] == $_SESSION['pass'])
                { $this->logged_in = TRUE; }
                else
                { unset($_SESSION['nick']); }
            }
            else
            { unset ($_SESSION['nick']); }
        }
    }

    function do_login($nick,$pass)
    {
        if (!empty($nick) && !empty($pass))
        {
            $nick = strtolower($nick);
            $nick = mysql_escape_string($nick);
            $pass = md5($pass);

            $sql  = "SELECT user_pass FROM jarty_user WHERE user_nick='".$nick."'";
            $res  = mysql_query($sql);

            if (@mysql_num_rows($res) == 1)
            {
                $row  = mysql_fetch_row($res);
                if ($row[0] == $pass)
                {
                    $_SESSION['nick'] = $nick;
                    $_SESSION['pass'] = $pass;
                    $this->login_state = 3; // login erfolgreich
                    $this->check_login();
                }
                else
                { $this->login_state = 2; } // falsches Passwort
            }
            else
            { $this->login_state = 1; } // falscher User
        }
        else
        { $this->login_state = 0; } // Entweder Passwort oder User leer
    }
}
?>