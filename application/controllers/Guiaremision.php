<?php

include_once "Controller.php";

class Guiaremision extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		// $this->set_title("Modulo Nota de Credito");
		// $this->set_subtitle("Lista de perfil");
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		// $this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		
		if( ! isset($data["anulado"]))
			$data["anulado"] = false;
		
		$igv = $this->get_param("igv");
		if(!is_numeric($igv)) {
			$igv = 18;
		}
		$data["valor_igv"] = $igv;
		$data["idtipodocumento"] = $this->get_param("idguia_remision");
		
		$this->load->library("combobox");
		
		// combo motivo
		$sql = "SELECT * FROM almacen.motivo_guia
			WHERE estado=? and mostrar_en_guia=? and operacion in (?,?)
			ORDER BY idmotivo_guia";
		$query = $this->db->query($sql, array('A', 'S', $data["guia_remision"]["tipo_guia"], 'A'));
		$this->combobox->setAttr(array("id"=>"idmotivo_guia", "name"=>"idmotivo_guia", "class"=>"form-control"));
		$this->combobox->addItem($query->result_array(), '', 
			array("idmotivo_guia", 'descripcion', 'ingreso_buscar_guia', 'ingreso_b_esta_sede', 'ingreso_b_otra_sede', 
				'ingreso_libre_item', 'salida_buscar_venta', 'salida_buscar_compra', 'salida_libre_item', 'afecta_stock'));
		if(isset($data["guia_remision"]["idmotivo_guia"])) {
			$this->combobox->setSelectedOption($data["guia_remision"]["idmotivo_guia"]);
		}
		$data["motivo"] = $this->combobox->getObject(true);
		
		// combo serie
		$this->combobox->setAttr(array("id"=>"serie", "name"=>"serie", "class"=>"form-control"));
		$this->combobox->setStyle("width", "100px");
		$sql = "SELECT serie 
			FROM venta.serie_documento
			WHERE idtipodocumento = ? AND idsucursal = ?
			ORDER BY serie";
		$query = $this->db->query($sql, array($data["idtipodocumento"], $this->get_var_session("idsucursal")));
		$this->combobox->addItem($query->result_array());
		if(isset($data["guia_remision"]["serie"])) {
			$this->combobox->setSelectedOption($data["guia_remision"]["serie"]);
		}
		$data["serie"] = $this->combobox->getObject(true);
		
		// combo almacen
		$query = $this->db->select('idalmacen, descripcion')->where("estado", "A")
			->where("idsucursal", $this->get_var_session("idsucursal"))->get("almacen.almacen");
		$this->combobox->setAttr(array("id"=>"idalmacen","name"=>"idalmacen","class"=>"form-control"));
		$this->combobox->removeAllStyle();
		$this->combobox->addItem($query->result_array());
		$data["almacen"] = $this->combobox->getObject(true);
		
		$this->load_controller("transporte");
		$data["form_transporte"] = $this->transporte_controller->form(null, "trans_", true);
		
		$this->load_controller("chofer");
		$data["form_chofer"] = $this->chofer_controller->form(null, "chof_", true);
		
		$data["controller"] = $this->controller;
		$data["ubigeo"] = $this->get_form_ubigeo();
		
		// para el datepicker
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		
		// checkbox
		$this->css('plugins/iCheck/custom');
		$this->js('plugins/iCheck/icheck.min');
		
		$this->js('form/'.$this->controller.'/form');
		$this->js('form/transporte/modal');
		$this->js('form/chofer/modal');
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	public function filtros_grilla($tipo_guia = "S") {
		$this->load_library("combobox");
		
		$this->combobox->setAttr("class", "form-control");
		
		$html = '<div class="row">';
		
		// div y combobox tipo_guia
		$this->combobox->setAttr("filter", "tipo_guia");
		$this->combobox->addItem("S", "SALIDA");
		$this->combobox->addItem("I", "ENTRADA");
		$this->combobox->setSelectedOption($tipo_guia);
		$html .= '<div class="col-sm-3"><div class="form-group">';
		$html .= '<label class="control-label">Tipo</label>';
		$html .= $this->combobox->getObject();
		$html .= '</div></div>';
		
		$html .= '</div>';
		
		$this->set_filter($html);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model('almacen.guia_remision_view');
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->guia_remision_view);
		$this->datatables->setIndexColumn("idguia_remision");
		
		$tipo_guia = "S";
		
		$this->datatables->where('estado', '<>', 'X');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		$this->datatables->where('tipo_guia', '=', $tipo_guia);
		
		$cols = array(
			"fecha_traslado"=>"F.Traslado"
			,"nroguia"=>"Nro.Guia"
			,"destinatario"=>"Destinatario"
			,"punto_partida"=>"Partida"
			,"punto_llegada"=>"Llegada"
			,"motivo"=>"Motivo"
		);
		
		$this->datatables->setColumns(array_keys($cols));
		$this->datatables->setCallback("checkAnulacion");
		$this->datatables->order_by('fecha_traslado', 'desc');
		//$this->datatables->order_by('nroguia', 'asc');
		
		$table = $this->datatables->createTable(array_values($cols));
		
		$script = "<script>".$this->datatables->createScript()."</script>";
		
		$this->js($script, false);
		$this->filtros_grilla($tipo_guia);
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function ingreso() {
		$data["readonly"] = false;
		$data["nuevo"] = true;
		$data["guia_remision"]["tipo_guia"] = "I";
		
		$this->load_model("seguridad.sucursal");
		$this->sucursal->find($this->get_var_session("idsucursal"));
		$data["guia_remision"]["punto_llegada"] = $this->sucursal->get("direccion");
		
		$this->set_title("Registrar Guia de Remision - Ingreso");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	public function salida() {
		$data["readonly"] = false;
		$data["nuevo"] = true;
		$data["guia_remision"]["tipo_guia"] = "S";
		
		
		$this->load_model("seguridad.sucursal");
		$this->sucursal->find($this->get_var_session("idsucursal"));
		$data["guia_remision"]["punto_partida"] = $this->sucursal->get("direccion");
		
		$this->set_title("Registrar Guia de Remision - Salida");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model(array("almacen.guia_remision", "general.transporte", "ubigeo"));
		
		$data["guia_remision"] = $this->guia_remision->find($id);
		$data["guia_remision"]["fecha_traslado"] = fecha_es($data["guia_remision"]["fecha_traslado"]);
		
		$es_anulado = ($this->guia_remision->get("estado") != "A");

		if( ! empty($data["guia_remision"]["idtransporte"]))
			$data["transporte"] = $this->transporte->find($data["guia_remision"]["idtransporte"]);
		if( ! empty($data["guia_remision"]["idubigeo_partida"]))
			$data["partida"] = $this->ubigeo->get_data($data["guia_remision"]["idubigeo_partida"]);
		if( ! empty($data["guia_remision"]["idubigeo_llegada"]))
			$data["llegada"] = $this->ubigeo->get_data($data["guia_remision"]["idubigeo_llegada"]);
		
		$this->load_model("detalle_guia_remision");
		$data["detalle"] = $this->detalle_guia_remision->get_items($id, $es_anulado);
		
		$data["readonly"] = ! ($this->guia_remision->get("fecha_registro") == date("Y-m-d"));
		$data["nuevo"] = false;
		
		$data["anulado"] = $es_anulado;
		
		$title = "Modificar Guia de Remision - ";
		if($data["guia_remision"]["tipo_guia"] == "S")
			$title .= "Salida";
		else
			$title .= "Entrada";
		$this->set_title($title);
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	public function guardar() {
		if($this->input->post("tipo_guia") == "S") {
			$this->guardar_salida();
		}
		else if($this->input->post("tipo_guia") == "I") {
			$this->guardar_ingreso();
		}
	}
	
	public function guardar_ingreso() {
		$this->load_model("almacen.guia_remision");
		
		$post = $this->input->post();
		$this->_default_post($post);
		
		$this->db->trans_start();
		
		if(empty($post["idguia_remision"])) {
			$post["fecha_registro"] = date("Y-m-d");
			$post["finalizado"] = "N";
			
			$idguia_remision = $this->guia_remision->insert($post);
		}
		else {
			$this->guia_remision->update($post);
			
			$idguia_remision = $post["idguia_remision"];
			
			// eliminamos el detalle de la guia de remision
			$this->db->where("idguia_remision", $idguia_remision)
				->update("almacen.detalle_guia_remision", array("estado"=>"I"));
			
			// eliminamos las series del detalle de la guia de remision
			$this->db->where("idguia_remision", $idguia_remision)
				->update("almacen.detalle_guia_remision_serie", array("estado"=>"I"));
			
			// eliminamos el ingreso en detalle_almacen
			$this->db->where("tabla", "GR")->where("idtabla", $idguia_remision)
				->update("almacen.detalle_almacen", array("estado"=>"I"));
			
			// eliminamos el ingreso de las series en almacen
			$this->db->where("tabla_ingreso", "GR")->where("idtabla_ingreso", $idguia_remision)
				->update("almacen.detalle_almacen_serie", array("estado"=>"I"));
			
			// eliminamos la recepcion
			$this->db->where("idcompra", $idguia_remision)->where("referencia", "GR")
				->update("almacen.recepcion", array("estado"=>"I"));
			
			// eliminamos el ingreso de kardex
			$this->load_library("jkardex");
			$this->jkardex->remove("guia_remision", $idguia_remision, $post["idsucursal"]);
		}
		
		// ingresamos el detalle de la nota de credito
		if( ! empty($post["deta_idproducto"])) {
			$this->load_model(array("almacen.detalle_guia_remision", "almacen.detalle_guia_remision_serie", 
				"almacen.motivo_guia", "producto", "producto_unidad"));
			
			$this->detalle_guia_remision->set("idguia_remision", $idguia_remision);
			$this->detalle_guia_remision->set("precio", 0);
			$this->detalle_guia_remision->set("finalizado", "N");
			$this->detalle_guia_remision->set("estado", "A");
			
			$this->motivo_guia->find($post["idmotivo_guia"]);
			$afecta_stock = ($this->motivo_guia->get("afecta_stock") == "S");
			
			if($afecta_stock) {
				// modelos para el almacen
				$this->load_model(array("detalle_almacen", "detalle_almacen_serie", 
					"recepcion", "tipo_movi_almacen"));
				
				$this->detalle_almacen->set("tipo", "E");
				$this->detalle_almacen->set("tipo_number", 1);
				$this->detalle_almacen->set("fecha", date("Y-m-d"));
				$this->detalle_almacen->set("tabla", "GR");
				$this->detalle_almacen->set("idtabla", $idguia_remision);
				$this->detalle_almacen->set("estado", "A");
				$this->detalle_almacen->set("idsucursal", $this->guia_remision->get("idsucursal"));
				
				$this->detalle_almacen_serie->set("fecha_ingreso", date("Y-m-d"));
				$this->detalle_almacen_serie->set("tabla_ingreso", "GR");
				$this->detalle_almacen_serie->set("idtabla_ingreso", $idguia_remision);
				$this->detalle_almacen_serie->set("despachado", "N");
				$this->detalle_almacen_serie->set("estado", "A");
				$this->detalle_almacen_serie->set("idsucursal", $this->guia_remision->get("idsucursal"));
				
				$this->recepcion->set("idcompra", $idguia_remision);
				$this->recepcion->set("tipo_docu", $this->guia_remision->get("idtipodocumento"));
				$this->recepcion->set("serie", $this->guia_remision->get("serie"));
				$this->recepcion->set("numero", $this->guia_remision->get("numero"));
				$this->recepcion->set("observacion", $this->motivo_guia->get("descripcion"));
				$this->recepcion->set("fecha", date("Y-m-d"));
				$this->recepcion->set("hora", date("H:i:s"));
				$this->recepcion->set("idusuario", $this->guia_remision->get("idusuario"));
				$this->recepcion->set("referencia", "GR");
				
				// $this->tipo_movi_almacen->find($this->get_idtipo_movimiento("guiaremision"));
				$this->tipo_movi_almacen->find($this->motivo_guia->get("ingreso_tipo_movimiento"));
				$correlativo = intval($this->tipo_movi_almacen->get("correlativo"));
			}
			
			$arrProductosKardex = array(); // datos almacen kardex
			if(empty($post["idalmacen"])) {
				$query = $this->db->where("estado", "A")->where("idsucursal", $post["idsucursal"])->get("almacen.almacen");
				if($query->num_rows() > 0)
					$idalmacen = $query->row()->idalmacen;
			}
			if(empty($idalmacen)) {
				$idalmacen = intval($post["idalmacen"]);
			}
			
			foreach($post["deta_idproducto"] as $key=>$val) {
				if(empty($post["deta_idalmacen"][$key]))
					$post["deta_idalmacen"][$key] = $idalmacen;
				
				// obtenemos el precio de costo
				$this->producto_unidad->find(array("idproducto"=>$val, "idunidad"=>$post["deta_idunidad"][$key]));
				$cantidad_um = floatval($this->producto_unidad->get("cantidad_unidad_min"));
				
				$costo = $this->producto->get_precio_compra_unitario($val, $post["idsucursal"], $post["deta_idunidad"][$key]);
				$costo_um = $costo * $cantidad_um;
				
				$this->detalle_guia_remision->set("idproducto", $val);
				$this->detalle_guia_remision->set("descripcion", $post["deta_producto"][$key]);
				$this->detalle_guia_remision->set("idunidad", $post["deta_idunidad"][$key]);
				$this->detalle_guia_remision->set("cantidad", floatval($post["deta_cantidad"][$key]));
				// $this->detalle_guia_remision->set("precio", $post["deta_precio"][$key]);
				$this->detalle_guia_remision->set("costo", $costo_um);
				$this->detalle_guia_remision->set("peso", $post["deta_peso"][$key]);
				$this->detalle_guia_remision->set("idalmacen", $post["deta_idalmacen"][$key]);
				$this->detalle_guia_remision->set("afecta_stock", $post["deta_controla_stock"][$key]);
				$this->detalle_guia_remision->set("afecta_serie", $post["deta_controla_serie"][$key]);
				$this->detalle_guia_remision->set("cantidad_um", $cantidad_um);
				$this->detalle_guia_remision->insert();
				
				// insertamos las series del detalle compra
				if($post["deta_controla_serie"][$key] == "S") {
					if( ! empty($post["deta_series"][$key])) {
						$this->detalle_guia_remision_serie->set($this->detalle_guia_remision->get_fields());
						$arr = explode("|", $post["deta_series"][$key]);
						foreach($arr as $serie) {
							$this->detalle_guia_remision_serie->set("serie", $serie);
							$this->detalle_guia_remision_serie->insert(null, false);
						}
					}
				}
				
				// si recepcionamos la nota de credito directamente, ingresamos el stock y las series al almacen
				if($afecta_stock) {
					// ingresamos a recepcion
					$this->recepcion->set($this->detalle_guia_remision->get_fields());
					$this->recepcion->set("iddetalle_compra", $this->detalle_guia_remision->get("iddetalle_guia_remision"));
					$this->recepcion->set("cant_recepcionada", $this->detalle_guia_remision->get("cantidad"));
					$this->recepcion->set("correlativo", $correlativo);
					$this->recepcion->set("estado", "C");
					$this->recepcion->insert();
					$correlativo = $correlativo + 1; // nuevo correlativo
					
					if($post["deta_controla_stock"][$key] == "S") {
						// ingresamos el stock en el almacen
						$this->detalle_almacen->set($this->detalle_guia_remision->get_fields());
						$this->detalle_almacen->set("precio_costo", $costo_um);
						$this->detalle_almacen->set("idrecepcion", $this->recepcion->get("idrecepcion"));
						$this->detalle_almacen->insert();
						
						// verificamos para ingresar las series al almacen
						if($post["deta_controla_serie"][$key] == "S") {
							if(empty($post["deta_serie"][$key])) {
								$this->exception("Ingrese las series del producto ".$post["deta_producto"][$key]);
								return false;
							}
							
							$count_real_serie = $cantidad_um * intval($post["deta_cantidad"][$key]);
							
							$arr = explode("|", $post["deta_serie"][$key]);
							if(count($arr) != $count_real_serie) {
								$this->exception("Debe ingresar $count_real_serie series para el producto: ".$post["deta_producto"][$key]);
								return false;
							}
							
							// ingresamos las series
							$this->detalle_almacen_serie->set($this->detalle_almacen->get_fields());
							foreach($arr as $serie) {
								if( $this->detalle_almacen_serie->exists(array("serie"=>$serie, "despachado"=>"N", "estado"=>"A")) ) {
									$this->exception("La serie $serie del producto ".$post["deta_producto"][$key]." ya existe.");
									return false;
								}
								$this->detalle_almacen_serie->set("serie", $serie);
								$this->detalle_almacen_serie->insert(null, false);
							}
						}
						
						$temp = $this->recepcion->get_fields();
						$temp["cantidad"] = $temp["cant_recepcionada"];
						$temp["preciocosto"] = $costo;
						// $temp["precioventa"] = floatval($post["deta_precio"][$key]) / $cantidad_um;
						$temp["precioventa"] = 0;
						$arrProductosKardex[] = $temp;
					}
				}
			}
			
			if($afecta_stock && ! empty($arrProductosKardex)) {
				// actualizamos el correlativo del tipo movimiento
				$this->tipo_movi_almacen->set("correlativo", $correlativo);
				$this->tipo_movi_almacen->update();
				
				if( ! isset($this->jkardex)) {
					// importamos librari
					$this->load_library("jkardex");
				}
				
				// $this->jkardex->idtercero = $this->notacredito->get("idcliente");
				// $this->jkardex->idmoneda = $this->notacredito->get("idmoneda");
				// $this->jkardex->tipocambio = $this->notacredito->get("cambio_moneda");
				$this->jkardex->observacion = $this->motivo_guia->get("descripcion");
				
				$this->jkardex->referencia("guia_remision", $idguia_remision, $post["idsucursal"], $this->motivo_guia->get("ingreso_tipo_movimiento"));
				$this->jkardex->entrada();
				$this->jkardex->push($arrProductosKardex);
				$this->jkardex->run();
			}
		}
		
		$this->db->trans_complete();
		
		$this->response($this->guia_remision->get_fields());
	}
	
	private function _default_post(&$post) {
		$post["idtipodocumento"] = $this->get_param("idguia_remision");
		$post["idusuario"] = $this->get_var_session("idusuario");
		$post["idsucursal"] = $this->get_var_session("idsucursal");
		$post["estado"] = "A";
		if(empty($post["idtransporte"]))
			$post["idtransporte"] = 0;
		if(empty($post["idreferencia"]))
			$post["idreferencia"] = 0;
		if(empty($post["costo_minimo"]))
			$post["costo_minimo"] = 0;
	}
	
	public function guardar_salida() {
		$this->load_model("almacen.guia_remision");
		
		$post = $this->input->post();
		$this->_default_post($post);
		
		$this->db->trans_start();
		
		if(empty($post["idguia_remision"])) {
			// verificamos si existe el recibo generado
			if($this->has_comprobante("guia_remision", $post["idtipodocumento"], $post["serie"], $post["numero"])) {
				$this->exception("Ya se ha generado la Guia de Remision ".$post["serie"]."-".$post["numero"]);
				return false;
			}
			
			$post["fecha_registro"] = date("Y-m-d");
			$post["finalizado"] = "N";
			
			$idguia_remision = $this->guia_remision->insert($post);
			
			// actualizamos el correlativo del documento
			$this->update_correlativo($post["idtipodocumento"], $post["serie"]);
		}
		else {
			$this->guia_remision->update($post);
			
			$idguia_remision = $post["idguia_remision"];
			
			// eliminamos el detalle de la guia de remision
			$this->db->where("idguia_remision", $idguia_remision)
				->update("almacen.detalle_guia_remision", array("estado"=>"I"));
				
			// eliminamos las series del detalle de la guia de remision
			$this->db->where("idguia_remision", $idguia_remision)
				->update("almacen.detalle_guia_remision_serie", array("estado"=>"I"));
			
			// eliminamos la salida del detalle_almacen
			$this->db->where("tabla", "GR")->where("idtabla", $idguia_remision)
				->update("almacen.detalle_almacen", array("estado"=>"I"));
			
			// eliminamos la salida de las series del almacen
			$this->db->where("tabla_salida", "GR")->where("idtabla_salida", $idguia_remision)->where("estado", "A")
				->update("almacen.detalle_almacen_serie", array("despachado"=>"N"));
			
			// eliminamos el despacho
			$this->db->where("idreferencia", $idguia_remision)->where("referencia", "GR")
				->update("almacen.despacho", array("estado"=>"I"));
			
			// eliminamos el ingreso de kardex
			$this->load_library("jkardex");
			$this->jkardex->remove("guia_remision", $idguia_remision, $post["idsucursal"]);
		}
		
		// ingresamos el detalle de la guia
		if( ! empty($post["deta_idproducto"])) {
			$this->load_model(array("almacen.detalle_guia_remision", "almacen.detalle_guia_remision_serie", 
				"almacen.motivo_guia", "producto", "producto_unidad"));
			
			$this->detalle_guia_remision->set("idguia_remision", $idguia_remision);
			$this->detalle_guia_remision->set("precio", 0);
			$this->detalle_guia_remision->set("finalizado", "N");
			$this->detalle_guia_remision->set("estado", "A");
			
			$this->motivo_guia->find($post["idmotivo_guia"]);
			$afecta_stock = ($this->motivo_guia->get("afecta_stock") == "S");
			
			if($afecta_stock) {
				// modelos para el almacen
				$this->load_model(array("detalle_almacen", "detalle_almacen_serie", 
					"almacen.despacho", "tipo_movi_almacen"));
				
				$this->detalle_almacen->set("tipo", "S");
				$this->detalle_almacen->set("tipo_number", -1);
				$this->detalle_almacen->set("fecha", date("Y-m-d"));
				$this->detalle_almacen->set("tabla", "GR");
				$this->detalle_almacen->set("idtabla", $idguia_remision);
				$this->detalle_almacen->set("estado", "A");
				$this->detalle_almacen->set("idsucursal", $this->guia_remision->get("idsucursal"));
				
				$this->despacho->set("idreferencia", $idguia_remision);
				$this->despacho->set("referencia", "GR");
				$this->despacho->set("tipo_docu", $this->guia_remision->get("idtipodocumento"));
				$this->despacho->set("serie", $this->guia_remision->get("serie"));
				$this->despacho->set("numero", $this->guia_remision->get("numero"));
				$this->despacho->set("observacion", $this->motivo_guia->get("descripcion"));
				$this->despacho->set("fecha", date("Y-m-d"));
				$this->despacho->set("hora", date("H:i:s"));
				$this->despacho->set("idusuario", $this->guia_remision->get("idusuario"));
				
				$this->tipo_movi_almacen->find($this->motivo_guia->get("salida_tipo_movimiento"));
				$correlativo = intval($this->tipo_movi_almacen->get("correlativo"));
			}
			
			$arrProductosKardex = array(); // datos almacen kardex
			
			foreach($post["deta_idproducto"] as $key=>$val) {
				// obtenemos el precio de costo
				$this->producto_unidad->find(array("idproducto"=>$val, "idunidad"=>$post["deta_idunidad"][$key]));
				$cantidad_um = floatval($this->producto_unidad->get("cantidad_unidad_min"));
				
				$costo = $this->producto->get_precio_compra_unitario($val, $post["idsucursal"], $post["deta_idunidad"][$key]);
				$costo_um = $costo * $cantidad_um;
				
				$this->detalle_guia_remision->set("idproducto", $val);
				$this->detalle_guia_remision->set("descripcion", $post["deta_producto"][$key]);
				$this->detalle_guia_remision->set("idunidad", $post["deta_idunidad"][$key]);
				$this->detalle_guia_remision->set("cantidad", floatval($post["deta_cantidad"][$key]));
				// $this->detalle_guia_remision->set("precio", $post["deta_precio"][$key]);
				$this->detalle_guia_remision->set("costo", $costo_um);
				$this->detalle_guia_remision->set("peso", $post["deta_peso"][$key]);
				$this->detalle_guia_remision->set("idalmacen", $post["deta_idalmacen"][$key]);
				$this->detalle_guia_remision->set("afecta_stock", $post["deta_controla_stock"][$key]);
				$this->detalle_guia_remision->set("afecta_serie", $post["deta_controla_serie"][$key]);
				$this->detalle_guia_remision->set("cantidad_um", $cantidad_um);
				$this->detalle_guia_remision->insert();
				
				// insertamos las series del detalle compra
				if($post["deta_controla_serie"][$key] == "S") {
					if( ! empty($post["deta_series"][$key])) {
						$this->detalle_guia_remision_serie->set($this->detalle_guia_remision->get_fields());
						$arr = explode("|", $post["deta_series"][$key]);
						foreach($arr as $serie) {
							$this->detalle_guia_remision_serie->set("serie", $serie);
							$this->detalle_guia_remision_serie->insert(null, false);
						}
					}
				}
				
				// si recepcionamos la nota de credito directamente, ingresamos el stock y las series al almacen
				if($afecta_stock) {
					// ingresamos a recepcion
					$this->despacho->set($this->detalle_guia_remision->get_fields());
					$this->despacho->set("iddetalle_referencia", $this->detalle_guia_remision->get("iddetalle_guia_remision"));
					$this->despacho->set("cant_despachado", $this->detalle_guia_remision->get("cantidad"));
					$this->despacho->set("correlativo", $correlativo);
					$this->despacho->set("estado", "C");
					$this->despacho->insert();
					$correlativo = $correlativo + 1; // nuevo correlativo
					
					if($post["deta_controla_stock"][$key] == "S") {
						// verificamos el stock del producto
						$stock = $this->has_stock($this->detalle_guia_remision->get_fields());
						if($stock !== TRUE) {
							$this->exception("No existe stock para el producto ".$post["deta_producto"][$key].". 
								Stock disponible: ".number_format($stock, 2));
							return false;
						}
						
						// ingresamos el stock en el almacen
						$this->detalle_almacen->set($this->detalle_guia_remision->get_fields());
						$this->detalle_almacen->set("precio_costo", $costo_um);
						$this->detalle_almacen->set("iddespacho", $this->despacho->get("iddespacho"));
						$this->detalle_almacen->insert();
						
						// verificamos para ingresar las series al almacen
						if($post["deta_controla_serie"][$key] == "S") {
							if(empty($post["deta_serie"][$key])) {
								$this->exception("Ingrese las series del producto ".$post["deta_producto"][$key]);
								return false;
							}
							
							$count_real_serie = $cantidad_um * intval($post["deta_cantidad"][$key]);
							
							$arr = explode("|", $post["deta_serie"][$key]);
							if(count($arr) != $count_real_serie) {
								$this->exception("Debe ingresar $count_real_serie series para el producto: ".$post["deta_producto"][$key]);
								return false;
							}
							
							// despachamos las series
							foreach($arr as $serie) {
								$sql = 'SELECT * FROM almacen.detalle_almacen_serie 
									WHERE estado=? AND despachado=? AND serie=? AND idalmacen=?';
								$query = $this->db->query($sql, array('A', 'N', $serie, $this->detalle_almacen->get("idalmacen")));
								
								if($query->num_rows() <= 0) {
									$this->exception("La serie {$serie} no existe o ya ha sido despachado");
									return false;
								}
								
								$this->detalle_almacen_serie->set($query->row_array());
								$this->detalle_almacen_serie->set("despachado", "S");
								$this->detalle_almacen_serie->set("fecha_salida", date("Y-m-d"));
								$this->detalle_almacen_serie->set("tabla_salida", "GR");
								$this->detalle_almacen_serie->set("idtabla_salida", $idguia_remision);
								$this->detalle_almacen_serie->set("iddespacho", $this->detalle_almacen->get("iddespacho"));
								$this->detalle_almacen_serie->update();
							}
						}
						
						$temp = $this->despacho->get_fields();
						$temp["cantidad"] = $temp["cant_despachado"];
						$temp["preciocosto"] = $costo;
						$temp["precioventa"] = 0;
						$arrProductosKardex[] = $temp;
					}
				}
			}
			
			if($afecta_stock && ! empty($arrProductosKardex)) {
				// actualizamos el correlativo del tipo movimiento
				$this->tipo_movi_almacen->set("correlativo", $correlativo);
				$this->tipo_movi_almacen->update();
				
				if( ! isset($this->jkardex)) {
					// importamos librari
					$this->load_library("jkardex");
				}
				
				// $this->jkardex->idtercero = $this->notacredito->get("idcliente");
				// $this->jkardex->idmoneda = $this->notacredito->get("idmoneda");
				// $this->jkardex->tipocambio = $this->notacredito->get("cambio_moneda");
				$this->jkardex->observacion = $post['destinatario'].'-'.$this->motivo_guia->get("descripcion");
				
				$this->jkardex->referencia("guia_remision", $idguia_remision, $post["idsucursal"], $this->motivo_guia->get("salida_tipo_movimiento"));
				$this->jkardex->salida();
				$this->jkardex->push($arrProductosKardex);
				$this->jkardex->run();
			}
		}
		
		$this->db->trans_complete();
		
		$this->response($this->guia_remision->get_fields());
	}
	
	public function eliminar($idguia_remision, $estado = "X") {
		$this->load_model("almacen.guia_remision");
		
		$this->guia_remision->find($idguia_remision);
		$idsucursal = $this->guia_remision->get("idsucursal");
		
		$this->db->trans_start();
		
		$post = $this->input->post();
		
		// eliminamos la guia de remision
		// $this->guia_remision->update(array("idguia_remision"=>$idguia_remision, "estado"=>$estado));
		$datos = array("idguia_remision"=>$idguia_remision, "estado"=>$estado, "fecha_hora_anulacion"=>date("Y-m-d H:i:s"), 
			"idusuario_anulacion"=>$this->get_var_session("idusuario"));
		if( ! empty($post["motivo"])) {
			$datos["motivo_anulacion"] = $post["motivo"];
		}
		$this->guia_remision->update($datos);
		
		// eliminamos el detalle de la guia de remision
		$this->db->where("idguia_remision", $idguia_remision)
			->update("almacen.detalle_guia_remision", array("estado"=>"I"));
			
		// eliminamos las series del detalle de la guia de remision
		$this->db->where("idguia_remision", $idguia_remision)
			->update("almacen.detalle_guia_remision_serie", array("estado"=>"I"));
		
		// eliminamos el movimiento del detalle_almacen
		$this->db->where("tabla", "GR")->where("idtabla", $idguia_remision)
			->update("almacen.detalle_almacen", array("estado"=>"I"));
			
		if($this->guia_remision->get("tipo_guia") == "S") {
			// eliminamos la salida de las series del almacen
			$this->db->where("tabla_salida", "GR")->where("idtabla_salida", $idguia_remision)->where("estado", "A")
				->update("almacen.detalle_almacen_serie", array("despachado"=>"N"));
			
			// eliminamos el despacho
			$this->db->where("idreferencia", $idguia_remision)->where("referencia", "GR")
				->update("almacen.despacho", array("estado"=>"I"));
		}
		else {
			// eliminamos el ingreso de las series en almacen
			$this->db->where("tabla_ingreso", "GR")->where("idtabla_ingreso", $idguia_remision)
				->update("almacen.detalle_almacen_serie", array("estado"=>"I"));
			
			// eliminamos la recepcion
			$this->db->where("idcompra", $idguia_remision)->where("referencia", "GR")
				->update("almacen.recepcion", array("estado"=>"I"));
		}
		
		// eliminamos el movimiento de kardex
		$this->load_library("jkardex");
		$this->jkardex->remove("guia_remision", $idguia_remision, $idsucursal);
		
		$this->db->trans_complete();
		
		$this->response($this->guia_remision->get_fields());
	}
	
	public function anular() {
		$this->load_model("almacen.guia_remision");
		$this->load_model("almacen.guia_remision_view");
		$this->guia_remision_view->set_column_pk("idguia_remision");
		
		$post = $this->input->post();
		
		$this->guia_remision_view->find($post["idguia_remision"]);
		
		if($this->guia_remision_view->get("estado") <> "A") {
			$this->exception("El comprobante ".$this->guia_remision_view->get("nroguia")." se encuentra anulado");
			return false;
		}
		
		$this->eliminar($post["idguia_remision"], "I");
	}
	
	public function serie_autocomplete() {
		$idalmacen = (int) $this->input->post("idalmacen");
		$idproducto = (int) $this->input->post("idproducto");
		$limit = (int) $this->input->post("maxRows");
		
		$referencia = $this->input->post("referencia");
		$idref = (int) $this->input->post("idreferencia");
		$idref_det = (int) $this->input->post("idreferencia_det");
		
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		if($referencia == "V") {
			$sql = "SELECT serie 
				FROM venta.detalle_venta_serie
				WHERE estado = 'A' AND idventa = ? AND idproducto = ? 
				AND iddetalle_venta = ? AND serie ILIKE ?
				ORDER BY serie LIMIT ?";
			$query = $this->db->query($sql, array($idref, $idproducto, $idref_det, $txt, $limit));
		}
		else if($referencia == "C") {
			$sql = "SELECT serie 
				FROM compra.detalle_compra_serie
				WHERE estado = 'A' AND idcompra = ? AND idproducto = ? 
				AND iddetalle_compra = ? AND serie ILIKE ?
				ORDER BY serie LIMIT ?";
			$query = $this->db->query($sql, array($idref, $idproducto, $idref_det, $txt, $limit));
		}
		else if($referencia == "G") {
			$sql = "SELECT serie 
				FROM almacen.detalle_guia_remision_serie
				WHERE estado = 'A' AND idguia_remision = ? AND idproducto = ? 
				AND iddetalle_guia_remision = ? AND serie ILIKE ?
				ORDER BY serie LIMIT ?";
			$query = $this->db->query($sql, array($idref, $idproducto, $idref_det, $txt, $limit));
		}
		else {
			$sql = "SELECT serie 
				FROM almacen.detalle_almacen_serie
				WHERE estado = 'A' AND despachado = 'N' 
				AND idalmacen = ? AND idproducto = ? AND serie ILIKE ?
				ORDER BY serie LIMIT ?";
			$query = $this->db->query($sql, array($idalmacen, $idproducto, $txt, $limit));
		}
		
		$this->response($query->result_array());
	}
	
	public function grilla_serie() {
		$idalmacen = (int) $this->input->get("idalmacen");
		$idproducto = (int) $this->input->get("idproducto");
		
		$referencia = $this->input->get("referencia");
		$idref = (int) $this->input->get("idreferencia");
		$idref_det = (int) $this->input->get("idreferencia_det");
		
		$this->load->library('datatables');
		
		$cols = array('serie'=>'Serie');
		
		if($referencia == 'V') {
			$this->load_model("venta.detalle_venta_serie");
			$this->datatables->setModel($this->detalle_venta_serie);
			$this->datatables->where('estado', '=', 'A');
			$this->datatables->where('idventa', '=', $idref);
			$this->datatables->where('iddetalle_venta', '=', $idref_det);
			$this->datatables->where('idproducto', '=', $idproducto);
		}
		else if($referencia == 'C') {
			$this->load_model("compra.detalle_compra_serie");
			$this->datatables->setModel($this->detalle_compra_serie);
			$this->datatables->where('estado', '=', 'A');
			$this->datatables->where('idcompra', '=', $idref);
			$this->datatables->where('iddetalle_compra', '=', $idref_det);
			$this->datatables->where('idproducto', '=', $idproducto);
		}
		else if($referencia == 'G') {
			$this->load_model("almacen.detalle_guia_remision_serie");
			$this->datatables->setModel($this->detalle_guia_remision_serie);
			$this->datatables->where('estado', '=', 'A');
			$this->datatables->where('idguia_remision', '=', $idref);
			$this->datatables->where('iddetalle_guia_remision', '=', $idref_det);
			$this->datatables->where('idproducto', '=', $idproducto);
		}
		else {
			$cols['fecha_ingreso'] = 'Fec. ingreso';
			
			$this->load_model("almacen.detalle_almacen_serie");
			$this->datatables->setModel($this->detalle_almacen_serie);
			$this->datatables->where('estado', '=', 'A');
			$this->datatables->where('despachado', '=', 'N');
			$this->datatables->where('idalmacen', '=', $idalmacen);
			$this->datatables->where('idproducto', '=', $idproducto);
		}
		
		$this->datatables->setColumns(array_keys($cols));
		$this->datatables->order_by("fecha_ingreso", "asc");
		
		$this->datatables->setPopup(true);
		
		$table = $this->datatables->createTable(array_values($cols));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	public function grilla_popup() {
		$this->load_model("almacen.motivo_guia");
		$temp = $this->motivo_guia->find($this->input->get("idmotivo_guia"));
		
		$tipo_guia = ($this->input->get("tipo_guia") == "S") ? "I" : "S";
		
		$this->load_model("almacen.guia_remision_view");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->guia_remision_view);
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('tipo_guia', '=', $tipo_guia);
		// descomentar para buscar las guias segun el motivo de salida
		$this->datatables->where('idmotivo_guia', '=', $temp["idmotivo_guia"]);
		
		if($temp["ingreso_b_esta_sede"] == "N" || $temp["ingreso_b_otra_sede"] == "N") {
			if($temp["ingreso_b_esta_sede"] == "S")
				$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
			if($temp["ingreso_b_otra_sede"] == "S"){
				$this->datatables->where('idsucursal', '<>', $this->get_var_session("idsucursal"));
				$this->datatables->where('usado', '=', 'N');
			}
		}
		
		$this->datatables->setColumns(array('fecha_traslado','nroguia','destinatario','punto_partida','punto_llegada','motivo'));
		$this->datatables->setPopup(true);
		
		$table = $this->datatables->createTable(array('Fec.Traslado','Nro.Guia','Destinatario','Partidad','Llegada','Motivo'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	public function get_detalle($idguia) {
		$sql = "select dv.iddetalle_guia_remision, dv.descripcion as producto, u.descripcion as unidad,
			dv.cantidad, dv.afecta_stock as controla_stock, dv.afecta_serie as controla_serie, 
			dv.idalmacen, dv.idproducto, dv.idunidad, 
			array_to_string(array_agg(dvs.serie), '|'::text) as serie
			from almacen.detalle_guia_remision dv
			join compra.unidad u on u.idunidad = dv.idunidad
			left join almacen.detalle_guia_remision_serie dvs on dvs.iddetalle_guia_remision=dv.iddetalle_guia_remision 
				and dvs.idguia_remision=dv.idguia_remision and dvs.idproducto=dv.idproducto and dvs.estado='A'
			where dv.estado = 'A' and dv.idguia_remision = ?
			group by dv.iddetalle_guia_remision, dv.descripcion, u.descripcion, dv.cantidad, dv.afecta_stock, 
				dv.afecta_serie, dv.idalmacen, dv.idproducto, dv.idguia_remision, dv.idunidad
			order by iddetalle_guia_remision";
		$query = $this->db->query($sql, array($idguia));
		$this->response($query->result_array());
	}
	
	public function imprimir($id){
		$this->load_model('almacen.guia_remision');
		$this->load->library('numeroLetra');
		$this->guia_remision->find($id);
		
		$idsucursal 	 = $this->guia_remision->get("idsucursal");
		$idtipodocumento = $this->guia_remision->get("idtipodocumento");
		$serie 			 = $this->guia_remision->get("serie");

		$sql = $this->db->query("SELECT contenido,cantidad_filas_detalle,ver_borde FROM general.formato_documento WHERE estado='A' AND idtipodocumento='$idtipodocumento' AND serie='$serie' AND idsucursal='$idsucursal';");
		$reg 				= $sql->row('contenido');
		$cant_filas_detalle = $sql->row('cantidad_filas_detalle');
		$ver_borde  	 	= $sql->row("ver_borde");
		
		$border = 'none';
		if($ver_borde=='S'){
			$border = '0.5px solid #ccc';
		}
		
		$this->load_model('general.formato_documento');
		$this->formato_documento->find(array("idtipodocumento"=>$idtipodocumento,"idsucursal"=>$idsucursal,"serie"=>$serie));
		
		if(!empty($reg)){
			$sql = $this->db->query("SELECT 
									COALESCE(td.abreviatura||'-'||'')||COALESCE(gr.serie||'-','')||COALESCE(gr.numero,'') comprobante_op
									,to_char(gr.fecha_traslado,'DD/MM/YYYY') f_traslado
									,gr.punto_partida
									,gr.destinatario nombre_cliente
									,gr.ruc_destinatario ruc_cliente
									,gr.punto_llegada
									,CAST('DNI' AS text)tdoc_cli
									,gr.dni_destinatario dni_cliente
									,((SELECT COALESCE(tdoc.abreviatura||'-','') FROM venta.tipo_documento tdoc WHERE tdoc.idtipodocumento=v.idtipodocumento )||COALESCE(v.serie||'-','')||COALESCE(v.correlativo,'')) doc_referencia
									,(to_char(v.fecha_venta,'DD/MM/YY')) f_venta_referenc
									,gr.transporte transportista
									,gr.ruc_transporte ruc_transportista
									,split_part(marca_nroplaca,' ',1) marca_transp
									,gr.const_inscripcion cert_inscr
									,split_part(marca_nroplaca,' ',2) nro_placa
									,gr.lic_conducir
									,SUM(dg.peso::double precision*dg.cantidad) peso_total
									FROM almacen.guia_remision gr
									JOIN venta.tipo_documento td ON td.idtipodocumento=gr.idtipodocumento
									JOIN (select idguia_remision,cast(case when peso = '' then '0' else peso end as numeric) peso,cantidad FROM almacen.detalle_guia_remision where estado = 'A') dg on dg.idguia_remision = gr.idguia_remision
									LEFT JOIN venta.venta v ON v.idventa=gr.idreferencia AND gr.referencia='V'
									WHERE gr.idguia_remision = $id
									GROUP BY  td.abreviatura,gr.serie,gr.numero,gr.fecha_traslado,gr.punto_partida,gr.destinatario
									, gr.ruc_destinatario,gr.punto_llegada,gr.dni_destinatario
									,v.idtipodocumento,v.fecha_venta,v.serie, v.correlativo,gr.marca_nroplaca
									, gr.transporte
									, gr.ruc_transporte,gr.const_inscripcion,gr.lic_conducir

									 ;");
			$dato = $sql->row_array();
			foreach($dato as $k=>$v){
				$reg=str_replace("{".$k."}",$v,$reg);
			}

			$sql = $this->db->query("SELECT
									(ROW_NUMBER() OVER (ORDER BY idguia_remision))||':::'||(ROW_NUMBER() OVER (ORDER BY idguia_remision)) d_item
									,(ROW_NUMBER() OVER (ORDER BY idguia_remision))||':::'||dgr.cantidad d_cant
									,(ROW_NUMBER() OVER (ORDER BY idguia_remision))||':::'||u.abreviatura d_um
									,(ROW_NUMBER() OVER (ORDER BY idguia_remision))||':::'||dgr.descripcion d_descripcion
									--,(ROW_NUMBER() OVER (ORDER BY idguia_remision))||':::'||COALESCE(dgr.peso,'0') d_peso
									,(ROW_NUMBER() OVER (ORDER BY idguia_remision))||':::'||cast(case when peso = '' then '0' else peso end as numeric)*dgr.cantidad d_peso 
									FROM almacen.detalle_guia_remision dgr
									JOIN compra.unidad u ON u.idunidad=dgr.idunidad AND dgr.estado='A'
									WHERE dgr.idguia_remision='$id'
									ORDER BY (ROW_NUMBER() OVER (ORDER BY idguia_remision));");

			$detalle = $sql->result_array();

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
			echo "<script>window.print();</script>";
			echo "<script>window.close();</script>";
		}else{
			echo "Error, formato no definido :(";
		}
	}
	
	public function restaurar($idguia_remision) {
		$this->load_model("almacen.guia_remision");
		
		$this->guia_remision->find($idguia_remision);
		
		if($this->guia_remision->get("estado") == "A") {
			$this->exception("La Guia de Remision ".$this->guia_remision->get("serie")."-".
				$this->guia_remision->get("numero")." se encuentra activo");
			return false;
		}
		
		$idsucursal = $this->guia_remision->get("idsucursal");
		
		$this->db->trans_start();
		
		// restablecemos la venta
		$this->guia_remision->update(array(
			"idguia_remision" => $idguia_remision
			,"motivo_anulacion" => null
			,"fecha_hora_anulacion" => null
			,"idusuario_anulacion" => null
			,"estado" => "A"
		));
		
		// eliminamos el detalle de la guia de remision
		$this->db->where("idguia_remision", $idguia_remision)
			->update("almacen.detalle_guia_remision", array("estado"=>"A"));
			
		// eliminamos las series del detalle de la guia de remision
		$this->db->where("idguia_remision", $idguia_remision)
			->update("almacen.detalle_guia_remision_serie", array("estado"=>"A"));
		
		// eliminamos el movimiento del detalle_almacen
		$this->db->where("tabla", "GR")->where("idtabla", $idguia_remision)
			->update("almacen.detalle_almacen", array("estado"=>"A"));
			
		if($this->guia_remision->get("tipo_guia") == "S") {
			// eliminamos la salida de las series del almacen
			$this->db->where("tabla_salida", "GR")->where("idtabla_salida", $idguia_remision)->where("estado", "A")
				->update("almacen.detalle_almacen_serie", array("despachado"=>"S"));
			
			// eliminamos el despacho
			$this->db->where("idreferencia", $idguia_remision)->where("referencia", "GR")
				->update("almacen.despacho", array("estado"=>"A"));
		}
		else {
			// eliminamos el ingreso de las series en almacen
			$this->db->where("tabla_ingreso", "GR")->where("idtabla_ingreso", $idguia_remision)
				->update("almacen.detalle_almacen_serie", array("estado"=>"A"));
			
			// eliminamos la recepcion
			$this->db->where("idcompra", $idguia_remision)->where("referencia", "GR")
				->update("almacen.recepcion", array("estado"=>"A"));
		}
		
		// eliminamos el movimiento de kardex
		$this->load_library("jkardex");
		$this->jkardex->restore("guia_remision", $idguia_remision, $idsucursal);
		
		$this->db->trans_complete();
		
		$this->response($this->guia_remision->get_fields());
	}
}

?>