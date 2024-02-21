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
			open_url(_controller+"/imprimir/"+id);
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

function formatoGrilla(nRow, aData, iDisplayIndex) {
	$('td:eq(6)', nRow).html("<div style='text-align:right;'>"+number_format(aData['monto'],2,'.',',')+"</div>");
	$('td:eq(7)', nRow).html("<div style='text-align:right;'>"+number_format(aData['monto_cancelado'],2,'.',',')+"</div>");
	$('td:eq(8)', nRow).html("<div style='text-align:right;'>"+number_format(aData['monto_deuda'],2,'.',',')+"</div>");
}

$("select[filter]").on("change", function() {
	if($(this).val() == "") {
		grilla.del_filter(_default_grilla, $(this).attr("filter"));
	}
	else {
		grilla.set_filter(_default_grilla, $(this).attr("filter"), "=", $(this).val());
	}
	grilla.reload(_default_grilla);
});