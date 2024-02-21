<?php

include_once "Controller.php";

class Modulo extends Controller {
	
	// public function __construct() {
	// 	echo FCPATH;exit;
	// }

	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Modulo");
		$this->set_subtitle("Lista de modulos");
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		$this->js('form/'.$this->controller.'/index');
	}
	
	public function get_icons() {
		// $str = @file_get_contents(base_url("app/font-awesome/css/font-awesome.css"));
		$str = @file_get_contents(FCPATH."app/font-awesome/css/font-awesome.css");
		
		$result = null;
		if(preg_match_all("/.fa-([a-z\-]+)\:before/", $str, $result)) {
			$result = $result[1];
		}
		
		return $result;
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null, $prefix = "", $modal=false) {
		$this->load->library('combobox');
		
		//sistema
		$query = $this->db->select('idsistema, descripcion')
			->where("estado", "A")
			->get("seguridad.sistema");
		$this->combobox->removeAllItems();
		$this->combobox->setAttr("id", "idsistema");
		$this->combobox->setAttr("name", "idsistema");
		$this->combobox->setAttr("class", "form-control ");
		$this->combobox->setAttr("required", "");
		$this->combobox->addItem('0','Seleccione...');
		$this->combobox->addItem($query->result_array());
		if( isset($data["idsistema"]) ) {
			$this->combobox->setSelectedOption($data["idsistema"]);
		}
		$data["sistema"] = $this->combobox->getObject();


		$data["controller"]	= $this->controller;
		$data["prefix"]		= $prefix;
		$data["icons"] = $this->get_icons();
		
		$this->load_controller("sistema");
		$data["form_sistema"] = $this->sistema_controller->form(null, "sys_", true);
		$this->js('form/sistema/modal');
		
		
		$this->load_controller("boton");
		$data["form_boton"] = $this->boton_controller->form(null, "bot_", true);
		$this->js('form/boton/modal');
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	public function bandera_mod(){
		$query = $this->db->query("SELECT count(*) FROM seguridad.detalle_boton WHERE idmodulo='".$this->_get_menu('menu_c')."' AND estado='A'");
		
		$cant = 0;
		foreach($query->row() as $k=>$v){
			$cant = $v;
		}
		
		return $cant;
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return null;
	}
	
	public function gridN(){
		$this->load_model('seguridad.view_modulos');
		$this->load->library('datatables');

		$this->datatables->setModel($this->view_modulos);
		$this->datatables->setIndexColumn("idmodulo");

		$this->datatables->setColumns(array('idmodulo','sistema','padre','modulos','orden','estado_modulo'));

		$columnasName = array(
			array('C&oacute;digo','8%')
			,array('Sistema','26%')
			,array('Padre','28%')
			,array('Modulo','20%')
			,array('orden','8%')
			,array('Estado','10%')
		);

		$table = $this->datatables->createTable($columnasName);

		$script = "<script>".$this->datatables->createScript()."</script>";
		$this->js($script, false);

		return $table;
	}
	
	public function inicio() {
		$data["controller"] = $this->controller;

		$data["botones"]	= $this->get_buttons();
		$data["grilla1"]	= $this->gridN();
		$data["prefix"]		= '';
		
		// $this->js('js/jquery-ui');
		return $this->load->view($this->controller."/inicio", $data, true);
	}
	
	public function index($tpl = "", $ir_a="inicio", $datos= null) {
		if($ir_a=="inicio")
			$data = array(
				"menu_title" => $this->menu_title
				,"menu_subtitle" => $this->menu_subtitle
				,"content" => $this->inicio()
				,"with_tabs" => $this->with_tabs
			);
		else
			$data = array(
				"menu_title" => $this->menu_title
				,"menu_subtitle" => $this->menu_subtitle
				,"content" => $this->form($datos)
				,"with_tabs" => $this->with_tabs
			);
		
		if($this->show_path) {
			$data['path'] = $this->get_path();
		}
		
		$str = $this->load->view("content_empty", $data, true);
		$this->show($str);
	}
	
	public function nuevo() {
		$this->set_title("Registrar Modulo");
		$this->set_subtitle("");
		$this->set_content($this->form());
		// $this->index("content");
		$this->index("content",'form');
	}
	
	public function editar($id) {
		$this->load_model($this->controller);

		$data = $this->modulo->find($id);
		
		$this->set_title("Modificar Modulo");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content",'form');
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		
		$this->modulo->text_uppercase(false);
		
		$fields = $this->input->post();
		
		// $fields['estado'] = "A";
		
		$this->db->trans_start();
		
		if(empty($fields["idpadre"]))
			$fields["idpadre"]=0;
		
		if(empty($fields["idmodulo"])) {
			$fields['fecha_registro'] = date("Y-m-d");
			$idmodulo = $this->modulo->insert($fields);
		}else {
			$idmodulo = $fields["idmodulo"];
			$this->modulo->update($fields);
		}
		
		$sql = "DELETE FROM  seguridad.detalle_boton WHERE idmodulo='$idmodulo'";//
		$estado = $this->db->query($sql);
		
		if(!isset($fields["idboton"]))
			$fields["idboton"] = array();
		
		if(!empty($fields["idboton"])){
			$this->load_model("detalle_boton");
			foreach($fields["idboton"] as $key=>$val) {				
				$data["idmodulo"] 	= $idmodulo;
				$data["idboton"]  	= $val;
				$data["orden"] 		= $key + 1;
				$data["estado"] 	= 'A';
				$this->detalle_boton->save($data,false);
			}
		}
		$this->db->trans_complete();
		$this->response($fields);
	}
	
	public function save_order(){		
		$post = $this->input->post();
		if(isset($post['idmodulo'])){
			foreach($post['idmodulo'] as $k=>$v){
				$this->db->query("UPDATE seguridad.modulo SET orden='".($k+1)."' WHERE idmodulo='{$v}';");
			}
		}
		
		$this->response(true);
	}
	
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		$fields = $this->modulo->find($id);
		$fields['estado'] = "I";
		$this->modulo->update($fields);
		
		$this->response($fields);
	}
	
	public function get($id) {
		$this->load_model($this->controller);
		$fields = $this->modulo->find($id);
		$this->response($fields);
	}
	
	public function get_all_padre() {
		$post = $this->input->post();
		if(empty($post['idsistema']))
			$post['idsistema']=0;
		
		$query = $this->db->select('idpadre, padre')
			->where("estado", "A")
			->where("idsistema", $post['idsistema'])
			->order_by("orden")
			->get("seguridad.view_modulos_padre");
		
		$this->response($query->result_array());
	}
	
	public function modulos_order(){		
		$post = $this->input->post();
		if(empty($post['idpadre']))
			$post['idpadre']=null;
		$query = $this->db->select('idmodulo, modulos,orden')
			->where("estado", "A")
			->where("idpadre", $post['idpadre'])
			->order_by("orden")
			->get("seguridad.view_modulos");
		
		$this->response($query->result_array());
	}
	
	public function armar_datos($datos,$id,$idpadre,$idmodulo = null){
		/*
		$datos 		= 	array modulos
		$id 		=	id sistema
		$idpadre	=	id padre
		$idmodulo = id modulo
		*/
		
		$new_array=array();
		foreach($datos as $kkk=>$vvv){
			if(empty($idpadre) && empty($idmodulo)){
				if( $vvv['idsistema']==$id && $idpadre == $vvv['idpadre']){
					$new_array[]=$vvv;
				}				
			}else{
				if( $vvv['idsistema']==$id && $idmodulo == $vvv['idpadre']){
					$new_array[]=$vvv;
				}				
			}
		}
		
		return $new_array;
	}
	
	public function ListBotones(){
		$fields = $this->input->post();
		$array_no_boton = array();
		
		$sql = "SELECT * FROM seguridad.view_boton WHERE estado='A'	";
		if(!empty($fields['idboton'])){
			foreach($fields['idboton'] as $val){
				$array_no_boton[]=$val;
			}
			
			if(!empty($array_no_boton))
				$sql.=" AND idboton  NOT IN ('" . implode("','", $array_no_boton) . "') ";
		}
		$query = $this->db->query($sql);
		// return $query->result_array();
		
		$html ='';
		foreach($query->result_array() as $k=>$v){
			$html.='<li data-value="'.$v['idboton'].'"><a class="here_boton" data-icon="'.$v['icono'].'" style="cursor:pointer">'.$v['boton'].'</a></li>';
		}
		
		return $this->response($html);
	}
	
	public function ListDetalleBoton(){
		$fields = $this->input->post();

		if (empty($fields['idmodulo'])) {
			$sql = "SELECT*, '' orden FROM seguridad.boton WHERE estado='A'";
		}else{
			$sql = "SELECT d.*,b.descripcion,b.icono,boton FROM seguridad.detalle_boton d JOIN seguridad.view_boton b ON b.idboton=d.idboton WHERE d.estado='A'	AND d.idmodulo='{$fields['idmodulo']}'";
		}
		$query = $this->db->query($sql." ORDER BY orden");
		
		$html ='';
		foreach($query->result_array() as $k=>$v){
			$html.= '<tr>';
			$html.= '	<td style="padding:3px;">'.'<button style="width:100%;text-align:left;" type="button" class="btn fa '.$v['icono'].'" >&nbsp;&nbsp;'.$v['descripcion'].'</button>'.'</td>';
			$html.= '	<td style="padding:3px;">';
			$html.= '		<button type="button" class="btn delete_boton btn-danger fa fa-times"></button>';
			$html.= '		<input type="hidden" name="idboton[]" value="'.$v['idboton'].'">';
			$html.= '	</td>';
			$html.= '</tr>';			
		}
		
		return $this->response($html);
	}
}
?>