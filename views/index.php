<section>
    <div class="container">
         <header><h2><?=lang('apoyos:title')?> 
         <?php if($anio): ?>
         <?=sprintf(lang('apoyos:admin'),$anio)?>
         <?php endif;?>
         </h2></header>

        {{ theme:partial name="notices" }}
        <?php if(!$director): ?>
        <div class="alert alert-danger"><?=lang('apoyos:error_access')?></div>
        <?php else:?>
        <div class="alert alert-info"><?=sprintf(lang('apoyos:welcome_front'),$director->nombre)?></div>
        <?php endif;?>
         <?php if(is_numeric($anio)){ ?>
        <ul class="nav nav-tabs">
          <li class="<?=$status=='pendientes'?'active':''?>"><a href="<?=base_url('apoyos/'.$anio.'?tab=pendientes')?>">Pendientes</a></li>
          <li class="<?=$status=='enviados'?'active':''?>"><a href="<?=base_url('apoyos/'.$anio.'?tab=enviados')?>">Enviados</a></li>
          <li class="<?=$status=='validados'?'active':''?>"><a href="<?=base_url('apoyos/'.$anio.'?tab=validados')?>">Validados</a></li>

        </ul>
        <div class="tab-content">


            <div  class="tab-pane fade in active">
                <?php if($depositos): ?>
                <table class="table">
                    <thead>
                        <tr>
                           
                            <th>Concepto</th>
                            <th>Fecha</th>
                            <th width="10%">Importe</th>
                            <th width="14%"></th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php foreach($depositos as $deposito){ ?>
                         <tr class="<?=$deposito->estatus=='rechazado'?'danger':''?>" data-original-title="<?=$deposito->observaciones?>" data-toggle="tooltip">
                            
                            <td><?=$deposito->concepto?></td>
                            <td><?=format_date_calendar($deposito->fecha_deposito)?></td>
                            <td class="text-right"><?= '$ '.number_format($deposito->importe, 2, '.', ',') ?></td>
                            <td class="text-center">
                            <?php if($status == 'pendientes'): ?>
                                <a href="<?=base_url('apoyos/enviar/'.$deposito->id_deposito)?>" class="btn btn-small" title="Subir documentos comprobatorios"><i class="fa fa-upload"></i></a>
                            <?php else:?>
                                <a href="<?=base_url('apoyos/details/'.$deposito->id_apoyo)?>" class="btn btn-color-grey-light btn-small" title="Detalles del registro"><i class="fa fa-search"></i></a>
                            <?php endif;?>
                            </td>
                         </tr>
                         <?php }?>
                    </tbody>
                </table>
                <?php if($status=='enviados'): ?>
                <p>
                    <i class="fa fa-stop text-danger"></i> Rechazados
                </p>
                <?php endif;?>
                <?php else:?>
                <div class="alert alert-info text-center"><?=lang('global:not_found')?></div>
                <?php endif;?>

            </div>
        </div>
        <div class="divider clearfix">
            <a href="<?=base_url('apoyos')?>" class="btn btn-color-grey-light">Regresar</a>
        </div>
        <br />
        <?php }else{?>
        <div class="row">
            <?php foreach($depositos as $deposito){ ?>
                <div class="col-md-3">
                        <a href="<?=base_url('apoyos/'.$deposito->anio)?>" class="universal-button framed">
                                    <h3><?=$deposito->anio?></h3>
                                    <figure class="date"><i class="fa fa-arrow-right"></i></figure>
                                </a>
                </div>
            <?php }?>
        </div>
        <?php }?>
    </div>
</section>
