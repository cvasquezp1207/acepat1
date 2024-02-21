<?php

include_once "Controller.php";

class Formatoimpresion extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("");
		$this->set_subtitle("");
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
	public function form() {
		$data["controller"] = $this->controller;
		
		$this->load->library('combobox');
		
		// combo etiqueta
		// $query = $this->db->where("estado", "A")->order_by("label_impresion", "asc")->get("general.etiqueta");
		
		// $this->combobox->setAttr("id", "idetiqueta");
		// $this->combobox->setAttr("name", "idetiqueta");
		// $this->combobox->setAttr("class", "form-control input-xs");
		// $this->combobox->addItem("","Seleccione..");
		// $this->combobox->addItem($query->result_array(), null, array("idetiqueta", "label_impresion"));
		// $data["etiqueta"] = $this->combobox->getObject();
		
		// combo tipo doc
		$query = $this->db->where("estado", "A")->order_by("descripcion", "asc")->get("venta.tipo_documento");
		$this->combobox->init();
		$this->combobox->setAttr(array("name"=>"idtipodocumento", "id"=>"idtipodocumento", "class"=>"form-control input-xs"));
		$this->combobox->addItem($query->result_array(), null, array("idtipodocumento", "descripcion"));
		$data["tipo_documento"] = $this->combobox->getObject();
		
		// combo sucursal
		$query = $this->db->where("estado", "A")->order_by("descripcion", "asc")->get("seguridad.sucursal");
		$this->combobox->init();
		$this->combobox->setAttr(array("name"=>"idsucursal", "id"=>"idsucursal", "class"=>"form-control input-xs"));
		$this->combobox->addItem($query->result_array(), null, array("idsucursal", "descripcion"));
		$data["sucursal"] = $this->combobox->getObject();
		
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
	
	public function data_etiqueta(){
		$post = $this->input->post();

		$excluir_etiqueta = array();
		$and_where = "";
		if(!empty($post['idetiqueta'])){
			foreach($post['idetiqueta'] as $k=>$v){
				// if(substr($x_etiqueta, 0, 2)!='d_'){
				if(!empty($v)){
					$query = $this->db->query("SELECT etiqueta FROM general.etiqueta WHERE idetiqueta='{$v}';");
					$x_etiqueta = $query->row('etiqueta');
					$excluir_etiqueta[]=$v;
				}	
				// }
			}
			
		}
		
		if(!(!$excluir_etiqueta)){
			$and_where = " AND idetiqueta NOT IN(".implode(',',$excluir_etiqueta).") ";
		}
		
		if($post['tipo_contenido']=='f'){//fila
			$and_where.=" AND substring(etiqueta from 1 for 2)  != 'd_'";
		}else{//detalle
			$and_where.=" AND substring(etiqueta from 1 for 2)   = 'd_'";
		}
		
		$sql = $this->db->query("SELECT idetiqueta,label_impresion FROM general.etiqueta WHERE estado='A' $and_where ORDER BY label_impresion;");
		$dato = $sql->result_array();

		$this->response($sql->result_array());
	}
	
	public function get_etiqueta(){
		$fields = $this->input->post();
		
		$query = $this->db->query("SELECT etiqueta FROM general.etiqueta WHERE idetiqueta='{$fields['idetiqueta']}';");

		$this->response($query->row('etiqueta'));
	}
	
	public function get_formato(){
		$fields = $this->input->post();
		if(empty($fields['idtipodocumento']))
			$fields['idtipodocumento']=0;
		
		$fields['idsucursal']=$this->get_var_session('idsucursal');
		
		if(empty($fields['serie']))
			$fields['serie']='0';
		$respuesta = array();

		$this->load_model("general.formato_documento");
		$respuesta['formato'] = $this->formato_documento->find(array("idtipodocumento"=>$fields['idtipodocumento'],"idsucursal"=>$fields['idsucursal'],"serie"=>$fields['serie']));
		
		$this->response($respuesta);		
	}
	
	public function save(){
		$post = $this->input->post();
		if(empty($post['idtipodocumento']))
			$post['idtipodocumento']=0;

		if(empty($post['cantidad_filas_detalle']))
			$post['cantidad_filas_detalle']=0;
		
		$post['idsucursal']=$this->get_var_session('idsucursal');
		
		if(empty($post['serie']))
			$post['serie']='0';
		
		// if(empty($post['serie']))
			// $post['serie']='0';
		
		$this->db->trans_start(); // inciamos transaccion
		$this->db->query("DELETE FROM general.formato_documento WHERE idtipodocumento='{$post['idtipodocumento']}' AND idsucursal='{$post['idsucursal']}' AND serie='{$post['serie']}' AND estado='A';");
		$this->load_model('general.formato_documento');
		$this->formato_documento->text_uppercase(false);

		$data["idtipodocumento"] 		= $post['idtipodocumento'];
		$data["idsucursal"] 			= $post['idsucursal'];
		$data["serie"] 					= $post['serie'];
		$data["contenido"] 				= $post['contenido'];
		$data["width"] 					= $post['width_lienzo'];
		$data["height"] 				= $post['height_lienzo'];
		$data["font_size"] 				= $post['font_size_lienzo'];
		$data["fuente_letra"] 			= $post['fuente_letra_lienzo'];
		$data["cantidad_filas_detalle"] = $post['cantidad_filas_detalle'];
		$data["ancho_celda_detalle"] 	= $post['ancho_celda_detalle'];
		$data["ver_borde"] 				= $post['ver_borde'];
		$data["estado"] 				= 'A';
		
		$this->formato_documento->insert($data,false);
		
		$this->db->trans_complete(); // finalizamos transaccion
		
		$this->response($post);
	}
}
?>