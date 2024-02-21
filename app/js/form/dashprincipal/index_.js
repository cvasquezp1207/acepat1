// funciones principales, estas funciones se invocaran cuando
// se haga click en cualquier boton de accion no incluye
// los eventos de botones dentro del formulario
var form = {
	nuevo: function() {

	},
	editar: function(id) {
		// alert(id);
	},
	eliminar: function(id) {
		model.del(id, function(res) {
			ventana.alert({titulo: "En horabuena!", mensaje: "Registro eliminado correctamente", tipo:"success"}, function() {
				grilla.reload(_default_grilla); // _default_grilla=perfil, si no se indico otro nombre
			});
		});
	},
	guardar: function() {
		// algunas validaciones aqui
		// var data = $("#form_perfil").serialize();
		// if($('#tipo').required()){
			var data = $("#form_"+_controller).serialize();
			model.save(data, function(res) {
				ventana.alert({titulo: "En horabuena!", mensaje: "Datos guardados correctamente", tipo:"success"}, function() {
					redirect(_controller);
				});
			});			
		// }
	},
	cancelar: function() {

	}
};

var chart = new Highcharts.Chart
    // $('#container').highcharts
    ({
        chart: {
            // type: 'column',
            // inverted: true,
            renderTo: 'container'
        },
        title: {
            text: null
        },
        subtitle: {
            // text: 'According to the Standard Atmosphere Model'
        },
        credits: {
            enabled: false
        },
        xAxis: {
            reversed: false,
            title: {
                enabled: true,
                text:null// 'Noviembre' //variable nombre del mes anio 
            },
            labels: {
                // formatter: function () {
                //     return this.value + 'km';
                // }
            },
            maxPadding: 0.0,
            showLastLabel: true
        },
        yAxis: {
            title: {
                text: null
            },
            maxPadding: 0.0,

            // labels: {
            //     formatter: function () {
            //         return this.value + '°';
            //     }
            // },
            lineWidth: 1
        },
        legend: {
            enabled: true
        },
        tooltip: {
            headerFormat: '<b>{series.name}</b><br/>',
            pointFormat: 'dia: {point.x} , S./ {point.y}'
        },
        plotOptions: {
            column: {
                marker: {
                    enable: false
                }
            },
            area: {
                // pointStart: 1940,
                marker: {
                    lineWidth : 1,
                    enabled: false,
                    symbol: 'circle',
                    radius: 1,
                    states: {
                        hover: {
                            enabled: true
                        }
                    }
                }
            }
        },
        navigation: {
            buttonOptions: {
                enabled: false
            }
        },
        series: [{
            type: 'column',
            name: 'Total Ventas',
            data: [80, 76.5,93,76,78,99,89,96,56,80, 76.5,93,76,78,99,89,96,56,70,76,79,84,34,78]
        },
        {
            type : 'area',
            name : 'Ventas Credito',
            data : [10,25,50,30,12,32,45,23,43,10,25,50,30,12,32,45,23,43,10,25,25,23,22,70]

        }
        ]
    });
validate();

function setIcon(icon) {
	$("#icono_preview").html('<i class="fa '+icon+'"></i>');
}

$("a.select_icon").click(function() {
	var icon = $(this).data("icon");
	setIcon(icon);
	$("#icono").val(icon);
});

$("#icono").blur(function() {
	setIcon($(this).val());
});

$("#btn_cancel").click(function() {
	redirect(_controller);
	return false;
});

$("#descripcion").focus();

$(".btn_estado").on("click", function() {
	
});

$("#descripcion,#clase_name,#id_name,alias").alfanumerico({'permitir':' -_'})
