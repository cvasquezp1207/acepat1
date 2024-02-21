var form={};
if (typeof prefix_sistema === 'undefined') {
    prefix_sistema='';
}
if(typeof form.guardar_sistema != 'function') {
	form.guardar_sistema = function() {
		var data = $("#form_sistema").serialize();
		model.save(data, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
				reload_sistema(res);
				$("#modal-sistema").modal("hide");
			});
		}, "sistema");
	}
}

$("#modal-sistema").on('shown.bs.modal', function () {
	$('#mar_descripcion').focus();
});

$("#modal-sistema").on('hidden.bs.modal', function () {
	clear_form("#form_sistema");
});

validate("#form_sistema", form.guardar_sistema);

$("a#select_icon"+prefix_sistema).click(function() {
	var icon = $(this).data("icon");
	setIconS(icon);
	$("#image"+prefix_sistema).val(icon);
});

$("#image"+prefix_sistema).blur(function() {
	setIconS($(this).val());
});

$("#modal-sistema #btn_cancel").click(function() {
	clear_form("#form_sistema");
	$("#modal-sistema").modal("hide");
});

function setIconS(icon){
	$("#icono_preview"+prefix_sistema).html('<i class="fa '+icon+'"></i>');
}

function reload_sistema(data){
	ajax.post({url: _base_url+"sistema/get_all", data: ''}, function(res) {
		html = "<option>Seleccione...</option>";
		$(res).each(function(i,j){
			selected = '';
			if(j.idsistema==data.idsistema)
				selected = 'selected';
			html+="<option value='"+j.idsistema+"' "+selected+">"+j.sistema+"</option>";
		});

		$("#idsistema").html(html);
		$("#idsistema").trigger("change");
	});
}