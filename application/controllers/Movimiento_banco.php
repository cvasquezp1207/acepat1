<?php
include_once "Controller.php";

class Movimiento_banco extends Controller {
	public function init_controller() {
		// $this->set_title("Movimiento de Bancos");
	}
	
	public function end_controller() {
		$this->js('form/'.$this->controller.'/index');
	}
	
	public function form($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		$this->load->library('combobox');

		$data["controller"]		= $this->controller;
		$data["sucursal"]		= $this->listsucursal();
		$data["all_sucursal"]	= $this->get_var_session("control_reporte")? $this->get_var_session("control_reporte"):'N';
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idbanco");
		$this->combobox->setAttr("name","idbanco");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($this->get_bancos());
		$data['banco'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idmoneda");
		$this->combobox->setAttr("name","idmoneda");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($this->get_moneda());
		$data['moneda'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");

		return $this->load->view($this->controller."/form", $data, true);
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
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return $this->form();
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
		$sql = "SELECT cb.*
				,u.usuario
				,mv.idoperacion
				,mv.tabla
				,CASE WHEN mv.estado='A' THEN mv.nro_operacion ELSE '**ANULADO' END nro_operacion
				,to_char(mv.fecha,'DD/MM/YYYY') fecha_operacion
				,mv.hora hora_operacion
				,to_char(mv.fecha_deposito,'DD/MM/YYYY') fecha_deposito
				,CASE WHEN mv.estado='A' THEN (CASE WHEN COALESCE(tm.simbolo,'E')='E' THEN mv.importe ELSE mv.importe*(-1) END) ELSE 0.00 END  importe
				,COALESCE(tm.simbolo,'E') simbolo
				,mv.fecha
				,cb.moneda_corto moneda
				FROM venta.movimiento_deposito mv
				JOIN general.view_cuentas_bancarias cb ON cb.idcuentas_bancarias=mv.idcuentas_bancarias
				JOIN general.banco b ON b.idbanco=cb.idbanco 
				JOIN seguridad.view_usuario u ON u.idusuario=mv.idusuario
				LEFT JOIN caja.conceptomovimiento concp ON concp.idconceptomovimiento=mv.idconceptomovimiento
				LEFT JOIN caja.tipomovimiento tm ON tm.idtipomovimiento=concp.idtipomovimiento
				AND b.estado='A'";
		return $sql;
	}
	
	public function get_moneda(){		
		$q=$this->db->query("SELECT DISTINCT idmoneda,moneda_corto moneda FROM ({$this->query_master()}) b WHERE estado='A' ORDER BY idmoneda");
		return $q->result_array();
	}
	
	public function get_bancos(){
		$post	=	$_REQUEST;
		
		$sql = "SELECT DISTINCT idbanco,banco FROM ({$this->query_master()}) b WHERE estado='A' ";
		if(isset($post["idbanco"]) && !empty($post["idbanco"]))
			$sql.=" AND idbanco='{$post["idbanco"]}' ";
		$sql.=" ORDER BY banco";
		$q=$this->db->query($sql);
		return $q->result_array();
	}
	
	public function get_cuentasb(){
		$post	=	$_REQUEST;
		
		$where = "";
		if(!empty($post['idbanco']))
			$where.= " AND idbanco='{$post['idbanco']}'";
		else
			$where.= " AND idbanco='0'";
		
		if(!empty($post['idmoneda']))
			$where.= " AND idmoneda='{$post['idmoneda']}'";
		
		$sql = "SELECT DISTINCT idcuentas_bancarias,cuenta,cuenta||'     '||sucursal cuenta_sucursal,moneda_corto FROM ({$this->query_master()}) b WHERE estado='A' {$where} ORDER BY cuenta";
		// echo $sql;exit;
		$q=$this->db->query($sql);
		
		if($post['type']=='json')
			$this->response($q->result_array());
		else
			return $q->result_array();
	}
	
	public function dataresumido(){
		$sql = "SELECT * FROM ({$this->query_master()}) b WHERE estado IN ('A','I') {$this->filtro()} ORDER BY fecha ASC";
		/* echo $sql;exit; */
		$q=$this->db->query($sql);
		return $q->result_array();
	}
	
	public function filtro(){
		$post	=	$_REQUEST;
		
		$where = "";
		if(!empty($post['fechainicio'])){
			if(!empty($post['fechafin']))
				$where.= " AND fecha>='{$post['fechainicio']}'  AND fecha<='{$post['fechafin']}' ";
			else
				$where.= " AND fecha='{$post['fechainicio']}'";
		}
		
		if(!empty($post['idbanco']))
			$where.= " AND idbanco='{$post['idbanco']}'";
		
		if(!empty($post['idmoneda']))
			$where.= " AND idmoneda='{$post['idmoneda']}'";
		
		if(isset($post['idcuentas_bancarias']) && !empty($post['idcuentas_bancarias']))
			$where.= " AND idcuentas_bancarias IN(".implode(",", $post['idcuentas_bancarias']).") ";
		
		if(!empty($post['idsucursal']))
			$where.= " AND idsucursal='{$post['idsucursal']}'";
		
		return $where;
	}
	
	public function array_head(){
		$whit_item		= 10;
		$whit_user		= 26;
		$whit_fecha		= 18;
		$whit_hora		= 20;
		$whit_opera		= 50;
		$whit_in		= 26;
		$whit_importe	= 20;
		
		$total_ancho	= $whit_item + $whit_user + $whit_fecha*2 +$whit_hora + $whit_opera + $whit_in + $whit_importe;

		$cabecera = array( 'item' => array('ITEM',$whit_item,$whit_item*100/$total_ancho,0,'L')
							,'usuario' => array('USUARIO',$whit_user,$whit_user*100/$total_ancho,0,'L')
							,'fecha_operacion' => array('F. OPERAC',$whit_fecha,$whit_fecha*100/$total_ancho,0,'L')
							,'hora_operacion' => array('HORA OPEAC',$whit_hora,$whit_hora*100/$total_ancho,0,'C')
							,'nro_operacion' => array('NRO. OPERAC',$whit_opera,$whit_opera*100/$total_ancho,0,'L')
							,'fecha_deposito' => array('F. DEPOSITO',$whit_fecha,$whit_fecha*100/$total_ancho,0,'C')
							,'sucursal' => array('OPERADO EN',$whit_in,$whit_in*100/$total_ancho,0,'R')
							,'importe' => array('IMPORTE',$whit_importe,$whit_importe*100/$total_ancho,1,'R')
						);
						
		return $cabecera;
	}
	
	public function imprimir(){
		$this->resumido();
	}
	
	public function resumido(){
		set_time_limit(0);
		$cabecera	= $this->array_head();
		$banco		= $this->get_bancos();
		$filas		= $this->dataresumido();
		

		$_REQUEST['type']	=	'server';
		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento","venta.tipopago","general.zona","general.ubigeosorsa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("MOVIMIENTO DE BANCOS DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechafin'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("MOVIMIENTO DE BANCOS DE ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(utf8_decode("MOVIMIENTO DE BANCOS "), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);

		$this->pdf->SetFont('Arial','B',8);
		$lienzo = 0;
		$space	= 5;
		
		$key_numeric = array("importe");
		foreach($cabecera as $k=>$v){
			$lienzo+=$v[1];
		}
		$lienzo = $lienzo + $space;
		/************************** CONTENIDO *****************************************/
		$this->pdf->SetDrawColor(204, 204, 204);
		foreach($banco as $key=>$val){
			$this->pdf->SetTextColor(0,0,0);
			$this->pdf->Cell($lienzo,5,utf8_decode($val['banco']),0,1,'L',false);
			
			$_REQUEST["idbanco"] = $val["idbanco"];
			$cuentas = $this->get_cuentasb();
			foreach($cuentas as $k=>$v){
				$moneda = $v['moneda_corto'];
				$view = true;
				if(isset($_REQUEST["idcuentas_bancarias"])){
					if(!in_array($v["idcuentas_bancarias"],$_REQUEST["idcuentas_bancarias"]))
						$view = false;
					else
						$view = true;
				}
				
				if($view){
					$new_array	=	$this->seleccion($filas,array("idbanco"=>$val["idbanco"],"idcuentas_bancarias"=>$v["idcuentas_bancarias"]));
					
					/*HEAD CUENTA*/
					$this->pdf->SetFont('Arial','B',7.8);
					$this->pdf->Cell($space,5,"",0,0,'L',false);
					$this->pdf->Cell($lienzo-$space,5,utf8_decode($v['cuenta_sucursal']),1,1,'L',false);
					
					$this->pdf->Cell($space,5,"",0,0,'L');
					foreach($cabecera as $kk=>$vv){
						$this->pdf->setFillColor(240, 240, 240);
						$this->pdf->Cell($vv[1],5,$vv[0],1,$vv[3],'C',true);
					}
					/*HEAD CUENTA*/
					
					/*BODY CUENTA*/
					$total_movimiento_cuenta = 0;
					$this->pdf->SetFont('Arial','',7.5);

					foreach($new_array as $j=>$i){
						$i["item"]	= $j+1;
						$this->pdf->Cell($space,5,"",0,0,'L');
						if($i['nro_operacion']=='**ANULADO')
							$this->pdf->SetTextColor(194,8,8);
						else
							$this->pdf->SetTextColor(0,0,0);
						foreach($cabecera as $kk=>$vv){
							if($kk=='importe')
								$total_movimiento_cuenta+=$i[$kk];
							if(in_array($kk,$key_numeric))
								$i[$kk] = number_format($i[$kk],3,'.',',');
							
							$this->pdf->Cell($vv[1],5,strtoupper($i[$kk]),1,$vv[3],$vv[4],false);
						}
						$moneda = $i["moneda_corto"];
					}
					/*BODY CUENTA*/
					
					/*FOOT CUENTA*/
					$total_foot = 0;
					foreach($cabecera as $kk=>$vv){
						$total_foot+=$vv[1];
					}
					
					$this->pdf->SetTextColor(0,0,0);
					$this->pdf->SetFont('Arial','B',8);
					$this->pdf->Cell($total_foot-15,5,"TOTAL ".$moneda,0,0,'R');
					$this->pdf->Cell(20,5,number_format($total_movimiento_cuenta,3,'.',','),0,0,'R');
					/*FOOT CUENTA*/
					
					$this->pdf->Ln(8);
				}
			}
			$this->pdf->Ln(5);
		}
		/************************** CONTENIDO *****************************************/
		$this->pdf->Output();
	}
}
?>