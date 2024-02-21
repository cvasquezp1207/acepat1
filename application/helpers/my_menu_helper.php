<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Muestra TODOS errores de validación de un formulario
if ( ! function_exists('my_validation_errors')) {

	function my_validation_errors($errors) {
		$salida = '';

		if ($errors) {
			$salida = '<div class="alert alert-error">';
			$salida = $salida.'<button type="button" class="close" data-dismiss="alert"> × </button>';
			$salida = $salida.'<h4> Mensajes Validacion </h4>';
			$salida = $salida.'<small>'.$errors.'</small>';
			$salida = $salida.'</div>';
		}
		return $salida;
	}

}

// Opciones de menú de la barra superior (las opciones dependen si hay session o no)
if ( ! function_exists('my_menu_ppal')) {

	function my_menu_ppal() {
		$opciones = '<li>'.anchor('home/index', 'Inicio').'</li>';
		$opciones = $opciones.'<li>'.anchor('home/acerca_de', 'Acerca De').'</li>';

		if (get_instance()->session->userdata('usuario')) {
			$opciones = $opciones.'<li>'.anchor('home/cambio_clave', 'Cambio Clave').'</li>';
			$opciones = $opciones.'<li>'.anchor('home/salir', 'Salir').'</li>';
		}
		else {
			$opciones = $opciones.'<li>'.anchor('home/ingreso', 'Ingreso').'</li>';
		}

		return $opciones;
	}

}

if ( ! function_exists('my_menu_lateral')) {

	function my_menu_lateral($usuario, $sistema, $perfil) {

		$opciones = null;

		if ($usuario) :
			$opciones = '';
			get_instance()->load->model('Modulo_model', 'modulo');
			$padres = get_instance()->modulo->get_modulo_acceso($sistema,$perfil);

			foreach ($padres as $opcion ) :

				$hijos = get_instance()->modulo->get_modulo_acceso($sistema,$perfil, $opcion->idmodulo);
				
				if($hijos):

					$submenu_existencia_clase = 'class="fa arrow"';
					$opciones .= '<li><a href="javascript:void(0);"><i class="'.$opcion->icono.'"></i> <span class="nav-label">' . $opcion->menu . '</span><span ' . $submenu_existencia_clase . '></span></a>';
					
					foreach ($hijos as $opcionh) :

						$opcionh->url = strtolower($opcionh->url);
						$opciones .= '<ul class="nav collapse nav-second-level">';
							$opciones .= '<li><a href="' . base_url().$opcionh->url . '"><i class="'.$opcionh->icono.'"></i> <span class="nav-label">' . $opcionh->menu . '</span><span></span></a></li>';
						$opciones .= '</ul>';

					endforeach;

					$opciones .= '</li>';
				
				else:

					$opciones .= '<li><a href="javascript:void(0);"><i class="'.$opcion->icono.'"></i> <span class="nav-label">' . $opcion->menu . '</span><span  ' . $submenu_existencia_clase . '></span></a></li>';
				
				endif;

				$submenu_existencia_clase = '';

			endforeach;
		
		endif;
		
		return $opciones;
	}

}

if ( ! function_exists('my_menu_hijos')) {

	function my_menu_hijos($datos) {

		$submenu_existencia_clase = 'class="fa arrow"';
		$opciones = '';

		foreach ($datos as $opcion ) :

			if($opcion->cantidad > 0):
				
				$submenu_existencia_clase = 'class="fa arrow"';
				$opciones .= '<li><a href="javascript:void(0);"><i class="'.$opcion->icono.'"></i> <span class="nav-label">' . $opcion->menu . '</span><span ' . $submenu_existencia_clase . '></span></a>';

				$hijos = get_instance()->modulo->get_modulo_acceso($sistema,$perfil, $opcion->idmenu);
				
				foreach ($hijos as $opcion) :


				endforeach;

			else:
				$opciones .= '<li><a href="javascript:void(0);"><i class="'.$opcion->icono.'"></i> <span class="nav-label">' . $opcion->menu . '</span><span  ' . $submenu_existencia_clase . '></span></a></li>';
			endif;

		endforeach;
	}

}