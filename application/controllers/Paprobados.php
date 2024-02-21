<?php

include_once "Controller.php";

class Paprobados extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Pedidos Aprobados");
		$this->set_subtitle("Lista de pedidos Aprobados");
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		// $this->js('plugins/jquery-ui/jquery-ui-autocomplete.min');
		// $this->js('form/'.$this->controller.'/index');
		// $this->css('plugins/jQueryUI/jquery-ui-autocomplete.min');
	}
	
	
		/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null) {
		
	}
	
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		$this->load_model("pedido");
		$this->load->library('datatables');
		
		$this->datatables->setModel($this->pedido);
		
		$this->datatables->where('estado', '=', 'C');
		
		$this->datatables->setColumns(array('idpedido','fecha','descripcion'));
		
		$columnasName = array(
			'Nro'
			,'Fecha de Emision'
			,'Descripci&oacute;n'
		);

		$table = $this->datatables->createTable($columnasName);
		$script = "<script>".$this->datatables->createScript()."</script>";
		$this->js($script, false);
		
		// $row = $this->get_permisos();
		// if($row->nuevo == 1 || $row->editar == 1 || $row->eliminar == 1) {
			// $this->add_button("btn_ok_pedido", "Aprobar Pedido", "thumbs-up","warning");
		// }
		
		return $table;
	}
	
	
	public function OC_pedido($idpedido) {
		$this->load_model(array("compra.pedido", "seguridad.sucursal", "seguridad.empresa"));
		$this->pedido->find($idpedido);
		$this->sucursal->find($this->pedido->get("idsucursal"));
		$this->empresa->find($this->sucursal->get("idsucursal"));
		
		$this->load->library("pdf");
		
		// $this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		$this->pdf->SetTitle(utf8_decode("PEDIDO DE COMPRA N° ".$this->pedido->get("idpedido")), 11, null, true);
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		
		// creamos la pagina
		$this->pdf->AddPage();

		$sql = "SELECT p.idpedido,  p.idsucursal, p.fecha, p.descripcion, su.descripcion as sucursal, su.direccion AS direccion, su.telefono
				FROM compra.pedido AS p
				INNER JOIN seguridad.sucursal AS su ON p.idsucursal= su.idsucursal
				WHERE p.idpedido=$idpedido";
		$query = $this->db->query($sql);
		$query0 = $query->result_array();
		$this->pdf->SetFont('Arial','',9);
		
		$i = 25;
		$a = 6;
		$this->pdf->SetXY(15,$i);
		$this->pdf->Cell(25,$a,'DESCRIPCION',0,0,'L');
		$this->pdf->Cell(5,$a,':',0,0,'C');
		$this->pdf->Cell(150,$a,$query0[0]['descripcion'],0,0,'L');
			
		$this->pdf->Ln();
				
		$this->pdf->SetX(15);
		$this->pdf->Cell(25,$a,'SUCURSAL',0,0,'L');
		$this->pdf->Cell(5,$a,':',0,0,'C');
		$this->pdf->Cell(90,$a,$query0[0]['sucursal'],0,0,'L');
		$this->pdf->Cell(20,$a,'FECHA',0,0,'L');
		$this->pdf->Cell(5,$a,':',0,0,'C');
		$this->pdf->Cell(35,$a,$query0[0]['fecha'],0,0,'L');
		
		$this->pdf->Ln();
			
		$this->pdf->SetX(15);
		$this->pdf->Cell(25,$a,'DIRECCION',0,0,'L');
		$this->pdf->Cell(5,$a,':',0,0,'C');
		$this->pdf->Cell(90,$a,$query0[0]['direccion'],0,0,'L');
		$this->pdf->Cell(20,$a,'TELEFONO',0,0,'L');
		$this->pdf->Cell(5,$a,':',0,0,'C');
		$this->pdf->Cell(35,$a,$query0[0]['telefono'],0,0,'L');
		
		$sql2 = "SELECT d.idproducto,p.descripcion as producto, d.idunidad, u.descripcion as medida, d.cantidad
				FROM compra.detalle_pedido AS d
				INNER JOIN compra.producto AS p ON d.idproducto = p.idproducto
				INNER JOIN compra.unidad AS u ON d.idunidad = u.idunidad
				WHERE d.idpedido = $idpedido";
		$query2 = $this->db->query($sql2);
		
		
		//DETALLE	
		$this->pdf->SetXY(8,57);
		$this->pdf->Cell(8,$a,'Nro.',1,0,'C');
		$this->pdf->Cell(20,$a,'Codigo Prod.',1,0,'C');
		$this->pdf->Cell(109,$a,'Descripcion',1,0,'C');
		$this->pdf->Cell(35,$a,'U. Medida',1,0,'C');
		$this->pdf->Cell(22,$a,'Cantidad',1,0,'C');
		
		$this->pdf->Ln();
		$this->pdf->SetX(8);
		if ($query2->num_rows() > 0){		
			$n=1;
			$cant = 0;
			foreach ($query2->result() as $row){
				$this->pdf->Cell(8,$a,$n,1,0,'C');
				$this->pdf->Cell(20,$a,$row->idproducto,1,0,'C');
				$this->pdf->Cell(109,$a,$row->producto,1,0,'L');
				$this->pdf->Cell(35,$a,$row->medida,1,0,'C');
				$this->pdf->Cell(22,$a,number_format($row->cantidad,2),1,0,'R');
				$this->pdf->Ln();
				$this->pdf->SetX(8);
				$n++;
				$cant = $cant + $row->cantidad;
		    }
			$this->pdf->SetFont('Arial','B',9);
			$this->pdf->Cell(172,$a,'TOTAL: ','R',0,'R');
			$this->pdf->Cell(22,$a,number_format($cant,2),1,0,'R');
			// $this->pdf->Ln();
		}
		
		// mostramos la pagina
		$this->pdf->Output();
	}
	
	
	
	
}
?>