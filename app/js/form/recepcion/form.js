if(typeof form == 'undefined') {
	form = {};
}

function consultar_recepcion(ind,idcompra,idproducto){
	can_pendiente = 0;
	ajax.post({
		url: _base_url+"recepcion/can_recepcionada/", data: "idcompra="+idcompra+"&idproducto="+idproducto}, function(data) {
			html1 = "<input type='text' style='text-align: right;' readonly id='cant_recep"+ind+"' name='cant_recep"+ind+"' class='form-control input-sm cant_recep"+ind+"' value='"+data[0]['recep']+"'>";
			
			cantidad = $("#cantidad"+ind).val();
			can_pendiente = cantidad - data[0]['recep'];
			
			estado='enabled';
			if(can_pendiente<=0){
				estado=' readonly disabled';	
			}
						
			html2 = "<input type='text' style='text-align: right;' readonly id='can_pendi"+ind+"' name='can_pendi"+ind+"' class='form-control input-sm can_pendi"+ind+"' value='"+can_pendiente+"'>";
			
			html3 = "<input type='text' style='text-align: right; border-color: #1ab394;' "+estado+" id='new_recepcion"+ind+"' name='new_recepcion' class='form-control input-sm new_recepcion"+ind+"' value='"+can_pendiente+"'>";
			
			$( "#tbl-detalle  tbody td.can_recep"+ind ).html(html1);
			$( "#tbl-detalle  tbody td.can_pendi"+ind ).html(html2);
			$( "#tbl-detalle  tbody td.new_recep"+ind ).html(html3);
		}
	);
}


$("#buscar_compra").focus();

$("#form_recepcion").submit(function(e) {
	e.preventDefault();
	return false;
});

function cargarDatosCompra(idcompra) {
	$("#tbl-detalle tbody tr").remove();
	
	ajax.post({url: _base_url+"recepcion/get_detalle_pendiente/"+idcompra}, function(res) {
		if(res.length) {
			var table = new Table(), cls, data;
			
			for(var i in res) {
				data = res[i];
				cls = (data.controla_serie == 'S') ? "has_serie" : "";
				
				table.tr({index: data.idproducto, class: cls});
				
				table.td('<input type="checkbox" name="deta_iddetalle[]" class="deta_iddetalle" value="'+data.iddetalle_compra+'">', {class:"text-center"});
				table.td('<input type="text" name="deta_producto[]" class="form-control input-xs deta_producto" value="'+data.producto+'" readonly>');
				table.td('<select name="deta_idalmacen[]" class="form-control input-xs deta_idalmacen">'+$("#idalmacen_temp").html()+'</select>');
				table.td(data.unidad, {style: "vertical-align:middle;"});
				table.td('<input type="text" name="deta_cantidad[]" class="form-control input-xs deta_cantidad" value="'+data.cantidad+'" readonly>');
				table.td('<input type="text" name="deta_recepcionado[]" class="form-control input-xs deta_recepcionado" value="'+data.cantidad_recepcionada+'" readonly>');
				table.td('<input type="text" name="deta_pendiente[]" class="form-control input-xs deta_pendiente" value="'+data.cantidad_pendiente+'" readonly>');
				
				table.td('<input type="text" name="deta_ingreso[]" class="form-control input-xs deta_ingreso">');
				
				if(cls == 'has_serie') {
					table.td('<input type="hidden" name="deta_series[]" class="deta_series" value="">'+
						'<button class="btn btn-success btn-xs btn_deta_serie" data-toggle="tooltip" title="Ingresar las series del producto">'+
						'<i class="fa fa-cubes"></i></button>', {class:"text-center"});
				}
				else {
					table.td('<input type="hidden" name="deta_series[]" class="deta_series" value="">', {class:"text-center"});
				}
				
				table.td('<input type="hidden" name="deta_controla_stock[]" class="deta_controla_stock" value="'+data.controla_stock+'">'+
					'<input type="hidden" name="deta_controla_serie[]" class="deta_controla_serie" value="'+data.controla_serie+'">'+
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

$("#input-text-serie").keypress(function(e) {
	var t = e.keyCode ? e.keyCode : e.which;
	if(t == 13) {
		e.preventDefault();
		$("#btn-add-serie").trigger("click");
		return false;
	}
});

$("#btn-add-serie").click(function(e) {
	e.preventDefault();
	if($.trim($("#input-text-serie").val()) != "") {
		var temp = String($("#input-text-serie").val()).replace(/\W/g, '').toUpperCase();
		if(arrListaSeries.indexOf(temp) != -1) {
			ventana.alert({titulo: '', mensaje: 'La serie <b>'+temp+'</b> ya se ha ingresado.'}, function() {
				$("#input-text-serie").focus().select();
			});
			return false;
		}
		
		var tr = $("#tbl-detalle tbody tr.current");
		var total = parseInt($(".deta_pendiente", tr).val()) * parseInt($(".deta_cantidad_um", tr).val());
		if(arrListaSeries.length >= total) {
			ventana.alert({titulo: '', mensaje: 'Ya se ha ingresado todas las series.'});
			return false;
		}
		
		add_series(temp);
		arrListaSeries.push(temp);
		$("#input-text-serie").val("").focus();
	}
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
	selector: "#buscar_compra"
	,controller: "recepcion"
	,label: "<strong>[documento]</strong> | [proveedor]"
	,value: "[documento] | [proveedor]"
	,highlight: true
	,onSelect: function(item) {
		$("#idcompra").val(item.idcompra);
		$("#idtipodocumento").val(item.idtipodocumento);
		$("#serie").val(item.serie);
		$("#numero").val(item.numero);
		cargarDatosCompra(item.idcompra);
	}
});

$("#btn-buscar-compra").click(function(e) {
	e.preventDefault();
	
	jFrame.create({
		title: "Buscar compra"
		,controller: "recepcion"
		,method: "grilla_popup"
		,widthclass: "modal-lg"
		,msg: ""
		,onSelect: function(datos) {
			$("#idcompra").val(datos.idcompra);
			$("#buscar_compra").val(datos.documento+' | '+datos.proveedor);
			$("#idtipodocumento").val(datos.idtipodocumento);
			$("#serie").val(datos.serie);
			$("#numero").val(datos.numero);
			cargarDatosCompra(datos.idcompra);
		}
	});
	
	jFrame.show();
	return false;
});

$("#btn_save_recepcion").click(function(e) {
	e.preventDefault();
	
	var v = true;
	v = v && $("#buscar_compra").required();
	v = v && $("#observacion").required();
	v = v && $("#idtipodocumento").required();
	v = v && $("#serie").required();
	v = v && $("#numero").required();
	if(v) {
		if( ! $("#idcompra").required()) {
			ventana.alert({titulo: "", mensaje: "Seleccione una Orden de Compra para poder hacer la recepcion de Mercaderia"});
			return false;
		}
		if($("#tbl-detalle tbody tr .deta_iddetalle:checked").length < 1) {
			ventana.alert({titulo: "", mensaje: "Seleccione los item ha recepcionar"});
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
