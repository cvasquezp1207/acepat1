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
		var id = grilla.get_id(_default_grilla);
		if(id != null) {
			alert(id);
		}
	},
	guardar: function() {
		var data = $("#form_"+_controller).serialize();
		/*model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect(_controller);
			});
		});*/
		Save_action("form_"+_controller);
	},
	cancelar: function() {
		
	},guardar_ocupacion: function() {
		// var data = $("#form_ocupacion").serialize();
		// model.save(data, function(res) {
		  // $("#modal-ocupacion").modal("hide");
		  // reload_combo("#idocupacion", {controller: "ocupacion"}, function() {
			// $("#idocupacion").val(res.idocupacion);
		  // });
		// }, "ocupacion");
	  }
};

$("#monto").numero_real();

$("#linea-credito-cliente").click(function(e){
	var id = grilla.get_id(_default_grilla);
	if(id != null) {
		OpenModalLinea(id);
	}else{
		ventana.alert({titulo: "", mensaje: "Seleccione el registro."});
	}
});

$("#btn_configurar").click(function(e){
	var id = grilla.get_id(_default_grilla);
	if(id != null) {
		OpenModalConfig(id);
	}else{
		ventana.alert({titulo: "", mensaje: "Seleccione el registro."});
	}
});

$("#save_linea_c").click(function(e){
	str = $("#form-linea").serialize();
	// str+= "&idcliente=";
	s = true && $("#f_desde").required();
	s = s && $("#f_hasta").required();
	s = s && $("#monto").required();
	
	if(s){
		ajax.post({
			url: _base_url+"cliente/save_ampliacion", 
			data: str
		},function(res) {
			if(res)
				ventana.alert({titulo: "", mensaje: "Se amplió la linea de credito correctamente."},function(){
					$("#modal-form").modal("hide");
					grilla.reload(_default_grilla);
				});
		});		
	}
});

$("#limite_credito").keyup(function(e){
	// if(e.keyCode == 13) {//Aqui debe ir solo pulsaciones de numeros
		e.preventDefault();
		x_saldo = parseFloat($(this).val()) - parseFloat($("#linea_consumida").val());
		
		$("#linea_disponible").val(x_saldo.toFixed(2));
	// }
});

function OpenModalLinea(id){
	id = id || 0;
	
	ajax.post({
		url: _base_url+"cliente/linea_cliente", 
		data: "idcliente="+id
	},function(data) {
		$("#idcliente_linea").val(data.cliente.idcliente);
		$("#cliente_ampliar").val(data.cliente.cliente);
		if(data.cliente.linea_credito=='S'){
			$(".sms_linea").empty().hide();
			$("#save_linea_c").removeAttr('disabled');
			$("#f_hasta,#monto").val('');
			if(data.u_ampliacion.length){//YA EXISTE UNA ASIGNACION
				if(data.u_ampliacion[0].f_desde)
					$("#f_desde").val('');

				if(data.u_ampliacion[0].f_hasta){
					$("#f_hasta").attr('readonly','readonly');
					$('.input-group.date').datepicker('remove');
				}else{
					$("#f_hasta").removeAttr('readonly');
					$('.input-group.date input#f_hasta').datepicker({
						todayBtn: "linked",
						keyboardNavigation: false,
						forceParse: false,
						autoclose: true,
						language: 'es',
						format: "dd/mm/yyyy"
					});
				}
				
				$("#f_desde").val(data.u_ampliacion[0].f_desde);
				$("#f_hasta").val(data.u_ampliacion[0].f_hasta);
				$("#monto").val(data.u_ampliacion[0].monto);
			}else{// NO TIENE ASIGNACION
				$("#f_hasta").removeAttr('readonly');
				$('.input-group.date input#f_hasta').datepicker({
					todayBtn: "linked",
					keyboardNavigation: false,
					forceParse: false,
					autoclose: true,
					language: 'es',
					format: "dd/mm/yyyy"
				});
			}
		}else{
			$("#save_linea_c").attr('disabled','disabled');
			$(".sms_linea").html("Por favor verifique que el cliente tenga linea de credito").show();
		}
		
		$("#modal-form").modal("show");
	});
}

function OpenModalConfig(id){
	id = id || 0;
	
	$("#linea_credito").prop("checked",false);
	ajax.post({
		url: _base_url+"cliente/config_cliente", 
		data: "idcliente="+id
	},function(data) {		
		$("#form-configurar").modal("show");
		// $('#linea_credito').trigger("click");
			$("#idcliente_block").val(data.cliente.idcliente);
			$("#cliente_block").val(data.cliente.cliente);
			if(data.cliente){
				if(data.cliente.linea_credito == 'S'){
					$("#linea_credito").prop("checked",true);
					$("#limite_credito").prop("readonly",false);
				}else{
					$("#linea_credito").prop("checked",false);
					$("#limite_credito").prop("readonly",true);
				}
				if(data.cliente.bloqueado == 'S')
					$("#bloqueado").prop("checked",true);
				else
					$("#bloqueado").prop("checked",false);
			
				consumido = parseFloat(data.cliente.limite_credito) - parseFloat(data.saldo);
				$('#limite_credito').val(data.cliente.limite_credito);
				$('#linea_consumida').val(consumido.toFixed(2));
				$('#linea_disponible').val(parseFloat(data.saldo).toFixed(2));
				$("#save_linea_c").removeAttr('disabled');
			}
		setTimeout(function(){
			$("#cliente_block").focus();
		},1000);
	});
}

function callbackCliente(nRow, aData, iDisplayIndex){
	fotito = '../app/img/cliente/anonimo.jpg';
	if($.trim(aData["foto"])){
		fotito='./app/img/cliente/'+aData["foto"];
	}
	
	if(!$.trim(aData["telefono"]))
		aData["telefono"]='';
	$('td', nRow).eq(1).html('<div style="display:inline-block;"><div class="client-avatar" style="display:inline-block;"><img src="'+fotito+'" /></div>'+aData["cliente"]+'</div>');
	$('td', nRow).eq(4).html('<div><i class="fa fa-phone">&nbsp;</i>'+aData["telefono"]+'</div>');
}

function leerarchivobin(f) {
    id='photoN';
    // if( $('#tipo').val() == 'J' ){
      // id='photoJ';
    // }
    var imagenAR = document.getElementById("file");
    if (imagenAR.files.length != 0 && imagenAR.files[0].type.match(/image.*/)) {
    var lecimg = new FileReader();
    lecimg.onload = function(e) { 
      var img = document.getElementById(id);
	  // console.log(id);
	  // console.log(img);
	  // return;
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

function Save_action(id){//id= id del formulario
  var fd = new FormData(document.getElementById(id));
    $.ajax({
      url: _base_url+_controller+"/guardar",
      type: "POST",
      data: fd,
      enctype: 'multipart/form-data',
      processData: false,  // tell jQuery not to process the data
      contentType: false   // tell jQuery not to set contentType
    }).done(function( data ) {
		if($.isNumeric(data)){
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect(_controller);
			});
		}else{
			ventana.alert({titulo: "Upss...!", mensaje: data, tipo:"warning"}, function() {
				
			});
		}
    });
    return false;
}

$(".load_photo").click(function() {
    $("#file").click();
});

$('.nombres').letras({'permitir':' &.'})
$('.apellidos,.representante_nombres,.representante_nombres').letras({'permitir':' '})
$('.direccion,#direccion_trabajo').alfanumerico({'permitir':' -#./'})
$('.cliente_email').alfanumerico({'permitir':'@-_.'})
$('.dni,.ruc,.dni_representante').numero_entero();
$('.telefono').numero_entero({'permitir':' -*#'})
$('#ingreso_mensual,#limite_credito').numero_real()

validate();
$("#btn_cancel").click(function() {
  redirect(_controller);
  return false;
});

$('.input-group.date').datepicker({
    todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	format: "dd/mm/yyyy",
	endDate: parseDate(_current_date)
});

$('#linea_credito').click(function(){
  if ($(this).is(':checked')) {
    $("#limite_credito").removeAttr('readonly');
  }else{
    $("#limite_credito").attr('readonly','readonly').val('0.00');
  }
  $("#limite_credito").trigger("keyup");
});

if(typeof(prefix_cliente) == "undefined") prefix_cliente ='';

var array_ck = ["linea_credito","bloqueado"];
$('#save_config_c').on('click',function(e){
	e.preventDefault();
	
	str = $("#form-config").serialize();
	$.each(array_ck, function(i, val) {
		if($("#"+val).is(':checked'))
			str += "&" + val + "=S";
		else
			str += "&" + val + "=N";
	});
	s = true && $("#cliente_block").required();
	s = s && $("#limite_credito").required();
	
	if(s){
		ajax.post({
			url: _base_url+"cliente/save_bloqueo", 
			data: str
		},function(res) {
			if(res)
				ventana.alert({titulo: "", mensaje: "Se realizaron los cambios correctamente."},function(){
					$("#form-configurar").modal("hide");
					
					$("#form-configurar input").val("");
					$.each(array_ck, function(i, val) {
						$("#"+val).prop('checked',false);
					});
					grilla.reload(_default_grilla);
				});
		});		
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
		if(cant_dir<=0)
			$('.dir_principal:first').prop("checked", true);
			
		$('#'+prefix_cliente+'btn_save_cliente').prop("disabled",true);
		form.guardar();
	}
});

$(document).on('click',"#addDireccion",function(){
	if( $('.direccion').required() ){
		var array = [];
		var data = {
				descripcion: ''
				,dir_principal: 'S'
				,direccion: ''
				,estado: 'A'
				,idcliente: ''
				,idclientedireccion: ''
			};

		array.push(data);
		direcciones_grid(array,false,prefix_cliente);
		// $html_dir ='<div class="input-group" style="margin-top:5px;">';
		// $html_dir+='  <span class="input-group-addon tooltip-demo" style="padding: 0px 2px 1px 0px;margin-left: 5px;">';
		// $html_dir+='    <div class="radio" style="padding-left: 30px;" data-toggle="tooltip" title="Seleccione Direccion Principal">';
		// $html_dir+='        <input type="radio" name="radio_dir" class="dir_principal" value="N" >';
		// $html_dir+='        <label></label>';
		// $html_dir+='    </div>';
		// $html_dir+='    <input type="hidden" class="dir_principal_val" name="dir_principal[]" value="N" >';
		// $html_dir+='  </span>';
		// $html_dir+='	<input type="text" name="direccion[]" placeholder="Direccion..." value="" class="form-control direccion here_req req" style="font-size:12px;padding:4px 4px;">';
		// $html_dir+='	<span class="input-group-addon cursor tooltip-demo delete_direccion" style="border:1px solid #1c84c6;color: #1c84c6;border-radius: 0px 3px 3px 0px;">';
	
		// $html_dir+='    <div data-toggle="tooltip" class="" title="Borrar direccion">';
		// $html_dir+='			<i class="fa fa-trash"></i>';
		// $html_dir+='    </div>';
		// $html_dir+='  </span>';
		// $html_dir+='</div>';
		// $('.list_direcciones').append($html_dir);
		// $('.direccion').required();
	}
})

$(document).on('click',"#addTelefono",function(){
	if( $('.telefono').required() ){
		// $html_telf='<div class="input-group" style="margin-top:5px;">';
		// $html_telf+='	<input type="text" name="telefono[]" value="" class="form-control telefono here_req req" style="font-size:12px;padding:4px 4px;">';
		// $html_telf+='	<span class="input-group-btn tooltip-demo">';
		// $html_telf+='		<button type="button" style="" class="btn btn-outline btn-success delete_telefono" data-toggle="tooltip" title="Borrar Telefono">';
		// $html_telf+='			<i class="fa fa-trash"></i>';
		// $html_telf+='		</button>';	
		// $html_telf+='	</span>';
		// $html_telf+='</div>';
		// $('.list_telefonos').append($html_telf);
		// $('.telefono').required();
		var array = [];
		var data = {
				idclientetelefono: ''
				,idcliente: ''
				,descripcion: ''
				,estado: 'A'
				,telefono: ''
			};
		array.push(data);
		telefonos_grid(array,false,prefix_cliente);
	}
})

$(document).on('click',"#addRepresentante",function(){
	s = true && $('.nombre_representante').required();
	s = s && $('.apellidos_representante').required();
	s = s && $('.dni_representante').required();
	
	if( s ){
		// $html_rep ='<div class="col-md-12">';
		// $html_rep+='	<div class="row" style="">';
		// $html_rep+='		<div class="col-md-3">';
		// $html_rep+='      <div class="">';
		// $html_rep+='			<label class="required">Nombres</label>';
		// $html_rep+='				<input type="text" name="nombre_representante[]" value="" placeholder="Nombre Representante" class="form-control nombre_representante here_req">';
		// $html_rep+='			</div>';
		// $html_rep+='		</div>';
		
		// $html_rep+='		<div class="col-md-6">';
		// $html_rep+='      <div class="">';    
		// $html_rep+='			<label class="required">Apellidos</label>';		
		// $html_rep+='				<input type="text" name="apellidos_representante[]" value="" placeholder="Apellidos Representante" class="form-control apellidos_representante here_req">';
		// $html_rep+='			</div>';
		// $html_rep+='		</div>';
		
		// $html_rep+='    <div class="col-md-3">';
		// $html_rep+='		<label class="required">Dni</label>';
		// $html_rep+='			<div class="input-group">';
		// $html_rep+='				<input type="text" name="dni_representante[]" value="" maxlength="8" placeholder="DNI" class="form-control dni_representante here_req">';
		// $html_rep+='				<span class="input-group-btn tooltip-demo">';

		// $html_rep+='					<button type="button" style="height: 30px;" class="btn btn-outline delete_repres btn-success" data-toggle="tooltip" title="Añadir Representante">';
		// $html_rep+='						<i class="fa fa-trash"></i>';
		// $html_rep+='					</button>';

		// $html_rep+='				</span>';
		// $html_rep+='			</div>';
		// $html_rep+='		</div>';

		// $html_rep+='	</div>';
		// $html_rep+='</div>';
		
		// $('.list_representantes').append($html_rep);
		// $('.nombre_representante').required();
		// $('.dni_representante').numero_entero()
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
	// $div_ = $(this).parent('div.input-group-addon');
	$div_ = $(this).parent('div.input-group');
	$radio = $($div_).find('input.dir_principal');
	// console.log($div_);
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

$(document).on('click',"#dtcliente_view tbody tr",function(){
	tr_=$(this).find('td:eq(0)');
	idcliente = tr_.html();
	if($(this).hasClass('active')){
		ajax.post({url: _base_url+_controller+"/retornar_detalle/", data: {idcliente:idcliente}}, function(res) {
			if(!res.cliente.apellidos)
				res.cliente.apellidos ='';
			$('.title_cliente').html($.trim(res.cliente.nombres+' '+res.cliente.apellidos));
			$('.referencia').html($.trim(res.cliente.observacion));
			if(res.cliente.cliente_email){
				$('.title_mail').html($.trim(res.cliente.cliente_email)).show();			
			}else
				$('.title_mail').hide();
			
			fotito = './app/img/cliente/anonimo.jpg';
			if($.trim(res.cliente.foto)){
				fotito='./app/img/cliente/'+res.cliente.foto;
			}
			
			$('.thumb_image').attr('src',fotito);
			
			$(".more_info").html(res.info);
		});
		
	}
});

$(function(){
  // if ($("#linea_credito").is(":checked")) {
    // $("#limite_credito").removeAttr('readonly');
  // }else{
    // $("#limite_credito").attr('readonly','readonly').val('0.00');
  // }
  
  $('#'+prefix_cliente+'tipo').trigger("click");
});

$("#btn-registrar-zona").on("click", function() {
  $("#modal-zona").modal("show");
  return false;
});

$("#btn-registrar-ocupacion").on("click", function() {
	$("#modal-ocupacion").modal("show");
	return false;
});

if($("#"+prefix_cliente+"idcliente").val()==''){
	open_modal_cliente('N');
}else{
	ajax.post({url: _base_url+"cliente/get_all/", data:{id:$("#"+prefix_cliente+"idcliente").val()}}, function(response) {
		if(!response.direccion.length){
			var array = [];
			var data = {
					descripcion: ''
					,dir_principal: 'S'
					,direccion: ''
					,estado: 'A'
					,idcliente: ''
					,idclientedireccion: ''
				};

			array.push(data);
			response.direccion = array;
		}

		if(!response.telefonos.length){
			var array = [];
			var data = {
					idclientetelefono: ''
					,idcliente: ''
					,descripcion: ''
					,estado: 'A'
					,telefono: ''
				};
			array.push(data);
			response.telefonos = array;
		}
		
		if(!response.representantes.length){
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
			response.representantes = array;
		}
		
		direcciones_grid(response.direccion,false,prefix_cliente);
		telefonos_grid(response.telefonos,false,prefix_cliente);
		representante_grid(response.representantes,false,prefix_cliente);
	});
}

	$(".telefono"+prefix_cliente).blur(function(e){
		e.preventDefault();
		$('.nav-tabs a[href="#tab'+prefix_cliente+'-3"]').tab('show');
	});

	$(document).ready(function(){
		$("#"+prefix_cliente+"tipo").trigger("change");
	});
	
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
							,".direccion"+prefix_cliente
							,".telefono"+prefix_cliente
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
	
	keyboardSequence([	"#"+prefix_cliente+"cliente_block"
						,"#"+prefix_cliente+"limite_credito"
						,retornar_boton("-configurar",prefix_cliente,"save_config_c",'N')
					], "#form-config");