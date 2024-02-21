var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		// alert(id);
	},
	eliminar: function(id) {
		model.del(id, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
				grilla.reload(_default_grilla);
			});
		});
	},
	imprimir: function() {
		var id = grilla.get_id(_default_grilla);
		if(id != null) {
			open_url(_controller+"/contrato/"+id);
		}
	},
	guardar: function() {
		var data = $("#form_"+_controller).serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				redirect(_controller);
			});
		});
	},
	cancelar: function() {
		
	}
};

function formatoFechaGrilla(nRow, aData, iDisplayIndex) {
	$("td:eq(0)",nRow).html(fecha_es(aData.fecha_credito, false));
}
// $(document).ready(function(){
	// grilla.isNumericGrid([6]);
// });