if(typeof form == 'undefined') {
	form = {};
}

if( !$.isFunction(form.guardar_cliente) ) {
	form.guardar_cliente = function() {
		// var data = $("#form_cliente").serialize();
		// model.save(data, function(res) {
			// $("#modal-cliente").modal("hide");
		// }, "cliente");
		// console.log( $("#form_cliente").serialize() );
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
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				$("#modal-cliente").modal("hide");
				$("#compra_idcliente").val(resp);
				
				ajax.post({url: _base_url+"cliente/get_post",data:"id="+resp}, function(rpt) {
					var html = "";
					if(rpt.ruc) {
						// html += "<strong>RUC: "+rpt.ruc+"</strong>| ";
					}
					else if(rpt.dni) {
						// html += "<strong>DNI: "+rpt.dni+"</strong>| ";
					}
					html += rpt.nombres+" "+rpt.apellidos;
					$("#cliente_razonsocial").val(html);
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

// validate("#form_cliente", form.guardar_cliente);

$(function(){
  if ($("#cli_linea_credito").is(":checked")) {
    $("#cli_limite_credito").removeAttr('readonly');
  }else{
    $("#cli_limite_credito").attr('readonly','readonly').val('0.00');
  }
  
  $('.animation_select').trigger("change");
});

$('.nombres').letras({'permitir':' &.'})
$('.apellidos,.representante_nombres,.representante_nombres').letras({'permitir':' '})
$('.direccion,#direccion_trabajo').alfanumerico({'permitir':' -#./'})
// $('.cliente_email').letras({'permitir':'1234567890@_-'})
$('.cliente_email').alfanumerico({'permitir':'@-_.'});
$('.dni,.ruc,.dni_representante').numero_entero();
$('.telefono').numero_entero({'permitir':' -*#'});
$('#cli_ingreso_mensual,#cli_limite_credito').numero_real();

// $('.input-group.date').datepicker({
    // todayBtn: "linked",
	// keyboardNavigation: false,
	// forceParse: false,
	// autoclose: true,
	// language: 'es',
	// format: "dd/mm/yyyy",
	// endDate: parseDate(_current_date)
// });

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

$('#cli_btn_save_cliente').click(function(e){
	e.preventDefault();
	if($("#cli_tipo").val()=='N'){//NARUTAL
		$('.info_natural input.here_req,.info_natural select.here_req').addClass('req');
		$('.info_juridico input.here_req,.info_juridico select.here_req').removeClass('req');
	}else{//JURIDICO
		$('.info_juridico input.here_req,.info_juridico select.here_req').addClass('req');
		$('.info_natural input.here_req,.info_natural select.here_req').removeClass('req');
	}
	
	if($('.req').required()){
		if($("#cli_tipo").val()=='N'){
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
				cant_dir++;
			}
		})
		if(cant_dir>1)
			form.guardar_cliente();
		else
			ventana.alert({titulo: "Hey!", mensaje: "Debe Seleccionar una Direccion como Principal", tipo:"warning"});
	}else
		console.log('Here...');
});

$(".load_photo").click(function() {
    $("#cli_file").click();
});

$("#cli_btn_cancel").click(function() {
    $("#modal-cliente").modal("hide");
	// $('.list_direcciones,.list_telefonos,.list_representantes').empty();
});

function leerarchivobin(f) {
    id='cli_photoN';
    if( $('#cli_tipo').val() == 'J' ){
      id='photoJ';
    }
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