<?php

include_once "Controller.php";

class Reporteanulados extends Controller {
	
	private $limitRow = 30;
	
	private $title = "REPORTE DE COMPROBANTES ANULADOS";
	
	private $items_mod = array(
		array("id"=>"V", "descripcion"=>"Venta", "tabla"=>"venta.venta", "pkey"=>"idventa")
		,array("id"=>"NC", "descripcion"=>"Nota de credito", "tabla"=>"venta.notacredito", "pkey"=>"idnotacredito")
		,array("id"=>"GR", "descripcion"=>"Guia de remision", "tabla"=>"almacen.guia_remision", "pkey"=>"idguia_remision")
		,array("id"=>"RI", "descripcion"=>"Recibo de ingreso", "tabla"=>"venta.reciboingreso", "pkey"=>"idreciboingreso")
		,array("id"=>"RE", "descripcion"=>"Recibo de egreso", "tabla"=>"venta.reciboegreso", "pkey"=>"idreciboegreso")
		,array("id"=>"C", "descripcion"=>"Compra", "tabla"=>"compra.compra", "pkey"=>"idcompra")
	);
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title($this->title);
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
		$data["columns"] = $this->get_columns();
		
		$this->load->library('combobox');
		
		$this->combobox->setAttr("id","modulo");
		$this->combobox->setAttr("name","modulo");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->combobox->addItem($this->items_mod);
		$data['modulos'] = $this->combobox->getObject();
		
		// combo sucursal
		$sql = "select idsucursal,descripcion from seguridad.sucursal where estado='A'";
		$query = $this->db->query($sql);
		$this->combobox->init();
		$this->combobox->setAttr("id","idsucursal");
		$this->combobox->setAttr("name","idsucursal");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->combobox->addItem($query->result_array());
		$data['sucursal'] = $this->combobox->getObject();
		
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
	
	protected function get_columns() {
		return array(
			array("field"=>"documento", "label"=>"Documento", "pos"=>"L", "width"=>24, "col"=>"A", "merge"=>"")
			,array("field"=>"total", "label"=>"Total", "pos"=>"R", "width"=>15, "col"=>"B", "merge"=>"")
			,array("field"=>"estado", "label"=>"Estado", "pos"=>"L", "width"=>16, "col"=>"C", "merge"=>"")
			,array("field"=>"usuario", "label"=>"Usuario", "pos"=>"L", "width"=>45, "col"=>"D", "merge"=>"E")
			,array("field"=>"motivo", "label"=>"Motivo", "pos"=>"L", "width"=>70, "col"=>"F", "merge"=>"H")
			,array("field"=>"fecha", "label"=>"Fecha", "pos"=>"L", "width"=>32, "col"=>"I", "merge"=>"J")
		);
	}
	
	public function get_tipodocumentos() {
		$mod = $this->input->post("modulo");
		
		$tabla = "venta.venta";
		foreach($this->items_mod as $v) {
			if($v["id"] == $mod) {
				$tabla = $v["tabla"];
				break;
			}
		}
		
		$sql = "select distinct d.idtipodocumento, d.descripcion from {$tabla} as t
			join venta.tipo_documento as d on d.idtipodocumento=t.idtipodocumento";
		$query = $this->db->query($sql);
		
		$this->load->library('combobox');
		$this->combobox->addItem($query->result_array());
		$this->response($this->combobox->getAllItems());
	}
	
	public function getData($post, $setLimit = TRUE) {
		$tabla = "venta.venta";
		$field_total = "t.subtotal+t.igv-t.descuento";
		$field_doc = "t.serie||'-'||t.correlativo";
		$pkey = "idventa";
		
		foreach($this->items_mod as $v) {
			if($v["id"] == $post["modulo"]) {
				$tabla = $v["tabla"];
				$pkey = $v["pkey"];
				
				if($v["id"] != "V")
					$field_doc = "t.serie||'-'||t.numero";
				
				if($v["id"] == "RI" || $v["id"] == "RE")
					$field_total = "t.monto";
				else if($v["id"] == "NC")
					$field_total = "t.subtotal+t.igv";
				else if($v["id"] == "GR")
					$field_total = "t.costo_minimo";
				
				break;
			}
		}
		
		$sql = "select t.{$pkey} as id, d.abreviatura||'/'||{$field_doc} as documento, {$field_total} as total,
			case when t.estado='X' then 'Eliminado' else 'Anulado' end as estado,
			coalesce(u.nombres||' '||u.appat, '-') as usuario, 
			coalesce(t.motivo_anulacion, '-') as motivo,
			coalesce(to_char(t.fecha_hora_anulacion, 'DD/MM/YYYY - HH12:MI AM'), '-') as fecha
			from {$tabla} t
			join venta.tipo_documento d on d.idtipodocumento = t.idtipodocumento
			left join seguridad.usuario u on u.idusuario = t.idusuario_anulacion
			where t.estado <> 'A'";
		
		if( ! empty($post["idsucursal"]))
			$sql .= " and t.idsucursal=".intval($post["idsucursal"]);
		if( ! empty($post["idtipodocumento"]))
			$sql .= " and t.idtipodocumento=".intval($post["idtipodocumento"]);
		if( ! empty($post["fecha_i"]))
			$sql .= " and t.fecha_hora_anulacion::date>='".$post["fecha_i"]."'";
		if( ! empty($post["fecha_f"]))
			$sql .= " and t.fecha_hora_anulacion::date<='".$post["fecha_f"]."'";
		
		$sql .= " order by t.fecha_hora_anulacion, id";
		
		if($setLimit === TRUE) {
			$page = ( ! empty($post["page"])) ? intval($post["page"]) : 0;
			$offset = $page * $this->limitRow;
			$sql .= " limit ".$this->limitRow." offset ".$offset;
		}
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function get_records() {
		$post = $this->input->post();
		$arr = $this->getData($post);
		
		$page = ( ! empty($post["page"])) ? intval($post["page"]) : 0;
		
		$columns = $this->get_columns();
		$html = '';
		
		if(empty($arr)) {
			if($page <= 0) {
				$html = '<tr class="empty-rs"><td colspan="'.count($columns).'"><i>Sin resultados para la b&uacute;squeda.</i></td></tr>';
			}
		}
		else {
			foreach($arr as $row) {
				$html .= '<tr data-pkey="'.$row["id"].'">';
				foreach($columns as $col) {
					$cls = ($col["pos"] == "R") ? "text-right" : "";
					$html .= '<td class="'.$cls.'">'.$row[$col["field"]].'</td>';
				}
				$html .= '</tr>';
			}
		}
		
		$res["more"] = (count($arr) >= $this->limitRow);
		$res["page"] = $page;
		$res["html"] = $html;
		
		$this->response($res);
	}
	
	protected function add_pdf($label, $value, $nl = true) {
		if( ! isset($this->pdf))
			return;
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->Cell(40,6,utf8_decode($label),0,0,'L');
		$this->pdf->Cell(2,6,utf8_decode(':'),0,0,'C');
		$this->pdf->SetFont('Arial','',9);
		$this->pdf->Cell(130,6,utf8_decode($value),0,0,'L');
		if($nl === true)
			$this->pdf->Ln();
	}
	
	public function imprimir() {
		$this->unlimit();
		$post = $this->input->get();
		$rows = $this->getData($post, false);
		
		$rangofecha = "";
		if( ! empty($post["fecha_i"]))
			$rangofecha = "DESDE ".fecha_es($post["fecha_i"])." ";
		if( ! empty($post["fecha_f"]))
			$rangofecha .= "HASTA ".fecha_es($post["fecha_f"]);
		
		$this->load_model(array("seguridad.empresa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->load->library("pdf");
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		$this->pdf->SetTitle(utf8_decode($this->title), 11, null, true);
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);
        $this->pdf->SetDrawColor(160, 160, 160);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',8);
		
		$this->pdf->SetHeight(3);
		$this->pdf->SetWidths(array(101, 101));
		$this->pdf->Row(array($this->empresa->get("descripcion"), date('d/m/Y')), array("L", "R"), "N", "Y");
		$this->pdf->Row(array("RUC: ".$this->empresa->get("ruc"), date('h:i A')), array("L", "R"), "N", "Y");
		
		$this->pdf->Ln();
		$this->pdf->SetHeight(5);
		if( ! empty($post["modulo"])) {
			$i = array_search($post["modulo"], array_column($this->items_mod, "id"));
			$this->add_pdf("MODULO", $this->items_mod[$i]["descripcion"]);
		}
		if( ! empty($post["idtipodocumento"])) {
			$query = $this->db->where("idtipodocumento", intval($post["idtipodocumento"]))->get("venta.tipo_documento");
			if($query->num_rows() > 0)
				$this->add_pdf("TIPO DOCUMENTO", $query->row()->descripcion);
		}
		if( ! empty($rangofecha)) {
			$this->add_pdf("FECHAS", $rangofecha);
		}
		if( ! empty($post["idsucursal"])) {
			$query = $this->db->where("idsucursal", intval($post["idsucursal"]))->get("seguridad.sucursal");
			if($query->num_rows() > 0)
				$this->add_pdf("SUCURSAL", $query->row()->descripcion);
		}
		$this->pdf->Ln();
		
		$cols = $this->get_columns();
		
		$fields = array_column($cols, "field");
		$pos = array_column($cols, "pos");
		
		$this->pdf->SetFont('Arial','B',9);
		$this->pdf->SetWidths(array_column($cols, 'width'));
		$this->pdf->Row(array_column($cols, 'label'), array_fill(0, count($fields), "C"), "Y", "Y");
		
		$this->pdf->SetFont('Arial','',8);
		foreach($rows as $row) {
			$values = array();
			foreach($fields as $field) {
				$values[] = array_key_exists($field, $row) ? utf8_decode($row[$field]) : "";
			}
			$this->pdf->Row($values, $pos, "Y", "Y");
		}
		
		$this->pdf->Output();
	}
	
	protected function add_excel(&$excel, &$row, $label, $value, $nl=true) {
		$this->row($excel, "A", $row, "{$label} :", "B", true);
		$this->row($excel, "C", $row, $value, "D");
		if($nl === true)
			$row++;
	}
	
	protected function row(&$excel, $col, &$row, $val, $merge="", $bold=false, $border=false) {
		if($bold === true) {
			$excel->getActiveSheet()->getStyle("{$col}{$row}")->getFont()->setBold(true);
		}
		$excel->getActiveSheet()->setCellValue("{$col}{$row}", $val);
		if( ! empty($merge)) {
			$excel->setActiveSheetIndex(0)->mergeCells("{$col}{$row}:{$merge}{$row}");
		}
		if($border === true) {
			$cels = empty($merge) ? "{$col}{$row}" : "{$col}{$row}:{$merge}{$row}";
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
		
		$rangofecha = "";
		if( ! empty($post["fecha_i"]))
			$rangofecha = "DESDE ".fecha_es($post["fecha_i"])." ";
		if( ! empty($post["fecha_f"]))
			$rangofecha .= "HASTA ".fecha_es($post["fecha_f"]);
		
		require_once APPPATH."/libraries/PHPExcel.php"; // libreria excel
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$excel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($excel,$this->title,true,4,58,150,false);
		
		$i = 7;
		
		// draw filtros
		if( ! empty($post["modulo"])) {
			$k = array_search($post["modulo"], array_column($this->items_mod, "id"));
			$this->add_excel($excel, $i, "MODULO", $this->items_mod[$k]["descripcion"]);
		}
		if( ! empty($post["idtipodocumento"])) {
			$query = $this->db->where("idtipodocumento", intval($post["idtipodocumento"]))->get("venta.tipo_documento");
			if($query->num_rows() > 0)
				$this->add_excel($excel, $i, "TIPO DOCUMENTO", $query->row()->descripcion);
		}
		if( ! empty($rangofecha)) {
			$this->add_excel($excel, $i, "FECHAS", $rangofecha);
		}
		if( ! empty($post["idsucursal"])) {
			$query = $this->db->where("idsucursal", intval($post["idsucursal"]))->get("seguridad.sucursal");
			if($query->num_rows() > 0)
				$this->add_excel($excel, $i, "SUCURSAL", $query->row()->descripcion);
		}
		
		$cols = $this->get_columns();
		$rows = $this->getData($post, false);
		
		// draw cabecera
		$i ++;
		foreach($cols as $val) {
			$this->row($excel, $val["col"], $i, $val["label"], $val["merge"], true, true);
		}
		
		// draw detalle
		$i ++;
		foreach($rows as $row) {
			foreach($cols as $val) {
				$v = array_key_exists($val["field"], $row) ? $row[$val["field"]] : "";
				$this->row($excel, $val["col"], $i, $v, $val["merge"], false, true);
			}
			$i ++;
		}
		//*/
		$filename='reporteanulados'.date("dmYhis").'.xlsx'; //save our workbook as this file name
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');  
        $objWriter->save('php://output');
	}
}
?>