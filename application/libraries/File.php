<?php 

include_once APPPATH."libraries/class.upload.php";

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class File {
	
	private $input_file = "file"; // Nombre del input.file del formulario
	private $folder = ""; // carpeta para guardar archivo
	private $path = ""; // ruta de la carpeta
	private $error = "";
	
	/** Variables de configuracion para las imagenes */
	public $file_name = null; // nombre para el archivo
	public $image_resize = true;
	public $width = 150;
	public $height = 150;
	public $max_width = false;
	public $max_height = false;
	public $ratio_x = false;
	public $ratio_y = false;
	public $create_thumbs = false;
	public $greyscale = false;
	
	/** Objeto upload */
	private $oupload = null;
	
	public function __construct() {
		$this->path = FCPATH."app/img"; // ruta por default
    }
	
	private function clean($string) {
		$string = trim($string);
		// $string = strtolower($string);
		$string = preg_replace("/\s+/", "_", $string);
		return $string;
	}
	
	public function set_path($path) {
		$this->path = $this->clean($path);
	}
	
	public function get_path() {
		return $this->path;
	}
	
	public function set_folder($folder) {
		$this->folder = $this->clean(basename($folder));
	}
	
	public function set_name($string) {
		$this->file_name = trim($string);
	}
	
	public function get_fullname() {
		$name = $this->file_name;
		$ext = $this->get_ext();
		if($ext != null)
			return $name.".".$ext;
		return $name;
	}
	
	public function get_absolute_path($path, $folder="") {
		$char = substr($path, -1);
		if($char == "/") {
			$path = substr($path, 0, -1);
		}
		
		if( ! empty($folder)) {
			$char = substr($folder, 0, 1);
			if($char == "/") {
				$folder = substr($folder, 1);
			}
			$path = $path."/".$folder;
		}
		
		return $path;
	}
	
	public function set_input_file($string) {
		global $_FILES;
		$this->oupload = new upload($_FILES[$string]);
	}
	
	public function is_uploaded() {
		if($this->oupload == null) {
			return false;
		}
		
		return $this->oupload->uploaded;
	}
	
	public function get_mime_type() {
		if($this->is_uploaded())
			return $this->oupload->file_src_mime;
		return null;
	}
	
	public function get_ext() {
		if($this->is_uploaded())
			return $this->oupload->file_src_name_ext;
		return null;
	}
	
	public function get_old_name() {
		if($this->is_uploaded()) {
			return $this->oupload->file_src_name_body;
		}
		return null;
	}
	
	public function get_old_fullname() {
		if($this->is_uploaded()) {
			$name = $this->oupload->file_src_name_body;
			$ext = $this->get_ext();
			if($ext != null)
				return $name.".".$ext;
			return $name;
		}
		return null;
	}
	
	public function get_upload_filename() {
		if($this->oupload->processed) {
			return $this->oupload->file_dst_name;
		}
		return $this->get_fullname();
	}
	
	public function upload() {
		if ($this->is_uploaded()) {
			$path = $this->get_absolute_path($this->path, $this->folder);
			
			if(is_dir($path)) {
				$this->file_name = trim($this->file_name);
				if($this->file_name == "") {
					$this->file_name = $this->oupload->file_src_name_body;
				}
				$this->file_name = $this->clean($this->file_name);
				
				$this->oupload->file_new_name_body = $this->file_name;
				
				if($this->oupload->file_src_name_ext == "png") {
					$this->oupload->preserve_transparency = true;
				}
				
				if($this->image_resize) {
					$this->oupload->image_resize = true;
					
					if($this->max_width !== false || $this->max_height !== false) {
						if($this->max_width !== false) {
							$this->oupload->image_max_width = $this->max_width;
						}
						if($this->max_height !== false) {
							$this->oupload->image_max_height = $this->max_height;
						}
					}
					else {
						$this->oupload->image_x = $this->width;
						$this->oupload->image_y = $this->height;
						$this->oupload->image_ratio_x = $this->ratio_x;
						$this->oupload->image_ratio_y = $this->ratio_y;
					}
				}
				else {
					$this->oupload->image_resize = false;
				}
				
				$this->oupload->process($path);
				
				if ($this->oupload->processed) {
					if($this->greyscale) {
						$this->oupload->file_new_name_body = $this->file_name.'_grey';
						$this->oupload->image_greyscale = true;
						$this->oupload->process($path);
					}
					
					if($this->create_thumbs) {
						$this->oupload->file_new_name_body = $this->file_name.'_thumb';
						$this->oupload->image_resize = true;
						$this->oupload->image_x = 100;
						$this->oupload->image_y = 74;
						$this->oupload->process($path);
					}
					
					$this->oupload->clean();
					return true;
				}
			}
			else {
				$this->error = "Path not found: ".$path;
			}
			
			
			return false;
		}
		else {
			$this->error = "Object upload not defined";
		}
		
		return false;
	}
	
	public function get_error() {
		if(!empty($this->error))
			return $this->error;
		
		if ($this->is_uploaded())
			return $this->oupload->error;
		
		return "";
	}
}

/* End of file File.php */