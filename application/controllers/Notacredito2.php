<?php

include_once "Controller.php";

class Notacredito extends Controller {
	
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
	
	private function _get_motivos() {
		return array("ANULACION", "BONIFICACION", "DESCUENTO", "DEVOLUCION", "OTROS");
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		
		$igv = $this->get_param("igv");
		if(!is_numeric($igv)) {
			$igv = 18;
		}
		$data["valor_igv"] = $igv;
		$data["idtipodocumento"] = $this->get_param("idnota_credito");
		
		$this->load->library("combobox");
		
		// combo motivo
		$this->combobox->setAttr(array("id"=>"motivo", "name"=>"motivo", "class"=>"form-control input-xs"));
		$this->combobox->addItem($this->_get_motivos());
		if(isset($data["notacredito"]["motivo"])) {
			$this->combobox->setSelectedOption($data["notacredito"]["motivo"]);
		}
		$data["motivo"] = $this->combobox->getObject(true);
		
		// combo serie
		$this->combobox->setAttr(array("id"=>"serie", "name"=>"serie", "class"=>"form-control input-xs"));
		$this->combobox->setStyle("width", "100px");
		$sql = "SELECT serie 
			FROM venta.serie_documento
			WHERE idtipodocumento = ? AND idsucursal = ?
			ORDER BY serie";
		$query = $this->db->query($sql, array($data["idtipodocumento"], $this->get_var_session("idsucursal")));
		$this->combobox->addItem($query->result_array());
		if(isset($data["notacredito"]["serie"])) {
			$this->combobox->setSelectedOption($data["notacredito"]["serie"]);
		}
		$data["serie"] = $this->combobox->getObject(true);
		
		// combo tipo nota
		$sql = "select idtipo_notacredito, descripcion from general.tipo_notacredito order by 1";
		$query = $this->db->query($sql);
		$this->combobox->addItem($query->result_array());
		$this->combobox->setAttr(array("id"=>"idtipo_notacredito", "name"=>"idtipo_notacredito", "class"=>"form-control input-xs"));
		$this->combobox->removeStyle("width");
		if(isset($data["notacredito"]["idtipo_notacredito"])) {
			$this->combobox->setSelectedOption($data["notacredito"]["idtipo_notacredito"]);
		}
		$data["tiponota"] = $this->combobox->getObject(true);

		//////////////////////////////////////////////////////// combo moneda
		$query = $this->db->select('idmoneda, descripcion')->where("estado", "A")
			->order_by("idmoneda", "asc")->get("general.moneda");
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idmoneda","name"=>"idmoneda","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem($query->result_array());
		if( isset($data["notacredito"]["idmoneda"]) ) {
			$this->combobox->setSelectedOption($data["notacredito"]["idmoneda"]);
		}
		$data["moneda"] = $this->combobox->getObject();
		
		//////////////////////////////////////// combos temporales facturacion /////////////////////////////
		$query = $this->db->order_by("orden", "asc")->get("general.grupo_igv");
		$this->combobox->removeAllItems();
		$this->combobox->setAttr("id", "grupo_igv_temp");
		$this->combobox->setAttr("name", "grupo_igv_temp");
		$this->combobox->addItem($query->result_array(), array("codgrupo_igv","decripcion","tipo_igv_default","tipo_igv_oferta","igv"));
		$data["combobox_grupo_igv"] = clone $this->combobox;
		$data["combo_grupo_igv"] = $this->combobox->getObject();
		
		$sql = "select codtipo_igv, codtipo_igv||': '||descripcion as descripcion from general.tipo_igv order by 1";
		$query = $this->db->query($sql);
		$this->combobox->removeAllItems();
		$this->combobox->setAttr("id", "tipo_igv_temp");
		$this->combobox->setAttr("name", "tipo_igv_temp");
		$this->combobox->addItem($query->result_array());
		$data["combobox_tipo_igv"] = clone $this->combobox;
		$data["combo_tipo_igv"] = $this->combobox->getObject();
		
		$data["default_igv"] = $this->get_param("default_igv");
		
		////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		$data["controller"] = $this->controller;
		
		$nueva_nota = "true";
		if( isset($data["notacredito"]["idnotacredito"]) ) {
			$nueva_nota = "false";
		}
		$this->js("<script>var _es_nueva_nota_ = $nueva_nota;</script>", false);
		
		// echo "<pre>";print_r($data);echo "</pre>";exit;
		
		$this->js('form/'.$this->controller.'/form');
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model('venta.notacredito_view');
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->notacredito_view);
		$this->datatables->setIndexColumn("idnotacredito");
		
		$this->datatables->where('estado', '=', "A");
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		
		$cols = array(
			"fecha"=>"Fecha"
			,"nrodocumento"=>"Nota credito"
			,"cliente"=>"Cliente"
			,"subtotal"=>"Subtotal"
			,"igv"=>"IGV"
			,"total"=>"Total"
			,"motivo"=>"Motivo"
			,"tipodoc_ref"=>"Doc. modifica"
		);
		
		$this->datatables->setColumns(array_keys($cols));
		
		$this->datatables->order_by('fecha', 'desc');
		$this->datatables->setCallback("formatoFechaGrilla");
		
		$table = $this->datatables->createTable(array_values($cols));
		
		$script = "<script>".$this->datatables->createScript()."</script>";
		
		$this->js($script, false);

		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Nota de credito");
		$this->set_subtitle("");
		$this->set_content($this->form(array("readonly"=>false, "nuevo"=>true)));
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model(array("venta.notacredito", "venta.notacredito_view"));
		
		$data["notacredito"] = $this->notacredito->find($id);
		$data["notacredito"]["fecha_ref"] = fecha_es($data["notacredito"]["fecha_ref"]);
		
		$this->notacredito->set_column_pk("idnotacredito");
		$data["notacredito_view"] = $this->notacredito_view->find($id);
		
		$this->load_model("detalle_notacredito");
		$data["detalle"] = $this->detalle_notacredito->get_items($id);
		
		$data["readonly"] = ! ($this->notacredito->get("fecha") == date("Y-m-d"));
		$data["nuevo"] = false;
		
		$this->set_title("Modificar Nota de credito");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->unlimit();
		$this->load_model("venta.notacredito");
		
		$post = $this->input->post();
		$post["idtipodocumento"] = $this->get_param("idnota_credito");
		$post["idusuario"] = $this->get_var_session("idusuario");
		$post["idsucursal"] = $this->get_var_session("idsucursal");
		$post["estado"] = "A";
		if(empty($post["igv"]))
			$post["igv"] = 0;
		if(empty($post["idmoneda"]))
			$post["idmoneda"] = 1;
		if(empty($post["cambio_moneda"]))
			$post["cambio_moneda"] = 1;
		
		$es_devolucion = ($post["motivo"] == "DEVOLUCION");
		$es_nuevo = empty($post["idnotacredito"]);
		
		if($es_nuevo) {
			$valid = $this->is_valid_doc_nota($post["idtipodocumento"], $post["serie"], $post["iddocumento_ref"], 
				$post["serie_ref"], $post["idcliente"],$post["total"],$post["idmoneda"]);
			if($valid !== true) {
				$this->exception($valid);
				return;
			}
		}
		
		$this->db->trans_start();
		
		if($es_nuevo) {
			// verificamos el documento generado
			if($this->has_comprobante("notacredito", $post["idtipodocumento"], $post["serie"], $post["numero"])) {
				$this->exception("Ya se ha generado la nota de credito ".$post["serie"]."-".$post["numero"]);
				return false;
			}
			
			$post["fecha"] = date("Y-m-d");
			
			$idnotacredito = $this->notacredito->insert($post);
			
			// actualizamos el correlativo del documento
			$this->update_correlativo($post["idtipodocumento"], $post["serie"]);
		}
		else {
			$this->notacredito->update($post);
			
			$idnotacredito = $post["idnotacredito"];
			
			// eliminamos el detalle de la nota de credito
			$this->db->where("idnotacredito", $idnotacredito)
				->update("venta.detalle_notacredito", array("estado"=>"I"));
			
			// eliminamos el ingreso en detalle_almacen
			$this->db->where("tabla", "NC")->where("idtabla", $idnotacredito)
				->update("almacen.detalle_almacen", array("estado"=>"I"));
			
			// eliminamos el ingreso de las series en almacen
			$this->db->where("tabla_ingreso", "NC")->where("idtabla_ingreso", $idnotacredito)
				->update("almacen.detalle_almacen_serie", array("estado"=>"I"));
			
			// eliminamos la recepcion
			$this->db->where("idcompra", $idnotacredito)->where("referencia", "NC")
				->update("almacen.recepcion", array("estado"=>"I"));
			
			// eliminamos el ingreso de kardex
			$this->load_library("jkardex");
			$this->jkardex->remove("notacredito", $idnotacredito, $post["idsucursal"]);
		}
		
		// ingresamos el detalle de la nota de credito
		if( ! empty($post["deta_idproducto"])) {
			$this->load_model("venta.detalle_notacredito");
			$this->detalle_notacredito->set("idnotacredito", $idnotacredito);
			$this->detalle_notacredito->set("estado", "A");
			
			if($es_devolucion) {
				// modelos para el almacen
				$this->load_model("detalle_almacen");
				$this->load_model("detalle_almacen_serie");
				$this->load_model("recepcion");
				$this->load_model("tipo_movi_almacen");
				$this->load_model("producto");
				$this->load_model("producto_unidad");
				
				$this->detalle_almacen->set("tipo", "E");
				$this->detalle_almacen->set("tipo_number", 1);
				$this->detalle_almacen->set("fecha", date("Y-m-d"));
				$this->detalle_almacen->set("tabla", "NC");
				$this->detalle_almacen->set("idtabla", $idnotacredito);
				$this->detalle_almacen->set("estado", "A");
				$this->detalle_almacen->set("idsucursal", $this->notacredito->get("idsucursal"));
				
				$this->detalle_almacen_serie->set("fecha_ingreso", date("Y-m-d"));
				$this->detalle_almacen_serie->set("tabla_ingreso", "NC");
				$this->detalle_almacen_serie->set("idtabla_ingreso", $idnotacredito);
				$this->detalle_almacen_serie->set("despachado", "N");
				$this->detalle_almacen_serie->set("estado", "A");
				$this->detalle_almacen_serie->set("idsucursal", $this->notacredito->get("idsucursal"));
				
				$this->recepcion->set("idcompra", $idnotacredito);
				$this->recepcion->set("tipo_docu", $this->notacredito->get("idtipodocumento"));
				$this->recepcion->set("serie", $this->notacredito->get("serie"));
				$this->recepcion->set("numero", $this->notacredito->get("numero"));
				$this->recepcion->set("observacion", $this->notacredito->get("descripcion"));
				$this->recepcion->set("fecha", date("Y-m-d"));
				$this->recepcion->set("hora", date("H:i:s"));
				$this->recepcion->set("idusuario", $this->notacredito->get("idusuario"));
				$this->recepcion->set("referencia", "NC");
				
				$this->tipo_movi_almacen->find($this->get_idtipo_movimiento("notacredito"));
				$correlativo = intval($this->tipo_movi_almacen->get("correlativo"));
			}
			
			$arrProductosKardex = array(); // datos almacen kardex
			// $deta_igv = ( ! empty($post["valor_igv"])) ? floatval($post["valor_igv"])/100 : 0;
			$this->load_model("general.grupo_igv");
			
			foreach($post["deta_idproducto"] as $key=>$val) {
				$this->grupo_igv->find($post["deta_grupo_igv"][$key]);
				
				$this->detalle_notacredito->set("idproducto", $val);
				$this->detalle_notacredito->set("descripcion", $post["deta_producto"][$key]);
				$this->detalle_notacredito->set("idunidad", $post["deta_idunidad"][$key]);
				$this->detalle_notacredito->set("cantidad", $post["deta_cantidad"][$key]);
				$this->detalle_notacredito->set("precio", $post["deta_precio"][$key]);
				$this->detalle_notacredito->set("idalmacen", $post["deta_idalmacen"][$key]);
				$this->detalle_notacredito->set("afecta_stock", $post["deta_controla_stock"][$key]);
				$this->detalle_notacredito->set("afecta_serie", $post["deta_controla_serie"][$key]);
				$this->detalle_notacredito->set("serie", $post["deta_serie"][$key]);
				$this->detalle_notacredito->set("igv", floatval($this->grupo_igv->get("igv")));
				$this->detalle_notacredito->set("codgrupo_igv", $post["deta_grupo_igv"][$key]);
				$this->detalle_notacredito->set("codtipo_igv", $post["deta_tipo_igv"][$key]);
				$this->detalle_notacredito->insert();
				
				// si recepcionamos la nota de credito directamente, ingresamos el stock y las series al almacen
				if($es_devolucion) {
					// ingresamos a recepcion
					$this->recepcion->set($this->detalle_notacredito->get_fields());
					$this->recepcion->set("iddetalle_compra", $this->detalle_notacredito->get("iddetalle_notacredito"));
					$this->recepcion->set("cant_recepcionada", $this->detalle_notacredito->get("cantidad"));
					$this->recepcion->set("correlativo", $correlativo);
					$this->recepcion->set("estado", "C");
					$this->recepcion->insert();
					$correlativo = $correlativo + 1; // nuevo correlativo
					
					if($post["deta_controla_stock"][$key] == "S") {
						$this->producto_unidad->find(array("idproducto"=>$val, "idunidad"=>$post["deta_idunidad"][$key]));
						$cantidad_um = floatval($this->producto_unidad->get("cantidad_unidad_min"));
						
						// $costo = $this->producto->get_precio_compra_unitario(
							// $val, $post["idsucursal"], $post["deta_idunidad"][$key], $post["idmoneda"]);
						
						$costo = $this->producto->get_precio_costo_unitario($val, $post["idsucursal"]);
						$costo *= floatval($post["cambio_moneda"]);
						$costo_um = $costo * $cantidad_um;
						
						// ingresamos el stock en el almacen
						$this->detalle_almacen->set($this->detalle_notacredito->get_fields());
						$this->detalle_almacen->set("precio_costo", $costo_um);
						$this->detalle_almacen->set("idrecepcion", $this->recepcion->get("idrecepcion"));
						$this->detalle_almacen->insert();
						
						// verificamos para ingresar las series al almacen
						if($post["deta_controla_serie"][$key] == "S") {
							if(empty($post["deta_serie"][$key])) {
								$this->exception("Ingrese las series del producto ".$post["deta_producto"][$key]);
								return false;
							}
							
							$count_real_serie = intval($this->producto_unidad->get("cantidad_unidad_min")) * intval($post["deta_cantidad"][$key]);
							
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
						$temp["precioventa"] = floatval($post["deta_precio"][$key]) / $cantidad_um;
						$arrProductosKardex[] = $temp;
					}
				}
			}
			
			if($es_devolucion && ! empty($arrProductosKardex)) {
				// actualizamos el correlativo del tipo movimiento
				$this->tipo_movi_almacen->set("correlativo", $correlativo);
				$this->tipo_movi_almacen->update();
				
				if( ! isset($this->jkardex)) {
					// importamos librari
					$this->load_library("jkardex");
				}
				
				$this->jkardex->idtercero = $this->notacredito->get("idcliente");
				$this->jkardex->idmoneda = $this->notacredito->get("idmoneda");
				$this->jkardex->tipocambio = $this->notacredito->get("cambio_moneda");
				
				$this->jkardex->referencia("notacredito", $idnotacredito, $post["idsucursal"]);
				$this->jkardex->entrada();
				$this->jkardex->push($arrProductosKardex);
				$this->jkardex->run();
			}
		}
		
		$this->db->trans_complete();
		
		// verificamos si se va crear los archivos de la facturacion
		if($es_nuevo) {
			if($this->es_electronico($post["idtipodocumento"], $post["serie"]) && $this->get_param("facturacion_electronica") == "S")
				$this->send_to_facturador("notacredito", $idnotacredito, $this->get_var_session("idsucursal"));
		}
		
		$this->response($this->notacredito->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model('venta.notacredito');
		// cambiamos de estado
		$fields['idnotacredito'] = $id;
		$fields['estado'] = "I";
		
		// $fields['controller']=$this->controller;
		// $fields['accion']=__FUNCTION__;
		
		$this->notacredito->update($fields);
		
		$this->response($fields);
	}
	
	public function imprimir($id) {
		$this->load_model(array('venta.notacredito','venta.tipo_documento'));
		$this->load->library('numeroLetra');
		
		$this->notacredito->find($id);
		
		// verificamos si corresponde a la facturacion electronica
		$cdp = $this->tipo_documento->find($this->notacredito->get("idtipodocumento"));
		$fe = $this->get_param("facturacion_electronica");
		if($cdp["facturacion_electronica"] == "S" && $fe == "S") {
			$this->imprimir_formato($id, "notacredito");
			return;
		}
		
		$idsucursal 	 = $this->notacredito->get("idsucursal");
		$idtipodocumento = $this->notacredito->get("idtipodocumento");
		$serie 			 = $this->notacredito->get("serie");
		
		$this->load_model('general.formato_documento');
		$this->formato_documento->find(array("idtipodocumento"=>$idtipodocumento,"idsucursal"=>$idsucursal,"serie"=>$serie));
		$reg= $this->formato_documento->get('contenido');
		
		$sql = $this->db->query("SELECT
								COALESCE(nc.serie||'-','')||COALESCE(nc.numero) comprobante_op
								,to_char(fecha,'DD/MM/YYYY') f_op
								,to_char(fecha,'DD') day_op
								,to_char(fecha,'MM') month_op
								,to_char(fecha,'YYYY') year_op
								,v.tipo_documento comprobante_referencia
								,((SELECT COALESCE(tdoc.abreviatura||'-','') FROM venta.tipo_documento tdoc WHERE tdoc.idtipodocumento=v.idtipodocumento )||COALESCE(v.serie||'-','')||COALESCE(v.correlativo,'')) doc_referencia
								,nc.descripcion motivo_op
								,v.nombres nombre_cliente
								,v.ruc
								,nc.subtotal subt_op
								,(nc.subtotal+nc.igv) total_op
								,nc.igv
								,nc.idnotacredito
								FROM venta.notacredito nc
								JOIN venta.tipo_documento td ON td.idtipodocumento=nc.idtipodocumento
								JOIN venta.venta_view v ON v.idventa=nc.idventa
								WHERE nc.idnotacredito=$id;");
								
		$dato = $sql->row_array();

		foreach($dato as $k=>$v){
			if($k=='total_letras'){
				$v = $this->numeroletra->convertir(number_format($v, 2, '.', ''), true);
			}
			$reg=str_replace("{".$k."}",$v,$reg);
		}
		
		$sql = $this->db->query("SELECT
								(ROW_NUMBER() OVER (ORDER BY idnotacredito))||':::'||dn.descripcion d_descripcion
								,(ROW_NUMBER() OVER (ORDER BY idnotacredito))||':::'||CAST(dn.cantidad AS numeric(10,2)) d_cant
								,(ROW_NUMBER() OVER (ORDER BY idnotacredito))||':::'||CAST(dn.precio AS numeric(10,2)) d_pu 
								,(ROW_NUMBER() OVER (ORDER BY idnotacredito))||':::'||CAST(dn.precio*dn.cantidad AS numeric(10,2)) d_imp
								FROM venta.detalle_notacredito dn
								WHERE dn.idnotacredito=$id AND dn.estado='A'
								ORDER BY (ROW_NUMBER() OVER (ORDER BY idnotacredito));");

		$detalle = $sql->result_array();
		$this->imprimir_comprobante_fisico($idtipodocumento, $idsucursal, $serie, $reg , $detalle);
	}
	
	public function print_test($id){
		$this->imprimir_formato($id,"notacredito","venta",true);
	}
}

?>