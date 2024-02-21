var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		
	},
	guardar_boton:function(){
		alert('Here.....')
	}
}

function guardar() {
	var data = $("#form-data").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect(_controller);
			});
		});
}

function modificar(id) {
	ajax.post({url: _base_url+"modulo/get/"+id, data: {}}, function(res) {
		$("#idmodulo").val(res.idmodulo);
		$("#idsystem").val(res.idsistema);
		$("#idpadrecito").val(res.idpadre);
		
		if( res.idpadre==0 ){//QUITAMOS LA COLUMNA PARA LOS BOTONES
			$('.content_form').removeClass('col-sm-6').addClass('col-sm-12');
			$('.content_boton').hide();
		}else{
			$('.content_form').removeClass('col-sm-12').addClass('col-sm-6');
			$('.content_boton').show();
			LoadBoton(res.idmodulo);//Revisar...
		}
		
		$("#descripcion").val(res.descripcion);
		$("#url").val(res.url);
		$("#orden").val(res.orden);
		$("#icono_preview").html('<i class="fa '+res.icono+'"></i>');
		$("#icono").val(res.icono);
		$("#modal-form").modal('show');
	});
}

function eliminar(id) {
	ventana.confirm({titulo:"Confirmar", 
	mensaje:"¿Desea eliminar el registro seleccionado?", 
	textoBotonAceptar: "Eliminar"}, function(ok){
		if(ok) {
			ajax.post({url: _base_url+"modulo/eliminar/"+id, data: {}}, function(res) {
				ventana.alert({titulo: "Modulo eliminado", 
				mensaje: "El modulo ha sido eliminado correctamete."}, function() {
					redirect(_controller);
				});
			});
		}
	});
}

// $(".ibox-content button.btn").removeAttr("id");
$(".form-inline button.btn").each(function(i,j){
	nueva_clase = $(this).attr("id");	
	$(this).addClass(nueva_clase);
	$(this).removeAttr('id');
});

$("a.select_icon").click(function(e) {
	var icon = $(this).data("icon");
	$name_modal = $(this).attr('data-modal');
	
	setIcon(icon,$name_modal);
	$("#"+$name_modal+" .icono").val(icon);
});

function setIcon(icon,modal){
	$("#"+modal+" .icono_preview").html('<i class="fa '+icon+'"></i>');
}

function LoadBoton($idmodulo){
	ajax.post({url: _base_url+"modulo/ListBotones",data:$("#form-data").serialize()}, function(res) {
		if(res){
			$("#idboton_sel").html(res);
			
			if($idmodulo!=''){
				LoadDetalleBoton();
			}
		}else
			$("#idboton_sel").empty();
		
	});
}

function LoadDetalleBoton(){
	ajax.post({url: _base_url+"modulo/ListDetalleBoton",data:$("#form-data").serialize()}, function(rpt) {
		if(rpt){
			$("#detalle_boton").html(rpt);
			LoadBoton('');
		}else
			$("#detalle_boton").empty();
		
	});
}

var  continuar = true;
		botones(continuar);
		$(".label_padre").hide();
		$('.save').addClass('disabled');

		$(document).on('click','.delete_boton',function(){
			$tr_ = $(this).parent('td').parent('tr');
			$tr_.remove();
			LoadBoton('');
		})

		$(document).on('click','a.here_boton',function(){
			$id_boton = $(this).parent('li').attr('data-value');
			if($id_boton)
				AddDetalleBoton($id_boton,$(this).text(),$(this).attr('data-icon'),'');
		});
		
		var nestable = UIkit.nestable('.uk-nestable',{
			maxDepth:2
			,group:'widgets'
			
		});
		
		$('.save_data').bind('click',function(e){
			e.preventDefault();
			s = true;
			s = s && $('#descripcion').required();
			s = s && $('#orden').required();
			s = s && $('#icono').required();
			
			if(s){
				guardar();
			}
			
		});
		
		// $('.manejable').click(function(e) {
		$(document).on('click','.manejable',function(){
			if(continuar){
				if(  $(this).hasClass('seleccinado') ){
					$(this).removeClass('seleccinado');
				}else{
					$('.manejable').removeClass('seleccinado');
					$(this).addClass('seleccinado');
					// alert(0);
					$tipo	=	$('.seleccinado').attr('data-type');
					$nivel	=	$('.seleccinado').attr('data-level');

					if($tipo=='system'){
						$('.btn_editar,.btn_delete,.btn_eliminar').addClass('disabled');
						$('.btn_nuevo').removeClass('disabled');
					}else{
						if($nivel<=2){
							$('.btn_editar,.btn_delete,.btn_eliminar').removeClass('disabled');
							$('.btn_nuevo').removeClass('disabled');
						}else{
							$('.btn_nuevo').addClass('disabled');
							$('.btn_editar,.btn_delete,.btn_eliminar').removeClass('disabled');
						}
					}
				}
			}
		});
		
		$('.btn_editar').click(function(){
			Limpiar()
			$id		=	$('.seleccinado').attr('data-modulo');
			$tipo	=	$('.seleccinado').attr('data-type');

			$nombre =   $('.seleccinado').attr('data-system');
			$icono	=	$('.seleccinado').attr('data-icono');
			$level	=	$('.seleccinado').attr('data-level');
			
			if(!$(this).hasClass('disabled')){
				if($tipo=='system'){
					alert('No Puede editar aqui..');
					return;
				}
				if($id){
					if($level<=2){
						$('#padrecito').val($nombre).removeAttr('readonly');
						// $('#icono_father').html('<i class="fa parent_icono '+$icono+'"></i>');
						$('#icono_father').empty();
						$('.label_padre').hide();
					}else{
						$nombre =   $('.seleccinado').attr('data-father');
						$('#padrecito').val($nombre).attr('readonly','readonly');
						$('#icono_father').html('<i class="fa parent_icono '+$('.seleccinado').attr('data-padre')+'"></i>');
						$('.label_padre').show();
						// LoadBoton()
					}
					$(".modal-title").html('<i class="fa modal_icono '+$('.seleccinado').attr('icon-system')+'"></i>&nbsp;&nbsp;EDITANDO MODULO EN '+$nombre);
					modificar($id);
				}else
					ventana.alert({titulo: "", mensaje: "Seleccione el registro."});		
			}
		});
		
		// $('.btn_delete').click(function(){
		$('.btn_eliminar').click(function(){
			$id		=	$('.seleccinado').attr('data-modulo');
			$tipo	=	$('.seleccinado').attr('data-type');
			if(!$(this).hasClass('disabled')){
				if($id){
					eliminar($id)
				}else{
					ventana.alert({titulo: "", mensaje: "Seleccione el registro."});
				}
			}
		})
		
		$('.btn_nuevo').click(function(){
			$("#name_system,#icono_father").empty();
			Limpiar();
			if(!$(this).hasClass('disabled')){
				$id		=	$('.seleccinado').attr('data-modulo');
				$tipo	=	$('.seleccinado').attr('data-type');
				$nombre	=	$('.seleccinado').attr('data-name');
				$icono	=	$('.seleccinado').attr('data-icono');
				$level	=	$('.seleccinado').attr('data-level');
				if($tipo=='system'){
					$('.label_padre').hide();
					//QUITAMOS LA COLUMNA PARA LOS BOTONES
						$('.content_form').removeClass('col-sm-6').addClass('col-sm-12');
						$('.content_boton').hide();
					//QUITAMOS LA COLUMNA PARA LOS BOTONES
					$(".modal-title").html('<i class="fa modal_icono '+$icono+'"></i>&nbsp;&nbsp;NUEVO MODULO EN '+$nombre);
					/*RECUPERAR DATOS*/
					$("#idsystem").val($id);
					$("#idpadrecito").val( $('.seleccinado').attr('data-father') );
					/*RECUPERAR DATOS*/
					$("#modal-form").modal('show');
					return;
				}
				if($id){
					if($level<=2){
						$('#icono_father').html('<i class="fa parent_icono '+$icono+'"></i>');
						$('#padrecito').val($nombre).attr('readonly','readonly');
						
						/*RECUPERAR DATOS*/
						$("#idsystem").val(  $('.seleccinado').attr('data-idsystem') );
						$("#idpadrecito").val( $id );
						/*RECUPERAR DATOS*/
						
						$('.label_padre').show();
						LoadBoton($("#idmodulo").val());
						//AÑADIMOS LA COLUMNA PARA LOS BOTONES
							$('.content_form').removeClass('col-sm-12').addClass('col-sm-6');
							$('.content_boton').show();
						//AÑADIMOS LA COLUMNA PARA LOS BOTONES
						
						$nombre = $('.seleccinado').attr('data-system');
						$icono = $('.seleccinado').attr('icon-system');
						$(".modal-title").html('<i class="fa modal_icono '+$icono+'"></i>&nbsp;&nbsp;NUEVO MODULO EN '+$nombre);
						$("#modal-form").modal('show');					
					}else{
						alert('No puede ingresar nuevo modulo');
					}
				}else
					ventana.alert({titulo: "", mensaje: "Seleccione el registro."});
			}			
		});
		
		$('.cancelar').click(function(){
			location.reload();
		})		
		
		$(".save_asign").click(function(){
			var data = $("#form-all").serialize();
			ajax.post({url: _base_url+"modulo/save_detail", data: $("#form-all").serialize()}, function(res) {
				ventana.alert({titulo: "En horabuena!", mensaje: "Modulos Asignados Correctamente", tipo:"success"}, function() {
					redirect(_controller);
				});
			});
		});
		
		$("#btn-registrar-boton").click(function(e){
			$("#modal-boton .modal-title").html('Nuevo Boton');
			$("#modal-boton").modal('show');
			return false;
		});
		
		function botones($band){
			if(!$band){
				$('.botoncito').addClass('disabled');
				$('.save').removeClass('disabled');
			}else{
				$('.botoncito').removeClass('disabled');
				$('.save').addClass('disabled');
			}
		}
		
		function Limpiar(){
			$("#form-data input").val('');
			$("#detalle_boton").empty()
		}
		
		function AddDetalleBoton($id,$boton,$icon,$data){
			$html = '<tr>';
			$html+= '	<td style="padding:3px;">'+'<button style="width:100%;text-align:left;" type="button" class="btn fa '+$icon+'" >&nbsp;&nbsp;'+$boton+'</button>'+'</td>';
			$html+= '	<td style="padding:3px;">';
			$html+= '		<button type="button" class="btn delete_boton btn-danger fa fa-times"></button>';
			$html+= '		<input type="hidden" name="idboton[]" value="'+$id+'">';
			$html+= '	</td>';
			$html+= '</tr>';
			$("#detalle_boton").append($html);
			LoadBoton( '' );
		}