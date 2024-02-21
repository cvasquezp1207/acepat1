<?php

include_once "Controller.php";

class Acceso extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Accesos");
		$this->set_subtitle("Lista de Accesos");
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
	public function form() {
		$this->load->library('combobox');

		//Sucursal
		$idsucursal_session = $this->get_var_session("idsucursal");
		
		$query = $this->db->select('idempresa, descripcion')
			->where("estado", "A")
			->where("idempresa", $idsucursal_session)
			->get("seguridad.empresa");
		$empresas = $query->result_array();
		
		$query = $this->db->select('idsucursal, descripcion,idempresa')
			->where("estado", "A")
			->get("seguridad.sucursal");
		$suc = $query->result_array();
		
		$combo_suc = "<select id='idsucursal' name='idsucursal' class='form-control input-sm sn_css' data-plugin='selectpicker'>";
		$combo_suc.="<option value=''>Seleccione...</option>";
		foreach($empresas as $key=>$val){
			$sucurs = $this->reordenar($suc,array('idempresa'=>$val['idempresa'])) ;
			
			$combo_suc.="<optgroup label='{$val['descripcion']}' >";
			foreach($sucurs as $k=>$v){
				$combo_suc.="<option value='{$v['idsucursal']}'>{$v['descripcion']}</option>";
			}
			$combo_suc.="</optgroup>";
		}
		$combo_suc.="</select>";
		$data["sucursal"] = $combo_suc;
		
		
		//perfil
		$query = $this->db->select('idperfil, descripcion')
			->where("estado", "A")
			->get("seguridad.perfil");
		$this->combobox->removeAllItems();
		$this->combobox->setAttr("id", "idperfil");
		$this->combobox->setAttr("name", "idperfil");
		$this->combobox->setAttr("class", "form-control ");
		$this->combobox->setAttr("required", "");
		$this->combobox->addItem('','Seleccione...');
		$this->combobox->addItem($query->result_array());
		$data["perfil"] = $this->combobox->getObject();

		
		$data["controller"] = $this->controller;
		$data["prefix"] = '';
		$this->js("plugins/jsTree/jstree.min");
		$this->css('plugins/jsTree/style.min');

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
	

	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model('acceso');
		$this->load_model("acceso_boton");

		$fields = $this->input->post();
		$fields['estado'] = "A";
		
		$sql = "SELECT idmodulo,idpadre FROM seguridad.modulo WHERE  idsistema='{$fields['idsistema']}' ORDER BY idpadre,idmodulo;";
		$query = $this->db->query($sql);
		$modulos_array = $query->result_array();
		$modulos_padre=array();
		$this->db->trans_start();
		foreach($modulos_array as $key=>$val) {
			$sql = "DELETE FROM seguridad.acceso WHERE idmodulo='{$val['idmodulo']}' AND idperfil='".$fields["idperfil"]."' AND idsucursal='".$fields["idsucursal"]."'; ";
			$this->db->query($sql);
			
			$sql = "DELETE FROM seguridad.acceso_boton WHERE idmodulo='{$val['idmodulo']}' AND idperfil='{$fields["idperfil"]}' AND idsucursal='{$fields['idsucursal']}'; ";
			$this->db->query($sql);
			
			$data["idmodulo"]   = $val['idmodulo'];
			$data["idperfil"]   = $fields["idperfil"];
			$data["idsucursal"] = $fields["idsucursal"];
			$data["acceder"]    = 1;
			if(!empty($fields["idboton".$val['idmodulo']])){//HAY ACCESO AL BOTON
				foreach($fields["idboton".$val['idmodulo']] as $k=>$v){
					$data1 = $this->acceso_boton->find(array("idmodulo"=>$data["idmodulo"], "idperfil"=>$data["idperfil"] , "idsucursal"=>$data["idsucursal"], "idboton"=>$v));
					$data1["idmodulo"]   = $data["idmodulo"];
					$data1["idperfil"]   = $data["idperfil"];
					$data1["idsucursal"] = $data["idsucursal"];
					$data1["idboton"]    = $v;
					$data1["estado"]     = 'A';
					$data1["valor"]      = 1;

					$this->acceso_boton->save($data1,false);
				}
				//if( in_array($val["idmodulo"], $fields['idmodulo']) ){
					$this->acceso->save($data,false);//INSERTAMOS EL MODULO HIJO
					$modulos_padre[]=$val["idpadre"];					
				//}
			}else{//Todos los modulos que no tienen boton pero se los esta dando acceso
				if(!empty($fields['idmodulo']))
					if( in_array($val["idmodulo"], $fields['idmodulo']) ){
						$modulos_padre[]=$val["idmodulo"];
					}
			}
		}

		$mod_padre = array_unique($modulos_padre);
		foreach($mod_padre as $key=>$val){
			$data["idmodulo"]   = $val;
			$data["idperfil"]   = $fields["idperfil"];
			$data["idsucursal"] = $fields["idsucursal"];
			$data["acceder"]    = 1;
			$this->acceso->save($data,false);//INSERTAMOS EL MODULO PADRE
		}
		
		$this->db->trans_complete();
		$this->response($fields);
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
	
	public function armar_boton($datos,$id){
		$new_array=array();
		foreach($datos as $kkk=>$vvv){
			if( $vvv['idmodulo']==$id ){
				$new_array[]=$vvv;
			}
		}
		
		return $new_array;
	}
	
	public function reordenar($datos, $filtro=array()){
		$new_array=array();
		foreach($datos as $kkk=>$vvv){
			$bval = true;
			foreach($filtro as $k=>$v){
				if($vvv[$k]!=$v){
					$bval = false;
					break;
				}
			}
			
			if($bval)
				$new_array[]=$vvv;
		}
		return $new_array;
	}
}
?>