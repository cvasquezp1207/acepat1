<?php

include_once "Controller.php";

class Hojaruta extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Hoja Ruta");
		$this->set_subtitle("Lista de Hoja Ruta");
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
		$this->load->library('combobox');
		
		// combo tipo documento
		$this->combobox->init();
		$this->combobox->setAttr(
			array(
				"id"=>"idtipodocumento"
				,"name"=>"idtipodocumento"
				,"class"=>"form-control input-xs"
				,"required"=>""
			)
		);
		$this->db->select('idtipodocumento, descripcion');
		$query = $this->db->where("estado", "A")->where("mostrar_en_cobranzas", "S")
			->order_by("descripcion", "asc")->get("venta.tipo_documento");
		$this->combobox->addItem($query->result_array());

		$data["tipodocumento"] = $this->combobox->getObject();

		// combo rutas
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr("id","idubigeo");
		$this->combobox->setAttr("name","idubigeo");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->combobox->setAttr("required","");
		$this->db->select('idubigeo,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.ubigeosorsa");
		$this->combobox->addItem("","TODOS");
		$this->combobox->addItem($query->result_array());
		$data['ruta'] = $this->combobox->getObject();
		
		// COMBO ZONA
		$this->combobox->init();
		$this->combobox->setAttr("id", "idzona_cartera");
		$this->combobox->setAttr("name", "idzona_search");
		$this->combobox->setAttr("class", "form-control input-xs");
		
		$this->db->select('idzona, zona');
		$query = $this->db->where("estado", "A")->order_by("zona", "asc")->get("general.zona");
		$this->combobox->addItem("0","[TODOS]");
		$this->combobox->addItem($query->result_array());

		$data["zona"] = $this->combobox->getObject();
		// COMBO ZONA


		// COMBO ESTADO CREDITO
		$this->combobox->init();
		$this->combobox->setAttr("id", "id_estado_credito");
		$this->combobox->setAttr("name", "id_estado_credito");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->setAttr("required", "");
		$this->db->select('id_estado_credito, descripcion');
		$query = $this->db->where("estado", "A")->where("idsucursal", $this->get_var_session("idsucursal"))->get("cobranza.view_estadocredito_cartera");
		$this->combobox->addItem("0","[TODOS]");
		$this->combobox->addItem($query->result_array());
		$data["estadocredito"] = $this->combobox->getObject();
		// COMBO ESTADO CREDITO
		
		
		// COMBO TIPO VENTA
		$venta_contado_ver = $this->get_param('ventacontado_hojaruta')? $this->get_param('ventacontado_hojaruta'):'N';
		$idventacredito    = $this->get_param('idpago_compra_credito');
		
		$this->combobox->init();
		$this->combobox->setAttr("id", "idtipoventa");
		$this->combobox->setAttr("name", "idtipoventa");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->setAttr("required", "");
		$this->db->select('idtipoventa, descripcion');
		// $query = $this->db->where("estado", "A")->where("mostrar_en_venta", $this->get_var_session("idsucursal"))->get("venta.tipo_venta");
		if($venta_contado_ver=='S')
			$query = $this->db->where("estado", "A")->get("venta.tipo_venta");
		else
			$query = $this->db->where("estado", "A")->where("idtipoventa", $idventacredito)->get("venta.tipo_venta");
		$this->combobox->addItem("0","[TODOS]");
		$this->combobox->addItem($query->result_array());
		$data["tipo_venta"] = $this->combobox->getObject();
		// COMBO TIPO VENTA
		


		// COMBO COBRADORES
		$rolcobrador 		= $this->get_param("idrolcobrador")?$this->get_param("idrolcobrador"):'0';
		$idperfilcobrador 	= $this->get_param("idperfilcobrador");
		$iduser_us   		= $this->get_var_session("idusuario");
		$idsucr_us   		= $this->get_var_session("idsucursal");
		$idperfil   		= $this->get_var_session("idperfil");
		$es_cobrador 		= $this->extrac_rol_user($iduser_us,$idsucr_us,$rolcobrador);
		$this->combobox->init();
		$this->combobox->setAttr("id", "idcobrador");
		$this->combobox->setAttr("name", "idcobrador");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->setAttr("required", "");
		$this->db->select('idusuario, empleado');
		if($es_cobrador=='A' && $idperfil == $idperfilcobrador){
			$query = $this->db->where("estado", "A")->where("idtipoempleado", $rolcobrador)->where("idsucursal", $idsucr_us)->where("idusuario", $iduser_us)->get("cobranza.view_cobradores");
		}else{
			$query = $this->db->where("estado", "A")->where("idtipoempleado", $rolcobrador)->where("idsucursal", $idsucr_us)->get("cobranza.view_cobradores");
			// $this->combobox->addItem("0","[TODOS]");
		}
		$this->combobox->addItem($query->result_array());
		$data["cobradores_empleados"] = $this->combobox->getObject();
		// COMBO COBRADORES
		
		
		//->LISTA COBRADORES PARA HACER EL CAMBIO
		$this->combobox->init();
		$this->combobox->setAttr("id", "idcobrador_past");
		$this->combobox->setAttr("name", "idcobrador_past");
		$this->combobox->setAttr("class", "form-control input-xs combo_cobrador");
		$this->combobox->setAttr("required", "");
		$this->db->select('idusuario, empleado');
		
		$query = $this->db->where("estado", "A")->where("idtipoempleado", $rolcobrador)->where("idsucursal", $idsucr_us)->get("cobranza.view_cobradores");
		$this->combobox->addItem("","Seleccione...");
		$this->combobox->addItem($query->result_array());
		$data["cobradores"] = $this->combobox->getObject();
		//->LISTA COBRADORES PARA HACER EL CAMBIO

		$data["controller"] = $this->controller;

		$data["carterita"] = $this->consulta_cartera();
		$data["botones"] = $this->armar_botones();
		$data["es_cobrador"] = $es_cobrador;
		$data["user_session"] = $iduser_us;

		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		
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

	public function save_incidencia(){
		$fields = $this->input->post();

		$this->db->trans_start(); // inciamos transaccion

		$this->load_model("cobranza.visita");

		if (empty($fields["fecha_prox_visita"])) {
			unset($fields["fecha_prox_visita"]);
		}

		if (empty($fields["posible_pago"])) {
			unset($fields["posible_pago"]);
		}

		if (empty($fields["serie"])) {
			unset($fields["serie"]);
		}

		if (empty($fields["numero"])) {
			unset($fields["numero"]);
		}

		if (empty($fields["monto_cobrado"])) {
			$fields["monto_cobrado"]=0;
		}
		
		$fields["estado"]='A';
		$fields["idempleado"]=$this->get_var_session("idusuario");
		$fields["fecha_visita"]=date("Y-m-d");
		$fields["hora_visita"]=date("H:i:s");

		if(empty($fields["idvisita"])) {
			$idvisita = $this->visita->insert($fields);
		}else {
			$idvisita = $fields["idvisita"];
			$this->visita->update($fields);
		}

		if ($fields['compromiso']=='S') {
			$this->load_controller("credito");
			$this->credito_controller->guardar_observacion($fields['idcredito']);
		}
		$fields['idvisita'] = $idvisita;
		$this->db->trans_complete(); // finalizamos transaccion
		$this->response($fields);
	}
	
	public function ver_incidencias(){
		$fields = $this->input->post();
		$resp = array();
		$and_where = '';
		if(!empty($fields['fecha_inicio']))
			if(!empty($fields['fecha_fin']))
				$and_where.=" AND fecha_visita>='{$fields['fecha_inicio']}' AND  fecha_visita<='{$fields['fecha_fin']}' ";
			else
				$and_where.=" AND fecha_visita='{$fields['fecha_inicio']}' ";
			
		//if(!empty($fields['idcliente']))
		//	$and_where.=" AND idcliente='{$fields['idcliente']}' ";
		
		if(!empty($fields['idcredito']))
			$and_where.=" AND idcredito='{$fields['idcredito']}' ";
		
		$query = $this->db->query("SELECT*FROM cobranza.visita WHERE estado='A' AND idempleado='{$this->get_var_session('idusuario')}' $and_where ");
		//echo "SELECT*FROM cobranza.visita WHERE estado='A' AND idempleado='{$this->get_var_session('idusuario')}' $and_where ";

		return $this->response($query->result_array());
	}

	public function credito_cliente(){
		$fields = $this->input->post();
		$estados_creditos = $this->creditos_cartera();
		if (empty($estados_creditos)) {
			$estados_creditos = array(null);
		}

		$query = $this->db->query("	SELECT idcredito,nro_credito 
									FROM credito.credito 
									WHERE id_estado_credito IN ('".implode("','", $estados_creditos)."') 
									AND idcliente='{$fields['idcliente']}'");
		$this->response($query->result_array());
	}
	
	public function cobradores_lista(){//
		$fields = $this->input->post();
		$rolcobrador 		= $this->get_param("idrolcobrador");
		$idsucr_us   		= $this->get_var_session("idsucursal");
		$sql = $this->db->query("SELECT idusuario idcobrador, empleado cobrador FROM cobranza.view_cobradores WHERE idusuario!='{$fields['idcobrador']}' AND estado='A' AND idtipoempleado='{$rolcobrador}' AND idsucursal='{$idsucr_us}';");
		$this->response($sql->result_array());
	}
	
	public function guardar_intercambio(){
		$fields 			 = $this->input->post();
		$fields['idsucursal']= $this->get_var_session("idsucursal");
		
		// $this->load_model(array( "cobranza.hoja_ruta"));
		// $this->hoja_ruta->find(array("idcobrador"=>$fields['idcobrador_past'],"idsucursal"=>$fields['idsucursal']));
		$data['idcobrador']=$fields['idcobrador_new'];
		$this->db->query("UPDATE cobranza.hoja_ruta SET idcobrador='{$fields['idcobrador_new']}' WHERE idcobrador='{$fields['idcobrador_past']}' AND idsucursal='{$fields['idsucursal']}';");
		// $this->hoja_ruta->update($data);
		$this->response($data);
	}
	
	public function armar_botones(){
		$arr = $this->get_permisos();
		$li='';
		if(!empty($arr)) {
			foreach($arr as $row) {
				$li.="<li><a href='#' id='{$row['id_name']}'><i class='fa {$row['icono']}'></i> {$row['descripcion']}</a></li>";
			}
		}
		return $li;
	}

	// public function query_master(){
		// $INNER = '';
		// $con_ventacontado = $this->get_param('ventacontado_hojaruta')?$this->get_param('ventacontado_hojaruta'):'N';
		// if($con_ventacontado=='S'){
			// $INNER = 'LEFT';
		// }
		// $sql="	SELECT
				// COALESCE(c.idcliente,cli_venta.idcliente) idcliente1
				// ,COALESCE(c.cliente,cli_venta.cliente) cliente1
				// ,COALESCE(c.direccion,cli_venta.direccion) direccion1
				// ,MIN(to_char(l.fecha_vencimiento,'DD/MM/YYYY')) fecha_vencimiento
				// ,(MIN(l.nro_letra)||'-'||MAX(l.nro_letra)) letras_vencidas
				// ,COALESCE(SUM(l.monto_letra),'0.00') monto_letra
				// ,COALESCE(SUM(l.mora),'0.00') monto_mora
				// ,COALESCE(SUM(l.gastos),'0.00') monto_gastos
				// ,COALESCE(c.nro_credito,'S/N') nro_credito
				// ,c.estado_credito
				// ,COALESCE(zona.zona,'SIN ZONA') zona
				// ,CAST('1' as integer) asignadocartera
				// ,COALESCE(c.central_riesgo,'N') central_riesgo
				// ,COALESCE(MAX(v.idvisita),0) idvisita
				// ,h.idcredito
				// ,COALESCE(h.idzona,0) idzona
				// ,COALESCE(h.idventa,venta.idventa) idventa
				// ,c.id_estado_credito
				// ,COALESCE(h.idcobrador,venta.idvendedor) idcobrador1
				// ,COALESCE(c.pagado,'S') pagado
				// ,COALESCE(h.orden,0) orden
				// ,COALESCE(h.orden_item,0) orden_item
				// ,tipo_venta.descripcion tipoventa
				// ,venta.idtipoventa
				// FROM venta.venta_view venta
				// JOIN venta.tipo_venta ON venta.idtipoventa=tipo_venta.idtipoventa
				// {$INNER} JOIN cobranza.hoja_ruta h ON h.idventa=venta.idventa --
				// {$INNER} JOIN credito.credito_view c ON c.idcredito=h.idcredito --
				// {$INNER} JOIN credito.letra l ON l.idcredito=c.idcredito AND l.pagado='N' --
				// LEFT JOIN venta.cliente_view cli_venta ON cli_venta.idcliente=venta.idcliente
				// LEFT JOIN general.zona ON zona.idzona=h.idzona
				// LEFT JOIN cobranza.visita v ON v.idcredito=c.idcredito AND v.estado='A' AND v.fecha_visita=CURRENT_DATE
				// GROUP BY c.idcliente
				// ,cliente1
				// ,direccion1
				// ,c.nro_credito
				// ,c.estado_credito
				// ,zona.zona
				// ,central_riesgo
				// ,h.idcredito
				// ,h.idventa
				// ,c.id_estado_credito
				// ,h.idzona
				// ,idcobrador1
				// ,c.pagado
				// ,h.orden
				// ,h.orden_item
				// ,venta.idventa
				// ,idcliente1
				// ,tipoventa
				// ,venta.idtipoventa
				// ORDER BY h.orden
				// ,h.orden_item,zona,cliente1,direccion1";
		// return $sql;
	// }
	
	public function query_master(){
		$INNER = '';
		// $con_ventacontado = $this->get_param('ventacontado_hojaruta')?$this->get_param('ventacontado_hojaruta'):'N';
		// if($con_ventacontado=='S'){
			// $INNER = 'LEFT';
		// }
		if(!isset($_REQUEST['idcobrador']))
			$_REQUEST['idcobrador'] = 0;
		$sql="	SELECT 
				COALESCE(h.idcliente,cli_venta.idcliente) idcliente1 
				,trim(COALESCE(c.cliente,cli_venta.cliente)) cliente1 
				,trim(COALESCE(c.cliente,cli_venta.cliente)) cliente
				,trim(COALESCE(c.direccion,cli_venta.direccion)) direccion1 
				,MIN(to_char(l.fecha_vencimiento,'DD/MM/YYYY')) fecha_vencimiento 
				,(MIN(l.nro_letra)||'-'||MAX(l.nro_letra)) letras_vencidas 
				,COALESCE(SUM(l.monto_letra),'0.00') monto_letra 
				,COALESCE(SUM(l.mora),'0.00') monto_mora 
				,COALESCE(SUM(l.gastos),'0.00') monto_gastos 
				,COALESCE(c.nro_credito,'S/N') nro_credito 
				,c.estado_credito ,COALESCE(zona.zona,'SIN ZONA') zona 
				,CAST('1' as integer) asignadocartera 
				,COALESCE(c.central_riesgo,'N') central_riesgo 
				,COALESCE(0) idvisita ,h.idcredito ,COALESCE(h.idzona,0) idzona 
				,COALESCE(h.idventa,v.idventa) idventa 
				,c.id_estado_credito 
				,COALESCE(h.idcobrador,v.idvendedor) idcobrador1 
				,COALESCE(c.pagado,'S') pagado 
				,COALESCE(h.orden,0) orden ,COALESCE(h.orden_item,0) orden_item 
				,tipo_venta.descripcion tipoventa 
				,v.idtipoventa 
				,h.idsucursal 
				,vend.user_nombres vendedor
				,v.comprobante
				,to_char(v.fecha_venta,'DD/MM/YYYY') fecha_emision
				,(CURRENT_DATE- v.fecha_venta) dias_transcurrido
				,CASE WHEN MIN(l.fecha_vencimiento)>CURRENT_DATE THEN 0 ELSE (CURRENT_DATE- MIN(l.fecha_vencimiento)) END dias_atrazo
				,v.moneda
				,cli_venta.limite_credito linea_credito
				,v.total total_operacion
				,am.total_amortizacion
				,COALESCE(SUM(l.monto_letra)  +  SUM(l.mora) + SUM(l.gastos) -  am.total_amortizacion,0) total_deuda_vigente
				,doc_cliente
				,h.estado
				,zona.idubigeo
				FROM cobranza.hoja_ruta h
				JOIN venta.venta_view v ON v.idventa=h.idventa AND v.idsucursal=h.idsucursal
				JOIN seguridad.view_usuario vend ON vend.idusuario=v.idvendedor
				JOIN venta.tipo_venta ON v.idtipoventa=tipo_venta.idtipoventa
				JOIN venta.cliente_view cli_venta ON cli_venta.idcliente=v.idcliente
				JOIN general.zona ON zona.idzona=h.idzona
				LEFT JOIN credito.credito_view c ON c.idcredito=h.idcredito AND c.idsucursal=v.idsucursal AND c.estado='A' AND h.idcobrador={$_REQUEST['idcobrador']}
				LEFT JOIN credito.letra l ON l.idcredito=c.idcredito AND l.pagado='N' 
				LEFT JOIN(
					SELECT (SUM(monto)+SUM(mora)) total_amortizacion,idletra,idcredito FROM credito.amortizacion WHERE estado='A'  GROUP BY idletra,idcredito
				) am ON am.idletra=l.idletra AND c.idcredito=am.idcredito
				GROUP BY c.idcliente ,cliente1 ,direccion1 ,c.nro_credito ,c.estado_credito ,zona.zona ,central_riesgo ,h.idcredito ,h.idventa 
				,c.id_estado_credito ,h.idzona ,idcobrador1 ,c.pagado ,h.orden ,h.orden_item ,v.idventa ,idcliente1 ,tipoventa ,v.idtipoventa ,h.idsucursal 
				,vend.user_nombres
				,v.comprobante
				,v.fecha_venta
				,v.moneda
				,cli_venta.limite_credito
				,total_operacion
				,total_amortizacion
				,doc_cliente
				,h.estado
				,zona.idubigeo
				ORDER BY h.orden,h.orden_item,zona,cliente1,direccion1";
		// echo "<pre>";
		// echo $sql;exit;
		return $sql;
	}
	
	public function consulta_cartera($ajax = false,$fields=array()){
		set_time_limit(0);
		if(empty($fields))
			$fields = $this->input->post();
		
		$sql="	SELECT 
				idcliente1 idcliente
				,cliente1 cliente
				,direccion1 direccion
				,pagado
				,fecha_vencimiento
				,idcobrador1 idcobrador
				,letras_vencidas
				,monto_letra
				,monto_mora
				,monto_gastos
				,nro_credito
				,estado_credito
				,zona
				,asignadocartera
				,central_riesgo
				,idcredito
				,idzona
				,idventa
				,id_estado_credito FROM (
				{$this->query_master()}) as query
				WHERE {$this->filtro_cartera($fields)} ;";
		// echo $sql;exit;
		$query = $this->db->query($sql);
		$resp = array("filas"=>$query->result_array(),"cantidad"=>count($query->result_array()));
		if (isset($fields['ajax'])) {
			$this->response($resp);
		}else{
			return $resp;
		}
	}
	
	public function datos_excel($fields=array()){
		if(empty($fields))
			$fields = $this->input->post();
		
		$sql="	SELECT 
				idcliente1 idcliente
				,dias_transcurrido
				,vendedor
				,comprobante
				,fecha_emision
				,COALESCE(dias_atrazo,0) dias_atrazo
				,idzona
				,total_operacion
				,cliente1 cliente
				,direccion1 direccion
				,fecha_vencimiento
				,moneda
				,COALESCE(linea_credito,0) linea_credito
				,COALESCE(total_amortizacion,0) total_amortizacion
				,COALESCE(monto_mora,0) total_moda
				,total_deuda_vigente
				,doc_cliente
				,0 dif_0
				,0 dif_0130
				,0 dif_3060
				,0 dif_6090
				,0 dif_90
				FROM (
				{$this->query_master()}) as query
				WHERE {$this->filtro_cartera($fields)} 
				ORDER BY dias_atrazo;";
		// echo $sql;Exit;
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function generar_hoja(){
		$this->response($this->genera_hojaruta());
	}
	
	public function cargar_zonas(){
		$fields = $this->input->post();
		$idsucr_us   		= $this->get_var_session("idsucursal");
		
		$and_where = '';
		// $add_item='';
		if(!empty($fields['idubigeo'])){
			$and_where.= " AND idubigeo='{$fields['idubigeo']}'";
		}else if(!empty($fields['id_ubigeo'])){
			$and_where.= " AND idubigeo='{$fields['id_ubigeo']}'";
		}else{
			$and_where.= " AND idubigeo='0'";
		}
	
		// if(empty($fields['order'])){
			// $add_item.=',orden';
		// }

		$sql=$this->db->query("	SELECT
								z.idzona
								,trim(z.zona) zona_h
								,COALESCE(zc.orden,0) orden
								FROM cobranza.zona_cobrador zc
								JOIN general.zona z ON z.idzona=zc.idzona
								WHERE zc.idsucursal={$idsucr_us} AND zc.idempleado={$fields['idcobrador']}
								{$and_where}
								ORDER BY orden;");
		$this->response($sql->result_array());
	}
	
	public function cargar_ruta(){
		$fields = $this->input->post();
		$idsucr_us   		= $this->get_var_session("idsucursal");
		
		$sql=$this->db->query("	SELECT
								DISTINCT u.idubigeo
								,u.descripcion ruta
								FROM cobranza.zona_cobrador zc
								JOIN general.zona z ON z.idzona=zc.idzona
								JOIN general.ubigeozona_view u ON u.idubigeo=z.idubigeo
								WHERE zc.idsucursal={$idsucr_us} AND zc.idempleado={$fields['idcobrador']}
								ORDER BY ruta;");
		$this->response($sql->result_array());
	}
	
	public function cargar_clientes(){
		$fields = $this->input->post();
		$fields['idzona'] = trim($fields['idzona']);
		$idsucr_us   		= $this->get_var_session("idsucursal");
		
		$where = " AND idsucursal='{$idsucr_us}'";
		if(empty($fields['idzona']) || $fields['idzona']==null)
			$fields['idzona'] = 0;
		else
			$where.=" AND idzona='{$fields['idzona']}'";
		
		if(!empty($fields['idubigeo']))
			$where.=" AND idubigeo='{$fields['idubigeo']}'";
		
		if(!empty($fields['cliente']))
			$where.=" AND cliente1 ILIKE '{$fields['cliente']}%'";
		
		$sql=$this->db->query("	SELECT 
								DISTINCT idcliente1 idcliente
								,(cliente1) cliente
								,(direccion1) direccion
								,COALESCE(orden_item,0) orden_cliente
								FROM ({$this->query_master()}) q
								WHERE idcobrador1='{$fields['idcobrador']}' 
								{$where}
								; ");
		$this->response($sql->result_array());
	}

	public function get_credito(){
		$fields = $this->input->post();
		$and_where = "";
		$fields['idcredito'] = trim($fields['idcredito']);
		if(!empty($fields['idcredito']) && $fields['idcredito']!=null && $fields['idcredito']!='null'){
			// print_r($fields);exit;
			$and_where.=" AND c.idcredito='{$fields['idcredito']}' ";
		}
		$query = $this->db->query("SELECT
									cli.idcliente
									,initcap((COALESCE(cli.apellidos,'')||' '||cli.nombres)) cliente
									,initcap((SELECT direccion FROM venta.cliente_direccion WHERE cliente_direccion.idcliente=cli.idcliente AND dir_principal='S')) direccion
									,MIN(to_char(letra.fecha_vencimiento,'DD/MM/YYYY')) fecha_vencimiento
									,(MIN(nro_letra)||'-'||MAX(nro_letra)) letra_vencida
									,COALESCE(SUM(letra.monto_letra),'0.00') monto_letra
									,COALESCE(SUM(letra.mora),'0.00') monto_mora
									,COALESCE(SUM(letra.gastos),'0.00') monto_gastos
									,c.nro_credito
									,cli.idzona
									,zona
									,c.idcredito
									,c.idventa
									FROM credito.credito c
									JOIN venta.cliente cli ON cli.idcliente=c.idcliente 
									LEFT JOIN general.zona ON zona.idzona=cli.idzona
									JOIN credito.estado_credito ec ON ec.id_estado_credito = c.id_estado_credito
									JOIN credito.letra ON letra.idcredito=c.idcredito AND letra.pagado='N'
									WHERE cli.idcliente='{$fields['idcliente']}' 
									AND c.idventa='{$fields['idventa']}'
									GROUP BY cli.idcliente,cliente,direccion,nro_credito,cli.idzona,zona,c.idcredito");

		$resp['credito'] = $query->result_array();

		$Where_and = "estado='A' ";
		if (!empty($fields['idvisita'])) {
			$Where_and.= "AND idvisita='{$fields['idvisita']}' ";
		}

		if (empty($fields['idempleado'])) {
			$fields['idempleado'] = $this->get_var_session("idusuario");
		}
		// $Where_and.= " AND idcredito='{$fields['idcredito']}' ";
		$Where_and.= $and_where;
		$Where_and.= " AND idempleado='{$fields['idempleado']}' ";
		$Where_and.= " AND idventa='{$fields['idventa']}' ";
		$Where_and.= " AND fecha_visita='".date('Y-m-d')."'";
		$query = $this->db->query("	SELECT
									idvisita,
									idempleado,idcredito,
									observacion,
									compromiso,
									posible_pago,
									fecha_prox_visita,
									serie,numero,monto_cobrado
									FROM cobranza.visita c
									WHERE $Where_and
									");
		$resp['visitas'] = $query->result_array();

		$this->response($resp);
	}

	public function filtro_cartera($fields){
		// $Where_and = " c.estado != 'I' ";
		$Where_and = " estado='A' AND idventa IS NOT NULL ";
		$Where_and.= " AND idsucursal= {$this->get_var_session("idsucursal")} ";
		$estados_creditos = $this->creditos_cartera();
		// $Where_and.=" AND cli.idzona IN (SELECT h.idzona FROM cobranza.hoja_ruta h WHERE h.idsucursal='{$this->get_var_session("idsucursal")}' ";
		// print_r($fields);exit;
		if (!empty($fields['idcobrador'])) {
			$Where_and.=" AND idcobrador1='{$fields['idcobrador']}' ";
		}else{
			$Where_and.=" AND idcobrador1='".$this->get_var_session("idusuario")."' ";
		}
		
		if (!empty($fields['id_estado_credito'])) {
			$Where_and.=" AND query.id_estado_credito='{$fields['id_estado_credito']}' ";
		}
		
		if (!empty($fields['nro_credito'])) {
			$Where_and.=" AND nro_credito='{$fields['nro_credito']}' ";
		}
		
		if (!empty($fields['idubigeo'])) {
			$Where_and.=" AND idubigeo='{$fields['idubigeo']}' ";
		}else if(!empty($fields['id_ubigeo'])){
			$Where_and.=" AND idubigeo='{$fields['id_ubigeo']}' ";
		}
		
		if (!empty($fields['idzona_search'])) {
			$Where_and.=" AND idzona='{$fields['idzona_search']}' ";
		}
		
		if (!empty($fields['idzona_cartera'])) {
			$Where_and.=" AND idzona='{$fields['idzona_cartera']}' ";
		}
		
		if (!empty($fields['cliente'])) {
			$Where_and.=" AND cliente ILIKE '{$fields['cliente']}%' ";
		}
		
		if (!empty($fields['central_riesgo'])) {
			$Where_and.=" AND central_riesgo='{$fields['central_riesgo']}' ";
		}
		
		if (!empty($fields['idtipoventa'])) {
			$Where_and.=" AND idtipoventa='{$fields['idtipoventa']}' ";
		}
		
		if (empty($estados_creditos)) {
			$estados_creditos = array('0');
		}
		
		$venta_contado_ver = $this->get_param('ventacontado_hojaruta')? $this->get_param('ventacontado_hojaruta'):'N';
		if($venta_contado_ver=='N'){
			$Where_and.=" AND query.id_estado_credito IN ('".implode("','", $estados_creditos)."') ";
		}

		return $Where_and;
	}

	public function seleccion($datos,$id,$key){
		$data = array();
		foreach($datos as $kk=>$vv){
			if($vv[$key]==$id){
				$data[]=$vv;
			}
		}	
		return $data;
	}
	
	public function arreglo_zonas($fields){
		$sql = "SELECT zona,idzona,orden FROM ({$this->query_master()}) query
				WHERE {$this->filtro_cartera($fields)} 
				GROUP BY zona,idzona,orden;";
		// echo $sql;exit;
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function guardar_orden_zona(){
		$fields = $this->input->post();
		$rolcobrador 		= $this->get_param("idrolcobrador");
		$idsucr_us   		= $this->get_var_session("idsucursal");
		$idcobrador   		= $fields['idcobrador'];
		$item = 1;
		// foreach($fields['idzona'] as $k=>$val){
			// $item++;
		// }
		
		foreach($fields['idzona'] as $k=>$val){
			$this->db->query("UPDATE cobranza.zona_cobrador SET orden='{$item}'
								WHERE idzona='{$val}'
								AND idempleado='{$idcobrador}'
								AND idsucursal='{$idsucr_us}';");
			
			$this->db->query("UPDATE cobranza.hoja_ruta SET orden='{$item}' WHERE COALESCE(idzona,0)='{$val}' AND idcobrador='$idcobrador' AND idsucursal='$idsucr_us';");
			
			$item++;
		}
		
		$this->response(true);
	}
	
	public function guardar_orden_cliente(){
		$fields = $this->input->post();
		$rolcobrador 		= $this->get_param("idrolcobrador");
		$idsucr_us   		= $this->get_var_session("idsucursal");
		$idcobrador   		= $fields['idcobrador'];
		$item = 1;
		foreach($fields['idcliente'] as $k=>$val){
				$this->db->query("UPDATE cobranza.hoja_ruta SET orden_item='{$item}' WHERE COALESCE(idzona,0)='{$fields['idzona_ref']}' AND idcobrador='$idcobrador' AND idsucursal='$idsucr_us' AND idcliente='{$val}';");
			$item++;
		}
		
		$this->response(true);
	}
	
	public function imprimir() {
		set_time_limit(0);
		$fields = $_REQUEST;
		$datos = $this->consulta_cartera(false,$fields);
		$zonas = $this->arreglo_zonas($fields);
		
		$i = 25;
		$a = 6;
		$this->load->library("pdf");
		
		$this->load_model(array( "seguridad.empresa","seguridad.sucursal","seguridad.usuario","credito.estado_credito","general.zona","venta.tipo_venta","general.ubigeosorsa"));
		
		$this->empresa->find($this->get_var_session("idempresa"));
		$this->sucursal->find($this->get_var_session("idsucursal"));
		$logo = ver_fichero_valido($this->empresa->get("logo"),FCPATH."app/img/empresa/");
		if( !empty($logo) )
			$this->pdf->SetLogo($logo);
		$this->pdf->SetTitle(utf8_decode("CARTERA DE COBRANZA DE ".$_REQUEST['cobrador']), 11, null, true);
		
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(2);
		$this->pdf->SetDrawColor(204, 204, 204);
		
		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);
		

		$this->pdf->Cell(45,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(126,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s a'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		$this->pdf->SetFont('Arial','B',9);
			
		$this->pdf->Cell(30,3,'SUCURSAL',0,0,'L');
		$this->pdf->Cell(5,3,' : ',0,0,'L');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(60,3,utf8_decode($this->sucursal->get("descripcion")),0,1,'L');
		
		if(!empty($fields['idcobrador'])){
			$this->pdf->SetFont('Arial','B',9);
			$this->usuario->find($fields['idcobrador']);
			
			$this->pdf->Cell(30,3,'COBRADOR',0,0,'L');
			$this->pdf->Cell(5,3,' : ',0,0,'L');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(60,3,utf8_decode($this->usuario->get("nombres").' '.$this->usuario->get("appat").' '.$this->usuario->get("apmat")),0,1,'L');
		}
		
		if(!empty($fields['id_estado_credito'])){
			$this->pdf->SetFont('Arial','B',9);
			$this->estado_credito->find($fields['id_estado_credito']);
			
			$this->pdf->Cell(30,3,'CREDITO',0,0,'L');
			$this->pdf->Cell(5,3,' : ',0,0,'L');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(60,3,utf8_decode($this->estado_credito->get("descripcion")),0,1,'L');
		}
		
		if(!empty($fields['idubigeo'])){
			$this->pdf->SetFont('Arial','B',9);
			$this->ubigeosorsa->find($fields['idubigeo']);
			
			$this->pdf->Cell(30,3,'RUTA',0,0,'L');
			$this->pdf->Cell(5,3,' : ',0,0,'L');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(60,3,utf8_decode($this->ubigeosorsa->get("descripcion")),0,1,'L');
		}
		
		if(!empty($fields['idzona_cartera'])){
			$this->pdf->SetFont('Arial','B',9);
			$this->zona->find($fields['idzona_cartera']);
			
			$this->pdf->Cell(30,3,'LOCALIDAD',0,0,'L');
			$this->pdf->Cell(5,3,' : ',0,0,'L');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(60,3,utf8_decode($this->zona->get("zona")),0,1,'L');
		}
		
		if(!empty($fields['nro_credito'])){
			$this->pdf->SetFont('Arial','B',9);			
			$this->pdf->Cell(30,3,'No CREDITO',0,0,'L');
			$this->pdf->Cell(5,3,' : ',0,0,'L');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(60,3,utf8_decode($fields['nro_credito']),0,1,'L');
		}
		
		if(!empty($fields['idtipoventa'])){
			$this->pdf->SetFont('Arial','B',9);			
			$this->tipo_venta->find($fields['idtipoventa']);
			$this->pdf->Cell(30,3,'TIPO VENTA',0,0,'L');
			$this->pdf->Cell(5,3,' : ',0,0,'L');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(60,3,utf8_decode($this->tipo_venta->get("descripcion")),0,1,'L');
		}
		
		// if(!empty($fields['cliente'])){
			// $this->pdf->SetFont('Arial','B',9);			
			// $this->pdf->Cell(30,3,'CLIENTE',0,0,'L');
			// $this->pdf->Cell(5,3,' : ',0,0,'L');
			// $this->pdf->SetFont('Arial','',9);
			// $this->pdf->Cell(60,3,utf8_decode($fields['cliente']),0,1,'L');
		// }
		
		//DETALLE	
		$cols = array('cliente','direccion','letras_vencidas','fecha_vencimiento','nro_credito','monto_letra','monto_mora','suma');
		$width_cli = 49;
		$width_dir = 60;
		$width_let = 14;
		$width_vec = 18;
		$width_crd = 17;
		$width_imp = 15;
		$width_mor = 15;
		$width_tot = 18;
		
		$this->pdf->Ln(5);
		$this->pdf->SetFont('Arial','B',7);
		
		$this->pdf->Cell($width_cli,$a,'CLIENTE',1,0,'C');
		$this->pdf->Cell($width_dir,$a,'DIRECCION',1,0,'C');
		$this->pdf->Cell($width_let,$a,'LETRAS',1,0,'C');
		$this->pdf->Cell($width_vec,$a,'VENCIMIENTO',1,0,'C');
		$this->pdf->Cell($width_crd,$a,'CREDITO',1,0,'C');
		$this->pdf->Cell($width_imp,$a,'IMPORTE',1,0,'C');
		$this->pdf->Cell($width_mor,$a,'MORAS',1,0,'C');
		$this->pdf->Cell($width_tot,$a,'TOTAL',1,1,'C');
		
		foreach($zonas as $k=>$v){
			$width = array($width_cli, $width_dir, $width_let, $width_vec, $width_crd, $width_imp, $width_mor, $width_tot);
			$pos = array("L", "L", "C", "C", "C", "R", "R", "R");
			$this->pdf->SetTextColor(194,8,8);
			$this->pdf->SetFont('Courier','B',9);
			$this->pdf->Cell(206,$a,$v['zona'],0,1,'L');
			$this->pdf->SetTextColor(0,0,0);
			
			$lista = $this->seleccion($datos['filas'],$v['idzona'],'idzona');
			
			$this->pdf->SetFont('Arial','',8);
			$this->pdf->SetWidths($width);
			
			// foreach($lista as $key=>$value){
				// $this->pdf->Cell(50,$a,utf8_decode($value['cliente']),1,0,'L');
				// $this->pdf->Cell(60,$a,$value['direccion'],1,0,'L');
				// $this->pdf->Cell(15,$a,$value['letras_vencidas'],1,0,'C');
				// $this->pdf->Cell(18,$a,$value['fecha_vencimiento'],1,0,'C');
				// $this->pdf->Cell(15,$a,$value['nro_credito'],1,0,'C');
				// $this->pdf->Cell(15,$a,$value['monto_letra'],1,0,'R');
				// $this->pdf->Cell(15,$a,$value['monto_mora'],1,0,'R');
				// $this->pdf->Cell(18,$a,number_format(($value['monto_mora'] + $value['monto_letra']),2),1,1,'R');
			// }
			foreach($lista as $key=>$v){
				$values = array();
				foreach($cols as $f){
					if($f=='suma'){
						$v['suma'] = number_format(($v['monto_mora'] + $v['monto_letra']),2);
					}
					$values[] = utf8_decode(($v[$f]));
					// print($value[$f])."<br>";
				}
				$this->pdf->Row($values, $pos, "Y", "Y");
			}
			$this->pdf->Ln();
		}
		$this->pdf->Output();
	}
	
	public function head_excel(){
		return array("item"=>array('ITEM')
					,"vendedor"=>array("VENDEDOR")
					,"comprobante"=>array("DOCUMENTO")
					,"fecha_emision"=>array("FEC. EMIC")
					,"dias_transcurrido"=>array("DIAS")
					,"fecha_vencimiento"=>array("FEC. VENC")
					,"moneda"=>array("MONEDA")
					,"cliente"=>array("CLIENTE")
					,"direccion"=>array("DIRECCION")
					,"doc_cliente"=>array("DOC. CLIENTE")
					,"linea_credito"=>array("LINEA C.")
					,"total_operacion"=>array("IMPORTE")
					,"total_amortizacion"=>array("PAGOS")
					,"total_deuda_vigente"=>array("SALDO")
					,"total_moda"=>array("MORA")
					,"dias_atrazo"=>array("DIAS ATRAZO")
		);
	}
	
	public function head_extra(){
		return array("dif_90"=>array('M90')
					,"dif_6090"=>array("M6090")
					,"dif_3060"=>array("M3060")
					,"dif_0130"=>array("M0130")
					,"dif_0"=>array("novence")
		);
	}
	
	public function exportar(){
		set_time_limit(0);
		$fields = $_REQUEST;

		$zonas = $this->arreglo_zonas($fields);
		$datos = $this->datos_excel($fields);
		
		$this->load_model(array( "seguridad.empresa","seguridad.sucursal","seguridad.usuario","credito.estado_credito","general.zona","venta.tipo_venta","general.ubigeosorsa"));
		$this->sucursal->find($this->get_var_session("idsucursal"));
		
		$this->load->library("phpexcel");
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"CARTERA DE CREDITO DE ".$_REQUEST['cobrador'],true);
		
		$filename='cartera'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');
		
		/************************** CABECERA *****************************************/
		$alfabeto = 65;
		$col=9;
		
		$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'SUCURSAL');
		$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$this->sucursal->get("descripcion"));
		$col++;
		
		if(!empty($fields['idcobrador'])){
			$this->usuario->find($fields['idcobrador']);
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'COBRADOR');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$this->usuario->get("nombres").' '.$this->usuario->get("appat").' '.$this->usuario->get("apmat"));
			
			$col++;
		}
		
		if(!empty($fields['id_estado_credito'])){
			$this->estado_credito->find($fields['id_estado_credito']);
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'CREDITO');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$this->estado_credito->get("descripcion"));
		
			$col++;
		}
		
		if(!empty($fields['idubigeo'])){
			$this->ubigeosorsa->find($fields['idubigeo']);
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'RUTA');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$this->ubigeosorsa->get("descripcion"));
			
			$col++;
		}
		
		if(!empty($fields['idzona_cartera'])){
			$this->zona->find($fields['idzona_cartera']);
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'LOCALIDAD');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$this->zona->get("zona"));
			
			$col++;
		}
		
		if(!empty($fields['nro_credito'])){
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'NRO CREDITO');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$fields['nro_credito']);
			$col++;
		}
		
		if(!empty($fields['idtipoventa'])){
			$this->tipo_venta->find($fields['idtipoventa']);
			
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, 'TIPO VENTA');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto+1).$col, ': '.$this->tipo_venta->get("descripcion"));
			$col++;
		}
		
		$col++;
		$aalfabeto = 65;
		foreach($zonas as $k=>$val){
			$alfabeto=65;
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true)->getColor()->setRGB('16A085');
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $val['zona']);
			$col++;
			
			$n_head = array_merge($this->head_excel(),$this->head_extra());
			$aalfabeto = 65;
			foreach($n_head as $key=>$v){
				$Oexcel->getActiveSheet()->getStyle(chr($aalfabeto).$col)->getFont()->setBold(true);
				$Oexcel->getActiveSheet()->getStyle(chr($aalfabeto).$col)->applyFromArray(
						array('borders' => array(
										'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
										'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
										'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
										'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
									)
						)
					);
				$Oexcel->getActiveSheet()->setCellValue(chr($aalfabeto).$col, $v[0]);
				$aalfabeto++;
			}
			$col++;
			$lista = $this->seleccion($datos,$val['idzona'],'idzona');
			
			foreach($lista as $key=>$val){
				$alfabeto=65;
				$val['item'] = $key+1;
				
				$n_head = array_merge($this->head_excel(),$this->head_extra());
				foreach($n_head as $k=>$v){
					if($val['dias_atrazo']<1)
						$val['dif_0'] = $val['total_deuda_vigente'];
					else if($val['dias_atrazo']>=1 && $val['dias_atrazo']<=30)
						$val['dif_0130'] = $val['total_deuda_vigente'];
					else if($val['dias_atrazo']>=31 && $val['dias_atrazo']<=60)
						$val['dif_3060'] = $val['total_deuda_vigente'];
					else if($val['dias_atrazo']>=61 && $val['dias_atrazo']<=90)
						$val['dif_6090'] = $val['total_deuda_vigente'];
					else if($val['dias_atrazo']>=91)
						$val['dif_90'] = $val['total_deuda_vigente'];
						
					
					$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $val[$k]);
					$alfabeto++;
				}
				$col++;
			}
			$col++;
		}
		/************************** CABECERA *****************************************/
		
		
		$objWriter->save('php://output');
	}
}
?>