<?php

include_once "Controller.php";

class Reportereciboegresos extends Controller {
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
		$this->combobox->init();
		$this->combobox->setAttr("id","idtipopago");
		$this->combobox->setAttr("name","idtipopago");
		$this->combobox->setAttr("class","input-xs form-control");
		$this->db->select('idtipopago,descripcion');
		$query = $this->db->where("estado","A")->where("mostrar_en_reciboingreso","S")->order_by("descripcion")->get("venta.tipopago");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['tipopago'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idmoneda");
		$this->combobox->setAttr("name","idmoneda");
		$this->combobox->setAttr("class","input-xs form-control");
		$this->db->select('idmoneda,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.moneda");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['moneda'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idtipodocumento");
		$this->combobox->setAttr("name","idtipodocumento");
		$this->combobox->setAttr("class","form-control");
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
		if ($this->get_var_session("idperfil")!=1) {// SI NO ES ADMINOSTRADOR LA BUSQUEDA SOLO ES POR LA SESION INICIADA
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
		$sql = "SELECT recibo_egreso_view.*
				FROM venta.recibo_egreso_view
				WHERE estado='A'
				{$this->condicion_resumido()}
				ORDER BY {$_REQUEST['orden']}";
		$query      = $this->db->query($sql);
		// echo $sql;exit;
		$data = $query->result_array();
		// print_r($query->result_array());exit;
		return $data;
	}
	
	public function moneda_colum(){
		$sql = "SELECT  idmoneda
				,moneda
				,simbolo
				,abreviatura
				FROM venta.recibo_egreso_view
				WHERE estado='A'
				{$this->condicion_resumido()}
				GROUP BY idmoneda
				,moneda
				,simbolo
				,abreviatura
				ORDER BY moneda;";
		$query= $this->db->query($sql);
		// echo $sql;exit;
		// print_r($query->result_array());exit;
		$data = $query->result_array();			
		if(empty($data)){
			$sql = "SELECT idmoneda, descripcion moneda, simbolo, abreviatura FROM general.moneda WHERE idmoneda='1';";
			$query= $this->db->query($sql);
			$data = $query->result_array();			
		}
		return $data;
	}
	
	public function totales_monedas($where_and = ''){
		$sql = "SELECT 
				COALESCE(SUM(monto),0) monto
				FROM venta.recibo_egreso_view 
				WHERE estado='A'
				{$this->condicion_resumido($where_and)}
				";
		// echo $sql."<br>";
		$query= $this->db->query($sql);

		$data = $query->row('monto');			
		
		return $data;
	}
	
	public function condicion_resumido($add_where=''){
		$where = "";
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin'])){
				$where.=" AND fecha>='{$_REQUEST['fechainicio']}' AND (fecha)<='{$_REQUEST['fechafin']}'";
			}else{
				$where.=" AND fecha='{$_REQUEST['fechainicio']}' ";
			}
		}
		
		if(!empty($_REQUEST['serie_d'])){
			if(!empty($_REQUEST['numero_d'])){
				if(!empty($_REQUEST['serie_h']) && !empty($_REQUEST['numero_h']) ){
						$where.=" AND (serie||'-'||numero)>='{$_REQUEST['serie_d']}-{$_REQUEST['numero_d']}' ";
						$where.=" AND (serie||'-'||numero)<='{$_REQUEST['serie_h']}-{$_REQUEST['numero_h']}' ";
				}else{
					$where.=" AND (serie||'-'||numero)>='{$_REQUEST['serie_d']}-{$_REQUEST['numero_d']}' ";
				}
			}else{
			}
		}
		
		if(!empty($_REQUEST['idcliente'])&&!empty($_REQUEST['cliente'])){
			$where.=" AND idcliente='{$_REQUEST['idcliente']}' ";
			if(!empty($_REQUEST['entidad'])){
				$where.=" AND entidad='{$_REQUEST['entidad']}' ";
			}
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$where.=" AND idmoneda='{$_REQUEST['idmoneda']}' ";
		}
		
		if(!empty($_REQUEST['idtipopago'])){
			$where.=" AND idtipopago='{$_REQUEST['idtipopago']}' ";
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$where.=" AND idsucursal='{$_REQUEST['idsucursal']}' ";
		}
		
		
		$where.=$add_where;
		return $where;
	}
	
	public function imprimir(){
		$this->resumido();
	}
	
	public function resumido(){
		$datos      = $this->dataresumido();
		$monedas = $this->moneda_colum();
		$whit_compr=25;
		$whit_fecha=18;
		$whit_prov=90;
		$whit_min = 25;//PARA EL WHIT DE LAS MONEDAS
		$whit_total_m = 25;//PARA EL WHIT DE LAS MONEDAS
		
		if(!empty($monedas)){
			$whit_total_m = $whit_min*count($monedas);
			$whit_prov = $whit_prov - $whit_min*(count($monedas)-1);
		}

		$cabecera = array('nro_recibo'=> array('RECIBO',$whit_min)
							,'fecha_operacion' => array('FECHA',$whit_fecha)
							,'entidad' => array('REFERENCIA',$whit_prov)
							,'tipopago' => array('TIPO PAGO',$whit_compr)
							,'cajero' => array('CAJERO',$whit_fecha)
						);

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","seguridad.usuario"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetDrawColor(204, 204, 204);
		$this->pdf->setFillColor(249, 249, 249);
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio']))
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTE RECIBO INGRESO DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE RECIBO INGRESO DE ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		else
			$this->pdf->SetTitle(utf8_decode("REPORTE RECIBO INGRESO "), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		
		$this->pdf->SetFont('Arial','',10);
		
		if(!empty($_REQUEST['idsucursal'])){
			$this->pdf->Cell($whit_compr,5,"SUCURSAL",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,$_REQUEST['sucursal'],0,1,'L');
		}
		
		if(!empty($_REQUEST['idcliente'])){
			$this->pdf->Cell($whit_compr,5,"CLIENTE",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,$_REQUEST['cliente'],0,1,'L');
		}
		
		if(!empty($_REQUEST['idtipopago'])){
			$this->pdf->Cell($whit_compr,5,"TIPO PAGO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->Cell(5,3,$_REQUEST['tipopago'],0,1,'L');
		}
		
		if(!empty($_REQUEST['idmoneda'])){
			$this->pdf->Cell($whit_compr,5,"MONEDA",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->Cell(5,3,$_REQUEST['moneda'],0,1,'L');
		}
		$this->pdf->Ln(5);
		$this->pdf->SetFont('Arial','B',8);

		/************************** CABECERA *****************************************/
		$total_lienzo = 0;
		foreach ($cabecera as $key => $val) {
			$this->pdf->Cell($val[1],9,$val[0],1,0);
			$total_lienzo = $total_lienzo + $val[1];
		}
		$this->pdf->Cell($whit_total_m,5,"TOTAL",1,1,'C');
		$this->pdf->Cell($total_lienzo,5,"",0,0,'C');
		foreach($monedas as $key=>$val){
			$this->pdf->Cell($whit_min,4,$val['abreviatura']." ".$val['simbolo'],1,0,'C');			
		}
		$this->pdf->Ln(10); 
		/************************** CABECERA *****************************************/
		

		$this->pdf->SetFont('Arial','',8);
		$x_total = 0;
		/************************** BODY *****************************************/
		foreach ($datos as $key => $val) {
			foreach ($cabecera as $k => $v) {
				$colum = $val[$k];
				if(isset($v[2])){
					$this->pdf->Cell($v[1],5,$colum,1,0,$v[2]);
				}else{
					$this->pdf->Cell($v[1],5,$colum,1,0);
				}
			}
			
			foreach($monedas as $k=>$v){
				$subt = 0;
				if($v['idmoneda']==$val['idmoneda']){
					$subt = $val['monto'];
				}
				$this->pdf->Cell($whit_min,5,number_format($subt,2,'.',','),1,0,'R');			
			}
			$this->pdf->Ln(); 
		}
		/************************** BODY *****************************************/
		
		
		/************************** PIE *****************************************/
		$this->pdf->SetFont('Arial','B',8);
		$this->pdf->Cell(($whit_compr + $whit_fecha*2 + $whit_prov + $whit_min),5,"TOTAL",0,0,'R');
		foreach($monedas as $k=>$v){
			$filtro = $this->totales_monedas(" AND idmoneda='$v[idmoneda]' ");
			$this->pdf->Cell($whit_min,5,number_format($filtro,2,'.',','),0,0,'R');			
		}
		/************************** PIE *****************************************/
		$this->pdf->Output();
	}
}
?>