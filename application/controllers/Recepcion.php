<?php

include_once "Controller.php";

class Recepcion extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Recepciones de Ordenes de Compras");
		$this->set_subtitle("Lista de Recepcion");
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
	
	public function filtros_grilla($recepcionado) {
		$this->load_library("combobox");
		
		$html = '<div class="row">';
		
		// div y combobox 
		$this->combobox->setAttr("filter", "recepcionado");
		$this->combobox->setAttr("class", "form-control");
		$this->combobox->addItem("N", "PENDIENTE");
		$this->combobox->addItem("S", "RECEPCIONADO");
		$this->combobox->setSelectedOption($recepcionado);
		
		$html .= '<div class="col-sm-4"><div class="form-group">';
		$html .= '<label class="control-label">Recepcionado</label>';
		$html .= $this->combobox->getObject();;
		$html .= '</div></div>';
		
		$html .= '</div>';
		
		$this->set_filter($html);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model("almacen.recepcion_view");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->recepcion_view);
		$this->datatables->setIndexColumn("idcompra");
		
		$recepcionado = "N";
		
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('recepcionado', '=', $recepcionado);
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
	
		$this->datatables->setColumns(array('fecha_compra','documento','proveedor','cantidad','fecha_recepcion'));
		$this->datatables->order_by("fecha_compra", "desc");
		$this->datatables->setCallback("format_fecha");
		
		$columnasName = array('Fecha','Compra','Proveedor','Item recepcionados','Ultima recepcion');
		
		$table = $this->datatables->createTable($columnasName);
		$script = "<script>".$this->datatables->createScript()."</script>";
		
		// $this->css('plugins/dataTables/dataTables.bootstrap');
		// $this->css('plugins/dataTables/dataTables.responsive');
		// $this->css('plugins/dataTables/dataTables.tableTools.min');
		
		// $this->js('plugins/dataTables/jquery.dataTables');
		// $this->js('plugins/dataTables/dataTables.bootstrap');
		// $this->js('plugins/dataTables/dataTables.responsive');
		// $this->js('plugins/dataTables/dataTables.tableTools.min');
		
		$this->js($script, false);
		
		$this->filtros_grilla($recepcionado);
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Recepcion");
		$this->set_subtitle("");
		
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model("compra.compra_view");
		$data["compra"] = $this->compra_view->find(array("idcompra"=>$id));
		
		$sql = "select r.idrecepcion, p.descripcion_detallada as producto, a.descripcion as almacen,
			t.abreviatura as tipodocumento, r.serie, r.numero, r.observacion, 
			to_char(r.fecha,'DD/MM/YYYY') as fecha, to_char(r.hora::interval, 'HH12:MI am') as hora,
			u.descripcion as unidad, r.cant_recepcionada as cantidad, e.nombres as usuario, 
			array_to_string(array_agg(das.serie), '|'::text) AS series
			from almacen.recepcion r
			join compra.producto p on p.idproducto = r.idproducto
			join almacen.almacen a on a.idalmacen = r.idalmacen
			join venta.tipo_documento t on t.idtipodocumento = r.tipo_docu::integer
			join compra.unidad u on u.idunidad = r.idunidad
			join seguridad.usuario e on e.idusuario = r.idusuario
			left join almacen.detalle_almacen_serie das on das.idrecepcion = r.idrecepcion and das.estado='A'
				and das.tabla_ingreso = 'C' and das.idtabla_ingreso = r.idcompra
			where r.estado <> 'I' and r.referencia = 'C' and r.idcompra = ?
			group by r.idrecepcion, p.descripcion_detallada, a.descripcion, t.abreviatura, r.serie, r.numero,
			r.observacion, r.fecha, r.hora, u.descripcion, r.cant_recepcionada, e.nombres
			order by r.idrecepcion desc, r.fecha desc";
		$query = $this->db->query($sql, array($id));
		$data["detalle"] = $query->result_array();
		
		$this->set_title("Lista de recepciones");
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
		$idcompra = $fields["idcompra"];
		
		// modelos
		$this->load_model($this->controller);
		$this->load_model("compra");
		$this->load_model("detalle_compra");
		$this->load_model("detalle_compra_serie");
		$this->load_model("compra.producto_unidad");
		$this->load_model("detalle_almacen");
		$this->load_model("detalle_almacen_serie");
		$this->load_model("recepcion");
		$this->load_model("tipo_movi_almacen");
		
		$this->compra->find(array("idcompra"=>$idcompra, "idsucursal"=>$fields["idsucursal"]));
		
		$this->detalle_almacen->set("tipo", "E");
		$this->detalle_almacen->set("tipo_number", 1);
		$this->detalle_almacen->set("fecha", date("Y-m-d"));
		$this->detalle_almacen->set("tabla", "C");
		$this->detalle_almacen->set("idtabla", $idcompra);
		$this->detalle_almacen->set("estado", "A");
		$this->detalle_almacen->set("idsucursal", $this->compra->get("idsucursal"));
		
		$this->detalle_almacen_serie->set("fecha_ingreso", date("Y-m-d"));
		$this->detalle_almacen_serie->set("tabla_ingreso", "C");
		$this->detalle_almacen_serie->set("idtabla_ingreso", $idcompra);
		$this->detalle_almacen_serie->set("despachado", "N");
		$this->detalle_almacen_serie->set("estado", "A");
		$this->detalle_almacen_serie->set("idsucursal", $this->compra->get("idsucursal"));
		
		$this->recepcion->set("tipo_docu", $fields["idtipodocumento"]);
		$this->recepcion->set("serie", $fields["serie"]);
		$this->recepcion->set("numero", $fields["numero"]);
		$this->recepcion->set("observacion", $fields["observacion"]);
		$this->recepcion->set("fecha", date("Y-m-d"));
		$this->recepcion->set("hora", date("H:i:s"));
		$this->recepcion->set("idusuario", $fields["idusuario"]);
		$this->recepcion->set("referencia", "C");
		
		$this->tipo_movi_almacen->find($this->get_idtipo_movimiento("compra"));
		$correlativo = intval($this->tipo_movi_almacen->get("correlativo"));
		
		$tipo_movimiento = $this->get_idtipo_movimiento("compra");
		$this->db->trans_start(); // inciamos transaccion
		$arrProductosKardex = array();
		
		// recorremos lista de producto
		foreach($fields["deta_iddetalle"] as $key=>$val) {
			$cantidad = floatval($fields["deta_ingreso"][$key]);
			$pendiente = floatval($fields["deta_pendiente"][$key]);
			
			if($cantidad > $pendiente) {
				$this->exception("La cantidad a ingresar es mayor a la cantidad pendiente 
					de recepcion para el producto ".$fields["deta_producto"][$key]);
				return false;
			}
			
			$this->detalle_compra->find(array("iddetalle_compra"=>$val, "idcompra"=>$idcompra));
			
			// insertamos las series del detalle compra
			if($fields["deta_controla_serie"][$key] == "S") {
				if( ! empty($fields["deta_series"][$key])) {
					$this->detalle_compra_serie->set($this->detalle_compra->get_fields());
					$arr = explode("|", $fields["deta_series"][$key]);
					foreach($arr as $serie) {
						$this->detalle_compra_serie->set("serie", $serie);
						$this->detalle_compra_serie->save(null, false);
					}
				}
			}
			
			// hacemos la recepcion de todos modos
			$this->recepcion->set($this->detalle_compra->get_fields());
			$this->recepcion->set("idalmacen", $fields["deta_idalmacen"][$key]);
			$this->recepcion->set("cant_recepcionada", $cantidad);
			$this->recepcion->set("correlativo", $correlativo);
			$this->recepcion->set("estado", "C");
			$this->recepcion->insert();
			$correlativo = $correlativo + 1; // nuevo correlativo
			
			// si recepcionamos la compra directamente, ingresamos el stock y las series al almacen
			if($fields["deta_controla_stock"][$key] == "S") {
				// ingresamos el stock
				$this->detalle_almacen->set($this->detalle_compra->get_fields());
				$this->detalle_almacen->set("idalmacen", $fields["deta_idalmacen"][$key]);
				$this->detalle_almacen->set("cantidad", $cantidad);
				$this->detalle_almacen->set("precio_costo", $this->detalle_compra->get("costo"));
				$this->detalle_almacen->set("idrecepcion", $this->recepcion->get("idrecepcion"));
				$this->detalle_almacen->insert();
				
				$this->producto_unidad->find(array(
					"idproducto"=>$this->detalle_compra->get("idproducto")
					,"idunidad"=>$this->detalle_compra->get("idunidad")
				));
				
				// verificamos el ingreso de las series
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
					
					// ingresamos las series al almacen
					$this->detalle_almacen_serie->set($this->detalle_almacen->get_fields());
					foreach($arr as $serie) {
						if( $this->detalle_almacen_serie->exists(array("serie"=>$serie, "despachado"=>"N", "estado"=>"A")) ) {
							$this->exception("La serie $serie del producto ".$fields["deta_producto"][$key]." ya existe.");
							return false;
						}
						$this->detalle_almacen_serie->set("serie", $serie);
						$this->detalle_almacen_serie->insert(null, false);
					}
				}
				
				// almacenmos los datos para el ingreso en kardex
				$temp = $this->recepcion->get_fields();
				$temp["cantidad"] = $temp["cant_recepcionada"];
				$temp["preciocosto"] = $this->detalle_compra->get("costo") / $this->producto_unidad->get("cantidad_unidad_min");
				$arrProductosKardex[] = $temp;
			}
			
			// actualizamos el campo recepcionado del detalle compra
			if($cantidad >= $pendiente) {
				$this->detalle_compra->set("recepcionado", "S");
				$this->detalle_compra->update();
			}
		} // fin [foreach]
		
		if( ! empty($arrProductosKardex)) {
			// actualizamos el correlativo del tipo movimiento
			$this->tipo_movi_almacen->set("correlativo", $correlativo);
			$this->tipo_movi_almacen->update();
			
			// registramos el movimiento de kardex
			$this->load_library("jkardex");
			
			$this->jkardex->idtipodocumento = $this->recepcion->get("tipo_docu");
			$this->jkardex->serie = $this->recepcion->get("serie");
			$this->jkardex->numero = $this->recepcion->get("numero");
			$this->jkardex->idtercero = $this->compra->get("idproveedor");
			$this->jkardex->idmoneda = $this->compra->get("idmoneda");
			$this->jkardex->tipocambio = $this->compra->get("cambio_moneda");
			$this->jkardex->observacion = $this->recepcion->get("observacion");
			// $this->jkardex->tipo_movimiento = $tipo_movimiento;
			
			$this->jkardex->referencia("compra", $idcompra, $fields["idsucursal"]);
			$this->jkardex->entrada();
			$this->jkardex->push($arrProductosKardex);
			$this->jkardex->run();
			
			
		}
		
		// verificamos el estado (recepcionado) de la compra
		$sql = "SELECT * FROM compra.detalle_compra
			WHERE idcompra=? AND estado=? AND recepcionado=?";
		$query = $this->db->query($sql, array($idcompra, "A", "N"));
		if($query->num_rows() <= 0) {
			$this->compra->update(array("idcompra"=>$idcompra, "recepcionado"=>"S"));
		}
		
		// finalizamos transaccion
		$this->db->trans_complete();
		
		$this->response($this->compra->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro segun varios parametros
	 */
	public function eliminar($id) {
		$this->load_model("almacen.recepcion");
		
		$this->recepcion->find($id);
		
		$this->db->trans_start(); // inciamos transaccion
		
		// obtenemos el listado de series del almacen
		$rs_series = $this->db->where("tabla_ingreso", "C")
			->where("idtabla_ingreso", $this->recepcion->get("idcompra"))
			->where("idrecepcion", $this->recepcion->get("idrecepcion"))
			->where("estado !=", "I")
			->get("almacen.detalle_almacen_serie");
		
		// eliminamos el ingreso en detalle_almacen
		$this->db->where("tabla", "C")->where("idtabla", $this->recepcion->get("idcompra"))
			->where("idrecepcion", $this->recepcion->get("idrecepcion"))
			->update("almacen.detalle_almacen", array("estado"=>"I"));
			
		// eliminamos el ingreso de las series en almacen
		$this->db->where("tabla_ingreso", "C")->where("idtabla_ingreso", $this->recepcion->get("idcompra"))
			->where("idrecepcion", $this->recepcion->get("idrecepcion"))
			->update("almacen.detalle_almacen_serie", array("estado"=>"I"));
			
		// eliminar del detalle_compra_serie las series
		if($rs_series->num_rows() > 0) {
			foreach($rs_series->result() as $row) {
				$this->db->where("idcompra", $this->recepcion->get("idcompra"))
					->where("iddetalle_compra", $this->recepcion->get("iddetalle_compra"))
					->where("idproducto", $row->idproducto)
					->where("serie", $row->serie)
					->update("compra.detalle_compra_serie", array("estado"=>"I"));
			}
		}
		
		// eliminar del kardex
		$this->db->where("tabla", "compra")->where("idreferencia", $this->recepcion->get("idcompra"))
			->where("correlativo", $this->recepcion->get("correlativo"))
			->update("almacen.kardex", array("estado"=>"I"));
		
		// eliminar la recepcion
		$this->recepcion->set("estado", "I");
		$this->recepcion->update();
		
		// actualizamos el estado del detalle_compra
		$recepcionado = "N";
		$sql = "select dc.cantidad - coalesce(sum(r.cant_recepcionada),0) as pendiente
			from compra.detalle_compra dc
			left join almacen.recepcion r on r.idcompra = dc.idcompra and r.referencia = 'C' 
				and r.iddetalle_compra = dc.iddetalle_compra and r.estado <> 'I'
			where dc.iddetalle_compra = ?
			group by dc.iddetalle_compra, dc.cantidad";
		$query = $this->db->query($sql, array($this->recepcion->get("iddetalle_compra")));
		if($query->num_rows() > 0) {
			$recepcionado =($query->row()->pendiente > 0) ? "N" : "S";
		}
		$this->db->where("iddetalle_compra", $this->recepcion->get("iddetalle_compra"))
			->update("compra.detalle_compra", array("recepcionado"=>$recepcionado));
		
		// actualizamos el estado de la compra
		$sql = "SELECT * FROM compra.detalle_compra
			WHERE idcompra=? AND estado=? AND recepcionado=?";
		$query = $this->db->query($sql, array($this->recepcion->get("idcompra"), "A", "N"));
		$recepcionado = ($query->num_rows() <= 0) ? "S" : "N";
		$this->db->where("idcompra", $this->recepcion->get("idcompra"))
			->update("compra.compra", array("recepcionado"=>$recepcionado));
		
		$this->db->trans_complete(); // finalizamos transaccion
		
		$this->response($this->recepcion->get_fields());
	}
	
	public function autocomplete() {
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		$sql = "SELECT idcompra, documento, proveedor, idtipodocumento, serie, numero
			FROM almacen.recepcion_view
			WHERE estado='A' and recepcionado='N' 
			and idsucursal=".$this->get_var_session("idsucursal")." 
			and (documento ILIKE ? OR proveedor ILIKE ?)
			ORDER BY documento
			LIMIT ?";
		$query = $this->db->query($sql, array($txt, $txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
	
	public function grilla_popup() {
		$this->load_model("almacen.recepcion_view");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->recepcion_view);
		$this->datatables->setIndexColumn("idcompra");
		
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('recepcionado', '=', 'N');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		
		$this->datatables->setColumns(array('fecha_compra','documento','proveedor','cantidad','fecha_recepcion'));
		$this->datatables->setPopup(true);
		
		$this->datatables->order_by("fecha_compra", "desc");
		$this->datatables->setCallback("format_fecha");
		
		$table = $this->datatables->createTable(array('Fecha','Compra','Proveedor','Item recepcionados','Ultima recepcion'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	public function get_detalle_pendiente($idcompra) {
		$sql = "select dc.iddetalle_compra, p.descripcion_detallada as producto, u.descripcion as unidad,
			dc.cantidad, coalesce(sum(r.cant_recepcionada),0) as cantidad_recepcionada, 
			dc.cantidad - coalesce(sum(r.cant_recepcionada),0) as cantidad_pendiente,
			dc.afecta_stock as controla_stock, dc.afecta_serie as controla_serie, 
			pu.cantidad_unidad_min as cantidad_um, dc.idalmacen
			from compra.detalle_compra dc
			join compra.producto p on p.idproducto = dc.idproducto
			join compra.unidad u on u.idunidad = dc.idunidad
			join compra.producto_unidad pu on pu.idproducto = dc.idproducto and pu.idunidad = dc.idunidad
			left join almacen.recepcion r on r.idcompra = dc.idcompra and r.referencia = 'C' 
				and r.iddetalle_compra = dc.iddetalle_compra and r.estado <> 'I'
			where dc.estado = 'A' and dc.recepcionado = 'N' and dc.idcompra = ?
			group by dc.iddetalle_compra, p.descripcion_detallada, u.descripcion, dc.cantidad, dc.afecta_stock, 
				dc.afecta_serie, pu.cantidad_unidad_min, dc.idalmacen
			order by iddetalle_compra";
		$query = $this->db->query($sql, array($idcompra));
		$this->response($query->result_array());
	}
}
?>