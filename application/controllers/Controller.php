<?php
defined('PAGE_TITLE') OR define('PAGE_TITLE', 'SYSTEMA COMERCIAL EVA');
defined('VERSION') OR define('VERSION', 'v0.63');

abstract class Controller extends CI_Controller {
	
	protected $session_active; // variable que indica si el usuario continua en sesion
	protected $response_method; // variable que indica el metodo de la peticion (ajax|html)
	protected $response_type; // variable que indica el tipo de respuesta de la peticion (json|plain)
	protected $response_data; // datos a devolver en cada peticion al servidor
	protected $css; // css a incluir a la plantilla
	protected $js; // js a incluir a la plantilla
	protected $menu_title = ""; // titulo del modulo
	protected $menu_subtitle = ""; // subtitulo del modulo
	protected $menu_content = array(); // contenido adicional del modulo
	protected $grilla_filter = ""; // datos para filtrar la grilla principal
	protected $show_path = true; // mostrar la ruta de la vista actual
	protected $buttons; // lista de botones del modulo
	protected $controller; // controlador de la clase
	protected $position_buttons = "top"; // posicion de los botones
	protected $type_form = "reload"; // tipo al acceder al formulario
	protected $tpl_menu = "menu_all"; // vista para los menus [menu|menu_all]
	protected $with_tabs = true; // indica si va trabajar con tabs
	
	public function __construct($normal = TRUE, $validate_login = TRUE) {
		if($normal) {
			parent::__construct();
		}
		$this->init($normal, $validate_login);
		$_POST['controller'] = $this->controller;
		if( !isset($this->router) ){
			$ci =& get_instance();
			$this->router = $ci->router;
		}
		$_POST['accion'] = $this->router->method;
	}
	
	/**
	 * Metodo de inicializacion, para verificar algunos datos del sistema
	 */
	private function init($normal = true, $validate_login = true) {
		$this->controller = strtolower(get_class($this));
		
		if($normal !== true) {
			return;
		}
		
		$this->session_active = $this->is_loggin();
		
		// obtenemos el tipo de respuesta
		$this->response_method = $this->input->get_post("response");
		if(empty($this->response_method)) {
			$this->response_method = "html";
		}
		
		$this->response_type = $this->input->get_post("type");
		if(empty($this->response_type)) {
			$this->response_type = "plain";
		}
		
		// verificamos la session del usuario
		if($this->session_active == false && $validate_login == true) {
			if($this->response_method == "html") {
				if($this->response_type != "json") {
					if($this->with_tabs) {
						if($this->router->class == "home" || $this->router->class == "login") {
							redirect('login/index');
							exit;
						}
					}
					else {
						redirect('login/index');
						exit;
					}
				}
			}
			$this->exception("La session del usuario ha finalizado. Por favor vuelva iniciar session.");
		}
		
		// guardamos en session el id de los modulos
		$p = $this->input->get_post("mop");
		if(!empty($p)) {
			$this->_save_menu("menu_p", $p);
		}
		$c = $this->input->get_post("moc");
		if(!empty($c)) {
			$this->_save_menu("menu_c", $c);
		}
		
		// inicializamos el controlador
		$this->init_controller();
	}
	
	/**
	 * Metodo para guardar datos del menu que se acceder
	 */
	public function _save_menu($key, $val) {
		$data = $this->session->userdata('access_menu');
		
		if( ! is_array($data)) {
			$data = array();
		}
		if( ! array_key_exists($this->controller, $data)) {
			$data[$this->controller] = array();
		}
		$data[$this->controller][$key] = $val;
		
		$this->session->set_userdata('access_menu', $data);
	}

	public function _get_menu($key) {
		$data = $this->session->userdata('access_menu');
		if(is_array($data)) {
			if(array_key_exists($this->controller, $data)) {
				if(array_key_exists($key, $data[$this->controller])) {
					return $data[$this->controller][$key];
				}
			}
		}
		
		return null;
	}
	
	/**
	 * Metodo para generar una respuesta al cliente en forma de error,
	 * imprime en pantalla el mensaje de error.
	 * @param String $msg opcional, el mensaje de error
	 */
	protected function response_error($msg = "") {
		if($this->response_method == "html") {
			if($this->response_type != "json") {
				echo $msg;
				return;
			}
		}
		$res["code"] = "ERROR";
		$res["message"] = $msg;
		$res["data"] = null;
		echo json_encode($res);
	}
	
	/**
	 * Metodo para generar una respuesta al navegador.
	 * @param mixed $data opcional, datos a enviar al cliente
	 * @return String
	 */
	protected function response_success($data = "") {
		if($this->response_method == "html") {
			if($this->response_type != "json") {
				return $data;
			}
			else {
				return json_encode($data);
			}
		}
		else { // ajax
			if($this->response_type == "json") { // json
				// $data = json_encode($data);
			}
			else {
				if(is_array($data) || is_object($data)) {
					$data = json_encode($data);
				}
			}
		}
		$res["code"] = "OK";
		$res["message"] = "";
		$res["data"] = $data;
		return json_encode($res);
	}
	
	/**
	 * Metodo para verificar si el usuario continua en session
	 * @return boolean
	 */
	protected function is_loggin() {
		return $this->in_session("usuario");
	}
	
	/**
	 * Metodo para obtener algun dato almacenado en session
	 * @param String $var opcional, nombre de la variable a obtener, 
	 * retorna todas las variables de sesion si no se envia parametro
	 * @return mixed valor de la variable
	 */
	public function get_var_session($var=null, $def=null) {
		if($var == null)
			return $this->session->all_userdata();
		if($def == null)
			return $this->session->userdata($var);
		$val = $this->session->userdata($var);
		if($val == null)
			return $def;
		return $val;
	}
	
	/**
	 * Verificar si una variable se encuentra en session
	 * @param String $var nombre de la variable
	 * @return boolean
	 */
	public function in_session($var) {
		$val = $this->get_var_session($var);
		return ($val != null && $val !== false);
	}
	
	/**
	 * Metodo para indicar las hojas de estilo a incluir en los formularios
	 * @param String $css ruta de la css
	 * @param boolean $tag opcional, si se generar la ruta y el tag
	 */
	public function css($css, $tag=true) {
		if(!is_array($this->css)) {
			$this->css = array();
		}
		if($tag) {
			if(!file_exists(FCPATH.'app/css/'.$css.'.css')) {
				return;
			}
			$css = '<link href="'.base_url('app/css/'.$css.'.css').'" rel="stylesheet">';
		}
		if(!in_array($css, $this->css)) {
			$this->css[] = $css;
		}
	}
	
	/**
	 * Metodo para indicar los javascript a incluir en los formularios
	 * @param String $js ruta del javascript
	 * @param boolean $tag opcional, si se generar la ruta y el tag
	 */
	public function js($js, $tag=true) {
		$CI =& get_instance();
		
		if(!is_array($CI->js)) {
			$CI->js = array();
		}
		if($tag) {
			if(!file_exists(FCPATH.'app/js/'.$js.'.js')) {
				return;
			}
			$js = '<script src="'.base_url('app/js/'.$js.'.js?'.VERSION).'"></script>';
		}
		
		if(!in_array($js, $CI->js)) {
			$CI->js[] = $js;
		}
	}
	
	/**
	 * Metodo para indicar el titulo de la vista
	 * @param String $title, titulo del modulo
	 */
	public function set_title($title) {
		$this->menu_title = $title;
	}
	
	/**
	 * Metodo para indicar el subtitulo de la vista
	 * @param String $subtitle, subtitulo del modulo
	 */
	public function set_subtitle($subtitle) {
		$this->menu_subtitle = $subtitle;
	}
	
	/**
	 * Metodo para indicar el contenido adicional a mostrar en la vista
	 * @param String $content, contenido del modulo
	 */
	public function set_content($content) {
		$this->menu_content[] = $content;
	}
	
	/**
	 * Contenido adicional para filtrar la grilla
	 * @param String $content, contenido del modulo
	 */
	public function set_filter($content) {
		$this->grilla_filter = $content;
	}
	
	/**
	 * Metodo para indicar si se va mostrar la ruta del modulo
	 * @param boolean $var
	 */
	public function set_path($val) {
		$this->show_path = $val;
	}
	
	/**
	 * Indicar la posicion de los botones
	 * @param $tipo posicion (left, top, right)
	 */
	public function set_position_button($tipo) {
		$tipo = strtolower($tipo);
		$pos = array("top", "left", "right");
		if(in_array($tipo, $pos)) {
			$this->position_buttons = $tipo;
		}
	}
	
	/**
	 * Indicar el tipo de acceso a los formularios
	 * @param $tipo (reload, modal)
	 */
	public function set_type_form($tipo) {
		$tipo = strtolower($tipo);
		$pos = array("reload", "modal");
		if(in_array($tipo, $pos)) {
			$this->type_form = $tipo;
		}
	}
	
	/**
	 * Metodo principal, donde se arma toda la primera vista al acceder a un modulo
	 * @return String la plantilla completa
	 */
	public function index($tpl = "") {
		if(empty($tpl)) {
			$tpl = "content_".$this->position_buttons;
		}
		
		$data = array(
			"menu_title" => $this->menu_title
			,"menu_subtitle" => $this->menu_subtitle
			,"form" => ""
			,"controller" => $this->controller
			,"with_tabs" => $this->with_tabs
		);
		
		if(in_array($tpl, array("content_top", "content_bottom"))) {
			$data["grilla"] = $this->grilla();
			$data["filter"] = $this->grilla_filter;
			$data["buttons"] = $this->get_buttons();
		}
		
		$content = "";
		if( ! empty($this->menu_content)) {
			$content = implode("\n", $this->menu_content);
		}
		$data["content"] = $content;
		
		if($this->show_path) {
			$data['path'] = $this->get_path();
		}
		if($this->type_form == "modal") {
			$data["form"] = $this->form();
		}
		
		$str = $this->load->view($tpl, $data, true);
		$this->show($str);
	}
	
	/**
	 * Metodo para generar la ruta al acceder a un modulo
	 * @return String, ruta del modulo 
	 */
	public function get_path() {
		$url = uri_string();
		if(substr($url, 0, 1)=="/") {
			$url = substr($url, 1);
		}
		$segmentos = explode("/", $url);
		if(count($segmentos) > 2) {
			$segmentos = array_slice($segmentos, 0, 2);
		}
		$last = count($segmentos)-1;
		
		$path = '<ol class="breadcrumb">';
		$path .= '<li><a href="'.base_url("home").'">Home</a></li>';
		
		foreach($segmentos as $k=>$v) {
			if($k!=$last) {
				$path .= '<li><a href="'.base_url($v).'">'.$v.'</a></li>';
			}
			else {
				$path .= '<li class="active"><strong>'.$v.'</strong></li>';
			}
		}
		// echo $this->router->class;
		// echo $this->router->method;
		
		$path .= '</ol>';
		return $path;
	}
	
	/**
	 * Obtener los permisos de los botones
	 */
	public function get_permisos() {
		/* $this->db->where("idmodulo", $this->get_var_session("menu_c"));
		$this->db->where("idperfil", $this->get_var_session("idperfil"));
		$this->db->where("idsucursal", $this->get_var_session("idsucursal"));
		$this->db->where("acceder", 1);
		$query = $this->db->get("seguridad.acceso"););
		
		if($query->num_rows() > 0) {
			$row = new stdClass();
			$row->nuevo = 1;
			$row->editar = 1;
			$row->eliminar = 1;
			$row->imprimir = 1;
			
			return $row;
		}
		
		$row = new stdClass();
		$row->nuevo = 0;
		$row->editar = 0;
		$row->eliminar = 0;
		$row->imprimir = 0;
		
		return $row; */
		
		$this->db->select("b.*");
		$this->db->from("seguridad.acceso_boton a");
		$this->db->join("seguridad.boton b", "b.idboton = a.idboton");
		$this->db->join("seguridad.detalle_boton db", "db.idboton = a.idboton AND db.idmodulo=a.idmodulo");
		$this->db->where("a.idmodulo", $this->_get_menu("menu_c"));
		$this->db->where("a.idperfil", $this->get_var_session("idperfil"));
		$this->db->where("a.idsucursal", $this->get_var_session("idsucursal"));
		$this->db->where("b.estado", "A");
		$this->db->order_by("db.orden", "ASC");
		$query = $this->db->get();

		return $query->result_array();
	}
	
	/**
	 * Metodo para generar los botones segun el permiso del usuario
	 * @return Array, botones
	 */
	protected function get_buttons($all_boton="") {
		$arr_button_temp = $this->buttons; // obtenemos los botones asignados
		$arr_button = array();
		// echo $all_boton;exit;
		$this->buttons = array();
		
		$arr = $this->get_permisos();
		if(!empty($arr)) {
			foreach($arr as $row) {
				$this->add_button($row["id_name"], $row["descripcion"], $row["icono"], $row["clase_name"],$row['tipo'], $all_boton);
			}
		}
		/* if($row->nuevo == 1) {
			$this->add_button("btn_nuevo", "Nuevo", "fa-file-o", "primary");
			// $this->add_button("btn_nuevo", "Nuevo", "fa-file-o");
		}
		if($row->editar == 1) {
			// $this->add_button("btn_editar", "Modificar", "fa-pencil", "warning");
			$this->add_button("btn_editar", "Modificar", "fa-pencil");
		}
		if($row->eliminar == 1) {
			// $this->add_button("btn_eliminar", "Eliminar", "fa-trash-o", "danger");
			$this->add_button("btn_eliminar", "Eliminar", "fa-trash-o", "dafault");
			// $this->add_button("btn_eliminar", "Eliminar", "fa-trash-o");
		}
		if($row->imprimir == 1) {
			// $this->add_button("btn_imprimir", "Imprimir", "fa-print", "info");
			$this->add_button("btn_imprimir", "Imprimir", "fa-print");
		} */
		
		$arr_button = array_values($this->buttons);
		
		if(!empty($arr_button_temp)) {
			foreach($arr_button_temp as $k=>$v) {
				if($k=='btn_nuevo' || $k=='btn_editar' || $k=='btn_eliminar' || $k=='btn_imprimir') {
					continue;
				}
				$arr_button[] = $v;
			}
		}
		
		return $arr_button;
	}
	
	/**
	 * Metodo para agregar botones adicionales en la grilla
	 * @param $id atributo id del boton
	 * @param $label texto del boton
	 * @param $tipo opcional, el tipo de boton (default, primary, success, info, ...)
	 * @param $icon opcional, icono del boton (fa-columns, fa-bold, ...)
	 */
	public function add_button($id, $label, $icon=null, $tipo="white",$tipo_b='',$botones="") {
		if(!is_array($this->buttons)) {
			$this->buttons = array();
		}
		
		// $btn = "<button id='{$id}' class='btn btn-outline btn-{$tipo} btn-sm'>";
		if(empty($botones)){
			$btn = "<button id='{$id}' class='btn btn-{$tipo} btn-sm'>";
			if(!empty($icon)) {
				$btn .= "<i class='fa {$icon}'></i> ";
			}
			$btn .= "{$label}</button>";
			$this->buttons[$id] = $btn;
		}else{
			// echo $botones;exit;
			// echo "<pre>".$tipo_b."</pre>";
			if($botones==$tipo_b){
					$btn = "<button id='{$id}' class='btn btn-{$tipo} btn-sm'>";
					if(!empty($icon)) {
						$btn .= "<i class='fa {$icon}'></i> ";
					}
					$btn .= "{$label}</button>";
				$this->buttons[$id] = $btn;
			}
		}
	}

	/*
	Tipo = Boton , 
	Atributo = para los estios{display:inline-block;float:right;}
	*/
	public function add_button_content($id, $label, $icon=null, $tipo="boton",$clase ="white",$atributo = array()) {
		if(!is_array($this->buttons)) {
			$this->buttons = array();
		}
		
		if ($tipo=='boton') {
			$this->add_button($id,$label, $icon,$clase);
		}else{
			$btn ="<div class='$clase' style=' ";
			if (!empty($atributo)) {
				foreach ($atributo as $k => $v) {
					$btn.=$k.":".$v.";";
				}
			}
			$btn.=" '>".$label."</div>";
			$this->buttons[$id] = $btn;
		}
	}
	
	/**
	 * Metodo para crear la barra superior del sistema
	 * @return String, vista toolbar
	 */
	protected function toolbar() {
		$data = $this->get_var_session();
		$data["alerta"]				=	$this->alertas(true);
		$data["shownotification"]	= $this->getConfig("shownotification")?$this->getConfig("shownotification"):'N';
		$data["disablechat"]		= $this->getConfig("disablechat")?$this->getConfig("disablechat"):'N';
		$data["modo_prueba_chat"]	= "N";//{N/S}Poner N para que aun no cargue en el sistema mientras se termine todos los detalles
		return $this->load->view("toolbar", $data, true);
	}
	
	/**
	 * Metodo para crear el menu|modulo del sistema
	 * @return String, vista menu
	 */
	protected function menu() {
		$data = $this->get_var_session();
		// $data["menus"] = $this->get_modulos();
		$data["menus"] = $this->lista_sistemas();
		return $this->load->view($this->tpl_menu, $data, true);
	}
	
	/**
	 * Obtener la lista de sistemas con todos sus modulos 
	 */
	private function lista_sistemas() {
		// $this->db->select("s.*");
		// $this->db->from("seguridad.acceso_sistema a");
		// $this->db->join('seguridad.sistema s', 's.idsistema = a.idsistema');
		// $this->db->where('a.idsucursal', $this->get_var_session("idsucursal"));
		// $this->db->where('s.estado', 'A');
		// $query = $this->db->get();
		
		// $arr_sistema = array();
		// if($query->num_rows() > 0) {
			// $sistemas = $query->result_array();
			// foreach($sistemas as $v) {
				// $v["menus"] = $this->get_modulos($v["idsistema"]);
				// $arr_sistema[] = $v;
			// }
		// }
		
		// return $arr_sistema;
		$idsucursal = $this->get_var_session("idsucursal") ? $this->get_var_session("idsucursal"):0;
		$idperfil = $this->get_var_session("idperfil") ? $this->get_var_session("idperfil"):0;
		$query = $this->db->query("SELECT*FROM seguridad.sistema WHERE estado='A' AND idsistema IN (SELECT 
									DISTINCT m.idsistema 
									FROM seguridad.acceso a
									JOIN seguridad.modulo m ON m.idmodulo=a.idmodulo
									WHERE idsucursal={$idsucursal} AND idperfil={$idperfil}) 
									ORDER BY orden;");
		
		$arr_sistema = array();
		if($query->num_rows() > 0) {
			$sistemas = $query->result_array();
			foreach($sistemas as $v) {
				$v["menus"] = $this->get_modulos($v["idsistema"]);
				$arr_sistema[] = $v;
			}
		}
		return $arr_sistema;
	}
	
	/**
	 * Obtener la lista de modulos para los usuarios logueados
	 * @return array lista de modulos
	 */
	private function get_modulos($idsistema=null) {
		$idusuario = intval($this->get_var_session("idusuario"));
		$idsucursal = intval($this->get_var_session("idsucursal"));
		$idperfil = intval($this->get_var_session("idperfil"));
		if($idsistema == null)
			$idsistema = intval($this->get_var_session("idsistema"));
		
		$param["idusuario"] = $idusuario;
		$param["idsucursal"] = $idsucursal;
		$param["idperfil"] = $idperfil;
		$param["idsistema"] = $idsistema;
		$param["idpadre"] = 0;
		
		$array_modulo = array();
		
		// obtenemos la lista de modulos padres
		$this->load_model("acceso");
		$mp = $this->acceso->get_modulo($param);
		if(!empty($mp)) {
			foreach($mp as $v) {
				$param["idpadre"] = $v["idmodulo"];
				$v["submenus"] = $this->acceso->get_modulo($param);
				$array_modulo[] = $v;
			}
		}
		
		return $array_modulo;
	}
	
	/**
	 * Enviar una exception al navegador interrumpiendo la ejecucion del script,
	 * imprime el mensaje de error.
	 * @param String $msg mensaje de error
	 */
	public function exception($msg) {
		$this->response_error($msg);
		// terminamos la ejecucion del script
		// notese que aqui se hace un exit, no es la forma correcta de hacer,
		// pero codeigniter no ofrece alguna solucion disponible.
		// Tener en cuenta si se va hacer algun otro proceso posterior.
		exit;
	}
	
	/**
	 * Formatear los datos a enviar al cliente
	 * @param mixed $data
	 */
	public function response($data) {
		$this->response_data = $this->response_success($data);
	}
	
	/**
	 * Metodo para procesar la plantilla.
	 * @param String $path_tpl opcional, ruta de la vista
	 * @param String $form opcional, el formulario
	 * @param String $grilla opcional, la grilla
	 */
	public function show($content="", $path_tpl="index", $force=false) {
		// $this->js("default");
		if(!$this->in_session("idsucursal")) {
			$this->js("<script>abrir_seleccion_sucursal();</script>", false);
		}
		
		$this->end_controller();
		
		if(empty($path_tpl)) {
			$path_tpl = "index";
		}
		if($this->with_tabs) {
			$path_tpl = "iframe";
		}
		if($path_tpl != "index") {
			if($force) {
				$path_tpl = "index";
			}
		}
		
		$data = array();
		if($path_tpl == "index") {
			$data["menu"] = $this->menu();
			$data["toolbar"] = $this->toolbar();
			$data["modal_sucursal"] = $this->modal_sucursal();
			$data["panel_config"] = $this->panel_config();
			$data["panel_chat"] = $this->panel_chat();
			//$this->initialize_chat();
		}
		
		$data2 = array(
			"page_title" => $this->get_param("titulo_pagina")
			,"css" => $this->css
			,"js" => $this->js
			,"content" => $content
			,"controller" => $this->controller
			,"type_form" => $this->type_form
			,"session" => $this->get_var_session()
		);
		
		$data = array_merge($data2, $data);
		
		$tpl = $this->load->view($path_tpl, $data, true);
		$this->response($tpl);
	}
	
	/**
	 * Metodo para devolver la lista de sucursales segun el usuario
	 * @return array
	 */
	public function get_sucursales() {
		$this->load_model("acceso_empresa");
		return $this->acceso_empresa->get_empresa_usuario($this->get_var_session("idusuario"));
	}
	
	/**
	 * Metodo para generar el modal de seleccion de la sucursal 
	 */
	public function modal_sucursal() {
		$data["sucursales"] = $this->get_sucursales();
		return $this->load->view("sucursales", $data, true);
	}
	
	/**
	 * Metodo para generar el panel de configuracion y valores predeterminados del sistema 
	 */
	public function panel_config() {
		$default = array(
			"idtipodocumento" => FALSE
			,"serie" => FALSE
			,"idtipoventa" => FALSE
			,"idmoneda" => FALSE
			,"idtipopago" => FALSE
			,"idalmacen" => FALSE
			,"idvendedor" => FALSE
			,"collapsemenu" => "N"
			,"fixednavbar" => "N"
			,"boxedlayout" => "N"
			,"shownotification" => "N"
			,"disablechat" => "N"
			,"offline_users" => "N"
			,"skin" => "s-skin-0"
			,"class_skin" => "skin-0"
		);
		
		// obtenemos datos almacenados segun el usuario
		$query = $this->db->where("idsucursal", $this->get_var_session("idsucursal"))
			->where("idusuario", $this->get_var_session("idusuario"))
			->get("seguridad.datos_usuario");
		if($query->num_rows() > 0) {
			$rs = $query->result_array();
			foreach($rs as $row) {
				if(array_key_exists($row["clave"], $default))
					$default[$row["clave"]] = $row["valor"];
			}
		}
		
		$data["serie"] = ($default["serie"] !== false) ? $default["serie"] : "";
		
		$combobox = $this->get_combobox();
		$combobox->estricto=false;
		$combobox->setAttr("class", "form-control input-xs input-config");
		$combobox->addItem("", "");
		
		// combobox tipo documento
		$sql = "select distinct t.idtipodocumento, t.descripcion 
			from venta.serie_documento s 
			join venta.tipo_documento t on t.idtipodocumento = s.idtipodocumento
			where s.idsucursal=? and t.mostrar_en_venta='S' and t.estado='A'
			order by 1";
		$query = $this->db->query($sql, array($this->get_var_session("idsucursal")));
		$combobox->setAttr("name", "idtipodocumento");
		$combobox->addItem($query->result_array());
		if(!empty($default["idtipodocumento"])){
			$combobox->setSelectedOption($default["idtipodocumento"]);
		}
		$data["tipo_documento"] = $combobox->getObject();
		
		// combobox tipo venta
		$sql = "select idtipoventa, descripcion from venta.tipo_venta
			where estado='A' and mostrar_en_venta='S' order by 1";
		$query = $this->db->query($sql);
		$combobox->setAttr("name", "idtipoventa");
		$combobox->removeItems(1);
		$combobox->addItem($query->result_array());
		$combobox->setSelectedOption($default["idtipoventa"]);
		$data["tipo_venta"] = $combobox->getObject();
		
		// combobox almacen
		$sql = "select idalmacen, descripcion from almacen.almacen
			where estado='A' and idsucursal=? order by 1";
		$query = $this->db->query($sql, array($this->get_var_session("idsucursal")));
		$combobox->setAttr("name", "idalmacen");
		$combobox->removeItems(1);
		$combobox->addItem($query->result_array());
		$combobox->setSelectedOption($default["idalmacen"]);
		$data["almacen"] = $combobox->getObject();
		
		// combobox tipo pago
		$sql = "select idtipopago, descripcion from venta.tipopago
			where estado='A' and mostrar_en_venta='S' order by 1";
		$query = $this->db->query($sql);
		$combobox->setAttr("name", "idtipopago");
		$combobox->removeItems(1);
		$combobox->addItem($query->result_array());
		$combobox->setSelectedOption($default["idtipopago"]);
		$data["tipo_pago"] = $combobox->getObject();
		
		// combobox moneda
		$sql = "select idmoneda, descripcion from general.moneda where estado='A' order by 1";
		$query = $this->db->query($sql);
		$combobox->setAttr("name", "idmoneda");
		$combobox->removeItems(1);
		$combobox->addItem($query->result_array());
		$combobox->setSelectedOption($default["idmoneda"]);
		$data["moneda"] = $combobox->getObject();
		
		// combobox vendedor
		$idperfil = $this->get_param("idtipovendedor"); // id del perfil vendedor, tal vez deberia ser contante
		$this->load_model("usuario");
		$datos = $this->usuario->get_vendedor($this->get_var_session("idsucursal"), $idperfil);
		$combobox->setAttr("name", "idvendedor");
		$combobox->removeItems(1);
		$combobox->addItem($datos);
		
		$combobox->setSelectedOption($default["idvendedor"]);
		
		$data["vendedor"]		= $combobox->getObject();

		$sql = "select * from theme.theme where estado='A';";
		$query = $this->db->query($sql);
		
		$data["skin"]				= $this->getConfig("skin");
		$data["class_skin"]			= $this->getConfig("class_skin");
		$data["collapsemenu"]		= $this->getConfig("collapsemenu");
		$data["fixednavbar"]		= $this->getConfig("fixednavbar");
		$data["boxedlayout"]		= $this->getConfig("boxedlayout");
		$data["shownotification"]	= $this->getConfig("shownotification");
		$data["disablechat"]		= $this->getConfig("disablechat");
		$data["offline_users"]		= $this->getConfig("offline_users");
		$data["theme"]				= $query->result_array();
		
		$obj_skin					= json_encode($data["theme"]);
		$this->js("<script> var _skin = '".$data["skin"]."';</script>",false);
		$this->js("<script> var _class_skin = '".$data["class_skin"]."';</script>",false);
		$this->js("<script> var obj_skin = ".$obj_skin.";</script>",false);
		foreach($data["theme"] as $k=>$v){
			$var_script = "";
			$var_script.= "$('.{$v['clase_skin']}').click(function(){";
			foreach($data["theme"] as $key=>$val){
				if($v["clase_skin"]==$val["clase_skin"]){
					$var_script.=" $('body').addClass('{$v['clase_theme']}');";
					$var_script.=" var skin_selected='{$v['clase_theme']}';";
					$var_script.=" var skin_class_selected='{$v['clase_skin']}';";
				}else{
					$var_script.=" $('body').removeClass('{$val['clase_theme']}');";
				}
			}

			$var_script.=" save_other_config(skin_selected, skin_class_selected);";
			$var_script.="});";
			
			$this->js("<script>$var_script</script>",false);
		}
		$this->js("config");
		// $this->js("<script>saveStorage('default_values', '".json_encode($default)."');</script>", false);
		$this->js("<script>setDefaultValue(".json_encode($default).");</script>", false);
		
		return $this->load->view("config", $data, true);
	}
	
	public function panel_chat() {
		return "";
		$data = $this->get_var_session();
		$data["usuarios"]			= $this->lista_usuario();
		return $this->load->view("content_chat", $data, true);
	}
	/*
	protected function initialize_chat() {
		$idsucursal = $this->get_var_session("idsucursal");
		if(empty($idsucursal))
			return;
		
		$habilitar_chat = ($this->get_param("habilitar_chat", "N") == "S");
		$nodejs_url = $this->get_param("nodejs_url", "");
		// $nodejs_active = ($nodejs_url == "") ? false : (is_url($nodejs_url) && exist_url($nodejs_url));
		$nodejs_active = ($nodejs_url == "") ? false : is_url($nodejs_url);
		// $nodejs_active = true;
		
		if($habilitar_chat && $nodejs_active) {
			if(substr($nodejs_url, -1) == "/")
				$nodejs_url = substr($nodejs_url, 0, -1);
			$this->js("<script src=\"{$nodejs_url}/socket.io/socket.io.js\"></script>", false);
			$this->js("chat");
			
			$params = array(
				"toolbarTitle" => "Inicie una conversacion"
				,"toolbarIconColor" => "#0084ff"
				,"toolbarIconSize" => "20px"
				,"users" => $this->lista_usuario(true)
			);
			
			$script = "";
			$script .= "if(chat.connect('{$nodejs_url}')) {\n\r";
			$script .= "chat.createHtml(".json_encode($params).");\n\r";
			$script .= "chat.addUser('".$this->get_var_session("idusuario")."');\n\r";
			$script .= "}";
			$this->js("<script>".$script."</script>", false);
		}
	}*/
	
	public function getConfig($clave=''){
		$idsucursal = $this->get_var_session("idsucursal")?$this->get_var_session("idsucursal"):0;
		$q=$this->db->query("SELECT valor FROM seguridad.datos_usuario WHERE clave='$clave' AND idsucursal='{$idsucursal}';");
		$rs = $q->result_array();
		if(!empty($rs))
			$val=$q->row()->valor;
		else
			$val='';
		if(!isset($val))
			return null;
		else if(empty($val))
			return null;
		else
			return $val;
	}
	
	/**
	 * Metodo de respuesta controlado por CodeIgniter.
	 * Imprime todo lo que se ha enviado al metodo [response]
	 */
	public function _output($output) {
		if(!empty($this->response_data)) {
			echo $this->response_data;
		}
	}
	
	/**
	 * Cargar los modelos a la aplicacion.
	 * El nombre del modelo debe ser el mismo ubicado en la carpeta 
	 * ./application/models/[nombre]_model.php
	 * @param string $uname nombre del modelo
	 * e.g.
	 * $this->load_model("usuario");
	 * ...
	 * $this->usuario->some_method($params);
	 */
	public function load_model($uname) {
		if(is_array($uname)) {
			foreach($uname as $mod) {
				$this->load_model($mod);
			}
			return;
		}
		
		if(strpos($uname, '.') === false) {
			$tabla = $uname;
			$esquema = "";
		}
		else {
			$data = explode('.', $uname, 2);
			if(count($data) == 2) {
				$esquema = $data[0];
				$tabla = str_replace('.', '', $data[1]);
			}
			else {
				$esquema = "";
				$tabla = $data[0];
			}
		}
		
		$model = ucfirst(strtolower($tabla))."_model";
		
		if(file_exists(APPPATH.'models/'.$model.'.php')) {
			$this->load->model($model, $tabla);
			return;
		}
		
		$this->load->model("Generic_model", $tabla);
		$this->$tabla->set_table_name($tabla);
		$this->$tabla->set_schema($esquema);
		$this->$tabla->initialize();
	}
	
	public function load_controller($name, $alias = null, $validate_login = TRUE) {
		$name = strtolower($name);
		if(!isset($alias)) {
			$alias = $name."_controller";
		}
		$name = ucfirst($name);
		
		include_once $name.".php";
		$controller = new $name(false, $validate_login);
		
		$CI =& get_instance();
		
		$this->$alias = $controller;
		$this->$alias->load = clone $CI->load;
		$this->$alias->db = clone $CI->db;
		$this->$alias->session = clone $CI->session;
		$this->$alias->input = clone $CI->input;
		$this->$alias->router = clone $CI->router;
	}
	
	public function load_library($lib, $alias = null) {
		if(!file_exists(APPPATH.'libraries/'.$lib.'.php')) {
			$nname = strtolower($lib);
			$nname = ucfirst($nname);
			if(!file_exists(APPPATH.'libraries/'.$nname.'.php')) {
				$this->load->library($lib);
				return;
			}
			$lib = $nname;
		}
		
		include_once APPPATH.'libraries/'.$lib.'.php';
		$class_name = ucfirst(strtolower($lib));
		
		if (!class_exists($class_name, FALSE)) {
			return;
		}
		
		if(!isset($alias)) {
			$alias = strtolower($lib);
		}
		
		$this->$alias = new $class_name();
	}
	
	/**
	 * Metodos abstracto a implementar por los controladores heredados.
	 * @return String el formulario
	 */
	public abstract function form();
	
	 /**
	 * Metodos abstracto a implementar por los controladores heredados.
	 * @return String la grilla
	 */
	public abstract function grilla();
	
	/**
	 * Metodo abstracto para inicializar los datos del controladores
	 */
	public abstract function init_controller();
	
	/**
	 * Metodo para llamar antes de renderizar la plantilla
	 */
	public abstract function end_controller();
	
	/**
	 * Obtener los valores del parametro
	 */
	public function get_param($param, $def = null) {
		//$this->load_model("param");
		if( ! isset($this->param)) {
			$this->load_model("seguridad.param");
		}
		if( ! isset($this->param)) {
			$this->param = new Param_model();
		}

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
		
		if(is_null($arr))
			return $def;
		
		return $arr;
	}
	
	/**
	 * Obtener una instancia de la libreria combobox, de forma independiente
	 */
	public function get_combobox() {
		// cargamos la libreria combobox, trataremos de crear un combobox independiente
		// para evitar que se alteren los datos previos asignados por el usuario
		if(isset($this->combobox)) {
			$combobox = clone $this->combobox;
			$combobox->init();
			$combobox->removeAllAttr();
			$combobox->removeAllStyle();
		}
		else {
			$this->load_library('combobox');
			$combobox = clone $this->combobox;
			unset($this->combobox);
		}
		
		return $combobox;
	}
	
	/**
	 * Formulario para las opciones de pago 
	 * @param String $modulo nombre en el modulo a generard
	 * @param bool $show_afecta_caja mostrar la opcion en caja
	 * @param String $tipo tipo opciones pago (single|multi)
	 */
	public function get_form_pago($modulo, $show_afecta_caja=false, $tipo="single") {
		$combobox = $this->get_combobox();
		
		$data = array("show_afecta_caja"=>$show_afecta_caja);
		
		$modulo = strtolower($modulo);
		$queryMov = $queryPay = $selectMov = $selectPay = null;
		
		switch($modulo) {
			case "venta":
				$queryMov = $this->db->select('idconceptomovimiento, descripcion')->where("estado", "A")
					->where("ver_venta", "S")->get("caja.conceptomovimiento");
				$queryPay = $this->db->select('idtipopago, descripcion')->where("estado", "A")
					->where("mostrar_en_venta", "S")->order_by("idtipopago","asc")->get("venta.tipopago");
				break;
				
			case "compra":
				$queryMov = $this->db->select('idconceptomovimiento, descripcion')->where("estado", "A")
					->where("ver_compra", "S")->get("caja.conceptomovimiento");
				$queryPay = $this->db->select('idtipopago, descripcion')->where("estado", "A")
					->where("mostrar_en_compra", "S")->order_by("idtipopago","asc")->get("venta.tipopago");
				break;
				
			case "reciboingreso":
				$queryMov = $this->db->select('idconceptomovimiento, descripcion')->where("estado", "A")
					->where("ver_reciboingreso", "S")->get("caja.conceptomovimiento");
				$queryPay = $this->db->select('idtipopago, descripcion')->where("estado", "A")
					->where("mostrar_en_reciboingreso", "S")->order_by("idtipopago","asc")->get("venta.tipopago");
				break;
				
			case "reciboegreso":
				$queryMov = $this->db->select('idconceptomovimiento, descripcion')->where("estado", "A")
					->where("ver_reciboegreso", "S")->get("caja.conceptomovimiento");
				$queryPay = $this->db->select('idtipopago, descripcion')->where("estado", "A")
					->where("mostrar_en_reciboegreso", "S")->order_by("idtipopago","asc")->get("venta.tipopago");
				break;
			case "pagoproveedor":
				$queryMov = $this->db->select('idconceptomovimiento, descripcion')->where("estado", "A")
					->where("ver_pagoproveedor", "S")->get("caja.conceptomovimiento");
				$queryPay = $this->db->select('idtipopago, descripcion')->where("estado", "A")
					->where("mostrar_en_pagoproveedor", "S")->order_by("idtipopago","asc")->get("venta.tipopago");
				break;
			
			case "cuentas_pagar":
				$queryMov = $this->db->select('idconceptomovimiento, descripcion')->where("estado", "A")
					->where("ver_pagoproveedor", "S")->get("caja.conceptomovimiento");
				$queryPay = $this->db->select('idtipopago, descripcion')->where("estado", "A")
					->where("mostrar_en_pagoproveedor", "S")->order_by("idtipopago","asc")->get("venta.tipopago");
				break;
				
			default: break;
		}
		
		//---------------------------------- combo concepto Movimiento -----------------------------
		$combobox->init();
		$combobox->setAttr(array("name"=>"idconceptomovimiento","class"=>"idconceptomovimiento form-control"));
		if($queryMov !== null) {
			$combobox->addItem($queryMov->result_array());
		}
		if($selectMov !== null) {
			$combobox->setSelectedOption($selectMov);
		}
		$data["movimiento"] = $combobox->getObject();
		
		//-------------------------------- combo concepto Tipo pago -----------------------------
		$combobox->init();
		$combobox->setAttr(array("name"=>"idtipopago","class"=>"idtipopago form-control"));
		if($queryPay !== null) {
			$combobox->addItem($queryPay->result_array());
		}
		if($selectPay !== null) {
			$combobox->setSelectedOption($selectPay);
		}
		$data["tipopago"] = $combobox->getObject();
		
		//----------------------------------- combo tarjeta -------------------------------------
		$query = $this->db->select('idtarjeta, descripcion')->where("estado", "A")->get("general.tarjeta");
		$combobox->init();
		$combobox->setAttr(array("name"=>"idtarjeta","class"=>"idtarjeta form-control"));
		$combobox->addItem($query->result_array());
		$data["tarjeta"] = $combobox->getObject();
		
		//-------------------------------------combo cuentas bancarias ----------------------------------
		// $query = $this->db->select('idcuentas_bancarias, cuenta,idmoneda, valor_cambio,moneda_corto,simbolo_moneda')->where("estado", "A")->where("idsucursal", $this->get_var_session("idsucursal"))->get("general.view_cuentas_bancarias");
		$query = $this->db->select('idcuentas_bancarias, cuenta,idmoneda, valor_cambio,moneda_corto,simbolo_moneda')->where("estado", "A")->get("general.view_cuentas_bancarias");
		$combobox->init();
		$combobox->setAttr(array("name"=>"idcuentas_bancarias","class"=>"idcuentas_bancarias form-control"));
		$combobox->addItem("","Seleccione cuenta..");
		$combobox->addItem($query->result_array(),array("idcuentas_bancarias","cuenta","idmoneda","valor_cambio","moneda_corto","simbolo_moneda"));
		$data["cuentas_bancarias"] = $combobox->getObject();
		
		// ---------------------------------------------------------------------------------------------
		
		// js
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js('form/caja/'.$tipo.'_pay');
		
		return $this->load->view("caja/".$tipo."_pay", $data, true);
	}

	public function creditos_cartera($tipo = 'SI'){
		$estados = array();
	    $query = $this->db->query("	SELECT id_estado_credito 
		    						FROM cobranza.detalle_parametrocartera 
		    						WHERE idparametrocartera IN (SELECT idparametrocartera FROM cobranza.parametrocartera WHERE tipo='$tipo' AND estado='A') 
		    						AND estado='A' 
		    						AND idsucursal='".$this->get_var_session("idsucursal")."' 
		    						ORDER BY id_estado_credito");

	    $list_omitidos = $query->result_array();
	    
	    foreach ($list_omitidos as $key => $value) {
	      	$estados[] = $value['id_estado_credito'];
	    }

	    return $estados;
	}
	
	public function alertas($limit=false){
		$add = "";

		$idsucursal = $this->get_var_session("idsucursal")?$this->get_var_session("idsucursal"):0;
		// if($limit)
			// $add.="LIMIT 5";
		
		$sql = "SELECT d.iddeuda id ,'Tiene una deuda con '||d.proveedor||', por el monto de '||COALESCE(d.monto_deuda,0)||' '||d.moneda_corto||', que venció hace '||(CURRENT_DATE-fecha_venc)||' días' sms_alerta 
				,to_char(now()::Time, 'HH12:MI:SS AM') hora_alerta 
				,'cuentas_vencidas'::text tipo_alerta 
				FROM compra.deuda_view d 
				WHERE d.estado='A' 
				AND d.pagado='N' 
				AND d.idsucursal='{$idsucursal}' 
				AND fecha_venc IS NOT NULL
				AND (CURRENT_DATE-fecha_venc)>1 
				
				UNION
				
				SELECT d.iddeuda id ,'Tiene una deuda con '||d.proveedor||', por el monto de '||COALESCE(d.monto_deuda,0)||' '||d.moneda_corto||', que vencerá en '||(CURRENT_DATE-fecha_venc)||' días' sms_alerta 
				,to_char(now()::Time, 'HH12:MI:SS AM') hora_alerta 
				,'cuentas_pagar'::text tipo_alerta 
				FROM compra.deuda_view d 
				WHERE d.estado='A' 
				AND d.pagado='N' 
				AND d.idsucursal='{$idsucursal}' 
				AND (CURRENT_DATE-fecha_venc)<7 
				
				UNION
				
				SELECT p.idproducto id
				,COALESCE(p.descripcion,'')||' Superó el minimo('||COALESCE(p.stock_minimo,0)||') de stock en '||u.abreviatura sms_alerta 
				,to_char(now()::Time, 'HH12:MI:SS AM') hora_alerta 
				,'productos'::text tipo_alerta 
				FROM compra.producto p
				JOIN (
					SELECT SUM(s.stock) stock,idproducto,idunidad FROM almacen.view_stock s WHERE idsucursal=$idsucursal GROUP BY idproducto,idunidad
				) ss ON ss.idproducto=p.idproducto AND p.stock_minimo>=ss.stock AND p.controla_stock='S' AND p.genera_alerta_stock='S'
				JOIN compra.unidad u ON u.idunidad=ss.idunidad
				WHERE p.estado='A'
				
				ORDER BY tipo_alerta,sms_alerta
				;";
		
		$q = $this->db->query($sql);
		$res["list"] = $q->result_array();
		$res["cant"] = count($res["list"]);
		return $res;
		// return $q->result_array();
	}

	public function creditos_no_cartera(){
		return $this->creditos_cartera("NO");
	}
	
	public function load_kardex($modelo,$alias){
		$this->load_model($modelo);
		$this->load_library("kardex" , $alias);
		$this->$alias->set_model($this->$modelo);
		$this->$alias->idusuario = $this->get_var_session("idusuario");
	}
	
	/**
	 * Formulario para el ubigeo
	 * @param String $select idubigeo seleccionado, default 220901 Tarapoto
	 */
	public function get_form_ubigeo($select = "220901") {
		$idubigeo = false;
		if(!empty($select) && is_string($select)) {
			$select = preg_replace('/\s+/', '', $select);
			if(preg_match("/\d{6}/", $select)) {
				$idubigeo = $select;
			}
		}
		
		if( ! isset($this->ubigeo)) {
			$this->load_model("general.ubigeo");
		}
		if( ! isset($this->ubigeo)) {
			$this->ubigeo = new Ubigeo_model();
		}
		
		$combobox = $this->get_combobox();
		$combobox->setAttr("class", "idubigeo_temp_modal form-control");
		
		// combo departamentos
		$combobox->init();
		$combobox->setAttr(array("data-name"=>"departamento","data-reload"=>"provincia"));
		$combobox->addItem($this->ubigeo->get_departamento());
		if($idubigeo) {
			$combobox->setSelectedOption(substr_replace($idubigeo, "0000", 2));
		}
		$data["departamento"] = $combobox->getObject();
		
		// combo provincias
		$combobox->init();
		$combobox->setAttr(array("data-name"=>"provincia","data-reload"=>"distrito"));
		if($idubigeo) {
			$combobox->addItem($this->ubigeo->get_provincia($idubigeo));
			$combobox->setSelectedOption(substr_replace($idubigeo, "00", 4));
		}
		$data["provincia"] = $combobox->getObject();
		
		// combo distritos
		$combobox->init();
		$combobox->setAttr(array("data-name"=>"distrito","data-reload"=>""));
		if($idubigeo) {
			$combobox->addItem($this->ubigeo->get_distrito($idubigeo));
			$combobox->setSelectedOption($idubigeo);
		}
		$data["distrito"] = $combobox->getObject();
		
		// js
		$this->js('form/ubigeo/modal');
		
		return $this->load->view("ubigeo/modal", $data, true);
	}
	
	public function get_idtipo_movimiento($tabla, $motivo=NULL) {
		switch(strtolower($tabla)) {
			case "recep":
			case "comp":
			case "compra":
				return 2;
			case "vent":
			case "venta":
				return 1;
			case "notacredito":
				return 24;
		}
		
		return 99;
	}
	
	public function get_next_correlativo($tabla) {
		$sql = "SELECT * FROM almacen.tipo_movimiento WHERE tipo_movimiento=".$this->get_idtipo_movimiento($tabla);
		$query = $this->db->query($sql);
		$row = $query->row_array();
		return intval($row["correlativo"]);
	}
	
	public function after_before_value($model,$key_val){
		if(!empty($key_val)){
			$sql = "SELECT ".$model->get_table_name(). " valores FROM ".$model->get_schema().".".$model->get_table_name()." WHERE ".$model->get_pk()."='{$key_val}' ;";
			$query =$this->db->query($sql);
			$row = $query->row_array();
			return $row['valores'];
		}else{
			return null;
		}
	}
	
	public function Save_log($controller = '',$accion ='',$model, $coduser='', $pk_value='', $old_value="" , $new_value="",$ide=''){
		// if(!empty($old_value) && $old_value!='ELIMINAR'){
			// $accion = 'EDITAR';
		// }
	
		 // $this->load_model('auditoria.tabla_log');
         // $fields['direccion_ip']  	= 	$_SERVER['REMOTE_ADDR'];
         // $fields['fecha_registro'] 	= 	date("Y-m-d");
         // $fields['hora_registro'] 	= 	date("H:i:s");
         // $fields['controller']		= 	$controller;
         // $fields['accion'] 			= 	$accion;
         // $fields['esquema'] 		= 	$model->get_schema();
         // $fields['tabla'] 			= 	$model->get_table_name();
         // $fields['pk_tabla'] 		= 	$model->get_pk();
         // $fields['old_value'] 	= 	$old_value;
         // $fields['new_value'] 	= 	(!empty($new_value)) ? $new_value : $this->after_before_value($model,$pk_value);
         // $fields['idusuario'] 		= 	$coduser;
         // $fields['pk_value'] 		= 	$pk_value;
         // $fields['identificador'] 	= 	(!empty($ide)) ? $ide : RandomString();
         // $fields['idsucursal'] 		= 	$this->get_var_session("idsucursal");
         // $fields['estado'] 			= 	'A';
         // $this->tabla_log->insert($fields);
         // return true;
	}
	
	public function update_correlativo($idtipodocumento, $serie, $idsucursal = false) {
		if($idsucursal === false) {
			$idsucursal = $this->get_var_session("idsucursal");
		}
		
		if( ! isset($this->tipo_documento)) {
			$this->load_model("tipo_documento");
		}
		$this->tipo_documento->find($idtipodocumento);
		
		if($this->tipo_documento->get("genera_correlativo") == 'S') {
			if( ! isset($this->serie_documento)) {
				$this->load_model("serie_documento");
			}
			
			$this->serie_documento->find(array("idsucursal"=>$idsucursal, "idtipodocumento"=>$idtipodocumento, "serie"=>$serie));
			$correlativo = intval($this->serie_documento->get("correlativo")) + 1;
			$this->serie_documento->set("correlativo", $correlativo);
			
			$this->serie_documento->update();
		}
	}
	
	public function has_stock($idproducto, $idunidad = NULL, $cantidad = NULL, $idalmacen = NULL) {
		$props = array(
			"idproducto" => $idproducto
			,"idunidad" => $idunidad
			,"cantidad" => $cantidad
			,"idalmacen" => $idalmacen
		);
		
		if(is_array($idproducto)) {
			$props = array_intersect_key($idproducto, $props);
		}
		
		extract($props);
		
		if( ! isset($this->producto)) {
			$this->load_model("producto");
		}
		if( ! isset($this->producto_unidad)) {
			$this->load_model("compra.producto_unidad");
		}
		
		$temp = $this->producto_unidad->find(array("idproducto"=>$idproducto, "idunidad"=>$idunidad));
		$stock_unidad = $this->producto->stock($idproducto, $idalmacen);
		
		if($temp["cantidad_unidad_min"] > 0) {
			$stock_unidad = $stock_unidad / $temp["cantidad_unidad_min"];
		}
		
		if($stock_unidad >= $cantidad) {
			return true;
		}
		
		return $stock_unidad;
	}
	
	public function destroy_hojaruta($idcredito=0, $idventa=0, $idcobrador=0){
		//eliminamos la hoja de ruta de cobranzas
		$this->load_model("credito.credito");
		$this->credito->find($idcredito);
		
		$idsucursal = $this->credito->get("idsucursal");
		$idventa	= (!empty($idventa)) ? $idventa : $this->credito->get("idventa");
		// $this->db->query("DELETE FROM cobranza.hoja_ruta WHERE idcredito='{$idcredito}' AND idventa='{$idventa}' AND idsucursal='{$idsucursal}';");
		$this->db->query("DELETE FROM cobranza.hoja_ruta WHERE idventa='{$idventa}' AND idsucursal='{$idsucursal}';");
	}
	
	public function has_comprobante($tabla, $idtipodocumento, $serie, $numero, $estado = NULL) {
		$conf = array(
			"reciboingreso" => array("model"=>"venta.reciboingreso")
			,"reciboegreso" => array("model"=>"venta.reciboegreso")
			,"venta" => array("model"=>"venta.venta")
			,"notacredito" => array("model"=>"venta.notacredito")
			,"guia_remision" => array("model"=>"almacen.guia_remision")
		);
		
		if(array_key_exists($tabla, $conf)) {
			$idsucursal = $this->get_var_session("idsucursal");
			
			$sql = "SELECT count(o.*) as cantidad 
				FROM ".$conf[$tabla]["model"]." o
				INNER JOIN venta.tipo_documento t ON t.idtipodocumento = o.idtipodocumento AND t.genera_correlativo = 'S'
				WHERE o.idsucursal = '{$idsucursal}' AND o.idtipodocumento = '{$idtipodocumento}'
				AND o.serie = '{$serie}'";
			
			if($tabla == "venta") {
				$sql .= " AND o.correlativo = '{$numero}'";
			}
			else {
				$sql .= " AND o.numero = '{$numero}'";
			}
			
			if($estado !== NULL) {
				$sql .= " AND o.estado = '{$estado}'";
			}
			$query = $this->db->query($sql);
			
			if($query->num_rows() > 0) {
				return ($query->row()->cantidad > 0);
			}
		}
		
		return false;
	}
	
	public function es_electronico($idtipodoc, $serie = FALSE) {
		$query = $this->db->where("idtipodocumento", $idtipodoc)->get("venta.tipo_documento");
		$documento = $query->row_array();
		
		if($serie === FALSE)
			return ($documento["facturacion_electronica"] == "S");
		
		$doc = substr($serie, 0, 1);
		$arr = array("F", "B");
		
		return ($documento["facturacion_electronica"] == "S" && in_array($doc, $arr));
	}
	
	public function is_valid_doc($idtipodoc, $serie, $idcliente=0, $total=0, $idmoneda=1) {
		$query = $this->db->where("idtipodocumento", $idtipodoc)->get("venta.tipo_documento");
		$documento = $query->row_array();
		
		if($documento["facturacion_electronica"] != "S") // comprobante no es electronico
			return true;
		
		if(empty($idmoneda))
			return "Indique el tipo de moneda para esta operacion";
		
		$query = $this->db->where("idmoneda",$idmoneda)->get("general.moneda");
		$moneda = $query->row_array();
		
		if(empty($moneda["abreviatura"]))
			return "Ingrese el codigo de la moneda segun Catalogo Nro 2";
		
		if(strlen($moneda["abreviatura"]) != 3)
			return "El codigo de la moneda debe tener 3 caracteres. Consultar ISO 4217, codigo de divisas";
		
		if(empty($documento["codsunat"]))
			return "Ingrese el Codigo Sunat del tipo de comprobante";
		
		if(strlen($serie) != 4)
			return "La serie del comprobante debe tener 4 caracteres";
		
		if($documento["codsunat"] == "01") { // factura electronica
			if( ! starts_with($serie, "F"))
				return "La serie del comprobante debe empezar con la letra F";
		}
		
		if($documento["codsunat"] == "03") { // boleta electronica
			if( ! starts_with($serie, "B"))
				return "La serie del comprobante debe empezar con la letra B";
		}
		
		$monto = floatval($this->get_param("min_monto_electr", 350));
		if($documento["codsunat"] == "01" || floatval($total) >= $monto) {
			if(empty($idcliente)) {
				if($documento["codsunat"] == "01") {
					return "Indique el cliente para la Factura.";
				}
				return "Indique el cliente para esta operacion, porque el monto supera los S/. ".number_format($monto,2);
			}
			
			$query = $this->db->where("idcliente",$idcliente)->get("venta.cliente");
			$cliente = $query->row_array();
			
			if(empty($cliente["nombres"]))
				return "El cliente no tiene razon social";
			
			if(empty($cliente["ruc"]) && empty($cliente["dni"]))
				return "Ingrese el DNI o RUC del cliente";
			
			if($documento["codsunat"] == "01") { // es factura
				if(empty($cliente["ruc"]))
					return "El cliente no tiene RUC";
				
				if(strlen($cliente["ruc"]) != 11)
					return "El RUC del cliente debe tener 11 caracteres";
				
				$query = $this->db->where("idcliente",$idcliente)->where("estado","A")->get("venta.cliente_direccion");
				if($query->num_rows() <= 0)
					return "El cliente no tiene direccion";
				
				return true; // por lo menos el cliente tiene RUC
			}
			
			if(strlen($cliente["dni"]) != 8)
				return "El DNI del cliente debe tener 8 caracteres";
		}
		
		return true;
	}
	
	public function extrac_rol_user($idusuario=0,$idsucursal=0,$idrol=0){
		$this->load_model("seguridad.acceso_empresa");
		$this->acceso_empresa->find(array("idusuario"=>$idusuario,"idsucursal"=>$idsucursal,"idtipoempleado"=>$idrol));
		$status = $this->acceso_empresa->get("estado");
		return (!empty($status)) ? $status : "I";
	}
	
	public function lista_usuario($verificar = FALSE){
		$idsucursal = $this->get_var_session("idsucursal",0);
		$q=$this->db->query("	SELECT distinct u.idusuario,u.user_nombres,u.nombres,u.avatar,u.usuario 
								,COALESCE(suc.descripcion,'-') sucursal
								,CASE WHEN s.id IS NULL THEN 'N' ELSE 'S' END online
								FROM seguridad.view_usuario u
								JOIN seguridad.acceso_empresa au ON au.idusuario=u.idusuario AND au.estado='A' AND au.idperfil IS NOT NULL
								LEFT JOIN auditoria.sesion s ON s.idusuario=u.idusuario AND s.estado='A'
								LEFT JOIN seguridad.sucursal suc ON s.idsucursal=suc.idsucursal
								WHERE u.baja='N' 
								AND u.estado='A' 
								AND COALESCE(u.usuario,'')<>'' 
								AND u.idusuario<>{$this->get_var_session("idusuario")}
								ORDER BY nombres ASC 
								");
		if($verificar === FALSE)
			return $q->result_array();
		
		$ruta = "app/img/usuario/";
		$r = array();
		foreach($q->result_array() as $v) {
			$photo = $ruta."anonimo.png";
			if( ! empty($v["avatar"]) && file_exists($ruta.$v["avatar"])) {
				$photo = $ruta.$v["avatar"];
			}
			$r[] = array(
				"key" => $v["idusuario"]
				,"fullname" => $v["user_nombres"]
				,"shortname" => $v["nombres"]
				,"icon" => base_url($photo)
				,"nick" => $v["sucursal"]
			);
		}
		return $r;
	}
	
	public function verificar_visita_cobrador($idcredito=0, $idventa=0, $id_recibo=0){//preguntar como sera la verificacion
		$this->load_model("cobranza.visita");
		$status = $this->visita->find(array("idcredito"=>$idcredito,"idventa"=>$idventa, "fecha_visita"=>date("Y-m-d"),"estado"=>'A'));
		return $status;
	}
	
	public function destroy_liquidacion_visita($idcredito=0,$idventa=0, $id_recibo=0){//cuando se anula un pago y esta asignado a un cobrador
		$this->load_model("cobranza.liquidacion_visita");
		
		$this->liquidacion_visita->find(array("idcredito"=>$idcredito,"idventa"=>$idventa, "id_recibo"=>$id_recibo));
		$this->liquidacion_visita->set("estado", 'I');
		$this->liquidacion_visita->update();
	}
	
	public function is_valid_doc_nota($idtipodoc, $serie, $idtipodoc_ref, $serie_ref, $idcliente=0, $total=0, $idmoneda=1) {
		$query = $this->db->where("idtipodocumento", $idtipodoc)->get("venta.tipo_documento");
		$documento = $query->row_array();
		
		if($documento["facturacion_electronica"] != "S") // comprobante no es electronico
			return true;
		
		$doc_mod = substr($serie, 0, 1);
		if($doc_mod != "F" && $doc_mod != "B") // el comprobante es electronico pero se emite otra serie
			return true;
		
		if(strlen($serie) != 4)
			return "La serie del comprobante debe tener 4 caracteres";
		
		// validamos datos de la moneda
		if(empty($documento["codsunat"]))
			return "Ingrese el Codigo Sunat del tipo de comprobante";
		
		if(empty($idmoneda))
			return "Indique el tipo de moneda para esta operacion";
		
		$query = $this->db->where("idmoneda",$idmoneda)->get("general.moneda");
		$moneda = $query->row_array();
		
		if(empty($moneda["abreviatura"]))
			return "Ingrese el codigo de la moneda segun Catalogo Nro 2";
		
		if(strlen($moneda["abreviatura"]) != 3)
			return "El codigo de la moneda debe tener 3 caracteres. Consultar ISO 4217, codigo de divisas";
		
		// validamos datos del comprobante que modifica
		if(strlen($serie_ref) != 4)
			return "La serie del comprobante que modifica debe tener 4 caracteres";
		
		$query = $this->db->where("idtipodocumento", $idtipodoc_ref)->get("venta.tipo_documento");
		$documento = $query->row_array();
		
		if($doc_mod == "F") { // estamos modificando una factura
			if($documento["codsunat"] != "01")
				return "El documento que modifica debe ser una Factura, verifique el Codigo Sunat del tipo de comprobante";
			
			if( ! starts_with($serie_ref, "F"))
				return "La serie del comprobante que modifica debe empezar con la letra F";
		}
		
		if($doc_mod == "B") { // estamos modificando una boleta
			if($documento["codsunat"] != "03")
				return "El documento que modifica debe ser una Boleta, verifique el Codigo Sunat del tipo de comprobante";
			
			if( ! starts_with($serie_ref, "B"))
				return "La serie del comprobante que modifica debe empezar con la letra B";
		}
		
		$monto = $this->get_param("min_monto_electr", 350);
		// validamos datos del cliente segun sea el caso
		if($documento["codsunat"] == "01" || floatval($total) >= $monto) { // doc modifica es factura o total supera los 350
			if(empty($idcliente)) {
				if($documento["codsunat"] == "01") {
					return "Indique el cliente para la Factura.";
				}
				return "Indique el cliente para esta operacion, porque el monto supera los S/. ".number_format($monto,2);
			}
			
			$query = $this->db->where("idcliente",$idcliente)->get("venta.cliente");
			$cliente = $query->row_array();
			
			if(empty($cliente["nombres"]))
				return "El cliente no tiene razon social";
			
			if(empty($cliente["ruc"]) && empty($cliente["dni"]))
				return "Ingrese el DNI o RUC del cliente";
			
			if($documento["codsunat"] == "01") { // es factura
				if(empty($cliente["ruc"]))
					return "El cliente no tiene RUC";
				
				if(strlen($cliente["ruc"]) != 11)
					return "El RUC del cliente debe tener 11 caracteres";
				
				$query = $this->db->where("idcliente",$idcliente)->where("estado","A")->get("venta.cliente_direccion");
				if($query->num_rows() <= 0)
					return "El cliente no tiene direccion";
				
				return true; // por lo menos el cliente tiene RUC
			}
			
			if(strlen($cliente["dni"]) != 8)
				return "El DNI del cliente debe tener 8 caracteres";
		}
		
		return true;
	}
	
	public function send_to_facturador($tabla, $idtabla, $idsucursal=FALSE,$fixed=2) {
		if( ! isset($this->jfacturacion))
			$this->load->library('jfacturacion');
		
		// guardamos los datos en la bd local para cualquier referencia
		if( ! isset($this->facturacion))
			$this->load_model("venta.facturacion");
		
		// $this->facturacion->fixed = $fixed;
		
		$this->facturacion->set("idreferencia", $idtabla);
		$this->facturacion->set("referencia", $tabla);
		$this->facturacion->set("estado", "A");
		$this->facturacion->set("fecha", date("Y-m-d"));
		$this->facturacion->set("actualizado", 0);
		$this->facturacion->text_uppercase(false);
		$this->facturacion->save(null, false);
		
		// creamos los archivos para el facturador
		$datos = $this->jfacturacion->crear_files($tabla, $idtabla, $idsucursal,$fixed);
		if($datos !== FALSE) {
			// enviamos los archivos al facturador
			$res = $this->jfacturacion->enviar_files($datos);
			if($res !== false) {
				if($res != "failed" && $res != "ok") {
					// recibimos datos del facturador
					$arr = json_decode($res, true);
					$datos = array_merge($datos, $arr);
				}
				
				$this->facturacion->set($datos);
				$this->facturacion->text_uppercase(false);
				$this->facturacion->update();
			}
		}
	}
	
	public function update_to_facturador($tabla, $idtabla, $idsucursal=FALSE,$fixed=2) {
		if( ! isset($this->jfacturacion))
			$this->load->library('jfacturacion');
		
		// guardamos los datos en la bd local para cualquier referencia
		if( ! isset($this->facturacion))
			$this->load_model("venta.facturacion");
		
		$this->load_model("venta.{$tabla}");
		$this->load_model("venta.tipo_documento");
		
		$datosfac = $this->facturacion->find(array("idreferencia"=>$idtabla, "referencia"=>$tabla));
		$datosref = $this->{$tabla}->find($idtabla);
		$datosdoc = $this->tipo_documento->find($datosref["idtipodocumento"]);
		
		// actualizamos el estado del comprobante
		$res = $this->jfacturacion->get_estado($datosfac["nom_arch"]);
		if($res != "ok" && $res != "failed") {
			$datos = json_decode($res, true);
			$this->facturacion->set($datos);
			$this->facturacion->text_uppercase(false);
			$this->facturacion->update(null);
			$datosfac = $this->facturacion->get_fields();
		}
		
		// verificamos si se trata de otro archivo
		if($datosfac["tipo_doc"] != $datosdoc["codsunat"] || $datosfac["serie"] != $datosref["serie"] || 
		intval($datosfac["numero"]) != intval($datosref["correlativo"])) {
			$datosfac["ind_situ"] = "01";
		}
		
		if(in_array($datosfac["ind_situ"], array("01","02","05","06","07","10"))) {
			// eliminamos el archivo enviado anteriormente
			$this->jfacturacion->delete($datosfac["nom_arch"]);
			
			// creamos los archivos para el facturador
			$datos = $this->jfacturacion->crear_files($tabla, $idtabla, $idsucursal,$fixed);
			if($datos !== FALSE) {
				// enviamos los archivos al facturador
				$res = $this->jfacturacion->enviar_files($datos);
				
				if($res !== false) {
					if($res != "failed" && $res != "ok") {
						// recibimos datos del facturador
						$arr = json_decode($res, true);
						$datos = array_merge($datos, $arr);
					}
					
					$this->facturacion->set($datos);
					$this->facturacion->text_uppercase(false);
					$this->facturacion->update();
				}
			}
		}
	}
	
	public function validar_datos_facturador($tabla, $idtabla, $idsucursal=FALSE) {
		if( ! isset($this->jfacturacion))
			$this->load->library('jfacturacion');
		
		if( ! $this->jfacturacion->is_ref($tabla))
			return "No existe la referencia del comprobante. Esta seguro que el comprobante es electronico";
		
		if( ! isset($this->facturacion))
			$this->load_model("venta.facturacion");
		
		// comprobamos si los archivos han sido enviados a la carpeta DATA
		$datos_fact = $this->facturacion->find(array("idreferencia"=>$idtabla, "referencia"=>$tabla));
		if($datos_fact == null) {
			$this->send_to_facturador($tabla, $idtabla, $idsucursal);
		}
		else if(empty($datos_fact["ind_situ"])) {
			$this->send_to_facturador($tabla, $idtabla, $idsucursal);
		}
		
		// aqui se supone que los archivos estan en la carpeta DATA
		// ahora verificamos si aun no se ha generado el comprobante (xml)
		
		$datos_fact = $this->facturacion->find(array("idreferencia"=>$idtabla, "referencia"=>$tabla));
		
		if(in_array($datos_fact["ind_situ"], array("01","06","07","10"))) {
			// se trata de un archivo (registro) nuevo o con error, creamos el comprobante
			$res = $this->jfacturacion->crear_comprobante($tabla, $idtabla);
			
			if($res !== FALSE) {
				if($res != "failed" && $res != "ok") {
					$datos = json_decode($res, true);
					$this->facturacion->set($datos);
					$this->facturacion->text_uppercase(false);
					$this->facturacion->update(null);
				}
			}
			else {
				return "No se ha podido conectar con el Facturador Sunat, o el archivo contiene errores. 
					Verifique en el modulo de Facturacion";
			}
		}
		
		// lo que sigue aqui debajo se hara desde otro modulo
		/* if($this->facturacion->get("ind_situ") == "02" || $this->facturacion->get("ind_situ") == "10") {
			$res = $data_resp = $this->jfacturacion->enviar_comprobante($tabla, $idtabla);
			if($res !== FALSE) {
				if($res != "failed" || $res != "ok") {
					$datos = json_decode($res, true);
					$this->facturacion->set($datos);
					$this->facturacion->text_uppercase(false);
					$this->facturacion->update(null);
				}
			}
			else {
				return "No se ha podido enviar el comprobante a Sunat. consulte el modulo de facturacion";
			}
		} */
		
		return true;
	}
	
	public function detalle_impresion($id,$operacion=''){
		$this->load->library("jfacturacion");
		$tipo_igv_oferta 	= $this->jfacturacion->tipo_igv_oferta;
		$tipo_igv_default 	= $this->jfacturacion->tipo_igv_default;
		$grupo_igv_default 	= $this->jfacturacion->grupo_igv_default;
		
		if($operacion=='venta'){
			$query = $this->db->query("	SELECT 
								dv.idproducto
								,CAST(dv.cantidad AS numeric(10,2)) cantidad
								,um.codsunat um
								,COALESCE(a.abreviatura||'-'||dv.descripcion,'') detalle
								,CAST(dv.precio AS numeric(10,2)) precio 
								,CAST(dv.precio*dv.cantidad AS numeric(10,2)) importe
								,dv.oferta
								,case when dv.oferta='S' then 0.00 else dv.precio end as valor_unit
								,case when dv.oferta='S' then 0.00 else 0.00 end as sum_dscto
								,case when dv.oferta='S' then 0.00 else dv.precio*dv.igv*dv.cantidad end as sum_igv
								,case when dv.oferta='S' then 0.00 else dv.precio*(1+dv.igv) end as precio_venta
								,case when dv.oferta='S' then 0.00 else dv.precio*dv.cantidad end as valor_venta
								,dv.precio as pu_real
								,array_to_string(array_agg(s.serie), ', '::text) AS serie
								,coalesce(dv.codgrupo_igv, '".$grupo_igv_default."') as codgrupo_igv
								,case when dv.codtipo_igv is not null then dv.codtipo_igv else
									case when dv.oferta='S' then '".$tipo_igv_oferta."'::text else 
										case when coalesce(dv.igv,0)>0 then '10'::text else '".$tipo_igv_default."'::text end
									end
								end as tipo_igv
								,0.00 descuento
								FROM venta.detalle_venta dv 
								JOIN compra.unidad um ON um.idunidad=dv.idunidad
								JOIN almacen.almacen a ON a.idalmacen = dv.idalmacen
								LEFT JOIN venta.detalle_venta_serie s on s.iddetalle_venta=dv.iddetalle_venta and s.idventa=dv.idventa
								WHERE dv.idventa=$id AND dv.estado='A'
								GROUP BY dv.idproducto,dv.cantidad,um,detalle,precio,dv,oferta,dv.igv,dv.codgrupo_igv,dv.idventa,dv.codtipo_igv
								ORDER BY (ROW_NUMBER() OVER (ORDER BY dv.idventa));
						");
						
			return $query->result_array();
		}else if($operacion=='notacredito'){
			$query=$this->db->query("	SELECT
								dn.idproducto
								,CAST(dn.cantidad AS numeric(10,2)) cantidad
								,um.codsunat um
								,dn.descripcion detalle
								,CAST(dn.precio AS numeric(10,2)) precio 
								,CAST(dn.precio*dn.cantidad AS numeric(10,2)) importe
								,COALESCE(dn.codgrupo_igv, '".$grupo_igv_default."') as codgrupo_igv
								,dn.precio*dn.cantidad as valor_venta
								,dn.precio*dn.igv*dn.cantidad as sum_igv
								, 'N'::text as oferta
								,0.00 descuento
								,dn.serie
								FROM venta.detalle_notacredito dn
								JOIN compra.unidad um ON um.idunidad=dn.idunidad
								WHERE dn.idnotacredito=$id AND dn.estado='A';");
			return $query->result_array();
		}else {
			return array();
		}
	}
	
	public function unlimit() {
		ini_set('memory_limit', '-1');
		set_time_limit(0);
	}
	
	public function imprimir_formato($id, $tabla="venta", $esquema="venta",$print_test=false,$fixed = 2,$dest="") {
		$this->unlimit();
		
		$this->load_model(array( "venta.venta_view","venta.tipo_documento","venta.notacredito_view","venta.facturacion"));
		$sql = "SELECT idtipodocumento, idsucursal FROM {$esquema}.{$tabla} WHERE id{$tabla}=?";
		$query = $this->db->query($sql, array($id));
		$row = $query->row_array();
		$cdp = $this->tipo_documento->find($row["idtipodocumento"]);
		
		$fe = $this->get_param("facturacion_electronica");
		
		// verificamos si el comprobante es electronico
		if($cdp["facturacion_electronica"] != "S" || $fe != "S") {
			echo "El comprobante de pago no es electronico";
			return;
		}
		
		/* verificamos datos necesarios para la generacion del pdf */
		if(!$print_test){//Este if es para omitir la restriccion de la impresion, para generar el pdf sin mandar al facturador
			$estado = $this->validar_datos_facturador($tabla, $id, $row["idsucursal"]);
			if($estado !== true) {
				echo $estado;
				return;				
			}
		}
		
		/* verificamos si el existe el valor resumen */
		$datos = $this->facturacion->find(array("idreferencia"=>$id, "referencia"=>$tabla));
		/* COMENTE ESTO PARA HACER FUNCIONAR LA IMPRESION SDE FACTURAS.WScL
		if(empty($datos["resumen_value"])) {//COMENTAR ESTO PARA EL USO REAL, LO COMENTE PARA HACER PRUEBAS
			if(!$print_test){//Este if es para omitir la restriccion de la impresion, para generar el pdf sin mandar al facturador
				echo "No se ha podido obtener el Valor Resumen. Por favor actualice esta pagina (Presione F5).
					Si el problema persiste, realice esta operacion por el modulo de Facturacion.<br><br>".
					$this->facturacion->get("des_obse");
				return;
			}
		}*/
		
		$head_data = array();
		$_REQUEST['tabla'] = $tabla;
		
		if($tabla == "venta") {
			$head_data = $this->venta_view->find(array("idventa"=>$id));
		}
		else if($tabla == "notacredito") {
			$head_data = $this->notacredito_view->find(array("idnotacredito"=>$id));
		}
		
		// echo "<pre>";print_r($this->facturacion->get_fields());echo "</pre><br>";
		// echo "HASH: ".$this->facturacion->get("resumen_value");
		return $this->formato_personalizado($id, $head_data,$datos, $this->detalle_impresion($id, $tabla),$fixed,$dest);
	}
	
	public function formato_personalizado($id=0, $head_data=array(),$data=array(), $detalle=array(),$fixed = 2, $dest = ""){
		// sobreescribir esta funcion si no se implementa codigo unico
		// echo "Imprimiendo formato personalizado para Facturacion Electronica...";
		$this->load->library("pdf");
		$this->load->library('numeroLetra');

		if(empty($data['serie']) || !isset($data['serie']))
			$data['serie']=$head_data['serie'];
		
		if(empty($data['numero']))
			$data['numero']=$head_data['correlativo'];
		
		if(empty($data['resumen_value']))
			$data['resumen_value']='';

		if(empty($head_data['tipodocumento']))
			$head_data['tipodocumento']='';
		
		if(empty($head_data['descuento']))
			$head_data['descuento'] = 0;

		if(empty($detalle)){
			$detalle = $this->detalle_impresion($id,'venta'); // venta?, si fuera nota de credito
		}
		
		$this->load_model(array( "seguridad.empresa","seguridad.sucursal","seguridad.view_usuario","venta.venta_view","venta.tipo_documento","venta.facturacion"));
		if( ! empty($head_data["idsucursal"]))
			$this->sucursal->find($head_data["idsucursal"]);
		else
			$this->sucursal->find($this->get_var_session("idsucursal"));
		$this->empresa->find($this->sucursal->get("idempresa"));

		if(empty($data)){
			$data=$this->facturacion->find(array("idreferencia"=>$id,"referencia"=>"venta"));
		}
		
		$logo = $this->empresa->get("logo");
		if(!empty($logo) && file_exists(FCPATH."app/img/empresa/".$logo))
			$this->pdf->SetLogo(FCPATH."app/img/empresa/".$logo);
		
		//$this->pdf->useFoot = FALSE;
		/*$this->pdf->ver_resolucion_sunat	= false;
		$this->pdf->ver_web					= false;
		$this->pdf->resolucion_sunat = $this->get_param('resolucion_sunat');
		$this->pdf->referencia_web	 = "PERNOS DEL ORIENTE ".$this->get_param("url_consulta_eletronica");*/
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);
		$this->pdf->setFillColor(249, 249, 249);
        $this->pdf->SetDrawColor(204, 204, 204);

		$label_doc="DNI/RUC";

		if(!empty($_REQUEST['tabla'])&&$_REQUEST['tabla']=='venta'){
			if($head_data['ruc_obligatorio']=='S'){//
				$label_doc="R.U.C.";
			}else if($head_data['dni_obligatorio']=='S'){
				$label_doc="D.N.I.";
			}else{
				$venta_v["ruc"] = '';//
			}
		}
		
		$this->pdf->AddPage('P','a4');
		$this->pdf->SetFont('Arial','',9);
		$alto_comprob=7;
		$this->pdf->Cell(38,$alto_comprob,'','0',0,'L');
		$this->pdf->SetFont('Arial','B',15);
		$this->pdf->SetTextColor(19,92,185);
		$this->pdf->Cell(90,$alto_comprob,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->SetTextColor(0,0,0);
		$this->pdf->Cell(2,4,'',0,0,'L');
		$this->pdf->SetFont('Arial','',15	);
		$this->pdf->Cell(65,$alto_comprob,utf8_decode('R.U.C. N° ').$this->empresa->get("ruc"),'LTR',1,'C');
		
		$this->pdf->Cell(42,4,'',0,0,'L');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell(70,4,"".$this->empresa->get("direccion").$this->sucursal->get("telefono"),0,0,'L');
			$this->pdf->Cell(18,4,'',0,0,'L');
		$this->pdf->SetFont('Arial','',10);
		$this->pdf->Cell(65,4,$this->tipo_documento->get('descripcion'),'LR',1,'C',true);
		
		
		$this->pdf->SetFont('Arial','',8);		
		$this->pdf->Cell(67,4,'',0,0,'L');		
		$this->pdf->SetTextColor(19,92,185);
		$this->pdf->Cell(63,4,"TOCACHE SAN MARTIN".utf8_decode($this->empresa->get("descripcionuno")),0,0,'L');		
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell(65,4,'','LR',1,'C');
		
		$this->pdf->Cell(65,$alto_comprob,'',0,0,'L');		
		$this->pdf->Cell(65,5,"gerenciagacepat@gmail.com".utf8_decode($this->empresa->get("descripciondos")),0,0,'L');
		$this->pdf->SetTextColor(0,0,0);
		$this->pdf->Cell(65,4,$data['serie']."-".$data['numero'],'LBR',1,'C');
		
		//$this->pdf->Cell(125,5,"Establecimiento Comercial: ".ucwords(strtolower(utf8_decode($head_data["empresa_dir"])))." - Telf. ".utf8_decode($head_data["empresa_tel"]),0,1,'C');
		
		
		$this->pdf->Ln(5);
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(20,5,utf8_decode('SEÑOR(ES)'),0,0,'L');
		$this->pdf->Cell(2,5,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(145,5,utf8_decode($head_data["full_nombres"]),0,0,'L');
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(13,5,$label_doc,0,0,'L');
		$this->pdf->Cell(2,5,utf8_decode(' :'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(36,5,$head_data["doc_cliente"],0,1,'L');
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(20,5,utf8_decode('DIRECCIÓN'),0,0,'L');
		$this->pdf->Cell(2,5,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(140,5,utf8_decode($head_data['direccion']),0,0,'L');		
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(20,5,'',0,0,'L');
		$this->pdf->Cell(2,5,'',0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(36,5,'',0,1,'L');
		
		$this->pdf->SetFont('Arial','B',9);
		//$this->pdf->Cell(20,6,utf8_decode('SEÑOR(ES)'),0,0,'L');
		//$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		//$this->pdf->Cell(150,6,$head_data["observacion"],0,0,'L');
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(10,6,'FECHA',0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(36,6,$head_data["fecha_venta_format"],0,1,'L');
	
	$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(10,6,'VEND',0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
	$this->pdf->SetFont('Arial','B',8);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell(15,6,$head_data["vendedor"],0,1,'L');	
		
/*
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(10,6,'FECHA',0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(36,6,$head_data["fecha_venta_format"],0,1,'L');
		*/
		//$this->pdf->SetFont('Arial','B',9);
		//$this->pdf->Cell(12,6,'FECHA',0,0,'L');
		//$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		//$this->pdf->SetFont('Arial','',9);
		//$this->pdf->Cell(66,6,$head_data["observacion"],0,1,'L');
		$formato_motivo=array("notacredito","notadebito");
		if(in_array($_REQUEST['tabla'],$formato_motivo)){
			$this->pdf->Ln();
			$this->pdf->SetFont('Arial','B',9);
			$this->pdf->Cell(48,6,utf8_decode('DOCUMENTO QUE MODIFICA'),0,0,'L');
			$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
			$this->pdf->SetFont('Arial','',8);
			$this->pdf->Cell(47,6,($head_data["documento_modifica"]),0,0,'L');
		
			$this->pdf->SetFont('Arial','B',9);
			$this->pdf->Cell(55,6,utf8_decode('SERIE Y NUMERO QUE MODIFICA'),0,0,'L');
			$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
			$this->pdf->SetFont('Arial','',8);
			$this->pdf->Cell(40,6,($head_data["comprobante_modifica"]),0,1,'L');
			$this->pdf->SetFont('Arial','B',9);
			$this->pdf->Cell(20,6,'MOTIVO',0,0,'L');
			$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
			$this->pdf->SetFont('Arial','',8);
			$this->pdf->Cell(40,6,($head_data["motivo"]),0,1,'L');
		}
		
		//$this->pdf->Ln();
		
		$this->pdf->Cell(190,0,"",1,1,'C');
		
		/* elementos array('key_colum',array('name_colum','ancho','align','salto')) */
		/* Cabecera */
		$width_cod=18;
		$width_cant=20;
		$width_um=15;
		$width_descr=102;
		$width_vu=22;
		$width_vtotal=25;
		
		$cabecera = array('idproducto'=> array('CÓDIGO',$width_cod,'R',0)
							,'cantidad' => array('CANT',$width_cant,'R',0)
							,'um' => array('UM',$width_um,'C',0)
							,'detalle' => array('DESCRIPCIÓN',$width_descr,'L',0)
							,'precio' => array('V. UNITARIO',$width_vu,'R',0)
							,'importe' => array('V. VENTA T.',$width_vtotal,'R',1)
						);
		$this->pdf->SetFont('Arial','B',8);
		
		foreach($cabecera as $f=>$b){
			$this->pdf->Cell(($b[1]),6,utf8_decode((($b[0]))),1,$b[3],'C', true);
		}
		$this->pdf->SetFont('Arial','',8);
		/* Cabecera */
		

		/* Cuerpo */
		$total_importe=0;
		$totalGra = $totalIna = $totalExo = $sumaIgv = $total_descuento = $totalOferta = 0;
		$cols = array('idproducto','cantidad','um','detalle','precio','importe');
		$pos = array("R", "R", "C", "L", "R", "R");
		$width = array($width_cod, $width_cant, $width_um, $width_descr, $width_vu, $width_vtotal);
		foreach($detalle as $k=>$v){
			// foreach($cabecera as $f=>$b){
				// $this->pdf->Cell(($b[1]),6,utf8_decode((($v[$f]))),1,$b[3],$b[2]);
			// }
			$this->pdf->SetWidths($width);
			$values = array();
			
			if($v["codgrupo_igv"] == "GRA")
				$totalGra += redondeosunat($v["valor_venta"],$fixed);
			else if($v["codgrupo_igv"] == "EXO")
				$totalExo += redondeosunat($v["valor_venta"],$fixed);
			else if($v["codgrupo_igv"] == "INA")
				$totalIna += redondeosunat($v["valor_venta"],$fixed);
				// $totalIna += $v["valor_venta"];
				
			$sumaIgv += $v["sum_igv"];
			
			if($v["oferta"] == "S") {
				$totalOferta += $v["pu_real"] * $v["cantidad"];
			}
			
			foreach($cols as $f){
				if(!empty($v['serie']) && $f=='detalle'){
					$v[$f] = $v[$f]." SERIE: ".$v['serie'];
				}else if($f=='importe'){
					$v[$f] = redondeosunat($v[$f],$fixed);
				}
				$values[] = utf8_decode((($v[$f])));
			}
			$this->pdf->Row($values, $pos, "Y", "Y");
			$total_importe = $total_importe + $v['importe'];
			$total_descuento = $total_descuento + $v['descuento'];
		}
		$importeTotal = $totalGra + $totalIna + $totalExo + $sumaIgv - $head_data['descuento'];
		/* Cuerpo */
		if(in_array($_REQUEST['tabla'],$formato_motivo)){
			if($head_data['descuento']>0)
				$importeTotal = $head_data['descuento'];
		}
		/* Pie */
		$this->pdf->SetFont('Arial','',8);
		$width_monto_descrp = $width_cant + $width_um +$width_descr;
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell($width_cod ,4,"SON : ",1,0,'R');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell($width_monto_descrp,4,$this->numeroletra->convertir(number_format($importeTotal, 2, '.', ''), true)." ".$head_data["moneda"],1,0,'L');
		$this->pdf->SetFont('Arial','B',6);
		$this->pdf->Cell($width_vu ,4,"T. DESCUENTO",1,0,'L');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell($width_vtotal ,4,number_format($total_descuento,$fixed,'.',','),1,1,'R');
		
		//aqui era vendedor
		//aqui deberua ir la cd barra
		$style = array(
			'border' => 0,
			'vpadding' => 'auto',
			'hpadding' => 'auto',
			'fgcolor' => array(0,0,0),
			'bgcolor' => false, //array(255,255,255)
			'module_width' => 1, // width of a single module in points
			'module_height' => 1 // height of a single module in points
		);
		
		// echo "<pre>";
		// print_r($data);exit;
	
		
		$code_bar = $data['num_ruc'];
		$code_bar.= "|".$data['tip_docu'];
		$code_bar.= "|".$data['serie'];
		$code_bar.= "|".$data['numero'];
		$code_bar.= "|".$head_data['igv'];
		$code_bar.= "|".$head_data['total'];
		$code_bar.= "|".$data['fecha'];
		$code_bar.= "|".$data['tip_docu_cliente'];
		$code_bar.= "|".$data['num_docu_cliente'];
		$code_bar.= "|".$data['resumen_value'];
		$code_bar.= "|".$data['resumen_firma'];
		
		$this->pdf->setFillColor(0, 0, 0);
		//$this->pdf->write2DBarcode($code_bar, 'QRCODE', 60, '',99,16,$style,'T',true);
		$this->pdf->write2DBarcode($code_bar, 'PDF417', 60, '',99,16,$style,'T',true);
			/* Parametros */
			// Codigo de barra
			// 60 = Formato
			// '' = centrar, de izquierda a derecha
			// 90 = ancho
			// 16 = altura(Y)
			//$style = estilos
			//'T' = alineacion (T,M,N,B)
			// true = distorcionar proporcion automatica del ancho
		$this->pdf->setFillColor(249, 249, 249);
		
		
		$this->pdf->SetFont('Arial','B',6);
		$this->pdf->Cell($width_vu ,4,"OP. GRAVADA",1,0,'L');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell($width_vtotal ,4,number_format($totalGra,$fixed,'.',','),1,1,'R');
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell($width_cod ,6,"",0,0,'R');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell($width_monto_descrp,6,"",0,0,'L');
		$this->pdf->SetFont('Arial','B',6);
		$this->pdf->Cell($width_vu ,4,"OP. INAFECTA",1,0,'L');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell($width_vtotal ,4,number_format($totalIna,$fixed,'.',','),1,1,'R');
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell($width_cod ,6,"",0,0,'R');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell($width_monto_descrp,6,"",0,0,'L');
		$this->pdf->SetFont('Arial','B',6);
		$this->pdf->Cell($width_vu ,4,"OP. EXONERADA",1,0,'L');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell($width_vtotal ,4,number_format($totalExo,$fixed,'.',','),1,1,'R');
		
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell($width_cod ,6,"",0,0,'R');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell($width_monto_descrp,6,"",0,0,'L');
		$this->pdf->SetFont('Arial','B',6);
		$this->pdf->Cell($width_vu ,4,"TOTAL IGV",1,0,'L');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell($width_vtotal ,4,number_format($head_data['igv'],$fixed,'.',','),1,1,'R');
		
		
		/*	$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell($width_cod ,6,"",0,0,'R');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell($width_monto_descrp,6,"",0,0,'L');
		$this->pdf->SetFont('Arial','B',6);
		*/
		
		/*
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(10,6,'VEND',0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
	$this->pdf->SetFont('Arial','B',8);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell(15,6,$head_data["vendedor"],0,1,'L');	
		*/
		
		
		//$this->pdf->Cell($width_cod ,4,"",0,0,'R');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell($width_monto_descrp+18,4,"",0,0,'L');
		
		$this->pdf->Cell($width_vu ,4,"ICBPER",1,0,'L');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell($width_vtotal ,4,number_format($head_data['igv'],$fixed,'.',','),1,1,'R');
		
		if($head_data["descuento"] > 0) {
			$this->pdf->SetFont('Arial','B',9);
			$this->pdf->Cell($width_cod ,6,"",0,0,'R');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell($width_monto_descrp,6,"",0,0,'L');
			$this->pdf->SetFont('Arial','B',6);
			$this->pdf->Cell($width_vu ,4,"DSCTO. GLOBAL",1,0,'L');
			$this->pdf->SetFont('Arial','',8);
			$this->pdf->Cell($width_vtotal ,4,number_format($head_data['descuento'],2,'.',','),1,1,'R');
			
			// if($head_data['descuento']>0)
				// $importeTotal = $head_data['descuento'];
		}
		
		
		$this->pdf->SetFont('Arial','B',9);
		//$this->pdf->Cell($width_cod ,4,"",0,0,'R');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell($width_monto_descrp,4,utf8_decode("BIENES TRANSFERIDOS EN LA AMAZONIA PARA SER CONSUMIDOS EN LA MISMA"),1,0,'L');
		
		
		
		/*$this->pdf->SetFont('Arial','B',6);
		$this->pdf->Cell($width_cod ,6,"",0,0,'R');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell($width_monto_descrp,6,"",0,0,'L');*/
		$this->pdf->SetFont('Arial','B',6);
		$this->pdf->Cell(18,4,"",0,0,'L');
		$this->pdf->Cell($width_vu ,4,"OP. GRATUITA",1,0,'L');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell($width_vtotal ,4,number_format($totalOferta,$fixed,'.',','),1,1,'R');
		
			$this->pdf->SetFont('Arial','B',9);
		//$this->pdf->Cell($width_cod ,4,"",0,0,'R');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell($width_monto_descrp,4,utf8_decode("BIENES TRANSFERIDOS / SERVICIOS PRESTAODS EN LA AMAZONIA PARA SER CONSUMIDOS EN LA MISMA"),1,0,'L');
		
		
		
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(18,4,"",0,0,'L');
		$this->pdf->Cell($width_vu ,6,"IMPORTE TOTAL",1,0,'L', true);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell($width_vtotal ,6,number_format($importeTotal,$fixed,'.',','),1,1,'R',true);


	$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell($width_monto_descrp+18,4,utf8_decode("Consulte sus documentos Electrónicos en la Página ".$this->get_param("url_consulta_eletronica")),0,0,'L');				
		
		//$this->pdf->MultiCell(202 ,3,utf8_decode("Resolución de la Sunat: 155-2017/SUNAT"),0,'J');
		//$this->pdf->Cell(86,4,utf8_decode($this->empresa->get("descripcionuno")),0,0,'L');		
		//$this->pdf->MultiCell(202 ,3,utf8_decode("Representación impresa de la boleta de venta electrónica generada desde el sistema facturador SUNAT. Puede verificarla utilizando su clave SOL"),0,'J');
		
		/* Pie */
		
		
		if(strlen($dest) > 1) {
			return $this->pdf->Output("F", $dest);
		}
		return $this->pdf->Output($dest);
	}
	
	public function imprimir_comprobante_fisico($idtipodocumento=0, $idsucursal=0, $serie=0, $reg = '', $detalle=array()){
		$sql = $this->db->query("SELECT contenido,cantidad_filas_detalle,ver_borde FROM general.formato_documento WHERE estado='A' AND idtipodocumento='$idtipodocumento' AND serie='$serie' AND idsucursal='$idsucursal';");
		// $reg 				= $sql->row('contenido');
		$cant_filas_detalle = $sql->row('cantidad_filas_detalle');
		$ver_borde  	 	= $sql->row("ver_borde");
		
		$border = 'none';
		if($ver_borde=='S'){
			$border = '0.5px solid #ccc';
		}
		if(!empty($reg)){
			$this->load_model('general.formato_documento');
			$this->formato_documento->find(array("idtipodocumento"=>$idtipodocumento,"idsucursal"=>$idsucursal,"serie"=>$serie));
			
			$dato_detalle=0;
			foreach($detalle as $k=>$v){
				foreach($v as $key=>$val){
					$extend = explode(":::",$val);
					$reg=str_replace("{".$key.$extend[0]."}",$extend[1],$reg);
				}
				$dato_detalle++;
			}

			for($xy=($dato_detalle + 1);$xy<=$cant_filas_detalle;$xy++){
				foreach($v as $key=>$val){
					$reg=str_replace("{".$key.$xy."}",'',$reg);
				}
			}
				
			echo "<style>";
			echo "	.panel-body{border:0px solid black;}";
			echo "";
			echo "@media print,screen{
				@page{
					margin: 0;
					size: ".$this->formato_documento->get('width')." ".$this->formato_documento->get('height')."
				}
				*{
					margin: 0px;font-family: ".$this->formato_documento->get('fuente_letra').";font-size:".$this->formato_documento->get('font_size').";
				}
				#content{width:".$this->formato_documento->get('width').";height:".$this->formato_documento->get("height").";border:0px solid #ccc; }
				table td,table{border:$border !important;}
				table thead tr td{border:none !important;}
				table{border-top: 0px !important;border-left: 0px !important;border-right: 0px !important;}
			}";

			echo "</style>";
			echo "<div id='content'>".$reg."</div>";
			// echo "<script>window.print();</script>";
			// echo "<script>window.close();</script>";
			
		}else{
			echo "Error, formato no definido :'(";
		}
	}
	
	public function genera_hojaruta(){
		$estados_creditos	= $this->creditos_cartera();
		$fields				= $this->input->post();
		if (empty($estados_creditos))
			$estados_creditos = array('0');
		
		if(empty($fields['idcobrador']))
			$fields['idcobrador'] = $this->get_var_session("idusuario");
		
		if(empty($fields['idsucursal']))
			$fields['idsucursal'] = $this->get_var_session("idsucursal");
		
		$zonas_cobrador		= $this->data_zona_cobrador($fields);
		$q = "	SELECT 
				initcap(trim(COALESCE(trim(cli.apellidos)||' ','')||trim(cli.nombres))) cliente 
				,c.nro_credito
				,cli.idzona 
				,v.idvendedor idempleado
				,c.idsucursal
				,c.idcredito 
				,c.idventa 
				,v.idvendedor idcobrador
				,c.idgarante
				,cli.idcliente 
				,zona.zona
				,CAST('A' AS text) estado
				,COALESCE(zc.orden,0) orden
				FROM credito.credito c JOIN venta.cliente cli ON cli.idcliente=c.idcliente 
				LEFT JOIN general.zona ON zona.idzona=cli.idzona 
				JOIN venta.venta v ON v.idventa=c.idventa
				JOIN cobranza.zona_cobrador zc ON zc.idzona=cli.idzona AND zc.idsucursal=c.idsucursal AND zc.estado='A' AND zc.idempleado={$fields['idcobrador']}
				WHERE c.estado != 'I' AND c.pagado='N'
				AND cli.idzona IN (SELECT h.idzona FROM cobranza.zona_cobrador h WHERE h.idsucursal='{$fields['idsucursal']}' AND h.estado='A' AND h.idempleado={$fields['idcobrador']} )
				AND c.idcredito NOT IN (SELECT h.idcredito FROM cobranza.hoja_ruta h WHERE h.idsucursal='{$fields['idsucursal']}' AND h.idcredito=c.idcredito AND h.idventa=c.idventa AND h.estado='A' AND h.idcobrador={$fields['idcobrador']} )
				AND c.id_estado_credito IN ('".implode("','", $estados_creditos)."')
				AND c.idsucursal={$fields['idsucursal']}
				;";
		// echo $q;exit;
		$sql=$this->db->query($q);
		
		$data = $sql->result_array();
		//Aqui guardamos la asignacion a la cartera de cobranzas

		$this->load_model("cobranzas.hoja_ruta");
		$this->db->trans_start(); // inciamos transaccion
		$data_c = array();
		foreach($data as $k=>$v){
			$data_c['idzona']		= trim($v['idzona']);
			if(empty($data_c['idzona'])){
				$data_c['idzona']	= null;
			}else{
				$dat_z = $this->seleccion_array($zonas_cobrador,$data_c['idzona'],'idzona');
				if(!empty($dat_z)){
					$v["orden"]			= $dat_z[0]['orden'];
				}
			}
			$data_c['idempleado']	= $v['idempleado'];
			$data_c['idsucursal']	= $v['idsucursal'];
			$data_c['idcredito']	= $v['idcredito'];
			$data_c['idventa']		= $v['idventa'];
			$data_c['idcobrador']	= $fields['idcobrador'];
			$data_c['idgarante']	= $v['idgarante'];
			$data_c['idcliente']	= $v['idcliente'];

			$data_c['orden']		= $v['orden'];
			$data_c['estado']		= $v['estado'];
			$this->hoja_ruta->insert($data_c);
		}
		$this->db->query("	UPDATE cobranza.hoja_ruta SET estado='I' 
							WHERE idcobrador={$fields['idcobrador']} 
							AND  idsucursal='{$fields['idsucursal']}' 
							AND idcredito IN (SELECT idcredito FROM credito.credito WHERE estado='A' AND pagado='S')");
							
		$this->db->query("	UPDATE cobranza.hoja_ruta SET estado='I' 
							WHERE idcobrador={$fields['idcobrador']} 
							AND idsucursal='{$fields['idsucursal']}' 
							AND estado='A' 
							AND idzona NOT IN (SELECT idzona FROM cobranza.zona_cobrador WHERE idempleado='{$fields['idcobrador']}' AND idsucursal='{$fields['idsucursal']}')");
		
		/* Quitamos los registros de zonas que ya no pertenecen al cobrador en cuestion */
		$this->db->query("	UPDATE cobranza.hoja_ruta SET estado='I' 
							WHERE idcobrador='{$fields['idcobrador']}' 
							AND idsucursal='{$fields['idsucursal']}'
							AND estado='A'
							AND idzona NOT IN('".implode("','", $this->data_zona_cobrador($fields,'idzona','simple'))."');");
		
		/* Actualizamos el orden de las zonas en la HOJA RUTA */
		$this->db->query("	UPDATE cobranza.hoja_ruta h 
							SET orden = (SELECT COALESCE(orden,0) FROM cobranza.zona_cobrador zc WHERE zc.idzona=h.idzona AND zc.idsucursal=h.idsucursal AND zc.estado='A' And zc.idempleado=h.idcobrador LIMIT 1) 
							WHERE h.estado='A' 
							AND h.idsucursal={$fields['idsucursal']} 
							AND h.idcobrador={$fields['idcobrador']}; ");
		$this->db->trans_complete(); // finalizamos transaccion
		return $data_c;
	}
	
	public function data_zona_cobrador($fields=array(), $file = "*", $devolver = 'doble'){
		$query = $this->db->query("SELECT {$file} FROM cobranza.zona_cobrador WHERE idempleado={$fields['idcobrador']} AND idsucursal={$fields['idsucursal']} AND estado='A'");
		if($devolver=='doble')
			return $query->result_array();
		else{
			$result = array();
			foreach ($query->result_array() as $key => $value) {
				$result[] = $value['idzona'];
			}
			
			if(empty($result))
				$result = array(0);
			return $result;
		}
	}
	
	public function seleccion_array($datos,$val,$key){
		$data = array();
		foreach($datos as $kk=>$vv){
			if($vv[$key]==$val){
				$data[]=$vv;
			}
		}	
		return $data;
	}
	
	public function consultaruc($ruc) {
		$patron = "/^[[:digit:]]+$/";
		if( ! preg_match($patron, $ruc)) {
			return array();
		}
		if(strlen($ruc) != 11) {
			return array();
		}
		
		$url = $this->get_param("url_consultas_sunat");
		if($url == null) {
			return array();
		}
		
		$file_headers = @get_headers($url);
		if($file_headers[0] == 'HTTP/1.1 404 Not Found'){ 	
		   return array();
		}
		//$url = $url.urlencode($ruc);
		
		$str = @file_get_contents($url.$ruc );
			
		return json_decode($str,true);
		
	}
	
	public function is_activo($ruc) {
		$data = $this->consultaruc($ruc);
		if(empty($data))
			return false;
		return ($data["estado"] == "ACTIVO");
	}
	
	public function credito_vencido($idcliente=0){
		$sql = $this->db->query("SELECT
							idcredito idcredito
							,(idventa) idventa
							,(nro_credito) nro_credito
							,to_char((q.fecha_vencimiento),'DD/MM/YYYY') fecha_vencimiento
							FROM credito.credito 
							JOIN (
								SELECT count(*) letras_vencidas,idcredito codcredito,MIN(fecha_vencimiento) fecha_vencimiento FROM credito.letra WHERE estado='A' AND pagado='N' AND fecha_vencimiento<=CURRENT_DATE
								GROUP BY codcredito
							) q ON q.codcredito=credito.idcredito AND (q.fecha_vencimiento + 2)<=CURRENT_DATE
							JOIN venta.cliente_view c ON c.idcliente=credito.idcliente
							WHERE credito.idcliente={$idcliente} AND 
							credito.estado='A' AND credito.pagado='N' AND c.bloqueado='S'
							ORDER BY fecha_vencimiento
							LIMIT 1");
		$data = $sql->result_array();
		
		return $data;
	}
	
	public function auditar_cliente($data=array(),$filter=array(),$model){
		$audit = $this->load_model("venta.auditar_cliente");
		if(!isset($data["accion"]))
			$data["accion"] = "Creado";
		foreach($data as $k=>$v){
			if(in_array($k,$filter)){
				$has = $this->view_change_input($model,$data,$k,$v);
				if($has){
					if(empty($data[$model->get_pk()])){
						$q= $this->db->query("SELECT MAX({$model->get_pk()}) pk FROM {$model->get_schema()}.{$model->get_table_name()} WHERE estado='A';");
						$data[$model->get_pk()] = $q->row()->pk;
						$v						= $q->row()->pk;
					}

					$data["tabla"]			= strtolower($model->get_schema().".".$model->get_table_name());
					$data["name_pk"]		= strtolower($model->get_pk());
					$data["valor_pk"]		= $data[$model->get_pk()];
					$data["fecha_registro"]	= date("Y-m-d");
					$data["hora_registro"]	= date("H:i:s");
					$data["idsucursal"]		= $this->get_var_session("idsucursal");
					$data["idusuario"]		= $this->get_var_session("idusuario");
					$data["ip_usuario"]		= $this->input->ip_address();
					$data["accion"]			= $data["accion"]?$data["accion"]:"CREADO";
					$data["desde"]			= strtolower($this->controller);
					$data["name_campo"]		= strtolower($k);
					$data["valor_campo"]	= $v;
					$data["estado"]			= "A";
					$status = $this->auditar_cliente->insert($data);
				}
			}
		}
	}
	
	public function view_change_input($model,$post,$campo='', $valor=''){
		if(empty($post[$model->get_pk()]))
			$post[$model->get_pk()] = 0;
		$q = $this->db->query("SELECT $campo campo FROM {$model->get_schema()}.{$model->get_table_name()} WHERE {$model->get_pk()}='{$post[$model->get_pk()]}';");
		$old_val = $q->row()->campo;
		
		if(empty($post[$model->get_pk()]))//Es nuevo
			return true;
		else if($old_val<>$valor)
			return true;
		return false;
	}
	
	public function insert_logoExcel(&$excel,$titulo,$subtitle=false,$startLine=6,$height=0,$width=0,$fill=true){
		$codsucursal = $this->get_var_session("idsucursal");
		$this->load_model('seguridad.sucursal');
		$suc = $this->sucursal->find($codsucursal);

		$this->load_model("seguridad.empresa");
		$emp = $this->empresa->find($this->sucursal->get("idempresa"));
		$logo = trim($this->empresa->get("logo"));

		$logo = ver_fichero_valido($logo,getcwd()."/app/img/empresa/");

		include_once APPPATH.'/libraries/PHPExcel.php';
		include_once APPPATH.'/libraries/PHPExcel/IOFactory.php';
		include_once APPPATH.'/libraries/PHPExcel/Cell/AdvancedValueBinder.php';

		$styleArray = array(
			'font' => array(
				'bold' => true,
			)
			,'alignment' => array(
				'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			)
			,'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
				'rotation' => 90,
				'startcolor' => array(
					'argb' => 'FFA0A0A0',
				),
				'endcolor' => array(
					'argb' => 'FFFFFFFF',
				),
			),
		);
		
		if($fill === false) {
			unset($styleArray["fill"]);
		}
		
		$excel->setActiveSheetIndex(0)->mergeCells('C2:I2');
		$excel->getActiveSheet()->setCellValue('C2', $titulo);

        $excel->getActiveSheet()->getStyle('C2:I2')->applyFromArray($styleArray);
        // $excel->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);
		
		if( !empty($logo) ){
			$info = getimagesize($logo);
			// echo "<pre>";print_r($info);exit;
			include_once APPPATH.'/libraries/PHPExcel/Worksheet/Drawing.php';
			$objDrawing = new PHPExcel_Worksheet_Drawing();
			
			$objDrawing->setPath($logo);  //setOffsetY has no effect
			// $objDrawing->setOffsetX(8);    // setOffsetX works properly
			// $objDrawing->setOffsetY(300);  //setOffsetY has no effect
			$objDrawing->setCoordinates('A1');
			if($width == 0 && $height == 0)
				$height = 100;
			if($width > 0)
				$objDrawing->setWidth($width);
			if($height > 0)
				$objDrawing->setHeight($height);
			if($height > 0 && $width > 0)
				$objDrawing->setResizeProportional(false);
			$objDrawing->setWorksheet($excel->getActiveSheet()); 
		}
		
		if(!empty($subtitle)){
			unset($styleArray["alignment"]);
			
			$excel->setActiveSheetIndex(0)->mergeCells("A{$startLine}:E{$startLine}");
			$excel->getActiveSheet()->setCellValue("A{$startLine}", $this->empresa->get("descripcion"));
			$excel->getActiveSheet()->getStyle("A{$startLine}:E{$startLine}")->applyFromArray($styleArray);
			
			$excel->setActiveSheetIndex(0)->mergeCells("I{$startLine}:L{$startLine}");
			$excel->getActiveSheet()->setCellValue("I{$startLine}", date('d/m/Y'));
			$excel->getActiveSheet()->getStyle("I{$startLine}:L{$startLine}")->applyFromArray($styleArray);
			
			$startLine ++;
			$excel->setActiveSheetIndex(0)->mergeCells("A{$startLine}:E{$startLine}");
			$excel->getActiveSheet()->setCellValue("A{$startLine}", "RUC: ".$this->empresa->get("ruc"));
			$excel->getActiveSheet()->getStyle("A{$startLine}:E{$startLine}")->applyFromArray($styleArray);
			
			$excel->setActiveSheetIndex(0)->mergeCells("I{$startLine}:L{$startLine}");
			$excel->getActiveSheet()->setCellValue("I{$startLine}", date('h:i A'));
			$excel->getActiveSheet()->getStyle("I{$startLine}:L{$startLine}")->applyFromArray($styleArray);
		}
	}
	
	public function cellColorByColumnAndRow(&$oExcel, $col, $row, $color) {
        $oExcel->getActiveSheet()
			->getStyleByColumnAndRow($col, $row)->getFill()
			->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => $color)));
    }
}

?>