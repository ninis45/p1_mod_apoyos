<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

//$route['apoyos/enviar/(:num)']			= 'apoyos_front/edit/$1';
//$route['apoyos/(:num)']			= 'apoyos_front/load/$1';


$route['apoyos/admin(:any)?']			= 'admin$1';

$route['apoyos/admin/(:num)/(:any)']			= 'admin/load/$1/$2';

$route['apoyos/admin/(:num)']			= 'admin/load/$1';
$route['apoyos/(:num)']			= 'apoyos_front/load/$1';
$route['apoyos/enviar/(:num)']			= 'apoyos_front/edit/$1';
$route['apoyos/detalles/(:num)']			= 'apoyos_front/details/$1';
$route['apoyos(:any)?']			= 'apoyos_front$1';




?>