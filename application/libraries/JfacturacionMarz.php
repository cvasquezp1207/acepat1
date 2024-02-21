<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Jfacturacion {
	
	private $ci = NULL;
	private $idsucursal = NULL;
	private $ruc = NULL;
	
	private $pad_length = 8;
	private $fixed = 2;
	private $tipo_operacion_venta = "01"; // Anexo 8 Catalogo 17, venta interna
	
	private $path = NULL;
	
	public $grupo_igv_default = "INA";
	public $tipo_igv_default = "30"; // Anexo 8 Catalogo 7, inafecto - operacion onerosa
	public $tipo_igv_oferta = "31"; // Anexo 8 Catalogo 7, inafecto - retiro por bonificacion
	// private $grupo_igv_default = "EXO";
	// private $tipo_igv_default = "20"; // Anexo 8 Catalogo 7, exonerado - operacion onerosa
	// private $tipo_igv_oferta = "21"; // Anexo 8 Catalogo 7, exonerado - transferencia gratuita
	
	private $config = array(
		"venta" => array("model"=>"venta.venta", "pkey"=>"idventa")
		,"notacredito" => array("model"=>"venta.notacredito", "pkey"=>"idnotacredito")
		,"notadebito" => array("model"=>"venta.notadebito", "pkey"=>"idnotadebito")
		,"documento_baja" => array("model"=>"venta.documento_baja", "pkey"=>"iddocumento_baja")
	);
	
	/**
	 * Constructor, obtemos el codeigniter 
	 */
	public function __construct() {
		$this->init();
	}
	
	private function init() {
		$this->ci =& get_instance();
		//$this->path = APPPATH."files";
		$this->path = APPPATH."files";
	}
	
	public function get_sucursal($ref, $id) {
		if($this->idsucursal != NULL)
			return $this->idsucursal;
		
		if( ! array_key_exists($ref, $this->config))
			return 0;
		
		$sql = "select idsucursal from ".$this->config[$ref]["model"]." where ".$this->config[$ref]["pkey"]." = ?";
		$query = $this->ci->db->query($sql, array($id));
		$this->idsucursal = $query->row()->idsucursal;
		
		return $this->idsucursal;
	}
	
	public function load_ruc($idsucursal=0) {
		if($this->ruc != NULL)
			return;
		
		$sql = "select ruc from seguridad.empresa 
			where idempresa in (
				select idempresa from seguridad.sucursal where idsucursal=?
			)";
		$query = $this->ci->db->query($sql, array($idsucursal));
		$this->ruc = $query->row()->ruc;
	}
	
	public function save_file($tipo_doc, $serie, $numero, $ext, $content) {
		$real_path = "E:\SFS_v1.3.4.2\sunat_archivos\sfs\DATA";
		$file_name = $real_path."/".$this->ruc."-{$tipo_doc}-{$serie}-{$numero}.{$ext}";
		
		$file = fopen($file_name, "w+");
		fwrite($file, $content);
		fclose($file);
		
		return $file_name;
	}
	
	public function get_data_venta($id, $all=FALSE) {
		// cabecera
		$sql = "select coalesce(t.codtipo_operacion, '".$this->tipo_operacion_venta."') as top, 
			d.codsunat as tdoc, t.serie, t.correlativo as numero, t.fecha_venta as fecha,to_char(t.fecha_registro,'HH12:MI:SS') hora, c.dni, c.ruc, 
			c.nombres||coalesce(c.apellidos,'') as razon, m.abreviatura as moneda, t.igv, t.subtotal, 
			t.subtotal+t.igv-t.descuento as total, t.idcliente, t.descuento, t.estado
			from venta.venta t
			join venta.tipo_documento d on d.idtipodocumento=t.idtipodocumento
			join venta.cliente c on c.idcliente=t.idcliente
			join general.moneda m on m.idmoneda=t.idmoneda
			where t.idventa=?";
		$query = $this->ci->db->query($sql, array($id));
		$res["cab"] = $query->row_array();
		
		$estado = $res["cab"]["estado"];
		
		// detalle
		$sql = "select u.codsunat as unidmed, t.cantidad, p.codigo_producto as codproducto, t.descripcion as producto, 
			case when t.oferta='S' then 0.00 else t.precio end as valor_unit, 
			case when t.oferta='S' then 0.00 else 0.00 end as sum_dscto, 
			case when t.oferta='S' then 0.00 else t.precio*t.igv*t.cantidad end as sum_igv,
			case when t.oferta='S' then 0.00  else t.precio*(1+t.igv) end as precio_venta, 
			case when t.oferta='S' then t.precio*t.cantidad else t.precio*t.cantidad end as valor_venta,
			case when t.codtipo_igv is not null then t.codtipo_igv else
				case when t.oferta='S' then '".$this->tipo_igv_oferta."'::text else 
					case when coalesce(t.igv,0)>0 then '10'::text else '".$this->tipo_igv_default."'::text end
				end
			end as tipo_igv
			,t.oferta, t.precio as pu_real, array_to_string(array_agg(s.serie), ', '::text) AS serie,
			coalesce(t.codgrupo_igv, '".$this->grupo_igv_default."') as codgrupo_igv
			from venta.detalle_venta t 
			join compra.producto p on p.idproducto=t.idproducto
			join compra.unidad u on u.idunidad=t.idunidad
			left join venta.detalle_venta_serie s on s.iddetalle_venta=t.iddetalle_venta and s.idventa=t.idventa
			where t.idventa=? and t.estado=? 
			group by u.codsunat, t.cantidad, p.codigo_producto, t.descripcion, t.oferta, t.precio, t.igv, 
			t.iddetalle_venta, t.codtipo_igv, t.codgrupo_igv
			order by t.iddetalle_venta";
		$query = $this->ci->db->query($sql, array($id, $estado));
		$res["det"] = $query->result_array();
		
		return $res;
	}
	
	public function get_data_nota_credito($id, $all=FALSE) {
		// cabecera
		$sql = "select t.idtipo_notacredito as top, d.codsunat as tdoc, t.serie, t.numero, 
			t.fecha, c.dni, c.ruc, c.nombres||coalesce(' '||c.apellidos,'') as razon, 
			m.abreviatura as moneda, t.igv, t.subtotal, t.subtotal+t.igv as total, 
			t.descripcion as motivo, d2.codsunat as tdoc_ref, t.serie_ref, t.numero_ref, 
			t.idcliente, t.estado
			from venta.notacredito t
			join venta.tipo_documento d on d.idtipodocumento=t.idtipodocumento
			join venta.cliente c on c.idcliente=t.idcliente
			join general.moneda m on m.idmoneda=t.idmoneda
			join venta.tipo_documento d2 on d2.idtipodocumento=t.iddocumento_ref
			where t.idnotacredito=?";
			
		$query = $this->ci->db->query($sql, array($id));
		$res["cab"] = $query->row_array();
		
		$estado = $res["cab"]["estado"];
		
		// detalle
		$sql = "select u.codsunat as unidmed, t.cantidad, p.codigo_producto as codproducto, 
			t.descripcion as producto, t.precio as valor_unit, 0.00 as sum_dscto, 
			t.precio*t.igv*t.cantidad as sum_igv, t.precio*(1+t.igv) as precio_venta, 
			t.precio*t.cantidad as valor_venta, t.codtipo_igv as tipo_igv, 'N'::text as oferta, 
			t.precio as pu_real, t.serie, t.codgrupo_igv
			from venta.detalle_notacredito t 
			join compra.unidad u on u.idunidad=t.idunidad
			join compra.producto p on p.idproducto=t.idproducto
			where t.idnotacredito=? and t.estado=? 
			order by iddetalle_notacredito";
			
		$query = $this->ci->db->query($sql, array($id, $estado));
		$res["det"] = $query->result_array();
		
		return $res;
	}
	
	public function get_data_nota_debito($id, $all=FALSE) {
		// cabecera
		$sql = "select t.idtipo_notadebito as top, d.codsunat as tdoc, t.serie, t.numero, 
			t.fecha, c.dni, c.ruc, c.nombres||coalesce(' '||c.apellidos,'') as razon, 
			m.abreviatura as moneda, t.igv, t.subtotal, t.subtotal+t.igv as total, 
			t.descripcion as motivo, d2.codsunat as tdoc_ref, t.serie_ref, t.numero_ref, 
			t.idcliente, t.estado
			from venta.notadebito t
			join venta.tipo_documento d on d.idtipodocumento=t.idtipodocumento
			join venta.cliente c on c.idcliente=t.idcliente
			join general.moneda m on m.idmoneda=t.idmoneda
			join venta.tipo_documento d2 on d2.idtipodocumento=t.iddocumento_ref
			where t.idnotadebito=?";
			
		$query = $this->ci->db->query($sql, array($id));
		$res["cab"] = $query->row_array();
		
		$estado = $res["cab"]["estado"];
		
		// detalle
		$sql = "select u.codsunat as unidmed, t.cantidad, p.codigo_producto as codproducto, 
			t.descripcion as producto, t.precio as valor_unit, 0.00 as sum_dscto, 
			t.precio*t.igv*t.cantidad as sum_igv, t.precio*(1+t.igv) as precio_venta, 
			t.precio*t.cantidad as valor_venta, t.codtipo_igv as tipo_igv, 'N'::text as oferta, 
			t.precio as pu_real, t.serie, t.codgrupo_igv
			from venta.detalle_notadebito t 
			join compra.unidad u on u.idunidad=t.idunidad
			join compra.producto p on p.idproducto=t.idproducto
			where t.idnotadebito=? and t.estado=?
			order by iddetalle_notadebito";
			
		$query = $this->ci->db->query($sql, array($id, $estado));
		$res["det"] = $query->result_array();
		
		return $res;
	}
	
	public function get_data_baja($id, $all=FALSE) {
		// cabecera
		$sql = "select correlativo, fec_gene, fecha, tip_docu, num_docu, motivo
			from venta.documento_baja where iddocumento_baja = ?";
			
		$query = $this->ci->db->query($sql, array($id));
		$res["cab"] = $query->row_array();
		$res["det"] = false;
		
		return $res;
	}
	
	public function make_file_ley($tdoc, $serie, $numero, $total,$oferta) {
		$str="";
		if($total > 0 ) {
			if( ! isset($this->ci->numeroletra))
				$this->ci->load->library('numeroletra');
			$str = "1000|".$this->ci->numeroletra->convertir(number_format($total,2,'.',''), true)."|\r\n";
			
			if($oferta > 0 ) {
			$str .= "1002|TRANSFERENCIA GRATUITA DE UN BIEN Y/O SERVICIO PRESTADO GRATUITAMENTE";
			}			
		} else{				
			$str = "1002|TRANSFERENCIA GRATUITA DE UN BIEN Y/O SERVICIO PRESTADO GRATUITAMENTE";
		}
				
				
		$this->save_file($tdoc, $serie, $numero, "LEY", $str);
	}
	
	public function make_file_tri($tdoc, $serie, $numero, $total,$oferta) {
		$str="";
		if($total > 0) {
			$str = "9997|EXO|VAT|".number_format($total,2,'.','')."|0|\r\n";
			
			if($oferta > 0) {
			$str .= "9996|GRA|FRE|".number_format($oferta,2,'.','')."|0|";
			}		
		}
		else{
				$str = "9996|GRA|FRE|".number_format($oferta,2,'.','')."|0|";
			}
		$this->save_file($tdoc, $serie, $numero, "TRI", $str);
	}
		
	public function make_file_det($tipo_doc, $serie, $numero, $data) {
		$content = "";
		if( ! empty($data)) {
			foreach($data as $i=>$row) {
				
				if ($row["oferta"]=="S"){
					$nombreigv = "GRA" ;
					$codinter = "FRE";	
					$codTri = "9996";
					$valvent = $row["pu_real"];					
					
				}else{
					$nombreigv = "EXO" ;
					$codinter = "VAT";
					$codTri = "9997"	;	
					$valvent = 0;
									
				}
				
				$producto = trim($row["producto"]);
				if( ! empty($row["serie"])) {
					$txtSeries = trim($row["serie"]);
					$txtSeries = str_replace("|", ",", $txtSeries);
					$producto .= " S/N: ".$txtSeries;
				}
				if(strlen($producto) > 250) {
					$producto = substr($producto, 0, 250);
				}
				$producto = str_replace("|","/",$producto);
				$producto = str_replace("\\","/",$producto);
				$producto = str_replace("&", "&amp;", $producto);
				
				$linea = array();
				$linea[] = $row["unidmed"]; // cod unidad medida (NIU)		 		codUnidadMedida				1
				$linea[] = $row["cantidad"]; // cantidad de unid x item		ctdUnidadItem						2
				$linea[] = $row["codproducto"]; // codigo producto		codProducto								3
				$linea[] = ""; // codigo producto sunat		 codProductoSUNAT									4 falta
				$linea[] = $producto; // descripcion item		desItem											5
				$linea[] = number_format($row["valor_unit"],2,".",""); // mtoValorUnitario						6
				$linea[] = number_format($row["sum_igv"],2,".",""); // monto igv x item	 sumTotTributosItem		7
				$linea[] = $codTri; //codTriIGV																	8
				$linea[] = number_format($row["sum_igv"],2,".",""); // monto igv x item	 mtoIgvItem				9
				$linea[] = number_format($row["valor_venta"],2,".",""); // mtoBaseIgvItem						10
				$linea[] = $nombreigv; // nomTributoIgvItem														11
				$linea[] = $codinter; // nomTributoIgvItem														12
				$linea[] = $row["tipo_igv"]; // nomTributoIgvItem												13
				$linea[] = "0.00"; // porIgvItem																14
				$linea[] = "-"; // descuento x item																15
				$linea[] = "0.00"; // mtoIscItem																16
				$linea[] = "0.00"; // mtoBaseIscItem															17
				$linea[] = ""; // mtoBaseIscItem																18
				$linea[] = ""; // mtoBaseIscItem																19
				$linea[] = ""; // tipo sistema isc (catalogo 8, Sistema al valor)								20
				$linea[] = "15"; // tipSisISC																	21
				$linea[] = "-"; // tipSisISC																	22
				$linea[] = "0.00"; // mtoIscItem																23
				$linea[] = "0.00"; // mtoBaseIscItem															24
				$linea[] = ""; // mtoBaseIscItem																25
				$linea[] = ""; // mtoBaseIscItem																26		
				$linea[] = "15"; // mtoBaseIscItem																27
				$linea[] = "-"; // tipSisISC																	28
				$linea[] = "0.00"; // mtoIscItem																29
				$linea[] = "0"; // mtoBaseIscItem																30
				$linea[] = ""; // mtoBaseIscItem																31
				$linea[] = ""; // mtoBaseIscItem																32
				$linea[] = "0.00"; // mtoIscItem																33
				$linea[] = number_format($row["precio_venta"],2,'.',''); // precio venta unitario x item		34
				$linea[] = number_format($row["valor_venta"],2,'.',''); //Valor Venta							35
				$linea[] = number_format($valvent,2,'.',''); // monto isc x item								36
							
				$content .= implode("|", $linea);
				$content .= "\r\n";
	/*	
	


codUnidadMedida			ok 1
ctdUnidadItem			ok 2
codProducto				ok 3
codProductoSUNAT		falta 4
desItem					ok 5
mtoValorUnitario		ok 6
sumTotTributosItem		ok 7
codTriIGV				ok 8
mtoIgvItem				ok 9
mtoBaseIgvItem			ok 10
nomTributoIgvItem		OK 11 REVISAR
codTipTributoIgvItem	OK 12 REVISA
tipAfeIGV				OK 13  REVISAR
porIgvItem				OK 14 REVISAR
codTriISC				OK 15 REVISAR
mtoIscItem				OK 16
mtoBaseIscItem			OK 17
nomTributoIscItem		OK 18
codTipTributoIscItem	OK 19
tipSisISC				OK 20
porIscItem				OK 21
codTriOtro				OK 22
mtoTriOtroItem			OK 23
mtoBaseTriOtroItem		OK 24
nomTributoOtroItem		OK 25
codTipTributoOtroItem	OK 26
porTriOtroItem			OK 27
codTriIcbper			28
mtoTriIcbperItem		29
ctdBolsasTriIcbperItem	OK 30 REVISAR CANTIDAD DE BOLSAS 
nomTributoIcbperItem	OK 31
codTipTributoIcbperItem	OK 32
mtoTriIcbperUnidad		OK 33
mtoPrecioVentaUnitario	34
mtoValorVentaItem		35
mtoValorReferencialUnitario	36

*/
				
				
				
			}
		}
		
		// guardamos el archivo detalle
		$this->save_file($tipo_doc, $serie, $numero, "DET", $content);
	}
	
	public function make_file_venta($cab, $det,$fixed=2) {
		// datos para el archivo
		$serie = $cab["serie"];
		if(is_numeric($serie)) {
			$serie = intval($serie);
			$serie = str_pad($serie, 4, "0", STR_PAD_LEFT);
		}
		$numero = $cab["numero"];
		if(is_numeric($numero)) {
			$numero = intval($numero);
			$numero = str_pad($numero, $this->pad_length, "0", STR_PAD_LEFT);
		}
		
		// datos del contenido del archivo cabecera
		$tdoc = $ruc_dni = "-";
		$razon = trim($cab["razon"]);
		if($cab["tdoc"] == "01") { // factura
			$tdoc = 6;
			$ruc_dni = $cab["ruc"];
			$razon = trim($cab["razon"]);
		}
		else if(!empty($cab["idcliente"]) && $cab["idcliente"] != 0 && !empty($cab["dni"])) {
			$tdoc = 1;
			$ruc_dni = $cab["dni"];
			$razon = trim($cab["razon"]);
		}
		
		if($tdoc == "-" && $ruc_dni == "-") { // fix, no deberia ser asi, consultar en SUNAT
			$tdoc = 1;
			$ruc_dni = "00000000";
		}
		
		if(strlen($razon) > 100)
			$razon = substr($razon, 0, 100);
		$razon = str_replace("&", "&amp;", $razon);
		
		// obtenemos el total descuento desde los items
		$strAde = "";
		$totalDscto = $totalGra = $totalIna = $totalExo = $sumaIgv = $totalOferta = 0;
		if( ! empty($det)) {
			foreach($det as $i=>$row) {
				// tener en cuenta que posiblemente haciendo un "redondeosunat" esto no 
				// sea igual con el total facturado en la venta
				if($row["codgrupo_igv"] == "GRA")
					$totalGra += redondeosunat($row["valor_venta"],$fixed);
				else if($row["codgrupo_igv"] == "EXO")
					$totalExo += redondeosunat($row["valor_venta"],$fixed);
				else if($row["codgrupo_igv"] == "INA")
					$totalIna += redondeosunat($row["valor_venta"],$fixed);
				
				$sumaIgv += $row["sum_igv"];
				$totalDscto += $row["sum_dscto"];
				
				if($row["oferta"] == "S") {
					$strAde .= number_format($row["pu_real"], 2, '.', '')."|-\r\n";
					$totalOferta += $row["pu_real"] * $row["cantidad"];
					$totalExo = $totalExo-$row["valor_venta"];					
				}
				else
					$strAde .= "0.00|-\r\n";
			}
		}
		// $importeTotal = $cab["total"];
		$importeTotal = $totalGra + $totalIna + $totalExo + $sumaIgv - $cab["descuento"];
		
		$linea = array();
		$linea[] = "0101"; // tipo operacion (Anexo 8 Catalogo 17, venta interna)								1
		$linea[] = $cab["fecha"]; // fecha emision																2
		$linea[] = $cab["hora"]; // hora emision																3
		$linea[] = "-"; // fecha vencimientos por defecto -											4
		$linea[] = "0000"; // codigo domicilio fiscal o de local anexo del emisor(N3)							5
		$linea[] = $tdoc; // tipo doc cliente (DNI o RUC)														6
		$linea[] = $ruc_dni; // numero doc cliente (numero DNI o RUC)											7
		$linea[] = $razon; // nombre cliente, razon social														8
		$linea[] = $cab["moneda"]; // moneda (PEN,USD,...)														9
		$linea[] = number_format($sumaIgv,2,".",""); // sum igv		 o tributos									10
		$linea[] = number_format($importeTotal,2,".",""); // sumTotValVenta										11
		$linea[] = number_format($importeTotal,2,".",""); // sumPrecioVenta										12
		$linea[] = number_format($totalDscto,2,'.',''); // total descuentos										13
		$linea[] = "0.00"; // sumOtrosCargos																	14
		$linea[] = "0.00"; // sumTotalAnticipos																	15		
		$linea[] = number_format($importeTotal,2,".",""); // sumPrecioVenta										16
		$linea[] = "2.1"; // VersionUbl																			17
		$linea[] = "2.0"; // Documento																			18
		
		// guardamos el archivo cabecera
		$file = $this->save_file($cab["tdoc"], $serie, $numero, "CAB", implode("|", $linea));
		
		// armamos el archivo detalle
		$this->make_file_det($cab["tdoc"], $serie, $numero, $det);
		
		// verificamos si la venta ha tenido ofertas
		/*if($totalOferta > 0 && $strAde != "") {
			// creamos los archivos adicionales de cabecera y detalles
			$strAca = "|0.00|0.00|0.00|".number_format($totalOferta, 2, ".", "")."|0.00|||||||".date("Y-m-d");
			$this->save_file($cab["tdoc"], $serie, $numero, "ACA", $strAca);
			
			// creamos el archivo adicionales de detalle
			$this->save_file($cab["tdoc"], $serie, $numero, "ADE", $strAde);
		}*/
		
		// creamos archivo leyenda
		$this->make_file_ley($cab["tdoc"], $serie, $numero, $importeTotal,$totalOferta);
		
		// creamos archivo tributo
		$this->make_file_tri($cab["tdoc"], $serie, $numero, $importeTotal,$totalOferta);
		
		$res["tipo_doc"] = $cab["tdoc"];
		$res["serie"] = $serie;
		$res["numero"] = $numero;
		$res["archivo"] = basename($file, ".CAB");
		$res["ruta"] = dirname($file);
		$res["tip_docu_cliente"] = $tdoc;
		$res["num_docu_cliente"] = $ruc_dni;
		return $res;
	}
	
	public function make_file_nota($cab, $det, $fixed=2) {
		// datos para el archivo
		$serie = $cab["serie"];
		if(is_numeric($serie)) {
			$serie = intval($serie);
			$serie = str_pad($serie, 4, "0", STR_PAD_LEFT);
		}
		$numero = $cab["numero"];
		if(is_numeric($numero)) {
			$numero = intval($numero);
			$numero = str_pad($numero, $this->pad_length, "0", STR_PAD_LEFT);
		}
		
		// datos del contenido del archivo cabecera
		$serie_ref = $cab["serie_ref"];
		if(is_numeric($serie_ref)) {
			$serie_ref = intval($serie_ref);
			$serie_ref = str_pad($serie_ref, 4, "0", STR_PAD_LEFT);
		}
		$numero_ref = $cab["numero_ref"];
		if(is_numeric($numero_ref)) {
			$numero_ref = intval($numero_ref);
			$numero_ref = str_pad($numero_ref, 8, "0", STR_PAD_LEFT);
		}
		
		// datos del contenido del archivo cabecera
		$tdoc = $ruc_dni = "-";
		$razon = trim($cab["razon"]);
		if($cab["tdoc_ref"] == "01") { // modifica una factura
			$tdoc = 6;
			$ruc_dni = $cab["ruc"];
			$razon = trim($cab["razon"]);
		}
		else if(!empty($cab["idcliente"]) && $cab["idcliente"] != 0 && !empty($cab["dni"])) {
			$tdoc = 1;
			$ruc_dni = $cab["dni"];
			$razon = trim($cab["razon"]);
		}
		
		if($tdoc == "-" && $ruc_dni == "-") { // fix, no deberia ser asi, consutar en SUNAT
			$tdoc = 1;
			$ruc_dni = "00000000";
		}
		
		if(strlen($razon) > 100)
			$razon = substr($razon, 0, 100);
		$razon = str_replace("&", "&amp;", $razon);
		
		$motivo = $cab["motivo"];
		if(strlen($motivo) > 250)
			$motivo = substr($motivo, 0, 250);
		$motivo = str_replace("|","/",$motivo);
		
		// obtenemos el total descuento desde los items
		$totalGra = $totalIna = $totalExo = $sumaIgv = 0;
		if( ! empty($det)) {
			foreach($det as $i=>$row) {
				if($row["codgrupo_igv"] == "GRA")
					$totalGra += $row["valor_venta"];
				else if($row["codgrupo_igv"] == "EXO")
					$totalExo += $row["valor_venta"];
				else if($row["codgrupo_igv"] == "INA")
					$totalIna += $row["valor_venta"];
				
				$sumaIgv += $row["sum_igv"];
			}
		}
		// $importeTotal = $cab["total"];
		$importeTotal = $totalGra + $totalIna + $totalExo + $sumaIgv;
		
		$linea = array();
		$linea[] = "0101"; // tipo nota (rev catalog 51)														1
		$linea[] = $cab["fecha"]; // fecha emision																2
		$linea[] = date("H:i:s", time()); // hora emision														3
		$linea[] = "0000"; // codigo domicilio fiscal o de local anexo del emisor(N3)							4
		$linea[] = $tdoc; // tipo doc cliente (DNI o RUC)														5
		$linea[] = $ruc_dni; // numero doc cliente (numero DNI o RUC)											6
		$linea[] = $razon; // nombre cliente, razon social														7
		$linea[] = $cab["moneda"]; // moneda (PEN,USD,...)														8
		$linea[] = $cab["top"];	// tipo nota (rev catalog 9 o 10)												9
		$linea[] = $motivo; // motivo o sustento																10
		$linea[] = $cab["tdoc_ref"]; // tipo doc que modifica													11
		$linea[] = $serie_ref.'-'.$numero_ref; // nro doc que modifica											12
		$linea[] = "0.00"; // Sumatoria de Tributos																13
		$linea[] = number_format($importeTotal,2,".",""); // importe total venta								14
		$linea[] = number_format($importeTotal,2,".","");// Total Precio Venta									15
		$linea[] = "0.00"; // total descuento																	16
		$linea[] = "0.00"; // sum otros cargos																	17
		$linea[] = "0.00"; // Total Anticipos																	18
		$linea[] = number_format($importeTotal,2,".",""); // Imp. Tot venta,  o del servicio prestado			19
		$linea[] = "2.1"; // VersionUbl																			20
		$linea[] = "2.0"; // Documento																			11
				
		// guardamos el archivo cabecera
		$file = $this->save_file($cab["tdoc"], $serie, $numero, "NOT", implode("|", $linea));
		
		// armamos el archivo detalle
		$this->make_file_det($cab["tdoc"], $serie, $numero, $det);
		
		// creamos archivo leyenda
		$this->make_file_ley($cab["tdoc"], $serie, $numero, $cab["total"]);
		
		// creamos archivo tributo
		$this->make_file_tri($cab["tdoc"], $serie, $numero, $importeTotal);
		
		$res["tipo_doc"] = $cab["tdoc"];
		$res["serie"] = $serie;
		$res["numero"] = $numero;
		$res["archivo"] = basename($file, ".NOT");
		$res["ruta"] = dirname($file);
		$res["tip_docu_cliente"] = $tdoc;
		$res["num_docu_cliente"] = $ruc_dni;
		return $res;
	}
	
	public function make_file_baja($cab, $det, $fixed) {
		// datos para el archivo
		$tdoc = "RA";
		$serie = str_replace("-", "", $cab["fecha"]);
		$numero = str_pad($cab["correlativo"], 3, "0", STR_PAD_LEFT);
		
		// datos del contenido del archivo cabecera
		$fec_gen = fecha_en($cab["fec_gene"]); // var: dd/mm/yyyy hh:ii:ss
		
		$motivo = $cab["motivo"];
		if(strlen($motivo) > 100)
			$motivo = substr($motivo, 0, 100);
		
		$linea = array();
		$linea[] = $fec_gen; // fecha generacion del doc de baja				1
		$linea[] = $cab["fecha"]; // fecha comunicacion							2
		$linea[] = $cab["tip_docu"]; // tipo doc baja							3
		$linea[] = $cab["num_docu"]; // num doc baja							4
		$linea[] = $motivo; // motivo baja										5
		
		// guardamos el archivo cabecera
		$file = $this->save_file($tdoc, $serie, $numero, "CBA", implode("|", $linea));
		
		$res["tipo_doc"] = $tdoc;
		$res["serie"] = $serie;
		$res["numero"] = $numero;
		$res["archivo"] = basename($file, ".CBA");
		$res["ruta"] = dirname($file);
		return $res;
	}
	
	public function is_ref($ref) {
		return array_key_exists($ref, $this->config);
	}
	
	public function crear_files($ref, $id, $idsucursal=FALSE,$fixed = 2) {
		if( ! $this->is_ref($ref))
			return false;
		
		if($idsucursal === FALSE) {
			$idsucursal = $this->get_sucursal($ref, $id);
		}
		$this->load_ruc($idsucursal);
		
		if($ref == "venta") {
			$data = $this->get_data_venta($id);
			$res = $this->make_file_venta($data["cab"], $data["det"],$fixed);
		}
		else if($ref == "notacredito") {
			$data = $this->get_data_nota_credito($id);
			$res = $this->make_file_nota($data["cab"], $data["det"],$fixed);
		}
		else if($ref == "notadebito") {
			$data = $this->get_data_nota_debito($id);
			$res = $this->make_file_nota($data["cab"], $data["det"],$fixed);
		}
		else if($ref == "documento_baja") {
			$data = $this->get_data_baja($id);
			$res = $this->make_file_baja($data["cab"], $data["det"],$fixed);
		}
		
		$res["idreferencia"] = $id;
		$res["referencia"] = $ref;
		return $res;
	}
	
	public function enviar_files($data) {
		$file = $data["archivo"];
		if($data["tipo_doc"] == "RA") {
			$ext = "CBA";
		}
		else if($data["tipo_doc"] == "07" || $data["tipo_doc"] == "08") { // nota debito o credito
			$ext = "NOT";
		}
		else {
			$ext = "CAB";
		}
		
		$temp = $this->path."/temp.zip";
		
		// creamos el archivo zip
		$zip_file = new ZipArchive();
		$zip_file->open($temp, ZipArchive::CREATE);
		
		// adjuntamos los archivos al zip
		if(file_exists($data["ruta"]."/{$file}.{$ext}"))
			$zip_file->addFile($data["ruta"]."/{$file}.{$ext}", "{$file}.{$ext}");
		if(file_exists($data["ruta"]."/{$file}.DET"))
			$zip_file->addFile($data["ruta"]."/{$file}.DET", "{$file}.DET");
		if(file_exists($data["ruta"]."/{$file}.LEY"))
			$zip_file->addFile($data["ruta"]."/{$file}.LEY", "{$file}.LEY");
		
		if($ext == "CAB") {
			if(file_exists($data["ruta"]."/{$file}.ACA"))
				$zip_file->addFile($data["ruta"]."/{$file}.ACA", "{$file}.ACA");
			if(file_exists($data["ruta"]."/{$file}.ADE"))
				$zip_file->addFile($data["ruta"]."/{$file}.ADE", "{$file}.ADE");
		}
		
		$zip_file->close();
		
		// obtenemos el contenido del zip
		$zip_content = base64_encode(file_get_contents($temp));
		
		// eliminamos el archivo temporal
		unlink($temp);
		
		include_once APPPATH."service/client.php";
		
		// parametros para el envio del zip al facturador
		$param["tipo_doc"] = $data["tipo_doc"];
		$param["archivo"] = $data["archivo"];
		$param["contenido_zip"] = $zip_content;
		// print_r($param);
		$url = $this->ci->get_param("url_webservice");
		//echo $url;
		
		// invocamos el servicio web
		return call($url, "add_file", $param);
	}
	
	public function crear_comprobante($ref, $id) {
		if( ! $this->is_ref($ref))
			return false;
		
		$sql = "select * from venta.facturacion where idreferencia=? and referencia=? and estado='A'";
		$query = $this->ci->db->query($sql, array($id, $ref));
		$data = $query->row_array();
		
		// parametros de la funcion
		$param["hddNumRuc"] = $data["num_ruc"];
		$param["hddTipDoc"] = $data["tip_docu"];
		$param["hddNumDoc"] = $data["num_docu"];
		$param["hddNomArc"] = $data["nom_arch"];
		$param["hddEstArc"] = $data["ind_situ"];
		
		$url = $this->ci->get_param("url_webservice");
		//echo $param;
		//global $db;
		//echo $db;
		
		// servicio web
		include_once APPPATH."service/client.php";
		
		// invocamos el servicio web
		return call($url, "build_comprobante", $param);
	}
	
	public function enviar_comprobante($ref, $id) {
		if( ! $this->is_ref($ref))
			return false;
		
		$sql = "select * from venta.facturacion where idreferencia=? and referencia=?";
		$query = $this->ci->db->query($sql, array($id, $ref));
		$data = $query->row_array();
		
		// parametros de la funcion
		$param["hddNumRuc"] = $data["num_ruc"];
		$param["hddTipDoc"] = $data["tip_docu"];
		$param["hddNumDoc"] = $data["num_docu"];
		$param["hddNomArc"] = $data["nom_arch"];
		$param["hddEstArc"] = $data["ind_situ"];
		
		$url = $this->ci->get_param("url_webservice");
		
		//echo $url;
		
		// servicio web
		include_once APPPATH."service/client.php";
		
		// invocamos el servicio web
		return call($url, "send_comprobante", $param);
	}
	
	public function get_estado($nom_arch) {
		$url = $this->ci->get_param("url_webservice");
		
		// servicio web
		include_once APPPATH."service/client.php";
		// invocamos el servicio web
		return call($url, "get_estado", array("hddNomArc"=>$nom_arch));
	}
	
	public function delete($nom_arch) {
		$url = $this->ci->get_param("url_webservice");
		
		// servicio web
		include_once APPPATH."service/client.php";
		// invocamos el servicio web
		return call($url, "delete", array("hddNomArc"=>$nom_arch));
	}
	
	public function get_xml($nom_arch) {
		$url = $this->ci->get_param("url_webservice");
		// servicio web
		include_once APPPATH."service/client.php";
		// invocamos el servicio web
		return call($url, "get_xml", array("hddNomArc"=>$nom_arch));
	}
}

