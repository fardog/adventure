<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Email
| -------------------------------------------------------------------------
| This file lets you define parameters for sending emails.
| Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/libraries/email.html
|
*/
$config['mailtype'] = 'html';
$config['charset'] = 'utf-8';
$config['newline'] = "\r\n";
$config['protocol'] = ''; /** ADVENTURE_REQUIRED **/
$config['smtp_host'] = ''; /** ADVENTURE_REQUIRED **/
$config['smtp_port'] = 465;
$config['smtp_user'] = ''; /** ADVENTURE_REQUIRED **/
$config['smtp_pass'] = ''; /** ADVENTURE_REQUIRED **/

/* End of file email.php */
/* Location: ./application/config/email.php */