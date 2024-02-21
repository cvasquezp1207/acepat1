var x_click = 0;
$tree_ = $('#tree');
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
		$tree_.jstree("close_all");
		
		var arr = ["baja"];
		$.each(arr, function(i, val) {
			if($("#"+val).is(':checked'))
				data += "&" + val + "=S";
			else
				data += "&" + val + "=N";
		});
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
	// $('td:eq(3)', nRow).html(fecha_es(aData.fecha_nac));
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
	// $('a[href="' + window.location.hash + '"]').trigger('click');

	/********************* eventos asignacion de sucursal ******************************/	
	// $('#btn_save').on('click',function(){		
	$(document).on('click','#btn_save',function(){
		x_click++;
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
		
		x_ckx=0;


		$tree_.jstree("open_all");
		
		$('.checkbox_nodo').each(function(i, element){
			$a_element = $(element).parent("a.jstree-anchor");
			$selector = $a_element.find('i.jstree-checkbox');
			
			if($a_element.hasClass('jstree-clicked')){
				$(this).prop("checked",true);
				x_ckx++;
			}else{
				$(this).prop("checked",false);
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
	
	
	$tree_.on('ready.jstree', function() {
		$tree_.jstree("close_all");
		$tree_.jstree(true).refresh();
		// $("#tree").jstree("open_node", $('#j1_1'));		
		// $("#tree").jstree("open_node", $('#j1_2'));		
	});
	/* EVENTOS JSTREE*/
});

function Grabar_User(){
	str= $("#form-user").serialize();
	
	$('.es_superusuario').each(function(){
		if($(this).is(":checked"))
			str+="&es_superusuario[]=S";
		else
			str+="&es_superusuario[]=N";
	});
	
	$('.control_reporte').each(function(){
		if($(this).is(":checked"))
			str+="&control_reporte[]=S";
		else
			str+="&control_reporte[]=N";
	});
	ajax.post({url: _base_url+_controller+"/save_detalle_usu", data: str}, function(res) {
		ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
			redirect(_controller);
		});
	});
}
//@@@@@@@@@ EVENTOS PARA LA ASIGNACION DE SUCURSAL*************************
function lista_empresa(){
	$id_user = $("#idusuario").val();
	if(!$id_user) $id_user='0';
	ajax.post({url: _base_url+_controller+"/tree_list", data: {idusuario:$id_user},type:'html'}, function(res) {
		$tree_.html(res);
		
		$tree_.jstree({
            plugins: ["checkbox"]
			,"checkbox": {
				"keep_selected_style": false
			}
			,'themes': {
                'name': 'proton',
                'responsive': true
            }
        });	
	});
}
//@@@@@@@@@ EVENTOS PARA LA ASIGNACION DE SUCURSAL*************************