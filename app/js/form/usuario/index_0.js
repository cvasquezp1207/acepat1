
// funciones principales, estas funciones se invocaran cuando
// se haga click en cualquier boton de accion no incluye
// los eventos de botones dentro del formulario
var form = {
	nuevo: function() {

	},
	editar: function(id) {
		// alert(id);
	},
	eliminar: function(id) {
		model.del(id, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
				grilla.reload(_default_grilla); // _default_grilla=perfil, si no se indico otro nombre
			});
		});
	},
	imprimir: function() {

		
	},
	guardar: function() {
		var data = $("#form_"+_controller).serialize();
		//Save_action("form_"+_controller);
		/* model.save(data, function(res) {
			 ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				 redirect(_controller);
			 });
		 });*/

		if ($("#dni").required_CarcEs()) {
			GrabarDatos( $("#form_"+_controller).serialize(), "form_"+_controller,'guardar');
		};

		//console.log($band);
	},
	cancelar: function() {

	}
};

validate();

$("#btn_cancel").click(function() {
	redirect(_controller);
	return false;
});

$("#descripcion").focus();

$(".btn_estado").on("click", function() {
	
});

$("#load_photo").click(function() {
    $("#file").click();
});

$('#btn_add_sucursal').click(function(){
	var id = grilla.get_id(_default_grilla);
	if(id != null) {
		lista_empresa()
		$("#modal-form-sucursal").modal('show');
	}else{
		ventana.alert({titulo: "Aviso", mensaje: "Seleccione un registro de la tabla"});
	}
	//redirect(_controller+"/asign_suc");
});

function GrabarDatos(){
	$('.checkbox_nodo').each(function(m,n){
		if( $(n).attr('ajax-type')== 'indeterminate'){
			$(n).prop('checked', true);
		}
	});
	// str = 'idsucursal='+$('.sucursal.seleccionado').attr('data-suc');
	// str+= '&idperfil='+$('.perfil.seleccionado').attr('data-perfil');
	// str+= '&idsistema='+$('.system.seleccionado').attr('data-system');
	// str+= '&'+$("#form_"+_controller).serialize();
	str= $("#form_"+_controller).serialize();
	console.log( str )
	// ajax.post({url: _base_url+_controller+"/guardar", data: str}, function(res) {
		// ventana.alert({titulo: "En horabuena!", mensaje: "Accesos guardados correctamente", tipo:"success"}, function() {
			
		// });
	// });
}

// function GrabarDatos(datitos,nombre_form,accion){
	// ajax.post({url: _base_url+_controller+"/verificar_user", data: datitos}, function(res) {
		// cant_reg = parseInt(res.cant);

		// if ($.trim($("#idusuario").val())=='') {//NUEVO
			// if ( cant_reg > 0 ) {//YA EXISTE 
				// ventana.alert({titulo: "Error!", mensaje: "El Nick '"+$("#usuario").val()+"'' ingresado ya esta en uso...!", tipo:"error"}, function() {
					// $("#usuario").focus();
				// });
			// }else{//EL NICK NO ESTA SIENDO USADO
				// Save_action(nombre_form,accion);
			// }
		// }else{//EDITAR
			// if ( cant_reg > 0 ) {//SIN NADA QUE HACER
				// console.log('Puede grabar..');
				// Save_action(nombre_form,accion);
			// }else{//EL NICK NO ESTA SIENDO USADO, PREGUNTAR DE NUEVO SI EL USER NO STA SIENDO USANDO X OTRA PERSONA
				// str = "usuario="+$('#usuario').val();
					// str+= "&idusuario=";
					// ajax.post({url: _base_url+_controller+"/verificar_user", data: str}, function(res) {
						// cant_reg = parseInt(res.cant);
						// if ( cant_reg>0 ) {//YA EXISTE 
							// ventana.alert({titulo: "Error!", mensaje: "El Nick ingresado ya esta en uso...!", tipo:"error"}, function() {
								// $("#usuario").focus();
							// });
						// }else{//EL NICK NO ESTA SIENDO USADO
							// Save_action(nombre_form,accion);
						// }
					// });
				// }
			// }
	// });
// }

function Save_action(id,action){//id= id del formulario
	var fd = new FormData(document.getElementById(id));
		$.ajax({
			url: _base_url+_controller+"/"+action,
			type: "POST",
			data: fd,
			enctype: 'multipart/form-data',
			processData: false,  // tell jQuery not to process the data
			contentType: false   // tell jQuery not to set contentType
		}).done(function( data ) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect(_controller);
			});
		});
		return false;
}

function leerarchivobin(f) {
    var imagenAR = document.getElementById("file");
    if (imagenAR.files.length != 0 && imagenAR.files[0].type.match(/image.*/)) {
		var lecimg = new FileReader();
		lecimg.onload = function(e) { 
			var img = document.getElementById("photo");
			img.src = e.target.result;
		} 
		lecimg.onerror = function(e) { 
			alert("Error leyendo la imagen!!");
		}
		lecimg.readAsDataURL(imagenAR.files[0]);
    } else {
		alert("Seleccione una imagen!!")
    }
}

function callbackUsuario(nRow, aData, iDisplayIndex){
	$('td:eq(3)', nRow).html(fecha_es(aData.fecha_nac));
}

function eliminar(id){
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

$('#fecha_nac').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
});

$("#p1").css("display", "none");
$("#p2").css("display", "block");


$(function(){
	lista_empresa();
	$("#view").change(function() {
	   if($(this).is(":checked")) {
	        $("#p1").css("display", "none");
	        $("#p2").css("display", "block");
	    }else {
	        $("#p2").css("display", "none");
	        $("#p1").css("display", "block");
	    }
	});

	$('#change_pass').change(function(){
		if($(this).is(":checked")) {
			$(this).val(1);
	       	$('.checkbox-primary').show();
	       	$("#p1,#p2").val("");
	       	$(".clavesita").removeAttr('readonly');
			$('.change_pass').html('Clave Anterior');
	    }else {
			$(this).val(0);
	        $('.checkbox-primary').hide();
	        $("#p2").css("display", "none");
	        $("#p1").css("display", "block");

	        $("#p2,#clave,#p1").val( $.trim($("#clave_past").val()) );
	        $(".clavesita").attr('readonly','readonly');
			
			$('.change_pass').html('Nueva Clave')
	    }
	})

	$("#nick").keyup(function() {
        var val = $(this).val();
        $("#clave, #p1, #p2").val(val);
    });
        
    $("#p1, #p2").keyup(function() {
        var val = $(this).val();
        if($(this).attr("id") == "p1") {
            $("#p2,#clave").val(val);
        }else {
            $("#p1,#clave").val(val);
        }
    });

    $("#nombres, #appat, #apmat").keyup(function() {
        var nombre = $.trim($("#nombres").val());
        var appaterno = $.trim($("#appat").val());
        var apmaterno = $.trim($("#apmat").val());
        var sf = String(nombre).charAt(0)+String(appaterno).replace(' ', '')+String(apmaterno).charAt(0);
    });

	$("#nombres,#appat,#apmat").letras({'permitir':' '});
	$("#telefono").numero_entero({'permitir':' #*-'});
	$("#dni").numero_entero();

	/********************* eventos asignacion de sucursal ******************************/
	$('.eliminar').bind('click',function(e){
		e.preventDefault();
		$su_li=$(this).parent('div').parent('li');
		Eventodelete($(this));
	});
	
	$('.btn_save').on('click',function(){
		$band = true;
		
		$('.idtipoempleado_select.form-control-req').each(function(){
			if( $(this).html() == '' ){
				$(this).addClass('ui-state-error ui-icon-alert');
				$band=false;
			}else
				$(this).removeClass('ui-state-error ui-icon-alert');
		})
		
		if( $band ){
			ajax.post({url: _base_url+"usuario/save_detalle_sucu/", data: $('#form').serialize()}, function(res) {
				ventana.alert({titulo: "Asignacion concluida", mensaje: "Asignacion realizada correctamente.", tipo:"success"}, function() {
					redirect(_controller+'/asign_suc');
				});
			});
		}
	});
	
	$("#regresar").click(function(){
		redirect(_controller);
	});
	/********************* eventos asignacion de sucursal ******************************/

	
	/********************* eventos asignacion de USUARIO ******************************/
	$('#btn_add_user').click(function(e){
		var id = grilla.get_id(_default_grilla);
		$('#change_pass').removeAttr('checked').val(0);
		if(id != null) {
			ajax.post({url: _base_url+"usuario/get_data/", data: {idusuario:id}}, function(res) {
				$("#idusuario").val(res.idusuario);
				$("#nombres_data").val(res.appat+' '+res.apmat+' '+res.nombres);
				$("#dni").val(res.dni);
				$("#usuario").val(res.usuario);
				$("#p2,#p1,#clave_past").val(res.clave);
				
				if(!$.trim($('#idusuario').val()))
					$('.checkbox-primary').show();
				else{
					$('.checkbox-primary').hide();
					$('#view').removeAttr('checked').trigger('change');
				}
				$('.change_pass').html('Nueva Clave');
				$("#lista_sucursal").empty();
				ajax.post({url: _base_url+"usuario/sucursal_asign/", data: {idusuario:id}}, function(arr) {
					$("#lista_sucursal").html(arr);
				});
				$("#modal-form").modal('show');
			});
		}else{
			ventana.alert({titulo: "Aviso", mensaje: "Seleccione un registro de la tabla"});
		}
	});
	
	$('.btn_user').click(function(){
		if( $('.obligatorio').required() )
			GrabarDatos( 'idusuario='+$("#idusuario").val()+'&usuario='+$("#usuario").val()+'&change_pass='+$("#change_pass").val(), 'form-user','save_detalle_usu');
	})

	/********************* eventos asignacion de USUARIO ******************************/
});

function Eventodelete(here){
	$input			= $(here).parent('div.pull-right');
	$input_usuario 	= $input.find('input.idusuario').val();
	$input_tipoemple= $input.find('input.idtipoempleado').val();
	$input_sucursal	= $input.find('input.idsucursal').val();
	
	$empleado = $input.find('input.idusuario').attr('data-name');
	$sucursal = $(here).parent('div.pull-right').parent('li').parent('ul').find('li.ui-state-disabled');
	$li = $(here).parent('div.pull-right').parent('li');
	
	ventana.confirm({titulo:"Confirmar", 
			mensaje:"¿Desea eliminar a "+$empleado+" de "+$sucursal.text()+"?", 
			textoBotonAceptar: "Eliminar"}, function(ok){
			if(ok) {
				if($input_sucursal && $input_tipoemple)
					ajax.post({url: _base_url+_controller+"/eliminar_detalle/", data: {idusuario:$input_usuario,idsucursal:$input_sucursal,idtipoempleado:$input_tipoemple}}, function(res) {
						// redirect(_controller+"/asign_suc");
					});
				else
					$($li).fadeOut(800,function(){
						$($li).remove();
					});			
			}
	});	
}

//@@@@@@@@@ EVENTOS PARA LA ASIGNACION DE SUCURSAL*************************
node_all_close = false;
function lista_empresa(){
	ajax.post({url: _base_url+_controller+"/Lista_sucursal", data: {},type:'html'}, function(res) {
		$('#lista_asignacion').html(res);
		if(node_all_close){
			$nodos = $(".main_expand").removeClass('hijo-open').addClass('hijo-close');
			$nodos.parent('div').parent('li').find('ul').hide();
			
		}else{//ABRIR NODOS
			$nodos = $(".main_expand").removeClass('hijo-close').addClass('hijo-open');
			$nodos.parent('div').parent('li').find('ul').show();
		}

		if($(".main_expand").hasClass('main_hijo')){//CERRAMOS TODOS LOS NODOS HIJOS
			$nodos = $(".main_expand.main_hijo").removeClass('hijo-open').addClass('hijo-close');
		}
		
		$(".main_expand").parent('div').parent('li').find('.botones').hide();
		
		//LoadAccesos( $('.sucursal.seleccionado').attr('data-suc'),  $('.perfil.seleccionado').attr('data-perfil'), $('.system.seleccionado').attr('data-system') );
	});
}

$(document).on('click','.main_expand',function(){
	$ul = ($(this) ).parent('div').parent('li').find('ul');
	$nodito =	$(this).parent('div').parent('li').find('ul');
	if( $(this).hasClass('hijo-close') ){//ABRIENDO NODO
		$(this).removeClass('hijo-close').addClass('hijo-open');
		$($nodito[0]).slideDown("slow")
	}else{//CERRANDO NODO
		$(this).removeClass('hijo-open').addClass('hijo-close');
		$($nodito[0]).slideUp("slow")
	}
});

$(document).on('click','.checkbox_nodo',function(){
	$lista_nodo  = $(this).parent('div').parent('div').parent('div').parent('li');
	$nodo_selecc = $(this).parent('div').parent('div').parent('div').parent('li').find('ul');
	
	if( !$(this).hasClass('ck_boton') ){//SON BOTONES
		if( $(this).is(':checked') ){
			$($lista_nodo.find('ul input.checkbox_nodo')).prop('checked', true);
		}else{
			$($lista_nodo.find('ul input.checkbox_nodo')).prop('checked', false);
		}		
		$($lista_nodo.find('ul input.checkbox_nodo')).attr("ajax-type",'checkbox');
	}
	Verificar_ck($lista_nodo);	
});

function Verificar_ck($lista_nodo){
	$all_check = 0;
	$all_hijo = 0;
	$check_nodo = $lista_nodo.parent('ul').parent('.botones').parent('li').find('.presentacion').find('.checkbox_parent div input.checkbox_nodo');
	$($lista_nodo.parent('ul').find('li')).each(function(x,y){
		$li_parent = $(y).find('.checkbox_nodo');
		$all_hijo++;
		if( $($li_parent).is(':checked') ){
			$all_check++;
		}
	})
	
	$new_input = $($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]);
		
	if( $all_check == $all_hijo ){
		$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).prop('indeterminate', false);
		$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).prop('checked', true);
		
		$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).attr("ajax-type",'checkbox');
	}else{
		if($all_check>1)
			$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).prop('indeterminate', true);
		else{
			// $($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).prop('indeterminate', false);
			$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).prop('indeterminate', true);
		}
		$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).attr("ajax-type",'indeterminate');
	}

	if($new_input.hasClass('ck_hijo')){
		$nlista_nodo = $new_input.parent('div').parent('div').parent('div').parent('li');
		Verificar_ck($nlista_nodo);
	}
}
//@@@@@@@@@ EVENTOS PARA LA ASIGNACION DE SUCURSAL*************************