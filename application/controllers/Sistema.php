<?php

include_once "Controller.php";

class Sistema extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Sistema");
		$this->set_subtitle("Lista de Sistema");
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

	// public function form($data = null) {
	public function form($data = null, $prefix = "", $modal = false) {
		if(!is_array($data)) {
			$data = array();
		}
		$data["controller"] = $this->controller;
		$data["prefix"] 	= $prefix;
		$this->js("<script>var prefix_sistema = '{$prefix}'; </script>", false);
		$data["icons"] = $this->get_icons();
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	public function form_asign($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		$data["controller"] = $this->controller;
		$data["sistemas"] = $this->ListaSistemas();
		$data["sistema_sucursal"] = $this->ListaSucursal();
		$this->css('plugins/iCheck/custom');
		return $this->load->view($this->controller."/form_asign", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		// cargamos el modelo y la libreria
		$this->load_model($this->controller);
		$this->load->library('datatables');


		// indicamos el modelo al datatables
		$this->datatables->setModel($this->sistema);
		// $this->datatables->setIndexColumn("idsistema");

		// filtros adicionales para la tabla de la bd (perfil en este caso)
		// $this->datatables->where('estado', '=', 'A');//

		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('descripcion','abreviatura','orden','image'));

		// columnas de la tabla, si no se envia este parametro, se muestra el
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'Descripci&oacute;n'
			,'Abreviatura'
			,'Orden'
			,'Icono'
			// array('Descripci&oacute;n', '95%') // ancho de la columna
		);

		$this->datatables->setCallback('callbackSistema');
		
		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName);

		// $table = $this->datatables->createTable();
		$script = "<script>".$this->datatables->createScript()."</script>";

		// agregamos los css para el dataTables
		// $this->css('plugins/dataTables/dataTables.bootstrap');
		// $this->css('plugins/dataTables/dataTables.responsive');
		// $this->css('plugins/dataTables/dataTables.tableTools.min');


		// agregamos los scripts para el dataTables
		// $this->js('plugins/dataTables/jquery.dataTables');
		// $this->js('plugins/dataTables/dataTables.bootstrap');
		// $this->js('plugins/dataTables/dataTables.responsive');
		// $this->js('plugins/dataTables/dataTables.tableTools.min');
		$this->js($script, false);

		// $row = $this->get_permisos();
		// if($row->nuevo == 1 || $row->editar == 1 || $row->eliminar == 1) {
			// $this->add_button("btn_add_sucursal", "Asignar Sucursal",'fa-map-marker','warning');
		// }

		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Sistema");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	public function asign_suc() {
		// $this->set_title("Asignar Usuario");
		$this->set_subtitle("ASIGNACION DE SISTEMA A SUCURSAL");
		$this->set_content($this->form_asign());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->sistema->find($id);
		
		$this->set_title("Modificar Sistema");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		

		$this->sistema->text_uppercase(false);

		$fields = $this->input->post();
		$fields['estado'] = "A";
		if(empty($fields["idsistema"])) {
			$this->sistema->insert($fields);
		}
		else {
			$this->sistema->update($fields);
		}
		
		$this->response($this->sistema->get_fields());
	}
	
	public function save_detalle_sucu(){
		$fields = $this->input->post();
		
		$this->db->trans_start(); // inciamos transaccion

		$sql = "UPDATE seguridad.acceso_sistema SET estado='I' ";//INACTIVO A TODAS LAS ASIGNACIONES DE LA SUCURSAL
		$estado = $this->db->query($sql);
		
		$this->load_model("acceso_sistema");
		
		if($estado){
			foreach($fields["idsucursal"] as $key=>$val) {
				$data = $this->acceso_sistema->find(array("idsistema"=>$fields['idsistema'][$key], "idsucursal"=>$val));
				if($data==null){
					$data["idsistema"] = $fields['idsistema'][$key];
					$data["idsucursal"] 		= $val;
				}
				$data["estado"] = 'A';
				$this->acceso_sistema->save($data,false);
			}
		}
		$this->db->trans_complete(); // finalizamos transaccion
		$this->response($fields);
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idsistema'] = $id;

		$fields['estado'] = "I";
		$this->sistema->update($fields);
		
		$this->response($fields);
	}
	
	public function eliminar_detalle(){
		$fields = $this->input->post();
		$sql = "UPDATE seguridad.acceso_empresa SET estado='I' WHERE idsistema='{$fields['idsistema']}' AND idsucursal='{$fields['idsucursal']}'";
		$query = $this->db->query($sql);
		
		$this->response($fields);
	}

	public function get_icons() {		
		$this->load_controller("modulo");
		return $this->modulo_controller->get_icons();
	}
	
	public function Listasistema(){
		$query = $this->db
			->select('idsistema, descripcion')
			->where("estado", "A")
			->order_by("idsistema")
			->get("seguridad.sistema");
		
		return $this->response($query->result_array());
	}
	
	public function ListaSistemas(){
		$sql = "SELECT*FROM seguridad.sistema
				WHERE estado='A'
				ORDER BY descripcion";
		$query = $this->db->query($sql);
		
		$html ='<ul id="sortable_none" class="sortable  ui-sortable" data-class="ui-state-none" data-padre="0">';
		$html.='	<li class="sortable_none ui-state-disabled" style="">LISTA DE SISTEMAS<div class="pull-right"><i class="fa fa-tasks fa-2x"></i></div></li>';
		foreach($query->result_array() as $key=>$value){
			$sistema = ucwords(strtolower($value['descripcion']));
			$html.='	<li class="ui-state-none draggable lista" class-parent="ui-state-none" data-sys="'.$value['idsistema'].'">';
			$html.='		<i class="fa '.$value['image'].' fa-2x inlista"></i>&nbsp;';
			$html.=			$sistema;
			$html.='		<div class="pull-right" style="margin-top: 0px;">';
			$html.='			<input type="hidden" name="idsucursal[]" value="" class="idsucursal"  />';
			$html.='			<input type="hidden" name="idsistema[]"  value="'.$value['idsistema'].'" class="idsistema" data-name="'.$sistema.'" />';
			$html.='			&nbsp;<i class="fa fa-trash-o cursor eliminar deletito fa-2x" style="display:none;"></i>';
			$html.='		</div>';
			$html.='	</li>';
		}
		$html.='</ul>';
		
		return $html;
	}
	
	public function ListaSucursal(){
		$html = '';
		
		$sql = "SELECT*FROM seguridad.sucursal
				WHERE estado='A'
				ORDER BY descripcion;";
		$query = $this->db->query($sql);
		
		$sucursal = $query->result_array();
		
		$query = $this->db->query("	SELECT a.* 
									,s.descripcion sistema,s.image
									FROM seguridad.acceso_sistema a 
									JOIN seguridad.sistema s ON s.idsistema=a.idsistema
									WHERE a.estado='A' AND s.estado='A'
									");
											
		$detalle = $query->result_array();
		
		foreach($sucursal as $k=>$v){
			$html.= '<div class="col-sm-4 content_all" >';
			$html.= '	<ul class="connectedSortable sortable sortable_connect ui-sortable" data-padre="1" data-sucu="'.$v['idsucursal'].'" >';
			$html.= '		<li class="ui-state-default-head ui-state-disabled" style="height:40px;">'.$v['descripcion'].'<div class="pull-right"><i class="fa fa-tasks fa-2x"></i></div></li>';
			$here	= $this->seleccion($detalle,$v['idsucursal']);
			foreach($here as $key=>$value){
				$sistema = ucwords(strtolower($value['sistema']));
				$html.= '	<li class="lista grabado" style="">&nbsp;';
				$html.= '		<i class="fa '.$value['image'].'  fa-2x"></i>&nbsp;';
				$html.= 		$sistema;
				$html.= '		<div class="pull-right" style="margin-top: 0px;">';
				$html.='			<input type="hidden" name="idsucursal[]" value="'.$value['idsucursal'].'" class="idsucursal"  />';
				$html.='			<input type="hidden" name="idsistema[]" value="'.$value['idsistema'].'" class="idsistema" data-name="'.$sistema.'" />';
				$html.= '			&nbsp;<i class="fa fa-trash-o cursor eliminar fa-2x"></i>';
				$html.= '		</div>';
				$html.= '	</li>';
			}
			$html.= '	</ul>';
			$html.= '</div>';
		}

		return $html;
	}
	
	public function get_all(){	
		$query = $this->db->select('idsistema, descripcion sistema')
			->where("estado", "A")
			->order_by("orden")
			->get("seguridad.sistema");
		
		$this->response($query->result_array());
	}
	
	public function seleccion($datos,$id){
		$data = array();
		foreach($datos as $kk=>$vv){
			if($vv['idsucursal']==$id){
				$data[]=$vv;
			}
		}	
		return $data;
	}
}
?>