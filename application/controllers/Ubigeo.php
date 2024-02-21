<?php

include_once "Controller.php";

class Ubigeo extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		// any code
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		// some code
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null) {
		return null;
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return null;
	}
	
	public function get_departamento() {
		$this->load_model("general.ubigeo");
		$this->response($this->ubigeo->get_departamento());
	}
	
	public function get_provincia($idubigeo) {
		$this->load_model("general.ubigeo");
		$this->response($this->ubigeo->get_provincia($idubigeo));
	}
	
	public function get_distrito($idubigeo) {
		$this->load_model("general.ubigeo");
		$this->response($this->ubigeo->get_distrito($idubigeo));
	}
}
?>