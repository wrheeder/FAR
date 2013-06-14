<?php

// DO NOT ADD ANYTHING TO THIS FILE!!

// This is a catch-all file for your project. You can change
// some of the values here, which are going to have affect
// on your project

// AgileProject - change to your own API name.
// agile_project - this is realm. It should be unique per-project
// jui - this is theme. Keep it jui unless you want to make your own theme
//set_include_path(get_include_path(). PATH_SEPARATOR .'/home/nsnexter/php/');
//require_once 'Spreadsheet/Excel/Writer.php';
include 'atk4/loader.php';

date_default_timezone_set('Africa/Johannesburg');
$api=new Frontend('FAR','CellC');
$api->main();
?>