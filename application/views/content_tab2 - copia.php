	<div class="fh-with-tab">
		<div id="jiframe-ymenu_header" class="ymenu-tab">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#ymtab-1"><?php echo $title; ?></a></li>
			</ul>
		</div>	
		
		<div class="full-height">
			<div class="full-height-scroll white-bg border-left">
				<div id="jiframe-ymenu_body" class="element-detail-box">
					<div class="tab-content">
						<div id="ymtab-1" class="tab-pane active">
							<div class="wrapper wrapper-content">
							<?php if($_SESSION['es_superusuario'] =='S'){?>
								<div class="row">
									<div class="col-sm-3">
										<div class="ibox float-e-margins">
											<div class="ibox-title">
												<span class="label label-success pull-right">Mes <?php $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"); 
												echo $meses[date('n')-1];?></span>
												<h5>Mano de Obra</h5>
											</div>                                                  
											<div class="ibox-content">
												<h1 class="no-margins"><?php foreach ($lista as $registro) {?> <?php echo  "S/.... ".number_format ($registro['venta'] , 2 , '.' , ','); ?><?php }?></h1>
												<small>Total Mano de Obra..</small>
											</div>
											
										</div>
									</div>
									<div class="col-sm-3">
										<div class="ibox float-e-margins">
											<div class="ibox-title">
												<span class="label label-info pull-right">Mes <?php $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"); 
												echo $meses[date('n')-1];?></span>
												<h5>VENTAS CR&Eacute;DITO</h5>
											</div>
											<div class="ibox-content">
												<h1 class="no-margins"><?php foreach($credito as $registro){?>
													<?php echo "S/. ".number_format($registro['v_credito'], 2, '.', ',')?><?php }?>     
												</h1>
												<small>Total Ventas Cr&eacute;dito</small>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="ibox float-e-margins">
											<div class="ibox-title">
												<span class="label label-primary pull-right">Mes <?php $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"); 
												echo $meses[date('n')-1];?></span>
												<h5>COMPRAS</h5>
											</div>
											<div class="ibox-content">
												<h1 class="no-margins"><?php foreach($compras as $registro){?> <?php echo "S/. ".number_format($registro['compras'], 2,'.',',')?><?php }?></h1>
												<small>Total compras</small>
											</div>
										</div>
									</div>
									<div class="col-sm-3">
										<div class="ibox float-e-margins">
											<div class="ibox-title">
												<span class="label label-danger pull-right">Mes <?php $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"); 
												echo $meses[date('n')-1];?></span>
												<h5>COMPRAS POR PAGAR</h5>
											</div>
											<div class="ibox-content">
												<h1 class="no-margins"><?php foreach ($compras_credito as $registro) {?> 
												<?php echo "S/. ".number_format($registro['compras_credito'], 2, '.',',')?><?php }?></h1>
												<small>Total compras por pagar</small>
											</div>
										</div>
									</div>
								</div>
								
								<div class="row">
									<?php foreach ($sucursal as $registro) {?>
									<div class="col-sm-3">
										<div class="widget style1 navy-bg">
											<div class="row">
												<div class="col-xs-2">
													<i class="fa fa-shopping-cart fa-2x"></i>
												</div>
												<div class="col-xs-10 text-right">              
										
													<span> Mano de Obra <?php echo $registro['nombre']?> </span>
													<h3 class="font-bold">S/. 24,518</h3>                                    
													<!--<h3 class="font-bold">S/. <?php if($_SESSION['idusuario'] =='1' || $_SESSION['idusuario'] =='3' || $_SESSION['idusuario'] =='8' ){ echo number_format($registro['venta'],2,'.',',')?></h3> -->
													<div class="stat-percent font-bold text-modif">
													<?php echo number_format(($registro['venta']*100)/$registro['total'],2,'.',',')?> %<?php } ?></div>             
													
													
												</div>
												
																							
											</div>
										</div>
									</div>
									
									<div class="col-sm-3">
										<div class="widget style1 navy-bg">
											<div class="row">
												<div class="col-xs-2">
													<i class="fa fa-shopping-cart fa-2x"></i>
												</div>
												<div class="col-xs-10 text-right">              
										
													<span> Mano de Obra <?php echo $registro['nombre']?> </span>
													<h3 class="font-bold">S/. 24,518</h3>                                    
													<!--<h3 class="font-bold">S/. <?php if($_SESSION['idusuario'] =='1' || $_SESSION['idusuario'] =='3' || $_SESSION['idusuario'] =='8' ){ echo number_format($registro['venta'],2,'.',',')?></h3> -->
													<div class="stat-percent font-bold text-modif">
													<?php echo number_format(($registro['venta']*100)/$registro['total'],2,'.',',')?> %<?php } ?></div>             
													
													
												</div>
												
																							
											</div>
										</div>
									</div>
									
									
									<div class="col-sm-3">
										<div class="widget style1 navy-bg">
											<div class="row">
												<div class="col-xs-2">
													<i class="fa fa-shopping-cart fa-2x"></i>
												</div>
												<div class="col-xs-10 text-right">              
										
													<span> Mano de Obra <?php echo $registro['nombre']?> </span>
													<h3 class="font-bold">S/. 24,518</h3>                                    
													<!--<h3 class="font-bold">S/. <?php if($_SESSION['idusuario'] =='1' || $_SESSION['idusuario'] =='3' || $_SESSION['idusuario'] =='8' ){ echo number_format($registro['venta'],2,'.',',')?></h3> -->
													<div class="stat-percent font-bold text-modif">
													<?php echo number_format(($registro['venta']*100)/$registro['total'],2,'.',',')?> %<?php } ?></div>             
													
													
												</div>
												
																							
											</div>
										</div>
									</div>
									
									<div class="col-sm-3">
										<div class="widget style1 navy-bg">
											<div class="row">
												<div class="col-xs-2">
													<i class="fa fa-shopping-cart fa-2x"></i>
												</div>
												<div class="col-xs-10 text-right">              
										
													<span> Mano de Obra <?php echo $registro['nombre']?> </span>
													<h3 class="font-bold">S/. 24,518</h3>                                    
													<!--<h3 class="font-bold">S/. <?php if($_SESSION['idusuario'] =='1' || $_SESSION['idusuario'] =='3' || $_SESSION['idusuario'] =='8' ){ echo number_format($registro['venta'],2,'.',',')?></h3> -->
													<div class="stat-percent font-bold text-modif">
													<?php echo number_format(($registro['venta']*100)/$registro['total'],2,'.',',')?> %<?php } ?></div>             
													
													
												</div>
												
																							
											</div>
										</div>
									</div>
									<?php }?>
								</div>
								
								
								
								<div class="row">
									<div class="col-lg-12">
										<div class="ibox float-e-margins">
											<div class="ibox-title">
												<h5>Ventas Diarias</h5>
											</div>
											
											<div class="ibox-content">
												<div class="row">
													<div class="col-lg-12">
														<div class="flot-chart">
															<div class="flot-chart-content" id="container"></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								
								<div class="row">
									<div class="col-lg-12">
										<div class="ibox float-e-margins">
											<div class="ibox-title">
												<h5>Ventas Mensuales</h5>
											</div>
											
											<div class="ibox-content">
												<div class="row">
													<div class="col-lg-12">
														<div class="flot-chart">
															<div  id="container2"></div>
														</div>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								
								
							<?php }?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<!--<script src="<?php echo base_url(); ?>app/js/plugins/highcharts/highcharts.js"></script>-->
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="<?php echo base_url(); ?>app/js/plugins/highcharts/modules/exporting.js"></script>

<script type="text/javascript">
    var chart = new Highcharts.Chart
    // $('#container').highcharts
    ({
        chart: {
            // type: 'column',
            // inverted: true,
            renderTo: 'container'
        },
        title: {
              text: 'Mano de Obra Diario'
        },
        subtitle: {
            // text: 'According to the Standard Atmosphere Model'
        },
        credits: {
            enabled: false
        },
        xAxis: {
			categories: [<?php foreach ($reporteventas as $reg){ echo $reg["dia"]; echo ','; }?>],
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
            //         return this.value + '�';
            //     }
            // },
            lineWidth: 1
        },
        legend: {
            enabled: true
        },
        tooltip: {
            headerFormat: '<b>{series.name}</b><br/>',
            pointFormat: 'S./ {point.y}'
        },
        plotOptions: {
            column: {
				//pointStart: 1,
                marker: {
                    enable: false
                }
            },
            area: {
                //pointStart: 1,
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
            name: 'Total Mo',
            data: [<?php foreach ($reporteventas as $reg){ echo $reg["total"]; echo ','; }?>]
        },
        {
            type : 'area',
            name : 'Ventas Credito',
            data : [<?php foreach ($ventas_d as $reg) { echo $reg['ventas_d']; echo ','; }?>]
        }
        ]
    });    
	
	
	var chart = new Highcharts.Chart
    // $('#container').highcharts
    ({
        chart: {
            // type: 'column',
            // inverted: true,
            renderTo: 'container2'
        },
        title: {
            text: 'Ventas por Meses'
        },
        subtitle: {
            // text: 'According to the Standard Atmosphere Model'
        },
        credits: {
            enabled: false
        },
        xAxis: {
			categories: [<?php foreach ($ventasmescon as $reg){ echo $reg["mes"]; echo ','; }?>],
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
            //         return this.value + '�';
            //     }
            // },
            lineWidth: 1
        },
        legend: {
            enabled: true
        },
        tooltip: {
           pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>S/ {point.y} ( {point.percentage:.0f} % )</b><br/>',
        shared: true
        },
        plotOptions: {
            column: {
				stacking: 'percent'
            },
           
        },
        navigation: {
            buttonOptions: {
                enabled: false
            }
        },
        series: [{
            type: 'column',
            name: 'Ventas Contado',
            data: [<?php foreach ($ventasmescon as $reg){ echo $reg["totalcon"]; echo ','; }?>]
        },
        {
			
			
            type : 'column',
            name : 'Ventas Credito',
            data : [<?php foreach ($ventasmescred as $reg) { echo $reg['totalcred']; echo ','; }?>]
        }
        ]
    });   
</script>