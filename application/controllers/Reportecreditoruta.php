<?php

include_once "Controller.php";

class Reportecreditoruta extends Controller {
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
		$this->js('form/'.$this->controller.'/index_');
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
		$data['control_reporte'] = $this->get_var_session("control_reporte")?$this->get_var_session("control_reporte"):'N';
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idubigeo");
		$this->combobox->setAttr("name","idubigeo");
		$this->combobox->setAttr("class","chosen-select form-control");
		$this->db->select("idubigeo,descripcion");
		$query = $this->db->order_by("descripcion")->get("general.ubigeozona_view");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['ruta'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idcliente");
		$this->combobox->setAttr("name","idcliente");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select("idcliente,cliente");
		$query = $this->db->order_by("cliente")->get("venta.cliente_credito_view");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['cliente'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/

		
		/*---------------------------------------------------------------------*/
		// $idperfil = 4; // id del perfil vendedor, tal vez deberia ser contante
		$idperfil = $this->get_param("idtipovendedor"); // id del perfil vendedor, tal vez deberia ser contante
		$this->load_model("usuario");
		$datos = $this->usuario->get_vendedor($this->get_var_session("idsucursal"), $idperfil,'A', array('S','N'));
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idvendedor","name"=>"idvendedor","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($datos);
		$data["vendedor"] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idmoneda");
		$this->combobox->setAttr("name","idmoneda");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->db->select('idmoneda,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.moneda");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['moneda'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","id_estado_credito");
		$this->combobox->setAttr("name","id_estado_credito");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->db->select('id_estado_credito,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("credito.estado_credito");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['estado_credito'] = $this->combobox->getObject();
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
	
	public function query_master(){
		$sql = "SELECT v.usuario, 
				trim(c.cliente) cliente , 
				trim(c.direccion) direcc, 
				v.comprobante doc ,
				to_char(cr.fecha_credito, 'DD/MM/YYYY') fecha_creditos ,
				cr.fecha_credito,
				to_char(l.fecha_vencimiento, 'DD/MM/YYYY') fech_vnto,  
				c.zona zona,
				d.descripcion , 
				cr.monto_credito AS importe ,
				COALESCE(SUM(a.monto),0.00) pago ,
				cr.monto_credito - COALESCE(SUM(a.monto),0.00) saldo ,
				c.limite_credito linea,
				c.limite_credito - (cr.monto_credito - COALESCE(SUM(a.monto),0.00)) disponible,
				1 total,
				2 descuento,
				3 moras_letras,
				4 moras_amortizado
				,extract(days from ( now() - l.fecha_vencimiento)) dias
				,cr.pagado
				,d.idubigeo
				,v.idtipodocumento
				,v.idmoneda
				,v.idsucursal
				,v.idvendedor
				,to_char(v.fecha_venta,'DD/MM/YYYY') fecha_venta
				,cr.id_estado_credito
				,cr.idcliente
				,COALESCE(c.telefono,'-') telefono
				,v.simbolo_moneda
				,v.idempresa
				,v.vendedor
				FROM credito.credito cr 
				JOIN venta.cliente_view c ON c.idcliente=cr.idcliente 
				JOIN venta.venta_view v ON v.idventa=cr.idventa 
				LEFT JOIN credito.letra l ON l.idcredito=cr.idcredito AND l.estado<>'I' 
				LEFT JOIN credito.amortizacion a ON a.idletra=l.idletra AND a.idcredito=cr.idcredito AND a.estado<>'I' 
				LEFT JOIN general.zona b ON c.idzona = b.idzona
				LEFT JOIN general.ubigeosorsa d ON d.idubigeo = b.idubigeo
				WHERE cr.estado<>'I' AND cr.pagado='N'
				GROUP BY l.fecha_vencimiento, fecha_credito
				,cliente,monto_credito,direcc
				, d.descripcion
				,v.comprobante
				, c.zona, fech_vnto, c.limite_credito  , v.usuario
				,cr.pagado
				,d.idubigeo
				,v.idtipodocumento
				,v.idmoneda
				,v.idsucursal
				,v.idvendedor
				,cr.id_estado_credito
				,cr.fecha_credito
				,cr.idcliente
				,c.telefono
				,fecha_venta
				,v.simbolo_moneda
				,v.idempresa
				,v.vendedor
				ORDER BY d.descripcion, cliente , fecha_credito ASC
				";
		return $sql;
	}
	
	public function dataresumido(){
		$sql = "SELECT*
				,0 dif_0
				,0 dif_0130
				,0 dif_3060
				,0 dif_6090
				,0 dif_90
				FROM ({$this->query_master()}) q
				WHERE pagado='N'
				{$this->condicion_resumido()}
				ORDER BY cliente,fech_vnto";
		$query      = $this->db->query($sql);
		// echo $sql;exit;
		$data = $query->result_array();
		return $data;
	}
	
	public function clientes(){
		$sql = "SELECT DISTINCT idcliente
				,cliente
				,direcc direccion
				FROM ({$this->query_master()}) q
				WHERE pagado='N'
				{$this->condicion_resumido()}
				ORDER BY cliente";
		$query      = $this->db->query($sql);
		// echo $sql;exit;
		$data = $query->result_array();
		return $data;
	}
	
	public function cuentas_b(){
		$idsucursal = $this->get_var_session("idsucursal")?$this->get_var_session("idsucursal"):0;
		$query      = $this->db->query("SELECT * FROM general.view_cuentas_bancarias WHERE estado='A' AND idsucursal='{$idsucursal}' ORDER BY banco,nro_cuenta;");
		$data = $query->result_array();
		return $data;
	}

	public function condicion_resumido($add_where=''){
		$where = " AND idempresa='{$this->get_var_session('idempresa')}'";
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin'])){
				$where.=" AND date(fecha_credito)>='{$_REQUEST['fechainicio']}' AND date(fecha_credito)<='{$_REQUEST['fechafin']}'";
			}else{
				$where.=" AND date(fecha_credito)>='{$_REQUEST['fechainicio']}' ";
			}
		}
		
		if(!empty($_REQUEST['idubigeo'])){
			$where.=" AND idubigeo='{$_REQUEST['idubigeo']}' ";
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$where.=" AND idtipodocumento='{$_REQUEST['idtipodocumento']}' ";
		}
		
		if(!empty($_REQUEST['idvendedor'])){
			$where.=" AND idvendedor='{$_REQUEST['idvendedor']}' ";
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$where.=" AND idmoneda='{$_REQUEST['idmoneda']}' ";
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$where.=" AND idsucursal='{$_REQUEST['idsucursal']}' ";
		}
		
		if(!empty($_REQUEST['idcliente'])){
			$where.=" AND idcliente='{$_REQUEST['idcliente']}' ";
		}
		
		if(!empty($_REQUEST['id_estado_credito'])){
			$where.=" AND id_estado_credito='{$_REQUEST['id_estado_credito']}' ";
		}
		
		$where.=$add_where;
		return $where;
	}

	public function imprimir(){
		set_time_limit(0);
		$this->resumido();
	}
	
	public function resumido(){
		$datos      = $this->dataresumido();
		
		$whit_vend=17;
		$whit_fecha=15;
		$whit_zona=31;
		// $whit_credt=22;
		$whit_doc=21;
		$whit_clien=56;
		$whit_direct=53;
		$whit_dias=8;
		$whit_credt = 56;
		$whit_monto=14;
		$whit_desct=13;
		$whit_total=13;
		
		$whit_cuota=15;
		$whit_moras=16;
		$whit_monto_dis=17;
		$whit_saldo=20;

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento","general.ubigeosorsa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		$titulo = "REPORTE DE CREDITOS EN RUTAS";
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin']))
				$titulo.= " DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio']);
			else
				$titulo.= " DE ".fecha_es($_REQUEST['fechainicio']);
		}
			
		$this->pdf->SetTitle(utf8_decode($titulo), 11, null, true);
		
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);
		$this->pdf->setFillColor(249, 249, 249);
        $this->pdf->SetDrawColor(204, 204, 204);

		$this->pdf->AddPage('H','a4');
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(200,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		
		$this->pdf->SetFont('Arial','B',10);
		
		if(!empty($_REQUEST['idubigeo'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"RUTA",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->ubigeosorsa->find($_REQUEST['idubigeo']);
			$this->pdf->Cell(5,3,$this->ubigeosorsa->get("descripcion"),0,1,'L');
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
			$this->pdf->Cell(5,3,utf8_decode($this->view_usuario->get("user_nombres")),0,1,'L');
		}
		
		$this->pdf->Ln();
		$this->pdf->SetFont('Arial','B',8);

		/************************** CABECERA *****************************************/		
		$this->pdf->Cell($whit_vend,10,'VEND',1,0,'C');
		$this->pdf->Cell($whit_clien,10,'CLIENTE',1,0,'C');
		$this->pdf->Cell($whit_direct,10,'DIRECCION',1,0,'C');
		$this->pdf->Cell($whit_zona,10,'ZONA',1,0,'C');
		$this->pdf->Cell($whit_doc,10,'DOC',1,0,'C');
		$this->pdf->Cell($whit_fecha,10,'FCRED',1,0,'C'); //63
		$this->pdf->Cell($whit_fecha,10,'FVNTO',1,0,'C'); //63
		$this->pdf->Cell($whit_dias,10,'DIA',1,0,'C'); //63
		$this->pdf->Cell($whit_credt,5,'CREDITO',1,0,'C');		
		$this->pdf->Cell($whit_monto_dis,5,'','TRL',1,'C');
		
		// FILA 2

		$this->pdf->Cell($whit_vend+$whit_clien+$whit_direct+$whit_zona+$whit_doc+$whit_fecha+$whit_fecha+$whit_dias,5,'',0,0,'C');
		
		$this->pdf->Cell($whit_monto,5,'IMPORTE',1,0,'C');
		$this->pdf->Cell($whit_monto,5,'PAGO',1,0,'C');
		$this->pdf->Cell($whit_monto,5,'SALDO',1,0,'C');		
		$this->pdf->Cell($whit_monto,5,'LC',1,0,'C');		
		$this->pdf->Cell($whit_monto_dis,5,'DISP','RBL',1,'C');
		/************************** CABECERA *****************************************/
		

		/************************** BODY *****************************************/
		$this->pdf->SetFont('Arial','',7);
		$width = array($whit_vend,$whit_clien, $whit_direct,$whit_zona,$whit_doc, $whit_fecha,$whit_fecha,$whit_dias, $whit_monto, $whit_monto, $whit_monto, $whit_monto,  $whit_monto_dis);
		$cols  = array('usuario','cliente','direcc','zona','doc','fecha_creditos','fech_vnto','dias','importe','pago', 'saldo','linea',   'disponible');
		$pos   = array("L", "L","L", "L","L","L","L", "R", "R", "R",'R','R','R');
		$fill_ = array(false, false,false, false,false,false,false, false, false, false,false,false,false);
		$totaltotal = $totalmora = $totalcuota = $totalsaldo = 0;

		// $this->pdf->SetDrawColor(0, 0, 0);
		foreach ($datos as $key => $val) {
			$this->pdf->setFillColor(249, 249, 249);
			
			$this->pdf->SetWidths($width);
			$values = array();
			
			foreach($cols as $f){
				$values[] = utf8_decode((($val[$f])));
			}
			
			$this->pdf->Row($values, $pos, "Y", "Y",$fill_);
			
			$total = $val['total'] - $val["descuento"];
			$amortizado = $val['pago'];
			$saldo = $total - $amortizado;
			$saldo_mora = $val["moras_letras"] - $val["moras_amortizado"];

			$totaltotal += $total;
			$totalcuota += $val['pago'];
			$totalmora += $val['saldo'];
			$totalsaldo += $saldo;
		}
		/************************** BODY *****************************************/
		
		/************************** PIE *****************************************/
		/************************** PIE *****************************************/
		$this->pdf->Output();
	}
	
	public function head_carta(){
		return array("doc"=>array(40,"Documento",0,'L')
					,"fecha_venta"=>array(25,"Fecha",0,'C')
					,"fech_vnto"=>array(25,"Fecha Vto",0,'C')
					,"dias"=>array(20,"Dias Vto",0,'C')
					,"importe"=>array(26,"Importe",0,'R')
					,"pago"=>array(26,"Pagos",0,'R')
					,"saldo"=>array(28,"Saldo",1,'R')
		);
	}
	
	public function carta(){
		set_time_limit(0);
		$clientes	= $this->clientes();
		$data		= $this->dataresumido();
		$cuenta		= $this->cuentas_b();
		
		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento","general.ubigeosorsa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		$this->pdf->SetTitle(utf8_decode($this->empresa->get("descripcion")), 11, null, true);
		
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->setFillColor(249, 249, 249);

		
		$this->pdf->SetFont('Arial','',9);
		
		foreach($clientes as $kk=>$value){
			$this->pdf->AddPage();
			$this->pdf->Ln(10);
			$this->pdf->Cell(30,3,utf8_decode("Señor(es)"),0,0,'L');
			$this->pdf->Cell(5,3,":",0,1,'C');
			$this->pdf->Cell(190,4,utf8_decode($value["cliente"]),0,1,'L');
			$this->pdf->Cell(190,4,utf8_decode($value["direccion"]),0,1,'L');
			
			$this->pdf->SetFont('Arial','B',9);
			$this->pdf->Cell(190,3,utf8_decode("Ref. Confirmacion de Saldos"),0,1,'L');
			$this->pdf->Ln();
			
			$this->pdf->Cell(190,3,utf8_decode("Estimado(s) señor(es):"),0,1,'L');
			$this->pdf->SetFont('Arial','',9);
			
			$sms = "Por medio de la presente se le(s) comunica que el Dpto, de Auditoria de la Empresa, mediante el área de CREDITOS Y COBRANZAS ";
			$sms.= "está revisando los saldos a la fecha ".date("d/m/Y").". Por tal razón le(s) hacemos llegar su(s) estado(s) de cuenta, ";
			$sms.= "a efecto de que usted(es), se sirva(n) dar su conformidad, o en su defecto las observaciones pertinentes";
			
			$this->pdf->SetWidths(array("190"));
			$this->pdf->Row(array(utf8_decode($sms)), array("J"), "N", "Y");
			$this->pdf->Ln();
			$this->pdf->SetFont('Arial','B',9);
			foreach($this->head_carta() as $k=>$v){
				$this->pdf->Cell($v[0],7,utf8_decode($v[1]),1,$v[2],$v[3],true);
			}
			
			$this->pdf->SetFont('Arial','',9);
			$credito_cliente = $this->seleccion($data, array("idcliente"=>$value['idcliente']));
			$moneda = "";
			$idmoneda = "";
			$saldo = 0;
			foreach($credito_cliente as $key=>$val){
				foreach($this->head_carta() as $k=>$v){
					$this->pdf->Cell($v[0],5,utf8_decode($val[$k]),'B',$v[2],$v[3],false);
				}
				$moneda = $val["simbolo_moneda"];
				$idmoneda = $val["idmoneda"];
				$saldo+=$val["saldo"];
			}
			// $this->pdf->Ln();
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(40,4,utf8_decode("Siendo su saldo ".$moneda),0,0,'L');
			$this->pdf->Cell(150,4,number_format($saldo,2,'.',','),0,1,'L');
			
			$this->pdf->SetFont('Arial','B',9);
			$this->pdf->Ln(8);
			$this->pdf->Cell(190,3,utf8_decode("CREDITOS Y COBRANZAS"),0,1,'L');
			
			$this->pdf->Cell(100,3,utf8_decode(""),0,0,'L');
			$this->pdf->Cell(90,3,utf8_decode("RESPUESTA"),0,1,'L');
			
			$this->pdf->Cell(80,5,utf8_decode("CONFORME"),0,0,'R');
			$this->pdf->Cell(5,5,utf8_decode(""),1,0,'L');
			$this->pdf->Cell(40,5,utf8_decode(""),0,0,'L');
			$this->pdf->Cell(20,5,utf8_decode("NO CONFORME"),0,0,'R');
			$this->pdf->Cell(5,5,utf8_decode(""),1,1,'L');
			
			$this->pdf->Ln(8);
			$this->pdf->SetFont('Arial','',8.5);
			
			$this->pdf->Cell(40,5,utf8_decode("Recibido y Verificado por:"),0,0,'L');
			$this->pdf->Cell(150,5,utf8_decode(""),"B",1,'L');
			
			$this->pdf->Cell(15,5,utf8_decode("Nombre:"),0,0,'L');
			$this->pdf->Cell(175,5,utf8_decode(""),"B",1,'L');
			
			$this->pdf->Cell(30,5,utf8_decode("OBSERVACIONES:"),0,0,'L');
			$this->pdf->Cell(160,5,utf8_decode(""),"B",1,'L');
			
			$this->pdf->Ln(4);
			$this->pdf->Cell(190,5,utf8_decode("Será muy reconocido si usted(es) deposita(n) o se acerca(n) a honrar su deuda."),0,1,'L');
			
			/*Cuentas de banco*/
			$nro_cuentas = $this->seleccion($cuenta,array("idmoneda"=>$idmoneda));
			$this->pdf->SetFont('Arial','',7.5);
			foreach($nro_cuentas as $k=>$v){
				$this->pdf->Cell(80,5,utf8_decode($v["banco"]),0,0,'L');
				$this->pdf->Cell(5,5,utf8_decode(":"),0,0,'L');
				$this->pdf->Cell(30,5,utf8_decode($v["nro_cuenta"]),0,1,'R');
			}
			/*Cuentas de banco*/
			
			$this->pdf->Ln(4);
			$this->pdf->SetFont('Arial','',8);
			$this->pdf->Cell(190,3,utf8_decode("Agradecíendole(s) la atención que brinde(n) a la presente, quedamos a la espera de su pronta respuesta."),0,1,'L');
			$this->pdf->Cell(190,3,utf8_decode("Atentamente."),0,1,'L');
			
			$this->pdf->Ln(20);
			$this->pdf->Cell(90,5,utf8_decode("Firma y huella digital del cliente"),'T',0,'C');
			$this->pdf->Cell(10,5,utf8_decode(""),0,0,'L');
			$this->pdf->Cell(90,5,utf8_decode("Area de Créditos - Cobranzas"),'T',1,'C');
			
			$this->pdf->Cell(90,5,utf8_decode("Nombre:"),0,0,'L');
			
		}
		
		
		$this->pdf->Output();
	}
	
	public function exportar(){
		set_time_limit(0);
		$datos      = $this->dataresumido();
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento","general.ubigeo"));
		$this->load->library("phpexcel");
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		
		$titulo = "REPORTE DE CREDITOS EN RUTAS";
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin']))
				$titulo.= " DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio']);
			else
				$titulo.= " DE ".fecha_es($_REQUEST['fechainicio']);
		}
		
		$this->insert_logoExcel($Oexcel,$titulo,true);
		
		$col = 9;
		if(!empty($_REQUEST['idubigeo'])){
			$this->ubigeo->find($_REQUEST['idubigeo']);

			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'RUTA : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, $this->ubigeo->get("descripcion"));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$this->sucursal->find($_REQUEST['idsucursal']);
			
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SUCURSAL : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, $this->sucursal->get("descripcion"));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$this->moneda->find($_REQUEST['idmoneda']);
			
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'MONEDA : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, $this->moneda->get("descripcion"));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$this->tipo_documento->find($_REQUEST['idtipodocumento']);
			
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'COMPROBANTE : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, $this->tipo_documento->get("descripcion"));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		if(!empty($_REQUEST['idvendedor'])){
			$this->view_usuario->find($_REQUEST['idvendedor']);
			
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'VENDEDOR : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col, $this->view_usuario->get("user_nombres"));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
	
		$keys  = array('vendedor','cliente','telefono','direcc','zona','doc','fecha_creditos','fech_vnto','dias','importe','pago', 'saldo','linea',   'disponible','dif_90','dif_6090','dif_3060','dif_0130','dif_0');
		$head = array('VEND','CLIENTE','TELEFONO','DIRECCION','ZONA','DOC','FCRED','FVNTO','DIA','IMPORTE','PAGO','SALDO','LC','DISP', 'M90','M6090' ,'M3060' ,'M1030','NOVENCE');
		$colores  = array(null, null, null, null, null, null, null, null, null, null, null, null, null, null,'A5A5A5', 'F13035','FF6600', 'E7E73A','6CFC5C');
		// $sub = array('IMPORTE','PAGO','SALDO','LC');
		$col++;
		
		$alfabeto = 65;
		$corte = 65;
		foreach($head as $val){
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, (" ".$val));
			
			if($val=='CREDITO'){
				$Oexcel->setActiveSheetIndex(0)->mergeCells(chr($alfabeto).$col.':'.chr($alfabeto+3).$col);
				$corte = $alfabeto;
				$alfabeto = $alfabeto + 4;
			}else
				$alfabeto++;
		}
		
		$columna = 0;
		for($i=65;$i<$alfabeto;$i++){
			$Oexcel->getActiveSheet()->getStyle(chr($i).$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->getStyle(chr($i).$col)->applyFromArray(
				array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
			
			if(!empty($colores[$columna]))
				$this->cellColorByColumnAndRow($Oexcel, $columna, $col, $colores[$columna]);
			$columna++;
		}
		$col++;
		
		// foreach($sub as $val){
			// $Oexcel->getActiveSheet()->setCellValue(chr($corte).$col, (" ".$val));
			// $Oexcel->getActiveSheet()->getStyle(chr($corte).$col)->getFont()->setBold(true);
			// $Oexcel->getActiveSheet()->getStyle(chr($corte).$col)->applyFromArray(
				// array('borders' => array(
								// 'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								// 'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								// 'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								// 'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							// )
				// )
			// );
			// $corte++;
		// }
		// $col++;
		
		$fila		= $col;
		foreach ($datos as $k => $val) {
			$columna	= 0;
			$alfabeto = 65;
			foreach($keys as $key){
				if($val['dias']<1)
					$val['dif_0'] = $val['saldo'];
				else if($val['dias']>=1 && $val['dias']<=30)
					$val['dif_0130'] = $val['saldo'];
				else if($val['dias']>=31 && $val['dias']<=60)
					$val['dif_3060'] = $val['saldo'];
				else if($val['dias']>=61 && $val['dias']<=90)
					$val['dif_6090'] = $val['saldo'];
				else if($val['dias']>=91)
					$val['dif_90'] = $val['saldo'];
				
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, ($val[$key]));
				$alfabeto++;
				if(!empty($colores[$columna]))
					$this->cellColorByColumnAndRow($Oexcel, $columna, $fila, $colores[$columna]);
				$columna++;
			}
			
			$fila++;
			$col++;
			// $columna++;
		}
		
		$filename='reportecreditoruta'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
}
?>