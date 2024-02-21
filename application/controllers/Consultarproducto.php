<?php

include_once "Controller.php";

class Consultarproducto extends Controller {
	public $decimales = 2;	//Decimales para el reporte de pdf y excel del stock
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Consulta de productos");
		$this->set_subtitle("Consultar Productos");
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
		$data["controller"] = $this->controller;
		
		$this->load->library('combobox');
		
		$this->combobox->setAttr("id","idalmacen");
		$this->combobox->setAttr("name","idalmacen");
		$this->combobox->setAttr("class","form-control input-xs combo-filtro");
		
		$query = $this->db->select('idalmacen, descripcion')
			->where("estado","A")->where("idsucursal", $this->get_var_session("idsucursal"))
			->order_by("descripcion")->get("almacen.almacen");
		
		$this->combobox->addItem($query->result_array());
		
		$idalmacen = $query->row()->idalmacen;
		
		$data['almacen'] = $this->combobox->getObject();
		
		// otro combo almacen
		$this->combobox->removeAttr("id");
		$this->combobox->setAttr("class","form-control input-xs idalmacen");
		$data["almacen_temp"] = $this->combobox->getObject(true);
		
		// $this->combobox->init();
		/*------------------------------------------------------------------------------------------*/
		// $this->combobox->setAttr("multiple","");
		$this->combobox->setAttr("id","idcategoria");
		$this->combobox->setAttr("name","idcategoria");
		$this->combobox->setAttr("class","form-control input-xs combo-filtro");
		// $this->combobox->setAttr("required","");
		$this->db->select('idcategoria,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.categoria");
		$this->combobox->addItem("T","TODOS");
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption('T');
		
		$data['categoria'] = $this->combobox->getObject();
		$this->combobox->init();
		/*------------------------------------------------------------------------------------------*/
		// $this->combobox->setAttr("multiple","");
		$this->combobox->setAttr("id","idmarca");
		$this->combobox->setAttr("name","idmarca");
		$this->combobox->setAttr("class","form-control input-xs combo-filtro");
		// $this->combobox->setAttr("required","");
		$this->db->select('idmarca,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.marca");
		$this->combobox->addItem("T","TODOS");
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption('T');
		
		$data['marca'] = $this->combobox->getObject();
		$this->combobox->init();
		/*------------------------------------------------------------------------------------------*/
		// $this->combobox->setAttr("multiple","");
		$this->combobox->setAttr("id","idmodelo");
		$this->combobox->setAttr("name","idmodelo");
		$this->combobox->setAttr("class","form-control input-xs combo-filtro");
		// $this->combobox->setAttr("required","");
		$this->db->select('idmodelo,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.modelo");
		$this->combobox->addItem("T","TODOS");
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption('T');
		
		$data['modelo'] = $this->combobox->getObject(true);
		$this->combobox->init();
		
		/*------------------------------------------------------------------------------------------*/		
		// combo tipo_movimiento
		$query = $this->db->select('tipo_movimiento,descripcion')
			->order_by("tipo_movimiento")->get("almacen.tipo_movimiento");
		$this->combobox->removeAttr("id");
		$this->combobox->setAttr("name", "tipo_movimiento");
		$this->combobox->setAttr("class", "form-control input-xs tipo_operacion");
		$this->combobox->addItem($query->result_array());
		$data["tipo_movimiento"] = $this->combobox->getObject(true);
		/*-------------------------------------------------------------------------------------------*/
		// combo tipo documento
		$query = $this->db->select('idtipodocumento,descripcion')->where("estado", "A")
			->order_by("descripcion")->get("venta.tipo_documento");
		$this->combobox->setAttr("name", "idtipodocumento");
		$this->combobox->setAttr("class", "form-control input-xs idtipodocumento");
		$this->combobox->addItem($query->result_array());
		$data["tipo_documento"] = $this->combobox->getObject(true);
		/*-------------------------------------------------------------------------------------------*/
		// combo unidades de medida
		$sql = "select idunidad,descripcion||' ('||abreviatura||')' as descripcion,abreviatura
			from compra.unidad where estado='A' order by descripcion";
		$query = $this->db->query($sql);
		$this->combobox->setAttr("name", "conversion_idunidad");
		$this->combobox->setAttr("class", "form-control input-xs conversion_idunidad");
		$this->combobox->addItem($query->result_array(), array("idunidad","descripcion","abreviatura"));
		$data["unidadmedida"] = $this->combobox->getObject(true);
		/*-------------------------------------------------------------------------------------------*/
		
		$data['grid'] = $this->grid($idalmacen);
		
		$this->css("plugins/chosen/chosen");
		$this->js("plugins/chosen/chosen.jquery");
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return null;
	}
	
	public function grid($idalmacen = FALSE){
		$this->load_model('almacen.view_stock');

		$this->load->library('datatables');
		
		$this->datatables->setModel($this->view_stock);

		$this->datatables->setIndexColumn("idproducto");
		
		if($idalmacen !== FALSE) {
			$this->datatables->where('idalmacen', '=', $idalmacen);
		}

		$this->datatables->setColumns(array('producto_detallado','marca','modelo','unidad_medida','precio_venta','stock'));
		//$this->datatables->setColumns(array('producto_detallado','marca','modelo','unidad_medida','precio_venta','precio_costo','stock'));
		$this->datatables->order_by('producto_detallado','asc');
		$this->datatables->showInfo(false);
		// $this->datatables->showFilter(false);
		
		$columnasName = array(
			array('Producto', '40%')
			,array('Marca', '12%')
			,array('Modelo', '12%')
			,array('U.M', '12%')
			,array('PSP', '8%')
	//		,array('Costo', '8%')
			,array('Stock', '8%')
		);

		// $this->datatables->setCallback('callbackStock');

		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName, false, "table-striped");

		$script = "<script>".$this->datatables->createScript(false, false)."</script>";

		// agregamos los css para el dataTables
		// $this->css('plugins/dataTables/dataTables.bootstrap');
		// $this->css('plugins/dataTables/dataTables.responsive');
		// $this->css('plugins/dataTables/dataTables.tableTools.min');

		// agregamos los scripts para el dataTables
		// $this->js('plugins/dataTables/jquery.dataTables');
		// $this->js('plugins/dataTables/dataTables.bootstrap');
		// $this->js('plugins/dataTables/dataTables.responsive');
		// $this->js('plugins/dataTables/dataTables.tableTools.min');		

		$this->js($script, false);

		return $table;
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
	
	public function cargar_producto(){
		$this->load_model("almacen.view_stock");

		$fields = $this->input->post();
		$data1 = $this->view_stock->find(array("idproducto"=>$fields["idproducto"]));
		$this->response($data1);
	}
	
	public function cargar_series(){
		$fields = $this->input->post();
		$query = $this->db->query("	SELECT serie 
									FROM almacen.detalle_almacen_serie 
									WHERE idproducto='{$fields['idproducto']}' 
									AND idalmacen='{$fields['idalmacen']}' 
									AND estado='A'
									AND despachado='N'; ");
		$this->response($query->result_array());
	}
	
	public function cargar_carrusel(){
		$fields = $this->input->post();
		$query = $this->db->query("	SELECT imagen_producto,idproducto,es_principal 
									FROM compra.producto_imagen WHERE idproducto='{$fields['idproducto']}' 
									AND estado='A' 
									ORDER BY es_principal DESC; ");
		$this->response($query->result_array());
	}
	
	public function cargar_data_panel(){
		$fields = $this->input->post();
		$where = $this->condicion();
		// if(!empty($fields['filter'])){
			// $where.=" AND {$fields['filter']} ILIKE '{$fields['search']}%'";
		// }
		
		// if(!empty($fields['idcategoria']) && $fields['idcategoria']!='T'){
			// $where.=" AND idcategoria='{$fields['idcategoria']}'";
		// }
		
		// if(!empty($fields['idmarca']) && $fields['idmarca']!='T'){
			// $where.=" AND idmarca='{$fields['idmarca']}'";
		// }
		
		// if(!empty($fields['idmodelo']) && $fields['idmodelo']!='T'){
			// $where.=" AND idmodelo='{$fields['idmodelo']}'";
		// }
		$query = $this->db->query("SELECT*, initcap(producto_detallado) prod FROM almacen.view_stock $where ORDER BY producto asc;");
		
		$this->response($query->result_array());
	}
	
	public function stock_um(){
		$fields = $this->input->post();
		$query = $this->db->query("	SELECT u.descripcion unidad_medida ,d.idunidad,u.abreviatura
									FROM almacen.detalle_almacen d
									JOIN compra.unidad u ON u.idunidad=d.idunidad
									WHERE d.idproducto='{$fields['idproducto']}' 
									AND d.idalmacen='{$fields['idalmacen']}'
									AND tipo='E'
									GROUP BY unidad_medida,d.idunidad,u.abreviatura
									ORDER BY unidad_medida;");
		$um = $query->result_array();
		$html = "";
		foreach($um as $key=>$val){
			$q = $this->db->query("SELECT COALESCE(SUM(d.cantidad) ,0.00) stock
									FROM almacen.detalle_almacen d
									JOIN compra.unidad u ON u.idunidad=d.idunidad
									WHERE d.idproducto='{$fields['idproducto']}' 
									AND d.idalmacen='{$fields['idalmacen']}'
									AND d.idunidad='{$val['idunidad']}'
									AND d.tipo='E'");
			$stock_um = $q->row()->stock;
			$addClass = '';
			if($key==0)
				$addClass = 'fist-item';
			
			$html.= '<li class="list-group-item '.$addClass.'">';
			$html.= '	<label>'.$val['unidad_medida'].'</label>';
			$html.= '	<div>'.$stock_um.' '.$val['abreviatura'].'</div>';
			$html.= '</li>';
		}
		
		$this->response($html);
	}
	
	public function lista_precios(){
		$fields = $this->input->post();
		$query = $this->db->query("	SELECT 
									u.descripcion unidad_medida ,u.idunidad,u.abreviatura
									FROM compra.producto_precio_venta p
									JOIN compra.unidad u ON u.idunidad=p.idmoneda
									JOIN seguridad.sucursal s ON s.idsucursal=p.idsucursal
									JOIN almacen.almacen a ON a.idsucursal=p.idsucursal
									WHERE p.idproducto='{$fields['idproducto']}' AND a.idalmacen='{$fields['idalmacen']}'
									GROUP BY u.descripcion ,u.idunidad,u.abreviatura
									ORDER BY unidad_medida;");
		$um = $query->result_array();
		$html = "";
		/*
		SELECT 
									COALESCE(precio,0.00) precio 
									,m.abreviatura moneda,u.descripcion
									FROM compra.producto_precio_venta p
									JOIN compra.unidad u ON u.idunidad=p.idmoneda
									JOIN seguridad.sucursal s ON s.idsucursal=p.idsucursal
									JOIN almacen.almacen a ON a.idsucursal=p.idsucursal
									JOIN general.moneda m ON m.idmoneda=p.idmoneda
									WHERE p.idunidad='1' 
									AND p.idproducto='2'  
									AND a.idalmacen='1'; 
		*/
		foreach($um as $key=>$val){
			$q = $this->db->query("	SELECT 
									COALESCE(precio,0.00) precio 
									,m.abreviatura moneda
									FROM compra.producto_precio_venta p
									JOIN compra.unidad u ON u.idunidad=p.idmoneda
									JOIN seguridad.sucursal s ON s.idsucursal=p.idsucursal
									JOIN almacen.almacen a ON a.idsucursal=p.idsucursal
									JOIN general.moneda m ON m.idmoneda=p.idmoneda
									WHERE p.idunidad='{$val['idunidad']}' 
									AND p.idproducto='{$fields['idproducto']}'  
									AND a.idalmacen='{$fields['idalmacen']}'
									ORDER BY moneda; ");
			$um_moneda = $q->result_array();
			// $precio = $q->row()->precio;
			// $moneda = $q->row()->moneda;
			$addClass = '';
			if($key==0)
				$addClass = 'fist-item';
			
			$html.= '<li class="list-group-item '.$addClass.'">';
			$html.= '	<label>'.$val['unidad_medida'].'</label>';
			// $html.= '	<div>'.$moneda.' '.$precio.'</div>';
			foreach($um_moneda as $k=>$v){
				$html.= '	<div>'.$v['precio'].' '.$v['moneda'].'</div>';				
			}
			$html.= '</li>';
		}
		
		$this->response($html);
	}
	
	public function condicion(){
		// $fields = $this->input->post();
		$fields = $_REQUEST;
		
		$where = " WHERE idproducto!='0' ";
		if(!empty($fields['filter'])){
			$where.=" AND {$fields['filter']} ILIKE '{$fields['search']}%'";
		}
		
		if(!empty($fields['idcategoria']) && $fields['idcategoria']!='T'){
			$where.=" AND idcategoria='{$fields['idcategoria']}'";
		}
		
		if(!empty($fields['idmarca']) && $fields['idmarca']!='T'){
			$where.=" AND idmarca='{$fields['idmarca']}'";
		}
		
		if(!empty($fields['idmodelo']) && $fields['idmodelo']!='T'){
			$where.=" AND idmodelo='{$fields['idmodelo']}'";
		}
		
		if(!empty($fields['idalmacen'])){
			$where.=" AND idalmacen='{$fields['idalmacen']}'";
		}
		
		if($fields['con_stock']!='T')
			if($fields['con_stock']=='N')
				$where.=" AND stock<=0";
			else
				$where.=" AND stock>0";
		return $where;
	}
	
	public function remove_stock($returnable = FALSE, $transaction = TRUE) {
		$post = $this->input->post();
		// print_r($post);exit;
		$this->load_model("detalle_almacen");
		$this->load_model("almacen.despacho");
		$this->load_model("tipo_movi_almacen");
		if( ! isset($this->producto))
			$this->load_model("producto");
		if( ! isset($this->producto_unidad))
			$this->load_model("producto_unidad");
		
		// datos default despacho
		$this->despacho->set("idreferencia", 0);
		$this->despacho->set("referencia", "");
		$this->despacho->set("idalmacen", $post["idalmacen"]);
		$this->despacho->set("tipo_docu", $post["idtipodocumento"]);
		$this->despacho->set("serie", $post["serie"]);
		$this->despacho->set("numero", $post["numero"]);
		$this->despacho->set("observacion", $post["observacion"]);
		$this->despacho->set("estado", "C");
		$this->despacho->set("fecha", date("Y-m-d"));
		$this->despacho->set("hora", date("H:i:s"));
		$this->despacho->set("idusuario", $this->get_var_session("idusuario"));
		$this->despacho->set("iddetalle_referencia", 0);
		
		// datos default detalle almacen
		$this->detalle_almacen->set("idalmacen", $post["idalmacen"]);
		$this->detalle_almacen->set("tipo", "S");
		$this->detalle_almacen->set("tipo_number", -1);
		$this->detalle_almacen->set("precio_venta", 0);
		$this->detalle_almacen->set("fecha", date("Y-m-d"));
		$this->detalle_almacen->set("tabla", "");
		$this->detalle_almacen->set("idtabla", '0');
		$this->detalle_almacen->set("estado", "A");
		$this->detalle_almacen->set("idrecepcion", "0");
		$this->detalle_almacen->set("iddespacho", "0");
		$this->detalle_almacen->set("idsucursal", $this->get_var_session("idsucursal"));
		
		// jalamos el movimiento
		$this->tipo_movi_almacen->find($post["tipo_movimiento"]);
		$correlativo = intval($this->tipo_movi_almacen->get("correlativo"));
		
		$arrProductosKardex = array(); // datos almacen kardex
		
		if($transaction == TRUE)
			$this->db->trans_start();
		
		// recorremos lista de item
		foreach($post["deta_idproducto"] as $k=>$val) {
			// verificamos el stock del producto
			$stock = $this->has_stock($val, $post["deta_idunidad"][$k], floatval($post["deta_cantidad"][$k]), $post["idalmacen"]);
			if($stock !== TRUE) {
				$this->exception("No existe stock para el producto ".$post["deta_producto"][$k].". 
					Stock disponible: ".number_format($stock, 2));
				return false;
			}
			
			$this->producto_unidad->find(array("idproducto"=>$val, "idunidad"=>$post["deta_idunidad"][$k]));
			
			if(empty($post["deta_costo"][$k])) {
				// $post["deta_costo"][$k] = 0;
				$costo = $this->producto->get_precio_costo_unitario($val, $this->get_var_session("idsucursal"));
				$costo = $costo * floatval($this->producto_unidad->get("cantidad_unidad_min"));
				$post["deta_costo"][$k] = $costo;
			}
			
			// registramos el despacho
			$this->despacho->set("idproducto", $val);
			$this->despacho->set("cant_despachado", floatval($post["deta_cantidad"][$k]));
			$this->despacho->set("correlativo", $correlativo);
			$this->despacho->set("idunidad", $post["deta_idunidad"][$k]);
			$this->despacho->set("cantidad_um", $this->producto_unidad->get("cantidad_unidad_min"));
			
			$this->despacho->insert();
			
			$correlativo = $correlativo + 1;
			
			$this->detalle_almacen->set("idproducto", $val);
			$this->detalle_almacen->set("idunidad", $post["deta_idunidad"][$k]);
			$this->detalle_almacen->set("cantidad", floatval($post["deta_cantidad"][$k]));
			$this->detalle_almacen->set("precio_costo", floatval($post["deta_costo"][$k]));
			$this->detalle_almacen->set("iddespacho", $this->despacho->get("iddespacho"));
			$this->detalle_almacen->set("cantidad_um", $this->producto_unidad->get("cantidad_unidad_min"));
			$this->detalle_almacen->insert();
			
			// almacenamos datos para kardex
			$temp = $this->despacho->get_fields();
			$temp["cantidad"] = $temp["cant_despachado"];
			$temp["preciocosto"] = $this->detalle_almacen->get("precio_costo");
			$temp["precioventa"] = $this->detalle_almacen->get("precio_venta");
			$arrProductosKardex[] = $temp;
		}
		
		// ingresamo registro en kardex
		if( ! empty($arrProductosKardex)) {
			// actualizamos el correlativo del tipo movimiento
			$this->tipo_movi_almacen->set("correlativo", $correlativo);
			$this->tipo_movi_almacen->update();
			
			if( ! isset($this->jkardex))
				$this->load_library("jkardex");
			
			$this->jkardex->idtipodocumento = $post["idtipodocumento"];
			$this->jkardex->serie = $post["serie"];
			$this->jkardex->numero = $post["numero"];
			$this->jkardex->observacion = $post["observacion"];
			
			$this->jkardex->referencia("despacho", $this->despacho->get("iddespacho"), $this->get_var_session("idsucursal"), $post["tipo_movimiento"]);
			$this->jkardex->salida();
			// $this->jkardex->calcular_precio_costo();
			$this->jkardex->push($arrProductosKardex);
			$this->jkardex->run();
		}
		
		if($transaction == TRUE)
			$this->db->trans_complete();
		
		if($returnable == TRUE)
			return true;
		
		$this->response("ok");
	}
	
	public function add_stock($returnable = FALSE, $transaction = TRUE) {
		if($this->input->post("tipo") == "S") {
			$this->remove_stock();
			return;
		}
		
		$post = $this->input->post();
		
		$this->load_model("detalle_almacen");
		$this->load_model("almacen.recepcion");
		$this->load_model("tipo_movi_almacen");
		if( ! isset($this->producto))
			$this->load_model("producto");
		if( ! isset($this->producto_unidad))
			$this->load_model("producto_unidad");
		
		// datos default recepcion
		$this->recepcion->set("idcompra", 0);
		$this->recepcion->set("idalmacen", $post["idalmacen"]);
		$this->recepcion->set("tipo_docu", $post["idtipodocumento"]);
		$this->recepcion->set("serie", $post["serie"]);
		$this->recepcion->set("numero", $post["numero"]);
		$this->recepcion->set("observacion", $post["observacion"]);
		$this->recepcion->set("estado", "C");
		$this->recepcion->set("fecha", date("Y-m-d"));
		$this->recepcion->set("hora", date("H:i:s"));
		$this->recepcion->set("idusuario", $this->get_var_session("idusuario"));
		$this->recepcion->set("iddetalle_compra", 0);
		$this->recepcion->set("referencia", "");
		
		// datos default detalle almacen
		$this->detalle_almacen->set("idalmacen", $post["idalmacen"]);
		$this->detalle_almacen->set("tipo", "E");
		$this->detalle_almacen->set("tipo_number", 1);
		$this->detalle_almacen->set("precio_venta", 0);
		$this->detalle_almacen->set("fecha", date("Y-m-d"));
		$this->detalle_almacen->set("tabla", "");
		$this->detalle_almacen->set("idtabla", '0');
		$this->detalle_almacen->set("estado", "A");
		$this->detalle_almacen->set("idrecepcion", "0");
		$this->detalle_almacen->set("iddespacho", "0");
		$this->detalle_almacen->set("idsucursal", $this->get_var_session("idsucursal"));
		
		// jalamos el movimiento
		$this->tipo_movi_almacen->find($post["tipo_movimiento"]);
		$correlativo = intval($this->tipo_movi_almacen->get("correlativo"));
		
		$arrProductosKardex = array(); // datos almacen kardex
		
		if($transaction == TRUE)
			$this->db->trans_start();
		
		// recorremos lista de item
		foreach($post["deta_idproducto"] as $k=>$val) {
			$this->producto_unidad->find(array("idproducto"=>$val, "idunidad"=>$post["deta_idunidad"][$k]));
			
			if(empty($post["deta_costo"][$k])) {
				// $post["deta_costo"][$k] = 0;
				$costo = $this->producto->get_precio_costo_unitario($val, $this->get_var_session("idsucursal"));
				$costo = $costo * floatval($this->producto_unidad->get("cantidad_unidad_min"));
				$post["deta_costo"][$k] = $costo;
			}
			
			// registramos el recepcion
			$this->recepcion->set("idproducto", $val);
			$this->recepcion->set("idunidad", $post["deta_idunidad"][$k]);
			$this->recepcion->set("cant_recepcionada", floatval($post["deta_cantidad"][$k]));
			$this->recepcion->set("correlativo", $correlativo);
			$this->recepcion->set("cantidad_um", $this->producto_unidad->get("cantidad_unidad_min"));
			$this->recepcion->insert();
			
			$correlativo = $correlativo + 1;
			
			$this->detalle_almacen->set("idproducto", $val);
			$this->detalle_almacen->set("idunidad", $post["deta_idunidad"][$k]);
			$this->detalle_almacen->set("cantidad", floatval($post["deta_cantidad"][$k]));
			$this->detalle_almacen->set("precio_costo", floatval($post["deta_costo"][$k]));
			$this->detalle_almacen->set("idrecepcion", $this->recepcion->get("idrecepcion"));
			$this->detalle_almacen->set("cantidad_um", $this->producto_unidad->get("cantidad_unidad_min"));
			$this->detalle_almacen->insert();
			
			// almacenamos datos para kardex
			$temp = $this->recepcion->get_fields();
			$temp["cantidad"] = $temp["cant_recepcionada"];
			$temp["preciocosto"] = $this->detalle_almacen->get("precio_costo");
			$temp["precioventa"] = $this->detalle_almacen->get("precio_venta");
			$arrProductosKardex[] = $temp;
		}
		
		// ingresamo registro en kardex
		if( ! empty($arrProductosKardex)) {
			// actualizamos el correlativo del tipo movimiento
			$this->tipo_movi_almacen->set("correlativo", $correlativo);
			$this->tipo_movi_almacen->update();
			
			if( ! isset($this->jkardex))
				$this->load_library("jkardex");
			
			$this->jkardex->idtipodocumento = $post["idtipodocumento"];
			$this->jkardex->serie = $post["serie"];
			$this->jkardex->numero = $post["numero"];
			$this->jkardex->observacion = $post["observacion"];
			
			$this->jkardex->referencia("recepcion", $this->recepcion->get("idrecepcion"), $this->get_var_session("idsucursal"), $post["tipo_movimiento"]);
			$this->jkardex->entrada();
			// $this->jkardex->calcular_precio_costo();
			$this->jkardex->push($arrProductosKardex);
			$this->jkardex->run();
		}
		
		if($transaction == TRUE)
			$this->db->trans_complete();
		
		if($returnable == TRUE)
			return true;
		
		$this->response("ok");
	}
	
	public function traslado() {
		// print_r($_POST);return;
		$this->load_model("producto");
		$this->load_model("producto_unidad");
		
		$this->db->trans_start();
		
		// salida
		$_POST["tipo"] = "S";
		$_POST["idalmacen"] = $_POST["idalmacen_salida"];
		$_POST["tipo_movimiento"] = 11; //SALIDA POR TRANSFERENCIA ENTRE ALMACENES
		$this->remove_stock(true, false);
		
		// entrada
		$_POST["tipo"] = "E";
		$_POST["idalmacen"] = $_POST["idalmacen_entrada"];
		$_POST["tipo_movimiento"] = 21; //ENTRADA POR TRANSFERENCIA ENTRE ALMACENES
		$this->add_stock();
		
		$this->db->trans_complete(true, false);
		
		$this->response("ok");
	}
	
	public function grilla_producto_popup() {
		$this->load_model("compra.producto");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->producto);
		// $this->datatables->setIndexColumn("idproveedor");
		
		$this->datatables->where('estado', '=', 'A');
		
		$this->datatables->setColumns(array('codigo_producto','descripcion_detallada'));
		// $this->datatables->order_by("fecha_ingreso", "asc");
		
		$this->datatables->setPopup(true);
		
		$table = $this->datatables->createTable(array('Codigo','Item'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	public function conversion() {
		$post = $this->input->post();
		
		$this->load_model("producto");
		$this->load_model("producto_unidad");
		
		$this->db->trans_start();
		
		// asignamos la unidad de medida al producto original, esto no es necesario al parecer
		// $this->producto_unidad->set("idproducto", $post["conversion_idproducto"]);
		// $this->producto_unidad->set("idunidad", $post["conversion_idunidad"]);
		// $this->producto_unidad->set("cantidad_unidad", 1);
		// $this->producto_unidad->set("cantidad_unidad_min", $post["conversion_equivalencia"]);
		// $this->producto_unidad->save(null, false);
		
		// creamos el producto segun la unidad de medida
		if( ! empty($post["resultado_idproducto"])) {
			// el producto existe o el usuario ha seleccionado un producto manualmente
			$this->producto->find($post["resultado_idproducto"]);
			$this->producto->set("idunidad", $post["conversion_idunidad"]);
			$this->producto->set("descripcion", $post["resultado_producto"]);
			$this->producto->set("descripcion_detallada", $post["resultado_producto"]);
			$this->producto->set("idproducto_padre", $post["conversion_idproducto"]);
			$this->producto->update();
		}
		else {
			// el producto no existe, creamos en base al producto original			
			$this->producto->find($post["conversion_idproducto"]);
			$this->producto->set("idunidad", $post["conversion_idunidad"]);
			$this->producto->set("descripcion", $post["resultado_producto"]);
			$this->producto->set("descripcion_detallada", $post["resultado_producto"]);
			$this->producto->set("idproducto_padre", $post["conversion_idproducto"]);
			$this->producto->insert();
		}
		
		$idproducto_nuevo = $this->producto->get("idproducto");
		
		// asignamos la unidad de medida al producto nuevo, la unidad minima y la nueva
		$this->producto_unidad->set("idproducto", $idproducto_nuevo);
		$this->producto_unidad->set("idunidad", $post["conversion_idunidad"]);
		$this->producto_unidad->set("cantidad_unidad", 1);
		$this->producto_unidad->set("cantidad_unidad_min", 1);
		$this->producto_unidad->save(null, false);
		
		// $this->producto_unidad->set("idproducto", $idproducto_nuevo);
		// $this->producto_unidad->set("idunidad", $post["producto_idunidad"]);
		// $this->producto_unidad->set("cantidad_unidad", 1);
		// $this->producto_unidad->set("cantidad_unidad_min", $post["conversion_equivalencia"]);
		// $this->producto_unidad->save(null, false);
		
		// acomodamos los datos
		$_POST["idtipodocumento"] = 0;
		$_POST["tipo_movimiento"] = 99; // OTROS
		$_POST["serie"] = "";
		$_POST["numero"] = "";
		$_POST["observacion"] = "Conversion | Transformacion de item";
		
		// salida del almacen
		$_POST["tipo"] = "S";
		$_POST["idalmacen"] = $post["idalmacen_salida"];
		$_POST["deta_idproducto"] = array($post["conversion_idproducto"]);
		$_POST["deta_idunidad"] = array($post["producto_idunidad"]);
		$_POST["deta_cantidad"] = array($post["conversion_cantidad"]);
		$_POST["deta_producto"] = array($post["conversion_producto"]);
		$this->remove_stock(true, false);
		
		// entrada del almacen
		$_POST["tipo"] = "E";
		$_POST["idalmacen"] = $post["idalmacen_entrada"];
		$_POST["deta_idproducto"] = array($idproducto_nuevo);
		$_POST["deta_idunidad"] = array($post["conversion_idunidad"]);
		$_POST["deta_cantidad"] = array($post["resultado_cantidad"]);
		$this->add_stock(true, false);
		
		
		$this->db->trans_complete(true, false);
		
		$this->response("ok");
	}
	
	public function gen_codigobarras() {
		$data = $this->input->get();
		
		// actualizamos valor del codigo de barras
		$this->load_model("producto");
		$this->producto->update($data);
		$this->producto->find();
		
		$data["path"] = "app/img/capchaimg.png";
		
		// generamos el pdf
		$this->load_library("jbarcode");
		// $this->jbarcode->print_text = true;
		$this->jbarcode->filepath = FCPATH.$data["path"];
		$this->jbarcode->barcode($this->producto->get("codigo_barras"));
		
		// generar el html
		$str = $this->load->view($this->controller."/genbarcode", $data, true);
		echo $str;
	}
	
	public function array_head(){
		$whit_item		= 8;
		$whit_prod		= 80;
		$whit_marca		= 19;
		$whit_um		= 10;
		$whit_cant		= 12;
		$whit_pv		= 17;
		$whit_imvt		= 20;
		$whit_cst		= 18;
		$whit_cstvta	= 21;
		$total_ancho	= $whit_item+$whit_prod+$whit_um+$whit_cant+$whit_pv+$whit_imvt+$whit_cst+$whit_cstvta+$whit_marca;

		$cabecera = array( 'idproducto' => array('COD',$whit_item,$whit_item*100/$total_ancho,0,'L')
							,'producto' => array('PRODUCTO',$whit_prod,$whit_prod*100/$total_ancho,0,'L')
							,'marca' => array('MARCA',$whit_marca,$whit_marca*100/$total_ancho,0,'L')
							,'um' => array('UM',$whit_um,$whit_um*100/$total_ancho,0,'L')
							,'stock' => array('STOCK',$whit_cant,$whit_cant*100/$total_ancho,0,'R')
							,'existencia' => array('EXIST',$whit_pv,$whit_pv*100/$total_ancho,0,'R')
							,'dif_neg' => array('DIF. NEG.',$whit_pv,$whit_pv*100/$total_ancho,0,'R')
							,'dif_pos' => array('DIF. POS.',$whit_pv,$whit_pv*100/$total_ancho,0,'R')
							,'observacion' => array('OBS',$whit_cstvta,$whit_cstvta*100/$total_ancho,1,'L')
						);
						
		return $cabecera;
	}
	
	public function get_data(){
		$q=$this->db->query("SELECT *
							,'' observacion 
							,'' dif_neg
							,'' dif_pos
							,'' existencia
							FROM almacen.view_stock 
							{$this->condicion()}
							ORDER BY producto");

		return $q->result_array();
	}
	
	public function imprimir(){
		set_time_limit(0);
		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","almacen.almacen"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		// if(!empty($_REQUEST['fechainicio'])){
			// if(!empty($_REQUEST['fechafin']))
				// $this->pdf->SetTitle(utf8_decode("REPORTE DE INTENVARIO DE".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio'])), 11, null, true);
			// else
				// $this->pdf->SetTitle(utf8_decode("REPORTE DE INTENVARIO  DE ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		// }else
			$this->pdf->SetTitle("REPORTE DE INTENVARIO  ", 11, null, true);

		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);
		$this->pdf->setFillColor(249, 249, 249);
        $this->pdf->SetDrawColor(204, 204, 204);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		
		$this->pdf->SetFont('Arial','B',10);
		
		if(!empty($_REQUEST['idcategoria']) && $_REQUEST['idcategoria']!='T'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"CATEGORIA",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->categoria->find($_REQUEST['idcategoria']);
			$this->pdf->Cell(5,3,$this->categoria->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idmarca']) && $_REQUEST['idmarca']!='T'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"MARCA",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->marca->find($_REQUEST['idmarca']);
			$this->pdf->Cell(5,3,$this->marca->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idmodelo']) && $_REQUEST['idmodelo']!='T'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"MODELO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->modelo->find($_REQUEST['idmodelo']);
			$this->pdf->Cell(5,3,$this->modelo->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idalmacen'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"ALMACEN",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->almacen->find($_REQUEST['idalmacen']);
			$this->pdf->Cell(5,3,$this->almacen->get("descripcion"),0,1,'L');
		}
		$this->pdf->Ln();
		
		$cols_h  = array();
		$name_h  = array();
		$pos_h   = array();
		$width_h = array();
		foreach($this->array_head() as $k=>$v){
			$cols_h[] =$k;
			$name_h[] =$v[0];
			$pos_h[]  =$v[4];
			$width_h[]=$v[1];
		}
		
		$this->pdf->SetFont('Arial','B',7.5);
		foreach ($this->array_head() as $k => $v) {
			$this->pdf->Cell($v[1],5,($v[0]),1,$v[3],'C');
		}
		
		foreach($this->get_data() as $key=>$val){
			$this->pdf->SetFont('Arial','',7.1);
			$values = array();
			// foreach ($this->array_head() as $k => $vv) {
				foreach($cols_h as $f){
					if($f=='stock' && is_numeric($val[$f])){
						$val[$f] = number_format($val[$f],$this->decimales);
					}
					$values[] = utf8_decode(((''.$val[$f])));
				}
			// }
			$this->pdf->SetWidths($width_h);
			$this->pdf->Row($values, $pos_h, "Y", "Y");		
		}
		
		$this->pdf->Output();
	}
	
	public function exportar(){
		set_time_limit(0);
		$this->load->library("phpexcel");
		
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE INVENTARIO",true);
		
		$col = 9;
		if(!empty($_REQUEST['idcategoria']) && $_REQUEST['idcategoria']!='T'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'CATEGORIA : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("general.categoria");
			$this->categoria->find($_REQUEST['idcategoria']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->categoria->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idmarca']) && $_REQUEST['idmarca']!='T'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'MARCA : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("general.marca");
			$this->marca->find($_REQUEST['idmarca']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->marca->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idmodelo']) && $_REQUEST['idmodelo']!='T'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'MODELO : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("general.modelo");
			$this->modelo->find($_REQUEST['idmodelo']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->modelo->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idalmacen'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'ALMACEN : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("almacen.almacen");
			$this->almacen->find($_REQUEST['idalmacen']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->almacen->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		/************************** CABECERA *****************************************/
		$alfabeto = 65;
		$col++;
		$styleHead = array(
			  'borders' => array(
				 'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN,
						'color' => array('argb' => 'FF000000'),
				 ),
			),
		);
		
		foreach ($this->array_head() as $key => $v) {
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->applyFromArray(
				array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $v[0]);
			
			$alfabeto++;
		}
		
		$col++;
		/************************** CABECERA *****************************************/
		
		
		/************************** CUERPO *****************************************/
		$rows 		= $this->get_data();
		foreach($rows as $key=>$val){
			$alfabeto = 65;
			foreach($this->array_head() as $k=>$v){
				if($k=='stock' && is_numeric($val[$k])){
					$val[$k] = number_format($val[$k],2,'.','');
				}
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, ($val[$k]));
				$alfabeto++;
			}
			$col++;
		}
		/************************** CUERPO *****************************************/
		
		$alfabeto = 67;
		foreach($this->array_head() as $k=>$v){
			$Oexcel->getActiveSheet()->getColumnDimension(chr($alfabeto))->setAutoSize(true);
			$alfabeto++;
		}
		
		$filename='reporte_inventario'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
	
	public function get_unidades() {
		$sql = "select p.idunidad, u.descripcion||' ('||u.abreviatura||')' as descripcion, u.abreviatura
			from compra.producto_unidad p
			join compra.unidad u on u.idunidad = p.idunidad
			where p.idproducto = ?";
		$query = $this->db->query($sql, array(intval($this->input->post("idproducto"))));
		
		$this->load->library('combobox');
		$this->combobox->addItem($query->result_array(), array("idunidad", "descripcion", "abreviatura"));
		$this->response($this->combobox->getAllItems());
	}
}
?>