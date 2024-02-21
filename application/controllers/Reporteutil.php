<?php
include_once "Controller.php";

class Reporteutil extends Controller {
	
	private $limitRow = 100;
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Reporte de precios");
		$this->set_subtitle("");
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
	public function form($data = null, $prefix = "", $modal = false) {
		$data["controller"] = $this->controller;
		
		$this->load->library('combobox');
		$sql = "select idsucursal,descripcion from seguridad.sucursal where estado='A' AND idempresa='{$this->get_var_session('idempresa')}'";
		$query = $this->db->query($sql);
		$this->combobox->setAttr("id","idsucursal");
		$this->combobox->setAttr("name","idsucursal");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->combobox->addItem($query->result_array());
		$data['sucursal']	= $this->combobox->getObject();
		$data["head"]		= $this->head_array();
		
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return null;
	}
	
	public function head_array(){
		$w_linea = 18;
		$w_cat	 = 30;
		$w_mar	 = 26;
		$w_mod	 = 22;
		$w_prod	 = 69;
		$w_um	 = 12;
		$w_cant	 = 15;
		$w_pv	 = 16;
		$w_imppv = 14;
		$w_costo = 14;
		$w_cv	 = 14;
		$w_ut	 = 14;
		$w_por	 = 12;
		
		$total = $w_linea + $w_cat + $w_mar+ $w_mod+ $w_prod+ $w_um + $w_cant + $w_pv + $w_imppv + $w_costo +$w_cv + $w_ut + $w_por;
		return array("linea"=>array($w_linea,"Linea",0,'L',100*$w_linea/$total,false)
					,"categoria"=>array($w_cat,"Categoria",0,'L',100*$w_cat/$total,false)
					,"marca"=>array($w_mar,"Marca",0,'L',100*$w_mod/$total,false)
					,"modelo"=>array($w_mod,"Modelo",0,'L',100*$w_mod/$total,false)
					,"producto"=>array($w_prod,"Producto",0,'L',(100*$w_prod/$total)-5,false)
					,"unidad"=>array($w_um,"UM",0,'C',100*$w_um/$total,false)
					,"cantidad"=>array($w_cant,"Cant",0,'R',100*$w_cant/$total,true)
					,"ppventa"=>array($w_pv,"P. Vta",0,'R',100*$w_pv/$total,false)
					,"importe"=>array($w_imppv,"Imp. Vta",0,'R',100*$w_imppv/$total,true)
					,"ppcosto"=>array($w_costo,"Costo",0,'R',100*$w_costo/$total,false)
					,"costoventa"=>array($w_cv,"Costo Vta",0,'R',100*$w_cv/$total,true)
					,"utilidad"=>array($w_ut,"Utilidad",0,'R',100*$w_ut/$total,true)
					,"porcentaje"=>array($w_por,"%",1,'R',100*$w_por/$total,true)
					);
	}
	
	public function index($tpl = "") {
		$data = array(
			"menu_title" => $this->menu_title
			,"menu_subtitle" => $this->menu_subtitle
			,"content" => $this->form()
			,"with_tabs" => $this->with_tabs
		);
		
		if($this->show_path) {
			$data['path'] = $this->get_path();
		}
		
		$str = $this->load->view("content_empty", $data, true);
		$this->show($str);
	}
	
	public function getData($post, $setLimit = TRUE) {
		$idsucursal = ( ! empty($post["idsucursal"])) ? intval($post["idsucursal"]) : $this->get_var_session("idsucursal");
		
		$sql = "SELECT 
				CASE WHEN l.descripcion='SIN LINEA' THEN '' ELSE l.descripcion END linea
				,CASE WHEN ca.descripcion='SIN CATEGORIA' THEN '' ELSE ca.descripcion END categoria
				,CASE WHEN ma.descripcion='SIN MARCA' THEN '' ELSE ma.descripcion END marca
				,CASE WHEN mo.descripcion='SIN MODELO' THEN '' ELSE mo.descripcion END  modelo
				,p.idproducto
				, p.descripcion producto
				, u.abreviatura unidad
				,sum(dv.cantidad) cantidad
				,avg(CASE WHEN oferta='N' THEN dv.precio ELSE 0 END) ppventa 
				,sum(CASE WHEN oferta='N' THEN dv.cantidad*dv.precio ELSE 0 END ) importe
				, avg(dv.costo)ppcosto 
				,sum(dv.cantidad*dv.costo) costoventa
				,sum(dv.cantidad*(CASE WHEN oferta='N' THEN dv.precio ELSE 0 END))-sum(dv.cantidad*dv.costo) as utilidad
				,CAST(100*(sum(dv.cantidad*(CASE WHEN oferta='N' THEN dv.precio ELSE 0 END))-sum(dv.cantidad*dv.costo))/sum(dv.cantidad*dv.costo) AS numeric(10,2)) porcentaje
				from venta.venta v
				inner join venta.detalle_venta dv on dv.idventa =v.idventa
				inner join compra.producto p on p.idproducto = dv.idproducto
				inner join compra.unidad u on u.idunidad = dv.idunidad
				inner join general.linea l on l.idlinea = p.idlinea
				inner join general.categoria ca on ca.idcategoria = p.idcategoria
				inner join general.marca ma on ma.idmarca = p.idmarca
				inner join general.modelo mo on mo.idmodelo =p.idmodelo
				JOIN seguridad.sucursal suc ON suc.idsucursal=v.idsucursal
				where dv.costo >0 AND suc.idempresa='{$this->get_var_session('idempresa')}' AND suc.idsucursal = '{$_REQUEST['idsucursal']}'
				";
		if( ! empty($post["idproducto"])) {
			$sql .= " and p.idproducto=".intval($post["idproducto"]);
		}
		
		if(!empty($post['fechainicio'])){
			if(!empty($post['fechafin'])){
				$sql.=" AND v.fecha_venta>='{$post['fechainicio']}' AND v.fecha_venta<='{$post['fechafin']}'";
			}else{
				$sql.=" AND v.fecha_venta='{$post['fechainicio']}'";
			}
		}
		$sql .= " group by l.descripcion, ca.descripcion, ma.descripcion,mo.descripcion
				,p.idproducto, p.descripcion, u.abreviatura";
		
		if(!isset($post['campo']))
			$sql.=" ORDER BY linea,categoria,marca, modelo, producto,unidad";
		else if(!empty($post['campo']))
			$sql.=" ORDER BY {$post['campo']} {$post['sort']}";
		
		if($setLimit === TRUE) {
			// $page = ( ! empty($post["page"])) ? intval($post["page"]) : 0;
			// $offset = $page * $this->limitRow;
			// $sql .= " limit ".$this->limitRow." offset ".$offset;
		}
		  //echo $sql;Exit;
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function get_records() {
		$post = $this->input->post();
		$arr = $this->getData($post);
		$head = $this->head_array();
		$page = ( ! empty($post["page"])) ? intval($post["page"]) : 0;
		
		$html = '';
		
		if(empty($arr)) {
			if($page <= 0) {
				$html = '<tr class="empty-rs"><td colspan="13"><i>Sin resultados para la b&uacute;squeda.</i></td></tr>';
			}
		}
		else {
			foreach($arr as $row) {
				$html .= '<tr data-idproducto="'.$row["idproducto"].'">';
				foreach($head as $k=>$v){
					$align="";
					if($v['3']=='R'){
						$align="right";
						$row[$k] = number_format($row[$k],2,".","");
					}else if($v['3']=='C')
						$align="center";
					else if($v['3']=='L')
						$align="left";
				
					$class = "";
					if($v[5])
						$class = "fila";
					$html .= "<td align='{$align}' class='$class' data-td='{$k}'>{$row[$k]}</td>";
				}
				$html .= '</tr>';
			}
		}
		
		$res["more"] = (count($arr) >= $this->limitRow);
		$res["page"] = $page;
		$res["html"] = $html;
		
		$this->response($res);
	}
	
	public function imprimir() {
		$this->unlimit();
		$post = $_REQUEST;
		$rows = $this->getData($post, false);
		$head = $this->head_array();
		
		$sucursal = "";
		if( ! empty($post["idsucursal"])) {
			$this->load_model("seguridad.sucursal");
			$this->sucursal->find(intval($post["idsucursal"]));
			$sucursal = "EN ".$this->sucursal->get("descripcion");
		}
		
		$this->load_model(array("seguridad.empresa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->load->library("pdf");
		if(file_exists(FCPATH."app/img/empresa/".$this->empresa->get("logo")))
			$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		$this->pdf->SetTitle("REPORTE DE PRECIOS {$sucursal}", 11, null, true);
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);

		$this->pdf->AddPage('L');
		$this->pdf->SetFont('Arial','',8);
		
		$this->pdf->Row(array($this->empresa->get("descripcion"), date('d/m/Y')), array("L", "R"), "N", "Y");
		$this->pdf->Row(array("RUC: ".$this->empresa->get("ruc"), date('H:i:s')), array("L", "R"), "N", "Y");
		
		$this->pdf->Ln();
		$this->pdf->SetHeight(5);
		$this->pdf->Ln();
		
		$this->pdf->SetDrawColor(204, 204, 204);
		$this->pdf->setFillColor(249, 249, 249);
		$this->pdf->SetFont('Arial','B',8);
		foreach($head as $k=>$v){
			$this->pdf->Cell($v[0],6,$v[1],1,$v[2],'C',true);
		}
		
		$this->pdf->SetFont('Arial','',7.5);
		foreach($rows as $key=>$val){
			/*For autosize*/
			$pos_h		= array();
			$width_h	= array();
			$values_c	= array();
			/*For autosize*/
			
			foreach($head as $k=>$v){
				$width_h[]	= $v[0];
				$pos_h[]	= $v[3];
				
				if($v[3]=='R')
					$values_c[]	= number_format($val[$k],2,'.','');
				else
					$values_c[]	= utf8_decode($val[$k]);
			}
			
			$this->pdf->SetWidths($width_h);
			$this->pdf->Row($values_c, $pos_h, "Y", "Y");
		}
		$this->pdf->Output();
	}

	public function excel(){
		set_time_limit(0);
		$head = $this->head_array();
		$post = $_REQUEST;
		$rows = $this->getData($post, false);
		
		$this->load->library("phpexcel");
		
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE DE VENTA RESUMIDO",true);
		
		$col		= 9;
		$alfabeto	= 65;
		foreach($head as $k=>$v){
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
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $v[1]);
			
			$alfabeto++;
		}
		
		$col++;
		foreach($rows as $key=>$val){
			$alfabeto	= 65;
			foreach($head as $k=>$v){
				if($v[3]=='R')
					$val[$k]	= number_format($val[$k],2,'.','');
				else
					$val[$k]	= utf8_decode($val[$k]);
				
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $val[$k]);
				$alfabeto++;
			}
			$col++;
		}
		
		$filename='reporteprecios'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  

        $objWriter->save('php://output');
	}
}
?>