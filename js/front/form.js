var total_facturas = 0;
var total_depositos = 0;
$(document).ready(function(){

    var tipo = '';//$('input[name="tipo"]').val();
    
    
     load_facturas(facturas);
     load_depositos(depositos);
    $('#display_importe').html(total_importe.toFixed(2));
    $('#Modal').on('hidden.bs.modal', function (e) {
         $('input[name="tipo"]').attr('checked',false);
    });
    $('#Modal').on('show.bs.modal', function (event) {
        
        //$('input[name="tipo"]').val('');
        tipo = '';
        $('.block-deposito,.block-factura').addClass('hide');
    });
    $('body').delegate('.remove-factura','click',function(e){
        
        e.preventDefault();
        
        var anchor = $(this),
            index  = $(this).data('index'),
            tr     = $(anchor).closest('tr');
            
            
        facturas.splice(index,1);
        
        load_facturas(facturas);
        //tr.remove();
        
       
    });
    $('body').delegate('.remove-deposito','click',function(e){
        
        e.preventDefault();
        
        var anchor = $(this),
            index  = $(this).data('index'),
            tr     = $(anchor).closest('tr');
            
            
        depositos.splice(index,1);
        
        load_depositos(depositos);
        //tr.remove();
        
       
    });
    $('input[name="tipo"]').on('change',function(){
        
         tipo = $(this).val();
         $('.block-deposito,.block-factura').addClass('hide');
        if(tipo=='deposito')
        {
            $('.block-deposito').removeClass('hide');
        }
        if(tipo=='factura')
        {
             $('.block-factura').removeClass('hide');
        }
    });
    $('#add-docs').on('click',function()
    {
            
            if(tipo == '')
            {
                alert('Seleccionar el tipo de documento');
                return false;
            }
            
            if(tipo=='deposito')
            {
                 var total     = $('input[name="total"]').val(),
                     operacion = $('input[name="no_operacion"]').val(),
                     banco     = $('input[name="banco"]').val();
                 depositos.push({operacion:operacion,total:total,banco});
                 
                 load_depositos(depositos);
                 
                 $('input[name="total"]').val('');
                 $('input[name="no_operacion"]').val('');
                 $('input[name="banco"]').val('');
                 $('#Modal').modal('hide');
            }
            if(tipo=='factura')
            {
                upload_files();
            }
            
            console.log('operacion:'+tipo);
            //load_facturas(facturas);
        
    });


 });
function upload_files()
{
        var data   = new FormData();
        var file_xml   = $('#xml_file')[0].files[0];
        var file_pdf   = $('#pdf_file')[0].files[0];
        var tipo   = $('input[name="tipo"]').val();
       // var parent = $(element).parent('label'),
         //   type   = $(element).data('file');
           // html   = ' | <a href="javascript:;" data-type="'+type+'" class="remove">Eliminar</a>';
      
      
        data.append('xml',file_xml);
        data.append('pdf',file_pdf);



        $.ajax({
            url: SITE_URL+'apoyos/upload',
            type: 'POST',
            data: data,
           
            processData: false, // Don't process the files
            contentType: false, // Set content type to false as jQuery will tell the server its a query string request
            enctype: 'multipart/form-data',

            success: function(data, textStatus, jqXHR)
            {
               
                
                var response = data;
                //parent.find('div.jumbotron').append(data.message);
                if(data.status == false)
                {
                    $('#notices-modal').html(data.message);
                  
                   
                   
                   
                }
                else
                {
                    facturas.push({tipo:tipo, xml:response.data.xml.data.id,pdf:response.data.pdf.data.id,total:response.data.xml.data.total,folio_uuid:response.data.xml.data.folio_uuid});
                    load_facturas(facturas);
                    
                    
                    $('#Modal').modal('hide');
                    //location.href = action_redirect;
                 //  parent.find('input[type="hidden"]').val(data.data.id);
                    //parent.find('span').html(data.data.name+' '+html);
                    //$(element).remove();
                }
                if(typeof data.error === 'undefined')
                {
                    // Success so call function to process the form
                    //submitForm(event, data);
                }
                else
                {
                    // Handle errors here
                    console.log('ERRORS: ' + data.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown)
            {
                // Handle errors here
                console.log('ERRORS: ' + textStatus);
                // STOP LOADING SPINNER
            },
            xhr: function() {
                var myXhr = $.ajaxSettings.xhr();
                if (myXhr.upload) {
                    // For handling the progress of the upload
                    myXhr.upload.addEventListener('progress', function(e) {
                       
                        if (e.lengthComputable) {
                            /*$('#counter').val(e.loaded);
                            $('progress').attr({
                                value: e.loaded,
                                max: e.total,
                            });*/
                            var loaded = e.loaded*100/e.total;
                         //  apoyo.progress(element,loaded);
                        }
                    } , false);
                }
                
               
                return myXhr;
            },
        });
}
function load_depositos(depositos)
{
     total_depositos = 0;
    var html = '';
    $.each(depositos,function(index,value){
         total_depositos += parseFloat(value.total);
        html+='<tr><td><input type="hidden"  value="'+value.operacion+'" name="depositos['+index+'][operacion]"/><input type="hidden"  value="'+value.banco+'" name="depositos['+index+'][banco]"/><input type="hidden"  value="'+value.total+'" name="depositos['+index+'][total]"/> '+value.banco+'</td><td>'+value.operacion+'</td><td>'+value.total+'</td>'+(typeof SITE_URL != 'undefined'? '<td class="text-center"><a href="#" class="btn btn-color-grey-light btn-small remove-deposito" data-index="'+index+'"><i class="fa fa-trash"></i></a></td>':'')+'</tr>';
    });
    
    $('.display_depositos').html(total_depositos.toFixed(2));
    $('#bind-depositos').html(html);
    
    display_saldo();
}
function load_facturas(facturas)
{
   
    total_facturas = 0;
    var html = '';
    $.each(facturas,function(index,value){
       total_facturas += parseFloat(value.total);
        html+='<tr><td><input type="hidden"  value="'+value.total+'" name="facturas['+index+'][total]"/><input type="hidden" value="'+value.xml+'" name="facturas['+index+'][xml]"/><input type="hidden" value="'+value.pdf+'" name="facturas['+index+'][pdf]"/><input type="hidden" value="'+value.folio_uuid+'" name="facturas['+index+'][folio_uuid]"/>'+value.pdf+'</td><td>'+value.xml+'</td><td class="text-right">'+value.total+'</td>'+(typeof SITE_URL != 'undefined'? '<td class="text-center"><a href="#" class="btn btn-color-grey-light btn-small remove-factura" data-index="'+index+'"><i class="fa fa-trash"></i></a></td>':'')+'</tr>';
    });
    
    if(total_facturas > parseFloat(total_importe))
    {
        ///total_facturas = 0;
    }
    //else
    $('.display_facturas').html(total_facturas.toFixed(2));
    
    $('#bind-facturas').html(html);
    
    display_saldo();
}
function display_saldo()
{
    $('#display_saldo').html((parseFloat(total_importe) - (total_facturas+total_depositos)).toFixed(2));
}

//como la utilizamos demasiadas veces, creamos una función para 
//evitar repetición de código
function showMessage(message){
    $(".messages").html("").show();
    $(".messages").html(message);
}
