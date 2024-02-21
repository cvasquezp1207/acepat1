<?php 

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Kardex {
	
	public  $correlativo = ''; // codigo kardex
	public  $fecha_emision = ''; // fecha_emision del movimiento de kardex
	public  $idalmacen = ''; // codigo del  almacen
	public  $idproducto = ''; // codigo del producto
	public  $idunidad = ''; // codigo de la unidad de medida
	public  $cantidad = ''; // cantidad que ingresa al kardex
	public  $precio_unit_venta_s = '';
	public  $precio_unit_venta_d = '';
	public  $costo_unit_s = '';
	public  $costo_unit_d = '';
	public  $importe_s = '';
	public  $importe_d = '';
	public  $idreferencia = '';
	public  $idtercero = '';	
	public  $tipo_docu = '';
	public  $numero = ''; // numero de documento de referencia del movimiento
	public  $serie = ''; 
	public  $observacion = ''; // informacion del movimiento
	public  $estado = ''; 
	public  $annio = ''; 
	public  $periodo = ''; 
	public  $idusuario = ''; 
	public  $fecha_registro = ''; 
	public  $hora = ''; 
	public  $tabla = ''; // caracter que hace referencia a la tabla que genera el movimiento
	public  $tipo_movimiento = ''; // ordinal de tipo movimiento (Ingreso | Egreso)
	
	private  $tipo = ''; // tipo de movimiento (Ingreso | Egreso)
	// private  $productos = array(); // lista de productos a generar movimiento
	// private  $model = null; // modelo de la tabla referencia
	// private  $calcularPrecioCosto = false;
	
	private  $arrTabla = array(
		'RECEP' => array(
			'desc'=>'Recepcion de O.C'
			,'model'=>'almacen.kardex'
			,'alias'=>'recepcion'
		)
		,'VEN' => array(
			'desc'=>'Venta'
			,'model'=>'venta.venta'
			,'alias'=>'venta'
		)
		,'COMP' => array(
			'desc'=>'Recepcion automatica desde Compras'
			,'model'=>'compra.compra'
			,'alias'=>'compras'
		)/*
		,'GR' => array(
			'desc'=>'Guia de remision'
			,'model'=>'almacen.guia_remision'
		)
		,'NC' => array(
			'desc'=>'Nota de credito'
			,'model'=>'venta.nota_credito'
		)*/
	);
	
	// invocador 1
	public  function referencia($tabla) {
		if(array_key_exists($tabla, $this->arrTabla)) {
			$this->tabla = $tabla;			
		}
		else {
			throw new Exception('No existe el tipo de referencia');
		}
	}
	
	// invocadores 2
	public  function ingreso($bool) {
		if($bool) {
			$this->tipo = 'I';
			$this->tipo_movimiento = 1;//ENTRADAS
		}
		else {
			self::egreso(true);
		}
	}
	
	
	// modelos
	public  function set_model($model) {
		$this->modelo = $model;
		
		$this->detalle_almacen = clone $model;
		$this->detalle_almacen->set_table_name("detalle_almacen","almacen");
		$this->detalle_almacen->initialize();
		
		$this->tip_movimiento = clone $model;
		$this->tip_movimiento->set_table_name("tipo_movimiento","almacen");
		$this->tip_movimiento->initialize();
	}
	
	
	
	public  function egreso($bool) {
		if($bool) {
			$this->tipo = 'E';
			$this->tipo_movimiento = 0;//SALIDAS
		}
		else {
			self::ingreso(true);
		}
	}
	
	public  function is_ingreso() {
		return ($this->tipo == 'I');
	}
	
	private  function defaults() {
		//defult
		$this->modelo->set("annio",date("Y"));
		$this->modelo->set("periodo", date("m"));
		$this->modelo->set("idusuario", $this->idusuario);
		$this->modelo->set("idusuario", $this->idusuario);
		$this->modelo->set("fecha_registro", date('Y-m-d'));
		$this->modelo->set("fecha_emision", date('Y-m-d'));
		$this->modelo->set("hora", date("H:i:s"));		
		$this->modelo->set("observacion", $this->observacion.'-'.$this->arrTabla[$this->tabla]['desc']);
		
		//valores que vienen del controlador
		$this->modelo->set("estado",$this->estado);
		$this->modelo->set("idproducto", $this->idproducto);				
		$this->modelo->set("correlativo", $this->correlativo);				
		$this->modelo->set("tipo_movimiento", $this->tipo_movimiento);				
		$this->modelo->set("idalmacen", $this->idalmacen);				
		$this->modelo->set("idunidad", $this->idunidad);				
		$this->modelo->set("cantidad", $this->cantidad);				
		$this->modelo->set("precio_unit_venta_s", $this->precio_unit_venta_s);				
		$this->modelo->set("precio_unit_venta_d", $this->precio_unit_venta_d);				
		$this->modelo->set("costo_unit_s", $this->costo_unit_s);				
		$this->modelo->set("costo_unit_d", $this->costo_unit_d);				
		$this->modelo->set("importe_s", $this->importe_s);				
		$this->modelo->set("importe_d", $this->importe_d);		
		$this->modelo->set("idreferencia", $this->idreferencia);		
		$this->modelo->set("idtercero", $this->idtercero);		
		$this->modelo->set("tipo_docu", $this->tipo_docu);		
		$this->modelo->set("serie", $this->serie);		
		$this->modelo->set("numero", $this->numero);		
		$this->modelo->set("tabla", $this->arrTabla[$this->tabla]['alias']);		
	}
	
	public  function run() {
		$this->defaults();
		// echo "<pre>";
		// print_r($this->modelo->get_fields());			
		$this->modelo->insert('',false);
			
		$datos_kardex = $this->modelo->get_fields();
		
		if($datos_kardex['tipo_movimiento']==1){//1 ES ENTRADA, 0 ES SALIDA
			$tipo_movimiento = 1;
			$datos_kardex['tipo'] = "E"; 
			$datos_kardex['tipo_number'] = 1;  //1 PARA ENTRADA, -1 PARA SALIDA
		}else{
			$tipo_movimiento = 0;
			$datos_kardex['tipo'] = "S"; 
			$datos_kardex['tipo_number'] = -1;  //1 PARA ENTRADA, -1 PARA SALIDA
		}
		
		$datos_kardex['tabla'] = substr($datos_kardex['tabla'], 0, 1);
		// echo  $datos_kardex['tabla'];
		$datos_kardex['precio_costo'] = $datos_kardex['costo_unit_s'];
		$datos_kardex['precio_venta'] = 0.00;
		$datos_kardex['fecha'] = date("Y-m-d");
		$datos_kardex['idtabla'] = $datos_kardex['idreferencia'];
		$datos_kardex['estado'] = "A";
		$this->detalle_almacen->insert($datos_kardex);
		
		
		$datos['correlativo'] = $datos_kardex['correlativo']+1;
		$datos['tipo_movimiento'] = $datos_kardex['tipo_movimiento'];
		$this->tip_movimiento->update($datos);
		
	}
}