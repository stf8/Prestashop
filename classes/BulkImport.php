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
class BulkImport {
	protected static $script_name = 'bulkimport.php';
	protected static $output_name = 'import/bulk.import.html';
	protected $key;
	protected $size;
	protected $shm;
	protected $mutex;
	public function __construct($key=4234420505, $size=10000) {
    $this->key = $key;
    $this->size = $size;
    $this->shm = shm_attach($this->key, $this->size);
		$this->mutex = sem_get($this->key, 1);
	}
	public function getOutput() {
		return self::$output_name;
	}
	public function run($args) {
		// this is run from the backoffice
		$commandline = 'php '. self::$script_name . ' '. $args . '> '.escapeshellarg(self::$output_name).' 2>&1  &';
		exec($commandline);
	}
	public function set($var) {
		sem_acquire($this->mutex);
		shm_put_var($this->shm, $this->key, $var);
		sem_release($this->mutex);
	}
		
	public function get() {
		sem_acquire($this->mutex);
		$var = @shm_get_var($this->shm, $this->key); // fail silently 
		sem_release($this->mutex);	
		return $var;		
	}
	public function running() {
		exec('ps -C "php" -o pid=,args=', $output);
		if (count($output) == 0)
			return false;
		foreach($output as $o) {
			list($pid, ,$cmd) = explode(' ',trim($o));
			if (strstr($cmd, self::$script_name) !== false) {
				return true;
			}
		}
		return false;
	}
	public function show($info, $p = true) {
		if (PHP_SAPI != 'cli') 
			return;
		switch(gettype($info)) {
			case "string":
				if ($p)
					echo "<p>$info</p>";
				else
					echo $info; 
				break;
			case "array":
				echo "<ul>";
				foreach($info as $i) {
					echo "<li>"; $this->show($i, false); echo "</li>";
				}
				echo "</ul>";
				break;
		}
	}
};
