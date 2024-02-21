<?php

include_once "Controller.php";

class Reportecomisiones extends Controller {
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
		$data['idperfil'] = $this->get_var_session("idperfil");
		
				
		/*---------------------------------------------------------------------*/
		// $idperfil = 4; // id del perfil vendedor, tal vez deberia ser contante
		$idperfil = $this->get_param("idtipovendedor"); // id del perfil vendedor, tal vez deberia ser contante
		$this->load_model("usuario");
		$datos = $this->usuario->get_vendedor($this->get_var_session("idsucursal"), $idperfil);
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idvendedor","name"=>"idvendedor","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($datos);
		// if( isset($data["venta"]["idvendedor"]) ) {
			// $this->combobox->setSelectedOption($data["venta"]["idvendedor"]);
		// }
		$data["vendedor"] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idlinea");
		$this->combobox->setAttr("name","idlinea");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select('idlinea,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.linea");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['linea'] = $this->combobox->getObject();
		
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
		$this->combobox->setAttr("id","idtipoventa");
		$this->combobox->setAttr("name","idtipoventa");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->db->select('idtipoventa,descripcion');
		$query = $this->db->where("estado","A")->where("mostrar_en_venta","S")->order_by("descripcion")->get("venta.tipo_venta");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['tipopago'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
				
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idtipodocumento");
		$this->combobox->setAttr("name","idtipodocumento");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->db->select('idtipodocumento,descripcion');
		$query = $this->db->where("mostrar_en_venta","S")->where("estado","A")->order_by("descripcion")->get("venta.tipo_documento");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['comprobante'] = $this->combobox->getObject();
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
		$es_superadmin = $this->get_var_session("es_superusuario")?$this->get_var_session("es_superusuario"):'N';
		
		if ($es_superadmin=='N') {// SI NO ES ADMINOSTRADOR LA BUSQUEDA SOLO ES POR LA SESION INICIADA
			$whereAnd.= ' AND s.idsucursal='.$idsucursal;
		}
		/*
		SELECT idsucursal,descripcion FROM seguridad.sucursal WHERE idempresa IN ( SELECT idempresa FROM seguridad.sucursal WHERE idsucursal='1') AND estado='A';
		*/
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
		$sql = "SELECT v.comprobante, c.fecha_credito::date fecha_credito, a.fecha_pago,v.nombres,v.direccion,v.zona
					,c.monto_facturado,w.pagos,a.monto,c.monto_facturado-w.pagos as saldo,v.moneda,v.nombre_vendedor,v.empresa descripcion
					,case when us.empleado is null then v.nombre_vendedor else us.empleado end cobrador
				FROM credito.amortizacion a
				INNER JOIN credito.credito c on c.idcredito = a.idcredito
				INNER JOIN venta.venta_view as v on v.idventa = c.idventa
				INNER JOIN (select idcredito,sum(monto)pagos from credito.amortizacion group by idcredito) w on w.idcredito = c.idcredito
				
				left JOIN venta.reciboingreso ri on ri.idreciboingreso = a.idrecibo_ingreso
				left JOIN cobranza.liquidacion_visita lv on lv.id_recibo = ri.idreciboingreso
				left join cobranza.view_cobradores us on lv.idcobrador = us.idusuario
				where {$this->condicion_resumido()}

";





/*
		SELECT v.idsucursal, s.descripcion as sucursal,'' as fecha_pago
				,(((td.abreviatura::text || '-'::text) || v.serie::text) || '-'::text) || v.correlativo::text AS comprobante
					,to_char(v.fecha_venta,'DD/MM/YYYY') fecha_venta,e.nombres ||' '|| e.appat as nombres
					,'' as marca
					, v.subtotal as totventa, p.monto as idrecibo_ingreso
					,COALESCE(v.subtotal,0)-COALESCE(p.monto,0.00) 
					as monto
					  ,'{$_REQUEST['fechafin']}' - v.fecha_venta as nrodias,v.idvendedor
					  ,(COALESCE(v.subtotal,0)-COALESCE(p.monto,0.00))*-0.5/100 as comisionado
					 ,'-0.5'as comision,'60-mas' as rango, 'DESCUENTO' as final
				FROM venta.venta v
				INNER JOIN credito.credito c on c.idventa = v.idventa
				INNER JOIN venta.tipo_documento td ON td.idtipodocumento = v.idtipodocumento	 
				INNER JOIN seguridad.usuario e ON e.idusuario = v.idvendedor 
				INNER JOIN seguridad.sucursal s ON s.idsucursal = v.idsucursal
				LEFT JOIN (select idcredito,coalesce(sum(monto),0) as monto from credito.amortizacion group by idcredito) AS p on p.idcredito = c.idcredito
				WHERE v.cancelado = 'N' AND '{$_REQUEST['fechafin']}' - v.fecha_venta between 60 AND 180 AND v.estado = 'A' and c.pagado = 'N'
		
		UNION
				
		SELECT a.idsucursal ,s.descripcion as sucursal
				, to_char(a.fecha_pago,'DD/MM/YYYY') fecha_pago 
				, a.comprobante
				, to_char(a.fecha_venta,'DD/MM/YYYY') fecha_venta
				, e.nombres ||' '|| e.appat as nombres
				, m.descripcion marca
				, a.totventa
				, a.idrecibo_ingreso
				, a.monto
				, a.nrodias
				, a.idvendedor
				
				, (a.monto::double precision * pc.comision / 100):: double precision AS comisionado
				, pc.comision
				, (pc.dias_min || '-'::text) || pc.dias_max AS rango
				, 'COMISION ' AS final
				from  
					(
						SELECT v.idsucursal,
						e.idempresa,
						dv.idventa,
						(((td.abreviatura::text || '-'::text) || v.serie::text) || '-'::text) || v.correlativo::text AS comprobante,
						v.idvendedor,
						a.idamortizacion,
						a.idrecibo_ingreso,
						v.fecha_venta,
						COALESCE(v.subtotal, 0::double precision) + COALESCE(v.igv::double precision, 0::double precision) - COALESCE(v.descuento::double precision, 0::double precision) AS totventa,
						a.fecha_pago,
						date_part('year'::text, a.fecha_pago)::integer AS anio,
						date_part('month'::text, a.fecha_pago)::integer AS mes,
						a.monto,
						dv.cantidad * dv.precio + dv.igv AS totproducto,
						case when pc.idmarca isnull then 84 else pc.idmarca end AS idmarca
						,a.fecha_pago - v.fecha_venta AS nrodias
					   FROM credito.amortizacion a
						 JOIN credito.credito c ON c.idcredito = a.idcredito
						 JOIN venta.venta v ON v.idventa = c.idventa
						 JOIN venta.tipo_documento td ON td.idtipodocumento = v.idtipodocumento
						 JOIN venta.detalle_venta dv ON dv.idventa = v.idventa
						 JOIN compra.producto p ON p.idproducto = dv.idproducto
						 JOIN seguridad.sucursal s ON s.idsucursal = v.idsucursal
						 JOIN seguridad.empresa e ON e.idempresa = s.idempresa
						 LEFT JOIN comision.param_comision pc on pc.idmarca = p.idmarca and pc.idvendedor = v.idvendedor and (a.fecha_pago - v.fecha_venta) BETWEEN pc.dias_min AND  pc.dias_max 
					)AS a 
				INNER JOIN comision.param_comision pc ON pc.idmarca = a.idmarca 
					AND a.fecha_venta between pc.fecha_inicio and pc.fecha_fin 
					AND (a.nrodias) BETWEEN pc.dias_min AND  pc.dias_max 
					AND pc.idvendedor = a.idvendedor 
				INNER JOIN seguridad.usuario e ON e.idusuario = a.idvendedor
				INNER JOIN general.marca m ON m.idmarca = pc.idmarca
				INNER JOIN seguridad.sucursal s ON s.idsucursal = a.idsucursal
				where 
				{$this->condicion_resumido()}
				group by a.idsucursal
				  ,s.descripcion	
				  , a.idventa
				  , a.comprobante
				  , a.idvendedor
				  , a.monto
				  , m.descripcion
				  , e.nombres
				  , e.appat
				  , a.totventa
				  , a.idamortizacion
				  , a.idrecibo_ingreso
				  , a.fecha_pago
				  , a.fecha_venta
				  , pc.comision
				  , a.nrodias
				  , pc.dias_min 
				  ,pc.dias_max
				  
				ORDER BY  fecha_pago,comprobante, nombres ";
				// echo $sql;exit;*/
			$query      = $this->db->query($sql);
		
				$data = $query->result_array();
			
		return $data;
	}
	
	
	public function condicion_resumido($add_where=''){
		$where = "";
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin'])){
				$where.="  (a.fecha_pago) between '{$_REQUEST['fechainicio']}' AND '{$_REQUEST['fechafin']}'";
			}else{
				$where.="  (a.fecha_pago)='{$_REQUEST['fechainicio']}' ";
			}
		}
		
		if(!empty($_REQUEST['idvendedor'])){
			$where.=" AND v.idvendedor='{$_REQUEST['idvendedor']}' ";
		}	
		if(!empty($_REQUEST['idsucursal'])){
			$where.=" AND v.idsucursal='{$_REQUEST['idsucursal']}' ";
		}
		if(!empty($_REQUEST['idlinea'])){
			//$where.=" AND a.idlinea ='{$_REQUEST['idlinea']}' ";
		}
		if(!empty($_REQUEST['idcategoria'])){
			//$where.=" AND a.idcategoria ='{$_REQUEST['idcategoria']}' ";
		}
		if(!empty($_REQUEST['idmarca'])){
			//$where.=" AND a.idmarca ='{$_REQUEST['idmarca']}' ";
		}
		if(!empty($_REQUEST['idmodelo'])){
			//$where.=" AND a.idmodelo ='{$_REQUEST['idmodelo']}' ";
		}
		
		$where.=$add_where;
		return $where;
	}
	
	
	
	public function imprimir(){
		if($_REQUEST['ver']=='R'){
			$this->resumido();
		}else if($_REQUEST['ver']=='D'){
			$this->detallado();
		}
	}
	
	public function resumido(){
		$datos      = $this->dataresumido();
		$suma = 0;
		// $monedas = $this->moneda_colum();
		$whit_fvent		= 18;
		$whit_compr		= 25;
		$whit_totalv	= 18;
		$whit_vendedor	= 50;
		$whit_amort 	= 20;
		$whit_famort 	= 18;
		$whit_ndias 	= 10;
		$whit_pcomis 	= 10;
		$whit_comis 	= 20;


		
		$whit_fecha	=15;
		$whit_prov=100;	
		$whit_vend=40;
		// $whit_compr=80;
		$whit_um=17;
		$whit_cant=10;
		$whit_min = 15;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m = 25;//PARA EL WHIT DE LAS MONEDAS
		
		if(!empty($monedas)){
			$whit_total_m = $whit_min*count($monedas);
			$whit_prov = $whit_prov - $whit_min*(count($monedas)-1);
		}

		$cabecera = array(
			             	'comprobante' => array('Comprobante',$whit_compr)
							,'fecha_credito' => array('F.Credito',$whit_famort)
							,'fecha_pago' => array('F.Amortiz',$whit_famort)
							,'nombres' => array('cliente',$whit_vendedor)
							//,'direccion' => array('Direccion',$whit_vendedor)
							//,'zona' => array('Zona',$whit_totalv)
							,'monto_facturado' => array('Tot. Vta',$whit_amort,'R')
							,'pagos' => array('Pagos',$whit_amort,'R')
							,'monto' => array('Amortz',$whit_amort,'R')
							,'saldo' => array('Saldo',$whit_totalv,'R')
							//,'moneda' => array('mone',$whit_fvent)
							,'nombre_vendedor' => array('vendedor',$whit_vend)
							,'cobrador' => array('cobrador',$whit_vend)
							,'descripcion' => array('sucursal',$whit_ndias,'R')
							
						);

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento"));
		$this->empresa->find($this->get_var_session("idsucursal"));
				$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTE COMISIONES DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechafin'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE COMISIONES DE ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(utf8_decode("REPORTE COMISIONES "), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);

		$this->pdf->AddPage('L');
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		
		$this->pdf->SetFont('Arial','B',10);
		
		if(!empty($_REQUEST['idcliente'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"CLIENTE",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->cliente_view->find($_REQUEST['idcliente']);
			$this->pdf->Cell(5,3,$this->cliente_view->get("cliente"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SUCURSAL",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->sucursal->find($_REQUEST['idsucursal']);
			$this->pdf->Cell(5,3,$this->sucursal->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"MONEDA",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->moneda->find($_REQUEST['idmoneda']);
			$this->pdf->Cell(5,3,$this->moneda->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"COMPROBANTE",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->tipo_documento->find($_REQUEST['idtipodocumento']);
			$this->pdf->Cell(5,3,$this->tipo_documento->get("descripcion"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idvendedor'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"VENDEDOR",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->view_usuario->find($_REQUEST['idvendedor']);
			$this->pdf->Cell(5,3,$this->view_usuario->get("user_nombres"),0,1,'L');
		}
		
		if(!empty($_REQUEST['idtipoventa'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"TIPO OPERACION",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->tipo_venta->find($_REQUEST['idtipoventa']);
			$this->pdf->Cell(5,3,$this->tipo_venta->get("descripcion"),0,1,'L');
		}
		$this->pdf->Ln();
		$this->pdf->SetFont('Arial','B',8);

		/************************** CABECERA *****************************************/
		$total_lienzo = 0;
		foreach ($cabecera as $key => $val) {
			$this->pdf->Cell($val[1],9,$val[0],1,0);
			$total_lienzo = $total_lienzo + $val[1];
		}
		// $this->pdf->Cell($whit_total_m,5,"TOTAL",1,1,'C');
		// $this->pdf->Cell($total_lienzo,5,"",0,0,'C');
		// foreach($monedas as $key=>$val){
		// 	$this->pdf->Cell($whit_min,4,$val['abreviatura']." ".$val['simbolo'],1,0,'C');			
		// }
		$this->pdf->Ln(9); 
		/************************** CABECERA *****************************************/
		

		$this->pdf->SetFont('Arial','',8);
		$x_total = 0;
		/************************** BODY *****************************************/
		$this->pdf->SetDrawColor(204, 204, 204);
		foreach ($datos as $key => $val) {
				//$suma += $val['comisionado'];
			foreach ($cabecera as $k => $v) {
				if(isset($v[2])){
					$this->pdf->Cell($v[1],5,utf8_decode($val[$k]),1,0,$v[2]);
				}else{
					$this->pdf->Cell($v[1],5,utf8_decode($val[$k]),1,0);
				}
				
					
				
			}
			
			
			$this->pdf->Ln(); 
		}
		/************************** BODY *****************************************/
		/************************** PIE *****************************************/
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(170,5,"TOTAL",0,0,'R');
		// foreach($monedas as $k=>$v){
		// 	$filtro = $this->totales_monedas(" AND idmoneda='$v[idmoneda]' ");
		 $this->pdf->Cell(20,5,number_format($suma,2,'.',','),0,0,'R');			
		// }
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

		$whit_fvent		= 18;
		$whit_compr		= 25;
		$whit_totalv	= 18;
		$whit_vendedor	= 50;
		$whit_amort 	= 20;
		$whit_famort 	= 18;
		$whit_ndias 	= 10;
		$whit_pcomis 	= 10;
		$whit_comis 	= 20;

		$whit_item		= 10;
		// $whit_compr		= 24;
		$whit_fecha		= 17;
		$whit_cli		= 75;
		$whit_vend		= 30;
		$whit_tpgo		= 20;
		
		$total_ancho	= $whit_compr+$whit_fecha+$whit_cli+$whit_vend + $whit_tpgo;


		$cabecera = array( 'item' => array('ITEM',$whit_item,$whit_item*100/$total_ancho,0,'L')
							,'comprobante' => array('COMPROBANTE',$whit_fecha,$whit_fecha*100/$total_ancho,0,'L')
							,'fecha_credito' => array('FECHA CREDITO',$whit_compr,$whit_compr*100/$total_ancho,0,'L')
							,'fecha_pago' => array('FECHA AMORT',$whit_compr,$whit_compr*100/$total_ancho,0,'L')
							,'nombres' => array('CLIENTE',$whit_vend,$whit_vend*10/$total_ancho,0,'L')
							,'direccion' => array('DIRECCION',$whit_vend,$whit_vend*10/$total_ancho,0,'L')
							,'zona' => array('ZONA',$whit_vend,$whit_vend*10/$total_ancho,0,'L')
							,'monto_facturado' => array('TOTAL VTA',$whit_cli,$whit_cli*10/$total_ancho,0,'L')
							,'pagos' => array('SUMA PAGOS',$whit_cli,$whit_cli*10/$total_ancho,0,'L')
							,'monto' => array('AMORTIZACION',$whit_cli,$whit_cli*10/$total_ancho,0,'L')
							,'saldo' => array('SALDO',$whit_cli,$whit_cli*10/$total_ancho,0,'L')
							,'moneda' => array('F. VENTA',$whit_tpgo,$whit_tpgo*10/$total_ancho,0,'L')
							,'nombre_vendedor' => array('MARCA',$whit_tpgo,$whit_tpgo*10/$total_ancho,0,'L')
							,'cobrador' => array('AMORTIZACION',$whit_tpgo,$whit_tpgo*10/$total_ancho,0,'L')
							,'descripcion' => array('SUCURSAL',$whit_tpgo,$whit_tpgo*10/$total_ancho,0,'L')
						);

		return $cabecera;
	}
 	
	
	public function exportar_resumido(){
		$this->load->library("phpexcel");
		
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE DE COMISIONES",true);
		
		$col = 9;
		if(!empty($_REQUEST['idcliente'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'CLIENTE : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("venta.cliente_view");
			$this->cliente_view->find($_REQUEST['idcliente']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->cliente_view->get("cliente")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SUCURSAL : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("seguridad.sucursal");
			$this->sucursal->find($_REQUEST['idsucursal']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->sucursal->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'MONEDA : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("general.moneda");
			$this->moneda->find($_REQUEST['idsucursal']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->moneda->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'TIPO DOCUMENTO : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("venta.tipo_documento");
			$this->tipo_documento->find($_REQUEST['idtipodocumento']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->tipo_documento->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idvendedor'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'VENDEDOR : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("seguridad.usuario");
			$this->usuario->find($_REQUEST['idvendedor']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->usuario->get("usuario")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idtipoventa'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'TIPO VENTA : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->load_model("venta.tipo_venta");
			$this->tipo_venta->find($_REQUEST['idtipoventa']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, utf8_decode($this->tipo_venta->get("descripcion")));
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
	$col++;

		/************************** CABECERA *****************************************/
		
		
		/************************** CUERPO *****************************************/
		$rows 		= $this->dataresumido();
		
		foreach($rows as $key=>$val){
			$val['item'] = (int) $key + 1;

			$alfabeto = 65;
			foreach($this->array_head() as $k=>$v){
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, ($val[$k]));
				$alfabeto++;
			}
			
			
			$col++;
		}
		/************************** CUERPO *****************************************/
		
		
		$alfabeto = 65;
		foreach($this->array_head() as $k=>$v){
			$Oexcel->getActiveSheet()->getColumnDimension(chr($alfabeto))->setAutoSize(true);
			$alfabeto++;
		}
		// foreach($this->moneda_colum() as $k=>$v){
		// 	$Oexcel->getActiveSheet()->getColumnDimension(chr($alfabeto))->setAutoSize(true);
		// 	$alfabeto++;
		// }
		
		$filename='reporteComisiones'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
	
}
?>