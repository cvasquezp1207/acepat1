// $idtipodocumento = 3;

LoadSerie($idtipodocumento);

$("#idtipodocumento_ref").on('change',function() {
	if($.isNumeric($(this).val())) {
		$("#serie_doc,#numero_doc").removeAttr("readonly").addClass("requerido");
	} else {
		$("#serie_doc,#numero_doc").html("").attr("readonly",true).removeClass("requerido");
	}
});

$("#serie").on('change',function() {
	if($.isNumeric($idtipodocumento)) {
		$serie_correlativo = $("#serie").val();
		if(!$("#serie").val())
			$serie_correlativo = 1;
		if (_es_nueva) {
			ajax.post({
				url: _base_url+"tipo_documento/get_correlativo", 
				data: "idtipodocumento="+$idtipodocumento+"&serie="+$serie_correlativo
			}, function(res) {
				if(res)
					$("#numero").val(res.correlativo);
				else
					$("#numero").val("");
			});
		}
	}else {
		// $("#numero").val("");
	}
});

$("#btn-buscar-cliente").click(function() {
	jFrame.create({
		title: "Buscar Cliente"
		,controller: "cliente"
		,method: "grilla_popup"
		// ,autoclose: false
		,onSelect: function(datos) {
			console.log(datos);
			$("#cliente_razonsocial").val(datos.cliente);
			$("#recibo_idcliente").val(datos.idcliente);
			// jFrame.close();
		}
	});
	
	jFrame.show();
	return false;
});

$("#btn-registrar-cliente").on("click", function() {
	open_modal_cliente(true);
	setTimeout(function(){
		$("#"+prefix_cliente+"tipo").focus();
	},1000);
	return false;
});

$('#fecha_deposito').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	endDate: parseDate(_current_date)
});

modalsillo( $("#idtipopago") );

$("#idtipopago_modal,#idtipopago").on('change',function(){
	modalsillo( $(this) );
})

$("#cliente_razonsocial").autocomplete({
	source: function( request, response ) {
		ajax.post({url: _base_url+"cliente/autocomplete", data: "maxRows=10&startsWith="+request.term, dataType: 'json'}, function(data) {
			response( $.map( data, function( item ) {
				return {
					label: item.nombres + " " + item.apellidos
				   ,value: item.nombres + " " + item.apellidos
				   ,nombres: item.nombres
				   ,apellidos: item.apellidos
				   ,dni: (item.dni)? item.dni :''
				   ,ruc: (item.ruc)? item.ruc :''
				   ,id: item.idcliente
				}
			}));
		});
	},
	select: function( event, ui ) {
		if(ui.item) {
			$("#recibo_idcliente").val(ui.item.id);
		}
	}
}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
	var html = "";
	if($.trim(item.ruc)) {
		html += "<strong>RUC: "+item.ruc+"</strong>| ";
	}
	else if($.trim(item.dni)) {
		html += "<strong>DNI: "+item.dni+"</strong>| ";
	}
	html += item.nombres+" "+item.apellidos;
	
	return $( "<li>" )
	.data( "ui-autocomplete-item", item )
	.append( html )
	.appendTo( ul );
};

$("#idmoneda").on("change", function() {
	if($.isNumeric($(this).val())) {
		ajax.post({url: _base_url+"moneda/get/"+$(this).val()}, function(data) {
			valor = parseFloat(data.valor_cambio);
			$("#cambio_moneda").val(valor.toFixed(2));
		});
		return;
	}
	$("#cambio_moneda").val("");
});

$("#btn_save_recibo").click(function(e){
	e.preventDefault();
	s = true && $("#serie").required();
	s = s && $("#numero").required();
	//s = s && $("#tipo_ingreso").required();
	s = s && $("#idtipo_recibo").required();
	s = s && $("#idtipopago").required();
	s = s && $("#idmoneda").required();
	s = s && $("#tipocambio").required();
	s = s && $("#monto").required();
	s = s && $("#cliente_razonsocial").required();
	s = s && $("#concepto").required();
	s = s && $(".requerido").required();
	if(s){
		if($.trim($("#recibo_idcliente").val())==''){
			ventana.alert({titulo: "Hey..!", mensaje: "Debe seleccionar un Cliente de la lista", tipo:"warning"}, function() {
				$("#cliente_razonsocial").focus();
			});
			return;
		}
		// $("#idtipopago_modal").val( $("#idtipopago").val() );
		// $("#idtipopago_modal").trigger("change");
		pay.setMonto($("#monto").val());
		
		$(".monto_entregado").val($("#monto").val());
		$(".monto_entregado").trigger("keyup");
		
		// $(".idcuentas_bancarias").trigger("change");
		$(".idcuentas_bancarias option[data-idmoneda!='"+$("#idmoneda").val()+"']").hide();
		$(".idcuentas_bancarias option[data-idmoneda='"+$("#idmoneda").val()+"']").show();
		$(".idcuentas_bancarias option:visible").first().prop("selected", true);
		
		setTimeout(function(){
			$(".monto_entregado").focus();
		},800);
		
		pay.ok(function(datos) {
			form.guardar(datos);
		});
		pay.show();
		// if ($("#idreciboingreso").val()){
			// console.log("Here.... New");
			// accion = "";
			// idtablilla = "";
			// $(".tabla_deposito input[type='text'],.tabla_tarjeta input[type='text']").attr("readonly",'readonly');
			// $("#idtarjeta,#idcuentas_bancarias").attr("disabled",true);
			// $("#idtipopago_modal").attr("disabled",true);

			// $(".block_content").css('display', 'block');
			// if ( $("#idtipopago").val() == '2' ){//TARJETA
				// accion = 'get_tarjeta';
				// idtablilla = $("#idtarjeta").val();

				// ajax.post({url: _base_url+_controller+"/"+accion, data: "id="+$("#idreciboingreso").val()+"&idtablilla="+idtablilla+"&tablilla="+_controller.toUpperCase()}, function(res) {
					// $("#idoperacion_tarjeta").val(res.operacion);
					// $("#tabla").val(res.tabla);
					// $("#nro_operaciont").val(res.nro_operacion);
					// $("#nro_tarjeta").val(res.nro_tarjeta);
					// $("#importe").val(res.importe);

					// $("#modal-form").modal('show');
				// });
			// }else if ($("#idtipopago").val() == '3') {//DEPOSITO
				// accion = 'get_deposito';
				// idtablilla = $("#idcuentas_bancarias").val();
				// $('#fecha_deposito').datepicker("remove");
				// ajax.post({url: _base_url+_controller+"/"+accion, data: "id="+$("#idreciboingreso").val()+"&idtablilla="+idtablilla+"&tablilla="+_controller.toUpperCase()}, function(res) {
					// $("#idoperacion").val(res.operacion);
					// $("#tabla").val(res.tabla);
					// $("#nro_operaciond").val(res.nro_operacion);
					// $("#fecha_deposito").val(res.fecha_deposito);
					// $("#importe_deposito").val(res.importe);

					// $("#modal-form").modal('show');
				// });
			// }else
				// $("#modal-form").modal('show');

		// }else{
			// $("#modal-form").modal('show');
		// }
	}
});

// $(".save_data").on('click',function(e){
	// e.preventDefault();
	// s = true && $("#idconceptomovimiento").required();
	// s = s && $("#idtipopago_modal").required();
	// s = s && $(".req").required();
	// if (s) {
		// $("#idtarjeta,#idcuentas_bancarias").attr("disabled",false);
		// $("#idtipopago_modal").attr("disabled",false);
		// form.guardar();
	// }
// })

function modalsillo(select){
	$("#idtipopago_modal").val( select.val() );
	$(".tabla_tarjeta,.tabla_deposito").hide();
	$("#idtarjeta,#idcuentas_bancarias").removeAttr("name").removeClass('req');
	$(".tabla_deposito input[type='text'],.tabla_tarjeta input[type='text']").removeClass("req");
	//$(".tabla_tarjeta input[type='text']").removeClass("req");

	$('.tabla_tarjeta input').each(function(x,y){
		$extact_id = $(y).attr("id");
		$("#"+$extact_id).removeAttr("name");
	})

	$('.tabla_deposito input').each(function(x,y){
		$extact_id = $(y).attr("id");
		$("#"+$extact_id).removeAttr("name");
	})

	if ( select.val() == '1' ) {//EFECTIVO
		//console.log("No hacer nada....");
	}else if ( select.val() == '2' ) {//TARJETA
		$(".tabla_tarjeta").show();
		$("#importe").val( parseFloat($("#monto").val()).toFixed(2) );
		$("#idtarjeta").attr("name","idtarjeta");

		$('.tabla_tarjeta input').each(function(x,y){
			$extact_name = $(y).attr("type-name");
			$extact_id = $(y).attr("id");
			$("#"+$extact_id).attr("name",$extact_name);
		})

		$(".tabla_tarjeta input[type='text'],#idtarjeta").addClass("req");
	}else if ( select.val() == '3' ) {//DEPOSITO
		$(".tabla_deposito").show();
		$("#importe_deposito").val( parseFloat($("#monto").val()).toFixed(2) );
		$("#idcuentas_bancarias").attr("name","idcuentas_bancarias");

		$('.tabla_deposito input').each(function(x,y){
			$extact_name = $(y).attr("type-name");
			$extact_id = $(y).attr("id");
			$("#"+$extact_id).attr("name",$extact_name);
		})

		$(".tabla_deposito input[type='text'],#idcuentas_bancarias").addClass("req");
	}
}

function LoadSerie(idtipodocumento){
	if($.isNumeric(idtipodocumento)) {
		reload_combo("#serie", 
		{
			controller: "tipo_documento",
			method: "get_series", 
			data: "idtipodocumento="+idtipodocumento
		}, function() {
			if($.trim(_serie))
				$("#serie").val(_serie);
			$("#serie").trigger("change");
		});
	}
	else {
		$("#serie").html("").trigger("change");
	}
}

if(_es_nueva) {
	$("#idmoneda").trigger("change");
}

	// $("#idtipodocumento").removeAttr("aria-required");