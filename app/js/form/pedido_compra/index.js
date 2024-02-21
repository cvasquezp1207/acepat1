$("#all_fecha").on("change", function() {
	if($(this).is(":checked")) {
		$('#fecha_i,#fecha_f').val("");
		$('#fecha_i,#fecha_f').prop("disabled", true);
	}
	else {
		$('#fecha_i,#fecha_f').prop("disabled", false);
	}
});

$('#fecha_i').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	enableOnReadonly: false,
	endDate: parseDate(_current_date)
});

$('#fecha_f').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	enableOnReadonly: false,
	endDate: parseDate(_current_date)
});

$("#reposicion,#seguridad,#para").numero_entero();

$("#btn-consultar").on("click", function(e) {
	e.preventDefault();
	if($("#reposicion,#seguridad,#para").required({numero:true,tipo:"int"})) {
		// if( ! $("#all_fecha").is(":checked")) {
			// if( ! $("#fecha_i,#fecha_f").required({tipo:"date"})) {
				// return;
			// }
		// }
		refrescarTablas();
	}
});

function refrescarTablas() {
	$(".table-proveedor tbody tr").remove();
	$(".table-producto tbody tr").remove();
	ajax.post({url: _base_url+"pedido_compra/get", data:$("#form").serialize()}, function(res) {
		if(res.idsucursal) {
			$("#idsucursal_consultar").val(res.idsucursal);
		}
		if(res.proveedor) {
			addFilasProveedor(res.proveedor.rows);
			$(".table-proveedor").data("more", res.proveedor.more);
			$(".table-proveedor").data("page", res.proveedor.page);
		}
		if(res.producto) {
			addFilasProducto(res.producto.rows);
			$(".table-producto").data("more", res.producto.more);
			$(".table-producto").data("page", res.producto.page);
		}
	});
}

function addFilasProveedor(arr) {
	if(arr.length) {
		var table = new Table();
		
		for(var i in arr) {
			table.tr({data:{idproveedor:arr[i].idproveedor}});
			table.td(arr[i].nombre);
			table.td(arr[i].cantidad);
		}
		
		$(".table-proveedor tbody").append(table.to_string());
	}
}

function addFilasProducto(arr) {
	if(arr.length) {
		var table = new Table();
		
		for(var i in arr) {
			table.tr({data:{idproducto:arr[i].idproducto}});
			table.td(arr[i].deuda);
			table.td(arr[i].stock);
			table.td(arr[i].stock_seguro);
			table.td(arr[i].pp);
			table.td(arr[i].critico);
			table.td(arr[i].promedio_ventas);
			table.td(arr[i].sugerido);
			table.td('<input type="checkbox" value="'+arr[i].idproducto+'" checked>', {class:"text-center"});
			
		}
		
		$(".table-producto tbody").append(table.to_string());
	}
}

$(".table-proveedor").on("click", "tbody tr[data-idproveedor]", function() {
	$(".table-proveedor tbody tr").not(this).removeClass("active");
	$(this).toggleClass("active");
	cargarProducto();
});

$(".table-producto").on("click", "tbody tr[data-idproducto]", function(e) {
	e.stopPropagation();
	var bool = ! $(":checkbox", this).is(":checked");
	$(":checkbox", this).prop("checked", bool).trigger("change");
});

$(".table-producto").on("click", "tbody tr :checkbox", function(e) {
	e.stopPropagation();
});

function cargarProducto(page, search) {
	if(_ajax_load_producto)
		return;
	
	_ajax_load_producto = true;
	
	if(typeof page == "undefined")
		page = 0;
	if(typeof search == "undefined")
		search = false;
	
	var str = "idsucursal="+$("#idsucursal_consultar").val()+"&page="+page;
	if($(".table-proveedor tbody tr.active").length) {
		str += "&idproveedor="+$(".table-proveedor tbody tr.active:first").data("idproveedor");
	}
	str += "&query="+$("#txtSearchProducto").val();
	
	if(page <= 0)
		$(".table-producto tbody tr").remove();
	ajax.post({url: _base_url+"pedido_compra/get_producto", data:str}, function(res) {
		if(res.rows.length) {
			addFilasProducto(res.rows);
			$(".table-producto").data("more", res.more);
			$(".table-producto").data("page", res.page);
		}
		else if(search == true) {
			$(".table-producto tbody").html('<tr><td colspan="10" class="text-center">Sin resultados para la busqueda.</td></tr>');
		}
		_ajax_load_producto = false;
	});
}

// buscamos los productos
$("#txtSearchProducto").on("keypress", function(e) {
	if(e.which == 13) {
		e.preventDefault();
		$(".btn-search-txt-producto").trigger("click");
	}
});
$(".btn-search-txt-producto").on("click", function(e) {
	e.preventDefault();
	if($(".table-producto tbody tr").length) {
		cargarProducto(0, true);
	}
});

function cargarProveedor(page, search) {
	if(_ajax_load_proveedor)
		return;
	
	_ajax_load_proveedor = true;
	
	if(typeof page == "undefined")
		page = 0;
	if(typeof search == "undefined")
		search = false;
	
	var str = "idsucursal="+$("#idsucursal_consultar").val()+"&page="+page+
		"&query="+$("#txtSearchProveedor").val();
	
	var sel = false;
	if($(".table-proveedor tbody tr.active").length) {
		sel = $(".table-proveedor tbody tr.active:first").data("idproveedor");
	}
	
	if(page <= 0)
		$(".table-proveedor tbody tr").remove();
	ajax.post({url: _base_url+"pedido_compra/get_proveedor", data:str}, function(res) {
		if(res.rows.length) {
			addFilasProveedor(res.rows);
			$(".table-proveedor").data("more", res.more);
			$(".table-proveedor").data("page", res.page);
			if(sel !== false) {
				if($(".table-proveedor tbody tr[data-idproveedor='"+sel+"']").length) {
					$(".table-proveedor tbody tr[data-idproveedor='"+sel+"']").addClass("active");
				}
				else {
					cargarProducto();
				}
			}
		}
		else if(search == true) {
			$(".table-proveedor tbody").html('<tr><td colspan="2" class="text-center">Sin resultados para la busqueda.</td></tr>');
		}
		_ajax_load_proveedor = false;
	});
}

// buscamos el proveedor
$("#txtSearchProveedor").on("keypress", function(e) {
	if(e.which == 13) {
		e.preventDefault();
		$(".btn-search-txt-proveedor").trigger("click");
	}
});
$(".btn-search-txt-proveedor").on("click", function(e) {
	e.preventDefault();
	if($(".table-proveedor tbody tr").length) {
		cargarProveedor(0, true);
	}
});

$(".table-producto").on("change", "tbody tr :checkbox", function() {
	if($(this).is(":checked")) {
		$(this).closest("tr").addClass("success");
	}
	else {
		$(this).closest("tr").removeClass("success");
	}
});

$("#btn-generar").on("click", function(e) {
	e.preventDefault();
/* mensaje de seleccion de filas
	if($(".table-producto tbody tr.success").length <= 0) {
		ventana.alert({titulo:"", mensaje:"Seleccione los productos para generar la orden"});
		return;
	}*/
	$("#descripcion").val("");
	$("#aprobado").val("N");
	$("#modal-generar-pedido").modal("show");
});

$("#btn-save-pedido").on("click", function(e) {
	e.preventDefault();
	
	
	if($(".table-producto tbody tr.success").length <= 0) {
		ventana.alert({titulo:"", mensaje:"Seleccione los productos para generar la orden"});
		return;
	}
	
	var arr = [];
	$(".table-producto tbody tr.success").each(function() {
		arr.push($(this).data("idproducto"));
	});
	
	if(arr.length) {
		var str = "idsucursal="+$("#idsucursal_consultar").val()+"&idproducto="+arr.join("|");
		if($(".table-proveedor tbody tr.active").length) {
			str += "&idproveedor="+$(".table-proveedor tbody tr.active:first").data("idproveedor");
		}
		str += "&" + $("#form-pedido").serialize();
		
		ajax.post({url: _base_url+"pedido_compra/guardar", data:str}, function(res) {
			if(res.idpedido) {
				$("#modal-generar-pedido").modal("hide");
				
				ventana.confirm({
					titulo: ""
					,mensaje: "Pedido de compra generado correctamente. Pedido N&deg; "+res.idpedido
					,textoBotonAceptar: "Ver pedido"
					,textoBotonCancelar: "Aceptar"
				}, function(ok){
					if(ok) {
						abrirPedido(res.idpedido);
					}
				});
			}
		});
	}
});

function abrirPedido(id) {
	var key = "ptemp2038";
	open_url_tab(_base_url+"pedido/editar/"+id+"/"+key, key, "Pedido "+id, true);
}

$("#btn-print").on("click", function(e) {
	e.preventDefault();
	if($(".table-producto tbody tr.success").length <= 0) {
		ventana.alert({titulo:"", mensaje:"Seleccione los productos para generar la orden"});
		return;
	}
	
	var arr = [];
	$(".table-producto tbody tr.success").each(function() {
		arr.push($(this).data("idproducto"));
	});
	
	if(arr.length) {
		var str = "idsucursal="+$("#idsucursal_consultar").val()+"&idproducto="+arr.join("|");
		if($(".table-proveedor tbody tr.active").length) {
			str += "&idproveedor="+$(".table-proveedor tbody tr.active:first").data("idproveedor");
		}
		open_url_windows("pedido_compra/imprimir?"+str);
	}
});

$("#btn-excel").on("click", function(e) {
	e.preventDefault();
	if($(".table-producto tbody tr.success").length <= 0) {
		ventana.alert({titulo:"", mensaje:"Seleccione los productos para generar la orden"});
		return;
	}
	
	var arr = [];
	$(".table-producto tbody tr.success").each(function() {
		arr.push($(this).data("idproducto"));
	});
	
	if(arr.length) {
		var str = "idsucursal="+$("#idsucursal_consultar").val()+"&idproducto="+arr.join("|");
		if($(".table-proveedor tbody tr.active").length) {
			str += "&idproveedor="+$(".table-proveedor tbody tr.active:first").data("idproveedor");
		}
		open_url_windows("pedido_compra/exportar?"+str);
	}
});

var _ajax_load_producto = false;
var _ajax_load_proveedor = false;

$(".table-responsive").on("scroll", function() {
	if($(this).scrollTop() + $(this).height() >= $(".table", this).height() - 100) {
		var t = $(".table", this);
		if(t.data("more") == true) {
			var p = t.data("page") + 1;
			if(t.hasClass("table-proveedor")) {
				cargarProveedor(p);
			}
			else {
				cargarProducto(p);
			}
		}
	}
});