<section>

     <div class="container">
        <header><h2><?=lang('apoyos:'.$this->method)?></h2></header>

<?php echo form_open_multipart(uri_string(), 'name="frm_apoyos"'); ?>


 <ul class="nav nav-tabs">

    <li class="active"><a data-toggle="tab" href="#Informacion_general">Información General</a></li>
     <?php if($this->method!='create'):?>
    <li><a data-toggle="tab" href="#Facturas">Documentos comprobatorios</a></li>
    <?php endif?>

  </ul>

  <div class="tab-content">
    <div id="Informacion_general" class="tab-pane fade in active">
                    <?php if($this->method=='create'){ ?>
                        <div class="alert alert-info"><?=lang('apoyos:init')?></div>
                    <?php }?>




                        <div class="form-group">
                            <?=form_textarea(array('name'=>'concepto','value'=>$deposito->concepto,'class'=>'form-control' ,'rows'=>'3','disabled'=>true))?>
                        </div>

                        <div class="row">
                          <div class="col-md-6">
                             <div class="form-group" >
                                <label>Centro</label>

                                         <?=form_input('nombre_centro',$deposito->nombre_centro,'class="form-control"
                                         disabled')?>

                                <?=form_error('nombre_centro')?>

                             </div>

                             <div class="form-group" >
                                <label>Importe</label>
                                 <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon1">$</span>
                                        <?=form_input('importev',number_format($deposito->importe,2),'class="form-control" disabled')?>
                                </div>
                              </div>

                              <?php if($this->method != 'create'){?>
                              <div class= "form-group">


                             <input type="hidden" name="estatus" value="Pendiente"/>

                              </div>
                              <?php }?>
                          </div>


                          <div class="col-md-6">
                            <div class="form-group"  >
                                <label>Director</label>

                                 <?=form_input('nombre_director',$deposito->nombre_director,'class="form-control"
                                 disabled')?>

                            </div>


                          </div>


                        </div>


                        <?php if($this->method=='create'):  ?>
                         <input type="hidden" name="id_deposito" value="<?=$apoyo->id_deposito?>" />
                         <input type="hidden" name="id_centro" value="<?=$datos->id_centro?>" />
                         <input type="hidden" name="id_director" value="<?=$datos->id?>"/>
                         <input type="hidden" name="status" value="1"/>
                       <?php endif;?>





    </div>


    <?php if($this->method!='create'):?>

    <div id="Facturas" class="tab-pane fade">
                    <?php if($this->method=='edit'):?>
                     <div class=" text-right">
                        <a class="btn btn-primary"  data-toggle="modal" data-target="#Modal"><i class="fa fa-plus"></i> Agregar documentos</a>
                     </div>

                     <?php endif;?>

                     <div class="row invoice-inner">
                        <?php if($facturas || $this->method=='edit'): ?>
                        <h4>Facturas</h4>
                        <div class="col-md-12">

                             <table class="table">
                                <thead>
                                    <tr>

                                        <th width="">PDF</th>
                                        <th width="">XML</th>

                                        <th width="10%">Importe</th>
                                        <?php if($this->method=='edit'):?>
                                        <th width="10%">Acciones</th>
                                        <?php endif;?>
                                    </tr>
                                </thead>
                                <tbody id="bind-facturas">

                                </tbody>
                                <!--tfoot>
                                    <tr>
                                        <td colspan="2" class="text-right">Total:</td>
                                        <td class="text-right display_facturas" ></td>
                                         <?php if($this->method=='edit'):?>
                                        <td></td>
                                        <?php endif;?>

                                    </tr>
                                </tfoot-->
                             </table>
                        </div>
                        <?php endif;?>
                         <?php if($fichas || $this->method=='edit'): ?>
                        <h4>Depósitos bancarios</h4>
                        <div class="col-md-12">

                             <table class="table">
                                <thead>
                                    <tr>

                                        <th width="">Banco</th>
                                        <th width="">No. de operación</th>

                                        <th width="10%">Importe</th>
                                        <?php if($this->method=='edit'):?>
                                        <th width="10%">Acciones</th>
                                        <?php endif;?>
                                    </tr>
                                </thead>
                                <tbody id="bind-depositos">

                                </tbody>
                             </table>
                        </div>

                        <?php endif;?>

                        <div class="col-md-4 col-md-offset-8 invoice-sum text-right">

                              <ul class="list-unstyled">
                                <li>Total a comprobar:  <span id="display_importe"></span></li>

                                <li>Total facturas: <span class="display_facturas"></span></li>
                                <li>Total depositos: <span class="display_depositos"></span></li>
                                <li><strong>Saldo: <span id="display_saldo"></span></strong></li>
                            </ul>
                         </div>
                     </div>


   <?php endif;?>


  </div>


<hr />

       <div class="buttons">
            <?php //$this->load-btn btn-w-md ui-wave>view('admin/partials/buttons', array('buttons' => array('save', 'cancel'))) ?>
            <a href="<?=base_url('apoyos/'.$anio)?>" class="btn btn-color-grey-light"><i class="fa fa-th-list"></i> Ir a listado</a>
            <?php if($this->method == 'edit'){?>

            <button type="submit" class="btn btn-success" value="save" confirm-action>Guardar</button>
            <?php }?>
       </div>
    <?php echo form_close();?>

<?php if($this->method=='edit'):?>

  <!-- Modal -->
  <div class="modal fade" id="Modal" role="dialog">
    <div class="modal-dialog">

      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Subir Archivos</h4>
        </div>
        <div class="modal-body" id="files-uploader">
            <div id="notices-modal"></div>
            <div class="form-group"">
                <label>Tipo de documento</label>
                <div class="radio">
                    <label><input type="radio" name="tipo" value="factura" /> Factura</label>
                    <label><input type="radio" name="tipo" value="deposito" /> Deposito</label>
                </div>

            </div>
            <div class="form-group block-deposito">
                <label>Banco</label>
                <input type="text" name="banco" onkeyup="mayus(this);"/>

            </div>
            <div class="form-group block-deposito">
                <label>Número de operacion</label>
                <input type="text" name="no_operacion" onkeyup="mayus(this);"/>

            </div>
            <div class="form-group block-deposito">
                <label>Importe</label>
                <div class="input-group">
                       <span class="input-group-addon" id="basic-addon1">$</span>
                <input type="text" name="total" onblur="formatoMoneda()" id="number_format"/>
              </div>

            </div>

                                <div class="form-group block-factura">
                                    <label>Archivo XML</label>

                                    <input type="file" accept="application/xml" name="xml_file" id="xml_file"/>

                                 </div>
                                 <div class="form-group block-factura">
                                    <label>Archivo PDF</label>
                                    <input type="file"  accept=".pdf" name="pdf_file" id="pdf_file"/>



                                 </div>


        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="add-docs" name="send">Agregar</button>
        <button type="button" class="btn btn-color-grey-light" data-dismiss="modal">Cerrar</button>
        </div>
      </div>

    </div>
  </div>

</div>
<?php endif;?>





    </div>
</section>
