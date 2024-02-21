<?php

include_once "Controller.php";

class Preventa_claro extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Preventas de Claro");
		$this->set_subtitle("Lista de preventas");
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
	public function form() {
		$data["controller"] = $this->controller;
		
		// combo empleados
		$idperfil = $this->get_param("idtipovendedor"); // id del perfil vendedor, tal vez deberia ser constante
		$this->load_model("usuario");
		$items = $this->usuario->get_vendedor($this->get_var_session("idsucursal"), $idperfil);
		
		$this->load->library('combobox');
		$this->combobox->setAttr(array("id"=>"idvendedor","class"=>"form-control input-xs"));
		$this->combobox->addItem("", "TODOS");
		$this->combobox->addItem($items);
		$this->combobox->setSelectedOption($this->get_var_session("idusuario"));
		$data["comboempleado"] = $this->combobox->getObject();
		
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
	
	protected function upload($formfile) {
		// subimos el archivo
		$this->load->library('file');
		$this->file->set_input_file($formfile); // atributo name del input[type=file]
		
		$this->file->set_name("temp".date("His")); // nuevo nombre para el archivo
		
		// subimos el archivo
		if($this->file->upload()) {
			return $this->file->get_path()."/".$this->file->get_upload_filename();
		}
		
		return false;
	}
	
	public function procesar() {
		// subimos el archivo
		$rutaarchivo = $this->upload("file");
		if($rutaarchivo === false) {
			$this->exception("Error al subir el archivo.");
			return false;
		}
		
		require_once APPPATH."/libraries/PHPExcel.php"; // libreria excel
		$reader = PHPExcel_IOFactory::createReader('Excel2007');
		$excel = $reader->load($rutaarchivo);
		
		$cols = array(
			"idpedido" => "B"
			,"idcliente" => "C"
			,"cliente" => "D"
			,"idsucursal" => "E"
			,"idusuario" => "G"
			,"idtipoventa" => "I"
			,"fecha_venta" => "K" // 31/01/2017 12:26:45:0
			,"documento" => "L" // Boletas
			,"idproducto" => "AB"
			,"cantidad" => "AD"
			,"precio" => "AF"
		);
		
		$row = 6; // fila de inicio de los datos
		
		$bool = true;
		$array = array();
		$blank = array();
		
		while($bool) {
			$index = false;
			$temp = array();
			
			foreach($cols as $key=>$col) {
				$temp[$key] = trim($excel->getActiveSheet()->getCell("{$col}{$row}")->getValue());
				if($key == "idpedido") {
					if($temp[$key] == "")
						break;
					$index = intval($temp[$key]);
				}
			}
			
			if($index !== false) {
				if( ! array_key_exists($index, $array)) {
					$array[$index] = $temp;
					$array[$index]["detalle"] = array();
				}
				$array[$index]["detalle"][] = $temp;
			}
			else {
				$blank[] = $row;
				$exists = array_intersect($blank, array($row, ($row-1), ($row-2)));
				$bool = (count($exists) < 3);
			}
			
			$row ++;
		}
		
		ksort($array); // ordenamos los item segun el id del pedido
		unlink($rutaarchivo);
		
		$res = $this->guardar($array);
		
		$this->response($res);
	}
	
	protected function guardar($array) {
		if( ! empty($array)) {
			$this->load_model(array("venta.preventa_claro", "venta.detalle_preventa_claro", "general.grupo_igv", "venta.cliente"));
			
			$this->grupo_igv->find($this->get_param("default_igv"));
			
			$this->db->trans_start();
			
			$datos["idusuario"] = $this->get_var_session("idusuario");
			$datos["estado"] = "A";
			$datos["descuento"] = 0;
			$datos["idmoneda"] = 1;
			$datos["pendiente"] = "S";
			$datos["codtipo_operacion"] = "01";
			
			$item["estado"] = "A";
			$item["oferta"] = "N";
			$item["codgrupo_igv"] = $this->grupo_igv->get("codgrupo_igv");
			$item["codtipo_igv"] = $this->grupo_igv->get("tipo_igv_default");
			
			$valor_igv = floatval($this->grupo_igv->get("igv"));
			
			foreach($array as $val) {
				$datos["idcliente"] = (strlen($val["idcliente"])<20) ? intval($val["idcliente"]) : -1;
				$datos["idsucursal"] = intval($val["idsucursal"]);
				$datos["fecha"] = fecha_en($val["fecha_venta"], true);
				$datos["idtipodocumento"] = ($val["documento"] != "Boletas")?1:2;
				$datos["subtotal"] = 0;
				$datos["igv"] = 0;
				$datos["idvendedor"] = intval($val["idusuario"]);
				
				// verificamos si existe el cliente
				$query = $this->db->where("idcliente", $datos["idcliente"])->get("venta.cliente");
				if($query->num_rows() <= 0) {
					$this->cliente->insert(array("nombres"=>$val["cliente"], "estado"=>"A"));
					$datos["idcliente"] = $this->cliente->get("idcliente");
				}
				
				$this->preventa_claro->insert($datos);
				
				$subtotal = $igv = 0;
				$item["idpreventa_claro"] = $this->preventa_claro->get("idpreventa_claro");
				
				foreach($val["detalle"] as $k=>$det) {
					$query = $this->db->where("idproducto", $det["idproducto"])->get("compra.producto");
					if($query->num_rows() <= 0) {
						continue;
					}
					
					$item["iddetalle_preventa_claro"] = $k + 1;
					$item["idproducto"] = $det["idproducto"];
					$item["idunidad"] = $query->row()->idunidad;
					$item["cantidad"] = floatval($det["cantidad"]);
					$item["precio"] = floatval($det["precio"]);
					
					$importe = $item["cantidad"] * $item["precio"];
					$subtotal += $importe;
					$igv += $importe * $valor_igv;
					
					$this->detalle_preventa_claro->insert($item, false);
				}
				
				$datos["idpreventa_claro"] = $this->preventa_claro->get("idpreventa_claro");
				$datos["subtotal"] = $subtotal;
				$datos["igv"] = $igv;
				$this->preventa_claro->update($datos);
			}
			
			$this->db->trans_complete();
		}
		
		return true;
	}
	
	public function get() {
		$post = $this->input->post();
		
		$sql = "select distinct on (idpreventa_claro) p.idpreventa_claro, p.fecha, c.nombres||' '||coalesce(c.apellidos,'') as cliente, 
			m.abreviatura as moneda, p.subtotal+p.igv-p.descuento as total, t.descripcion as tipodoc, 
			i.descripcion_detallada as producto, v.nombres as vendedor, p.pendiente, p.idpreventa
			from venta.preventa_claro p
			join venta.cliente c on c.idcliente = p.idcliente
			join general.moneda m on m.idmoneda = p.idmoneda
			join venta.tipo_documento t on t.idtipodocumento = p.idtipodocumento
			join venta.detalle_preventa_claro d on d.idpreventa_claro = p.idpreventa_claro
			join compra.producto i on i.idproducto = d.idproducto
			join seguridad.usuario v on v.idusuario = p.idvendedor
			where p.estado = 'A' and p.idsucursal = ".$this->get_var_session("idsucursal");
		
		if( ! empty($post["idvendedor"])) {
			$sql .= " and p.idvendedor = ".intval($post["idvendedor"]);
		}
		if( ! empty($post["pendiente"])) {
			$sql .= " and p.pendiente = '".$post["pendiente"]."'";
		}
		
		$sql .= " order by idpreventa_claro";
		
		$query = $this->db->query($sql);
		$this->response($query->result_array());
	}
	
	public function delete() {
		$arr = explode("|", $this->input->post("idpreventa_claro"));
		
		$this->load_model("venta.preventa_claro");
		
		$this->db->trans_start();
		
		$param["estado"] = "I";
		
		foreach($arr as $cod) {
			$param["idpreventa_claro"] = intval($cod);
			$this->preventa_claro->update($param);
		}
		
		$this->db->trans_complete();
		
		$this->response(true);
	}
	
	public function getedit($id) {
		$res = array();
		
		$sql = "select p.idpreventa_claro, p.fecha, t.descripcion as documento, 
			c.nombres||' '||coalesce(c.apellidos,'') as cliente, 
			u.nombres||' '||coalesce(appat,'') as vendedor, 
			p.subtotal, p.igv, p.subtotal+p.igv as total
			from venta.preventa_claro p
			join venta.tipo_documento t on t.idtipodocumento = p.idtipodocumento
			join venta.cliente c on c.idcliente = p.idcliente
			join seguridad.usuario u on u.idusuario = p.idvendedor
			where p.idpreventa_claro=?";
		$query = $this->db->query($sql, array($id));
		if($query->num_rows() > 0)
			$res["cab"] = $query->row_array();
		
		$sql = "select d.idproducto, p.descripcion_detallada as producto, 
			u.abreviatura as unidad, d.cantidad, d.precio, d.cantidad*d.precio as importe
			from venta.detalle_preventa_claro d
			join compra.producto p on p.idproducto = d.idproducto
			join compra.unidad u on u.idunidad = d.idunidad
			where d.estado='A' and d.idpreventa_claro=?";
		$query = $this->db->query($sql, array($id));
		if($query->num_rows() > 0)
			$res["det"] = $query->result_array();
		
		$this->response($res);
	}
	
	public function enviar() {
		$arr = explode("|", $this->input->post("idpreventa_claro"));
		$res = array();
		
		if( ! empty($arr)) {
			$this->load_model(array("venta.preventa_claro", "venta.preventa"));
			
			$this->db->trans_start();
			
			foreach($arr as $id) {
				$id = intval($id);
				if($id <= 0)
					continue;
				
				$datos = $this->preventa_claro->find($id);
				if($datos == null)
					continue;
				
				if($datos["pendiente"] == "N")
					continue;
				
				if(empty($datos["idtipoventa"])) {
					$datos["idtipoventa"] = 1; // contado, deberia jalar del excel, pero no hay el campo
				}
				
				$this->preventa->insert($datos);
				$sql = "INSERT INTO venta.detalle_preventa(
						iddetalle_preventa, idpreventa, idproducto, idunidad, cantidad, 
						precio, estado, idalmacen, serie, oferta, codgrupo_igv, codtipo_igv)
					SELECT iddetalle_preventa_claro, ?, idproducto, idunidad, cantidad,
						precio, estado, idalmacen, serie, oferta, codgrupo_igv, codtipo_igv
						FROM venta.detalle_preventa_claro
						WHERE estado = 'A' AND idpreventa_claro=?";
				$this->db->query($sql, array($this->preventa->get("idpreventa"), $id));
				
				$datos["pendiente"] = "N";
				$datos["idpreventa"] = $this->preventa->get("idpreventa");
				$this->preventa_claro->update($datos);
				
				$res[] = $this->preventa->get_fields();
			}
			
			$this->db->trans_complete();
		}
		
		$this->response($res);
	}
}
?>