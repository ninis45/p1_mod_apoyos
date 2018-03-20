(function () {
    'use strict';
    
    angular.module('app.apoyos')
    
    .controller('InputCtrl',['$scope','$http','$uibModal',InputCtrl])
    .controller('IndexCtrl',['$scope',IndexCtrl])
    .controller('InputReport',['$scope',InputReport])
    .controller('ModalInstanceCtrl', ['$scope', '$uibModalInstance','$timeout','$cookies','Upload','files','item', ModalInstanceCtrl]);
    function InputReport($scope)
    {
        $scope.percent = 0;
        $scope.easypiechart3 = {
            percent: 0,
            options: {
                animate: {
                    duration: 1000,
                    enabled: true
                },
                barColor: $scope.color.info,
                lineCap: 'square',
                size: 180,
                lineWidth: 20,
                scaleLength: 0
            }
        };
        
        $scope.$watch('percent',function(newValue,oldValue){
           
            
            $scope.easypiechart3.percent = newValue;
            
        });
    }
    function IndexCtrl($scope)
    {
        $scope.pendientes = depositos['Pendientes'];
        $scope.depositos = depositos;
        
        console.log($scope.depositos);
    }
    function ModalInstanceCtrl($scope, $uibModalInstance,$timeout,$cookies,Upload,files,item) {
        
        $scope.dispose = true;
        $scope.id_factura=item?item.id:'';
        $scope.item = {};
        
        $scope.close = function()
        {
            var index = files.indexOf(item);
            
            if(index == -1)
            {
                files.push(item);
            }
            
            $uibModalInstance.close();
        }
        $scope.upload_file = function(file,type)
        {
            $scope.dispose = false;
            
            file.upload = Upload.upload({
              url: SITE_URL+'admin/apoyos/upload',
              data: { id_factura:$scope.id_factura,id:id,type:type,file: file,name:file.name,csrf_hash_name:$cookies.get(pyro.csrf_cookie_name)},
            });
            
            file.upload.then(function (response) {
              var  result = response.data,
                   data   = response.data.data;
              $timeout(function () {
                  file.result = response.data;
                  $scope.dispose = true;
                  
                  if(typeof item == 'undefined' || !item)
                  {
                      item = {id:data.id_factura,xml:'',pdf:'',total:0,messages:[]};
                  }
                  
                  if(type == 'xml' )
                  {
                      item['total']    = data.total;
                      item['messages'] = result.message;
                  }
                  
                  $scope.id_factura = response.data.data.id_factura;
                  item[type] = data.id;
                 
                 
              });
            }, function (response) {
              if (response.status > 0)
                $scope.errorMsg = response.status + ': ' + response.data;
            }, function (evt) {
              
              file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
            });
            
            
        }
        function upload_file(file,type){
            file.upload = Upload.upload({
              url: SITE_URL+'admin/apoyos/upload',
              data: { id:id,type:type,file: file,name:file.name,csrf_hash_name:$cookies.get(pyro.csrf_cookie_name)},
            });
            
            file.upload.then(function (response) {
              $timeout(function () {
                file.result = response.data;
              });
            }, function (response) {
              if (response.status > 0)
                $scope.errorMsg = response.status + ': ' + response.data;
            }, function (evt) {
              // Math.min is to fix IE which reports 200% sometimes
              file.progress = Math.min(100, parseInt(100.0 * evt.loaded / evt.total));
            });
        }
    }
    function InputCtrl($scope,$http,$uibModal)
    {
          $scope.files = files;
          $scope.importe = 0;
          $scope.total = 0;
          $scope.saldo = 0;
          $scope.method = '';
          $scope.remove = function(item)
          {
            
             var index = files.indexOf(item);
             
             $http.get(SITE_URL+'admin/apoyos/remove_factura',{params:{id_factura:item.id}}).then(function(response){
                
                var response = response.data,
                    status   = response.status;
                
                
                if(status)
                {
                    $scope.files.splice(index,1);
                }
                
                
             });
          }
          $scope.edit = function(item)
          {
               
               var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'myModalUpload.html',
                            controller: 'ModalInstanceCtrl',
                            //size: size,
                            resolve: {
                               item: function () {
                                 return item;
                               },
                               files: function () {
                                 return $scope.files;
                               },
                            }
                        });
          }
          $scope.open_modal = function(){
                var modalInstance = $uibModal.open({
                            animation: $scope.animationsEnabled,
                            templateUrl: 'myModalUpload.html',
                            controller: 'ModalInstanceCtrl',
                            //size: size,
                            resolve: {
                               item: function () {
                                 return false;
                               },
                               files: function () {
                                 return $scope.files;
                               },
                            }
                        });
          }
          
          $scope.$watch('files',function(newValue,oldValue){
              
              if(!newValue) return false;
              console.log(newValue);
              $scope.total = 0;
              $.each(newValue,function(index,file){
                    if(file.total)
                        $scope.total += parseFloat(file.total);
                
              });
              $scope.saldo = $scope.importe - $scope.total;
          },true);
   

    }



})();