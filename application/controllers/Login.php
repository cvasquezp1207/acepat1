<?php

class Login extends CI_Controller {

	private $usu;
	private $pass;
	public $data = array();

	public function __construct() {
		parent::__construct();
		$this->load->library('form_validation');
		$this->load->model('Usuario_model', 'usuario');
		$this->load->library('bcrypt');//cargamos la librería
	}

	public function index($tpl = "") {
		$user = $this->session->userdata('usuario');
		if(!empty($user)) {
			redirect('home/index');
		}
		else {
			$logo = $this->get_param("logo");
			$titulo = $this->get_param("titulo_pagina");
			
			$this->data['usuario'] = false;
			$this->data['pass'] = false;
			$this->data['logo'] = $logo;
			$this->data['title'] = $titulo;
			$this->data['error'] = false;
			$this->load->view('login/index', $this->data);
		}
	}
	
	public function get_param($param) {
		$this->load->model("Param_model", "param");
		$arr = $this->param->find($param);
		if(is_array($arr)) {
			if(is_numeric($arr["valor"])) {
				if(strpos($arr["valor"], ".") !== false) {
					return floatval($arr["valor"]);
				}
				return intval($arr["valor"]);
			}
			return $arr["valor"];
		}
		return $arr;
	}

	public function ingresar(){

		$this->form_validation->set_rules('usuario', 'usuario', 'required');
        $this->form_validation->set_rules('password', 'password', 'required',
            array('required' => 'El campo %s es obligatorio.')
        );

        if (!$this->form_validation->run() == FALSE){

			$this->usu  = $this->input->post('usuario');
			$this->pass = $this->input->post('password');

			// if($this->validar_usuario(strtoupper($this->usu), strtoupper($this->pass))){
			if($this->validar_usuario(($this->usu), ($this->pass))){//quitando esto
				redirect('home/');
				return;
			}
		}

		$logo = $this->get_param("logo");
		$titulo = $this->get_param("titulo_pagina");
		
		$this->data['usuario'] = $this->usu;
		$this->data['pass'] = $this->pass;
		$this->data['logo'] = $logo;
		$this->data['title'] = $titulo;
		$this->data['error'] = "No existe el usuario, o quizas se ha olvidado la contrase&ntilde;a... lamentable";
		$this->load->view('login/index', $this->data);
	}

	public function validar_usuario($usuario, $password){

		$encriptado_password = $this->bcrypt->hash_password($password);
		if ($this->bcrypt->check_password($password, $encriptado_password)) {

			$datos_usuarios = $this->usuario->autentificar_usuario($usuario, $password);

			if($datos_usuarios !== false){
				$this->session->set_userdata($datos_usuarios);
				$this->session->set_userdata('es_superusuario', 'N');
				return true;
			}
		}
		
		return false;
	}

 	public function salir(){
  		$this->session->sess_destroy();
  		redirect('login');
 	}
}
?>