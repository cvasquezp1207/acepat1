<?php

include_once "Controller.php";

class Seguimientoserie extends Controller {
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
		$data["comprobante"] = $this->listcomprobante();
		
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
	
	public function listcomprobante(){
		$sql = "SELECT tipo_documento,idtipodocumento FROM 
				({$this->sql_interno()}) query
				WHERE COALESCE(query.serie,'')!=''
				--{$this->condicion()}
				GROUP BY tipo_documento, idtipodocumento";

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
	
	public function sql_interno(){
		$cadena="SELECT 
				v.fecha_venta_format fecha_op
				,v.tipo_documento
				,(v.serie||'-'||v.correlativo) comprobante
				,v.full_nombres referencia
				,COALESCE(v.ruc,v.dni) doc_referencia
				,dv.descripcion producto
				,dvs.serie 
				,dv.precio precio_operacion
				,CAST('venta' AS text) tabla
				,s.descripcion sucursal
				,al.idsucursal
				,v.idtipodocumento
				FROM venta.detalle_venta_serie dvs 
				JOIN venta.detalle_venta dv ON dv.iddetalle_venta=dvs.iddetalle_venta
				JOIN venta.venta_view v ON v.idventa=dvs.idventa
				JOIN almacen.almacen al ON al.idalmacen=v.idalmacen
				JOIN seguridad.sucursal s ON s.idsucursal=al.idsucursal
				WHERE dvs.estado='A'

				UNION 
				
				SELECT
				c.fecha_compra_es fecha_op
				,c.tipo_documento
				,c.nrodocumento comprobante
				,c.proveedor referencia
				,c.ruc doc_referencia
				,COALESCE(p.descripcion_detallada||' ','') producto
				,dcs.serie
				,dc.costo precio_operacion
				,CAST('compra' AS text) tabla
				,s.descripcion sucursal
				,al.idsucursal
				,c.idtipodocumento
				FROM compra.detalle_compra_serie dcs
				JOIN compra.detalle_compra dc ON dc.idcompra=dcs.idcompra
				JOIN compra.producto p ON p.idproducto = dc.idproducto
				JOIN compra.compra_view c ON c.idcompra=dcs.idcompra
				JOIN almacen.almacen al ON al.idalmacen=c.idalmacen
				JOIN seguridad.sucursal s ON s.idsucursal=al.idsucursal
				WHERE dcs.estado='A'

				UNION
				
				SELECT 
				to_char(fecha,'DD/MM/YYYY') fecha_op
				,td.descripcion tipo_documento
				,nc.nrodocumento comprobante
				,nc.cliente referencia
				,COALESCE(nc.ruc,nc.dni) doc_referencia
				,COALESCE(dnc.descripcion||' ','') producto
				,dnc.serie
				,dnc.precio precio_operacion 
				,CAST('nota_credito' as text) tabla
				,s.descripcion sucursal
				,al.idsucursal
				,nc.idtipodocumento
				FROM venta.detalle_notacredito dnc
				JOIN venta.notacredito_view nc ON nc.idnotacredito=dnc.idnotacredito
				JOIN venta.tipo_documento td ON td.idtipodocumento=nc.idtipodocumento 
				JOIN venta.venta v ON v.idventa=nc.idventa
				JOIN almacen.almacen al ON al.idalmacen=v.idalmacen
				JOIN seguridad.sucursal s ON s.idsucursal=al.idsucursal
				WHERE dnc.estado='A'
				
				UNION 
				
				SELECT 
				to_char(gr.fecha_traslado,'DD/MM/YYYY') fecha_op
				,td.descripcion tipo_documento
				,gr.nroguia comprobante
				,gr.destinatario referencia
				,COALESCE(gr.ruc_destinatario,gr.dni_destinatario) doc_referencia
				,dg.descripcion producto
				,dgs.serie 
				,dg.precio precio_operacion
				,CAST('guia_remision' AS text) tabla
				,s.descripcion sucursal
				,gr.idsucursal
				,gr.idtipodocumento
				FROM almacen.detalle_guia_remision_serie dgs
				JOIN almacen.detalle_guia_remision dg On dg.iddetalle_guia_remision=dgs.iddetalle_guia_remision
				JOIN almacen.guia_remision_view gr ON  gr.idguia_remision=dg.idguia_remision
				JOIN venta.tipo_documento td ON td.idtipodocumento=gr.idtipodocumento
				JOIN seguridad.sucursal s ON s.idsucursal=gr.idsucursal
				WHERE dgs.estado='A'";
				
		return $cadena;
	}
	
	public function data(){
		$sql = "SELECT * FROM 
				( {$this->sql_interno()}) query
				WHERE COALESCE(query.serie,'')!=''
				{$this->condicion()}
				;
				";
		$query      = $this->db->query($sql);

		$data = $query->result_array();
		return $data;
	}
	
	public function condicion($add_where=''){
		$where = "";
		
		if(!empty($_REQUEST['idtipodocumento'])){
			$where.=" AND idtipodocumento='{$_REQUEST['idtipodocumento']}' ";
		}
		
		if(!empty($_REQUEST['idsucursal'])){
			$where.=" AND idsucursal='{$_REQUEST['idsucursal']}' ";
		}
		
		if(!empty($_REQUEST['serie'])){
			$where.=" AND serie ILIKE '{$_REQUEST['serie']}%' ";
		}else{
			$where.=" AND serie IS NULL ";
		}
		
		$where.=$add_where;
		return $where;
	}
	
	public function imprimir(){
		$datos      = $this->data();
		
		$whit_fecha=18;
		$whit_td=20;
		$whit_comp=20;
		// $whit_prov =100;
		$whit_prod =85;
		$whit_serie =40;
		$whit_min = 20;//PARA EL WHIT DE LAS MONEDAS
		// $whit_total_m = 25;//PARA EL WHIT DE LAS MONEDAS


		$cabecera = array('fecha_op' => array('columna'=>"Fecha","width"=>$whit_fecha,"align_valor"=>"L","salto"=>0)
							,'tipo_documento' => array("columna"=>'Doc',"width"=>$whit_td,"align_valor"=>"L","salto"=>0)
							,'comprobante' => array("columna"=>'Comprobante',"width"=>$whit_comp,"align_valor"=>"L","salto"=>0)
							// ,'referencia' => array("columna"=>'Referencia',"width"=>$whit_prov,"align_valor"=>"L","salto"=>0)
							,'producto' => array("columna"=>'Producto',"width"=>$whit_prod,"align_valor"=>"L","salto"=>0)
							,'serie' => array("columna"=>'Serie',"width"=>$whit_serie,"align_valor"=>"L","salto"=>0)
							,'precio_operacion' => array("columna"=>'Precio',"width"=>$whit_min,"align_valor"=>"R","salto"=>1)
						);

		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		$this->pdf->SetTitle(utf8_decode("SEGUIMIENTO DE PRODUCTOS CON SERIE "), 11, null, true);
		
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);

		$this->pdf->AddPage();
		$this->pdf->SetDrawColor(204, 204, 204);
		$this->pdf->setFillColor(249, 249, 249);
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		$this->pdf->SetFont('Arial','B',8);

		/************************** CABECERA *****************************************/
		$total_lienzo = 0;
		foreach ($cabecera as $key => $val) {
			$this->pdf->Cell($val['width'],9,$val['columna'],1,$val['salto']);
			$total_lienzo = $total_lienzo + $val['width'];
		}
		// $this->pdf->Ln(10);
		/************************** CABECERA *****************************************/
		

		$this->pdf->SetFont('Arial','',8);
		$x_total = 0;
		/************************** BODY *****************************************/
		foreach($datos as $k=>$v){
			foreach($cabecera as $key => $val){
				if(is_numeric($v[$key]))
					$this->pdf->Cell($val['width'],5,number_format($v[$key],2),1,$val['salto'],$val['align_valor']);
				else
					$this->pdf->Cell($val['width'],5,$v[$key],1,$val['salto'],$val['align_valor']);
			}
		}
		/************************** BODY *****************************************/
		$this->pdf->Output();
	}
	
	public function excel(){
		// $this->load->library("excel");
	}
}
?>