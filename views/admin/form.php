<div ng-controller="InputCtrl" class="panel panel-body" ng-init="method='<?=$this->method?>'">
<?php echo form_open_multipart(uri_string(), 'name="frm_apoyos"'); ?>
        <div class="lead text-success">
            
                <?=lang('apoyos:'.$this->method).($this->method!='create'?', ID #'.$apoyo->id:'')?>
                
                
            
        </div>


    <div class="ui-tab-container ui-tab-horizontal" ng-init="importe=<?=$deposito->importe?>">
        
        
    	<uib-tabset justified="false" class="ui-tab">
    	        <uib-tab heading="Información General">
                    <?php if($this->method=='create'){ ?>
                        <div class="alert alert-info"><?=lang('apoyos:init')?></div>
                    <?php }?>
                    <fieldset>

                        
                        <div class="form-group">
                            <?=form_textarea(array('name'=>'concepto','value'=>$deposito->concepto,'class'=>'form-control' ,'rows'=>'3','disabled'=>true))?>
                        </div>  
                        <div class="row">
                          <div class="col-md-6">
                             <div class="form-group" >
                                <label>Centro</label>
                                 
                                         <?=form_input('nombre_centro',null,'class="form-control" 
                                         ng-model="nombre_centro"
                                         disabled
                                         ng-init="nombre_centro=\''.$deposito->nombre_centro.'\'"
                                        ')?>
                                <?=form_error('nombre_centro')?>
                                
                             </div> 
                             <div class="form-group" ng-init="importev='<?=number_format($deposito->importe,2)?>'">
                                <label>Importe</label>
                                 <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon1">$</span>
                                        <?=form_input('importev',null,'class="form-control" disabled ng-model="importev"')?>   
                                </div>
                              </div>
                              <div class="form-group" >
                                <label>Fecha depósito</label>
                                <?=form_input('fecha_deposito',format_date_calendar($deposito->fecha_deposito),'class="form-control" disabled')?>
                              </div>
                              <?php if($this->method != 'create'){?>
                              <div class= "form-group">
                                    <label>Estado de Comprobación</label>
                                    <?=form_dropdown('estatus',array('Pendiente'=>'Pendiente','Validado'=>'Validado','Rechazado'=>'Rechazado'),$apoyo->estatus,'class="form-control" '.($apoyo->estatus=='Validado'?'disabled':''))?>
                                    
    
                                    
                              </div> 
                              <?php }?>
                          </div> 
                          <div class="col-md-6">
                            <div class="form-group"  >
                                <label>Director</label>
                                                       
                                 <?=form_input('nombre_director',null,'class="form-control" 
                                  ng-model="nombre_director"
                                  disabled
                                  ng-init="nombre_director=\''.$deposito->nombre_director.'\'"
                                 ')?>

                            </div>  
                            <?php if($this->method != 'create'){?>
                            <div class="form-group"  >
                                <label>Observaciones</label>
                                <?=form_textarea(array('name'=>'observaciones','value'=>$apoyo->observaciones,'class'=>'form-control' ,'rows'=>'4','placeholder'=>'Anota tus observaciones aquí','ng-disabled'=>'method==\'details\''))?> 
                            </div>
                            <?php }?>
                          </div> 
                        </div>
                        <?php if($this->method=='create'):  ?>
                         <input type="hidden" name="id_deposito" value="<?=$apoyo->id_deposito?>" />
                         <input type="hidden" name="id_centro" value="<?=$datos->id_centro?>" />
                         <input type="hidden" name="id_director" value="<?=$datos->id?>"/>
                         <input type="hidden" name="status" value="1"/>
                       <?php endif;?>

                        
                        <div class="divider"></div>


                        
  
                   </fieldset>  
                 </uib-tab>


                 <?php if($this->method!='create'):?>
                 <uib-tab heading="Documentos comprobatorios">
                     <?php if($this->method=='edit'):?>
                     <div class="divider text-right">
                        <a href="#" ng-click="open_modal()" class="btn btn-primary"><i class="fa fa-plus"></i> Agregar documentos</a>
                     </div>
                     <hr />
                     <?php endif;?>
                     <div class="alert alert-warning" ng-if="saldo<0">
                        <?=lang('apoyos:maximo')?>
                     </div>
                     <div class="row invoice-inner">
                        <h4>Depósitos</h4>
                        <table class="table">
                                <thead>
                                    <tr>
                                        <th >Banco</th>
                                        <th >No. de operación</th>
                                       
                                        <th width="10%">Importe</th>
                                        <?php if($this->method=='edit'):?>
                                        <th width="14%">Acciones</th>
                                        <?php endif;?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="deposito in depositos">
                                        <td>
                                        <input type="hidden" name="depositos[{{$index}}][banco]" value="{{deposito.banco}}" />
                                        <input type="hidden" name="depositos[{{$index}}][operacion]" value="{{deposito.operacion}}" />
                                        <input type="hidden" name="depositos[{{$index}}][total]" value="{{deposito.total}}" />
                                        {{deposito.banco}}
                                        
                                        </td>
                                        <td>{{deposito.operacion}}</td>
                                        <td>{{deposito.total|number:2}}</td>
                                         <?php if($this->method=='edit'):?>
                                        <td >
                                           
                                            <a href="#" title="Eliminar registro"  ng-click="remove_deposito($index)" class="btn btn-mini btn-danger"><i class="fa fa-remove"></i></a>
                                        
                                        </td>
                                        <?php endif;?>
                                    </tr>
                                </tbody>
                        </table>
                        <h4>Facturas</h4>
                        <div class="col-md-12">
                             <table class="table">
                                <thead>
                                    <tr>
                                        <th width="10%">PDF</th>
                                        <th width="10%">XML</th>
                                        <th >Estado de la factura</th>
                                        <th width="10%">Importe</th>
                                        <?php if($this->method=='edit'):?>
                                        <th width="14%">Acciones</th>
                                        <?php endif;?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="(i,file) in files">
                                        <td>
                                            <input type="hidden" name="facturas[{{$index}}][xml]" value="{{file.xml}}" /> 
                                            <input type="hidden" name="facturas[{{$index}}][pdf]" value="{{file.pdf}}"/>
                                            <input type="hidden" name="facturas[{{$index}}][total]" value="{{file.total}}" />
                                            <input type="hidden" name="facturas[{{$index}}][xml_uuid]" value="{{file.xml_uuid}}" />
                                             <a target="_blank" href="<?=base_url('files/download/{{file.pdf}}')?>" data-toggle="popover" title="Popover title" data-content="">{{file.pdf}}</a></td>
                                        <td>
                                            <a target="_blank" href="<?=base_url('files/download/{{file.xml}}')?>">{{file.xml}}</a>
                                            
                                            
                                        </td>
                                        <td>
                                        
                                            <div ng-if="!file.messages" class="text-warning">Al parecer el documento XML no ha sido validado. Para realizarlo pulse el boton <a href="<?=base_url('admin/apoyos/valid_xml/'.$apoyo->id.'/{{file.id}}/{{file.xml}}')?>" class="btn btn-default">Validar documento</a></div>
                                            
                                            
                                            <span class="text-success"  ng-class="{'text-danger':message.code==0}" ng-repeat="(j,message) in file.messages">
                                                <i ng-if="message.code==1" class="fa fa-check"></i> 
                                                <i ng-if="message.code==0" class="fa fa-remove"></i>
                                                {{message.message}} 
                                                 <input type="hidden" name="facturas[{{i}}][messages][{{j}}][code]" value="{{message.code}} "/>
                                                 <input type="hidden" name="facturas[{{i}}][messages][{{j}}][message]" value="{{message.message}} "/>
                                            </span>
                                        </td>
                                        <td class="text-right">{{file.total|number:2}}</td>
                                        <?php if($this->method=='edit'):?>
                                        <td>
                                            
                                            <a href="#" title="Eliminar registro" ng-click="remove_factura(i)" class="btn btn-mini btn-danger"><i class="fa fa-remove"></i></a>
                                        </td>
                                        <?php endif;?>
                                    </tr>
                                </tbody>
                                <!--tfoot>
                                    <tr>
                                        <td colspan="3" class="text-right">Total:</td>
                                        <td class="text-right">{{total| number:2}}</td>
                                        <?php if($this->method=='edit'):?>
                                        
                                        <td></td>
                                        <?php endif;?>
                                    </tr>
                                </tfoot-->
                             </table>
                        </div>
                        
                        <div class="col-md-4 col-md-offset-8 invoice-sum text-right">
                     
                              <dl class="dl-horizontal">
                                <dt>Total a comprobar:</dt>  <dd>{{importe | number:2}}</dd>
                               
                                <dt>Total facturas: </dt><dd>{{total_facturas| number:2}}</dd>
                                <dt>Total depositos:</dt><dd> {{total_depositos| number:2}}</dd>
                                <dt>Saldo: </dt><dd>{{(importe - (total_facturas+total_depositos))| number:2}}</dd>
                            </dl>
                         </div>
                     </div>
                     

                 

  

  
    
    
    
    
    
    
   
   

                
                    

               </uib-tab>
              <?php endif;?>
            </uib-tabset>

        </div>   
        
        <?php if($apoyo->updated_on): ?>
            <hr />
            <p class="text-right">Actualizado: <?php echo format_date($apoyo->updated_on,'d/M/Y')?></p>
        <?php endif;?>
        <div class="divider"></div>
    
       <div class="buttons">
    	    <?php //$this->load-btn btn-w-md ui-wave>view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))) ?>
            <a href="<?=base_url('admin/apoyos/'.$anio)?>" class="btn btn-w-md ui-wave btn-default">Cancelar</a>
            <?php if($this->method == 'create'){?>
            <button type="submit" class="btn btn-w-md ui-wave btn-success">Siguiente</button>
            <?php }else{?>
            <button type="submit" class="btn btn-w-md ui-wave btn-success" value="save" confirm-action>Guardar</button>
            <?php }?>
       </div>
    <?php echo form_close();?>
</div>
<?php if($this->method=='edit'):?>
<script type="text/ng-template" id="myModalUpload.html">
 <form name="myForm">
                            <div class="modal-header">
                                <h3>Subir archivos</h3>
                            </div>
                             
                            <div class="modal-body" id="files-uploader">
                                <div class="alert alert-warning" ng-if="!dispose">Favor de no cerrar esta ventana, hasta terminar con el proceso</div>
                                <div class="form-group">
                                    <label>Tipo de documento</label>
                                    <div class="radio">
                                        <label><input type="radio" name="tipo" ng-model="tipo" value="factura" /> Factura</label>
                                        <label><input type="radio" name="tipo" ng-model="tipo" value="deposito" /> Deposito</label>
                                    </div>
                    
                                </div>
                                <div class="form-group" ng-if="tipo=='deposito'">
                                    <label>Banco</label> 
                                    <input type="text" class="form-control" ng-model="form.banco" required/>
                                </div>
                                <div class="form-group" ng-if="tipo=='deposito'">
                                    <label>No. de operación</label>
                                    <input type="text" class="form-control" ng-model="form.operacion" required/>
                                </div>
                                
                                <div class="form-group" ng-if="tipo=='deposito'">
                                    <label>Importe</label>
                                    <input type="text" class="form-control" ng-model="form.total" formato-moneda required/>
                                </div>
                                <div class="form-group" ng-if="tipo=='factura'">
                                    <label>Archivo XML</label>
                                    
                                    <input type="file"  accept="application/xml" ng-disabled="!dispose" ngf-select="upload_file(file_xml,'xml')" ng-model="file_xml" name="xml" ngf-model-invalid="errorFile"
                                     ngf-max-size="80MB" required/>
                                     <md-progress-linear md-mode="determinate" ng-show="file_xml.progress >= 0" value="{{file_xml.progress}}"></md-progress-linear>
                                     <span class="err" ng-show="errorMsg">{{errorMsg}}</span>
                                     
                                 </div>
                                 <div class="form-group" ng-if="tipo=='factura'">
                                    <label>Archivo PDF</label>
                                    <input type="file"   ng-disabled="!dispose" accept=".pdf" ngf-select="upload_file(file_pdf,'pdf')"  ng-model="file_pdf"
                                     ngf-max-height="10000" ngf-max-size="80MB"/>
                                    <md-progress-linear md-mode="determinate" ng-show="file_pdf.progress >= 0" value="{{file_pdf.progress}}"></md-progress-linear>
                                     <span class="err" ng-show="errorMsg">{{errorMsg}}</span>
                                     
                                 </div>
                                  
                                  
                            </div>
                            <div class="modal-footer">
                                 
                                <button ui-wave class="btn btn-flat btn-primary" ng-disabled="!dispose" ng-click="close()" >Cerrar</button>
                                <button ui-wave class="btn btn-flat" ng-disabled="!myForm.$valid"  ng-click="save()" >Guardar</button>
                            </div>
</form>
</script>
<?php endif;?>