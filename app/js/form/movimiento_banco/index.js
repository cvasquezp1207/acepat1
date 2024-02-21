	$("#idbanco").trigger("change");
	$('#fechainicio,#fechafin').datepicker({
		todayBtn: "linked",
		keyboardNavigation: false,
		forceParse: false,
		autoclose: true,
		language: 'es',
	});
	
	$("#idbanco").change(function(e){
		// if($.isNumeric($(this).val())) {
			ajax.post({url: _base_url+_controller+"/get_cuentasb", data:"idbanco="+$.trim($(this).val())+"&idmoneda="+$.trim($("#idmoneda").val())}, function(res) {
				html = "";
				$(res).each(function(k,v){
					html+="<option value='"+v['idcuentas_bancarias']+"'>"+v['cuenta']+"</option>";
				});
				$("#idcuentas_bancarias").html(html);
			});
			return;
		// }
	});
	
	$("#idmoneda").change(function(e){
		// if($.isNumeric($(this).val())) {
			ajax.post({url: _base_url+_controller+"/get_cuentasb", data:"idbanco="+$.trim($("#idbanco").val())+"&idmoneda="+$.trim($(this).val())}, function(res) {
				html = "";
				$(res).each(function(k,v){
					html+="<option value='"+v['idcuentas_bancarias']+"'>"+v['cuenta']+"</option>";
				});
				$("#idcuentas_bancarias").html(html);
			});
			return;
		// }
	});
	
	$("#ver-pdf").click(function(e){
		e.preventDefault();
		str = $("#parametros").serialize();
		
		if($("#externo").is(":checked"))
			open_url_windows(_controller+"/imprimir?"+str);
		else
			$("#cuadroReporte").attr("src", _base_url +_controller+ "/imprimir?" + str);
	});
		
	// $("#ver-excel").click(function(e){
		// e.preventDefault();
		// str = $("#parametros").serialize();
		
		// open_url_windows(_controller+"/exportar?"+str);
	// });