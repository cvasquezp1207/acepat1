<?php
include_once "Controller.php";

class Cuentas_pagar extends Controller {
	public function init_controller() {
		$this->set_title("Cuentas por Cobrar");
		$this->set_subtitle("Lista de Accesos");
		$this->js('form/'.$this->controller.'/index');
	}
	
	public function end_controller() {

	}
	
	public function form() {
		$data["controller"] = $this->controller;
		
		$this->load->library('combobox');
		// combo moneda
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idmoneda"
				,"name"=>"idmoneda"
				,"class"=>"form-control input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idmoneda, descripcion')->where("estado", "A")->get("general.moneda");
		$this->combobox->addItem($query->result_array());
		$data["moneda"] = $this->combobox->getObject();
		// combo moneda
		
		// combo moneda
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>"idmoneda_deuda"
				,"name"=>"idmoneda_deuda"
				,"class"=>"form-control input-xs"
				,"required"=>""
			)
		);
		$query = $this->db->select('idmoneda, descripcion')->where("estado", "A")->get("general.moneda");
		$this->combobox->addItem($query->result_array());
		$data["moneda_deuda"] = $this->combobox->getObject();
		// combo moneda
		
		$data["modal_pago"] = $this->get_form_pago("pagoproveedor", false);
		
		$this->css("plugins/awesome-bootstrap-checkbox/awesome-bootstrap-checkbox");
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
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
	
	public function proveedor_deuda() {
		$post = $this->input->post();
		$q = $post["q"].'%';
		
		$pagado='N';
		if(isset($post['pagado']))
			if($post['pagado']=='S')
				$pagado='S';
		
		if($post["f"] == "R") {
			$sql = "SELECT idproveedor, ruc as value, 
					ruc||' | '||coalesce(nombre,'') as label
					FROM compra.proveedor WHERE ruc like ? AND idproveedor IN (
						SELECT DISTINCT idproveedor 
						FROM compra.deuda 
						WHERE pagado='$pagado' 
						AND estado='A' 
						--AND idsucursal='{$this->get_var_session('idsucursal')}'
					) ORDER BY label LIMIT ?";
		}
		else if($post["f"] == "N") {
			$sql = "SELECT idproveedor, 
					coalesce(nombre,'') as label,
					coalesce(nombre,'') as value
					FROM compra.proveedor 
					WHERE coalesce(nombre,'') ilike ?
					AND idproveedor IN (
						SELECT DISTINCT idproveedor 
						FROM compra.deuda 
						WHERE pagado='$pagado' 
						AND estado='A' 
						--AND idsucursal='{$this->get_var_session('idsucursal')}'
					) ORDER BY label LIMIT ?";
		}
		
		$query = $this->db->query($sql, array($q, $post["m"]));
		$this->response($query->result_array());
	}
	
	public function get_credito(){
		$post = $this->input->post();
		
		$this->load_model(array("compra.deuda_view","compra.proveedor"));
		$deuda = $this->deuda_view->find(array("idproveedor"=>$post['idproveedor'],"pagado"=>$post['pagado'],"idmoneda"=>$post['idmoneda'],"estado"=>'A'));
		$prove = $this->proveedor->find(array("idproveedor"=>$post['idproveedor']));
	
		$res['deuda']		= $deuda;
		$res['proveedor']	= $prove;
		
		$this->response($res);
	}
	
	public function get_comprobante(){
		$post = $this->input->post();
		
		$this->load_model(array("compra.compra_deuda_view","compra.deuda_view"));
		$deuda		= $this->deuda_view->find(array("iddeuda"=>$post['iddeuda'],"pagado"=>$post['pagado']));
		$compras	= $this->compra_deuda_view->find(array("iddeuda"=>$post['iddeuda'],"pagado"=>$post['pagado']));
		
		$res["deuda"]		=	$deuda;
		$res["comprobante"]	=	$compras;

		$this->response($res);
	}
	
	public function get_comprobante_multiple(){
		$post = $this->input->post();
		
		if(!isset($post['id_creditos']))
			$post['id_creditos'] = array(0);
		else if(empty($post['id_creditos']))
			$post['id_creditos'] = array(0);
		
		$q = $this->db->query("	SELECT idcompra, comprobante FROM compra.compra_deuda_view 
								WHERE iddeuda IN ('".implode("','", $post['id_creditos'])."') 
								AND idmoneda='{$post['idmoneda_deuda']}'
								ORDER BY comprobante");
		$compras = $q->result_array();
		
		$q = $this->db->query("	SELECT SUM(monto_pendiente) monto_pendiente,idmoneda,MIN(valor_cambio) valor_cambio
								FROM compra.deuda_view WHERE iddeuda IN ('".implode("','", $post['id_creditos'])."') 
								AND idmoneda='{$post['idmoneda_deuda']}'
								AND estado='A'
								GROUP BY idmoneda;");
		$deuda = $q->row_array();

		$res["comprobante"]	=	$compras;
		$res["deuda"]		=	$deuda;

		$this->response($res);
	}
	
	public function get_letras(){
		$post = $this->input->post();
		
		$pagado='N';
		if(isset($post['pagado'])){
			if($post['pagado']=='S')
				$pagado='S';
		}
		if(!isset($post['id_creditos']))
			$post['id_creditos'] = array(0);
		else if(empty($post['id_creditos']))
			$post['id_creditos'] = array(0);
		$q = $this->db->query("	SELECT l.*
								,d.moneda_corto moneda
								,COALESCE(tp.descripcion,'') tipopago 
								,to_char(l.fecha_vencimiento,'DD/MM/YYYY') fecha_venc
								,to_char(fecha_deuda,'DD/MM/YYYY') fecha_credito
								,COALESCE(to_char(fecha_cancelado,'DD/MM/YYYY'),'') fecha_pago
								,comprobante
								,d.nro_credito
								,CASE WHEN l.idletra=qq.id_letra THEN true ELSE false END last_pago
								FROM compra.letra l
								JOIN compra.deuda_view d ON d.iddeuda=l.iddeuda
								LEFT JOIN venta.tipopago tp ON tp.idtipopago=l.idtipo_pago
								LEFT JOIN(
								SELECT MAX(idletra) id_letra, iddeuda idcredito FROM compra.letra ll WHERE ll.estado='A' AND ll.pagado='S' AND pagado='S' GROUP BY idcredito
								) qq ON qq.idcredito=l.iddeuda
								WHERE l.estado='A' 
								AND l.iddeuda IN ('".implode("','", $post['id_creditos'])."') 
								AND d.pagado='{$pagado}'
								AND d.idmoneda='{$post['idmoneda_deuda']}'
								ORDER BY l.nro_letra, l.fecha_vencimiento");
		
		$this->response($q->result_array());
	}
	
	public function guardar_pago(){
		$post = $this->input->post();
		$this->load_model(array("compra.deuda","compra.letra"));
		
		if(!isset($post['idletra']))
			$post['idletra'] = array();
		
		$post["serie"]			=	$this->get_var_session('idsucursal').date("Ymd");
		$post["numero"]			=	$this->get_var_session('idsucursal').date("His");
		$post["idsucursal"]		=	$this->get_var_session('idsucursal');
		$post["idusuario"]		=	$this->get_var_session('idusuario');
		
		
		if(!isset($post['idletra'])){
			$post['idletra'] = array();
		}
		$post["id_referencia"] 	= $post['idproveedor'];
		foreach($post['id_creditos'] as $k=>$v){
			if($k==0)
				$post["id_referencia"].=$v;
		}
		
		foreach($post['idletra'] as $k=>$v){
			if($k==0)
				$post["id_referencia"].=$v;
		}
		$post["id_referencia"].=date("s");
		$this->db->trans_start(); // inciamos transaccion
		foreach($post['idletra'] as $k=>$v){
			
			$datoletra["idletra"]			=	$v;
			$datoletra["iddeuda"]			=	$post['id_deuda_letra'][$k];
			$datoletra["idusuario"]			=	$post["idusuario"];
			$datoletra["idtipo_pago"]		=	$post['idtipopago'];
			$datoletra["fecha_vencimiento"]	=	$post['fecha_vencimiento'][$k];
			$datoletra["nro_dias_formapago"]=	(!empty($post["nro_dias_formapago"][$k]))?$post['nro_dias_formapago'][$k]:0;
			$datoletra["fecha_cancelado"]	=	(!empty($post["fecha_deposito"])) ? $post["fecha_deposito"]: date("Y-m-d");
			$datoletra["hora_pago"]			=	date("H:i:s");
			$datoletra["pagado"]			=	'S';
			$datoletra["serie"]				=	$post["serie"];
			$datoletra["numero"]			=	$post["numero"];
			$datoletra["idsucursal_pago"]	=	$post["idsucursal"];
			$datoletra["id_referencia"]		=	$post["id_referencia"];
			
			$has = $this->letra->find(array("iddeuda"=>$post['id_deuda_letra'][$k],"idletra"=>$v));
			
			if($has){
				$this->letra->update($datoletra);
			}
		}
		
		// $this->db->query("UPDATE compra.letra SET id_referencia='{$post["id_referencia"]}' WHERE iddeuda='{$post['iddeuda']}' AND idletra IN ('".implode("','", $post['idletra'])."')");
		foreach($post['id_creditos'] as $k=>$v){
			$datodeuda = $this->deuda->find($v);
			$cant_let  = $this->deuda->get("cant_letras");

			$q = $this->db->query("SELECT count(*) c FROM compra.letra WHERE iddeuda='{$v}' AND pagado='S' AND estado='A';");
			$cant_pdo = $q->row()->c;//Cantidad letras pagadas
			if($cant_let==$cant_pdo){
				$datodeuda["pagado"] = "S";
				$this->deuda->update($datodeuda);
			}
		}
		
		
		// controlador de caja
		$this->load_controller("caja");
		// libreria para procesar el pago
		$monto_pago= $post["monto_pagar"];
		$id_moneda = $post["idmoneda"];
		unset($post["idmoneda"]);
		
		$post["idoperacion"]	= $post['id_referencia'];
		$post["idcliente"] 		= $post['idproveedor'];
		$post["tabla"] 			= "compra.letra";
		$post["descripcion"]	= "PAGO A PROVEEDOR";
		$post["referencia"]		= $post['referencia'];
		$post["idmoneda"]		= $post["id_moneda_cambio"]?$post["id_moneda_cambio"]:$id_moneda;
		$post["monto_convertido_pay"]	= $post["total_acumulado"]?$post["total_acumulado"]:$monto_pago;//Monto para guardar en Deposito
		
		$this->load->library('pay');
		$this->pay->set_controller($this->caja_controller);
		$this->pay->set_data($post);
		$this->pay->entrada(false);
		$this->pay->process();
		$this->db->trans_complete(); // finalizamos transaccion
		
		$this->response($post);
	}
	
	public function delete_pago(){
		$post = $this->input->post();
		$post['idsucursal_pago'] = $this->get_var_session('idsucursal');
		$this->load_model(array("compra.deuda","compra.letra"));
		
		$this->db->trans_start(); // inciamos transaccion
		// $arr = $this->letra->find(array("iddeuda"=>$post['iddeuda'],"idsucursal_pago"=>$post['idsucursal_pago'],"serie"=>$post['serie'],"numero"=>$post['numero']));
		$query = $this->db->query("SELECT * FROM compra.letra WHERE estado='A' AND idsucursal_pago='{$post['idsucursal_pago']}' AND serie='{$post['serie']}' AND numero='{$post['numero']}';");
		
		$res=$query->result_array();
		$id_referencia = 0;
		$deudas = array();
		if(count($res)>0){
			$id_referencia = $res[0]['id_referencia'];
			foreach($res as $k=>$dataletra){
				$deudas[] = $dataletra["iddeuda"];
				
				$dataletra['idusuario']			= null;
				$dataletra['idtipo_pago']		= null;
				$dataletra['fecha_cancelado']	= null;
				$dataletra['hora_pago']			= null;
				$dataletra['pagado']			= 'N';
				$dataletra['serie']				= null;
				$dataletra['numero']			= null;
				$dataletra['idsucursal_pago']	= null;
				$dataletra['id_referencia']		= null;
				$this->letra->update($dataletra);				
			}
		}
		
		foreach($deudas as $v){
			$datodeuda = $this->deuda->find($v);
			$datodeuda["pagado"] = "N";
			$this->deuda->update($datodeuda);			
		}
		
		
		/*	si la caja esta abierta, eliminamos el registro nomas, 
			de lo contrario que hagan un recibo de egreso, si afecta caja
		*/
		$this->load_library('pay');
		$this->pay->remove_if_open("compra.letra", $id_referencia, $this->get_var_session('idsucursal'));
		
		$this->db->trans_complete(); // finalizamos transaccion
		$this->response($post);
	}
}
?>