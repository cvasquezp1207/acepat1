<?php

include_once "Controller.php";

class Despacho extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Despacho de ventas");
		$this->set_subtitle("Lista de ventas");
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
		
		$this->load->library('combobox');
		
		// combo tipodocumento
		$this->combobox->init(); // un nuevo combo		
		$this->combobox->setAttr(
			array(
				"id"=>"idtipodocumento"
				,"name"=>"idtipodocumento"
				,"class"=>"form-control"
			)
		);
		$this->db->select('idtipodocumento, descripcion');
		$query = $this->db->where("estado", "A")->get("venta.tipo_documento");
		$this->combobox->addItem($query->result_array());
		$data["tipodocumento"] = $this->combobox->getObject();
		
		// combo almacen
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr("id", "idalmacen_temp");
		$query = $this->db->select('idalmacen, descripcion')->where("estado", "A")
			->where("idsucursal", $this->get_var_session("idsucursal"))->get("almacen.almacen");
		$this->combobox->addItem($query->result_array());
		$data["almacen"] = $this->combobox->getObject();
		
		$data["controller"] = $this->controller;
		
		$this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
		
		$this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		$this->js('form/'.$this->controller.'/form');
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	public function form_view($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		
		$data["controller"] = $this->controller;
		
		$this->js('form/'.$this->controller.'/form_view');
		
		return $this->load->view($this->controller."/form_view", $data, true);
	}
	
	public function filtros_grilla($despachado) {
		$this->load_library("combobox");
		
		$html = '<div class="row">';
		
		// div y combobox 
		$this->combobox->setAttr("filter", "despachado");
		$this->combobox->setAttr("class", "form-control");
		$this->combobox->addItem("N", "PENDIENTE");
		$this->combobox->addItem("S", "DESPACHADO");
		$this->combobox->setSelectedOption($despachado);
		
		$html .= '<div class="col-sm-4"><div class="form-group">';
		$html .= '<label class="control-label">Despachado</label>';
		$html .= $this->combobox->getObject();;
		$html .= '</div></div>';
		
		$html .= '</div>';
		
		$this->set_filter($html);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model("almacen.despacho_view");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->despacho_view);
		$this->datatables->setIndexColumn("idventa");
		
		$despachado = "N";
		
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('despachado', '=', $despachado);
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
	
		$this->datatables->setColumns(array('fecha_venta','documento','cliente','cantidad','fecha_despacho'));
		$this->datatables->order_by("fecha_venta", "desc");
		$this->datatables->setCallback("format_fecha");
		
		$columnasName = array('Fecha','Venta','Cliente','Item despachados','Ultimo despacho');
		
		$table = $this->datatables->createTable($columnasName);
		$script = "<script>".$this->datatables->createScript()."</script>";
		
		$this->js($script, false);
		
		$this->filtros_grilla($despachado);
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Despacho");
		$this->set_subtitle("");
		
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model("venta.venta_view");
		$data["venta"] = $this->venta_view->find(array("idventa"=>$id));
		
		$sql = "select d.iddespacho, p.descripcion_detallada as producto, a.descripcion as almacen,
				t.abreviatura as tipodocumento, d.serie, d.numero, d.observacion, 
				to_char(d.fecha,'DD/MM/YYYY') as fecha, to_char(d.hora::interval, 'HH12:MI am') as hora,
				u.descripcion as unidad, d.cant_despachado as cantidad, e.nombres as usuario, 
				array_to_string(array_agg(das.serie), '|'::text) AS series
			from almacen.despacho d
			join compra.producto p on p.idproducto = d.idproducto
			join almacen.almacen a on a.idalmacen = d.idalmacen
			join venta.tipo_documento t on t.idtipodocumento = d.tipo_docu::integer
			join compra.unidad u on u.idunidad = d.idunidad
			join seguridad.usuario e on e.idusuario = d.idusuario
			left join almacen.detalle_almacen_serie das on das.iddespacho = d.iddespacho and das.estado='A'
				and das.tabla_salida = 'V' and das.idtabla_salida = d.idreferencia and das.despachado='S'
			where d.estado <> 'I' and d.referencia = 'V' and d.idreferencia = ?
			group by d.iddespacho, p.descripcion_detallada, a.descripcion, t.abreviatura, d.serie, d.numero,
				d.observacion, d.fecha, d.hora, u.descripcion, d.cant_despachado, e.nombres
			order by d.iddespacho desc, d.fecha desc";
		$query = $this->db->query($sql, array($id));
		$data["detalle"] = $query->result_array();
		
		$this->set_title("Lista de despachos");
		$this->set_subtitle("");
		$this->set_content($this->form_view($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$fields = $this->input->post();
		$fields['idsucursal'] = $this->get_var_session("idsucursal");
		$fields['idusuario'] = $this->get_var_session("idusuario");
		$idventa = $fields["idventa"];
		
		// modelos
		$this->load_model($this->controller);
		$this->load_model("venta");
		$this->load_model("detalle_venta");
		$this->load_model("detalle_venta_serie");
		$this->load_model("compra.producto_unidad");
		$this->load_model("detalle_almacen");
		$this->load_model("detalle_almacen_serie");
		$this->load_model("almacen.despacho");
		$this->load_model("tipo_movi_almacen");
		
		$this->venta->find(array("idventa"=>$idventa, "idsucursal"=>$fields["idsucursal"]));
		
		$this->detalle_almacen->set("tipo", "S");
		$this->detalle_almacen->set("tipo_number", -1);
		$this->detalle_almacen->set("fecha", date("Y-m-d"));
		$this->detalle_almacen->set("tabla", "V");
		$this->detalle_almacen->set("idtabla", $idventa);
		$this->detalle_almacen->set("estado", "A");
		$this->detalle_almacen->set("idsucursal", $this->venta->get("idsucursal"));
		
		$this->despacho->set("idreferencia", $idventa);
		$this->despacho->set("referencia", "V");
		$this->despacho->set("tipo_docu", $fields["idtipodocumento"]);
		$this->despacho->set("serie", $fields["serie"]);
		$this->despacho->set("numero", $fields["numero"]);
		$this->despacho->set("observacion", $fields["observacion"]);
		$this->despacho->set("fecha", date("Y-m-d"));
		$this->despacho->set("hora", date("H:i:s"));
		$this->despacho->set("idusuario", $fields["idusuario"]);
		
		$this->tipo_movi_almacen->find($this->get_idtipo_movimiento("venta"));
		$correlativo = intval($this->tipo_movi_almacen->get("correlativo"));
		
		$this->db->trans_start(); // inciamos transaccion
		$arrProductosKardex = array();
		
		// recorremos lista de producto
		foreach($fields["deta_iddetalle"] as $key=>$val) {
			$cantidad = floatval($fields["deta_ingreso"][$key]);
			$pendiente = floatval($fields["deta_pendiente"][$key]);
			
			if($cantidad > $pendiente) {
				$this->exception("La cantidad a despachar es mayor a la cantidad pendiente 
					para el producto ".$fields["deta_producto"][$key]);
				return false;
			}
			
			$this->detalle_venta->find(array("iddetalle_venta"=>$val, "idventa"=>$idventa));
			
			// insertamos las series del detalle de la venta
			if($fields["deta_controla_serie"][$key] == "S") {
				if( ! empty($fields["deta_series"][$key])) {
					$this->detalle_venta_serie->set($this->detalle_venta->get_fields());
					$this->detalle_venta_serie->set("despachado", "S");
					$arr = explode("|", $fields["deta_series"][$key]);
					foreach($arr as $serie) {
						$this->detalle_venta_serie->set("serie", $serie);
						$this->detalle_venta_serie->save(null, false);
					}
				}
			}
			
			// hacemos el despacho de todos modos
			$this->despacho->set($this->detalle_venta->get_fields());
			$this->despacho->set("idalmacen", $fields["deta_idalmacen"][$key]);
			$this->despacho->set("cant_despachado", $cantidad);
			$this->despacho->set("correlativo", $correlativo);
			$this->despacho->set("estado", "C");
			$this->despacho->set("iddetalle_referencia", $val);
			$this->despacho->insert();
			$correlativo = $correlativo + 1; // nuevo correlativo
			
			// actualizamos el stock y damos de baja las series al almacen
			if($fields["deta_controla_stock"][$key] == "S") {
				// verificamos el stock del producto
				$stock = $this->has_stock($this->despacho->get_fields(), NULL, $cantidad);
				if($stock !== TRUE) {
					$this->exception("No existe stock para el producto ".$fields["deta_producto"][$key].". 
						Stock disponible: ".number_format($stock, 2));
					return false;
				}
				
				// retiramos el stock
				$this->detalle_almacen->set($this->detalle_venta->get_fields());
				$this->detalle_almacen->set("idalmacen", $fields["deta_idalmacen"][$key]);
				$this->detalle_almacen->set("cantidad", $cantidad);
				$this->detalle_almacen->set("precio_costo", $this->detalle_venta->get("costo"));
				$this->detalle_almacen->set("precio_venta", $this->detalle_venta->get("precio"));
				$this->detalle_almacen->set("iddespacho", $this->despacho->get("iddespacho"));
				$this->detalle_almacen->insert();
				
				// cantidad segun unidad de medida
				$this->producto_unidad->find(array(
					"idproducto"=>$this->detalle_venta->get("idproducto")
					,"idunidad"=>$this->detalle_venta->get("idunidad")
				));
				
				// quitamos las series del almacen
				if($fields["deta_controla_serie"][$key] == "S") {
					if(empty($fields["deta_series"][$key])) {
						$this->exception("Ingrese las series del producto ".$fields["deta_producto"][$key]);
						return false;
					}
					
					$count_real_serie = intval($this->producto_unidad->get("cantidad_unidad_min")) * $cantidad;
					
					$arr = explode("|", $fields["deta_series"][$key]);
					if(count($arr) != $count_real_serie) {
						$this->exception("Debe ingresar $count_real_serie series para el producto: ".$fields["deta_producto"][$key]);
						return false;
					}
					
					// despachamos las series al almacen
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
						$this->detalle_almacen_serie->set("tabla_salida", "V");
						$this->detalle_almacen_serie->set("idtabla_salida", $idventa);
						$this->detalle_almacen_serie->set("iddespacho", $this->detalle_almacen->get("iddespacho"));
						$this->detalle_almacen_serie->update();
					}
				}
				
				// almacenmos los datos para el ingreso en kardex
				$temp = $this->despacho->get_fields();
				$temp["cantidad"] = $temp["cant_despachado"];
				$temp["preciocosto"] = $this->detalle_venta->get("costo") / $this->producto_unidad->get("cantidad_unidad_min");
				$temp["precioventa"] = $this->detalle_venta->get("precio") / $this->producto_unidad->get("cantidad_unidad_min");
				$arrProductosKardex[] = $temp;
			}
			
			// actualizamos el campo despachado del detalle venta
			if($cantidad >= $pendiente) {
				$this->detalle_venta->set("despachado", "S");
				$this->detalle_venta->update();
			}
		} // fin [foreach]
		
		if( ! empty($arrProductosKardex)) {
			// actualizamos el correlativo del tipo movimiento
			$this->tipo_movi_almacen->set("correlativo", $correlativo);
			$this->tipo_movi_almacen->update();
			
			// registramos el movimiento de kardex
			$this->load_library("jkardex");
			
			$this->jkardex->idtipodocumento = $this->despacho->get("tipo_docu");
			$this->jkardex->serie = $this->despacho->get("serie");
			$this->jkardex->numero = $this->despacho->get("numero");
			$this->jkardex->idtercero = $this->venta->get("idcliente");
			$this->jkardex->idmoneda = $this->venta->get("idmoneda");
			$this->jkardex->tipocambio = $this->venta->get("cambio_moneda");
			$this->jkardex->observacion = $this->despacho->get("observacion");
			
			$this->jkardex->referencia("venta", $idventa, $fields["idsucursal"]);
			$this->jkardex->salida();
			$this->jkardex->push($arrProductosKardex);
			$this->jkardex->run();
		}
		
		// verificamos el estado (despachado) de la venta
		$sql = "SELECT * FROM venta.detalle_venta
			WHERE idventa=? AND estado=? AND despachado=?";
		$query = $this->db->query($sql, array($idventa, "A", "N"));
		if($query->num_rows() <= 0) {
			$this->venta->update(array("idventa"=>$idventa, "despachado"=>"S"));
		}
		
		// finalizamos transaccion
		$this->db->trans_complete();
		
		$this->response($this->venta->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro segun varios parametros
	 */
	public function eliminar($id) {
		$this->load_model("almacen.despacho");
		
		$this->despacho->find($id);
		
		$this->db->trans_start(); // inciamos transaccion
		
		// obtenemos el listado de series del almacen
		$rs_series = $this->db->where("tabla_salida", "V")
			->where("idtabla_salida", $this->despacho->get("idreferencia"))
			->where("iddespacho", $this->despacho->get("iddespacho"))
			->where("despachado", "S")->where("estado !=", "I")
			->get("almacen.detalle_almacen_serie");
		
		// eliminamos el despacho en detalle_almacen
		$this->db->where("tabla", "V")->where("idtabla", $this->despacho->get("idreferencia"))
			->where("iddespacho", $this->despacho->get("iddespacho"))
			->update("almacen.detalle_almacen", array("estado"=>"I"));
			
		// eliminamos el despacho de las series en almacen
		$this->db->where("tabla_salida", "V")->where("idtabla_salida", $this->despacho->get("idreferencia"))
			->where("iddespacho", $this->despacho->get("iddespacho"))->where("despachado", "S")
			->update("almacen.detalle_almacen_serie", array("despachado"=>"N"));
			
		// eliminar del detalle venta las series
		if($rs_series->num_rows() > 0) {
			foreach($rs_series->result() as $row) {
				$this->db->where("idventa", $this->despacho->get("idreferencia"))
					->where("iddetalle_venta", $this->despacho->get("iddetalle_referencia"))
					->where("idproducto", $row->idproducto)
					->where("serie", $row->serie)
					->update("venta.detalle_venta_serie", array("estado"=>"I"));
			}
		}
		
		// eliminar del kardex
		$this->db->where("tabla", "venta")->where("idreferencia", $this->despacho->get("idreferencia"))
			->where("correlativo", $this->despacho->get("correlativo"))
			->update("almacen.kardex", array("estado"=>"I"));
		
		// eliminar la recepcion
		$this->despacho->set("estado", "I");
		$this->despacho->update();
		
		// actualizamos el estado del detalle venta
		$despachado = "N";
		$sql = "select dv.cantidad - coalesce(sum(d.cant_despachado),0) as pendiente
			from venta.detalle_venta dv
			left join almacen.despacho d on d.idreferencia = dv.idventa 
				and d.iddetalle_referencia = dv.iddetalle_venta 
				and d.referencia = 'V' and d.estado <> 'I'
			where dv.iddetalle_venta = ?
			group by dv.iddetalle_venta, dv.cantidad";
		$query = $this->db->query($sql, array($this->despacho->get("iddetalle_referencia")));
		if($query->num_rows() > 0) {
			$despachado =($query->row()->pendiente > 0) ? "N" : "S";
		}
		$this->db->where("iddetalle_venta", $this->despacho->get("iddetalle_referencia"))
			->update("venta.detalle_venta", array("despachado"=>$despachado));
		
		// actualizamos el estado de la venta
		$sql = "SELECT * FROM venta.detalle_venta
			WHERE idventa=? AND estado=? AND despachado=?";
		$query = $this->db->query($sql, array($this->despacho->get("idreferencia"), "A", "N"));
		$despachado = ($query->num_rows() <= 0) ? "S" : "N";
		$this->db->where("idventa", $this->despacho->get("idreferencia"))
			->update("venta.venta", array("despachado"=>$despachado));
		
		$this->db->trans_complete(); // finalizamos transaccion
		
		$this->response($this->despacho->get_fields());
	}
	
	public function autocomplete() {
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		$sql = "SELECT idventa, documento, cliente, idtipodocumento, serie, numero
			FROM almacen.despacho_view
			WHERE estado='A' and despachado='N' 
			and idsucursal=".$this->get_var_session("idsucursal")." 
			and (documento ILIKE ? OR cliente ILIKE ?)
			ORDER BY documento
			LIMIT ?";
		$query = $this->db->query($sql, array($txt, $txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
	
	public function grilla_popup() {
		$this->load_model("almacen.despacho_view");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->despacho_view);
		$this->datatables->setIndexColumn("idventa");
		
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('despachado', '=', 'N');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		
		$this->datatables->setColumns(array('fecha_venta','documento','cliente','cantidad','fecha_despacho'));
		$this->datatables->setPopup(true);
		
		$this->datatables->order_by("fecha_venta", "desc");
		$this->datatables->setCallback("format_fecha");
		
		$table = $this->datatables->createTable(array('Fecha','Venta','Cliente','Item despachados','Ultima despacho'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	public function get_detalle_pendiente($idventa) {
		$sql = "select dv.iddetalle_venta, dv.descripcion as producto, u.descripcion as unidad,
			dv.cantidad, coalesce(sum(d.cant_despachado),0) as cantidad_despachado, 
			dv.cantidad - coalesce(sum(d.cant_despachado),0) as cantidad_pendiente,
			dv.afecta_stock as controla_stock, dv.afecta_serie as controla_serie, 
			pu.cantidad_unidad_min as cantidad_um, dv.idalmacen, dv.idproducto
			,(
				select array_to_string(array_agg(serie), '|'::text)
				from venta.detalle_venta_serie
				where iddetalle_venta = dv.iddetalle_venta 
				and idventa = dv.idventa and estado = 'A'
			) as serie_venta
			,(
				select array_to_string(array_agg(serie), '|'::text)
				from almacen.detalle_almacen_serie 
				where tabla_salida = 'V' and idtabla_salida = dv.idventa
				and despachado = 'S' and estado = 'A'
			) AS serie_almacen
			from venta.detalle_venta dv
			join compra.unidad u on u.idunidad = dv.idunidad
			join compra.producto_unidad pu on pu.idproducto = dv.idproducto and pu.idunidad = dv.idunidad
			left join almacen.despacho d on d.idreferencia = dv.idventa and d.referencia = 'V'
				and d.iddetalle_referencia = dv.iddetalle_venta and d.estado <> 'I'
			where dv.estado = 'A' and dv.despachado = 'N' and dv.idventa = ?
			group by dv.iddetalle_venta, dv.descripcion, u.descripcion, dv.cantidad, dv.afecta_stock, 
				dv.afecta_serie, pu.cantidad_unidad_min, dv.idalmacen, dv.idproducto, dv.idventa
			order by iddetalle_venta";
		$query = $this->db->query($sql, array($idventa));
		
		$rs = $query->result_array();
		if( ! empty($rs)) {
			foreach($rs as $key=>$row) {
				$serie = "";
				if( ! empty($row["serie_venta"])) {
					$arr_venta = explode("|", $row["serie_venta"]);
					$arr_almacen = explode("|", $row["serie_almacen"]);
					$serie = implode("|", array_diff($arr_venta, $arr_almacen));
				}
				$rs[$key]["serie"] = $serie;
			}
		}
		
		$this->response($rs);
	}
}
?>