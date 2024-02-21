$("#idcategoria").chosen();
$("#idmarca").chosen();
$("#idmodelo").chosen();
$("#idalmacen").chosen();

$(document).on('click','.odd,.even',function(){
	_tr = $(this);
	selector_tr = _tr.find('td').find('input.pk_index') ;
	$div_ = $('.ver_detalle_producto').find('i.fa');
	// $(".panel_detalles").hide();
	if( _tr.hasClass('active') ){
		if($div_.hasClass("fa-angle-double-up")){
			str= 'idproducto='+selector_tr.val();
			
			ajax.post({url: _base_url+"consultarproducto/cargar_producto",data:str}, function(datos) {
				$(".prod_cat").html(MaysPrimera((datos.categoria).toLowerCase()));
				$(".prod_descr").html(MaysPrimera((datos.producto_detallado).toLowerCase()));
				$(".prod_codb").html(datos.codigo_barras);
				$(".prod_peso").html(((datos.peso) ? datos.peso : '0.00')+' Kg');
				$(".prod_tam").html(datos.tamanio+' Mtr');
				funciones_datos(datos.idproducto);
				$(".panel_detalles").show();
			});
		}
	}else{
		$(".panel_detalles").hide();
		
		$(".ver_detalle_producto i.fa").addClass("fa-angle-double-down").removeClass("fa-angle-double-up").attr({'data-original-title':'Mostrar detalles de producto'});
		$(".panel_detalles").hide();
	}
});

$(document).on('click','.change_panel',function(){
	$(".panel_detalles").hide();
	if( $("#panel_lista_producto").hasClass('panel_active') ){
		$("#panel_lista_producto").removeClass('panel_active').hide();
		
		$("#panel_imagen_producto").addClass('panel_active').show();
		cargarPanelimagen();
	}else{
		$("#panel_lista_producto").addClass('panel_active').show();
		
		$("#panel_imagen_producto").removeClass('panel_active').hide();
		filtrar_grid();
		$('.ver_detalle_producto i.fa').addClass("fa-angle-double-down").removeClass("fa-angle-double-up").attr({'data-original-title':'Mostrar detalles de producto'});
	}
});

$(document).on('click','.info_prod',function(){
	$idproducto = $(this).attr('ajax-prod');
	
	cargar_modal_detalle($idproducto);
});

$(".ver_detalle_producto").click(function(e){
	$div_ = $(this).find('i.fa');
	_tr = $("#dtview_stock tbody tr.active");
	if( $('.odd,.even').hasClass('active') ){
		selector_tr = _tr.find('td').find('input.pk_index') ;
		
		
		if($div_.hasClass("fa-angle-double-down")){
			str= 'idproducto='+selector_tr.val();
			ajax.post({url: _base_url+"consultarproducto/cargar_producto",data:str}, function(datos) {
				$(".prod_cat").html(MaysPrimera((datos.categoria).toLowerCase()));
				$(".prod_descr").html(MaysPrimera((datos.producto_detallado).toLowerCase()));
				$(".prod_codb").html(datos.codigo_barras);
				$(".prod_peso").html(((datos.peso) ? datos.peso : '0.00')+' Kg');
				$(".prod_tam").html(datos.tamanio+' Mtr');
				
				funciones_datos(datos.idproducto);

				$div_.removeClass("fa-angle-double-down").addClass("fa-angle-double-up").attr({'data-original-title':'Ocultar detalles de producto'});
				$(".panel_detalles").show();
			});			
		}else{
			$div_.addClass("fa-angle-double-down").removeClass("fa-angle-double-up").attr({'data-original-title':'Mostrar detalles de producto'});
			$(".panel_detalles").hide();
		}	
	}else{
		if($div_.hasClass("fa-angle-double-down")){
			ventana.alert({titulo: "Hey..!!", mensaje: "Debe seleccionar una fila del producto que desea ver los detalles.."});
		}else{
			$div_.addClass("fa-angle-double-down").removeClass("fa-angle-double-up").attr({'data-original-title':'Mostrar detalles de producto'});
		}
	}
})

/* FILTROS PARA LOS PANELES */
$("#search").keyup(function(e){
	filtrar_grid( $(this) );
});

$("#filter").change(function(e){
	filtrar_grid( $(this) );
});

$("#idcategoria").change(function(e){
	filtrar_grid($(this));
});

$("#idmarca").change(function(e){
	filtrar_grid($(this));
});

$("#idmodelo").change(function(e){
	filtrar_grid($(this));
});

$("#idalmacen").change(function(e){
	filtrar_grid($(this));
});

//Falta la funcion para añadir mas elementos en el panel de imagenes 
/* FILTROS PARA LOS PANELES */

function callbackStock(nRow, aData, iDisplayIndex){
	$('td:eq(0)', nRow).html("<div ><input class='pk_index' type='hidden' value='"+aData['idproducto']+"'>"+aData['producto']+"</div>");
	$('td:eq(4)', nRow).html("<div style='text-align:right;'>"+parseFloat(aData['precio_costo']).toFixed(2)+"</div>");
	$('td:eq(5)', nRow).html("<div style='text-align:right;'>"+parseFloat(aData['precio_venta']).toFixed(2)+"</div>");
	$('td:eq(6)', nRow).html("<div style='text-align:center;'>"+aData['stock']+"</div>");
	$(".panel_detalles").hide();
}

function cargarSeries(idproducto){
	str= 'idproducto='+idproducto+'&idalmacen='+$("#idalmacen").val();
	ajax.post({url: _base_url+"consultarproducto/cargar_series",data:str}, function(res) {
		$(".prod_serie").empty();
		html = '';
		$(res).each(function(i,j){
			html+="<option value='"+j+"'>"+j.serie+"</option>";
		})
		$(".list_serie").html(html);
	});
}

function cargarCarrusel(idproducto){
	str= 'idproducto='+idproducto;
	ajax.post({url: _base_url+"consultarproducto/cargar_carrusel",data:str}, function(res) {
		$(".list_carrusel").empty();
		html = '<div class="carousel slide" id="carousel1">';
		if(res.length){
			html+='<div class="carousel-inner">';
			$(res).each(function(i,j){
				clase = '';
				if(j.es_principal=='S'){
					clase = 'active';
				}
				html+='<div class="item '+clase+'">';
				html+='	<img alt="image" class="img-responsive" src="app/img/producto/'+j.idproducto+'/'+j.imagen_producto+'"/>';
				html+='</div>';
			});
			html+='</div>';
			if(res.length>1){
				html+='<a data-slide="prev" href="#carousel1" class="left carousel-control">';
				html+='	<span class="icon-prev"></span>';
				html+='</a>';
				
				html+='<a data-slide="next" href="#carousel1" class="right carousel-control">';
				html+='	<span class="icon-next"></span>';
				html+='</a>';
			}
		}
		html+='</div>';
		$div__ = $(".list_carrusel");
		if( !$("#panel_lista_producto").hasClass('panel_active') ){
			$div__ = $("#modal_producto .list_carrusel");
		}
		$div__.html(html);
	});
}

function cargarStockUM(idproducto){
	str = 'idproducto='+idproducto;
	str+= '&idalmacen='+$("#idalmacen").val();;
	$(".list_stock").empty()
	ajax.post({url: _base_url+"consultarproducto/stock_um",data:str}, function(res) {
		$(".list_stock").html(res)
	});
}

function cargarListaprecios(idproducto){
	str = 'idproducto='+idproducto;
	str+= '&idalmacen='+$("#idalmacen").val();;
	$(".list_precios").empty()
	ajax.post({url: _base_url+"consultarproducto/lista_precios",data:str}, function(res) {
		$(".list_precios").html(res)
	});
}

function cargar_modal_detalle(idproducto){
	str= 'idproducto='+idproducto;
	ajax.post({url: _base_url+"consultarproducto/cargar_producto",data:str}, function(datos) {
		$(".prod_cat").html(MaysPrimera((datos.categoria).toLowerCase()));
		$(".prod_descr").html(MaysPrimera((datos.producto_detallado).toLowerCase()));
		$(".prod_codb").html(datos.codigo_barras);
		$(".prod_peso").html(((datos.peso) ? datos.peso : '0.00')+' Kg');
		$(".prod_tam").html(datos.tamanio+' Mtr');

		funciones_datos(datos.idproducto);
		$("#modal_producto").modal('show');
		// $(".panel_detalles").show();
	});
}

function funciones_datos(idproducto){
	cargarSeries(idproducto);
	cargarCarrusel(idproducto);
	cargarStockUM(idproducto);
	cargarListaprecios(idproducto);
}

function filtrar_grid(campo){
	if($("#idcategoria").val()=='T') idcategoria = ''; else idcategoria = $("#idcategoria").val()[0];
	if($("#idmarca").val()=='T') 	 idmarca = ''; else idmarca = $("#idmarca").val()[0];
	if($("#idmodelo").val()=='T') 	 idmodelo = ''; else idmodelo = $("#idmodelo").val()[0];
	if($("#idalmacen").val()=='') 	 idalmacen = ''; else idalmacen = $("#idalmacen").val();

	grilla.set_filter(_default_grilla, $("#filter").val(), " ILIKE ", $("#search").val()+"%");
	grilla.set_filter(_default_grilla, "idcategoria", "=", idcategoria);
	grilla.set_filter(_default_grilla, "idmarca", "=", idmarca);
	grilla.set_filter(_default_grilla, "idmodelo", "=", idmodelo);
	grilla.set_filter(_default_grilla, "idalmacen", "=", idalmacen);
	
	grilla.reload(_default_grilla);
	cargarPanelimagen();
}

function cargarPanelimagen(){
	str= $("#form_filtro").serialize();
	ajax.post({url: _base_url+"consultarproducto/cargar_data_panel",data:str}, function(res) {
		$("#content_imagen").empty();
		html = '';
		if(res.length){
			$(res).each(function(i,j){
				ruta_imagen = "app/img/producto/"+j.idproducto+"/"+j.imagen_producto;
				add_style = '';
				if(!j.imagen_producto){
					ruta_imagen = "app/img/producto/no_disponible.jpg";
				}
				html+='<div class="col-md-4">';
				html+='		<div class="ibox">';
				html+='			<div class="ibox-content product-box">';
				html+='				<div class="product-imitation" style="padding:0px;">';
				html+='					<img src="'+ruta_imagen+'" class="img-responsive" '+add_style+'></img>';
				html+='				</div>';
				
				html+='				<div class="product-desc" style="padding: 10px;">';
				html+='					<span class="product-price">S/ '+parseFloat(j.precio_venta).toFixed(2)+'</span>';
				html+='					<small class="text-muted">'+j.categoria+'</small>';
				html+='					<a href="#" class="product-name">'+j.prod+'</a>';
				html+='					<div class="m-t text-righ">';
				html+='						<a href="#" class="btn btn-xs btn-outline info_prod btn-primary" ajax-prod="'+j.idproducto+'">Info <i class="fa fa-long-arrow-right"></i> </a>';
				html+='					</div>';
				html+='				</div>';
				html+='			</div>';
				html+='		</div>';
				html+='</div>';
			});			
		}else{
			html+='<div class="col-md-12">';
			html+='	<div class="ibox">';
			html+='		<div class="ibox-content product-box">';
			html+='			<div class="product-imitation" style="padding: 119px;">';
			html+='				No existen registros';
			html+='			</div>';
			html+='		</div>';
			html+='	</div>';
			html+='</div>';
		}
		$("#content_imagen").html(html);
	});
}

$("#cantidadtemp").numero_real();

$("#btn-ingreso-stock").click(function(e) {
	e.preventDefault();
	
	var id = grilla.get_id(_default_grilla);
	if(id != null) {
		$("#tipotemp").val("I");
		$("#productotemp").val(id);
		cargarUnidadTemp(id);
		$("#modal-cambio-stock .modal-title").html("Ingresar stock");
		$("#modal-cambio-stock").modal("show");
	}
	else {
		ventana.alert({titulo:"", mensaje: "Seleccione un registro de la tabla."});
	}
});

$("#btn-salida-stock").click(function(e) {
	e.preventDefault();
	
	var id = grilla.get_id(_default_grilla);
	if(id != null) {
		$("#tipotemp").val("S");
		$("#productotemp").val(id);
		cargarUnidadTemp(id);
		$("#modal-cambio-stock .modal-title").html("Disminuir stock");
		$("#modal-cambio-stock").modal("show");
	}
	else {
		ventana.alert({titulo:"", mensaje: "Seleccione un registro de la tabla."});
	}
});

function cargarUnidadTemp(idproducto) {
	ajax.post({url: _base_url+"producto/get_unidades/"+idproducto}, function(res) {
		var data, options='';
		
		if($.isArray(res)) {
			for(var i in res) {
				data = res[i];
				options += '<option value="'+data.idunidad+'" title="" count="'+data.cantidad_unidad_min+'">'+data.descripcion+'</option>';
			}
		}
		
		$("#unidadtemp").html(options);
	});
}


$("#cantidadtemp").keypress(function(e) {
	if(e.keyCode == 13) {
		e.preventDefault();
		var str = "idalmacen="+$("#idalmacen").val()+"&"+$("#modal-cambio-stock").serialize();
		
		ajax.post({url: _base_url+"consultarproducto/add_stock", data:str}, function() {
			grilla.reload(_default_grilla);
			$("#modal-cambio-stock").modal("hide");
		});
	}
});