if(typeof form == 'undefined') {
	form = {};
}

$("#form_recepcion").submit(function() {
	return false;
});

function detele_recepcion(indice) {
	idcompra = $("#idcompra").val();
	kardex = $("#kardex"+indice).val();
	alma =   $("#alma"+indice).val();
	produc = $("#produc"+indice).val();
	tipo_docu = $("#tipo_docu"+indice).val();
	serie = $("#serie"+indice).val();
	numero = $("#numero"+indice).val();
	
	ventana.confirm({titulo:"Confirmar",
		mensaje:"¿Desea eliminar el registro seleccionado?",
		textoBotonAceptar: "Eliminar"}, function(ok){
			if(ok) {
				str = "idcompra="+idcompra+"&kardex="+kardex+"&alma="+alma+"&produc="+produc+"&tipo_docu="+tipo_docu+"&serie="+serie+"&numero="+numero;
				form.eliminar_recepcion(str);
			}
		});
}