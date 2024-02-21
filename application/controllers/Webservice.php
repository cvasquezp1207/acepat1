<?php

include_once "Controller.php";

class Webservice extends Controller {
	
	protected $idempresa = null;
	protected $idsucursal = null;
	
	public function __construct() {
		parent::__construct(true, false);
	}

	public function init_controller() {
		return null;
	}
	
	public function end_controller() {
		return null;
	}
	
	public function form() {
		return null;
	}
	
	public function grilla() {
		return null;
	}
	
	public function index($tpl = "") {
		global $TipoComprobante;
		global $db;
		global $_this;
		
		$this->load_controller("facturacion", null, false);
		
		$_this = $this;
		$db = $this->db;
		$TipoComprobante = $this->facturacion_controller->TipoComprobante;
		
		include_once APPPATH."/service/server.php";
	}
}
