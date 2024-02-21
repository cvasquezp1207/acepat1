<?php

include_once "Model.php";

class Caja_model extends Model {
	public function init() {
		$this->set_schema("caja");
	}
	
	// public function existsCajaHoy($idusuario) {

		// $sql = "SELECT * FROM caja.caja
				// WHERE estado = 'A' 
				// AND fecha_apertura::date = current_date
				// AND idusuario_apertura = '$idusuario' ";
		// $query = $this->db->query($sql);

		// return ( $query->num_rows() > 0 );
	// }

	// public function getCajaAnterior($idusuario) {
		// $sql = "SELECT * FROM caja.caja
				// WHERE estado = 'A' 
				// AND (abierto = 'S' OR tienearqueo = 'N')
				// AND fecha_apertura::date < current_date
				// AND idusuario_apertura = '$idusuario'
				// ORDER BY  fecha_apertura
				// limit 1";
		// $query = $this->db->query($sql);

		// if ( $query->num_rows() > 0 ) {
			// return $query->result_array();
		// }
		
		// return false;
	// }

	// public function data_cierre($idusuario){
		// $sql = "SELECT * FROM caja.cierrecaja WHERE idcaja = '".$this->cajita($idusuario)."' AND estado='A'";
		// $query = $this->db->query($sql);
		// return $query->result_array();
	// }
	
	// public function ingresoCaja($idconceptomovimiento, $monto, $descripcion, $referencia, $tabla, $codtabla,$idmoneda='1' ,$tipocambio='1.00' ,$tipodocumento ,$idcliente, $serie, $numero) {
		// return $this->saveCaja($idconceptomovimiento, $monto, $descripcion, $referencia, $tabla, $codtabla,$idmoneda, $tipocambio ,$tipodocumento);
	// }
	
	// public function egresoCaja ($idconceptomovimiento, $monto, $descripcion, $referencia, $tabla, $codtabla,$idmoneda='1' ,$tipocambio='1.00' ,$tipodocumento ,$idcliente, $serie, $numero) {
		// return $this->saveCaja($idconceptomovimiento, $monto, $descripcion, $referencia, $tabla, $codtabla,$idmoneda, $tipocambio ,$tipodocumento);
	// }
	// private function saveCaja($tipo, $monto, $descripcion, $referencia, $tabla, $codtabla = null,$monenda='1',$tipocambio='1.00',$tipodocumento='') {
		// $idusuario = (empty($this->idusuario)) ? $this->get_var_session("idusuario") : $this->idusuario;
	
		// $caja = $this->getCajaActive('',$this->get_var_session("idusuario"));
		// $data_cierre = $this->data_cierre($this->get_var_session("idusuario"));
		
		// if($caja !== false) {
			// $monto = abs(doubleval($monto));
			
			// if($tipo == 'S' && $mon=='1') {
				// if($data_cierre[0]['monto_cierre'] >= $monto) {
					// $monto = $monto * (-1);
				// }else {
					// $this->exception('El monto total supera al saldo disponible en caja');
					// return false;
				// }
			// }else if($tipo == 'S' && $mon!='1') {$monto = $monto * (-1);}
			
			// $fecha = ($this->current_caja == true) ? date('Y-m-d') : $this->fecha_caja;
			// if(empty($fecha)) {
				// $fecha = date('Y-m-d');
			// }
			

			// $data2["idcaja"]				=  $caja[0]['idcaja'];
			// $data2["fecha"] 				=  date("Y-m-d");
			// $data2["hora"] 					=  date("H:m:s");
			// $data2["idconceptomovimiento"] 	=  $tipo;
			// $data2["monto"] 				=  $monto;
			// $data2["tabla"] 				=  $tabla;
			// $data2["idtabla"] 				=  $idtabla;
			// $data2["descripcion"] 			=  $descripcion;
			// $data2["idusuario"] 			=  $idusuario;
			// $data2["idmoneda"] 				=  $monenda;
			// $data2["idtipodocumento"] 		=  $tipodocumento;//
			// $data2["idcliente"] 			=  $idcliente;// 
			// $data2["serie"] 				=  $serie;// 
			// $data2["numero"] 				=  $numero;// 
			// $data2["tipocambio"] 			=  $tipocambio;// 
			// $data2["montoconvertido"] 		=  $monto*$tipocambio;// 
			// $data2["referencia"] 			=  $referencia;// 
			// $data2["estado"] 				=  "A";// 

			// $estado = $this->detalle_caja_insert($data2);
			
			// return $estado;
		// }else {
			// $this->exception('La caja aun no se ha creado o ya se encuentra cerrado');
		// }
		
		// return false;
	// }
	
	// protected function detalle_caja_insert($data) {
		// $this->db->insert('caja.detalle_caja', $data);
		
		// return true;
	// }
}
?>