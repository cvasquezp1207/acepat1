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
$('#fechainicio,#fechafin').datepicker({
    todayBtn: "linked",
    keyboardNavigation: false,
    forceParse: false,
    autoclose: true,
    language: 'es',
    endDate: parseDate(_current_date)
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
    if(typeof page == "undefined")
        page = 0;
    

	var class_fa 	= $("thead tr th div i.fa").not("thead tr th div i.fa.fa-sort").attr("class");
		icon_fa 	= class_fa.split(" ");
		argument	= icon_fa[1].split("-");
	
	var sort	 	= argument[2];
	var campo		= $("thead tr th div i.fa").not("thead tr th div i.fa.fa-sort").parent(".pull-right").parent("th").find("a.sorting").attr("data-sort");
	
	var str = "page=" + page + "&" + $("#form-data").serialize();
		str+= "&sort="+sort;
		str+= "&campo="+campo;
	
    ajax.post({url:_base_url+"reporteutil/get_records", data:str}, function(res) {
        if(res.page <= 0)
            $("#tabla-result tbody").html(res.html);
        else
            $("#tabla-result tbody").append(res.html);
        $("#tabla-result").data("more", res.more);
        $("#tabla-result").data("page", res.page);
		
		totales();
    });
}

// $(window).on("scroll", function() {
    // if($(window).scrollTop() + $(window).height() == $(document).height()) {
        // if($("#tabla-result").data("more") == true) {
            // var p = $("#tabla-result").data("page") + 1;
            // getRecords(p);
        // }
    // }
// });

$("#btnpdf").on("click", function(e) {
    e.preventDefault();
    open_url_windows("reporteutil/imprimir?"+$("#form-data").serialize());
});

$("#btnexcel").on("click", function(e) {
    e.preventDefault();
    open_url_windows("reporteutil/excel?"+$("#form-data").serialize());
});

$("thead tr th").on("click",function(e){
	var icon = $(this).find("i.fa");
	var sort = icon.attr("class");

	$("thead tr th div i.fa").not(this).removeClass("fa-sort-asc").removeClass("fa-sort-desc").addClass("fa-sort");

	if(sort == "fa fa-sort-asc"){
		icon.removeClass("fa-sort").removeClass("fa-sort-asc").addClass("fa-sort-desc");
	}else if(icon=="fa fa-sort-desc"){
		icon.removeClass("fa-sort").removeClass("fa-sort-desc").addClass("fa-sort-asc");
	}else{
		icon.removeClass("fa-sort").removeClass("fa-sort-desc").addClass("fa-sort-asc");
	}
	
	getRecords();
});

function totales(){
	var total_c = total_i = total_cv = total_u = 0;
	$(".fila").each(function(){
		key = $(this).attr("data-td");
		
		if(key=='cantidad')
			total_c+=parseFloat($(this).html());
		else if(key=='importe')
			total_i+=parseFloat($(this).html());
		else if(key=='costoventa')
			total_cv+=parseFloat($(this).html());
		else if(key=='utilidad')
			total_u+=parseFloat($(this).html());
		// else if(key=='porcentaje')
			// total_p+=parseFloat($(this).html());
	});
	total_p = total_u*100/total_cv;
	$(".total_cantidad").html(total_c.toFixed(2));
	$(".total_importe").html(total_i.toFixed(2));
	$(".total_costoventa").html(total_cv.toFixed(2));
	$(".total_utilidad").html(total_u.toFixed(2));
	$(".total_porcentaje").html(total_p.toFixed(2));
}