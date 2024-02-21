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
			window.open(_base_url+'/guiaremision/imprimir/'+id,'_blank');
		}
	},
	guardar: function() {
		var data = $("#form_"+_controller).serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				// redirect(_controller);
				if( $("idguia_remision").val()=='' ){//es nueva guia
						setTimeout(function() {
							ventana.confirm({
								titulo: "Imprimir"
								,mensaje: "¿Desea Imprimir El Comprobante...??"
								,textoBotonAceptar: "SI"
								,textoBotonCancelar: "Mas tarde"
								,cerrarConTeclaEscape: false
							}, function(isOk) {
								if(isOk) {
									form.imprimir(res.idguia_remision);
								}
								redirect(_controller);
							});
						}, 250);
					}else{
					    redirect(_controller);
					}
			});
		});
	},
	cancelar: function() {

	}
};

$("select[filter]").on("change", function() {
	grilla.set_filter(_default_grilla, $(this).attr("filter"), "=", $(this).val());
	grilla.reload(_default_grilla);
});

$("#btn-ingreso, #btn-salida").click(function(e) {
	e.preventDefault();
	if(this.id == "btn-ingreso")
		redirect(_controller+"/ingreso");
	else
		redirect(_controller+"/salida");
});

$("#btn-anular").click(function(e) {
	e.preventDefault();
	
	var data = grilla.get_data(_default_grilla);
	if(data != null) {
		if(data.estado == "I") {
			ventana.confirm({
				titulo:"",
				mensaje:"<div style='text-align:left;margin-bottom:6px;'>La Guia de Remision <b>"+data.nroguia+"</b> "+
					"se encuentre actualmente anulado &iquest;Desea Restaurar la Guia de Remision?</div>",
				tipo: false,
				textoBotonAceptar: "Restaurar"
			}, function(ok){
				if(ok) {
					ajax.post({url: _base_url+"guiaremision/restaurar/"+data.idguia_remision}, function(res) {
						ventana.alert({titulo: "En horabuena!", mensaje: "Guia de remision Restaurado correctamente", tipo:"success"}, function() {
							grilla.reload(_default_grilla);
						});
					});
				}
			});
		}
		else {
			ventana.prompt({titulo:"",
				mensaje:"<div style='text-align:left;margin-bottom:6px;'><b>&iquest;Desea Anular la Guia de Remision "+
					data.nroguia+"...?</b></div>",
				tipo: false,
				textoBotonAceptar: "Anular",
				placeholder: 'Ingrese algun motivo'
			}, function(inputValue){
				if(inputValue === false)
					return false;
				
				ajax.post({url: _base_url+"guiaremision/anular", data:"idguia_remision="+data.idguia_remision+"&motivo="+inputValue}, function(res) {
					ventana.alert({titulo: "En horabuena!", mensaje: "Guia de remision Anulado correctamente", tipo:"success"}, function() {
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