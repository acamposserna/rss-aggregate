const __DEBUG = false; // Indica si mostrar o no los textos en console.log()

/**
 * Funcion para mostrar el log. Solo se muestra si estamos en __DEBUG.
 *
 * @param string texto Cadena de texto a mostrar.
 */
function log(texto) {
    if (__DEBUG) {
        console.log(texto);
    }
}

/**
 * Guardamos en sessionStorage el estado de los checkboxes y el número de noticias a mostrar.
 */
function guardarEstado() {
    let elementos = document.getElementsByTagName("input");
    let num_elementos = elementos.length;

    // Guardamos el número de noticias
    sessionStorage.setItem("numero", document.getElementById("numero").value);

    // Guardamos el estado de los checkboxes
    for (var i = 0; i < num_elementos; i++) {
        let id = elementos.item(i).id;

        if (elementos.item(i).checked) {
            sessionStorage.setItem(id, "on");
        }
        else {
            sessionStorage.setItem(id, "off");
        }
    }
}

/**
 * Obtenemos el estado de los checkboxes y el número de noticias a mostrar que hay en sessionStorage.
 */
function obtenerEstado() {
    let elementos = document.getElementsByTagName("input");
    let num_elementos = elementos.length;

    for (var i = 0; i < num_elementos; i++) {
        let id = elementos.item(i).id;
        let is_checked = sessionStorage.getItem(id);

        if (typeof is_checked !== 'undefined' && is_checked !== null) {
            if (is_checked === "on") {
                elementos.item(i).checked = true;
            }
            else {
                elementos.item(i).checked = false;
            }
        }
        else {
            elementos.item(i).checked = false;
        }
    }
}

/**
 * Función que se ejecuta cuando hacemos click en el checkbox "Todos".
 *
 * @param input elementoTodos
 */
function cambiaTodos(elementoTodos) {
    let elementos = document.getElementsByTagName("input");
    let num_elementos = elementos.length;

    for (var i = 0; i < num_elementos; i++) {
        let id = elementos.item(i).id;

        if (id.includes("Off")) {
            elementos.item(i).checked = !elementoTodos.checked;
        }
        else {
            elementos.item(i).checked = elementoTodos.checked;
        }
    }
}

/**
 * Cambia el estado de on/off de un checkbox
 *
 * @param elementoOn
 */
function cambioEstado(elementoOn) {
    let id = elementoOn.id + "Off";
    let elementoOff = document.getElementById(id);

    elementoOff.checked = !elementoOn.checked;
    cambiaEstadoTodos();
}

/**
 * Cambia es estado de todos los checkboxes.
 *
 */
function cambiaEstadoTodos() {
    let elementos = document.getElementsByTagName("input");
    let num_elementos = elementos.length;
    let nuevo_estado = true;

    for (var i = 0; i < num_elementos; i++) {
        let id = elementos.item(i).id;

        if (elementos.item(i).type==="checkbox" && !id.includes("todos") && !id.includes("Off")) {
            log('elemento ' + id + ': ' + elementos.item(i).checked)
            if (elementos.item(i).checked === false) {
                nuevo_estado = false;
                break;
            }
        }
    }
    log('Nuevo estado: ' + nuevo_estado);
    document.getElementById('todos').checked = nuevo_estado;
}
