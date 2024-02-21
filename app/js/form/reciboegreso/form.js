// $idtipodocumento = 4;
//if ( !$("#idreciboegreso").val() ) {
//}
// if(_es_nueva) {
	LoadSerie($idtipodocumento);
	// $("#idtipodocumento").trigger("change");
	// $("#idmoneda").trigger("change");
// }
// else {
	// llenarDetalle();
// }

$("#btn-registrar-cliente").on("click", function() {
	if($("#tabla").val() == 'CLIENTE'){
		open_modal_cliente(true);
		setTimeout(function(){
			$("#"+prefix_cliente+"tipo").focus();
		},1000);
		return false;
	}else
		$("#modal-empleado").modal("show");
  return false;
});

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

$('#fecha_deposito,#cli_fecha_nac').datepicker({
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
		ajax.post({url: _base_url+$("#tabla").val().toLowerCase()+"/autocomplete", data: "maxRows=10&startsWith="+request.term, dataType: 'json'}, function(data) {
			response( $.map( data, function( item ) {
				return {
					label: item.nombres + " " + item.apellidos
				   ,value: item.nombres + " " + item.apellidos
				   ,nombres: item.nombres
				   ,apellidos: item.apellidos
				   ,dni: item.dni
				   ,ruc: item.ruc
				   ,id: item.idcliente
				}
			}));
		});
	},
	select: function( event, ui ) {
		if(ui.item) {
			$("#recibo_idpersona").val(ui.item.id);
		}
	}
}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
	var html = "";
	if(item.ruc) {
		html += "<strong>RUC: "+item.ruc+"</strong>| ";
	}
	else if(item.dni) {
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

$("#idmoneda_cierre").on("change", function() {
	if($.isNumeric($(this).val())) {
		var id_moneda = $(this).val();
		$('.numero.colum').each(function(x,y){
			$arraysito = $(y).attr("ajax-data");
			res = $arraysito.split("-");
			str = "idmoneda="+id_moneda;
			str+= "&idconceptomovimiento="+res[0]+"&idtipomovimiento="+res[1];
			str+= "&fecha="+_current_date;
			str+= "&idsucursal="+_current_sucursal;
			str+= "&idusuario="+_current_user_id;
			str+= "&id_tipopago=1";
			ajax.post({url: _base_url+_controller+"/get_resumen_c", data: str}, function(res) {
				$id = $(y).attr("id");
				montito = parseFloat(res.monto).toFixed(2);
				$("#"+$id).val(montito);
				if($(y).attr("ajax-type")=='S'){//Salida
					$("#ckb"+res[1]).prop("checked",true);
				}
			});
		});
		
		return;
	}
});

$(".ckb_m").on("click",function(){
	var t_acumulado = 0;
	$(".ckb_m").each(function(){
		if($(this).is(":checked")){
			t_acumulado+=parseFloat($("#monto_"+$(this).attr("ajax-cm")).val());
		}
	});
	$("#monto_acumulado").val(parseFloat(t_acumulado).toFixed(2));
});

$("#tabla").on("change", function() {
	if( $(this).val() != '') {
		$("#cliente_razonsocial,#recibo_idpersona").val('');
		if( $(this).val() == 'CLIENTE' ){
			$('.refern').html('Cliente');
			$("#btn-registrar-cliente").attr({'title':'¿No existe el cliente? Registrar aqui'}).tooltip('fixTitle').tooltip('setContent');
			$("#btn-buscar-cliente").attr({'title':'Buscar clientes'}).tooltip('fixTitle').tooltip('setContent');
		}else{
			$("#btn-registrar-cliente").attr({'title':'¿No existe el empleado? Registrar aqui'}).tooltip('fixTitle').tooltip('setContent');;
			$("#btn-buscar-cliente").attr({'title':'Buscar Empleados'}).tooltip('fixTitle').tooltip('setContent');
			// $("#btn-registrar-cliente").data('tooltip').options.title='¿No existe el empleado? Registrar aqui';
			$('.refern').html('Empleado');
		}
		return;
	}
	// $("#cambio_moneda").val("");
});

$("#btn-buscar-monto").click(function(e){
	e.preventDefault();
	
	if($(this).attr("con_cierre")){
		$("#idmoneda_cierre").html("<option value='"+$("#idmoneda").val()+"'>"+$("#idmoneda option:selected").text()+"</option>").trigger("change");
		
		$("#modal-montos").modal("show");
		
		setTimeout(function(){
			var total_acumulado = 0;
			$('.numero.colum').each(function(x,y){
				$id = $(y).attr("id");
				$arraysito = $(y).attr("ajax-data");
				res = $arraysito.split("-");
				
				if($("#ckb"+res[1]).is(":checked"))
					total_acumulado+= parseFloat($("#"+$id).val());
			});
			console.log(total_acumulado);
			$("#monto_acumulado").val(parseFloat(total_acumulado).toFixed(2));			
		},500);
	}else{
		ventana.alert({titulo: "Hey..!", mensaje: "Usted ya realizó un recibo para el cierre", tipo:"warning"}, function() {
			
		});
	}
});

$("#btn_asign").click(function(e){
	if($("#monto_acumulado").val()<=0){
		ventana.alert({titulo: "Hey..!", mensaje: "El monto debe ser mayor a cero", tipo:"warning"}, function() {
			$("#monto_acumulado").focus();
		});
		return;
	}
	$("#monto").val($("#monto_acumulado").val()).prop("readonly",true);
	$("#en_cierrecaja").val("S");
	$("#modal-montos").modal("hide");
});

$("#btn_no_asign").click(function(e){
	$("#monto").val('').prop("readonly",false).focus();
	$("#en_cierrecaja").val("N");
	$("#modal-montos").modal("hide");
});

$("#btn_save_recibo").click(function(e){
	e.preventDefault();
	s = true && $("#serie").required();
	s = s && $("#numero").required();
	s = s && $("#idtipo_recibo").required();
	s = s && $("#idtipopago").required();
	s = s && $("#idmoneda").required();
	s = s && $("#tipocambio").required();
	s = s && $("#monto").required();
	s = s && $("#cliente_razonsocial").required();
	s = s && $("#concepto").required();
	s = s && $(".requerido").required();
	if(s){
		if($.trim($("#recibo_idpersona").val())==''){
			ventana.alert({titulo: "Hey..!", mensaje: "Debe seleccionar una referencia (Empleado/Cliente) de la lista", tipo:"warning"}, function() {
				$("#cliente_razonsocial").focus();
			});
			return;
		}
		
		pay.setMonto($("#monto").val());
		if($(".idtipopago").val()==1){
			$(".monto_entregado").val($("#monto").val());
			$(".monto_entregado").trigger("keyup");
			
			$(".idcuentas_bancarias option[data-idmoneda!='"+$("#idmoneda").val()+"']").hide();
			$(".idcuentas_bancarias option[data-idmoneda='"+$("#idmoneda").val()+"']").show();
			$(".idcuentas_bancarias option:visible").first().prop("selected", true);

			setTimeout(function(){
				$(".monto_entregado").focus();
			},800);
		}
		pay.ok(function(datos) {
			$(".ckb_m").each(function(){
				datos+= "&id_conceptomovimiento[]="+$(this).val();
			})
			form.guardar(datos);
		});
		pay.show();
	}
})

$(".save_data").on('click',function(e){
	e.preventDefault();
	s = true && $("#idconceptomovimiento").required();
	s = s && $("#idtipopago_modal").required();
	s = s && $(".req").required();
	if (s) {
		$("#idtarjeta,#idcuentas_bancarias").attr("disabled",false);
		$("#idtipopago_modal").attr("disabled",false);
		form.guardar();
	}
});

$("#btn-buscar-cliente").click(function() {
	if( $("#tabla").val() == 'CLIENTE' ){
		jFrame.create({
			title: "Buscar Cliente"
			,controller: "cliente"
			,method: "grilla_popup"
			// ,autoclose: false
			,onSelect: function(datos) {
				$("#cliente_razonsocial").val(datos.cliente);
				$("#recibo_idpersona").val(datos.idcliente);
				// jFrame.close();
			}
		});		
	}else{
		jFrame.create({
			title: "Buscar Empleado"
			,controller: "usuario"
			,method: "grilla_popup"
			// ,autoclose: false
			,onSelect: function(datos) {
				$("#cliente_razonsocial").val(datos.nombres+' '+datos.apellidos);
				$("#recibo_idpersona").val(datos.idusuario);
				// jFrame.close();
			}
		});
	}
	
	jFrame.show();
	return false;
});

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
	// $("#idtipodocumento").trigger("change");
	// $("#serie").trigger("change");
	$("#idmoneda").trigger("change");
}

	// $("#idtipodocumento").removeAttr("aria-required");