var pay = {
	selector: "#modal-pay" // selector, ID div modal
	,onCancel: $.noop // callback when click button cancel
	,onSave: $.noop // callback button save
	
	// title modal window
	,setTitle: function(title) {
		$(".modal-title", this.selector).html(title);
	}
	
	// function set values
	,setIdconceptomovimiento: function(idconceptomovimiento) {
		$("select.idconceptomovimiento", this.selector).val(idconceptomovimiento);
	}
	,setIdtipopago: function(idtipopago) {
		var d = $("select.idtipopago", this.selector).prop("disabled");
		
		$("select.idtipopago", this.selector).prop("disabled", false);
		$("select.idtipopago", this.selector).val(idtipopago).trigger("change");
		$("select.idtipopago", this.selector).prop("disabled", d);
	}
	,setFecha: function(fecha) {
		$("input.fecha_deposito", this.selector).val(fecha);
	}
	,setMonto: function(monto_pagar) {
		monto_pagar = parseFloat(monto_pagar);
		if(isNaN(monto_pagar))
			monto_pagar = 0;
		
		$("input.monto_pagar", this.selector).val(monto_pagar.toFixed(2));
	}
	
	// disable combo idtipopago
	,disabledTipopago: function(disabled) {
		$("select.idtipopago", this.selector).prop("disabled", disabled);
	}
	
	,show: function() {
		$(this.selector).modal("show");
	}
	,close: function() {
		$("input.monto_entregado", this.selector).val("");
		$("input.monto_vuelto", this.selector).val("");
		$(this.selector).modal("hide");
	}
	
	// obtener datos serializados
	,getSerialize: function() {
		var d = $("select.idtipopago", this.selector).prop("disabled");
		
		$("select.idtipopago", this.selector).prop("disabled", false);
		var data = $("form", this.selector).serialize();
		$("select.idtipopago", this.selector).prop("disabled", d);
		
		return data;
	}
	,getSerializeArray: function() {
		var d = $("select.idtipopago", this.selector).prop("disabled");
		
		$("select.idtipopago", this.selector).prop("disabled", false);
		var data = $("form", this.selector).serializeArray();
		$("select.idtipopago", this.selector).prop("disabled", d);
		
		return data;
	}
	,getField: function(field) {
		var v = $("."+field, this.selector).val();
		
		if(field == "idtipopago") {
			var d = $("select.idtipopago", this.selector).prop("disabled");
			$("select.idtipopago", this.selector).prop("disabled", false);
			v = $("form", this.selector).val();
			$("select.idtipopago", this.selector).prop("disabled", d);
		}
		
		return v;
	}
	
	,cancel: function(callback) {
		this.onCancel = callback;
	}
	,ok: function(callback) {
		this.onSave = callback;
	}
};

// evento show del modal
$(pay.selector).on('shown.bs.modal', function() {
	if( $("input.afecta_caja", pay.selector).length ) {
		$("input.afecta_caja", pay.selector).trigger("change");
	}
});

// numeric monto entregado pago EFECTIVO
$("input.monto_entregado,input.monto_convertido_pay,input.tipo_cambio_vigente", pay.selector).numero_real();

// calendar fecha deposito
$("input.fecha_deposito", pay.selector).datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	endDate: parseDate(_current_date)
});

// event click switch
$(".onoffswitch-label", pay.selector).on("click", function() {
	var sel = $(this).attr("for");
	var check = !$("input."+sel, pay.selector).prop("checked");
	$("input."+sel, pay.selector).prop("checked", check);
	if(sel == "afecta_caja") {
		$("input."+sel, pay.selector).trigger("change");
	}
});

// event change afecta caja
$("input.afecta_caja", pay.selector).change(function() {
	if($(this).is(":checked")) {
		$("div.no_afecta", pay.selector).addClass("hide");
		$("div.afecta", pay.selector).removeClass("hide");
		$("select.idtipopago", pay.selector).trigger("change");
	}
	else {
		$("div.no_afecta", pay.selector).removeClass("hide");
		$("div.afecta", pay.selector).addClass("hide");
	}
});

// trigger change idtipopago
$("select.idtipopago", pay.selector).on("change", function() {
	$("div.efectivo", pay.selector).addClass("hide");
	$("div.tarjeta", pay.selector).addClass("hide");
	$("div.deposito", pay.selector).addClass("hide");
	
	if( $(this).val() == "1" ) {
		$("div.efectivo", pay.selector).removeClass("hide");
		
		keyboardSequence([	".idconceptomovimiento"
						,".idtipopago"
						,".monto_pagar"
						,".monto_entregado"
						,".monto_vuelto"
						,retornar_boton( "-pay-mov","","btn-accept-pay",'N')
						], "#form-pay-mov");
	}
	else if( $(this).val() == "2" ) {
		$("div.tarjeta", pay.selector).removeClass("hide");
		
		keyboardSequence([	".idconceptomovimiento"
						,".idtipopago"
						,".monto_pagar"
						,".idtarjeta"
						,".nro_tarjeta"
						,".operacion_tarjeta"
						,retornar_boton( "-pay-mov","","btn-accept-pay",'N')
						], "#form-pay-mov");
	}
	else if( $(this).val() == "3" ) {
		$("div.deposito", pay.selector).removeClass("hide");
		
		keyboardSequence([	".idconceptomovimiento"
						,".idtipopago"
						,".monto_pagar"
						,".idcuentas_bancarias"
						,".fecha_deposito"
						,".operacion_deposito"
						,retornar_boton( "-pay-mov","","btn-accept-pay",'N')
						], "#form-pay-mov");
	}
});

// keyup monto_entregado calculo vuelto
$("input.monto_entregado", pay.selector).on("keyup", function() {
	var v = parseFloat($(this).val());
	if(isNaN(v))
		v = 0;
	var m = parseFloat($("input.monto_pagar", pay.selector).val());
	var r = v - m;
	$("input.monto_vuelto", pay.selector).val(r.toFixed(2));
});

// change cuentas bancarias
$("select.idcuentas_bancarias", pay.selector).on("change", function(e) {
	if( $(".idcuentas_bancarias option:selected", pay.selector).attr("data-idmoneda")==$("#idmoneda").val()){
		$(".monto_convertido_pay,.tipo_cambio_vigente").prop("readonly",true);
	}else{
		$(".monto_convertido_pay,.tipo_cambio_vigente").prop("readonly",false);
	}
	
	if($(this).val()!=''){
		var valor_cambio_cta = $(".idcuentas_bancarias option:selected", pay.selector).attr("data-valor_cambio");
		$("input.tipo_cambio_vigente", pay.selector).val(parseFloat(valor_cambio_cta).toFixed(2))
		$("#id_moneda_cambio").val($(".idcuentas_bancarias option:selected", pay.selector).attr("data-idmoneda"));
	}else{
		$("input.tipo_cambio_vigente", pay.selector).val(0);
		$("#id_moneda_cambio").val('');
	}
	$("input.tipo_cambio_vigente", pay.selector).trigger("keyup");
});

// keyup monto_convertido_pay calculo tipo cambio vigente conversion
$("input.monto_convertido_pay", pay.selector).on("keyup", function() {
	// var v = parseFloat($("input.tipo_cambio_vigente", pay.selector).val());
	var v = parseFloat($(this, pay.selector).val());
	if(isNaN(v))
		v = 0;
	
	var id_moneda_cuentax = $(".idcuentas_bancarias option:selected", pay.selector).attr("data-idmoneda");
	var id_moneda_operacx = $("#idmoneda").val();

	var m_convertido = v;
	var valor_cambio_ope = $("#cambio_moneda").val();
	if(isNaN(id_moneda_operacx))
		id_moneda_operacx = 1;//1 por defecto que es en soles
	var tc_convt = 0;
	
	if(id_moneda_operacx==1){//Si la cuenta del pay es en soles
		var tc_convt = parseFloat($("input.monto_pagar", pay.selector).val())/parseFloat(m_convertido);
	}else if(id_moneda_operacx==1){// No vamos a convertir de otra moneda a soles
		var tc_convt = parseFloat($("input.monto_pagar", pay.selector).val())*parseFloat(m_convertido);
	}else if(id_moneda_cuentax!=1 && id_moneda_operacx==1){// la moneda de la operacion es dolares, y la moneda 
		var xmonto_c =  parseFloat($("input.monto_pagar", pay.selector).val())/parseFloat(m_convertido);
		var tc_convt = xmonto_c * valor_cambio_cta;
	}

	$("input.tipo_cambio_vigente", pay.selector).val(tc_convt.toFixed(3));
});


// keyup cambio moneda calculo monto convertido vigente conversion
$("input.tipo_cambio_vigente", pay.selector).on("keyup", function() {
	var v = parseFloat($(this).val());
	if(isNaN(v))
		v = 0;
	
	var id_moneda_cuentax = $(".idcuentas_bancarias option:selected", pay.selector).attr("data-idmoneda");
	var id_moneda_operacx = $("#idmoneda").val();

	var valor_cambio_cta = v;
	var valor_cambio_ope = $("#cambio_moneda").val();
	var monto_pay_pagar	 = $("input.monto_pagar", pay.selector).val();
	if(isNaN(id_moneda_operacx))
		id_moneda_operacx = 1;//1 por defecto que es en soles
	var monto_convt = 0;
	
	if(id_moneda_operacx==1){//Si la cuenta del pay es en soles
		var monto_convt = parseFloat(monto_pay_pagar)/parseFloat(valor_cambio_cta);
	}else if(id_moneda_cuentax==1){// No vamos a convertir de otra moneda a soles
		var monto_convt = parseFloat(monto_pay_pagar)*parseFloat(valor_cambio_ope);
	}else if(id_moneda_cuentax!=1 && id_moneda_operacx==1){// la moneda de la operacion es dolares, y la moneda 
		var xmonto_c =  parseFloat(monto_pay_pagar)/parseFloat(valor_cambio_ope);
		var monto_convt = xmonto_c * valor_cambio_cta;
	}
	console.log(id_moneda_operacx);
	console.log(id_moneda_cuentax);
	console.log(valor_cambio_cta);

	$("input.monto_convertido_pay", pay.selector).val(monto_convt.toFixed(3));
});

// event button cancel
$("button.btn-cancel-pay", pay.selector).on("click", function(e) {
	e.preventDefault();
	if($.isFunction(pay.onCancel)) {
		pay.onCancel();
	}
	pay.close();
	return false;
});

// event button save
$("button.btn-accept-pay", pay.selector).on("click", function(e) {
	e.preventDefault();
	
	// eliminas la propiedad disabled del combo tipopago
	var d = $(".idtipopago", pay.selector).prop("disabled");
	$(".idtipopago", pay.selector).prop("disabled", false);
	var s = true, idpago = $(".idtipopago", pay.selector).val();
	
	// verificamos si afecta a caja
	if($(".afecta_caja", pay.selector).is(":checked")) {
		// algunas validaciones
		s = s && $(".idconceptomovimiento", pay.selector).required();
		s = s && $(".idtipopago", pay.selector).required();
		s = s && $(".monto_pagar", pay.selector).required({numero:true, tipo:"float", aceptaCero:true});//Esto anteriormente era aceptaCero:false( pero se puso true para aceptar ventas con monto cero)
		if( idpago == "1" ) { // efectivo
			s = s && $(".monto_entregado", pay.selector).required({numero:true, tipo:"float", aceptaCero:true});//Esto anteriormente era aceptaCero:false( pero se puso true para aceptar ventas con monto cero)
		}
		else if( idpago == "2" ) { // tarjeta
			s = s && $(".idtarjeta", pay.selector).required();
			s = s && $(".nro_tarjeta", pay.selector).required();
			s = s && $(".operacion_tarjeta", pay.selector).required();
		}
		else if( idpago == "3" ) { // deposito
			s = s && $(".idcuentas_bancarias", pay.selector).required();
			s = s && $(".fecha_deposito", pay.selector).required();
			s = s && $(".operacion_deposito", pay.selector).required();
			if(s){
				// var id_moneda_cuenta = $(".idcuentas_bancarias option:selected").attr("data-idmoneda");
				// var id_moneda_operac = $("#idmoneda").val();
				// if(isNaN(id_moneda_operac))
					// id_moneda_operac = 1;//1 por defecto que es en soles
				
				
				// if(id_moneda_operac!=id_moneda_cuenta){
					// var band = true;
					// if(parseFloat($(".monto_convertido_pay", pay.selector).val())==parseFloat($(".monto_pagar", pay.selector).val())){
						// ventana.alert({titulo: "Espere...!", mensaje: "Te falta convertir el monto con el tipo de cambio de la cuenta bancaria seleccionada.", tipo:"warning"}, function() {
							
						// });
						// band = false;
					// }
					
					// if(band){
						// if(parseFloat($(".monto_convertido_pay", pay.selector).val()*$(".tipo_cambio_vigente", pay.selector).val()) != $(".monto_pagar",pay.selector).val()){
							// ventana.alert({titulo: "Hey...!", mensaje: "El monto a pagar no es igual que el monto convertido por su tipo de cambio..", tipo:"warning"});
							// band = false;
						// }
					// }
					
					// if(!band){
						// return;
					// }
				// }
			}
		}
	}
	
	if(s) {
		if($.isFunction(pay.onSave)) {
			pay.onSave($("form", pay.selector).serialize());
			$(".idtipopago", pay.selector).prop("disabled", d);
		}
		
		pay.close();
	}
	
	return false;
});

$('.monto_pagar,.monto_entregado,.monto_vuelto').css({"text-align":'right'});