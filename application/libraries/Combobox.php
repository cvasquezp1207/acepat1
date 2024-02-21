<?php 

include_once APPPATH."libraries/objeto.php";

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Combobox extends Objeto {
	
	private $jComboBox;
	private $jMenuItem = array();
	private $optionSelected = null;
	public $estricto = true;
	
	public function __construct() {
		parent::__construct('select');
		$this->init();
	}
	
	public function init() {
		$this->jMenuItem = array();
		$this->optionSelected = null;
		$this->jComboBox = "<select [cfeature]>[coption]</select>";
	}
	
	/**
	 * Aniade un item al comboBox.
	 * @param $value, el valor del item option
	 * @param $text, el texto que sera visualizado por el usuario
	 * e.g.
	 *		...
	 *		obj->addItem(0, 'Seleccione');
	 *		obj->addItem('M', 'Masculino');
	 *		obj->addItem('', 'Femenino');
	 *		....
	 */
	public function addItem($value, $text = '', $cols = array()) {
		if(is_array($value))
			if(is_array($text) && ! empty($text))
				$this->addItems($value, $text);
			else
				$this->addItems($value, $cols);
		else
			$this->jMenuItem[] = array($value, $text);
	}
	
	/**
	 * function para enviar una lista de options a ser agregados, util cuando
	 * se desea cargar el comboBox desde el result de una consulta sql.
	 * @param String | array 1D | array 2D
	 * e.g.
	 *		...
	 *		obj->addItems('Seleccione');
	 *		obj->addItems(array('Masculino', 'femenino'));
	 *		obj->addItems(array(array('0', 'Seleccione'), array('1', 'Masculino')[, ...]);
	 *		....
	 * Si se envia un String o un Array 1D tanto el valor como el texto del option sera lo mismo.
	 */
	public function addItems($items, $cols = array()) {
		if(!empty($items)) {
			if(!is_array($items)) {
				$this->addItem($items, $items);
			}
			else {
				if(is_array($items[0])) {
					if( count($items[0]) >= 2 ) {
						$k = 0;
						$d = 1;
						if(!empty($cols)) {
							$k = $d = array_shift($cols);
							if(!empty($cols)) {
								$d = array_shift($cols);
							}
						}
						else {
							if(!isset($items[0][0])) {
								$arr = array_keys($items[0]);
								$k = array_shift($arr);
								$d = array_shift($arr);
							}
						}
						
						foreach($items as $i) {
							$opt = array();
							$opt[] = $i[$k];
							
							if(isset($i[$d]))
								$opt[] = $i[$d];
							else
								$opt[] = $i[$k];
							
							$data = array();
							if(!empty($cols)) {
								foreach($cols as $c) {
									if(isset($i[$c])) {
										$data[$c] = $i[$c];
									}
								}
							}
							$opt[] = $data;
							
							$this->jMenuItem[] = $opt;
						}
					}
					else {
						$k = $d = 0;
						if(!isset($items[0][$k])) {
							$arr = array_keys($items[0]);
							$k = $d = array_shift($arr);
						}
							
						foreach($items as $i) {
							$this->jMenuItem[] = array($i[$k], $i[$d]);
						}
					}
				}
				else {
					foreach($items as $i) {
						$this->jMenuItem[] = array($i, $i);
					}
				}
			}
		}
	}
	
	/**
	 * Metodo para indicar el option a seleccionar
	 */
	public function setSelectedOption($optionSelected) {
		$this->optionSelected = $optionSelected;
	}
	
	/*
	$combo->removeAllItems();
	$combo->addItems($result);
	$com = $combo->getObject();
	*/
	public function removeItems($offset = 0, $length = null) {
		if(is_null($length)) {
			if(is_int($offset))
				array_splice($this->jMenuItem, $offset);
		}
		else {
			if(is_int($offset) && is_int($length))
				array_splice($this->jMenuItem, $offset, $length);
		}
	}
	
	public function removeAllItems() {
		$this->jMenuItem = array();
	}
	
	public function item_exists($select) {
		if( ! empty($this->jMenuItem)) {
			foreach($this->jMenuItem as $i) {
				if($select == $i[0])
					return true;
			}
		}
		return false;
	}
	
	public function getAllItems() {
		$item = "";
		if(!empty($this->jMenuItem)) {
			$selected = (!empty($this->optionSelected)) ? $this->optionSelected : "o7l;_oxbcs#+*.-10=7?@./*+-zxa.:";
			// if($this->ev) {
				// var_dump($selected);echo "<br>";
			// }
			foreach($this->jMenuItem as $i) {
				$item .= "<option value='{$i[0]}'";
				if(!empty($i[2])) {
					$item .= " ".$this->buildData($i[2])." ";
				}
				// if($this->ev) {
					// echo("-----------------------------<br>");
					// var_dump($i[0]);echo("<br>");
					// var_dump($selected === $i[0]);echo("<br>");
				// }
				// var_dump($i[0]);exit;
				if($this->estricto){
					if($selected === $i[0]) {$item .= " selected='selected'";}
				}else{
					if($selected == $i[0]) {$item .= " selected='selected'";}else{}
				}
				$item .= ">{$i[1]}</option>";
			}
		}
		// if($this->ev)
			// exit;
		return $item;
	}
	
	public function getObject($clear = FALSE) {
		$jComboBox = str_replace('[cfeature]', $this->pack(), $this->jComboBox);
		$jComboBox = str_replace('[coption]', $this->getAllItems(), $jComboBox);
		
		if($clear) {
			$this->init();
		}
		
		return $jComboBox;
	}
}

/* End of file Combobox.php */