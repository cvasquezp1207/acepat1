<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Jkardex {
	
	public $idusuario = NULL;
	public $idalmacen = NULL; // codigo del almacen
	public $fecha = NULL; // fecha del movimiento de kardex
	public $idtipodocumento = NULL;
	public $serie = NULL;
	public $numero = NULL;
	
	public $observacion = NULL;
	public $idtercero = NULL; // segun la referencia (idcliente | idproveedor)
	public $idmoneda = 1; // idmoneda de la operacion (precios)
	public $tipocambio = NULL; // tipo de cambio USD
	
	/**
	 * Tabla de referencia que genera el movimiento en kardex
	 */
	private $tabla = ''; // nombre de la tabla
	private $idtabla = 0; // codigo pk (correlativo) del registro
	private $idsucursal = 0; // codigo de la sucursal
	private $model = NULL; // modelo de la tabla referencia
	
	private $tipo = 'E'; // tipo de movimiento (Entrada | Salida)
	private $productos = array(); // lista de productos a generar movimiento
	private $get_precio_costo = FALSE;
	private $get_precio_venta = FALSE;
	
	private $tabla_kardex = "almacen.kardex"; // tabla almacenar los registros
	
	private $config = array(
		'recepcion' => array(
			'desc'=>'Recepcion de item'
			,'model'=>'almacen.recepcion'
			,'pkey'=>'idrecepcion'
			,'tipo_movimiento'=>2
		)
		,'venta' => array(
			'desc'=>'Venta'
			,'model'=>'venta.venta'
			,'pkey'=>'idventa'
			,'tipo_movimiento'=>1
		)
		,'compra' => array(
			'desc'=>'Compra'
			,'model'=>'compra.compra'
			,'pkey'=>'idcompra'
			,'tipo_movimiento'=>2
		)
		,'notacredito' => array(
			'desc'=>'Nota de credito'
			,'model'=>'venta.notacredito'
			,'pkey'=>'idnotacredito'
			,'tipo_movimiento'=>24
		)
		,'guia_remision' => array(
			'desc'=>'Guia de remision'
			,'model'=>'almacen.guia_remision'
			,'pkey'=>'idguia_remision'
			,'tipo_movimiento'=>99
		)
		,'despacho' => array(
			'desc'=>'Despacho de item'
			,'model'=>'almacen.despacho'
			,'pkey'=>'iddespacho'
			,'tipo_movimiento'=>1
		)
	);
	
	private $ci = NULL;
	
	/**
	 * Constructor, obtemos el codeigniter 
	 */
	public function __construct() {
		$this->ci =& get_instance();
		$this->_init();
	}
	
	/**
	 * Inicializamos la libreria
	 */
	private function _init() {
		// obtenemos el model
		$this->ci->load_model("perfil");
		$this->model = clone $this->ci->perfil;
		unset($this->ci->perfil);
		
		// datos default
		$this->fecha = date("Y-m-d");
		$this->idusuario = $this->ci->get_var_session("idusuario");
		$this->idsucursal = $this->ci->get_var_session("idsucursal");
	}
	
	/**
	 * Almacenamos temporalmente el modelo.
	 * Este metodo deberia ser invocado antes de otros metodos.
	 * @param CI_Model $model cualquier instancia
	 */
	public function set_model(CI_Model $model) {
		$this->model = clone $model;
	}
	
	/**
	 * Referencia de la tabla que genera el movimiento
	 * @param String $ref nombre de la tabla referencia
	 * @param integer $idref pk del registro en la tabla referencia
	 * @param integer $idsucursal
	 */
	public function referencia($ref, $idref, $idsucursal, $tipo_movimiento = FALSE) {
		if(array_key_exists($ref, $this->config)) {
			$this->tabla = $ref;
			$this->idtabla = $idref;
			$this->idsucursal = $idsucursal;
			
			if($this->model == NULL) {
				throw new Exception('Llame primero al metodo [set_model]');
				return;
			}
			
			$this->model->set_table_name($this->config[$this->tabla]['model']);
			$this->model->initialize();
			
			$params = array(
				$this->config[$this->tabla]["pkey"] => $this->idtabla
				,"idsucursal" => $this->idsucursal
			);
			
			if( ! $this->model->exists($params)) {
				throw new Exception('La referencia que ha indicado no existe');
				return;
			}
			
			$this->model->find($params);
			
			if($tipo_movimiento !== FALSE) {
				if( ! is_int($tipo_movimiento)) {
					if(is_numeric($tipo_movimiento) && strpos($tipo_movimiento, ".") === false)
						$tipo_movimiento = intval($tipo_movimiento);
				}
				
				if(is_int($tipo_movimiento) && $tipo_movimiento > 0)
					$this->config[$this->tabla]["tipo_movimiento"] = $tipo_movimiento;
			}
		}
		else {
			throw new Exception('No existe el tipo de referencia');
		}
	}
	
	/**
	 * Metodos para indicar el tipo de movimiento de kardex
	 * @param boolean $bool default true
	 */
	public function entrada($bool = TRUE) {
		if($bool) {
			$this->tipo = 'E';
		}
		else {
			$this->tipo = 'S';
		}
	}
	
	public function salida($bool = TRUE) {
		$this->entrada( ! $bool);
	}
	
	/**
	 * Metodos para verificar el tipo de movimiento de kardex
	 * @return boolean
	 */
	public function es_entrada() {
		return ($this->tipo == 'E');
	}
	
	public function es_salida() {
		return ($this->tipo == 'S');
	}
	
	public function calcular_precio_costo($bool = TRUE) {
		$this->get_precio_costo = $bool;
	}
	
	public function calcular_precio_venta($bool = TRUE) {
		$this->get_precio_venta = $bool;
	}
	
	public function push($idproducto, $cantidad = 0, $preciocosto = 0, $precioventa = 0, $idunidad = NULL, $idalmacen = NULL, $correlativo = NULL) {
		if(is_array($idproducto)) {
			foreach($idproducto as $arr) {
				if(! isset($arr["cantidad"])) {$arr["cantidad"] = 0;}
				if(! isset($arr["preciocosto"])) {$arr["preciocosto"] = 0;}
				if(! isset($arr["precioventa"])) {$arr["precioventa"] = 0;}
				if(! isset($arr["idunidad"])) {$arr["idunidad"] = NULL;}
				if(! isset($arr["idalmacen"])) {$arr["idalmacen"] = NULL;}
				if(! isset($arr["correlativo"])) {$arr["correlativo"] = NULL;}
				$this->push($arr['idproducto'], $arr['cantidad'], $arr["preciocosto"], $arr["precioventa"], 
					$arr["idunidad"], $arr["idalmacen"], $arr["correlativo"]);
			}
		}
		else {
			$this->productos[] = array(
				'idproducto'=>$idproducto
				,'cantidad'=>$cantidad
				,'preciocosto'=>$preciocosto
				,'precioventa'=>$precioventa
				,'idunidad'=>$idunidad
				,'idalmacen'=>$idalmacen
				,'correlativo'=>$correlativo
			);
		}
	}
	
	private function _get_cambio_dolar($idmoneda = 2) {
		$query = $this->model->query("SELECT valor_cambio FROM general.moneda WHERE idmoneda = $idmoneda");
		if($query->num_rows() > 0) {
			return $query->row()->valor_cambio;
		}
		return 1;
	}
	
	private function _next_correlativo() {
		$id = $this->config[$this->tabla]['tipo_movimiento'];
		
		$query = $this->model->query("SELECT correlativo FROM almacen.tipo_movimiento WHERE tipo_movimiento=$id");
		if($query->num_rows() > 0) {
			return intval($query->row()->correlativo);
		}
		return 0;
	}
	
	private function _defaults(&$kardex) {
		$kardex->set("annio", date("Y"));
		$kardex->set("periodo", date("m"));
		$kardex->set("idusuario", $this->idusuario);
		$kardex->set("fecha_registro", date('Y-m-d'));
		$kardex->set("fecha_emision", $this->fecha);
		$kardex->set("hora", date("H:i:s"));
		$kardex->set("observacion", $this->observacion.'-'.$this->config[$this->tabla]['desc']);
		
		$kardex->set("estado", "C");
		$kardex->set("idreferencia", $this->idtabla);
		$kardex->set("idtercero", $this->idtercero);
		$kardex->set("tabla", $this->tabla);
		$kardex->set("idsucursal", $this->idsucursal);
		$kardex->set("tipo_cambio_d", $this->tipocambio);
		
		$iddocumento = ($this->idtipodocumento != NULL) ? $this->idtipodocumento : $this->model->get("idtipodocumento");
		$serie = ($this->serie != NULL) ? $this->serie : $this->model->get("serie");
		$numero = ($this->numero != NULL) ? $this->numero : $this->model->get("numero");
		
		$kardex->set("serie", $serie);
		$kardex->set("numero", $numero);
		$kardex->set("tipo_docu", $iddocumento);
		
		$tipo = $this->es_entrada() ? "ENT" : "SAL";
		$kardex->set("tipo", $tipo);
		
		$kardex->set("tipo_movimiento", $this->config[$this->tabla]["tipo_movimiento"]);
	}
	
	/**
	 * Insertamos los registros en kardex
	 */
	public function run() {
		if($this->model == NULL) {
			return;
		}
		
		if(!empty($this->productos)) {
			// instanciamos los modelos
			$kardex = clone $this->model;
			$kardex->set_table_name($this->tabla_kardex);
			$kardex->initialize();
			$kardex->text_uppercase(false);
			
			if(! isset($this->ci->producto)) {
				$this->ci->load_model("producto");
				$producto_model = clone $this->ci->producto;
				unset ($this->ci->producto);
			}
			else {
				$producto_model = clone $this->ci->producto;
			}
			
			if(! isset($this->ci->producto_unidad)) {
				$this->ci->load_model("producto_unidad");
				$producto_unidad_model = clone $this->ci->producto_unidad;
				unset ($this->ci->producto_unidad);
			}
			else {
				$producto_unidad_model = clone $this->ci->producto_unidad;
			}
			
			// $this->ci->load->model("producto_model");
			// $this->ci->load->model("producto_unidad_model");
			
			$tip_movimiento = clone $this->model;
			$tip_movimiento->set_table_name("tipo_movimiento", "almacen");
			$tip_movimiento->initialize();
			
			if($this->tipocambio == NULL || ($this->idmoneda == 1 && $this->tipocambio <= 1)) {
				$this->tipocambio = $this->_get_cambio_dolar();
			}
			
			// valores por default del modelo
			$this->_defaults($kardex);
			
			// obtenemos el correlativo
			$tip_movimiento->find($this->config[$this->tabla]['tipo_movimiento']);
			$correlativo = intval($tip_movimiento->get("correlativo"));
			
			// recorremos la lista de items
			foreach($this->productos as $arr) {
				$preciocosto = floatval($arr["preciocosto"]);
				$precioventa = floatval($arr["precioventa"]);
				if(empty($arr["idalmacen"])) {
					$arr["idalmacen"] = $this->idalmacen;
				}
				
				// buscamos los registros en la bd
				// $producto_model->find($arr["idproducto"]);
				$producto_unidad_model->find(array("idproducto"=>$arr["idproducto"], "idunidad"=>$arr["idunidad"]));
				$cantidad_unid = $producto_unidad_model->get("cantidad_unidad_min"); // cantidad segun la unidad de medida
				$arr["cantidad_um"] = $cantidad_unid;
				
				// calculamos los precio de costo y venta
				if($this->get_precio_costo) {
					// precio en PEN
					$preciocosto = $producto_model->get_precio_costo_unitario($arr["idproducto"], $this->idsucursal);
					$preciocosto = $preciocosto * $cantidad_unid;
				}
				if($this->get_precio_venta) {
					// precio en PEN
					$precioventa = $producto_model->get_precio_venta_unitario($arr["idproducto"], $this->idsucursal);
					$precioventa = $precioventa * $cantidad_unid;
				}
				
				$arr["costo_unit_s"] = $preciocosto;
				$arr["costo_unit_d"] = $preciocosto * $this->tipocambio;
				$arr["precio_unit_venta_s"] = $precioventa;
				$arr["precio_unit_venta_d"] = $precioventa * $this->tipocambio;
				$arr["importe_s"] = $arr["costo_unit_s"] * $arr["cantidad"];
				$arr["importe_d"] = $arr["costo_unit_d"] * $arr["cantidad"];
				if(! isset($arr["correlativo"])) {
					$arr["correlativo"] = $correlativo;
					$correlativo = $correlativo + 1;
				}
				
				$kardex->set($arr);
				$kardex->insert(null, false);
			}
			
			// actualizamos el correlativo
			// throw new Exception("en kardex");
			$tip_movimiento->set("correlativo", $correlativo);
			$tip_movimiento->update();
			
			// vaciamos la lista de productos
			$this->productos = array();
		}
	}
	
	public function remove($tabla, $idtabla, $idsucursal) {
		$this->ci->db
			->where("tabla", $tabla)
			->where("idreferencia", $idtabla)
			->where("idsucursal", $idsucursal)
			->update($this->tabla_kardex, array("estado"=>"I"));
	}
	
	public function restore($tabla, $idtabla, $idsucursal) {
		$this->ci->db
			->where("tabla", $tabla)
			->where("idreferencia", $idtabla)
			->where("idsucursal", $idsucursal)
			->update($this->tabla_kardex, array("estado"=>"A"));
	}
}

/* fin de la clase JKardex */