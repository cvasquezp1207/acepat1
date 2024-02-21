<?php
// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');
// create new PDF document
$pdf = new TCPDF();

$pdf->setPrintHeader(false);
// $pdf->setPrintFooter(true);
$pdf->AddPage();
$style = array(
	'border' => 1,
	'vpadding' => 'auto',
	'hpadding' => 'auto',
	'fgcolor' => array(0,0,0),
	'bgcolor' => false, //array(255,255,255)
	'module_width' => 1, // width of a single module in points
	'module_height' => 1 // height of a single module in points
);
//90=>top
//80=>left
//30 ancho
$pdf->write2DBarcode('www.tcpdf.oaarg', 'PDF417', 80, 90, 0, 30);
$pdf->Text(80, 85, 'PDF417 (ISO/IEC 15438:2006)');

$pdf->Output('example_050.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
