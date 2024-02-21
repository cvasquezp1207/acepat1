
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">

            <div class="navbar-header">
                <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="javascript:void(0);"><i class="fa fa-bars"></i> </a>
            </div>
            <ul class="nav navbar-top-links navbar-right">
                <li>
                    <span class="m-r-sm text-muted welcome-message">Bienvenido al Sistema Administrable SysAgroPalm</span>
                </li>
                <li>
                    <a href="<?php echo base_url()?>login/salir">
                        <i class="fa fa-sign-out"></i> Salir
                    </a>
                </li>
            </ul>
            </nav>
        </div>
        <div class="wrapper wrapper-content">
            <div class="row">
                <!-- Creacion del Menú -->
                <?php if(isset($menu)): ?>
                    <?php foreach ($menu as $modulo_sistema):?>
                    <div class="col-lg-<?php echo $division;?> col-md-<?php echo $division;?> col-sm-6 col-xs-12">
                        <div class="ibox float-e-margins">
                            <div class="ibox-title">
                                <h5><?php echo $modulo_sistema->sistema;?></h5>
                            </div>
                            <div class="ibox-content">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                        <h1 class = "no-margins">40 886,200</h1>
                                        <small><?php echo $modulo_sistema->abre;?></small>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6  text-right">
                                        <a href="<?php echo base_url();?>home/principal/<?php echo $modulo_sistema->idsistema;?>"><i class = "fa fa-hand-o-right fa-3x"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
