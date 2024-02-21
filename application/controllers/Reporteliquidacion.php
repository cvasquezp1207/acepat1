<?php

include_once "Controller.php";

class Reporteliquidacion extends Controller {
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
		
		// COMBO ZONA
		$this->combobox->init();
		$this->combobox->setAttr("id", "idzona_cartera");
		$this->combobox->setAttr("name", "idzona_search");
		$this->combobox->setAttr("class", "form-control input-xs");
		
		$this->db->select('idzona, zona');
		$query = $this->db->where("estado", "A")->order_by("zona", "asc")->get("general.zona");
		$this->combobox->addItem("0","[TODOS]");
		$this->combobox->addItem($query->result_array());
		$data["zona"] = $this->combobox->getObject();
		// COMBO ZONA
		
		////////////////////////////////////////////////////// combo tipodocumento
		$query = $this->db->select('idtipodocumento, descripcion')
			->where("estado", "A")->where("mostrar_en_venta", "S")
			->order_by("descripcion", "asc")->get("venta.tipo_documento");
		
		$this->combobox->init();
		$this->combobox->setAttr(array("id"=>"idtipodocumento","name"=>"idtipodocumento","class"=>"form-control input-xs","required"=>""));
		$this->combobox->addItem("0","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data["tipodocumento"] = $this->combobox->getObject();


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
	
	public function query_master(){
		$sql="SELECT 
			u.user_nombres cobrador
			,COALESCE(zona.zona,'SIN ZONA') zona
			,venta.comprobante
			,cliente.cliente
			,liq.fecha_liquidacion
			,td.abreviatura||' '||liq.serie||'-'||liq.numero nro_recibo
			,liq.tipo_liquidacion
			,CAST(liq.importe AS numeric(10,2)) importe
			,COALESCE(liq.idzona,0) idzona
			,liq.idcobrador
			,liq.estado
			,venta.idtipodocumento
			,credito.nro_credito credito
			,tp.descripcion tipopago
			FROM cobranza.liquidacion_visita liq
			JOIN venta.cliente_view cliente ON cliente.idcliente = liq.idcliente
			LEFT JOIN general.zona ON zona.idzona=liq.idzona
			JOIN cobranza.visita ON visita.idvisita=liq.idvisita
			JOIN venta.venta_view venta ON venta.idventa=liq.idventa
			JOIN seguridad.view_usuario u ON u.idusuario=liq.idcobrador
			JOIN venta.tipo_documento td ON td.idtipodocumento=liq.idtipodocumento
			JOIN credito.credito ON credito.idcredito=liq.idcredito
			JOIN venta.tipopago tp ON tp.idtipopago=liq.idtipo_pago";
		
		return $sql;
	}
	
	public function data_reporte($_REQUEST){
		$sql =" SELECT * FROM ( 
					{$this->query_master()}
				) as q
				{$this->filtro_reporte($_REQUEST)};
				";
		// echo $sql;
		$query  = $this->db->query($sql);
		$datos  = $query->result_array();
		
		return $datos;
	}
	
	public function data_cobrador($_REQUEST){
		$sql =" SELECT idcobrador,cobrador FROM ( 
					{$this->query_master()}
				) as q
				{$this->filtro_reporte($_REQUEST)}
				GROUP BY idcobrador,cobrador;
				";
		$query  = $this->db->query($sql);
		$datos  = $query->result_array();
		
		return $datos;
	}
	
	public function data_zona($_REQUEST){
		$sql =" SELECT idzona,idcobrador,zona FROM ( 
					{$this->query_master()}
				) as q
				{$this->filtro_reporte($_REQUEST)}
				GROUP BY idzona,idcobrador,zona;
				";
		$query  = $this->db->query($sql);
		$datos  = $query->result_array();
		
		return $datos;
	}
	
	public function filtro_reporte($post,$and_where=''){
		$where = "WHERE estado<>'I' ";
		if(!empty($post['fechainicio'])){
			if(!empty($post['fechafin'])){
				$where.=" AND fecha_liquidacion>='{$post['fechainicio']}' AND fecha_liquidacion>='{$post['fechafin']}'";
			}else{
				$where.=" AND fecha_liquidacion='{$post['fechainicio']}'";
			}
		}
		
		if(!empty($post['idcobrador'])){
			$where.=" AND idcobrador='{$post['idcobrador']}'";
		}
		
		if(!empty($post['idzona'])){
			$where.=" AND idzona='{$post['idzona']}'";
		}
		
		if(!empty($post['idtipodocumento'])){
			$where.=" AND idtipodocumento='{$post['idcobrador']}'";
		}
		
		if(!empty($post['tipo_liquidacion'])){
			$where.=" AND tipo_liquidacion='{$post['tipo_liquidacion']}'";
		}
		
		$where.=$and_where;
		return $where;
	}
	
	public function imprimir(){
		$datos      = $this->data_reporte($_REQUEST);
		$list_cob   = $this->data_cobrador($_REQUEST);
		$list_zona  = $this->data_zona($_REQUEST);

		$datosfinal = array();

		$whit_fre  = 5;
		$whit_doc  = 28;
		$whit_cli  = 75;
		$whit_rec  = 20;
		$width_tpg  = 25;
		$whit_cre  = 20;
		$whit_tli = 12;
		$whit_imp = 17;

		$cols_h  = array('','comprobante','cliente','nro_recibo','tipopago','credito','tipo_liquidacion','importe');
		$name_h  = array(''=>array($whit_fre,'0')
						,'Comprobante'=>array($whit_doc,'0')
						,'Cliente'=>array($whit_cli,'0')
						,'Recibo'=>array($whit_rec,'0')
						,'Tipo Pago'=>array($width_tpg,'0')
						,'Credito'=>array($whit_cre,'0')
						,'T. Liq'=>array($whit_tli,'0')
						,'Importe'=>array($whit_imp,'1'));

		$pos   = array("C","L", "L", "C",'L', "R", "R", "R");
		$width = array($whit_fre,$whit_doc, $whit_cli, $whit_rec, $width_tpg, $whit_cre, $whit_tli, $whit_imp);

		$this->load->library("pdf");
		
		$this->load_model(array( "seguridad.empresa","seguridad.view_usuario","credito.estado_credito","general.zona","venta.tipo_documento"));
		
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		if(!empty($_REQUEST['fechainicio'])){
			if(!empty($_REQUEST['fechafin']))
				$this->pdf->SetTitle(utf8_decode("LIQUIDACION DE COBRANZA DE ".fecha_es($_REQUEST['fechainicio']).' A '.fecha_es($_REQUEST['fechainicio'])), 11, null, true);
			else
				$this->pdf->SetTitle(utf8_decode("LIQUIDACION DE COBRANZA DE ".$_REQUEST['fechainicio']), 11, null, true);
		}else
			$this->pdf->SetTitle(utf8_decode("LIQUIDACION DE COBRANZA"), 11, null, true);
			
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
		$width_pdf=32;
		
		if(!empty($_REQUEST['idcobrador'])){
			$this->pdf->Cell($width_pdf,3,"COBRADOR",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->view_usuario->find(array("idusuario"=>$_REQUEST['idcobrador']));
			$this->pdf->Cell(5,3,$this->view_usuario->get("user_nombres"),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['idzona_cartera'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell($width_pdf,3,"ZONA",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->zona->find(array("idzona"=>$_REQUEST['idzona_cartera']));
			$this->pdf->Cell(5,3,$this->zona->get("zona"),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell($width_pdf,3,"TIPO DOCUMENTO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->tipo_documento->find(array("idtipodocumento"=>$_REQUEST['idtipodocumento']));
			$this->pdf->Cell(5,3,$this->tipo_documento->get("descripcion"),0,1,'L');
			$this->pdf->Ln();
		}
		
		$this->pdf->SetFont('Arial','B',10);
		// if(!empty($_REQUEST['tipo_liquidacion'])){
			$this->pdf->Cell($width_pdf,3,"TIPO LIQUIDACION",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,"1: AMORTIZACION",0,1,'L');
			$this->pdf->Cell(5+$width_pdf,3,'',0,0,'L');
			$this->pdf->Cell(20,3,"2: LETRA COMPLETA",0,1);
		// }
		$this->pdf->Ln();
		$this->pdf->SetFont('Arial','B',8);
		
		$this->pdf->SetDrawColor(204, 204, 204);
		
		foreach($list_cob as $key=>$val){
			$this->pdf->Cell(200,6,utf8_decode($val['cobrador']),0,1,'L', false);
			$zona_cobrador = $this->seleccion($list_zona,$val['idcobrador'],'idcobrador');
			$data_cobrador = $this->seleccion($datos,$val['idcobrador'],'idcobrador');
			foreach($zona_cobrador as $kk=>$vv){
				$this->pdf->SetFillColor(249, 249, 249); 
				$this->pdf->Cell($whit_fre,6,'',0,0,'L', true);
				$this->pdf->Cell(197,6,utf8_decode($vv['zona']),0,1,'L', true);
				$this->pdf->SetFillColor(0,0,0); 
				foreach($name_h as $k=>$v){
					$this->pdf->Cell(($v[0]),6,utf8_decode($k),1,$v[1],'C', false);
				}
				
				$data_zona = $this->seleccion($data_cobrador,$vv['idzona'],'idzona');
				$this->pdf->SetFont('Arial','',8);
				foreach ($data_zona as $key => $f) {
					$this->pdf->SetWidths($width);
					$values = array();
					foreach($cols_h as $v){
						if($v=='')
							$values[] = '';
						else
							$values[] = utf8_decode((($f[$v])));
					}
					$this->pdf->Row($values, $pos, "Y", "Y");
				}
			}
		}
		$this->pdf->Output();
	}
}
?>