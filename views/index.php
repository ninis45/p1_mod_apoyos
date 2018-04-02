<section>
    <div class="container">
         <header><h2><?=lang('apoyos:title')?></h2></header>
        
        {{ theme:partial name="notices" }}
         <?php if(is_numeric($anio)){ ?>
        <ul class="nav nav-tabs">
          <li class="<?=$status=='pendientes'?'active':''?>"><a href="<?=base_url('apoyos/'.$anio.'?tab=pendientes')?>">Pendientes</a></li>
          <li class="<?=$status=='enviados'?'active':''?>"><a href="<?=base_url('apoyos/'.$anio.'?tab=enviados')?>">Enviados</a></li>
          <li class="<?=$status=='validados'?'active':''?>"><a href="<?=base_url('apoyos/'.$anio.'?tab=validados')?>">Validados</a></li>
        
        </ul>
        <div class="tab-content">


            <div  class="tab-pane fade in active">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Concepto</th>
                            <th>Fecha</th>
                            <th>Importe</th>
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php foreach($depositos as $deposito){ ?>
                         <tr>
                            <td><?=$deposito->id?></td>
                            <td><?=$deposito->concepto?></td>
                            <td><?=$deposito->fecha_deposito?></td>
                            <td><?=$deposito->importe?></td>
                            <td>
                            <?php if($status == 'pendientes'): ?>
                                <a href="<?=base_url('apoyos/enviar/'.$deposito->id_deposito)?>" class="btn btn-small"><i class="fa fa-upload"></i></a>
                            <?php else:?>
                                <a href="<?=base_url('apoyos/details/'.$deposito->id_apoyo)?>" class="btn btn-color-grey-light btn-small"><i class="fa fa-search"></i></a>
                            <?php endif;?>
                            </td>
                         </tr>
                         <?php }?>
                    </tbody>
                </table>
                   
            </div>
        </div>
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