<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Muestra TODOS errores de validación de un formulario
if ( ! function_exists('imagen_upload')) {
	function imagen_upload($item,$ruta,$img_default,$rename=false,$resize=array()){
		global $_FILES;
		$respuesta = '';
		/**************** UPLOAD *********************/
		if(!empty($_FILES['file']['size']))//EXISTE UPLOAD
			$respuesta = SubirFichero($_FILES['file'],$ruta,$rename,$resize);
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
	function SubirFichero($FILES=array(),$ruta,$resize){
		$resp = '';
		$archivo    = $FILES['name'];
		$ext        = $this->extension($archivo);
		$newname    = date('YmdHis').'.'.$ext;
		$newname    = $archivo;
		$tmp_name   = $FILES['tmp_name'];
			
		if (move_uploaded_file($tmp_name, $ruta.$newname)) {
			if($resize){
				 if($resize['resize'])
					 $this->resize_Image($ruta.$newname,$resize['ancho'],$resize['alto']);
			}
			$resp = $newname;
		}else{
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
	function extension($fichero,$ancho,$alto){
		 end(explode(".", $str));
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
				$ext = substr($str, 10);
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
		return fecha_es($str, $full, "/", "-")
	}
}