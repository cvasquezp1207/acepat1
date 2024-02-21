<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
$botones_estado = '';
// Muestra TODOS errores de validación de un formulario
if ( ! function_exists('mi_cambio_estado')) {

	function mi_cambio_estado($estado) {

		switch ($estado) :
			case 'A':
				$botones_estado  =  ' <div class="i-checks">';
				$botones_estado .=  ' <label> <input type="radio" id="activo" value="A" name="estado" checked> <i></i> Activo &nbsp;&nbsp;&nbsp;&nbsp;</label>';
				$botones_estado .=  ' <label> <input type="radio" id="inactivo" value="I" name="estado"> <i></i> Inactivo </label>';
				$botones_estado .=  ' </div>';
			break;
			case 'I':
				$botones_estado  =  ' <div class="i-checks">';
				$botones_estado .=  ' <label> <input type="radio" id="activo" value="A" name="estado"/> <i></i> Activo &nbsp;&nbsp;&nbsp;&nbsp;</label>';
				$botones_estado .=  ' <label> <input type="radio" id="inactivo" value="I" name="estado" checked /> <i></i> Inactivo </label>';
				$botones_estado .=  ' </div>';
			break;
			default:
				$botones_estado  =  ' <div class="i-checks">';
				$botones_estado .=  ' <label> <input type="radio" id="activo" value="A" name="estado" checked> <i></i> Activo &nbsp;&nbsp;&nbsp;&nbsp;</label>';
				$botones_estado .=  ' <label> <input type="radio" id="inactivo" value="I" name="estado"> <i></i> Inactivo </label>';
				$botones_estado .=  ' </div>';
			break;

		endswitch;

		return $botones_estado;
	}

}

// Muestra TODOS errores de validación de un formulario
if ( ! function_exists('my_token')) {

	function my_token() {
		$token = md5(uniqid(rand(),true));
		$this->session->set_userdata('token',$token);
		return $token;
	}

}

if ( ! function_exists('radio_sexo')) {

	function radio_sexo($estado) {
		switch ($estado) :
			case 'M':
				$botones_estado  =  ' <div class="i-checks">';
				$botones_estado .=  ' <label> <input type="radio" id="activo" value="M" name="sexo" checked> <i></i> Masculino &nbsp;&nbsp;&nbsp;&nbsp;</label>';
				$botones_estado .=  ' <label> <input type="radio" id="inactivo" value="F" name="sexo"> <i></i> Femenino </label>';
				$botones_estado .=  ' </div>';
			break;
			case 'F':
				$botones_estado  =  ' <div class="i-checks">';
				$botones_estado .=  ' <label> <input type="radio" id="activo" value="M" name="sexo"/> <i></i> Masculino &nbsp;&nbsp;&nbsp;&nbsp;</label>';
				$botones_estado .=  ' <label> <input type="radio" id="inactivo" value="F" name="sexo" checked /> <i></i> Femenino </label>';
				$botones_estado .=  ' </div>';
			break;
			default:
				$botones_estado  =  ' <div class="i-checks">';
				$botones_estado .=  ' <label> <input type="radio" id="activo" value="M" name="sexo" checked> <i></i> Masculino &nbsp;&nbsp;&nbsp;&nbsp;</label>';
				$botones_estado .=  ' <label> <input type="radio" id="inactivo" value="F" name="sexo"> <i></i> Femenino </label>';
				$botones_estado .=  ' </div>';
			break;

		endswitch;

		return $botones_estado;
	}

}

if ( ! function_exists('imagen_upload')) {
	function imagen_upload($item,$ruta,$img_default,$rename=false,$resize=array(),$file='file'){
		$respuesta = '';
		/**************** UPLOAD *********************/
		if(!empty($_FILES[$file]['size']))//EXISTE UPLOAD
			$respuesta = SubirFichero($_FILES[$file],$ruta,$rename,$resize);
		else{// NO EXISTE UPLOAD
			if(!empty($_POST["$item"]))
				$respuesta = $_POST["$item"];	
			else
				$respuesta = $img_default;			
		}
		
		return $respuesta;
		/**************** UPLOAD *********************/
	}
}

if ( ! function_exists('SubirFichero')) {
	function SubirFichero($FILES=array(),$ruta,$rename,$resize){
		$resp = '';
		$archivo    = $FILES['name'];
		$newname    = $archivo;
		if (!empty($rename)) {
			$ext        = extension($archivo);
			$newname    = date('YmdHis').'.'.$ext;
			
		}
		$tmp_name   = $FILES['tmp_name'];
			
		if (move_uploaded_file($tmp_name, $ruta.$newname)) {
			// print_r($resize);exit;
			if(!empty($resize)){
				if($resize['resize'])
					resize_Image($ruta.$newname,$resize['ancho'],$resize['alto']);
			}
			$resp = $newname;
		}
		return $resp;
	}
}

if ( ! function_exists('resize_Image')) {
	function resize_Image($fichero,$ancho,$alto){
		 $ruta_imagen = $fichero;

		 $miniatura_ancho_maximo = $ancho;
		 $miniatura_alto_maximo  = $alto;

		 $info_imagen = getimagesize($ruta_imagen);
		 $imagen_ancho = $info_imagen[0];
		 $imagen_alto = $info_imagen[1];
		 $imagen_tipo = $info_imagen['mime'];


		 $proporcion_imagen = $imagen_ancho / $imagen_alto;
		 $proporcion_miniatura = $miniatura_ancho_maximo / $miniatura_alto_maximo;

		 $miniatura_alto = $miniatura_alto_maximo;
		 $miniatura_ancho = $miniatura_ancho_maximo;

		 switch ( $imagen_tipo ){
			 case "image/jpg":
			 case "image/jpeg":
				 $imagen = imagecreatefromjpeg( $ruta_imagen );
				 break;
			 case "image/png":
				 $imagen = imagecreatefrompng( $ruta_imagen );
				 break;
			 case "image/gif":
				 $imagen = imagecreatefromgif( $ruta_imagen );
				 break;
		 }
		
		 $imgAncho = imagesx ($imagen); 
		 $imgAlto =imagesy($imagen); 

		 $lienzo = imagecreatetruecolor( $miniatura_ancho, $miniatura_alto );

		 imagecopyresampled($lienzo, $imagen, 0, 0, 0, 0, $miniatura_ancho, $miniatura_alto, $imagen_ancho, $imagen_alto);


		imagejpeg($lienzo, $fichero, 80);
	}
}

if ( ! function_exists('extension')) {
	function extension($fichero){
		return pathinfo($fichero, PATHINFO_EXTENSION);
	}
}

if ( ! function_exists('getMonthsName')) {
	function getMonthsName($iMes = null){
		$months = array(
		"","enero","febrero","marzo","abril","mayo","junio","julio"
		,"agosto","setiembre","octubre","noviembre","diciembre");
	
		if($iMes !== null) {
			$index = intval($iMes);
			return $months[$index];
		}
		
		return $months;
	}
}

if ( ! function_exists('getDaysName')) {
	function getDaysName($iMes){
		$months = array("","lunes","martes","miercoles","jueves","viernes","sabado","domingo");		
		$index = intval($iMes);		
		return $months[$index];
	}
}

if ( ! function_exists('mi_swicht')) {

	function mi_swicht($valor,$name,$id ,$atributo = array()) {

		$value = '';

		if (empty($atributo)) {
			
		}

		switch ($valor) :
			case 'A':
				$botones_estado  =  ' <div class="switch">';
				$botones_estado .=  ' 	<div class="onoffswitch">';
				$botones_estado .=  ' 		<input type="checkbox" checked="" class="onoffswitch-checkbox" name="$name" id="$id">';
				$botones_estado .=  ' 		 <label class="onoffswitch-label" for="example1">';
				$botones_estado .=  ' 		 <span class="onoffswitch-inner"></span>';
				$botones_estado .=  ' 		 <span class="onoffswitch-switch"></span>';
				$botones_estado .=  ' 	</div>';
				$botones_estado .=  ' </div>';
			break;

			case 'I':
				$botones_estado  =  ' <div class="switch">';
				$botones_estado .=  ' 	<div class="onoffswitch">';
				$botones_estado .=  ' 		<input type="checkbox" class="onoffswitch-checkbox" name="$name" id="$id">';
				$botones_estado .=  ' 		 <label class="onoffswitch-label" for="example1">';
				$botones_estado .=  ' 		 <span class="onoffswitch-inner"></span>';
				$botones_estado .=  ' 		 <span class="onoffswitch-switch"></span>';
				$botones_estado .=  ' 	</div>';
				$botones_estado .=  ' </div>';
			break;

			default:
				$botones_estado  =  ' <div class="switch">';
				$botones_estado .=  ' 	<div class="onoffswitch">';
				$botones_estado .=  ' 		<input type="checkbox" class="onoffswitch-checkbox" name="$name" id="$id">';
				$botones_estado .=  ' 		 <label class="onoffswitch-label" for="example1">';
				$botones_estado .=  ' 		 <span class="onoffswitch-inner"></span>';
				$botones_estado .=  ' 		 <span class="onoffswitch-switch"></span>';
				$botones_estado .=  ' 	</div>';
				$botones_estado .=  ' </div>';
			break;

		endswitch;

		return $botones_estado;
	}

}

if ( ! function_exists('ver_fichero_valido')) {
	 function ver_fichero_valido($fichero,$url='/',$default='default_logo_0'){
		$url_fichero = $url.$fichero;
		if (file_exists($url_fichero)) {
			return $url_fichero;
		} else if (file_exists($url.$default)) {
			return $url.$default;
		}else{
			return null;
		}
	}
}

if ( ! function_exists('RandomString')) {
	function RandomString($length=10,$uc=true,$n=true,$sc=false){
		$source = 'abcdefghijklmnopqrstuvwxyz';
		if($uc==1) $source .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		if($n==1) $source .= '1234567890';
		if($sc==1) $source .= '|@#~$%()=^*+[]{}-_';
		if($length>0){
			$rstr = "";
			$source = str_split($source,1);
			for($i=1; $i<=$length; $i++){
				mt_srand((double)microtime() * 1000000);
				$num = mt_rand(1,count($source));
				$rstr .= $source[$num-1];
			}	 
		}
		return $rstr;
	}
}

/**
 * convierte formato de fechas, soporta fecha datetime (fecha y hora)
 * input format: yyyy-mm-dd
 * output format: dd/mm/yyyy
 */
if ( ! function_exists('fecha_es')) {
	function fecha_es($str, $full = FALSE, $split = "-", $join = "/") {
		if( ! empty($str)) {
			$ext = "";
			if(strlen($str) > 10) {
				$ext = substr($str, 10, 8);
				$str = substr($str, 0, 10);
			}
			
			if($full)
				return implode($join, array_reverse(explode($split, $str))) . $ext;
			
			return implode($join, array_reverse(explode($split, $str)));
		}
		
		return "";
	}
}

/**
 * convierte formato de fechas
 * input format: dd/mm/yyyy
 * output format: yyyy-mm-dd
 */
if( ! function_exists("fecha_en") && function_exists("fecha_es")) {
	function fecha_en($str, $full = FALSE) {
		return fecha_es($str, $full, "/", "-");
	}
}

/**
 * verifica si un string empieza por el string enviado por parametro 
 */
if( ! function_exists("starts_with")) {
	function starts_with($string, $pattern) {
		return substr($string, 0, strlen($pattern)) == $pattern;
	}
}

if( ! function_exists("resize_imagen")) {
	function resize_imagen($file, $w, $h, $crop=FALSE) {
		list($width, $height) = getimagesize($file);
		$r = $width / $height;
		if ($crop) {
			if ($width > $height) {
				$width = ceil($width-($width*abs($r-$w/$h)));
			} else {
				$height = ceil($height-($height*abs($r-$w/$h)));
			}
			$newwidth = $w;
			$newheight = $h;
		} else {
			if ($w/$h > $r) {
				$newwidth = $h*$r;
				$newheight = $h;
			} else {
				$newheight = $w/$r;
				$newwidth = $w;
			}
		}
		$ext = extension($file);
		switch ($ext) {
			case "jpg":
				$src = imagecreatefromjpeg($file);
				break;
			case "png":
				$src = imagecreatefrompng($file);
				break;
			case "gif":
				$src = imagecreatefromgif($file);
				break;
		}
		
		// $src = imagecreatefromjpeg($file);
		$dst = imagecreatetruecolor($newwidth, $newheight);
		imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

		return $dst;
	}
}

if ( ! function_exists('is_url')) {
	function is_url($var) {
		return (filter_var($var, FILTER_VALIDATE_URL) !== false);
	}
}

if ( ! function_exists('exist_url')) {
	function exist_url($var) {
		$file_headers = @get_headers($var);
		if($file_headers[0] == 'HTTP/1.1 404 Not Found')
		   return false;
		else
		   return true;
	}
}

if ( ! function_exists('redondeosunat')) {
	function redondeosunat($n,$fixed = 2) {
		$str = number_format($n,2,'.','');
		
		$x_n = explode(".",$str);
		$u_dig 	  = $x_n[1]%10;
		$n_decimal = 0;
		
		if($u_dig>=5){
			$n_decimal = intval ($x_n[1]) + intval (10-$u_dig);
			if($n_decimal==100){
				$n_decimal=0;
				$x_n[0] = intval ($x_n[0])+1;
			}

		}else if($u_dig>1){
			$n_decimal = intval($x_n[1]) - intval($u_dig);
		}else{
			$n_decimal = $x_n[1];
		}
		return number_format($x_n[0].".".$n_decimal,$fixed,'.','');
	}
}