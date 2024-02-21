<?php

include_once "Controller.php";

class Reporteinveval extends Controller {
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		// $this->set_title("Movimiento de Caja");
		//$this->set_subtitle("Lista de Caja");
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
	public function form($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		$this->load->library('combobox');

		$data["controller"] = $this->controller;
		$data["sucursal"] = $this->listsucursal();
		$data['control_reporte'] = $this->get_var_session("control_reporte")?$this->get_var_session("control_reporte"):'N';
		
	
		
		

		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idcategoria");
		$this->combobox->setAttr("name","idcategoria");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select('idcategoria,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.categoria");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['categoria'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idmarca");
		$this->combobox->setAttr("name","idmarca");
		$this->combobox->setAttr("class","chosen-select form-control");
		$this->db->select('idmarca,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.marca");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['marca'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idmodelo");
		$this->combobox->setAttr("name","idmodelo");
		$this->combobox->setAttr("class","chosen-select form-control");
		$this->db->select('idmodelo,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.modelo");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['modelo'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idproducto");
		$this->combobox->setAttr("name","idproducto");
		$this->combobox->setAttr("class","chosen-select form-control");
		$this->db->select('idproducto,producto');
		$query = $this->db->order_by("producto")->get("almacen.view_productos_stock");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['producto'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
	
		
		
		/*---------------------------------------------------------------------*/
		$data["sucursal"] = $this->listsucursal();
		
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->css("plugins/chosen/chosen");
		$this->js("plugins/chosen/chosen.jquery");

		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return $this->form();
	}

	public function listsucursal(){
		$idsucursal = $this->get_var_session("idsucursal");
		$whereAnd = '';
		if ($this->get_var_session("control_reporte")=='N') {
			$whereAnd.= ' AND s.idsucursal='.$idsucursal;
		}
		$sql = "SELECT
				s.idsucursal,s.descripcion, idempresa
				FROM seguridad.sucursal s 
				WHERE s.estado='A' AND idempresa IN (SELECT e.idempresa FROM seguridad.empresa e JOIN seguridad.sucursal ss ON ss.idempresa=e.idempresa WHERE ss.idsucursal=$idsucursal $whereAnd)";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function seleccion($datos,$comparar = array()){
		$data = array();
		foreach($datos as $kk=>$vv){
			if(!empty($comparar)){
				$band = false;
				foreach($comparar as $k=>$v){
					if($vv[$k]==$v)
						$band=true;
					else{
						$band=false;
						break;
					}
				}
				if($band){
					$data[]=$vv;					
				}
			}
		}	
		return $data;
	}
	
	public function dataresumido(){
		$sql = "

				SELECT a.idsucursal,'NOGASA NEGOCIOS GENERALES SRL' sucursal,da.idalmacen,a.descripcion almacen,p.idlinea,l.descripcion linea,p.idmarca,m.descripcion marca, p.idproducto,p.descripcion_detallada producto,vs.stock,vs.um unidad
					, almacen.fn_costo_promedio(p.idproducto,da.idalmacen) as costo
				FROM almacen.detalle_almacen da 
			    INNER JOIN (SELECT distinct(idproducto),descripcion_detallada,idlinea,idcategoria,idmarca,idmodelo, estado FROM compra.producto WHERE estado = 'A') p on p.idproducto = da.idproducto 
				INNER JOIN almacen.almacen a on a.idalmacen = da.idalmacen
				INNER JOIN seguridad.sucursal s on s.idsucursal = a.idsucursal
				INNER JOIN general.marca m on m.idmarca = p.idmarca
				INNER JOIN general.linea l on l.idlinea = p.idlinea
				INNER JOin almacen.view_stock vs on vs.idproducto = p.idproducto and vs.idalmacen = da.idalmacen
				WHERE da.fecha <= '{$_REQUEST['fechainicio']}' {$this->condicion_resumido()} and vs.stock > 0
				group by a.idsucursal,s.descripcion,da.idalmacen,a.descripcion, p.idlinea,l.descripcion, p.idmarca,m.descripcion, p.idproducto,p.descripcion_detallada,vs.stock, vs.um
				order by p.idproducto
";

			//	FROM venta.venta_detalle_view
			//	WHERE estado='{$_REQUEST['estado']}' 
			//	{$this->condicion_resumido()}
			//	{$this->condicion_detallado()}
			//	ORDER BY comprobante;
			//	";
		 echo "<pre>";echo $sql;exit;
		$query      = $this->db->query($sql);
		$data = $query->result_array();
		$dataw = array();
		foreach ($data as $key => $value) {

			$dataw [$value['idsucursal']][$value['sucursal']][$value['idalmacen']][$value['almacen']][$value['idlinea']][$value['linea']][$value['idmarca']][$value['marca']][]=array(	'idproducto'=>$value['idproducto']
				  ,'producto'=> $value['producto']
				  ,'stock'=>$value['stock']
				  ,'unidad'=>$value['unidad']
				  ,'costo'=>$value['costo']);

			# code...
		}
      //echo '<pre>';print_r($dataw);echo '</pre>';exit;
		return $dataw;
	}
	


	public function condicion_resumido(){
		$where = '';

		if(!empty($_REQUEST['idcategoria'])){
			$where.=" AND p.idcategoria='{$_REQUEST['idcategoria']}' ";
		}
		
		if(!empty($_REQUEST['idmarca'])){
			$where.=" AND p.idmarca='{$_REQUEST['idmarca']}' ";
		}
		
		if(!empty($_REQUEST['idmodelo'])){
			$where.=" AND p.idmodelo='{$_REQUEST['idmodelo']}' ";
		}
		
		if(!empty($_REQUEST['idproducto'])){
			$where.=" AND p.idproducto='{$_REQUEST['idproducto']}' ";
		}

		if(!empty($_REQUEST['idsucursal'])){
			$where.=" AND da.idsucursal='{$_REQUEST['idsucursal']}' ";
		}
		return $where;
	}
	
	public function imprimir(){
		if($_REQUEST['ver']=='R'){
			$this->resumido();
		}else if($_REQUEST['ver']=='D'){
			$this->resumido();
		}
	}
	
	public function resumido(){
		set_time_limit(0);
		$datos      = $this->dataresumido();
		
		$whit_compr=30;
		$whit_1=20;
		$whit_2=100;
		$whit_3		=20;
		$whit_4		=10;
		$whit_41		=20;
		$whit_5		=20;
		$whit_vend=60;
		$whit_min = 25;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m = 25;//PARA EL WHIT DE LAS MONEDAS
		
		
		$cabecera = $this->array_head();

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento","venta.tipopago","general.zona","general.ubigeosorsa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(120,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(15,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(55,3,"RUC: ".$this->empresa->get("ruc"),0,0,'C');
		$this->pdf->Cell(85,3,"INVENTARIO VALORIZADO AL ".fecha_es($_REQUEST['fechainicio']));
		$this->pdf->Ln(5);
		
		
		$this->pdf->SetFont('Arial','B',10);
		
		$this->pdf->Ln();
		$this->pdf->SetFont('Arial','B',8);

		/************************** CABECERA *****************************************/
		$total_lienzo = 0;
		foreach ($cabecera as $key => $val) {
			$this->pdf->Cell($val[1],9,$val[0],1,0);
			$total_lienzo = $total_lienzo + $val[1];
		}
				
		$this->pdf->Ln(10); 
		/************************** CABECERA *****************************************/
		

		$this->pdf->SetFont('Arial','',8);
		$x_total = 0;
		/************************** BODY *****************************************/
		$item = 1;
		$this->pdf->SetDrawColor(204, 204, 204);

		foreach ($datos as $key => $value) {

			foreach ($value as $key1 => $value1) {

				foreach ($value1 as $key2 => $value2) {

					//idalmacen
					$this->pdf->SetFont('Arial','B',9);
					$this->pdf->Cell($whit_1,5,"ALMACEN ".$key2,0,0,'L');
					$cantalmacen =0;
					$totlalmacen = 0;
					foreach ($value2 as $key3 => $value3) {
						$this->pdf->Cell($whit_2,5,$key3,0,1,'L');
						//$this->pdf->Ln(5); 
						//almacen
						foreach ($value3 as $key4 => $value4) {
							$this->pdf->SetFont('Arial','B',9);
							$this->pdf->Cell($whit_1,5,"LINEA ".$key4,0,0,'L');
							$cantlinea=0;
							$totalinea=0;
							//idlinea
							foreach ($value4 as $key5 => $value5) {
								$this->pdf->Cell($whit_2,5,$key5,0,1,'L');
							//	$this->pdf->Ln(5);
								# lineacode...
								foreach ($value5 as $key6 => $value6) {
									$this->pdf->SetFont('Arial','B',9);
									$this->pdf->Cell($whit_1,5,"MARCA ".$key6,0,0,'L');
									//idmarca
									$cantmarca = 0;
									$totamarca = 0;
									foreach ($value6 as $key7 => $value7) {
										# code...marca
										$this->pdf->Cell($whit_2,5,$key7,0,1,'L');
									//	$this->pdf->Ln(5);
											# code...
										foreach ($value7 as $key8=> $value8) {
											$this->pdf->SetFont('Arial','',8);
											$this->pdf->Cell($whit_1,5,str_pad($value8['idproducto'],5,"0", STR_PAD_BOTH),0,0,'L');
											$this->pdf->Cell($whit_2,5,$value8['producto'],0,0,'L');
											$this->pdf->Cell($whit_3,5,$value8['unidad'],0,0,'L');
											$this->pdf->Cell($whit_3,5,round($value8['stock'],2),0,0,'R');
											$this->pdf->Cell($whit_3,5,round($value8['costo'],2),0,0,'R');
											$this->pdf->Cell($whit_3,5,round($value8['costo']*$value8['stock'],2),0,1,'R');

											$cantalmacen += $value8['stock'];
											$cantlinea += $value8['stock'];
											$cantmarca += $value8['stock'];

											$totlalmacen += $value8['costo']*$value8['stock'];
											$totalinea += $value8['costo']*$value8['stock'];
											$totamarca += $value8['costo']*$value8['stock'];
										}
										
									}
									$this->pdf->SetFont('Arial','B',10);
									$this->pdf->SetTextColor(243,73,37);
									$this->pdf->Cell(33,5,"TOTAL MARCA ".$key6,0,0,'L');
									$this->pdf->Cell($whit_2,5,$key7,0,0,'L');
									$this->pdf->Cell(17,5,"",0,0,'R');
									$this->pdf->Cell($whit_4,5,number_format($cantmarca,'2','.',','),0,0,'R');
									$this->pdf->Cell($whit_5,5,"",0,0,'R');
									$this->pdf->Cell($whit_5,5,number_format($totamarca,'2','.',','),0,1,'R');
									$this->pdf->Line(100, 45, 50, 45);
									$this->pdf->SetTextColor(0,0,0);
									$this->pdf->Ln(5);
								}

							}
							
							$this->pdf->SetFont('Arial','B',10);
							$this->pdf->SetTextColor(243,73,37);
							$this->pdf->Cell(30,5,"TOTAL LINEA ".$key4,0,0,'L');
							$this->pdf->Cell($whit_2,5,$key5,0,0,'L');
							$this->pdf->Cell(20,5,"",0,0,'R');
							$this->pdf->Cell($whit_4,5,number_format($cantlinea,'2','.',','),0,0,'R');
							$this->pdf->Cell($whit_5,5,"",0,0,'R');
							$this->pdf->Cell($whit_5,5,number_format($totalinea,'2','.',','),0,1,'R');
							$this->pdf->SetTextColor(0,0,0);
							$this->pdf->Ln(5);
						}
						# code...
					}
					$this->pdf->SetFont('Arial','B',10);
					$this->pdf->SetTextColor(25,106,209);
					$this->pdf->Cell(30,5,"TOTAL ",0,0,'L');
					$this->pdf->Cell($whit_2,5,$key3,0,0,'L');
					$this->pdf->Cell(10,5,"",0,0,'R');
					//$this->pdf->Cell($whit_5,5,round($cantalmacen,2),0,0,'R');
					$this->pdf->Cell($whit_5,5,number_format($cantalmacen,'2','.',','),0,0,'R');
					$this->pdf->Cell($whit_5,5,"",0,0,'R');
					$this->pdf->Cell($whit_5,5,number_format($totlalmacen,'2','.',','),0,1,'R');
					$this->pdf->SetTextColor(0,0,0);

					$this->pdf->Ln(5);
				}
				# code...
			}
			# code...
		}
		
		/************************** BODY *****************************************/
		
		/************************** PIE *****************************************/
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->SetTextColor(0,0,0);
		/************************** PIE *****************************************/
		$this->pdf->Output();
	}
	
	

	public function exportar(){
		if($_REQUEST['ver']=='R'){
			$this->exportar_resumido();
		}else if($_REQUEST['ver']=='D'){
			$this->exportar_detallado();
		}
	}
	
	public function array_head(){
		$whit_1		=20;
		$whit_2		=100;
		$whit_3		=20;
		$whit_4		=10;
		$whit_41		=20;
		$whit_5		=20;
		$whit_igv		=15;
		$whit_min 		= 25;
		
		$total_ancho	= 300;

		$cabecera = array('item' => array('COD EXIST',20,'C')
							,'producto' => array('DESCRIPCION',100,'C')
							//,'factor' => array('TABLA 05',$whit_3,'R')
							,'unidadmedida' => array('TABLA 06',20,'C')
							,'cantidad_detalle' => array('STOCK',20,'C')
							,'precio_venta_detalle' => array('COSTO',20,'C')
							,'subtotal' => array('VALOR TOTAL',22,'C')
						);
		return $cabecera;
	}


	

	
	

}
?>