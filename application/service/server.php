<?php

require_once APPPATH."/service/nusoap/nusoap.php";

if( ! isset($TipoComprobante))
	$TipoComprobante = array();

function getTipoComprobantes() {
	global $TipoComprobante;
	return json_encode($TipoComprobante);
}

function buscarComprobante($tip_docu, $serie, $numero) {
	global $db;
	$sql = "SELECT * FROM venta.facturacion WHERE estado='A' AND tipo_doc=? AND serie=? AND numero::integer=?";
	$query = $db->query($sql, array($tip_docu, $serie, $numero));
	if($query->num_rows() > 0) {
		return json_encode($query->row_array());
	}
	return "{}";
}

function getPDF($nom_arch) {
	global $db;
	global $_this;
	$sql = "SELECT * FROM venta.facturacion WHERE estado='A' AND nom_arch=?";
	$query = $db->query($sql, array($nom_arch));
	if($query->num_rows() > 0) {
		$row = $query->row_array();
		
		$fc = $_this->get_param("fixed_venta");
		if(!is_numeric($fc)) {
			$fc = 2;
		}
		
		$file = APPPATH."/files/".$row["nom_arch"].".pdf";
		$_this->imprimir_formato($row["idreferencia"], $row["referencia"], "venta", true, $fc, $file);
		if(file_exists($file)) {
			$content = base64_encode(file_get_contents($file));
			unlink($file);
			return $content;
		}
	}
	return "";
}

function getXML($nom_arch) {
	global $_this;
	$_this->load->library('jfacturacion');
	return base64_encode($_this->jfacturacion->get_xml($nom_arch));
}

$server = new soap_server();
$server->configureWSDL("server", "urn:server");

$server->register(
	"getTipoComprobantes",
	array(),
	array("return" => "xsd:string"),
	"urn:server",
	"urn:server#getTipoComprobantes",
	"rpc",
	"encoded",
	"Obtener los tipos de comprobantes del facturador"
);

$server->register(
	"buscarComprobante",
	array(
		"tip_docu" => "xsd:string"
		,'serie' => 'xsd:string'
		,'numero' => 'xsd:int'
	),
	array("return" => "xsd:string"),
	"urn:server",
	"urn:server#buscarComprobante",
	"rpc",
	"encoded",
	"Buscar algun comprobante registrado"
);

$server->register(
	"getPDF",
	array(
		"nom_arch" => "xsd:string"
	),
	array("return" => "xsd:string"),
	"urn:server",
	"urn:server#getPDF",
	"rpc",
	"encoded",
	"Obtener el pdf del comprobante"
);

$server->register(
	"getXML",
	array(
		"nom_arch" => "xsd:string"
	),
	array("return" => "xsd:string"),
	"urn:server",
	"urn:server#getXML",
	"rpc",
	"encoded",
	"Obtener el xml del comprobante"
);

$postdata = (isset($HTTP_RAW_POST_DATA)) ? $HTTP_RAW_POST_DATA : file_get_contents("php://input");
$server->service($postdata);
