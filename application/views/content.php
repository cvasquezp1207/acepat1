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
<div class="wrapper wrapper-content <?php echo $cls; ?>">
	<div class="row">
		<div class="col-lg-12">
			<div class="ibox float-e-margins">
				<div class="ibox-title">
					<h5><?php echo $menu_title; ?> <small><?php echo $menu_subtitle; ?></small></h5>
				</div>
				<div class="ibox-content">
					<!-- fin grilla datatables -->
					<!-- contenido adicional -->
					<?php echo $content; ?>
					<!-- fin contenido inicial -->
				</div>
			</div>
		</div>
	</div>
</div>