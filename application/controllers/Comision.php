<?php

include_once "Controller.php";

class Comision extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Modulo Comisiones");
		$this->set_subtitle("Administracion de comisiones");
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		// nada
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form() {
		$data["controller"] = $this->controller;
		
		$idsucursal = $this->get_var_session("idsucursal");
		$anio = date("Y");
		
		$this->load->library("combobox");
		
		// datos sucursal
		$this->load_model("seguridad.sucursal");
		$sucursal = $this->sucursal->find($idsucursal);
		
		// combo empresa
		$query = $this->db->select('idempresa, descripcion')
			->where("estado", "A")->order_by("descripcion", "asc")
			->get("seguridad.empresa");
		
		$this->combobox->setAttr("id", "idempresa");
		$this->combobox->setAttr("name", "idempresa");
		$this->combobox->setAttr("class", "form-control input-xs");
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption($sucursal["idempresa"]);
		
		$data["empresa"] = $this->combobox->getObject();
		
		// combo sucursal
		$query = $this->db->select('idsucursal, descripcion')
			->where("estado", "A")->where("idempresa", $sucursal["idempresa"])
			->order_by("descripcion", "asc")->get("seguridad.sucursal");
		
		$this->combobox->setAttr("id", "idsucursal");
		$this->combobox->setAttr("name", "idsucursal");
		$this->combobox->removeItems();
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption($sucursal["idsucursal"]);
		
		$data["sucursal"] = $this->combobox->getObject();
		
		// combo empleado
		$this->combobox->setSelectedOption(null);
		
		$this->combobox->setAttr("id", "idempleado");
		$this->combobox->setAttr("name", "idempleado");
		$this->combobox->removeItems();
		$this->get_empleados($idsucursal, false);
		
		$data["empleado"] = $this->combobox->getObject();
		
		// combo anio
		$this->combobox->setAttr("id", "anio");
		$this->combobox->setAttr("name", "anio");
		$this->combobox->removeItems();
		$this->get_anios($idsucursal, false);
		$this->combobox->setSelectedOption($anio);
		
		$data["anio"] = $this->combobox->getObject();
		
		// combo meses
		$this->combobox->removeItems();
		$this->combobox->setAttr("id", "mes");
		$this->combobox->setAttr("name", "mes");
		$meses = getMonthsName();
		foreach($meses as $k=>$v) {
			if($k == 0) {
				continue;
			}
			$this->combobox->addItem($k, strtoupper($v));
		}
		$this->combobox->setSelectedOption(intval(date("m")));
		$data['mes'] = $this->combobox->getObject();
		
		$this->css("plugins/chosen/chosen");
		$this->js("plugins/chosen/chosen.jquery");
		$this->js('form/'.$this->controller.'/index');
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return null;
	}
	
	public function index($tpl = "content_empty") {
		$data = array(
			"menu_title" => $this->menu_title
			,"menu_subtitle" => $this->menu_subtitle
			,"with_tabs" => $this->with_tabs
		);
		
		$content = "";
		if( ! empty($this->menu_content)) {
			$content = implode("\n", $this->menu_content);
		}
		else {
			$content = $this->form();
		}
		$data["content"] = $content;
		
		if($this->show_path) {
			$data['path'] = $this->get_path();
		}
		
		$str = $this->load->view($tpl, $data, true);
		$this->show($str);
	}
	
	public function config() {
		// datos sucursal
		$this->load_model("seguridad.sucursal");
		$sucursal = $this->sucursal->find($this->get_var_session("idsucursal"));
		
		$this->load->library('combobox');
		
		// combo empresa
		$query = $this->db->select('idempresa, descripcion')
			->where("estado", "A")->order_by("descripcion", "asc")
			->get("seguridad.empresa");
		
		$this->combobox->setAttr("id", "idempresa");
		$this->combobox->setAttr("name", "idempresa");
		$this->combobox->setAttr("class", "form-control input-sm");
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption($sucursal["idempresa"]);
		
		$data["empresa"] = $this->combobox->getObject();
		
		// combo sucursal
		$query = $this->db->select('idsucursal, descripcion')
			->where("estado", "A")->where("idempresa", $sucursal["idempresa"])
			->order_by("descripcion", "asc")->get("seguridad.sucursal");
		
		$this->combobox->setAttr("id", "idsucursal");
		$this->combobox->setAttr("name", "idsucursal");
		$this->combobox->removeItems();
		$this->combobox->addItem($query->result_array());
		$this->combobox->setSelectedOption($sucursal["idsucursal"]);
		
		$data["sucursal"] = $this->combobox->getObject();
		
		$this->combobox->setSelectedOption(null);
		
		// combo marca
		$query = $this->db->select('idmarca, descripcion')->where("estado", "A")
			->order_by("descripcion", "asc")->get("general.marca");
		
		$this->combobox->setAttr("id", "idmarca");
		$this->combobox->setAttr("name", "idmarca");
		$this->combobox->removeItems();
		$this->combobox->addItem($query->result_array());
		
		$data["marca"] = $this->combobox->getObject();
		
		// combo rango dias
		$this->combobox->setAttr("id", "idrango");
		$this->combobox->setAttr("name", "idrango");
		$this->combobox->removeItems();
		$this->get_rangos(false);
		$data["rangodias"] = $this->combobox->getObject();
		
		// formulario
		$data["controller"] = $this->controller;
		$form = $this->load->view($this->controller."/registrar", $data, true);
		
		$this->css("plugins/chosen/chosen");
		$this->js("plugins/chosen/chosen.jquery");
		$this->js('form/'.$this->controller.'/registrar');
		
		$this->set_title("Configurar datos comisi&oacute;n");
		$this->set_subtitle("");
		$this->set_content($form);
		$this->index("content");
	}
	
	public function get_rangos($response = TRUE) {
		$query = $this->db->select('dias_min, dias_max')->where("estado", "A")
			->order_by("dias_min", "asc")->get("comision.rango_comision");
		
		if( ! isset($this->combobox))
			$this->load->library('combobox');
		
		if($query->num_rows() > 0) {
			foreach($query->result_array() as $row) {
				$i = $row["dias_min"].";".$row["dias_max"];
				$l = $row["dias_min"]." - ";
				if($row["dias_max"] == -1)
					$l .= "mas";
				else
					$l .= $row["dias_max"];
				
				$this->combobox->addItem($i, $l);
			}
		}
		
		if($response === TRUE)
			$this->response($this->combobox->getAllItems());
	}
	
	public function guardar_rango() {
		$post = $this->input->post();
		if(empty($post["dias_max"]))
			$post["dias_max"] = -1;
		$post["estado"] = "A";
		
		$this->load_model("comision.rango_comision");
		$this->rango_comision->save($post, false);
		
		$this->response($this->rango_comision->get_fields());
	}
	
	public function eliminar_rango() {
		$str = $this->input->post("id");
		list($min, $max) = explode(";", $str);
		$data["dias_min"] = $min;
		$data["dias_max"] = $max;
		
		$this->load_model("comision.rango_comision");
		$this->rango_comision->delete($data);
		
		$this->response($this->rango_comision->get_fields());
	}
	
	public function guardar_parametros() {
		$post = $this->input->post();
		
		$datos["anio"] = (int) date("Y");
		$datos["mes"] = (int) date("m");
		$datos["idsucursal"] = $post["idsucursal"];
		
		$this->load_model("comision.param_comision");
		
		// eliminamos datos almacenados
		$this->param_comision->delete($datos);
		
		if( ! empty($post["datos"])) {
			foreach($post["datos"] as $val) {
				list($min, $max) = explode(";", $val["rango"]);
				
				$datos["idmarca"] = $val["idmarca"];
				$datos["comision"] = floatval($val["comision"]);
				$datos["dias_min"] = $min;
				$datos["dias_max"] = $max;
				
				$this->param_comision->insert($datos, false);
			}
		}
		
		$this->response($this->param_comision->get_fields());
	}
	
	// public function get_tabladet($idsucursal=FALSE, $anio = FALSE, $mes = FALSE, $idempleado =FALSE, $return = FALSE){
		// set_time_limit(0);
		// if(empty($idsucursal))
			// $idsucursal = $this->input->post("idsucursal");
		
		// if(empty($idempleado))
			// $idempleado = $this->input->post("idempleado");
		// if($anio === FALSE) {
			// $sql = "select max(anio) as anio from comision.param_comision where idsucursal=?";
			// $query = $this->db->query($sql, array($idsucursal));
			// $anio = $query->row()->anio;
		// }
		
		// if($mes === FALSE) {
			// $sql = "select max(mes) as mes from comision.param_comision where idsucursal=? and anio=?";
			// $query = $this->db->query($sql, array($idsucursal, $anio));
			// $mes = $query->row()->mes;
		// }
		
		// $sql = "SELECT cv.idsucursal
				// ,to_char(cv.fecha_venta,'DD/MM/YYYY') fecha_venta
				// ,cv.comprobante
				// ,cv.idrecibo_ingreso
				// ,COALESCE(cv.totventa,0) totventa
				// , cv.monto
				// ,to_char(cv.fecha_pago,'DD/MM/YYYY') fecha_pago
				// , cv.nrodias
				// ,u.nombres|| ' ' ||u.appat as vendedor
				// ,(sum(cv.comisionado))*100/cv.monto as porcentaje 
				// ,sum (cv.comisionado) as comision
				// ,cv.idvendedor 
				// FROM venta.comision_view cv
				// INNER JOIN seguridad.usuario u ON u.idusuario = cv.idvendedor
				// WHERE cv.idvendedor={$idempleado} AND cv.idsucursal = {$idsucursal} and cv.anio = {$anio} and cv.mes = {$mes}
				// GROUP BY cv.idsucursal, cv.fecha_venta ,cv.idrecibo_ingreso
						// , cv.comprobante,cv.totventa, cv.monto,cv.fecha_pago
						// , cv.nrodias,vendedor
						// ,cv.idvendedor 

				// ";

		// $qq = $this->db->query($sql);
		// $qq = $this->db->query($sql, array($idempleado,$idsucursal,$anio,$mes));
		// if($return === TRUE)
			// return $query->result_array();
		
		// $res["detalle_pagos"] = $query->result_array();
		
		// $this->response($query->result_array());		
	// }
	
	public function get_tabladet($ajax=true){
		set_time_limit(0);
		$post = $_REQUEST;
		$sql = "SELECT a.idsucursal
				, to_char(a.fecha_venta,'DD/MM/YYYY') fecha_venta
				, a.comprobante
				, a.idrecibo_ingreso
				, a.monto
				, a.totventa
				, to_char(a.fecha_pago,'DD/MM/YYYY') fecha_pago 
				, a.nrodias
				, a.idvendedor
				, sum(a.monto*pc.comision)/100 comisionado
				, sum(a.monto::double precision * pc.comision / 100) AS comisionado
				, pc.comision
				, (pc.dias_min || '-'::text) || pc.dias_max AS rango
				from venta.comision_view  a 
				INNER JOIN comision.param_comision pc ON pc.idmarca = a.idmarca AND pc.anio = {$post['anio']} 
				 AND pc.mes = {$post['mes']}
				where a.idvendedor = {$post['idempleado']} and a.idsucursal = {$post['idsucursal']}
					and (a.nrodias) >= pc.dias_min AND (a.nrodias) <= pc.dias_max
				group by a.idsucursal
				  , a.idventa
				  , a.comprobante
				  , a.idvendedor
				  , a.totventa
				  , a.monto
				  , a.idamortizacion
				  , a.idrecibo_ingreso
				  , a.fecha_pago
				  , a.fecha_venta
				  , pc.comision
				  , a.nrodias
				  , pc.dias_min 
				  ,pc.dias_max

				";
		$query = $this->db->query($sql);
		
		if($ajax)
			$this->response($query->result_array());
		else
			return $query->result_array();
	}
	
	public function get_parametros($idsucursal, $anio = FALSE, $mes = FALSE, $return = FALSE) {
		// $idsucursal = $this->input->post("idsucursal");
		
		// obtenemos ultimos mes configurado
		if($anio === FALSE) {
			$sql = "select max(anio) as anio from comision.param_comision where idsucursal=?";
			$query = $this->db->query($sql, array($idsucursal));
			$anio = $query->row()->anio;
		}
		
		if($mes === FALSE) {
			$sql = "select max(mes) as mes from comision.param_comision where idsucursal=? and anio=?";
			$query = $this->db->query($sql, array($idsucursal, $anio));
			$mes = $query->row()->mes;
		}
		
		// obtenemos los datos de comision configurado
		$sql = "select pc.*, m.descripcion as marca
			from comision.param_comision pc
			join general.marca m on m.idmarca = pc.idmarca
			where pc.idsucursal = ? and pc.anio = ? and pc.mes = ?
			order by pc.anio desc, pc.mes desc, pc.dias_min asc, marca";
		$query = $this->db->query($sql, array($idsucursal, $anio, $mes));
		
		if($return === TRUE)
			return $query->result_array();
		
		$this->response($query->result_array());


	}
	
	public function get_anios($idsucursal = null, $response = TRUE) {
		$anio = (int) date("Y");
		if($idsucursal == null)
			$idsucursal = $this->input->post("idsucursal");
		if($idsucursal == null)
			$idsucursal = $this->get_var_session("idsucursal");
		
		$sql = "select distinct on (anio) anio, anio as descripcion 
			from comision.mes_comision where estado='A' and anio<>? and idsucursal=?
			order by anio desc";
		$query = $this->db->query($sql, array($anio, $idsucursal));
		
		if( ! isset($this->combobox))
			$this->load->library('combobox');
		
		$this->combobox->addItem($anio, $anio);
		$this->combobox->addItem($query->result_array());
		
		if($response === TRUE)
			$this->response($this->combobox->getAllItems());
	}
	
	public function get_empleados($idsucursal = null, $response = TRUE) {
		if($idsucursal == null)
			$idsucursal = $this->input->post("idsucursal");
		if($idsucursal == null)
			$idsucursal = $this->get_var_session("idsucursal");
		
		$sql = "select distinct u.idusuario, u.nombres||' '||u.appat||' '||u.apmat as nombres
			from seguridad.usuario u 
			join seguridad.acceso_empresa a on a.idusuario=u.idusuario and a.idsucursal=?
			where u.estado='A'";
		$query = $this->db->query($sql, array($idsucursal));
		
		if( ! isset($this->combobox))
			$this->load->library('combobox');
		
		$this->combobox->addItem($query->result_array());
		
		if($response === TRUE)
			$this->response($this->combobox->getAllItems());
	}
	

	public function get_estado() {
		set_time_limit(0);
		$post = $this->input->post();
		
		$this->load_model("comision.mes_comision");
		$mes = $this->mes_comision->find($post);
		
		$bconfig = true;
		if( ! empty($mes))
			$bconfig = ($mes["abierto"] == "S");
		
		$datos = $this->get_parametros($post["idsucursal"], $post["anio"], $post["mes"], true);
		
		$html = '';
		if(empty($datos)) {
			if($bconfig) {
				$html = '<div class="alert alert-danger">Falta configurar los datos para el c&aacute;lculo de la comisi&oacute;n. '.
					'<a class="btn btn-primary btn-xs" href="'.base_url("comision/config").'">Configure aqui</a></div>';
			}
			else {
				$html = '<div class="alert alert-danger">No existen datos para el c&aacute;lculo de la comisi&oacute;n.</div>';
			}
		}
		else {
			$cols = array("marca"=>"Marca");
			$rows = array();
			foreach($datos as $row) {
				$l = ($row["dias_max"] == -1) ? "mas" : $row["dias_max"];
				$kp = "p" . $row["dias_min"] . $l;
				if( ! array_key_exists($kp, $cols) ) {
					$cols[$kp] = $row["dias_min"] ." - ".$l;
				}
				
				$km = "m" . $row["idmarca"];
				$data = array("marca" => $row["marca"]);
				if(array_key_exists($km, $rows)) {
					$data = $rows[$km];
				}
				$data[$kp] = $row["comision"];
				$rows[$km] = $data;
			}
			
			// la tabla
			$html = '<div class="row"><div class="col-md-12"><table class="table table-bordered detail-table no-header-background"><thead><tr>';
			foreach($cols as $col) {
				$html .= '<th>'.$col.'</th>';
			}
			$html .= '</tr></thead><tbody>';
			foreach($rows as $row) {
				$html .= '<tr>';
				foreach($cols as $k=>$col) {
					$html .= '<td>'.$row[$k].'</td>';
				}
				$html .= '</tr>';
			}
			$html .= '</tbody></table></div></div>';
			
			if($bconfig) {
				$html .= '<div class="row"><div class="col-md-12"><a class="btn btn-info btn-xs" href="'.base_url("comision/config").'">Modificar datos</a></div></div>';
			}
		}
		
		$res["mes"] = $mes;
		$res["tabla"] = $html;
		$res["info"] = '';
		if($bconfig == false) {
			$res["info"] = '<div class="alert alert-danger">El mes que esta consultando ya se ha cerrado.</div>';
		}
		
		$this->response($res);
	}
	
	public function save_comision(){
		$post = $this->input->post();
		
		if(!empty($post['idvendedor'])){
			foreach($post['idvendedor'] as $k=>$v){
				$this->load_model("comision.pago_comision");
				$this->pago_comision->set("idvendedor",$v);
				// $this->pago_comision->fecha_venta = $post['fecha_venta'][$k];
				// $model->fecha_venta = $post['fecha_venta'][$k];
				$this->pago_comision->insert();
			}
		}
	}
}
