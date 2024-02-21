node_all_close = false;

LoadModulo( $('.system.seleccionado').attr('data-system'));

$('.manejable').click(function(e) {
	$ul = $(this).parent('li').parent('ul');
	$ul.find('li').find('div.pull-right').empty();
	if(  $(this).hasClass('seleccionado') ){
		$(this).find('div.pull-right').html('<i class="fa fa-check-square-o"></i>');
	}else{
		$ul.find('li div').removeClass('seleccionado');
		$(this).addClass('seleccionado');
		$(this).find('div.pull-right').html('<i class="fa fa-check-square-o"></i>');
		LoadModulo($('.system.seleccionado').attr('data-system'));
	}
});

$('#save_acces').click(function(e) {
	$('.checkbox_nodo').each(function(m,n){
		if( $(n).attr('ajax-type')== 'indeterminate'){
			$(n).prop('checked', true);
		}
	});
	str = 'idsucursal='+$('.sucursal.seleccionado').attr('data-suc');
	str+= '&idperfil='+$('.perfil.seleccionado').attr('data-perfil');
	str+= '&idsistema='+$('.system.seleccionado').attr('data-system');
	str+= '&'+$("#form-all").serialize();
	console.log( str )
	ajax.post({url: _base_url+_controller+"/guardar", data: str}, function(res) {
		ventana.alert({titulo: "En horabuena!", mensaje: "Accesos guardados correctamente", tipo:"success"}, function() {
			
		});
	});
});

$(document).on('click','.main_expand',function(){
	$ul = ($(this) ).parent('div').parent('li').find('ul');
	$nodito =	$(this).parent('div').parent('li').find('ul');
	if( $(this).hasClass('hijo-close') ){//ABRIENDO NODO
		$(this).removeClass('hijo-close').addClass('hijo-open');
		$($nodito[0]).slideDown("slow")
	}else{//CERRANDO NODO
		$(this).removeClass('hijo-open').addClass('hijo-close');
		$($nodito[0]).slideUp("slow")
	}
});

$(document).on('click','.checkbox_nodo',function(){
	$lista_nodo  = $(this).parent('div').parent('div').parent('div').parent('li');
	$nodo_selecc = $(this).parent('div').parent('div').parent('div').parent('li').find('ul');
	
	if( !$(this).hasClass('ck_boton') ){//SON BOTONES
		if( $(this).is(':checked') ){
			$($lista_nodo.find('ul input.checkbox_nodo')).prop('checked', true);
		}else{
			$($lista_nodo.find('ul input.checkbox_nodo')).prop('checked', false);
		}		
		$($lista_nodo.find('ul input.checkbox_nodo')).attr("ajax-type",'checkbox');
	}
	Verificar_ck($lista_nodo);	
});

function Verificar_ck($lista_nodo){
	$all_check = 0;
	$all_hijo = 0;
	$check_nodo = $lista_nodo.parent('ul').parent('.botones').parent('li').find('.presentacion').find('.checkbox_parent div input.checkbox_nodo');
	console.log($check_nodo);
	$($lista_nodo.parent('ul').find('li')).each(function(x,y){
		$li_parent = $(y).find('.checkbox_nodo');
		$all_hijo++;
		if( $($li_parent).is(':checked') ){
			$all_check++;
		}
	})
	
	$new_input = $($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]);
		
	if( $all_check == $all_hijo ){
		$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).prop('indeterminate', false);
		$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).prop('checked', true);
		
		$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).attr("ajax-type",'checkbox');
	}else{
		if($all_check>1)
			$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).prop('indeterminate', true);
		else{
			// $($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).prop('indeterminate', false);
			$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).prop('indeterminate', true);
		}
		$($lista_nodo.parent('ul').parent('li').find('div input.checkbox_nodo')[0]).attr("ajax-type",'indeterminate');
	}

	if($new_input.hasClass('ck_hijo')){
		$nlista_nodo = $new_input.parent('div').parent('div').parent('div').parent('li');
		Verificar_ck($nlista_nodo);
	}
}

function LoadModulo(idsistema){
	ajax.post({url: _base_url+_controller+"/ListaModulo", data: 'idsistema='+idsistema,type:'html'}, function(res) {
		$('#lista_modulo').html(res);
		if(node_all_close){
			$nodos = $(".main_expand").removeClass('hijo-open').addClass('hijo-close');
			$nodos.parent('div').parent('li').find('ul').hide();
			
		}else{//ABRIR NODOS
			$nodos = $(".main_expand").removeClass('hijo-close').addClass('hijo-open');
			$nodos.parent('div').parent('li').find('ul').show();
		}

		if($(".main_expand").hasClass('main_hijo')){//CERRAMOS TODOS LOS NODOS HIJOS
			$nodos = $(".main_expand.main_hijo").removeClass('hijo-open').addClass('hijo-close');
		}
		
		$(".main_expand").parent('div').parent('li').find('.botones').hide();
		
		LoadAccesos( $('.sucursal.seleccionado').attr('data-suc'),  $('.perfil.seleccionado').attr('data-perfil'), $('.system.seleccionado').attr('data-system') );
	});
}

function LoadAccesos(idsucursal,idperfil,idsistema){
	ajax.post({url: _base_url+_controller+"/ListAccesos", data: 'idsistema='+idsistema+'&idperfil='+idperfil+'&idsucursal='+idsucursal}, function(res) {
		$(res).each(function(i,j){
			LoadAccboton(idsucursal,idperfil,idsistema,j.idmodulo);			
			// Verificar_ck($("#checkbox"+j.idmodulo).parent('div').parent('div').parent('div').parent('li'));
		})
	});
}

function LoadAccboton(idsucursal,idperfil,idsistema,idmodulo){
	ajax.post({url: _base_url+_controller+"/Accesoboton", data: 'idsistema='+idsistema+'&idperfil='+idperfil+'&idsucursal='+idsucursal+'&idmodulo='+idmodulo}, function(arr) {
		if(arr.length){
			$(arr).each(function(x,y){
				$("#checkbox"+y.idmodulo+'_'+y.idboton).prop('checked', true);
				$("#checkbox"+y.idmodulo+'_'+y.idboton).attr("ajax-type",'checkbox');
				Verificar_ck($("#checkbox"+y.idmodulo+'_'+y.idboton).parent('div').parent('div').parent('div').parent('li'));
			})
		}else{//NO TIENE BOTONES
			$("#checkbox"+idmodulo).prop('indeterminate', true);
			$("#checkbox"+idmodulo).attr("ajax-type",'indeterminate');
			// console.log(idmodulo);
			Verificar_ck($("#checkbox"+idmodulo).parent('div').parent('div').parent('div').parent('li'));
		}
	})
}