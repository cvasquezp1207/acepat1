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
			$("#idvendedor").val(datos.idusuario).trigger("change");
		}
	});
	jFrame.show();
});

$("#idvendedor").on("change", function() {
	refrescarTabla();
});

$("#pendiente").on("change", function() {
	refrescarTabla();
});

$("#file_nombre").on("click", function() {
	$("#file").trigger("click");
});

$(document).on("change", "#file", function() {
	var inputFile = this;
    if(inputFile.files.length != 0) {
		$("#file_nombre").val(inputFile.files[0].name);
    }
	else {
		$("#file_nombre").val("");
	}
});

$("#btn-upload").on("click", function(e) {
	e.preventDefault();
	
	if( ! $("#file").required()) {
		ventana.alert({titulo:"", mensaje:"Indique el archivo a procesar."});
		return;
	}
	
	var required = ["xls", "xlsx"];
	var ext = $("#file")[0].files[0].name.split(".").pop();
	if(required.indexOf(ext) == -1) {
		ventana.alert({titulo:"", mensaje:"Solo se puede subir archivos con extension: "+required.join(", ")+"."});
		return;
	}
	
	var datos = {
		response: "ajax"
		,type: "json"
	};
	var fd = new FormData();
	$.each(datos, function(k, v) {
		fd.append(k, v);
	});
	fd.append("file", $("#file")[0].files[0]);
	
	$.ajax({
		url: _base_url+"preventa_claro/procesar",
		type: "POST",
		data: fd,
		dataType: "json",
		enctype: 'multipart/form-data',
		processData: false,  // tell jQuery not to process the data
		contentType: false   // tell jQuery not to set contentType
	}).done(function( data ) {
		if(data.code == "ERROR") {
			ventana.alert({titulo:"", mensaje:data.message, type:"error"});
			return;
		}
		if(data.data === true) {
			$("#file,#file_nombre").val("");
			refrescarTabla();
		}
	});
});

function refrescarTabla() {
	$(".table-preventa tbody tr").remove();
	var str = "idvendedor="+$("#idvendedor").val()+"&pendiente="+$("#pendiente").val();
	ajax.post({url: _base_url+"preventa_claro/get", data:str}, function(res) {
		addFilas(res);
	});
}

function addFilas(arr) {
	if(arr.length) {
		var table = new Table(), cls;
		
		for(var i in arr) {
			cls = (arr[i].pendiente == "N") ? "atendido" : "";
			table.tr({data:{idpreventa_claro:arr[i].idpreventa_claro}, class:cls});
			table.td((Number(i)+1), {class:"text-center item"});
			table.td(fecha_es(arr[i].fecha));
			table.td(arr[i].cliente);
			table.td(arr[i].moneda);
			table.td(Number(arr[i].total).toFixed(2));
			table.td(arr[i].tipodoc);
			table.td(arr[i].producto);
			table.td(arr[i].vendedor);
			if(arr[i].pendiente == "N") {
				table.td('<button class="btn btn-warning btn-xs btn-ver-preventa" idp="'+arr[i].idpreventa+'" title="Pedido '+
					'N&deg; '+arr[i].idpreventa+'" data-toggle="tooltip"><i class="fa fa-shopping-cart"></i></button>');
			}
			else {
				table.td('');
			}
			table.td('<input type="checkbox" value="'+arr[i].idpreventa_claro+'">', {class:"text-center"});
		}
		
		$(".table-preventa tbody").append(table.to_string());
	}
}

$(".table-preventa").on("click", "tbody tr", function(e) {
	e.stopPropagation();
	$(".table-preventa tbody tr").not(this).removeClass("active");
	$(this).toggleClass("active");
});

$(".table-preventa").on("click", "tbody tr :checkbox", function(e) {
	e.stopPropagation();
});

$(".table-preventa").on("change", "tbody tr :checkbox", function() {
	if($(this).is(":checked")) {
		$(this).closest("tr").addClass("warning");
		var tabla = ".table-preventa";
		var bool = ($("tbody tr :checkbox:checked", tabla).length == $("tbody tr", tabla).length);
		$("#checkAll").prop("checked", bool);
	}
	else {
		$(this).closest("tr").removeClass("warning");
		$("#checkAll").prop("checked", false);
	}
});

$("#checkAll").on("change", function() {
	var bool = $(this).prop("checked");
	$(".table-preventa tbody tr :checkbox").prop("checked", bool);
	if(bool)
		$(".table-preventa tbody tr").addClass("warning");
	else
		$(".table-preventa tbody tr").removeClass("warning");
});

$("#txtSearch").on("keyup", function(e) {
	buscar();
});

$(".btn-search-txt").on("click", function(e) {
	e.preventDefault();
	buscar();
});

function buscar() {
	var str = $.trim($("#txtSearch").val());
	if(str == "") {
		$(".table-preventa tbody tr").removeClass("hide");
	}
	else {
		$(".table-preventa tbody tr").addClass("hide");
		$(".table-preventa tbody tr td:not(.item):contains('"+str.toUpperCase()+"')").closest("tr").removeClass("hide");
	}
}

$("#btn-delete").on("click", function(e) {
	e.preventDefault();
	if($(".table-preventa tbody tr.warning").length) {
		var msg = "";
		if($(".table-preventa tbody tr.warning").length == 1) {
			msg = "¿Desea eliminar el registro seleccionado?";
		}
		else {
			msg = "¿Desea eliminar los "+$(".table-preventa tbody tr.warning").length+" registros seleccionados?";
		}
		ventana.confirm({
			titulo:"Confirmar"
			,mensaje:msg
			,textoBotonAceptar: "Eliminar"
		}, function(ok){
			if(ok) {
				var mat = [];
				$(".table-preventa tbody tr.warning").each(function() {
					mat.push($(this).data("idpreventa_claro"));
				});
				
				var str = "idpreventa_claro="+mat.join("|");
				
				ajax.post({url:_base_url+"preventa_claro/delete", data:str}, function(data) {
					if(data === true) {
						ventana.alert({titulo:"", mensaje:"Datos eliminados correctamente.", tipo:"success"});
						refrescarTabla();
					}
				});
			}
		});
	}
});

$("#btn-ver").on("click", function(e) {
	e.preventDefault();
	
	var id = false;
	if($(".table-preventa tbody tr.active").length) {
		id = $(".table-preventa tbody tr.active").data("idpreventa_claro");
	}
	else if($(".table-preventa tbody tr.warning").length) {
		id = $(".table-preventa tbody tr.warning:first").data("idpreventa_claro");
	}
	
	if(id !== false) {
		$(".input-control-static").text("");
		$("#idpreventa_claro").val("");
		$(".table-detalle-preventa tbody tr").remove();
		ajax.post({url:_base_url+"preventa_claro/getedit/"+id}, function(res) {
			if(res.cab) {
				$.each(res.cab, function(k, v) {
					if(k == "fecha")
						v = fecha_es(v);
					$(".input-control-static."+k).text(v);
				});
				$("#idpreventa_claro").val(res.cab.idpreventa_claro);
			}
			if(res.det) {
				if(res.det.length) {
					var table = new Table(), v, cls, i, j;
					var cols = ["item", "producto", "unidad", "cantidad", "precio", "importe"];
					for(i in res.det) {
						table.tr();
						for(j in cols) {
							cls = "";
							if(cols[j] == "item")
								v = Number(i) + 1;
							else if(cols[j] == "precio" || cols[j] == "importe") {
								cls = "text-right";
								v = Number(res.det[i][cols[j]]).toFixed(2);
							}
							else
								v = res.det[i][cols[j]];
							table.td(v, {class:cls});
						}
					}
					$(".table-detalle-preventa tbody").html(table.to_string());
				}
			}
			$("#modal-ver-preventa").modal("show");
		});
	}
});

$("#btn-make-preventa").on("click", function(e) {
	e.preventDefault();
	if($.trim($("#idpreventa_claro").val()) == "") {
		ventana.alert({titulo:"", mensaje:"No se puede encontrar la preventa."});
		return false;
	}
	
	sendPreventa([$("#idpreventa_claro").val()], function() {
		$("#modal-ver-preventa").modal("hide");
	});
});

$("#btn-send").on("click", function(e) {
	e.preventDefault();
	var arr = [];
	
	if($(".table-preventa tbody tr.active").length) {
		arr.push(Number($(".table-preventa tbody tr.active").data("idpreventa_claro")));
	}
	
	if($(".table-preventa tbody tr.warning:not(.active)").length) {
		$(".table-preventa tbody tr.warning:not(.active)").each(function() {
			arr.push(Number($(this).data("idpreventa_claro")));
		});
	}
	
	if(arr.length <= 0) {
		ventana.alert({titulo:"", mensaje:"Seleccione las filas que desea enviar para preventa."});
		return false;
	}
	
	arr.sort();
	sendPreventa(arr);
});

function sendPreventa(arr, callback) {
	var str = "idpreventa_claro="+arr.join("|");
	ajax.post({url:_base_url+"preventa_claro/enviar", data:str}, function(res) {
		if(res.length) {
			if(res.length == 1) {
				ventana.confirm({
					titulo: ""
					,mensaje: "El registro ha sido movido al modulo de Preventa. Pedido N&deg; "+res[0].idpreventa
					,textoBotonAceptar: "Ver pedido"
					,textoBotonCancelar: "Aceptar"
				}, function(ok){
					if(ok) {
						abrirPreventa(res[0].idpreventa);
					}
				});
			}
			else {
				ventana.alert({titulo:"", mensaje:res.length+" registros enviados a Preventa. "+
					"Puede consultar en los registros Atendidos o ir al modulo de Preventa."});
			}
		}
		refrescarTabla();
		if($.isFunction(callback))
			callback();
	});
}

$(".table-preventa").on("click", ".btn-ver-preventa", function(e) {
	e.stopPropagation();
	e.preventDefault();
	var id = Number($(this).attr("idp"));
	if(id > 0) {
		abrirPreventa(id);
	}
});

function abrirPreventa(id) {
	var key = "temp2038";
	open_url_tab(_base_url+"preventa/editar/"+id+"/"+key, key, "Preventa "+id, true);
}

(function() {
	$("#idvendedor").trigger("change");
})();