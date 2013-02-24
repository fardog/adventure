<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "adventure/about";
$route['adventure'] = "adventure/all";
$route['adventure/view/(:num)'] = "adventure/view/$1";
$route['adventure/edit/(:num)'] = "adventure/edit/$1";
$route['adventure/pages/(:any)'] = "adventure/pages/$1";
$route['adventure/editors/(:num)'] = "adventure/editors/$1";
$route['adventure/remove_editor/(:num)/(:num)'] = "adventure/remove_editor/$1/$2";
$route['adventure/all'] = "adventure/all";
$route['adventure/create'] = "adventure/create";
$route['adventure/delete/(:num)'] = "adventure/delete/$1";
$route['adventure/(:any)/pages'] = "adventure/pages/$1";
$route['adventure/(:any)/(:num)'] = "page/view/$2";
$route['adventure/(:any)'] = "adventure/view/$1";
$route['creator/view/(:any)'] = "creator/view/$1";
$route['creator/all'] = "creator/all";
$route['creator/(:any)'] = "creator/view/$1";
$route['page/(:num)'] = "page/view/$1/1";
$route['page/view/(:num)'] = "page/view/$1/1";
$route['help'] = "static_page/view/2";
$route['terms'] = "static_page/view/3";
$route['contact'] = "static_page/contact";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */