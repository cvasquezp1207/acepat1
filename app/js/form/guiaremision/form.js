if(typeof form == 'undefined') {
	form = {};
}

form.guardar_transporte = function() {
	var data = $("#form_transporte").serialize();
	model.save(data, function(res) {
		$("#modal-transporte").modal("hide");
		$("#transporte").val(res.nombre);
		$("#ruc_transporte").val(res.ruc);
	}, "transporte");
}

form.guardar_chofer = function() {
	var data = $("#form_chofer").serialize();
	model.save(data, function(res) {
		$("#modal-chofer").modal("hide");
		$("#chofer").val(res.nombre);
		$("#lic_conducir").val(res.licencia);
		$("#marca_nroplaca").val(res.placa);
		$("#const_inscripcion").val(res.inscripcion);
	}, "chofer");
}
	
function LoadSerieDoc(idtipodocumento, tpl) {
	tpl = tpl || "#serie";
	if($(tpl).length <= 0 || $(tpl).prop("tagName") != "SELECT")
		return;
	
	if($.isNumeric(idtipodocumento)) {
		reload_combo(tpl, 
		{
			controller: "tipo_documento",
			method: "get_series", 
			data: "idtipodocumento="+idtipodocumento
		}, function() {
			$(tpl).trigger("change");
		});
	}
	else {
		$(tpl).html("").trigger("change");
	}
}

function LoadNumeroDoc(idtipodocumento, serie, tpl) {
	tpl = tpl || "#numero";
	if($(tpl).length <= 0 || $(tpl).prop("tagName") != "INPUT")
		return;
	
	if($.isNumeric(idtipodocumento) && $.trim(serie) != "") {
		ajax.post({
			url: _base_url+"tipo_documento/get_correlativo", 
			data: "idtipodocumento="+idtipodocumento+"&serie="+serie
		}, function(res) {
			$(tpl).val(res.correlativo);
		});
	}
	else {
		$(tpl).val("");
	}
}

function disabledBuscarItem() {
	$(".row_buscar_detalle .libre_item").addClass("hidden");
	$(".row_buscar_detalle .libre_item :input").prop("disabled", true);
	$(".row_buscar_detalle button").prop("disabled", true).addClass("hidden");
}

function llenarDetalle() {
	var real_datos = null;
	
	if($.isArray($data_detalle))
		real_datos = $data_detalle;
	
	if($.isArray(real_datos) && real_datos.length) {
		$("#tbl-detalle tbody tr").remove();
		var tr = null;
		for(var i in real_datos) {
			tr = addDetalle(real_datos[i]);
			updateUnidades(tr, real_datos[i]);
		}
	}
}

function init() {
	if($tipo_guia == "S" && $nuevo) {
		$("#serie").trigger("change");
	}
	$("#idmotivo_guia").trigger("change");
	
	if($nuevo == false) {
		llenarDetalle();
	}
}

if($tipo_guia == "S") {
	$(document).on("change", "#serie", function() {
		LoadNumeroDoc($idtipodocumento, $(this).val());
	});
}

$(".btn_ubigeo").click(function(e) {
	e.preventDefault();
	var s = $(this).data("dir");
	
	ubigeo.ok(function(data) {
		$("#idubigeo_"+s).val( data.idubigeo );
		var k = ["distrito", "provincia", "departamento"];
		for(var i in k) {
			if( data[k[i]] )
				$("#"+k[i]+"_"+s).val( data[k[i]] );
		}
	});
	ubigeo.show();
	return false;
});

$("#btn-cliente").click(function(e) {
	e.preventDefault();
	
	jFrame.create({
		title: "Buscar Destinatario - Cliente"
		,controller: "cliente"
		,method: "grilla_popup"
		,msg: ""
		,onSelect: function(datos) {
			$("#destinatario").val(datos.cliente);
			$("#ruc_destinatario").val(datos.ruc);
			$("#dni_destinatario").val(datos.dni);
			cargarDatosCliente(datos.idcliente);
		}
	});
	
	jFrame.show();
	return false;
});

$("#btn-proveedor").click(function(e) {
	e.preventDefault();
	
	jFrame.create({
		title: "Buscar Destinatario - Proveedor"
		,msg: ""
		,controller: "proveedor"
		,method: "grilla_popup"
		,onSelect: function(datos) {
			$("#destinatario").val(datos.nombre);
			$("#ruc_destinatario").val(datos.ruc);
			$("#dni_destinatario").val("");
			if($("#tipo_guia").val() == "S")
				$("#punto_llegada").val(datos.direccion);
			else
				$("#punto_partida").val(datos.direccion);
		}
	});
	
	jFrame.show();
	return false;
});

$("#btn-b-transporte").click(function(e) {
	e.preventDefault();
	
	jFrame.create({
		title: "Buscar Empresa de Transporte"
		,msg: ""
		,controller: "transporte"
		,method: "grilla"
		,data: "popup=S"
		,onSelect: function(datos) {
			$("#idtransporte").val(datos.idtransporte);
			$("#transporte").val(datos.nombre);
			$("#ruc_transporte").val(datos.ruc);
		}
	});
	
	jFrame.show();
	return false;
});

$("#btn-n-transporte").click(function(e) {
	e.preventDefault();
	$("#modal-transporte").modal("show");
	return false;
});



$("#btn-b-chofer").click(function(e) {
	e.preventDefault();
	
	jFrame.create({
		title: "Buscar Conductor"
		,msg: ""
		,controller: "chofer"
		,method: "grilla"
		,data: "popup=S"
		,widthclass: 'modal-lg'
		,onSelect: function(datos) {
			$("#chofer").val(datos.nombre);
			$("#lic_conducir").val(datos.licencia);
			$("#marca_nroplaca").val(datos.placa);
			$("#const_inscripcion").val(datos.inscripcion);
		}
	});
	
	jFrame.show();
	return false;
});

$("#btn-n-chofer").click(function(e) {
	e.preventDefault();
	$("#modal-chofer").modal("show");
	return false;
});


$('#fecha_traslado').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	endDate: parseDate(_current_date)
});

$("#costo_minimo").numero_real();

$('#buscar_serie').iCheck({
	checkboxClass: 'icheckbox_square-green',
	radioClass: 'iradio_square-green',
}).on('ifChanged', function(e){
	if($(this).is(":checked")) {
		$("#producto_descripcion").attr("placeholder", "Ingrese o escanee la serie").focus();
	}
	else {
		$("#producto_descripcion").attr("placeholder", "Ingrese el nombre o codigo del producto").focus();
	}
});

$("#idmotivo_guia").change(function() {
	disabledBuscarItem();
	var op = $("option:selected", this);
	var t = $("#tipo_guia").val();
	if(t == "I") {
		if( op.data("ingreso_buscar_guia") == "S" )
			$("#btn-buscar-guia").prop("disabled", false).removeClass("hidden");
		if( op.data("ingreso_libre_item") == "S" ) {
			$(".row_buscar_detalle .libre_item :input").prop("disabled", false);
			$(".row_buscar_detalle .libre_item").removeClass("hidden");
		}
		if( op.data("ingreso_b_otra_sede") == "S" ) {
			$(".row_buscar_detalle .libre_item_almacen :input").prop("disabled", false);
			$(".row_buscar_detalle .libre_item_almacen").removeClass("hidden");
		}
	}
	else if(t == "S") {
		if( op.data("salida_buscar_venta") == "S" )
			$("#btn-buscar-venta").prop("disabled", false).removeClass("hidden");
		if( op.data("salida_buscar_compra") == "S" )
			$("#btn-buscar-compra").prop("disabled", false).removeClass("hidden");
		if( op.data("salida_libre_item") == "S" ) {
			$(".row_buscar_detalle .libre_item :input").prop("disabled", false);
			$(".row_buscar_detalle .libre_item").removeClass("hidden");
		}
	}
	$("#tbl-detalle tbody tr").remove();
});

$("#tbl-detalle").on("change", "select.deta_idunidad", function() {
	var self = $(this);
	var tr = $(this).closest("tr");
	tr.data("idunidad", self.val());
});

function limpiarBusqueda() {
	$("#producto_idproducto,#producto_descripcion,#producto_has_serie,#producto_idunidad,#producto_idalmacen,#producto_serie").val("");
}

function updateUnidades(tr, params, callback) {
	if(params && typeof params.idproducto == "undefined")
		return;
	
	ajax.post({url: _base_url+"producto/get_unidades/"+params.idproducto}, function(res) {
		var data, options='';
		
		if($.isArray(res)) {
			for(var i in res) {
				data = res[i];
				options += '<option value="'+data.idunidad+'" title="'+data.descripcion+'" count="'+data.cantidad_unidad_min+'">'+data.abreviatura+'</option>';
			}
		}
		
		if(tr) {
			v = (params.idunidad) ? parseInt(params.idunidad) : parseInt($(".deta_idunidad", tr).val());
			$(".deta_idunidad", tr).html(options);
			if(!isNaN(v)) {
				// $(".deta_idunidad", tr).val(v).trigger("change");
				$(".deta_idunidad", tr).val(v);
			}
		}
		else {
			$("#tbl-detalle tbody tr[index="+params.idproducto+"] select.deta_idunidad").each(function() {
				v = (params.idunidad) ? parseInt(params.idunidad) : parseInt($(this).val());
				
				$(this).html(options);
				if(!isNaN(v)) {
					// $(this).val(v).trigger("change");
					$(this).val(v);
				}
			});
		}
		
		if($.isFunction(callback)) {
			callback(tr, params);
		}
	});
}

function agregarSerie(tr, ops) {
	var def = {cantidad: 0, serie: false};
	var data = $.extend({}, def, ops);
	
	var cantidad = 0, series = [], temp;
	
	// obtenemos los nuevos datos
	temp = parseFloat($(".deta_cantidad", tr).val());
	if( ! isNaN(temp))
		cantidad += temp;
	
	if( $.trim($(".deta_series", tr).val()) != "" )
		series = String($(".deta_series", tr).val()).split("|");
	
	if(data.serie !== false && series.indexOf(data.serie) == -1) {
		cantidad += data.cantidad;
		series.push(data.serie);
	}
	
	// actualizamos los datos
	$(".deta_cantidad", tr).val(cantidad);
	$(".deta_series", tr).val(series.join("|"));
}

function addDetalle(data) {
	if(typeof data.cantidad == "undefined") {
		data.cantidad = "";
	}
	if(typeof data.peso == "undefined") {
		data.peso = "";
	}
	if(typeof data.iddetalle_ref == "undefined") {
		data.iddetalle_ref = "";
	}
	if(typeof data.serie == "undefined" || data.serie == null) {
		data.serie = "";
	}
	
	cls = (data.controla_serie == 'S') ? "has_serie" : "";
	
	var table = new Table();
	table.tr({index: data.idproducto, class: cls, data:{idalmacen:data.idalmacen, idunidad:data.idunidad}});
	
	table.td('<input type="text" name="deta_producto[]" class="form-control input-xs deta_producto" value="'+data.descripcion_detallada+'">');
	table.td('<select name="deta_idunidad[]" class="form-control input-xs deta_idunidad" data-toggle="tooltip" title=""></select>');
	table.td('<input type="text" name="deta_cantidad[]" class="form-control input-xs deta_cantidad" value="'+data.cantidad+'">');
	table.td('<input type="text" name="deta_peso[]" class="form-control input-xs deta_peso" value="'+data.peso+'">');
	
	if(cls == 'has_serie') {
		table.td('<input type="hidden" name="deta_series[]" class="deta_series" value="'+data.serie+'">'+
			'<button class="btn btn-success btn-xs btn_deta_serie" data-toggle="tooltip" '+
			'title="Ingresar las series del producto"><i class="fa fa-cubes"></i></button>', {class:"text-center"});
	}
	else {
		table.td('<input type="hidden" name="deta_series[]" class="deta_series" value="'+data.serie+'">');
	}
	
	table.td('<button class="btn btn-danger btn-xs btn_deta_delete" data-toggle="tooltip" '+
		'title="Eliminar registro"><i class="fa fa-trash"></i></button>', {class:"text-center"});
	
	table.td('<input type="hidden" name="deta_idproducto[]" class="deta_idproducto" value="'+data.idproducto+'">'+
		'<input type="hidden" name="deta_idalmacen[]" class="deta_idalmacen" value="'+data.idalmacen+'">'+
		'<input type="hidden" name="deta_controla_stock[]" class="deta_controla_stock" value="'+data.controla_stock+'">'+
		'<input type="hidden" name="deta_iddetalle[]" class="deta_iddetalle" value="">'+
		'<input type="hidden" name="deta_iddetalle_ref[]" class="deta_iddetalle_ref" value="'+data.iddetalle_ref+'">'+
		'<input type="hidden" name="deta_controla_serie[]" class="deta_controla_serie" value="'+data.controla_serie+'">', {style:"display:none"});
	
	$("#tbl-detalle tbody").append(table.to_string());
	$("#tbl-detalle tbody tr:last input.deta_cantidad").numero_real();
	
	return $("#tbl-detalle tbody tr:last");
}

function agregarProducto(idproducto, idunidad, idalmacen, has_serie, serie, callback) {
	ajax.post({url: _base_url+"producto/get/"+idproducto}, function(data) {
		if(idunidad) { // el usuario ha indicado una unidad de medida
			data.idunidad = idunidad;
		}
		if(idalmacen) { // el almacen del usuario
			data.idalmacen = idalmacen;
		}
		if(has_serie) { // se ha hecho una busqueda por serie
			data.cantidad = 1;
			data.serie = serie;
			
			var tr = null;
			
			// buscamos si existe algun registro en la tabla
			if($("#tbl-detalle tbody tr[index="+data.idproducto+"][data-idunidad="+data.idunidad+"][data-idalmacen="+data.idalmacen+"]").length) {
				tr = $("#tbl-detalle tbody tr[index="+data.idproducto+"][data-idunidad="+data.idunidad+"][data-idalmacen="+data.idalmacen+"]");
			}
			else if($("#tbl-detalle tbody tr[index="+data.idproducto+"][data-idalmacen="+data.idalmacen+"]").length) {
				tr = $("#tbl-detalle tbody tr[index="+data.idproducto+"][data-idalmacen="+data.idalmacen+"]");
			}
			
			if(tr != null) {
				agregarSerie(tr, data); // actualizamos la cantidad y serie
			}
			else { // creamos nueva fila
				tr = addDetalle(data);
				updateUnidades(tr, data);
			}
			if($.isFunction(callback)) {
				callback();
			}
			return;
		}
		
		if($("#tbl-detalle tbody tr[index="+data.idproducto+"]").length) {
			ventana.confirm({
				titulo:"Confirmar"
				,mensaje:"El producto "+data.descripcion_detallada+" ya se encuentra en la tabla. ¿Desea volver a agregar otra vez?"
				,textoBotonAceptar: "Agregar"
			}, function(ok) {
				if(ok) {
					var tr = addDetalle(data);
					updateUnidades(tr, data);
				}
				if($.isFunction(callback)) {
					callback();
				}
			});
		}
		else {
			var tr = addDetalle(data);
			updateUnidades(tr, data);
			if($.isFunction(callback)) {
				callback();
			}
		}
	});
}

function verificarProducto() {
	if( ! $("#producto_idproducto").required()) {
		return false;
	}
	
	if( $("#producto_descripcion").required() ) {
		agregarProducto(
			$("#producto_idproducto").val()
			,$("#producto_idunidad").val()
			,$("#producto_idalmacen").val()
			,($("#producto_has_serie").val() == "1")
			,$("#producto_serie").val()
			,function() {
				limpiarBusqueda();
				$("#producto_descripcion").focus();
			}
		);
	}
}

$("#tbl-detalle").on("click", "button.btn_deta_delete", function() {
	$(this).tooltip('destroy');
	$(this).closest("tr").remove();
});

/* modal de las series */
var arrListaSeries = [];

$("#tbl-detalle").on("click", "button.btn_deta_serie", function(e) {
	e.preventDefault();
	var tr = $(this).closest("tr");
	
	if($.trim($(".deta_series", tr).val()) != "") {
		// obtenemos las series ingresadas
		var arrSeries = String($(".deta_series", tr).val()).split("|");
		add_series(arrSeries);
	}
	
	// obtenemos la lista completa de todas las series
	arrListaSeries = [];
	$("#tbl-detalle tbody tr[index="+tr.attr("index")+"]").each(function() {
		if($.trim($(".deta_series", this).val()) != "") {
			// obtenemos las series ingresadas
			temp = String($(".deta_series", this).val()).split("|");
			arrListaSeries = arrListaSeries.concat(temp);
		}
	});
	
	tr.addClass("current");
	$("#modal-series .modal-title").text($(".deta_producto", tr).val());
	$("#modal-series").modal("show");
	return false;
});

function add_series(arr) {
	if($.isArray(arr) == false) {
		var temp = [];
		temp.push(arr);
		arr = temp;
	}
	if(arr.length) {
		var c = $("#table-serie tbody tr").length, html = '';
		for(var i in arr) {
			c++;
			html += '<tr index="'+arr[i]+'">';
			html += '<td>'+c+'</td>';
			html += '<td>'+arr[i]+'</td>';
			html += '<td><button class="btn btn-xs btn-danger btn_remove_serie" title="Eliminar fila"><i class="fa fa-trash"></i></button></td>';
			html += '</tr>';
		}
		$("#table-serie tbody").append(html);
		$('div.div_scroll').scrollTop($('div.div_scroll')[0].scrollHeight);
	}
}

$("#btn-close-serie").click(function(e) {
	e.preventDefault();
	var tr = $("#tbl-detalle tbody tr.current");
	var txt = "", cant = 0;
	
	if($("#table-serie tbody tr").length) {
		var arr = [];
		$("#table-serie tbody tr").each(function() {
			arr.push($(this).attr("index"));
		});
		cant = arr.length;
		txt = arr.join("|");
	}
	
	$(".deta_series", tr).val(txt);
	if(cant > 0) {
		cant = cant / parseFloat($(".deta_idunidad option:selected", tr).attr("count"));
		$(".deta_cantidad", tr).val(Math.round(cant));
	}
	
	$("#input-text-serie").val("");
	$("#table-serie tbody tr").remove();
	tr.removeClass("current");
	
	$("#modal-series").modal("hide");
	return false;
});

input.autocomplete({
	selector: "#input-text-serie"
	,controller: "guiaremision"
	,method: "serie_autocomplete"
	,label: "[serie]"
	,value: "[serie]"
	,highlight: true
	,appendTo: $("#input-text-serie").closest("div")
	,data: function() {
		var tr = $("#tbl-detalle tbody tr.current");
		return {
			idalmacen: $(".deta_idalmacen", tr).val()
			,idproducto: $(".deta_idproducto", tr).val()
			,referencia: $("#referencia").val()
			,idreferencia: $("#idreferencia").val()
			,idreferencia_det: $(".deta_iddetalle_ref", tr).val()
		};
	}
	,onSelect: function(item) {
		setTimeout(function() {
			checkSerie();
		}, 100);
	}
});

$("#input-text-serie").keypress(function(e) {
	var t = e.keyCode ? e.keyCode : e.which;
	if(t == 13) { // cuando se usa el lector
		e.preventDefault();
		checkSerie();
	}
});

function checkSerie() {
	if($.trim($("#input-text-serie").val()) != "") {
		var temp = String($("#input-text-serie").val()).replace(/\W/g, '').toUpperCase();
		if(arrListaSeries.indexOf(temp) != -1) {
			ventana.alert({titulo: '', mensaje: 'La serie <b>'+temp+'</b> ya se ha agregado'}, function() {
				$("#input-text-serie").focus().select();
			});
			return;
		}
		add_series(temp);
		arrListaSeries.push(temp);
		
		setTimeout(function() {
			$("#input-text-serie").val("").focus();
		}, 200);
	}
}

$("#table-serie").on("click", "button.btn_remove_serie", function(e) {
	e.preventDefault();
	var serie = $(this).closest("tr").attr("index");
	var index = arrListaSeries.indexOf(serie);
	
	// eliminamos la serie
	arrListaSeries.splice(index, 1);
	$(this).closest("tr").remove();
	
	// reordenamos las series
	if($("#table-serie tbody tr").length) {
		var c = 0;
		$("#table-serie tbody tr").each(function() {
			$("td:eq(0)", this).text(++c);
		});
	}
	
	return false;
});

$("#btn-search-serie").click(function(e) {
	e.preventDefault();
	var tr = $("#tbl-detalle tbody tr.current");
	
	jFrame.create({
		title: "Buscar series"
		,msg: ""
		,controller: "guiaremision"
		,method: "grilla_serie"
		,data: {
			idalmacen: $(".deta_idalmacen", tr).val()
			,idproducto: $(".deta_idproducto", tr).val()
			,referencia: $("#referencia").val()
			,idreferencia: $("#idreferencia").val()
			,idreferencia_det: $(".deta_iddetalle_ref", tr).val()
		}
		,autoclose: false
		,onSelect: function(datos) {
			$("#input-text-serie").val(datos.serie);
			checkSerie();
		}
	});
	
	jFrame.show();
	return false;
});

/* fin modal de las series */

/* buscar productos libre autocomplete */
function buscarProducto(txt, idalmacen) {
	if($.trim(txt) == "") {
		return;
	}
	
	ajax.post({url: _base_url+"producto/search_serie/", data:{query:txt, idalmacen:idalmacen}}, function(res) {
		if(res.length <= 0) {
			ventana.alert({titulo: "", mensaje: "No se han encontrado resultados de la b&uacute;squeda."});
			return;
		}
		if(res.length == 1) {
			$("#producto_idproducto").val(res[0].idproducto);
			$("#producto_has_serie").val(res[0].with_serie);
			$("#producto_idunidad").val(res[0].idunidad);
			$("#producto_idalmacen").val(res[0].idalmacen);
			$("#producto_serie").val(res[0].codigo_producto);
			verificarProducto();
			return;
		}
		
		$("#modal-product-list .result-list a.list-group-item").remove();
		$("#modal-product-list .count-result-list").text(res.length);
		
		var a = null;
		for(var i in res) {
			a = $('<a href="#" class="list-group-item"></a>');
			a.html("<strong>"+res[i].codigo_producto+"</strong> | "+res[i].descripcion_detallada);
			a.data("datos", res[i]);
			$("#modal-product-list .result-list").append(a);
		}
		
		$("#modal-product-list").modal("show");
	});
}

$("#modal-product-list").on("click", "a.list-group-item", function(e) {
	e.preventDefault();
	var item = $(this).data("datos");
	
	$("#producto_idproducto").val(item.idproducto);
	$("#producto_has_serie").val(item.with_serie);
	$("#producto_idunidad").val(item.idunidad);
	$("#producto_idalmacen").val(item.idalmacen);
	$("#producto_serie").val(item.codigo_producto);
	verificarProducto();
	
	$("#modal-product-list").modal("hide");
	return false;
});

input.autocomplete({
	selector: "#producto_descripcion"
	,controller: "producto"
	,method: "autocomplete"
	,label: "<strong>[codigo_producto]</strong>| [descripcion_detallada]"
	,value: "[descripcion_detallada]"
	,highlight: true
	,data: function() {
		return {
			idalmacen: $("#idalmacen").val()
			,with_serie: ( $('#buscar_serie').is(":checked") ? "1" : "0" )
		};
	}
	,onSelect: function(item) {
		$("#producto_idproducto").val(item.idproducto);
		$("#producto_has_serie").val(item.with_serie);
		$("#producto_idunidad").val(item.idunidad);
		$("#producto_idalmacen").val(item.idalmacen);
		$("#producto_serie").val(item.codigo_producto);
		verificarProducto();
	}
});

$("#producto_descripcion").keypress(function(e) {
	var t = e.keyCode ? e.keyCode : e.which;
	if(t == 13) {
		if( $('#buscar_serie').is(":checked") ) { // la busqueda es por serie
			// verificamos si ha ha seleccionado el autocomplete, 
			// aunque si este fuera el caso no deberia estar aqui
			if( $("#producto_idproducto").required() && $("#producto_has_serie").required() ) {
				verificarProducto();
				return false;
			}
			// aqui podria llegar cuando se ha escaneado el codigo de barras con el lector
			buscarProducto( $("#producto_descripcion").val(), $("#idalmacen").val() );
			return false;
		}
		verificarProducto();
		return false;
	}
});

/* fin buscar productos libre autocomplete */

/* evento boton venta */
function cargarDetalleReferencia(controller, idreferencia, idalmacen) {
	ajax.post({url: _base_url+controller+"/get_detalle/"+idreferencia, dataType: 'json'}, function(res) {
		$("#tbl-detalle tbody tr").remove();
		
		if(res.length) {
			var tr = null;
			for(var i in res) {
				res[i].descripcion_detallada = res[i].producto;
				if(typeof idalmacen != "undefined")
					res[i].idalmacen = Number(idalmacen);
				
				if(controller == 'venta')
					res[i].iddetalle_ref = res[i].iddetalle_venta;
				else if(controller == 'compra')
					res[i].iddetalle_ref = res[i].iddetalle_compra;
				else if(controller == 'guiaremision')
					res[i].iddetalle_ref = res[i].iddetalle_guia_remision;
				
				tr = addDetalle(res[i]);
				updateUnidades(tr, res[i]);
			}
		}
	});
}

$("#btn-buscar-venta").click(function(e) {
	e.preventDefault();
	jFrame.create({
		title: "Buscar venta"
		,controller: "venta"
		,method: "grilla_popup"
		,msg: ""
		,widthclass: "modal-lg"
		,data: "g=S"
		,onSelect: function(datos) {
			$("#referencia").val("V");
			$("#idreferencia").val(datos.idventa);
			if( $.trim($("#destinatario").val()) == "")
				$("#destinatario").val(datos.full_nombres);
			if( $.trim($("#ruc_destinatario").val()) == "" && datos.ruc )
				$("#ruc_destinatario").val(datos.ruc);
			if( $.trim($("#dni_destinatario").val()) == "" && datos.dni )
				$("#dni_destinatario").val(datos.dni);
			cargarDetalleReferencia('venta', datos.idventa);
			cargarDatosCliente(datos.idcliente);
		}
	});
	
	jFrame.show();
});
/* fin evento boton venta */

/* evento boton compra */
$("#btn-buscar-compra").click(function(e) {
	e.preventDefault();
	jFrame.create({
		title: "Buscar compra"
		,controller: "compra"
		,method: "grilla_popup"
		,msg: ""
		,widthclass: "modal-lg"
		,onSelect: function(datos) {
			$("#referencia").val("C");
			$("#idreferencia").val(datos.idcompra);
			if( $.trim($("#destinatario").val()) == "")
				$("#destinatario").val(datos.proveedor);
			if( $.trim($("#ruc_destinatario").val()) == "" && datos.ruc )
				$("#ruc_destinatario").val(datos.ruc);
			
			cargarDetalleReferencia('compra', datos.idcompra);
		}
	});
	
	jFrame.show();
});
/* fin evento boton compra */

/* evento boton guia */
$("#btn-buscar-guia").click(function(e) {
	e.preventDefault();
	jFrame.create({
		title: "Buscar guia de remision"
		,controller: "guiaremision"
		,method: "grilla_popup"
		,msg: ""
		,widthclass: "modal-lg"
		,data: "idmotivo_guia="+$("#idmotivo_guia").val()+
			"&tipo_guia="+$("#tipo_guia").val()
		,onSelect: function(datos) {
			if($.trim(datos)){
				$("#referencia").val("G");
				$("#idreferencia").val(datos.idguia_remision);
				if( $.trim($("#destinatario").val()) == "")
					$("#destinatario").val(datos.destinatario);
				if( $.trim($("#ruc_destinatario").val()) == "" && datos.ruc_destinatario )
					$("#ruc_destinatario").val(datos.ruc_destinatario);
				if( $.trim($("#dni_destinatario").val()) == "" && datos.dni_destinatario )
					$("#dni_destinatario").val(datos.dni_destinatario);
				if( $.trim($("#serie").val()) == "" && datos.serie )
					$("#serie").val(datos.serie);
				if( $.trim($("#numero").val()) == "" && datos.numero )
					$("#numero").val(datos.numero);
				if( $.trim($("#punto_partida").val()) == "" && datos.punto_partida )
					$("#punto_partida").val(datos.punto_partida);
				
				if($("#idmotivo_guia>option:selected").data("ingreso_b_otra_sede") == "S") {
					var a = String(datos.nroguia).split("-");
					if( $.trim($("#serie").val()) == "" )
						$("#serie").val(a[0]);
					if( $.trim($("#numero").val()) == "" )
						$("#numero").val(a[1]);
					cargarDetalleReferencia('guiaremision', datos.idguia_remision, 0);
					$(".row_buscar_detalle .libre_item_almacen :input").prop("disabled", false);
					$(".row_buscar_detalle .libre_item_almacen").removeClass("hidden");
				}
				else
					cargarDetalleReferencia('guiaremision', datos.idguia_remision);
			}
		}
	});
	
	jFrame.show();
});
/* fin evento boton compra */

$("#btn_save_guiaremision").click(function(e) {
	e.preventDefault();
	var v = true;
	v = v && $("#serie").required();
	v = v && $("#numero").required();
	v = v && $("#idmotivo_guia").required();
	v = v && $("#fecha_traslado").required();
	v = v && $("#destinatario").required();
	v = v && $("#punto_partida").required();
	v = v && $("#punto_llegada").required();
	if(v) {
		var table = $("#tbl-detalle");
		
		if($("tbody tr", table).length < 1) {
			ventana.alert({titulo: "Error", mensaje: "Agregue algun item a la tabla"});
			return;
		}
		
		if($("#tipo_guia").val() == "I") {
			if($("tbody tr[data-idalmacen='0']", table).length) {
				v = v && $("#idalmacen").required();
			}
		}
		
		v = v && $(".deta_idunidad", table).required();
		v = v && $(".deta_cantidad", table).required({numero:true, tipo:"float"});
		if(v) {
			var a, c, msg = '';
			
			$( "tbody tr", table ).each(function() {
				c = parseFloat($(".deta_cantidad", this).val());
				
				if($(this).hasClass("has_serie")) {
					if( $(".deta_series", this).val() != '' ) {
						a = String($(".deta_series", this).val()).split('|');
						c = c * parseFloat($(".deta_idunidad option:selected", this).attr("count"));
						if(a.length != c) {
							v = false;
							msg = "Ingrese las series del producto "+$(".deta_producto", this).val();
						}
					}
					else {
						v = false;
						msg = "Ingrese las series del producto "+$(".deta_producto", this).val();
					}
				}
				return v;
			});
			
			if(v == false && msg != '') {
				ventana.alert({titulo: '', mensaje: msg});
				return;
			}
			
			if(v)
				form.guardar();
		}
	}
});

function cargarDatosCliente(id) {
	var sel = "#punto_llegada";
	
	if($("#tipo_guia").val() != "S")
		sel = "#punto_partida";
	
	ajax.post({url: _base_url+"cliente/get_direcciones/"+id}, function(res) {
		if(res.length <= 0)
			return;
		
		if(res.length == 1) {
			$(sel).val(res[0].direccion);
			return;
		}
		
		$("#modal-cliente-direccion .result-list a.list-group-item").remove();
		
		var a = null;
		for(var i in res) {
			a = $('<a href="#" class="list-group-item" target="'+sel+'"></a>');
			a.html(res[i].direccion);
			a.data("datos", res[i]);
			$("#modal-cliente-direccion .result-list").append(a);
		}
		
		setTimeout(function() {
			$("#modal-cliente-direccion").modal("show");
		}, 300);
	});
}

$("#modal-cliente-direccion").on("click", "a.list-group-item", function(e) {
	e.preventDefault();
	var item = $(this).data("datos");
	$($(this).attr("target")).val(item.direccion);
	$("#modal-cliente-direccion").modal("hide");
	return false;
});

init();