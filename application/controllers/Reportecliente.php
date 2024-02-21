<?php
include_once "Controller.php";

class Reportecliente extends Controller {
	public function init_controller() {}
	
	/**
	 * Datos finales del controlador antes de renderizar la plantilla
	 */
	public function end_controller() {
		$this->js('form/'.$this->controller.'/index');
	}
	
	/**
	 * Metodo que retorna el formulario
	 */
	public function form($data = null,$prefix='') {
		if(!is_array($data)) {
			$data = array();
		}
		$this->load->library('combobox');

		$data["controller"] = $this->controller;
		
		/*---------------------------------------------------------------------*/
		$this->combobox->init();
		$this->combobox->setAttr("id","idcliente");
		$this->combobox->setAttr("name","idcliente");
		$this->combobox->setAttr("class","chosen-select form-control");
		// $this->combobox->setAttr("required","");
		$this->db->select("idcliente,cliente");
		$query = $this->db->order_by("cliente")->get("venta.cliente_venta_view");
		$this->combobox->addItem("","[TODOS]");
		$this->combobox->addItem($query->result_array());
		
		$data['cliente'] = $this->combobox->getObject();
		/*---------------------------------------------------------------------*/
		
		$this->combobox->init(); // un nuevo combo
		$this->combobox->setAttr(
			array(
				"id"=>$prefix."idzona"
				,"name"=>"idzona"
				,"class"=>"form-control here_req input-xs"
				,"required"=>""
			)
		);
		
		// combo rutas
		$this->combobox->setAttr("id","idubigeo");
		$this->combobox->setAttr("name","idubigeo");
		$this->combobox->setAttr("class","form-control input-xs");
		$this->combobox->setAttr("required","");
		$this->db->select('idubigeo,descripcion');
		$query = $this->db->where("estado","A")->order_by("descripcion")->get("general.ubigeosorsa");
		$this->combobox->addItem("","TODOS");
		$this->combobox->addItem($query->result_array());
		$data['ruta'] = $this->combobox->getObject();
		
		// $query = $this->db->select('idzona,zona descripcion')->where("estado", "A")->get("general.zona");
		// $this->combobox->addItem('','Seleccione...');
		// $this->combobox->addItem($query->result_array());
		// if( isset($data["idzona"]) ) {
			// $this->combobox->setSelectedOption($data["idzona"]);
		// }
		// $data["zona_combo"] = $this->combobox->getObject();
		
		$this->css("plugins/datapicker/datepicker3");
		$this->js("plugins/datapicker/bootstrap-datepicker");
		$this->js("plugins/datapicker/bootstrap-datepicker.es");
		$this->css("plugins/chosen/chosen");
		$this->js("plugins/chosen/chosen.jquery");

		return $this->load->view($this->controller."/form", $data, true);
	}
	
	/**
	 * Retornamos la grilla
	 */
	public function grilla() {
		return $this->form();
	}

	public function seleccion($datos,$comparar = array()){
		$data = array();
		foreach($datos as $kk=>$vv){
			if(!empty($comparar)){
				$band = false;
				foreach($comparar as $k=>$v){
					if($vv[$k]==$v)
						$band=true;
					else{
						$band=false;
						break;
					}
				}
				if($band){
					$data[]=$vv;					
				}
			}
		}	
		return $data;
	}
	
	public function condicion(){
		$post = $_REQUEST;
		$where = "";
		$post['idcliente'] = trim($post['idcliente']);
		if(!empty($post['idcliente'])){
			$where.=" AND idcliente='{$post['idcliente']}' ";
		}else if($post['idcliente']==0 && $post['cliente']!='[TODOS]'){
			$where.=" AND idcliente='0' ";
		}
		
		if(!empty($post['idubigeo']))
			$where.=" AND idubigeo='{$post['idubigeo']}' ";
		
		if(!empty($post['idzona']))
			$where.=" AND idzona='{$post['idzona']}' ";
		
		if(!empty($post['tipo']))
			$where.=" AND tipo='{$post['tipo']}' ";
		
		if(!empty($post['sin_direccion']) && $post['sin_direccion']=='S' )
			$where.=" AND COALESCE(trim(direccion),'')=''";
		
		if(!empty($post['sin_telefono']) && $post['sin_telefono']=='S' )
			$where.=" AND COALESCE(trim(telefono),'')=''";
		
		if(!empty($post['sin_ruc']) && $post['sin_ruc']=='S' )
			$where.=" AND COALESCE(trim(ruc),'')=''";
		
		if(!empty($post['sin_dni']) && $post['sin_dni']=='S' )
			$where.=" AND COALESCE(trim(dni),'')=''";
		
		if(!empty($post['sin_email']) && $post['sin_email']=='S' )
			$where.=" AND COALESCE(trim(cliente_email),'')=''";
		return $where;
	}
	
	public function query_master(){
		$sql="SELECT cliente_view.*
			,CASE WHEN linea_credito='S' THEN 'SI' ELSE 'NO' END tiene_linea
			,CASE WHEN bloqueado='S' THEN 'SI' ELSE 'NO' END tiene_bloqueo
			,CASE WHEN tipo='N' THEN 'NATURAL' ELSE 'JURIDICO' END tipo_cliente,u.descripcion ruta,cliente_view.zona localidad
			FROM venta.cliente_view
			LEFT JOIN general.ubigeozona_view u ON u.idubigeo=cliente_view.idubigeo";
		return $sql;
	}
	
	public function get_data(){
		$sql = "SELECT*FROM({$this->query_master()}) qq
								WHERE estado='A'
								{$this->condicion()}
								ORDER BY cliente
				";
		// echo $sql;Exit;
		$q = $this->db->query($sql);
		
		return $q->result_array();
	}
	
	public function data_representante(){
		$sql = "SELECT*FROM venta.cliente_representante WHERE idcliente IN (SELECT idcliente FROM({$this->query_master()}) qq
				WHERE estado='A'
				{$this->condicion()})";
		$q = $this->db->query($sql);
		
		return $q->result_array();
	}
	
	public function head_resumido($tipo='pdf'){
		if($tipo=='pdf'){
			return array('item'=>array("ITEM",10,0,'L')
						,'cliente'=>array("CLIENTE",57,0,'L')
						,'direccion'=>array("DIRECCION",67,0,'L')
						,'zona'=>array("LOCALIDAD",32,0,'L')
						,'ruc'=>array("RUC",20,0,'L')
						,'dni'=>array("DNI",15,1,'L')
						// ,'telefono'=>array("TELEFONO",20,1,'L')
			);
		}else{
			return array('item'=>array("ITEM",10,0,'L')
						,'cliente'=>array("CLIENTE",57,0,'L')
						,'direccion'=>array("DIRECCION",67,0,'L')
						,'ruta'=>array("RUTA",32,0,'L')
						,'zona'=>array("LOCALIDAD",32,0,'L')
						,'ruc'=>array("RUC",20,0,'L')
						,'dni'=>array("DNI",15,1,'L')
						,'telefono'=>array("TELEFONO",20,1,'L')
						,'cliente_email'=>array("E-MAIL",20,1,'L')
						,'tiene_linea'=>array("CON LINEA",20,1,'L')
						,'limite_credito'=>array("MONTO LINEA",20,1,'L')
						,'tiene_bloqueo'=>array("BLOQUEADO",20,1,'L')
						,'tipo_cliente'=>array("TIPO",20,1,'L')
			);
		}
	}
	
	public function head_detallado(){
		return array('direccion'=>array("DIRECCION",73,0,'L')
					,'zona'=>array("LOCALIDAD",36,0,'L')
					,'ruc'=>array("RUC",18,0,'L')
					,'dni'=>array("DNI",14,0,'L')
					,'telefono'=>array("TELEFONO",20,0,'L')
					,'cliente_email'=>array("EMAIL",41,1,'L')
		);
	}
	
	public function head_tipo($tipo=''){
		if(!empty($tipo)){
			if($tipo=='N'){
				return array("estado_civil"=>array("ESTADO CIVIL",25,0,'L')
							,"sexo"=>array("SEXO",25,0,'L')
							,"fecha_nacimiento"=>array("F. NACIMIENTO",25,0,'L')
						);
			}else if($tipo=='J'){
				return array("nombre_representante"=>array("NOMBRES",42,0,'L')
							,"apellidos_representante"=>array("APELLIDOS",80,0,'L')
							,"dni_representante"=>array("DNI",20,1,'L')
						);
			}
		}
	}
	
	public function imprimir(){
		set_time_limit(0);
		
		if($_REQUEST['ver']=='R')
			$this->pdf_resumido();
		else
			$this->pdf_detallado();
	}
	
	public function pdf_resumido(){
		$data = $this->get_data();
		$head = $this->head_resumido();
		
		$this->load->library("pdf");
		
		$this->load_model(array( "seguridad.empresa","venta.cliente_view","seguridad.sucursal","general.zona","general.ubigeosorsa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetTitle(utf8_decode("REPORTE CLIENTES"), 11, null, true);
		
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		if(!empty($_REQUEST['idcliente'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"CLIENTE",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->cliente_view->find($_REQUEST['idcliente']);
			$this->pdf->Cell(5,3,utf8_decode($this->cliente_view->get("cliente")),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['idubigeo'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"RUTA",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->ubigeosorsa->find($_REQUEST['idubigeo']);
			$this->pdf->Cell(5,3,utf8_decode($this->ubigeosorsa->get("descripcion")),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['idzona'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"LOCALIDAD",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->zona->find($_REQUEST['idzona']);
			$this->pdf->Cell(5,3,utf8_decode($this->zona->get("zona")),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['tipo'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"TIPO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$tipo = "NATURAL";
			if($_REQUEST['tipo']=='J')
				$tipo = "JURIDICO";
			$this->pdf->Cell(5,3,utf8_decode($tipo),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['sin_direccion']) && $_REQUEST['sin_direccion']=='S'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SIN DIRECCION",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,utf8_decode('SI'),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['sin_telefono']) && $_REQUEST['sin_telefono']=='S'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SIN TELEFONO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,utf8_decode('SI'),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['sin_ruc']) && $_REQUEST['sin_ruc']=='S'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SIN RUC",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,utf8_decode('SI'),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['sin_dni']) && $_REQUEST['sin_dni']=='S'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SIN DNI",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,utf8_decode('SI'),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['sin_email']) && $_REQUEST['sin_email']=='S'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SIN EMAIL",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,utf8_decode('SI'),0,1,'L');
			$this->pdf->Ln();
		}
		
		/*----------------------------------------HEAD---------------------------------------------*/
		$this->pdf->SetFont('Arial','B',8);
		foreach ($head as $key => $val) {
			$this->pdf->Cell($val[1],9,$val[0],1,$val[2],$val[3]);
		}
		/*----------------------------------------HEAD---------------------------------------------*/
		
		
		/*----------------------------------------BODY---------------------------------------------*/
		$this->pdf->SetFont('Arial','',7.5);
		$item = 1;
		$this->pdf->SetDrawColor(204, 204, 204);
		foreach ($data as $key => $val) {
			/*For file autosize*/
				$values = array();
				$width = array();
				$pos = array();
				$fill = array();
			/*For file autosize*/

			foreach ($head as $k => $v) {
				$val['item']=$item;
				$width[] = $v[1];
				$values[] = utf8_decode(trim($val[$k]));
				$pos[] = $v[3];
				
				if($k=='dni'){
					if(!empty($val[$k]) && strlen($val[$k])<>$this->get_param("long_dni")){
						$fill[] = true;
					}else{
						$fill[] = false;
					}
				}else if($k=='ruc'){
					if(!empty($val[$k]) && strlen($val[$k])<>$this->get_param("long_ruc")){
						$fill[] = true;
					}else{
						$fill[] = false;
					}
				}else{
					$fill[] = false;
				}
			}
			
			$this->pdf->SetFillColor(230,106,100);
			$this->pdf->SetWidths($width);
			$this->pdf->Row($values, $pos, "Y", "Y",$fill);
			$item++;
		}
		/*----------------------------------------BODY---------------------------------------------*/
		
		$this->pdf->Output();
	}
	
	public function pdf_detallado(){
		$data		= $this->get_data();
		$repr		= $this->data_representante();
		// $clientes	= $this->clientes();
		$head		= $this->head_detallado();
		
		$this->load->library("pdf");
		
		$this->load_model(array( "seguridad.empresa","venta.cliente_view","seguridad.sucursal","general.zona","general.ubigeosorsa"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetTitle(utf8_decode("REPORTE CLIENTES"), 11, null, true);
		
		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		if(!empty($_REQUEST['idcliente'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"CLIENTE",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->cliente_view->find($_REQUEST['idcliente']);
			$this->pdf->Cell(5,3,utf8_decode($this->cliente_view->get("cliente")),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['idubigeo'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"RUTA",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->ubigeosorsa->find($_REQUEST['idubigeo']);
			$this->pdf->Cell(5,3,utf8_decode($this->ubigeosorsa->get("descripcion")),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['idzona'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"LOCALIDAD",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->zona->find($_REQUEST['idzona']);
			$this->pdf->Cell(5,3,utf8_decode($this->zona->get("zona")),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['tipo'])){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"TIPO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$tipo = "NATURAL";
			if($_REQUEST['tipo']=='J')
				$tipo = "JURIDICO";
			$this->pdf->Cell(5,3,utf8_decode($tipo),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['sin_direccion']) && $_REQUEST['sin_direccion']=='S'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SIN DIRECCION",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,utf8_decode('SI'),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['sin_telefono']) && $_REQUEST['sin_telefono']=='S'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SIN TELEFONO",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,utf8_decode('SI'),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['sin_ruc']) && $_REQUEST['sin_ruc']=='S'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SIN RUC",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,utf8_decode('SI'),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['sin_dni']) && $_REQUEST['sin_dni']=='S'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SIN DNI",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,utf8_decode('SI'),0,1,'L');
			$this->pdf->Ln();
		}
		
		if(!empty($_REQUEST['sin_email']) && $_REQUEST['sin_email']=='S'){
			$this->pdf->SetFont('Arial','B',10);
			$this->pdf->Cell(30,3,"SIN EMAIL",0,0,'L');
			$this->pdf->Cell(5,3,":",0,0,'C');
			$this->pdf->SetFont('Arial','',10);
			$this->pdf->Cell(5,3,utf8_decode('SI'),0,1,'L');
			$this->pdf->Ln();
		}
		
		/*----------------------------------------HEAD---------------------------------------------*/
		foreach($data as $k=>$v){
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->SetTextColor(22,160,133);
			$this->pdf->SetDrawColor(204, 204, 204);
			$this->pdf->Cell(200,6,"Cod: ".$v['idcliente'].") ".utf8_decode($v['cliente']),1,1,'L');
			
			$bloqueado		= 'SI';
			if($v["bloqueado"]=='N'){
				$bloqueado		= 'NO';
			}
			$linea_credito	= 'SI';
			if($v["linea_credito"]=='N'){
				$linea_credito		= 'NO';
			}
			
			$sexo = '';
			if(!empty($v['sexo'])){
				if($v['sexo']=='M')
					$sexo='MASCULINO';
				else
					$sexo='FEMENINO';
			}
			$persona='JURIDICO';
			if($v['tipo']=='N')
				$persona='NATURAL';
			
			/* FORMATO PLANTILLA*/
			$this->pdf->SetTextColor(0,0,0);
			$this->pdf->Cell(40,6,"DIRECCION",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,utf8_decode($v['direccion']),'B',1,'L');
			
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Cell(40,6,"RUTA",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,utf8_decode($v['ruta']),'B',1,'L');
			
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Cell(40,6,"LOCALIDAD",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,utf8_decode($v['localidad']),'B',1,'L');
			
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Cell(40,6,"RUC",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');
			if(!empty($v['ruc']) && strlen($v['ruc'])<>$this->get_param("long_ruc")){
				$this->pdf->SetTextColor(230,106,100);
				$this->pdf->Cell(155,6,utf8_decode($v['ruc']." ** Hey..! falta ".($this->get_param("long_ruc") - strlen($v['ruc'])." en éste documento")),'B',1,'L');
				$this->pdf->SetTextColor(0,0,0);
			}else{
				$this->pdf->Cell(155,6,utf8_decode($v['ruc']),'B',1,'L');
			}
			$this->pdf->SetFont('Arial','B',8);
			
			$this->pdf->Cell(40,6,"DNI",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');
			if(!empty($v['dni']) && strlen($v['dni'])<>$this->get_param("long_dni")){
				$this->pdf->SetTextColor(230,106,100);
				$this->pdf->Cell(155,6,utf8_decode($v['dni']." ** Hey..! falta ".($this->get_param("long_ruc") - strlen($v['ruc'])." en éste documento")),'B',1,'L');
				$this->pdf->SetTextColor(0,0,0);
			}else{
				$this->pdf->Cell(155,6,utf8_decode($v['dni']),'B',1,'L');
			}
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Cell(40,6,"TELEFONO",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,utf8_decode($v['telefono']),'B',1,'L');
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Cell(40,6,"E-MAIL",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,utf8_decode($v['cliente_email']),'B',1,'L');
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Cell(40,6,"TIENE LINEA CREDITO?",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,$linea_credito,'B',1,'L');
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Cell(40,6,"MONTO LINEA CREDITO",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,number_format($v['limite_credito'],2,'.',','),'B',1,'L');
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Cell(40,6,"ESTA BLOQUEADO?",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,($bloqueado),'B',1,'L');
			$this->pdf->SetFont('Arial','B',8);
			$this->pdf->Cell(40,6,"PERSONA",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,($persona),'B',1,'L');
			/* FORMATO PLANTILLA*/
			if($v['tipo']=='N'){
				$this->pdf->SetFont('Arial','B',8);
				$this->pdf->Cell(40,6,"OCUPACION",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,($v['ocupacion']),'B',1,'L');
				$this->pdf->SetFont('Arial','B',8);
				$this->pdf->Cell(40,6,"ESTADO CIVIL",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,($v['estado_civil']),'B',1,'L');
				$this->pdf->SetFont('Arial','B',8);
				$this->pdf->Cell(40,6,"SEXO",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,($sexo),'B',1,'L');
				$this->pdf->SetFont('Arial','B',8);
				$this->pdf->Cell(40,6,"FECHA NACIMIENTO",'B',0,'L');$this->pdf->SetFont('Arial','',8);$this->pdf->Cell(5,6,":",0,0,'L');$this->pdf->Cell(155,6,($v['fecha_nacimiento']),'B',1,'L');
			}else if($v['tipo']=='J'){
				$this->pdf->SetFont('Arial','B',8);
				$this->pdf->Ln(3);
				$this->pdf->Cell(200,6,"REPRESENTANTE(S)",0,1,'L');
				
				$format_array = $this->head_tipo($v['tipo']);
				foreach($format_array as $kk=>$vv){
					$this->pdf->SetFont('Arial','B',8);
					$this->pdf->SetTextColor(0,0,0);
					$this->pdf->Cell($vv[1],4,$vv[0],1,$vv[2],$vv[3]);
				}
				
				$new_repre = $this->seleccion($repr,array("idcliente"=>$v['idcliente']));
				$this->pdf->SetFont('Arial','',8);
				if(!empty($new_repre)){
					foreach($new_repre as $x=>$y){
						foreach($format_array as $kk=>$vv){
							$this->pdf->Cell($vv[1],4,$y[$kk],1,$vv[2],$vv[3]);
						}
					}
				}else{
					$this->pdf->Cell(142,6,"SIN REPRESENTANTE",1,1,'C');
				}
			}
			
			
			/*For file autosize*/
				// $values = array();
				// $width = array();
				// $pos = array();
				// $fill = array();
			/*For file autosize*/
			
			// $this->pdf->SetTextColor(0,0,0);
			// foreach ($head as $key => $val) {
				// $this->pdf->Cell($val[1],4,$val[0],1,$val[2],$val[3]);
				// $width[] = $val[1];
				// $values[] = utf8_decode(trim($v[$key]));
				// $pos[] = $val[3];
				
				// if($key=='dni'){
					// if(!empty($v[$key]) && strlen($v[$key])<>$this->get_param("long_dni")){
						// $fill[] = true;
					// }else{
						// $fill[] = false;
					// }
				// }else if($key=='ruc'){
					// if(!empty($v[$key]) && strlen($v[$key])<>$this->get_param("long_ruc")){
						// $fill[] = true;
					// }else{
						// $fill[] = false;
					// }
				// }else{
					// $fill[] = false;
				// }
			// }
			// $this->pdf->SetFont('Arial','',7.3);
			// $this->pdf->SetDrawColor(204, 204, 204);
			// $this->pdf->SetFillColor(230,106,100);
			// $this->pdf->SetWidths($width);
			// $this->pdf->Row($values, $pos, "Y", "Y",$fill);
			// $format_array = $this->head_tipo($v['tipo']);
			// $this->pdf->Ln();
			
			// foreach($format_array as $kk=>$vv){
				// $this->pdf->SetFont('Arial','B',8);
				// $this->pdf->SetTextColor(0,0,0);
				// $this->pdf->Cell($vv[1],4,$vv[0],1,$vv[2],$vv[3]);
			// }
			$this->pdf->Ln(8);
		}
		/*----------------------------------------HEAD---------------------------------------------*/
		$this->pdf->Output();
	}
	
	public function exportar(){
		set_time_limit(0);
		
		if($_REQUEST['ver']=='R')
			$this->excel_resumido();
		else
			$this->excel_detallado();
	}
	
	public function excel_resumido(){
		ini_set('memory_limit', '2048M');
		$head = $this->head_resumido('excel');
		$data = $this->get_data();
		
		$this->load_model(array( "seguridad.empresa","venta.cliente_view","seguridad.sucursal","general.zona","general.ubigeosorsa"));
		
		$this->load->library("phpexcel");
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE CLIENTES",true);
		
		$filename='reportecliente_resumido'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');
		
		$col=9;
		if(!empty($_REQUEST['idcliente'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'CLIENTE : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->cliente_view->find($_REQUEST['idcliente']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,($this->cliente_view->get("cliente")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
			
		}
		if(!empty($_REQUEST['idubigeo'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'RUTA : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->ubigeosorsa->find($_REQUEST['idubigeo']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,($this->ubigeosorsa->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		if(!empty($_REQUEST['idzona'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'LOCALIDAD : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->zona->find($_REQUEST['idzona']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,($this->zona->get("zona")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		if(!empty($_REQUEST['tipo'])){
			$tipo = "NATURAL";
			if($_REQUEST['tipo']=='J')
				$tipo = "JURIDICO";
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'TIPO : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,($tipo));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		if(!empty($_REQUEST['sin_direccion']) && $_REQUEST['sin_direccion']=='S'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SIN DIRECCION : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,('SI'));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		if(!empty($_REQUEST['sin_telefono']) && $_REQUEST['sin_telefono']=='S'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SIN TELEFONO : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,('SI'));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		if(!empty($_REQUEST['sin_ruc']) && $_REQUEST['sin_ruc']=='S'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SIN RUC : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,('SI'));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
			
		}
		if(!empty($_REQUEST['sin_dni']) && $_REQUEST['sin_dni']=='S'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SIN DNI : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,('SI'));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		if(!empty($_REQUEST['sin_email']) && $_REQUEST['sin_email']=='S'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SIN EMAIL : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,('SI'));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		/************************** CABECERA *****************************************/
		$alfabeto = 65;
		foreach($head as $k=>$v){
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->applyFromArray(
					array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $v[0]);
			
			$alfabeto++;
		}
		/************************** CABECERA *****************************************/
		
		
		/************************** CUERPO *****************************************/
		$alfabeto = 65;
		$col++;
		$item=0;
		foreach($data as $key=>$val){
			$alfabeto=65;
			$item++;
			$val['item'] = $item;
			foreach($head as $k=>$v){
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $val[$k]);
				
				$alfabeto++;
			}
			$col++;
		}
		/************************** CUERPO *****************************************/
		
		$objWriter->save('php://output');
	}
	
	public function excel_detallado(){
		ini_set('memory_limit', '2048M');
		$head = $this->head_resumido('excel');
		$data = $this->get_data();
		
		$this->load_model(array( "seguridad.empresa","venta.cliente_view","seguridad.sucursal","general.zona","general.ubigeosorsa"));
		
		$this->load->library("phpexcel");
		$objReader = PHPExcel_IOFactory::createReader('Excel2007');
		$Oexcel = $objReader->load("./application/views/plantilla/plantilla.xlsx");
		$this->insert_logoExcel($Oexcel,"REPORTE CLIENTES DETALLADO",true);
		
		$col=9;
		if(!empty($_REQUEST['idcliente'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'CLIENTE : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->cliente_view->find($_REQUEST['idcliente']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,($this->cliente_view->get("cliente")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
			
		}
		if(!empty($_REQUEST['idubigeo'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'RUTA : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->ubigeosorsa->find($_REQUEST['idubigeo']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,($this->ubigeosorsa->get("descripcion")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		if(!empty($_REQUEST['idzona'])){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'LOCALIDAD : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$this->zona->find($_REQUEST['idzona']);
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,($this->zona->get("zona")));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		if(!empty($_REQUEST['tipo'])){
			$tipo = "NATURAL";
			if($_REQUEST['tipo']=='J')
				$tipo = "JURIDICO";
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'TIPO : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,($tipo));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		if(!empty($_REQUEST['sin_direccion']) && $_REQUEST['sin_direccion']=='S'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SIN DIRECCION : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,('SI'));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		if(!empty($_REQUEST['sin_telefono']) && $_REQUEST['sin_telefono']=='S'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SIN TELEFONO : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,('SI'));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		if(!empty($_REQUEST['sin_ruc']) && $_REQUEST['sin_ruc']=='S'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SIN RUC : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,('SI'));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
			
		}
		if(!empty($_REQUEST['sin_dni']) && $_REQUEST['sin_dni']=='S'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SIN DNI : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,('SI'));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		if(!empty($_REQUEST['sin_email']) && $_REQUEST['sin_email']=='S'){
			$Oexcel->getActiveSheet()->getStyle('A'.$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->setCellValue('A'.$col, 'SIN EMAIL : ');
			$Oexcel->setActiveSheetIndex(0)->mergeCells('A'.$col.':B'.$col);
			
			$Oexcel->getActiveSheet()->setCellValue('C'.$col,('SI'));
			$Oexcel->setActiveSheetIndex(0)->mergeCells('C'.$col.':D'.$col);
			$col++;
		}
		
		/************************** CABECERA *****************************************/
		$alfabeto = 65;
		foreach($head as $k=>$v){
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->getFont()->setBold(true);
			$Oexcel->getActiveSheet()->getStyle(chr($alfabeto).$col)->applyFromArray(
					array('borders' => array(
								'bottom'    => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'right'     => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'left'      => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM),
								'top'       => array('style' =>PHPExcel_Style_Border::BORDER_MEDIUM)
							)
				)
			);
			$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $v[0]);
			
			$alfabeto++;
		}
		/************************** CABECERA *****************************************/
		
		/************************** BODY *****************************************/
		$col++;
		foreach($data as $key=>$val){
			$alfabeto = 65;
			$val["item"] = $key+1;
			$columna = 0;
			foreach($head as $k=>$v){
				if($k=='dni' || $k=='ruc'){
					if($k=='dni' && strlen(trim($val[$k]))>0 && strlen(trim($val[$k]))<>$this->get_param("long_dni"))
						$this->cellColorByColumnAndRow($Oexcel, $columna, $col, "e66a64");
					else if($k=='ruc' && strlen(trim($val[$k]))>0 && strlen(trim($val[$k]))<>$this->get_param("long_ruc"))
						$this->cellColorByColumnAndRow($Oexcel, $columna, $col, "e66a64");
					$val[$k]=" ".$val[$k];
				}
				$Oexcel->getActiveSheet()->setCellValue(chr($alfabeto).$col, $val[$k]);
				$alfabeto++;
				$columna++;
			}
			$col++;
		}
		/************************** BODY *****************************************/
		
		$filename='reporteclientedetallado'.date("dmYhis").'.xls'; //save our workbook as this file name
        header('Content-Type: application/vnd.ms-excel'); //mime type
        header('Content-Disposition: attachment;filename="'.$filename.'"'); //tell browser what's the file name
        header('Cache-Control: max-age=0'); //no cache 
        $objWriter = PHPExcel_IOFactory::createWriter($Oexcel, 'Excel5');  
		$objWriter->save('php://output');
	}
}
?>