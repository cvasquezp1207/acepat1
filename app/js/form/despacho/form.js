$("#buscar_venta").focus();

$("#form_despacho").submit(function(e) {
	e.preventDefault();
	return false;
});

function cargarDatosVenta(idventa) {
	$("#tbl-detalle tbody tr").remove();
	
	ajax.post({url: _base_url+"despacho/get_detalle_pendiente/"+idventa}, function(res) {
		if(res.length) {
			var table = new Table(), cls, data;
			
			for(var i in res) {
				data = res[i];
				cls = (data.controla_serie == 'S') ? "has_serie" : "";
				
				table.tr({index: data.idproducto, class: cls});
				
				table.td('<input type="checkbox" name="deta_iddetalle[]" class="deta_iddetalle" value="'+data.iddetalle_venta+'">', {class:"text-center"});
				table.td('<input type="text" name="deta_producto[]" class="form-control input-xs deta_producto" value="'+data.producto+'" readonly>');
				table.td('<select name="deta_idalmacen[]" class="form-control input-xs deta_idalmacen">'+$("#idalmacen_temp").html()+'</select>');
				table.td(data.unidad, {style: "vertical-align:middle;"});
				table.td('<input type="text" name="deta_cantidad[]" class="form-control input-xs deta_cantidad" value="'+data.cantidad+'" readonly>');
				table.td('<input type="text" name="deta_despachado[]" class="form-control input-xs deta_despachado" value="'+data.cantidad_despachado+'" readonly>');
				table.td('<input type="text" name="deta_pendiente[]" class="form-control input-xs deta_pendiente" value="'+data.cantidad_pendiente+'" readonly>');
				
				table.td('<input type="text" name="deta_ingreso[]" class="form-control input-xs deta_ingreso">');
				
				if(cls == 'has_serie') {
					table.td('<input type="hidden" name="deta_series[]" class="deta_series" value="'+data.serie+'">'+
						'<button class="btn btn-success btn-xs btn_deta_serie" data-toggle="tooltip" title="Ingresar las series del producto">'+
						'<i class="fa fa-cubes"></i></button>', {class:"text-center"});
				}
				else {
					table.td('<input type="hidden" name="deta_series[]" class="deta_series" value="">', {class:"text-center"});
				}
				
				table.td('<input type="hidden" name="deta_controla_stock[]" class="deta_controla_stock" value="'+data.controla_stock+'">'+
					'<input type="hidden" name="deta_controla_serie[]" class="deta_controla_serie" value="'+data.controla_serie+'">'+
					'<input type="hidden" name="deta_idproducto[]" class="deta_idproducto" value="'+data.idproducto+'">'+
					'<input type="hidden" name="deta_cantidad_um[]" class="deta_cantidad_um" value="'+data.cantidad_um+'">', {style:"display:none;"});
				
				$("#tbl-detalle tbody").append(table.to_string());
				
				if(data.idalmacen) {
					$("#tbl-detalle tbody tr:last .deta_idalmacen").val(data.idalmacen);
				}
			}
			
			$("#tbl-detalle tbody tr input.deta_ingreso").numero_real();
			$("#tbl-detalle tbody tr :input:not(.deta_iddetalle)").prop("disabled", true);
		}
	});
}

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
	$("#input-text-serie").val("").focus();
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
		setTimeout(function() {
			checkSerie();
		}, 100);
	}
});

$("#input-text-serie").keypress(function(e) {
	var t = e.keyCode ? e.keyCode : e.which;
	if(t == 13) {
		e.preventDefault();
		checkSerie();
		return false;
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

$("#tbl-detalle").on("change", ".deta_iddetalle", function() {
	var tr = $(this).closest("tr");
	var bool = ( ! $(this).is(":checked"));
	$(":input:not(.deta_iddetalle)", tr).prop("disabled", bool);
});

$("#check_all").on("change", function() {
	var bool = $(this).is(":checked");
	$("#tbl-detalle tbody tr .deta_iddetalle").prop("checked", bool).trigger("change");
});

input.autocomplete({
	selector: "#buscar_venta"
	,controller: "despacho"
	,label: "<strong>[documento]</strong> | [cliente]"
	,value: "[documento] | [cliente]"
	,highlight: true
	,onSelect: function(item) {
		$("#idventa").val(item.idventa);
		$("#idtipodocumento").val(item.idtipodocumento);
		$("#serie").val(item.serie);
		$("#numero").val(item.numero);
		cargarDatosVenta(item.idventa);
	}
});

$("#btn-buscar-venta").click(function(e) {
	e.preventDefault();
	
	jFrame.create({
		title: "Buscar venta"
		,controller: "despacho"
		,method: "grilla_popup"
		,widthclass: "modal-lg"
		,msg: ""
		,onSelect: function(datos) {
			$("#idventa").val(datos.idventa);
			$("#buscar_venta").val(datos.documento+' | '+datos.cliente);
			$("#idtipodocumento").val(datos.idtipodocumento);
			$("#serie").val(datos.serie);
			$("#numero").val(datos.numero);
			cargarDatosVenta(datos.idventa);
		}
	});
	
	jFrame.show();
	return false;
});

$("#btn_save_despacho").click(function(e) {
	e.preventDefault();
	
	var v = true;
	v = v && $("#buscar_venta").required();
	v = v && $("#observacion").required();
	v = v && $("#idtipodocumento").required();
	v = v && $("#serie").required();
	v = v && $("#numero").required();
	if(v) {
		if( ! $("#idventa").required()) {
			ventana.alert({titulo: "", mensaje: "Seleccione una Venta para poder hacer el despacho de los productos"});
			return false;
		}
		if($("#tbl-detalle tbody tr .deta_iddetalle:checked").length < 1) {
			ventana.alert({titulo: "", mensaje: "Seleccione los item ha despachar"});
			return false;
		}
		
		var error = false, msg = '', tr, cant, series;
		$("#tbl-detalle tbody tr .deta_iddetalle:checked").each(function() {
			tr = $(this).closest("tr");
			error = ! ( $(".deta_idalmacen", tr).required() && $(".deta_ingreso", tr).required({numero:true, tipo:"float"}) );
			
			if(error == false) {
				if(tr.hasClass("has_serie")) {
					if($.trim($(".deta_series", tr).val()) == "") {
						msg = 'Ingrese las series del producto '+$(".deta_producto", tr).val();
						error = true;
					}
					
					if(error == false) {
						cant = parseInt($(".deta_ingreso", tr).val()) * parseInt($(".deta_cantidad_um", tr).val());
						series = String($(".deta_series", tr).val()).split("|").length;
						if(cant != series) {
							msg = 'Complete las series que faltan del producto '+$(".deta_producto", tr).val()+
								'. Debe ingresar '+cant+' series';
							error = true;
						}
					}
				}
			}
			
			return (error == false);
		});
		
		if(error) {
			if(msg != '') {
				ventana.alert({titulo: "", mensaje: msg});
			}
			return false;
		}
		
		form.guardar();
	}
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
		cant = cant / parseInt($(".deta_cantidad_um", tr).val());
		$(".deta_ingreso", tr).val(Math.round(cant));
	}
	
	$("#input-text-serie").val("");
	$("#table-serie tbody tr").remove();
	tr.removeClass("current");
	
	$("#modal-series").modal("hide");
	return false;
});
