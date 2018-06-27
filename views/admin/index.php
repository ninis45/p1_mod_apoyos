<div class="content" ng-controller="IndexCtrl">
     <div class="lead text-success"><?=lang('apoyos:title')?></div>
    <?php echo form_open('admin/apoyos/'.$anio, 'class="form-inline" method="get" ') ?>
            
                
                <div class="form-group col-md-4">
                    
                    
                    <?=form_dropdown('c',array(''=>' [ Todos los centros ] ')+$centros,false,'class="form-control"')?>
                </div>
            
               <div class="form-group col-md-4">
                   
                    <?=form_input('q','','class="form-control" placeholder="Buscar ID o concepto"')?>
               </div>
                <button class="md-raised btn btn-default"><i class="fa fa-search"></i> Buscar</button>
                <?php if($_GET):?>
                <a href="<?=base_url('admin/apoyos/'.$anio)?>" class="md-raised btn btn-success"><i class="fa fa-refresh"></i> Mostrar todos</a>
                <?php endif;?>
                
                
                
                
                
            
        <?php echo form_close() ?>
        <hr />


  
    <div class="ui-tab-container ui-tab-horizontal" >
        <uib-tabset justified="false" class="ui-tab">
            
            <uib-tab heading="Pendientes" >
        
        
                  
                  
                  <uib-accordion close-others="!oneAtATime" class="ui-accordion">
                      <?php foreach($data['Pendientes'] as $concepto=>$depositos ){?>
                     
                      <uib-accordion-group     heading="<?=str_replace('"','',$concepto)?> (<?=count($depositos)?>)"  > 
                            
                            <div class="divider text-right">
                                Total registros:   <?=count($depositos)?>  
                                
                            </div>
                            <table class="table" width="100%">
                                <thead>
                                 <tr>
                                   <th width="14%">Fecha</th>
                                   <th >Centro</th>
                                  
                                  
                                   <th width="10%">Importe</th>
                                   <th width="10%"></th>
                                 </tr>
                                 </thead>
                                 <tbody>
                                        <?php $total_importe = 0;?>
                                        <?php foreach($depositos as $deposito){?>
                                            <?php $total_importe+=$deposito->importe;?>
                                           <tr >
                                              <td><?=format_date_calendar($deposito->fecha_deposito)?></td>
                                              <td><?=$deposito->nombre_centro?></td>
                                             
                                             
                                              <td class="text-right"><?=number_format($deposito->importe,2)?></td>
                                              <td class="text-center  text-green box-icon">
                                              <?php if(group_has_role('apoyos','create')):?>
                                                <a href="<?=base_url('admin/apoyos/create/'.$anio.'/'.$deposito->id_deposito)?>" ui-wave class="btn-icon  btn-icon-sm btn-tumblr" title="Crear registro"><i class="fa fa-plus" ></i></a>
                                              <?php endif;?>
                                              
                                                  
                                                
                                               </td>
                                           </tr>
                                       <?php }?> 
                                   
                                  </tbody>
                                  <tfoot>
                                      <tr>
                                        <th colspan="2" class="text-right">Total:</th>
                                        <th class="text-right"><?=number_format($total_importe,2)?></th>
                                        <th></th>
                                      </tr>
                                  </tfoot>
                           </table>
                      </uib-accordion-group>
                      <?php }?>
                  </uib-accordion>
            </uib-tab>
        
            <uib-tab heading="Recibidos">
                <uib-accordion close-others="!oneAtATime" class="ui-accordion">
                <?php if($data['Recibidos']){?>
                     
                      <?php foreach($data['Recibidos'] as $concepto=>$depositos ){?>
                      <uib-accordion-group   heading="<?=str_replace('"','',$concepto)?>"  > 
                            <div class="divider text-right">
                                Total registros:   <?=count($depositos)?>  
                                <!--a class="" target="_blank" href="<?=base_url('admin/apoyos/download/'.$anio.'?centro='.$this->input->get('f_centro').'&concepto='.urlencode($concepto))?>"><i class="fa fa-download"></i> Desgargar XLS</a-->
                            </div>
                            <table class="table" width="100%">
                                <thead>
                                 <tr>
                                  <th width="10%">ID</th>
                                   <th width="14%">Fecha</th>
                                   <th >Centro</th>
                                  
                                  
                                   <th width="10%">Importe</th>
                                   <th width="10%">Comprobado</th>
                                   <th width="10%"></th>
                                 </tr>
                                 </thead>
                                 <tbody>
                                  
                                        <?php 
                                        $total_importe    = 0;
                                        $total_comprobado = 0;
                                        ?>
                                        <?php foreach($depositos as $deposito){?>
                                            <?php 
                                            $total_importe    += $deposito->importe;
                                            $total_comprobado += $deposito->comprobado;
                                            ?>
                                           <tr class="<?=$deposito->estatus=='Rechazado'?'danger':''?>">
                                              <td><?=$deposito->id_apoyo?></td>
                                              <td><?=format_date_calendar($deposito->fecha_deposito)?></td>
                                              <td><?=$deposito->nombre_centro?></td>
                                             
                                              
                                              <td class="text-right"><?=number_format($deposito->importe,2)?></td>
                                              <td class="text-right"><?=number_format($deposito->comprobado,2)?></td>
                                              <td class="text-center  text-green box-icon">
                                              
                                              
                                                <a href="<?=base_url('admin/apoyos/edit/'.$deposito->id_apoyo)?>" ui-wave class="btn-icon  btn-icon-sm btn-primary" title="Detalles registro"><i class="fa fa-pencil"></i></a>
                                                <?php if(group_has_role('apoyos','delete')):?>
                                                 <a href="<?=base_url('admin/apoyos/delete/'.$anio.'/'.$deposito->id_apoyo.'/')?>" ui-wave class="btn-icon  btn-icon-sm btn-danger" confirm-action><i class="fa fa-times"></i></a>
                                                <?php endif;?>
                                               </td>
                                           </tr>
                                       <?php }?>
                                       
                                   
                                  </tbody>
                                  <tfoot>
                                          <tr>
                                            <th colspan="2" class="text-right">Total:</th>
                                            <th class="text-right"><?=number_format($total_importe,2)?></th>
                                            <th class="text-right"><?=number_format($total_comprobado,2)?></th>
                                            <th></th>
                                          </tr>
                                  </tfoot>
                           </table>
                      
                      </uib-accordion-group>
                      <?php }?>
                <?php }else{?>
                    <div class="alert alert-info text-center"><?=lang('global:not_found')?></div>
                <?php }?>
                </uib-accordion>
            
        
        
        
            </uib-tab>
        
            <uib-tab heading="Validados">
                <uib-accordion close-others="!oneAtATime" class="ui-accordion">
                <?php if($data['Validados']){?>
                    <?php foreach($data['Validados'] as $concepto=>$depositos ){?>
                    <uib-accordion-group  heading="<?=str_replace('"','',$concepto)?>"  > 
                            <div class="divider text-right">
                                Total registros:   <?=count($depositos)?>  
                                <!--a class="" target="_blank" href="<?=base_url('admin/apoyos/download/'.$anio.'?concepto='.urlencode($concepto))?>"><i class="fa fa-download"></i> Desgargar XLS</a-->
                            </div>
                            <table class="table" width="100%">
                                <thead>
                                 <tr>
                                   <th width="10%">ID</th>
                                   <th width="14%">Fecha</th>
                                   <th >Centro</th>
                                  
                                  
                                   <th width="10%">Importe</th>
                                   <th width="10%">Comprobado</th>
                                   <th width="10%"></th>
                                 </tr>
                                 </thead>
                                 <tbody>
                                  
                                        <?php 
                                        $total_importe    = 0;
                                        $total_comprobado = 0;
                                        ?>
                                        <?php foreach($depositos as $deposito){?>
                                            <?php 
                                            $total_importe    += $deposito->importe;
                                            $total_comprobado += $deposito->comprobado;
                                            ?>
                                           <tr>
                                              <td><?=$deposito->id_apoyo?></td>
                                              <td><?=format_date_calendar($deposito->fecha_deposito)?></td>
                                              <td><?=$deposito->nombre_centro?></td>
                                             
                                              
                                              <td class="text-right"><?=number_format($deposito->importe,2)?></td>
                                              <td class="text-right"><?=number_format($deposito->comprobado,2)?></td>
                                              <td class="text-center  text-green box-icon">
                                              
                                              
                                                <a href="<?=base_url('admin/apoyos/details/'.$deposito->id_apoyo)?>" ui-wave class="btn-icon  btn-icon-sm btn-primary" title="Detalles registro"><i class="fa fa-search"></i></a>
                                                
                                               </td>
                                           </tr>
                                           <?php }?> 
                                     
                                  </tbody>
                                  <tfoot>
                                          <tr>
                                            <th colspan="2" class="text-right">Total:</th>
                                            <th class="text-right"><?=number_format($total_importe,2)?></th>
                                            <th class="text-right"><?=number_format($total_comprobado,2)?></th>
                                            <th></th>
                                          </tr>
                                  </tfoot>
                           </table>
                      
                      </uib-accordion-group>
                      <?php }?>
                <?php }else{?>
                    <div class="alert alert-info text-center"><?=lang('global:not_found')?></div>
                <?php }?>
                </uib-accordion>
                 
        
        
            </uib-tab>
        
        </uib-tabset>
    </div> 


</div> 
