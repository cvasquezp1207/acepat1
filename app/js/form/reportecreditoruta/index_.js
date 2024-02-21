$('#fechainicio,#fechafin').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	endDate: parseDate(_current_date)
});

$("#ver-pdf").click(function(e){
	e.preventDefault();
	str = $("#parametros").serialize();
	str+= "&sucursal="+$("#idsucursal").val();

	if($("#externo").is(":checked"))
		open_url_windows(_controller+"/imprimir?"+str);
	else
		$("#cuadroReporte").attr("src", _base_url +_controller+ "/imprimir?" + str);
});

$("#ver-carta").click(function(e){
	e.preventDefault();
	str = $("#parametros").serialize();
	str+= "&sucursal="+$("#idsucursal").val();

	open_url_windows(_controller+"/carta?"+str);
});

$("#ver-excel").click(function(e){
	str = $("#parametros").serialize();
	str+= "&sucursal="+$("#idsucursal").val();
	open_url_windows(_controller+"/exportar?"+str);
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