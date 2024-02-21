<?php  
 include_once "Controller.php";

class Reporte extends Controller {
	
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

	public function index(){
		// Se carga el modelo alumno
		$this->load->model('consulta_model');
		// Se carga la libreria fpdf
		$this->load->library('pdf');
	 
		// Se obtienen los alumnos de la base de datos
		$alumnos = $this->consulta_model->obtenerListaAlumnos();
	 
		// Creacion del PDF
	 
		/*
		 * Se crea un objeto de la clase Pdf, recuerda que la clase Pdf
		 * heredó todos las variables y métodos de fpdf
		 */
		$this->pdf = new Pdf();
		// Agregamos una página
		$this->pdf->AddPage();
		// Define el alias para el número de página que se imprimirá en el pie
		$this->pdf->AliasNbPages();
	 
		/* Se define el titulo, márgenes izquierdo, derecho y
		 * el color de relleno predeterminado
		 */
		$this->pdf->SetTitle("Lista de alumnos");
		$this->pdf->SetLeftMargin(15);
		$this->pdf->SetRightMargin(15);
		$this->pdf->SetFillColor(200,200,200);
	 
		// Se define el formato de fuente: Arial, negritas, tamaño 9
		$this->pdf->SetFont('Arial', 'B', 9);
		/*
		 * TITULOS DE COLUMNAS
		 *
		 * $this->pdf->Cell(Ancho, Alto,texto,borde,posición,alineación,relleno);
		 */
	 

		$this->pdf->Cell(25,7,'MATERNO','TB',0,'L','1');
		$this->pdf->Cell(25,7,'NOMBRE','TB',0,'L','1');
		$this->pdf->Cell(40,7,'FECHA DE NACIMIENTO','TB',0,'C','1');
		$this->pdf->Cell(25,7,'GRADO','TB',0,'L','1');
		$this->pdf->Ln(7);
		// La variable $x se utiliza para mostrar un número consecutivo
		$x = 1;
		foreach ($alumnos as $alumno) {
		  // se imprime el numero actual y despues se incrementa el valor de $x en uno
		  $this->pdf->Cell(15,5,$x++,'BL',0,'C',0);
		  // Se imprimen los datos de cada alumno
		  $this->pdf->Cell(25,5,$alumno->idpedido,'B',0,'L',0);
		  $this->pdf->Cell(25,5,$alumno->fecha,'B',0,'L',0);
		  $this->pdf->Cell(25,5,$alumno->descripcion,'B',0,'L',0);
		  //Se agrega un salto de linea
		  $this->pdf->Ln(5);
		}
    /*
     * Se manda el pdf al navegador
     *
     * $this->pdf->Output(nombredelarchivo, destino);
     *
     * I = Muestra el pdf en el navegador
     * D = Envia el pdf para descarga
     *
     */
		$this->pdf->Output("Lista de alumnos.pdf", 'I');
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
	
}