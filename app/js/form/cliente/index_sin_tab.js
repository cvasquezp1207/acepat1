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
    if( $('#tipo').val() == 'J' ){
      id='photoJ';
    }
    var imagenAR = document.getElementById("file");
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
      ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
        redirect(_controller);
      });
    });
    return false;
}

$(".load_photo").click(function() {
    $("#file").click();
});
// console.log($("#credito_juridico").parent('div.ibox-content'));
$("#credito_juridico").parent('div.ibox-content').css({'background':'transparent !important'}).addClass('here_transparent');

$('.nombres').letras({'permitir':' &.'})
$('.apellidos,.representante_nombres,.representante_nombres').letras({'permitir':' '})
$('.direccion,#direccion_trabajo').alfanumerico({'permitir':' -#./'})
// $('.cliente_email').letras({'permitir':'1234567890@_-'})
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

$('.animation_select').change( function(){
  $('.content_animed').addClass('animated');
  if ( $(this).val() == 'N' ) {//NARUTAL
    $('.info_natural').addClass('rollIn').removeClass('rollOut').css('display','block');
    $('.info_juridico').addClass('rollOut').removeClass('rollIn').css('display','none');
	$('.info_juridico input.here_req,.info_juridico select.here_req').removeClass('req ui-state-error ui-icon-alert');
  }else{//JURIDICO
    $('.info_juridico').addClass('rollIn').removeClass('rollOut').css('display','block');
    $('.info_natural').addClass('rollOut').removeClass('rollIn').css('display','none');
	$('.info_natural input.here_req,.info_natural select.here_req').removeClass('req ui-state-error ui-icon-alert');
  }
  return false;
});

$('#linea_credito').click(function(){
  if ($(this).is(':checked')) {
    $("#limite_credito").removeAttr('readonly');
  }else{
    $("#limite_credito").attr('readonly','readonly').val('0.00');
  }
})

$('#btn_save_cliente').on('click',function(e){
	e.preventDefault();
	if($("#tipo").val()=='N'){//NARUTAL
		$('.info_natural input.here_req,.info_natural select.here_req').addClass('req');
		$('.info_juridico input.here_req,.info_juridico select.here_req').removeClass('req');
	}else{//JURIDICO
		$('.info_juridico input.here_req,.info_juridico select.here_req').addClass('req');
		$('.info_natural input.here_req,.info_natural select.here_req').removeClass('req');
	}
	
	if($('.req').required()){
		if($("#tipo").val()=='N'){
			$('.info_natural input.nombres').attr('name','nombres');
			$('.info_natural input.cliente_email').attr('name','cliente_email');
			$('.info_natural input.ruc').attr('name','ruc');
			
			$('.info_juridico input, .info_juridico select').attr('value','');
		}else{
			$('.info_juridico input.nombres').attr('name','nombres');
			$('.info_juridico input.cliente_email').attr('name','cliente_email');
			$('.info_juridico input.ruc').attr('name','ruc');
			
			$('.info_natural input, .info_juridico select').attr('value','');
		}
		
		cant_dir = 0;
		$('.direccion').each(function(){
			if($('.dir_principal').is(':checked')){
				console.log($('.dir_principal'));
				cant_dir++;
			}else{
				console.log("No check");
			}
		})
		if(cant_dir>=1)
			form.guardar();
		else
			ventana.alert({titulo: "Hey!", mensaje: "Debe Seleccionar una Direccion como Principal", tipo:"warning"});
	}
});

$("#addDireccion").on('click',function(){
	if( $('.direccion').required() ){
		$html_dir ='<div class="input-group" style="margin-top:5px;">';
		$html_dir+='  <span class="input-group-addon tooltip-demo" style="padding: 0px 2px 1px 0px;margin-left: 5px;">';
		$html_dir+='    <div class="radio" style="padding-left: 30px;" data-toggle="tooltip" title="Seleccione Direccion Principal">';
		$html_dir+='        <input type="radio" name="radio_dir" class="dir_principal" value="N" >';
		$html_dir+='        <label></label>';
		$html_dir+='    </div>';
		$html_dir+='    <input type="hidden" class="dir_principal_val" name="dir_principal[]" value="N" >';
		$html_dir+='  </span>';
		$html_dir+='	<input type="text" name="direccion[]" placeholder="Direccion..." value="" class="form-control direccion here_req req">';
		$html_dir+='	<span class="input-group-addon cursor tooltip-demo delete_direccion" style="border:1px solid #1c84c6;color: #1c84c6;border-radius: 0px 3px 3px 0px;">';
		//$html_dir+='		<button type="button" style="height: 30px;" class="btn btn-outline btn-success delete_direccion" data-toggle="tooltip" title="Borrar direccion">';
		$html_dir+='    <div data-toggle="tooltip" class="" title="Borrar direccion">';
		$html_dir+='			<i class="fa fa-trash"></i>';
		//$html_dir+='		</button>';	
		$html_dir+='    </div>';
		$html_dir+='  </span>';
		$html_dir+='</div>';
		$('.list_direcciones').append($html_dir);
		$('.direccion').required();
	}
})

$("#addTelefono").on('click',function(){
	if( $('.telefono').required() ){
		$html_telf='<div class="input-group">';
		$html_telf+='	<input type="text" name="telefono[]" value="" class="form-control telefono here_req req">';
		$html_telf+='	<span class="input-group-btn tooltip-demo">';
		$html_telf+='		<button type="button" style="" class="btn btn-outline btn-success delete_telefono" data-toggle="tooltip" title="Borrar Telefono">';
		$html_telf+='			<i class="fa fa-trash"></i>';
		$html_telf+='		</button>';	
		$html_telf+='	</span>';
		$html_telf+='</div>';
		$('.list_telefonos').append($html_telf);
		$('.telefono').required();
	}
})

$("#addRepresentante").on('click',function(){
	s = true && $('.nombre_representante').required();
	s = s && $('.apellidos_representante').required();
	s = s && $('.dni_representante').required();
	
	if( s ){
		$html_rep ='<div class="col-md-12">';
		$html_rep+='	<div class="row" style="">';
		$html_rep+='		<div class="col-md-3">';
		$html_rep+='      <div class="">';
		$html_rep+='			<label class="required">Nombres</label>';
		$html_rep+='				<input type="text" name="nombre_representante[]" value="" placeholder="Nombre Representante" class="form-control nombre_representante here_req">';
		$html_rep+='			</div>';
		$html_rep+='		</div>';
		
		$html_rep+='		<div class="col-md-6">';
		$html_rep+='      <div class="">';    
		$html_rep+='			<label class="required">Apellidos</label>';		
		$html_rep+='				<input type="text" name="apellidos_representante[]" value="" placeholder="Apellidos Representante" class="form-control apellidos_representante here_req">';
		$html_rep+='			</div>';
		$html_rep+='		</div>';
		
		$html_rep+='    <div class="col-md-3">';
		$html_rep+='		<label class="required">Dni</label>';
		$html_rep+='			<div class="input-group">';
		$html_rep+='				<input type="text" name="dni_representante[]" value="" maxlength="8" placeholder="DNI" class="form-control dni_representante here_req">';
		$html_rep+='				<span class="input-group-btn tooltip-demo">';

		$html_rep+='					<button type="button" style="height: 30px;" class="btn btn-outline delete_repres btn-success" data-toggle="tooltip" title="Añadir Representante">';
		$html_rep+='						<i class="fa fa-trash"></i>';
		$html_rep+='					</button>';

		$html_rep+='				</span>';
		$html_rep+='			</div>';
		$html_rep+='		</div>';

		$html_rep+='	</div>';
		$html_rep+='</div>';
		
		$('.list_representantes').append($html_rep);
		$('.nombre_representante').required();
		$('.dni_representante').numero_entero()
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
  if ($("#linea_credito").is(":checked")) {
    $("#limite_credito").removeAttr('readonly');
  }else{
    $("#limite_credito").attr('readonly','readonly').val('0.00');
  }
  
  $('.animation_select').trigger("change");
});

$("#btn-registrar-zona").on("click", function() {
  $("#modal-zona").modal("show");
  return false;
});

$("#btn-registrar-ocupacion").on("click", function() {
  $("#modal-ocupacion").modal("show");
  return false;
});