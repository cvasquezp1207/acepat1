<?php

include_once "Controller.php";

class Facturacion extends Controller {
	
	public $ListaSituacion = null;
	public $TipoComprobante =  null;
	private $limitRow = 1000;
	private $limitUpdate = 5;
	
	public function __construct($normal = TRUE, $validate_login = TRUE) {
		parent::__construct($normal, $validate_login);
		
		$this->ListaSituacion = array(
			array("id"=>"01","nombre"=>"Por Generar XML"),
			array("id"=>"02","nombre"=>"XML Generado"),
			array("id"=>"03","nombre"=>"Enviado y Aceptado SUNAT"),
			array("id"=>"04","nombre"=>"Enviado y Aceptado SUNAT con Obs."),
			array("id"=>"05","nombre"=>"Enviado y Anulado por SUNAT"),
			array("id"=>"06","nombre"=>"Con Errores"),
			array("id"=>"07","nombre"=>"Por Validar XML"),
			array("id"=>"08","nombre"=>"Enviado a SUNAT Por Procesar"),
			array("id"=>"09","nombre"=>"Enviado a SUNAT Procesando"),
			array("id"=>"10","nombre"=>"Rechazado por SUNAT")
		);
		
		$this->TipoComprobante = array(
			array("id"=>"01","nombre"=>"Factura","abrev"=>"F"),
			array("id"=>"03","nombre"=>"Boleta de Venta","abrev"=>"BV"),
			array("id"=>"07","nombre"=>"Nota de Credito","abrev"=>"NC"),
			array("id"=>"08","nombre"=>"Nota de Debito","abrev"=>"ND"),
			array("id"=>"RA","nombre"=>"Comunicación de Baja","abrev"=>"CB")
		);
	}
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Consulta de productos");
		$this->set_subtitle("Consultar Productos");
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
		
		$this->load->library('combobox');
		
		// combo tipo documentos
		$this->combobox->setAttr("id","idtipodocumento");
		$this->combobox->setAttr("name","idtipodocumento");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->combobox->setStyle("width","120px");
		$this->combobox->addItem("", "TODOS");
		$this->combobox->addItem($this->TipoComprobante);
		$data['tipodocumento'] = $this->combobox->getObject();
		
		// combo estado situacion
		$this->combobox->removeItems(1);
		$this->combobox->setAttr("id","situacion");
		$this->combobox->setAttr("name","situacion");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->combobox->addItem($this->ListaSituacion);
		$data['situacion'] = $this->combobox->getObject();
		
		// combo sucursal
		$idsucursal = $this->get_var_session("idsucursal");
		$sql = "select idsucursal,descripcion from seguridad.sucursal where estado='A'";
		if ($this->get_var_session("control_reporte")=='N') {
			$sql.= ' AND idsucursal='.$idsucursal;
		}
		$query = $this->db->query($sql);
		$this->combobox->removeItems(1);
		$this->combobox->init();
		$this->combobox->setAttr("id","idsucursal");
		$this->combobox->setAttr("name","idsucursal");
		$this->combobox->setAttr("class","form-control input-xs");
		if ($this->get_var_session("control_reporte")=='S') 
			$this->combobox->addItem("", "TODOS");
		
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
	
	public function getSituacion($var) {
		foreach($this->ListaSituacion as $array) {
			if($array["id"] == $var)
				return $array["nombre"];
		}
		
		return "-";
	}
	
	public function getComprobante($var, $key="nombre") {
		foreach($this->TipoComprobante as $array) {
			if($array["id"] == $var)
				return $array[$key];
		}
		
		return "-";
	}
	
	private function _queryNotaCredito($order_by = TRUE, $limit = TRUE) {
		$post = $this->input->post();
		
		$sql = "select f.num_ruc, f.tip_docu, f.num_docu, to_char(r.fecha,'DD/MM/YYYY') as fec_emis, f.fec_carg, 
			f.fec_gene, f.fec_envi, f.ind_situ, f.des_obse, f.referencia, f.idreferencia, r.fecha as fecha_real
			,'-'::text as doc_ref
			from venta.facturacion f
			join venta.notacredito r on r.idnotacredito=f.idreferencia and f.referencia='notacredito'
			where coalesce(f.estado,'A') = 'A'";
		
		if( ! empty($post["fecha_i"]))
			$sql .= " and r.fecha >= '" . $post["fecha_i"] . "'";
		
		if( ! empty($post["fecha_f"]))
			$sql .= " and r.fecha <= '" . $post["fecha_f"] . "'";
		
		if( ! empty($post["idtipodocumento"]))
			$sql .= " and f.tipo_doc = '" . $post["idtipodocumento"] . "'";
		
		if( ! empty($post["situacion"]))
			$sql .= " and f.ind_situ = '" . $post["situacion"] . "'";
		
		if( ! empty($post["idsucursal"]))
			$sql .= " and r.idsucursal = " . intval($post["idsucursal"]);
		
		if($order_by) {
			$sql .= " order by fecha_real desc, idreferencia desc";
		}
		
		if($limit) {
			$page = ( ! empty($post["page"])) ? intval($post["page"]) : 0;
			$offset = $page * $this->limitRow;
			$sql .= " limit " . $this->limitRow . " offset " . $offset;
		}
		
		return $sql;
	}
	
	private function _queryNotaDebito($order_by = TRUE, $limit = TRUE) {
		$post = $this->input->post();
		
		$sql = "select f.num_ruc, f.tip_docu, f.num_docu, to_char(r.fecha,'DD/MM/YYYY') as fec_emis, f.fec_carg, 
			f.fec_gene, f.fec_envi, f.ind_situ, f.des_obse, f.referencia, f.idreferencia, r.fecha as fecha_real
			,'-'::text as doc_ref
			from venta.facturacion f
			join venta.notadebito r on r.idnotadebito=f.idreferencia and f.referencia='notadebito'
			where coalesce(f.estado,'A') = 'A'";
		
		if( ! empty($post["fecha_i"]))
			$sql .= " and r.fecha >= '" . $post["fecha_i"] . "'";
		
		if( ! empty($post["fecha_f"]))
			$sql .= " and r.fecha <= '" . $post["fecha_f"] . "'";
		
		if( ! empty($post["idtipodocumento"]))
			$sql .= " and f.tipo_doc = '" . $post["idtipodocumento"] . "'";
		
		if( ! empty($post["situacion"]))
			$sql .= " and f.ind_situ = '" . $post["situacion"] . "'";
		
		if( ! empty($post["idsucursal"]))
			$sql .= " and r.idsucursal = " . intval($post["idsucursal"]);
		
		if($order_by) {
			$sql .= " order by fecha_real desc, idreferencia desc";
		}
		
		if($limit) {
			$page = ( ! empty($post["page"])) ? intval($post["page"]) : 0;
			$offset = $page * $this->limitRow;
			$sql .= " limit " . $this->limitRow . " offset " . $offset;
		}
		
		return $sql;
	}
	
	private function _queryVenta($order_by = TRUE, $limit = TRUE) {
		$post = $this->input->post();
		
		$sql = "select f.num_ruc, f.tip_docu, f.num_docu, to_char(r.fecha_venta,'DD/MM/YYYY') as fec_emis, f.fec_carg, 
			f.fec_gene, f.fec_envi, f.ind_situ, f.des_obse, f.referencia, f.idreferencia, r.fecha_venta as fecha_real
			,'-'::text as doc_ref
			from venta.facturacion f
			join venta.venta r on r.idventa=f.idreferencia and f.referencia='venta'
			where coalesce(f.estado,'A') = 'A'";
		
		if( ! empty($post["fecha_i"]))
			$sql .= " and r.fecha_venta >= '" . $post["fecha_i"] . "'";
		
		if( ! empty($post["fecha_f"]))
			$sql .= " and r.fecha_venta <= '" . $post["fecha_f"] . "'";
		
		if( ! empty($post["idtipodocumento"]))
			$sql .= " and f.tipo_doc = '" . $post["idtipodocumento"] . "'";
		
		if( ! empty($post["situacion"]))
			$sql .= " and f.ind_situ = '" . $post["situacion"] . "'";
		
		if( ! empty($post["idsucursal"]))
			$sql .= " and r.idsucursal = " . intval($post["idsucursal"]);
		
		if($order_by) {
			$sql .= " order by fecha_real desc, idreferencia desc";
		}
		
		if($limit) {
			$page = ( ! empty($post["page"])) ? intval($post["page"]) : 0;
			$offset = $page * $this->limitRow;
			$sql .= " limit " . $this->limitRow . " offset " . $offset;
		}
		
		return $sql;
	}
	
	private function _queryBaja($order_by = TRUE, $limit = TRUE) {
		$post = $this->input->post();
		
		$sql = "select f.num_ruc, f.tip_docu, f.num_docu, to_char(r.fecha,'DD/MM/YYYY') as fec_emis, f.fec_carg, 
			f.fec_gene, f.fec_envi, f.ind_situ, f.des_obse, f.referencia, f.idreferencia, r.fecha as fecha_real,
			r.tip_docu||'-'||r.num_docu as doc_ref
			from venta.facturacion f
			join venta.documento_baja r on r.iddocumento_baja=f.idreferencia::integer and f.referencia='documento_baja'
			where coalesce(f.estado,'A')='A' and f.tipo_doc = 'RA'";
		
		if( ! empty($post["fecha_i"]))
			$sql .= " and r.fecha >= '" . $post["fecha_i"] . "'";
		
		if( ! empty($post["fecha_f"]))
			$sql .= " and r.fecha <= '" . $post["fecha_f"] . "'";
		
		if( ! empty($post["situacion"]))
			$sql .= " and f.ind_situ = '" . $post["situacion"] . "'";
		
		if( ! empty($post["idsucursal"])) {
			$sql .= " and r.idsucursal = " . intval($post["idsucursal"]);
		}
		
		if($order_by) {
			$sql .= " order by fecha_real desc, idreferencia desc";
		}
		
		if($limit) {
			$page = ( ! empty($post["page"])) ? intval($post["page"]) : 0;
			$offset = $page * $this->limitRow;
			$sql .= " limit " . $this->limitRow . " offset " . $offset;
		}
		
		return $sql;
	}
	
	public function getData() {
		$post = $this->input->post();
		
		if( ! empty($post["idtipodocumento"])) {
			$sql = "";
			if($post["idtipodocumento"] == "RA") {
				$sql = $this->_queryBaja();
			}
			else if($post["idtipodocumento"] == "07") {
				$sql = $this->_queryNotaCredito();
			}
			else if($post["idtipodocumento"] == "08") {
				// $sql = $this->_queryNotaDebito();
			}
			else {
				$sql = $this->_queryVenta();
			}
			
			if($sql != "") {
				$query = $this->db->query($sql);
				return $query->result_array();
			}
			return array();
		}
		
		// todos los documentos
		$sql = $this->_queryVenta(false, false);
		$sql .= " union ".$this->_queryNotaCredito(false, false);
		// $sql .= " union ".$this->_queryNotaDebito(false, false);
		$sql .= " union ".$this->_queryBaja(false, false);
		$sql .= " order by fecha_real";
		
		$page = ( ! empty($post["page"])) ? intval($post["page"]) : 0;
		$offset = $page * $this->limitRow;
		$sql .= " limit " . $this->limitRow . " offset " . $offset;
		
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function get_records() {
		$arr = $this->getData();
		
		$post = $this->input->post();
		$page = ( ! empty($post["page"])) ? intval($post["page"]) : 0;
		
		$html = '';
		
		if(empty($arr)) {
			if($page <= 0) {
				$html = '<tr class="empty-rs"><td colspan="9"><i>Sin resultados para la b&uacute;squeda.</i></td></tr>';
			}
		}
		else {
			foreach($arr as $row) {
				$doc_ref = $row["doc_ref"];
				if($doc_ref != "-") {
					$ref = explode("-", $doc_ref);
					$doc_ref = $this->getComprobante($ref[0], "abrev").". ".$ref[1]."-".intval($ref[2]);
				}
				
				$html .= '<tr data-idref="'.$row["idreferencia"].'" data-ref="'.$row["referencia"].'" data-tdoc="'.$row["tip_docu"].'">';
				$html .= '<td>'.$row["num_ruc"].'</td>';
				$html .= '<td>'.$this->getComprobante($row["tip_docu"]).'</td>';
				$html .= '<td>'.$row["num_docu"].'</td>';
				$html .= '<td>'.$doc_ref.'</td>';
				$html .= '<td>'.$row["fec_carg"].'</td>';
				$html .= '<td>'.$row["fec_gene"].'</td>';
				$html .= '<td>'.$row["fec_envi"].'</td>';
				$html .= '<td>'.$this->getSituacion($row["ind_situ"]).'</td>';
				$html .= '<td>'.$row["des_obse"].'</td>';
				$html .= '</tr>';
			}
		}
		
		$res["more"] = (count($arr) >= $this->limitRow);
		$res["page"] = $page;
		$res["html"] = $html;
		
		$this->response($res);
	}
	
	public function update($response = TRUE) {
		$this->load_model("venta.facturacion");
		$this->load->library('jfacturacion');
		
		$this->facturacion->find($this->input->post());
		$nom_arch = $this->facturacion->get("nom_arch");
		if(empty($nom_arch))
			$nom_arch = $this->facturacion->get("archivo");
		// echo $nom_arch;return;
		$res = $this->jfacturacion->get_estado($nom_arch);
		// print_r($res);return;
		if($res != "ok" && $res != "failed") {
			$datos = json_decode($res, true);
			$this->facturacion->set($datos);
			$this->facturacion->text_uppercase(false);
			$this->facturacion->update(null);
		}
		
		if($response === TRUE) {
			$this->response($this->facturacion->get_fields());
		}
	}
	
	public function generar($show_exception = TRUE) {
		$this->unlimit();
		
		$this->update(false);
		
		$this->load_model("venta.facturacion");
		$this->load->library('jfacturacion');
		
		$datos_fact = $this->facturacion->find($this->input->post());
		
		$estado = true;
		
		if($datos_fact["referencia"] == "venta") {
			$this->load_model("venta.venta");
			$v = $this->venta->find($datos_fact["idreferencia"]);
			$t = $v["subtotal"] + $v["igv"] - $v["descuento"];
			$estado = $this->is_valid_doc($v["idtipodocumento"],$v["serie"],$v["idcliente"],$t,$v["idmoneda"]);
		}
		else if($datos_fact["referencia"] == "notacredito" || $datos_fact["referencia"] == "notadebito") {
			$this->load_model("venta.notacredito");
			$v = $this->notacredito->find($datos_fact["idreferencia"]);
			$t = $v["subtotal"] + $v["igv"];
			$estado = $this->is_valid_doc_nota($v["idtipodocumento"],$v["serie"],$v["iddocumento_ref"],$v["serie_ref"],$v["idcliente"],$t,$v["idmoneda"]);
		}
		
		if($estado !== true) {
			if($show_exception === TRUE) {
				$this->exception($estado);
				return;
			}
		}
		
		if(empty($datos_fact["ind_situ"])) {
			$datos_fact["ind_situ"] = "01";
		}
		
		if(in_array($datos_fact["ind_situ"], array("01","06","07","10"))) {
			$datos = $this->jfacturacion->crear_files($datos_fact["referencia"], $datos_fact["idreferencia"]);
			if($datos !== FALSE) {
				$res = $this->jfacturacion->enviar_files($datos);
				if($res !== false) {
					if($res != "failed" && $res != "ok") {
						$arr = json_decode($res, true);
						$datos = array_merge($datos, $arr);
					}
					
					$this->facturacion->set($datos);
					$this->facturacion->text_uppercase(false);
					$this->facturacion->update(null);
				}
			}
			
			// se trata de un archivo (registro) nuevo o con error, creamos el comprobante
			$res = $this->jfacturacion->crear_comprobante($datos_fact["referencia"], $datos_fact["idreferencia"]);
			
			if($res !== FALSE) {
				if($res != "failed" && $res != "ok") {
					$datos = json_decode($res, true);
					$this->facturacion->set($datos);
					$this->facturacion->text_uppercase(false);
					$this->facturacion->update(null);
				}
			}
		}
		
		$this->response($this->facturacion->get_fields());
	}
	
	public function send() {
		$this->unlimit();
		
		$this->update(false);
		
		$this->load_model("venta.facturacion");
		
		$datos_fact = $this->facturacion->find($this->input->post());
		
		if($datos_fact["ind_situ"] == "02" || $datos_fact["ind_situ"] == "10") {
			$this->load->library('jfacturacion');
			
			$res = $this->jfacturacion->enviar_comprobante($datos_fact["referencia"], $datos_fact["idreferencia"]);
			if($res !== FALSE) {
				if($res != "failed" && $res != "ok") {
					$datos = json_decode($res, true);
					$this->facturacion->set($datos);
					$this->facturacion->text_uppercase(false);
					$this->facturacion->update(null);
				}
			}
		}
		
		$this->response($this->facturacion->get_fields());
	}
	
	public function baja() {
		$this->unlimit();
		
		$this->update(false);
		
		$this->load_model("venta.facturacion");
		
		$datos_fact = $this->facturacion->find($this->input->post());
		
		if($datos_fact["tip_docu"] == "RA") {
			$this->exception("Debe seleccionar el comprobante que desea dar de baja");
			return;
		}
		
		if( ! in_array($datos_fact["ind_situ"], array("03","04","05"))) {
			$this->exception("El comprobante aun no se envia a SUNAT, no hay razon para dar de baja.");
			return;
		}
		
		$sql = "SELECT * FROM venta.documento_baja WHERE idreferencia=? AND referencia=? and estado='A'";
		$query = $this->db->query($sql, array($datos_fact["idreferencia"], $datos_fact["referencia"]));
		if($query->num_rows() > 0) {
			$this->exception("La ".$this->getComprobante($datos_fact["tip_docu"])." ".$datos_fact["num_docu"]." ya se ha comunicado de baja.");
			return;
		}
		
		$current_date = date("Y-m-d");
		
		// obtenemos el maximo correlativo del dia
		$sql = "select max(correlativo) as corr from venta.documento_baja where fecha=? and estado='A'";
		$query = $this->db->query($sql, array($current_date));
		$correlativo = intval($query->row()->corr) + 1;
		
		// ingresamos nuevo registro
		$this->load_model("venta.documento_baja");
		$this->documento_baja->set($datos_fact);
		$this->documento_baja->set("idsucursal", $this->get_var_session("idsucursal"));
		$this->documento_baja->set("idusuario", $this->get_var_session("idusuario"));
		$this->documento_baja->set("fecha", $current_date);
		$this->documento_baja->set("correlativo", $correlativo);
		$this->documento_baja->set("motivo", $this->input->post("motivo"));
		$this->documento_baja->set("estado", "A");
		$this->documento_baja->text_uppercase(false);
		$id = $this->documento_baja->insert(null);
		
		// fix: limpiamos datos almacenados
		$this->facturacion->clear_fields();
		
		// enviamos el archivo a la carpeta DATA del facturador
		$this->send_to_facturador("documento_baja", $id, $this->get_var_session("idsucursal"));
		
		// verificamos el estado para generar el comprobante xml
		$datos_fact = $this->facturacion->find(array("idreferencia"=>$id, "referencia"=>"documento_baja"));
		if(in_array($datos_fact["ind_situ"], array("01","06","07","10"))) {
			$res = $this->jfacturacion->crear_comprobante("documento_baja", $id);
			if($res !== FALSE) {
				if($res != "failed" && $res != "ok") {
					$datos = json_decode($res, true);
					$this->facturacion->set($datos);
					$this->facturacion->text_uppercase(false);
					$this->facturacion->update(null);
				}
			}
		}
		
		/* // enviamos sunat el comprobante
		$datos_fact = $this->facturacion->find(array("idreferencia"=>$id, "referencia"=>"documento_baja"));
		if(in_array($datos_fact["ind_situ"], array("02","10"))) {
			$res = $this->jfacturacion->enviar_comprobante("documento_baja", $id);
			if($res !== FALSE) {
				if($res != "failed" && $res != "ok") {
					$datos = json_decode($res, true);
					$this->facturacion->set($datos);
					$this->facturacion->text_uppercase(false);
					$this->facturacion->update(null);
				}
			}
		} */
		
		$this->response($this->facturacion->get_fields());
	}
	
	public function buscar() {
		// buscamos registros que no esten enviados y aceptados
		$sql = "select * from venta.facturacion 
			where estado='A' and coalesce(ind_situ,'01') not in ('03','04')
			and coalesce(actualizado,0) <= ?
			order by actualizado, fecha desc limit 1";
		$query = $this->db->query($sql, array($this->limitUpdate));
		if($query->num_rows() > 0) {
			$row = $query->row();
			
			// actualizamos correlativo de actualizacion
			$sql = "update venta.facturacion set actualizado = actualizado + 1
				where idreferencia=? and referencia=?";
			$this->db->query($sql, array($row->idreferencia, $row->referencia));
			
			// actualizamos el estado del registros segun facturador sunat
			$_POST["idreferencia"] = $row->idreferencia;
			$_POST["referencia"] = $row->referencia;
			$this->update();
			return;
		}
		
		/* // comprobantes que falta generar el xml
		$sql = "select * from venta.facturacion where estado='A' and ind_situ='01' 
			order by referencia desc limit 1";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0) {
			$row = $query->row();
			$_POST["idreferencia"] = $row->idreferencia;
			$_POST["referencia"] = $row->referencia;
			// $this->generar(false);
			$this->update();
			return;
		}
		
		// comprobantes con xml generado
		$sql = "select * from venta.facturacion where estado='A' and ind_situ='02' 
			order by referencia desc limit 1";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0) {
			$row = $query->row();
			$_POST["idreferencia"] = $row->idreferencia;
			$_POST["referencia"] = $row->referencia;
			// $this->send();
			$this->update();
			return;
		}
		
		// comprobantes con errores
		$sql = "select * from venta.facturacion where estado='A' and ind_situ in ('06','07') 
			order by referencia desc limit 1";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0) {
			$row = $query->row();
			$_POST["idreferencia"] = $row->idreferencia;
			$_POST["referencia"] = $row->referencia;
			// $this->generar(false);
			$this->update();
			return;
		}
		
		// comprobantes rechazados por sunat
		$sql = "select * from venta.facturacion where estado='A' and ind_situ='10'
			order by referencia desc limit 1";
		$query = $this->db->query($sql);
		if($query->num_rows() > 0) {
			$row = $query->row();
			$_POST["idreferencia"] = $row->idreferencia;
			$_POST["referencia"] = $row->referencia;
			// $this->generar(false);
			$this->update();
			return;
		} */
		
		$this->response(array());
	}
}
?>