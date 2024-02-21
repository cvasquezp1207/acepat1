(function( $ ){
    $.fn.required = function(opciones) {
        var estado = true;
        var first = true;
        var valoresDefault = {
            numero: false,
            tipo: "entero",
            aceptaCero: false,
            showError: true,
			size: false
			// ,minLength: false
			// ,maxLength: false
        };
        
        $.extend(valoresDefault, opciones);
        
        this.each (function() {
            var valor = $.trim( $(this).val() );
            if ( valor.length < 1) {
                if(valoresDefault.showError)
                    $(this).addClass('ui-state-error ui-icon-alert');
                if(first) {
                    $(this).focus();
                    first = false;
                }
                estado = estado && false;
            } else {
				if( valoresDefault.size !== false && typeof valoresDefault.size == 'number' ) {
					estado = estado && ( valor.length == valoresDefault.size );
				}
			
                if(valoresDefault.numero) {
                    if(valoresDefault.tipo == "entero" || valoresDefault.tipo == "int" || valoresDefault.tipo == "integer") {
                        valor = parseInt(valor);
                    }
                    else if(valoresDefault.tipo == "real" || valoresDefault.tipo == "double" || valoresDefault.tipo == "float") {
                        valor = parseFloat(valor);
                    }
                    if(isNaN(valor)) {
                        if(valoresDefault.showError)
                            $(this).addClass('ui-state-error ui-icon-alert');
                        if(first) {
                            $(this).focus();
                            first = false;
                        }
                        estado = estado && false;
                    }
                    else {
                        if(valoresDefault.aceptaCero) {
                            if(valoresDefault.showError)
                                $(this).removeClass('ui-state-error ui-icon-alert')
                            estado = estado && true;
                        }
                        else {
                            if(valor == 0) {
                                if(valoresDefault.showError)
                                    $(this).addClass('ui-state-error ui-icon-alert');
                                if(first) {
                                    $(this).focus();
                                    first = false;
                                }
                                estado = estado && false;
                            }
                            else {
                                if(valoresDefault.showError)
                                    $(this).removeClass('ui-state-error ui-icon-alert')
                                estado = estado && true;
                            }
                        }
                    }
                }
                else {
                    if(valoresDefault.showError)
                        $(this).removeClass('ui-state-error ui-icon-alert')
                    estado = estado && true;
                }
            }
        });
        return estado;
    };
})( jQuery );

(function( $ ){
    $.fn.numero_entero = function(a) {
        a = $.extend({permitir: ""}, a);
        
        var az = "abcdefghijklmnñopqrstuvwxyz";
        az += az.toUpperCase();
        az += "!@#$%^&*()+=[]\\\';,/{}|\":<>?~`.-´ºª·¬¿Ç¡¨_ ";
        if(a.permitir != "") {
            s = a.permitir.split('');
            for (i=0;i<s.length;i++) {
                //if (ichars.indexOf(s[i]) != -1) s[i] = "\\" + s[i];
                az = az.replace(s[i],'');
            }
        }
    
        return this.each (function() {
            $(this).keypress(function (e){
                if (!e.charCode) k = String.fromCharCode(e.which);
                else k = String.fromCharCode(e.charCode);
				
                if (az.indexOf(k) != -1) e.preventDefault();
                if (e.ctrlKey&&k=='v') e.preventDefault();
            });
        });  
    };
})( jQuery );

(function( $ ){
	$.fn.numero_real = function() {
		var counterNumberPointWrite = 1;
		var az = "abcdefghijklmnñopqrstuvwxyz";
		az += az.toUpperCase();
		az += "!@#$%^&*()+=[]\\\';,/{}|\":<>?~`-´ºª·¬¿Ç¡¨_ ";

		return this.each (function() {
			$(this).keypress(function (e) {
				if (!e.charCode) 
					k = String.fromCharCode(e.which);
				else 
					k = String.fromCharCode(e.charCode);

				if (az.indexOf(k) != -1) 
					e.preventDefault();
				if (e.ctrlKey&&k=='v') 
					e.preventDefault();

				if (e.keyCode == 46) {
					var counter = 1;
					var character = $(this).val();				
					s = character.split('');
					for (var i=0; i < s.length; i++) {
						if(s[i] == ".")
							counter ++;
					}
					counterNumberPointWrite = counter;
					if(counterNumberPointWrite > 1) {
						e.preventDefault();
					}
				}
			});
		});	 
	};
})( jQuery );

(function( $ ){
    $.fn.letras = function(a) {
        a = $.extend({permitir: ""}, a);
        
        var az = "0123456789";
        az += az.toUpperCase();
        az += "!@#$%^&*()+=[]\\\';,/{}|\":<>?~`.-´ºª·¬¿Ç¡¨_ ";
        if(a.permitir != "") {
            s = a.permitir.split('');
            for (i=0;i<s.length;i++) {
                //if (ichars.indexOf(s[i]) != -1) s[i] = "\\" + s[i];
                az = az.replace(s[i],'');
            }
        }
    
        return this.each (function() {
            $(this).keypress(function (e){
                if (!e.charCode) k = String.fromCharCode(e.which);
                else k = String.fromCharCode(e.charCode);
                                        
                if (az.indexOf(k) != -1) e.preventDefault();
                if (e.ctrlKey&&k=='v') e.preventDefault();
                                    
            });
                        
            /*$(this).bind('contextmenu',function () {return false});*/
        });  
    };
})( jQuery );

(function( $ ){
    $.fn.required_CarcEs = function() {
        var atributo=$(this).attr("id");
          atributo=atributo.toLowerCase();

            if(/dni/.test(atributo)) valor_limit=7;
            if(/nrodoc/.test(atributo)) valor_limit=7;
            if(/ruc/.test(atributo)) valor_limit=10;
            if(/codmatricula/.test(atributo)) valor_limit=5;
            var cant_dig = $(this).val().length;
            diferencia=(valor_limit+1)-cant_dig;
            if(diferencia==0){
                $('.sms_'+atributo).fadeOut(300, function(){ 
                    $(this).remove();
                }); 
            }
            
            
            if ( $(this).val().length <=valor_limit ) {
                $(this).addClass('ui-state-error Input-Standar');
                $(this).removeClass('input-focus Input-KarEll TextArea-Standar');
                $(this).focus();
                $('.sms_'+atributo).remove();
                $("<div class='msg sms_"+atributo+"'>Este campo debe tener "+(valor_limit+1)+" caracteres, le falta "+diferencia+"</div>").insertAfter("#"+atributo);
                return false;
            }else {
                $(this).removeClass('ui-state-error');
                $(this).addClass('input-focus');

                return true;
            }   
           
     };
})( jQuery );

(function( $ ) {
    $.fn.alfanumerico = function(a) {
        a = $.extend({permitir: ""}, a);
        
        var az = "ñÑ!@#$%^&*()+=[]\\\';,/{}|\":<>?~`.-´ºª·¬¿Ç¡¨_ ";
        if(a.permitir != "") {
            s = a.permitir.split('');
            for (i=0;i<s.length;i++) {
                //if (ichars.indexOf(s[i]) != -1) s[i] = "\\" + s[i];
                az = az.replace(s[i],'');
            }
        }
    
        return this.each (function() {
            $(this).keypress(function (e){
                if (!e.charCode) k = String.fromCharCode(e.which);
                else k = String.fromCharCode(e.charCode);
                                        
                if (az.indexOf(k) != -1) e.preventDefault();
                if (e.ctrlKey&&k=='v') e.preventDefault();
                                    
            });
                        
            /*$(this).bind('contextmenu',function () {return false});*/
        });  
    };
})( jQuery );