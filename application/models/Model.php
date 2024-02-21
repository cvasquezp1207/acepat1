<?php

abstract class Model extends CI_Model {
	protected $schema = "public"; // esquema actual de la tabla
	protected $table_name; // nombre de la tabla
	protected $columns; // array con los nombres de las columnas de la tabla
	protected $pk; // array de columnas pk de la tabla
	protected $field_data; // array asociativo con las columnas y datos asignados
	protected $columns_autoinc; // array con las columnas autoincrement
	protected $text_uppercase = TRUE; // convertir a uppercase
	protected $ci; // instancia del controlador
	protected $tabla_audit = array("seguridad.usuario"
											,"venta.reciboingreso"
											,"venta.reciboegreso"
											,"venta.venta"
											,"venta.cliente"
											,"venta.notacredito"
											,"compra.compra"
											,"compra.cronograma_pago"//solo para probar el delete, aun falta
											// ,"caja.caja"
											// ,"caja.detalle_caja"
											,"credito.credito"
											// ,"credito.letra"
											,"credito.amortizacion"
											,"seguridad.perfil"
									); // aqui va todas las tablas que se quiere auditar

    public function __construct($default = TRUE) {
        parent::__construct();
		$this->ci =& get_instance();
		if($default) {
			$this->init();
			$this->initialize();
		}
    }
	
	/**
	 * Metodo para obtener datos sobre la tabla 
	 */
	public function initialize() {
		if(empty($this->table_name)) {
			$table = strtolower(str_replace('_model', '', get_class($this)));
			$this->set_table_name($table);
		}
		
		$this->columns = array();
		$this->pk = array();
		$this->field_data = array();
		
		// $fields = $this->db->field_data($this->get_table_name());
		$fields = $this->column_postgres($this->get_table_name(), $this->get_schema());
		foreach($fields as $field) {
			$this->columns[] = $field->column_name;
			$this->field_data[$field->column_name] = null;
			// if(!empty($field->primary_key)) {
				// $this->pk[] = $field->name;
			// }
			if($this->is_seq($field->column_default)) {
				$this->columns_autoinc[] = $field->column_name;
			}
		}
		
		if(empty($this->pk)) {
			$fields = $this->pk_postgres($this->get_table_name(), $this->get_schema());
			if(!empty($fields)) {
				foreach($fields as $field) {
					$this->pk[] = $field->column_name;
				}
			}
		}
		
		if(empty($this->pk)) {
			if(!empty($this->columns)) {
				$this->pk[] = $this->columns[0];
			}
		}
	}
	
	/**
	 * Indicar el nombre del esquema de la tabla
	 * @paramt $schema
	 */
	public function set_schema($schema) {
		$this->schema = $schema;
	}
	
	/**
	 * Indicar el nombre de la tabla
	 * @param $table
	 */
	public function set_table_name($table, $schema = NULL) {
		if($schema != null) {
			$this->set_schema($schema);
		}
		
		if(strpos($table, '.') === false) {
			$this->table_name = $table;
		}
		else {
			$split = explode('.', $table, 2);
			if(count($split) == 2) {
				$this->set_schema($split[0]);
				$this->table_name = str_replace('.', '', $split[1]);
			}
			else {
				$this->table_name = $split[0];
			}
		}
	}
	
	/**
	 * Metodo para obtener el nombre del esquema de la tabla
	 * @return String
	 */
	public function get_schema() {
		return $this->schema;
	}
	
	/**
	 * Metodo para obtener el nombre de la tabla del modelo
	 * @return String
	 */
	public function get_table_name() {
		return $this->table_name;
	}
	
	/**
	 * Obtener el nombre completo de la tabla incluido el esquema
	 * @return String
	 */
	public function get_full_table_name() {
		$table = $this->get_schema();
		if(empty($table)) {
			return $this->get_table_name();
		}
		$table .= ".".$this->get_table_name();
		return $table;
	}
	
	/**
	 * Metodo para obtener el nombre de las columnas
	 * @return Array
	 */
	public function get_columns() {
		return $this->columns;
	}
	
	/**
	 * Obtener los pk de la tabla, si el pk es mas de una columna, 
	 * retorna un array con todas las columnas
	 * @return mixed
	 */
	public function get_pk($all = TRUE) {
		$pks = $this->pk;
		if(count($pks) > 1) {
			if($all) {
				return $pks;
			}
		}
		return array_shift($pks);
	}
	
	/**
	 * Indicar el primary key de una tabla, esto en el caso de que se instancie 
	 * un modelo con una vista
	 */
	public function set_column_pk($column) {
		if(is_array($column)) {
			$this->pk = $column;
		}
		else {
			$this->pk = array($column);
		}
	}
	
	/**
	 * Ejecutar una consulta sql
	 * @param String consulta sql
	 * @return Array resultSet
	 */
	public function query($sql) {
		return $this->db->query($sql);
	}
	
	/**
	 * Escapar datos para ejecutar la consulta sql
	 * @param $val valor a escapar
	 * @return String
	 */
	public function escape($val) {
		return $this->db->escape($val);
	}
	
	/**
	 * Obtenemos las columnas de una tabla, solo funciona para postgres,
	 * el metodo [field_data] de codeigniter tiene problemas con tablas 
	 * del mismo nombre que estan en diferentes esquemas
	 */
	public function column_postgres($table, $schema) {
		$sql = "SELECT column_name, data_type, column_default
			FROM information_schema.columns 
			WHERE table_name = '$table' AND table_schema = '$schema'
			ORDER BY ordinal_position";
		
		$rs = $this->db->query($sql);
		return $rs->result();
	}
	
	/**
	 * Obtenemos las columnas pk de una tabla, la sentencia sql solo 
	 * sirve para postgres, porque codeigniter no obtiene los pk 
	 * cuando se llama al metodo [field_data]
	 * @param String $tabla nombre de la tabla
	 * @param String $schema nombre del esquema de la tabla
	 * @return mixed resultado de la consulta
	 */
	public function pk_postgres($table, $schema) {
		$sql = "SELECT column_name
			FROM information_schema.key_column_usage 
			WHERE table_name = '$table' and table_schema = '$schema'
			and position_in_unique_constraint is null
			ORDER BY ordinal_position";
		
		$rs = $this->db->query($sql);
		return $rs->result();
	}
	
	/**
	 * Verificar si una columna es autoincrement
	 * solo funciona para postgres
	 * @param $string valor por default de un campo
	 */
	public function is_seq($string) {
		return substr($string, 0, 7) == "nextval";
	}
	
	/**
	 * Metodo para obtener datos iniciales del modelo
	 */
	public abstract function init();
	
	/**
	 * Metodo para indicar si se va a convertir a letras mayusculas los datos
	 */
	public function text_uppercase($upper) {
		$this->text_uppercase = $upper;
	}
	
	/**
	 * buscar algun registro en la tabla segun el parametro enviado
	 * @param mixed func_get_args(), el valor a buscar o un array asociativo
	 * con los nombres de las columnas
	 */
	public function find() {
		$c = func_num_args();
		if($c < 1) {
			return null;
		}
		
		$cols = $this->get_columns();
		$pks = $this->pk;
		$wheres = array();
		
		$param = func_get_args();
		for($i=0; $i < $c; $i++) {
			$data = $param[$i];
			
			if(is_array($data)) {
				foreach($data as $k=>$v) {
					if(in_array($k, $cols)) {
						$wheres[$k] = $v;
					}
					else if(is_numeric($k)) {
						$k = array_shift($pks);
						if($k != null) {
							$wheres[$k] = $v;
						}
					}
				}
			}
			else {
				$k = array_shift($pks);
				if($k != null) {
					$wheres[$k] = $data;
				}
			}
		}
		
		if(empty($wheres)) {
			return null;
		}
		$query = $this->db->get_where($this->get_full_table_name(), $wheres);
		if($query->num_rows() < 1) {
			return null;
		}
		
		$this->set($query->row_array());
		
		if($query->num_rows() == 1) {
			return $query->row_array();
		}
		
		return $query->result_array();
	}
	
	/**
	 * Metodo para validar los datos a insertar o actualizar en la bd
	 */
	private function prepare_data($data) {
		if(!empty($data)) {
			$fields = array_intersect_key($data, $this->field_data);
		}
		else {
			$fields = $this->field_data;
		}
		
		// limpiamos los datos
		if(!empty($fields)) {
			foreach($fields as $k=>$v) {
				if(is_string($v)) {
					$v = trim($v);
					$v = preg_replace('/\s+/', ' ', $v);
				}
				$fields[$k] = $v;
			}
		}
		
		// si es text_uppercase convertimos a mayus
		if($this->text_uppercase) {
			if(!empty($fields)) {
				foreach($fields as $k=>$v) {
					if(is_string($v)) {
						$v = strtoupper($v);
					}
					$fields[$k] = $v;
				}
			}
		}
		
		// almacenas los datos
		if(!empty($fields)) {
			foreach($fields as $k=>$v) {
				if(array_key_exists($k, $this->field_data)) {
					$this->field_data[$k] = $v;
				}
				else {
					$this->field_data[$k] = null;
				}
			}
		}
		
		return $fields;
	}
	
	public function set($column, $value = NULL) {
		if(is_array($column)) {
			foreach($column as $k=>$v) {
				$this->set($k, $v);
			}
			return;
		}
		$column = strtolower($column);
		if(array_key_exists($column, $this->field_data)) {
			$this->field_data[$column] = $value;
		}
	}
	
	public function get($column) {
		if(array_key_exists($column, $this->field_data)) {
			return $this->field_data[$column];
		}
		return null;
	}
	
	public function get_var_session($var = NULL) {
		if($var == NULL) {
			return $this->session->all_userdata();
		}
		return $this->session->userdata($var);
	}
	
	/**
	 * Funciones magicas REVISAR
	 */
	/*
	public function __set($column, $value) {
		if(array_key_exists($column, $this->field_data)) {
			if(is_numeric($value)) {
				if(stripos($value, ".") !== false) {
					$value = floatval($value);
				}
				else {
					$value = intval($value);
				}
			}
			$this->field_data[$column] = $value;
		}
	}
	
	public function __get($column) {
		if(array_key_exists($column, $this->field_data)) {
			return $this->field_data[$column];
		}
		return null;
	}
	//*/
	
	/**
	 * Retorna los datos almacenamos de la consulta insert o update
	 */
	public function get_fields() {
		return $this->field_data;
	}
	
	public function clear_fields() {
		$columns = $this->get_columns();
		if( ! empty($columns)) {
			foreach($columns as $col) {
				$this->field_data[$col] = null;
			}
		}
	}
	
	public function insert($param = NULL, $auto_increment = TRUE, $return_pk = TRUE) {
		$fields 		= $this->prepare_data($param);
		if( in_array($this->get_full_table_name(), $this->tabla_audit)) {
			if($auto_increment){
				$name_pk	= array($this->get_pk());
				$pk_tabla 	= (!empty( $fields[$this->get_pk()] )) ? array($fields[$this->get_pk()]) : array();
			}else{
				$name_pk = $this->get_pk();
				$pk_tabla= array();
				foreach($name_pk as $k=>$v){
					$pk_tabla[] = $fields[$v];
				}
			}
			$controller = (!empty($param['controller'])) ? $param['controller'] : null;
			$accion 	= (!empty($param['accion'])) ? $param['accion'] : null;
			$new_value = $old_value= '';		
		}
		
		if($auto_increment) {
			// si auto increment, eliminamos las pk
			foreach($this->pk as $pk) {
				if(in_array($pk, $this->columns_autoinc) && array_key_exists($pk, $fields)) {
					unset($fields[$pk]);
				}
			}
		}

		// en insert no hay valor anterior ¿?
		if( in_array($this->get_full_table_name(), $this->tabla_audit) )// ESTO ES PARA COGER EL VALOR ANTERIOR
			$old_value = $this->after_before_value_audit($this, $pk_tabla, $name_pk);
		
		// insertamos los datos
		$this->db->insert($this->get_full_table_name(), $fields);
		
		// obtenemos el ultimo id insertado
		$insert_id = '';
		if($auto_increment && $return_pk) {
			$insert_id = $this->db->insert_id();
			$this->set($this->get_pk(false), $insert_id);
			
			$id_audit = array($insert_id);
		}else{
			if(isset($pk_tabla))
				$id_audit = $pk_tabla;
			else
				$id_audit = array();
		}
		/* registramos en la tabla auditoria */
		if( in_array($this->get_full_table_name(), $this->tabla_audit) )// ESTO ES PARA COGER EL VALOR NUEVO
			$new_value = $this->after_before_value_audit($this, $id_audit, $name_pk);
		if( in_array($this->get_full_table_name(), $this->tabla_audit) )//ESTO ES PARA INGRESAR YA EN LA TABLA AUDITORIA
			$this->insert_audit( $controller, $accion, $this,'' ,$id_audit, $old_value, $new_value, '',$name_pk);
		/* devolvemos el ultimo id ingresado */
		if($auto_increment && $return_pk) {
			return $insert_id;
		}
		
		return true;
	}
	
	public function update($param = NULL) {
		$fields = $this->prepare_data($param);
		
		if( in_array($this->get_full_table_name(), $this->tabla_audit)) {
			if(is_array($this->get_pk())){
				$name_pk	= $this->get_pk();
				$pk_tabla= array();
				foreach($name_pk as $k=>$v){
					$pk_tabla[] = $fields[$v];
				}
			}else{
				$name_pk	= array($this->get_pk());
				$pk_tabla 	= (!empty( $fields[$this->get_pk()] )) ? array($fields[$this->get_pk()]) : array();
			}
			$controller = (!empty($param['controller'])) ? $param['controller'] : null;
			$accion 	= (!empty($param['accion'])) ? $param['accion'] : null;
			$new_value = $old_value= '';		
		}
		
		$pks = array_fill_keys($this->pk, '0');
		$pks = array_intersect_key($fields, $pks);
		
		if( ! empty($pks)) {
			foreach($pks as $k=>$v) {
				if(array_key_exists($k, $fields)) {
					unset($fields[$k]);
				}
				$this->db->where($k, $v);
			}
			
			if( in_array($this->get_full_table_name(), $this->tabla_audit) )// ESTO ES PARA COGER EL VALOR ANTERIOR
				$old_value = $this->after_before_value_audit($this, $pk_tabla, $name_pk);
			
			$this->db->update($this->get_full_table_name(), $fields); 
			
			if( in_array($this->get_full_table_name(), $this->tabla_audit) )// ESTO ES PARA COGER EL VALOR NUEVO
				$new_value = $this->after_before_value_audit($this, $pk_tabla, $name_pk);
				
			if( in_array($this->get_full_table_name(), $this->tabla_audit) )//ESTO ES PARA INGRESAR YA EN LA TABLA AUDITORIA
				$this->insert_audit( $controller, $accion, $this,'' ,$pk_tabla, $old_value, $new_value, "", $name_pk );
		}
		
		return true;
	}
	
	public function delete($param) {
		$fields = $this->prepare_data($param);
		
		if(is_array($this->get_pk(false))){
			$name_pk = $this->get_pk(false);
			$pk_tabla= array();
			foreach($name_pk as $v){
				$pk_tabla[] = $fields[$v];
			}
		}else{
			if(isset($fields[$this->get_pk(false)])) {
				$name_pk	= array($this->get_pk(false));
				$pk_tabla 	= array($fields[$this->get_pk(false)]);
			}
		}

		$controller = (!empty($param['controller'])) ? $param['controller'] : null;
		$accion 	= (!empty($param['accion'])) ? $param['accion'] : null;
		$new_value = $old_value= '';
		
		if( in_array($this->get_full_table_name(), $this->tabla_audit) && isset($pk_tabla) )// ESTO ES PARA COGER EL VALOR ANTERIOR
			$old_value = $this->after_before_value_audit($this, $pk_tabla, $name_pk);
		
		$this->db->delete($this->get_full_table_name(), $fields); 
		
		if( in_array($this->get_full_table_name(), $this->tabla_audit) && isset($pk_tabla) )// ESTO ES PARA COGER EL VALOR NUEVO
			$new_value = $this->after_before_value_audit($this, $pk_tabla, $name_pk);
				
		if( in_array($this->get_full_table_name(), $this->tabla_audit) && isset($pk_tabla))//ESTO ES PARA INGRESAR YA EN LA TABLA AUDITORIA
			$this->insert_audit( $controller, $accion, $this,'' ,$pk_tabla, $old_value, $new_value , "", $name_pk);
		
		return true;
	}
	
	public function save($param = NULL, $auto_increment = TRUE, $return_pk = TRUE) {
		$fields = $this->prepare_data($param);
		
		$pks = array_fill_keys($this->pk, '0');
		$pks = array_intersect_key($fields, $pks);
		
		if(!empty($pks)) {
			$query = $this->db->get_where($this->get_full_table_name(), $pks);
			if($query->num_rows() < 1) {
				return $this->insert($param, $auto_increment, $return_pk);
			}
			
			return $this->update($param);
		}
		
		return $this->insert($param, $auto_increment, $return_pk);
	}
	
	public function exists($param, $strict = TRUE) {
		$fields = $this->prepare_data($param);
		
		$sql = "SELECT * FROM ".$this->get_full_table_name();
		
		$filters = array();
		$cols = array_keys($fields);
		
		foreach($cols as $col) {
			if($strict) {
				$filters[] = "UPPER(CAST($col AS TEXT)) LIKE UPPER('".$this->db->escape_like_str("".$fields[$col])."')";
			}
			else {
				$filters[] = "CAST($col AS TEXT) LIKE '".$this->db->escape_like_str("".$fields[$col])."'";
			}
		}
		
		if(count($filters) > 0) {
			$sql .= " WHERE " . implode(" AND ", $filters);
		}
		
		$query = $this->db->query($sql);
		
		return ($query->num_rows() >= 1);
	}
	
	public function after_before_value_audit($model,$key_val, $name_pk=array()){
		if(!empty($key_val) && !empty($name_pk)){
			// $sql = "SELECT tablita  valores FROM ".$model->get_schema().".".$model->get_table_name()." tablita WHERE ".$name_pk."='{$key_val}' ;";
			$sql = "SELECT tablita  valores FROM ".$model->get_schema().".".$model->get_table_name()." tablita ";
			foreach($name_pk as $k=>$v){
				if($k==0)
					$sql.=" WHERE {$v} = '{$key_val[$k]}'";
				else
					$sql.=" AND {$v} = '{$key_val[$k]}' ";
			}
			
			$query =$this->db->query($sql);
			$row = $query->row_array();
			return $row['valores'];
		}else{
			return null;
		}
	}
	
	public function insert_audit( $controller = '',$accion ='',$model, $coduser='', $pk_value=array(), $old_value="" , $new_value="",$ide='', $pk_tabla=array() ){
		if(!empty($old_value) && $accion!='eliminar' && !empty($accion)){
			$accion = 'editar';
		}

         // $fields_log['direccion_ip']  	= 	$_SERVER['REMOTE_ADDR'];
        $fields_log['direccion_ip']  	= 	$this->input->ip_address();
        $fields_log['fecha_registro'] 	= 	date("Y-m-d");
        $fields_log['hora_registro'] 	= 	date("H:i:s");
        $fields_log['controller']		= 	$controller;
        $fields_log['accion'] 			= 	$accion;
        $fields_log['esquema'] 		= 	$model->get_schema();
        $fields_log['tabla'] 				= 	$model->get_table_name();
        $fields_log['pk_tabla'] 		= 	implode(',',$pk_tabla);
        $fields_log['old_value'] 		= 	$old_value;
        $fields_log['new_value'] 		= 	(!empty($new_value)) ? $new_value : $this->after_before_value_audit($model,$pk_value,$pk_tabla);
        $fields_log['idusuario'] 		= 	(!empty($coduser)) ? $coduser : $this->get_var_session("idusuario");
        $fields_log['pk_value'] 		= 	implode(',',$pk_value);
        $fields_log['identificador'] 	= 	(!empty($ide)) ? $ide : RandomString();
        $fields_log['idsucursal'] 		= 	$this->get_var_session("idsucursal");
        $fields_log['estado'] 			= 	'A';
		if($old_value != $new_value)
			$this->db->insert('auditoria.tabla_log', $fields_log);
	}
}

?>