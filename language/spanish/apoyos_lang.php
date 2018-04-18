<?php defined('BASEPATH') OR exit('No direct script access allowed');
$lang['apoyos:title']			=	'Apoyos';
$lang['apoyos:create']			=	'Nueva Comprobación';
$lang['apoyos:details']			=	'Detalles del Comprobación';
$lang['apoyos:edit']			=	'Modificando el registro de Comprobación';
$lang['apoyos:not_found']		=	'Actualmente no hay registro';
$lang['apoyos:admin']		=	'Administrando %s';


$lang['apoyos:all_required']			=	'Todos los archivo son requeridos';

//$lang['apoyos:info']				=	'Se va agregar un registro dentro del mes de "%s" para el director "%s"';
$lang['apoyos:save_success']		=	'El registro  ha sido guardado correctamente, ahora ya puedes agregar los comprobantes.';

$lang['apoyos:welcome']			=	'En este módulo, podrás visualizar, cargar facturas y descargar toda información acerca de los pagos realizados a los Planteles como parte de Apoyos. Si necesitas descargar el mes, haz clic en <i class="fa fa-download"></i> para iniciar la descarga.';
$lang['apoyos:error_assoc']		=	'El usuario no se está asociado con alguna cuenta de director. Verificar datos de acceso con tu administrador.';
$lang['apoyos:error_access']		=	'Para acceder al módulo de depósitos es necesario autenticarte.';

$lang['apoyos:error_estatus']		=	'No es posible modificar este registro, el motivo es que se encuentra ya validado.';
$lang['apoyos:report'] = 'Reporte';
$lang['apoyos:report_help'] = 'Bienvenido al panel de reporte del módulo de apoyos. Para extraer la información  introduzca la fecha de inicio y la fecha fin.';
$lang['apoyos:report_not_found'] = 'La consulta no trajo resultados, intentelo nuevamente con otras fechas.';
$lang['apoyos:report_found'] = 'La consulta ha traido el siguiente resultado del período  <strong>%s</strong> al <strong>%s</strong>, si  deseas realizar otra consulta haga clic en <em>Reiniciar</em> en la parte de abajo de esta sección.';

$lang['apoyos:init']   = 'Para agregar las facturas, es necesario crear y guardar el registro. Haga clic en <em>Siguiente</em>';
$lang['apoyos:maximo'] = 'Se ha rebasado el monto máximo del saldo por lo que se redondeara a {{importe|number:2}}';
$lang['apoyos:delete_success'] = 'El registro de apoyo ha sido borrado, ahora podras consultarlo en Pendientes  de apoyo.';
$lang['apoyos:error_deposito'] = 'Debe de ingresar todos los datos del depósito';


$lang['apoyos:error_access']			= 'La cuenta de usuario no esta completamente vinculada a una cuenta de director, te recomendamos que nos envies un mensaje al correo <a href="mailto:'.Settings::get('contact_email').'">'.Settings::get('contact_email').'</a> reportando este problema.';
$lang['apoyos:welcome_front'] = 'Bienvenido(a) <strong>%s</strong> al panel de Apoyos, en este apartado podras subir tus documentos comprobatorios para la justificación de los apoyos. Si tu nombre es incorrecto, te pedimos que nos envies un mensaje al correo <a href="mailto:'.Settings::get('contact_email').'">'.Settings::get('contact_email').'</a> reportando este problema.';	
?>
