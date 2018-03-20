<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


$route['apoyos/admin/(:num)/(:any)']			= 'admin/load/$1/$2';

$route['apoyos/admin/(:num)']			= 'admin/load/$1';
?>