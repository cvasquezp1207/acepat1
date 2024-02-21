<?php

include_once "Controller.php";

class Pedido_compra extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Pedidos reposicion de compra");
		$this->set_subtitle("");
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
		
		// combo sucursal
		$query = $this->db->where("estado", "A")->where("idempresa", $this->get_var_session("idempresa"))->get("seguridad.sucursal");
		$this->combobox->addItem($query->result_array(), array("idsucursal", "descripcion"));
		$this->combobox->setSelectedOption($this->get_var_session("idsucursal"));
		$data["combosucursal"] = $this->combobox->getAllItems();
		
		// combo tipo pedido
		$query = $this->db->where("estado", "A")->get("venta.combobanco");
		$this->combobox->init();
		$this->combobox->addItem($query->result_array(), array("entidadbancaria", "entidadbancaria"));
		$data["combotipopedido"] = $this->combobox->getAllItems();
		
		
		// combo tipo pedido
		$query = $this->db->where("estado", "A")->get("compra.comboproveedor");
		$this->combobox->init();
		$this->combobox->addItem($query->result_array(), array("descripcion", "descripcion"));
		$data["combotipotercero"] = $this->combobox->getAllItems();
		
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return null;
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
	
	// inet_client_addr, inet_client_port, inet_server_addr, inet_server_port, network, netmask
	public function get() {
		$post = $this->input->post();
		
		//$reposicion = intval($post["reposicion"]);
		//$seguridad = intval($post["seguridad"]);
		//$para = intval($post["para"]);
		$current_sucursal = $this->get_var_session("idsucursal");
		$idsucursal = ( ! empty($post["idsucursal"])) ? intval($post["idsucursal"]) : $current_sucursal;
		$descripcion = empty($post["descripcion"]) ? "" : $post["descripcion"];
		//$idsucursal = intval($post["idsucursal"]);
		$entidadbancaria = empty($post["entidadbancaria"]) ? "" : $post["entidadbancaria"];

		$fi =  empty($post["fecha_i"]) ? "" : $post["fecha_i"];
		$ff =  empty($post["fecha_f"]) ? "" : $post["fecha_f"];
		
		$this->db->query("delete from compra.temp_pedido where idsucursal=?", array($current_sucursal));
		
		// insertamos los datos
		$sql = "insert into compra.temp_pedido(idproducto, iddeuda,nro_credito,idproveedor,monto,amortizacion,saldo,idsucursal,serie,numero, id_referencia)
			select 
			a.iddeuda
			, a.iddeuda
			, a.nro_credito
			, a.idproveedor
			, a.monto 
			, coalesce(compra.fn_amortiza(b.id_referencia,a.idsucursal),0)
			, a.monto + coalesce(compra.fn_amortiza(b.id_referencia,a.idsucursal),0)
			,{$idsucursal}
			, case when  b.serie is null then cast (a.idsucursal ||  to_char(NOW(),'yyyymmdd') as varchar) else b.serie  end 
			, case when b.numero is null then cast(cast(a.idsucursal ||  to_char(NOW(),'hhmmss') as integer)+a.iddeuda as varchar) else b.numero end 
			, case when b.id_referencia is null then cast(a.idproveedor||cast(a.iddeuda as varchar)||to_char(NOW(),'hh') as Integer) else b.id_referencia end 
			from 
			compra.deuda a , compra.letra b, compra.proveedor c, venta.cliente d
			where  a.iddeuda = b.iddeuda 
			and   a.idproveedor =  c.idproveedor 
			and   c.idcliente =  d.idcliente
			and a.idsucursal='{$idsucursal}'
			and a.descripCion='{$descripcion}'
			and d.entidadbancaria='{$entidadbancaria}'";
		
		if( ! empty($fi)) {
			$sql .= " and a.fecha_deuda >= '{$fi}'";
		}
		if( ! empty($ff)) {
			$sql .= " and a.fecha_deuda <= '{$ff}'";
		}
		
			
			// echo $sql; exit;
		//$this->db->query($sql);
		
		// hacemos el calculo y actualizamos
		/*
		$sql = "update compra.temp_pedido set 
			stock_seguro = round((promedio_ventas * tiempo_reposicion)::numeric, 2), 
			pp = round((promedio_ventas * (tiempo_reposicion + tiempo_seguridad))::numeric, 2), 
			critico = case when stock < (promedio_ventas * tiempo_reposicion) then 2 else 1 end, 
			sugerido = round(((promedio_ventas * pedido_para) + (promedio_ventas * tiempo_reposicion))::numeric, 2), 
			cantidad = floor((promedio_ventas * pedido_para) + (promedio_ventas * tiempo_reposicion)),
			valor_compra = round((floor((promedio_ventas * pedido_para) + (promedio_ventas * tiempo_reposicion)) * precio_compra)::numeric, 2) 
			where idsucursal=?";// AND floor((promedio_ventas * pedido_para) + (promedio_ventas * tiempo_reposicion))";
			
			*/
		$this->db->query($sql, array($current_sucursal));
		
		$res["idsucursal"] = $idsucursal;
		$res["proveedor"] = $this->get_proveedor(0, 20, true);
		$res["producto"] = $this->get_producto(0, 20, true);
		
		$this->response($res);
	}
	
	public function get_proveedor($page=0, $limit=20, $return=false) {
		$post = $this->input->post();
		if( ! empty($post["page"]))
			$page = intval($post["page"]);
		if( ! empty($post["limit"]))
			$limit = intval($post["limit"]);
		$current_sucursal = $this->get_var_session("idsucursal");
		$idsucursal = ( ! empty($post["idsucursal"])) ? intval($post["idsucursal"]) : $current_sucursal;
		$offset = $page * $limit;
		/*
		$sql = "select p.idproveedor, p.nombre, coalesce(count(t.idproducto),0) as cantidad
			from compra.detalle_compra d
			join compra.compra c on c.idcompra=d.idcompra and c.estado='A' and c.idsucursal={$idsucursal}
			join compra.proveedor p on p.idproveedor=c.idproveedor
			left join compra.temp_pedido t on t.idproducto=d.idproducto and t.critico=2 and t.idsucursal={$current_sucursal}
			where d.estado='A' and d.idproducto in (
				select distinct idproducto from compra.temp_pedido where idsucursal={$current_sucursal}
			)";
		if( ! empty($post["query"])) {
			$q = preg_replace("/\s+/", "%", trim($post["query"]));
			$sql .= " and p.nombre ilike '%{$q}%'";
		}
		$sql .= " group by p.idproveedor, p.nombre
			order by cantidad desc, nombre limit {$limit} offset {$offset*/
			$sql = "select idproveedor, nombre
			from compra.proveedor limit {$limit}";
			//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data["page"] = $page;
		$data["more"] = ($query->num_rows() >= $limit);
		$data["rows"] = $query->result_array();
		
		if($return === true)
			return $data;
		
		$this->response($data);
	}
	
	public function get_producto($page=0, $limit=1000, $return=false) {
		$post = $this->input->post();
		if( ! empty($post["page"]))
			$page = intval($post["page"]);
		if( ! empty($post["limit"]))
			$limit = intval($post["limit"]);
		$current_sucursal = $this->get_var_session("idsucursal");
		$idsucursal = ( ! empty($post["idsucursal"])) ? intval($post["idsucursal"]) : $current_sucursal;
		$offset = $page * $limit;
		
		$sql = "";
		/*
		if(empty($post["idproveedor"])) {
			$sql = "select t.idproducto, p.descripcion_detallada as producto, t.stock, t.stock_seguro, 
				t.pp, t.critico, t.promedio_ventas, t.sugerido, t.cantidad, t.valor_compra
				from compra.temp_pedido t
				join compra.producto p on p.idproducto=t.idproducto
				where t.idsucursal={$current_sucursal}";
		}
		else {
			$sql = "select distinct t.idproducto, p.descripcion_detallada as producto, t.stock, 
				t.stock_seguro, t.pp, t.critico, t.promedio_ventas, t.sugerido, t.cantidad, t.valor_compra
				from compra.temp_pedido t
				join compra.producto p on p.idproducto=t.idproducto
				join compra.detalle_compra d on d.idproducto=t.idproducto and d.estado='A'
				join compra.compra c on c.idcompra=d.idcompra and c.idsucursal={$idsucursal} 
				and c.idproveedor=".intval($post["idproveedor"])."
				where   t.idsucursal={$current_sucursal}";
		}
		
		if( ! empty($post["query"])) {
			$q = preg_replace("/\s+/", "%", trim($post["query"]));
			$sql .= " and p.descripcion_detallada ilike '%{$q}%'";
		}
		$sql .= " order by critico desc, producto limit {$limit} offset {$offset}";*/
		
		$sql ="select 
		a.iddeuda deuda
		,b.nro_credito stock
		,b.fecha_deuda stock_seguro
		,c.nombre pp
		, a.monto critico
		, a.amortizacion promedio_ventas
		, a.saldo sugerido
		from compra.temp_pedido a, compra.deuda b, compra.proveedor c
		where a.iddeuda = b.iddeuda and a.idproveedor = c.idproveedor limit {$limit} offset {$offset}";

		//echo $sql; exit;
		$query = $this->db->query($sql);
		
		$data["page"] = $page;
		$data["more"] = ($query->num_rows() >= $limit);
		$data["rows"] = $query->result_array();
		
		if($return === true)
			return $data;
		
		$this->response($data);
	}
	
	public function guardar() {
		$post = $this->input->post();
		$idusuario = $this->get_var_session("idusuario");
		//$reposicion = intval($post["reposicion"]);
		//$seguridad = intval($post["seguridad"]);
		//$para = intval($post["para"]);
		$current_sucursal = $this->get_var_session("idsucursal");
		$idsucursal = ( ! empty($post["idsucursal"])) ? intval($post["idsucursal"]) : $current_sucursal;
		$descripcion = empty($post["descripcion"]) ? "" : $post["descripcion"];
		//$idsucursal = intval($post["idsucursal"]);
		$entidadbancaria = empty($post["entidadbancaria"]) ? "" : $post["entidadbancaria"];

		$sql = $this->db->query("SELECT idcaja FROM caja.caja WHERE abierto='S' AND idusuario_apertura='$idusuario' AND idsucursal='$idsucursal';");
		$idcaja 				= $sql->row('idcaja');
		//$this->db->query("delete from compra.temp_pedido where idsucursal=?", array($current_sucursal));
		//exec('c:\WINDOWS\system32\cmd.exe /c START D:\Excel\Sincronizar.bat');
		exec('c:\WINDOWS\system32\cmd.exe /c START D:\Excel\Hello.bat');
		// insertamos los datos
		$sql = "INSERT INTO caja.detalle_caja(idcaja, fecha, hora, idconceptomovimiento, monto, tabla, idtabla, descripcion, idusuario, 
							idmoneda, idtipodocumento,idcliente, serie, numero, tipocambio, montoconvertido, referencia, estado, idsucursal, idtipopago, en_deposito)
							
							SELECT 
							{$idcaja}
							, cast(to_char(NOW(),'yyyy-mm-dd') as date)
							,cast(to_char(NOW(),'hh:mm:ss') as time)
							,'7'
							,-saldo
							,'compra.letra'
							, a.id_referencia
							, 'PAGO A PROVVEDOR'
							,{$idusuario}
							,1
							,0
							,a.idproveedor
							,a.serie
							, a.numero
							,1.00
							,-saldo
							,b.nombre
							,'A'
							,{$idsucursal}
							,3
							,'N'
							FROM compra.temp_pedido a, compra.proveedor b where a.idproveedor = b.idproveedor";
							
							 
					
//			 echo $sql; exit;
		$this->db->query($sql);
		
		// hacemos el calculo y actualizamos
		$sql = "update compra.deuda set 
			pagado = 'S'
			where idsucursal={$idsucursal} AND iddeuda in (SELECT iddeuda  FROM compra.temp_pedido)";// AND floor((promedio_ventas * pedido_para) + (promedio_ventas * tiempo_reposicion))";
		
		
		$this->db->query($sql);
		$sql = "update compra.letra set 
			idtipo_pago = '3', 
			fecha_cancelado = cast(to_char(NOW(),'yyyy-mm-dd') as date),
			pagado = 'S',
			serie = compra.fn_ser({$idsucursal}, iddeuda),
			numero = compra.fn_num({$idsucursal}, iddeuda),			
			hora_pago = cast(to_char(NOW(),'hh:mm:ss') as time),
			idsucursal_pago = {$idsucursal},
			nro_dias_formapago = '0',
			id_referencia = compra.fn_ref({$idsucursal}, iddeuda)
			WHERE  iddeuda in (SELECT iddeuda  FROM compra.temp_pedido)";// AND floor((promedio_ventas * pedido_para) + (promedio_ventas * tiempo_reposicion))";
		
		/*
		$sql = "update compra.temp_pedido set 
			stock_seguro = round((promedio_ventas * tiempo_reposicion)::numeric, 2), 
			pp = round((promedio_ventas * (tiempo_reposicion + tiempo_seguridad))::numeric, 2), 
			critico = case when stock < (promedio_ventas * tiempo_reposicion) then 2 else 1 end, 
			sugerido = round(((promedio_ventas * pedido_para) + (promedio_ventas * tiempo_reposicion))::numeric, 2), 
			cantidad = floor((promedio_ventas * pedido_para) + (promedio_ventas * tiempo_reposicion)),
			valor_compra = round((floor((promedio_ventas * pedido_para) + (promedio_ventas * tiempo_reposicion)) * precio_compra)::numeric, 2) 
			where idsucursal=?";// AND floor((promedio_ventas * pedido_para) + (promedio_ventas * tiempo_reposicion))";
			
			*/
			echo $sql; exit;
		$this->db->query($sql, array($current_sucursal));
		
		//$res["idsucursal"] = $idsucursal;
		//$res["proveedor"] = $this->get_proveedor(0, 20, true);
		//$res["producto"] = $this->get_producto(0, 20, true);
		
		//$this->response($res);
	}
	
	protected function get_datos($post) {
		if(empty($post["idproducto"]))
			return array();
		
		$arr = explode("|", $post["idproducto"]);
		foreach($arr as $k=>$id) {
			$arr[$k] = intval($id);
		}
		
		$sql = "select t.*, p.descripcion_detallada as producto
			from compra.temp_pedido t
			join compra.producto p on p.idproducto = t.idproducto
			where t.idproducto in (".implode(",", $arr).") and t.idsucursal = ? 
			order by critico desc, producto";
		$query = $this->db->query($sql, array($this->get_var_session("idsucursal")));
		if($query->num_rows() <= 0)
			return array();
		
		return $query->result_array();
	}
	
	protected function add_pdf($label, $value, $nl = true) {
		if( ! isset($this->pdf))
			return;
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(40,6,utf8_decode($label),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($value),0,0,'L');
		if($nl === true)
			$this->pdf->Ln();
	}
	
	public function imprimir() {
		$this->unlimit();
		$post = $this->input->get();
		$rows = $this->get_datos($post);
		
		if(empty($rows))
			return true;
		
		$cab = $rows[0];
		
		$rangofecha = "";
		if( ! empty($cab["fecha_i"]))
			$rangofecha = "DESDE ".fecha_es($cab["fecha_i"])." ";
		if( ! empty($cab["fecha_f"]))
			$rangofecha .= "HASTA ".fecha_es($cab["fecha_f"]);
		if($rangofecha == "")
			$rangofecha = "TODOS";
		
		$this->load_model(array("seguridad.empresa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->load->library("pdf");
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		$this->pdf->SetTitle(utf8_decode("GENERACION DE PEDIDOS"), 11, null, true);
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);
        $this->pdf->SetDrawColor(160, 160, 160);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',8);
		
		$this->pdf->SetHeight(3);
		$this->pdf->SetWidths(array(101, 101));
		$this->pdf->Row(array($this->empresa->get("descripcion"), date('d/m/Y')), array("L", "R"), "N", "Y");
		$this->pdf->Row(array("RUC: ".$this->empresa->get("ruc"), date('H:i:s')), array("L", "R"), "N", "Y");
		
		$this->pdf->Ln();
		$this->pdf->SetHeight(5);
		$this->add_pdf("FECHAS", $rangofecha);
		$this->add_pdf("TIEMPO REPOSICION", $cab["tiempo_reposicion"]." DIAS");
		$this->add_pdf("TIEMPO SEGURIDAD", $cab["tiempo_seguridad"]." DIAS");
		$this->add_pdf("PEDIDO PARA", $cab["pedido_para"]." DIAS");
		if( ! empty($post["idproveedor"])) {
			$this->load_model("compra.proveedor");
			$this->proveedor->find(intval($post["idproveedor"]));
			$this->add_pdf("PROVEEDOR", $this->proveedor->get("nombre"));
		}
		$this->pdf->Ln();
		
		$cols = array(
			array("col" => "producto", "label" => "Producto", "pos" => "L", "width" => 62)
			,array("col" => "stock", "label" => "Stock", "pos" => "R", "width" => 18)
			,array("col" => "stock_seguro", "label" => "St.Segur.", "pos" => "R", "width" => 18)
			,array("col" => "pp", "label" => "P.P.", "pos" => "R", "width" => 18)
			,array("col" => "critico", "label" => "Critico", "pos" => "C", "width" => 13)
			,array("col" => "promedio_ventas", "label" => "Prom.Vta.", "pos" => "R", "width" => 18)
			,array("col" => "sugerido", "label" => "Sugerido", "pos" => "R", "width" => 18)
			,array("col" => "cantidad", "label" => "Cantidad", "pos" => "R", "width" => 18)
			,array("col" => "valor_compra", "label" => "V.Compra", "pos" => "R", "width" => 18)
		);
		
		$fields = array_column($cols, "col");
		$pos = array_column($cols, "pos");
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->SetWidths(array_column($cols, 'width'));
		$this->pdf->Row(array_column($cols, 'label'), array_fill(0, count($fields), "C"), "Y", "Y");
		
		$this->pdf->SetFont('Arial','',8);
		foreach($rows as $row) {
			$values = array();
			foreach($fields as $field) {
				$values[] = array_key_exists($field, $row) ? utf8_decode($row[$field]) : "";
			}
			$this->pdf->Row($values, $pos, "Y", "Y");
		}
		
		$this->pdf->Output();
	}
	
	protected function add_excel(&$excel, &$row, $label, $value, $nl=true) {
		$this->row($excel, "A", $row, "{$label} :", "B", true);
		$this->row($excel, "C", $row, $value, "D");
		if($nl === true)
			$row++;
	}
	
	protected function row(&$excel, $col, &$row, $val, $merge="", $bold=false, $border=false) {
		if($bold === true) {
			$excel->getActiveSheet()->getStyle("{$col}{$row}")->getFont()->setBold(true);
		}
		$excel->getActiveSheet()->setCellValue("{$col}{$row}", utf8_decode($val));
		if( ! empty($merge)) {
			$excel->setActiveSheetIndex(0)->mergeCells("{$col}{$row}:{$merge}{$row}");
		}
		if($border === true) {
			$cels = empty($merge) ? "{$col}{$row}" : "{$col}{$row}:{$merge}{$row}";
			$excel->getActiveSheet()->getStyle($cels)->applyFromArray(array(
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
						,'color' => array('argb' => 'FF000000')
					)
				)
			));
		}
	}
	
	public function exportar() {
		$this->unlimit();
		
		$post = $this->input->get();
		$rows = $this->get_datos($post);
		
		if(empty($rows))
			return true;
		
		$cab = $rows[0];
		
		$rangofecha = "";
		if( ! empty($cab["fecha_i"]))
			$rangofecha = "DESDE ".fecha_es($cab["fecha_i"])." ";
		if( ! empty($cab["fecha_f"]))
			$rangofecha .= "HASTA ".fecha_es($cab["fecha_f"]);
		if($rangofecha == "")
			$rangofecha = "TODOS";
		
		require_once APPPATH."/libraries/PHPExcel.php"; // libreria excel
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$excel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($excel,"GENERACION DE PEDIDOS",true,4,58,150,false);
		
		$i = 7;
		
		// draw filtros
		$this->add_excel($excel, $i, "FECHAS", $rangofecha);
		$this->add_excel($excel, $i, "TIEMPO REPOSICION", $cab["tiempo_reposicion"]." DIAS");
		$this->add_excel($excel, $i, "TIEMPO SEGURIDAD", $cab["tiempo_seguridad"]." DIAS");
		$this->add_excel($excel, $i, "PEDIDO PARA", $cab["pedido_para"]." DIAS");
		if( ! empty($post["idproveedor"])) {
			$this->load_model("compra.proveedor");
			$this->proveedor->find(intval($post["idproveedor"]));
			$this->add_excel($excel, $i, "PROVEEDOR", $this->proveedor->get("nombre"));
		}
		
		$cols = array(
			array("col" => "producto", "label" => "Producto", "pos" => "A", "merge" => "D")
			,array("col" => "stock", "label" => "Stock", "pos" => "E", "merge" => "")
			,array("col" => "stock_seguro", "label" => "St.Segur.", "pos" => "F", "merge" => "")
			,array("col" => "pp", "label" => "P.P.", "pos" => "G", "merge" => "")
			,array("col" => "critico", "label" => "Critico", "pos" => "H", "merge" => "")
			,array("col" => "promedio_ventas", "label" => "Prom.Vta.", "pos" => "I", "merge" => "")
			,array("col" => "sugerido", "label" => "Sugerido", "pos" => "J", "merge" => "")
			,array("col" => "cantidad", "label" => "Cantidad", "pos" => "K", "merge" => "")
			,array("col" => "valor_compra", "label" => "V.Compra", "pos" => "L", "merge" => "")
		);
		
		// draw cabecera
		$i ++;
		foreach($cols as $val) {
			$this->row($excel, $val["pos"], $i, $val["label"], $val["merge"], true, true);
		}
		
		// draw detalle
		$i ++;
		foreach($rows as $row) {
			foreach($cols as $val) {
				$v = array_key_exists($val["col"], $row) ? $row[$val["col"]] : "";
				$this->row($excel, $val["pos"], $i, $v, $val["merge"], false, true);
			}
			$i ++;
		}
		
		$filename='pedido'.date("dmYhis").'.xlsx'; //save our workbook as this file name
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');  
        $objWriter->save('php://output');
	}
}
?>