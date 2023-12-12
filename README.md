# Plugin: Around The World

El plugin "Around The World" es un plugin que permite mostrar información destacada de un país, tomando en cuenta el título del post como referencia.

---

## DECONSTRUCCIÓN DEL PLUGIN

### ***La Base de Datos***

* Inicialmente, el plugin contiene una base de datos con información de ciertos paises. Teniendo como propiedades el nombre del país, su población y su plato típico.

![img.png](images%2Fimg.png)

> [!NOTE]  
> La creación de la tabla se realiza al momento de activar el plugin.

```php
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
```

* Para la inserción del contenido en esta tabla, se utilizan una serie de arrays que contienen la información de los paises. denominados **countries, population, typicalFood**.

```php
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
```

* Con respecto a la selección  de la información, usamos una función que recibe como parámetro el nombre del país, para así hacer la respectiva consulta SQL y obtener específicamente dicha información.

    ```php
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

    ```

### ***El funcionamiento del plugin en concreto***

*  Apreciando el código proporcionado, notamos que primero hay una verificación de que estamos accediendo a una entrada específicamente y no a otro tipo de contenido. Esto se realiza con la función **get_post_type($post_id)**, la cual recibe como parámetro el id del post y retorna el tipo de contenido al que pertenece.

    ```php
    if (get_post_type($post_id) === 'post') {
        // Obtener el objeto de la entrada
        $entrada = get_post($post_id);
    }
    ```
   
* Luego, obtenemos el título de la entrada, para así poder buscar el país en la base de datos. Para esto, se utiliza la función **get_post($post_id)**, la cual recibe como parámetro el id del post y retorna un objeto con toda la información de la entrada.

    ```php
    $titulo_entrada = $entrada->post_title;
    ```

* Posteriormente, se realiza un ciclo for para buscar el país en la base de datos, utilizando la función **strpos($titulo_entrada, $countries[$i])**, la cual recibe como parámetros el título de la entrada y el nombre del país, y retorna la posición de la primera coincidencia encontrada. En caso de no encontrar ninguna coincidencia, retorna false. Una vez encontrado el país, se procede a buscar la información del mismo en la base de datos, utilizando la función **selectData($countries[$i])**, la cual recibe como parámetro el nombre del país y retorna la información del mismo.

    ```php
    for ($i = 0; $i < count($countries); $i++) {
        if (strpos($titulo_entrada, $countries[$i]) !== false) {
            $informacion_pais = selectData($countries[$i]);
            break;
        }
    }
    ```

* En caso de no obtener información del país, se procede a agregar un mensaje de error en el contenido de la entrada.

    ```php
    if ($informacion_pais != null) {
        $entrada->post_content .= "<br><br> <b>Información del país:</b><br>" . print_r($informacion_pais, true);
        // Actualizar la entrada
        wp_update_post($entrada);
    } else {
        $entrada->post_content .= "<br><br>Información del país: No se ha encontrado información sobre este país";
        // Actualizar la entrada
        wp_update_post($entrada);
    }
    ```
  > [!IMPORTANT]
  > El uso de "wp_update_post" permite actualizar el contenido de la entrada, agregando la información del país o el mensaje de error, según sea el caso.

* Finalmente, esta función es lanzada al momento de publicar el post.

```php
    add_action('publish_post', 'add_country_info');
```
## MODIFICACIONES IMPLEMENTADAS EN EL PLUGIN

### ***¿Por qué el uso de 'publish_post'?***

En el momento del desarrollo del plugin, al utilizar otro tipo de  acción, como por ejemplo 'save_post', se presentaba un error en el momento de actualizar el contenido de la entrada, ya que se entraba en un bucle infinito de actualizaciones. Esto se debe a que al momento de actualizar el contenido de la entrada, se vuelve a lanzar la acción 'save_post', y por ende, se vuelve a actualizar el contenido de la entrada, y así sucesivamente.

Para corregir este error, se utilizó la acción 'publish_post', la cual se lanza al momento de publicar el post, y no al momento de actualizarlo. Siendo una modificación puntual en nuestra entrada.

### ***¿Por qué el uso de un 'for' para ubicación del país y un 'strpos'?***

Al implementar la herramienta *"strpos"*, no limitamos al título a coincidir exactamente con el nombre del país, sino que también permite que el título contenga más palabras. Buscando así una subcadena dentro del mismo título.

## VISTA PREVIA DEL PLUGIN

1. Activamos nuestro plugin

![img_1.png](images%2Fimg_1.png)

2. Creamos una nueva entrada

![img_2.png](images%2Fimg_2.png)

3. Publicamos la entrada, y observamos que se ha agregado la información del país

![img_3.png](images%2Fimg_3.png)

---

## SI TE GUSTÓ EL PLUGIN, NO OLVIDES DEJAR TU 10! :smile:
