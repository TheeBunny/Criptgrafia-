<?php
$resultado = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['texto']) && isset($_POST['metodo'])) { //comprueba que fue enviado el metodos POST y verificar si existen campos 'texto' y 'metodo'
    //almacenar los valores enviados desde el formulario 
    $texto=$_POST['texto'];
    $metodo=$_POST['metodo'];
    $clave=$_POST ['clave'];
    $resultado = '';

    if ($metodo=="ascci") { //elegir el metodo a usar 
        for ($i=0; $i<strlen($texto); $i++) {
            $ascii = ord($texto[$i]);// convierte el caracter en su código ASSCI 
            $resultado .= $ascii; // el . es para contatenar ese numero a la variable 
        }
        $resultado = trim($resultado); //eimina posibles espacios al inicio o final de la cadena
    } elseif ($metodo =="base64") {
        $clave=isset($_POST['clave'])?$_POST['clave'] : '';//se asegura de que haya una clave, si no la define como cadena vacía
        $operacion=$_POST['operacion'] ?? 'codificar';//toma el valor de operacion, si no se proporciona, se asume que se quiere codificar 
        
        if ($operacion == 'codificar') {
            $ascii_cifrados = [];//alamacenar los valores ascci cifrados 
            for ($i=0; $i<strlen($texto); $i++) {
                $caracter = $texto[$i];//obtener el carácter actual del texto en la posición $i
                $desplazamiento = ord($clave[$i % strlen($clave)]);//obtiene el índice de la clave que corresponde al carácter actual es para claves más cortas que el texto 
                $nuevo_valor = ord($caracter) + $desplazamiento;//suma el valor ASCII del caracter del texto con el valor ASCII del caracter correspondiente de la clave 
                $ascii_cifrados[] = $nuevo_valor%256;// aplicamos módulo 256 para asegurar que el valor resultante esté en el rango de 0-255
            }
            $resultado = base64_encode(implode(array_map('chr', $ascii_cifrados)));//base_64 es un sistema de codificación que representa datos binarios, array_map convierte números a caracteres, implode convierte array de caracteres a string 
        } else {
            $ascii_cifrados = unpack('C*', base64_decode($texto));//unpack convierte la cadena binaria en un array de valores ASCII 
            $texto_descifrado= '';
            for ($i=1; $i<count($ascii_cifrados); $i++) {
                $desplazamiento = ord($clave[($i-1) % strlen($clave)]);//calcula la poscisión correspondiente en la clave 
                $original = ($ascii_cifrados[$i] - $desplazamiento) % 256; //resta el desplazamiento que se había sumado en el cifrado %256 para asegurar el resultado entre 0 y 255 
                $texto_descifrado .= chr($original); //convierte el valor ASCII al caracter que le corresponde 
            }
            $resultado=$texto_descifrado; //almacenar el resultado 
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi proyecto de parcial</title>
    <link href="style.css" rel="stylesheet" type="text/css">
</head>
<body>
    <!--Interfaz -->
    <div class="contenedor">
        <h2>Codificar y Decodificar mensajes</h2>
        <form method="post">
            <div class="formagrupo">
                <!-- Método -->
                <label for="metodo">Método</label>
                <select id="metodo" name="metodo" requiered>
                    <option value="" selected disabled> Seleccione un metodo </option>
                    <option value="ascci">Ascci</option>
                    <option value="base64">Base 64</option>
                </select>
            </div>
            <!-- Texto a procesar  -->
            <div class="formagrupo">
                <label for="texto">Texto a procesar:</label>
                <textarea id="texto" name="texto" placeholder="Ingresa aquí tu texto"></textarea>
            </div>
            <!-- Elegir la clave  -->
            <div class="formagrupo">
                <label for="clave">Clave:</label>
                <input type="text" id="clave" name="clave" placeholder="Ingresa aquí tu clave para codificar">
            </div>
            <!-- Botones  -->
            <div class="botones">
                <button type="submit" id="botonCodificar" class="boton-codificar" name="operacion" value="codificar">Codificar</button>
                <button type="submit" id="botonDecodificar" class="boton-decodificar" name="operacion" value="decodificar">Decodificar</button>
            </div>
            <!-- Imprimir el resultado  -->
            <div class="formagrupo">
                <label for="resultado">Resultado:</label>
                <textarea id="textoresultado" name="textoresultado" readonly><?php echo $resultado; ?></textarea>
            </div>
        </form>
    </div>
</body>
</html>