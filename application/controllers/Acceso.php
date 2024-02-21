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
		$idempresa_session = $this->get_var_session("idempresa");
		//Aqui ver si es super admin para poder dar accesos a las demas sucursales y empresa
		$query = $this->db->select('idempresa, descripcion')
			->where("estado", "A")
			// ->where("idempresa", $idempresa_session)
			->get("seguridad.empresa");
		$empresas = $query->result_array();
		
		$query = $this->db->select('idsucursal, descripcion,idempresa')
			->where("estado", "A")
			// ->where("idsucursal", $idsucursal_session)
			->get("seguridad.sucursal");
		$suc = $query->result_array();
		
		$combo_suc = "<select id='idsucursal' name='idsucursal' class='form-control input-sm sn_css' data-plugin='selectpicker'>";
		$combo_suc.="<option value=''>Seleccione...</option>";
		foreach($empresas as $key=>$val){
			$sucurs = $this->reordenar($suc,array('idempresa'=>$val['idempresa'])) ;
			
			$combo_suc.="<optgroup label='{$val['descripcion']}' >";
			foreach($sucurs as $k=>$v){
				$selected = '';
				if($v['idsucursal']== $idsucursal_session)
					$selected='selected';
				$combo_suc.="<option value='{$v['idsucursal']}' {$selected}>{$v['descripcion']}</option>";
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
	
	public function ListAccesos(){
		$fields = $this->input->post();
		$sql="SELECT a.* FROM seguridad.acceso a JOIN seguridad.modulo m ON m.idmodulo=a.idmodulo WHERE m.estado='A' AND a.idsucursal='".$fields['idsucursal']."' AND idperfil='".$fields['idperfil']."' AND m.idsistema='".$fields['idsistema']."' ";
		$query = $this->db->query($sql);
		
		return $this->response($query->result_array());
	}
	
	public function Accesoboton(){
		$fields = $this->input->post();
		$sql="SELECT acceso_boton.*FROM seguridad.acceso_boton JOIN seguridad.detalle_boton db ON db.idmodulo='{$fields['idmodulo']}' WHERE acceso_boton.idmodulo='".$fields['idmodulo']."' AND acceso_boton.idperfil='".$fields['idperfil']."' AND acceso_boton.idsucursal='".$fields['idsucursal']."' AND acceso_boton.estado='A'";
		$query = $this->db->query($sql);
		
		return $this->response($query->result_array());
	}
	
	public function ListaSucursal(){
		$idsucursal_session = $this->get_var_session("idsucursal");
		$sql = "SELECT*FROM seguridad.sucursal WHERE estado='A'
				AND idempresa=(SELECT idempresa FROM seguridad.sucursal s WHERE s.idsucursal='$idsucursal_session')
				ORDER BY descripcion DESC";
		$query = $this->db->query($sql);
		
		$list_sucursal = $query->result_array();		
		$html= '<div class="col-sm-3">';
		$html.=	'	<div class="ibox">';
		$html.=	'		<div class="" style="">';			
		$html.=	'			<div class="sistema ibox-title" style="font-weight:bold;">';
		$html.= '				<i class="fa fa-plane fa-2x" ></i>&nbsp;&nbsp;SUCURSAL';
		$html.= '				<div class="pull-right">';
		$html.= '					<div class="ibox-tools">';
		$html.= '						<a class="collapse-link">';
		$html.= '							<i class="fa fa-chevron-up"></i>';
		$html.= '						</a>';
		$html.= '					</div>';
		$html.= '				</div>';
		$html.= '			</div>';
		
		$html.= '			<div class="ibox-content" style="height:auto;">';
		$html.='				<ul id="" class="uk-nestable">';
		foreach($list_sucursal as $key=>$value){
			$sucursal = ucwords(strtolower($value['descripcion']));
			$html.='				<li class="uk-nestable-item uk-parent">';
			$html.='					<div data-suc="'.$value['idsucursal'].'" class="uk-nestable-panel manejable sucursal';
			if($key==0){
				$html.='					seleccionado';
			}
			$html.='">';
			$html.=							$sucursal;
				$html.='					<div class="pull-right">';
			if($key==0){
				$html.='						<i class="fa fa-check-square-o"></i>';
			}
				$html.='					</div>';
			$html.='					</div>';
			$html.='				</li>';
		}
		$html.='				</ul>';
		$html.='			</div>';
		
		$html.= '		</div>';
		$html.= '	</div>';
		$html.= '</div>';
		
		// $this->response($html);
		return $html;
	}
	
	public function ListaPerfil(){
		// $fields = $this->input->post();
		$sql = "SELECT 
				p.idperfil
				,p.descripcion perfil
				FROM seguridad.perfil p 
				WHERE p.estado='A'				
				ORDER BY idperfil";
		$query = $this->db->query($sql);
		
		$lista = $query->result_array();
		
		$html= '<div class="col-sm-2">';
		$html.=	'	<div class="ibox">';
		$html.=	'		<div class="" style="">';			
		$html.=	'			<div class="sistema " style="font-weight:bold;">';
		$html.= '				<i class="fa fa-user-secret fa-2x"></i>&nbsp;&nbsp;PERFIL';
		$html.= '				<div class="pull-right">';
		$html.= '					<div class="ibox-tools">';
		$html.= '						<a class="collapse-link">';
		$html.= '							<i class="fa fa-chevron-up"></i>';
		$html.= '						</a>';
		$html.= '					</div>';
		$html.= '				</div>';
		$html.= '			</div>';
		
		$html.= '			<div class="ibox-content" style="height:auto;">';
		$html.='				<ul class="uk-nestable">';
		foreach($lista as $key=>$value){
			$sucursal = ucwords(strtolower($value['perfil']));
			$html.='				<li class="uk-nestable-item uk-parent">';
			$html.='					<div data-perfil="'.$value['idperfil'].'" class="uk-nestable-panel manejable perfil case-perfil';
			if($key==0){
				$html.='					seleccionado';
			}
			$html.='">';
			$html.=							$sucursal;
				$html.='					<div class="pull-right">';
			if($key==0){
				$html.='						<i class="fa fa-check-square-o"></i>';
			}
				$html.='					</div>';
			$html.='					</div>';
			$html.='				</li>';
		}
		$html.='				</ul>';
		$html.='			</div>';		
		$html.= '		</div>';
		$html.= '	</div>';
		$html.= '</div>';
		
		// $this->response($html);
		return $html;
	}
	
	public function ListaSistema(){
		// $fields = $this->input->post();
		$idsucursal_session = $this->get_var_session("idsucursal");
		// $sql = "SELECT*FROM seguridad.sistema WHERE estado='A'";
		$sql = "SELECT s.idsistema, s.descripcion,s.image 
				FROM seguridad.acceso_sistema
				JOIN seguridad.sistema s ON s.idsistema=acceso_sistema.idsistema AND s.estado='A'
				WHERE acceso_sistema.estado='A' AND acceso_sistema.idsucursal=$idsucursal_session
				GROUP BY s.idsistema, s.descripcion,s.image;";
		$query = $this->db->query($sql);
		
		$lista = $query->result_array();
		
		$html= '<div class="col-sm-3">';
		$html.=	'	<div class="ibox" style="">';
		$html.=	'		<div class="" style="">';			
		$html.=	'			<div class="sistema " style="font-weight:bold;">';
		$html.= '				<i class="fa fa-linux fa-2x"></i>&nbsp;&nbsp; SISTEMA';
		$html.= '				<div class="pull-right">';
		$html.= '					<div class="ibox-tools">';
		$html.= '						<a class="collapse-link">';
		$html.= '							<i class="fa fa-chevron-up"></i>';
		$html.= '						</a>';
		$html.= '					</div>';
		$html.= '				</div>';
		$html.= '			</div>';
		
		$html.= '			<div class="ibox-content" style="height:auto;">';
		$html.='				<ul class="uk-nestable">';
		foreach($lista as $key=>$value){
			$sistema = ucwords(strtolower($value['descripcion']));
			$html.='				<li class="uk-nestable-item uk-parent">';
			$html.='					<div data-system="'.$value['idsistema'].'" class="uk-nestable-panel manejable system case-sistema';
			if($key==0){
				$html.='					seleccionado';
			}
			$html.='">';
			$html.='						<i class="fa '.$value['image'].'" style="font-size:18px;"></i>';
			$html.=							'&nbsp;&nbsp;'.$sistema;
				$html.='					<div class="pull-right">';
			if($key==0){
				$html.='						<i class="fa fa-check-square-o"></i>';
			}
				$html.='					</div>';
			$html.='					</div>';
			$html.='				</li>';
		}
		$html.='				</ul>';
		$html.='			</div>';		
		$html.= '		</div>';
		$html.= '	</div>';
		$html.= '</div>';
		
		return $html;
		// $this->response($html);
	}
	
	public function ListaModulo(){
		$fields = $this->input->post();
		$sql = "SELECT*FROM seguridad.modulo WHERE estado='A' AND idsistema='{$fields['idsistema']}' ORDER BY idpadre,orden";
		$query = $this->db->query($sql);
		
		$lista = $query->result_array();

		$query = $this->db->query("SELECT d.*,b.descripcion,icono FROM seguridad.detalle_boton d JOIN seguridad.boton b ON b.idboton=d.idboton WHERE d.estado='A'");
		$lista_boton = $query->result_array();
		
		$array_padre = $this->armar_datos($lista,$fields['idsistema'],0,null);
		// print_r($array_padre);exit;
		$html = '<ul style="margin-left:-30px;">';
		foreach($array_padre as $key=>$value){
			$old = "li_old";
			
			$html.= '<li class="">';
			$html.= '	<div class="presentacion '.$old.' " >';
			$html.= '		<div class="nada nada-icon main_expand main_father" style="display:inline-block;">&nbsp;</div>';
			$html.= '		<div class="menu_padre checkbox_parent" >';
			$html.= '			<div class="checkbox checkbox-success">';
			$html.= '				<input id ="checkbox'.$value['idmodulo'].'" class="checkbox_nodo ck_father" type="checkbox" name="idmodulo[]" value="'.$value['idmodulo'].'" >';
			$html.= '				<label  class="label_checkbox" id="label_checkbox'.$value['idmodulo'].'" for="checkbox'.$value['idmodulo'].'" ajax-icon ="">';
			$html.= '					<i class="fa '.$value['icono'].' " style="font-size:20px;"></i>&nbsp;&nbsp;'.ucwords(($value['descripcion']));
			$html.= '				</label>';
			$html.= '			</div>';
			$html.= '		</div>';
			$html.= '	</div>';
			
			$array_hijo = $this->armar_datos($lista,$fields['idsistema'],$value['idmodulo'],$value['idmodulo']);
			$clase_ul = 'nada-icon grupo';
			if(count($array_padre)==($key+1))
				$clase_ul = '';
			
			$html.= '	<ul class="'.$clase_ul.'" style="" >';
			foreach($array_hijo as $k=>$v){
				$array_boton = $this->armar_boton($lista_boton,$v['idmodulo']);
				
				$clase_ul = 'nada-icon grupo';
				if(count($array_hijo)==($k+1))
					$clase_ul = '';
				
				$clase_extend = 'main_expand main_hijo';
				if(count($array_boton)<1)
					$clase_extend = 'hijito';
				
				$html.= '	<li class="'.$clase_ul.'" >	';
				$html.= '		<div class="presentacion" >';
				$html.= '			<div class="nada nada-icon '.$clase_extend.'" style="display:inline-block;">&nbsp;</div>';
				$html.= '			<div class="menu_hijillo checkbox_parent" style="display:inline-block;">';
				$html.= '				<div class="checkbox checkbox-success" >';
				$html.= '					<input id="checkbox'.$v['idmodulo'].'" class="checkbox_nodo ck_hijo" type="checkbox" name="idmodulo[]"  value="'.$v['idmodulo'].'" >';
				$html.= '					<label class="label_checkbox" id="label_checkbox'.$v['idmodulo'].'" for="checkbox'.$v['idmodulo'].'" >';
				$html.= '						<i class="fa '.$v['icono'].'" ></i>&nbsp;&nbsp;'.ucwords(strtolower(($v['descripcion'])));
				$html.= '					</label>';
				$html.= '				</div>';
				$html.= '			</div>';
				$html.= '		</div>';
				
				if(count($array_boton)>0){
					$html.= '			<ul style="display:inline-block; " class="botones">';
					foreach($array_boton as $kk=>$vv){
						$clase_ul = 'nada-icon grupo';
						if(count($array_boton)==($kk+1))
							$clase_ul = '';
						$html.= '				<li class="'.$clase_ul.'"  >';
						$html.= '					<div class="presentacion" >';
						$html.= '						<div class="nada nada-icon hijito" style="display:inline-block;">&nbsp;</div>';
						$html.= '						<div class="nodo_botoncito" style="display:inline-block;">';
						$html.= '							<div class="checkbox" >';
						$html.= '								<input id="checkbox'.$vv['idmodulo'].'_'.$vv['idboton'].'" class="checkbox_nodo ck_boton" type="checkbox" name="idboton'.$vv['idmodulo'].'[]" value="'.$vv['idboton'].'" >';
						$html.= '								<label class="label_checkbox" id="label_checkbox'.$vv['idmodulo'].'_'.$vv['idboton'].'" for="checkbox'.$vv['idmodulo'].'_'.$vv['idboton'].'">';
						$html.= '									<i class="fa '.$vv['icono'].'" ></i>&nbsp;&nbsp;'.$vv['descripcion'];
						$html.= '								</label>';
						$html.= '							</div>';
						$html.= '						</div>';
						$html.= '					</div>';
						$html.= '				</li>';
					}
					$html.= '			</ul>';
				}
				
				$html.= '	</li>';
			}
			$html.= '	</ul>';
			
			$html.= '</li>';
		}
		$html.= '</ul>';
		// echo $html;exit;
		$this->response($html);
	}
	
	public function tree_list(){
		$fields = $this->input->post();
		$sql = "SELECT*FROM seguridad.modulo WHERE estado='A' AND idsistema='{$fields['idsistema']}' ORDER BY idpadre,orden";
		$query = $this->db->query($sql);
		
		$lista = $query->result_array();

		$query = $this->db->query("SELECT d.*,b.descripcion,icono FROM seguridad.detalle_boton d JOIN seguridad.boton b ON b.idboton=d.idboton WHERE d.estado='A';");
		$lista_boton = $query->result_array();
		
		$array_padre = $this->armar_datos($lista,$fields['idsistema'],0,null);
		
		$query = $this->db->query("SELECT*FROM seguridad.acceso_boton WHERE estado='A' AND idperfil='{$fields['idperfil']}' AND idsucursal='{$fields['idsucursal']}';");
		$array_accesos = $query->result_array();
		
		$query = $this->db->query("SELECT*FROM seguridad.acceso WHERE idperfil='{$fields['idperfil']}' AND idsucursal='{$fields['idsucursal']}';");
		$array_modulo = $query->result_array();
		
		$html = '<ul style="">';
		foreach($array_padre as $key=>$value){
			$array_hijo = $this->armar_datos($lista,$fields['idsistema'],$value['idmodulo'],$value['idmodulo']);
			$firts_node='';
			if($key==0){
				$firts_node = 'here_firts';
			}
			
			$html.= "<li class='".$firts_node."' data-jstree='{\"icon\":\"fa {$value['icono']}\"}' >";
			$html.= '		<input class="checkbox_nodo" type="checkbox" name="idmodulo[]" value="'.$value['idmodulo'].'" >';
			$html.= '	'.ucwords(($value['descripcion']));			
			$html.= '	<ul style="" >';
			foreach($array_hijo as $k=>$v){
				if(!empty( $firts_node )){
					if($key!=0){
						$firts_node = '';
					}
				}
				$array_boton = $this->armar_boton($lista_boton,$v['idmodulo']);
				$add_data = '';
				if(empty($array_boton)){
						$verificar = $this->reordenar($array_modulo,array('idmodulo'=>$v['idmodulo'])) ;
						if( !empty($verificar) )
							$add_data = ",\"selected\":\"true\"";
				}
				
				$html.= "	<li class='".$firts_node."' data-jstree='{\"icon\":\"fa {$v['icono']}\" $add_data}' >	";
				$html.= '		<input class="checkbox_nodo" type="checkbox" name="idmodulo[]" value="'.$v['idmodulo'].'" >';
				$html.= 		ucwords(strtolower(($v['descripcion'])));

				if(count($array_boton)>0){
					$html.= '	<ul >';
					foreach($array_boton as $kk=>$vv){
						$add_data = '';
						// $verificar = $this->reordenar($array_accesos,array('idmodulo'=>$v['idmodulo'])) ;
						$verificar = $this->reordenar($array_accesos,array('idmodulo'=>$v['idmodulo'],'idboton'=>$vv['idboton'])) ;
						if( !empty($verificar) )
							$add_data = ",\"selected\":\"true\"";
						$html.= "	<li data-jstree='{\"icon\":\"fa {$vv['icono']} fa-1x\" $add_data}' >	";
						$html.= '		<input class="checkbox_nodo" type="checkbox" name="idboton'.$v['idmodulo'].'[]" value="'.$vv['idboton'].'" >';
						$html.= '		'.$vv['descripcion'];
						$html.= '	</li>';
					}
					$html.= '	</ul>';
				}
				$html.= '	</li>';
			}
			$html.= '	</ul>';			
			$html.= '</li>';
		}
		$html.= '</ul>';
		
		$this->response($html);
	}
	
	public function sistemas_sucursal(){
		$fields = $this->input->post();
		if(empty($fields['idsucursal']))
			$fields['idsucursal'] = 0;

		$query = $this->db->query("SELECT sistema.idsistema,sistema.descripcion sistema 
								FROM seguridad.acceso_sistema ae
								JOIN seguridad.sucursal s ON s.idsucursal=ae.idsucursal
								JOIN seguridad.sistema ON sistema.idsistema=ae.idsistema
								WHERE ae.idsucursal='{$fields['idsucursal']}'
								ORDER BY sistema.orden;");
		$this->response($query->result_array());
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
	
	public function listar_empleados(){
		$q=$this->db->query("SELECT DISTINCT u.idusuario,user_nombres FROM seguridad.acceso_empresa a
						JOIN seguridad.view_usuario u ON u.idusuario=a.idusuario AND u.estado='A' AND u.baja='N'
						WHERE a.idperfil={$this->input->post('idperfil')} AND idsucursal={$this->input->post('idsucursal')} ;");
		$this->response($q->result_array());
	}
}
?>