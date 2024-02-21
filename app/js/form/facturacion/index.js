var ListaSituacion = [
	{"id":"01","nombre":"Por Generar XML"},
    {"id":"02","nombre":"XML Generado"},
    {"id":"03","nombre":"Enviado y Aceptado SUNAT"},
    {"id":"04","nombre":"Enviado y Aceptado SUNAT con Obs."},
    {"id":"05","nombre":"Enviado y Anulado por SUNAT"},
    {"id":"06","nombre":"Con Errores"},
    {"id":"07","nombre":"Por Validar XML"},
    {"id":"08","nombre":"Enviado a SUNAT Por Procesar"},
    {"id":"09","nombre":"Enviado a SUNAT Procesando"},
    {"id":"10","nombre":"Rechazado por SUNAT"}
];

var TipoComprobante = [
    {"id":"01","nombre":"Factura"},
    {"id":"03","nombre":"Boleta de Venta"},
    {"id":"07","nombre":"Nota de Credito"},
    {"id":"08","nombre":"Nota de Debito"},
    {"id":"RA","nombre":"Comunicación de Baja"}
];

function getSituacion(v) {
	for(var i in ListaSituacion) {
		if(ListaSituacion[i].id == v)
			return ListaSituacion[i].nombre;
	}
	
	return "-";
}

function getComprobante(v) {
	for(var i in TipoComprobante) {
		if(TipoComprobante[i].id == v)
			return TipoComprobante[i].nombre;
	}
	
	return "-";
}

function updateRow(tr, data) {
	$("td:eq(0)", tr).text(data.num_ruc);
	$("td:eq(1)", tr).text(getComprobante(data.tip_docu));
	$("td:eq(2)", tr).text(data.num_docu);
	$("td:eq(4)", tr).text(data.fec_carg);
	$("td:eq(5)", tr).text(data.fec_gene);
	$("td:eq(6)", tr).text(data.fec_envi);
	$("td:eq(7)", tr).text(getSituacion(data.ind_situ));
	$("td:eq(8)", tr).text(data.des_obse);
}

function getRecords(page) {
	if(typeof page == "undefined")
		page = 0;
	var str = "page=" + page + "&"+$("#form-data").serialize();
	ajax.post({url:_base_url+"facturacion/get_records", data:str}, function(res) {
		if(res.page <= 0)
			$("#tabla-result tbody").html(res.html);
		else
			$("#tabla-result tbody").append(res.html);
		$("#tabla-result").data("more", res.more);
		$("#tabla-result").data("page", res.page);
	});
}

function init() {
	getRecords();
}

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

$("select").change(function(e) {
	$("#btnsearch").trigger("click");
});

$("#btnsearch").click(function(e) {
	e.preventDefault();
	getRecords();
});

$("#tabla-result").on("click", "tbody tr", function() {
	if($(this).hasClass("empty-rs"))
		return;
	
	if($(this).hasClass("active"))
		$("#tabla-result tbody tr").removeClass("active");
	else {
		$("#tabla-result tbody tr").removeClass("active");
		$(this).addClass("active");
	}
});

$("#btnupdate").click(function(e) {
	e.preventDefault();
	if($("#tabla-result tbody tr.active").length <= 0) {
		ventana.alert({titulo:"", mensaje:"Seleccione un registro de la tabla."});
		return;
	}
	
	var tr = $("#tabla-result tbody tr.active");
	var str = "idreferencia="+tr.data("idref")+"&referencia="+tr.data("ref");
	
	ajax.post({url:_base_url+"facturacion/update", data: str}, function(res) {
		updateRow(tr, res);
	});
	
	ajax.post({url:_base_url+"facturacion/update"}, function(res) {
		updateRow(tr, res);
	});
});

$("#btnprint").click(function(e) {
	e.preventDefault();
	if($("#tabla-result tbody tr.active").length <= 0) {
		ventana.alert({titulo:"", mensaje:"Seleccione un registro de la tabla."});
		return;
	}
	
	var tr = $("#tabla-result tbody tr.active");
	open_url(tr.data("ref")+"/imprimir/"+tr.data("idref"));
});

$("#btngenerar").click(function(e) {
	e.preventDefault();
	if($("#tabla-result tbody tr.active").length <= 0) {
		ventana.alert({titulo:"", mensaje:"Seleccione un registro de la tabla."});
		return;
	}
	
	var tr = $("#tabla-result tbody tr.active");
	var str = "idreferencia="+tr.data("idref")+"&referencia="+tr.data("ref");
	
	ajax.post({url:_base_url+"facturacion/generar", data: str}, function(res) {
		updateRow(tr, res);
	});
});

$("#btnsend").click(function(e) {
	e.preventDefault();
	if($("#tabla-result tbody tr.active").length <= 0) {
		ventana.alert({titulo:"", mensaje:"Seleccione un registro de la tabla."});
		return;
	}
	
	var tr = $("#tabla-result tbody tr.active");
	var str = "idreferencia="+tr.data("idref")+"&referencia="+tr.data("ref");
	
	ajax.post({url:_base_url+"facturacion/send", data: str}, function(res) {
		updateRow(tr, res);
	});
});

$("#btnbaja").click(function(e) {
	e.preventDefault();
	if($("#tabla-result tbody tr.active").length <= 0) {
		ventana.alert({titulo:"", mensaje:"Seleccione un registro de la tabla."});
		return;
	}
	
	var tr = $("#tabla-result tbody tr.active");
	
	if(tr.data("tdoc") != "RA") {
		ventana.prompt({titulo:"",
			mensaje:"&iquest;Desea dar de baja al comprobante "+$("td:eq(2)",tr).text()+"?",
			tipo: false,
			textoBotonAceptar: "Dar de baja",
			placeholder: 'Ingrese algun motivo'
		}, function(inputValue){
			if(inputValue === false)
				return false;
			
			if (inputValue === "") {
				swal.showInputError("Ingrese el motivo para dar de baja");
				return false
			}
			
			var str = "idreferencia="+tr.data("idref")+"&referencia="+tr.data("ref")+"&motivo="+inputValue;
			
			ajax.post({url:_base_url+"facturacion/baja", data: str}, function(res) {
				getRecords();
			});
		});
	}
	else {
		ventana.alert({titulo:'',mensaje:'Seleccione el comprobante que desea dar de baja.'});
	}
});

$(window).on("scroll", function() {
	if($(window).scrollTop() + $(window).height() == $(document).height()) {
		if($("#tabla-result").data("more") == true) {
			var p = $("#tabla-result").data("page") + 1;
			getRecords(p);
		}
	}
});

init();

$("#btnconfig").on("click", function(e) {
	e.preventDefault();
	$("#usar_temporizador").trigger("change");
	$("#modal-config").modal("show");
});

$("#usar_temporizador").on("change", function() {
	var b = !($(this).val() == "S");
	$("#minutos").prop("readonly", b);
});

$("#btn-guardar-temporizador").on("click", function(e) {
	e.preventDefault();
	if($("#usar_temporizador").required()) {
		if(intervalPid != null) {
			clearInterval(intervalPid);
		}
		if($("#usar_temporizador").val() == "S") {
			if($("#minutos").required({numero:true,tipo:"int"})) {
				// var t = Number($("#minutos").val()) * 1000 * 60; // minutos
				var t = Number($("#minutos").val()) * 1000; // segundos
				intervalPid = setInterval(enableTemporizador, Math.round(t));
			}
		}
		$("#modal-config").modal("hide");
	}
});

var intervalPid = null;

function enableTemporizador() {
	/*ajax.post({url:_base_url+"facturacion/buscar"}, function(res) {
		if(res.idreferencia && res.referencia) {
			if($("#tabla-result tbody tr[data-idref='"+res.idreferencia+"'][data-ref='"+res.referencia+"']").length) {
				var tr = $("#tabla-result tbody tr[data-idref='"+res.idreferencia+"'][data-ref='"+res.referencia+"']");
				updateRow(tr, res);
			}
		}
	});*/
	
	
	ajax.post({url:_base_url+"facturacion/buscar"}, function(res) {
		if(res.idreferencia && res.referencia) {
			if($("#tabla-result tbody tr[data-idref='"+res.idreferencia+"'][data-ref='"+res.referencia+"']").length) {
				var tr = $("#tabla-result tbody tr[data-idref='"+res.idreferencia+"'][data-ref='"+res.referencia+"']");
				ajax.post({url:_base_url+"facturacion/generar", data: str}, function(res) {
					updateRow(tr, res);
					});
			}
		}
	});
	
	
	
	
	
	
	
}