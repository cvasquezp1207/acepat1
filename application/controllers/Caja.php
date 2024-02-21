<?php

include_once "Controller.php";

class Caja extends Controller {
	protected $current_caja = true;
	public $idusuario = null;
	public $idsucursal = null;

	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("");
		//$this->set_subtitle("Lista de Caja");
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
	public function form($data = null) {
		if(!is_array($data)) {
			$data = array();
		}
		$this->set_subtitle("Lista de Caja");
		$data["controller"] = $this->controller;
		$data["grilla"] = $this->grid();
		$data["monedas"] = $this->monedas();
		$data["cierre_temp"] = $this->cierre_temp($this->get_var_session("idusuario"),$this->get_var_session("idsucursal"));
		$data["codcaja"] = $this->cajita($this->get_var_session("idusuario"), $this->get_var_session("idsucursal"));
		$data["denominacion"] = $this->denominacion();
		$data["prefix"] = "";
		$data["caja_pasada"] = $this->caja_anterior(true);
		$data["cierrecaja"] = $this->data_cierre($this->get_var_session("idusuario"));
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */

	public function grid(){
		$contenido = array();
		$rolcobrador = $this->get_param('idrol_cajero')?$this->get_param('idrol_cajero'):0;
		$es_cajero 		= $this->extrac_rol_user($this->get_var_session("idusuario"),$this->get_var_session("idsucursal"),$rolcobrador);

		if($es_cajero=='I'){
			return "<span style='color:orange;font-size:50px;'>Hey..!!</span> <span style='font-size:38px;'>Usted no tiene el rol de cajero en esta sucursal...<i class='fa fa-meh-o'></i></span><br><span style='font-size:30px;'>Solicite al ADMINISTRADOR la asignacion de rol cajero</span>";
		}
		$row = $this->get_permisos();
		$this->load_model('detallecaja_view');
		$this->load_model('caja');

		$this->load->library('datatables');

		//////////////////////////////////////
		$data = $this->getCajaAnterior($this->get_var_session("idusuario"), $this->get_var_session("idsucursal"));
			if($data !== false) {
				$fecha = explode(' ', $data[0]['fecha_apertura']);
				$date = new DateTime(array_shift($fecha));
				
				$txt = '<span style="margin-right:10px;font-size:1.4em;color:#099517;"><b style="color:#E15252;">Caja Actual</b>: '.
					getDaysName($date->format("N")).", ".$date->format("d").
					" de ".getMonthsName($date->format("m"))." de ".$date->format("Y").'</span>';

				$this->add_button_content(null,$txt,null,$txt,'white',array('display'=>'inline-block'));

				if($data[0]['abierto'] == 'S') {
					$this->add_button("button-cerrar", "Cerrar Caja",null,'primary');
				}
				else if($data[0]['tienearqueo'] == 'N') {
					$this->add_button("button-arqueo", "Arqueo caja",null,'primary');
				}
			}else {
				$data = $this->getCajaHoy($this->get_var_session("idusuario"), $this->get_var_session("idsucursal"));
				if($data === false) {
						$this->add_button("button-abrir", "Abrir caja",null,'primary');
				}else {
					$fecha = explode(' ', $data[0]['fecha_apertura']);
					$date = new DateTime(array_shift($fecha));
						
					$txt = '<span style="margin-right:10px;font-size:1.4em;color:#099517;"><b style="color:#E15252;">Caja Actual</b>: '.
							getDaysName($date->format("N")).", ".$date->format("d").
							" de ".getMonthsName($date->format("m"))." de ".$date->format("Y").'</span>';
				
					$this->add_button_content(null,$txt,null,$txt,'white',array('display'=>'inline-block'));
					
					if($data[0]['abierto'] == 'S') {
						$this->add_button("button-cerrar", "Cerrar caja",null,'primary');
					}else if($data[0]['tienearqueo'] == 'N') {

						$this->add_button("button-reabrir", "Reaperturar caja",null,'primary');
						$this->add_button("button-arqueo", "Arqueo caja",null,'primary');
					}else {
						$this->add_button_content(null,'<h1 style="text-align: center; color: #a94442;font-size:22px;">Se ha cerrado la caja. Por favor regrese ma&ntilde;ana</h1>',null,$txt,'white',array('display'=>'inline-block'));
					}
				}
			}


		$codcaja = ($data !== false) ? $data[0]['idcaja'] : '-1';
		//////////////////////////////////////
		
		$this->datatables->setModel($this->detallecaja_view);

		$this->datatables->setIndexColumn("iddetalle_caja");

		$this->datatables->where('idcaja', '=', $codcaja);
		$this->datatables->where('idsucursal', '=', $this->get_var_session("idsucursal"));

		$this->datatables->setColumns(array('fecha','hora','tipo','descripcion','referencia','monto','saldo'));

		// columnas de la tabla, si no se envia este parametro, se muestra el
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			array('Fecha', '9%')
			,array('Hora', '6%')
			,array('Tipo', '9%')
			,array('Descripci&oacute;n', '30%')
			,array('Referencia', '30%')
			,array('Monto', '9%')
			,array('Saldo', '12%')
		);

		$this->datatables->setCallback('callbackCaja');

		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName);

		$script = "<script>".$this->datatables->createScript()."</script>";

		// agregamos los css para el dataTables
		$this->css('plugins/dataTables/dataTables.bootstrap');
		$this->css('plugins/dataTables/dataTables.responsive');
		$this->css('plugins/dataTables/dataTables.tableTools.min');

		// agregamos los scripts para el dataTables
		$this->js('plugins/dataTables/jquery.dataTables');
		$this->js('plugins/dataTables/dataTables.bootstrap');
		$this->js('plugins/dataTables/dataTables.responsive');
		$this->js('plugins/dataTables/dataTables.tableTools.min');		

		$this->js($script, false);

		return $table;
	}

	public function grilla() {
		return $this->form();
	}
	
	public function getCajaAnterior($idusuario, $idsucursal) {
		$idsucursal = (empty($idsucursal)) ? $this->get_var_session("idsucursal") : $idsucursal;
		$sql = "SELECT * FROM caja.caja
				WHERE estado = 'A' 
				AND (abierto = 'S' OR tienearqueo = 'N')
				AND fecha_apertura::date < current_date
				AND idusuario_apertura = '$idusuario'
				AND idsucursal = '$idsucursal'
				ORDER BY  fecha_apertura
				limit 1";
		$query = $this->db->query($sql);

		if ( $query->num_rows() > 0 ) {
			return $query->result_array();
		}
		
		return false;
	}

	public function cajita($idusuario, $idsucursal=''){
		$idsucursal = (empty($idsucursal)) ? $this->get_var_session("idsucursal") : $idsucursal;
		$data = $this->getCajaAnterior($idusuario, $idsucursal);
		if($data === false)
			$data = $this->getCajaHoy($idusuario, $idsucursal);

		return $codcaja = ($data !== false) ? $data[0]['idcaja'] : '0';
	}
	
	public function getCajaHoy($codusuario='', $codsucursal = '') {
		$codusuario = (empty($codusuario)) ? $this->get_var_session("idusuario") : $codusuario;
		$codsucursal = (empty($codsucursal)) ? $this->get_var_session("idsucursal") : $codsucursal;

		if($this->current_caja) {
			$sql = "SELECT * FROM caja.caja
					WHERE estado = 'A' 
					AND idusuario_apertura = '$codusuario'
					AND fecha_apertura::date =CURRENT_DATE
					AND idsucursal='$codsucursal'
					ORDER BY  fecha_apertura
					limit 1";

		}else{
			$sql = "SELECT * FROM caja.caja WHERE estado = 'A' AND abierto = 'S' AND tienearqueo = 'N' 
					AND idusuario_apertura = '".$codusuario."' AND idsucursal='$$idsucursal' 
					ORDER BY fecha_apertura ASC LIMIT 1";			
		}

		$query = $this->db->query($sql);

		if($query->num_rows() > 0) {
			$arr = $query->result_array();
			$fecha = explode(' ', $arr[0]['fecha_apertura']);
			$this->fecha_caja = $fecha[0]; // yyyy-mm-dd
			return $arr;
		}else {
			return false;
		}
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */

	public function monedas(){
		/*$this->load_controller("reportecaja");
		return $this->reportecaja_controller->moneda();*/
		$sql = "SELECT*FROM general.moneda WHERE estado='A' ORDER BY idmoneda";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function save(){
		$idusuario = $this->get_var_session("idusuario");
		$this->load_model($this->controller);
		if($this->existsCajaHoy($idusuario)) {
			$this->exception("La caja para el dia de hoy ya fue abierto");
		}else {
			$fields = $this->input->post();
			$fields['idusuario_apertura'] = $this->get_var_session("idusuario");
			$fields['fecha_apertura'] = date("Y-m-d");
			$fields['idsucursal'] = $this->get_var_session("idsucursal");
			$fields['abierto'] = "S";
			$fields['tienearqueo'] = "N" ;
			$fields['estado'] = "A";
			
			$this->db->trans_start(); // inciamos transaccion
			
			$idcaja = $this->caja->insert($fields);
			if($idcaja) {
				$this->load_model("aperturacaja");
				$this->load_model("cierrecaja");
				$data1["idcaja"] = $idcaja;
				$data1["estado"] = 'A';
				
				$data3["idcaja"] = $idcaja;
				$data3["estado"] = 'A';

				foreach($fields["idmoneda"] as $key=>$val) {
					$data1["idmoneda"] = $val;
					$data1["monto"] = (!empty($fields["monto"][$key])) ? $fields["monto"][$key] : 0;
					$data1["tipocambio"] = $fields["tipocambio"][$key];
					$data1["montoconvertido"] = intval($data1["tipocambio"]*$data1["monto"]);
					$data1["idsucursal"]		= $this->get_var_session("idsucursal");
					$estado_apertura = $this->aperturacaja->insert($data1);
					
					$data3["idmoneda"] 			= $val;
					$data3["monto"] 			= 0;
					$data3["tipocambio"] 		= $fields["tipocambio"][$key];
					$data3["montoconvertido"]	= 0;
					$data3["idsucursal"]		= $this->get_var_session("idsucursal");
					$estado_cierre = $this->cierrecaja->insert($data3);

					if ($estado_apertura && $estado_cierre && $data1["monto"]>0) {
						$this->load_model("detalle_caja");

						$data2["idcaja"]				=  $idcaja;
						$data2["fecha"] 				=  date("Y-m-d");
						$data2["hora"] 					=  date("H:m:s");
						$data2["idconceptomovimiento"] 	= '1';//APERTURA
						$data2["monto"] 				=  $data1["monto"];
						$data2["tabla"] 				=  "A";
						$data2["descripcion"] 			= 	"MONTO DE APERTURA DE CAJA ".$fields["denominacion"][$key];
						$data2["idusuario"] 			=  $fields['idusuario_apertura'];
						$data2["idmoneda"] 				=  $data1["idmoneda"];
						$data2["idtipopago"]			=  '1';// EFECTIVO
						//$data2["idtipodocumento"] 		=  '';// FACTURA, BOLETA
						//$data2["idcliente"] 			=  '';// 
						$data2["serie"] 				=  '';// 
						$data2["numero"] 				=  '';// 
						$data2["tipocambio"] 			=  $data1["tipocambio"];// 
						$data2["montoconvertido"] 		=  $data1["montoconvertido"];// 
						$data2["referencia"] 			=   $this->get_var_session("nombres").' '.$this->get_var_session("appat").' '.$this->get_var_session("apmat");// 
						$data2["estado"] 				=  "A";// 
						$data2["idsucursal"] 			=   $this->get_var_session("idsucursal");//

						$this->detalle_caja->insert($data2);
					}
				}
			}
			
			$this->db->trans_complete(); // finalizamos transaccion
			
			$this->response($fields);
		}
	}
	
	public function existsCajaHoy($idusuario, $idsucursal='') {
		$idsucursal = (empty($idsucursal)) ? $this->get_var_session("idsucursal") : $idsucursal;
		$sql = "SELECT * FROM caja.caja
				WHERE estado = 'A' 
				AND fecha_apertura::date = current_date
				AND idusuario_apertura = '$idusuario' 
				AND idsucursal='$idsucursal'
				";
		$query = $this->db->query($sql);

		return ( $query->num_rows() > 0 );
	}

	public function cerrar_caja(){
		$this->load_model($this->controller);
		$fields = $this->input->post();
		$this->caja->find($fields['idcaja']);
		
		$fields['idusuario_cierre'] = $this->get_var_session("idusuario");
		$fields['fecha_cierre'] = date('Y-m-d H:i:s');
		$fields['abierto'] = "N";

		$this->caja->update($fields);

		
		$this->response($this->caja->get_fields());
	}

	public function reaperturar_caja(){
		$this->load_model($this->controller);

		$data = $this->getCajaHoy();
		if($data !== false) {
			if($data[0]['abierto'] != 'S') {

				$fields = $this->input->post();
				$this->caja->find($fields['idcaja']);

				$fields['abierto'] = "S";
				$this->caja->update($fields);

				$this->response($this->caja->get_fields());
			}else {
				$this->exception('La caja ya se encuentra abierto');
			}
		}else {
			$this->exception('No existe ninguna caja cerrada');
		}
	}
	
	public function arqueo_caja(){
		$this->load_model($this->controller);
		
		$fields = $this->input->post();
		$fields1 = $this->input->post();
		
		$this->caja->find($fields['idcaja']);
		
		$fields['tienearqueo'] = "S";
		$this->caja->update($fields);

		$this->load_model('arqueo_caja');
		$fields1['estado'] 	= "A";
		$fields1['fecha'] 	= date('Y-m-d H:i:s');
		$fields1['idusuario'] = $this->get_var_session("idusuario");
		$idarqueo_caja = $this->arqueo_caja->insert($fields1);
		
		$data1['idarqueo_caja'] = $idarqueo_caja;
		if($idarqueo_caja){
			$this->load_model('detalle_arqueo');
			foreach($fields["iddenominacion"] as $key=>$val) {
				$data1["iddenominacion"] = $val;
				
				$data1["valor_billete"] = (!empty($fields["billete"][$key])) ? $fields["billete"][$key] : 0;
				// $data1["tipocambio"] = $fields["tipocambio"][$key];
				$data1["estado"] 				=  "A";// 
				$this->detalle_arqueo->insert($data1);
			}			
		}
		
		$this->response($this->arqueo_caja->get_fields());
	}
	
	public function data_cierre($idusuario, $idmoneda=false){
		$sql = "SELECT * FROM caja.cierrecaja WHERE idcaja = '".$this->cajita($idusuario)."' AND estado='A' ";
		if($idmoneda!==FALSE)
			$sql.=" AND idmoneda='$idmoneda';";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function cierre_temp($idusuario, $idsucursal){
		$sql = "SELECT
				SUM(a.monto) monto_apertura
				,(SELECT SUM(ci.monto) FROM caja.cierrecaja ci WHERE ci.idcaja=c.idcaja AND ci.idmoneda=a.idmoneda AND ci.idcaja = {$this->cajita($idusuario,$idsucursal)}) monto_cierre
				,a.idmoneda
				,m.descripcion moneda
				,m.simbolo
				FROM caja.caja c
				JOIN caja.aperturacaja a ON c.idcaja=a.idcaja
				JOIN general.moneda m ON m.idmoneda=a.idmoneda
				WHERE c.estado='A' AND c.idcaja = {$this->cajita($idusuario,$idsucursal)}
				AND c.idsucursal='$idsucursal'
				GROUP BY a.idmoneda,c.idcaja,moneda,m.simbolo";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function denominacion(){
		$sql = "SELECT
				d.*
				,m.simbolo
				,m.descripcion
				,m.abreviatura
				FROM 
				caja.denominacion d 
				JOIN general.moneda m ON m.idmoneda=d.idmoneda
				WHERE d.estado='A' 
				ORDER BY d.idmoneda, d.billete DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}

	public function getCajaActive($campo = '', $codusuario = '') {
		$cajas = $this->getCajaHoy($codusuario);
		
		if($cajas !== false) {
			if($cajas[0]['abierto'] == 'S') {
				if(!empty($campo))
					return $cajas[$campo];
				else
					return $cajas;
			}
		}		
		return false;
	}
	
	public function ingresoCaja($idconceptomovimiento=null, $monto, $descripcion, $referencia, $tabla, $codtabla,$idmoneda='1' ,$tipocambio='1.00' ,$tipodocumento ,$idcliente, $serie=null, $numero=null ,$idsucursal, $idtipopago) {
		return $this->saveCaja($idconceptomovimiento, $monto, $descripcion, $referencia, $tabla, $codtabla,$idmoneda, $tipocambio ,$tipodocumento ,$idcliente, $serie, $numero ,$idsucursal, $idtipopago);
	}
	
	public function egresoCaja ($idconceptomovimiento=null, $monto, $descripcion, $referencia, $tabla, $codtabla,$idmoneda='1' ,$tipocambio='1.00' ,$tipodocumento ,$idcliente, $serie=null, $numero=null ,$idsucursal, $idtipopago) {
		return $this->saveCaja($idconceptomovimiento, $monto, $descripcion, $referencia, $tabla, $codtabla,$idmoneda, $tipocambio ,$tipodocumento ,$idcliente, $serie, $numero ,$idsucursal, $idtipopago);
	}
	
	private function saveCaja($idtipomovimiento, $monto, $descripcion, $referencia, $tabla, $codtabla = null,$idmoneda='1',$tipocambio='1.00',$tipodocumento='',$idcliente=0, $serie, $numero ,$idsucursal, $idtipopago=null) {
		$ci =& get_instance();
		$ci->load_model('caja');
		$ci->load_model("caja.detalle_caja");
		$query = $this->db->query("SELECT simbolo FROM caja.conceptomovimiento cm JOIN caja.tipomovimiento tm ON tm.idtipomovimiento=cm.idtipomovimiento WHERE cm.idconceptomovimiento='{$idtipomovimiento}';");
		$tipo=$query->row()->simbolo;
		
		$idusuario = (empty($this->idusuario)) ? $this->get_var_session("idusuario") : $this->idusuario;
		$caja = $this->getCajaActive('',$idusuario);
		$data_cierre = $this->data_cierre($idusuario,$idmoneda);

		if($caja !== false) {
			$monto = abs(doubleval($monto));
			
			if($tipo == 'S' && $idmoneda=='1') {
				$ci->load_model("venta.tipopago");
				$ci->tipopago->find($idtipopago);
				$validar_caja = $ci->tipopago->get('valida_caja_egreso')?$ci->tipopago->get('valida_caja_egreso'):'S';
				if($validar_caja=="S"){
					if($data_cierre[0]['monto'] >= $monto ) {
						$monto = $monto * (-1);
					}else{
						$this->exception('El monto total supera al saldo disponible en caja');
						return false;
					}					
				}else{
					$monto = $monto * (-1);
				}
			}else if($tipo == 'S' && $idmoneda!='1') {
				$monto = $monto * (-1);
			}
			
			$fecha = ($this->current_caja == true) ? date('Y-m-d') : $this->fecha_caja;
			if(empty($fecha)) {
				$fecha = date('Y-m-d');
			}

			$data2["idcaja"]				=  $caja[0]['idcaja'];
			$data2["fecha"] 				=  date("Y-m-d");
			$data2["hora"] 					=  date("H:m:s");
			$data2["idconceptomovimiento"] 	=  $idtipomovimiento;
			$data2["monto"] 				=  $monto;
			$data2["tabla"] 				=  $tabla;
			$data2["idtabla"] 				=  $codtabla;
			$data2["descripcion"] 			=  $descripcion;
			$data2["idusuario"] 			=  $idusuario;
			$data2["idmoneda"] 				=  $idmoneda;
			$data2["idtipodocumento"] 		=  $tipodocumento;//
			$data2["idcliente"] 			=  $idcliente;// 
			$data2["serie"] 				=  $serie;// 
			$data2["numero"] 				=  $numero;// 
			$data2["tipocambio"] 			=  $tipocambio;// 
			$data2["montoconvertido"] 		=  $monto*$tipocambio;// 
			$data2["referencia"] 			=  $referencia;// 
			$data2["idsucursal"] 			=  $idsucursal;// 
			$data2["idtipopago"] 			=  $idtipopago;// 
			$data2["estado"] 				=  "A";// 
			
			$data2['controller']=$this->controller;
			$data2['accion']=__FUNCTION__;

			$ci->detalle_caja->text_uppercase(false);
			$estado = $ci->detalle_caja->insert($data2);
			// $estado = $this->detalle_caja_insert($data2);
			
			return $estado;
		}else {
			$this->exception('La caja aun no se ha creado o ya se encuentra cerrado');
		}
		
		return false;
	}
	
	public function caja_anterior($server = false){
		$fields = $this->input->post();
		$fields['idusuario'] 	= $this->get_var_session("idusuario");
		$fields['idsucursal'] 	= $this->get_var_session("idsucursal");
		$and_where = '';
		if(!empty($fields['idmoneda']))
			$and_where=" AND cc.idmoneda='{$fields['idmoneda']}'";
		
		$query = $this->db->query("	SELECT 
									COALESCE(cc.monto,'0.00') monto
									,to_char(date(caja.fecha_apertura),'DD/MM/YYYY') fecha_caja
									,cc.idmoneda
									FROM caja.cierrecaja cc 
									JOIN general.moneda m ON m.idmoneda=cc.idmoneda
									JOIN caja.caja ON caja.idcaja=cc.idcaja
									WHERE cc.idcaja=(SELECT MAX(idcaja) FROM caja.caja WHERE idusuario_apertura='{$fields['idusuario']}' AND idusuario_cierre='{$fields['idusuario']}' AND idsucursal={$fields['idsucursal']}) 
									$and_where
									AND cc.estado='A'
									ORDER BY m.idmoneda;");
									
		$res = $query->result_array();

		if(!$server)
			if(!empty($res))
				$this->response($query->row()->monto);
			else
				$this->response(0);
		else
			return $res;
	}
	
	public function detalle_caja_insert($data) {
		$this->db->insert('caja.detalle_caja', $data);
		
		return true;
	}
}
?>