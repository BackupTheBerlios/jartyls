<?php
define ('DB_HOST','localhost'); // the hostname of the MySQL DB server
define ('DB_USER','music'); // the username for the MySQL DB server
define ('DB_PASS','b9yu7g'); // the password for the MySQL DB server
define ('DB_NAME','music_server'); // the database name

session_start();

class lanpage
{
    function lanpage ($mdir = './')
    {
        // ein paar Verzeichnisse. Das Modulverzeichnis ist sehr wichtig, deshalb wird abgebrochen wenn das bei der Initialisierung
        // angegebene Verzeichnis nicht existiert
        $this->working_dir = getcwd() . '/';
        if (is_dir($this->working_dir.$mdir))
        { $this->module_dir = $this->working_dir . $mdir . '/'; }
        else
        { die ("<b>Fatal Error:</b> Can't open module directory (".$this->working_dir . $mdir."). Exiting ..."); }

        // Was wären wir ohne MySQL? ;)
        $this->db_con = @mysql_connect(DB_HOST,DB_USER,DB_PASS);
        if (empty($this->db_con))
        { die ("<b>Fatal Error:</b> Can't connect to MySQL Database Server. Exiting ..."); }

        $this->db_sdb = @mysql_select_db(DB_NAME);
        if (empty($this->db_sdb))
        { die ("<b>Fatal Error:</b> Can't select Database. Exiting ..."); }

        // das User Modul wird auf jeder Seite benötigt
        $this->load_module('user');
        $this->user->check_login();
    }

    function load_module ($module = '')
    {
        // existiert das Modul im Modulverzeichnis?
        if (file_exists($this->module_dir . $module . '.mod.php'))
        {
            include_once ($this->module_dir . $module . '.mod.php');

            // Ohne Modulname können wir nicht den Klassennamen bestimmen
            if (!empty($module_name))
            {
                $this->$module_name = new $module_name;
                $this->module_name = $module_name;
            }
            else
            { die ("<b>Fatal Error:</b> The requested module (".$this->working_dir . $module.".mod.php) is invalid. Exiting ..."); }
        }
        else
        { die ("<b>Fatal Error:</b> Can't open requested module (".$this->working_dir . $module.".mod.php). Exiting ..."); }
    }

    function load_templates($t_dir)
    {
        include('HTML/IT.php');

        // existiert das angegebene template verzeichnis?
        if (is_dir($this->working_dir.$t_dir))
        {
            $this->template_dir = $this->working_dir.$t_dir.'/';
            $this->main_template = new IntegratedTemplate($this->template_dir);
            $this->main_template->loadTemplateFile('main.tpl');

            $this->mod_template = new IntegratedTemplate($this->template_dir);
        }
        else
        { die ("<b>Fatal Error:</b> Can't open template directory. Exiting ..."); }

        if (file_exists($this->template_dir.$this->module_name.'.tpl'))
        { $this->mod_template->loadTemplateFile($this->module_name.'.tpl'); }
        else
        { die ("<b>Fatal Error:</b> Can't find template file. Exiting ..."); }
    }

    function user_template_show()
    {
        // wird benutzt wenn ein login nötig ist
        $this->user_template = new IntegratedTemplate($this->template_dir);
        $this->user_template->loadTemplateFile('user.tpl');

        $this->user_template->setVariable('module_name',$this->module_name.'.php');

        $u_tpl = $this->user_template->get();
        $this->mod_template->setVariable('user',$u_tpl);
    }

    function main_template_show()
    {
        $m_tpl = $this->mod_template->get();
        $this->main_template->setVariable('module',$m_tpl);
        $this->main_template->show();
    }
}
?>
