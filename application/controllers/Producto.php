<?php

include_once "Controller.php";

class Producto extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		//$this->set_title("Mantenimiento de Productos");
		// $this->set_subtitle("Lista de Productos");
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null, $prefix = "", $modal=false) {
		if(!is_array($data)) {
			$data = array();
		}
		
		// creamos los combos
		// $this->load->library('combobox','','combobox');
		$this->load_library('combobox');
		
		// combo unidad
		$this->combobox->setAttr("id",$prefix."idunidad");
		$this->combobox->setAttr("name","idunidad");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		
		$this->db->select('idunidad,descripcion')->order_by("descripcion", "asc");
		$query = $this->db->where("estado","A")->get("compra.unidad");
		// $unidades_medidas = $query->result_array();
		
		$this->combobox->addItem("");
		// $this->combobox->addItem($unidades_medidas);
		$this->combobox->addItem($query->result_array());
		$data['unidades'] = $this->combobox->getAllItems();
		if( isset($data["producto"]["idunidad"]) ) {
			$this->combobox->setSelectedOption($data["producto"]["idunidad"]);
		}
		$data['unidad'] = $this->combobox->getObject();
		// $data["unidades"] = $unidades_medidas;
		
		// reseteamos el combo, para hacer un nuevo combo
		// $this->combobox->init();
		
		// combo unidad
		// $this->combobox->setAttr("id",$prefix."idmarca");
		// $this->combobox->setAttr("name","idmarca");
		// $this->combobox->setAttr("class","form-control");
		// $this->combobox->setAttr("required","");
		// $this->db->select('idmarca,descripcion')->order_by("descripcion", "asc");
		// $query = $this->db->where("estado","A")->get("general.marca");
		// $this->combobox->addItem("");
		// $this->combobox->addItem($query->result_array());
		// if( isset($data["producto"]["idmarca"]) ) {
			// $this->combobox->setSelectedOption($data["producto"]["idmarca"]);
		// }
		// $data['marca'] = $this->combobox->getObject();
		
		// eliminamos los items anteriores, para hacer un nuevo combo
		// $this->combobox->init();
		
		// combo unidad
		// $this->combobox->setAttr("id",$prefix."idmodelo");
		// $this->combobox->setAttr("name","idmodelo");
		// $this->combobox->setAttr("class","form-control");
		// $this->combobox->setAttr("required","");
		// $this->combobox->addItem("");
		// if( isset($data["producto"]["idmarca"]) ) {
			// $this->db->select('idmodelo,descripcion')->order_by("descripcion", "asc");
			// $query = $this->db->where("estado","A")
				// ->where("idmarca", $data["producto"]["idmarca"])->get("general.modelo");
			// $this->combobox->addItem($query->result_array());
			
			// if( isset($data["producto"]["idmodelo"]) ) {
				// $this->combobox->setSelectedOption($data["producto"]["idmodelo"]);
			// }
		// }
		// $data['modelo'] = $this->combobox->getObject();
		
		// eliminamos los items anteriores, para hacer un nuevo combo
		$this->combobox->init();
		
		// combo tipo_producto
		$this->combobox->setAttr("id",$prefix."idtipo_producto");
		$this->combobox->setAttr("name","idtipo_producto");
		$this->combobox->setAttr("class","form-control");
		$this->combobox->setAttr("required","");
		$this->db->select('idtipo_producto,descripcion')->order_by("descripcion", "asc");
		$query = $this->db->where("estado","A")->get("general.tipo_producto");
		// $this->combobox->addItem("P", "PRODUCTO");
		// $this->combobox->addItem("S", "SERVICIO");
		$this->combobox->addItem($query->result_array());
		if( isset($data["producto"]["idtipo_producto"]) ) {
			$this->combobox->setSelectedOption($data["producto"]["idtipo_producto"]);
		}
		$data['tipo'] = $this->combobox->getObject();
		
		
		// combos temporales
		// $query = $this->db->where("estado","A")->get("compra.tipo_precio");
		// $this->combobox->init();
		// $this->combobox->setAttr(array("id"=>"tipo_precio_temp", "name"=>"tipo_precio_temp"));
		// $this->combobox->addItem($query->result_array());
		// $data["tipo_precio_temp"] = $this->combobox->getObject();
		$data["tipo_precio_temp"] = '';
		
		$this->db->select('idmoneda,descripcion')->order_by("descripcion", "desc");
		$query = $this->db->where("estado","A")->get("general.moneda");
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"moneda_temp", "name"=>"moneda_temp", "class"=>"form-control input-sm"));
		$this->combobox->addItem($query->result_array());
		$data["moneda_temp"] = $this->combobox->getObject();
		
		if( !isset($data["producto"]["nro_codigo_producto"]) ) {
			$data["producto"]["nro_codigo_producto"] = str_pad($this->get_last_value(), 5, "0", STR_PAD_LEFT);
		}
		
		$data["corr_temp"] = $data["producto"]["nro_codigo_producto"];
		$data["controller"] = $this->controller;
		$data["prefix"] = $prefix;
		$data["modal"] = $modal;
		
		// formulario linea
		$this->load_controller("linea");
		$data["form_linea"] = $this->linea_controller->form(null, "lin_", true);
		
		// formulario categoria
		$this->load_controller("categoria");
		$data["form_categoria"] = $this->categoria_controller->form(null, "cat_", true);
		
		// formulario marca
		$this->load_controller("marca");
		$data["form_marca"] = $this->marca_controller->form(null, "mar_", true);
		
		// formulario modelo
		$this->load_controller("modelo");
		$data["form_modelo"] = $this->modelo_controller->form(null, "mod_", true);
		
		// formulario color
		$this->load_controller("color");
		$data["form_color"] = $this->color_controller->form(null, "col_", true);
		
		// formulario tamanio
		$this->load_controller("tamanio");
		$data["form_tamanio"] = $this->tamanio_controller->form(null, "tam_", true);
		
		// formulario material
		$this->load_controller("material");
		$data["form_material"] = $this->material_controller->form(null, "mat_", true);
		
		// formulario unidad
		$this->load_controller("unidad");
		$data["form_unidad"] = $this->unidad_controller->form(null, "uni_", true);
		
		$this->js('form/linea/modal');
		$this->js('form/categoria/modal');
		$this->js('form/marca/modal');
		$this->js('form/modelo/modal');
		$this->js('form/unidad/modal');
		$this->js('form/color/modal');
		$this->js('form/material/modal');
		$this->js('form/tamanio/modal');
		
		// if( isset($data["producto"]["idproducto"]) ) {
			// $this->js("<script>update_combo_unidad_temp();</script>", false);
		// }
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	public function form_unidad_medida($data=null, $prefix="", $modal=false) {
		if(!is_array($data)) {
			$data = array();
		}
		
		$query = $this->db->where("estado", "A")->order_by("descripcion", "asc")->get("compra.unidad");
		$data["unidades"] = $query->result_array();
		
		if(isset($data["producto"]["idunidad"])) {
			$this->load_model("unidad");
			$data["unidad"] = $this->unidad->find($data["producto"]["idunidad"]);
		}
		else {
			$data["unidad"] = $data["unidades"];
		}
		
		if(isset($data["producto"]["idproducto"])) {
			$query = $this->db
				->select("u.descripcion,u.abreviatura,p.idunidad,p.cantidad_unidad,p.cantidad_unidad_min")
				->where("p.idproducto", $data["producto"]["idproducto"])
				->join("compra.unidad u", "u.idunidad=p.idunidad")
				->order_by("u.descripcion", "asc")->get("compra.producto_unidad p");
			$data["producto_unidad"] = $query->result_array();
		}
		
		// $data["permiso"] = $this->get_permisos();
		
		$data["controller"] = $this->controller;
		$data["prefix"] = $prefix;
		$data["modal"] = $modal;
		
		$this->js('form/'.$this->controller.'/producto_unidad');
		
		return $this->load->view($this->controller."/form_producto_unidad", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 /*
	public function grilla() {
		// cargamos el modelo y la libreria
		$this->load_model("producto");
		$this->load->library('datatables');
		
		$this->load_model("general.linea");
		$this->load->library('datatables');	
	
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->producto);
		
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('idproducto','descripcion_detallada','descripcion','idlinea'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'Codigo'
			,'Descripcion detallada'
			,'Descripcion generica'
			,'Linea'
			// array('Descripci&oacute;n', '95%') // ancho de la columna
		);

		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName);
		// $table = $this->datatables->createTable();
		$script = "<script>".$this->datatables->createScript()."</script>";
		$this->js($script, false);
		
		$row = $this->get_permisos();
		// if($row->nuevo == 1 || $row->editar == 1 || $row->eliminar == 1) {
		// 	$this->add_button("btn_unidad_medida", "Asignar unidad medida");
		// }
		
		return $table;
	}
	*/
	
	public function grilla() {
		 
		$this->load_model("compra.producto_view");
		$this->load->library('datatables');
		$this->datatables->setModel($this->producto_view);
		//$this->datatables->setIndexColumn("idventa");
		
		//$this->datatables->where('estado', '<>', 'X');
		//$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		//$this->datatables->where('fecha_venta', '=', date("Y-m-d"));//
		
		// $this->datatables->setColumns(array('idventa','fechaventa','cliente','comprobante','serie','descripcion'));
		$this->datatables->setColumns(array('idproducto','descripcion','linea'));
		
		$columnasName = array(
			array('Codigo','5%')
			,'Descripcion generica'
			,'Linea'
			// array('Descripci&oacute;n', '95%') // ancho de la columna
		);


		$table = $this->datatables->createTable($columnasName);
		$script = "<script>".$this->datatables->createScript()."</script>";
		$this->js($script, false);
			$this->set_content($this->get_form_pago("venta", true));
		
		return $table;
	}
	
	
	
	
	public function modal_grilla() {
		// cargamos el modelo y la libreria
		$this->load_model("producto");
		$this->load->library('datatables');
		
		// indicamos el modelo al datatables
		$this->datatables->setModel($this->producto);
		
		// filtros adicionales para la tabla de la bd (perfil en este caso)
		$this->datatables->where('estado', '=', 'A');
		
		// indicamos las columnas a mostrar de la tabla de la bd
		$this->datatables->setColumns(array('descripcion','stock_minimo','precio_compra'));
		
		// columnas de la tabla, si no se envia este parametro, se muestra el 
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			'Producto'
			,'Stock'
			,'Precio'
			// ,'Telefono'
			// array('Descripci&oacute;n', '95%') // ancho de la columna
		);

		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName);
		// $table = $this->datatables->createTable();
		$script = "<script>".$this->datatables->createScript()."</script>";
		$this->js($script, false);
		
		$row = $this->get_permisos();
		if($row->nuevo == 1 || $row->editar == 1 || $row->eliminar == 1) {
			$this->add_button("btn_unidad_medida", "Asignar unidad medida");
		}
		
		return $table;
	}
	
	protected function get_last_value() {
		$query = $this->db->select("last_value")->get("compra.producto_idproducto_seq");
		$data = $query->row();
		return ($data->last_value + 1);
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Producto");
		$this->set_subtitle("");
		
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->set_title("Modificar Producto");
		$this->set_subtitle("");
	
		$this->load_model(array("compra.producto", "general.linea", "general.categoria", "general.marca", 
			"general.modelo", "general.color", "general.material", "general.tamanio", "compra.unidad", 
			"compra.producto_precio_unitario"));
		
		$idsucursal = $this->get_var_session("idsucursal");
		
		$data["producto"] = $this->producto->find($id);
		$data["linea"] = $this->linea->find($data["producto"]["idlinea"]);
		$data["categoria"] = $this->categoria->find($data["producto"]["idcategoria"]);
		$data["marca"] = $this->marca->find($data["producto"]["idmarca"]);
		$data["modelo"] = $this->modelo->find($data["producto"]["idmodelo"]);
		$data["color"] = $this->color->find($data["producto"]["idcolor"]);
		$data["material"] = $this->material->find($data["producto"]["idmaterial"]);
		$data["tamanio"] = $this->tamanio->find($data["producto"]["idtamanio"]);
		$data["producto_alterno"] = $this->producto->find(intval($data["producto"]["codigo_alterno"]));
		$data["precio"] = $this->producto_precio_unitario->find(array("idproducto"=>$id, "idsucursal"=>$idsucursal));
		// echo '<pre>';print_r($data);echo '</pre>';exit;
		
		$unidad = $this->unidad->find($data["producto"]["idunidad"]);
		
		$data["tr_unidad"] = $this->table_tr_unidades($id, $unidad["descripcion"]);
		// $data["tr_compra"] = $this->table_tr_precio_compra($id);
		$data["tr_venta"] = $this->table_tr_precio_venta($id);

		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	public function unidad_medida($id) {
		$this->load_model("producto");
		$data["producto"] = $this->producto->find($id);

		
		$this->set_title("Asignar unidades de medida");
		$this->set_subtitle($data["producto"]["descripcion"]);

		$this->set_content($this->form_unidad_medida($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model(array("producto", "producto_unidad", "compra.producto_precio_compra", "compra.producto_precio_venta"));
		
		$fields = $this->input->post();
		$fields['estado'] = "A";
		
		if(empty($fields["nro_codigo_producto"])) {
			$corr = $this->get_last_value();
		}
		else {
			$corr = intval($fields["nro_codigo_producto"]);
		}
		$fields["nro_codigo_producto"] = str_pad($corr, 5, "0", STR_PAD_LEFT);
		
		$fields["controla_stock"] = (!empty($fields["controla_stock"])) ? 'S' : 'N';
		$fields["controla_serie"] = (!empty($fields["controla_serie"])) ? 'S' : 'N';
		$fields["aplica_igv"] = (!empty($fields["aplica_igv"])) ? 'S' : 'N';
		$fields["aplica_icbper"] = (!empty($fields["aplica_icbper"])) ? '1' : '0';
		//$fields["genera_alerta_stock"] = (!empty($fields["alerta_stock"])) ? 'S' : 'N';
		$fields["codigo_producto"] = $fields["pref_codigo_producto"].$fields["nro_codigo_producto"];

		if(empty($fields["precio_compra"])) {$fields["precio_compra"] = 0;}
		if(empty($fields["precio_mercado"])) {$fields["precio_mercado"] = 0;}
		if(empty($fields["stock_minimo"])) {$fields["stock_minimo"] = 0;}
		if(empty($fields["ganancia_min"])) {$fields["ganancia_min"] = 0;}
		if(empty($fields["ganancia_medio"])) {$fields["ganancia_medio"] = 0;}
		if(empty($fields["ganancia_max"])) {$fields["ganancia_max"] = 0;}
		if(empty($fields["idcolor"])) {$fields["idcolor"] = 0;}
		if(empty($fields["idmaterial"])) {$fields["idmaterial"] = 0;}
		if(empty($fields["idtamanio"])) {$fields["idtamanio"] = 0;}
		if(empty($fields["stock_maximo"])) {$fields["stock_maximo"] = 0;}
		if(empty($fields["factor"])) {$fields["factor"] = 0;}
		// if(empty($fields["codigo_anterior"])) {$fields["codigo_anterior"] = 0;}
		if(empty($fields["codigo_barras"])) {$fields["codigo_barras"] = $fields["codigo_producto"];}
		
		$this->db->trans_start();
		
		// guardamos el producto
		if(empty($fields["idproducto"])) {
			if($this->producto->exists(array("codigo_producto"=>$fields["codigo_producto"])) == false) {
				$id = $this->producto->insert($fields);
				$fields["idproducto"] = $id;
			}
			else {
				$this->exception("El codigo de producto ".$fields["codigo_producto"]." ya existe.");
			}
		}
		else {
			$this->producto->update($fields);
		}
		
		// eliminamos las unidades asignadas anteriormente
		$this->db->delete("compra.producto_unidad", array("idproducto"=>$fields["idproducto"]));
		// asignamos las undades de medida
		if(!empty($fields["prod_unidad"])) {
			$param["idproducto"] = $fields["idproducto"];
			foreach($fields["prod_unidad"] as $k=>$idunidad) {
				if(!empty($fields["prod_cantidad_unidad"][$k]) && !empty($fields["prod_cantidad_unidad_min"][$k])) {
					$param["idunidad"] = $idunidad;
					$param["cantidad_unidad"] = floatval($fields["prod_cantidad_unidad"][$k]);
					$param["cantidad_unidad_min"] = floatval($fields["prod_cantidad_unidad_min"][$k]);
					$this->producto_unidad->insert($param, false);
				}
			}
		}
		// asignamos la unidad de medida minima
		$fields["cantidad_unidad"] = 1;
		$fields["cantidad_unidad_min"] = 1;
		$this->producto_unidad->save($fields, false);
		
		
		// asignamos los precios de compra
		/* $this->db->delete("compra.producto_precio_compra", array("idproducto"=>$fields["idproducto"], "idsucursal"=>$this->get_var_session("idsucursal")));
		if(!empty($fields["precio_compra_idunidad"])) {
			$param["idproducto"] = $fields["idproducto"];
			$param["idsucursal"] = $this->get_var_session("idsucursal");
			foreach($fields["precio_compra_idunidad"] as $k=>$idunidad) {
				if(!empty($fields["precio_compra_precio"][$k])) {
					$param["idunidad"] = $idunidad;
					$param["idmoneda"] = $fields["precio_compra_idmoneda"][$k];
					$param["precio"] = floatval($fields["precio_compra_precio"][$k]);
					$this->producto_precio_compra->insert($param, false);
				}
			}
		} */
		
		// asignamos los precios unitarios
		$this->load_model("compra.producto_precio_unitario");
		$param["idproducto"] = $fields["idproducto"];
		$param["idsucursal"] = $this->get_var_session("idsucursal");
		$param["precio_compra"] = floatval($fields["precio_compra"]);
		$param["precio_venta"] = floatval($fields["precio_venta"]);
		$this->producto_precio_unitario->save($param, false);
		
		// asignamos los precios de venta
		$this->db->delete("compra.producto_precio_venta", array("idproducto"=>$fields["idproducto"], "idsucursal"=>$this->get_var_session("idsucursal")));
		if(!empty($fields["precio_venta_idunidad"])) {
			$param["idproducto"] = $fields["idproducto"];
			$param["idsucursal"] = $this->get_var_session("idsucursal");
			$param["idtipo_precio"] = 1;
			foreach($fields["precio_venta_idunidad"] as $k=>$idunidad) {
				if(!empty($fields["precio_venta_cantidad"][$k])) {
					if(!empty($fields["precio_venta_precio"][$k]) || !empty($fields["precio_venta_porcentaje"][$k])) {
						if(isset($param["precio"]))
							unset($param["precio"]);
						if(isset($param["porcentaje"]))
							unset($param["porcentaje"]);
						
						$param["idunidad"] = $idunidad;
						// $param["idmoneda"] = $fields["precio_venta_idmoneda"][$k];
						$param["idmoneda"] = 1;
						$param["idprecio"] = $k + 1;
						$param["cantidad"] = floatval($fields["precio_venta_cantidad"][$k]);
						if(!empty($fields["precio_venta_precio"][$k])) {
							$param["precio"] = floatval($fields["precio_venta_precio"][$k]);
						}
						if(!empty($fields["precio_venta_porcentaje"][$k])) {
							$param["porcentaje"] = floatval($fields["precio_venta_porcentaje"][$k]);
						}
						$this->producto_precio_venta->insert($param, false);
					}
				}
			}
		}
		
		$this->db->trans_complete();
		
		$this->response($fields);
	}
	
	public function guardar_unidad() {
		$post = $this->input->post();
		
		// eliminamos todas las unidades existentes
		$this->db->where("idproducto", $post["idproducto"]);
		$this->db->delete("compra.producto_unidad");
		
		// insertamos las unidades
		$this->load_model("producto_unidad");
		$param["idproducto"] = $post["idproducto"];
		
		if(!empty($post["idunidad"])) {
			foreach($post["idunidad"] as $k=>$v) {
				$param["idunidad"] = $v;
				$param["cantidad_unidad"] = $post["cantidad_unidad"][$k];
				$param["cantidad_unidad_min"] = $post["cantidad_unidad_min"][$k];
				
				$this->producto_unidad->insert($param, false);
			}
		}
		
		// guardamos la unidad minima
		$this->load_model("producto");
		$data = $this->producto->find($param["idproducto"]);
		
		$param["idunidad"] = $data["idunidad"];
		$param["cantidad_unidad"] = 1;
		$param["cantidad_unidad_min"] = 1;
		
		$this->producto_unidad->save($param, false);
		
		$this->response($this->producto_unidad->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model("producto");
		
		// cambiamos de estado
		$fields['idproducto'] = $id;
		$fields['estado'] = "I";
		$this->producto->update($fields);
		
		$this->response($fields);
	}
	
	/**
	 * Metodo para obtener los datos del producto
	 */
	public function get($id) {
		$idsucursal = $this->get_var_session("idsucursal");
		
		$sql = "SELECT p.*, pu.precio_compra, pu.precio_venta
			FROM compra.producto p
			LEFT JOIN compra.producto_precio_unitario pu 
			on pu.idproducto=p.idproducto and pu.idsucursal={$idsucursal}
			WHERE p.idproducto = ?";
		
		$query = $this->db->query($sql, array($id));
		
		$data = $query->row_array();
		if(empty($data["precio_venta"])) {
			// obtenemos el precio de venta
			$precios = $this->get_real_precio_venta($data["idproducto"], true);
			$data["precio_venta"] = array_shift($precios);
		}
		
		$this->response($data);
	}
	
	public function get_unidades($id, $idunidad = FALSE) {
		$this->load_model("producto");
		$this->response($this->producto->unidades($id, $idunidad));
	}
	
	public function get_precio_compra($id) {
		$this->load_model("producto");
		$this->response($this->producto->precio_compra($id, $this->get_var_session("idsucursal")));
	}
	
	public function get_precio_venta($id) {
		$this->load_model("producto");
		$this->response($this->producto->precio_venta($id, $this->get_var_session("idsucursal")));
	}
	
	private function _get_precio_compra($idproducto, $idunidad, $idmoneda=null) {
		$idsucursal = $this->get_var_session("idsucursal");
		$idmoneda = ($idmoneda != null) ? $idmoneda : 1;
		
		// precios de compra
		$preciocompra = false;
		
		// obtenemos el precio de compra segun las opciones del usuario
		$sql = "select precio from compra.producto_precio_compra 
			where idproducto=? and idsucursal=? and idunidad=? and idmoneda=?";
		$query = $this->db->query($sql, array($idproducto, $idsucursal, $idunidad, $idmoneda));
		if($query->num_rows() > 0) {
			// el precio indicado requerido por el usuario existe
			$row = $query->row_array();
			$preciocompra = $row["precio"];
		}
		else {
			// el precio no existe, calculamos el precio desde otra unidad de medida
			$sql = "select pc.precio/pu.cantidad_unidad_min as precio
				from compra.producto_precio_compra pc
				join compra.producto_unidad pu on pu.idproducto=pc.idproducto and pu.idunidad = pc.idunidad
				where pc.idproducto=? and pc.idsucursal=? and pc.idmoneda=?
				order by pu.cantidad_unidad_min, pu.idunidad";
			$query = $this->db->query($sql, array($idproducto, $idsucursal, $idmoneda));
			if($query->num_rows() > 0) {
				$row = $query->row_array();
				$preciocompra = $row["precio"];
			}
		}
		
		return $preciocompra;
	}
	
	public function get_real_precio_venta($idproducto=FALSE, $return=FALSE) {
		$post = $this->input->post();
		if( ! is_array($post))
			$post = array();
		
		if($idproducto !== FALSE)
			$post["idproducto"] = $idproducto;
		
		// datos previos
		$idproducto = intval($post["idproducto"]);
		$idsucursal = $this->get_var_session("idsucursal");
		$idunidad = (!empty($post["idunidad"])) ? intval($post["idunidad"]) : 1;
		$idmoneda = (!empty($post["idmoneda"])) ? intval($post["idmoneda"]) : 1;
		$cantidad = (!empty($post["cantidad"])) ? floatval($post["cantidad"]) : 1;
		
		// modelos
		$this->load_model(array("compra.producto_unidad", "compra.producto"));
		$this->producto->find($idproducto);
		$this->producto_unidad->find(array("idproducto"=>$idproducto, "idunidad"=>$idunidad));
		$cantidad_min = $this->producto_unidad->get("cantidad_unidad_min");
		
		// $preciocompra = $this->_get_precio_compra($idproducto, $idunidad, $idmoneda);
		// $preciocompra = $this->producto->get_precio_costo_unitario($idproducto, $idsucursal, $idunidad, $idmoneda);
		$preciocompra = $this->producto->get_precio_costo_unitario($idproducto, $idsucursal, null, $idmoneda);
		
		$arr_precio = array();
		
		// generamos la lista de precios de venta
		// obtenemos el precio unitario
		$sql = "select coalesce(precio_venta,0) as precio from compra.producto_precio_unitario 
			where idproducto=? and idsucursal=? and coalesce(precio_venta,0)>0";
		$query = $this->db->query($sql, array($idproducto, $idsucursal));
		if($query->num_rows() > 0) {
			$arr_precio[] = $query->row()->precio * $cantidad_min;
		}
		
		// primero obtenemos los precios que acepten el filtro
		$sql = "select * from compra.producto_precio_venta
			where idproducto=? and idsucursal=? and idunidad=? and idmoneda=? and cantidad=?
			order by precio desc";
		$query = $this->db->query($sql, array($idproducto, $idsucursal, $idunidad, $idmoneda, $cantidad));
		if($query->num_rows() > 0) {
			$rs = $query->result_array();
			foreach($rs as $row) {
				if(!empty($row["precio"]) && $row["precio"] > 0) {
					$arr_precio[] = $row["precio"];
				}
				/*if(!empty($row["porcentaje"]) && $row["porcentaje"] > 0 && $preciocompra !== false) {
					$arr_precio[] = $cantidad_min * $preciocompra * (1 + $row["porcentaje"] / 100);
				}*/
			}
			
			// print_r($arr_precio); return true;
		}
		// print_r($arr_precio); return true;
		// obtenemos los registros de precios por debajo de la cantidad indicada, 
		// solo hasta un nivel
		$sql = "select * from compra.producto_precio_venta 
			where idproducto=? and idsucursal=? and idunidad=? and idmoneda=? and cantidad<?
			order by precio desc";
		$query = $this->db->query($sql, array($idproducto, $idsucursal, $idunidad, $idmoneda, $cantidad));
		if($query->num_rows() > 0) {
			$rs = $query->result_array();
			$co = $cn = 0;
			foreach($rs as $k=>$row) {
				if($k == 0) {
					$cn = $co = $row["cantidad"];
				}
				$cn = $row["cantidad"];
				if($cn != $co)
					break;
				if(!empty($row["precio"]) && $row["precio"] > 0) {
					$arr_precio[] = $row["precio"];
				}
				/*if(!empty($row["porcentaje"]) && $row["porcentaje"] > 0 && $preciocompra !== false) {
					$arr_precio[] = $cantidad_min * $preciocompra * (1 + $row["porcentaje"] / 100);
				}*/
			}
		}
		
		if(empty($arr_precio)) {
			// si despues de toda la verificacion, aun no se han conseguido los precios
			// obtenemos el precio a partir de algun registro diferente de la tabla
			// haremos el calculo (multiplicacion o division segun sea el caso) 
			// para obtener el precio de venta
			$ct = $cantidad_min * $cantidad;
			$sql = "select pv.*, pu.cantidad_unidad_min, pu.cantidad_unidad_min * pv.cantidad as cantidad_total
				from compra.producto_precio_venta pv
				join compra.producto_unidad pu on pu.idproducto=pv.idproducto and pu.idunidad = pv.idunidad
				where pv.idproducto=? and pv.idsucursal=? and pv.idmoneda=? 
				and (pu.cantidad_unidad_min * pv.cantidad) <=?
				order by cantidad_total desc, cantidad_unidad_min desc, cantidad desc, idunidad, precio desc
				limit 1";
			$query = $this->db->query($sql, array($idproducto, $idsucursal, $idmoneda, $ct));
			if($query->num_rows() > 0) {
				$row = $query->row_array();
				/* transformamos el precio obtenido a la unidad de medida indicada por el user */
				if(!empty($row["precio"]) && $row["precio"] > 0) {
					$arr_precio[] = ($row["precio"] / $row["cantidad"]) * $cantidad_min;
				}
				/*if(!empty($row["porcentaje"]) && $row["porcentaje"] > 0 && $preciocompra !== false) {
					$arr_precio[] = $cantidad_min * $preciocompra * (1 + $row["porcentaje"] / 100);
				}*/
			}
		}
		
		if( ! empty($post["precio"]) && is_numeric($post["precio"])) {
			settype($post["precio"], "double");
			array_push($arr_precio, $post["precio"]);
		}
		
		/*--*/
		$n_arr_precio = array();
		$fc = $this->get_param("fixed_venta");
		if(!is_numeric($fc)) {
			$fc = 2;
		}
		foreach($arr_precio as $v){
			settype($v,"double");
			$v=number_format($v,$fc,'.','');
			$n_arr_precio[]=$v;
		}
		$n_arr_precio = array_unique($n_arr_precio);
		/*--*/
		
		rsort($n_arr_precio);
		
		if($return === TRUE)
			return $n_arr_precio;
		
		// $this->response(($arr_precio));
		$this->response(($n_arr_precio));
	}
	
	public function get_all($id, $return=false) {
		$this->load_model(array("unidad", "producto"));
		$data["producto"] = $this->producto->find($id);
		$data["producto_unidad"] = $this->unidad->find($data["producto"]["idunidad"]);
		$data["unidades"] = $this->producto->unidades($id);
		if($return) {
			return $data;
		}
		$this->response($data);
	}
	
	public function autocomplete() {
		$with_serie = (boolean) $this->input->post("with_serie");
		$idalmacen = (int) $this->input->post("idalmacen");
		$idsucursal = $this->get_var_session("idsucursal");
		
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		if($with_serie) {
			$sql = "SELECT p.idproducto, p.codigo_barras as codigo_producto, p.descripcion_detallada, p.idunidad, 
				0::integer as with_serie, {$idalmacen}::integer as idalmacen, pu.precio_compra, pu.precio_venta
				FROM compra.producto p
				LEFT JOIN compra.producto_precio_unitario pu on pu.idproducto=p.idproducto and pu.idsucursal={$idsucursal}
				WHERE p.estado='A' AND p.codigo_barras ILIKE ?
				UNION ALL
				SELECT das.idproducto, das.serie as codigo_producto, p.descripcion_detallada||' *' as descripcion_detallada, 
				das.idunidad, 1::integer as with_serie, das.idalmacen, pu.precio_compra, pu.precio_venta
				FROM almacen.detalle_almacen_serie as das
				JOIN compra.producto p ON p.idproducto = das.idproducto AND p.estado = 'A'
				LEFT JOIN compra.producto_precio_unitario pu on pu.idproducto=p.idproducto and pu.idsucursal={$idsucursal}
				WHERE das.estado = 'A' AND das.despachado = 'N' 
				AND das.idalmacen = '{$idalmacen}' AND das.serie ILIKE ?
				ORDER BY codigo_producto, descripcion_detallada
				LIMIT ?";
		}
		else {
			$sql = "SELECT p.idproducto, p.codigo_producto, p.descripcion_detallada, p.idunidad,p.codigo_barras, 
				0::integer as with_serie, {$idalmacen}::integer as idalmacen, pu.precio_compra, pu.precio_venta,COALESCE(st.stock,0) AS stock, um.abreviatura
				FROM compra.producto p
				INNER JOIN compra.unidad um ON um.idunidad=p.idunidad
				INNER JOIN compra.producto_precio_unitario pu on pu.idproducto=p.idproducto and pu.idsucursal={$idsucursal}
				LEFT JOIN (SELECT SUM(d.cantidad*u.cantidad_unidad_min*d.tipo_number) as stock ,d.idproducto ,d.idalmacen 
						FROM almacen.detalle_almacen d
						INNER JOIN compra.producto_unidad u ON u.idproducto=d.idproducto AND u.idunidad=d.idunidad
						WHERE d.estado = 'A'
						GROUP BY d.idproducto, d.idalmacen) st on st.idproducto = p.idproducto and st.idalmacen = {$idalmacen}
				WHERE p.estado='A' AND (p.descripcion_detallada ILIKE ? OR p.codigo_barras ILIKE ?)
				ORDER BY codigo_producto, descripcion_detallada
				LIMIT ?";
		}

		$query = $this->db->query($sql, array($txt, $txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
	
	public function get_stock($idproducto, $idalmacen = NULL) {
		$this->load_model("producto");
		$stock = $this->producto->stock($idproducto, $idalmacen);
		$this->response($stock);
	}
	
	public function autocomplete_descripcion() {
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		$sql = "SELECT distinct descripcion FROM compra.producto
			WHERE estado='A' and descripcion ILIKE ?
			ORDER BY descripcion LIMIT ?";
		$query = $this->db->query($sql, array($txt, $this->input->post("maxRows")));
		$this->response($query->result_array());
	}
	
	public function table_tr_unidades($idproducto, $udesc) {
		$rs = $this->producto->unidades($idproducto);
		$html = '';
		if(!empty($rs)) {
			foreach($rs as $val) {
				$html .= '<tr index="'.$val["idunidad"].'">';
				$html .= '<td><input type="text" name="prod_cantidad_unidad[]" class="prod_cantidad_unidad form-control input-xs" value="'.$val["cantidad_unidad"].'" readonly></td>';
				$html .= '<td><input type="hidden" name="prod_unidad[]" class="prod_unidad" value="'.$val["idunidad"].'">'.$val["descripcion"].'</td>';
				$html .= '<td>es <strong>equivalente</strong> a</td>';
				$html .= '<td><input type="text" name="prod_cantidad_unidad_min[]" class="prod_cantidad_unidad_min form-control input-xs" value="'.$val["cantidad_unidad_min"].'"></td>';
				$html .= '<td><abbr title="Unidad de Medida para el control del stock del producto escogido por el usuario en la pesta&ntilde;a [Datos b&aacute;sicos]">'.$udesc.'</abbr></td>';
				$html .= '<td><button class="btn btn-danger btn-xs btn-del-unidad"><i class="fa fa-trash"></i></button></td>';
				$html .= '</tr>';
			}
		}
		return $html;
	}
	
	public function table_tr_precio_compra($idproducto) {
		$rs = $this->producto->precio_compra($idproducto, $this->get_var_session("idsucursal"));
		$html = '';
		if(!empty($rs)) {
			foreach($rs as $val) {
				$html .= '<tr index="'.$val["idmoneda"].$val["idunidad"].'">';
				$html .= '<td><input type="hidden" name="precio_compra_idunidad[]" class="precio_compra_idunidad" value="'.$val["idunidad"].'">'.$val["descripcion"].'</td>';
				$html .= '<td><input type="hidden" name="precio_compra_idmoneda[]" class="precio_compra_idmoneda" value="'.$val["idmoneda"].'">'.$val["moneda"].'</td>';
				$html .= '<td><input type="text" name="precio_compra_precio[]" class="precio_compra_precio form-control input-xs" value="'.$val["precio"].'"></td>';
				$html .= '<td><button class="btn btn-danger btn-xs btn-del-precio-compra"><i class="fa fa-trash"></i></button></td>';
				$html .= '</tr>';
			}
		}
		return $html;
	}
	
	public function table_tr_precio_venta($idproducto) {
		$rs = $this->producto->precio_venta($idproducto, $this->get_var_session("idsucursal"));
		$html = '';
		if(!empty($rs)) {
			// $this->load->library('combobox', '', 'combo_tipo');
			// $this->combo_tipo->setAttr("name","precio_venta_idtipo_precio[]");
			// $this->combo_tipo->setAttr("class","precio_venta_idtipo_precio form-control input-xs");
			// $query = $this->db->select("idtipo_precio, descripcion")->where("estado", "A")->get("compra.tipo_precio");
			// $this->combo_tipo->addItem($query->result_array());
			
			$this->load->library('combobox', '', 'combo_unidad');
			$this->combo_unidad->setAttr("name","precio_venta_idunidad[]");
			$this->combo_unidad->setAttr("class","precio_venta_idunidad form-control input-xs");
			$this->combo_unidad->addItem($this->producto->unidades($idproducto));
			
			// $this->load->library('combobox', '', 'combo_moneda');
			// $this->combo_moneda->setAttr("name","precio_venta_idmoneda[]");
			// $this->combo_moneda->setAttr("class","precio_venta_idmoneda form-control input-xs");
			// $query = $this->db->select("idmoneda, descripcion")->where("estado", "A")->get("general.moneda");
			// $this->combo_moneda->addItem($query->result_array());
			
			foreach($rs as $val) {
				$this->combo_unidad->setSelectedOption($val["idunidad"]);
				// $this->combo_moneda->setSelectedOption($val["idmoneda"]);
				
				$html .= '<tr>';
				$html .= '<td>'.$this->combo_unidad->getObject().'</td>';
				// $html .= '<td>'.$this->combo_moneda->getObject().'</td>';
				$html .= '<td><input type="text" name="precio_venta_cantidad[]" class="precio_venta_cantidad form-control input-xs" value="'.$val["cantidad"].'"></td>';
				$html .= '<td><input type="text" name="precio_venta_precio[]" class="precio_venta_precio form-control input-xs" value="'.$val["precio"].'"></td>';
				$html .= '<td><input type="text" name="precio_venta_porcentaje[]" class="precio_venta_porcentaje form-control input-xs" value="'.$val["porcentaje"].'"></td>';
				$html .= '<td><button class="btn btn-danger btn-xs btn-del-precio-venta"><i class="fa fa-trash"></i></button></td>';
				$html .= '</tr>';
			}
		}
		return $html;
	}
	
	public function search_serie() {
		$idalmacen = (int) $this->input->post("idalmacen");
		$idsucursal = $this->get_var_session("idsucursal");
		
		$txt = trim($this->input->post("query"));
		$txt = preg_replace('/\s+/', '', $txt);
		
		$sql = "SELECT p.idproducto, p.codigo_barras as codigo_producto, p.descripcion_detallada, p.idunidad, 
			0::integer as with_serie, {$idalmacen}::integer as idalmacen--, pu.precio_compra, pu.precio_venta
			FROM compra.producto p
			WHERE p.estado='A' AND p.codigo_barras ILIKE ?
			UNION ALL
			SELECT das.idproducto, das.serie as codigo_producto, p.descripcion_detallada||' *' as descripcion_detallada, 
			das.idunidad, 1::integer as with_serie, das.idalmacen
			FROM almacen.detalle_almacen_serie as das
			JOIN compra.producto p ON p.idproducto = das.idproducto AND p.estado = 'A'
			LEFT JOIN compra.producto_precio_unitario pu on pu.idproducto=p.idproducto and pu.idsucursal={$idsucursal}
			WHERE das.estado = 'A' AND das.despachado = 'N' 
			AND das.idalmacen = '{$idalmacen}' AND das.serie ILIKE ?
			ORDER BY codigo_producto, descripcion_detallada";
			
		$query = $this->db->query($sql, array($txt, $txt));
		$this->response($query->result_array());
	}
	
	public function serie_autocomplete() {
		$idalmacen = (int) $this->input->post("idalmacen");
		$idproducto = (int) $this->input->post("idproducto");
		$limit = (int) $this->input->post("maxRows");
		
		$txt = trim($this->input->post("startsWith"));
		$txt = "%".preg_replace('/\s+/', '%', $txt)."%";
		
		$sql = "SELECT serie 
			FROM almacen.detalle_almacen_serie
			WHERE estado = 'A' AND despachado = 'N' 
			AND idalmacen = ? AND idproducto = ? AND serie ILIKE ?
			ORDER BY serie LIMIT ?";

		$query = $this->db->query($sql, array($idalmacen, $idproducto, $txt, $limit));
		$this->response($query->result_array());
	}
	
	public function grilla_serie() {
		$this->load_model("almacen.detalle_almacen_serie");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->detalle_almacen_serie);
		// $this->datatables->setIndexColumn("idproveedor");
		
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('despachado', '=', 'N');
		$this->datatables->where('idalmacen', '=', $this->input->get_post("idalmacen"));
		$this->datatables->where('idproducto', '=', $this->input->get_post("idproducto"));
		
		$this->datatables->setColumns(array('serie', 'fecha_ingreso'));
		$this->datatables->order_by("fecha_ingreso", "asc");
		
		$this->datatables->setPopup(true);
		
		$table = $this->datatables->createTable(array('Serie','Fec. ingreso'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	public function get_equivalencia($idproducto=0, $idunidad=0, $idunidadprod=0) {
		$post = $this->input->post();
		if(empty($idproducto) && ! empty($post["idproducto"])) {
			$idproducto = intval($post["idproducto"]);
		}
		if(empty($idunidad) && ! empty($post["idunidad"])) {
			$idunidad = intval($post["idunidad"]);
		}
		if(empty($idunidad) && ! empty($post["idunidadprod"])) {
			$idunidadprod = intval($post["idunidadprod"]);
		}
		
		$res = array();
		
		$sql = "select * from compra.producto where idproducto_padre=? and idunidad=?";
		$query = $this->db->query($sql, array($idproducto, $idunidad));
		if($query->num_rows() > 0)
			$res["producto"] = $query->row_array();
		
		$sql = "select * from compra.producto_unidad where idproducto=? and idunidad=?";
		$query = $this->db->query($sql, array($idproducto, $idunidadprod));
		if($query->num_rows() > 0)
			$res["unidad"] = $query->row_array();
		
		$this->response($res);
	}
}
?>