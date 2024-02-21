<?php

include_once "Controller.php";

class Home extends Controller {

	public function init_controller() {
		$this->set_title("Bienvenido");
		$this->set_subtitle("Haga clic en el modulo que desea ingresar");
		
		if(!$this->with_tabs) {
			if(!$this->in_session("idsistema")) {
				$this->set_subtitle("Haga clic en el sistema que desea ingresar");
				$this->set_content($this->get_sistemas());
			}
		}
		
		$this->session->unset_userdata('menu_p');
		$this->session->unset_userdata('menu_c');
	}
	
	public function index($tpl = "") {
		// print_r($this->get_var_session());exit;
		if(!$this->with_tabs) {
			parent::index();
			return;
		}
		$var = $this->get_param("mostrar_dashboard", "S");		
		$idusuariodos = $this->get_var_session("idusuario");		
		$ver_dash = ($var == "S");//Variable para hacer pruebas, hasta que se termine de armar todo el dash
		$data["title"] = "Dashboard1"; 
		// echo "<pre>";
		// print_r($this->session);
		// exit;
		if(!$ver_dash){
			$data["content"] = "Algun contenido aqui"; //Enviar una vista por el array content
			$str = $this->load->view("content_tab", $data, true); //,trues
		}else{			
						
			$data["lista"] = $this->ventas(); /*Ventas Total Mes sucursales*/
			//$data["credito"] = $this->ventas_credito(); /*Venta Total Mes al credito*/
			$data["compras"] = $this->compras(); /*Compras Total Mes*/
			//$data["compras_credito"] = $this->compras_credito(); /*Compras al credito por pagar*/			
			$data["reporteventas"] = $this->ventas_total(); /*Reporte*/			
			$data["ventasmescon"] = $this->ventas_mescon(); /*Reporte*/			
			//$data["ventasmescred"] = $this->ventas_mescred(); /*Reporte*/		
			$data["ventas_d"] = $this->ventas_c(); /*Reporte*/			
			$data["sucursal"] = $this->sucursal(); /*Reporte Ventas Sucursales*/				
			$data["content"] = file_get_contents("application/views/content_tab2.php"); //Enviar una vista por el array content			
			$str = $this->load->view("content_tab2", $data, true); //,true
								
		}		
		$this->show($str, null, true);
		
	}
	
	public function end_controller() {
		return null;
	}
	
	public function form() {
		return null;
	}
	
	public function grilla() {
		return null;
	}
	
	public function cambiar_sucursal($idsucursal) {
		$this->db->where("idusuario",$this->get_var_session("idusuario"));
		$this->db->where("idsucursal",$idsucursal);
		$query = $this->db->get("seguridad.acceso_empresa");
		
		if($query->num_rows() > 0) {
			$row = $query->row();
			
			$query1 = $this->db->where("idsucursal",$row->idsucursal)->get("seguridad.sucursal");
			$query2 = $this->db->where("idperfil",$row->idperfil)->get("seguridad.perfil");
			
			$this->session->set_userdata('idsucursal', $row->idsucursal);
			$this->session->set_userdata('es_superusuario', $row->es_superusuario);
			$this->session->set_userdata('control_reporte', $row->control_reporte);
			$this->session->set_userdata('sucursal', $query1->row()->descripcion);
			$this->session->set_userdata('idperfil', $row->idperfil);
			$this->session->set_userdata('idempresa', $query1->row()->idempresa);
			if( !empty($query2->row()->descripcion) )
				$this->session->set_userdata('perfil', $query2->row()->descripcion);
			else
				$this->session->set_userdata('perfil', '');

			$this->insert_session("S");
			$this->insert_session("I");
		}
		redirect('home/');
	}
	
	public function get_sistemas() {
		$this->db->select("s.*");
		$this->db->from("seguridad.acceso_sistema a");
		$this->db->join('seguridad.sistema s', 's.idsistema = a.idsistema');
		$this->db->where('a.idsucursal', $this->get_var_session("idsucursal"));
		$this->db->where('s.estado', 'A');
		$this->db->order_by("s.orden", "ASC");
		
		$arr = array();
		$query = $this->db->get();
		if($query->num_rows() > 0) {
			$arr = $query->result_array();
		}
		
		return $this->load->view("sistemas", array("sistemas"=>$arr), true);
	}
	
	public function cambiar_sistema($idsistema) {
		$this->db->select("s.*");
		$this->db->from("seguridad.acceso_sistema a");
		$this->db->where("a.idsucursal",$this->get_var_session("idsucursal"));
		$this->db->where("a.idsistema",$idsistema);
		$this->db->join("seguridad.sistema s", "s.idsistema=a.idsistema");
		$query = $this->db->get();
		
		if($query->num_rows() > 0) {
			$row = $query->row();
			
			$this->session->set_userdata('idsistema', $row->idsistema);
			$this->session->set_userdata('sistema', $row->descripcion);
		}
		$this->insert_session("S");
		$this->insert_session("I");
		redirect('home/');
	}
	
	public function seleccion_sistema($redirect = true) {
		// eliminamos variable de session sistema
		$this->session->unset_userdata('idsistema');
		$this->session->unset_userdata('sistema');
		$this->session->unset_userdata('menu_p');
		$this->session->unset_userdata('menu_c');
		$this->insert_session("S");
		if($redirect) {
			redirect('home/');
		}
	}
	
	public function seleccion_sucursal() {
		// eliminamos variable de session
		$this->seleccion_sistema(false);
		
		// eliminamos variable de session sucursal
		$this->session->unset_userdata('idsucursal');
		$this->session->unset_userdata('idempresa');
		$this->session->unset_userdata('sucursal');
		$this->session->unset_userdata('idperfil');
		$this->session->unset_userdata('perfil');
		$this->insert_session("S");
		redirect('home/');
	}
	
	public function default_values() {
		$this->load_model("seguridad.datos_usuario");
		
		$post = $this->input->post();
		$idsucursal = $this->get_var_session("idsucursal");
		$idusuario = $this->get_var_session("idusuario");
		
		// eliminamos los datos almacenados
		$sql = "DELETE FROM seguridad.datos_usuario WHERE idsucursal=? AND idusuario=?";
		$this->db->query($sql, array($idsucursal, $idusuario));
		
		$this->datos_usuario->set("idsucursal", $idsucursal);
		$this->datos_usuario->set("idusuario", $idusuario);
		
		if( ! empty($post["datos"])) {
			foreach($post["datos"] as $row) {
				if( ! empty($row["valor"])) {
					$this->datos_usuario->set($row);
					$this->datos_usuario->text_uppercase(false);
					$this->datos_usuario->insert(null, false);
				}
			}
		}
		
		$this->response($this->datos_usuario->get_fields());
	}
	
	public function insert_session($option='I'){
		/*
		$option = I => Ingreso al sistema
		$option = S => Salida del sistema
		*/
		$this->load_model("auditoria.sesion");
		if($option=='I'){
			$dato["idusuario"]	= $this->get_var_session("idusuario");
			$dato["idperfil"]	= $this->get_var_session("idperfil");
			$dato["idsucursal"]	= $this->get_var_session("idsucursal");
			$dato["idempresa"]	= $this->get_var_session("idempresa");
			$dato["fecha"]		= date("Y-m-d");
			$dato["hora"]		= date("H:i:s");
			$dato["estado"]		= "A";
			$this->sesion->insert($dato);
		}else{
			$dato["idusuario"]	= $this->get_var_session("idusuario");
			// $this->sesion->delete($dato);
			$this->db->query("DELETE FROM auditoria.sesion WHERE idusuario='{$dato["idusuario"]}';");
		}
	}
	
	/*Dashboard*/
	public function ventas() {
		$idempresa = $this->get_var_session("idempresa")?$this->get_var_session("idempresa"):0;
		$sql = "SELECT round((sum(venta.subtotal))::DECIMAL, 2)::TEXT as venta
				FROM venta.venta, seguridad.sucursal
				WHERE sucursal.idsucursal = venta.idsucursal 
				and venta.estado = 'A' 
				--AND venta.IDUSUARIO IN ('1') 
				and extract(Year from venta.fecha_venta) = extract( 'Year' from now()) 
				and venta.idtipodocumento in ('12','14')
				and extract(MONTH from venta.fecha_venta) = extract( 'Month' from now()) 
				and sucursal.idempresa = ".$idempresa.";";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
/*
	public function ventas_credito() {
		$idempresa = $this->get_var_session("idempresa")?$this->get_var_session("idempresa"):0;
		$sql = "SELECT coalesce(sum(subtotal), '0.00') as v_credito FROM venta.venta, seguridad.sucursal 
			where sucursal.idsucursal = venta.idsucursal  
			and venta.idtipodocumento in ('12','14') and
			venta.estado = 'A' and venta.con_credito = 'S' 
			and extract(Year from venta.fecha_venta) = extract (Year from Now()) 
			and extract(MONTH from venta.fecha_venta) = extract( 'Month' from now()) 
			and sucursal.idempresa = ".$idempresa.";";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
*/
	public function compras() {
		$pago_contado = $this->get_param('idpago_compra_contado')?$this->get_param('idpago_compra_contado'):0;
		/*$sql = "SELECT round((sum(subtotal + igv))::DECIMAL, 2)::TEXT as compras 
		FROM compra.compra 
		where estado = 'A' and extract( 'Day' from now()) = extract(Day from fecha_compra) 
		and extract ('Month' from now()) = extract(Month from fecha_compra) and idforma_pago_compra = 1"; Mes actual*/
		$sql = "SELECT COALESCE(round((sum(subtotal + igv))::DECIMAL, 2)::TEXT,'0.00') as compras 
		FROM compra.compra 
		where estado = 'A' 
		and extract(month from fecha_compra)  = extract ('month' from now())  ;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
/*
	public function compras_credito() {
		$pago_credito = $this->get_param('idpago_compra_credito')?$this->get_param('idpago_compra_credito'):0;
		//$sql = "SELECT round((sum(subtotal + igv))::DECIMAL, 2)::TEXT as compras_credito FROM compra.compra where estado = 'A' and extract( 'Day' from now()) = extract(Day from fecha_compra) and extract ('Month' from now()) = extract(Month from fecha_compra) and idforma_pago_compra = 2"; Mes actual
		$sql = "SELECT COALESCE(round((sum(subtotal + igv))::DECIMAL, 2)::TEXT,'0.00') as compras_credito
		FROM compra.compra where estado = 'A' 
		and extract(Month from fecha_compra)  = extract ('Month' from now())
		and idforma_pago_compra = $pago_credito ;";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
*/
	public function ventas_total() {
		$idempresa = $this->get_var_session("idempresa")?$this->get_var_session("idempresa"):0;		
		$sql="SELECT coalesce(sum(subtotal), '0.00') as total, extract(Day from venta.fecha_venta) as dia 
		FROM venta.venta, seguridad.sucursal 
			where sucursal.idsucursal = venta.idsucursal and venta.estado = 'A' 
			
			and extract(Year from venta.fecha_venta) = extract (Year from Now()) 
			and extract(MONTH from venta.fecha_venta) = extract( 'Month' from now()) 
			and venta.idtipodocumento in ('12','14')
			and sucursal.idempresa = $idempresa
			GROUP BY venta.fecha_venta order by venta.fecha_venta";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	


	public function ventas_c() {
		$idempresa = $this->get_var_session("idempresa")?$this->get_var_session("idempresa"):0;
		
		$sql = "SELECT coalesce(sum(subtotal), '0.00') as ventas_d, extract(Day from venta.fecha_venta) as dia 
		FROM venta.venta, seguridad.sucursal 
			where sucursal.idsucursal = venta.idsucursal and venta.estado = 'A' 
			and venta.idtipodocumento in ('12','14')
			and extract(Year from venta.fecha_venta) = extract (Year from Now()) 
			and extract(MONTH from venta.fecha_venta) = extract( 'Month' from now()) 
			and sucursal.idempresa = $idempresa and venta.con_credito = 'S' 
			GROUP BY venta.fecha_venta order by venta.fecha_venta";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
		public function ventas_mescon() {
		$idempresa = $this->get_var_session("idempresa")?$this->get_var_session("idempresa"):0;		
		$sql="SELECT coalesce(sum(subtotal), '0.00') as totalcon,extract('MONTH' from venta.fecha_venta)  mes
		FROM venta.venta, seguridad.sucursal 
			where sucursal.idsucursal = venta.idsucursal and venta.estado = 'A' 
			and venta.idtipodocumento in ('12','14')
			and extract(Year from venta.fecha_venta) = extract (Year from Now()) 
			and sucursal.idempresa = $idempresa and venta.con_credito = 'N' 
			GROUP BY extract('MONTH' from venta.fecha_venta)  order by extract('MONTH' from venta.fecha_venta) ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
	
	/*
		public function ventas_mescred() {
		$idempresa = $this->get_var_session("idempresa")?$this->get_var_session("idempresa"):0;		
		$sql="SELECT coalesce(sum(subtotal), '0.00') as totalcred,extract('MONTH' from venta.fecha_venta)  mes
		FROM venta.venta, seguridad.sucursal 
			where sucursal.idsucursal = venta.idsucursal and venta.estado = 'A' 
			and venta.idtipodocumento in ('12','14')
			and extract(Year from venta.fecha_venta) = extract (Year from Now()) 
			and sucursal.idempresa = $idempresa and venta.con_credito = 'S' 
			GROUP BY extract('MONTH' from venta.fecha_venta)  order by extract('MONTH' from venta.fecha_venta) ";
		$query = $this->db->query($sql);
		return $query->result_array();
	}
*/
	public function sucursal() {
		//$idusuariodos = $this->get_var_session("idusuario")?$this->get_var_session("idusuario"):0;
		//echo $idusuariodos;
		
		$idempresa = $this->get_var_session("idempresa")?$this->get_var_session("idempresa"):0;
		//if($idusuariodos=='2'){
		$sql = "SELECT sucursal.idsucursal, sucursal.descripcion as nombre, round((sum(subtotal))::DECIMAL, 2)::TEXT as venta, (SELECT round((sum(venta.subtotal))::DECIMAL, 2)::TEXT as venta
			FROM venta.venta, seguridad.sucursal
			WHERE sucursal.idsucursal = venta.idsucursal and venta.estado = 'A' and extract(Year from venta.fecha_venta) = extract( 'Year' from now()) 
			and extract(MONTH from venta.fecha_venta) = extract( 'Month' from now()) 
			and sucursal.idempresa = $idempresa) as total 
			FROM venta.venta, seguridad.sucursal
			WHERE sucursal.idsucursal = venta.idsucursal and venta.estado = 'A' 
			
			and extract(Year from fecha_venta) = extract( 'Year' from now()) 
			and extract(MONTH from venta.fecha_venta) = extract( 'Month' from now()) 
			and sucursal.idempresa = $idempresa 
			GROUP BY venta.idsucursal, sucursal.idsucursal, nombre
			ORDER BY venta.idsucursal;";
		$query = $this->db->query($sql);
		return $query->result_array();
		//}
	}
	
	public function cabecera_alerta(){
		return array('item'=> array('ITEM',10,0,'L')
					,'sms_alerta' => array('ALERTA',173,0,'L')
					,'hora_alerta' => array('HORA',20,1,'C')
				);
	}
	
	/*
	public function crontab(){
		set_time_limit(0);
		// Para Hoja de ruta 
		$rolcobrador 		= $this->get_param("idrolcobrador")?$this->get_param("idrolcobrador"):'0';
		$q = $this->db->query("SELECT*FROM seguridad.sucursal WHERE estado='A';");
		$sucursales = $q->result_array();
		
		foreach($sucursales as $k=>$val){
			$query = $this->db->where("estado", "A")->where("idtipoempleado", $rolcobrador)->where("idsucursal", $val["idsucursal"])->get("cobranza.view_cobradores");
			foreach($query->result_array() as $key=>$v){
				$_POST["idcobrador"] = $v['idusuario'];
				$_POST["idsucursal"] = $val['idsucursal'];
				$this->genera_hojaruta();
			}
		}
		
		/ Para Estado de creditos /
		$q = $this->db->query("SELECT c.idcredito
							,c.idventa
							,c.idsucursal
							,cliente
							,estado_credito
							,fecha_vencimiento+dias_gracia fecha_venc
							,CASE WHEN (fecha_vencimiento+dias_gracia)>CURRENT_DATE THEN 'PUNTUAL' ELSE 'ATRAZADO' END status  
							FROM credito.credito_view c 
							LEFT JOIN(
								SELECT MIN(fecha_vencimiento) fecha_vencimiento,idcredito FROM credito.letra WHERE letra.estado='A' AND letra.pagado='N' GROUP BY idcredito
							) l ON l.idcredito=c.idcredito
							WHERE c.estado='A' AND c.pagado='N'
							ORDER BY fecha_venc;");
		$creditos = $q->result_array();
		foreach($creditos as $k=>$val){
			$id_estado_credito = "";
			if($val["status"]=='PUNTUAL'){
				$id_estado_credito = 1;
			}else if($val["status"]=='ATRAZADO'){
				$id_estado_credito = 2;
			}
			
			if(!empty($id_estado_credito)){
				$this->db->query("UPDATE credito.credito SET id_estado_credito='$id_estado_credito' WHERE idcredito='{$val['idcredito']}';");
			}
		}
		echo "Success..!! ".date("H:i:s");
	}
	
	public function see_alert(){
		set_time_limit(0);
		$this->load->library("pdf");
		$this->load_model(array( "seguridad.empresa","general.marca","general.modelo","general.categoria","venta.cliente_view","venta.tipo_venta","general.moneda","seguridad.sucursal","seguridad.view_usuario","venta.tipo_documento"));
		$this->empresa->find($this->get_var_session("idempresa"));
		
		$this->pdf->SetLogo(FCPATH."app/img/empresa/".$this->empresa->get("logo"));
		
		$this->pdf->SetTitle(utf8_decode("TODAS LAS ALERTAS"), 11, null, true);

		$this->pdf->AliasNbPages(); // para el conteo de paginas
		$this->pdf->SetLeftMargin(4);
		$this->pdf->setFillColor(249, 249, 249);
        $this->pdf->SetDrawColor(204, 204, 204);

		$this->pdf->AddPage();
		$this->pdf->SetFont('Arial','',9);

		$this->pdf->Cell(65,3,$this->empresa->get("descripcion"),0,0,'L');
		$this->pdf->Cell(106,3,date('d/m/Y'),0,0,'R');
		$this->pdf->Cell(20,3,date('H:i:s'),0,1,'R');
		$this->pdf->Cell(45,3,"RUC: ".$this->empresa->get("ruc"),0,1,'C');
		$this->pdf->Ln(5);
		
		$this->pdf->SetFont('Arial','B',9);
		foreach ($this->cabecera_alerta() as $key => $val) {
			$this->pdf->Cell($val[1],6,$val[0],1,$val[2],$val[3]);
		}
		$data = $this->alertas();
		$this->pdf->SetFont('Arial','',8.3);
		$i = 1;
		$tipo = '';
		if(count($data['list'])>0){
			$tipo = $data['list'][0]["tipo_alerta"];
		}
		$array_fill=array("cuentas_pagar","productos");
		foreach($data['list'] as $k=>$v){
			$v["item"] = $i;
			//For file autosize
				$values = array();
				$width = array();
				$pos = array();
				$fill = array();
			//For file autosize
			$pintar =  true;
			$kk='';
			if(in_array($v["tipo_alerta"],$array_fill)){
				$pintar =  true;
			}else{
				$pintar = false;
			}
			foreach ($this->cabecera_alerta() as $key => $val) {
				// print_r($key);exit;
				// var_dump(in_array($key,$array_fill));exit;
				
				$width[] = $val[1];
				$values[] = utf8_decode(trim($v[$key]));
				$pos[] = $val[3];
				$fill[] = $pintar ;
				// $kk = $key;
			}
			$this->pdf->setFillColor(235, 235, 235);
			$this->pdf->SetWidths($width);
			$this->pdf->Row($values, $pos, "Y", "Y",$fill);
			$i++;
		}
		$this->pdf->Output();
	}*/
}
?>