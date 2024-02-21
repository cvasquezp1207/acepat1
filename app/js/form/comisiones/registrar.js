
$('#fecha_inicio,#fecha_fin').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	endDate: parseDate(_current_date)
});

$("#idmarca").chosen();

$("#btn-new-rango").on("click", function(e) {
	e.preventDefault();
	$("#modal-rango").modal("show");
});

$("#dias_min, #dias_max").numero_entero();

$("#btn-save-rango").on("click", function(e) {
	e.preventDefault();
	if($("#dias_min").required()) {
		ajax.post({url: _base_url+"comisiones/guardar_rango", data: $("#modal-rango").serialize()}, function(res) {
			ventana.alert({titulo:"", mensaje:"Datos guardados correctamente", tipo:"success"}, function() {
				$("#dias_min, #dias_max").val("");
				$("#modal-rango").modal("hide");
			});
			reload_combo("#idrango", {controller: "comisiones", method: "get_rangos"}, function() {
				$("#idrango").val(res.dias_min+";"+res.dias_max);
			});
		});
	}
});

$("#btn-del-rango").on("click", function(e) {
	e.preventDefault();
	if($("#idrango").required()) {
		var label = $("#idrango option:selected").text();
		
		ventana.confirm({
			titulo:""
			,mensaje:"&iquest;Desea eliminar el rango [<strong>"+label+"</strong>] d&iacute;as?"
			,textoBotonAceptar: "Eliminar"
		}, function(ok) {
			if(ok) {
				ajax.post({url: _base_url+"comisiones/eliminar_rango", data: "id="+$("#idrango").val()}, function(res) {
					ventana.alert({titulo:"", mensaje:"Se ha eliminado el rango [<strong>"+label+"</strong>] d&iacute;as", tipo:"success"});
					reload_combo("#idrango", {controller: "comisiones", method: "get_rangos"});
				});
			}
		});
	}
});

$("#btn-add-comision").on("click", function(e) {
	e.preventDefault();
	if($.trim($("#idmarca").val()) == "") {
		ventana.alert({titulo:"", mensaje:"Seleccione la marca que desea agregar a la tabla."});
		return;
	}
	
	if($("#idrango").required()) {
		var d = String($("#idrango").val()).split(";");
		// agregamos el header
		addHeader([{dias_min: parseInt(d[0]), dias_max: parseInt(d[1])}]);
		addBody([{
			idmarca: $("#idmarca").val()
			, marca: $("#idmarca option:selected").text()
			, dias_min: parseInt(d[0])
			, dias_max: parseInt(d[1])
		}], true);
	}
});

function addHeader(arr) {
	if(arr.length) {
		var l, a, table = $("#table-comision"), td, pos;
		for(var i in arr) {
			a = arr[i].dias_min+";"+arr[i].dias_max;
			
			// no existe la columna, creamos
			if($("thead th.col[val='"+a+"']", table).length <= 0) {
				l = arr[i].dias_min + " - ";
				if(arr[i].dias_max == -1)
					l += "mas";
				else
					l += arr[i].dias_max;
				
				// buscamos la columna minima
				td = $("thead th.marca", table);
				$("thead th.col", table).each(function() {
					if(arr[i].dias_min >= parseInt($(this).attr("min")))
						td = $(this);
					else
						return false;
				});
				
				// agregamos la columna en el header
				$('<th class="col" val="'+a+'" min="'+arr[i].dias_min+'">'+l+' <button class="btn btn-link btn-xs btn-delete-col text-danger" title="Eliminar columna"><i class="fa fa-times-circle"></i></button></th>').insertAfter(td);
				
				// verificamos si tenemos el body
				if($("tbody tr", table).length) {
					// verificamos si la col no existe
					if($("tbody td.col[val='"+a+"']", table).length <= 0) {
						pos = td.index() + 1;
						$('<td class="col" val="'+a+'" min="'+arr[i].dias_min+'">'+crearInput()+'</td>').insertAfter($("tbody tr td:nth-child("+pos+")", table));
					}
				}
			}
		}
	}
}

function crearInput() {
	return '<input type="text" name="comision[]" class="form-control input-xs deta_comision">';
}

function crearFila(idmarca, marca, table) {
	var tr = $('<tr class="trow" val="'+idmarca+'"></tr>');
	var html = '<td class="marca">'+marca+'</td>';
	
	// armamos las celdas de acuerdo al header
	$("thead th.col", table).each(function() {
		html += '<td class="col" val="'+$(this).attr("val")+'" min="'+$(this).attr("min")+'">'+crearInput()+'</td>';
	});
	
	html += '<td class="text-center"><button class="btn btn-danger btn-xs btn-delete-row" title="Eliminar fila"><i class="fa fa-trash"></i></button></td>';
	
	tr.append(html);
	$("tbody", table).append(tr);
	
	return tr;
}

function addBody(arr, foco) {
	if(arr.length) {
		var m, a, table = $("#table-comision"), tr;
		for(var i in arr) {
			a = arr[i].dias_min+";"+arr[i].dias_max;
			m = arr[i].idmarca;
			
			if($("tbody tr.trow[val='"+m+"']", table).length <= 0) {
				tr = crearFila(m, arr[i].marca, table);
			}
			else {
				tr = $("tbody tr.trow[val='"+m+"']", table);
			}
			
			if($("td.col[val='"+a+"'] .deta_comision", tr).length <= 0) {
				$("td.col[val='"+a+"']", tr).append(crearInput());
			}
			
			if(arr[i].comision)
				$("td.col[val='"+a+"'] .deta_comision", tr).val(arr[i].comision);
			
			if(typeof foco == "boolean" && foco === true)
				$("td.col[val='"+a+"'] .deta_comision", tr).focus();
		}
	}
}

$("#table-comision").on("click", ".btn-delete-row", function(e) {
	e.preventDefault();
	$(this).closest("tr").remove();
});

$("#table-comision").on("click", ".btn-delete-col", function(e) {
	e.preventDefault();
	var a = $(this).closest("th").attr("val");
	var table = $("#table-comision");
	$("thead th.col[val='"+a+"']", table).remove();
	$("tbody td.col[val='"+a+"']", table).remove();
});

$("#btn_save").on("click", function(e) {
	e.preventDefault();
	
	var datos = [];
	if($(".deta_comision").length) {
		if( ! $(".deta_comision").required({numero:true, tipo:"float", aceptaCero:true}))
			return;
		
		$("#table-comision tbody tr").each(function() {
			tr = $(this);
			$("td.col", tr).each(function() {
				datos.push({
					idmarca: tr.attr("val")
					,rango: $(this).attr("val")
					,comision: $(".deta_comision", this).val()
				});
			});
		});
	}
	
	ajax.post({url: _base_url+"comisiones/guardar_parametros", 
		data: {idsucursal:$("#idsucursal").val(),nombre:$("#nombre").val(),fecha_inicio:$("#fecha_inicio").val()
				,fecha_fin:$("#fecha_fin").val(), datos:datos}}, function(res) {
		ventana.alert({titulo:"", mensaje:"Datos guardados correctamente", tipo:"success"});
	});
});

$("#idempresa").on("change", function() {
	reload_combo("#idsucursal", {controller: "sucursal", data: "idempresa="+$("#idempresa").val()}, function() {
		$("#idsucursal").trigger("change");
	});
});

$("#idsucursal").on("change", function() {
	$("#table-comision thead th.col").remove();
	$("#table-comision tbody tr").remove();
	
	if($.trim($("#idsucursal").val()) != "") {
		ajax.post({url: _base_url+"comisiones/get_parametros/"+$("#idsucursal").val()}, function(res) {
			if(res) {
				addHeader(res);
				addBody(res);
			}
		});
	}
});

function cargarDatos(res) {
	if(res) {
		addHeader(res);
		addBody(res);
	}
}

(function() {
	$("#idsucursal").trigger("change");
})();