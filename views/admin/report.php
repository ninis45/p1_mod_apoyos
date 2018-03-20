<style type="text/css">

    .form-actions{
        padding:30px 0px;
    }
</style>
<section ng-controller="InputReport">
    <div class="lead text-success"><?=lang('apoyos:report')?></div>
    <?php if(!$data){?>
        
    <?php echo form_open('','class="form-inline"')?>
    <!--div class="form-group">
        <?=form_dropdown('centro',array(''=>' [ Todos ] ')+$centros,'','class="form-control" placeholder="Inicio"');?>
    </div-->    
    <div class="form-group">
        <div class="input-group ui-datepicker">
            <?=form_input('fecha_ini','',
            'class="form-control" 
            placeholder="Inicio"
            uib-datepicker-popup="yyyy-MM-dd"
            ng-model="fecha_ini"
            is-open="fecha.ini" 
            datepicker-options="dateOptions" 
            date-disabled="disabled(date, mode)" 
            ');?>
            <span class="input-group-addon" ng-click="fecha.ini=true;"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
    </div>
    <div class="form-group">
        <div class="input-group ui-datepicker">
        <?=form_input('fecha_fin','','class="form-control" placeholder="Fin"
        
        uib-datepicker-popup="yyyy-MM-dd"
            ng-model="fecha_fin"
            is-open="fecha.fin" 
            datepicker-options="dateOptions" 
            date-disabled="disabled(date, mode)" 
        
        ');?>
        <span class="input-group-addon" ng-click="fecha.fin=true;"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
    </div>
    <button type="submit" class="btn btn-success"><i class="fa fa-search"></i> Buscar</button>
    <hr />
        <?php if(!$status){?>
        <div class="alert alert-info"><?=lang('apoyos:report_help')?></div>
        <?php }?>
    <?php }?>
    
    <?php echo form_close();?>
    <?php if($status && !$data){?>
            <div class="alert alert-info"><i class="fa fa-exclamation-circle"></i> <?=lang('apoyos:report_not_found')?></div>
                   
    <?php }?>
    <?php if($data){ ?>
    
    <div class="alert alert-info"><?=sprintf(lang('apoyos:report_found'),format_date_calendar($_POST['fecha_ini']),format_date_calendar($_POST['fecha_fin']))?></div>
    <?php 
    
    
    $g_total_importe    = 0;
    $g_total_comprobado = 0;
    
    ?>
        <?php foreach($data as $concepto=>$depositos){?>
        <h4 class="text-success"><a target="_blank" href="<?=base_url('admin/apoyos/download/?'.http_build_query($_POST).'&concepto='.urlencode($concepto))?>" tooltip-placement="top" uib-tooltip="Descargar xlsx de este concepto"><?=$concepto?></a></h4>
        <hr />
        
        <?php 
        $total_importe    = 0;
        $total_comprobado = 0;
        ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Director</th>
                    <th width="10%" title="Fecha que se realizó el depósito">Fecha</th>
                    <th width="10%">Importe</th>
                    <th width="10%">Comprobado</th>
                    <th width="10%">Saldo</th>
                    <th width="2%"></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach($depositos as $deposito){?>
                 <?php 
                $total_importe    += $deposito->importe;
                $total_comprobado += $deposito->comprobado;
                
                $g_total_importe    += $deposito->importe;
                $g_total_comprobado += $deposito->comprobado;
                ?>
                <tr>
                    <td>
                        <?=$deposito->nombre_director?><br />
                        <span class="text-muted"><?=$deposito->nombre_centro?></span>
                    </td>
                    <td><?=format_date_calendar($deposito->fecha_deposito)?></td>
                    <td class="text-right"><?=number_format($deposito->importe,2)?></td>
                    <td class="text-right"><?=number_format($deposito->comprobado,2)?></td>
                    <td class="text-right"><?=number_format($deposito->importe-$deposito->comprobado,2)?></td>
                    <td class="text-center"><?=($deposito->importe-$deposito->comprobado)>0?'<i class="fa fa-exclamation text-danger" tooltip-placement="left" uib-tooltip="Existe un saldo pendiente por comprobar"></i> ':'<i class="fa fa-check text-success" tooltip-placement="left" uib-tooltip="El saldo ha sido finiquitado"></i>'?></td>
                </tr>
            <?php  }?>
            </tbody>
            <tfoot>
                <tr>
                    
                    <th class="text-right" colspan="2">Total</th>
                    <th class="text-right"><?=number_format($total_importe,2)?></th>
                    <th class="text-right"><?=number_format($total_comprobado,2)?></th>
                    <th class="text-right"><?=number_format($total_importe-$total_comprobado,2)?></th>
                </tr>
            </tfoot>
        </table>
        <?php }?>
        <hr />
        <div class="row invoice-inner" ng-init="percent=<?=number_format(($g_total_comprobado*100)/$g_total_importe,0)?>">
            <div class="col-md-3 col-md-offset-3">
               
                            <div class="panel-body text-center">
                                <div easypiechart options="easypiechart3.options" percent="easypiechart3.percent" class="easypiechart">
                                    <span class="pie-percent" ng-bind="easypiechart3.percent"></span>
                                </div>
                            </div>
                            <p class="text-center">Porcentaje comprobado</p>
                            
                
            </div>
            <div class="col-md-4  col-md-offset-2 invoice-sum text-right">
                         
                                  <ul class="list-unstyled">
                                    <li>Total a comprobar:  <?=number_format($g_total_importe,2)?></li>
                                   
                                    <li>Total facturas: <?=number_format($g_total_comprobado,2)?></li>
                                    <li><strong>Saldo: <?=number_format($g_total_importe-$g_total_comprobado,2)?></strong></li>
                                </ul>
             </div>
        </div>
    <?php }?>
    <div class="form-actions text-center">
        <a href="<?=base_url('admin/apoyos')?>" class="btn btn-w-md ui-wave btn-default">Regresar</a>
        <?php if($data){ ?>
        <a href="<?=base_url($this->uri->uri_string())?>?refresh=1" class="btn btn-w-md ui-wave btn-default"><i class="fa fa-refresh"></i> Reiniciar</a>
        <a href="<?=base_url('admin/apoyos/download/?'.http_build_query($_POST))?>" target="_blank" class="btn btn-w-md ui-wave btn-primary">Descargar XLSX</a>
        <?php }?>
        
    </div>
    
</section>