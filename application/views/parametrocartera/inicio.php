<div class="row">
    <div class="col-sm-12">
		<div class="row" style="">
			<div class="col-sm-5" style="">
				<div class="row">
					<div class="ibox-content">
							<?php
							if(count($botones)){
								foreach($botones as $k){
									echo "\n".$k;
								}
							}
							?>
						<div class="table-responsive">
							<?php echo $grid;?>
							<br>
							</br>
							
							
						</div>
					</div>
				</div>
			</div>
			
			<div class="col-sm-7" style="">
				<div class="row">
					<div style="">
						<div class="col-sm-12" style="border:0px solid red;">
							<div style="margin-left:15px">
							<?php
								if(count($botones_sub)){
									foreach($botones_sub as $k){
										echo "\n".$k;
									}
								}
							?>
							</div>
							<br>
							<br>
						</div>

						<div class="col-sm-12">
							<form id="form">
							<?php echo $listado;?>
							</form>
						</div>					
					</div>
					
				</div>
				<!--
				
					<div class="col-sm-4" style="border:1px solid red;">
						<div class="" style="">
							<div class="row">
								<div class="ibox-content">
									<div class="col-sm-12" style="">
										LISTA 1
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-sm-4" style="border:1px solid red;">
						<div class="" style="">
							<div class="row">
								<div class="ibox-content">
									<div class="col-sm-12" style="">
										LISTA 1
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="col-sm-4" style="border:1px solid red;">
						<div class="" style="">
							<div class="row">
								<div class="ibox-content-0">
									<div class="col-sm-12" style="">
										LISTA 1
									</div>
								</div>
							</div>
						</div>
					</div>
				-->
				
			</div>
		</div>
    </div>
</div>
 

<style>
	.sortable{		
		list-style-type: none;
		padding: 5px 0 0 0;
		margin-bottom:20px;
		height:auto;
		min-height:235px;
		border: 1px solid #ccc;
		background:white;
	}
	
	.sortable li{
		margin: 2px 0px 0px 2px;
		padding: 5px;
		font-size: 11px;
		font-weight:bold;
		width:97%;
	}
	
	.sortable_none{
		background:#1ab394;
		color:white !important;
		font-weight:bold;
		text-align:center;
		/*height:40px;*/
	}
  
	.lista{
		background: #f7f7f7;
		border-radius: 4px;
		border: 1px solid rgba(0,0,0,.2);
		border-bottom-color: rgba(0,0,0,.3);
		background-origin: border-box;
		background-image: -webkit-linear-gradient(top,#fff,#eee);
		background-image: linear-gradient(to bottom,#fff,#eee);
		text-shadow: 0 1px 0 #fff;
		font-weight:bold;
		width:97% !important;
	}
	
	.ui-state-none,.ui-not-asig{
		/* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#cedce7+0,596a72+100;Grey+3D+%231 */
		background: #cedce7; /* Old browsers */
		background: -moz-linear-gradient(top,  #cedce7 0%, #596a72 100%); /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  #cedce7 0%,#596a72 100%); /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  #cedce7 0%,#596a72 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#cedce7', endColorstr='#596a72',GradientType=0 ); /* IE6-9 */
		color:black !important;
		font-weight:bold;
	}
	
	.ui-state-second{
		background: #fefcea !important; /* Old browsers */
		background: -moz-radial-gradient(center, ellipse cover,  #fefcea 0%, #f1da36 100%) !important; /* FF3.6-15 */
		background: -webkit-radial-gradient(center, ellipse cover,  #fefcea 0%,#f1da36 100%) !important; /* Chrome10-25,Safari5.1-6 */
		background: radial-gradient(ellipse at center,  #fefcea 0%,#f1da36 100%) !important; /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fefcea', endColorstr='#f1da36',GradientType=1 ) !important; /* IE6-9 fallback on horizontal gradient */
		color: #1C94C4 !important;
		text-shadow: 3px -1px 4px rgba(153, 150, 150, 1) !important;
	}
  
	.ui-state-defaultP{
		background: #fcfff4 !important; /* Old browsers */
		background: -moz-radial-gradient(center, ellipse cover,  #fcfff4 0%, #dfe5d7 40%, #b3bead 100%) !important;
		background: -webkit-radial-gradient(center, ellipse cover,  #fcfff4 0%,#dfe5d7 40%,#b3bead 100%) !important;
		background: radial-gradient(ellipse at center,  #fcfff4 0%,#dfe5d7 40%,#b3bead 100%) !important;
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fcfff4', endColorstr='#b3bead',GradientType=1 );
		color: #1C94C4 !important;
		text-shadow: 3px -1px 4px rgba(153, 150, 150, 1) !important;
	}
	
	.ui-state-default-head{
		background: #45484d !important; /* Old browsers */
		background: -moz-linear-gradient(top,  #45484d 0%, #000000 100%) !important; /* FF3.6-15 */
		background: -webkit-linear-gradient(top,  #45484d 0%,#000000 100%) !important; /* Chrome10-25,Safari5.1-6 */
		background: linear-gradient(to bottom,  #45484d 0%,#000000 100%) !important; /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#45484d', endColorstr='#000000',GradientType=0 ); /* IE6-9 */
		color:white !important;
		font-weight:bold !important;
		text-align:center;
	}
  
	.ui-state-highlight { height: 1.5em; line-height: 1.2em;border: 1px dashed #CCC !important; background: #f0f0f0 !important;}
  
	li.ui-state-disabled{
		opacity: 1 !important;
	}

	div.dataTables_filter, div.dataTables_length {
	    display: none !important;
	    /*width: 7em !important;*/
	}
  </style>
  
<script src="./app/js/jquery-2.1.1.js"></script>
<script src="./app/js/jquery-ui.js"></script>

<script>
	var drag_all = false;
	var sms = true;
	var xitem = 0;

	if(drag_all){
		$( ".sortable_connect" ).sortable({
			//connectWith: "#sortable2,#sortable1"
			 connectWith: ".sortable_connect"
			,placeholder: "ui-state-highlight"
			,items: "li:not(.ui-state-disabled)"
			,stop:function( event, ui ) {
				$(ui.item[0]).each(function(i,j){
					UpdateName($(this).find('input.idparametrocartera').attr('id'),$(this));
				});
			}
		}).disableSelection();
	}else{
		$("#sortable_none").removeClass('sortable_connect');
		$( "#sortable_none" ).sortable({
			connectWith: ".connectedSortable"
			,placeholder: "ui-state-highlight"
			,items: "li:not(.ui-state-disabled)"
			,stop:function( event, ui ) {
					$(ui.item[0]).each(function(i,j){
						UpdateName($(this).find('input.idparametrocartera').attr('id'),$(this));
					});
				}
		}).disableSelection();
		
		$( ".sortable_connect" ).sortable({
			connectWith: ".sortable_connect"
			,placeholder: "ui-state-highlight"
			,items: "li:not(.ui-state-disabled)"
			,stop:function( event, ui ) {
					$(ui.item[0]).each(function(i,j){
						UpdateName($(this).find('input.idparametrocartera').attr('id'),$(this));
					});
				}
		}).disableSelection();	
	}

	// $('.btn_save').click(function(){
		
	// });

  
function UpdateName(id,aqui){
	$new_padre = aqui.parent('ul').attr('data-padre');

	$clase_prev = aqui.attr('class-parent');

	$clase_padre = aqui.parent('ul').attr('data-class');
	
	$('#'+id).val($new_padre);
	
	aqui.removeClass('ui-state-none');

	if($clase_padre != $clase_prev)
		aqui.addClass($clase_padre).removeClass($clase_prev).attr('class-parent',$clase_padre);
}
</script>