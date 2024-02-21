<?php

abstract class Objeto {
	
	protected $typeObject; // contiene el tipo de objeto creado
	public $attr = array(); // almacena los attributos asignados a los objetos
	public $style = array(); // almacena los stylos asignados a los objetos
	
	/**
	 * Construtor de la clase.
	 * @param $type, el identificador del tipo de objeto que se crea.
	 * e.g.
	 *		new Object('button')
	 */
	public function __construct($type) {
		$this->typeObject = $type;
	}
	
	public function getTypeObject() {
		return $this->typeObject;
	}
	
	/**
	 * Metodos para asignar parametro a los objetos.
	 * @param Mixed $attr string que contiene el nombre del attributo 
	 * o un array con las claves de los nombres de los atributos
	 * @param String $value el valor del parametro.
	 * e.g.
	 *		obj = new Object('button');
	 *		obj->setAttr('width', '100px');
	 */
	public function setAttr($attr, $value = '') {
		if(!empty($attr)) {
			if(is_array($attr)) {
				$this->setAttrs($attr);
			}
			else {
				if(strcasecmp(trim($attr), "style") == 0)
					$this->setStyles($value);
				else {
					$this->attr[strtolower(trim($attr))] = $value;
				}
			}
		}
	}

	/**
	 * Asigna atributos desde un array
	 * @param array $attr contiene los atributos a agregar,
	 * las claves con los nombres de los atributos
	 * e.g.
	 *		...
	 *		$obj->setAttrs( array('name'=>'nombre', 'type'=>'text') );
	 *		....
	 */
	public function setAttrs(array $attr) {
		foreach($attr as $key => $val) {
			if(is_string($key) && !is_numeric($key) && !is_array($val)) {
				if(strcasecmp(trim($key), "style") == 0)
					$this->setStyles($val);
				else 
					$this->attr[strtolower(trim($key))] = $val;
			}
		}
	}
	
	/**
	 * Añadir contenido a un atributo
	 * @param $attr, string con el nombre del attributo
	 * @param $value, string con el valor a agregar
	 * e.g.
	 * 		...
	 * 		$obj->appendAttr('class', 'myclase');
	 * 		....
	 * Esto genera algo asi: <select class='combo myclase'>...</select>
	 */
	public function appendAttr($attr, $value) {
		if(array_key_exists($attr, $this->attr))
			$this->attr[strtolower(trim($attr))] .= " $value";
		else
			$this->setAttr($attr, $value);
	}
	
	/**
	 * Devuelve un parametro especifico si existe.
	 * @param $attr, string con el nombre del parametro que se desea obtener
	 */
	public function getAttr($attr) {
		return $this->attr[strtolower(trim($attr))];
	}
	
	/** Remover un attributo **/
	public function removeAttr($attr) {
		if(array_key_exists(strtolower(trim($attr)), $this->attr)) {
			unset($this->attr[strtolower(trim($attr))]);
		}
	}
	
	/**
	 * Remover todos los atributtos
	 */
	public function removeAllAttr() {
		$this->attr = array();
	}
	
	/** Retorna todos los attributos en String, util cuando se desea imprimir **/
	public function attrToString() {
		$attrs = "";
		if(!empty($this->attr)) {
			/*foreach($his->attr as $key => $val) {
				$attrs .= "{$key}='{$val}' ";
			}*/
			$attr = array_keys($this->attr);
			foreach($attr as $key) {
				$attrs .= "{$key}='{$this->attr[$key]}' ";
			}
		}
		
		return $attrs;
	}
	
	/**
	 * Metodos para asignar stylos a los objetos.
	 * @param $style, String con el nombre del stylos a aplicar
	 * @param $value, String con el valor del stylo a aplicar
	 */
	public function setStyle($style, $value) {
		if(!empty($style))
			$this->style[strtolower(trim($style))] = $value;
	}
	
	/**
	 * Metodo para asignar los stylos de una cadena de stylos.
	 * @param $styles, String que contiene una lista de stylos.
	 * e.g.
	 *		...
	 *		obj->setStyle('color:#CCC; width:100px; margin:auto');
	 */
	public function setStyles($styles) {
		if(is_string($styles)) {
			if(!empty($styles)) {
				$styles = explode(';', $styles);
				foreach($styles as $val) {
					list($key, $value) = explode(':', $val);
					if(trim($key) != "" && trim($value) != "")
						$this->style[strtolower(trim($key))] = $value;
				}
			}
		}
		else {
			throw new Exception ("La funcion setStyles espera un parametro del tipo String");
		}
	}
	
	/** Devuelve el stylos especificado en el parametro **/
	public function getStyle($attr) {
		return $this->style[strtolower(trim($attr))];
	}
	
	/** Elimina un stylo indicado en el parametro **/
	public function removeStyle($style) {
		if(array_key_exists(strtolower(trim($style)), $this->style)) {
			unset($this->style[strtolower(trim($style))]);
		}
	}
	
	/** Elimina todos los estylos **/
	public function removeAllStyle() {
		$this->style = array();
	}
	
	/** Retorna todos los stylos en String **/
	public function styleToString() {
		$styles = "style='";
		if(!empty($this->style)) {
			/*foreach($his->style as $k => $v) {
				$styles .= "{$k}:{$v};";
			}*/
			$style = array_keys($this->style);
			foreach($style as $key) {
				$styles .= "{$key}:{$this->style[$key]};";
			}
		}
		$styles .= "'";
		
		return $styles;
	}
	
	/** Empaquetar los objetos o unir el objeto para imprimirlo **/
	public function pack() {
		return $this->attrToString()." ".$this->styleToString();
	}
	
	/** Armamos Atributo Data HTML5 **/
	public function buildData($arr) {
		$str = "";
		if(is_array($arr) && !empty($arr)) {
			foreach($arr as $d => $v) {
				if(is_string($d)) {
					$str .= " data-{$d}='{$v}'";
				}
			}
			$str = substr($str, 1);
		}
		return $str;
	}
}

?>