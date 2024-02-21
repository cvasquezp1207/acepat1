$("#modulo").on("change", function() {
	reload_combo("#idtipodocumento", {
		controller: _controller
		,method: "get_tipodocumentos"
		,data: "modulo="+$(this).val()
		,empty: true
		,labelEmpty: "TODOS"
	});
});

$('#fecha_i').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	enableOnReadonly: false,
	endDate: parseDate(_current_date)
});

$('#fecha_f').datepicker({
	todayBtn: "linked",
	keyboardNavigation: false,
	forceParse: false,
	autoclose: true,
	language: 'es',
	enableOnReadonly: false,
	endDate: parseDate(_current_date)
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
	ajax.post({url:_base_url+_controller+"/get_records", data:str}, function(res) {
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
	open_url_windows(_controller+"/imprimir?"+$("#form-data").serialize());
});

$("#btnexcel").on("click", function(e) {
	e.preventDefault();
	open_url_windows(_controller+"/excel?"+$("#form-data").serialize());
});

(function() {
	$("#modulo").trigger("change");
})();