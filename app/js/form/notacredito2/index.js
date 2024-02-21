var form = {
	nuevo: function() {

	},
	editar: function(id) {
		
	},
	eliminar: function(id) {
		model.del(id, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
				grilla.reload(_default_grilla);
			});
		});
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

function formatoFechaGrilla(nRow, aData, iDisplayIndex) {
	$("td:eq(0)",nRow).html(fecha_es(aData.fecha));
}