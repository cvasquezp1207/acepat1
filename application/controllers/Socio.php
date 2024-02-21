<?php
include_once "Controller.php";

class Socio extends Controller {
	private $campos_auditar = array("idcliente","limite_credito");
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Cliente");
		//$this->set_subtitle("Lista de Cliente");
		//$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		$this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
		$this->css("plugins/datapicker/datepicker3");
		$this->css('plugins/iCheck/custom');
		$this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null, $prefix = "", $modal = false) {
		if(!is_array($data)) {
			$data = array();
			$data['idcliente']=0;
		}
		
		$this->load_library('combobox');
		
		// combo ESTADO CIVIL
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>$prefix."idestado_civil"
				,"name"=>"idestado_civil"
				,"class"=>"form-control here_req  input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idestado_civil, descripcion')->where("estado", "A")->get("general.estado_civil");
		$this->combobox->addItem('','Seleccione...');
		$this->combobox->addItem($query->result_array());
		if( isset($data["idestado_civil"]) ) {
			$this->combobox->setSelectedOption($data["idestado_civil"]);
		}
		$data["estado_civil"] = $this->combobox->getObject();
		// combo ESTADO CIVIL
		
		
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>$prefix."idzona"
				,"name"=>"idzona"
				,"class"=>"form-control here_req input-xs"
				,"required"=>""
			)
		);
		// combo zona
		$query = $this->db->select('idzona,zona descripcion')->where("estado", "A")->get("general.zona");
		$this->combobox->addItem('','Seleccione...');
		$this->combobox->addItem($query->result_array());
		if( isset($data["idzona"]) ) {
			$this->combobox->setSelectedOption($data["idzona"]);
		}
		$data["zona_combo"] = $this->combobox->getObject();
		
		
		// combo SITUACION LABORAL
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>$prefix."idsit_laboral"
				,"name"=>"idsit_laboral"
				,"class"=>"form-control input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idsit_laboral,descripcion')->where("estado", "A")->get("general.sit_laboral");
		$this->combobox->addItem('','Seleccione...');
		$this->combobox->addItem($query->result_array());
		if( isset($data["idsit_laboral"]) ) {
			$this->combobox->setSelectedOption($data["idsit_laboral"]);
		}
		$data["situacion"] = $this->combobox->getObject();
		
		
		// combo OCUPACION
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>$prefix."idocupacion"
				,"name"=>"idocupacion"
				,"class"=>"form-control  input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idocupacion,ocupacion descripcion')->where("estado", "A")->get("general.ocupacion");
		$this->combobox->addItem('','Seleccione...');
		$this->combobox->addItem($query->result_array());
		if( isset($data["idocupacion"]) ) {
			$this->combobox->setSelectedOption($data["idocupacion"]);
		}
		$data["ocupacion_cli"] = $this->combobox->getObject();

		$this->js('form/'.$this->controller.'/form');
		
		$data["controller"] = $this->controller;
		$data['credito_juridico']=$this->get_param("credito_juridico");
		$data["prefix"] = $prefix;
		$data["modal"] = $modal;
		
		//EXTRAS
		$data['direcciones'] 	= $this->direcciones($data['idcliente']);
		$data['telefonos'] 		= $this->telefonos($data['idcliente']);
		$data['representantes'] = $this->representantes($data['idcliente']);
		//EXTRAS
		
		// formulario ZONA
		$this->load_controller("zona");
		// $this->zona_controller->load = $this->load;
		// $this->zona_controller->db = $this->db;
		// $this->zona_controller->session = $this->session;
		// $this->zona_controller->combobox = $this->combobox;
		$data["form_zona"] = $this->zona_controller->form(null, "zon_", true);

		// formulario OCUPACION
		$this->load_controller("ocupacion");
		$data["form_ocupacion"] = $this->ocupacion_controller->form(null, "ocup_", true);

		// $this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
		// $this->css("plugins/datapicker/datepicker3");
		// $this->css('plugins/iCheck/custom');
		// $this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		// $this->js("plugins/datapicker/bootstrap-datepicker");
		// $this->js("plugins/datapicker/bootstrap-datepicker.es");
		
		$this->css('plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox');
		$this->js('form/zona/modal');
		$this->js('form/ocupacion/modal');
		
		$data["long_dni"] = $this->get_param("long_dni")? $this->get_param("long_dni") : '0';
		$data["long_ruc"] = $this->get_param("long_ruc")? $this->get_param("long_ruc") : '0';
		return $this->load->view($this->controller."/form", $data, true);
		// return $this->load->view($this->controller."/form_tab", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function inicio() {
		$data["controller"] = $this->controller;

		$data["botones"] = $this->get_buttons();
		$data["grilla1"] = $this->gridN();

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
		//$str = $this->load->view($this->controller."/form", $data, true);
		$this->show($str);
		// $winser = "winser sape";
	}
	
	public function gridN(){
		// $this->load_model($this->controller);
		$idempresa	= $this->get_var_session("idempresa");
		$this->load_model('venta.cliente_view');
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->cliente_view);
		$this->datatables->setIndexColumn("idcliente");
		$this->datatables->where('estado', '=', 'A');
		//$this->datatables->where('idempresa', '=', $idempresa);
		
		$this->datatables->setColumns(array('idcliente','cliente','documento_cliente','tipo_cliente'));
		//$this->datatables->setColumns(array('idcliente','cliente','documento_cliente','tipo_cliente','telefono'));
		
		$columnasName = array(
			'Id'
			,'Cliente'
			,'Documento'
			,'Tipo'
			//,'Telefono'
		);
		$this->datatables->setCallback('callbackCliente');
		$table = $this->datatables->createTable($columnasName);
		$script = "<script>".$this->datatables->createScript()."</script>";
		
		// agregamos los css para el dataTables
		$this->css('plugins/dataTables/dataTables.bootstrap');
		$this->css('plugins/dataTables/dataTables.responsive');
		$this->css('plugins/dataTables/dataTables.tableTools.min');
		
		// agregamos los scripts para el datatables
		$this->js('plugins/dataTables/jquery.dataTables');
		$this->js('plugins/dataTables/dataTables.bootstrap');
		$this->js('plugins/dataTables/dataTables.responsive');
		$this->js('plugins/dataTables/dataTables.tableTools.min');
		$this->js($script, false);
		
		return $table;
	}
	
	public function grilla() {
		return null;
	}
	
	public function grilla_popup() {
		// $this->load_model($this->controller);
		$this->load_model('venta.cliente_view');
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->cliente_view);
		$this->datatables->setIndexColumn("idcliente");
		$this->datatables->where('estado', '=', 'A');
		// $this->datatables->setColumns(array('idcliente','cliente','documento_cliente','tipo_cliente'));
		// $this->datatables->setColumns(array('idcliente','cliente','documento_cliente'));
		$this->datatables->setColumns(array('idcliente','cliente','dni','ruc'));
		$this->datatables->setPopup(true);
		
		// $table = $this->datatables->createTable(array('Codigo','Cliente','Documento','Tipo'));
		// $table = $this->datatables->createTable(array('Codigo','Cliente','Documento'));
		$table = $this->datatables->createTable(array('Codigo','Cliente','DNI','RUC'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar cliente");
		$this->set_subtitle("");
		// $this->set_content($this->form());
		$this->index("content",'form');
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);
		$data = $this->socio->find($id);

		$this->set_title("Modificar Socio");
		$this->set_subtitle("");
		// $this->set_content($this->form($data));
		// $this->index("content");
		$this->index("content",'form',$data);
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model("cliente");
		
		$fields = $this->input->post();
		$idsucursal	= $this->get_var_session("idsucursal");
		
		$fields['estado'] = "A";
		
		if(empty($fields['idocupacion'])){
			$fields['idocupacion'] = null;
		}
		
		if(empty($fields['idestado_civil'])){
			$fields['idestado_civil'] = null;
		}
		
		if(empty($fields['idzona'])){
			$fields['idzona'] = null;
		}
		
		if(empty($fields['idsit_laboral'])){
			$fields['idsit_laboral'] = null;
		}
		
		if(empty($fields['idocupacion'])){
			$fields['idocupacion'] = null;
		}
		
		if(empty($fields['ingreso_mensual'])){
			$fields['ingreso_mensual'] = 0;
		}

		if(empty($fields['fecha_nac'])){
			$fields['fecha_nac'] = null;
		}
		
		if(empty($fields['limite_credito'])){
			$fields['limite_credito'] = 0;
		}
		
		if(!isset($fields['especial'])){
			$fields['especial'] = 'N';
		}else{
			if(empty($fields['especial'])){
				$fields['especial'] = 'N';
			}else{
				$fields['especial'] = 'S';
			}
		}
		
		// if(!isset($fields['linea_credito'])){
			// $fields['linea_credito'] = 'N';
		// }else{
			// if(empty($fields['linea_credito'])){
				// $fields['linea_credito'] = 'N';
			// }else{
				// $fields['linea_credito'] = 'S';
			// }
		// }
		
		$long_dni = $this->get_param("long_dni", 8);
		$long_ruc = $this->get_param("long_ruc", 11);
		$DNI_ = trim($fields['dni']);
		$RUC_ = trim($fields['ruc']);
		/////////////////////////////////////////////////// verificando el dni ...
		if(!empty($DNI_)) {
			if(strlen($DNI_) != $long_dni) {
				$this->exception('El DNI debe tener '.$long_dni.' caracteres.');
				return false;
			}
			
			// $cod = (!empty($fields["idcliente"])) ? $fields["idcliente"] : 'null';
			$cod = (!empty($fields["idcliente"])) ? $fields["idcliente"] : '0';
			
			$q=$this->db->query("SELECT count(*) cant FROM venta.cliente WHERE dni = '".$DNI_."' AND idcliente<>{$cod} AND  estado = 'A';");
			
			$has = $q->row()->cant;
			
			if($has > 0) {//ESTO X AHORA NO XK ESTAMOS EN CORRECCION
				$this->exception('El DNI que ha ingresado ya se encuentra registrado.');
				return false;
			}
		}
		/////////////////////////////////////////////////// verificando el ruc ...
		if(!empty($RUC_)) {
			if(strlen($RUC_) != $long_ruc) {
				$this->exception('El RUC debe tener '.$long_ruc.' caracteres.');
				return false;
			}
			
			// $cod = (!empty($fields["idcliente"])) ? $fields["idcliente"] : 'null';
			$cod = (!empty($fields["idcliente"])) ? $fields["idcliente"] : '0';
			
			$q=$this->db->query("SELECT count(*) cant FROM venta.cliente WHERE ruc = '".$RUC_."' AND idcliente<>{$cod} AND estado = 'A';");
			
			$has = $q->row()->cant;
			
			if($has > 0) {//ESTO X AHORA NO XK ESTAMOS EN CORRECCION
				$this->exception('El RUC que ha ingresado ya se encuentra registrado.');
				return false;
			}
		}

		$fields['foto'] = imagen_upload('foto','./app/img/cliente/','anonimo.jpg',true);

		$this->db->trans_start(); // inciamos transaccion
		
		if(empty($fields["idcliente"])) {
			$fields['fecha_registro']	= date("Y-m-d");
			$fields["accion"]			= "Creado";
			$idcliente = $this->cliente->insert($fields);

			$this->auditar_cliente($_REQUEST,array("idcliente"), $this->cliente);
		} else {
			$fields["accion"]			= "Editar";
			$idcliente = $fields["idcliente"];
			$this->cliente->update($fields);
		}
		
		$fields['direccion_principal'] = '';
		$this->db->query("DELETE FROM venta.cliente_direccion WHERE idcliente='$idcliente'; ");
		if(!empty($fields['direccion'])){
			$this->load_model("cliente_direccion");
			foreach($fields['direccion'] as $k=>$v){
				if(trim($v)){
					$data1["dir_principal"] = 'N';
					if (isset($fields['dir_principal'][$k]) && !empty($fields['dir_principal'][$k]) ) {
						$data1["dir_principal"] = $fields['dir_principal'][$k];
					}
					
					if($data1["dir_principal"]=='S'){
						$fields['direccion_principal'] = $v;
					}
					
					$data1["idcliente"] = $idcliente;
					$data1["direccion"] = $v;
					$data1["estado"] 	= 'A';
					$this->cliente_direccion->insert($data1);					
				}
			}
		}
		
		
		
		
		
		$this->db->query("DELETE FROM venta.cliente_telefono WHERE idcliente='$idcliente' ;");
		if(!empty($fields['telefono'])){
			$this->load_model("cliente_telefono");
			foreach($fields['telefono'] as $k=>$v){
				if(trim($v)){
					$data2["idcliente"] = $idcliente;
					$data2["telefono"] = $v;
					$data2["estado"] 	= 'A';
					$this->cliente_telefono->insert($data2);
				}
			}
		}
		
		$this->db->query("DELETE FROM venta.cliente_representante WHERE idcliente='$idcliente'; ");
		if(!empty($fields['nombre_representante'])){
			$this->load_model("cliente_representante");
			foreach($fields['nombre_representante'] as $k=>$v){
				if(trim($v)){
					$data3["idcliente"] = $idcliente;
					$data3["nombre_representante"] = $v;
					$data3["apellidos_representante"] = $fields["apellidos_representante"][$k];
					$data3["dni_representante"] = $fields["dni_representante"][$k];
					$data3["estado"] 	= 'A';
					$this->cliente_representante->insert($data3);					
				}
			}
		}
		/*
		AGREGAR LIMITE DE CREDITO
		*/
		
		$this->db->query("DELETE FROM venta.cliente_lineadecredito WHERE idcliente='$idcliente'; ");
		$idempresa	= $this->get_var_session("idempresa");
		$this->load_model("cliente_lineadecredito");
			$data4["idcliente"] = $idcliente;
			$data4["idempresa"] = $idempresa;
			$data4["limite_credito"] 	= 0;
		$this->cliente_lineadecredito->insert($data4);					
		
		
		
		
		
		
		
		// FIn de Agregar Credito
		
		
		
		$this->db->query("UPDATE venta.cliente SET direccion_principal='{$fields['direccion_principal']}' WHERE idcliente='{$idcliente}';");
		
		if(!empty($fields['idzona']))
			$this->db->query("UPDATE cobranza.hoja_ruta SET idzona='{$fields['idzona']}' WHERE idcliente='{$idcliente}';");
		
		$this->db->trans_complete(); // finalizamos transaccion
		$this->response($idcliente);
		//$this->response($fields);
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model("cliente");
		
		$fields['idcliente'] = $id;
		$fields['estado'] = "I";
		
		$fields['controller']=$this->controller;
		$fields['accion']=__FUNCTION__;
		
		$this->cliente->update($fields);
		
		$this->response($fields);
	}
	
	public function direcciones($id=null){
		$respuesta = $this->db->query("SELECT*FROM venta.cliente_direccion WHERE idcliente='$id' ");
		return $respuesta->result_array();
	}
	
	public function telefonos($id=null){
		$respuesta = $this->db->query("SELECT*FROM venta.cliente_telefono WHERE idcliente='$id' ");
		return $respuesta->result_array();
	}
	
	public function representantes($id=null){
		$respuesta = $this->db->query("SELECT*FROM venta.cliente_representante WHERE idcliente='$id' ");
		return $respuesta->result_array();
	}
	
	public function referencia($id=null){
		$respuesta = $this->db->query("SELECT observacion FROM venta.cliente  WHERE idcliente='$id' ");
		return $respuesta->result_array();
	}
	
	public function lista_creditos($id=null){
		$post 		= $this->input->post();
		$idempresa	= $this->get_var_session("idempresa");
		$respuesta = $this->db->query(" SELECT cred.*,ec.descripcion estadocredito
										FROM 
										credito.credito cred
										JOIN credito.estado_credito ec ON ec.id_estado_credito= cred.id_estado_credito
										JOIN seguridad.sucursal seg ON seg.idsucursal = cred.idsucursal
										WHERE cred.idcliente='$id' 
										AND seg.idempresa='$idempresa'
																			AND cred.estado='A'");
		return $respuesta->result_array();
	}
	
	public function get($id=null){
		$this->load_model($this->controller);
		$fields = $this->socio->find($id);
		return $fields;
	}
	
	public function get_post(){
		$fields = $this->input->post();
		$this->load_model($this->controller);
		$fields = $this->socio->find($fields['id']);
		
		$this->response($fields);
	}
	
	public function autocomplete() {
		$txt = $this->input->post("startsWith").'%';
		
		$sql = "SELECT idcliente, trim(nombres) nombres, COALESCE(trim(apellidos),'') apellidos, COALESCE(dni,'') dni, COALESCE(ruc,'') ruc
			FROM venta.cliente
			WHERE estado='A' 
			and (nombres ILIKE ? OR apellidos ILIKE ? OR dni ILIKE ? OR ruc ILIKE ?)
			ORDER BY nombres, apellidos
			LIMIT ?";
		$query = $this->db->query($sql, array($txt, $txt, $txt, $txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
	
	public function get_saldo($idcliente) {
		$idempresa	= $this->get_var_session("idempresa");
		$this->load_model("venta.cliente_lineadecredito");
		$this->load_model("venta.cliente");
		$datos = $this->cliente_lineadecredito->find(array("idcliente"=>$idcliente,"idempresa"=>$idempresa));
		$datos["saldo"] = $this->cliente->saldo($idcliente);
		$this->response($datos);
	}
	
	public function auditar($idcliente){
		$q=$this->db->query("	SELECT 
								DISTINCT accion
								,to_char((auditar_cliente.fecha_registro),'DD/MM/YYYY') fecha
								,to_char(auditar_cliente.hora_registro,'HH:MM:SS AM') hora
								,(valor_campo) valor_linea
								,(COALESCE(usuario,u.nombres)) usuario
								FROM venta.auditar_cliente 
								JOIN seguridad.view_usuario u ON u.idusuario=auditar_cliente.idusuario
								WHERE valor_pk='{$idcliente}' 
								AND name_pk='IDCLIENTE' AND accion='CREADO'
								UNION

								(SELECT 
								accion
								,to_char((auditar_cliente.fecha_registro),'DD/MM/YYYY') fecha
								,to_char(auditar_cliente.hora_registro,'HH:MM:SS AM') hora
								,(valor_campo) valor_linea
								,(COALESCE(usuario,u.nombres)) usuario
								FROM venta.auditar_cliente 
								JOIN seguridad.view_usuario u ON u.idusuario=auditar_cliente.idusuario
								WHERE valor_pk='{$idcliente}' 
								AND name_pk='IDCLIENTE' AND accion='LINEA CREDITO'
								ORDER BY idauditar_cliente DESC
								LIMIT 1);");
		return $q->result_array();
	}
	
	public function retornar_detalle(){
		$fields = $this->input->post();
		$direccion			= $this->direcciones($fields['idcliente']);
		$telefonos			= $this->telefonos($fields['idcliente']);
		$representantes		= $this->representantes($fields['idcliente']);
		$creditos			= $this->lista_creditos($fields['idcliente']);
		$observacion		= $this->referencia($fields['idcliente']);
		$cliente_Data		= $this->get($fields['idcliente']);
		$audit_cliente		= $this->auditar($fields['idcliente']);
		
		$html ='';
		
		$html.='<div class="row">';
		$html.='	<div class="col-sm-12">';
		$html.='		<div class="row">';
		if(count($telefonos)>0){
			$html.='			<div class="col-sm-8">';
			$html.='				<strong><i class="fa fa-map-marker" aria-hidden="true">&nbsp;</i><span  style="font-size:11px;">DIRECCION (ES)</span></strong>';
			foreach($direccion as $k=>$v){
				$html.='			<div><i class="fa fa-hand-o-right" aria-hidden="true">&nbsp;&nbsp;</i><span  style="font-size:11px;">'.$v['direccion'].'</span></div>';
			}
			$html.='			</div>';
			
			$html.='			<div class="col-sm-4">';
			$html.='				<strong><span  style="font-size:11px;">TELEFONO (S)</span></strong>';
			foreach($telefonos as $k=>$v){
				$html.='			<div><i class="fa fa-phone" aria-hidden="true">&nbsp;</i>'.$v['telefono'].'</div>';
			}
			$html.='			</div>';
			
		}else{
			$html.='			<div class="col-sm-12">';
			$html.='				<strong><i class="fa fa-map-marker" aria-hidden="true">&nbsp;</i><span  style="font-size:11px;">DIRECCION (ES)</span></strong>';
			foreach($direccion as $k=>$v){
				$html.='			<div><i class="fa fa-hand-o-right" aria-hidden="true">&nbsp;&nbsp;</i><span  style="font-size:11px;">'.$v['direccion'].'</span></div>';
			}
			$html.='			</div>';		
		}
		$html.='		</div>';
		$html.='		<hr style="margin-top: 5px;margin-bottom: 5px;"></hr>';
		$html.='	</div>';
		$html.='</div>';
		
		if(count($representantes)>0){
			$html.='<div class="row">';
			$html.='	<div class="col-sm-12">';
			$html.='		<div class="row">';
			$html.='			<div class="col-sm-12">';
			$html.='				<strong><i class="fa fa-briefcase" aria-hidden="true">&nbsp;&nbsp;</i><span  style="font-size:11px;">REPRESENTANTES</span></strong>';
			$html.='				<ul class="list-group clear-list">';
			foreach($representantes as $k=>$v){
				$html.='					<li class="list-group-item fist-item">';
				$html.='						<span class="pull-right"> DNI:'.$v['dni_representante'].' </span>';
				$html.='						<i class="fa fa-child" aria-hidden="true">&nbsp;&nbsp;</i><span  style="font-size:11px;">'.trim($v['nombre_representante']).' '.trim($v['apellidos_representante']).'</span>';
				$html.='					</li>';				
			}
			$html.='				</ul>';
			$html.='			</div>';
			$html.='		</div>';
			$html.='		<hr style="margin-top: 5px;margin-bottom: 5px;"></hr>';
			$html.='	</div>';
			$html.='</div>';			
		}
		
		if(count($creditos)>0){
			$html.='<div class="row">';
			$html.='	<div class="col-sm-12">';
			$html.='		<strong><span  style="font-size:11px;">CREDITOS ('.count($creditos).')</span></strong>';
			$html.='			<ul class="list-group clear-list">';
			foreach($creditos as $k=>$v){
				$html.='				<li class="list-group-item fist-item" style="padding: 5px 0px 0px 0px">';
				$html.='					<span class="pull-right"> <span class="label label-primary">'.$v['estadocredito'].'</span> </span>';
				$html.='					No Credito '.$v['nro_credito'];
				$html.='				</li>';				
			}
			// $html.='				<li class="list-group-item">';
			// $html.='					<span class="pull-right"> <span class="label label-warning">ATRAZADO</span></span>';
			// $html.='					No 20000';
			// $html.='				</li>';
			// $html.='				<li class="list-group-item">';
			// $html.='					<span class="pull-right"> <span class="label label-danger">JUDICIAL</span> </span>';
			// $html.='					No 30000';
			// $html.='				</li>';
			$html.='			</ul>';
			$html.='	</div>';
			$html.='</div>';
		}
		
		if(count($audit_cliente)>0){
			$html.='<div class="row">';
			$html.='	<div class="col-sm-12">';
			$html.='		<strong><span  style="font-size:11px;">ACCIONES</span></strong>';
			foreach($audit_cliente as $k=>$v){
				if($v["accion"]=='CREADO')
					$html.="	<div><span style='color:blue;font-size:10px;'>{$v['accion']}</span>, <span style='color:green;font-size:10px;'>{$v['usuario']}</span> el <span style='color:#ed5565;font-size:10px;'>{$v['fecha']} - {$v['hora']}</span></div>";
				else
					// $html.="	<div><span style='color:blue;font-size:10px;'>{$v['accion']}</span>, <span style='color:green;font-size:10px;'>{$v['usuario']}, {$v['valor_linea']}</span> el <span style='color:#ed5565;font-size:10px;'>{$v['fecha']} - {$v['hora']}</span></div>";
					$html.="	<div><span style='color:blue;font-size:10px;'>{$v['accion']}</span>, <span style='color:green;font-size:10px;'>{$v['usuario']}</span> el <span style='color:#ed5565;font-size:10px;'>{$v['fecha']} - {$v['hora']}</span></div>";
			}
			$html.='	</div>';
			$html.='</div>';
		}
		
		// $this->response("info"=>$html,"cliente"=>$cliente_Data);
		$this->response(array("info"=>$html,"cliente"=>$cliente_Data));
	}
	
	public function get_direcciones($id) {
		$arr = $this->direcciones($id);
		$this->response($arr);
	}
	
	public function get_all(){
		$fields = $this->input->post();
		if(empty($fields['id']))
			$fields['id']=0;
		
		$this->load_model($this->controller);
		// $this->load_model("venta.cliente_direccion");
		$this->load_model("venta.cliente_telefono");
		$this->load_model("venta.cliente_representante");
		
		$data 			= $this->cliente->find($fields['id']);
		$query = $this->db->query("SELECT *FROM venta.cliente_direccion WHERE idcliente='{$fields['id']}';");
		$direccion = $query->result_array();
		// $direccion 		= $this->cliente_direccion->find(array("idcliente"=>$fields['id']));
		
		$query = $this->db->query("SELECT *FROM venta.cliente_telefono WHERE idcliente='{$fields['id']}';");
		// $telefonos 		= $this->cliente_telefono->find(array("idcliente"=>$fields['id']));
		$telefonos = $query->result_array();
		
		$query = $this->db->query("SELECT *FROM venta.cliente_representante WHERE idcliente='{$fields['id']}';");
		$representantes = $query->result_array();
		// $representantes = $this->cliente_representante->find(array("idcliente"=>$fields['id']));
		/*
		$query = $this->db->query("SELECT *FROM venta.cliente WHERE idcliente='{$fields['id']}';");
		$otrosdatos = $query->result_array();
		*/
		$datos=array("cliente"=>$data
					,"direccion"=>$direccion
					,"telefonos"=>$telefonos
					,"representantes"=>$representantes
					);
		$this->response($datos);
	}
	
	public function linea_cliente(){
		$fields = $this->input->post();
		if(empty($fields['idcliente']))
			$fields['idcliente']=0;
		
		$this->load_model("venta.cliente_view");
		$data = $this->cliente_view->find(array("idcliente"=>$fields['idcliente']));
		
		$query = $this->db->query("	SELECT 
									idampliar_linea_credito
									,idcliente
									,to_char(f_desde , 'DD/MM/YYYY') f_desde
									,to_char(f_hasta , 'DD/MM/YYYY') f_hasta
									,monto 
									FROM credito.ampliar_linea_credito WHERE estado='A' AND f_desde<=CURRENT_DATE AND f_hasta>=CURRENT_DATE AND idcliente='{$fields['idcliente']}';");
		$ampliacion = $query->result_array();
		
		$datos= array("cliente"=>$data
					,"u_ampliacion"=>$ampliacion
				);
		$this->response($datos);
	}
	
	public function config_cliente(){
		$fields = $this->input->post();
		$idempresa	= $this->get_var_session("idempresa");
		if(empty($fields['idcliente']))
			$fields['idcliente']=0;
		
		$this->load_model("venta.cliente_viewlineadecredito");
		$this->load_model("venta.cliente");
		$this->load_model("venta.cliente_view");
		$data = $this->cliente_viewlineadecredito->find(array("idcliente"=>$fields['idcliente'],"idempresa"=>$idempresa));
		
		if(empty($data)){
			$data = $this->cliente_view->find(array("idcliente"=>$fields['idcliente']));
		}
		
		$datos= array("cliente"=>$data
					  ,"saldo"=>$this->cliente->saldo($fields['idcliente'])
				);
		$this->response($datos);
	}
	
	public function save_ampliacion(){
		$fields = $this->input->post();
		$this->load_model("credito.ampliar_linea_credito");
		$data = $this->ampliar_linea_credito->find(array("idcliente"=>$fields['idcliente'],"f_desde"=>$fields['f_desde'],"f_hasta"=>$fields['f_hasta'],"monto"=>$fields['monto']));
		if(empty($data)){//No existe concidencia, por lo cual se toma como nuevo
			$this->db->query("UPDATE credito.ampliar_linea_credito SET estado='I' WHERE idcliente='{$fields['idcliente']}';");
			$fields['estado']='A';
			$this->ampliar_linea_credito->insert($fields);
		}
		$this->response($fields);
	}
	
	public function save_bloqueo(){
		$fields = $this->input->post();
		$idempresa	= $this->get_var_session("idempresa");
		$this->load_model("venta.cliente_lineadecredito");
		$data = $this->cliente_lineadecredito->find(array("idcliente"=>$fields['idcliente'],"idempresa"=>$idempresa));
		//$this->auditar_cliente($fields,array("limite_credito"), $this->cliente);
		if(empty($data)){//No existe concidencia, por lo cual se toma como nuevo
			
			$fields['linea_credito']='S';
			$fields['idempresa']=$idempresa;
			$this->cliente_lineadecredito->insert($fields);
		}else{
			$this->db->query("UPDATE venta.cliente_lineadecredito SET linea_credito='{$fields['linea_credito']}',bloqueado='{$fields['bloqueado']}',limite_credito='{$fields['limite_credito']}'  WHERE idcliente='{$fields['idcliente']}' and idempresa='{$idempresa}' ;");
		}
		$this->response($fields);
		
			}
	
	
	/*
	public function save_bloqueo(){
		$fields = $this->input->post();
		$fields["accion"]="Linea Credito";
		$this->load_model("venta.cliente");
		$this->auditar_cliente($fields,array("limite_credito"), $this->cliente);
		$this->db->query("UPDATE venta.cliente SET linea_credito='{$fields['linea_credito']}',bloqueado='{$fields['bloqueado']}',limite_credito='{$fields['limite_credito']}' WHERE idcliente='{$fields['idcliente']}';");
		
		$this->response($fields);
	}*/
	/*
	public function save_bloqueo(){
		$fields = $this->input->post();
		$idempresa	= $this->get_var_session("idempresa");
		$fields["accion"]="Linea Credito";
		$this->load_model("venta.cliente_lineadecredito");
		//$this->auditar_cliente($fields,array("limite_credito"), $this->cliente);
		$this->db->query("UPDATE venta.cliente_lineadecredito SET limite_credito='{$fields['limite_credito']}' WHERE idcliente='{$fields['idcliente']}' and idempresa='{$idempresa}' ;");
		
		$this->response($fields);
		
		$this->load_model("venta.cliente");
		$this->auditar_cliente($fields,array("limite_credito"), $this->cliente);
		$this->db->query("UPDATE venta.cliente SET linea_credito='{$fields['linea_credito']}',bloqueado='{$fields['bloqueado']}' WHERE idcliente='{$fields['idcliente']}';");
		
	}
	
	*/
	
	
	
	
	/*
		public function save_bloqueo(){
		$fields = $this->input->post();
		$fields["accion"]="Linea Credito";
		$this->load_model("venta.cliente");
		$this->auditar_cliente($fields,array("limite_credito"), $this->cliente);
		$this->db->query("UPDATE venta.cliente SET linea_credito='{$fields['linea_credito']}',bloqueado='{$fields['bloqueado']}',limite_credito='{$fields['limite_credito']}' WHERE idcliente='{$fields['idcliente']}';");
		
		$this->response($fields);
	}
	}*/
	
	public function is_activo($idcliente) {
		$this->load_model("venta.cliente");
		$data = $this->cliente->find($idcliente);
		
		$res = array("code"=>"ok", "msg"=>"El cliente esta <strong>ACTIVOssss</strong>. RUC ".$data["ruc"]);
		
		if(empty($data["ruc"])) {
			$res["code"] = "error";
			$res["msg"] = "El cliente no tiene RUC";
		}
		else if(strlen($data["ruc"]) != 11) {
			$res["code"] = "error";
			$res["msg"] = "El RUC debe tener 11 digitos: ".$data["ruc"];
		}else {
			$rs = $this->consultaruc($data["ruc"]);
			//if(empty($rs)) {
				if($rs["result"] === false) {
				$res["code"] = "error";
				$res["msg"] = "El Ruc Ingresado No es Valido Verifique en Sunat.".$rs["result"];
				//$res["msg"] = "El Ruc Ingresado No es Valido Verifique en SunatNos se ha podido obtener informaci&oacute;n, intente nuevamente.";
			}else if($rs["result"]["estado"] !== "ACTIVO") {
				$res["code"] = "error";
				$res["msg"] = "El contribuyente se encuentra como <strong>".$rs["result"]["estado"]."</strong>. Verifique RUC ".$data["ruc"];
			}
		}
		
		$this->response($res);
	}
}
?>