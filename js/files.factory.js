(function () {
    'use strict';
     var isOnGitHub = window.location.hostname === 'blueimp.github.io',
        url = isOnGitHub ? '//jquery-file-upload.appspot.com/' : 'files/upload';
    angular.module('app.files')
    .factory('$treeData',['$rootScope','$http','$sce','logger',treeDataFactory]);
    
    function treeDataFactory($rootScope,$http,$sce,logger)
    {
        return {
            delete_folder:function($itemScope)
            {
                     var $item = $(window).data($itemScope.file.menu+'_'+$itemScope.file.id),
                          type  = $itemScope.file.menu,
                          id    = $itemScope.file.id,
                          index = $itemScope.$index;
                      
                      var post_data = {};
                      
                      post_data[type + '_id'] = id;
                      
                     
                     
                      $http.post(SITE_URL + 'admin/galeria/delete_' + type,post_data).then(function(response){
                        
                          var result = response.data;
                        
                          if(result.status)
                          {
                              
                              $itemScope.files.splice(index,1);
                              
                              
                              $.each($rootScope.folders,function(index,data){
                                    if(data.id == id)
                                    {
                                        $rootScope.folders.splice(index,1);
                                        return false;
                                    }
                                  
                              });
                              
                              
                              logger.logSuccess(result.message);
                          }
                          else
                          {
                              logger.logError(result.message);
                          }
                         
                          
                          
                        
                      });
            },
            delete_file:function($itemScope)
            {
                     if(!confirm('¿Desea eliminar el siguiente elemento?'))
                      {
                          return false;
                      }
                    
          	          var $item = $(window).data($itemScope.file.menu+'_'+$itemScope.file.id),
                          type  = $itemScope.file.menu,
                          id    = $itemScope.file.id,
                          index = $itemScope.$index;
                      
                      var post_data = {};
                      
                      post_data[type + '_id'] = id;
                      
                     
                     
                      $http.post(SITE_URL + 'admin/galeria/delete_' + type,post_data).then(function(response){
                        
                          var result = response.data;
                        
                          if(result.status)
                          {
                              
                              $itemScope.files.splice(index,1);
                              logger.logSuccess(result.message);
                          }
                          else
                          {
                              logger.logError(result.message);
                          }
                         
                          
                          
                        
                      });
            },
            folder_contents:function(id_parent)
            {
                  var items = [];
                  $rootScope.$broadcast('preloader:active');
                  
                  $rootScope.folders = folders;
                  
                  
                  $http.post('galeria/folder_contents',{parent:id_parent}).then(function(response){
                            
                      var results=angular.fromJson(response.data);       
                       
                       
                      delete(results.data.parent_id);  
                       
                      angular.forEach(results.data,function(data,type){
                          
                          angular.forEach(data,function(item,index){
                                
                               
                                item.el_type     = type + (type==='file'? ' type-'+item.type:'');
                                item.menu        = type;                                                
                                item.on_editable = false;
                                item.filesize    = type == 'file'? (item.filesize < 1000 ? item.filesize+'Kb' : (item.filesize / 1000)+'MB'):false;
                                
                                
                                item.img         = item.type && item.type === 'i'?SITE_URL+'files/cloud_thumb/'+item.id+'?'+(new Date().getMilliseconds()):'';
                                
                                items.push(item);
                                
                                
                                
                            
                          });
                         
                      });
                      
                       
                      if(!items)
                      {
                           logger.logError('Carpeta vacia');
                      }
                      
                      $rootScope.current_level = id_parent;               
                      $rootScope.files         = items;
                      
                      $rootScope.$broadcast('preloader:hide');
                      
                      
                      
                     
                      
                      
                 });
                
            },
            new_folder:function(level)
            {
                    var new_folder={
                         
                         
                         menu:'folder',
                         on_editable : true,                         
                         name:pyro.lang.new_folder_name,                        
                         el_type:'folder',
                         id:false,
                         
                         
                      };
                      
                      var post_data={parent : $rootScope.current_level?$rootScope.current_level:level, name : pyro.lang.new_folder_name};
                      
                      
                      $http.post(SITE_URL+'admin/galeria/new_folder',post_data).then(function(response){
                        
                           var result = response.data;
                           
                           if(result.status)
                           {
                               
                               new_folder['id'] = result.data.id;
                               
                               $rootScope.files.push(new_folder);
                               
                               
                               $rootScope.folders.push(new_folder);
                              
                               
                              
                                
                           }
                           
                           
                            
                       });
                       
                        return new_folder;
            }
        }
    }
    
    
})();