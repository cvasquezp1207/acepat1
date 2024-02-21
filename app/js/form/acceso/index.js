$tree_ = $('#tree');

$("#idsucursal").change(function(e){
	$idsys		= $("#idsistema").val();
	$idperf		= $("#idperfil").val();
	$idsuc		= $("#idsucursal").val();
	
	ajax.post({url: _base_url+_controller+"/sistemas_sucursal", data: {idsistema:$idsys,idperfil:$idperf,idsucursal:$idsuc},type:'html'}, function(res) {
		option = "<option value=''>Seleccione...</option>";
		$(res).each(function(x,y){
			selected = '';
			if($idsys==y.idsistema)
				selected = 'selected';
			option+="<option value='"+y.idsistema+"' "+selected+" >"+y.sistema+"</option>";
		});
		
		$("#idsistema").html(option);
		$("#idsistema").trigger("change");
	});
});

$("#idsistema").change(function(e){
	cargar_empleados();
	LoadModulo($("#idsucursal").val(),$('#idperfil').val() ,$('#idsistema').val());
});

$("#idperfil").change(function(e){
	cargar_empleados();
	LoadModulo($("#idsucursal").val(),$('#idperfil').val() ,$('#idsistema').val());
});

$('#save_acces').click(function(e) {
	// $tree_.jstree(true).refresh();	
	$tree_.jstree("open_all");
	$('.checkbox_nodo').each(function(i, element){
		$a_element = $(element).parent("a.jstree-anchor");
		$selector = $a_element.find('i.jstree-checkbox');
			
		if($a_element.hasClass('jstree-clicked')){
			$(this).prop("checked",true);
		}else{
			if($selector.hasClass('jstree-undetermined'))
				$(this).prop("checked",true);
			else
				$(this).prop("checked",false);
		}
	});
	// str = 'idsucursal='+$('.sucursal.seleccionado').attr('data-suc');
	// str+= '&idperfil='+$('.perfil.seleccionado').attr('data-perfil');
	// str+= '&idsistema='+$('.system.seleccionado').attr('data-system');
	str = $("#form-all").serialize();

	ajax.post({url: _base_url+_controller+"/guardar", data: str}, function(res) {
		ventana.alert({titulo: "En horabuena!", mensaje: "Accesos guardados correctamente", tipo:"success"}, function() {
			$tree_.jstree("close_all");
			$tree_.jstree(true).refresh();
		});
	});
});

$(function(){
	$("#idsucursal").trigger("change");
	$tree_.on('ready.jstree', function() {
			// $tree_.jstree("close_all");
			// $tree_.jstree("open_all");
			$tree_.jstree(true).refresh();
			// $("#tree").jstree("open_node", $('#j1_1'));		
			// $("#tree").jstree("open_node", $('#j1_2'));		
	});
});

function LoadModulo(idsucursal, idperfil, idsistema){
	$idsistema  = idsistema||'0';
	$idsucursal = idsucursal||'0';
	$idperfil	= idperfil||'0';

	$("#tree").jstree("destroy");
	ajax.post({url: _base_url+_controller+"/tree_list", data: {idsistema:$idsistema,idperfil:$idperfil,idsucursal:$idsucursal},type:'html'}, function(res) {
		$tree_.html(res);
		
		$tree_.jstree({
            plugins: ["checkbox"]
			,"checkbox": {
				"keep_selected_style": false
			}
			,'themes': {
                'name': 'proton',
                'responsive': true
            }
        });
		
		// $tree_.jstree("open_all");
		$tree_.jstree(true).refresh();
	});
}

function cargar_empleados(){
	idsucursal	= $("#idsucursal").val();
	idsistema 	= $("#idsistema").val();
	idperfil	= $("#idperfil").val();
	$(".empleados").empty();
	if($.trim(idsucursal)!='' && $.trim(idperfil)!='' ){
		str = "idsucursal="+idsucursal;
		str+= "&idsistema="+idsistema;
		str+= "&idperfil="+idperfil;

		ajax.post({url: _base_url+_controller+"/listar_empleados", data: str}, function(res) {
			option="";
			$(res).each(function(x,y){
				option+="<option value='"+y['idusuario']+"'>"+y['user_nombres'];
			});
			$(".empleados").html(option);
		});
	}
}