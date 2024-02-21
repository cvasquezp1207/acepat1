if(typeof form == 'undefined') {
	form = {};
}
var form = {
	imprimir: function() {
		var datos = $("#form_"+_controller).serialize();
		// alert("ss");
		
		// datos =  $("#idproveedor").val()+"_"+$("#idproducto").val()+"_"+$("#anio").val();
		// datos += "_"+$("#periodo").val()+"_"+$("#idalmacen_i").val()+"_"+$("#fecha").val()+"_"+$("#idalmacen_f").val()+"_"+$(".opc_tipo").val();
		
		// alert(datos);
		open_url_windows(_controller+"/reporte_kardex?"+datos);
		  // $.post(_controller+"/reporte_kardex/",{id:"vvv"});

		
		// ajax.post({url: _controller+"/reporte_kardex", data:datos, function(data) {

		// });
		
	}
};

$("#proveedor_descripcion").focus();

$("#form_kardex").submit(function() {
	return false;
});

$("#proveedor_descripcion" ).autocomplete({
	source: function( request, response ) {
		ajax.post({url: _base_url+"proveedor/autocomplete", data: "maxRows=50&startsWith="+request.term, dataType: 'json'}, function(data) {
			response( $.map( data, function( item ) {
				return {
					label: item.nombre
				   ,value: item.nombre
				   ,descripcion: item.nombre
				   ,ruc: item.ruc
				   ,id: item.idproveedor
				}
			}));
		});
	},
	minLength: 2,
	select: function( event, ui ) {
		if(ui.item) {
			$("#idproveedor").val(ui.item.id);
		}
	}
});


$("#producto_descripcion").autocomplete({
	source: function( request, response ) {
		ajax.post({url: _base_url+"producto/autocomplete", data: "maxRows=50&startsWith="+request.term, dataType: 'json'}, function(data) {
			response( $.map( data, function( item ) {
				return {
					label: item.descripcion_detallada
				   ,value: item.descripcion_detallada
				   ,descripcion: item.descripcion_detallada
				   ,id: item.idproducto
				   ,codigo_producto: item.codigo_producto
				}
			}));
		});
	},
	select: function( event, ui ) {
		if(ui.item) {
			$("#idproducto").val(ui.item.id);
		}
	}
}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
	return $( "<li>" )
	.data( "ui-autocomplete-item", item )
	.append( "<strong>"+item.codigo_producto+"</strong>| "+item.descripcion )
	.appendTo( ul );
};



$('#fecha_i').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	endDate: parseDate(_current_date)
});

$('#fecha_f').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	endDate: parseDate(_current_date)
});


$("#btn_generar_report").click(function() {
	// alert("ff");
	form.imprimir();
	// SELECT mlosto040.sto_item, mplite001.ite_dsit, mplite005.ite_abum, mlosto040.sto_alma, mplite012.ite_dsal, mplsto001.sto_tipo, mplsto001.sto_dsti, mlosto040.sto_feem, mlosto040.sto_hore, mlosto040.sto_obse, mlosto040.sto_orde, mlosto040.sto_ordo, mlosto040.sto_codo, mlosto040.sto_seri, mlosto040.sto_nume, mlosto040.sto_cant, mlosto040.sto_cous, mlosto040.sto_imps FROM mlosto040, mplite001, mplite005, mplite012, mplsto001 WHERE mlosto040.sto_item = mplite001.ite_item AND mlosto040.sto_coum = mplite005.ite_coum AND mlosto040.sto_alma = mplite012.ite_alma AND mlosto040.sto_timo = mplsto001.sto_timo AND mlosto040.sto_lote LIKE '%%' AND mlosto040.sto_stat in (90,99) AND mlosto040.sto_year = '2016' AND mlosto040.sto_peri between '06' and '06' AND mlosto040.sto_timo LIKE '%%' AND mlosto040.sto_alma between '101' AND '999' AND mlosto040.sto_item LIKE '%%' ORDER BY mlosto040.sto_item, mlosto040.sto_alma, mlosto040.sto_feem, mlosto040.sto_hore
	
});

$("#all_proveedor").on("change", function() {
	var b = $(this).is(":checked");
	if(b) {
		$("#idproveedor,#proveedor_descripcion").val("");
	}
	$("#proveedor_descripcion").prop("readonly", b);
});

$("#all_producto").on("change", function() {
	var b = $(this).is(":checked");
	if(b) {
		$("#idproducto,#producto_descripcion").val("");
	}
	$("#producto_descripcion").prop("readonly", b);
});