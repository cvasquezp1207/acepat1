$(".table-despacho").on("click", ".btn_view", function(e) {
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

$(".table-despacho").on("click", ".btn_del", function(e) {
	e.preventDefault();
	var self = $(this);
	
	ventana.confirm({titulo:"Confirmar", mensaje:"&iquest;Desea eliminar el despacho?", 
		textoBotonAceptar: "Eliminar"}, function(ok){
		if(ok) {
			model.del(self.data("iddespacho"), function(res) {
				ventana.alert({titulo: "", mensaje: "El despacho se ha eliminado correctamente", tipo:"success"}, function() {
					self.closest("tr").remove();
					set_conteo();
				});
			});
		}
	});
	return false;
});

function set_conteo() {
	if($(".table-despacho tbody tr").length) {
		var c = 0;
		$(".table-despacho tbody tr").each(function() {
			$(".item-count", this).text(++c);
		});
	}
}