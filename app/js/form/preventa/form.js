var add_new_precio = true;
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
			var s = getDefaultValue("serie");
			if(s && $(tpl+">option[value='"+s+"']").length)
				$(tpl).val(s);
		});
	}
}

function addDetalle(data) {
	if(typeof data.cantidad == "undefined") {
		data.cantidad = "";
	}
	if(typeof data.serie == "undefined" || data.serie == null) {
		data.serie = "";
	}
	if(typeof data.oferta == "undefined") {
		data.oferta = 'N';
	}
	if(typeof data.codgrupo_igv == "undefined") {
		data.codgrupo_igv = default_grupo_igv;
	}
	if(typeof data.codtipo_igv == "undefined") {
		data.codtipo_igv = false;
	}
	if(typeof data.precio_compra == "undefined") {
		data.precio_compra = 0;
	}
	if(typeof data.precio_venta == "undefined") {
		data.precio_venta = 0;
	}
	
	cls = (data.controla_serie == 'S') ? "has_serie" : "";
	c = $("#tbl-detalle tbody tr").length + 1;
	ckb = (data.oferta == 'S') ? 'checked' : '';
	
	table = new Table();
	table.tr({index: data.idproducto, class: cls, data:{idalmacen:data.idalmacen, idunidad:data.idunidad}});
	
	table.td('<span class="badge">'+c+'</span>', {class: "item"});
	table.td(data.descripcion_detallada, {class: "text-sm"});
	table.td('<select name="deta_idunidad[]" class="form-control input-xs deta_idunidad" data-toggle="tooltip" title=""></select>');
	if(mostrar_precio_costo === true) {
		table.td('<input type="text" name="deta_costo[]" class="form-control input-xs text-success deta_costo" readonly>');
	}
	table.td('<input type="text" name="deta_stock[]" class="form-control input-xs text-success deta_stock" readonly>');
	table.td('<input type="text" name="deta_cantidad[]" class="form-control input-xs deta_cantidad" value="'+data.cantidad+'">');
	table.td('<select name="deta_precio[]" class="form-control input-xs deta_precio"></select>', {class:'td_has_block'});
	// table.td('<input type="text" name="deta_precio[]" class="form-control input-xs deta_precio">');
	table.td('<input type="text" name="deta_importe[]" class="form-control input-xs font-bold deta_importe" readonly>');
	
	table.td('<input type="checkbox" class="deta_ckb_oferta" value="1" data-toggle="tooltip" title="Activar para oferta" '+ckb+'>');
	
	if(cls == 'has_serie') {
		table.td('<input type="hidden" name="deta_series[]" class="deta_series" value="'+data.serie+'">'+
			'<button class="btn btn-success btn-xs btn_deta_serie" data-toggle="tooltip" title="Ingresar las series del producto">'+
			'<i class="fa fa-cubes"></i></button>');
	}
	else {
		table.td('<input type="hidden" name="deta_series[]" class="deta_series" value="'+data.serie+'">');
	}
	
	table.td('<select name="deta_grupo_igv[]" class="form-control input-xs deta_grupo_igv">'+$("#grupo_igv_temp").html()+'</select>');
	table.td('<select name="deta_tipo_igv[]" class="form-control input-xs deta_tipo_igv">'+$("#tipo_igv_temp").html()+'</select>');
	
	table.td('<button class="btn btn-danger btn-xs btn_deta_delete" data-toggle="tooltip" title="Eliminar registro"><i class="fa fa-trash"></i></button>');
	
	table.td('<input type="hidden" name="deta_idproducto[]" class="deta_idproducto" value="'+data.idproducto+'">'+
		'<input type="hidden" name="deta_idalmacen[]" class="deta_idalmacen" value="'+data.idalmacen+'">'+
		'<input type="hidden" name="deta_controla_stock[]" class="deta_controla_stock" value="'+data.controla_stock+'">'+
		'<input type="hidden" name="deta_oferta[]" class="deta_oferta" value="'+data.oferta+'">'+
		'<input type="hidden" name="deta_pc_unit[]" class="deta_pc_unit" value="'+data.precio_compra+'">'+
		'<input type="hidden" name="deta_pv_unit[]" class="deta_pv_unit" value="'+data.precio_venta+'">'+
		'<input type="hidden" name="deta_controla_serie[]" class="deta_controla_serie" value="'+data.controla_serie+'">', {style:"display:none"});
	
	$("#tbl-detalle tbody").append(table.to_string());
	$("#tbl-detalle tbody tr:last input.deta_cantidad").numero_real();
	
	tr = $("#tbl-detalle tbody tr:last");
	$(".deta_grupo_igv", tr).val(data.codgrupo_igv);
	setTipoIgv(tr, data.codtipo_igv);
	return tr;
}

// function updateUnidades(tr, idproducto, idunidad, callback) {
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
			var v = (params.idunidad) ? parseInt(params.idunidad) : parseInt($(".deta_idunidad", tr).val());
			$(".deta_idunidad", tr).html(options);
			if(!isNaN(v)) {
				// $(".deta_idunidad", tr).val(v).trigger("change");
				$(".deta_idunidad", tr).val(v);
			}
			
			calcularPrecioCosto(tr);
		}
		
		if($.isFunction(callback)) {
			callback(tr, params);
		}
	});
}

function calcularStock(idproducto, idalmacen, empty) {
	if(typeof empty != "boolean")
		empty = false;
	
	ajax.post({url: _base_url+"producto/get_stock/"+idproducto+"/"+idalmacen}, function(stock) {
		if(empty === true)
			idalmacen = 0;
		
		$("#tbl-detalle tbody tr[index="+idproducto+"][data-idalmacen="+idalmacen+"]").each(function() {
			cantidad = parseFloat($(".deta_idunidad option:selected", this).attr("count"));
			stock_real = stock;
			if(cantidad > 0)
				stock_real = stock_real / cantidad;
			if(isNaN(stock_real))
				stock_real = 0;
			$(".deta_stock", this).val(stock_real.toFixed(2));
		});
	});
}

function getPrecio(tr, data, callback) {
	var _defaults = {
		idproducto: 0
		,idunidad: 1
		,idmoneda: 1
		,cantidad: 1
		,precio: false
	};
	var params = $.extend({}, _defaults, data);
	
	ajax.post({url: _base_url+"producto/get_real_precio_venta", data: params}, function(arr) {
		if(arr.length <= 0 && params.precio === false) {
			// ventana.alert({titulo:"Precio no definido", mensaje: "No se ha podido obtener los precios de venta del producto. "+
				// "Por favor ingrese los precios en el modulo de Producto"});
			if(add_new_precio)
				$(".deta_precio", tr).html('<option value="0.00">0.00</option><option value="M">Agregar otro precio</option>');
			// $(".deta_precio", tr).html('<option value="0.00">0.00</option>');
			return;
		}
		var html = '', p, pt = 0;
		if(params.precio !== false) {
			pt = parseFloat(params.precio);
			// html += '<option value="'+pt.toFixed(_fixed_venta)+'" selected>'+pt.toFixed(_fixed_venta);+'</option>';
			if(arr.indexOf(pt) == -1) 
				arr.push(pt.toFixed(_fixed_venta));
		}

		for(var i in arr) {
			p = parseFloat(arr[i]);
			html += '<option value="'+p.toFixed(_fixed_venta)+'" ';
			if(p == pt)
				html += 'selected';
			html += ">"+p.toFixed(_fixed_venta)+'</option>';
		}
		if(add_new_precio)
			html += '<option value="M">Agregar otro precio</option>'
		$(".deta_precio", tr).html(html);
		
		if($.isFunction(callback)) {
			callback(tr);
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
				calcularDatos(tr);
			}
			else { // creamos nueva fila
				tr = addDetalle(data);
				updateUnidades(tr, data, function(tr, data) {
					getPrecio(tr, {
						idproducto: data.idproducto
						,idunidad: data.idunidad
						,idmoneda: $("#idmoneda").val()
						,cantidad: data.cantidad
					}, function(tr) {
						calcularDatos(tr);
					});
					calcularStock(data.idproducto, data.idalmacen);
				});
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
					updateUnidades(tr, data, function(tr, data) {
						getPrecio(tr, {
							idproducto: data.idproducto
							,idunidad: data.idunidad
							,idmoneda: $("#idmoneda").val()
						}, function(tr) {
							calcularDatos(tr);
						});
						calcularStock(data.idproducto, data.idalmacen);
					});
				}
				if($.isFunction(callback)) {
					callback();
				}
			});
		}
		else {
			var tr = addDetalle(data);
			updateUnidades(tr, data, function(tr, data) {
				getPrecio(tr, {
					idproducto: data.idproducto
					,idunidad: data.idunidad
					,idmoneda: $("#idmoneda").val()
				}, function(tr) {
					calcularDatos(tr);
				});
				calcularStock(data.idproducto, data.idalmacen);
			});
			if($.isFunction(callback)) {
				callback();
			}
		}
	});
}

function calcularDatos(tr) {
	calcularImporte(tr);
	calcularSubtotal();
	calcularIgv();
	calcularTotal();
}

function calcularTotal() {
	if( $.isNumeric($("#subtotal").val()) ) {
		var total = parseFloat($("#subtotal").val());
		if($.isNumeric($("#igv").val())) {
			total += parseFloat($("#igv").val());
		}
		if($.isNumeric($("#descuento").val())) {
			total -= parseFloat($("#descuento").val());
		}
		total = redondeosunat(total);
		$("#total").val(total.toFixed(2));
		return;
	}
	$("#total").val("");
}

function calcularIgv() {
	if( $("#tbl-detalle tbody tr").length ) {
		var impuesto = 0, importe, igv;
        $("#tbl-detalle tbody tr").each(function() {
			importe = parseFloat($("input.deta_importe", this).val());
			if(isNaN(importe))
				importe = 0;
			
			igv = parseFloat( $(".deta_grupo_igv>option:selected", this).data("igv") );
            if(isNaN(igv))
                igv = 0;
			
            impuesto += importe * igv;
        });
		$("#igv").val(impuesto.toFixed(2));
		return;
    }
	$("#igv").val("");
}

function calcularSubtotal() {
	if( $("#tbl-detalle tbody tr").length ) {
		var t = 0;
		$("#tbl-detalle tbody tr").each(function() {
			if($.isNumeric($("input.deta_importe", this).val())) {
				t += parseFloat($("input.deta_importe", this).val());
			}
		});
		$("#subtotal").val(t.toFixed(2));
		return;
	}
	$("#subtotal").val("");
}


function calcularImporte(tr) {
	if($(".deta_ckb_oferta", tr).is(":checked")) {
		$("input.deta_importe", tr).val("0.00");
		return;
	}
	if($.isNumeric($("input.deta_cantidad", tr).val()) && $.isNumeric($(".deta_precio", tr).val())) {
		var importe = parseFloat($("input.deta_cantidad", tr).val()) * parseFloat($(".deta_precio", tr).val());
		var impsunat = redondeosunat(importe);
		$("input.deta_importe", tr).val(impsunat.toFixed(_fixed_venta));
		return;
	}
	$("input.deta_importe", tr).val("");
}

function limpiarBusqueda() {
	$("#producto_idproducto,#producto_descripcion,#producto_has_serie,#producto_idunidad,#producto_idalmacen,#producto_serie").val("");
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
				focus_prod = true;
				$('.deta_cantidad').each(function(x,y){
					if( $(y).val() == '' || $(y).val()>0 ){
						$(y).required();
						$(y).focus();
						focus_prod = false;
					}
				});
				
				if(focus_prod)
					$("#producto_descripcion").focus();
			}
		);
	}
}

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

$("#cliente_razonsocial").autocomplete({
	source: function( request, response ) {
		ajax.post({url: _base_url+"cliente/autocomplete", data: "maxRows=50&startsWith="+request.term, dataType: 'json'}, function(data) {
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
			$("#compra_idcliente").val(ui.item.id);
			// get_saldo($("#compra_idcliente").val());
			is_activo();
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

input.autocomplete({
	selector: "#producto_descripcion"
	,controller: "producto"
	,method: "autocomplete"
	,label: "<strong>[codigo_barras]</strong>| [descripcion_detallada]&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|<FONT COLOR=#0000FF><strong>Stock</strong> : [stock] [abreviatura]</FONT>"
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
		// $("#producto_precio_compra").val(item.precio_compra);
		// $("#producto_precio_venta").val(item.precio_venta);
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

$("#btn-buscar-cliente").click(function() {
	jFrame.create({
		title: "Buscar Cliente"
		,controller: "cliente"
		,method: "grilla_popup"
		,msg: ""
		,widthclass: "modal-lg"
		,onSelect: function(datos) {
			$("#cliente_razonsocial").val(datos.cliente);
			$("#compra_idcliente").val(datos.idcliente);
			// get_saldo($("#compra_idcliente").val());
			is_activo();
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

$("#tbl-detalle").on("change", "select.deta_idunidad", function() {
	var self = $(this);
	var tr = $(this).closest("tr");
	tr.data("idunidad", self.val());
	
	var idalmacen = $.trim($("input.deta_idalmacen", tr).val());
	var bool = false;
	if(idalmacen == "") {
		idalmacen = $("#idalmacen").val();
		bool = true;
	}
	
	calcularPrecioCosto(tr);
	
	if(tr.hasClass("tr_oferta")) {
		var opt = $("option:selected", self);
		self.attr("title", opt.attr("title"));
		self.attr("data-original-title", opt.attr("title"));
		calcularStock($("input.deta_idproducto", tr).val(), idalmacen, bool);
	}
	else {
		getPrecio(tr, {
			idproducto: $(".deta_idproducto", tr).val()
			,idunidad: self.val()
			,idmoneda: $("#idmoneda").val()
			,cantidad: $(".deta_cantidad", tr).val()
		}, function(tr) {
			var opt = $("option:selected", self);
			self.attr("title", opt.attr("title"));
			self.attr("data-original-title", opt.attr("title"));
			calcularStock($("input.deta_idproducto", tr).val(), idalmacen, bool);
		});
	}
});

$("#idalmacen").on("change", function() {
	if($("#tbl-detalle tbody tr[data-idalmacen='0']").length) {
		$("#tbl-detalle tbody tr[data-idalmacen='0']").each(function() {
			calcularStock($("input.deta_idproducto", this).val(), $("#idalmacen").val(), true);
		});
	}
});

$("#tbl-detalle").on("keyup", "input.deta_cantidad", function() {
	var tr = $(this).closest("tr");
	calcularDatos(tr);
});

$("#tbl-detalle").on("blur", "input.deta_cantidad", function() {
	var tr = $(this).closest("tr");
	
	if(tr.hasClass("tr_oferta")) {
		calcularDatos(tr);
	}
	else {
		return;
		var p = $(".deta_precio", tr).val();
		getPrecio(tr, {
			idproducto: $(".deta_idproducto", tr).val()
			,idunidad: $(".deta_idunidad", tr).val()
			,idmoneda: $("#idmoneda").val()
			,cantidad: $(".deta_cantidad", tr).val()
		}, function(tr) {
			if($(".deta_precio>option[value='"+p+"']", tr).length) {
				$(".deta_precio>option[value='"+p+"']", tr).prop("selected", true);
			}
			calcularDatos(tr);
		});
	}
});

// $("#tbl-detalle").on("keyup", "input.deta_precio", function() {
	// calcularDatos($(this).closest("tr"));
// });

$("#tbl-detalle").on("change", ".deta_ckb_oferta", function() {
	var tr = $(this).closest("tr");
	set_precio_oferta(tr, $(this).is(":checked"));
	setTipoIgv(tr);
	calcularDatos(tr);
});

$("#tbl-detalle").on("change", ".deta_grupo_igv", function() {
	setTipoIgv($(this).closest("tr"));
	calcularDatos($(this).closest("tr"));
});

$("#ptemp").numero_real();

$("#ptemp").keypress(function(e) {
	if(e.keyCode == 13) {
		addPrecio($(this));
		// if($(this).required({numero:true,tipo:"float",aceptaCero:true})) {
			// var tr = $("#tbl-detalle tbody tr.current-precio");
			// var precio = parseFloat($(this).val());
			// $("select.deta_precio option:last", tr).remove();
			
			// $("select.deta_precio", tr).append('<option value="'+precio.toFixed(_fixed_venta)+'">'+precio.toFixed(_fixed_venta)+'</option>');
			// $("select.deta_precio option:last", tr).prop("selected", true);
			
			// $("select.deta_precio", tr).append('<option value="M">Agregar otro precio</option>');
			// calcularDatos(tr);
			
			// $("#modal-precio-tempp").modal("hide");
		// }
	}
});

$("#tbl-detalle").on("change", "select.deta_precio", function() {
	if($(this).val() == "M") {
		$("#tbl-detalle tbody tr").removeClass("current-precio");
		$(this).closest("tr").addClass("current-precio");
		$("#modal-precio-tempp").modal("show");
		setTimeout(function(e){
			$("#ptemp").focus();
		},800);
		return;
	}
	
	calcularDatos($(this).closest("tr"));
});

// $("#valor_igv").on("change", function() {
	// calcularIgv();
	// calcularTotal();
// });

$("#descuento").on("keyup", function() {
	calcularTotal();
});


$("#idmodalidad").on("change", function(){
	if($(this).val()==1){
        $(".idrampa").fadeIn(2000);
        $(".idrampa").show().css('display','block');
        $(".idmecanico").fadeIn(2000);
        $(".idmecanico").show().css('display','block');
    }else{
        $("#idrampa").val('0');
        $(".idrampa").fadeOut(2000);
        $(".idrampa").show().css('display','none');
        $("#idmecanico").val('0');
        $(".idmecanico").fadeOut(2000);
        $(".idmecanico").show().css('display','none');
    }
});


/*$("#idrampa").on("change", function(){
        

        if($(this).val()>0){
            $(".idmecanico").fadeIn(2000);
            $(".idmecanico").show().css('display','block');
           


        }else{
           $("#idmecanico").val("");
            $(".idmecanico").fadeOut(2000);
            $(".idmecanico").show().css('display','none');
           
        }
        });*/


$(document).on("click", "button.btn_deta_delete", function() {
	$(this).tooltip('destroy');
	$(this).closest("tr").remove();
	calcularSubtotal();
	calcularIgv();
	calcularTotal();
});

$("#subtotal,#btn_save_preventa,#total").hover(function(e) {
	calcularSubtotal();
	calcularTotal();
});

$("#btn_save_preventa").click(function(e) {
	e.preventDefault();
	var v = true;
	v = v && $("#idtipodocumento").required();
	// v = v && $("#cliente_razonsocial").required();
	v = v && $("#idtipoventa").required();
	v = v && $("#idmoneda").required();
	// v = v && $("#idvendedor").required();
	v = v && $("#idalmacen").required();
		//v = v && $("#idmodalidad").required();
		
	v = v && $("#subtotal").required({numero:true, tipo:"float"});
	v = v && $("#total").required({numero:true, tipo:"float"});
	if(v) {
		var table = $("#tbl-detalle");
		
		if($("tbody tr", table).length < 1) {
			ventana.alert({titulo: "Error", mensaje: "Agregue los productos a la tabla"});
			return;
		}
		v = v && $(".deta_idunidad", table).required({numero:true, tipo:"int"});
		v = v && $(".deta_cantidad", table).required({numero:true, tipo:"float"});
		v = v && $(".deta_precio", table).required({numero:true, tipo:"float", aceptaCero:true});
		if(v) {
			// validamos el ruc del cliente
			if($("#idtipodocumento option:selected").data("ruc_obligatorio") == "S") {
				if( ! $("#compra_idcliente").required()) {
					ventana.alert({titulo: "Error", mensaje: "Seleccione un cliente de la lista o registre el clientes."});
					return;
				}
				
				var r1 = $("#idtipodocumento option:selected").data("idtipodocumento");
				if(r1 !== "14") {
					ventana.alert({titulo: "Error", mensaje: "No se puede Realizar la Venta a Pernosa Juridad una Boleta"});
					return;
			return;
		}
				/*if(validar_ruc) {
					if($("#estado_cliente").val() != "ok") {
						ventana.alert({titulo: "Consultar RUC", 
						mensaje: "No se ha podido verificar el estado del cliente, haga clic en Consultar RUC para continuar."});
						return;
					}
				}*/
			}

			$("#btn_save_preventa").prop("disabled",true);
			// setTimeout(function(){
				form.guardar();
			// },500);
		}
	}
});

function llenarDetalle() {
	if($.isArray(data_detalle)) {
		var tr = null, bool;
		for(var i in data_detalle) {
			if($.trim(data_detalle[i].idalmacen) == "")
				data_detalle[i].idalmacen = 0;
			tr = addDetalle(data_detalle[i]);
			if(data_detalle[i].oferta && data_detalle[i].oferta == "S") {
				// $(".deta_ckb_oferta", tr).trigger("change");
				set_precio_oferta(tr, $(".deta_ckb_oferta", tr).is(":checked"));
			}
			updateUnidades(tr, data_detalle[i], function(tr, data) {
				getPrecio(tr, {
					idproducto: data.idproducto
					,idunidad: data.idunidad
					,idmoneda: $("#idmoneda").val()
					,cantidad: data.cantidad
					,precio: data.precio
				}, function(tr) {
					calcularImporte(tr);
				});
				
				bool = false;
				if(Number(data.idalmacen) <= 0) {
					data.idalmacen = $("#idalmacen").val();
					bool = true;
				}
				
				calcularStock(data.idproducto, data.idalmacen, bool);
			});
		}
	}
}



$("#idtipodocumento").change(function() {
	// LoadSerieDoc($(this).val());
	is_activo();
});


$("#btn-consultar-ruc").click(function(e) {
	e.preventDefault();
	is_activo(false);
});

function init() {
	var s = getStorage("default_values");
	console.log(data);
	if(s == null)
		return;
	var data = $.parseJSON(s);
	
	if(data.idtipodocumento)
		$("#idtipodocumento").val(data.idtipodocumento);
	if(data.idtipoventa)
		$("#idtipoventa").val(data.idtipoventa);
	if(data.idmoneda)
		$("#idmoneda").val(data.idmoneda);
	if(data.idvendedor)
		$("#idvendedor").val(data.idvendedor);
	if(data.idalmacen)
		$("#idalmacen").val(data.idalmacen);

	if(data.idmodalidad){		
		$("#idmodalidad").val(data.idmodalidad);
		$("#idmodalidad").trigger("change");
	}
	/*
	if(data.idrampa)
		$("#idrampa").val(data.idrampa);
	

	if(data.idmecanico)
		$("#idrampa").val(data.idmecanico);
	
	$("#idrampa").fadeOut();*/

	$("#idtipodocumento").trigger("change");
	
}

if(_es_nuevo_) {
	init();
}
else {
	$("#idmodalidad").trigger("change");
	setTimeout(function() {
		llenarDetalle();
	}, 200);
}

$("#idtipodocumento").focus();

$("#modal-product-list").on('shown.bs.modal', function () {
	$(".list-group-item:first", this).focus();
});



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
			// $("#producto_precio_compra").val(res[0].precio_compra);
			// $("#producto_precio_venta").val(res[0].precio_venta);
			verificarProducto();
			return;
		}
		
		$("#modal-product-list .result-list a.list-group-item").remove();
		$("#modal-product-list .count-result-list").text(res.length);
		
		var a = null;
		for(var i in res) {
			a = $('<a href="#" class="list-group-item"></a>');
			a.html("<strong>"+res[i].codigo_barras+"</strong> | "+res[i].descripcion_detallada);
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
	// $("#producto_precio_compra").val(item.precio_compra);
	// $("#producto_precio_venta").val(item.precio_venta);
	verificarProducto();
	
	$("#modal-product-list").modal("hide");
	return false;
});

$("#modal-product-list").on("keydown", "a.list-group-item", function(e) {
	e.preventDefault();
	
	var c = $("#modal-product-list a.list-group-item").length;
	var i = $(this).index();
	
	if(e.which == 40) { // down
		i++;
		if(i >= c)
			i = 0;
	}
	else if(e.which == 38) { // up
		i--;
		if(i < 0)
			i = c - 1;
	}
	
	$("#modal-product-list a.list-group-item:eq("+i+")").focus();
	
	if(e.which == 13) {
		$(this).trigger("click");
	}
});

/****************************** js modal serie select ********************************/
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
	$("#modal-series .modal-title").text($("td:eq(1)", tr).text());
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
		calcularDatos(tr);
	}
	
	$("#input-text-serie").val("");
	$("#table-serie tbody tr").remove();
	tr.removeClass("current");
	
	$("#modal-series").modal("hide");
	return false;
});

input.autocomplete({
	selector: "#input-text-serie"
	,controller: "producto"
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
		};
	}
	,onSelect: function(item) {
		checkSerie();
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
		}, 1);
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
		,controller: "producto"
		,method: "grilla_serie"
		,data: {
			idalmacen: $(".deta_idalmacen", tr).val()
			,idproducto: $(".deta_idproducto", tr).val()
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

/****************************** fin js modal serie select ********************************/

function set_precio_oferta(tr, bool) {
	var str = $.trim(String($("td:eq(1)", tr).text()).replace(/\(A TITULO GRATUITO\)/g, ""));
	
	// $("select.deta_precio>option[oferta='S']", tr).remove();
	// $("td:eq(5)>.block", tr).remove();
	$(tr).removeClass("tr_oferta");
	
	if(bool) {
		// $("select.deta_precio option:last", tr).remove();
		// $("select.deta_precio", tr).append('<option value="0.00" oferta="S">0.00</option>');
		// $("select.deta_precio", tr).append('<option value="M">Agregar otro precio</option>');
		
		// $("select.deta_precio>option[oferta='S']", tr).prop("selected", true);
		str += " (A TITULO GRATUITO)";
		
		$(".deta_oferta", tr).val("S");
		$("td:eq(5)", tr).append('<div class="block"></div>');
		$(tr).addClass("tr_oferta");
	}
	else {
		$(".deta_oferta", tr).val("N");
	}
	
	$("td:eq(1)", tr).text(str);
}

function addPrecio(combo_precio){
	combo_precio = combo_precio || $("#ptemp");
	if(combo_precio.required({numero:true,tipo:"float",aceptaCero:true})) {
		var tr = $("#tbl-detalle tbody tr.current-precio");
		var precio = parseFloat(combo_precio.val());
		$("select.deta_precio option:last", tr).remove();
			
		$("select.deta_precio", tr).append('<option value="'+precio.toFixed(_fixed_venta)+'">'+precio.toFixed(_fixed_venta)+'</option>');
		$("select.deta_precio option:last", tr).prop("selected", true);

		if(add_new_precio)
			$("select.deta_precio", tr).append('<option value="M">Agregar otro precio</option>');
		calcularDatos(tr);

		$("#modal-precio-tempp").modal("hide");
	}
}

function get_saldo(idcliente) {//No se si funcionara esta funcion en las preventas
	// ajax.post({url: _base_url+"cliente/get_saldo/"+idcliente}, function(res) {
		// html = '';
		// if(res.linea_credito == "S") {
			// html = '<div class="alert alert-success">'+
				// '<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>'+
				// 'El cliente tiene una linea de credito de '+res.limite_credito+
				// '. <strong>Saldo disponible: '+res.saldo+'</strong>.</div>';
		// }
		// $("#info-saldo-cliente").html(html);
	// });
}

function setTipoIgv(tr, sel) {
	if(sel) {}
	else {
		if($(".deta_ckb_oferta", tr).is(":checked")) // es una oferta
			sel = $(".deta_grupo_igv>option:selected", tr).data("tipo_igv_oferta");
		else
			sel = $(".deta_grupo_igv>option:selected", tr).data("tipo_igv_default");
	}
	
	$(".deta_tipo_igv", tr).val(sel);
}

function calcularPrecioCosto(tr) {
	if(mostrar_precio_costo === false)
		return;
	
	var c = parseFloat($(".deta_idunidad>option:selected", tr).attr("count"));
	c = c * parseFloat($(".deta_pc_unit", tr).val());
	if(isNaN(c))
		c = 0;
	$(".deta_costo", tr).val(c);
}

function is_activo(p) {
	if(typeof p != "boolean")
		p = true;
	
	$(".msg-about-cliente").html("");
	$("#estado_cliente").val("");
	
	if(p) {
		var r = $("#idtipodocumento option:selected").data("ruc_obligatorio");
		if(r !== "S") {
			return;
		}
	}
	
	if($.trim($("#compra_idcliente").val()) != "") {
		ajax.post({url: _base_url+"cliente/is_activo/"+$("#compra_idcliente").val()}, function(res) {
			$("#estado_cliente").val(res.code);
			if(res.code == "ok") {
				$(".msg-about-cliente").html('<div class="alert alert-success">'+res.msg+'</div>').fadeIn();
			}
			else {
				$(".msg-about-cliente").html('<div class="alert alert-danger">'+res.msg+'</div>').fadeIn();
			}
			
			setTimeout(function(){
				$(".msg-about-cliente").fadeOut();
			},4000);
		});
	}
	else {
		ventana.alert({titulo: "Error", mensaje: "Seleccione un cliente de la lista para continuar."});
	}
}

$(".btn-search-vendedor").on("click", function(e) {
	e.preventDefault();
	jFrame.create({
		title: "Buscar empleados"
		,controller: "usuario"
		,method: "grilla_popup"
		,onSelect: function(datos) {
			if($("#idvendedor>option[value='"+datos.idusuario+"']").length <= 0) {
				var s = datos.nombres+' '+$.trim(datos.apellidos);
				$("#idvendedor").append('<option value="'+datos.idusuario+'">'+s+'</option>');
			}
			$("#idvendedor").val(datos.idusuario);
		}
	});
	jFrame.show();
});

$("#btn_cerrar_tab").on("click", function(e) {
	e.preventDefault();
	close_tab($(this).data("tabkey"));
});