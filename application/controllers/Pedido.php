<?php

include_once "Controller.php";

class Pedido extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Pedidos");
		$this->set_subtitle("Lista de pedidos");
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
		
		// combo almacen
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idalmacen"
				,"name"=>"idalmacen"
				,"class"=>"form-control"
				,"required"=>""
			)
		);
		$query = $this->db->select('idalmacen, descripcion')->where("estado", "A")
			->where("idsucursal", $this->get_var_session("idsucursal"))->get("almacen.almacen");
		$this->combobox->addItem($query->result_array());
		if( isset($data["pedido"]["idalmacen"]) ) {
			$this->combobox->setSelectedOption($data["pedido"]["idalmacen"]);
		}
		$data["almacen"] = $this->combobox->getObject();
		
		//combo tipo pedito interno o pedido a proveedor
		$this->combobox->init();
		$this->combobox->setAttr(
			array( "id"=>"idtipo_pedido"
				,"name"=>"idtipo_pedido"
				,"class"=>"form-control input-sm"
				,"required"=>"")
			);
		$this->db->select('idtipo_pedido, descripcion');
		$query = $this->db->where("estado", "A")->get("compra.tipo_pedido");
		$this->combobox->addItem($query->result_array());
		
		if( isset($data["pedido"]["idtipo_pedido"]) ) {
			$this->combobox->setSelectedOption($data["pedido"]["idtipo_pedido"]);
		}
		
		$data["tipo_pedido"] = $this->combobox->getObject();

		$es_nuevo = "true";
		if( isset($data["pedido"]["idpedido"]) ) {
			$es_nuevo = "false";
		}
		$this->js("<script>var _es_nuevo_".$this->controller."_ = $es_nuevo;</script>", false);
		
		if( isset($data["detalle_pedido"]) ) {
			$this->js("<script>var data_detalle = ".json_encode($data["detalle_pedido"]).";</script>", false);
		}
		
		$this->combobox->init(); // un nuevo combo
		
		$data["controller"] = $this->controller;
		
		$this->load_controller("producto");
		$data["form_producto"] = $this->producto_controller->form(null, "", true);
		$data["form_producto_unidad"] = $this->producto_controller->form_unidad_medida(null, "", true);
		
		// form proveedor
		$this->load_controller("proveedor");
		$data["form_proveedor"] = $this->proveedor_controller->form(null, "prov_", true);
		
		// $this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
		$this->css("plugins/datapicker/datepicker3");
		// $this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->js('form/'.$this->controller.'/form');
		$this->js('form/producto/modal');
		$this->js('form/proveedor/modal');
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	public function filtros_grilla($aprobado = "N", $atendido = "N") {
		$this->load_library("combobox");
		
		$html = '<div class="row">';
		
		// div y combobox aprobado
		$this->combobox->setAttr("filter", "aprobado");
		$this->combobox->setAttr("class", "form-control");
		$this->combobox->addItem("S", "APROBADO");
		$this->combobox->addItem("N", "NO APROBADO");
		$this->combobox->setSelectedOption($aprobado);
		
		$html .= '<div class="col-sm-4"><div class="form-group">';
		$html .= '<label class="control-label">Aprobado</label>';
		$html .= $this->combobox->getObject();;
		$html .= '</div></div>';
		
		// div y combobox atendido
		$this->combobox->removeItems();
		$this->combobox->setAttr("filter", "atendido");
		$this->combobox->addItem("S", "ATENDIDO");
		$this->combobox->addItem("N", "NO ATENDIDO");
		$this->combobox->setSelectedOption($atendido);
		
		$html .= '<div class="col-sm-4"><div class="form-group">';
		$html .= '<label class="control-label">Atendido</label>';
		$html .= $this->combobox->getObject();;
		$html .= '</div></div>';
		
		$html .= '</div>';
		
		$this->set_filter($html);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model($this->controller);
		$this->load->library('datatables');
		
		$aprobado = "N";
		$atendido = "N";
		
		$this->datatables->setModel($this->pedido);
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('aprobado', '=', $aprobado);
		$this->datatables->where('atendido', '=', $atendido);
		
		$this->datatables->setColumns(array('idpedido','fecha','descripcion'));
		$this->datatables->order_by('idpedido', "desc");
		
		$columnasName = array(
			'Nro'
			,'Fecha de Emision'
			,'Descripci&oacute;n'
		);

		$table = $this->datatables->createTable($columnasName);
		$script = "<script>".$this->datatables->createScript()."</script>";
		$this->js($script, false);
		
		$this->filtros_grilla($aprobado, $atendido);
		
		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Pedido");
		$this->set_subtitle("");
		
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id, $tabkey="") {
		$this->load_model(array("pedido", "detalle_pedido"));
		$data["tabkey"] = $tabkey;
		$data["pedido"] = $this->pedido->find($id);
		
		if( ! empty($data["pedido"]["idproveedor"])) {
			$this->load_model("compra.proveedor");
			$data["proveedor"] = $this->proveedor->find($data["pedido"]["idproveedor"]);
		}
		
		$data["detalle_pedido"] = $this->detalle_pedido->get_item_pedido($id);
		
		$this->set_title("Modificar Pedido");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/* public function pedido_detalle($id) {
		$this->load_model("pedido");
		$data["pedido"] = $this->pedido->find($id);

		
		$this->set_title("Detalle del Pedido / ");
		$this->set_subtitle($data["pedido"]["descripcion"]);

		$this->set_content($this->form_pedido_detalle($data,$id));
		$this->index("content");
	} */
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		
		$fields = $this->input->post();
		$fields['idsucursal'] = $this->get_var_session("idsucursal");
		$fields['idusuario'] = $this->get_var_session("idusuario");
		$fields['fecha'] =  date('Y-m-d H:i:s');
		$fields['estado'] = "A";
		$fields['atendido'] = "N";
		$fields['aprobado'] = "N";
		if(empty($fields["idproveedor"]))
			$fields["idproveedor"] = 0;
		
		$this->db->trans_start(); // inciamos transaccion
		
		if(empty($fields["idpedido"])) {
			$idpedido = $this->pedido->insert($fields);
		}
		else {
			$idpedido = $fields["idpedido"];
			$datos = $this->pedido->find($idpedido);
			$fields["aprobado"] = $datos["aprobado"];
			$this->pedido->update($fields);
		}
		
		// detalle pedido
		$this->load_model("detalle_pedido");
		$this->db->query("UPDATE compra.detalle_pedido SET estado=? WHERE idpedido=?", array("I", $idpedido));		
		
		foreach($fields["deta_idproducto"] as $key=>$val) {
			$data = $this->detalle_pedido->find(array("idpedido"=>$idpedido, "idproducto"=>$val, "idunidad"=>$fields["deta_idunidad"][$key], "estado"=>"I"));
			if($data != null) {
				$data["estado"] = 'A';
				$data["cantidad"] = floatval($fields["deta_cantidad"][$key]);
				$data['atendido'] = "N";
				$this->detalle_pedido->update($data);
			}
			else {
				$data["idpedido"] = $idpedido;
				$data["idproducto"] = $val;
				$data["estado"] = 'A';
				$data["idunidad"] = $fields["deta_idunidad"][$key];
				$data["cantidad"] = floatval($fields["deta_cantidad"][$key]);
				$data['atendido'] = "N";
				$this->detalle_pedido->insert($data);
			}
		}
		
		$this->db->trans_complete(); // finalizamos transaccion
		
		$this->response($this->pedido->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		$fields['idpedido'] = $id;
		$fields['estado'] = "I";
		$this->pedido->update($fields);
		
		$this->response($fields);
	}
	
	public function aprobar_pedido($id){
		$this->load_model($this->controller);
		
		$this->pedido->find($id);
		
		$aprobado = ($this->pedido->get("aprobado") == "N")?"S":"N";
		
		$this->pedido->set("aprobado", $aprobado);
		$this->pedido->update();
		
		$this->response($this->pedido->get_fields());
	}
	
	public function grilla_popup() {
		$this->load_model("compra.pedido_view");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->pedido_view);
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->where('aprobado', '=', 'S');
		$this->datatables->where('atendido', '=', 'N');
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));
		$this->datatables->setColumns(array('idpedido','fecha','descripcion'));
		$this->datatables->setPopup(true);
		$this->datatables->setSubgrid("cargarDetalle", true);
		
		$table = $this->datatables->createTable(array('Nro','Fecha de Emision','Descripci&oacute;n'));
		$script = "<script>".$this->datatables->createScript("", false)."</script>";
		
		$this->response($script.$table);
	}
	
	public function get_detalle_pedido() {
		$this->load_model("pedido");
		
		$post = $this->input->post();
		
		$params = array("d.idpedido"=>$post["idpedido"], "d.estado"=>'A', "d.atendido"=>'N');
		if(!empty($post["idproducto"])) {
			$params["d.idproducto"] = $post["idproducto"];
		}
		
		$prods = $this->pedido->get_detalle($params);
		$this->response($prods);
	}
	
	public function cabecera_detalle(){
		return array('item'=> array('ITEM',10,0,'L')
					,'producto' => array('PRODUCTO',140,0,'L')
					,'unidad' => array('UNIDAD',20,0,'L')
					,'cantidad' => array('CANTIDAD',20,1,'R')
				);
	}
	
	public function detalle_body($id=0){
		$q = $this->db->query("SELECT prod.descripcion producto
								,u.descripcion unidad
								,d.cantidad 
								FROM compra.detalle_pedido d
								JOIN compra.pedido p ON p.idpedido=d.idpedido
								JOIN compra.unidad u ON u.idunidad=d.idunidad
								JOIN compra.producto prod ON prod.idproducto=d.idproducto
								WHERE d.idpedido=$id");
		return $q->result_array();
	}
	
	public function imprimir(){
		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","compra.tipo_pedido","seguridad.view_usuario","compra.pedido"));
		$this->empresa->find($this->get_var_session("idempresa"));
		$this->pedido->find($_REQUEST['id']);
		$fechas = explode(" ",$this->pedido->get("fecha"));
		$fecha = '';
		if(!empty($fechas))
			$fecha = fecha_es($fechas[0])." ".date("g:i:s a",strtotime($fechas[1]));
		
		$aprobado = 'NO';
		if($this->pedido->get("aprobado")=='S')
			$aprobado = 'SI';
		
		$atendido = 'NO';
		if($this->pedido->get("atendido")=='S')
			$atendido = 'SI';
		
		$this->view_usuario->find($this->pedido->get("idusuario"));
		$this->tipo_pedido->find($this->pedido->get("idtipo_pedido"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		$this->pdf->SetTitle(utf8_decode("DETALLE DE PEDIDO N° {$_REQUEST['id']}"), 11, null, true);

		$this->pdf->AliasNbPages(); // para el conteo de paginas
		// $this->pdf->SetLeftMargin(4);
		$this->pdf->setFillColor(249, 249, 249);
        $this->pdf->SetDrawColor(204, 204, 204);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('TIPO PEDIDO'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->tipo_pedido->get("descripcion")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('FECHA EMISION'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($fecha),0,0,'L');
		$this->pdf->Ln();
		
		$idprov = $this->pedido->get("idproveedor");
		if( ! empty($idprov)) {
			$this->load_model("compra.proveedor");
			$this->proveedor->find($this->pedido->get("idproveedor"));
			
			$this->pdf->SetFont('Arial','B',9);
			$this->pdf->Cell(30,6,utf8_decode('PROVEEDOR'),0,0,'L');
			$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
			$this->pdf->SetFont('Arial','',9);
			$this->pdf->Cell(130,6,utf8_decode($this->proveedor->get("nombre")),0,0,'L');
			$this->pdf->Ln();
		}
		
		// $this->pdf->SetFont('Arial','B',9);
		// $this->pdf->Cell(30,6,utf8_decode('ALMACEN'),0,0,'L');
		// $this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		// $this->pdf->SetFont('Arial','',9);
		// $this->pdf->Cell(130,6,utf8_decode('--'),0,0,'L');
		// $this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('ATENDIDO'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($atendido),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('APROBADO'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($aprobado),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('USUARIO'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->view_usuario->get("user_nombres")),0,0,'L');
		$this->pdf->Ln();
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(30,6,utf8_decode('DESCRIPCION'),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($this->pedido->get("descripcion")),0,0,'L');
		$this->pdf->Ln(10);
		
		$this->pdf->SetFont('Arial','B',9);
		foreach ($this->cabecera_detalle() as $key => $val) {
			$this->pdf->Cell($val[1],6,$val[0],1,$val[2],$val[3]);
		}
		
		$this->pdf->SetFont('Arial','',8.5);
		$i = 1;
		foreach($this->detalle_body($_REQUEST['id']) as $k=>$v){
			$v["item"] = $i;
			foreach ($this->cabecera_detalle() as $key => $val) {
				$this->pdf->Cell($val[1],4,utf8_decode($v[$key]),1,$val[2],$val[3]);
			}
			$i++;
		}
		$this->pdf->Output();
	}
}
?>