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
	public function form($data = null, $prefix = "", $modal = false) {
		if(!is_array($data)) {
			$data = array();
		}
		$data["controller"] = $this->controller;
		$data["prefix"] = $prefix;
		$data["modal"] = $modal;
		//Js y css para la asignacion de roles, no borrar
		$this->css('plugins/iCheck/custom');
		$this->css("plugins/datapicker/datepicker3");
		
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js("plugins/jsTree/jstree.min");
		$this->css('plugins/jsTree/style.min');
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
		// $this->load_model('view_usuario');
		// $this->load->library('datatables');

		// $this->datatables->setModel($this->view_usuario);

		// $this->datatables->setIndexColumn("idusuario");

		// $this->datatables->where('estado', '=', 'A');//

		// $this->datatables->setColumns(array('nombres','apellidos','usuario','fecha_nac'));

		// $columnasName = array(
			// array('Nombres','30%')
			// ,array('Apellidos','20%')
			// ,array('Nick','10%')
			// ,array('Fecha Nac','3%')
		// );
		// $this->datatables->setCallback('callbackUsuario');
		// $table = $this->datatables->createTable($columnasName);

		// $script = "<script>".$this->datatables->createScript()."</script>";

		// $this->css("plugins/datapicker/datepicker3");

		// $this->css('plugins/dataTables/dataTables.bootstrap');
		// $this->css('plugins/dataTables/dataTables.responsive');
		// $this->css('plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox');
		// $this->css('plugins/dataTables/dataTables.tableTools.min');

		// $this->js('plugins/dataTables/jquery.dataTables');
		// $this->js('plugins/dataTables/dataTables.bootstrap');
		// $this->js('plugins/dataTables/dataTables.responsive');
		// $this->js('plugins/dataTables/dataTables.tableTools.min');
		// $this->js('plugins/iCheck/icheck.min');
		// $this->js("<script>$(document).ready(function () {
                // $('.i-checks').iCheck({
                    // checkboxClass: 'icheckbox_square-green',
                    // radioClass: 'iradio_square-green',
                // });
            // });</script>", false);
		// $this->js("plugins/datapicker/bootstrap-datepicker");
		// $this->js("plugins/datapicker/bootstrap-datepicker.es");
		// $this->js($script, false);
		

		// $row = $this->get_permisos();
		// if($row->nuevo == 1 || $row->editar == 1 || $row->eliminar == 1) {
			// $this->add_button("btn_add_sucursal", "Asignar Sucursal",'fa-map-marker','warning');
			// $this->add_button("btn_add_user", "Asignar Usuario",'fa-key','success');
		// }
		// return $table;
		
		return $this->inicio();
	}
	
	public function grid(){
		$this->load_model('view_usuario');
		$this->load->library('datatables');

		$this->datatables->setModel($this->view_usuario);

		$this->datatables->setIndexColumn("idusuario");

		$this->datatables->where('estado', '=', 'A');//

		$this->datatables->setColumns(array('idusuario','nombres','apellidos','usuario','fecha_nac'));

		$columnasName = array(
			array('ID','5%')
			,array('Nombres','28%')
			,array('Apellidos','20%')
			,array('Nick','10%')
			,array('Fecha Nac','6%')
		);
		$this->datatables->setCallback('callbackUsuario');
		$table = $this->datatables->createTable($columnasName);

		$script = "<script>".$this->datatables->createScript()."</script>";

		$this->css("plugins/datapicker/datepicker3");

		$this->css('plugins/dataTables/dataTables.bootstrap');
		$this->css('plugins/dataTables/dataTables.responsive');
		$this->css('plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox');
		$this->css('plugins/dataTables/dataTables.tableTools.min');
		
		$this->css('plugins/jsTree/style.min');

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
		
		$this->js("plugins/jsTree/jstree.min");
		$this->js($script, false);
		

		// $row = $this->get_permisos();
		// if($row->nuevo == 1 || $row->editar == 1 || $row->eliminar == 1) {
			// $this->add_button("btn_add_sucursal", "Asignar Sucursal",'fa-map-marker','warning');
			// $this->add_button("btn_add_user", "Asignar Usuario",'fa-key','success');
		// }
		return $table;
	}
	
	public function grilla_popup() {
		// $this->load_model($this->controller);
		$this->load_model('seguridad.view_usuario');
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->view_usuario);
		$this->datatables->setIndexColumn("idusuario");
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->setColumns(array('idusuario','nombres','apellidos'));
		$this->datatables->setPopup(true);
		// $this->datatables->setSubgrid("cargarDetalle", true);
		
		$table = $this->datatables->createTable(array('Id','Nombres','Apellidos'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	public function inicio($data = null){
		if(!is_array($data)) {
			$data = array();
		}
		$data["controller"] = $this->controller;
		$data["grilla"] = $this->grid();
		// $data["perfiles"] = $this->perfiles();
		return $this->load->view($this->controller."/inicio", $data, true);
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
		$this->set_subtitle("ASIGNACION DE SUCURSAL Y ROL( TIPO EMPLEADO)");
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
		
		$fields['controller']=$this->controller;//AUDIT
		$fields['accion']=__FUNCTION__;//AUDIT
		$fields['estado'] = "A";
		
		if(empty($fields["idusuario"])) {
			$id_usuario = $this->usuario->insert($fields);
		}else {
			$id_usuario = $fields["idusuario"];
			$this->usuario->update($fields);
		}
		
		/*ASIGNACION DE SUCURSAL SAVE*/
		$this->load_model("acceso_empresa");
		
		$this->db->query("UPDATE seguridad.acceso_empresa SET estado='I' WHERE  idusuario='$id_usuario' ");
		
		$query = $this->db->query("SELECT idsucursal FROM seguridad.sucursal WHERE estado='A' ORDER BY sucursal;");
		// foreach($fields["idsucursal"] as $key=>$val) {
		foreach($query->result_array() as $key=>$val) {
			$idsucursal = $val['idsucursal'];

			if(isset($fields["idtipoempleado".$idsucursal])){
				foreach( $fields["idtipoempleado".$idsucursal] as $k=>$v ){
					$query = $this->db->query("SELECT idperfil,es_superusuario FROM seguridad.acceso_empresa WHERE idusuario='$id_usuario' AND idsucursal='$idsucursal' --AND idtipoempleado='$v' ;");
					$dat = $query->row();
					$data["idusuario"] 				= $id_usuario;
					$data["idtipoempleado"] 	= $v;
					$data["idsucursal"] 			= $idsucursal;
					$data["estado"] 					= 'A';
					
					if(!empty($idsucursal) && !empty($dat)){
						$data["idperfil"] 			= $dat->idperfil;
						$data["es_superusuario"] 	= $dat->es_superusuario;
					}
					$this->acceso_empresa->save($data,false);
				}
			}
		}
		$this->db->query("DELETE FROM seguridad.acceso_empresa WHERE  idusuario='$id_usuario'  AND estado='I' ;");
		/*ASIGNACION DE SUCURSAL SAVE*/
		
		$this->response($this->usuario->get_fields());
	}
	
	public function save_user(){
		$this->load_model($this->controller);
		$clave_anterior = $this->input->post('clave_past');
		$this->usuario->text_uppercase(false);
		
		$this->load->library('bcrypt');//cargamos la librería

		$fields = $this->input->post();
		
		//$fields['avatar'] = imagen_upload('avatar','./app/img/usuario/','anonimo.png',true,$resize=array('resize'=>true,'ancho'=>174,'alto'=>178));
		/* $fields['avatar'] = 'anonimo.png'; */
		$fields['idusuario'] = $this->input->post('idusuario_firts');
		if( empty( $clave_anterior ) ){
			$fields['clave'] = $this->bcrypt->hash_password($this->input->post('clave'));
		}else{
			$nuevo = $this->input->post('change_pass');
			if (empty($nuevo))
				$fields['clave'] = $this->input->post('clave_past');
			else
				$fields['clave'] = $this->bcrypt->hash_password($this->input->post('clave'));
		}

		$this->usuario->update($fields);
		
		$this->response($fields);
	}
	
	public function save_detalle_usu(){
		$this->load->library('bcrypt');//cargamos la librería
		$fields = $this->input->post();
		$this->db->trans_start(); // inciamos transaccion
		
		$mod = $this->db->query("SELECT count(*) cant FROM seguridad.usuario WHERE usuario = '{$fields['usuario']}' AND idusuario <> {$fields['idusuario_firts']} and estado = 'A';");
		$has = $mod->row('cant');
		
		if($has > 0) {
			$this->exception('El Nick ya esta siendo usado por otra persona.');
			return false;
		}
		
		/*Verificamos si las claves de los usuarios se esta repitiendo*/
		// $q = $this->db->query("SELECT clave FROM seguridad.usuario WHERE idusuario={$fields['idusuario_firts']}");
		// $clave = $q->row('clave');
		
		// if($this->bcrypt->check_password($password, $encriptado_password)){
			
		// }
		
		$mod = $this->db->query("SELECT count(*) existe FROM seguridad.usuario WHERE clave='{$this->bcrypt->hash_password($fields['clave'])}' AND usuario<>'{$fields['usuario']}';");
		$has = $mod->row('existe');
		
		if($has > 0) {
			$this->exception('La contrase&ntilde;a ingresada ya esta siendo usada.');
			return false;
		}

		$estado = $this->save_user();
		
		$this->load_model("acceso_empresa");
		$estado = true;
		
		if($estado){
			if(!isset($fields["idsucursal"]))
				$fields["idsucursal"] = array();
			
			foreach($fields["idsucursal"] as $key=>$val) {
				if(empty($fields['es_superusuario'][$key]))
					$fields['es_superusuario'][$key]='N';
				
				if(empty($fields['control_reporte'][$key]))
					$fields['control_reporte'][$key]='N';
				
				$this->db->query("	UPDATE seguridad.acceso_empresa 
									SET idperfil='{$fields['idperfil'][$key]}' 
									,es_superusuario='{$fields['es_superusuario'][$key]}'
									,control_reporte='{$fields['control_reporte'][$key]}'
									WHERE idusuario='{$fields['idusuario'][$key]}' AND idsucursal='{$fields['idsucursal'][$key]}' ");
			}
		}
		$this->db->trans_complete(); // finalizamos transaccion
		$this->response($fields);
	}
	
	public function get_data(){
		$fields = $this->input->post();
		$id = $fields["idusuario"];
		
		$this->load_model('usuario');
		$data = $this->usuario->find($id);
		
		$this->response($this->usuario->get_fields());
	}
	
	public function sucursal_asign(){
		$fields = $this->input->post();
		$id = $fields["idusuario"];
		
		$query = $this->db->query("	SELECT e.idempresa,e.descripcion empresa
									FROM seguridad.acceso_empresa ae 
									JOIN seguridad.sucursal s ON s.idsucursal=ae.idsucursal
									JOIN seguridad.empresa e ON e.idempresa=s.idempresa
									WHERE ae.idusuario='$id' AND ae.estado='A'
									GROUP BY empresa,e.idempresa
									ORDER BY empresa;");
		$list_empresa = $query->result_array();									
	
		
		$sql = "SELECT ae.idusuario
				,ae.idsucursal
				,p.descripcion perfil
				,s.descripcion sucursal
				,ae.idperfil
				,su.idempresa
				,ae.es_superusuario
				,ae.control_reporte
				FROM seguridad.acceso_empresa ae 
				JOIN seguridad.sucursal su ON su.idsucursal=ae.idsucursal
				LEFT JOIN seguridad.perfil p ON p.idperfil=ae.idperfil
				JOIN seguridad.sucursal s ON s.idsucursal=ae.idsucursal
				WHERE ae.estado='A' AND ae.idusuario='$id'
				GROUP BY ae.idusuario,ae.idsucursal,perfil,sucursal,ae.idperfil,su.idempresa,ae.es_superusuario,ae.control_reporte;";
		$query = $this->db->query($sql);
		
		$sucursales = $query->result_array();

		$html = "";
		foreach($list_empresa as $key=>$value){
			$suc = $this->armar_datos($sucursales, $value['idempresa'], 'idempresa');
			$html.="<tr>";
			$html.="	<td colspan=4>".'<i class="fa fa-university"></i>&nbsp;&nbsp;&nbsp;<label>'.ucwords(strtolower($value['empresa']))."</label></td>";
			$html.="</tr>";
			// $html.='<li>';
			// $html.= 	'<i class="fa fa-university"></i>'.ucwords(strtolower($value['empresa']));
			foreach($suc as $kk=>$vv){
				$es_superusuario = '';
				if($vv['es_superusuario']=='S')
					$es_superusuario = 'checked';
				
				$control_reporte = '';
				if($vv['control_reporte']=='S')
					$control_reporte = 'checked';
				
				$html.="<tr>";
				$html.="	<td>".'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.ucwords(strtolower($vv['sucursal']))."</td>";
				$html.="	<td>";
				$html.='		<div class="onoffswitch">';
				$html.='			<input type="checkbox" id="es_superusuario'.$vv['idsucursal'].'" class="onoffswitch-checkbox es_superusuario" '.$es_superusuario.' />';
				$html.='			<label class="onoffswitch-label" for="es_superusuario'.$vv['idsucursal'].'">';
				$html.='				<span class="onoffswitch-inner"></span>';
				$html.='				<span class="onoffswitch-switch"></span>';
				$html.='			</label>';
				$html.='		</div>';
				$html.="	</td>";
				
				$html.="	<td>";
				$html.='		<div class="onoffswitch">';
				$html.='			<input type="checkbox" id="control_reporte'.$vv['idsucursal'].'" class="onoffswitch-checkbox control_reporte" '.$control_reporte.' />';
				$html.='			<label class="onoffswitch-label" for="control_reporte'.$vv['idsucursal'].'">';
				$html.='				<span class="onoffswitch-inner"></span>';
				$html.='				<span class="onoffswitch-switch"></span>';
				$html.='			</label>';
				$html.='		</div>';
				$html.="	</td>";
				
				$html.="	<td>".'<select class="idperfil obligatorio form-control input-xs" name="idperfil[]" >'.$this->perfiles($vv['idperfil']).'</select>'."</td>";
				$html.="	<td style='display:none;'>";
				$html.=	'		<input name="idsucursal[]" class="idsucursal" type="hidden" value="'.$vv['idsucursal'].'" />';
				$html.=	'		<input name="idusuario[]"  class="idusuario"  type="hidden" value="'.$vv['idusuario'].'" />';
				$html.="	</td>";
				$html.="</tr>";
			}
			// $html.='</li>';
		}
		
		// return $html;
		$this->response($html);
	}
	
	public function perfiles($idperfil){
		$sql = "SELECT p.*,initcap(descripcion) perfil FROM seguridad.perfil p WHERE estado='A' ORDER BY descripcion";
		$query = $this->db->query($sql);
		$html = '<option></option>';
		foreach($query->result_array() as $key=>$value){
			$html.='<option value="'.$value['idperfil'].'" ';
			if($idperfil == $value['idperfil']){
				$html.='selected';
			}
			$html.='>'.$value['perfil'].'</option>';
		}
		
		return $html;
		// $this->response($query->result_array());
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
		$sql = "UPDATE seguridad.acceso_empresa SET estado='I' WHERE idusuario='{$fields['idusuario']}' AND idsucursal='{$fields['idsucursal']}' AND idtipoempleado='{$fields['idtipoempleado']}' ";
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
	
	public function verificar_clave(){
		$this->load_model($this->controller);
		$this->load->library('bcrypt');//cargamos la librería
		
		$fields = $this->input->post();
		$usuario  		= $this->get_var_session("usuario");
		$fields['clav']  		= $this->get_var_session("clave");
		$encriptado_password 	= $this->bcrypt->hash_password($fields['clave_anterior']);
		
		if ($this->bcrypt->check_password($fields['clave_anterior'], $encriptado_password)) {

			$datos_usuarios = $this->usuario->autentificar_usuario($usuario, $fields['clave_anterior']);

			if($datos_usuarios !== false){
				if($datos_usuarios["idusuario"] == $this->get_var_session("idusuario"))
					return $this->response(true);
			}
		}
		return $this->response(false);
	}
	
	public function change_avatar(){
		$this->load_model($this->controller);
		$this->usuario->text_uppercase(false);
		$fields = $this->input->post();
		// $fields['avatar']   = imagen_upload('foto','./app/img/usuario/','anonimo.png',true,array('resize'=>true,'ancho'=>48,'alto'=>48),'file_avatar');
		$fields['avatar']   = imagen_upload('foto','./app/img/usuario/','anonimo.png',true,array(),'file_avatar');
		$fields['idusuario']=$this->get_var_session("idusuario");

		$this->db->trans_start(); // inciamos transaccion
		
		if($fields['idusuario']){
			$this->usuario->update($fields);
		}else{
			$this->exception('Error al encontrar el usuario.');
			return false;
		}
		$this->db->trans_complete(); // finalizamos transaccion
		$this->session->set_userdata('avatar', $fields['avatar']);
		$this->response(json_encode($fields));
	}
	
	public function change_pass(){
		$this->load->library('bcrypt');//cargamos la librería
		$this->load_model($this->controller);
		$this->usuario->text_uppercase(false);
		
		$fields = $this->input->post();
		$fields['idusuario']=	$this->get_var_session("idusuario");
		$fields['clave']	=	$this->bcrypt->hash_password($this->input->post('clave'));
		if($fields['idusuario']){
			$this->usuario->update($fields);
		}else{
			$this->exception('Error al encontrar el usuario.');
			return false;
		}
		
		$this->db->trans_complete(); // finalizamos transaccion
		// $this->session->set_userdata('clave', $fields['clave']);
		$this->response(json_encode($fields));
	}
	
	public function ListaEmpleados(){
		$sql = "SELECT*FROM seguridad.usuario
				WHERE estado='A'
				ORDER BY appat";
		$query = $this->db->query($sql);
		
		$html ='<ul id="sortable_none" class="sortable  ui-sortable" data-class="ui-state-none" data-padre="0">';
		$html.='	<li class="sortable_none ui-state-disabled" style="">LISTA DE EMPLEADOS<div class="pull-right"><i class="fa fa-users fa-2x"></i></div></li>';
		foreach($query->result_array() as $key=>$value){
			// $empleado = ucwords(strtolower($value['appat'].' '.$value['apmat'].' '.$value['nombres']));
			$empleado = strtoupper (($value['appat'].' '.$value['apmat'].' '.$value['nombres']));
			$html.='	<li class="ui-state-none draggable lista" class-parent="ui-state-none" data-text="'.$empleado.'" data-emp="'.$value['idusuario'].'">';
			$html.='		<i class="fa fa-user inlista"></i>&nbsp;';
			$html.=			($empleado);
			$html.='		<div class="pull-right" style="margin-top: -5px;">';
			// $html.='			<select class="idtipoempleado idtmp" name="idtipoempleado[]">'.$this->tipoempleado().'</select>';
			$html.= '				<div class="btn-group">';
			$html.= '					<button data-toggle="dropdown" class="btn btn-white idtipoempleado_select dropdown-toggle idtmp fa fa-sort-desc" style="width:85px;font-size:11px;text-align:left;">'.$this->tipoempleado(null,true).'</button>';
			$html.= 					$this->tipoempleado(null,false);
			$html.= '				</div>';
			$html.='			<input type="hidden" name="idsucursal[]" value="" class="idsucursal"  />';
			$html.='			<input type="hidden" name="idusuario[]"  value="'.$value['idusuario'].'" class="idusuario" data-name="'.$empleado.'" />';
			$html.='			<input type="hidden" name="idperfil[]"  value="" class="idperfil" />';
			$html.='			&nbsp;<i class="fa fa-trash-o cursor eliminar deletito fa-2x" style=""></i>';
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
		$col = 6;
		foreach($sucursal as $k=>$v){
			$html.= '<div class="col-sm-'.$col.' content_all" >';
			$html.= '	<ul class="connectedSortable sortable sortable_connect ui-sortable" data-padre="1" data-sucu="'.$v['idsucursal'].'" >';
			$html.= '		<li class="ui-state-default-head ui-state-disabled" style="height:40px;">'.$v['descripcion'].'<div class="pull-right"><i class="fa fa-users fa-2x"></i></div></li>';
			$here	= $this->seleccion($detalle,$v['idsucursal']);
			foreach($here as $key=>$value){
				// $empleado = ucwords(strtolower($value['appat'].' '.$value['apmat'].' '.$value['nombres']));
				$empleado = strtoupper(($value['appat'].' '.$value['apmat'].' '.$value['nombres']));
				$html.= '	<li class="lista grabado" data-text="'.$empleado.'" style="">&nbsp;';
				$html.= 		$empleado;
				$html.= '		<div class="pull-right" style="margin-top: -5px;">';
				$html.= '				<div class="btn-group">';
				$html.= '					<button data-toggle="dropdown" class="btn btn-white idtipoempleado_select dropdown-toggle fa fa-sort-desc form-control-req" style="width:85px;font-size:11px;text-align:left;">'.$this->tipoempleado($value['idtipoempleado'],true).'</button>';
				$html.= 					$this->tipoempleado($value['idtipoempleado'],false);
				$html.= '				</div>';
				$html.= '			<input type="hidden" name="idsucursal[]" value="'.$value['idsucursal'].'" class="idsucursal"  />';
				$html.= '			<input type="hidden" name="idusuario[]" value="'.$value['idusuario'].'" class="idusuario" data-name="'.$empleado.'" />';
				$html.='			<input type="hidden" name="idperfil[]"  value="'.$this->perfil_user($value['idusuario'],$value['idsucursal']).'" class="idperfil" />';
				$html.= '			&nbsp;<i class="fa fa-trash-o cursor eliminar fa-2x"></i>';
				$html.= '		</div>';
				$html.= '	</li>';
			}
			$html.= '	</ul>';
			$html.= '</div>';
		}
		return $html;
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
	
	public function perfil_user($idusuario,$idsucursal){
		$sql = "SELECT MAX(idperfil) idperfil FROM seguridad.acceso_empresa WHERE idusuario='{$idusuario}' AND idsucursal='{$idsucursal}' AND estado='A' ORDER BY idperfil ASC";
		// echo $sql;exit;
		$query = $this->db->query($sql);
		$idperfil_aux = $query->row('idperfil');
		
		if(empty($idperfil_aux))
			$idperfil_aux = null;

		return $idperfil_aux;
	}
	
	public function tipoempleado($idtipoempleado=null,$solo_seleccionado=false){
		$sql = "SELECT*FROM general.tipoempleado WHERE estado='A' ORDER BY descripcion";
		$query = $this->db->query($sql);
		
		$selected = '';
		$valor = '';
		$html ='<ul class="dropdown-menu" style="min-width:88px !important;margin-top: 0px;">';
		foreach($query->result_array() as $k=>$v){
			$html.='<li data-value="'.$v['idtipoempleado'].'"><a class="li_tipoempleado" style="cursor:pointer;font-size:10px;">'.ucwords(strtolower($v['descripcion'])).'</a></li>';
			// $html.='<option value="'.$v['idtipoempleado'].'" ';
			if($idtipoempleado == $v['idtipoempleado'] && !empty($idtipoempleado) ){
				$selected = ucwords(strtolower($v['descripcion']));
				$valor = $v['idtipoempleado'];
			}	// $html.='selected';
			// $html.='>'.ucwords(strtolower($v['descripcion'])).'</option>';
		}
		$html.='</ul>';
		$html.='<input type="hidden" class="idtipoempleado" name="idtipoempleado[]" value="'.$valor.'">';
		if(empty($solo_seleccionado))
			return $html;
		else
			return $selected;
	}
	
	public function tipoempleado_tmp($idtipoempleado=null){
		$sql = "SELECT*FROM general.tipoempleado WHERE estado='A' ORDER BY descripcion";
		$query = $this->db->query($sql);
		
		$html ='	<option></option>';
		foreach($query->result_array() as $k=>$v){
			$html.='<option value="'.$v['idtipoempleado'].'" ';
			if($idtipoempleado == $v['idtipoempleado'] && !empty($idtipoempleado) )
				$html.='selected';
			$html.='>'.ucwords(strtolower($v['descripcion'])).'</option>';
		}		
		return $html;
	}
	
	public function autocomplete() {
		$txt = $this->input->post("startsWith").'%';
		
		$sql = "SELECT idusuario idcliente, nombres, (appat||' '||apmat) apellidos, dni
			FROM seguridad.usuario
			WHERE estado='A' 
			and (nombres ILIKE ? OR appat ILIKE ? OR dni ILIKE ?)
			ORDER BY nombres, apellidos
			LIMIT ?";
		$query = $this->db->query($sql, array($txt, $txt, $txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}

	/*PARA LA ASIGNACION DE SUCURSALES*/
	public function Lista_sucursal(){
		$fields = $this->input->post();
		$sql = "SELECT idempresa, descripcion empresa,ruc FROM seguridad.empresa WHERE estado='A' ORDER BY empresa; ";
		$query = $this->db->query($sql);
		
		$array_padre = $query->result_array();

		$query = $this->db->query("SELECT idsucursal,idempresa,descripcion sucursal FROM seguridad.sucursal WHERE estado='A' ORDER BY sucursal;");
		$lista = $query->result_array();
		
		$query = $this->db->query("SELECT idtipoempleado,descripcion tipoempleado FROM general.tipoempleado WHERE estado='A' ORDER BY tipoempleado;");
		$array_boton = $query->result_array();

		$html = '<ul style="">';
		foreach($array_padre as $key=>$value){
			$array_hijo = $this->armar_datos($lista,$value['idempresa'],'idempresa');
			$class_padre = "";
			if(count($array_hijo)<1)
				$class_padre = " padrecito";
				
			$old = "li_old";
			
			$html.= '<li class="">';
			$html.= '	<div class="presentacion '.$old.' " >';
			$html.= '		<div class="nada nada-icon main_expand main_father '.$class_padre.'" style="display:inline-block;">&nbsp;</div>';
			$html.= '		<div class="menu_padre checkbox_parent" >';
			$html.= '			<div class="checkbox checkbox-success">';
			$html.= '				<input id ="checkbox'.$value['idempresa'].'" class="checkbox_nodo ck_father" type="checkbox"  >';
			$html.= '				<label  class="label_checkbox" id="label_checkbox'.$value['idempresa'].'" for="checkbox'.$value['idempresa'].'" ajax-icon ="">';
			$html.= '					<i class="fa fa-university" aria-hidden="true"></i>'.ucwords(($value['empresa']));
			$html.= '				</label>';
			$html.= '			</div>';
			$html.= '		</div>';
			$html.= '	</div>';
			
			$clase_ul = 'nada-icon grupo';
			if(count($array_padre)==($key+1))
				$clase_ul = '';
			
			$html.= '	<ul class="'.$clase_ul.'" style="" >';
			foreach($array_hijo as $k=>$v){
				$clase_ul = 'nada-icon grupo';
				if(count($array_hijo)==($k+1))
					$clase_ul = '';

				$clase_extend = 'main_expand main_hijo';
				if(count($array_boton)<1)
					$clase_extend = 'hijito';
				
				$html.= '	<li class="'.$clase_ul.'" >	';
				$html.= '		<div class="presentacion" >';
				$html.= '			<div class="nada nada-icon '.$clase_extend.'" style="display:inline-block;">&nbsp;</div>';
				$html.= '			<div class="menu_hijillo checkbox_parent" style="display:inline-block;">';
				$html.= '				<div class="checkbox checkbox-success" >';
				$html.= '					<input id="checkbox'.$v['idsucursal'].'" class="checkbox_nodo ck_hijo" type="checkbox" name="idsucursal[]"  value="'.$v['idsucursal'].'" >';
				$html.= '					<label class="label_checkbox" id="label_checkbox'.$value['idempresa']."_".$v['idsucursal'].'" for="checkbox'.$value['idempresa']."_".$v['idsucursal'].'" >';
				$html.= '						'.ucwords(strtolower(($v['sucursal'])));
				$html.= '					</label>';
				$html.= '				</div>';
				$html.= '			</div>';
				$html.= '		</div>';

				if(count($array_boton)>0){
					$html.= '			<ul style="display:inline-block; " class="botones">';
					// $html.= '				<li>';
					// $html.= '					<div class="" style="background:#293846;font-weight: bold;color: white;" >';
					// $html.= '						<div class="nodo_botoncito" style="display:inline-block;margin-left: 12px;">SELECCIONE ROL';
					// $html.= '						</div>';
					// $html.= '					</div>';
					// $html.= '				</li>';
					foreach($array_boton as $kk=>$vv){
						$clase_ul = 'nada-icon grupo';
						if(count($array_boton)==($kk+1))
							$clase_ul = '';
						$html.= '				<li class="'.$clase_ul.'"  >';
						$html.= '					<div class="presentacion" >';
						$html.= '						<div class="nada nada-icon hijito" style="display:inline-block;">&nbsp;</div>';
						$html.= '						<div class="nodo_botoncito" style="display:inline-block;">';
						$html.= '							<div class="checkbox" >';
						$html.= '								<input id="checkbox'.$value['idempresa']."_".$v['idsucursal'].'_'.$vv['idtipoempleado'].'" class="checkbox_nodo ck_boton" type="checkbox" name="idtipoempleado'.$v['idsucursal'].'[]" value="'.$vv['idtipoempleado'].'" >';
						$html.= '								<label class="label_checkbox" id="label_checkbox'.$value['idempresa']."_".$v['idsucursal'].'_'.$vv['idtipoempleado'].'" for="checkbox'.$v['idempresa'].'_'.$vv['idtipoempleado'].'">';
						$html.= '									<i class="fa fa-user-secret " style="font-size:20px;"></i>'.$vv['tipoempleado'];
						$html.= '								</label>';
						$html.= '							</div>';
						$html.= '						</div>';
						$html.= '					</div>';
						$html.= '				</li>';
					}
					$html.= '			</ul>';
				}
				
				// $html.= '	</li>';
			}
			$html.= '	</ul>';
			
			$html.= '</li>';
		}
		$html.= '</ul>';
		//echo $html;exit;
		$this->response($html);
	}
	
	public function tree_list(){
		$fields = $this->input->post();
		// print_r($fields);exit;
		$sql = "SELECT idempresa, descripcion empresa,ruc FROM seguridad.empresa WHERE estado='A' ORDER BY empresa; ";
		$query = $this->db->query($sql);
		
		$array_padre = $query->result_array();

		$query = $this->db->query("SELECT idsucursal,idempresa,descripcion sucursal FROM seguridad.sucursal WHERE estado='A' ORDER BY sucursal;");
		$lista = $query->result_array();
		
		$query = $this->db->query("SELECT idtipoempleado,descripcion tipoempleado FROM general.tipoempleado WHERE estado='A' ORDER BY tipoempleado;");
		$array_rol = $query->result_array();

		$query = $this->db->query("SELECT * FROM seguridad.acceso_empresa WHERE idusuario='{$fields['idusuario']}';");
		$array_accesos = $query->result_array();
		
		$html = '<ul style="">';
		foreach($array_padre as $key=>$value){
			$array_hijo = $this->armar_datos($lista,$value['idempresa'],'idempresa');
			$firts_node='';
			if($key==0){
				$firts_node = 'here_firts';
			}
			
			$html.= "<li class='".$firts_node."' data-jstree='{\"icon\":\"fa fa-university\"}' >";
			// $html.= '	<input class="checkbox_nodo" type="checkbox" >';
			$html.= '	'.ucwords(($value['empresa']));			
			$html.= '	<ul style="" >';
			foreach($array_hijo as $k=>$v){
				if(!empty( $firts_node )){
					if($key!=0){
						$firts_node = '';
					}
				}
				
				$html.= "	<li class='".$firts_node."' data-jstree='{\"icon\":\"glyphicon glyphicon-map-marker\" }' >	";
				// $html.= '					<input class="checkbox_nodo" type="checkbox" name="idsucursal[]"  value="'.$v['idsucursal'].'" >';
				$html.= 		ucwords(strtolower(($v['sucursal'])));

				if(count($array_rol)>0){
					$html.= '	<ul >';
					foreach($array_rol as $kk=>$vv){
						$add_data = '';
						$verificar = $this->reordenar($array_accesos,array('idsucursal'=>$v['idsucursal'],'idtipoempleado'=>$vv['idtipoempleado'])) ;
						if( !empty($verificar) )
							$add_data = ",\"selected\":\"true\"";
						$html.= "	<li data-jstree='{\"icon\":\"fa fa-user-secret fa-1x\" $add_data}' >	";
						$html.= '		<input class="checkbox_nodo" type="checkbox" name="idtipoempleado'.$v['idsucursal'].'[]" value="'.$vv['idtipoempleado'].'" >';
						$html.= '		'.$vv['tipoempleado'];
						$html.= '	</li>';
					}
					$html.= '	</ul>';
				}
				$html.= '	</li>';
			}
			$html.= '	</ul>';			
			$html.= '</li>';
		}
		$html.= '</ul>';
		
		$this->response($html);
	}
	
	public function Listar_sucursal(){
		$fields = $this->input->post();
		$sql="SELECT ae.*,s.idsucursal,s.idempresa FROM seguridad.acceso_empresa ae 
					JOIN seguridad.sucursal s ON s.idsucursal=ae.idsucursal
					WHERE ae.idusuario='{$fields['idusuario']}' AND ae.estado='A';";
		$query = $this->db->query($sql);
		
		return $this->response($query->result_array());
	}

	public function armar_datos($datos,$id,$id_key = null){		
		$new_array=array();
		foreach($datos as $kkk=>$vvv){
			if( $vvv[$id_key]==$id){
				$new_array[]=$vvv;
			}				
		}
		
		return $new_array;
	}
	
	public function reordenar($datos, $filtro=array()){
		$new_array=array();
		foreach($datos as $kkk=>$vvv){
			$bval = true;
			foreach($filtro as $k=>$v){
				if($vvv[$k]!=$v){
					$bval = false;
					break;
				}
			}
			
			if($bval)
				$new_array[]=$vvv;
		}
		return $new_array;
	}
	/*PARA LA ASIGNACION DE SUCURSALES*/
}
?>