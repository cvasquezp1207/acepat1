var form = {
	nuevo: function() {

	},
	editar: function(id) {
		
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
	imprimir: function(id) {
		var id = id||grilla.get_id(_default_grilla);
		if(id != null) {
			window.open(_base_url+'notacredito/imprimir/'+id,'_blank');
			location.reload();
		}else{
			
		}
	},
	guardar: function(datos_aux) {
		var data = $("#form_"+_controller).serialize();
		if(datos_aux) {
			data += "&"+datos_aux;
		}
		model.save(data, function(res) {
			// ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				if(_es_nueva_nota_){
					form.imprimir(res.idnotacredito);
				}else{
					redirect(_controller);
				}
			// });
		});
	},
	cancelar: function() {

	}
};

$("#btn-anular").click(function(e) {
	e.preventDefault();
	
	var data = grilla.get_data(_default_grilla);
	if(data != null) {
		if(data.estado == "I") {
			ventana.confirm({
				titulo:"",
				mensaje:"<div style='text-align:left;margin-bottom:6px;'>La Nota de Credito <b>"+data.nrodocumento+"</b> "+
					"se encuentre actualmente anulado &iquest;Desea Restaurar la Nota de Credito?</div>",
				tipo: false,
				textoBotonAceptar: "Restaurar"
			}, function(ok){
				if(ok) {
					ajax.post({url: _base_url+"notacredito/restaurar/"+data.idnotacredito}, function(res) {
						ventana.alert({titulo: "En horabuena!", mensaje: "Nota de Credito Restaurado correctamente", tipo:"success"}, function() {
							grilla.reload(_default_grilla);
						});
					});
				}
			});
		}
		else {
			ventana.prompt({titulo:"",
				mensaje:"<div style='text-align:left;margin-bottom:6px;'><b>&iquest;Desea Anular la Nota de Credito "+
					data.nrodocumento+"...?</b></div>",
				tipo: false,
				textoBotonAceptar: "Anular",
				placeholder: 'Ingrese algun motivo'
			}, function(inputValue){
				if(inputValue === false)
					return false;
				
				ajax.post({url: _base_url+"notacredito/anular", data:"idnotacredito="+data.idnotacredito+"&motivo="+inputValue}, function(res) {
					ventana.alert({titulo: "En horabuena!", mensaje: "Nota de Credito Anulado correctamente", tipo:"success"}, function() {
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

function checkAnulacion(nRow, aData, iDisplayIndex) {
	if(aData.estado == "I")
		$(nRow).addClass("danger");
}