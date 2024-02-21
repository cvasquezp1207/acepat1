<?php

include_once "Controller.php";

class Modulo extends Controller {
	
	// public function __construct() {
	// 	echo FCPATH;exit;
	// }

	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Modulo");
		$this->set_subtitle("Lista de modulos");
	}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		$this->js('form/'.$this->controller.'/index');
	}
	
	public function get_icons() {
		// $str = @file_get_contents(base_url("app/font-awesome/css/font-awesome.css"));
		$str = @file_get_contents(FCPATH."app/font-awesome/css/font-awesome.css");
		
		$result = null;
		if(preg_match_all("/.fa-([a-z\-]+)\:before/", $str, $result)) {
			$result = $result[1];
		}
		
		return $result;
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null, $prefix = "", $modal=false) {
		$this->load->library('combobox');
		
		$data["lista"] = $this->Armarlista();
		$data["sistemas"] = $this->Loadsistemas();

		$data["controller"] = $this->controller;
		$data["icons"] = $this->get_icons();
		$data["boton"] = $this->ListBotones();
		$data["botones"] = $this->get_buttons('default');
		$data["boton_all"] = $this->bandera_mod();

		$this->load_controller("boton");
		// $this->boton_controller->load = $this->load;
		// $this->boton_controller->db = $this->db;
		// $this->boton_controller->session = $this->session;
		// $this->boton_controller->combobox = $this->combobox;
		
		$data["form_boton"] = $this->boton_controller->form(null, "bot_", true);
		$this->js('form/boton/modal');
		
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	public function bandera_mod(){
		$query = $this->db->query("SELECT count(*) FROM seguridad.detalle_boton WHERE idmodulo='".$this->_get_menu('menu_c')."' AND estado='A'");
		
		/* $menu_array = $this->get_var_session();
		$id_modulo = 0;
		if(!empty($menu_array['access_menu']))
			if(!empty($menu_array['modulo']))
				if(!empty($menu_array['menu_c']))
					$id_modulo = $menu_array['menu_c'];
			
		$query = $this->db->query("SELECT count(*) FROM seguridad.detalle_boton WHERE idmodulo='{$id_modulo}' AND estado='A' ");
		 */
		$cant = 0;
		foreach($query->row() as $k=>$v){
			$cant = $v;
		}
		
		return $cant;
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
	
	public function Loadsistemas(){
		$sql = "SELECT
				s.idsistema
				,s.descripcion
				,s.abreviatura
				,s.image
				,(SELECT count(*) FROM seguridad.modulo m WHERE m.idsistema=s.idsistema) cant_hijos
				FROM seguridad.sistema s
				WHERE s.estado='A' 
				ORDER BY cant_hijos DESC";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	public function LoadPadre(){
		$sql = "SELECT
				idmodulo
				,descripcion
				,abreviatura
				,url
				,icono
				,idsistema
				,idpadre
				,orden
				FROM seguridad.modulo s
				WHERE s.estado='A' --AND idpadre=0
				ORDER BY orden,descripcion";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		
		$this->modulo->text_uppercase(false);
		
		$fields = $this->input->post();
		
		$fields['estado'] = "A";
		
		$this->db->trans_start();
		
		if(empty($fields["idmodulo"])) {
			$fields['fecha_registro'] = date("Y-m-d");
			$idmodulo = $this->modulo->insert($fields);
		}else {
			$idmodulo = $fields["idmodulo"];
			$this->modulo->update($fields);
		}
		
		$this->load_model("detalle_boton");
		
		// $sql = "UPDATE seguridad.detalle_boton SET estado='I' WHERE idmodulo='$idmodulo'";//
		$sql = "DELETE FROM  seguridad.detalle_boton WHERE idmodulo='$idmodulo'";//
		$estado = $this->db->query($sql);
		// echo $sql;
		if(!empty($fields["idboton"]))
			foreach($fields["idboton"] as $key=>$val) {
				// $data = $this->detalle_boton->find(array("idmodulo"=>$idmodulo, "idboton"=>$val));
				
				$data["idmodulo"] 	= $idmodulo;
				$data["idboton"]  	= $val;
				$data["orden"] 		= $key + 1;
				$data["estado"] 	= 'A';
				$this->detalle_boton->save($data,false);
			}
		$this->db->trans_complete();
		$this->response($fields);
	}
	
	public function save_detail(){
		$this->load_model($this->controller);
		
		$fields = $this->input->post();
		
		foreach($fields["idmodulo"] as $key=>$val) {
			$data1["idmodulo"] = $val;
			$data1["idsistema"] = $fields["idsistema"][$key];
			$data1["idpadre"] 	= $fields["idpadre"][$key];
			// $data1["orden"] 	= ($key + 1);
			$data1["orden"] 	= $fields["orden"][$key];
			
			$this->modulo->update($data1);
		}
		
		$this->response($fields);
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		
		$fields = $this->modulo->find($id);
		$fields['estado'] = "I";
		$this->modulo->update($fields);
		
		$this->response($fields);
	}
	
	public function get($id) {
		$this->load_model($this->controller);
		$fields = $this->modulo->find($id);
		$this->response($fields);
	}
	
	public function get_modulos_html() {
		$this->db->where("idsistema", $this->input->post("idsistema"));
		$this->db->where("idpadre", $this->input->post("idmodulo"));
		$this->db->where("estado", "A");
		$this->db->order_by("orden");
		$query = $this->db->get("seguridad.modulo");
		if($query->num_rows() > 0) {
			$html = "";
			foreach($query->result_array() as $val) {
				$icon = '';
				if(!empty($val['icono'])) {
					$icon = '<i class="fa '.$val['icono'].'"></i>';
				}
				$html .= '<tr data-idmodulo="'.$val["idmodulo"].'">';
				$html .= '<td>'.$val["descripcion"].'</td>';
				$html .= '<td>'.$val["url"].'</td>';
				$html .= '<td>'.$icon.'</td>';
				$html .= '<td>'.$val["orden"].'</td>';
				$html .= '</tr>';
			}
			$this->response($html);
			return;
		}
		
		$this->response("<tr class='no_active'><td colspan='4'>No se han registrado modulos para este sistema.</td></tr>");
	}
	
	public function Listamodulo(){
		$query = $this->db
			->select('idmodulo, descripcion')
			->where("idsistema", $this->input->post("idsistema"))
			->where("idpadre", $this->input->post("idmodulo"))
			->where("estado", "A")
			->order_by("orden")
			->get("seguridad.modulo");
					
		return $this->response($query->result_array());
	}
	
	public function Armarlista(){
		$sistemas = $this->Loadsistemas();
		$modulo = $this->Loadpadre();
		$permisos = $this->get_permisos();
		$html = "";
		$columna = 4;
		foreach($sistemas as $k=>$v){
			$html.= '<div class="col-sm-'.$columna.' content_all">';
			$html.=	'	<div class="ibox">';
			$html.=	'		<div class="ibox-content" style="">';			
			$html.=	'			<div class="sistema manejable" data-modulo="'.$v['idsistema'].'" data-type="system" data-name="'.$v['descripcion'].'" data-father="0" data-icono="'.$v['image'].'" data-level=1>';
			$html.= '				<i class="fa '.$v["image"].' fa-2x"></i>&nbsp;&nbsp;&nbsp;'.$v['abreviatura'];
			
			// $html.= '				<div class="pull-right">';
			// $html.=	'					<div class="tooltip-demo" >';
			// $html.=	'						<button type="button" class="btn btn_nuevo_m fa fa-file-o btn-xs" data-toggle="tooltip" data-placement="top" title="Nuevo Modulo en el Sistema Seguridad"></button>';
			// $html.=	'					</div>';
			// $html.=	'				</div>';
			$html.= '			</div>';
			
			$array_padre = $this->armar_datos($modulo,$v['idsistema'],0,null);
			
			$height = '300px';
			if(count($array_padre)<1){
				// $height = '60px';
			}
			
			$html.= '			<div class="" style="border-left:2px dashed #f3f3f4;border-right:2px dashed #f3f3f4;border-bottom:2px dashed #f3f3f4; height:'.$height.' ; max-height:350px ; overflow-y:auto;">';
				$html.=	'			<ul class="uk-nestable " data-father-super = "'.$v['idsistema'].'">';
				if(count($array_padre)>0){
					foreach($array_padre as $kk => $vv){
						$html.= '		<li class="uk-nestable-item">';
						$html.= '			<div class="uk-nestable-panel manejable" data-modulo="'.$vv['idmodulo'].'" data-type="module" data-name="'.$vv['descripcion'].'" data-icono="'.$vv['icono'].'" data-system="'.$v['descripcion'].'" data-idsystem="'.$v['idsistema'].'" data-level=2 icon-system="'.$v['image'].'" >';
						$html.=	'				<i class="uk-nestable-handle fa '.$vv['icono'].'" style="font-size:15px;"></i>&nbsp;&nbsp;'.$vv['descripcion'];
						$html.=	'				<input name="idmodulo[]"  class="idmodulo" 	value="'.$vv['idmodulo'].'" type="hidden">';
						$html.=	'				<input name="idsistema[]" class="idsistema" value="'.$v['idsistema'].'" type="hidden">';
						$html.=	'				<input name="idpadre[]"   class="idpadre"   value="'.$vv['idpadre'].'" type="hidden">';
						$html.=	'				<input name="orden[]"   class="orden"   value="'.$vv['orden'].'" type="hidden">';
						$html.=	'			</div>';
						
						$array_hijo = $this->armar_datos($modulo, $v['idsistema'], $vv['idmodulo'],$vv['idmodulo']);
						if(count($array_hijo)>0){
							$html.=	'		<ul class="uk-nestable-list">';
							foreach($array_hijo as $key => $value){
								$html.=	'		<li class="uk-nestable-item">';
								$html.=	'			<div class="uk-nestable-panel manejable" data-modulo="'.$value['idmodulo'].'" data-type="module"  data-padre="'.$vv['icono'].'"  data-name="'.$value['descripcion'].'" data-icono="'.$value['icono'].'" data-idsystem="'.$v['idsistema'].'" data-system="'.$v['descripcion'].'" data-father="'.$vv['descripcion'].'" data-level=3 icon-system="'.$v['image'].'" >';
								$html.=	'				<i class="uk-nestable-handle fa '.$value['icono'].'" ></i>&nbsp;&nbsp;'.$value['descripcion'];
								$html.=	'				<input name="idmodulo[]" class="idmodulo" value="'.$value['idmodulo'].'" type="hidden">';
								$html.=	'				<input name="idsistema[]" class="idsistema" value="'.$value['idsistema'].'" type="hidden">';
								$html.=	'				<input name="idpadre[]"   class="idpadre"   value="'.$value['idpadre'].'" type="hidden">';
								$html.=	'				<input name="orden[]"   class="orden"   value="'.$value['orden'].'" type="hidden">';
								$html.=	'			</div>';
								$html.=	'		</li>';
							}							
							$html.=	'		</ul>';
						}
						$html.=	'			</li>';
					}
				}else{
					$html.= '			<li class="uk-nestable-item" style="height:3px !important;">&nbsp;</li>';
				}
				$html.=	'			</ul>';
			$html.=	"			</div>";
			$html.=	"		</div>";
			$html.=	"	</div>";
			$html.= "</div>";
		}
		
		return $html;
	}
	
	public function armar_datos($datos,$id,$idpadre,$idmodulo = null){
		/*
		$datos 		= 	array modulos
		$id 		=	id sistema
		$idpadre	=	id padre
		$idmodulo = id modulo
		*/
		
		$new_array=array();
		foreach($datos as $kkk=>$vvv){
			if(empty($idpadre) && empty($idmodulo)){
				if( $vvv['idsistema']==$id && $idpadre == $vvv['idpadre']){
					$new_array[]=$vvv;
				}				
			}else{
				if( $vvv['idsistema']==$id && $idmodulo == $vvv['idpadre']){
					$new_array[]=$vvv;
				}				
			}
		}
		
		return $new_array;
	}
	
	public function ListBotones(){
		$fields = $this->input->post();
		$array_no_boton = array();
		
		$sql = "SELECT * FROM seguridad.view_boton WHERE estado='A'	";
		if(!empty($fields['idboton'])){
			foreach($fields['idboton'] as $val){
				$array_no_boton[]=$val;
			}
			
			if(!empty($array_no_boton))
				$sql.=" AND idboton  NOT IN ('" . implode("','", $array_no_boton) . "') ";
		}
		$query = $this->db->query($sql);
		// return $query->result_array();
		
		$html ='';
		foreach($query->result_array() as $k=>$v){
			$html.='<li data-value="'.$v['idboton'].'"><a class="here_boton" data-icon="'.$v['icono'].'" style="cursor:pointer">'.$v['boton'].'</a></li>';
		}
		
		return $this->response($html);
	}
	
	public function ListDetalleBoton(){
		$fields = $this->input->post();

		if (empty($fields['idmodulo'])) {
			$sql = "SELECT*FROM seguridad.boton WHERE estado='A'";
		}else{
			$sql = "SELECT d.*,b.descripcion,b.icono,boton FROM seguridad.detalle_boton d JOIN seguridad.view_boton b ON b.idboton=d.idboton WHERE d.estado='A'	AND d.idmodulo='{$fields['idmodulo']}'";
		}
		$query = $this->db->query($sql);
		
		$html ='';
		foreach($query->result_array() as $k=>$v){
			$html.= '<tr>';
			$html.= '	<td style="padding:3px;">'.'<button style="width:100%;text-align:left;" type="button" class="btn fa '.$v['icono'].'" >&nbsp;&nbsp;'.$v['descripcion'].'</button>'.'</td>';
			$html.= '	<td style="padding:3px;">';
			$html.= '		<button type="button" class="btn delete_boton btn-danger fa fa-times"></button>';
			$html.= '		<input type="hidden" name="idboton[]" value="'.$v['idboton'].'">';
			$html.= '	</td>';
			$html.= '</tr>';			
		}
		
		return $this->response($html);
	}
}
?>