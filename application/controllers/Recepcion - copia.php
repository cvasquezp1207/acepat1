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
		// $this->css("plugins/datapicker/datepicker3");
		// $this->css('plugins/iCheck/custom');
		$this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		// $this->js("plugins/datapicker/bootstrap-datepicker");
		// $this->js("plugins/datapicker/bootstrap-datepicker.es");
		// $this->js('plugins/iCheck/icheck.min');
		$this->js('form/'.$this->controller.'/form');
				
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	
	
	public function form_view_recepcion($data = null,$id) {
		//$id es idcompra
		if(!is_array($data)) {
			$data = array();
		}
		
		$this->load_model("recepcion");		
		$query = $this->db
				->select("r.correlativo, r.idcompra, r.estado, r.idalmacen, a.descripcion almacen,r.cant_recepcionada cantidad, r.tipo_docu, t.descripcion tipo,r.serie, r.numero, r.idproducto, p.descripcion producto,r.fecha  fecha_registro")
				->where("r.idcompra", $id)
				->where("r.estado !=", "I")
				->join("compra.producto p", "p.idproducto=r.idproducto")
			    ->join("almacen.almacen a", "a.idalmacen=r.idalmacen")
			    ->join("venta.tipo_documento t", "t.idtipodocumento=r.tipo_docu")
				->order_by("p.idproducto", "asc")->get("almacen.recepcion r");
				
				
		$data["kardex_det"] = $query->result_array();
		
		
		// $this->load_model("kardex");		
		// $query = $this->db
				// ->select("k.correlativo, k.idcompra, k.idalmacen, a.descripcion almacen,k.cantidad, k.tipo_docu, t.descripcion tipo,k.serie, k.numero, k.idproducto, p.descripcion producto,k.fecha_registro")
				// ->where("k.idcompra", $id)
				// ->where("k.estado", "A")
				// ->join("compra.producto p", "p.idproducto=k.idproducto")
			    // ->join("almacen.almacen a", "a.idalmacen=k.idalmacen")
			    // ->join("venta.tipo_documento t", "t.idtipodocumento=k.tipo_docu")
				// ->order_by("p.idproducto", "asc")->get("almacen.kardex k");
		// $data["kardex_det"] = $query->result_array();
		
		
		
		$data["idcompra"] = $id;		
		$data["controller"] = $this->controller;
		
		$this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
		$this->css("plugins/datapicker/datepicker3");
		// $this->css('plugins/iCheck/custom');
		$this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		// $this->js('plugins/iCheck/icheck.min');
		$this->js('form/'.$this->controller.'/form');
		$this->js('form/producto/modal');
						
		$this->js('form/'.$this->controller.'/recepcion_view');
	
		return $this->load->view($this->controller."/form_recepcion_view", $data, true);
	}
	
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model("compra.recepcion_view");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->recepcion_view);
		$this->datatables->setIndexColumn("idcompra");
		
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('recepcionado', '=', 'N');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
	
		$this->datatables->setColumns(array('fecha_compra','documento','proveedor','cantidad','fecha_recepcion'));
		$this->datatables->order_by("fecha_compra", "desc");
		$this->datatables->setCallback("format_fecha");
		
		$columnasName = array('Fecha','Compra','Proveedor','Item recepcionados','Ultima recepcion');
		
		$table = $this->datatables->createTable($columnasName);
		$script = "<script>".$this->datatables->createScript()."</script>";
		
		$this->css('plugins/dataTables/dataTables.bootstrap');
		$this->css('plugins/dataTables/dataTables.responsive');
		$this->css('plugins/dataTables/dataTables.tableTools.min');
		
		$this->js('plugins/dataTables/jquery.dataTables');
		$this->js('plugins/dataTables/dataTables.bootstrap');
		$this->js('plugins/dataTables/dataTables.responsive');
		$this->js('plugins/dataTables/dataTables.tableTools.min');
		$this->js($script, false);
		
		// $row = $this->get_permisos();
		// if($row->nuevo == 1 || $row->editar == 1 || $row->eliminar == 1) {
			// $this->add_button("btn_ver", "Ver Recepcion", "thumbs-up","");
		// }
		
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
	// public function editar($id) {
		// $this->load_model($this->controller);
		// $data = $this->perfil->find($id);
		
		// $this->set_title("Modificar Compra");
		// $this->set_subtitle("");
		// $this->set_content($this->form($data));
		// $this->index("content");
	// }
	
	
	public function view_all_recepcion($id) {
		$this->load_model("compra");
		$data["compra"] = $this->compra->find($id);
		
		$sql = "SELECT compra.fecha_compra, proveedor.nombre
				FROM compra.compra, compra.proveedor 
				WHERE compra.idproveedor=proveedor.idproveedor and compra.idcompra=$id";
		$query = $this->db->query($sql);
		$query = $query->result_array();
								
		$fecha_compra = $query[0]['fecha_compra'];
		$nombre = $query[0]['nombre'];
		
		$this->set_title("Lista de Recepciones de la compra Nro $id/ ");
		$this->set_subtitle("FECHA: ".$fecha_compra." PROVEDOR: ".$nombre);

		$this->set_content($this->form_view_recepcion($data,$id));
		$this->index("content");
	} 
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		print_r($this->input->post());return true;
		$this->load_model($this->controller);
		
		$fields['idrecepcion'] = $this->input->post("idrecepcion");
		$fields['idcompra'] = $this->input->post("idcompra");
		$fields['observacion'] = $this->input->post("observacion");
		$fields['tipo_docu'] = $this->input->post("idtipodocumento");
		$fields['serie'] = $this->input->post("serie");
		$fields['numero'] = $this->input->post("numero");
		$ls_producto = $this->input->post("ls_producto");
		$fields['idusuario'] = $this->get_var_session("idusuario");
		$fields['fecha'] = date("Y-m-d");
		$fields['hora'] = date("H:i:s");
		$fields['estado'] = "C";
		
		//sacamos el idproveedor de la tabla compra
		$sql0 = "SELECT compra.*,moneda.abreviatura 
				FROM compra.compra
				LEFT JOIN general.moneda ON compra.idmoneda = moneda.idmoneda
				WHERE idcompra=".$fields['idcompra'];
				
		$query0 = $this->db->query($sql0);
		$query0 = $query0->result_array();
		$idtercero = $query0[0]['idproveedor'];//tipo movimiento como ingresra al karde 1 -> compras
		$moneda = $query0[0]['abreviatura'];//moneda de compra
		$tipo_cambio = $query0[0]['tipo_cambio'];//tipo_cambio de compra
		if(empty($tipo_cambio)) {$tipo_cambio = 1;}
		
		$this->db->trans_start(); // inciamos transaccion
		$lsFila = explode('*', $ls_producto);
		
		$tipo_movimiento = 1;
		for($i=0;$i<count($lsFila);$i++){
			$lsCol = explode('-', $lsFila[$i]);
			$fields['idproducto'] = $lsCol[0];
			$fields['idalmacen']= $lsCol[1];
			$fields['cant_recepcionada']= $lsCol[2];
			
			//Preparamos para hacer el ingreso de la recepcion al kardex
			// tipo_movimiento=1 es para  compras
			$sql = "SELECT* FROM almacen.tipo_movimiento WHERE tipo_movimiento=$tipo_movimiento";
			$query = $this->db->query($sql);
			$query = $query->result_array();
								
			$tip_movimiento = $query[0]['tipo_movimiento'];//tipo movimiento como ingresra al karde 1 -> compras
			$correlativo = $query[0]['correlativo']; // correlativo, este numero se vera refleado en el kardex
			$fields['correlativo'] = $correlativo;
			
			if(empty($fields["idrecepcion"])) {
				
				// echo "<pre>";
				// print_r($this->recepcion->get_fields());	
				$this->recepcion->insert($fields);

				//armamos el array para ingresar a kardex
				$sql2 = "SELECT* FROM compra.detalle_compra WHERE idcompra=".$fields['idcompra']." AND idproducto=".$fields['idproducto'];
				$query2 = $this->db->query($sql2);
				// $query2 = $query2->result_array();
				// echo "<pre>";
				// print_r($query2);
				
				foreach ($query2->result() as $row){				
					$precio = $row->precio;
					$igv = $row->igv;
					$flete = $row->flete;
					$gastos = $row->gastos;
					
					if($moneda=='USD'){
						$costo_unit_d = $precio+$igv+$flete+$gastos;
						$costo_unit_s = $costo_unit_d*$tipo_cambio;		
						
						$costo_unit_d = number_format($costo_unit_d,4, '.', '');				
						$costo_unit_s = number_format($costo_unit_s,4, '.', '');				
												
					}else if($moneda=='PEN'){
						$costo_unit_s = $precio+$igv+$flete+$gastos;
						$costo_unit_d = number_format($costo_unit_s/$tipo_cambio,4, '.', '');
						
						$costo_unit_d = number_format($costo_unit_d,4, '.', '');				
						$costo_unit_s = number_format($costo_unit_s,4, '.', '');
					}
					
					$importe_s = number_format($costo_unit_s*$fields['cant_recepcionada'],4, '.', '');
					$importe_d = number_format($costo_unit_d*$fields['cant_recepcionada'],4, '.', '');
					
					//falta crear a ecuacion
					$precio_unit_venta_s = number_format(0,4, '.', '');
					$precio_unit_venta_d = number_format(0,4, '.', '');
					
					
					// registramos movimiento kardex
					$this->load_kardex("kardex","libreria_kardex");

					$this->libreria_kardex->correlativo = $fields['correlativo'];
					$this->libreria_kardex->ingreso(true);
					$this->libreria_kardex->idproducto = $fields['idproducto'];			
					$this->libreria_kardex->idalmacen = $fields['idalmacen'];
					$this->libreria_kardex->idunidad = $row->idunidad;
					$this->libreria_kardex->cantidad = number_format($fields['cant_recepcionada'],2, '.', '');
					$this->libreria_kardex->precio_unit_venta_s = $precio_unit_venta_s;
					$this->libreria_kardex->precio_unit_venta_d = $precio_unit_venta_d;
					$this->libreria_kardex->costo_unit_s = $costo_unit_s;
					$this->libreria_kardex->costo_unit_d = $costo_unit_d;				
					$this->libreria_kardex->importe_s = $importe_s;
					$this->libreria_kardex->importe_d = $importe_d;
					$this->libreria_kardex->idreferencia = $row->idcompra;
					$this->libreria_kardex->idtercero = $idtercero;
					$this->libreria_kardex->tipo_docu = $fields['tipo_docu'];
					$this->libreria_kardex->serie = $fields['serie'];
					$this->libreria_kardex->numero = $fields['numero'];
					$this->libreria_kardex->observacion = $fields['observacion'];
					$this->libreria_kardex->estado = "C";
					$this->libreria_kardex->referencia('RECEP');					
					$this->libreria_kardex->run();
					
					/*
					$fields2['correlativo'] = $correlativo;
					$fields2['tipo_movimiento'] = $tip_movimiento;
					$fields2['fecha_emision'] = date("Y-m-d");
					$fields2['idproducto'] = $row->idproducto;
					$fields2['idalmacen'] = $fields['idalmacen'];
					$fields2['idunidad'] =  $row->idunidad;
					$fields2['cantidad'] = $fields['cant_recepcionada'];
					$fields2['precio_unit_venta_s'] = 0.00;
					$fields2['precio_unit_venta_d'] = 0.00;
					$fields2['costo_unit_s'] = $costo_unit_s;
					$fields2['costo_unit_d'] = $costo_unit_d;
					$fields2['importe_s'] = $importe_s;
					$fields2['importe_d'] = $importe_d;
					$fields2['idcompra'] = $row->idcompra;
					$fields2['idtercero'] = $idtercero;					
					$fields2['tipo_docu'] = $fields['tipo_docu'];
					$fields2['serie'] = $fields['serie'];
					$fields2['numero'] = $fields['numero'];
					$fields2['observacion'] = $fields['observacion'];
					$fields2['estado'] = "A";
					$fields2['annio'] = date("Y");
					$fields2['periodo'] = date("m");
					$fields2['idusuario'] = $fields['idusuario'];
					$fields2['fecha_registro'] = date("Y-m-d");
					$fields2['hora'] = date("H:i:s");
					$fields2['tabla'] = "RECE";
					
					$this->load_model("kardex");
					$idkardex = $this->kardex->insert($fields2, false);*/
					
					/*
					
					$fields3['idalmacen'] =	$fields['idalmacen'];
					$fields3['tipo'] = "E";
					$fields3['idproducto'] = $row->idproducto;
					$fields3['idunidad'] = $row->idunidad;
					$fields3['cantidad'] = $fields['cant_recepcionada'];
					$fields3['tipo_number'] = 1;
					$fields3['precio_costo'] = $costo_unit_s;
					$fields3['precio_venta'] = 0.00;
					$fields3['fecha'] =	date("Y-m-d");
					$fields3['tabla'] =	"R";//tabla recepcion
					$fields3['idtabla'] = 36;
					$fields3['estado'] = "A";
					
					//insertamos en la tabla almacen.detalle_almacen
					$this->load_model("detalle_almacen");
					$detalle_almacen = $this->detalle_almacen->insert($fields3);
										
					//actualizamos el correlatvo de de la tabla Tipo de movimiento
					$data = array(
					   'correlativo' => $correlativo+1
					);
					$this->db->where('tipo_movimiento', $tipo_movimiento);
					$this->db->update('almacen.tipo_movimiento', $data);*/
				}
			}else {
				$this->compra->update($fields);
			}
		}
		
		$this->db->trans_complete(); // finalizamos transaccion
		
		if ($this->db->trans_status() === FALSE) {
			// generate an error... or use the log_message() function to log your error
		}
		
		$this->response($fields);
	}
	
	/**
	 * Metodo para eliminar un registro segun varios parametros
	 */
	 
	public function eliminar($id) {
		
		// cambiamos de estado a todas las compras que ingresaron a la tabla  kardex---Estado=Inactivo (I)
		// $this->load_model("kardex");		
		// $fields['idcompra'] = $id;
		// $fields['estado'] = "I";	
		// $this->kardex->update($fields);

		$this->db->trans_start(); // inciamos transaccion
		$sql = "UPDATE almacen.kardex SET estado='I'
				WHERE idcompra=$id";				
		$query = $this->db->query($sql);
			
					
		$sql1 = "UPDATE almacen.recepcion SET estado='I'
				WHERE idcompra=$id";			
		$query1 = $this->db->query($sql1);
		
		$this->db->trans_complete(); // finalizamos transaccion		
		if ($this->db->trans_status() === FALSE) {
			// generate an error... or use the log_message() function to log your error
		}
		$this->response($query);	
	}
	
	
	public function eliminar_param() {
		$this->load_model($this->controller);
		
		// cambiamos de estado
		$this->load_model("kardex");		
		$fields['correlativo'] = $this->input->post("kardex");
		$fields['tipo_movimiento'] = 1;
		$fields['idproducto'] = $this->input->post("produc");
		$fields['idalmacen'] = $this->input->post("alma");
		$fields['tipo_docu'] = $this->input->post("tipo_docu");
		$fields['serie'] = $this->input->post("serie");
		$fields['numero'] = $this->input->post("numero");
		$fields['idcompra'] = $this->input->post("idcompra");
		$fields['estado'] = "I";


		$this->db->trans_start(); // inciamos transaccion
		$this->kardex->update($fields);
		
		
		// $this->load_model("recepcion");		
		// $fields2['idcompra'] = $this->input->post("idcompra");
		// $fields2['idproducto'] = $this->input->post("produc");
		// $fields2['idalmacen'] = $this->input->post("alma");
		// $fields2['tipo_docu'] = $this->input->post("tipo_docu");
		// $fields2['serie'] = $this->input->post("serie");
		// $fields2['numeros'] = $this->input->post("numero");
		// $fields2['estado'] = "I";		
		// $fields2['correlativo'] = $this->input->post("kardex");
		// $this->recepcion->update($fields2);
		
		$sql = "UPDATE almacen.recepcion SET estado='I'
				WHERE idcompra=".$this->input->post('idcompra')." AND 
					  idproducto=".$this->input->post('produc')." AND 
					  idalmacen=".$this->input->post('alma')." AND 
					  tipo_docu='".$this->input->post('tipo_docu')."' AND 
					  serie='".$this->input->post('serie')."' AND 
					  numero='".$this->input->post('numero')."' AND
					  correlativo=".$this->input->post('kardex');
			
		$query = $this->db->query($sql);
		// $cons = $query->result_array();
		
		$this->db->trans_complete(); // finalizamos transaccion
		
		if ($this->db->trans_status() === FALSE) {
			// generate an error... or use the log_message() function to log your error
		}
		$this->response($fields);	
	}
	
	public function lista_almacen() {
		
		$this->db->select('idalmacen, descripcion');
		$query = $this->db->where("estado", "A")->get(" almacen.almacen");		
		$this->response($query->result_array());	
	}
	
	
	// public function can_recepcionada($idcompra, $idproducto, $return=false) {
	public function can_recepcionada() {
		$idcompra = $this->input->post("idcompra");
		$idproducto = $this->input->post("idproducto");
		
		$sql = "SELECT coalesce(sum(cant_recepcionada),0) AS recep
				FROM almacen.recepcion
				WHERE idcompra=$idcompra AND idproducto= $idproducto AND estado<>'I'";
		// echo $sql;
		$query = $this->db->query($sql);
		$query2 = $query->result_array();
		
		$this->response($query2);
	}
	
	public function autocomplete() {
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		$sql = "SELECT idcompra, documento, proveedor, idtipodocumento, serie, numero
			FROM compra.recepcion_view
			WHERE estado='A' and recepcionado='N' 
			and idsucursal=".$this->get_var_session("idsucursal")." 
			and (documento ILIKE ? OR proveedor ILIKE ?)
			ORDER BY documento
			LIMIT ?";
		$query = $this->db->query($sql, array($txt, $txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
	
	public function grilla_popup() {
		$this->load_model("compra.recepcion_view");
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
			pu.cantidad_unidad_min as cantidad_um
			from compra.detalle_compra dc
			join compra.producto p on p.idproducto = dc.idproducto
			join compra.unidad u on u.idunidad = dc.idunidad
			join compra.producto_unidad pu on pu.idproducto = dc.idproducto and pu.idunidad = dc.idunidad
			left join almacen.recepcion r on r.idcompra = dc.idcompra 
				and r.iddetalle_compra = dc.iddetalle_compra and r.estado = 'A'
			where dc.estado = 'A' and dc.recepcionado = 'N' and dc.idcompra = ?
			group by dc.iddetalle_compra, p.descripcion_detallada, u.descripcion, dc.cantidad, dc.afecta_stock, 
				dc.afecta_serie, pu.cantidad_unidad_min";
		$query = $this->db->query($sql, array($idcompra));
		$this->response($query->result_array());
	}
}
?>