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
		console.log(data);
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect(_controller);
			});
		});
	},
	cancelar: function() {

	}
};

validate();
$("#usuario,#p2,#p1").alfanumerico();
$('#fecha_nac').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
});

$("#p1").css("display", "none");
$("#p2").css("display", "block");

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
});

function callbackUsuario(nRow, aData, iDisplayIndex){
	$('td:eq(3)', nRow).html(fecha_es(aData.fecha_nac));
}

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
	// $('.nav-tabs a[href="#tab-2"]').trigger('click');
	// console.log($('.nav-tabs a[href="#tab-2"]'));
	// $('a[href="' + window.location.hash + '"]').trigger('click');

	/********************* eventos asignacion de sucursal ******************************/	
	$('#btn_save').on('click',function(){		
		bval = true && $("#nombres").required();
		bval = bval && $("#appat").required();
		bval = bval && $("#apmat").required();
		bval = bval && $("#dni").required();
		bval = bval && $("#direccion").required();
		bval = bval && $("#fecha_nac").required();
		
		if(!bval){
			$('.nav-tabs a[href="#tab-1"]').tab('show');
			return false;
		}
		
		rellenando_checkbox();
		x_ckx=0;
		$('.checkbox_nodo').each(function(){
			if( $(this).is(":checked") ){
				x_ckx++;
			}
		});
		
		if(x_ckx<1){
			$('.nav-tabs a[href="#tab-2"]').tab('show');
			bval = false;
			$("#sms_checkbox").html("DEBE SELECCIONAR AL MENOS UNA SUCURSAL Y UN ROL PARA EL USUARIO");
			return false;
		}
		
		$("#sms_checkbox").empty();
		
		if(bval){
			console.log("Direccionando a la funcion guardar del formulario... Linea19");
		}
	});
	
	$('.btn_user').click(function(){
		if( $('.obligatorio').required() )
			Grabar_User();
	})
	
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
	/********************* eventos asignacion de USUARIO ******************************/
	
	/* EVENTOS JSTREE*/
	 $("#tree").jstree({
        "checkbox": {
            "keep_selected_style": false
        },
            "plugins": ["checkbox"]
    });
    $("#tree").bind("changed.jstree",
    function (e, data) {
		if(data.node){
			console.log(data.node);
		   alert("Checked: " + data.node.id);
		   alert("Parent: " + data.node.parent); 
			//alert(JSON.stringify(data));			
		}
    });
	/* EVENTOS JSTREE*/
});

function rellenando_checkbox(){
	$('.checkbox_nodo').each(function(m,n){
		if( $(n).attr('ajax-type')== 'indeterminate'){
			$(n).prop('checked', true);
		}
	});
}

function Grabar_User(){
	str= $("#form-user").serialize();
	console.log( str )
	ajax.post({url: _base_url+_controller+"/save_detalle_usu", data: str}, function(res) {
		ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
			redirect(_controller);
		});
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
		
		if($("#idusuario").val()!='')
			Accesos_rol( $("#idusuario").val());
			// Accesos_empresa( $("#idusuario").val());
			
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
	console.log($check_nodo);
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
		// console.log($all_check);
		if($all_check>1)
			$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).prop('indeterminate', true);
		else{
			console.log("Nada de hijo");
			$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).prop('indeterminate', true);
		}
		$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).attr("ajax-type",'indeterminate');
	}

	if($new_input.hasClass('ck_hijo')){
		$nlista_nodo = $new_input.parent('div').parent('div').parent('div').parent('li');
		Verificar_ck($nlista_nodo);
	}
}

function Accesos_empresa(idusuario){
	ajax.post({url: _base_url+_controller+"/Listar_sucursal", data: 'idusuario='+idusuario}, function(res) {
		$(res).each(function(i,j){
			// console.log(j);
			// Accesos_rol(idsucursal,idperfil,idsistema,j.idmodulo);			
		})
	});
}

function Accesos_rol(idusuario){
	ajax.post({url: _base_url+_controller+"/Listar_sucursal", data: 'idusuario='+idusuario}, function(arr) {
		if(arr.length){
			$(arr).each(function(x,y){
				$("#checkbox"+y.idempresa+'_'+y.idsucursal+'_'+y.idtipoempleado).prop('checked', true);
				$("#checkbox"+y.idempresa+'_'+y.idsucursal+'_'+y.idtipoempleado).attr("ajax-type",'checkbox');
				Verificar_ck($("#checkbox"+y.idempresa+'_'+y.idsucursal+'_'+y.idtipoempleado).parent('div').parent('div').parent('div').parent('li'));
			})
		}else{//NO TIENE BOTONES
		console.log("no tiene botones...");
			// $("#checkbox"+idmodulo).prop('indeterminate', true);
			// $("#checkbox"+idmodulo).attr("ajax-type",'indeterminate');
			// Verificar_ck($("#checkbox"+idmodulo).parent('div').parent('div').parent('div').parent('li'));
		}
	});
}
//@@@@@@@@@ EVENTOS PARA LA ASIGNACION DE SUCURSAL*************************