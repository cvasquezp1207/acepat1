<?php

require_once APPPATH."libraries/fpdf/fpdf.php";

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pdf extends FPDF {
	// multicell table
	protected $widths;
	protected $aligns;
	
	// text html
	protected $B = 0;
	protected $I = 0;
	protected $U = 0;
	protected $HREF = '';
	
	// cabecera
	protected $logo = null;
	protected $title = "Untitle";
	
	public function SetTitle($title, $isUTF8=false) {
		$this->title = $isUTF8 ? $title : utf8_encode($title);
		parent::SetTitle($title, $isUTF8);
	}
	
	public function SetLogo($path_to_image) {
		$this->logo = $path_to_image;
	}
	
	// Cabecera de página
	public function Header() {
		// Logo
		if($this->logo != null) {
			$this->Image($this->logo,10,8,33);
		}
		// Arial bold 15
		$this->SetFont('Arial','B',15);
		// Movernos a la derecha
		$this->Cell(80);
		// Título
		$this->Cell(30,10,$this->title,0,0,'C');
		// Salto de línea
		$this->Ln(20);
	}

	// Pie de página
	public function Footer() {
		// Posición: a 1,5 cm del final
		$this->SetY(-15);
		// Arial italic 8
		$this->SetFont('Arial','I',8);
		// Número de página
		$this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
	}

	/**
	 * Multicell table function
	 * @param array $w ancho de las celdas
	 * e.g:
	 * $pdf->SetWidths(array(24, 30, 20)); // 3 columnas
	 */
	public function SetWidths($w) {
		$this->widths=$w;
	}
	
	/**
	 * Multicell table function
	 * @param array $a alineacion del texto en la celda
	 * e.g:
	 * $pdf->SetAligns(array("L", "L", "R")); // 3 columnas, Left y Right
	 */
	public function SetAligns($a) {
		$this->aligns=$a;
	}
	
	/**
	 * Multicell table function
	 * @param array $data texto a imprimir en las celdas
	 * e.g:
	 * $pdf->Row(array("USD", "DOLARES AMERICANOS", 150.00)); // 3 columnas, Left y Right
	 */
	public function Row($data) {
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=5*$nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++) {
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			//Draw the border
			$this->Rect($x,$y,$w,$h);
			//Print the text
			$this->MultiCell($w,5,$data[$i],0,$a);
			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	/**
	 * Multicell table function
	 */
	protected function CheckPageBreak($h) {
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
			$this->AddPage($this->CurOrientation);
	}
	protected function NbLines($w,$txt) {
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb) {
			$c=$s[$i];
			if($c=="\n") {
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax) {
				if($sep==-1) {
					if($i==$j)
						$i++;
				}
				else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
				$i++;
		}
		return $nl;
	}
	
	/**
	 * write text html function
	 * @param String $html texto a escribir
	 * e.g.:
	 * $pdf->WriteHTML("Hola <b>mundo</b>, <i><b>¡Bienvenidos!</b></i>");
	 */
	public function WriteHTML($html) {
		// Intérprete de HTML
		$html = str_replace("\n",' ',$html);
		$a = preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
		foreach($a as $i=>$e) {
			if($i%2==0) {
				// Text
				if($this->HREF)
					$this->PutLink($this->HREF,$e);
				else
					$this->Write(5,$e);
			}
			else {
				// Etiqueta
				if($e[0]=='/')
					$this->CloseTag(strtoupper(substr($e,1)));
				else {
					// Extraer atributos
					$a2 = explode(' ',$e);
					$tag = strtoupper(array_shift($a2));
					$attr = array();
					foreach($a2 as $v) {
						if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
							$attr[strtoupper($a3[1])] = $a3[2];
					}
					$this->OpenTag($tag,$attr);
				}
			}
		}
	}

	/**
	 * write text html function
	 */
	protected function OpenTag($tag, $attr) {
		// Etiqueta de apertura
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,true);
		if($tag=='A')
			$this->HREF = $attr['HREF'];
		if($tag=='BR')
			$this->Ln(5);
	}
	protected function CloseTag($tag) {
		// Etiqueta de cierre
		if($tag=='B' || $tag=='I' || $tag=='U')
			$this->SetStyle($tag,false);
		if($tag=='A')
			$this->HREF = '';
	}
	protected function SetStyle($tag, $enable) {
		// Modificar estilo y escoger la fuente correspondiente
		$this->$tag += ($enable ? 1 : -1);
		$style = '';
		foreach(array('B', 'I', 'U') as $s) {
			if($this->$s>0)
				$style .= $s;
		}
		$this->SetFont('',$style);
	}
	protected function PutLink($URL, $txt) {
		// Escribir un hiper-enlace
		$this->SetTextColor(0,0,255);
		$this->SetStyle('U',true);
		$this->Write(5,$txt,$URL);
		$this->SetStyle('U',false);
		$this->SetTextColor(0);
	}
}

/* End of file Pdf.php */