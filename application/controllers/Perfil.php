<?php

include_once "Controller.php";

class Perfil extends Controller {
	
	/**
	 * Datos iniciales del controlador
	 */
	public function init_controller() {
		$this->set_title("Mantenimiento de Perfil");
		$this->set_subtitle("Lista de perfil");
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
		$data["ubigeo"] = $this->get_form_ubigeo();
		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		// cargamos el modelo y la libreria
		$this->load_model('view_perfil');
		// $this->load_model($this->controller);
		$this->load->library('datatables');

		// indicamos el modelo al datatables
		$this->datatables->setModel($this->view_perfil);
		// $this->datatables->setModel($this->perfil);
		$this->datatables->setIndexColumn("idperfil");

		// filtros adicionales para la tabla de la bd (perfil en este caso)
		// $this->datatables->where('estado', '=', "ACTIVO");
		// $this->datatables->where('idperfil', '=', 1);

		// indicamos las columnas a mostrar de la tabla de la bd
		// $this->datatables->setColumns(array('descripcion','estado'));
		$this->datatables->setColumns(array('descripcion','estado'));

		// columnas de la tabla, si no se envia este parametro, se muestra el
		// nombre de la columna de la tabla de la bd
		$columnasName = array(
			array('Descripci&oacute;n','80%')
			,array('Estado','10%')
			// array('Descripci&oacute;n', '95%') // ancho de la columna
		);
		
		// probando subgrid
		// $this->datatables->setSubgrid("prueba2",true);

		// generamos la tabla y el script para el dataTables
		$table = $this->datatables->createTable($columnasName);

		// $table = $this->datatables->createTable();
		$script = "<script>".$this->datatables->createScript()."</script>";

		// agregamos los css para el dataTables
		// $this->css('plugins/dataTables/dataTables.bootstrap');
		// $this->css('plugins/dataTables/dataTables.responsive');
		// $this->css('plugins/dataTables/dataTables.tableTools.min');
		
		$this->css('plugins/iCheck/custom');

		// agregamos los scripts para el dataTables
		// $this->js('plugins/dataTables/jquery.dataTables');
		// $this->js('plugins/dataTables/dataTables.bootstrap');
		// $this->js('plugins/dataTables/dataTables.responsive');
		// $this->js('plugins/dataTables/dataTables.tableTools.min');
		$this->js('plugins/iCheck/icheck.min');
		$this->js("<script>$(document).ready(function () {
                $('.i-checks').iCheck({
                    checkboxClass: 'icheckbox_square-green',
                    radioClass: 'iradio_square-green',
                });
            });</script>", false);
		$this->js($script, false);

		return $table;
	}
	
	/**
	 * Metodo para registrar un nuevo registro
	 */
	public function nuevo() {
		$this->set_title("Registrar Perfil");
		$this->set_subtitle("");
		$this->set_content($this->form());
		$this->index("content");
	}
	
	/**
	 * Metodo para editar registro
	 */
	public function editar($id) {
		$this->load_model($this->controller);

		$data = $this->perfil->find($id);
		
		$this->set_title("Modificar Perfil");
		$this->set_subtitle("");
		$this->set_content($this->form($data));
		$this->index("content");
	}
	
	/**
	 * Metodo para guardar un registro
	 */
	public function guardar() {
		$this->load_model($this->controller);
		$fields = $this->input->post();
		// echo $fields["accion"];exit;
		// $fields['controller']=$this->controller;
		// $fields['accion']=__FUNCTION__;

		if(empty($fields["idperfil"])) {
			$fields["idperfil"] = $this->perfil->insert($fields);
		}
		else {
			$this->perfil->update($fields);
		}
		
		$this->response($this->perfil->get_fields());
	}
	
	/**
	 * Metodo para eliminar un registro
	 */
	public function eliminar($id) {
		$this->load_model($this->controller);
		// cambiamos de estado
		$fields['idperfil'] = $id;
		$fields['estado'] = "I";
		
		$fields['controller']=$this->controller;
		$fields['accion']=__FUNCTION__;
		
		$this->perfil->update($fields);
		// $this->perfil->delete($fields);
		
		$this->response($fields);
	}
}
?>