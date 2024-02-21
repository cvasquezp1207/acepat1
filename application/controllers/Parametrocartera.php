<?php

include_once "Controller.php";

class Parametrocartera extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Parametro para Carteras");
		$this->set_subtitle("Lista de Hoja Ruta");
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

		$data["controller"] = $this->controller;
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	public function inicio($data = null) {
		if(!is_array($data)) {
			$data = array();
		}

		$data["controller"] = $this->controller;
		$data["grid"] = $this->grid();
		$data["listado"] = $this->Armar_asignacion();
		$data["botones"] = $this->get_buttons('default');
		$data["botones_sub"] = $this->get_buttons('personalizado');
		
		return $this->load->view($this->controller."/inicio", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return null;
	}
	
	public function grid(){
		// $this->load_model('view_perfil');
		$this->load_model($this->controller);
		$this->load->library('datatables');

		$this->datatables->setModel($this->parametrocartera);

		$this->datatables->setIndexColumn("idparametrocartera");
		$this->datatables->where('estado', '=', 'A');
		$this->datatables->setColumns(array('idparametrocartera','descripcion'));
		//$this->datatables->setColumns(array('idparametrocartera','descripcion','tipo'));

		// columnas de la tabla, si no se envia este parametro, se muestra el
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			array('Id','10%')
			,array('Descripci&oacute;n','80%')
			//,array('Tipo','10%')
		);

		$table = $this->datatables->createTable($columnasName);

		$script = "<script>".$this->datatables->createScript()."</script>";
		
		$this->js($script, false);

		return $table;
	}

	public function index($tpl = "",$ir_a="inicio") {
		if($ir_a=="inicio")
			$data = array(
				"menu_title" => $this->menu_title
				,"menu_subtitle" => $this->menu_subtitle
				,"content" => $this->inicio()
				,"with_tabs" => $this->with_tabs
			);
		else
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
	
	public function nuevo(){
		$this->set_title("Registrar Parametro Cartera");
		$this->set_subtitle("");
		$data['titulo_form'] = "Nuevo Parametro Cartera";
		$this->set_content($this->form($data));
		$this->index("content","form");
	}
	
	public function guardar(){
		$this->load_model($this->controller);
		
		$fields = $this->input->post();
		$fields['estado'] = 'A';
		if(empty($fields["idparametrocartera"])) {
			$this->parametrocartera->insert($fields);
		}
		else {
			$this->parametrocartera->update($fields);
		}
		
		$this->response($this->parametrocartera->get_fields());
	}
	
	public function editar($id){
		$this->load_model($this->controller);

		$data = $this->parametrocartera->find($id);
		
		$data['titulo_form'] = "Modificar Parametro Cartera";
		// $this->set_title("Modificar Parametro Cartera");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content","form");
	}
	
	public function eliminar($id) {
		$this->load_model($this->controller);
		// cambiamos de estado
		$fields['idparametrocartera'] = $id;
		$fields['estado'] = "I";
		$this->parametrocartera->update($fields);
		
		$this->response($fields);
	}
	
	public function save_detalle(){
		// $this->load_model($this->controller);
		$this->load_model("detalle_parametrocartera");
		
		$fields = $this->input->post();
		// print_r($fields);
		$this->db->query("DELETE FROM cobranza.detalle_parametrocartera WHERE idsucursal='{$this->get_var_session("idsucursal")}' ");
		foreach($fields["idparametrocartera"] as $key=>$val) {
			if(!empty($val)){
				$data1["idsucursal"] 		 = $this->get_var_session("idsucursal");
				$data1["id_estado_credito"]  = $fields["id_estado_credito"][$key];
				$data1["idparametrocartera"] = $val;
				$data1["estado"] 	= "A";
				
				$this->detalle_parametrocartera->save($data1,false);
			}
		}
		
		$this->response($fields);
	} 
	
	public function Armar_asignacion(){
		$query = $this->db->query("SELECT c.id_estado_credito ,e.descripcion estadocredito
						FROM credito.credito c
						JOIN credito.estado_credito e ON e.id_estado_credito=c.id_estado_credito
						WHERE c.idsucursal='{$this->get_var_session("idsucursal")}' 
						AND c.estado!='I' AND c.pagado='N'
						GROUP BY c.id_estado_credito,estadocredito
						ORDER BY estadocredito;");
						
		$array_estados = $query->result_array();
		
		$query = $this->db->query("SELECT idparametrocartera,descripcion FROM cobranza.parametrocartera WHERE estado='A' ORDER BY descripcion");
		$array_head = $query->result_array();

		$query = $this->db->query("	SELECT
						dt.iddetalle_parametrocartera
						,e.descripcion estadocredito
						,dt.idparametrocartera
						,dt.id_estado_credito
						FROM cobranza.detalle_parametrocartera dt
						JOIN cobranza.parametrocartera p ON p.idparametrocartera=dt.idparametrocartera
						JOIN credito.estado_credito e ON e.id_estado_credito=dt.id_estado_credito
						WHERE dt.idsucursal='{$this->get_var_session("idsucursal")}' AND dt.estado='A' AND p.estado='A'

						ORDER BY idparametrocartera,estadocredito");
		$array_condatos = $query->result_array();				
		
		
		$html = '';
		
		if(count($array_condatos)<=0){
			$html.='<div class="col-sm-4" style="">';
			$html.='	<div class="row">';
			$html.='		<ul id="sortable_none" class="connectedSortable sortable sortable_connect" data-class="ui-state-none" data-padre="0">';
			$html.='			<li class="sortable_none ui-state-disabled" style="">';
			$html.='				SIN ASIGNAR';
			$html.='			</li>';
			$array_hijos = $this->ListaHijos('',$array_estados);
			foreach($array_hijos as $kk =>$vv){
				$html.='		<li class="ui-state-none ui-not-asig" class-parent="ui-state-none">';
				$html.='			<input type="hidden" class="id_estado_credito" 				name="id_estado_credito[]" 			  id="id_estado_credito'.$kk.'" value="'.$vv['cod'].'">';
				$html.='			<input type="hidden" class="iddetalle_parametrocartera" 	name="iddetalle_parametrocartera[]" id="iddetalle_parametrocartera'.$kk.'"  >';
				$html.='			<input type="hidden" class="idparametrocartera"			name="idparametrocartera[]" id="idparametrocartera'.$kk.'"  >';
				$html.=$vv['estadocredito'];
				$html.='		</li>';
			}
			$html.='		</ul>';
			$html.='	</div>';
			$html.='</div>';
		}else if(count($array_condatos)!=count($array_estados)){
				$html.='<div class="col-sm-4" style="">';
				$html.='	<div class="row">';
				$html.='		<ul id="sortable_none" class="connectedSortable sortable sortable_connect" data-class="ui-state-none" data-padre="0">';
				$html.='			<li class="sortable_none ui-state-disabled" style="">';
				$html.='				SIN ASIGNAR';
				$html.='			</li>';

				$array_hijos = $this->ListaNot();
				foreach($array_hijos as $kk =>$vv){
					$html.='		<li class="ui-state-none ui-not-asig" class-parent="ui-state-none">';
					$html.='			<input type="hidden" class="id_estado_credito" 				name="id_estado_credito[]" 			  id="id_estado_credito'.$kk.'" value="'.$vv['cod'].'">';
					$html.='			<input type="hidden" class="iddetalle_parametrocartera" 	name="iddetalle_parametrocartera[]" id="iddetalle_parametrocartera'.$kk.'"  >';
					$html.='			<input type="hidden" class="idparametrocartera"			name="idparametrocartera[]" id="idparametrocartera'.$kk.'"  >';
					$html.=$vv['estadocredito'];
					$html.='		</li>';
				}
				$html.='		</ul>';
				$html.='	</div>';
				$html.='</div>';
		}
		
		foreach($array_head as $key => $val){
			$clase = 'ui-state-defaultP';
			if(($key+1)%2==0){
				$clase = 'ui-state-second';
			}
			$html.='<div class="col-sm-4" style="">';
			$html.='	<div class="">';
			$html.='		<ul id="" class="connectedSortable sortable sortable_connect" data-class="'.$clase.'" data-padre="'.$val['idparametrocartera'].'">';
			$html.='			<li class="ui-state-default-head ui-state-disabled" style="">';
			$html.='				'.ucwords(strtolower($val['descripcion']));
			$html.='			</li>';
			
			$array_hijos = $this->ListaHijos($val['idparametrocartera'],$array_condatos);
			
			foreach($array_hijos as $kk =>$vv){
				$html.='		<li class="'.$clase.' ui-yes-asign" class-parent="'.$clase.'" >';
				$html.='			<input type="hidden" class="id_estado_credito" 				name="id_estado_credito[]" 			  id="id_estado_credito'.$kk.$key.'" value="'.$vv['cod'].'">';
				$html.='			<input type="hidden" class="iddetalle_parametrocartera" 	name="iddetalle_parametrocartera[]" id="iddetalle_parametrocartera'.$kk.$key.'" value="'.$vv['pkdetalle'].'" >';
				$html.='			<input type="hidden" class="idparametrocartera"			name="idparametrocartera[]" id="idparametrocartera'.$kk.$key.'" value="'.$val['idparametrocartera'].'" >';
				$html.=				$vv['estadocredito'];
				$html.='		</li>';
			}
			$html.='		</ul>';
			$html.='	</div>';
			$html.='</div>';
		}
		
		return $html;
	}
	
	public function ListaHijos($idparametrocartera,$datos,$iguales = true){
		$new_array =array();
		if($iguales){
			if(!empty($idparametrocartera)){
				foreach($datos as $key=>$val){
					if($idparametrocartera == $val['idparametrocartera'])
						$new_array[] = array('cod'=>$val['id_estado_credito'],'estadocredito'=>$val['estadocredito'],'pkdetalle'=>$val['iddetalle_parametrocartera']);
				}			
			}else{
				foreach($datos as $key=>$val){
					$new_array[] = array('cod'=>$val['id_estado_credito'],'estadocredito'=>$val['estadocredito']);
				}
			}
		}
		return $new_array;
	}
	
	public function ListaNot(){
		$arr_new = array();
		
		$objeto=$this->db->query("	SELECT  c.id_estado_credito ,e.descripcion estadocredito
									FROM credito.credito c
									JOIN credito.estado_credito e ON e.id_estado_credito=c.id_estado_credito
									WHERE c.idsucursal='{$this->get_var_session("idsucursal")}' 
									AND c.estado!='I' c.pagado='N'
									AND c.id_estado_credito NOT IN (
										SELECT 
									
											dt.id_estado_credito
											FROM cobranza.detalle_parametrocartera dt
											JOIN cobranza.parametrocartera p ON p.idparametrocartera=dt.idparametrocartera
											JOIN credito.estado_credito e ON e.id_estado_credito=dt.id_estado_credito
											WHERE dt.idsucursal='{$this->get_var_session("idsucursal")}' AND dt.estado='A' AND p.estado='A'
									)
									GROUP BY c.id_estado_credito,estadocredito
									ORDER BY estadocredito");
		
		$data = $objeto->result_array();
		
		foreach($data as $key => $v){
			$arr_new[]=array("cod"=>$v['codestadocredito'],"estadocredito"=>$v['estadocredito']);			
		}

		return $arr_new;

	}
}
?>