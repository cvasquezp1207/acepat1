var form = {
	nuevo: function() {
		
	},
	editar: function(id) {
		// alert(id);
	},
	eliminar: function(id) {
		
	},
	imprimir: function() {
		// alert("ddd");
		var id = grilla.get_id(_default_grilla);
		if(id != null) {
			open_url_windows(_controller+"/OC_pedido/"+id);
		}
	}
};