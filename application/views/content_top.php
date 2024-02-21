<?php 
if(!isset($with_tabs)) 
	$with_tabs = false;
$cls = "animated fadeInRight";
?>
<div class="row wrapper border-bottom white-bg page-heading fixed-button-top">
	<div class="col-sm-6">
	<?php if( !empty($menu_title) || !empty($menu_subtitle) ){ ?>
			<h2 class="title-heading"><?php echo $menu_title; ?></h2><small><?php echo $menu_subtitle; ?></small>
		<?php } ?>
	</div>
	<div class="col-sm-6">
		<div class="title-action">
			<?php
			if(!empty($buttons)) {
				if(is_array($buttons)) {
					$buttons = implode("\n", $buttons);
				}
				echo $buttons;
			}
			?>
		</div>
	</div>
</div>

<div class="wrapper <?php echo $cls; ?>">
	<?php if(! empty($filter)) { ?>
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<!-- filtro de la grilla -->
					<?php echo $filter; ?>
					<!-- fin -->
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
	
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-content">
					<!-- filtro de la grilla -->
					<!--<div class="panel panel-default"><div class="panel-body"><?php //echo $filter; ?></div></div>-->
					<!-- fin -->
				
					<!-- grilla datatables -->
					<div class="table-responsive"><?php echo $grilla; ?></div>
					<!-- fin grilla datatables -->
					
					<!-- contenido adicional -->
					<?php echo $content; ?>
					<!-- fin contenido inicial -->
				</div>
			</div>
		</div>
	</div>
</div>