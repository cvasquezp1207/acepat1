$("#operacion").on("change", function() {
	// $(".panel-body :checkbox").prop("checked", false);
	$(".panel-body :input").prop("disabled", true);
	
	if($(this).val() == "A")
		$(".panel-body :input").prop("disabled", false);
	else if($(this).val() == "I")
		$(".panel-body-ingreso :input").prop("disabled", false);
	else if($(this).val() == "S")
		$(".panel-body-salida :input").prop("disabled", false);
});

$("#ingreso_buscar_guia").on("change", function() {
	var bool = ! $(this).is(":checked");
	if(bool) {
		$("#ingreso_b_esta_sede,#ingreso_b_otra_sede")
			.prop("disabled", false).prop("checked", false);
	}
	$("#ingreso_b_esta_sede,#ingreso_b_otra_sede").prop("disabled", bool);
});

$("#btn_save").on("click", function(e) {
	e.preventDefault();
	if($("#descripcion").required() && $("#operacion").required())
		form.guardar();
	return false;
});

function init() {
	$("#operacion").trigger("change");
	$("#descripcion").focus();
}

init();