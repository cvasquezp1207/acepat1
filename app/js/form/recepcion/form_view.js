$(".table-recepcion").on("click", ".btn_view", function(e) {
	e.preventDefault();
	var tr = $(this).closest("tr");
	var arr = String($(this).data("series")).split("|");
	
	var c = 0, html = '';
	for(var i in arr) {
		c++;
		html += '<tr>';
		html += '<td>'+c+'</td>';
		html += '<td>'+arr[i]+'</td>';
		html += '</tr>';
	}
	$("#table-serie tbody").html(html);
	
	$("#modal-series .modal-title").text($(".producto_desc", tr).text());
	$("#modal-series").modal("show");
	
	return false;
});

$(".table-recepcion").on("click", ".btn_del", function(e) {
	e.preventDefault();
	var self = $(this);
	
	ventana.confirm({titulo:"Confirmar", mensaje:"&iquest;Desea eliminar la recepcion?", 
		textoBotonAceptar: "Eliminar"}, function(ok){
		if(ok) {
			model.del(self.data("idrecepcion"), function(res) {
				ventana.alert({titulo: "", mensaje: "La recepcion se ha eliminado correctamente", tipo:"success"}, function() {
					self.closest("tr").remove();
					set_conteo();
				});
			});
		}
	});
	return false;
});

function set_conteo() {
	if($(".table-recepcion tbody tr").length) {
		var c = 0;
		$(".table-recepcion tbody tr").each(function() {
			$(".item-count", this).text(++c);
		});
	}
}