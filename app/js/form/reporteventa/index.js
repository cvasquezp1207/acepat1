var item = 0;
var form = {};

$('#fecha_inicio,#fecha_fin').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	endDate: parseDate(_current_date)
});

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

$("#ver-excel-detallado").click(function(e){
	e.preventDefault();
	str = $("#parametros").serialize();
	str+= "&sucursal="+$("#idsucursal").val();
	str+= "&cliente="+$("#idcliente option:selected").text();
	open_url_windows(_controller+"/exportarDetallado?"+str);
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
