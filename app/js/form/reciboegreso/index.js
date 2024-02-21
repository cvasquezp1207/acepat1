var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		// alert(id);
	},
	eliminar: function(id) {
		setTimeout(function() {
			ventana.prompt({titulo:"",
				mensaje:"<div style='text-align:left;margin-bottom:6px;'>&iquest;Por qu&eacute; esta eliminando el registro?.</div>",
				tipo: false,
				textoBotonAceptar: "Eliminar",
				placeholder: 'Ingrese motivo'
			}, function(inputValue) {
				if(inputValue !== false) {
					model.del(id, function(res) {
						ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
							grilla.reload(_default_grilla);
						});
					}, _controller, "eliminar", "motivo="+inputValue);
				}
			});
		}, 300);
	},
	imprimir: function() {
		var id = grilla.get_id(_default_grilla);
		if(id != null) {
			// alert(id);
		}
	},
	guardar: function(datos_aux) {
		var data = $("#form_"+_controller).serialize();
			// data+= "&"+$("#form_more").serialize()
			if(datos_aux) {
				data += "&"+datos_aux;
			}

		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect(_controller);
			});
		});
	},
	cancelar: function() {
		
	}
};

// validate();

// $("#descripcion").letras({'permitir':' '});
$("#monto,.numero").numero_real();
$("#serie_doc,#numero_doc").numero_entero();

$("#btn-anular").click(function(e) {
	e.preventDefault();
	
	var data = grilla.get_data(_default_grilla);
	if(data != null) {
		if(data.estado == "I") {
			/* ventana.confirm({
				titulo:"",
				mensaje:"<div style='text-align:left;margin-bottom:6px;'>El Recibo de Egreso <b>"+data.nro_recibo+"</b> "+
					"se encuentre actualmente anulado &iquest;Desea Restaurar el Recibo de Egreso?</div>",
				tipo: false,
				textoBotonAceptar: "Restaurar"
			}, function(ok){
				if(ok) {
					ajax.post({url: _base_url+_controller+"/restaurar/"+data.idreciboegreso}, function(res) {
						ventana.alert({titulo: "En horabuena!", mensaje: "Recibo de Egreso Restaurado correctamente", tipo:"success"}, function() {
							grilla.reload(_default_grilla);
						});
					});
				}
			}); */
			ventana.alert({titulo: "", mensaje: "El Recibo de Egreso "+data.nro_recibo+" se encuentra anulado."});
		}
		else {
			ventana.prompt({titulo:"",
				mensaje:"<div style='text-align:left;margin-bottom:6px;'><b>&iquest;Desea Anular el Recibo de Egreso "+
					data.nro_recibo+"...?</b></div>",
				tipo: false,
				textoBotonAceptar: "Anular",
				placeholder: 'Ingrese algun motivo'
			}, function(inputValue){
				if(inputValue === false)
					return false;
				
				ajax.post({url: _base_url+_controller+"/anular", data:"idreciboegreso="+data.idreciboegreso+"&motivo="+inputValue}, function(res) {
					ventana.alert({titulo: "En horabuena!", mensaje: "Recibo de Egreso Anulado correctamente", tipo:"success"}, function() {
						grilla.reload(_default_grilla);
					});
				});
			});
		}
	}
	else {
		
	}
	
	return false;
});

function callbackRE(nRow, aData, iDisplayIndex) {
	$('td:eq(4)', nRow).html(fecha_es(aData['fecha']));
	$('td:eq(5)', nRow).html("<div style='text-align:right;'>"+aData['monto']+"</div>");
	
	if(aData.estado == "I")
		$(nRow).addClass("danger");
}