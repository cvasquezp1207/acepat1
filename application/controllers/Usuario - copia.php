<?php

include_once "Controller.php";

class Usuario extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Empleado");
		$this->set_subtitle("Lista de Empleado");
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
		if(!is_array($data)) {
			$data = array();
		}
		$data["controller"] = $this->controller;
		
		$this->css('plugins/iCheck/custom');
		return $this->load->view($this->controller."/form", $data, true);
	}

	public function form_asign($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		$data["controller"] = $this->controller;
		$data["empleados"] = $this->ListaEmpleados();
		$data["sucursal"] = $this->ListaSucursal();
		$this->css('plugins/iCheck/custom');
		return $this->load->view($this->controller."/form_asign", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model('view_usuario');
		$this->load->library('datatables');

		$this->datatables->setModel($this->view_usuario);

		$this->datatables->setIndexColumn("idusuario");

		$this->datatables->where('estado', '=', 'A');//

		$this->datatables->setColumns(array('nombres','apellidos','usuario','fecha_nac'));

		// columnas de la tabla, si no se envia este parametro, se muestra el
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			array('Nombres','30%')
			,array('Apellidos','20%')
			,array('Nick','10%')
			,array('Fecha Nac','3%')
			// array('Descripci&oacute;n', '95%') // ancho de la columna
		);
		$this->datatables->setCallback('callbackUsuario');
		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName);

		// $table = $this->datatables->createTable();
		$script = "<script>".$this->datatables->createScript()."</script>";

		$this->css("plugins/datapicker/datepicker3");

		// agregamos los css para el dataTables
		$this->css('plugins/dataTables/dataTables.bootstrap');
		$this->css('plugins/dataTables/dataTables.responsive');
		$this->css('plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox');
		$this->css('plugins/dataTables/dataTables.tableTools.min');

		// agregamos los scripts para el dataTables
		$this->js('plugins/dataTables/jquery.dataTables');
		$this->js('plugins/dataTables/dataTables.bootstrap');
		$this->js('plugins/dataTables/dataTables.responsive');
		$this->js('plugins/dataTables/dataTables.tableTools.min');
		$this->js('plugins/iCheck/icheck.min');
		$this->js("<script>$(document).ready(function () {
                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });
            });</script>", false);
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js($script, false);
		

		$row = $this->get_permisos();
		if($row->nuevo == 1 || $row->editar == 1 || $row->eliminar == 1) {
			$this->add_button("btn_add_sucursal", "Asignar Sucursal",'fa-map-marker','warning');
			$this->add_button("btn_add_user", "Asignar Usuario",'fa-key','success');
		}
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Usuario");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}

	public function asign_suc() {
		// $this->set_title("Asignar Usuario");
		$this->set_subtitle("ASIGNACION DE SUCURSAL Y ROL");
		$this->set_content($this->form_asign());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model('usuario');
		$data = $this->usuario->find($id);
		
		$this->set_title("Modificar Usuario");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);

		$this->usuario->text_uppercase(false);
		
		$this->load->library('bcrypt');//cargamos la librería

		$fields = $this->input->post();
		$fields['estado'] = "A";
		//$fields['avatar'] = imagen_upload('avatar','./app/img/usuario/','anonimo.png',true,$resize=array('resize'=>true,'ancho'=>174,'alto'=>178));
		//$fields['clave'] = $this->bcrypt->hash_password($this->input->post('clave'));
		
		if(empty($fields["idusuario"])) {
			$this->usuario->insert($fields);
		}else {
			//$nuevo = $this->input->post('change_pass');
			//if (empty($nuevo))
			//	$fields['clave'] = $this->input->post('clave_past');

			$this->usuario->update($fields);
		}
		
		$this->response($this->usuario->get_fields());
		//$this->response($fields);
	}
	
	public function save_detalle_sucu(){
		$fields = $this->input->post();
		$sql = "UPDATE seguridad.acceso_empresa SET estado='I' ";//INACTIVO A TODAS LAS ASIGNACIONES DE LA SUCURSAL
		$estado = $this->db->query($sql);
		
		$this->load_model("acceso_empresa");
		
		if($estado){
			foreach($fields["idsucursal"] as $key=>$val) {
				$data = $this->acceso_empresa->find(array("idusuario"=>$fields['idusuario'][$key], "idsucursal"=>$val));
				if(!empty($fields["idperfil"][$key])){
					$data["idperfil"] 			= $fields["idperfil"][$key];
				}
				$data["idusuario"] = $fields['idusuario'][$key];
				$data["idsucursal"] 		= $val;
				$data["idtipoempleado"] 	= $fields["idtipoempleado"][$key];
				$data["estado"] = 'A';
				// print_r($data);
				$this->acceso_empresa->save($data,false);
			}
			// exit;
		}
		
		$this->response($fields);
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$fields['idusuario'] = $id;
		$fields['estado'] = "I";
		$this->usuario->update($fields);
		
		$this->response($fields);
	}
	
	public function eliminar_detalle(){
		$fields = $this->input->post();
		$sql = "UPDATE seguridad.acceso_empresa SET estado='I' WHERE idusuario='{$fields['idusuario']}' AND idsucursal='{$fields['idsucursal']}'";
		$query = $this->db->query($sql);
		
		$this->response($fields);
	}

	public function verificar_user(){
		$id = $this->input->post("idusuario");
		if (!empty($id)) {
			$query = $this->db
				->select('count(*) cant')
				->where("upper(usuario)", strtoupper($this->input->post("usuario")))
				->where("idusuario", $this->input->post("idusuario"))
				->get("seguridad.usuario");
		}else{
			$query = $this->db
				->select('count(*) cant')
				->where("upper(usuario)", strtoupper($this->input->post("usuario")))
				->get("seguridad.usuario");
		}
					
		return $this->response($query->row());
	}
	
	public function ListaEmpleados(){
		$sql = "SELECT*FROM seguridad.usuario
				WHERE estado='A'
				ORDER BY appat";
		$query = $this->db->query($sql);
		
		$html ='<ul id="sortable_none" class="sortable  ui-sortable" data-class="ui-state-none" data-padre="0">';
		$html.='	<li class="sortable_none ui-state-disabled" style="">LISTA DE EMPLEADOS<div class="pull-right"><i class="fa fa-users fa-2x"></i></div></li>';
		foreach($query->result_array() as $key=>$value){
			$empleado = ucwords(strtolower($value['appat'].' '.$value['apmat'].' '.$value['nombres']));
			$html.='	<li class="ui-state-none draggable lista" class-parent="ui-state-none" data-emp="'.$value['idusuario'].'">';
			$html.='		<i class="fa fa-user inlista"></i>&nbsp;';
			$html.=			$empleado;
			$html.='		<div class="pull-right" style="margin-top: -5px;">';
			$html.='			<select class="idtipoempleado idtmp" name="idtipoempleado[]">'.$this->tipoempleado().'</select>';
			$html.='			<input type="hidden" name="idsucursal[]" value="" class="idsucursal"  />';
			$html.='			<input type="hidden" name="idusuario[]"  value="'.$value['idusuario'].'" class="idusuario" data-name="'.$empleado.'" />';
			$html.='			<input type="hidden" name="idperfil[]"  value="" class="idperfil" />';
			$html.='			&nbsp;<i class="fa fa-trash-o cursor eliminar deletito fa-2x" style="display:none;"></i>';
			$html.='		</div>';
			// $html.='		<input type="hidden" name="idtupoempleado[]" value="" class="idtupoempleado" />';
			$html.='	</li>';
		}
		$html.='</ul>';
		
		return $html;
		
		// return $query->result_array();
	}
	
	public function ListaSucursal(){
		$html = '';
		
		$sql = "SELECT*FROM seguridad.sucursal
				WHERE estado='A'
				ORDER BY descripcion";
		$query = $this->db->query($sql);
		
		$sucursal = $query->result_array();
		
		$query = $this->db->query("	SELECT a.* 
									,u.*
									FROM seguridad.acceso_empresa a 
									JOIN seguridad.usuario u ON u.idusuario=a.idusuario AND u.estado='A'
									WHERE a.estado='A'
									");
											
		$detalle = $query->result_array();
		
		foreach($sucursal as $k=>$v){
			$html.= '<div class="col-sm-4 content_all" >';
			$html.= '	<ul class="connectedSortable sortable sortable_connect ui-sortable" data-padre="1" data-sucu="'.$v['idsucursal'].'" >';
			$html.= '		<li class="ui-state-default-head ui-state-disabled" style="height:40px;">'.$v['descripcion'].'<div class="pull-right"><i class="fa fa-users fa-2x"></i></div></li>';
			$here	= $this->seleccion($detalle,$v['idsucursal']);
			foreach($here as $key=>$value){
				$empleado = ucwords(strtolower($value['appat'].' '.$value['apmat'].' '.$value['nombres']));
				// $html.= '	<li class="lista" style="">&nbsp;'.ucwords(strtolower($value['appat'].' '.$value['apmat'].' '.$value['nombres'])).'<div class="pull-right"><i class="fa fa-pencil-square-o fa-2x cursor editar"></i>&nbsp;<i class="fa fa-trash-o cursor eliminar fa-2x"></i></div></li>';
				$html.= '	<li class="lista grabado" style="">&nbsp;';
				$html.= 		$empleado;
				$html.= '		<div class="pull-right" style="margin-top: -5px;">';
				$html.= '			<select class="idtipoempleado form-control" name="idtipoempleado[]">'.$this->tipoempleado($value['idtipoempleado']).'</select>';
				// $html.= $this->tipoempleado($value['idtipoempleado']);
				$html.='			<input type="hidden" name="idsucursal[]" value="'.$value['idsucursal'].'" class="idsucursal"  />';
				$html.='			<input type="hidden" name="idusuario[]" value="'.$value['idusuario'].'" class="idusuario" data-name="'.$empleado.'" />';
				$html.='			<input type="hidden" name="idperfil[]"  value="'.$value['idperfil'].'" class="idperfil" />';
				$html.= '			&nbsp;<i class="fa fa-trash-o cursor eliminar fa-2x"></i>';
				$html.= '		</div>';
				// $html.='		<input type="hidden" name="idtupoempleado[]" value="" class="idtupoempleado" />';
				$html.= '	</li>';
			}
			$html.= '	</ul>';
			$html.= '</div>';
		}
		
		// foreach($query->result_array() as $key=>$value){
			// $html.='	<li class="ui-state-none draggable" class-parent="ui-state-none">';
			// $html.='		<i class="fa fa-user"></i>&nbsp;&nbsp;'.$value['appat'].' '.$value['apmat'].' '.$value['nombres'];
			// $html.='	</li>';
		// }
		return $html;
		// return $query->result_array();
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
	
	public function tipoempleado($idtipoempleado=null){
		$sql = "SELECT*FROM general.tipoempleado WHERE estado='A' ORDER BY descripcion";
		$query = $this->db->query($sql);
		
		// $html ='<SELECT class="idtipoempleado" name="idtipoempleado[]">';
		$html ='	<option></option>';
		foreach($query->result_array() as $k=>$v){
			$html.='<option value="'.$v['idtipoempleado'].'" ';
			if($idtipoempleado == $v['idtipoempleado'])
				$html.='selected';
			$html.='>'.ucwords(strtolower($v['descripcion'])).'</option>';
		}
		// $html.='</SELECT>';
		
		return $html;
	}
}
?>