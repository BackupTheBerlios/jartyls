<?php
include('main.class.php');

$lanpage = new lanpage('modules');

$lanpage->load_module('news');

$lanpage->load_templates('templates');

$lanpage->mod_template->setVariable('test','test');

print $lanpage->user->logged_in;

$lanpage->main_template_show();

?>
