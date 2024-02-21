bloquear_monto_apertura = true;
$('#button-abrir').click(function(){
	$('.monto_cierre').each(function(i,j){
		$idmoney = $(j).attr('ajax-money');
		ajax.post({url: _base_url+_controller+"/caja_anterior", data: "idmoneda="+$idmoney}, function(res) {
			$idmoney = $(j).attr('ajax-money');
			if(res)
				$("#monto_cierre"+$idmoney+",#monto_abrir_caja"+$idmoney).val(res);
			else
				$("#monto_cierre"+$idmoney+",#monto_abrir_caja"+$idmoney).val('0.00');
		});
	});
	if(bloquear_monto_apertura)
		$(".input_apertura").attr("readonly",true);
	$("#abrir-caja").modal('show');
});

$('#button-cerrar').click(function(){
	$("#cerrar-caja").modal('show');
});

$('#button-reabrir').click(function(){
	ventana.confirm({
		titulo:"Aviso",
		mensaje:"¿Desea volver abrir caja?",
		textoBotonAceptar: "Abrir",
		tipo: "warning"
	}, function(ok){
		if(ok) {
			ajax.post({url: _base_url+_controller+"/reaperturar_caja", data: "idcaja="+$("#caja_hoy").val()}, function(res) {
				ventana.alert({titulo: "En horabuena!", mensaje: "Caja reaperturada correctamente", tipo:"success"}, function() {
					redirect(_controller);
				});
			});
		}
	});
});

$(".numero").numero_real();

$('#button-arqueo').click(function(){
	$("#arqueo-caja").modal('show');
	setTimeout(function(){
		// console.log($('#form-arqueo').find('input[type=text]').filter(':visible:first'));
		$('#form-arqueo').find('input[type=text]').filter(':visible:first').focus();
	},1000);
});

$("#open-caja .btn-save").click(function(){
	band = true;
	valida_solo_soles = true;
		/* 
		true = solo valida nuevos soles
		false = ambas monedas
		*/
	texto = "La caja en ";
	$("#open-caja input.input_apertura").each(function(){
		if(valida_solo_soles){
			if($(this).attr("text-idmoneda")==1 && parseFloat($(this).val())==0){
				$(this).prop("readonly",false);
				texto+=$(this).attr("text-data");
				band = false;
				return false;
			}else{
				$(this).prop("readonly",true);
			}
		}else{
			if(parseFloat($(this).val())==0){
				$(this).prop("readonly",false);
				texto+=$(this).attr("text-data");
				band = false;
				return false;
			}else{
				$(this).prop("readonly",true);
			}
		}
	});
	texto+=" no debe aperturarse con saldo cero :(";
	
	if(band){
		$("#open-caja .btn-save").prop("disabled",true);
		ajax.post({url: _base_url+_controller+"/save", data: $("#open-caja").serialize()}, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Caja aperturada correctamente", tipo:"success"}, function() {
				$("#open-caja .btn-save").prop("disabled",false);
				redirect(_controller);
			});
		});		
	}else{
		ventana.confirm({titulo: "Espere..!", mensaje: texto, tipo:"warning"
						,textoBotonAceptar: "Abrir"
						,textoBotonCancelar:"Cancelar"}, function(ok) {
			if(ok)
			{
				$("#open-caja .btn-save").prop("disabled",true);
				ajax.post({url: _base_url+_controller+"/save", data: $("#open-caja").serialize()}, function(res) {
				ventana.alert({titulo: "En horabuena!", mensaje: "Caja aperturada correctamente", tipo:"success"}, function() {
				$("#open-caja .btn-save").prop("disabled",false);
				redirect(_controller);
					});
				});	
			}
		});
	}
});

$('.idinero').keyup(function() {
	calcularTotal( $(this).attr('data-moneda') );
});

$("#form-cerrar .btn-save").click(function(){
	ajax.post({url: _base_url+_controller+"/cerrar_caja", data: $("#form-cerrar").serialize()}, function(res) {
		ventana.alert({titulo: "En horabuena!", mensaje: "Caja cerrada correctamente", tipo:"success"}, function() {
			redirect(_controller);
		});
	});
})

$("#form-arqueo .btn-save").click(function(){
	band = false;
	html = '';
	input = '';
	tab_ac= '';
	$('.total').each(function(){
		$id_name 	= $(this).attr('id');
		$idmoneda 	= $(this).attr('data-money');
		$simbolo	= $(this).attr('data-simbolo');
		var to = parseFloat( $(this).val() );
		var sc = parseFloat( $("#saldo"+$idmoneda ).val() );

		if( parseFloat($(this).val()) < parseFloat($( '#saldo'+$idmoneda ).val()) ){
			// $(this).required().focus();
			input = $(this);
			tab_ac= $(this).attr("data-tab");
			$(this).addClass('ui-state-error ui-icon-alert');
			band = false;
			return false;
		}else{
			$(this).removeClass('ui-state-error ui-icon-alert');
			var re = to - sc;
			if(re > 0 ) {
				html += 'Se registrará el arqueo con un dinero excedente de: '+$simbolo+' '+re.toFixed(2)+'';
			}
			band = true;
		}
	});
	
	if(input){
		$('.nav-tabs a[href="#tab-'+(tab_ac)+'"]').tab('show');
		input.focus();
		return;
	}
	
	if(band){
		html += '¿Realmente desea guardar el arqueo?';
		ventana.confirm({
				titulo:"Aviso",
				mensaje:html,
				textoBotonAceptar: "Si",
				textoBotonCancelar: "No",
		}, function(ok){
				if(ok) {
					$("#arqueo_confirmar").modal('show');
					setTimeout(function(e){
						$("#arqueo_confirmar .btn-save").focus();
					},800);
				}
		});
	}
});

$("#form-arqueo .btn-cancel").click(function(e){
	$("#form-arqueo").find('input.idinero').val('');
	$("#form-arqueo").find('input.total').val('0.00');
});

$("#arqueo_confirmar .btn-save").click(function(){
	ajax.post({url: _base_url+_controller+"/arqueo_caja", data: $("#form-arqueo").serialize()}, function(res) {
		ventana.alert({titulo: "En horabuena!", mensaje: "El arqueo se realizo con exito..!", tipo:"success"}, function() {
			redirect(_controller);
		});
	});
});

// Secuencia enter index arqueo caja
var my_form_arqu 	= "#arqueo-caja";
$(my_form_arqu+' input').keydown( function(e) {
    var key = e.charCode ? e.charCode : e.keyCode ? e.keyCode : 0;
    if(key == 13) {
        e.preventDefault();
        var inputs 		= $(this).closest('form').find(':input:visible');
		var cont   		= inputs.index(this);//Posicion del focus input -1
		var cont_tab	= $("ul.nav-tabs li").length;
		var acti_tab	= parseInt($("ul.nav-tabs li.active").attr('index_li'));
		var xacti_tab	= parseInt($("ul.nav-tabs li.active").attr('index_li'))+1;
		var cont_input_a= $(".tab-pane#tab-"+acti_tab).find('input:visible').length;//cantidad de input en el tab activo
		var init_tab	= 1;

        inputs.eq( inputs.index(this)+ 1 ).focus();
		
		// if(cont==(inputs.length-1)){
			// console.log(inputs.index(this));
			// if(parseInt(cont_tab)!=parseInt(acti_tab+1)){
				// acti_tab++;
				// $('.nav-tabs a[href="#tab-'+(acti_tab)+'"]').tab('show');
				// var mone_tab	= $("ul.nav-tabs li.active").attr('mon_li');
				// $("#billete_"+mone_tab+"_"+acti_tab).focus();
			// }else{
				// console.log(retornar_boton(my_form_arqu));
				// $(retornar_boton(my_form_arqu)).trigger("click");
			// }
		// }
		if(cont_input_a==cont){
			if(parseInt(cont_tab)!=parseInt(acti_tab+1)){
				acti_tab++;
				$('.nav-tabs a[href="#tab-'+(acti_tab)+'"]').tab('show');
				var mone_tab	= $("ul.nav-tabs li.active").attr('mon_li');
				$("#billete_"+mone_tab+"_"+acti_tab).focus();
			}else{
				$("#form-arqueo .btn-save").trigger("click");
			}
		}
    }
});

function callbackCaja(nRow, aData, iDisplayIndex) {
	// $('td:eq(0)', nRow).html(dateFormat(parseDate(aData['fecha']), "d/m/Y"));
	$('td:eq(5)', nRow).html("<div style='text-align:right;'>"+aData['monto']+"</div>");
	$('td:eq(6)', nRow).html("<div style='text-align:right;'>"+aData['saldo']+"</div>");
}

function calcularTotal(idmoneda) {
	var total = 0, val, cons;
	$('.idinero').each(function() {
		if( $(this).attr('data-moneda') == idmoneda ){
			val = parseInt( $(this).val() );
			if(isNaN(val))
				val = 0;
			cons = parseFloat( $(this).attr('const') );
			total += val * cons;
		}
	});
	$("#total"+idmoneda).val( total.toFixed(2) );
}