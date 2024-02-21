<?php

include_once "Controller.php";

class Reporteprecios extends Controller {
	
	private $limitRow = 30;
	
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
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null, $prefix = "", $modal = false) {
		$data["controller"] = $this->controller;
		
		$this->load->library('combobox');
		
		// combo sucursal
		$sql = "select idsucursal,descripcion from seguridad.sucursal where estado='A'";
		$query = $this->db->query($sql);
		$this->combobox->setAttr("id","idsucursal");
		$this->combobox->setAttr("name","idsucursal");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->combobox->addItem($query->result_array());
		$data['sucursal'] = $this->combobox->getObject();
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return null;
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
		
		$sql = "select p.idproducto, trim(p.descripcion_detallada) as producto, ma.descripcion as marca, 
			mo.descripcion as modelo, coalesce(pu.precio_venta,0) as preciounit, 
			array_to_string(array_agg(pv.precio), '|') as precios, count(pv.precio) as cant,
			coalesce(da.stock,0) as stock
			from compra.producto p
			join general.marca ma on ma.idmarca=p.idmarca
			join general.modelo mo on mo.idmodelo=p.idmodelo
			left join (
				select idproducto, idsucursal, sum(cantidad*coalesce(cantidad_um,1)*tipo_number) as stock
				from almacen.detalle_almacen where estado='A' group by idproducto, idsucursal
			) as da on da.idproducto=p.idproducto and da.idsucursal={$idsucursal}
			left join compra.producto_precio_unitario pu on pu.idproducto=p.idproducto and pu.idsucursal={$idsucursal}
			left join compra.producto_precio_venta pv on pv.idproducto=p.idproducto and 
			pv.idunidad=p.idunidad and pv.idsucursal={$idsucursal} and pv.cantidad=1
			where p.estado='A' and stock >0";
		if( ! empty($post["idproducto"])) {
			$sql .= " and p.idproducto=".intval($post["idproducto"]);
		}
		$sql .= " group by p.idproducto, p.descripcion_detallada, ma.descripcion, mo.descripcion, pu.precio_venta, da.stock
			order by producto, marca, modelo";
		if($setLimit === TRUE) {
			$page = ( ! empty($post["page"])) ? intval($post["page"]) : 0;
			$offset = $page * $this->limitRow;
			$sql .= " limit ".$this->limitRow." offset ".$offset;
		}
		// echo $sql;exit;
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function get_records() {
		$post = $this->input->post();
		$arr = $this->getData($post);
		
		$page = ( ! empty($post["page"])) ? intval($post["page"]) : 0;
		
		$html = '';
		
		if(empty($arr)) {
			if($page <= 0) {
				$html = '<tr class="empty-rs"><td colspan="9"><i>Sin resultados para la b&uacute;squeda.</i></td></tr>';
			}
		}
		else {
			foreach($arr as $row) {
				$html .= '<tr data-idproducto="'.$row["idproducto"].'">';
				$html .= '<td>'.$row["producto"].'</td>';
				$html .= '<td>'.$row["marca"].'</td>';
				$html .= '<td>'.$row["modelo"].'</td>';
				$html .= '<td>'.$row["stock"].'</td>';
				$html .= '<td>'.number_format($row["preciounit"],2,".","").'</td>';
				
				$precios = array();
				if( ! empty($row["precios"])) {
					$precios = explode("|", $row["precios"]);
					sort($precios);
				}
				
				for($i=0; $i < 5; $i++) {
					$v = "";
					if(count($precios) > 0)
						$v = number_format(floatval(array_shift($precios)), 2, ".", "");
					$html .= '<td>'.$v.'</td>';
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
		$post = $this->input->get();
		$rows = $this->getData($post, false);
		
		$sucursal = "";
		if( ! empty($post["idsucursal"])) {
			$this->load_model("seguridad.sucursal");
			$this->sucursal->find(intval($post["idsucursal"]));
			$sucursal = "EN ".$this->sucursal->get("descripcion");
		}
		
		$this->load_model(array("seguridad.empresa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->load->library("pdf");
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		$this->pdf->SetTitle("REPORTE DE PRECIOS {$sucursal}", 11, null, true);
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);
        $this->pdf->SetDrawColor(160, 160, 160);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',8);
		
		$this->pdf->SetHeight(3);
		$this->pdf->SetWidths(array(101, 101));
		$this->pdf->Row(array($this->empresa->get("descripcion"), date('d/m/Y')), array("L", "R"), "N", "Y");
		$this->pdf->Row(array("RUC: ".$this->empresa->get("ruc"), date('H:i:s')), array("L", "R"), "N", "Y");
		
		$this->pdf->Ln();
		$this->pdf->SetHeight(5);
		$this->pdf->Ln();
		
		$cols = array(
			array("col" => "producto", "label" => "Producto", "pos" => "L", "width" => 60)
			,array("col" => "marca", "label" => "Marca", "pos" => "L", "width" => 32)
			,array("col" => "modelo", "label" => "Modelo", "pos" => "L", "width" => 32)
			,array("col" => "stock", "label" => "Stock", "pos" => "R", "width" => 12)
			,array("col" => "preciounit", "label" => "P.U.", "pos" => "R", "width" => 11)
			,array("col" => "precio", "label" => "P.1", "pos" => "R", "width" => 11)
			,array("col" => "precio", "label" => "P.2", "pos" => "R", "width" => 11)
			,array("col" => "precio", "label" => "P.3", "pos" => "R", "width" => 11)
			,array("col" => "precio", "label" => "P.4", "pos" => "R", "width" => 11)
			,array("col" => "precio", "label" => "P.5", "pos" => "R", "width" => 11)
		);
		
		$fields = array_column($cols, "col");
		$pos = array_column($cols, "pos");
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->SetWidths(array_column($cols, 'width'));
		$this->pdf->Row(array_column($cols, 'label'), array_fill(0, count($fields), "C"), "Y", "Y");
		
		$this->pdf->SetFont('Arial','',8);
		foreach($rows as $row) {
			$precios = array();
			if( ! empty($row["precios"])) {
				$precios = explode("|", $row["precios"]);
				sort($precios);
			}
			$values = array();
			foreach($fields as $field) {
				if($field == "preciounit") {
					$values[] = number_format($row[$field], 2, ".", "");
				}
				else if($field == "precio") {
					$values[] = (count($precios) > 0) ? number_format(floatval(array_shift($precios)), 2, ".", "") : "";
				}
				else {
					$values[] = array_key_exists($field, $row) ? utf8_decode($row[$field]) : "";
				}
			}
			$this->pdf->Row($values, $pos, "Y", "Y");
		}
		
		$this->pdf->Output();
	}
	
	//$this->row($excel, $val["pos"], $i, $val["label"], $val["merge"], true, true);
	protected function row(&$excel, $col, &$row, $val, $merge=null, $bold=false, $border=false) {
		if($bold === true) {
			$excel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
		}
		// $excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, utf8_decode($val));
		$excel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $val);
		if(isset($merge) && is_int($merge)) {
			$excel->setActiveSheetIndex(0)->mergeCellsByColumnAndRow($col, $row, $merge, $row);
		}
		if($border === true) {
			$cels = PHPExcel_Cell::stringFromColumnIndex($col) . $row;
			if(isset($merge) && is_int($merge)) {
				$cels .= ":" . PHPExcel_Cell::stringFromColumnIndex($merge) . $row;
			}
			$excel->getActiveSheet()->getStyle($cels)->applyFromArray(array(
				'borders' => array(
					'outline' => array(
						'style' => PHPExcel_Style_Border::BORDER_THIN
						,'color' => array('argb' => 'FF000000')
					)
				)
			));
		}
	}
	
	public function excel() {
		$this->unlimit();
		$post = $this->input->get();
		$rows = $this->getData($post, false);
		
		$sucursal = "";
		if( ! empty($post["idsucursal"])) {
			$this->load_model("seguridad.sucursal");
			$this->sucursal->find(intval($post["idsucursal"]));
			$sucursal = "EN ".$this->sucursal->get("descripcion");
		}
		
		require_once APPPATH."/libraries/PHPExcel.php"; // libreria excel
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$excel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($excel,"REPORTE DE PRECIOS {$sucursal}",true,4,58,150,false);
		
		$cols = array(
			array("col" => "producto", "label" => "Producto", "pos" => 0, "merge" => 3)
			,array("col" => "marca", "label" => "Marca", "pos" => 4, "merge" => null)
			,array("col" => "modelo", "label" => "Modelo", "pos" => 5, "merge" => null)
			,array("col" => "stock", "label" => "Stock", "pos" => 6, "merge" => null)
			,array("col" => "preciounit", "label" => "Precio unit.", "pos" => 7, "merge" => null)
		);
		
		$y = 7;
		$x = max(array_column($cols, "pos"));
		
		$cants = array_column($rows, "cant");
		rsort($cants);
		$length = array_shift($cants);
		for($i=1; $i <= $length; $i++) {
			$cols[] = array("col" => "precio", "label" => "Precio {$i}", "pos" => ++$x, "merge" => null);
		}
		
		// draw cabecera
		foreach($cols as $val) {
			$this->row($excel, $val["pos"], $y, $val["label"], $val["merge"], true, true);
		}
		
		// draw detalle
		$y ++;
		foreach($rows as $row) {
			$precios = array();
			if( ! empty($row["precios"])) {
				$precios = explode("|", $row["precios"]);
				sort($precios);
			}
			
			foreach($cols as $val) {
				$v = "";
				if($val["col"] == "precio") {
					if(count($precios) > 0) {
						$v = floatval(array_shift($precios));
					}
				}
				else if(array_key_exists($val["col"], $row)) {
					$v = $row[$val["col"]];
				}
				$this->row($excel, $val["pos"], $y, $v, $val["merge"], false, true);
			}
			$y ++;
		}
		
		$filename='reporteprecios'.date("dmYhis").'.xlsx'; //save our workbook as this file name
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');  
        $objWriter->save('php://output');
	}
}
?>