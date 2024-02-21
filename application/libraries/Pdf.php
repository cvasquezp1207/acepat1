<?php

require_once APPPATH."libraries/fpdf/fpdf.php";

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Para multicell en tabla por ejemplo vea las funciones: SetWidths, SetAligns, Row.
 * Para escribir texto Html, vea la funcion WriteHTML
 * Para mas ejemplos, manual, info consulte.
 * http://www.fpdf.org/es/tutorial/index.php
 * http://www.fpdf.org/es/doc/index.php
 */

class Pdf extends FPDF {
	// multicell table
	protected $widths;
	protected $aligns;
	
	// text html
	protected $B = 0;
	protected $I = 0;
	protected $U = 0;
	protected $HREF = '';
	public $useFoot=true;
	public $ver_resolucion_sunat=false;
	public $ver_web				=false;
    public $hash_sunat='';
    public $resolucion_sunat='';
    public $referencia_web='';
	// cabecera
	protected $logo = null;
	protected $title = null;
	protected $titleSize = 15;
	protected $titleStyle = 'B';
	protected $height = 5;
	
	protected $cell_padding = array('T' => 0, 'R' => 0, 'B' => 0, 'L' => 0);
	protected $cell_margin = array('T' => 0, 'R' => 0, 'B' => 0, 'L' => 0);
	protected $linestyleWidth = '';
	protected $linestyleCap = '0 J';
	protected $linestyleJoin = '0 j';
	protected $linestyleDash = '[] 0 d';
	protected $textrendermode = 0;
	protected $textstrokewidth = 0;
	protected $bgcolor;
	protected $fgcolor;
	protected $htmlvspace = 0;
	protected $listindent = 0;
	protected $listindentlevel = 0;
	protected $listnum = 0;
	protected $listordered = array();
	protected $listcount = array();
	protected $lispacer = '';
	protected $cell_height_ratio = 1.25;
	protected $font_stretching = 100;
	protected $font_spacing = 0;
	protected $alpha = array('CA' => 1, 'ca' => 1, 'BM' => '/Normal', 'AIS' => false);
	protected $fwPt;
	protected $fhPt;
	protected $current_column = 0;
	protected $num_columns = 1;
	protected $check_page_regions = true;
	protected $rtl = false;
	
	public function SetTitle($title, $size=null, $style=null, $isUTF8=false) {
		$this->title = $isUTF8 ? $title : utf8_encode($title);
		if(is_numeric($size))
			$this->titleSize = $size;
		if($style !== null)
			$this->titleStyle = $style;
		parent::SetTitle($title, $isUTF8);
	}
	
	public function SetHeight($h) {
		$this->height = $h;
	}
	
	public function SetLogo($path_to_image) {
		$this->logo = $path_to_image;
	}
	
	function setFoot($val){
        $this->useFoot=$val;
    }
	
	// Cabecera de página
	public function Header() {
		// Logo
		if($this->logo != null) {
			//$this->Image($this->logo,10,8,25,24);
			$this->Image($this->logo,5,4,35,30);
		}
		if($this->title != null) {
			// Arial bold 15
			$this->SetFont('Arial',$this->titleStyle,$this->titleSize);
			// Movernos a la derecha
			$this->Cell(80);
			// Título
			$this->Cell(30,10,$this->title,0,0,'C');
			// Salto de línea
			$this->Ln(20);
		}
	}

	// Pie de pagina
	public function Footer() {
		// Posición: a 1,5 cm del final
		if($this->useFoot){
			$this->SetY(-15);
			// Arial italic 8
			$this->SetFont('Arial','I',8);
			// Número de página
			$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
		}else if($this->ver_resolucion_sunat){
            if($this->ver_web)
				$this->SetY(6);
			else
				$this->SetY(6);
			$this->SetFont('Arial','',7);
			$this->MultiCell(202 ,4,utf8_decode($this->referencia_web),0,'C',false);
			
			// $this->Ln();
			$this->Cell(202 ,3,$this->hash_sunat,0,1,'C');
			if($this->ver_web){
				$this->SetFont('Arial','',8);
			$this->MultiCell(120 ,4,("Resolución de Superinterdencia N° ".$this->resolucion_sunat),0,'R',false);	
			}
			$this->SetFont('Arial','',6);
			$this->SetY(1);
			$this->MultiCell(202 ,5,("Representación impresa de la boleta de venta electrónica generada desde el sistema facturador SUNAT. Puede verificarla utilizando su clave SOL"),1,'C',true);
		}
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
	public function Row($data, $aligns = array(), $border = "N", $ln = "Y", $fill=array()) {
		$fill_cell = false;
		if (count($aligns) > 0)
            $this->aligns = $aligns;
		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=$this->height*$nb;
		//Issue a page break first if needed
		$this->CheckPageBreak($h);
		//Draw the cells of the row
		for($i=0;$i<count($data);$i++) {
			if(count($fill)>0)
				$fill_cell = $fill[$i];
			$w=$this->widths[$i];
			$a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			//Draw the border
			if ($border == "Y") {
				$this->Rect($x,$y,$w,$h);
			}
			//Print the text
			$this->MultiCell($w,$this->height,$data[$i],0,$a,$fill_cell);
			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		if ($ln == "Y")
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
	
	public function write2DBarcode($code, $type, $x='', $y='', $w='', $h='', $style='', $align='', $distort=false) {
		require_once(dirname(__FILE__).'/tcpdf/tcpdf_barcodes_2d.php');
		// save current graphic settings
		$gvars = $this->getGraphicVars();
		// create new barcode object
		$barcodeobj = new TCPDF2DBarcode($code, $type);
		$arrcode = $barcodeobj->getBarcodeArray();
		if (($arrcode === false) OR empty($arrcode) OR !isset($arrcode['num_rows']) OR ($arrcode['num_rows'] == 0) OR !isset($arrcode['num_cols']) OR ($arrcode['num_cols'] == 0)) {
			$this->Error('Error in 2D barcode string');
		}
		// set default values
		if (!isset($style['position'])) {
			$style['position'] = '';
		}
		if (!isset($style['fgcolor'])) {
			$style['fgcolor'] = array(0,0,0); // default black
		}
		if (!isset($style['bgcolor'])) {
			$style['bgcolor'] = false; // default transparent
		}
		if (!isset($style['border'])) {
			$style['border'] = false;
		}
		// padding
		if (!isset($style['padding'])) {
			$style['padding'] = 0;
		} elseif ($style['padding'] === 'auto') {
			$style['padding'] = 4;
		}
		if (!isset($style['hpadding'])) {
			$style['hpadding'] = $style['padding'];
		} elseif ($style['hpadding'] === 'auto') {
			$style['hpadding'] = 4;
		}
		if (!isset($style['vpadding'])) {
			$style['vpadding'] = $style['padding'];
		} elseif ($style['vpadding'] === 'auto') {
			$style['vpadding'] = 4;
		}
		$hpad = (2 * $style['hpadding']);
		$vpad = (2 * $style['vpadding']);
		// cell (module) dimension
		if (!isset($style['module_width'])) {
			$style['module_width'] = 1; // width of a single module in points
		}
		if (!isset($style['module_height'])) {
			$style['module_height'] = 1; // height of a single module in points
		}
		if ($x === '') {
			$x = $this->x;
		}
		if ($y === '') {
			$y = $this->y;
		}
		// check page for no-write regions and adapt page margins if necessary
		list($x, $y) = $this->checkPageRegions($h, $x, $y);
		// number of barcode columns and rows
		$rows = $arrcode['num_rows'];
		$cols = $arrcode['num_cols'];
		if (($rows <= 0) || ($cols <= 0)){
			$this->Error('Error in 2D barcode string');
		}
		// module width and height
		$mw = $style['module_width'];
		$mh = $style['module_height'];
		if (($mw <= 0) OR ($mh <= 0)) {
			$this->Error('Error in 2D barcode string');
		}
		// get max dimensions
		if ($this->rtl) {
			$maxw = $x - $this->lMargin;
		} else {
			$maxw = $this->w - $this->rMargin - $x;
		}
		$maxh = ($this->h - $this->tMargin - $this->bMargin);
		$ratioHW = ((($rows * $mh) + $hpad) / (($cols * $mw) + $vpad));
		$ratioWH = ((($cols * $mw) + $vpad) / (($rows * $mh) + $hpad));
		if (!$distort) {
			if (($maxw * $ratioHW) > $maxh) {
				$maxw = $maxh * $ratioWH;
			}
			if (($maxh * $ratioWH) > $maxw) {
				$maxh = $maxw * $ratioHW;
			}
		}
		// set maximum dimensions
		if ($w > $maxw) {
			$w = $maxw;
		}
		if ($h > $maxh) {
			$h = $maxh;
		}
		// set dimensions
		if ((($w === '') OR ($w <= 0)) AND (($h === '') OR ($h <= 0))) {
			$w = ($cols + $hpad) * ($mw / $this->k);
			$h = ($rows + $vpad) * ($mh / $this->k);
		} elseif (($w === '') OR ($w <= 0)) {
			$w = $h * $ratioWH;
		} elseif (($h === '') OR ($h <= 0)) {
			$h = $w * $ratioHW;
		}
		// barcode size (excluding padding)
		$bw = ($w * $cols) / ($cols + $hpad);
		$bh = ($h * $rows) / ($rows + $vpad);
		// dimension of single barcode cell unit
		$cw = $bw / $cols;
		$ch = $bh / $rows;
		if (!$distort) {
			if (($cw / $ch) > ($mw / $mh)) {
				// correct horizontal distortion
				$cw = $ch * $mw / $mh;
				$bw = $cw * $cols;
				$style['hpadding'] = ($w - $bw) / (2 * $cw);
			} else {
				// correct vertical distortion
				$ch = $cw * $mh / $mw;
				$bh = $ch * $rows;
				$style['vpadding'] = ($h - $bh) / (2 * $ch);
			}
		}
		// fit the barcode on available space
		list($w, $h, $x, $y) = $this->fitBlock($w, $h, $x, $y, false);
		// set alignment
		$this->img_rb_y = $y + $h;
		// set alignment
		if ($this->rtl) {
			if ($style['position'] == 'L') {
				$xpos = $this->lMargin;
			} elseif ($style['position'] == 'C') {
				$xpos = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
			} elseif ($style['position'] == 'R') {
				$xpos = $this->w - $this->rMargin - $w;
			} else {
				$xpos = $x - $w;
			}
			$this->img_rb_x = $xpos;
		} else {
			if ($style['position'] == 'L') {
				$xpos = $this->lMargin;
			} elseif ($style['position'] == 'C') {
				$xpos = ($this->w + $this->lMargin - $this->rMargin - $w) / 2;
			} elseif ($style['position'] == 'R') {
				$xpos = $this->w - $this->rMargin - $w;
			} else {
				$xpos = $x;
			}
			$this->img_rb_x = $xpos + $w;
		}
		$xstart = $xpos + ($style['hpadding'] * $cw);
		$ystart = $y + ($style['vpadding'] * $ch);
		// barcode is always printed in LTR direction
		$tempRTL = $this->rtl;
		$this->rtl = false;
		// print background color
		if ($style['bgcolor']) {
			$this->Rect($xpos, $y, $w, $h, $style['border'] ? 'DF' : 'F', '', $style['bgcolor']);
		} elseif ($style['border']) {
			$this->Rect($xpos, $y, $w, $h, 'D');
		}
		// set foreground color
		$this->SetDrawColorArray($style['fgcolor']);
		// print barcode cells
		// for each row
		for ($r = 0; $r < $rows; ++$r) {
			$xr = $xstart;
			// for each column
			for ($c = 0; $c < $cols; ++$c) {
				if ($arrcode['bcode'][$r][$c] == 1) {
					// draw a single barcode cell
					$this->Rect($xr, $ystart, $cw, $ch, 'F', array(), $style['fgcolor']);
				}
				$xr += $cw;
			}
			$ystart += $ch;
		}
		// restore original direction
		$this->rtl = $tempRTL;
		// restore previous settings
		$this->setGraphicVars($gvars);
		// set pointer to align the next text/objects
		switch($align) {
			case 'T':{
				$this->y = $y;
				$this->x = $this->img_rb_x;
				break;
			}
			case 'M':{
				$this->y = $y + round($h/2);
				$this->x = $this->img_rb_x;
				break;
			}
			case 'B':{
				$this->y = $this->img_rb_y;
				$this->x = $this->img_rb_x;
				break;
			}
			case 'N':{
				$this->SetY($this->img_rb_y);
				break;
			}
			default:{
				break;
			}
		}
		$this->endlinex = $this->img_rb_x;
	}
	
	protected function getGraphicVars() {
		$grapvars = array(
			'FontFamily' => $this->FontFamily,
			'FontStyle' => $this->FontStyle,
			'FontSizePt' => $this->FontSizePt,
			'rMargin' => $this->rMargin,
			'lMargin' => $this->lMargin,
			'cell_padding' => $this->cell_padding,
			'cell_margin' => $this->cell_margin,
			'LineWidth' => $this->LineWidth,
			'linestyleWidth' => $this->linestyleWidth,
			'linestyleCap' => $this->linestyleCap,
			'linestyleJoin' => $this->linestyleJoin,
			'linestyleDash' => $this->linestyleDash,
			'textrendermode' => $this->textrendermode,
			'textstrokewidth' => $this->textstrokewidth,
			'DrawColor' => $this->DrawColor,
			'FillColor' => $this->FillColor,
			'TextColor' => $this->TextColor,
			'ColorFlag' => $this->ColorFlag,
			'bgcolor' => $this->bgcolor,
			'fgcolor' => $this->fgcolor,
			'htmlvspace' => $this->htmlvspace,
			'listindent' => $this->listindent,
			'listindentlevel' => $this->listindentlevel,
			'listnum' => $this->listnum,
			'listordered' => $this->listordered,
			'listcount' => $this->listcount,
			'lispacer' => $this->lispacer,
			'cell_height_ratio' => $this->cell_height_ratio,
			'font_stretching' => $this->font_stretching,
			'font_spacing' => $this->font_spacing,
			'alpha' => $this->alpha,
			// extended
			'lasth' => $this->lasth,
			'tMargin' => $this->tMargin,
			'bMargin' => $this->bMargin,
			'AutoPageBreak' => $this->AutoPageBreak,
			'PageBreakTrigger' => $this->PageBreakTrigger,
			'x' => $this->x,
			'y' => $this->y,
			'w' => $this->w,
			'h' => $this->h,
			'wPt' => $this->wPt,
			'hPt' => $this->hPt,
			'fwPt' => $this->fwPt,
			'fhPt' => $this->fhPt,
			'page' => $this->page,
			'current_column' => $this->current_column,
			'num_columns' => $this->num_columns
			);
		return $grapvars;
	}
	
	protected function checkPageRegions($h, $x, $y) {
		// set default values
		if ($x === '') {
			$x = $this->x;
		}
		if ($y === '') {
			$y = $this->y;
		}
		if (!$this->check_page_regions OR empty($this->page_regions)) {
			// no page regions defined
			return array($x, $y);
		}
		if (empty($h)) {
			$h = $this->getCellHeight($this->FontSize);
		}
		// check for page break
		if ($this->checkPageBreak($h, $y)) {
			// the content will be printed on a new page
			$x = $this->x;
			$y = $this->y;
		}
		if ($this->num_columns > 1) {
			if ($this->rtl) {
				$this->lMargin = ($this->columns[$this->current_column]['x'] - $this->columns[$this->current_column]['w']);
			} else {
				$this->rMargin = ($this->w - $this->columns[$this->current_column]['x'] - $this->columns[$this->current_column]['w']);
			}
		} else {
			if ($this->rtl) {
				$this->lMargin = max($this->clMargin, $this->original_lMargin);
			} else {
				$this->rMargin = max($this->crMargin, $this->original_rMargin);
			}
		}
		// adjust coordinates and page margins
		foreach ($this->page_regions as $regid => $regdata) {
			if ($regdata['page'] == $this->page) {
				// check region boundaries
				if (($y > ($regdata['yt'] - $h)) AND ($y <= $regdata['yb'])) {
					// Y is inside the region
					$minv = ($regdata['xb'] - $regdata['xt']) / ($regdata['yb'] - $regdata['yt']); // inverse of angular coefficient
					$yt = max($y, $regdata['yt']);
					$yb = min(($yt + $h), $regdata['yb']);
					$xt = (($yt - $regdata['yt']) * $minv) + $regdata['xt'];
					$xb = (($yb - $regdata['yt']) * $minv) + $regdata['xt'];
					if ($regdata['side'] == 'L') { // left side
						$new_margin = max($xt, $xb);
						if ($this->lMargin < $new_margin) {
							if ($this->rtl) {
								// adjust left page margin
								$this->lMargin = max(0, $new_margin);
							}
							if ($x < $new_margin) {
								// adjust x position
								$x = $new_margin;
								if ($new_margin > ($this->w - $this->rMargin)) {
									// adjust y position
									$y = $regdata['yb'] - $h;
								}
							}
						}
					} elseif ($regdata['side'] == 'R') { // right side
						$new_margin = min($xt, $xb);
						if (($this->w - $this->rMargin) > $new_margin) {
							if (!$this->rtl) {
								// adjust right page margin
								$this->rMargin = max(0, ($this->w - $new_margin));
							}
							if ($x > $new_margin) {
								// adjust x position
								$x = $new_margin;
								if ($new_margin > $this->lMargin) {
									// adjust y position
									$y = $regdata['yb'] - $h;
								}
							}
						}
					}
				}
			}
		}
		return array($x, $y);
	}
	
	protected function fitBlock($w, $h, $x, $y, $fitonpage=false) {
		if ($w <= 0) {
			// set maximum width
			$w = ($this->w - $this->lMargin - $this->rMargin);
			if ($w <= 0) {
				$w = 1;
			}
		}
		if ($h <= 0) {
			// set maximum height
			$h = ($this->PageBreakTrigger - $this->tMargin);
			if ($h <= 0) {
				$h = 1;
			}
		}
		// resize the block to be vertically contained on a single page or single column
		if ($fitonpage OR $this->AutoPageBreak) {
			$ratio_wh = ($w / $h);
			if ($h > ($this->PageBreakTrigger - $this->tMargin)) {
				$h = $this->PageBreakTrigger - $this->tMargin;
				$w = ($h * $ratio_wh);
			}
			// resize the block to be horizontally contained on a single page or single column
			if ($fitonpage) {
				$maxw = ($this->w - $this->lMargin - $this->rMargin);
				if ($w > $maxw) {
					$w = $maxw;
					$h = ($w / $ratio_wh);
				}
			}
		}
		// Check whether we need a new page or new column first as this does not fit
		$prev_x = $this->x;
		$prev_y = $this->y;
		if ($this->checkPageBreak($h, $y) OR ($this->y < $prev_y)) {
			$y = $this->y;
			if ($this->rtl) {
				$x += ($prev_x - $this->x);
			} else {
				$x += ($this->x - $prev_x);
			}
			$this->newline = true;
		}
		// resize the block to be contained on the remaining available page or column space
		if ($fitonpage) {
			$ratio_wh = ($w / $h);
			if (($y + $h) > $this->PageBreakTrigger) {
				$h = $this->PageBreakTrigger - $y;
				$w = ($h * $ratio_wh);
			}
			if ((!$this->rtl) AND (($x + $w) > ($this->w - $this->rMargin))) {
				$w = $this->w - $this->rMargin - $x;
				$h = ($w / $ratio_wh);
			} elseif (($this->rtl) AND (($x - $w) < ($this->lMargin))) {
				$w = $x - $this->lMargin;
				$h = ($w / $ratio_wh);
			}
		}
		return array($w, $h, $x, $y);
	}
	
	public function SetDrawColorArray($color, $ret=false) {
		return $this->setColorArray('draw', $color, $ret);
	}
	
	public function setColorArray($type, $color, $ret=false) {
		if (is_array($color)) {
			$color = array_values($color);
			// component: grey, RGB red or CMYK cyan
			$c = isset($color[0]) ? $color[0] : -1;
			// component: RGB green or CMYK magenta
			$m = isset($color[1]) ? $color[1] : -1;
			// component: RGB blue or CMYK yellow
			$y = isset($color[2]) ? $color[2] : -1;
			// component: CMYK black
			$k = isset($color[3]) ? $color[3] : -1;
			// color name
			$name = isset($color[4]) ? $color[4] : '';
			if ($c >= 0) {
				return $this->setColor($type, $c, $m, $y, $k, $ret, $name);
			}
		}
		return '';
	}
	
	public function setColor($type, $col1=0, $col2=-1, $col3=-1, $col4=-1, $ret=false, $name='') {
		// set default values
		if (!is_numeric($col1)) {
			$col1 = 0;
		}
		if (!is_numeric($col2)) {
			$col2 = -1;
		}
		if (!is_numeric($col3)) {
			$col3 = -1;
		}
		if (!is_numeric($col4)) {
			$col4 = -1;
		}
		// set color by case
		$suffix = '';
		if (($col2 == -1) AND ($col3 == -1) AND ($col4 == -1)) {
			// Grey scale
			$col1 = max(0, min(255, $col1));
			$intcolor = array('G' => $col1);
			$pdfcolor = sprintf('%F ', ($col1 / 255));
			$suffix = 'g';
		} elseif ($col4 == -1) {
			// RGB
			$col1 = max(0, min(255, $col1));
			$col2 = max(0, min(255, $col2));
			$col3 = max(0, min(255, $col3));
			$intcolor = array('R' => $col1, 'G' => $col2, 'B' => $col3);
			$pdfcolor = sprintf('%F %F %F ', ($col1 / 255), ($col2 / 255), ($col3 / 255));
			$suffix = 'rg';
		} else {
			$col1 = max(0, min(100, $col1));
			$col2 = max(0, min(100, $col2));
			$col3 = max(0, min(100, $col3));
			$col4 = max(0, min(100, $col4));
			if (empty($name)) {
				// CMYK
				$intcolor = array('C' => $col1, 'M' => $col2, 'Y' => $col3, 'K' => $col4);
				$pdfcolor = sprintf('%F %F %F %F ', ($col1 / 100), ($col2 / 100), ($col3 / 100), ($col4 / 100));
				$suffix = 'k';
			} else {
				// SPOT COLOR
				$intcolor = array('C' => $col1, 'M' => $col2, 'Y' => $col3, 'K' => $col4, 'name' => $name);
				$this->AddSpotColor($name, $col1, $col2, $col3, $col4);
				$pdfcolor = $this->setSpotColor($type, $name, 100);
			}
		}
		switch ($type) {
			case 'draw': {
				$pdfcolor .= strtoupper($suffix);
				$this->DrawColor = $pdfcolor;
				$this->strokecolor = $intcolor;
				break;
			}
			case 'fill': {
				$pdfcolor .= $suffix;
				$this->FillColor = $pdfcolor;
				$this->bgcolor = $intcolor;
				break;
			}
			case 'text': {
				$pdfcolor .= $suffix;
				$this->TextColor = $pdfcolor;
				$this->fgcolor = $intcolor;
				break;
			}
		}
		$this->ColorFlag = ($this->FillColor != $this->TextColor);
		if (($type != 'text') AND ($this->state == 2)) {
			if (!$ret) {
				$this->_out($pdfcolor);
			}
			return $pdfcolor;
		}
		return '';
	}
	
	protected function setGraphicVars($gvars, $extended=false) {
		if ($this->state != 2) {
			 return;
		}
		$this->FontFamily = $gvars['FontFamily'];
		$this->FontStyle = $gvars['FontStyle'];
		$this->FontSizePt = $gvars['FontSizePt'];
		$this->rMargin = $gvars['rMargin'];
		$this->lMargin = $gvars['lMargin'];
		$this->cell_padding = $gvars['cell_padding'];
		$this->cell_margin = $gvars['cell_margin'];
		$this->LineWidth = $gvars['LineWidth'];
		$this->linestyleWidth = $gvars['linestyleWidth'];
		$this->linestyleCap = $gvars['linestyleCap'];
		$this->linestyleJoin = $gvars['linestyleJoin'];
		$this->linestyleDash = $gvars['linestyleDash'];
		$this->textrendermode = $gvars['textrendermode'];
		$this->textstrokewidth = $gvars['textstrokewidth'];
		$this->DrawColor = $gvars['DrawColor'];
		$this->FillColor = $gvars['FillColor'];
		$this->TextColor = $gvars['TextColor'];
		$this->ColorFlag = $gvars['ColorFlag'];
		$this->bgcolor = $gvars['bgcolor'];
		$this->fgcolor = $gvars['fgcolor'];
		$this->htmlvspace = $gvars['htmlvspace'];
		$this->listindent = $gvars['listindent'];
		$this->listindentlevel = $gvars['listindentlevel'];
		$this->listnum = $gvars['listnum'];
		$this->listordered = $gvars['listordered'];
		$this->listcount = $gvars['listcount'];
		$this->lispacer = $gvars['lispacer'];
		$this->cell_height_ratio = $gvars['cell_height_ratio'];
		$this->font_stretching = $gvars['font_stretching'];
		$this->font_spacing = $gvars['font_spacing'];
		$this->alpha = $gvars['alpha'];
		if ($extended) {
			// restore extended values
			$this->lasth = $gvars['lasth'];
			$this->tMargin = $gvars['tMargin'];
			$this->bMargin = $gvars['bMargin'];
			$this->AutoPageBreak = $gvars['AutoPageBreak'];
			$this->PageBreakTrigger = $gvars['PageBreakTrigger'];
			$this->x = $gvars['x'];
			$this->y = $gvars['y'];
			$this->w = $gvars['w'];
			$this->h = $gvars['h'];
			$this->wPt = $gvars['wPt'];
			$this->hPt = $gvars['hPt'];
			$this->fwPt = $gvars['fwPt'];
			$this->fhPt = $gvars['fhPt'];
			$this->page = $gvars['page'];
			$this->current_column = $gvars['current_column'];
			$this->num_columns = $gvars['num_columns'];
		}
		$this->_out(''.$this->linestyleWidth.' '.$this->linestyleCap.' '.$this->linestyleJoin.' '.$this->linestyleDash.' '.$this->DrawColor.' '.$this->FillColor.'');
		// if (!TCPDF_STATIC::empty_string($this->FontFamily)) {
			$this->SetFont($this->FontFamily, $this->FontStyle, $this->FontSizePt);
		// }
	}
}

/* End of file Pdf.php */