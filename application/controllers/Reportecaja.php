<?php

include_once "Controller.php";

class Reportecaja extends Controller {
	protected $current_caja = true;
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
		$data["conceptos"] = $this->conceptos();
		$data["monedaactiva"] = $this->moneda();
		//$data["iniciales"] = $this->apertura();
		$data["sucursal"] = $this->listsucursal();
		$data["tipomov"] = $this->tipomovimiento();
		$data['idperfil'] = $this->get_var_session("idperfil");
		$data['es_superusuario']= $this->get_var_session("es_superusuario")?$this->get_var_session("es_superusuario"):'N';
		// combo tipopago
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idtipopago"
				,"name"=>"idtipopago"
				,"class"=>"form-control input-xs"
				,"required"=>""
				,"multiple"=>""
			)
		);
		$query = $this->db->select('idtipopago, descripcion')->where("estado", "A")->get("venta.tipopago");
		// $this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		$data["tipopago"] = $this->combobox->getObject();
		// combo tipopago
		// $this->css('plugins/iCheck/custom');
		// $this->js('plugins/iCheck/icheck.min');
		// $this->js("<script>$(document).ready(function () {
                // $('.i-checks').iCheck({
                    // checkboxClass: 'icheckbox_square-green',
                    // radioClass: 'iradio_square-green',
                // });
            // });</script>", false);
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
		$sql = "SELECT*FROM caja.conceptomovimiento WHERE estado='A' ORDER BY orden;";
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

	public function Listaempleados(){
		//$sql ="";//FALTA DEFINIR QUIENES SON LOS EMPLEADOS CON ROL DE CAJEROS
	}
	
	public function return_cajero(){
		$fields = $this->input->post();
		$idcajero = $this->get_param('idrol_cajero')?$this->get_param('idrol_cajero'):4;
		$idsucursal = $this->get_var_session("idsucursal");
		if(!empty($fields['idsucursal']))
			$idsucursal = $fields['idsucursal'];
		$codtipoempleado_cajero	= $this->get_param('idtipoempleado_cajero')?$this->get_param('idtipoempleado_cajero'):'0';
		$es_superadmin			= $this->get_var_session("es_superusuario")?$this->get_var_session("es_superusuario"):'N';
		$id_usuario				= $this->get_var_session("idusuario");
		
		$sql ="	SELECT u.idusuario,u.nombres,u.appat,u.apmat 
				FROM seguridad.acceso_empresa ae
				JOIN seguridad.usuario u ON u.idusuario=ae.idusuario
				WHERE ae.estado='A' 
				AND idtipoempleado='{$idcajero}' 
				AND ae.idsucursal='$idsucursal' ";
		if($es_superadmin=='N')
			$sql.=" AND ae.idusuario='{$id_usuario}'";

		$query = $this->db->query($sql);
		$this->response($query->result_array());
	}

	public function recoger_data(){
		$fields = $this->input->post();

		$sql = "SELECT COALESCE(SUM((CASE WHEN estado='A' THEN monto ELSE 0 END)),0) monto
				FROM caja.detalle_caja 
				WHERE detalle_caja.idcaja 
				IN (SELECT idcaja FROM caja.caja WHERE estado='A' ";

		$sql.=$this->filtro($fields);
		$query = $this->db->query($sql);
		$this->response($query->row());
	}

	public function recoger_subtotal(){
		$fields = $this->input->post();

		$sql = "SELECT COALESCE(SUM((CASE WHEN estado='A' THEN monto ELSE 0 END)),0) monto
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
		
		$sql = "SELECT COALESCE(SUM((CASE WHEN estado='A' THEN monto ELSE 0 END)),0)monto
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

		$sql = "SELECT dc.fecha
				,dc.hora
				,CASE WHEN dc.estado='A' THEN dc.monto ELSE 0 END monto
				,CASE WHEN dc.descripcion = 'PAGO DE LETRAS' THEN 'PL'||'-'||v.nombres ELSE dc.descripcion END descripcion
				,dc.descripcion descripcion2
				,dc.serie
				,dc.numero
				,CASE WHEN dc.estado='A' THEN dc.referencia ELSE '**ANULADO' END referencia
				,dc.idmoneda
				,COALESCE((SELECT abreviatura FROM venta.tipo_documento td WHERE td.idtipodocumento=dc.idtipodocumento)||' ','')||dc.serie||'-'||dc.numero doc
				,dc.iddetalle_caja
				,(SELECT usuario FROM seguridad.view_usuario u WHERE u.idusuario=dc.idusuario) cajero
				,(SELECT descripcion FROM venta.tipopago WHERE tipopago.idtipopago=dc.idtipopago) tipopago
				FROM caja.detalle_caja dc
				left join  venta.cliente v on v.idcliente= dc.idcliente
				WHERE dc.idcaja 
				IN (SELECT idcaja FROM caja.caja WHERE estado='A' ";

		$sql.=$this->filtro($fields)." ORDER BY iddetalle_caja";//.$_REQUEST['orden'];
		// echo $sql;exit;
		$query = $this->db->query($sql);

		$html ="";
		foreach ($query->result_array() as $key => $value) {
			$html.="<tr>";
			$html.="	<td>".($key+1)."</td>";
			$html.="	<td style='font-size:11.5px;'>".($value['serie'].'-'.$value['numero'])."</td>";
			$html.="	<td style='font-size:11.5px;'>".date("g:i:s a",strtotime($value['hora']))."</td>";
			$html.="	<td style='font-size:11.5px;'>".ucwords(strtolower($value['referencia']))."</td>";
			$html.="	<td style='font-size:11.5px;'>".ucwords(strtolower($value['descripcion']))."</td>";
			foreach ($this->moneda() as $k => $v) {
				$monto=0.00;
				if ($v['idmoneda']==$value['idmoneda']) {
					$monto=$value['monto'];
				}
				$html.="	<td style='text-align:right;font-size:10.5px;'>".number_format($monto,2)."</td>";
			}
			$html.="</tr>";
		}
		if($fields['type']=='json'){
			$xx=$html;
			$this->response($xx);
		}else 
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
			// $sql.=" ) AND estado='A' ";
			$sql.=" )  ";
			if (!empty($fields['idtipomovimiento'])) {
				$sql.=" AND idconceptomovimiento IN (SELECT idconceptomovimiento FROM caja.conceptomovimiento WHERE idtipomovimiento ='{$fields['idtipomovimiento']}')";
			}
		}else{
			if ( !empty($fields['idconceptomovimiento']) ) {
				// $sql.=" ) AND estado='A' AND idconceptomovimiento='{$fields['idconceptomovimiento']}' ";
				$sql.=" ) AND idconceptomovimiento='{$fields['idconceptomovimiento']}' ";
			}else{
				$sql.=" ) ";
				// $sql.=" ) AND estado='A' ";
			}
		}

		if (isset($fields['idmoneda'])) {
			$sql.=" AND idmoneda='{$fields['idmoneda']}'";
		}

		if ( isset($fields['idtipopago']) && !empty($fields['idtipopago']) ) {
			// $sql.=" AND idtipopago='{$fields['idtipopago']}'";
			$id_tipopago = explode(",",$fields['idtipopago']);
			$sql.=" AND idtipopago IN ('".implode("','", $id_tipopago)."') ";
		}else if(isset($fields['id_tipopago']) && !empty($fields['id_tipopago'])){
			$id_tipopago = explode(",",$fields['id_tipopago']);
			$sql.=" AND idtipopago IN ('".implode("','", $id_tipopago)."') ";
		}

		if (!empty($fields['fecha'])) {
			$sql.=" AND fecha='{$fields['fecha']}' ";
		}else{
			$sql.=" AND fecha=CURRENT_DATE";
		}

		if (!empty($fields['idusuario']) && $fields["idusuario"]!= 'null') {
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
		return $sql."";
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
	////Resumido
	public function generarPDFRES(){
		$this->load->library("pdf");
		
		$this->load_model(array( "seguridad.empresa","seguridad.usuario","credito.estado_credito","venta.tipopago"));
		
		$this->empresa->find($this->get_var_session("idempresa"));

		$logo = ver_fichero_valido($this->empresa->get("logo"),"app/img/empresa/");
		if( !empty($logo) ){
			$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		}
		
		$this->pdf->SetTitle(utf8_decode("ARQUEO DE CAJA DE ".$_REQUEST['empleado']." DEL ".fecha_es($_REQUEST['fecha'])), 11, null, true);
		
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
		$width_pdf=150;
		$width_colum=25;
		$width_sangria = 15;
		$width_pointer = 50;
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
		
		if(!empty($_REQUEST['id_tipopago'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"TIPO PAGO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			
			$id_tipopago = explode(",",$_REQUEST['id_tipopago']);
			$this->pdf->SetFont('Arial','',10);
			foreach($id_tipopago as $v){
				$this->tipopago->find($v);
				$this->pdf->Cell(30,3,$this->tipopago->get("descripcion")." || ",0,0,'C');
			}
			$this->pdf->Ln();
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
			//Abreviaturas de Monedas
			$this->pdf->Cell(25,3,$v['abreviatura'],0,$salto,'R');
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
						
						//sangria de detalles
						$this->pdf->Cell(($width_sangria),10," ",0,0,'L');
						$this->pdf->Cell(($width_pdf-$width_sangria-$total_width_row -$width_pointer),10,$v['descripcion'],0,0,'L');
						$this->pdf->Cell(($width_pointer),10,'..................................................................................',0,0,'C');
						foreach($this->moneda() as $k=>$vv){
							$_POST['idmoneda'] = $vv['idmoneda'];
							
							foreach($this->recoger_subtotal() as $kkk=>$vvv){
								$this->pdf->Cell(($width_colum),10,number_format($vvv,2),0,0,'R');
								//$this->pdf->Cell((20),10,number_format($vvv,2),0,0,'R');
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
			$this->pdf->Cell(($width_pdf),0," ",1,1,'L');
			$this->pdf->Ln(1);
			$this->pdf->Cell(($width_pdf),0," ",1,1,'L');
			
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
	
	
	//Detallado
	public function generarPDFDET(){
		$this->load->library("pdf");
		
		$this->load_model(array( "seguridad.empresa","seguridad.usuario","credito.estado_credito","venta.tipopago"));
		
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		$this->pdf->SetTitle(utf8_decode("ARQUEO DE CAJA DE ".$_REQUEST['empleado']." DEL ".fecha_es($_REQUEST['fecha'])), 11, null, true);
		
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
		$width_pdf=203;
		$width_colum=15;
		$width_sangria = 5;
		$width_subsangria = 10;
		$width_pointer = 80;
		$cant_row_moneda = count($this->moneda());
		$total_width_row = $cant_row_moneda*$width_colum;
		
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
		if(!empty($_REQUEST['id_tipopago'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"TIPO PAGO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			
			$id_tipopago = explode(",",$_REQUEST['id_tipopago']);
			$this->pdf->SetFont('Arial','',10);
			foreach($id_tipopago as $v){
				$this->tipopago->find($v);
				$this->pdf->Cell(30,3,$this->tipopago->get("descripcion")." || ",0,0,'C');
			}
			$this->pdf->Ln();
		}
		$ancho_moneda=15;
		$ancho_ref=65;// cambiar aca tambien
		$ancho_doc=20;// cambiar aca tambien
		$reducir = 0;
		if(count($this->moneda())>=1){
			if(count($this->moneda())>1){
				$reducir = $ancho_moneda/(count($this->moneda()))*2;//2 es por la columna referencia y concepto
			}else{
				$reducir = $ancho_moneda/(count($this->moneda())*2);//2 es por la columna referencia y concepto
			}
		}
		$ancho_desc=95;

		$filas_detalle = array( array('doc',$ancho_doc,'L')
								,array('referencia',$ancho_ref-$reducir,'L')
								// ,array('referencia',$ancho_ref-13,'L')
								,array('descripcion',$ancho_desc-$reducir-15,'L')
								,array('tipopago',$ancho_doc,'L')
						);
		$total_sangria = $width_subsangria+$width_sangria-3; // aqui cambiar tambien -3
		if(count($this->moneda())>0){
			$width_libre = $ancho_ref + $width_subsangria + $ancho_doc + $width_sangria + $ancho_desc - ($ancho_moneda*count($this->moneda()));
		}else{
			$width_libre = $ancho_ref + $width_subsangria + $ancho_doc + $width_sangria + $ancho_desc;
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
			
			$this->pdf->Cell($ancho_moneda,3,$v['abreviatura']." ".$v['simbolo'],0,$salto,'R');
		}
		
		$_POST = $_REQUEST;
		$_POST['type'] = 'server';
		
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
								$this->pdf->Cell(($width_sangria),10," ",0,0,'L');//  cambiar aqui para encabezados de saldos inicial
								$this->pdf->Cell(($width_pdf-$width_sangria-$total_width_row -$width_pointer),5,$v['descripcion'],0,1,'L');
								
								$this->pdf->SetFont('Arial','',7.5);
								foreach($this->return_filas() as $ky=>$m){
									$_POST['iddetalle_caja'] = $m['iddetalle_caja'];
									$this->pdf->Cell(($total_sangria),4," ",0,0,'L');
									$this->pdf->setFillColor(249, 249, 249);
                                    $this->pdf->SetDrawColor(204, 204, 204);
									
									// $filas_print = array();
									/*For file autosize*/
									$values = array();
									$width = array();
									$pos = array();
									/*For file autosize*/
									
									foreach($filas_detalle as $f=>$b){
										$values[] = utf8_decode(ucwords(($m[$b[0]])));
										$width[] = $b[1];
										$pos[] = $b[2];
										// $filas_print[] = array($b[1],4,utf8_decode(ucwords(strtolower($m[$b[0]]))) , 1,0, $b[2]);
										// $this->pdf->Cell(($b[1]),4,utf8_decode(ucwords(strtolower($m[$b[0]]))),1,0,$b[2]);
									}
									
									foreach($this->moneda() as $k=>$vv){
										$_POST['idmoneda'] = $vv['idmoneda'];
											foreach($this->recoger_subtotal() as $j=>$i){
												// $this->pdf->Cell(($width_colum),4,number_format($i,2),1,0,'R');
												// $filas_print[] = array($width_colum,4,number_format($i,2) , 1,0, 'R');
												$values[] = number_format($i,2);
												$width[] = $width_colum;
												$pos[] = 'R';
											}
									}
									
									if($m['referencia']=='**ANULADO'){
										$this->pdf->SetTextColor(194,8,8);
									}else{
										$this->pdf->SetTextColor(0,0,0);
									}
									
									$this->pdf->SetFont('Arial','',6);
									$this->pdf->SetWidths($width);
									$this->pdf->Row($values, $pos, "Y", "Y");
									
									// $this->pdf->Ln();
								}
								$this->pdf->setFillColor(0, 0, 0);
                                $this->pdf->SetDrawColor(0, 0, 0);
								$this->pdf->SetFont('Arial','B',8);
								$this->pdf->Cell(($width_sangria),0,'',0,0,'L');
								$this->pdf->Cell(($width_pdf-$width_sangria),0,'',0,1,'L');
								$this->pdf->Cell(($width_sangria),0,'',0,1,'L');
								$this->pdf->SetTextColor(0,0,0);
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
						$this->pdf->SetFont('Arial','B',8.5);
						$this->pdf->Cell(($width_pdf),0," ",1,1,'L');
						$this->pdf->SetTextColor(0,0,0);
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
				
				unset($_POST['idmoneda']);
				$this->pdf->Ln(10);
			}
			$this->pdf->Cell(($width_pdf),0," ",1,1,'L');
			$this->pdf->Ln(1);
			$this->pdf->Cell(($width_pdf),0," ",1,1,'L');
			$this->pdf->SetTextColor(0,0,0);
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
		
		$this->pdf->Output();
	}
}
?>