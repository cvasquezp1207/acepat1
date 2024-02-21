<?php

include_once "Model.php";

class Generic_model extends Model {
	
	public function __construct() {
		parent::__construct(false);
	}
	
	public function init() {
		// nada
	}
}