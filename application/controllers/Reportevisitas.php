<?php

include_once "Controller.php";

class Reportevisitas extends Controller {
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
		// $data["conceptos"] = $this->conceptos();
		// $data["monedaactiva"] = $this->moneda();
		//$data["iniciales"] = $this->apertura();
		$data["sucursal"] = $this->listsucursal();
		// $data["tipomov"] = $this->tipomovimiento();
		$data['idperfil'] = $this->get_var_session("idperfil");

		// combo tipopago
		// $this->combobox->init(); // un nuevo combo
		// $this->combobox->setAttr(
			// array(
				// "id"=>"idusuario"
				// ,"name"=>"nombres"
				// ,"class"=>"form-control"
				// ,"required"=>""
			// )
		// );
		// $query = $this->db->select('idusuario, nombres')->where("estado", "A")->where("idusuario","1")->get("seguridad.usuario");
		// $this->combobox->addItem("","[TODOS]");
		// $this->combobox->addItem($query->result_array());
		// $data["usuario"] = $this->combobox->getObject();
		$rolcobrador 		= $this->get_param("idrolcobrador");
		$idperfilcobrador 	= $this->get_param("idperfilcobrador");
		$iduser_us   		= $this->get_var_session("idusuario");
		$idsucr_us   		= $this->get_var_session("idsucursal");
		$idperfil   		= $this->get_var_session("idperfil");
		$es_cobrador 		= $this->extrac_rol_user($iduser_us,$idsucr_us,$rolcobrador);
		$this->combobox->init();
		$this->combobox->setAttr("id", "idcobrador");
		$this->combobox->setAttr("name", "idcobrador");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->setAttr("required", "");
		$this->db->select('idusuario, empleado');
		if($es_cobrador=='A' && $idperfil == $idperfilcobrador){
			$query = $this->db->where("estado", "A")->where("idtipoempleado", $rolcobrador)->where("idsucursal", $idsucr_us)->where("idusuario", $iduser_us)->get("cobranza.view_cobradores");
		}else{
			$query = $this->db->where("estado", "A")->where("idtipoempleado", $rolcobrador)->where("idsucursal", $idsucr_us)->get("cobranza.view_cobradores");
		}
		$this->combobox->addItem("0","[TODOS]");
		$this->combobox->addItem($query->result_array());

		$data["usuario"] = $this->combobox->getObject();
		// COMBO COBRADORES

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
		
	public function seleccion($datos,$id,$key){
		$data = array();
		foreach($datos as $kk=>$vv){
			if($vv[$key]==$id){
				$data[]=$vv;
			}
		}	
		return $data;
	}
	
	public function data_reporte($fields){
		$sql ="SELECT v.idempleado,
				u.nombres||' '||u.appat||' '||u.apmat empleado,
				to_char(v.fecha_visita,'DD/MM/YYYY') fvisita,
				v.idcredito,
				cl.cliente ncliente,
				cl.direccion,
				v.observacion,
				v.letra_vencidas,
				c.nro_letras,
				to_char(l.fecha_vencimiento,'DD/MM/YYYY') fecha_vencimiento,
				to_char(v.fecha_prox_visita,'DD/MM/YYYY') fecha_prox_visita,
				v.monto_cobrado cuota,
				l.mora,
				c.nro_credito
			FROM cobranza.visita AS v
			INNER JOIN credito.credito AS c ON c.idcredito = v.idcredito 
			INNER JOIN seguridad.usuario AS u ON u.idusuario = v.idempleado
			INNER JOIN venta.cliente_view AS cl ON cl.idcliente = c.idcliente
			INNER JOIN credito.letra AS l ON l.idcredito = c.idcredito
			{$this->filtro_reporte($fields)}";
		$query      = $this->db->query($sql);
		$datos      = $query->result_array();
		
		return $datos;
	}
	
	public function filtro_reporte($post,$and_where=''){
		$where = "WHERE c.estado<>'I' ";
		if(!empty($post['fechainicio'])){
			if(!empty($post['fechafin'])){
				$where.=" AND fecha_visita>='{$post['fechainicio']}' AND fecha_visita>='{$post['fechafin']}'";
			}else{
				$where.=" AND fecha_visita='{$post['fechainicio']}'";
			}
		}
		
		if(!empty($post['idcobrador'])){
			$where.=" AND idempleado='{$post['idcobrador']}'";
		}
		$where.=$and_where;
		return $where;
	}
	
	public function imprimir(){
		$datos      = $this->data_reporte($_REQUEST);
		$datosfinal = array();

		foreach ($datos as $key => $v) {
			if(!array_key_exists($v['idempleado'], $datosfinal)) {
				$datosfinal[$v['idempleado']] = array(); 
			}
			if(!array_key_exists($v['empleado'], $datosfinal[$v['idempleado']])) {
				$datosfinal[$v['idempleado']][$v['empleado']] = array(); 
			}
			if(!array_key_exists($v['fvisita'], $datosfinal[$v['idempleado']][$v['empleado']])) {
				$datosfinal[$v['idempleado']][$v['empleado']][$v['fvisita']] = array(); 
			}
				
				$datosfinal[$v['idempleado']][$v['empleado']][$v['fvisita']][] = 
					array(	'ncliente' => $v['ncliente']
							,'direccion'=> $v['direccion']
							,'incidencia' => $v['observacion']
							,'letra' => $v['nro_letras']
							,'letrav' => $v['letra_vencidas']
							,'vencimiento' => $v['fecha_vencimiento']
							,'visita' => $v['fecha_prox_visita']
							,'fvisita' => $v['fvisita']
							,'cuota' => $v['cuota']
							,'mora' => $v['mora']
							,'credito' => $v['nro_credito']);
		}
		$whit_cli  = 50;
		$whit_dir  = 20;
		$whit_vis  = 55;
		$whit_let  = 13;
		$whit_letv = 15;
		$whit_fvct = 17;
		$whit_fvta = 17;
		$whit_cta  = 17;
		$whit_mora = 17;
		$whit_cred = 18;

		$cols_h  = array('ncliente','incidencia','letrav','fvisita','vencimiento','cuota','mora','credito');
		$name_h  = array('CLIENTE','INCIDENCIA','LETRA','F. VISITA','F: VENC','CUOTA','MORA','CREDITO');
		$pos_h   = array("C", "C", "C", "C", "C", "C","C");
		
		$pos_d   = array("L", "L", "C", "C", "R", "R", "R");
		$width_h = array($whit_cli, $whit_vis, $whit_let, $whit_fvta, $whit_fvct, $whit_cta, $whit_mora, $whit_cred);

		$this->load->library("pdf");
		
		$this->load_model(array( "seguridad.empresa","seguridad.usuario","credito.estado_credito"));
		
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("REPORTE VISITAS ".$_REQUEST['empleado']." DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("REPORTE VISITAS ".$_REQUEST['empleado']." DE ".fecha_es($_REQUEST['fechainicio'])), 11, null, true);
		}else
			$this->pdf->SetTitle(utf8_decode("REPORTE VISITAS ".$_REQUEST['empleado']), 11, null, true);
			
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(3);
		$this->pdf->AddPage();

		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(45,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(126,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s a'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		
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
		$this->pdf->SetFont('Arial','B',8);
		$pX = 5; //posicion X
		$pY = 3; //posicion Y
		foreach ($datosfinal as $key => $arrayEmpleado) {
			// foreach ($arrayEmpleado as $key1 => $arrayFecha) {
				// $this->pdf->Cell(5,3,$key1,0,1,'L');

				// foreach ($arrayFecha as $key2 => $arrayCliente) {
					// $this->pdf->Cell($pX,3,$key2,0,1,'L');
					// $this->pdf->Ln(6);  
					// $this->pdf->SetX($pX);
					// $y = $this->pdf->GetY(); 
					// $this->pdf->Multicell(70,7,'CLIENTE',1,'C');
					// $this->pdf->SetXY($pX+70,$y);
					// $this->pdf->Multicell(70,7,'DIRECCION',1,'C');
					// $this->pdf->SetXY($pX+140,$y);
					// $this->pdf->Multicell(30,7,'INCIDENCIA',1,'C');
					// $this->pdf->SetXY($pX+170,$y);
					// $this->pdf->Multicell(20,3.5,'LETRAS VENCIDAS',1,'C');
					// $this->pdf->SetXY($pX+190,$y);
					// $this->pdf->Multicell(25,3.5,'FECHA VENCIMIENTO',1,'C');
					// $this->pdf->SetXY($pX+215,$y);
					// $this->pdf->Multicell(20,3.5,'FECHA VISITA',1,'C');
					// $this->pdf->SetXY($pX+235,$y);
					// $this->pdf->Multicell(20,7,'CUOTA',1,'C');
					// $this->pdf->SetXY($pX+255,$y);
					// $this->pdf->Multicell(15,7,'MORA',1,'C');
					// $this->pdf->SetXY($pX+270,$y);
					// $this->pdf->Multicell(15,7,'CREDITO',1,'C');
					// $y = $this->pdf->GetY(); 
					// $this->pdf->SetXY(5,$y);

					// $this->pdf->Ln(5);  
					// foreach ($arrayCliente as $key3 => $val) {
						// $this->pdf->SetXY(5,$y);
						// $this->pdf->Cell(70,5,$val['ncliente'],1,0,'L');
						// $this->pdf->Cell(70,5,substr($val['direccion'],0,40),1,0,'L');
						// $this->pdf->Cell(30,5,substr($val['incidencia'],0,15),1,0,'L');
						// $this->pdf->Cell(20,5,$val['letrav'],1,0,'C');
						// $this->pdf->Cell(25,5,$val['vencimiento'],1,0,'C');
						// $this->pdf->Cell(20,5,$val['visita'],1,0,'C');
						// $this->pdf->Cell(20,5,$val['cuota'],1,0,'R');
						// $this->pdf->Cell(15,5,$val['mora'],1,0,'R');
						// $this->pdf->Cell(15,5,$val['credito'],1,0,'R');
						// $this->pdf->Ln(5);  
						// $y = $this->pdf->GetY(); 
					// }
					// $this->pdf->Ln(5);  
				// }
			// }
		}
		
		$this->pdf->SetDrawColor(204, 204, 204);
		foreach ($datosfinal as $key => $arrayEmpleado) {
			foreach ($arrayEmpleado as $key1 => $arrayFecha) {
				$this->pdf->SetFont('Arial','B',9);
				$this->pdf->Cell(5,3,$key1,0,1,'L');

				foreach ($arrayFecha as $key2 => $arrayCliente) {
					$this->pdf->SetTextColor(42,185,212);
					$this->pdf->Cell(5,3,fecha_es($key2),0,1,'L');
					$this->pdf->SetTextColor(0,0,0);
					
					$this->pdf->SetWidths($width_h);
					$values_c = array();
					foreach($name_h as $k=>$v){
						$values_c[] = utf8_decode((($v)));
					}
					$this->pdf->Row($values_c, $pos_h, "Y", "Y");
					
					foreach($arrayCliente as $key3 => $val) {
						$values = array();
						foreach($cols_h as $f){
							$values[] = utf8_decode(((''.$val[$f])));
						}
						$this->pdf->SetFont('Arial','',8);
						$this->pdf->Row($values, $pos_d, "Y", "Y");						
					}
					
					$this->pdf->Ln(4);
				}
			}
		}
		$this->pdf->Output();
	}
}
?>