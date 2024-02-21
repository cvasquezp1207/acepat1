<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pay {
	
	private $caja = null; // controlador caja
	public $post = null; // datos para las tablas (normalmente es el post)
	private $is_ingreso = true; // datos para las tablas (normalmente es el post)
	
	private $ci = NULL;
	
	public function __construct() {
		// constructor
		$this->ci =& get_instance();
		$this->defaults();
	}
	
	protected function defaults() {
		$this->post = array(
			// otros datos (para caja)
			"descripcion" => ""
			,"referencia" => ""
			,"tabla" => "" // referencia a la tabla que genera el movimiento
			,"idoperacion" => "" // pk del registro de la tabla referencia
			,"cambio_moneda" => 1 // venta, compra
			,"idcliente" => 0  // recibo_ingreso (en recibo_egreso???)
			
			// datos en recibo_ingreso, venta, compra
			,"idmoneda" => 1
			,"idtipodocumento" => 0
			,"serie" => ""
			,"numero" => ""
			,"idsucursal" => 0
			,"idusuario" => 0
			
			// datos del formulario pago
			,"afecta_caja" => "S"
			,"idconceptomovimiento" => 0
			,"idtipopago" => 0
			,"monto_pagar" => 0
			,"monto_entregado" => 0
			,"monto_vuelto" => 0
			,"idtarjeta" => 0
			,"nro_tarjeta" => ""
			,"operacion_tarjeta" => ""
			,"idcuentas_bancarias" => 0
			,"fecha_deposito" => "1900-01-01"
			,"operacion_deposito" => ""
			,"monto_convertido_pay" => 0
		);
	}
	
	/**
	 * Establecer el controlado que contiene las funciones para el 
	 * registro de los movimientos de caja
	 * @param object $control
	 */
	public function set_controller(CI_Controller $control) {
		if( ! method_exists($control, "ingresoCaja")) {
			throw new Exception("No existe el metodo <b>ingresoCaja</b>");
			return;
		}
		if( ! method_exists($control, "egresoCaja")) {
			throw new Exception("No existe el metodo <b>egresoCaja</b>");
			return;
		}
		$this->caja = $control;
		// $ci =& get_instance();
		// $this->caja->db = $ci->db;
	}
	
	/**
	 * datos a recibir con los cuales se hara una insercion en las tablas de kardex, 
	 * movimientos de tarjeta y deposito
	 * @param array $array
	 */
	public function set_data($array) {
		foreach($this->post as $key => $val) {
			if(array_key_exists($key, $array)) {
				$this->post[$key] = $array[$key];
			}
		}
		// $this->post = array_intersect_key($array, $this->post);
	}
	
	/**
	 * Tipo de operacion a realizar (Entrada o Salida)
	 * @param boolean $bool
	 */
	public function entrada($bool = TRUE) {
		$this->is_ingreso = $bool;
	}
	
	public function salida($bool = TRUE) {
		$this->entrada( ! $bool);
	}
	
	/**
	 * procesar el registro (movimiento) en caja
	 */
	public function process() {
		// existe el controlador
		if($this->caja == null) {
			return true;
		}
		
		// si el movimiento tiene afecto en caja
		if($this->post["afecta_caja"] == "S") {
			
			// tipo de movimiento
			$method = "ingresoCaja";
			if($this->is_ingreso == false) {
				$method = "egresoCaja";
			}
			
			// datos default caja controler
			$this->caja->idusuario = $this->post['idusuario'];
			$this->caja->idsucursal = $this->post["idsucursal"];
			
			// registramos movimiento de caja
			$this->caja->{$method}(
				$this->post["idconceptomovimiento"]
				, $this->post["monto_pagar"]
				, $this->post["descripcion"]
				, $this->post["referencia"]
				, $this->post["tabla"]
				, $this->post["idoperacion"]
				, $this->post["idmoneda"]
				, $this->post["cambio_moneda"]
				, $this->post["idtipodocumento"]
				, $this->post["idcliente"]
				, $this->post["serie"]
				, $this->post["numero"]
				, $this->post["idsucursal"]
				, $this->post["idtipopago"]
			);
			
			// datos adicionales para las otras tablas
			$this->post["importe"] = $this->post["monto_pagar"];
			$this->post["estado"] = "A";
			$this->post["fecha"] = date("Y-m-d");
			$this->post["hora"] = date("H:i:s");
			
			// verificamos el tipo de pago
			$idpago = intval($this->post["idtipopago"]); // 1 es efectivo
			
			if($idpago == 2) {
				// registramos el movimiento de tarjeta
				$this->post["nro_operacion"] = $this->post["operacion_tarjeta"];
				$this->ci->load_model("venta.movimiento_tarjeta");
				$this->ci->movimiento_tarjeta->text_uppercase(false);
				$this->ci->movimiento_tarjeta->save($this->post, false);
			}
			else if($idpago == 3) {
				// registramos el movimiento de deposito
				$this->post["nro_operacion"] = $this->post["operacion_deposito"];
				$this->post["monto_convertido"] = $this->post["monto_convertido_pay"];
				$this->ci->load_model("venta.movimiento_deposito");
				$this->ci->movimiento_deposito->text_uppercase(false);
				$this->ci->movimiento_deposito->save($this->post, false);
			}
			else {
				// nothing, some code here
			}
		}
		
		return true;
	}
	
	public function remove($tabla, $idtabla, $idsucursal, $restore = FALSE) {
		$estado = ($restore === TRUE) ? "A" : "I";
		
		// eliminamos el ingreso de caja
		$this->ci->db->where("tabla", $tabla)->where("idtabla", $idtabla)
			->where("idsucursal", $idsucursal)
			->update("caja.detalle_caja", array("estado"=>$estado));
		
		// eliminamos el ingreso del movimiento de tarjeta
		$this->ci->db->where("tabla", $tabla)->where("idoperacion", $idtabla)
			->where("idsucursal", $idsucursal)
			->update("venta.movimiento_tarjeta", array("estado"=>$estado));
		
		// eliminamos el ingreso de movimiento deposito
		$this->ci->db->where("tabla", $tabla)->where("idoperacion", $idtabla)
			->where("idsucursal", $idsucursal)
			->update("venta.movimiento_deposito", array("estado"=>$estado));
			
		return true;
	}
	
	public function restore($tabla, $idtabla, $idsucursal) {
		return $this->remove($tabla, $idtabla, $idsucursal, TRUE);
	}
	
	public function caja_is_open($tabla, $idtabla, $idsucursal) {
		// obtenemos el detalle de la caja
		$query = $this->ci->db->where("tabla", $tabla)->where("idtabla", $idtabla)
			->where("idsucursal", $idsucursal)->where("estado", "A")
			->get("caja.detalle_caja");
		
		if($query->num_rows() > 0) {
			$datos_detalle = $query->row_array();
			
			// obtenemos datos de la caja
			$query = $this->ci->db->where("idcaja", $datos_detalle["idcaja"])
				->where("idsucursal", $idsucursal)->where("estado", "A")
				->get("caja.caja");
			
			if($query->num_rows() > 0)
				return ($query->row()->abierto == "S");
		}
		
		return false;
	}
	
	public function remove_if_open($tabla, $idtabla, $idsucursal, $restore = FALSE) {
		if($this->caja_is_open($tabla, $idtabla, $idsucursal)) {
			if($restore === TRUE)
				return $this->restore($tabla, $idtabla, $idsucursal);
			else
				return $this->remove($tabla, $idtabla, $idsucursal);
		}
		return false;
	}
	
	public function restore_if_open($tabla, $idtabla, $idsucursal) {
		return $this->remove_if_open($tabla, $idtabla, $idsucursal, TRUE);
	}
}

/* End of file Pay.php */