$(document).on('click',"#table-clientes tbody tr",function(){
	$("#table-clientes tbody tr").removeClass('file_selected');
	
	$(this).addClass("file_selected");
	idcliente = $(this).attr("index");
	get_creditos(idcliente);
});

$(document).on('click',"#table-creditos tbody tr",function(){
	$("#table-creditos tbody tr").removeClass('file_selected');
	
	$(this).addClass("file_selected");
	// idcredito = $(this).attr("index");
});

$("#ver").click(function(e){
	idcredito	= $("#table-creditos tbody tr.file_selected").attr("index");
	idventa		= $("#table-creditos tbody tr.file_selected").attr("ivta");
	
	if($.trim(idcredito)!='' && $.trim(idventa)){
		get_amortizaciones(idcredito);
		get_venta(idventa);
		$('.nav-tabs a[href="#tab-1"]').tab('show');
		$("#modal-detalles").modal("show");		
	}
});

$(document).on("click",".filter",function(){
	var credito = $(this).attr("valor");
	var filter	= $(this).attr("text");
	
	$("#ver_credito").val(credito);
	$("#status").html(filter);
	get_creditos($("#table-clientes tbody tr.file_selected").attr("index"));
});

$("#search_idcliente,#search_cliente,#search_limite,#search_dedua,#search_disponible,#search_telefono,#search_zona,#search_direccion").keyup(function(e) {
	if(e.keyCode == 13) {
		e.preventDefault();
		get_clientes();
		return false;
	}
});

$("#exportar").click(function(e){
	e.preventDefault();
	var id_cliente = $("#table-clientes tbody tr.file_selected").attr("index");
	var str = "idcliente="+id_cliente;
		str+= "&pagado="+$("#ver_credito").val();
	if($.trim(id_cliente))
		open_url_windows(_controller+"/exportar?"+str);
});

function init() {
	get_clientes();
}

function get_clientes(){
	var s = {
		idcliente: $.trim($("#search_idcliente").val())
		,cliente: $.trim($("#search_cliente").val())
		,limite_credito: $.trim($("#search_limite").val())
		,deuda: $.trim($("#search_dedua").val())
		,disponible: $.trim($("#search_disponible").val())
		,telefono: $.trim($("#search_telefono").val())
		,zona: $.trim($("#search_zona").val())
		,direccion: $.trim($("#search_direccion").val())
	};
		ajax.post({url: _base_url+"historial_credito/get_clientes", data: s, dataType: 'json'}, function(res) {
			html = "";
			$("#cant_clientes").html('0 CLIENTES');
			$(res).each(function(x,y){
				html+="<tr index='"+y['cod']+"'>";
				html+="		<td>"+y['cod']+"</td>";
				html+="		<td>"+y['cliente']+"</td>";
				html+="		<td class='text-number'>"+number_format(y['limite_credito'],2,'.',',')+"</td>";
				html+="		<td class='text-number'>"+number_format(y['deuda'],2,'.',',')+"</td>";
				html+="		<td class='text-number'>"+number_format(y['disponible'],2,'.',',')+"</td>";
				html+="		<td>"+y['telefono']+"</td>";
				html+="		<td>"+y['zona']+"</td>";
				html+="		<td>"+y['direccion']+"</td>";
				html+="</tr>";
			});
			$("#table-clientes tbody").html(html);
			$("#cant_clientes").html($("#table-clientes tbody tr").length+" CLIENTES");
		});
		$("#table-creditos tbody").empty();
		calcular_totales();
}

function get_creditos(id){
	var str = "idcliente="+id;
		str+= "&pagado="+$("#ver_credito").val();
	ajax.post({url: _base_url+"historial_credito/get_creditos", data: str, dataType: 'json'}, function(res) {
		html = "";
		$(res).each(function(x,y){
			fecha_pago = y['fecha_pago']?y['fecha_pago']:'';
			
			html+="<tr index='"+y['idcredito']+"' ivta='"+y['idventa']+"'>";
			html+="		<td>"+y['comprobante']+"</td>";
			html+="		<td>"+y['nombre_vendedor']+"</td>";
			html+="		<td class='text-center'>"+y['fecha_emision']+"</td>";
			html+="		<td class='text-center'>"+y['fecha_vencimiento']+"</td>";
			html+="		<td class='text-center'>"+fecha_pago+"</td>";
			html+="		<td class='text-center'>"+y['moneda']+"</td>";
			html+="		<td class='text-number'>"+y['dias']+"</td>";
			html+="		<td class='text-number importe_t'>"+y['importe']+"</td>";
			html+="		<td class='text-number totalnc_t'>"+y['total_nc']+"</td>";
			html+="		<td class='text-number moras'>"+y['mora']+"</td>";
			html+="		<td class='text-number total_pag'>"+y['total_pagos']+"</td>";
			html+="		<td class='text-number saldo'>"+y['saldo']+"</td>";
			html+="</tr>";
		});
		$("#table-creditos tbody").html(html);
		calcular_totales();
	});
}

function get_venta(idventa){
	ajax.post({url: _base_url+"venta/get_all/"+idventa, dataType: 'json'}, function(data) {
		$("#venta_comprobante").val(data.venta.comprobante);
		$("#venta_direccion").val(data.venta.direccion);
		$("#venta_cliente").val(data.venta.full_nombres);
		$("#venta_fecha_venta").val(fecha_es(data.venta.fecha_venta));
		
		$("#table-productos tbody tr").remove();
		if(data.detalle_venta.length) {
			var html = '';
			for(var i in data.detalle_venta) {
				html += '<tr><td><small>'+data.detalle_venta[i].descripcion+'</small></td>'+
					'<td class="text-navy"><small style="white-space:nowrap;">'+
					data.detalle_venta[i].cantidad+' '+data.detalle_venta[i].abreviatura+
					'</small></td></tr>';
			}
			$("#table-productos tbody").html(html);
		}
	});
}

function get_amortizaciones(idcredito) {
	ajax.post({url: _base_url+"cuentas_cobrar/get_amortizaciones/"+idcredito, dataType: 'json'}, function(res) {
		$("#table-amortizaciones tbody tr").remove();
		if(res.array.length) {
			var last = res.last_recibo, table = new Table(), data;
			
			for(var i in res.array) {
				data = res.array[i];
				
				rec = '<span class="text-navy">'+data.recibo+'</span>';
				
				table.tr();
				table.td(data.fecha);
				// table.td(data.hora);
				table.td(data.idletra);
				table.td(data.monto, {class:'text-navy'});
				table.td(data.mora, {class:'text-navy'});
				table.td("<small>"+data.moneda_corto+"</small>");
				table.td("<small>"+data.tipo_pago+"</small>");
				table.td(rec);
				table.td("<small>"+data.usuario+"</small>");
				// table.td("<small>"+data.sucursal+"</small>");
			}
			
			$("#table-amortizaciones tbody").html(table.to_string());
		}
	});
}

function calcular_totales(){
	total_importe = total_nc = total_pagos = total_saldo = cant_creditos = 0;
	$(".importe_t").each(function(){
		cant_creditos++;
		total_importe = total_importe + parseFloat($(this).html());
	});
	
	$(".totalnc_t").each(function(){
		total_nc = total_nc + parseFloat($(this).html());
	});
	
	$(".total_pag").each(function(){
		total_pagos = total_pagos + parseFloat($(this).html());
	});
	
	$(".saldo").each(function(){
		total_saldo = total_saldo + parseFloat($(this).html());
	});
	
	$(".total_creditos").html(cant_creditos+' CREDITOS');
	$(".tot_importe").html(number_format(total_importe,2,'.',','));
	$(".tot_notacre").html(number_format(total_nc,2,'.',','));
	$(".tot_saldo").html(number_format(total_pagos,2,'.',','));
	$(".tot_saldo").html(number_format(total_saldo,2,'.',','));
}

init();