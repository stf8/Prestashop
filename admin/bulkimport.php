<?php 
/* Copyright (c) 2012 Stéphane Mariel <stf@mariel.fr>
   Permission is hereby granted, free of charge, to any person obtaining a copy
   of this software  and associated  documentation files ( the "Software" ), to
   deal in the  Software  without restriction, including without limitation the
   rights to  use, copy, modify, merge, publish, distribute, sublicense, and/or
   sell copies of the  Software, and to permit persons  to whom the Software is
   furnished to do so, subject to the following conditions:

   The above copyright notice  and this permission notice shall be  included in
   all copies or substantial portions of the Software.

   THE SOFTWARE IS PROVIDED "AS IS",  WITHOUT WARRANTY OF ANY KIND, EXPRESS  OR
   IMPLIED, INCLUDING  BUT NOT  LIMITED TO THE  WARRANTIES OF  MERCHANTABILITY,
   FITNESS FOR  A  PARTICULAR  PURPOSE AND  NONINFRINGEMENT. IN NO EVENT  SHALL
   THE  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE  FOR ANY CLAIM, DAMAGES OR OTHER
   LIABILITY, WHETHER  IN AN  ACTION OF  CONTRACT, TORT OR  OTHERWISE,  ARISING 
   FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
   IN THE SOFTWARE. */

if (PHP_SAPI != 'cli') die();

// we run as a backoffice process...
define('_PS_ADMIN_DIR_', getcwd());  
require('../config/config.inc.php');
require('functions.php');

// let's simulate a quick & dirty post (for better compatibility)

foreach(array_slice($_SERVER['argv'],1) as $arg) {

	list($key,$value) = explode('=',$arg);
	if ($key == 'type_value')
		$_POST[$key] = explode(";",  trim($value, "'"));
	else
	$_POST[$key] = trim($value, "'");
}

// time to play...

echo "This is bulk import 1.0...\n";
$import  = Controller::getController('AdminImportController');

$import->productImport();

