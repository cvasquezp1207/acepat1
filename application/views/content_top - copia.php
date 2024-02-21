<?php 
if(!isset($with_tabs)) 
	$with_tabs = false;
$cls = "";
if(!$with_tabs) {
?>
<div class="row wrapper border-bottom white-bg page-heading">
	<div class="col-lg-10">
		<h2><?php echo $menu_title; ?></h2>
		<?php 
		if(!empty($path)) {
			echo $path;
		}
		?>
	</div>
</div>
<?php $cls="animated fadeInRight"; } ?>
<div class="wrapper <?php echo $cls; ?>">
	<div class="row" style="">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<?php if( !empty($menu_title) || !empty($menu_subtitle) ){ ?>
				<div class="ibox-title">
					<h5><?php echo $menu_title; ?> <small><?php echo $menu_subtitle; ?></small></h5>
				</div>
				<?php } ?>
				<div class="ibox-content">
					<!-- botones -->
					<div class="row">
						<div class="col-sm-12">
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
					<!-- fin botones -->
					
					<!-- contenido adicional -->
					<?php echo $content; ?>
					<!-- fin contenido inicial -->
					
					<!-- grilla datatables -->
					<br/><div class="table-responsive"><?php echo $grilla; ?></div>
					<!-- fin grilla datatables -->
				</div>
			</div>
		</div>
	</div>
</div>