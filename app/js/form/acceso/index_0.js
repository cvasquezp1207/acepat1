$tree_ = $('#tree');

LoadModulo($('.sucursal.seleccionado').attr('data-suc'),$('.perfil.seleccionado').attr('data-perfil') ,$('.system.seleccionado').attr('data-system'));
$('.manejable').click(function(e) {
	$ul = $(this).parent('li').parent('ul');
	$ul.find('li').find('div.pull-right').empty();
	if(  $(this).hasClass('seleccionado') ){
		$(this).find('div.pull-right').html('<i class="fa fa-check-square-o"></i>');
	}else{
		$ul.find('li div').removeClass('seleccionado');
		$(this).addClass('seleccionado');
		$(this).find('div.pull-right').html('<i class="fa fa-check-square-o"></i>');
		
		LoadModulo($('.sucursal.seleccionado').attr('data-suc'),$('.perfil.seleccionado').attr('data-perfil') ,$('.system.seleccionado').attr('data-system'));
	}
});

$('#save_acces').click(function(e) {
	$tree_.jstree(true).refresh();	
	$tree_.jstree("open_all");
	$('.checkbox_nodo').each(function(i, element){
		$a_element = $(element).parent("a.jstree-anchor");
		$selector = $a_element.find('i.jstree-checkbox');
			
		if($a_element.hasClass('jstree-clicked')){
			$(this).prop("checked",true);
		}else{
			// console.log($selector.hasClass('jstree-undetermined'));
			$(this).prop("checked",false);
		}
	});
	str = 'idsucursal='+$('.sucursal.seleccionado').attr('data-suc');
	str+= '&idperfil='+$('.perfil.seleccionado').attr('data-perfil');
	str+= '&idsistema='+$('.system.seleccionado').attr('data-system');
	str+= '&'+$("#form-all").serialize();
	// console.log( str ); return true;
	ajax.post({url: _base_url+_controller+"/guardar", data: str}, function(res) {
		ventana.alert({titulo: "En horabuena!", mensaje: "Accesos guardados correctamente", tipo:"success"}, function() {
			
		});
	});
});

$(function(){
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
	// if(!$idsistema) $idsistema='0';
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
		
		$tree_.jstree("open_all");
		$tree_.jstree(true).refresh();
	});
}