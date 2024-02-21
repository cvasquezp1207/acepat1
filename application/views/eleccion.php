
            <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">

                <div class="navbar-header">
                    <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="javascript:void(0);"><i class="fa fa-bars"></i> </a>
                </div>
            </nav>
        </div>

        <?php //var_dump($empresas);exit; ?>
        <div class="wrapper wrapper-content">
            <div class="row">
                <div data-backdrop="static" class="modal inmodal" id="modal_inicio" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content animated bounceInRight">
                            <div class="modal-body">
                               <div class="row">
                                    <div class="col-sm-12">
                                        <div class="ibox">
                                            <div class="ibox-content">
                                                <span class="text-muted small pull-right">
                                                    <a href="<?php echo base_url()?>login/salir"><i class="fa fa-sign-out"></i> Salir</a>
                                                </span>
                                                <h2>Empresas</h2>
                                                <div class="clients-list">
                                                    <div class="panel-group" id="accordion">
                                                    <?php foreach ($empresas as $idempresa => $sucursales) : ?>
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                            <h5 class="panel-title">
                                                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse-<?php echo $idempresa;?>"><?php echo strtoupper($sucursales['nombre']);?></a>
                                                            </h5>
                                                            </div>
                                                            <?php unset($sucursales['nombre']); ?>
                                                            <div id="collapse-<?php echo $idempresa;?>" class="panel-collapse collapse in">
                                                                <div class="panel-body">
                                                                    <table class="table table-striped table-hover">
                                                                        <tbody>
                                                                            <?php foreach ($sucursales as $idsucursal => $valores) : ?>
                                                                            <tr>
                                                                                <td class="client-avatar"><a href=""></a> </td>
                                                                                <td><a data-toggle="tab" href="#contact-3" class="client-link"><?php echo strtoupper($valores['nombre']); ?></a></td>
                                                                                <td><?php echo strtoupper($valores['direccion']);?></td>
                                                                                <td class="contact-type"><i class="fa fa-phone"> </i></td>
                                                                                <td><?php echo strtoupper($valores['telefono']);?></td>
                                                                                <td class="client-status"><a href="<?php echo base_url()?>home/index/<?php echo $idsucursal;?>" class="btn btn-xs btn-w-m btn-primary">Entrar <i class="fa fa-check fa-lg"></i></a></td>
                                                                            </tr>
                                                                            <?php endforeach; ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    <?php endforeach; ?>

                                                    </div><!-- Fin de acoordion-->

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
