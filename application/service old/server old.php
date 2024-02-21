<?php
    
	require_once ("constants.php");
	require_once ("functions.php");
	require_once ("db.php");
	require_once ("lib/simple_html_dom.php");
	require_once (NUSOAP_DIR."/nusoap.php");
	
	/** 
	 * Referescar la pagina y devolver datos del archivo
	 */
	function get_estado_archivo($archivo, $reload = FALSE) {
		$str = "";
		if($reload == TRUE) {
			// $str = download_page(FACTURADOR_URL."index.htm");
		}
		
		$sql = "SELECT * FROM DOCUMENTO WHERE NOM_ARCH='".trim($archivo)."'";
		
		$db = new Db(FACTURADOR_DB);
		$db->query($sql);
		if($db->get_num_rows()) {
			return $db->get_array();
		}
		
		if($str != "") {
			$html = str_get_html(clearfix($str));
			$script = $html->find('script',0)->innertext;
			$array = explode(";", $script);
			$var = array_shift($array);
			
			preg_match("/\[(.*)\]/", $var, $match);
			$datos = json_decode(clearfix($match[0]), true);
			
			if( ! empty($datos)) {
				foreach($datos as $row) {
					if($archivo == $row["nom_arch"]) {
						return $row;
					}
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Obtener el codigo hash del comprobante
	 */
	function get_hashcode($archivo) {
		if(file_exists(FACTURADOR_DIR."/ENVIO/{$archivo}.zip")) {
			// copiamos el archivo zip a la carpeta de este archivo
			copy(FACTURADOR_DIR."/ENVIO/{$archivo}.zip", dirname(__FILE__)."/{$archivo}.zip");
			
			// verificamos si existe el archivo zip despues de haber copiado
			if(file_exists("{$archivo}.zip")) {
				// abrimos el archivo
				$zip = new ZipArchive();
				if($zip->open("{$archivo}.zip") === TRUE) {
					// extraemos los archivos xml en esta carpeta
					$zip->extractTo(dirname(__FILE__)."/");
					$zip->close();
				}
				
				// eliminamos el archivo zip
				unlink("{$archivo}.zip");
			}
			
			// verificamos si existe el xml despues de haber extraido el archivo zip
			if(file_exists("{$archivo}.xml")) {
				$content = file_get_contents("{$archivo}.xml");
				
				$pattern = "/<ds\:Signature\s+[^>]*\bId\s*\=\s*[\x27\x22]SignSUNAT[\x27\x22]>([\s\S]*)<\/ds\:Signature>/";
				preg_match($pattern, $content, $match);
				$content = $match[1];
				
				// obtenemos valor resumen o codigo hash
				$pattern = "/<ds\:DigestValue\s*>([\s\S]*)<\/ds\:DigestValue>/";
				preg_match($pattern, $content, $match);
				$res["resumen_value"] = $match[1];
				
				// obtenemos valor firma
				$pattern = "/<ds\:SignatureValue\s*>([\s\S]*)<\/ds\:SignatureValue>/";
				preg_match($pattern, $content, $match);
				$res["resumen_firma"] = $match[1];
				
				// eliminamos el archivo xml
				unlink("{$archivo}.xml");
				
				return $res;
			}
		}
		
		return false;
	}
	
	/*********************************************************************************************************/
	
	/**
	 * Funcion webservice para colocar los archivos TXT del sistema a la carpeta 
	 * DATA del facturador de Sunat
	 */
	function add_file($tipo_doc, $archivo, $contenido_zip) {
		$temp = "temp".date("His").".zip";
		$res = "ok";
		
		// creamos el archivo zip temporal
		$file = fopen($temp, 'w');
		fwrite($file, base64_decode($contenido_zip));
		fclose($file);
		
		// leemos el zip
		$zip = new ZipArchive();
		if($zip->open($temp) === TRUE) {
			// extraemos los archivos txt en la carpeta data del facturador
			$zip->extractTo(FACTURADOR_DIR."/DATA/");
			$zip->close();
			
			$datos = get_estado_archivo($archivo, true);
			if($datos !== false) {
				$res = json_encode($datos);
			}
		} else {
			$res = "failed";
		}
		
		// eliminamos el archivo temporal
		unlink($temp);
		
		return $res;
	}
	
	/**
	 * Funcion webservice para generar el XML del comprobante
	 */
	function build_comprobante($hddNumRuc, $hddTipDoc, $hddNumDoc, $hddNomArc, $hddEstArc) {
		/*$post_data = "hddNumRuc={$hddNumRuc}&hddTipDoc={$hddTipDoc}&".
			"hddNumDoc={$hddNumDoc}&hddNomArc={$hddNomArc}&hddEstArc={$hddEstArc}";*/
			
		$post_data = "hddNumRuc={$hddNumRuc}&hddTipDoc={$hddTipDoc}&hddNumDoc={$hddNumDoc}";
		//`"num_ruc`":`"20606830671`",`"tip_docu`":`"01`",`"num_docu`":`"F001-00000291`"
				//$post_data = "num_ruc={$hddNumRuc}&tip_docu={$hddTipDoc}&num_docu={$hddNumDoc}";
			//	$post_data = "num_ruc:{$hddNumRuc},tip_docu:{$hddTipDoc},num_docu:{$hddNumDoc}";
		//print_r($post_data);
		$html = socket_post(FACTURADOR_URL."GenerarComprobante.htm", $post_data);
		print_r($html);
		if($html === false) {
			return "failed";
		}

		$hash = get_hashcode($hddNomArc);
		 
		$datos = get_estado_archivo($hddNomArc);
		
		if($hash !== false) {
			if($datos === false)
				return json_encode($hash);
			
			return json_encode(array_merge($datos, $hash));
		}
		else if($datos !== false) {
			return json_encode($datos);
		}
		
		return "ok";
	}
	
	/**
	 * Funcion webservice para enviar el comprobante (XML) a Sunat
	 */
	function send_comprobante($hddNumRuc, $hddTipDoc, $hddNumDoc, $hddNomArc, $hddEstArc) {
		$post_data = "hddNumRuc={$hddNumRuc}&hddTipDoc={$hddTipDoc}&".
			"hddNumDoc={$hddNumDoc}&hddNomArc={$hddNomArc}&hddEstArc={$hddEstArc}";
		
		$html = socket_post(FACTURADOR_URL."enviarXML.htm", $post_data);
		if($html === false) {
			return "failed";
		}
		
		$datos = get_estado_archivo($hddNomArc);
		if($datos !== false) {
			return json_encode($datos);
		}
		
		return "ok";
	}
	
	function get_estado($hddNomArc) {
		// return $hddNomArc;
		$datos = get_estado_archivo($hddNomArc);
		if($datos !== false) {
			$hash = get_hashcode($hddNomArc);
			if($hash !== false) {
				return json_encode(array_merge($datos, $hash));
			}
			return json_encode($datos);
		}
		
		return "ok";
	}
	
	function delete($hddNomArc) {
		$datos = get_estado_archivo($hddNomArc);
		if($datos !== false) {
			if(in_array($datos["IND_SITU"], array("01","02","06","07","10"))) {
				$files = array(
					"DATA" => array("CAB", "DET", "LEY", "ACA", "ADE", "NOT", "CBA")
					,"TEMP" => array("xml")
					,"PARSE" => array("xml")
					,"FIRMA" => array("xml")
					,"ENVIO" => array("zip")
				);
				
				foreach($files as $folder=>$arr) {
					foreach($arr as $ext) {
						$path = FACTURADOR_DIR."/{$folder}/".$datos["NOM_ARCH"].".{$ext}";
						if(file_exists($path)) {
							unlink($path);
						}
					}
				}
				
				$sql = "DELETE FROM DOCUMENTO WHERE NOM_ARCH='".$datos["NOM_ARCH"]."'";
				$db = new Db(FACTURADOR_DB);
				$db->query($sql);
			}
		}
		
		return "ok";
	}
	
	function get_xml($hddNomArc) {
		$folder = "FIRMA";
		$pathfile = FACTURADOR_DIR."/{$folder}/{$hddNomArc}.xml";
		
		if(file_exists($pathfile)) {
			return file_get_contents($pathfile);
		}
		
		return "";
	}
	
	$server = new soap_server();
	$server->configureWSDL("server", "urn:server");

	$server->register(
		"get_estado",
		array("hddNomArc" => "xsd:string"),
		array("return" => "xsd:string"),
		"urn:server",
		"urn:server#get_estado",
		"rpc",
		"encoded",
		"Obtener el estado real del comprobante en el facturador de sunat"
	);
	
	$server->register(
		"delete",
		array("hddNomArc" => "xsd:string"),
		array("return" => "xsd:string"),
		"urn:server",
		"urn:server#delete",
		"rpc",
		"encoded",
		"Eliminar un comprobante generado si se desea modificar algun dato"
	);
	
	$server->register(
		"get_xml",
		array("hddNomArc" => "xsd:string"),
		array("return" => "xsd:string"),
		"urn:server",
		"urn:server#get_xml",
		"rpc",
		"encoded",
		"Obtener el xml firmado del comprobante"
	);
	
	$server->register(
		"add_file",
		array("tipo_doc" => "xsd:string", 'archivo' => 'xsd:string', 'contenido_zip' => 'xsd:string'),
		array("return" => "xsd:string"),
		"urn:server",
		"urn:server#add_file",
		"rpc",
		"encoded",
		"Recibo un archivo zip con los txt de la factura a emitir"
	);

	$server->register(
		"build_comprobante",
		array(
			"hddNumRuc" => "xsd:string"
			,'hddTipDoc' => 'xsd:string'
			,'hddNumDoc' => 'xsd:string'
			//,'hddNomArc' => 'xsd:string'
			//,'hddEstArc' => 'xsd:string'
		),
		array("return" => "xsd:string"),
		"urn:server",
		"urn:server#build_comprobante",
		"rpc",
		"encoded",
		"Generar xml del comprobante para Sunat"
	);
	
	$server->register(
		"send_comprobante",
		array(
			"hddNumRuc" => "xsd:string"
			,'hddTipDoc' => 'xsd:string'
			,'hddNumDoc' => 'xsd:string'
			,'hddNomArc' => 'xsd:string'
			,'hddEstArc' => 'xsd:string'
		),
		array("return" => "xsd:string"),
		"urn:server",
		"urn:server#send_comprobante",
		"rpc",
		"encoded",
		"Enviar el xml del comprobante a Sunat"
	);

	$postdata = (isset($HTTP_RAW_POST_DATA)) ? $HTTP_RAW_POST_DATA : file_get_contents("php://input");
	$server->service($postdata);

?>