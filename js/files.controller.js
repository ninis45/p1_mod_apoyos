(function () {
    'use strict';
     var isOnGitHub = window.location.hostname === 'blueimp.github.io',
        url = isOnGitHub ? '//jquery-file-upload.appspot.com/' : 'files/upload';
    angular.module('app.files')
      
     
      .directive('uiFolder',['UiTreeHelper',uiFolder])
      .controller('ModalInstanceCtrl', ['$scope', '$uibModalInstance', '$cookies','$timeout','$treeData','Upload', ModalInstanceCtrl])
      .controller('DetailsInstanceCtrl',['$scope','$http','$uibModalInstance','logger','folder',DetailsInstanceCtrl])
      .controller('CtrlFiles', ['$scope','$window','$http','$cookies','$sce','$treeData','logger','Upload','$timeout','$uibModal', ctrlFiles]);
      
      
      
     
      function treeDataFactory($rootScope,$http,$sce)
      {
          var setData=function(id_parent,data)
          {
            
          }
          
          var getData=function(id_parent)
          {
            
          }
          var contentFolders=function(id_parent)
          {
              var items = [];
              $rootScope.$broadcast('preloader:active');
              $http.post('descargas/folder_contents',{parent:id_parent}).then(function(response){
                        
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
                  
                  
                  
                  $rootScope.current_level = id_parent;               
                  $rootScope.folders       = items;
                  
                  $rootScope.$broadcast('preloader:hide');
                  
                  
                  
                 
                  
                  
             });
            
          }
          return {contentFolders:contentFolders,getData:getData,setData:setData};
        
      }
      
    
    function uiFolder(UiTreeHelper)
    {
        return {
          priority: -1,
          restrict: 'A',
          controller: 'TreeNodeController',
          link: function(scope, element, attrs){
            
            
            
            scope.collapsed = !!UiTreeHelper.getNodeAttribute(scope, 'collapsed');
            
            scope.$watch(attrs.collapsed, function (val) {
              if ((typeof val) == 'boolean') {
                scope.collapsed = val;
              }
            });
            
            scope.$watch('collapsed', function (val) {
              UiTreeHelper.setNodeAttribute(scope, 'collapsed', val);
              attrs.$set('collapsed', val);
            });

          
          }
        }
    }
    
      
      
      
      function ModalInstanceCtrl($scope, $uibModalInstance,$cookies, $timeout,$treeData,Upload) {
            /*$scope.items = items;
    
            $scope.selected = {
                item: $scope.items[0]
            };
    
            $scope.ok = function() {
                $uibModalInstance.close($scope.selected.item);
            };
    
            $scope.cancel = function() {
                $uibModalInstance.dismiss("cancel");
            };*/
           
            $scope.uploadFiles = function(uploads, errFiles) {
                $scope.uploads = uploads;
                $scope.errFiles = errFiles;
                angular.forEach(uploads, function(file) {
                    file.upload = Upload.upload({
                        url: SITE_URL+'admin/galeria/upload',
                        data: {
                            
                            name:file.name,
                            file: file,
                            csrf_hash_name:$cookies.get(pyro.csrf_cookie_name),
                            folder_id: $scope.current_level,
                            width:'0',
                            height:'0',
                            ratio:'1',
                            alt_attribute:''
                            
                        }
                    });
        
                    file.upload.then(function (response) {
                        $timeout(function () {
                            file.result = response.data;
                        });
                    }, function (response) {
                        if (response.status > 0)
                            $scope.errorMsg = response.status + ': ' + response.data;
                    }, function (evt) {
                        file.progress = Math.min(100, parseInt(100.0 * 
                                                 evt.loaded / evt.total));
                    });
                });
            }
            
            $scope.ok = function() {
                $treeData.folder_contents($scope.current_level);/* Verificar funcionamiento */
                $uibModalInstance.close();
            };
    
      }
      function DetailsInstanceCtrl($scope,$http,$uibModalInstance,logger,folder)
      {
          
         
          $scope.detail     = folder;
          $scope.old_item   = folder;
          
          
          $scope.cancelDetail = function() {
                $uibModalInstance.dismiss("cancel");
          };
          $scope.updateDetail=function()
          {
                 var new_description = $scope.detail.description,
        			new_keywords     = $scope.detail.keywords,
        			new_alt_attribute = $scope.detail.alt_attribute,
                    
        		    post_data = {
        				file_id : folder.id,
        				description : new_description,
        				keywords : new_keywords,
        				old_hash : folder.keywords_hash,
        				alt_attribute : new_alt_attribute
        			}; // end varold
                    
                    
                    $http.post(SITE_URL+'admin/galeria/save_description',post_data).then(function(response){
                        
                        var result = response.data;
                        
                        if(result.status)
                        {
                            logger.logSuccess(result.message);
                        }
                        else
                        {
                            logger.logError(result.message);
                        }
                        
                        $uibModalInstance.close();
                        
                    });
        
        		// only save it if it's different than the old one
                console.log(folder);
                console.log(post_data);
        		//if (old_item.description !== new_description || old_item.keywords !== new_keywords || old_item.alt_attribute !== new_alt_attribute)
                //{ 
                  //  console.log('actualizo');
      		   // }
               
               
          }
      }
      function ctrlFiles($scope,$window,$http,$cookies,$sce,$treeData,logger,Upload,$timeout,$uibModal)
      {
          
            
          
              /**********Inicializadores***************/
              $scope.input_name = [];
              $scope.older_name = '';
            
              //$scope.folders = folders;
              
              
              //$treeData.contentFolders(10);
              
              
              $treeData.folder_contents(parent_id);
              
              /***********Funciones generales***********/
              function set_message(status, message,callback)
              {
                  if(!message)
                  {
                      logger.log('Ha ocurrido un error');
                  }
                  if(status)
                  {
                      logger.logSuccess(message);
                  }
                  else
                  {
                      logger.logError(message);
                  }
                          
                     
              }
              
              var fnDetails = function($folder)
              
              {
                    
                   
                    
                    
                    var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'myModalDetails.html',
                            controller: 'DetailsInstanceCtrl',
                            //size: size,
                            resolve: {
                                folder: function () {
                                    return $folder;
                                }
                            }
                    });
              }
              
              
              
              /***********Funciones para el menú**********/
              $scope.menu_main = [
                 
                 
                  
                  
                   ['Nueva carpeta', function ($itemScope, $event, color) {
                     
                       
                       
                       
                      var new_folder = $treeData.new_folder($scope.id_parent); 
                      // console.log(new_folder);
                      //if(new_folder.id)
                      //{
                          //$scope.folders.push(new_folder);
                      //}
                      
                      
                     
                      
                      
                     
                  }],
                  ['Subir archivo', function ($itemScope, $event, color) {
                  	    //ModalDemoCtrl($scope);
                       
                          //console.log($itemScope.folder.name);
                          //alert($itemScope.folder.name);
                          
                          
                          

                        var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'myModalUpload.html',
                            controller: 'ModalInstanceCtrl',
                            //size: size,
                            resolve: {
                               // items: function () {
                                 //   return $scope.items;
                                //}
                            }
                        });
                        
                       
            
                        modalInstance.result.then(function (selectedItem) {
                           // $scope.selected = selectedItem;
                        }, function () {
                            
                            $treeData.contentFolders($scope.current_level); //verificar funcionamiento
                            
                        });
        
                  }],
                   null,
                  ['Detalles', function ($itemScope, $event, color) {
                    
                        
                        
                       
                     
                  },function(){
                    
                    return true;
                  }]
              ];
              $scope.menu_file = [
                  ['Descargar', function ($itemScope, $event, color) {
                    
                        
                       $window.open('/files/download/'+$itemScope.file.id);
                       
                     
                  }],
                  ['Cambiar nombre', function ($itemScope, $event, color) {
                     
                      $scope.old_name =  $scope.files[$itemScope.$index].name;
                      $scope.files[$itemScope.$index].on_editable = true;
                  }],
                  ['Eliminar archivo', function ($itemScope, $event,$index) {
                    
                     $treeData.delete_file($itemScope);
                     
                  }],
                  null,
                  ['Detalles', function ($itemScope, $event) {
                        
                        
                        //$scope.last_click = $itemScope.folder;
                        //$scope.detail = $itemScope.folder
                        fnDetails($itemScope.file);
                        
                        
                        
                     
                  }]
               ];
               $scope.menu_folder = [
                  ['Abrir', function ($itemScope, $event, color) {
                  	   
                          
                          
                       $treeData.folder_contents($itemScope.file.id);
                  }],
                  
                 
                  ['Cambiar nombre', function ($itemScope, $event, color) {
               
                      $scope.old_name =  $scope.files[$itemScope.$index].name;
                      $scope.files[$itemScope.$index].on_editable = true;
                  }],
                  ['Eliminar', function ($itemScope, $event,$index) {
                      
                      
                       $treeData.delete_folder($itemScope);
                     
                      
                      
                    
                  }],
                  null,
                   ['Detalles', function ($itemScope, $event, color) {
                    
                      
                      fnDetails($itemScope.file);
                  }],
                  
              ];
             $scope.folder_contents= function(id_parent){
            
                    $treeData.folder_contents(id_parent);
             };
             $scope.save_name = function($index)
             {
                
                var new_name = $scope.files[$index].name,
                        type = $scope.files[$index].menu,
                        post = { name: new_name },
                          id = $scope.files[$index].id;
                
                post[type+'_id'] = id;
                
                if(new_name === $scope.old_name )
                {
                    $scope.files[$index].on_editable = false;
                    return false;
                }
                
                
                $http.post(SITE_URL + 'admin/galeria/rename_'+type,post).then(function(response){
                    
                    
                    var result = response.data;
                    
                   if(result.status)
                   {
                        logger.logSuccess(result.message);
                        
                        $scope.files[$index].name        = result.data.name;
                   }
                   else
                   {
                       logger.logError(result.message);
                   }
                                         
                    $scope.files[$index].on_editable = false;
                    
                });
                
             }
             
             
             
            
             
            $scope.seleccionar = function(index)
            {
                
               
            }
             
       
      }
      
    
    
})();