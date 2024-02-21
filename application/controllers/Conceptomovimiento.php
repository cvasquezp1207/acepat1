<?php

include_once "Controller.php";

class Conceptomovimiento extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Movimientos");
		$this->set_subtitle("Lista de Movimientos");
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null) {
		$this->load->library('combobox');
		
		if(!is_array($data)) {
			$data = array();
		}
		
		// combo tipomovimiento
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idtipomovimiento"
				,"name"=>"idtipomovimiento"
				,"class"=>"form-control"
				,"required"=>""
			)
		);
		$query = $this->db->select('idtipomovimiento, descripcion')->where("estado", "A")->get("caja.tipomovimiento");
		$this->combobox->addItem($query->result_array());
		// if( isset($data["idtipomovimiento"]) ) {
			// $this->combobox->setSelectedOption($data["idtipomovimiento"]);
		// }
		$data["tipomovimiento"] = $this->combobox->getObject();

		$data["columnas"] = $this->columnas();
		
		$data["controller"] = $this->controller;
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return null;
	}
	
	public function index($tpl = "") {
		$data = array(
			"menu_title" => $this->menu_title
			,"menu_subtitle" => $this->menu_subtitle
			,"content" => $this->form()
			,"with_tabs" => $this->with_tabs
		);
		
		if($this->show_path) {
			$data['path'] = $this->get_path();
		}
		
		$str = $this->load->view("content_empty", $data, true);
		$this->show($str);
	}
	
	// public function indexito(){
	public function columnas(){
		$tipo_mov = $this->Loadtipomov();
		$concepto = $this->Loadconcepto();
		$html = "";
		$columna = 4;
		$height = '300px';
		
		$html ='';
		foreach( $tipo_mov as $key=>$value ){
			$html.= '<div class="col-sm-'.$columna.' content_all">';
			$html.=	'	<div class="ibox">';
			$html.=	'		<div class="ibox-content" style="">';			
			$html.=	'			<div class="sistema manejable" data-modulo="'.$value['idtipomovimiento'].'" data-type="system" data-name="'.$value['descripcion'].'" data-father="0" data-level=1>';
			$html.= '				<i class="fa fa-list fa-2x"></i>&nbsp;&nbsp;&nbsp;'.$value['descripcion'];
			
			$html.= '			</div>';
			
			$array_padre = $this->armar_datos($concepto,$value['idtipomovimiento']);
			

			$html.= '			<div class="" style="border-left:2px dashed #f3f3f4;border-right:2px dashed #f3f3f4;border-bottom:2px dashed #f3f3f4; height:'.$height.' ; max-height:350px ; overflow-y:auto;">';
				$html.=	'			<ul class="uk-nestable" data-father-super = "'.$value['idtipomovimiento'].'">';
				foreach($array_padre as $kk => $vv){
						$html.= '		<li class="uk-nestable-item">';
						$html.= '			<div class="uk-nestable-panel manejable" data-type="module" data-value="'.$vv['idconceptomovimiento'].'" data-name="'.$vv['descripcion'].'" data-level=2>';
						$html.=	'				<i class="uk-nestable-handle fa uk-icon-bars" style="font-size:15px;"></i>&nbsp;&nbsp;'.$vv['descripcion'];
						$html.=	'				<input name="idconceptomovimiento[]"  class="idconceptomovimiento" 	value="'.$vv['idconceptomovimiento'].'" type="hidden">';
						$html.=	'				<input name="idtipomovimiento[]" class="idtipomovimiento" value="'.$value['idtipomovimiento'].'" type="hidden">';
						$html.=	'				<input name="orden[]"   class="orden"   value="'.$vv['orden'].'" type="hidden">';
						$html.=	'			</div>';
				}
				$html.=	'			</ul>';
			$html.=	"			</div>";
			$html.=	"		</div>";
			$html.=	"	</div>";
			$html.= "</div>";
		}
		
		return $html;
	}
	
	public function Loadtipomov(){
		$sql = "SELECT*FROM caja.tipomovimiento WHERE estado='A' ORDER BY orden";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function Loadconcepto(){
		$sql = "SELECT*FROM caja.conceptomovimiento WHERE estado='A' ORDER BY orden";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Movimiento");
		$this->set_subtitle("");
		
		$this->load->library('combobox');
		
		// combo presentacion
		$this->combobox->setAttr("id","idtipomovimiento");
		$this->combobox->setAttr("name","idtipomovimiento");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idtipomovimiento,descripcion');
		$query = $this->db->where("estado","A")->where("lineal","S")->order_by("descripcion")->get("caja.tipomovimiento");
		$this->combobox->addItem("","Seleccione...");
		$this->combobox->addItem($query->result_array());
		
		$data['tipomovimiento'] = $this->combobox->getObject();
		
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->conceptomovimiento->find($id);
		
		$this->set_title("Modificar Movimiento");
		$this->set_subtitle("");
		
		$this->load->library('combobox');
		
		// combo presentacion
		$this->combobox->setAttr("id","idtipomovimiento");
		$this->combobox->setAttr("name","idtipomovimiento");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idtipomovimiento,descripcion');
		$query = $this->db->where("estado","A")->where("lineal","S")->order_by("descripcion")->get("caja.tipomovimiento");
		$this->combobox->addItem("","Seleccione...");
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption($data["idtipomovimiento"]);
		
		$data['tipomovimiento'] = $this->combobox->getObject();
		
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	public function get($id) {
		$this->load_model($this->controller);
		$fields = $this->conceptomovimiento->find($id);
		$this->response($fields);
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		
		$this->conceptomovimiento->text_uppercase(false);

		$fields = $this->input->post();
		$fields['estado'] = "A";
		
		if (!isset($fields['ver_compra'])) {
			$fields['ver_compra'] = 'N';
		}else{
			$fields['ver_compra'] = 'S';
		}

		if (!isset($fields['ver_venta'])) {
			$fields['ver_venta'] = 'N';
		}else{
			$fields['ver_venta'] = 'S';
		}

		if (!isset($fields['ver_reciboingreso'])) {
			$fields['ver_reciboingreso'] = 'N';
		}else{
			$fields['ver_reciboingreso'] = 'S';
		}

		if (!isset($fields['ver_reciboegreso'])) {
			$fields['ver_reciboegreso'] = 'N';
		}else{
			$fields['ver_reciboegreso'] = 'S';
		}

		//print_r($fields);exit;
		if(empty($fields["idconceptomovimiento"])) {
			$this->conceptomovimiento->insert($fields);
		}
		else {
			$this->conceptomovimiento->update($fields);
		}
		
		$this->response($this->conceptomovimiento->get_fields());
	}
	
	public function guardar_orden(){
		$this->load_model($this->controller);
		$fields = $this->input->post();
		
		$this->db->trans_start();
		
		foreach($fields["idconceptomovimiento"] as $key=>$val) {
			$this->db->query("UPDATE caja.conceptomovimiento SET orden='{$fields['orden'][$key]}' WHERE idconceptomovimiento='{$val}' AND estado='A' ");
		}
			
		$this->db->trans_complete();
		$this->response($fields);
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idconceptomovimiento'] = $id;
		$fields['estado'] = "I";
		$this->conceptomovimiento->update($fields);
		
		$this->response($fields);
	}
	
	public function armar_datos($datos,$id){
		/*
		$datos 		= 	array modulos
		$id 		=	id sistema
		$idpadre	=	id padre
		$idmodulo = id modulo
		*/
		
		$new_array=array();
		foreach($datos as $kkk=>$vvv){
			if(empty($idpadre) && empty($idmodulo)){
				if( $vvv['idtipomovimiento']==$id ){
					$new_array[]=$vvv;
				}				
			}
		}
		
		return $new_array;
	}
}
?>