<?php

include_once "Controller.php";

class Pagoproveedor extends Controller {
	
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
	public function form() {
		$data["controller"] = $this->controller;
		
		$this->load->library('combobox');
		// combo Tipo Pago
		$this->combobox->setAttr("id","idtipopago");
		$this->combobox->setAttr("name","idtipopago");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->combobox->setAttr("required","");
		$this->db->select('idtipopago,descripcion');
		$query = $this->db->where("estado","A")->where("mostrar_en_pagoproveedor","S")->order_by("descripcion")->get("venta.tipopago");
		$this->combobox->addItem($query->result_array());
		
		$data['tipopago'] = $this->combobox->getObject();
		$this->combobox->init();
		/*---------------------------------------------------------------------*/
		$this->combobox->setAttr("id","idbanco");
		$this->combobox->setAttr("name","idbanco");
		$this->combobox->setAttr("class","form-control input-xs");
		// $this->combobox->setAttr("required","");
		$this->db->select('idbanco,banco');
		$query = $this->db->where("estado","A")->order_by("banco")->get("general.banco");
		$this->combobox->addItem($query->result_array());
		
		$data['banco'] = $this->combobox->getObject();
		$this->combobox->init();
		/*---------------------------------------------------------------------*/
		$this->combobox->setAttr("id","idproveedor");
		$this->combobox->setAttr("name","idproveedor");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select('idproveedor,proveedor');
		$query = $this->db->where("cancelado","N")->order_by("proveedor")->get("compra.proveedor_deuda_view");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['proveedor'] = $this->combobox->getObject();
		$this->combobox->init();
		/*---------------------------------------------------------------------*/
		
		$data['grid'] = $this->grid();
		
		$data["modal_pago"] = $this->get_form_pago("pagoproveedor", false);
		
		$this->css("plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox");
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		
		$this->css("plugins/chosen/chosen");
		$this->js("plugins/chosen/chosen.jquery");
		// $this->js("form/caja/single_pay");

		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return null;
	}
	
	public function grid(){		
		$this->load_model('compra.cronograma_pago_view');

		$this->load->library('datatables');

		//////////////////////////////////////
		$data = false;


		// $codcaja = ($data !== false) ? $data[0]['idcaja'] : '-1';
		//////////////////////////////////////
		
		$this->datatables->setModel($this->cronograma_pago_view);

		$this->datatables->setIndexColumn("idcompra");
		
		$this->datatables->where('estado', '=', 'A');

		$this->datatables->setColumns(array('idcompra','comprobante','fecha_emision','fecha_vencimiento','letra','abreviatura_moneda','monto_letra'));
		$this->datatables->order_by('fecha_vencimiento','asc');
		// columnas de la tabla, si no se envia este parametro, se muestra el
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			array('', '2%')
			,array('Comprobante', '6%')
			,array('Fecha Emic', '8%')
			,array('Fecha Venc', '8%')
			,array('Letra', '4%')
			,array('Moneda', '2%')
			,array('Importe', '8%')
		);

		$this->datatables->setCallback('callbackDeudas');

		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName);

		$script = "<script>".$this->datatables->createScript()."</script>";

		// agregamos los css para el dataTables
		$this->css('plugins/dataTables/dataTables.bootstrap');
		$this->css('plugins/dataTables/dataTables.responsive');
		$this->css('plugins/dataTables/dataTables.tableTools.min');

		// agregamos los scripts para el dataTables
		$this->js('plugins/dataTables/jquery.dataTables');
		$this->js('plugins/dataTables/dataTables.bootstrap');
		$this->js('plugins/dataTables/dataTables.responsive');
		$this->js('plugins/dataTables/dataTables.tableTools.min');		

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
	
	public function capture_temp(){
		$fields = $this->input->post();
		
		$query = $this->db->query("SELECT *FROM compra.cronograma_pago_view WHERE idcompra='{$fields['id']}' AND letra='{$fields['letra']}' AND estado='A'; ");
		
		$res = $query->result_array();
		
		$this->response($res);
	}
	
	public function return_temp(){
		$fields = $this->input->post();
		
		// $query = $this->db->query("SELECT *FROM compra.pago_compra_view WHERE idcompra='{$fields['id']}' AND letra='{$fields['letra']}' AND estado='A'; ");
		$query = $this->db->query("SELECT pg_v.*,cp_v.fecha_vencimiento 
												FROM compra.pago_compra_view pg_v 
												JOIN compra.cronograma_pago_view cp_v ON cp_v.idcompra=pg_v.idcompra AND cp_v.letra=pg_v.letra AND cp_v.estado='A'
												WHERE pg_v.idcompra='{$fields['id']}' AND pg_v.letra='{$fields['letra']}' AND pg_v.estado='A';  ");
		
		$res = $query->result_array();
		
		$this->response($res);
	}
	
	public function guardar(){
		$idusuario = $this->get_var_session("idusuario");
		$this->load_model("compra.pago_compra");
		$this->load_model("compra.cronograma_pago");

		$fields = $this->input->post();
			
		$this->db->trans_start(); // inciamos transaccion
		foreach($fields['idcompra'] as $key=>$val){
			$data["idcompra"] 				= $val;
			$data["letra"] 					= $fields["letra"][$key];
			$data["monto"] 					= $fields["monto"][$key];
			$data["fecha"] 					= (!empty($fields["fecha_deposito"])) ? $fields["fecha_deposito"]: date("Y-m-d");
			$data["idmoneda"] 				= $fields["idmoneda"][$key];
			$data["cambio_moneda"] 			= $fields["cambio_moneda"][$key];
			$data["descripcion"] 			= (!empty($fields["descripcion"][$key])) ? $fields["descripcion"][$key] : null;
			$data["idusuario"] 				= $idusuario;
			$data["fecha_registro"] 		= date("Y-m-d");
			$data["estado"] 					= "A";
			$data["monto_notacredito"] = (!empty($fields["monto_notacredito"][$key])) ? $fields["monto_notacredito"][$key] : 0;
			$data["doc_notacredito"] 	= (!empty($fields["doc_notacredito"][$key])) ? $fields["doc_notacredito"][$key] : null;

			$status = $this->pago_compra->insert($data);
			
			if($status){
				$data1 = $this->cronograma_pago->find(array("idcompra"=>$data["idcompra"], "letra"=>$data["letra"] ));
				$data1['fecha_pago']	    =	(!empty($fields["fecha_deposito"])) ? $fields["fecha_deposito"]: date("Y-m-d");
				$data1["cancelado"] 		= "S";
				$data1["idpagocompra"] = $status;
				// $data1["saldo"] = $status;//No se que valor va aqui
				$this->cronograma_pago->update($data1);
			}
		}
		
		$this->db->trans_complete(); // finalizamos transaccion
			
		$this->response($fields);
	}
	
	public function eliminar_letra(){
		$idusuario = $this->get_var_session("idusuario");
		$this->load_model("compra.pago_compra");
		$this->load_model("compra.cronograma_pago");

		$fields = $this->input->post();
		
		$this->db->trans_start(); // inciamos transaccion
		foreach($fields['idcompra'] as $key=>$val){
			$data1 = $this->cronograma_pago->find(array("idcompra"=>$val, "letra"=>$fields["letra"][$key] ));
			$data1["estado"] = 'I';
				// $data1["saldo"] = $status;//No se que valor va aqui
			$this->cronograma_pago->update($data1);
		}
		$this->db->trans_complete(); // finalizamos transaccion
		$this->response($fields);
	}
	
	public function anular_pago(){
		$idusuario = $this->get_var_session("idusuario");
		$this->load_model("compra.pago_compra");
		$this->load_model("compra.cronograma_pago");

		$fields = $this->input->post();
		$idcompra = null;
		$this->db->trans_start(); // inciamos transaccion
		foreach($fields['idpagocompra'] as $key=>$val){
			$data1 = $this->pago_compra->find(array("idpagocompra"=>$val));
			$data1["estado"] = 'I';
			$this->pago_compra->update($data1);
			
			$data2 = $this->cronograma_pago->find(array("idcompra"=>$fields["idcompra"][$key], "letra"=>$fields["letra"][$key] ));
			$data2["fecha_pago"] = null;
			$data2["idpagocompra"] = null;
			$data2["cancelado"] 		= "N";
			$this->cronograma_pago->update($data2);
		}
		
		// $this->db->query("UPDATE ");
		
		$this->db->trans_complete(); // finalizamos transaccion
		$this->response($fields);
	}
	
	public function buscar_proveedor_deuda(){
		$fields = $this->input->post();
		$s = $this->db->query("SELECT idproveedor, proveedor FROM compra.proveedor_deuda_view WHERE cancelado='{$fields['cancelado']}';");
		$res = $s->result_array();
		
		$this->response($res);
	}
}
?>