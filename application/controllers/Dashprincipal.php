<?php

include_once "Controller.php";

class Dashprincipal extends Controller {
	protected $current_caja = true;
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		$this->js("plugins/easypiechart/jquery.easypiechart");
		 $this->js("plugins/highcharts/highcharts");
		 $this->js("plugins/highcharts/modules/exporting");
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
		$data["conceptos"] = $this->conceptos();
		$data["monedaactiva"] = $this->moneda();
		//$data["iniciales"] = $this->apertura();
		$data["sucursal"] = $this->listsucursal();
		$data["tipomov"] = $this->tipomovimiento();
		$data['idperfil'] = $this->get_var_session("idperfil");

		// combo tipopago
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idtipopago"
				,"name"=>"idtipopago"
				,"class"=>"form-control"
				,"required"=>""
			)
		);
		$query = $this->db->select('idtipopago, descripcion')->where("estado", "A")->get("venta.tipopago");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		$data["tipopago"] = $this->combobox->getObject();
		// combo tipopago

		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return $this->form();
		//$this->index("xxx");
	}
	
	public function conceptos() {
		$sql = "SELECT*FROM caja.conceptomovimiento WHERE estado='A' ORDER BY orden";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function moneda() {
		$sql = "SELECT*FROM general.moneda WHERE estado='A' ORDER BY idmoneda";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function tipomovimiento() {
		$sql = "SELECT*FROM caja.tipomovimiento WHERE estado='A' ORDER BY orden ";
		$query = $this->db->query($sql);
		return $query->result_array();
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

	public function Listaempleados(){
		//$sql ="";//FALTA DEFINIR QUIENES SON LOS EMPLEADOS CON ROL DE CAJEROS
	}
	
	public function return_cajero(){
		$fields = $this->input->post();
		$idsucursal = 0;
		if(!empty($fields['idsucursal']))
			$idsucursal = $fields['idsucursal'];
		
		$sql ="SELECT u.idusuario,u.nombres,u.appat,u.apmat FROM seguridad.acceso_empresa ae
				JOIN seguridad.usuario u ON u.idusuario=ae.idusuario
				WHERE ae.estado='A' 
				AND idtipoempleado='4' 
				AND ae.idsucursal='$idsucursal' ";
		// echo $sql;
		$query = $this->db->query($sql);
		$this->response($query->result_array());
	}

	public function recoger_data(){
		$fields = $this->input->post();

		$sql = "SELECT coalesce(SUM(monto),0.00) monto
				FROM caja.detalle_caja 
				WHERE detalle_caja.idcaja 
				IN (SELECT idcaja FROM caja.caja WHERE estado='A' ";

		$sql.=$this->filtro($fields);
		$query = $this->db->query($sql);
		$this->response($query->row());
	}

	public function recoger_subtotal(){
		$fields = $this->input->post();

		$sql = "SELECT coalesce(SUM(monto),0.00) monto
				FROM caja.detalle_caja 
				WHERE detalle_caja.idcaja 
				IN (SELECT idcaja FROM caja.caja WHERE estado='A' ";
		$sql.=$this->filtro($fields);
		$query = $this->db->query($sql);
		// echo $sql;exit;
		if($fields['type'] == 'json')
			$this->response($query->row());
		else{
			return $query->row();
		}	
	}

	public function recoger_total(){
		$fields = $this->input->post();
		
		$sql = "SELECT coalesce(SUM(monto),0.00) monto
				FROM caja.detalle_caja 
				WHERE detalle_caja.idcaja 
				IN (SELECT idcaja FROM caja.caja WHERE estado='A' ";

		$sql.=$this->filtro($fields);
		$query = $this->db->query($sql);
		
		if($fields['type']=='json')
			$this->response($query->row());
		else
			return $query->row();
	}

	public function return_filas(){
		$fields = $this->input->post();

		$sql = "SELECT fecha,hora,monto,descripcion,serie,numero,referencia,idmoneda,(serie||'-'||numero) doc,iddetalle_caja
				FROM caja.detalle_caja 
				WHERE detalle_caja.idcaja 
				IN (SELECT idcaja FROM caja.caja WHERE estado='A' ";

		$sql.=$this->filtro($fields);
		
		$query = $this->db->query($sql);

		$html ="";
		foreach ($query->result_array() as $key => $value) {
			$html.="<tr>";
			$html.="	<td width='10px'>".($key+1)."</td>";
			$html.="	<td width='80px' style='font-size:11.5px;'>".($value['serie'].'-'.$value['numero'])."</td>";
			$html.="	<td width='180px' style='font-size:11.5px;'>".ucwords(strtolower($value['referencia']))."</td>";
			$html.="	<td width='250px' style='font-size:11.5px;'>".ucwords(strtolower($value['descripcion']))."</td>";
			foreach ($this->moneda() as $k => $v) {
				$monto=0.00;
				if ($v['idmoneda']==$value['idmoneda']) {
					$monto=$value['monto'];
				}
				$html.="	<td width='80px' style='text-align:right;font-size:10.5px;'>".number_format($monto,2)."</td>";
			}
			$html.="</tr>";
		}
		if($fields['type']=='json')
			$this->response($html);
		else 
			return $query->result_array();
	}

	public function filtro($fields){
		$sql="";
		if (!empty($fields['idsucursal'])) {
			$sql.=" AND idsucursal='{$fields['idsucursal']}' ";
		}

		if (!empty($fields['idusuario'])) {
			$sql.=" AND idusuario_apertura='{$fields['idusuario']}' ";
		}

		if ( !isset($fields['idconceptomovimiento']) ) {
			$sql.=" ) AND estado='A' ";
			if (!empty($fields['idtipomovimiento'])) {
				$sql.=" AND idconceptomovimiento IN (SELECT idconceptomovimiento FROM caja.conceptomovimiento WHERE idtipomovimiento ='{$fields['idtipomovimiento']}')";
			}
		}else{
			if ( !empty($fields['idconceptomovimiento']) ) {
				$sql.=" ) AND estado='A' AND idconceptomovimiento='{$fields['idconceptomovimiento']}' ";
			}else{
				$sql.=" ) AND estado='A' ";
			}
		}

		if (isset($fields['idmoneda'])) {
			$sql.=" AND idmoneda='{$fields['idmoneda']}'";
		}

		if ( isset($fields['idtipopago']) && !empty($fields['idtipopago']) ) {
			$sql.=" AND idtipopago='{$fields['idtipopago']}'";
		}

		if (!empty($fields['fecha'])) {
			$sql.=" AND fecha='{$fields['fecha']}' ";
		}else{
			$sql.=" AND fecha=CURRENT_DATE";
		}

		if (!empty($fields['idusuario'])) {
			$sql.=" AND idusuario='{$fields['idusuario']}' ";
		}
		
		// if (!empty($fields['referencia'])) {
			// $sql.=" AND referencia='{$fields['referencia']}' ";
		// }
		
		// if (!empty($fields['idcliente'])) {
			// $sql.=" AND idcliente='{$fields['idcliente']}' ";
		// }
		
		// if (!empty($fields['idtipodocumento'])) {
			// $sql.=" AND idtipodocumento='{$fields['idtipodocumento']}' ";
		// }
		
		// if (!empty($fields['tabla'])) {
			// $sql.=" AND tabla='{$fields['tabla']}' ";
		// }
		
		if (!empty($fields['iddetalle_caja'])) {
			$sql.=" AND iddetalle_caja='{$fields['iddetalle_caja']}' ";
		}
		return $sql;
	}
	
	public function imprimir(){		
		if($_REQUEST['tipo']=='detallado'){
			$this->generarPDFDET();
		}
		
		if($_REQUEST['tipo']=='resumido'){
			$this->generarPDFRES();
		}
	}
	
	public function seleccion($datos,$id,$key){
		$data = array();
		foreach($datos as $kk=>$vv){
			if($vv[$key]==$id){
				$data[]=$vv;
			}
		}	
		return $data;
	}
	
	public function generarPDFRES(){
		$this->load->library("pdf");
		
		$this->load_model(array( "seguridad.empresa","seguridad.usuario","credito.estado_credito"));
		
		$this->empresa->find($this->get_var_session("idempresa"));

		$this->empresa->find($this->get_var_session("idempresa"));
		$logo = ver_fichero_valido($this->empresa->get("logo"),"app/img/empresa/");
		if( !empty($logo) ){
			$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		}
		
		$this->pdf->SetTitle(utf8_decode("ARQUEO DE CAJA DE ".$_REQUEST['empleado']." DEL ".$_REQUEST['fecha']), 11, null, true);
		
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		// $this->pdf->SetLeftMargin(4);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);
		

		$this->pdf->Cell(45,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(126,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		$concepto = $this->conceptos();
		$width_pdf=190;
		$width_colum=30;
		$width_sangria = 10;
		$width_pointer = 80;
		$cant_row_moneda = count($this->moneda());
		$total_width_row = $cant_row_moneda*$width_colum;
		$width_libre = $width_pdf - $total_width_row;
		$salto = 1;
		
		if($cant_row_moneda>0){
			$salto = 0;
		}
		$this->pdf->SetFont('Arial','B',10);
		
		if(!empty($_REQUEST['idsucursal'])){
			$this->pdf->Cell(30,3,"SUCURSAL",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,$_REQUEST['sucursal'],0,1,'L');
		}
		
		if(!empty($_REQUEST['idtipopago'])){
			$this->pdf->Cell($width_pdf,3,"TIPO PAGO",0,1,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->Cell(5,3,$_REQUEST['tipopago'],0,1,'L');
		}
		

		$this->pdf->Ln(5);
		$this->pdf->SetFont('Arial','B',10);
		$this->pdf->Cell($width_pdf,0,"",1,1,'C');
		$this->pdf->Ln(5);
		$this->pdf->Cell($width_libre,3,"",0,$salto,'C');
		
		foreach($this->moneda() as $k=>$v){
			$salto = 0;
			if(($k+1)==$cant_row_moneda)
				$salto = 1;
			
			$this->pdf->Cell(30,3,$v['descripcion'],0,$salto,'R');
		}
		
		$_POST = $_REQUEST;
		$_POST['type'] = 'server';
		// if( count($this->tipomovimiento())>0 ){
			foreach ($this->tipomovimiento() as $key => $value) {
				$_POST['idtipomovimiento'] = $value['idtipomovimiento'];

				$this->pdf->SetFont('Arial','B',10);
				$this->pdf->Cell($width_pdf,5,$value['alias'],0,1,'L');
				$new_array = $this->seleccion($concepto,$value['idtipomovimiento'],'idtipomovimiento');
				
				if(count($new_array)>0){
					$this->pdf->SetFont('Arial','',10);
					foreach($new_array as $k=>$v){
						$_POST['idconceptomovimiento'] = $v['idconceptomovimiento'];
						
						$this->pdf->Cell(($width_sangria),10," ",0,0,'L');
						$this->pdf->Cell(($width_pdf-$width_sangria-$total_width_row -$width_pointer),10,$v['descripcion'],0,0,'L');
						$this->pdf->Cell(($width_pointer),10,'..................................................................................',0,0,'C');
						foreach($this->moneda() as $k=>$vv){
							$_POST['idmoneda'] = $vv['idmoneda'];
							
							foreach($this->recoger_subtotal() as $kkk=>$vvv){
								$this->pdf->Cell(($width_colum),10,number_format($vvv,2),0,0,'R');
							}
						}
						$this->pdf->Ln();
					}					
				}

				$this->pdf->SetFont('Arial','B',10);
				$this->pdf->Cell(($width_pdf-$total_width_row),10,"TOTAL ".$value['alias'],0,0,'L');
				
				foreach($this->moneda() as $k=>$vv){
					unset($_POST['idconceptomovimiento']);
					$_POST['idmoneda'] = $vv['idmoneda'];
					foreach($this->recoger_total() as $kkk=>$vvv){
						$this->pdf->Cell(($width_colum),10,number_format($vvv,2),0,0,'R');
					}
				}
				
				$this->pdf->Ln(20);
			}
		
			$this->pdf->Cell(($width_pdf-$total_width_row-$width_pointer),10,"SALDO TOTAL ",0,0,'L');
			$this->pdf->Cell(($width_pointer),10,'..................................................................................',0,0,'C');
			foreach($this->moneda() as $k=>$vv){
				unset($_POST['idtipomovimiento']);
				unset($_POST['idconceptomovimiento']);
				$_POST['idmoneda'] = $vv['idmoneda'];
				foreach($this->recoger_total() as $kkk=>$vvv){
					$this->pdf->Cell(($width_colum),10,number_format($vvv,2),0,0,'R');
				}
			}
		// }
		
		$this->pdf->Output();
	}
	
	public function generarPDFDET(){
		$this->load->library("pdf");
		
		$this->load_model(array( "seguridad.empresa","seguridad.usuario","credito.estado_credito"));
		
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		$this->pdf->SetTitle(utf8_decode("ARQUEO DE CAJA DE ".$_REQUEST['empleado']." DEL ".$_REQUEST['fecha']), 11, null, true);
		
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(2);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);
		

		$this->pdf->Cell(45,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(126,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		$concepto = $this->conceptos();
		$width_pdf=205;
		$width_colum=30;
		$width_sangria = 5;
		$width_subsangria = 10;
		$width_pointer = 80;
		$cant_row_moneda = count($this->moneda());
		$total_width_row = $cant_row_moneda*$width_colum;
		$width_libre = $width_pdf - $total_width_row;
		$salto = 1;
		
		if($cant_row_moneda>0){
			$salto = 0;
		}
		$this->pdf->SetFont('Arial','B',10);
		
		if(!empty($_REQUEST['idsucursal'])){
			$this->pdf->Cell(30,3,"SUCURSAL",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,$_REQUEST['sucursal'],0,1,'L');
		}
		
		if(!empty($_REQUEST['idtipopago'])){
			$this->pdf->Cell($width_pdf,3,"TIPO PAGO",0,1,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->Cell(5,3,$_REQUEST['tipopago'],0,1,'L');
		}
		
		$filas_detalle = array( array('doc','15','L')
								,array('referencia','50','L')
								,array('descripcion','70','L')
						);

		$this->pdf->Ln(5);
		$this->pdf->SetFont('Arial','B',10);
		$this->pdf->Cell($width_pdf,0,"",1,1,'C');
		$this->pdf->Ln(5);
		$this->pdf->Cell($width_libre,3,"",0,$salto,'C');
		
		foreach($this->moneda() as $k=>$v){
			$salto = 0;
			if(($k+1)==$cant_row_moneda)
				$salto = 1;
			
			$this->pdf->Cell(30,3,$v['descripcion'],0,$salto,'R');
		}
		
		$_POST = $_REQUEST;
		$_POST['type'] = 'server';
		// if( count($this->tipomovimiento())>0 ){
			foreach ($this->tipomovimiento() as $key => $value) {
				$_POST['idtipomovimiento'] = $value['idtipomovimiento'];
				if(count($this->return_filas())>0){
					$this->pdf->SetFont('Arial','B',10);
					$this->pdf->Cell($width_pdf,5,$value['alias'],0,1,'L');
					$new_array = $this->seleccion($concepto,$value['idtipomovimiento'],'idtipomovimiento');					
					if(count($new_array)>0){
						foreach($new_array as $k=>$v){
							$_POST['idconceptomovimiento'] = $v['idconceptomovimiento'];
							if(count($this->return_filas())>0){
								
								$this->pdf->SetFont('Arial','B',10);
								$this->pdf->Cell(($width_sangria),10," ",0,0,'L');
								$this->pdf->Cell(($width_pdf-$width_sangria-$total_width_row -$width_pointer),5,$v['descripcion'],0,1,'L');
								
								$this->pdf->SetFont('Arial','',8);
								foreach($this->return_filas() as $ky=>$m){
									$_POST['iddetalle_caja'] = $m['iddetalle_caja'];
									$this->pdf->Cell(($width_subsangria),4," ",0,0,'L');
									foreach($filas_detalle as $f=>$b){
										$this->pdf->Cell(($b[1]),4,ucwords(strtolower($m[$b[0]])),0,0,$b[2]);
									}
									
									foreach($this->moneda() as $k=>$vv){
										$_POST['idmoneda'] = $vv['idmoneda'];
											foreach($this->recoger_subtotal() as $j=>$i){
												$this->pdf->Cell(($width_colum),4,number_format($i,2),0,0,'R');
											}
									}
									$this->pdf->Ln();
								}
								
								$this->pdf->SetFont('Arial','B',8);
								$this->pdf->Cell(($width_sangria),0,'',0,0,'L');
								$this->pdf->Cell(($width_pdf-$width_sangria*2),0,'',1,1,'L');
								$this->pdf->Cell(($width_sangria),0,'',0,1,'L');
								
								$this->pdf->Cell(($width_pdf-$total_width_row),4,'',0,0,'L');
								unset($_POST['iddetalle_caja']);
								foreach($this->moneda() as $k=>$vv){
									$_POST['idmoneda'] = $vv['idmoneda'];
									foreach($this->recoger_subtotal() as $j=>$i){
										$this->pdf->Cell(($width_colum),4,number_format($i,2),0,0,'R');
									}
								}
							}
							
							unset($_POST['idmoneda']);
							$this->pdf->Ln();
						}
						$this->pdf->SetFont('Arial','B',10);
						$this->pdf->Cell(($width_pdf-$total_width_row),4,'TOTAL '.$value['alias'],0,0,'L');
						foreach($this->moneda() as $k=>$vv){
							unset($_POST['idconceptomovimiento']);
							$_POST['idmoneda'] = $vv['idmoneda'];
							foreach($this->recoger_total() as $kkk=>$vvv){
								$this->pdf->Cell(($width_colum),4,number_format($vvv,2),0,0,'R');
							}
						}
						$this->pdf->Ln();
						
					}
				}
				
				
				$this->pdf->Ln(10);
			}
		
			$this->pdf->Cell(($width_pdf-$total_width_row-$width_pointer),10,"SALDO TOTAL ",0,0,'L');
			$this->pdf->Cell(($width_pointer),10,'...........................................................................................',0,0,'R');
			foreach($this->moneda() as $k=>$vv){
				unset($_POST['idtipomovimiento']);
				unset($_POST['idconceptomovimiento']);
				$_POST['idmoneda'] = $vv['idmoneda'];
				foreach($this->recoger_total() as $kkk=>$vvv){
					$this->pdf->Cell(($width_colum),10,number_format($vvv,2),0,0,'R');
				}
			}
		// }
		
		$this->pdf->Output();
	}
}
?>