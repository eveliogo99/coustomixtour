<?php

class Email {

   //nombre
   var $nombre;
   //email del emisor
   var $mail;
   //email del receptor
   var $mailr;
   var $asunto;
   //mensaje
   var $msn;
   //archivo adjunto
   var $adjunto;
   //enviar el mensaje
   private $sender;

     //función constructora
     public function __construct() {
       //cada uno de ellos es el parámetro que enviamos desde el formulario
       $this->nombre = $n;
       $this->mail = $m;
       $this->mailr = $mr;
       $this->asunto = $a;
       $this->msn = $ms;
       $this->adjunto = $ad;
     }

    //método enviar con los parámetros del formulario
     public function enviar($n,$m,$mr,$a,$ms,$ad){
       //si existe post
       if(isset($_POST)){
          //Limpiamos los datos del formulario
          $n = str_replace(array("\r","\n"),array(" "," ") , strip_tags(trim($n)));
          $m = filter_var(trim($m), FILTER_SANITIZE_EMAIL);
          $ms = trim($ms);

          // Comprobamos que los datos no estén vacíos
          if ( empty($n) OR !filter_var($m, FILTER_VALIDATE_EMAIL)) {
              # Establecer un código de respuesta y salida.
              http_response_code(400);
              echo "Por favor completa el formulario y vuelve a intentarlo.";
              exit;
          }

          //si existe adjunto
          if($ad) {
             //añadimos texto al nombre original del archivo
             $dir_subida = 'fichero_';
             //nombre del fichero creado -> fichero_nombreArchivo.pdf
             $fichero_ok = $dir_subida . basename($ad);
             //y lo subimos a la misma carpeta
             move_uploaded_file($_FILES['adjunto']['tmp_name'], $fichero_ok);
          }
         //creamos el mensaje
         $contenido = '
         <h2>Nuevo mensaje de: '.$n.'</h2>
         <hr>
         Email: <b>'.$m.'</b><br>
         Mensaje: <br><b>'.$ms.'</b><br>
         ';
         //adjuntamos el archivo necesario para enviar los archivos adjuntos
         require_once 'AttachMailer.php';

         //enviamos el mensaje (emisor,receptor,asunto,mensaje)
         $this->sender = new AttachMailer($m, $mr, $a, $contenido);
         $this->sender->attachFile($fichero_ok);
         //eliminamos el fichero de la carpeta con unlink()
         //si queremos que se guarde en nuestra carpeta, lo comentamos o borramos
         unlink($fichero_ok);
         //enviamos el email con el archivo adjunto
         $success = $this->sender->send();

         if ($success) {
             # Establece un código de respuesta 200 (correcto).
             http_response_code(200);
             echo "¡Gracias! Tu mensaje ha sido enviado.";
         } else {
             # Establezce un código de respuesta 500 (error interno del servidor).
             http_response_code(500);
             echo "Oops! Algo salió mal, no pudimos enviar tu mensaje, inténtalo más tarde o escríbenos directamente a info@coutomixtour.com";
         }
      }
     else{
       # No es una solicitud POST, establezce un código de respuesta 403 (prohibido).
       http_response_code(403);
       echo "Ha habido un problema con tu envío, inténtalo de nuevo.";
     }
   }
}

//llamamos a la clase
$obj = new Email();
//ejecutamos el método enviar con los parámetros que recibimos del formulario
$obj->enviar($_POST['name'], $_POST['email'], "info@coutomixtour.com", "Solicitud web para Coutomixtour", $_POST['message'], $_FILES['adjunto']['name']);

?>
