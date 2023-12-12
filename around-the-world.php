<?php
/**
 * @package Around_the_World
 * @version 1.0.0
 */
/*
Plugin Name: Around The World
Plugin URI: http://wordpress.org/plugins/around_the_world/
Description: A plugin that allows you to know information about the countries of a specific country.
Author: Gabriel Pérez
Version: 1.0.0
Author URI: http://gabriel_dam2/
*/

$countries= array("Suiza",
    "España",
    "Francia",
    "Italia",
    "Alemania",
    "Portugal",
    "Reino Unido",
    "Irlanda",
    "Bélgica",
    "Luxemburgo",
    "Holanda",
    "Dinamarca",
    "Noruega",
    "Suecia",
    "Finlandia",
    "Polonia",
    "Rusia",
    "Grecia",
    "Turquía",
    "Bulgaria",
    "Rumanía",
    "Ucrania",
    "Croacia",
    "Serbia",
    "Bosnia",
    "Eslovenia",
    "Eslovaquia",
    "República Checa",
    "Austria",
    "Hungría",
    "Marruecos",
    "Argelia",
    "Túnez",
    "Egipto",
    "Libia",
    "Sudán",
    "Mauritania",
    "Senegal",
    "Gambia",
    "Guinea",
    "Guinea-Bissau",
    "Guinea Ecuatorial",
    "Sierra Leona",
    "Liberia",
    "Costa de Marfil",
    "Burkina Faso",
    "Ghana",
    "Togo",
    "Benín",
    "Níger",
    "Nigeria",
    "Camerún",
    "Chad",
    "República Centroafricana",
    "Congo",
    "Gabón",
    "Guinea Ecuatorial",
    "Santo Tomé y Príncipe",
    "Sudáfrica",
    "Namibia",
    "Botsuana",
    "Zimbabue",
    "Zambia",
    "Angola",
    "Mozambique",
    "Madagascar",
    "Malaui",
    "Tanzania",
    "Kenia",
    "Uganda",
    "Ruanda",
    "Burundi",
    "Etiopía",
    "Yibuti",
    "Somalia",
    "Eritrea",
    "Seychelles",
    "Comoras",
    "Mauricio",
    "Maldivas",
    "Sri Lanka",
    "Bangladesh",
    "India",
    "Pakistán"
);

$population=array(8.5,
    46.5,
    66.9,
    60.6,
    82.8,
    10.3,
    65.6,
    4.8,
    11.4,
    0.6,
    16.9,
    5.6,
    5.2,
    9.9,
    5.5,
    38.5,
    143.5,
    10.8,
    80.8,
    7.1,
    19.6,
    44.4,
    4.2,
    7.1,
    3.8,
    2.1,
    5.4,
    10.6,
    9.8,
    9.8,
    33.0,
    40.4,
    11.4,
    82.1,
    6.4,
    39.0,
    1.8,
    1.0,
    1.8,
    1.9,
    2.0,
    1.8,
    6.3,
    2.1);

$typicalFood=array(
    "Fondue de queso",
    "Paella",
    "Foie gras",
    "Pizza",
    "Currywurst",
    "Bacalhau",
    "Fish and chips",
    "Irish stew",
    "Moules-frites",
    "Judd mat Gaardebounen",
    "Stamppot",
    "Smørrebrød",
    "Bacalao",
    "Köttbullar",
    "Salmón",
    "Kotlet schabowy",
    "Pelmeni",
    "Moussaka",
    "Kebab",
    "Banitsa",
    "Sarmale",
    "Borscht",
    "Ćevapi",
    "Pljeskavica",
    "Ćevapi",
    "Kranjska klobasa",
    "Bryndzové halušky",
    "Svíčková na smetaně",
    "Gulyás",
    "Couscous",
    "Cuscús",
    "Brik",
    "Koshari",
    "Shakshuka",
    "Ful medames",
    "Thieboudienne",
    "Domoda",
    "Yassa",
    "Jollof",
    "Caldo de bolas",
    "Saka saka",
    "Fufu",
    "Fuf");

// Agrega el contenido de la base de datos a la entrada según el país
function add_Info($post_id) {
    global $countries;

    // Verificar si es una entrada y no una página u otro tipo de contenido
    if (get_post_type($post_id) === 'post') {
        // Obtener el objeto de la entrada
        $entrada = get_post($post_id);

        // Obtener el título de la entrada
        $titulo_entrada = $entrada->post_title;

        // Buscar el país en la base de datos según el título
        for ($i = 0; $i < count($countries); $i++) {
            if (strpos($titulo_entrada, $countries[$i]) !== false) {
                $informacion_pais = selectData($countries[$i]);
                break;
            }
        }

        // Verificar si ya se agregó la información
        if (strpos($entrada->post_content, 'Información del país:') === false) {
            // Si se encuentra información del país, agregarla al contenido de la entrada
            if ($informacion_pais != null) {
                $entrada->post_content .= "<br><br> <b>Información del país:</b><br>" . print_r($informacion_pais, true);
                // Actualizar la entrada
                wp_update_post($entrada);
            } else {
                $entrada->post_content .= "<br><br>Información del país: No se ha encontrado información sobre este país";
                // Actualizar la entrada
                wp_update_post($entrada);
            }
        }
    }
}


// Ejecutar la función cuando se publica el post
add_action('publish_post', 'add_Info');

/*
 * Añadir una tabla a la Base de Datos
 */

function crearBD(){
    global $wpdb;
    //Nombre de la tabla
    $table_name = $wpdb->prefix . "world";
    //Charset de la tabla
    $charset_collate = $wpdb->get_charset_collate();
    //Sentencia SQL
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        pais varchar(255) NOT NULL,
        poblacion int(11) NOT NULL,
        platotipico varchar(255) NOT NULL,
        PRIMARY KEY  (pais)
    ) $charset_collate;";
    //Incluir el fichero para poder ejecutar dbDelta
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

add_action("plugins_loaded", "crearBD");

/*
 * Insertar datos en la tabla (si ya están añadidos no se añaden)
 */
function insertData(){
    global $wpdb, $countries, $population, $typicalFood;
    $table_name = $wpdb->prefix . "world";
    $flag = $wpdb->get_results("SELECT * FROM $table_name");
    if (count($flag)==0){
        for ($i = 0; $i < count($countries); $i++){
            $wpdb->insert(
                $table_name,
                array(
                    'pais' => $countries[$i],
                    'poblacion' => $population[$i],
                    'platotipico' => $typicalFood[$i]
                )
            );
        }
    }
}

add_action("plugins_loaded", "insertData");

/*
 * Seleccionar datos de la tabla
 */
function selectData($countryName){
    global $wpdb;
    $table_name = $wpdb->prefix . "world";

    $result = $wpdb->get_results("SELECT * FROM $table_name WHERE pais = '$countryName'");

    if (count($result)==0){
        return null;
    }else{
        return "Población: ".$result[0]->poblacion." millones. <br>Plato típico: ".$result[0]->platotipico." habitantes.";
    }
}