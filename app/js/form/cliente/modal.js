if(typeof form == 'undefined') {
	form = {};
}

if (typeof ruc_obligatorio === 'undefined') {
    ruc_obligatorio='N';
}

if (typeof id_cliente_retornar === 'undefined') {
    id_cliente_retornar = $("#compra_idcliente");
}

if (typeof cliente_retornar === 'undefined') {
    cliente_retornar=$("#cliente_razonsocial");
}

if(typeof form.guardar_cliente != 'function') {
	form.guardar_cliente = function() {
		id = "form_cliente";
		var fd = new FormData(document.getElementById(id));
		$.ajax({
		  url: _base_url+"cliente/guardar",
		  type: "POST",
		  data: fd,
		  enctype: 'multipart/form-data',
		  processData: false,  // tell jQuery not to process the data
		  contentType: false   // tell jQuery not to set contentType
		}).done(function( resp ) {
			if(isNaN(resp)){
				ventana.alert({titulo: "Hey..!", mensaje: resp, tipo:"warning"}, function() {
					
				});
				$('#'+prefix_cliente+'btn_save_cliente').prop("disabled",false);
				return;
			}
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				
				$("#modal-cliente").modal("hide");
				id_cliente_retornar.val(resp);
				
				ajax.post({url: _base_url+"cliente/get_post",data:"id="+resp}, function(rpt) {
					var html = "";
					if($("#ruc_obligatorio").val()=='S') {
						$("#cliente_doc").val(rpt.ruc);
					}
					else if(rpt.dni) {
						$("#cliente_doc").val(rpt.dni);
					}
					
					$("#dni_cliente").val(rpt.dni);
					$("#ruc_cliente").val(rpt.ruc);
					html += rpt.nombres+" "+rpt.apellidos;
					cliente_retornar.val(html);
					$("#cliente_razonsocial").focus();
				});
				
			});
		});
		return false;
	}
}

form.guardar_ocupacion = function() {
	var data = $("#form_ocupacion").serialize();
	model.save(data, function(res) {
		$("#modal-ocupacion").modal("hide");
		reload_combo("#idocupacion", {controller: "ocupacion"}, function() {
			$("#idocupacion").val(res.idocupacion);
		});
	}, "ocupacion");
}

$("#modal-cliente").on('hidden.bs.modal', function () {
	clear_form("#form_cliente");
});

$(function(){
  if ($("#cli_linea_credito").is(":checked")) {
    $("#cli_limite_credito").removeAttr('readonly');
  }else{
    $("#cli_limite_credito").attr('readonly','readonly').val('0.00');
  }
  
  $('#'+prefix_cliente+'tipo').trigger("change");
});

$('.nombres').letras({'permitir':' &.'})
$('.apellidos,.representante_nombres,.representante_nombres').letras({'permitir':' '})
$('.direccion,#direccion_trabajo').alfanumerico({'permitir':' -#./'})
// $('.cliente_email').letras({'permitir':'1234567890@_-'})
$('.cliente_email').alfanumerico({'permitir':'@-_.'});
$('.dni,.ruc,.dni_representante').numero_entero();
$('.telefono').numero_entero({'permitir':' -*#'});
$('#cli_ingreso_mensual,#cli_limite_credito').numero_real();

$('.input-group.date').datepicker({
    todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	format: "dd/mm/yyyy",
	endDate: parseDate(_current_date)
});

$(document).on("keypress",".direccion,.telefono",function(e){
	if(e.keyCode==13){
		e.preventDefault();
		$('#'+prefix_cliente+'btn_save_cliente').trigger("click");
	}
});

$('#'+prefix_cliente+'btn_save_cliente').on('click',function(e){
	e.preventDefault();
	bval = true;
	bval = bval && $("#"+prefix_cliente+"tipo").required();

	if($("#"+prefix_cliente+"tipo").val()=='N'){//NATURAL
		bval = bval && $("#"+prefix_cliente+"nombres").required();
		bval = bval && $("#"+prefix_cliente+"apellidos").required();
		bval = bval && $("#"+prefix_cliente+"dni").required();
	}else{//JURIDICO
		bval = bval && $("#"+prefix_cliente+"ruc").required();
		bval = bval && $("#"+prefix_cliente+"nombres").required();
	}
	
	/*Verificando longitud de RUC y DNI*/
	if($("#"+prefix_cliente+"dni").val()!='' ){//verificamos si estan poniendo algun dato aqui
		var digitos = long_dni - $("#"+prefix_cliente+"dni").val().length;
		if(digitos>0){
			$('.nav-tabs a[href="#tab'+prefix_cliente+'-1"]').tab('show');
			$("#"+prefix_cliente+"dni").removeAttr('title');
			$("#"+prefix_cliente+"dni").tooltip('destroy');
				
			var digitos = long_dni - $("#"+prefix_cliente+"dni").val().length;
			$("#"+prefix_cliente+"dni").attr({ title: 'Si está intentando poner DNI, le falta  '+digitos+' digitos'}).tooltip('fixTitle').tooltip('show');;
			$("#"+prefix_cliente+"dni").focus();

			setTimeout(function(){
				$("#"+prefix_cliente+"dni").tooltip('destroy');
			},1200);
			return;
		}
	}

	if($("#"+prefix_cliente+"ruc").val()!='' ){//verificamos si estan poniendo algun dato aqui
		var digitos = long_ruc - $("#"+prefix_cliente+"ruc").val().length;
		if(digitos>0){
			$('.nav-tabs a[href="#tab'+prefix_cliente+'-1"]').tab('show');
			$("#"+prefix_cliente+"ruc").removeAttr('title');
			$("#"+prefix_cliente+"ruc").tooltip('destroy');
			var digitos = long_ruc - $("#"+prefix_cliente+"ruc").val().length;
			$("#"+prefix_cliente+"ruc").attr({ title: 'Si está intentando poner RUC, le falta  '+digitos+' digitos'}).tooltip('fixTitle').tooltip('show');;
			$("#"+prefix_cliente+"ruc").focus();
			
			setTimeout(function(){
				$("#"+prefix_cliente+"ruc").tooltip('destroy');
			},1200);
			return;			
		}
	}
	/*Verificando longitud de RUC y DNI*/

	bval = bval && $(".direccion").required();
	bval = bval && $(".telefono.req").required();
	
	if(!bval) {
		$('.nav-tabs a[href="#tab'+prefix_cliente+'-1"]').tab('show');
		return false;
	}
	
	if($("#"+prefix_cliente+"tipo").val()=='N'){//NATURAL
		// bval = bval && $("#"+prefix_cliente+"idestado_civil").required();		
		bval = bval && $("#"+prefix_cliente+"sexo").required();
		bval = bval && $("#"+prefix_cliente+"fecha_nac").required();
	}
	
	if(!bval){
		$('.nav-tabs a[href="#tab'+prefix_cliente+'-3"]').tab('show');
		return false;
	}
	
	if(bval){
		cant_dir = 0;
		$('.direccion').each(function(){
			if($('.dir_principal').is(':checked')){
				cant_dir++;
			}
		})
		if(cant_dir>=1)
			form.guardar_cliente();
		else
			ventana.alert({titulo: "Hey!", mensaje: "Debe Seleccionar una Direccion como Principal", tipo:"warning"});
	}
});

$(".load_photo").click(function() {
    $("#cli_file").click();
});

$("#cli_btn_cancel").click(function() {
    $("#modal-cliente").modal("hide");
	// $('.list_direcciones,.list_telefonos,.list_representantes').empty();
});

function leerarchivobin(f) {
    id=prefix_cliente+'photoN';
    // if( $('#cli_tipo').val() == 'J' ){
      // id='photoJ';
    // }
    var imagenAR = document.getElementById("cli_file");
    if (imagenAR.files.length != 0 && imagenAR.files[0].type.match(/image.*/)) {
    var lecimg = new FileReader();
    lecimg.onload = function(e) { 
      var img = document.getElementById(id);
      img.src = e.target.result;
    } 
    lecimg.onerror = function(e) { 
	  ventana.alert({titulo: "Hey!", mensaje: "Error leyendo la imagen!!", tipo:"warning"});
    }
    lecimg.readAsDataURL(imagenAR.files[0]);
    } else {
      ventana.alert({titulo: "Hey!", mensaje: "Seleccione una imagen", tipo:"warning"});
    }
}

$("#btn-registrar-zona").on("click", function() {
  $("#modal-zona").modal("show");
  return false;
});

$("#btn-registrar-ocupacion").on("click", function() {
  $("#modal-ocupacion").modal("show");
  return false;
});

$(document).on('click',"#addDireccion",function(){
	if( $('.direccion').required() ){
		// $html_dir ='<div class="input-group" style="margin-top:5px;">';
		// $html_dir+='  <span class="input-group-addon tooltip-demo" style="padding: 0px 2px 1px 0px;margin-left: 5px;">';
		// $html_dir+='    <div class="radio" style="padding-left: 30px;" data-toggle="tooltip" title="Seleccione Direccion Principal">';
		// $html_dir+='        <input type="radio" name="radio_dir" class="dir_principal" value="N" >';
		// $html_dir+='        <label></label>';
		// $html_dir+='    </div>';
		// $html_dir+='    <input type="hidden" class="dir_principal_val" name="dir_principal[]" value="N" >';
		// $html_dir+='  </span>';
		// $html_dir+='	<input type="text" name="direccion[]" placeholder="Direccion..." value="" class="form-control direccion here_req req">';
		// $html_dir+='	<span class="input-group-addon cursor tooltip-demo delete_direccion" style="border:1px solid #1c84c6;color: #1c84c6;border-radius: 0px 3px 3px 0px;">';
		// $html_dir+='    <div data-toggle="tooltip" class="" title="Borrar direccion">';
		// $html_dir+='			<i class="fa fa-trash"></i>';
		// $html_dir+='    </div>';
		// $html_dir+='  </span>';
		// $html_dir+='</div>';
		// $('.list_direcciones').append($html_dir);
		// $('.direccion').required();
		var array = [];
		var data = {
				descripcion: ''
				,dir_principal: 'N'
				,direccion: ''
				,estado: 'A'
				,idcliente: ''
				,idclientedireccion: ''
			};
			
		array.push(data);

		direcciones_grid(array,false,prefix_cliente);
	}
})

$(document).on('click',"#addTelefono",function(){
	if( $('.telefono').required() ){
		var array = [];
		var data = {
				idclientetelefono: ''
				,idcliente: ''
				,descripcion: ''
				,telefono: ''
			};
				
		array.push(data);
		telefonos_grid(array,false,prefix_cliente);
		// $('.telefono').required();
	}
})

$(document).on('click',"#addRepresentante",function(){
	s = true && $('.nombre_representante').required();
	s = s && $('.apellidos_representante').required();
	s = s && $('.dni_representante').required();
	
	if( s ){		
		var array = [];
		var data = {
				idcliente_representante: ''
				,idcliente: ''
				,nombre_representante: ''
				,apellidos_representante: ''
				,dni_representante: ''
				,email_representante: ''
				,estado: 'A'
			};
		array.push(data);
		representante_grid(array,true,prefix_cliente);
	}	
});

$(document).on('click','.delete_direccion',function(){
  //$div_ = $(this).parent('span').parent('div.input-group');
	$div_ = $(this).parent('div.input-group');
  $radio = $($div_).find('input.dir_principal');
  if ( $radio.is(':checked') ) {
    ventana.alert({titulo: "Hey..!", mensaje: "No puede eliminar una direccion Principal..!!", tipo:"warning"}, function() {
      $($div_).find('input.direccion').focus();
    });
  }else{
    $($div_).remove();
  }
  
});

$(document).on('click','.dir_principal',function(){
  $div = $(this).parent('div.radio').parent('span');
  $('input.dir_principal_val').val('N');
  $div.find('input.dir_principal_val').val('S');
})

$(document).on('click','.delete_telefono',function(){
	$div_ = $(this).parent('span').parent('div.input-group');
	$($div_).remove();
});

$(document).on('click','.delete_repres',function(){
	$div_ = $(this).parent('span').parent('div').parent('div').parent('div.row');
	$($div_).remove();
});

$('#'+prefix_cliente+'linea_credito').click(function(){
  if ($(this).is(':checked')) {
    $("#"+prefix_cliente+"limite_credito").removeAttr('readonly');
  }else{
    $("#"+prefix_cliente+"limite_credito").attr('readonly','readonly').val('0.00');
  }
});

	$("#"+prefix_cliente+"observacion").blur(function(){
		$('.nav-tabs a[href="#tab'+prefix_cliente+'-3"]').tab('show');
	});

	$(document).ready(function(){
		$("#"+prefix_cliente+"tipo").trigger("change");
	})
	$("#"+prefix_cliente+"tipo").change(function(){
		if($("#"+prefix_cliente+"tipo").val()=='N'){
			$(".apellidos").removeAttr('readonly','readonly');
			$('.label_dni').addClass('required');
			$('.label_ruc').removeClass('required');
		
			$(".info_natural").show();
			$(".info_juridico").hide();
		
			$(".label_secundario").html("Datos Adicionales");
			keyboardSequence([	"#"+prefix_cliente+"tipo"
							,"#"+prefix_cliente+"ruc"
							,"#"+prefix_cliente+"nombres"
							,"#"+prefix_cliente+"apellidos"
							,"#"+prefix_cliente+"dni"
							,"#"+prefix_cliente+"cliente_email"
							,"#"+prefix_cliente+"observacion"
							// ,".direccion"+prefix_cliente
							// ,".telefono"+prefix_cliente							
							,'a[href="#tab'+prefix_cliente+'-3"]'
							,"#"+prefix_cliente+"idzona"
							,"#"+prefix_cliente+"idestado_civil"
							,"#"+prefix_cliente+"sexo"
							,"#"+prefix_cliente+"fecha_nac"
							,"#"+prefix_cliente+"idsit_laboral"
							,"#"+prefix_cliente+"centro_laboral"
							,"#"+prefix_cliente+"centro_laboral"
							,"#"+prefix_cliente+"direccion_trabajo"
							,"#"+prefix_cliente+"ingreso_mensual"
							,retornar_boton("cliente",prefix_cliente,"btn_save_cliente")
					], "#form_cliente");
		}else{
			$(".apellidos").val('').attr('readonly','readonly');
			$('.label_ruc').addClass('required');
			$('.label_dni').removeClass('required');
		
			$(".info_juridico").show();
			$(".info_natural").hide();

			$(".label_secundario").html("Datos Representante");
			
			keyboardSequence([	"#"+prefix_cliente+"tipo"
							,"#"+prefix_cliente+"ruc"
							,"#"+prefix_cliente+"nombres"
							,"#"+prefix_cliente+"dni"
							,"#"+prefix_cliente+"cliente_email"
							,"#"+prefix_cliente+"observacion"
							// ,"#"+prefix_cliente+"limite_credito"
							,".direccion"+prefix_cliente
							,".telefono"+prefix_cliente
							,'a[href="#tab'+prefix_cliente+'-3"]'
							,"#"+prefix_cliente+"idzona"
							,".nombre_representante"
							,".apellidos_representante"
							,".dni_representante"
							,retornar_boton("cliente",prefix_cliente,"btn_save_cliente")
					], "#form_cliente");
		}
	});