/* PARA EL PROVEEDOR*/
input.autocomplete({
	selector: "#deuda_proveedor"
	,controller: "deuda"
	,method:"proveedor_proveedor"
	,label: "[nombre]"
	,value: "[nombre]"
	,maxRows: 10
	,highlight: true
	,onSelect: function(item) {
		$("#idproveedor").val(item.idproveedor);
		$("#proveedor_ruc").val(item.ruc);
		
		cargar_compras();
	}
});

$("#btn-buscar-proveedor").click(function(e) {
	e.preventDefault();
	
	jFrame.create({
		title: "Buscar Proveedor"
		,msg: ""
		,controller: "proveedor"
		,method: "grilla_popup_deuda"
		// ,autoclose: false
		,onSelect: function(datos) {
			$("#deuda_proveedor").val(datos.proveedor);
			$("#idproveedor").val(datos.idproveedor);
			$("#proveedor_ruc").val(datos.ruc);
			cargar_compras();
		}
	});
	
	jFrame.show();
	return false;
});
/* PARA EL PROVEEDOR*/

$("#idmoneda").change(function(e){
	cargar_compras();
});

$("#monto,#descuento").numero_real();
$("#cant_letras,#nro_dias").numero_entero();
$("#fecha_deuda").datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es'
}).on("changeDate",function(e){
	e.preventDefault();
	calculo_totales();
});

$("#id_compras").click(function(e){
	var suma = 0;
	var selc = $(this).val();
	console.log(selc);
	console.log($(this));
	$(selc).each(function(i,j){
		suma+=parseFloat($("#id_compras option[value='"+j+"']").attr("data-monto"));
		// suma+=parseFloat(j);
	});
	
	$(this).each(function(x,y){
		// console.log($(y));
	});
	$("#monto").val(suma.toFixed(2));
	
	calculo_totales();
});

$("#gastos").keyup(function(e){
	calculo_totales();
});

$("#descuento").keyup(function(e){
	calculo_totales();
});

$(document).on("keyup",".gastos",function(e){
	x_gastos = 0;
	$('.gastos').each(function(){
		x_gastos+=parseFloat($(this).val());
	});
	x_total = x_gastos + parseFloat($(".total_cuota").val());
	$(".total_gastos,#gastos").val(parseFloat(x_gastos).toFixed(2));
	$('.total_total').val(parseFloat(x_total).toFixed(2));
});

$(document).on("keyup",".gastos",function(e){
	var x_gastos = 0;
	var fila = $(this).closest("tr");
	$('.gastos').each(function(){
		x_gastos+=parseFloat($(this).val());
	});
	x_total = x_gastos + parseFloat($(".total_cuota").val());
	
	x_subto = parseFloat($(this).val()) + parseFloat(fila.find(".monto_letra").val()) - parseFloat(fila.find(".descuento").val());
	fila.find(".monto_capital").val(parseFloat(x_subto).toFixed(2));
	$(".total_gastos,#gastos").val(parseFloat(x_gastos).toFixed(2));
	$('.total_total').val(parseFloat(x_total).toFixed(2));
});

$(document).on("keyup",".monto_letra",function(e){
	var x_mtotal = 0;
	var fila = $(this).closest("tr");
	$('.monto_letra').each(function(){
		x_mtotal+=parseFloat($(this).val());
	});
	x_total = x_mtotal + parseFloat($(".total_cuota").val());
	
	x_subto = parseFloat($(this).val()) + parseFloat(fila.find(".gastos").val()) - parseFloat(fila.find(".descuento").val());
	fila.find(".monto_capital").val(parseFloat(x_subto).toFixed(2));
	$(".total_cuota").val(parseFloat(x_mtotal).toFixed(2));
	$('.total_total').val(parseFloat(x_total).toFixed(2));
});

$(document).on("click",".btn_deta_delete",function(e){
	e.preventDefault();
	tr_		= $(this).parent("td").parent("tr");
	tr_.fadeOut( "slow", function() {
		tr_.remove();
		calculo_totales();
	});
});

$("#btn_generarletras").click(function(e){
	e.preventDefault();
	s = true && $("#monto").required();
	s = s && $("#gastos").required();
	s = s && $("#descuento").required();
	// s = s && $("#idforma_pago_compra").required();
	s = s && $("#nro_dias").required();
	s = s && $("#id_compras").required();
	if(s){
		
		// if($("#table-cronograma tbody tr[forma_pago_compra='"+$("#idforma_pago_compra").val()+"']").length){
		if($("#table-cronograma tbody tr[forma_pago_compra='"+$("#nro_dias").val()+"']").length){
			// ventana.alert({titulo: "", mensaje: "La forma de pago "+$("#idforma_pago_compra option:selected").text()+", ya fue agregado al cronograma", tipo: "warning"});
			ventana.alert({titulo: "", mensaje: "La forma de pago F/"+$("#nro_dias").val()+" DIAS, ya fue agregado al cronograma", tipo: "warning"});
			return;
		}
		
		// if($("#table-cronograma tbody tr").length){
			// ventana.alert({titulo: "", mensaje: "Debe generar las letras de la deuda", tipo: "warning"});
			// return;
		// }
		nro_letra	= $("#table-cronograma tbody tr").length + 1;
		// dias		= $("#idforma_pago_compra option:selected").attr("data-nrodias");
		dias		= $("#nro_dias").val();
			dias	= Math.round(dias);
		monto		= $("#monto").val()/nro_letra;
		gastos		= $("#monto").val()/nro_letra;
		fechaini	= $("#fecha_deuda").val();
		total		= monto+gastos;
		
		var arrFecha = String(fechaini).split('/');
		var fecha = new Date(parseInt(arrFecha[2]), (parseInt(arrFecha[1]) - 1), parseInt(arrFecha[0]));
		fecha_v		= addDate(fecha,dias);
		
		// html = "<tr index='' forma_pago_compra='"+$("#idforma_pago_compra").val()+"'>";
		// html+= "	<td>"+"<input type='text' class='form-control input-sm nro_letra' name='nro_letra[]' value='"+nro_letra+"' readonly=''>"+"</td>";
		// html+= "	<td>"+"<input type='text' class='form-control input-sm forma_pago_compra' data-dias='"+dias+"' name='forma_pago_compra[]' value='"+$("#idforma_pago_compra option:selected").text()+"'</td>";
		// html+= "	<td>"+"<input type='text' class='form-control input-sm fecha_vencimiento' name='fecha_vencimiento[]' value='"+dateFormat(fecha_v, 'd/m/Y')+"' readonly=''>"+"</td>";
		// html+= "	<td>"+"<input type='text' class='form-control input-sm monto_letra numerillo' name='monto_letra[]'  placeholder='0.00'>"+"</td>";
		// html+= "	<td>"+"<input type='text' class='form-control input-sm gastos numerillo' name='gasto[]'  placeholder='0.00'>"+"</td>";
		// html+= "	<td>"+"<input type='text' class='form-control input-sm monto_capital numerillo' name='monto_capital[]'  placeholder='0.00'>"+"</td>";
		// html+= "	<td>"+"<button class='btn btn-danger btn-xs btn_deta_delete' data-toggle='tooltip' title='Eliminar registro'><i class='fa fa-trash'></i></button>"+"</td>";
		// html+= "	<td style='display:none;'>"+"<input type='text' class='idforma_pago_compra' name='idforma_pago_compra[]'  value='"+$("#idforma_pago_compra").val()+"'>"+"</td>";
		// html+= "</tr>";
		
		html = "<tr index='' forma_pago_compra='"+$("#nro_dias").val()+"'>";
		html+= "	<td>"+"<input type='text' class='form-control input-xs nro_letra' name='nro_letra[]' value='"+nro_letra+"' readonly=''>"+"</td>";
		html+= "	<td>"+"<input type='text' class='form-control input-xs forma_pago_compra' data-dias='"+dias+"' readonly='' value='F/"+$("#nro_dias").val()+" DIAS' ></td>";
		html+= "	<td>"+"<input type='text' class='form-control input-xs fecha_vencimiento' name='fecha_vencimiento[]' value='"+dateFormat(fecha_v, 'd/m/Y')+"' >"+"</td>";
		html+= "	<td>"+"<input type='text' class='form-control input-xs monto_letra numerillo' name='monto_letra[]' readonly='' placeholder='0.00'>"+"</td>";
		html+= "	<td>"+"<input type='text' class='form-control input-xs gastos numerillo' name='gasto[]'  placeholder='0.00'>"+"</td>";
		html+= "	<td>"+"<input type='text' class='form-control input-xs descuento numerillo' name='descuento_letra[]'  readonly=''  placeholder='0.00'>"+"</td>";
		html+= "	<td>"+"<input type='text' class='form-control input-xs monto_capital numerillo' name='monto_capital[]' readonly=''  placeholder='0.00'>"+"</td>";
		html+= "	<td>"+"<button class='btn btn-danger btn-xs btn_deta_delete' data-toggle='tooltip' title='Eliminar registro'><i class='fa fa-trash'></i></button>"+"</td>";
		html+= "	<td style='display:none;'>";
		html+= "		<input type='text' class='idforma_pago_compra' name='idforma_pago_compra[]'  value=''>";
		html+= "		<input type='text' class='nro_dias_formapago' name='nro_dias_formapago[]'  value='"+$("#nro_dias").val()+"'>";
		html+= "		<input type='text' class='id_referencia' name='id_referencia[]'  value=''>";
		html+= "	</td>";
		html+= "</tr>";
		
		$("#cant_letras").val(nro_letra);
		$("#table-cronograma tbody").append(html);
		$(".monto_letra,.gastos,.total").numero_real();
		$(".fecha_vencimiento").datepicker({
			todayBtn: "linked",
			keyboardNavigation: false,
			forceParse: false,
			autoclose: true,
			language: 'es'
		}).on("changeDate",function(e){
			e.preventDefault();
			calcular_dias($(this));
		});
		
		calculo_totales();
		$("#nro_dias").val('').focus();
	}
});

$("#btn_save_credito").click(function(e){
	e.preventDefault();
	var t = true && $("#deuda_proveedor").required();
		t = t && $("#fecha_deuda").required();
		t = t && $("#idmoneda").required();
		t = t && $("#gastos").required();
		t = t && $("#monto").required();
		t = t && $("#cant_letras").required();
		if(t){
			var ss = $("#form_"+_controller).serialize();
			ajax.post({url: _base_url+"deuda/guardar", data:ss}, function(data) {
				ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
					redirect(_controller);
				});
			});			
		}
});

$(".btn_cancel_credito").click(function(e){
	e.preventDefault();
	redirect(_controller);
});

if(!_es_nuevo_credito_){
	cargar_compras();
}

function cargar_compras(){
	if($.trim($("#idproveedor").val())!='' && $.trim($("#idmoneda").val())!='' ){
		var str = "idproveedor="+$("#idproveedor").val();
			str+= "&idmoneda="+$("#idmoneda").val();
			str+= "&iddeuda="+$("#iddeuda").val();
			str+= "&es_nuevo="+_es_nuevo_credito_;
		ajax.post({url: _base_url+"deuda/get_compra_credito", data:str}, function(data) {
			html = "";
			$(data).each(function(x,y){
				html+="<option value='"+y['idcompra']+"' data-monto='"+y['monto']+"' data-usado='"+y['usado']+"' ";
				if(y['usado']=='S')
					html+="selected";
				html+=" >"+y['comprobante'];
			});
			$("#id_compras").html(html);
		});
	}
}

function calcular_dias(input){
	var f_input = input.val();
	var f_credt = $("#fecha_deuda").val();
	
	var dias = resta_Fechas(f_credt, f_input);
	input.parent("td").parent("tr").find(".forma_pago_compra").attr('value','F/'+dias+' DIAS');
	input.parent("td").parent("tr").find(".nro_dias_formapago").attr('value',dias);
}

function calculo_totales(){
	var monto_temporal	= parseFloat($("#monto").val());
	var gasto_temporal	= parseFloat($("#gastos").val());
	var dscto_temporal	= parseFloat($("#descuento").val());
	var nro_letras		= $("#table-cronograma tbody tr").length;
	
	if(isNaN(gasto_temporal))
		gasto_temporal = 0;
	
	if(isNaN(dscto_temporal))
		dscto_temporal = 0;
	
	monto_letra			= monto_temporal/nro_letras;
	monto_gasto			= gasto_temporal/nro_letras;
	monto_dscto			= dscto_temporal/nro_letras;
	monto_cap			= monto_letra + monto_gasto - monto_dscto;
	xfecha_credito		= $("#fecha_deuda").val();
	console.log();
	$('.monto_letra').val(monto_letra.toFixed(2));
	$('.gastos').val(monto_gasto.toFixed(2));
	$('.descuento').val(monto_dscto.toFixed(2));
	$('.monto_capital').val(monto_cap.toFixed(2));
	console.log(dscto_temporal);
	console.log(nro_letras);
	console.log(monto_dscto);
	monto_acumulado = total_gastoss = total_cuotas = total_descuento = 0;
	
	$(".forma_pago_compra").each(function(){
		diasx	= $(this).attr("data-dias");
			diasx = Math.round(diasx);
		tr_		= $(this).parent("td").parent("tr");
		
		var arrFecha = String(xfecha_credito).split('/');
		var fechax = new Date(parseInt(arrFecha[2]), (parseInt(arrFecha[1]) - 1), parseInt(arrFecha[0]));
		var n_fecha_v		= addDate(fechax,diasx);
		
		tr_.find('.fecha_vencimiento').attr({'value':dateFormat(n_fecha_v,'d/m/Y')});
	});
	
	$('.monto_letra').each(function(){
		total_cuotas+= parseFloat($(this).val());
	});
	$('.total_cuota').val(total_cuotas.toFixed(2));
	
	$('.gastos').each(function(){
		total_gastoss+= parseFloat($(this).val());
	});
	$('.total_gastos').val(total_gastoss.toFixed(2));

	$('.descuento').each(function(){
		total_descuento+= parseFloat($(this).val());
	});
	$('.total_descuento').val(total_descuento.toFixed(2));

	$('.monto_capital').each(function(){
		monto_acumulado+= parseFloat($(this).val());
	});
	$('.total_total').val(monto_acumulado.toFixed(2));
	$("#cant_letras").val(nro_letras);
}