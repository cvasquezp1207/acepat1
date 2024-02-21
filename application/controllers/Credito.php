<?php

include_once "Controller.php";

class Credito extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Creditos");
		$this->set_subtitle("Lista de creditos");
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		// $this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		// $this->js('form/'.$this->controller.'/index');
		// $this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		
		if(!isset($data["redirect"])) {
			$data["redirect"] = $this->controller;
		}
		
		$this->load->library('combobox');
		
		// combo tipo_credito
		$query = $this->db->where("estado", "A")->get("credito.tipo_credito");
		$this->combobox->setAttr(array("name"=>"id_tipo_credito", "id"=>"id_tipo_credito", "class"=>"form-control"));
		$this->combobox->addItem($query->result_array());
		if(isset($data["credito"]["id_tipo_credito"])) {
			$this->combobox->setSelectedOption($data["credito"]["id_tipo_credito"]);
		}
		$data["tipo_credito"] = $this->combobox->getObject();
		
		// combo ciclo
		$query = $this->db->where("estado", "A")->get("credito.ciclo");
		$this->combobox->init();
		$this->combobox->setAttr(array("name"=>"id_ciclo", "id"=>"id_ciclo", "class"=>"form-control"));
		$this->combobox->addItem($query->result_array(), "", array("id_ciclo", "descripcion", "dias"));
		if(isset($data["credito"]["id_ciclo"])) {
			$this->combobox->setSelectedOption($data["credito"]["id_ciclo"]);
		}
		$data["ciclo"] = $this->combobox->getObject();
		
		// combo tipo_tasa
		// $query = $this->db->where("estado", "A")->get("credito.tipo_tasa");
		// $this->combobox->init();
		// $this->combobox->setAttr(array("name"=>"id_tipo_tasa", "id"=>"id_tipo_tasa", "class"=>"form-control"));
		// $this->combobox->addItem($query->result_array());
		// if(isset($data["credito"]["id_tipo_tasa"])) {
			// $this->combobox->setSelectedOption($data["credito"]["id_tipo_tasa"]);
		// }
		// $data["tipo_tasa"] = $this->combobox->getObject();
		
		// combo estado_credito
		$query = $this->db->where("estado", "A")->get("credito.estado_credito");
		$this->combobox->init();
		$this->combobox->setAttr(array("name"=>"id_estado_credito", "id"=>"id_estado_credito", "class"=>"form-control"));
		$this->combobox->addItem($query->result_array());
		if(isset($data["credito"]["id_estado_credito"])) {
			$this->combobox->setSelectedOption($data["credito"]["id_estado_credito"]);
		}
		$data["estado_credito"] = $this->combobox->getObject();
		
		// requisitos para el credito
		$query = $this->db->where("estado", "A")->get("credito.requisito_credito");
		$data["requisitos"] = $query->result_array();
		
		// obtenemos dias de gracia
		if(!isset($data["credito"]["dias_gracia"])) {
			$data["credito"]["dias_gracia"] = $this->get_param("dias_gracia");
		}
		
		$data["controller"] = $this->controller;
		
		$nuevo = "true";
		if( isset($data["credito"]["idcredito"]) ) {
			$nuevo = "false";
		}
		$this->js("<script>var _es_nuevo_credito_ = $nuevo;</script>", false);
		$this->js("<script>var _dias_mes_ = ".$this->get_param("dias_mes").";</script>", false);
		
		// $this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
		$this->css("plugins/datapicker/datepicker3");
		$this->css('plugins/iCheck/custom');
		$this->css('plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox');
		// $this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js('plugins/iCheck/icheck.min');
		$this->js('form/'.$this->controller.'/form');
		
		// formulario CLIENTE
		$this->load_controller("cliente");
		$data["form_cliente"] = $this->cliente_controller->form(null, "cli_", true);

		$this->js('form/cliente/modal');
		
		// formulario CLIENTE(GARANTE)
		// $this->load_controller("cliente");
		// $data["form_garante"] = $this->cliente_controller->form(null, "gar_", true);
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model("credito.credito_view");
		$this->load->library('datatables');
		
		$columnasName = array(
			'fecha_credito'=>'Fecha'
			,'comprobante'=>'Comprobante'
			,'nro_credito'=>'Credito'
			,'cliente'=>'Cliente'
			,'nro_letras'=>'Letras'
			,'capital'=>'Capital'
			,'interes'=>'Interes'
			,'gastos'=>'Gastos'
			,'monto_credito'=>'Total'
		);
		
		$this->datatables->setModel($this->credito_view);
		$this->datatables->setIndexColumn("idcredito");
		
		$this->datatables->setColumns(array_keys($columnasName));
		
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('pagado', '=', 'N');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		
		$this->datatables->order_by('fecha_credito', 'desc');
		$this->datatables->setCallback("formatoFechaGrilla");
		
		$table = $this->datatables->createTable(array_values($columnasName));
		$script = "<script>".$this->datatables->createScript()."</script>";
		
		$this->js($script, false);
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo($idventa = 0) {
		$this->set_title("Registrar credito");
		$this->set_subtitle("");
		
		$data = array();
		if(!empty($idventa)) {
			$this->load_model("venta.venta_view");
			$this->venta_view->set_column_pk("idventa");
			$data["venta"] = $this->venta_view->find($idventa);
			$data["credito"]["idventa"] = $this->venta_view->get("idventa");
			$data["credito"]["idcliente"] = $this->venta_view->get("idcliente");
			$data["credito"]["idmoneda"] = $this->venta_view->get("idmoneda");
			$data["credito"]["monto_facturado"] = $this->venta_view->get("total");
			$data["credito"]["inicial"] = "0.00";
			$data["credito"]["capital"] = $this->venta_view->get("total");
			$data["credito_view"]["cliente"] = $this->venta_view->get("nombres");
			$data["redirect"] = "venta"; // redireccionar a venta
		}
		
		$data["has_amortizacion"] = false;
		
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model(array("credito.credito", "credito.credito_view", "venta.venta_view"));
		$this->credito_view->set_column_pk("idcredito");
		$this->venta_view->set_column_pk("idventa");
		
		$data["credito"] = $this->credito->find($id);
		$data["credito_view"] = $this->credito_view->find($id);
		$data["venta"] = $this->venta_view->find($this->credito->get("idventa"));
		
		$sql = "SELECT idletra, to_char(fecha_vencimiento,'DD/MM/YYYY') as fecha,
			monto_capital as amortizacion, interes, gastos, monto_letra as total
			FROM credito.letra 
			WHERE estado=? AND idcredito=?
			ORDER BY idletra";
		$query = $this->db->query($sql, array("A", $id));
		$data["letras"] = $query->result_array();
		
		$data["has_amortizacion"] = $this->credito->has_amortizacion($id);
		
		$this->set_title("Modificar Credito");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	public function get_all($id, $update_mora=0) {
		$this->load_model(array("credito.credito", "credito.credito_view", "venta.cliente"));
		$this->credito_view->set_column_pk("idcredito");
		
		$res["credito"] = $this->credito->find($id);
		// $this->verificar_credito_estado();//Aqui se cambia el estado de credito, atrasado, por atrasar
		$res["credito_view"] = $this->credito_view->find($this->credito->get("idcredito"));
		// if($res["credito"]["idgarante"] > 0)
			// $res["garante"] = $this->cliente->find($res["credito"]["idgarante"]);
		
		if( ! empty($update_mora)) {
			$this->update_mora($id);
		}
		$pagado = 'N';
		if(isset($_REQUEST['pagado']))
			if(($_REQUEST['pagado'])=='S')
				$pagado = 'S';
		
		$sql = "SELECT l.idletra
				, l.nro_letra
				, l.fecha_vencimiento
				, l.tipo_letra
				, l.monto_letra
				, l.descuento
				, l.mora
				, coalesce(sum(a.monto),0) as monto_pagado
				, max(a.fecha_pago) as fecha_pago
				, date_larger((l.fecha_vencimiento + 10)
				, coalesce(max(a.fecha_pago), '1900-01-01'::date)) as last_fecha_venc
				, COALESCE(MAX(serie)||'-')||MAX(numero) recibo
				, SUM(a.mora) moras_amortizado
				, SUM(gastos) gastos
				,COALESCE(letra_pagado,0) letras_canceladas
				,COALESCE(letra_pendiente,0) letras_pendientes
				,l.idcredito
				,MAX(serie) serie
				,MAX(numero) numero
				FROM credito.letra l
				LEFT JOIN credito.amortizacion a on a.idcredito=l.idcredito AND a.idletra=l.idletra AND a.estado='A'
				-- join añadido para ver las letras pendientes y canceladas
				LEFT JOIN(
				 SELECT count(*) letra_pagado,idcredito FROM credito.letra WHERE letra.estado='A' AND pagado='S' GROUP BY idcredito
				) lc ON lc.idcredito=l.idcredito
				LEFT JOIN(
				 SELECT count(*) letra_pendiente,idcredito FROM credito.letra WHERE letra.estado='A' AND pagado='N' GROUP BY idcredito
				) lp ON lp.idcredito=l.idcredito

				WHERE l.estado='A' AND l.pagado='$pagado' AND l.idcredito = ?
				GROUP BY l.idletra, l.nro_letra, l.fecha_vencimiento, l.tipo_letra, l.monto_letra, l.descuento, l.mora
				,letra_pagado,letras_pendientes
				,l.idcredito
				ORDER BY idletra";
		// echo $sql;exit;
		$query = $this->db->query($sql, array($id));
		$res["arr_letras_pendientes"] = $query->result_array();
		
		$letr  = $query->result_array();
		if(!empty($letr)){
			$res["credito"]["letras_pendientes"] = $letr[0]["letras_pendientes"];
			$res["credito"]["letras_canceladas"] = $letr[0]["letras_canceladas"];
		}else{
			$res["credito"]["letras_pendientes"] = count($res["arr_letras_pendientes"]);
		$res["credito"]["letras_canceladas"] = $res["credito"]["nro_letras"] - $res["credito"]["letras_pendientes"];
		}
		
		
		$sql = "select current_date - fecha_vencimiento as atrazo from credito.letra
			where estado='A' and pagado='$pagado' and idcredito=? order by idletra limit 1";
		
		$query = $this->db->query($sql, array($id));
		if($query->num_rows() && $pagado=='N')
			$res["dias_atrazo"] = $query->row()->atrazo;
		else
			$res["dias_atrazo"] = 0;
		
		// si se debe considerar el ciclo para el calculo de descuento
		// $this->load_model("credito.ciclo");
		// $res["ciclo"] = $this->ciclo->find($this->credito->get("id_ciclo"));
		
		$this->response($res);
	}
	
	public function update_mora($idcredito=0,$force=false) {
		$and_where = "";
		if(!empty($idcredito)) {
			$and_where .= " AND l.idcredito = ".intval($idcredito);
		}
		if($force == false) {
			$and_where .= " AND l.fecha_actualizacion <> current_date";
		}
		
		$sql = "UPDATE credito.letra 
			SET fecha_actualizacion = current_date,
			mora = CASE WHEN (current_date - sq.last_fecha_vencimiento) > 0 
				THEN round((sq.monto_letra - sq.descuento - sq.amortizado)*sq.valor_mora/100*(current_date - sq.last_fecha_vencimiento)/sq.dias_mes)
				ELSE 0 END
			FROM (
				SELECT l.idletra, l.idcredito, l.monto_letra, l.descuento, coalesce(sum(a.monto),0) as amortizado, 
				coalesce(cast(p1.valor AS numeric),0) as valor_mora, coalesce(cast(p2.valor AS numeric),30) as dias_mes,
				date_larger((l.fecha_vencimiento+c.dias_gracia), coalesce(max(a.fecha_pago), '1900-01-01'::date)) as last_fecha_vencimiento,
				c.dias_gracia
				FROM credito.letra l
				JOIN credito.credito c ON c.idcredito = l.idcredito
				LEFT JOIN credito.amortizacion a ON a.idcredito = l.idcredito AND a.idletra = l.idletra
				LEFT JOIN seguridad.param p1 ON p1.idparam = 'mora'
				LEFT JOIN seguridad.param p2 ON p2.idparam = 'dias_mes'
				WHERE c.estado='A' AND c.genera_mora='S' AND c.pagado='N' 
				AND l.estado='A' AND l.pagado='N' $and_where
				GROUP BY l.idletra, l.idcredito, l.fecha_vencimiento, l.monto_letra, l.descuento, p1.valor, p2.valor, c.dias_gracia
				ORDER BY idletra
			) AS sq
			WHERE sq.idletra = letra.idletra AND sq.idcredito = letra.idcredito";
		//echo $sql;exit;
		$this->db->query($sql);
		return true;
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model(array("credito.credito", "credito.letra", "credito.requerimiento_credito","venta.cliente","venta.venta"));
		
		// datos form
		$post = $this->input->post();
		
		// datos del credito
		$datoscredito['idcredito'] = $post["idcredito"];
		$datoscredito['idsucursal'] = $this->get_var_session("idsucursal");
		$datoscredito['idventa'] = $post["idventa"];
		$datoscredito['idcliente'] = $post["idcliente"];
		$datoscredito['id_estado_credito'] = $post["id_estado_credito"];
		$datoscredito['id_ciclo'] = $post["id_ciclo"];
		// $datoscredito['id_tipo_tasa'] = $post["id_tipo_tasa"]; // esto ya no hay
		$datoscredito['id_tipo_credito'] = $post["id_tipo_credito"];
		$datoscredito['nro_credito'] = $post["nro_credito"];
		$datoscredito['fecha_credito'] = date("Y-m-d H:i:s");
		$datoscredito['nro_letras'] = $post["nro_letras"];
		$datoscredito['monto_facturado'] = $post["monto_facturado"];
		$datoscredito['interes'] = $post["total_interes"];
		$datoscredito['monto_credito'] = $post["total_total"];
		$datoscredito['pagado'] = "N";
		$datoscredito['central_riesgo'] = "N";
		$datoscredito['estado'] = "A";
		$datoscredito['dias_gracia'] = $post["dias_gracia"];
		$datoscredito['idgarante'] = (empty($post["idgarante"])) ? 0 : $post["idgarante"];
		$datoscredito['genera_mora'] = (empty($post['genera_mora'])) ? "N" : "S";
		$datoscredito['tasa'] = $post['tasa'];
		$datoscredito['inicial'] = (!empty($post['inicial'])) ? $post['inicial'] : 0;
		$datoscredito['gastos'] = $post['total_gastos'];
		$datoscredito['capital'] = $post['capital'];
		$datoscredito['idmoneda'] = $post['idmoneda'];
		
		$crearLetras = true;
		
		$this->db->trans_start(); // inciamos transaccion
		
		$datosventa = $this->venta->find($post["idventa"]);
		if(empty($datoscredito["idcredito"])) {
			$this->load_model(array("seguridad.sucursal", "credito.nrocredito_empresa"));
			
			// datos venta
			if($datosventa["con_credito"] == "S") {
				$this->exception("Ya se ha generado un credito para la ".$post["venta_tipo_documento"]." ".$post["venta_numero_documento"]);
				return;
			}
			
			// datos config
			$sucursal = $this->sucursal->find($this->get_var_session("idsucursal"));
			$config = $this->nrocredito_empresa->find($sucursal["idempresa"]);
			if($config == null) {
				$this->exception("No existe el registro para generar el numero de credito.");
				return;
			}
			
			$nro_credito = str_pad($config["idempresa"], 2, "0", STR_PAD_LEFT);
			$nro_credito .= str_pad($datoscredito['idsucursal'], 2, "0", STR_PAD_LEFT);
			$nro_credito .= str_pad($config['numero'], 5, "0", STR_PAD_LEFT);
			// registramos el credito
			if($this->credito->is_active($config["idempresa"], $nro_credito)) {
				$this->exception("El credito ".$config['numero']." ya se ha registrado, indique otro n&uacute;umero de cr&eacute;dito.");
				return false;
			}
			$datoscredito['nro_credito'] = $nro_credito;
			$idcredito = $this->credito->insert($datoscredito);
			
			// actualizamos el correlativo del nro credito y estado de la venta
			$config["numero"] = $config["numero"] + 1;
			$this->nrocredito_empresa->update($config);
		}
		else {
			$this->credito->update($datoscredito);
			$idcredito = $datoscredito["idcredito"];
			
			// eliminamos los requerimientos ingresados previamente
			$this->requerimiento_credito->delete(array("idcredito"=>$idcredito));
			
			// existe alguna amortizacion
			if($this->credito->has_amortizacion($idcredito)) {
				$crearLetras = false;
			}
			else {
				$this->letra->delete(array("idcredito"=>$idcredito));
			}
		}
		
		// creamos las letras
		if($crearLetras) {
			if(!empty($post["letra"])) {
				$datosletra["idcredito"] = $idcredito;
				$datosletra["idusuario"] = $this->get_var_session("idusuario");
				$datosletra["tipo_letra"] = "L";
				$datosletra["descripcion"] = "LETRA";
				$datosletra["descuento"] = 0;
				$datosletra["mora"] = 0;
				$datosletra["estado"] = "A";
				$datosletra["pagado"] = "N";
				$datosletra["fecha_actualizacion"] = date("Y-m-d");
				
				foreach($post["letra"] as $k => $idletra) {
					$datosletra["idletra"] = $idletra;
					$datosletra["fecha_vencimiento"] = $post["fecha_vencimiento"][$k];
					$datosletra["nro_letra"] = $idletra;
					$datosletra["monto_capital"] = $post["amortizacion"][$k];
					$datosletra["monto_letra"] = $post["total"][$k];
					$datosletra["gastos"] = $post["gastos"][$k];
					$datosletra["interes"] = $post["interes"][$k];
					$this->letra->insert($datosletra, false);
				}
			}
			
			// ingresamos la inicial como letra
			/* if($datoscredito["inicia"] > 0) {
				$datosletra["idletra"] = 0;
				$datosletra["idcredito"] = $idcredito;
				$datosletra["idusuario"] = $this->get_var_session("idusuario");
				$datosletra["idtipo_pago"] = date("Y-m-d");
				$datosletra["fecha_vencimiento"] = date("Y-m-d");
			} */
		}
		
		if(!empty($post["idrequisito_credito"])) {
			foreach($post["idrequisito_credito"] as $idrequisito_credito) {
				$datosreq["idrequisito_credito"] = $idrequisito_credito;
				$datosreq["idcredito"] = $idcredito;
				$datosreq["confirmado"] = "N";
				$datosreq["estado"] = "N";
				$this->requerimiento_credito->insert($datosreq, false);
			}
		}
		
		if(!empty($idcredito)){
			// Eliminamos el credito de la venta, para evitar repeticion
			$this->destroy_hojaruta($idcredito);
			
			$this->cliente->find($this->credito->get("idcliente"));
			$this->venta->find($this->credito->get("idventa"));
			
			//Aqui guardamos la asignacion a la cartera de cobranzas
			$this->load_model("cobranzas.hoja_ruta");
			$data_c['idzona']		= trim($this->cliente->get("idzona"));
			if(empty($data_c['idzona']))
				$data_c['idzona']		= null;
			
			$data_c['idempleado']	= $this->venta->get("idusuario");
			$data_c['idsucursal']	= $this->get_var_session("idsucursal");
			$data_c['idcredito']	= $idcredito;
			$data_c['idventa']		= $post["idventa"];
			$data_c['idcobrador']	= $this->venta->get("idvendedor");
			$data_c['idgarante']	= $datoscredito['idgarante'];
			$data_c['idcliente']	= $this->credito->get("idcliente");
			// $data_c['orden']		= null;
			$data_c['estado']		= "A";
			$this->hoja_ruta->insert($data_c);
		}
		
		$datosventa["con_credito"] = "S";
		$this->venta->update($datosventa);
		$this->db->trans_complete(); // finalizamos transaccion
		
		$this->response($this->credito->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model("credito.credito");
		
		$fields['idcredito'] = $id;
		$fields['estado'] = "I";
		
		if($this->credito->has_amortizacion($id)) {
			$this->exception("Antes de eliminar el credito primero elimine las amortizaciones realizadas.");
			return;
		}
		
		$this->credito->update($fields);
		
		//eliminamos la hoja de ruta de cobranzas
		$this->destroy_hojaruta($id);
		$this->response($fields);
	}
	
	public function get_tasa($id_ciclo, $nro_letras) {
		$this->load_model("credito.ciclo");
		$ciclo = $this->ciclo->find($id_ciclo);
		
		$dias_mes = $this->get_param("dias_mes");
		
		$nro_meses = ($ciclo["dias"] / $dias_mes) * $nro_letras;
		$nro_meses = round($nro_meses);
		
		$query = $this->db->where("estado", "A")->where("mes", $nro_meses)->get("general.tasacredito");
		if($query->num_rows() > 0) {
			$r = $query->row_array();
			$tasa = $r["porcentaje"] / $nro_letras;
		}
		else {
			$tasa = 0;
		}
		
		$res["mes"] = $nro_meses;
		$res["tasa"] = $tasa;
		
		$this->response($res);
	}
	
	public function complete_req() {
		$this->load_model("credito.requerimiento_cliente");
		$this->requerimiento_cliente->text_uppercase(false);
		
		$post = $this->input->post();
		
		// buscamos algun registro con este requerimiento
		$this->db->where("idrequisito_credito", $post["idrequisito_credito"]);
		$this->db->where("idcliente", $post["idcliente"]);
		$query = $this->db->get("credito.requerimiento_cliente");
		if($query->num_rows() > 0) {
			$param = $query->row_array();
			$param["file_url"] = null;
			$param["estado"] = "A";
			$param["fecha"] = date("Y-m-d");
			$param["con_archivo"] = "N";
			$this->requerimiento_cliente->update($param);
		}
		else {
			$post["confirmado"] = "N";
			$post["estado"] = "A";
			$post["file_url"] = null;
			$post["fecha"] = date("Y-m-d");
			$post["con_archivo"] = "N";
			$this->requerimiento_cliente->insert($post, true);
		}
		
		$this->response($this->requerimiento_cliente->get_fields());
	}
	
	public function uncomplete_req() {
		$this->load_model("credito.requerimiento_cliente");
		
		$post = $this->input->post();
		
		// buscamos algun registro con este requerimiento
		$this->db->where("idrequisito_credito", $post["idrequisito_credito"]);
		$this->db->where("idcliente", $post["idcliente"]);
		$query = $this->db->get("credito.requerimiento_cliente");
		if($query->num_rows() > 0) {
			$param = $query->row_array();
			$param["estado"] = "I";
			$this->requerimiento_cliente->text_uppercase(false);
			$this->requerimiento_cliente->update($param);
		}
		
		$this->response($this->requerimiento_cliente->get_fields());
	}
	
	public function upload_req() {
		$this->load_model(array("credito.requisito_credito", "credito.requerimiento_cliente"));
		
		// datos del post
		$post = $this->input->post();
		
		// datos del requerimiento
		$requisito = $this->requisito_credito->find($post["idrequisito_credito"]);
		
		// requerimientos del cliente
		$sql = "SELECT COUNT(*) FROM credito.requerimiento_cliente 
			WHERE idrequisito_credito=? AND idcliente=? and estado=?";
		$query = $this->db->query($sql, array($post["idrequisito_credito"], $post["idcliente"], "A"));
		$row = $query->row_array();
		
		// si aun no se ha alcanzado la cantidad deseada
		if($requisito["cantidad"] > $row["count"]) {
			// subimos el archivo
			$this->load->library('file'); // importamos la libreria
			$this->file->set_input_file("file"); // atributo name del input[type=file]
			// $this->file->set_folder("requerimiento_cliente"); // carpeta para el archivo
			$this->file->set_folder($post["folder"]); // carpeta para el archivo
			$this->file->set_name($post["file_nombre"]); // nuevo nombre para el archivo
			// en caso de que el archivo sea una imagen
			$this->file->width = 400; // ancho de la imagen
			$this->file->ratio_y = true; // indicamos que el alto sea calculado
			// subimos el archivo
			if($this->file->upload()) {
				$param["idrequisito_credito"] = $post["idrequisito_credito"];
				$param["idcliente"] = $post["idcliente"];
				$param["confirmado"] = "N";
				$param["estado"] = "A";
				$param["file_url"] = $this->file->get_fullname();
				$param["fecha"] = date("Y-m-d");
				$param["con_archivo"] = "S";
				$this->requerimiento_cliente->text_uppercase(false);
				$this->requerimiento_cliente->insert($param, true);
			}
			else {
				$this->exception($this->file->get_error());
			}
		}
		else {
			$this->exception("El requerimiento ya se ha completado.");
		}
		
		$this->response($this->requerimiento_cliente->get_fields());
	}
	
	public function get_req($idcliente, $idrequisito_credito = 0) {
		$this->db->where("idcliente", $idcliente)->where("estado", "A");
		if(!empty($idrequisito_credito)) {
			$this->db->where("idrequisito_credito", $idrequisito_credito);
		}
		$query = $this->db->get("credito.requerimiento_cliente");
		$this->response($query->result_array());
	}
	
	public function del_req($idrequerimiento_cliente) {
		$this->load_model("credito.requerimiento_cliente");
		$data = $this->requerimiento_cliente->find($idrequerimiento_cliente);
		// eliminar archivo tambien?
		// if($data["con_archivo"] == "S") {
			
		// }
		$data["estado"] = "I";
		$this->requerimiento_cliente->text_uppercase(false);
		$this->requerimiento_cliente->update($data);
		$this->response($this->requerimiento_cliente->get_fields());
	}
	
	public function guardar_observacion() {
		$post = $this->input->post();
		if(!empty($post["idcredito"])) {
			$this->load_model("credito.credito");
			$this->credito->update($post);
		}
		$this->response($post);
	}
	
	
	
	public function get_detalle($id) {
		$this->load_model("credito");
		$res = $this->credito->get_items($id);
		$this->response($res);
	}
public function cabecera_detalle(){
		return array('item'=> array('CUOTA',14,0,'C')
					,'fecha' => array('FECHA DE PAGO',40,0,'C')
					,'capital' => array('CAPITAL',20,0,'C')
					,'interes' => array('INTERES',20,0,'C')
					,'monto' => array('PAGO MENSUAL',30,0,'C')
					,'saldo' => array('SALDO',20,1,'C')
					
					
				);
	}
	
	public function detalle_body($id=0){
		$q = $this->db->query("SELECT 
		A.FECHA_vencimiento fecha, 
		a.monto_letra capital, 
		a.interes interes,
		a.monto_letra monto, 
		b.monto_facturado-(nro_letra*a.monto_letra) saldo   
		FROM credito.LETRA a, credito.credito b 
		where a.idcredito = b.idcredito and  a.idcredito =$id" );
		return $q->result_array();
	}

	public function contrato($idcredito) {
		
		$this->load_model(array("credito.credito", "seguridad.sucursal", "seguridad.empresa","credito.credito_view","credito.credito_dias_view"));
		$this->credito_view->find($idcredito);
		$this->sucursal->find($this->credito_view->get("idsucursal"));
		$this->empresa->find($this->sucursal->get("idempresa"));		
		$importeTotal = $this->credito_view->get("monto_credito");		
		$this->load->library('numeroLetra');
		$this->load->library("pdf");
		
		
		
		$this->credito_dias_view->find($idcredito);
		$diascred = $this->credito_dias_view->get("dias");		
				
		/*inicio*/
		
		//$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","venta.preventa_view","venta.tipo_venta","seguridad.cliente_view","venta.detalle_preventa","seguridad.view_usuario"));
		$this->empresa->find($this->get_var_session("idempresa"));
		$this->preventa_view->find($idcredito);
		$this->cliente_view->find($this->preventa_view->get("idcliente"));
		$fechas = explode(" ",$this->preventa_view->get("fecha"));
		$fecha = '';
		$this->view_usuario->find($this->detalle_preventa->get("idusuario"));
		

		
		
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$parrafouno ='Conste por el presente documento, que se suscribe por duplicado, el contrato de venta al crédito de productos agricolas de una parte la COOPERATIVA AGRARIA ACEPAT, a quien en adelante se le denominará ';
		
		//$parrafouno = ' con RUC 20404175913, con domicilio legal en ----VILLAPALMA NRO. S/N CENTRO POBLADO VILLAPALMA (COSTADO DE OLPESA) SAN MARTIN - TOCACHE - TOCACHE, debidamente representado por su Gerente General Econ. GUSTAVO GINO SIGARROSTEGUI RUIZ, identificado con DNI N° 23018840 y de la otra parte el (la) Sr.(a) JULCA DELGADO SANTOS AGAPITO, identificado con DNI N° 80413720, con domicilio en , a quien adelante se denominará “EL SOCIO”, y bajo los términos y condiciones siguientes:';
		//$this->pdf->SetFont('Arial','',8);
		//$parrafouno .=' NEGRITA';
		
		
		// creamos la pagina
		$this->pdf->AddPage();
		
		$this->pdf->Image(FCPATH."app/img/empresa/".$this->empresa->get("logo"),10,10,30,0);
		$this->pdf->SetFont('Arial','B',20);
		$this->pdf->Cell(160, 16, utf8_decode('CONTRATO MUTUO N° '.$this->credito_view->get("nro_credito")),0,'L',0);
		//$this->pdf->SetTitle(utf8_decode("CONTRATO MUTUO N° ".$this->credito_view->get("nro_credito")), null, null, true);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Ln(30);
		//$this->pdf->MultiCell(190, 6, utf8_decode($parrafouno),0,'J',0);
		$this->pdf->Cell(173, 6, utf8_decode('Conste por el presente documento, que se suscribe por duplicado, el contrato de venta al crédito de productos agricolas de una parte la '),0,'J',0);
		$this->pdf->Ln(4);
		$this->pdf->Cell(98, 6, utf8_decode('COOPERATIVA AGRARIA ACEPAT, a quien en adelante se le denominará '),0,'J',0);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(28, 6, utf8_decode('"LA COOPERATIVA"'),0,'J',0);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell(60, 6, utf8_decode('con RUC 20404175913, con domicilio legal en '),0,'J',0);
		$this->pdf->Ln(4);
		$this->pdf->Cell(185, 6, utf8_decode('----VILLAPALMA NRO. S/N CENTRO POBLADO VILLAPALMA (COSTADO DE OLPESA) SAN MARTIN - TOCACHE - TOCACHE, debidamente '),0,'J',0);
		$this->pdf->Ln(4);
		$this->pdf->Cell(58, 6, utf8_decode('representado por su Gerente General Econ. '),0,'J',0);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(54, 6, utf8_decode('GUSTAVO GINO SIGARROSTEGUI RUIZ'),0,'J',0);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell(32, 6, utf8_decode(', identificado con DNI N° '),0,'J',0);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(13, 6, utf8_decode('23018840'),0,'J',0);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell(30, 6, utf8_decode(' y de la otra parte el (la)'),0,'J',0);
		$this->pdf->Ln(4);
		$this->pdf->MultiCell(200, 6, utf8_decode(' el (la) Sr.(a) '.utf8_decode($this->credito_view->get("cliente")).' identificado con ').utf8_decode($this->credito_view->get("documento")).' con domicilio en '.utf8_decode($this->credito_view->get("direccion")).',',0,'J',0);		
		//$this->pdf->Cell(60,6, utf8_decode($this->credito_view->get("cliente")),0,0,'L');
		$this->pdf->Cell(20,2, utf8_decode(' a quien adelante se denominará '),0,0,'L');
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(40, 2, utf8_decode('"EL SOCIO"'),0,'J',0);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell(180,2, utf8_decode(', y bajo los términos y condiciones siguientes:'),0,0,'L');
			$this->pdf->Ln(6);
			
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(16, 2, utf8_decode('PRIMERO:'),0,'L',0);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->MultiCell(170, 4, utf8_decode(' LA COOPERATIVA ha aprobado a favor de EL SOCIO un CREDITO DE VENTA DE PRODUCTOS AGRICOLAS, por la cantidad de ').$this->numeroletra->convertir(number_format($importeTotal, 2, '.', ''), true)." ".$this->credito_view->get("moneda").' ( S/ '.number_format($importeTotal, 2, '.', '').')'.utf8_decode(', a su entera conformidad y se obliga a devolver los términos convenidos en común acuerdo y que señala a continuación: '),0,'L',0);		
		//$this->pdf->SetFont('Arial','B',8);
			
	
				$this->pdf->Ln(4);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(17, 2, utf8_decode('SEGUNDO:'),0,'L',0);
			$this->pdf->SetFont('Arial','',8);
		$this->pdf->Cell(89, 2, utf8_decode('El préstamo otorgado generará un interés efectivo de 0.00 % según el cronograma de pagos.') ,0,0,'L');	
		
		$this->pdf->Ln(5);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(16, 2, utf8_decode('TERCERO:'),0,'L',0);
			$this->pdf->SetFont('Arial','',8);
		$this->pdf->MultiCell(170, 4, utf8_decode('EL SOCIO, se compromete en devolver el préstamo otorgado en el pazo máximo de '.utf8_decode($this->credito_dias_view->get("dias")*$this->credito_view->get("nro_letras")).' días, donde pagará los intereses y capital. Según lo establecido en el cronograma de pagos. De acuerdo a lo establecido en el Art. 1249 del Código Civil se pacta la capitalización mensual de los intereses compensatorios y moratorios vencidos a misma tasa pactado.') ,0,'J',0);		
	
		$this->pdf->Ln(4);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(16, 2, utf8_decode('CUARTO:'),0,'L',0);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->MultiCell(170, 4, utf8_decode('EL SOCIO, suscribe un pagaré en respaldo de fiel cumplimiento de la obligación, el cual podrá ser ejecutado en caso de incumplimiento de pago de una o más cuotas vencidas, pudiendo LA COOPERATIVA requerir el pago total, mas los intereses y demás gastos que demande la cobranza. Para el efecto EL SOCIO, suscribe el pagaré debidamente AVALADO.') ,0,'J',0);		
		
		$this->pdf->Ln(4);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(16, 2, utf8_decode('QUINTO:'),0,'L',0);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->MultiCell(170, 4, utf8_decode('Las partes convienen que los pagos se efectuarán como fecha máxima en los días señalados en el cronograma respectivo del pagaré, en las oficinas de la COOPERATIVA o cualquier centro de Acopio mas cercano de su sector.') ,0,'J',0);		
		
		$this->pdf->Ln(4);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(16, 2, utf8_decode('SEXTO:'),0,'L',0);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->MultiCell(170, 4, utf8_decode('De acuerdo a lo establecido por la ley de Cooperativas y luego de haber convenido las partes LA COOPERATIVA, esta facultada irrevocablemente para que de sus cuentas de aportes y de distribución de excedentes que tuviese el SOCIO y su GARANTE se le aplique el descuento del pago de la deuda, siempre y cuando el préstamo se encuentre en situación de morosidad. Es de responsabilidad de EL SOCIO y GARANTE todos los gastos derivados de la cobranza administrativa o judicial de la deuda.') ,0,'J',0);		
		
		
		$this->pdf->Ln(4);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(16, 2, utf8_decode('EL SOCIO, '),0,'L',0);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->MultiCell(170, 4, utf8_decode('se somete a la competencia de los jueces y tribunales de la ciudad de Tocache, renunciando al fuero de su domicilio. Cualquier cambio de domicilio deberá ser comunicado a LA COOPERATIVA, caso contrario se mantendrá vigente el señalado en el presente contrato.') ,0,'J',0);		
		
		
		
		$this->pdf->Ln(8);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(45, 2, utf8_decode('CLAUSULA ADICIONAL '),0,0,'L');
		$this->pdf->Ln(4);
		setlocale(LC_TIME, "spanish");
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->MultiCell(188, 4, utf8_decode('Intervienen en el presente contrato EL(la)Sr.(a) '.utf8_decode($this->credito_view->get("cliente")).', identificado(a) con '.utf8_decode($this->credito_view->get("documento")).' con domicilio en '.utf8_decode($this->credito_view->get("direccion")).'. Las partes suscriben el presente contrato en la ciudad de Villapalma, '.utf8_encode(strftime('%A, %d de %B del %Y'))) ,0,'J',0);		
		
		$this->pdf->Ln(38);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Line(11,221,60,221);
		$this->pdf->Cell(60, 3, utf8_decode('SOCIO '),0,0,'C');
		$this->pdf->Cell(60, 2, '',0,0,'C');
		$this->pdf->Line(130,221,189,221);
		$this->pdf->Cell(60, 2, utf8_decode('JEFE DE FINANCIAMIENTO '),0,0,'C');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Ln(4);
		$this->pdf->Cell(80, 2, utf8_decode($this->credito_view->get("cliente")),0,0,'L');
		$this->pdf->Cell(40, 2, '',0,0,'C');
		$this->pdf->Cell(80, 2, 'LIA JENY MENDOZA GAMBOA',0,0,'L');
		$this->pdf->Ln(4);
		$this->pdf->Cell(80, 2, utf8_decode($this->credito_view->get("documento")),0,0,'L');
		$this->pdf->Cell(40, 2, '',0,0,'C');
		$this->pdf->Cell(80, 2, 'D.N.I.: 74533416',0,0,'L');
		
		
		
	//	$this->pdf->Image(FCPATH."app/img/empresa/".$this->empresa->get("logo"),80,230,30,0);
		
		
		
		
		//PAGINA 2
		$this->pdf->AddPage();
		
		$this->pdf->Image(FCPATH."app/img/empresa/".$this->empresa->get("logo"),10,10,30,0);
		$this->pdf->SetFont('Arial','B',20);
		$this->pdf->Cell(140, 16, utf8_decode('PAGARE N° '.$this->credito_view->get("nro_credito")),0,'L',0);
		//$this->pdf->SetTitle(utf8_decode("CONTRATO MUTUO N° ".$this->credito_view->get("nro_credito")), null, null, true);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Ln(30);
		//$this->pdf->MultiCell(190, 6, utf8_decode($parrafouno),0,'J',0);
		$this->pdf->MultiCell(190, 4, utf8_decode('PRESTATARIO: '.utf8_decode($this->credito_view->get("cliente")).' identificado con '.utf8_decode($this->credito_view->get("documento")).' con domicilio en '.utf8_decode($this->credito_view->get("direccion")).', MONTO DEL VENTA OTORGADO: S/ '.number_format($importeTotal, 2, '.', '').' NUMERO DE CUOTAS A PAGAR: '.utf8_decode($this->credito_view->get("nro_letras")).' en '.utf8_decode($this->credito_view->get("nro_letras")*$this->credito_dias_view->get("dias")).' dias. INTERES: 0.0% CUOTA FIJA S/ '.utf8_decode($this->credito_view->get("monto_letra")).' TOTAL A PAGAR: S/ '.number_format($importeTotal, 2, '.', '').' CUOTA A PAGAR: '.utf8_decode($this->credito_view->get("monto_letra")).' cada una en '.utf8_decode($this->credito_dias_view->get("dias")).' dias, según cronograma de pagos. '),0,'J',0);		
		$this->pdf->Ln(4);
		$this->pdf->MultiCell(190,4, utf8_decode('SON: '.$this->numeroletra->convertir(number_format($importeTotal, 2, '.', ''), true)." ".$this->credito_view->get("moneda").' ( S/ '.number_format($importeTotal, 2, '.', '').') importe recibido a mi entera satisfacción y me obliga a pagar cuotas cada '.utf8_decode($this->credito_dias_view->get("dias")).' días, conforme al cronograma de pagos que se incluye en este pagaré. La falta de pago de las cuotas en las fechas indicadas, faculta a la COOPERATIVA AGRARIA ACEPAT a cobrar el 3% mensual por mora. ') ,0,'J',0);
				
		$this->pdf->Ln(8);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(45, 2, utf8_decode('OBLIGACION DEL AVAL '),0,0,'L');
		$this->pdf->Ln(4);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->MultiCell(188, 4, utf8_decode('EL AVAL se constituye como deudor solidario del prestatario y compromete su patrimonio para cancelar la deuda del préstamo, según lo establecido en el Pagaré.') ,0,'J',0);		
		
		
		$this->pdf->Ln(8);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(45, 2, utf8_decode('OBLIGACION DEL PRESTATARIO '),0,0,'L');
		$this->pdf->Ln(4);
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->MultiCell(188, 4, utf8_decode('El prestatario puede realizar pagos anticipados en cualquier momento. Asimismo, declaro conocer el Reglamento de créditos, incluyendo las condiciones de financiamiento, plazos, intereses y garantías.') ,0,'J',0);		
		$this->pdf->MultiCell(188, 4, utf8_decode('De acuerdo a lo establecido en el Art. 1249° del Código Civil se pacta la capitalización mensual de los intereses compensatorios y moratorios vencidos a la misma tasa pactada. ') ,0,'J',0);		
		
		
		$this->pdf->Ln(8);
		
		//inicio del Cuadro
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(188, 2, utf8_decode('CRONOGRAMA DE PAGO '),0,0,'C');
		$this->pdf->Ln(8);
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(25,6,'',0,0,"L");
		foreach ($this->cabecera_detalle() as $key => $val) {
				
			$this->pdf->Cell($val[1],6,$val[0],1,$val[2],$val[3]);
		}
		
		$this->pdf->SetFont('Arial','',8.5);
		$i = 1;
		foreach($this->detalle_body($idcredito) as $k=>$v){
			$v["item"] = $i;
			$this->pdf->Cell(25,6,'',0,0,"L");
			foreach ($this->cabecera_detalle() as $key => $val) {
				
				$this->pdf->Cell($val[1],4,utf8_decode($v[$key]),1,$val[2],$val[3]);
			}
			$i++;
		}
		
		$this->pdf->SetFont('Arial','B',10);
		$this->pdf->Cell(79,4,'TOTAL:',0,'J',0);
		$this->pdf->Cell(20,4, utf8_decode($importeTotal) ,0,0,'C');
		$this->pdf->Cell(20,4, '0.00' ,0,'C',0);
		$this->pdf->Cell(30,4, utf8_decode($importeTotal) ,0,0,'C');
		
		$this->pdf->Ln(8);
		
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->MultiCell(188 ,6,utf8_decode('En los términos y condiciones antes expresas declaro haber leído en su integridad, encontrando conforme y aceptado en todos sus extremos, interviene este pagaré, el prestamista y el aval asumiendo las responsabilidades y obligaciones de estos conforme al Art. 292° y 315° del código civil.'),0,'J');
			$this->pdf->Ln(2);
		$this->pdf->MultiCell(188, 6,'Villapalma, '.utf8_encode(strftime('%A, %d de %B del %Y')) ,0,'J',0);		
		
		//Fin del Cuadro
		
		$this->pdf->Ln(30);
				
	
		
		
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Line(11,202+(($i-2)*4),60,202+($i-2)*4);
		$this->pdf->Cell(60, 2, utf8_decode('SOCIO '),0,0,'C');
		$this->pdf->Cell(60, 2, '',0,0,'C');
		$this->pdf->Line(130,202+(($i-2)*4),189,202+(($i-2)*4));
		$this->pdf->Cell(60, 2, utf8_decode('JEFE DE FINANCIAMIENTO '),0,0,'C');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Ln(4);
		$this->pdf->Cell(80, 2, utf8_decode($this->credito_view->get("cliente")),0,0,'L');
		$this->pdf->Cell(40, 2, '',0,0,'C');
		$this->pdf->Cell(80, 2, 'LIA JENY MENDOZA GAMBOA',0,0,'L');
		$this->pdf->Ln(4);
		$this->pdf->Cell(80, 2, utf8_decode($this->credito_view->get("documento")),0,0,'L');
		$this->pdf->Cell(40, 2, '',0,0,'C');
		$this->pdf->Cell(80, 2, 'D.N.I.: 74533416',0,0,'L');
		
		//$this->pdf->Image(FCPATH."app/img/empresa/".$this->empresa->get("logo"),80,190,30,0);

		//80413720, con domicilio en , a quien adelante se denominará “EL SOCIO”, y bajo los términos y condiciones siguientes:',0,0,'L');
		//$this->pdf->Cell(11, 6, "Texto 2",0,'J',0);
		//$this->tcpdf->MultiCell(202 ,6,("Representación impresa de la boleta de venta electrónica generada desde el sistema facturador SUNAT. Puede verificarla utilizando su clave SOL"),0,'J');
		//$this->pdf->MultiCell(190, 6, utf8_decode($parrafouno).$this->pdf->SetFont('Arial','',8)."RUC",0,'J',0);
		//$this->pdf->MultiCell(190, 6, utf8_decode($parrafouno).$this->empresa->get("ruc"),0,'J',0);
		//$this->pdf->SetFont('Arial','B',8);
		//$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		
		
		//pagina 3
		
		
		$this->pdf->AddPage();
		
		$this->pdf->Image(FCPATH."app/img/empresa/".$this->empresa->get("logo"),10,10,30,0);
		$this->pdf->SetFont('Arial','B',15);
		$this->pdf->Cell(150, 6, utf8_decode('COOPERATIVA AGRARIA ACEPAT'),0,0,'R');
		$this->pdf->Ln(8);
		$this->pdf->SetFont('Arial','B',10);
		$this->pdf->Cell(120, 6, utf8_decode('R.U.C. 20404175913'),0,0,'R');
		$this->pdf->Ln(8);
		$this->pdf->Cell(50, 6,'',0,0,'R');
		$this->pdf->MultiCell(120, 6, utf8_decode('----VILLAPALMA NRO. S/N CENTRO POBLADO VILLAPALMA (COSTADO DE OLPESA) SAN MARTIN - TOCACHE - TOCACHE'),0,'C',0);
		$this->pdf->Ln(8);
		$this->pdf->Cell(180, 6, utf8_decode('CRONOGRAMA DE PAGO CRÉDITOS - AGRICOLA'),0,0,'C');
		$this->pdf->Ln(8);
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(35, 4, utf8_decode('APELLIDOS Y NOMBRES :'),0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(90, 4, utf8_decode($this->credito_view->get("cliente")),0,0,'L');
			$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(30, 4, utf8_decode('FECHA DE PRESTAMO :'),0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(45, 4, utf8_decode($this->credito_view->get("fecha_credito")),0,0,'L');
		$this->pdf->Ln(6);
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(15, 4, utf8_decode('DNI o RUC :'),0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(30, 4, utf8_decode($this->credito_view->get("documento")),0,0,'L');
			$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(15, 4, utf8_decode('Campaña :'),0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(45, 4, '',0,0,'L');
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(15, 4, utf8_decode('Cod. Vent :'),0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(25, 4, utf8_decode($this->credito_view->get("nro_credito")),0,0,'L');
			$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(15, 4, utf8_decode('DEPOSITO :'),0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(25, 4, '',0,0,'L');
		$this->pdf->Ln(6);
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(25, 4, utf8_decode('COMITE SECTORIAL :'),0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(30, 4,'',0,0,'L');

$this->pdf->Ln(6);
$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(30, 4, utf8_decode('Periodo de Pago (DIAS): '),0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(30, 4, utf8_decode($this->credito_dias_view->get("dias")),0,0,'L');
			$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(20, 4, utf8_decode('Tasa de Interes :'),0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(45, 4, '0.00%',0,0,'L');
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(35, 4, utf8_decode('Monto de Préstamo :'),0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(25, 4, number_format($importeTotal, 2, '.', ''),0,0,'L');
		
		
		
		$this->pdf->Ln(6);
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(30, 4, '',0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(30, 4, '',0,0,'L');
			$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(20, 4, '',0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(45, 4, '',0,0,'L');
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(35, 4, utf8_decode('Interés de Préstamo :'),0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(25, 4, '0.00',0,0,'L');
		
		
		
		
		
		$this->pdf->Ln(6);
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(30, 4,'',0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(30, 4, '',0,0,'L');
			$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(20, 4, '',0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(45, 4, '',0,0,'L');
		$this->pdf->SetFont('Arial','B',7);
		$this->pdf->Cell(35, 4, utf8_decode('Amortización o Inicial :'),0,0,'L');
		$this->pdf->SetFont('Arial','',7);
		$this->pdf->Cell(25, 4, '0.00',0,0,'L');


		$this->pdf->Ln(16);
//inicio del Cuadro
		
		
		$this->pdf->Ln(8);
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(25,6,'',0,0,"L");
		foreach ($this->cabecera_detalle() as $key => $val) {
				
			$this->pdf->Cell($val[1],6,$val[0],1,$val[2],$val[3]);
		}
		
		$this->pdf->SetFont('Arial','',8.5);
		$i = 1;
		foreach($this->detalle_body($idcredito) as $k=>$v){
			$v["item"] = $i;
			$this->pdf->Cell(25,6,'',0,0,"L");
			foreach ($this->cabecera_detalle() as $key => $val) {
				
				$this->pdf->Cell($val[1],4,utf8_decode($v[$key]),1,$val[2],$val[3]);
			}
			$i++;
		}
		$this->pdf->SetFont('Arial','B',10);
		$this->pdf->Cell(79,4,'TOTAL:',0,'J',0);
		$this->pdf->Cell(20,4, utf8_decode($importeTotal) ,0,0,'C');
		$this->pdf->Cell(20,4, '0.00' ,0,'C',0);
		$this->pdf->Cell(30,4, utf8_decode($importeTotal) ,0,0,'C');
		
		
		//Fin del Cuadro


		$this->pdf->Ln(38);
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Line(11,153+(($i-2)*4),60,153+($i-2)*4);
		$this->pdf->Cell(60, 2, utf8_decode('SOCIO '),0,0,'C');
		$this->pdf->Cell(60, 2, '',0,0,'C');
		$this->pdf->Line(130,153+(($i-2)*4),189,153+(($i-2)*4));
		$this->pdf->Cell(60, 2, utf8_decode('CREDI-ACEPAT '),0,0,'C');
		$this->pdf->SetFont('Arial','',8);
		$this->pdf->Ln(4);
		$this->pdf->Cell(80, 2, utf8_decode($this->credito_view->get("cliente")),0,0,'L');
		$this->pdf->Cell(40, 2, '',0,0,'C');
		$this->pdf->Cell(80, 2, '',0,0,'L');
		$this->pdf->Ln(4);
		$this->pdf->Cell(80, 2, utf8_decode($this->credito_view->get("documento")),0,0,'L');
		$this->pdf->Cell(40, 2, '',0,0,'C');
		$this->pdf->Cell(80, 2, '',0,0,'L');
		
		//$this->pdf->Image(FCPATH."app/img/empresa/".$this->empresa->get("logo"),80,230,30,0);
		
		
			
		
		
		
		
		

		$this->pdf->Output();
		
		
		
		
	}
}
?>