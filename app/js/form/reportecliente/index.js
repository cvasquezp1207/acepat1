var check=["sin_direccion","sin_telefono","sin_dni","sin_ruc","sin_email"];

$("#idubigeo").change(function(e){
	e.preventDefault();
	str = "idubigeo="+$(this).val();
	$("#idzona").html("<option value=''>TODOS</option>");
	ajax.post({url: _base_url+"zona/get_localidad", data: str}, function(res) {
		option="<option value=''>TODOS</option>";
		$(res).each(function(k,v){
			option+="<option value='"+v['idzona']+"'>"+v['zona']+"</option>";
		});
		$("#idzona").html(option);
	});
});

$("#ver-excel").click(function(e){
	e.preventDefault();
	str = $("#parametros").serialize()+"&cliente="+$("#idcliente option:selected").text();
	$.each(check, function(i, val) {
		if($("#"+val).is(':checked'))
			str += "&" + val + "=S";
		else
			str += "&" + val + "=N";
	});
	open_url_windows(_controller+"/exportar?"+str);
});

$("#ver-pdf").click(function(e){
	e.preventDefault();
	var str = $("#parametros").serialize()+"&cliente="+$("#idcliente option:selected").text();
	$.each(check, function(i, val) {
		if($("#"+val).is(':checked'))
			str += "&" + val + "=S";
		else
			str += "&" + val + "=N";
	});
	if($("#externo").is(":checked"))
		open_url_windows(_controller+"/imprimir?"+str);
	else
		$("#cuadroReporte").attr("src", _base_url +_controller+ "/imprimir?" + str);
});

var config = {
        '.chosen-select'           : {},
        '.chosen-select-deselect'  : {allow_single_deselect:true},
        '.chosen-select-no-single' : {disable_search_threshold:10},
        '.chosen-select-no-results': {no_results_text:'Oops, nothing found!'},
        '.chosen-select-width'     : {width:"99%"}
    }
	for (var selector in config) {
        $(selector).chosen(config[selector]);
    }