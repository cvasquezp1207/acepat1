$("#filtrar").click(function(e){
	e.preventDefault();
	
	get_vendedor();
});

$("#venta_fecha_inicio,#venta_fecha_fin").datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es'
});

$(document).on('click',"#table-vendedor tbody tr",function(){
	$("#table-vendedor tbody tr").removeClass('file_selected');
	
	$(this).addClass("file_selected");
	idvendedor = $(this).attr("index");
	get_creditos(idvendedor);
});

$(document).on("keyup",".filter",function(e){
	if(e.keyCode == 13) {
		e.preventDefault();
		get_creditos($("#table-vendedor tbody tr.file_selected").attr("index"));
		return false;
	}
});

$("#exportar_head").click(function(e){
	e.preventDefault();
	var has = $("#table-vendedor tbody tr").length;
	var str = $("#form-filtro").serialize();
	if(has>0)
		open_url_windows(_controller+"/exportar_head?"+str);
});

$("#exportar_ventas").click(function(e){
	e.preventDefault();
	var has = $("#table-creditos tbody tr").length;
	var str = $("#form-filtro").serialize();
		str+= "&idvendedor="+$("#table-vendedor tbody tr.file_selected").attr("index");
		str+= "&comprobante="+$.trim($(".search_comprobante").val());
		str+= "&cliente="+$.trim($(".search_cliente").val());
		str+= "&ruc="+$.trim($(".search_ruc").val());
		str+= "&dni="+$.trim($(".search_dni").val());
	if(has>0)
		open_url_windows(_controller+"/exportar_ventas?"+str);
});

function init() {
	$("#filtrar").trigger("click");
}

function get_vendedor(){
	var s = $("#form-filtro").serialize();
		// s+= "&comprobante="+$(".search_comprobante").val();
		// s+= "&cliente="+$(".search_cliente").val();
		// s+= "&ruc="+$(".search_ruc").val();
		// s+= "&dni="+$(".search_dni").val();
		ajax.post({url: _base_url+_controller+"/get_vendedor", data: s, dataType: 'json'}, function(res) {
			html = "";
			$("#cant_vendedor").html('0 VENDEDORES');
			$(res).each(function(x,y){
				html+="<tr index='"+y['cod']+"'>";
				html+="		<td>"+y['cod']+"</td>";
				html+="		<td>"+y['vendedor']+"</td>";
				html+="		<td class='text-number cobranza_t'>"+number_format(y['cuota_cobranza'],2,'.','')+"</td>";
				html+="		<td class='text-number cobrado_t'>"+number_format(y['monto_cobrado'],2,'.','')+"</td>";
				html+="		<td class='text-number'>"+number_format(y['avance'],2,'.','')+"%</td>";
				html+="</tr>";
			});
			$("#table-vendedor tbody").html(html);
			
			$("#table-vendedor tbody tr:first").addClass('file_selected');
			get_creditos($("#table-vendedor tbody tr.file_selected").attr("index"));
			
			$("#cant_vendedor").html($("#table-vendedor tbody tr").length+" VENDEDORES");
			resumen_totales();
		});
		$("#table-creditos tbody").empty();
}

function get_creditos(id){
	
	var str = $("#form-filtro").serialize();
		str+= "&idvendedor="+id;
		str+= "&comprobante="+$.trim($(".search_comprobante").val());
		str+= "&cliente="+$.trim($(".search_cliente").val());
		str+= "&ruc="+$.trim($(".search_ruc").val());
		str+= "&dni="+$.trim($(".search_dni").val());
	if($.trim(id)){
		ajax.post({url: _base_url+_controller+"/get_creditos", data: str, dataType: 'json'}, function(res) {
			html = "";
			$(res).each(function(x,y){
				u_pago = y['ultimo_pago']?y['ultimo_pago']:'';
				
				html+="<tr index='"+y['idventa']+"' >";
				html+="		<td class='text-center'>"+y['fecha_venta']+"</td>";
				html+="		<td class='text-center'>"+u_pago+"</td>";
				html+="		<td>"+y['comprobante']+"</td>";
				html+="		<td>"+y['cliente']+"</td>";
				html+="		<td>"+y['ruc']+"</td>";
				html+="		<td>"+y['dni']+"</td>";
				html+="		<td class='text-center'>"+y['moneda']+"</td>";
				html+="		<td class='text-number importe_t'>"+number_format(y['importe_venta'],2,'.','')+"</td>";
				html+="		<td class='text-number cobros_t'>"+number_format(y['importe_cobrado'],2,'.','')+"</td>";
				html+="		<td class='text-number saldo_t'>"+number_format(y['saldo'],2,'.','')+"</td>";
				html+="</tr>";
			});
			$("#table-creditos tbody").html(html);
			calcular_totales();
		});		
	}
}

function resumen_totales(){
	total_cobranza = total_monto_c = 0 ;
	$(".cobranza_t").each(function(){
		total_cobranza = total_cobranza + parseFloat($(this).html());
	});
	$(".cobrado_t").each(function(){
		total_monto_c = total_monto_c + parseFloat($(this).html());
	});
	
	$(".total_cobranza").html(number_format(total_cobranza,2,'.',','));
	$(".total_cobrado").html(number_format(total_monto_c,2,'.',','));
}

function calcular_totales(){
	total_importe_doc = total_monto_cobrado = tot_saldo = cant_creditos = 0 ;
	
	$(".importe_t").each(function(){
		cant_creditos++;
		total_importe_doc = total_importe_doc + parseFloat($(this).html());
	});
	
	$(".cobros_t").each(function(){
		total_monto_cobrado = total_monto_cobrado + parseFloat($(this).html());
	});
	
	$(".saldo_t").each(function(){
		// console.log($(this).html());
		tot_saldo = tot_saldo + parseFloat($(this).html());
	});
	
	$(".tot_impt_doc").html(number_format(total_importe_doc,2,'.',','));
	$(".tot_m_cobrado").html(number_format(total_monto_cobrado,2,'.',','));
	// $(".tot_saldo").html(number_format(tot_saldo,2,'.',','));
	$(".total_creditos").html(cant_creditos+" CREDITOS");
}

init();