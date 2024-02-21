<?php

include_once "Controller.php";

class Asignarzonas extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Cartera de Credito");
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
		$data["zonas"] = $this->ListaZonas();
		$data["zona_x_cobrador"] = $this->ListaCobradores();
		$data["multi_zona"] = $this->get_param("multi_zona")?$this->get_param("multi_zona"):'N';
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
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

	public function save(){
		$fields = $this->input->post();

		$this->db->trans_start(); // inciamos transaccion
		
		$idsucursal = $this->get_var_session("idsucursal");
		$sql = "UPDATE cobranza.hoja_ruta SET estado='I' WHERE idsucursal='".$idsucursal."' ";
		//INACTIVO A TODAS LAS ASIGNACIONES DE LA SUCURSAL

		$estado = $this->db->query($sql);
		
		$this->load_model("hoja_ruta");
		
		if($estado){
			foreach($fields["idempleado"] as $key=>$val) {
				$data = $this->hoja_ruta->find(array("idempleado"=>$val, "idsucursal"=>$idsucursal, "idzona"=>$fields['idzona'][$key]));

				$data["idempleado"] 	= $val;
				$data["idsucursal"]     = $idsucursal;
				$data["idzona"] 		= $fields["idzona"][$key];
				$data["estado"] 		= 'A';
				
				$this->hoja_ruta->save($data,false);
			}
		}
		$this->db->trans_complete(); // finalizamos transaccion
		$this->response($fields);
	}

	public function ListaZonas(){
		$multi_zona = $this->get_param("multi_zona")?$this->get_param("multi_zona"):'N';
		$and_where ='';
		if($multi_zona=='N'){
			$and_where = " AND idzona NOT IN(SELECT idzona FROM cobranza.hoja_ruta WHERE idzona=zona.idzona) ";
		}
		$sql = "SELECT*FROM general.zona WHERE estado='A' {$and_where} ORDER BY zona";
		$query = $this->db->query($sql);
		
		$html ='<ul id="sortable_none" style="background:#f3f3f4;" class="sortable ui-sortable" data-class="ui-state-none" data-padre="0">';
		$html.='	<li class="sortable_none ui-state-disabled" style="">ZONAS SIN ASIGNAR';
		$html.='		<div class="pull-right">';
		$html.='			<i class="fa fa-sitemap fa-1x"></i>';
		$html.='		</div>';
		$html.='	</li>';
		foreach($query->result_array() as $key=>$value){
			$zona = strtoupper (trim($value['zona']));
			$html.='	<li class="ui-state-none draggable lista" class-parent="ui-state-none" style="font-size:8.5px !important;" data-text="'.$zona.'" data-zona="'.$value['idzona'].'">';
			$html.='		<i class="fa fa-map-marker inlista"></i>&nbsp;';
			$html.=			($zona);
			$html.='		<div style="display:none;">';
			$html.='			<input type="hidden" name="idzona[]"  value="'.$value['idzona'].'" class="idzona" />';
			$html.='			<input type="hidden" value="" class="idempleado"  />';
			$html.='		</div>';
			$html.= '		<div class="pull-right in_list" style="margin-top: -5px;display:none;">';
			$html.= '			<i class="fa fa-share-alt fa-1x"></i>';
			$html.='		</div>';
			$html.='	</li>';
		}
		$html.='</ul>';
		
		return $html;
	}

	public function ListaCobradores(){
		$html = '';
		$codtipoempleado_cobrador = $this->get_param("idrolcobrador")?$this->get_param("idrolcobrador"):'0';
		$sql = "SELECT usuario.*,usuario.idusuario idempleado FROM seguridad.usuario 
				WHERE idusuario IN (SELECT idusuario 
									FROM seguridad.acceso_empresa 
									WHERE estado='A' AND idtipoempleado='{$codtipoempleado_cobrador}' 
									AND idsucursal='{$this->get_var_session("idsucursal")}'  )";
		$query = $this->db->query($sql);
		
		$emplead = $query->result_array();
		
		$query = $this->db->query("	SELECT
									DISTINCT zona.idzona,zona.zona,zona.idubigeo,zona.estado
									,h.idempleado
									FROM cobranza.hoja_ruta h
									JOIN general.zona ON h.idzona=zona.idzona
									WHERE h.estado='A'
									");

		$detalle = $query->result_array();
		$col = 3;
		$style = ' style="background:#f3f3f4;" ';
		foreach($emplead as $k=>$v){
			$html.= '<div class="col-sm-'.$col.' content_all" >';
			$html.= '	<ul class="connectedSortable sortable sortable_connect ui-sortable" data-padre="1" data-cob="'.$v['idempleado'].'" '.$style.' >';
			$html.= '		<li class="ui-state-default-head ui-state-disabled" style="height:40px;">';
			$html.= '			<div class="pull-left"><i class="fa fa-user fa-1x"></i></div>';
			$html.= 			trim($v['nombres'].' '.$v['appat']);
			//$html.= 			trim($v['nombres'].' '.$v['appat'].' '.$v['apmat']);
			$html.='		</li>';
			$here	= $this->seleccion($detalle,$v['idempleado']);
			foreach($here as $key=>$value){
				$zona = strtoupper(($value['zona']));

				$html.= '	<li class="lista grabado" data-text="'.$zona.'" style="font-size:8.5px !important;">&nbsp;';
				$html.= 		$zona;
				$html.= '		<div class="pull-right" style="margin-top: -5px;">';
				$html.= '			<input type="hidden" name="idzona[]" value="'.$value['idzona'].'" class="idzona"  />';
				$html.= '			<input type="hidden" name="idempleado[]" value="'.$v['idempleado'].'" class="idempleado" />';
				//$html.= '			&nbsp;<i class="fa fa-trash-o cursor eliminar fa-2x"></i>';
				$html.= '			&nbsp;<i class="fa fa-share-alt fa-1x"></i>';
				$html.= '		</div>';
				$html.= '	</li>';
			}
			$html.= '	</ul>';
			$html.= '</div>';
		}
		return $html;
	}
	public function seleccion($datos,$id){
		$data = array();
		foreach($datos as $kk=>$vv){
			if($vv['idempleado']==$id){
				$data[]=$vv;
			}
		}	
		return $data;
	}
}
?>