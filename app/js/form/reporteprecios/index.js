input.autocomplete({
	selector: "#producto"
	,controller: "producto"
	,method: "autocomplete"
	,label: "<strong>[codigo_producto]</strong>| [descripcion_detallada]"
	,value: "[descripcion_detallada]"
	,highlight: true
	,appendTo: $("#producto").closest("form")
	,onSelect: function(item) {
		$("#idproducto").val(item.idproducto);
	}
});

$("#all_producto").on("change", function() {
	var b = $(this).is(":checked");
	if(b) {
		$("#idproducto,#producto").val("");
	}
	$("#producto").prop("readonly", b);
});

$("#btnsearch").on("click", function(e) {
	e.preventDefault();
	getRecords();
});

function getRecords(page) {
	if(_ajax_load)
		return;
	_ajax_load = true;
	if(typeof page == "undefined")
		page = 0;
	var str = "page=" + page + "&" + $("#form-data").serialize();
	ajax.post({url:_base_url+"reporteprecios/get_records", data:str}, function(res) {
		if(res.page <= 0)
			$("#tabla-result tbody").html(res.html);
		else
			$("#tabla-result tbody").append(res.html);
		$("#tabla-result").data("more", res.more);
		$("#tabla-result").data("page", res.page);
		_ajax_load = false;
	});
}

var _ajax_load = false;

$(window).on("scroll", function() {
	if($(window).scrollTop() + $(window).height() >= $(document).height() - 100) {
		if($("#tabla-result").data("more") == true) {
			var p = $("#tabla-result").data("page") + 1;
			getRecords(p);
		}
	}
});

$("#btnpdf").on("click", function(e) {
	e.preventDefault();
	open_url_windows("reporteprecios/imprimir?"+$("#form-data").serialize());
});

$("#btnexcel").on("click", function(e) {
	e.preventDefault();
	open_url_windows("reporteprecios/excel?"+$("#form-data").serialize());
});