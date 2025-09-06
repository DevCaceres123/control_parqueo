import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";
let permisosGlobal;
let tabla_atraso;

$(document).ready(function () {
    listar_atraso();
});

function listar_atraso() {
    tabla_atraso = $("#tabla_atraso").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "listarConfAtraso", // Ruta que recibe la solicitud en el servidor
            type: "GET", // Método de la solicitud (GET o POST)
            dataSrc: function (json) {
                permisosGlobal = json.permisos;
                // console.log(permisosGlobal); // Guardar los permisos para usarlos en las columnas
                return json.data; // Data que se pasará al DataTable
            },
        },
        columns: [
            {
                data: null,
                className: 'table-td',
                render: function (data, type, row, meta) {
                    // Calcula el índice global usando el start actual
                    let start = $('#tabla_atraso').DataTable().page.info().start;
                    return start + meta.row + 1;
                }
            },
            {
                data: "tiempo_extra",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },
            {
                data: "monto",
                className: "table-td text-uppercase",
                render: function (data) {
                   return `                            
                        <span  class='p-1 pe-3 ps-3 bg-warning rounded-pill text-light fs-15'>${data} <b>Bs</b></span>
                    `;
                },
            },
            {
                data: null,
                className: "table-td",
                render: function (data, type, row) {
                    let estadoChecked =
                        row.estado === "activo" ? "checked" : "";

                    // Aquí verificamos el permiso de desactivar
                    let desactivarContent =
                        permisosGlobal["estado"] == false
                            ? `
                            <a class="cambiar_estado_atraso" data-id="${row.id},${row.estado}">
                                <div class="form-check form-switch ms-3">
                                    <input class="form-check-input" type="checkbox" 
                                           ${estadoChecked} style="transform: scale(2.0);">
                                </div>
                            </a>`
                            : `
                           <p>No permitido...<p/>
                        `;

                    return `
                            <div data-class="">
                                ${desactivarContent}
                            </div>`;
                },
            },
          

            {
                data: null,
                className: "table-td text-end",
                render: function (data, type, row) {
                    return ` <div class="d-flex justify-content-center">
                      
                             ${permisosGlobal.eliminar
                            ? ` <a class="btn btn-sm btn-outline-warning px-2 d-inline-flex align-items-center editar_atraso me-1" data-id="${row.id}" title="Editar Sede">
                            <i class="fas fa-pencil-alt fs-16"></i>
                        </a>`
                            : ``
                        }
                                            
                         
                        </div>`;
                },
            },
        ],
    });
}

// Llamada a la función para recargar la tabla después de una operación
function actualizarTabla() {
    tabla_atraso.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}


// Agregar o editar el atraso
$("#form_atraso").on("submit", function (e) {

    e.preventDefault();
    let ruta = $("#btn_guardar_atraso").attr("data-id");
    let idEditar = null;
    let metodo = null;
    let formData = new FormData(this);

    if (ruta == 'nuevo') {
        ruta = 'admin/atraso';
        metodo = 'POST';
    }

    if (ruta == 'editar') {
        ruta = 'admin/atraso';
        metodo = 'PUT';
        idEditar = $("#atraso_id").val();      
        formData = Object.fromEntries(new FormData(this));  
    }
        
    vaciar_errores("form_atraso");
    // Opcional: Desactivar botón para evitar doble clic    

    const btn = $("#btn_guardar_atraso");
    btn.prop("disabled", true).html('<i class="ri-loader-4-line spin"></i> Subiendo...');

    crud(ruta, metodo, idEditar, formData, function (error, response) {
        btn.prop("disabled", false).html('<i class="ri-upload-cloud-line me-1"></i>Guardar');

        // Verificamos que no haya un error o que todos los campos sean llenados
        if (response.tipo === "errores") {
            mensajeAlerta(response.mensaje, "errores");
            return;
        }
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        // //si todo esta correcto muestra el mensaje de correcto
        $("#modal_atraso").modal("hide");

        mensajeAlerta(response.mensaje, response.tipo);
        vaciar_formulario('form_atraso');
        actualizarTabla();
    });
});

// cambiar estado atraso
$("#tabla_atraso").on("click", ".cambiar_estado_atraso", function (e) {
    e.preventDefault(); // Evitar que el enlace recargue la página

    // Obtener el valor de data-id
    var dataId = $(this).data("id");

    // Separar el id y el estado
    var values = dataId.split(",");

    
    let datos = {
        estado: values[1],
    };



    crud("admin/cambiarEstadoConfig", "PUT", values[0], datos, function (error, response) {
        if (response.tipo === "errores") {
            mensajeAlerta(response.mensaje, "errores");
            return;
        }
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        mensajeAlerta(response.mensaje, response.tipo);

        actualizarTabla();
   });
});

// obtener valores del atraso
$(document).on("click", ".editar_atraso", function () {
    const idEditar = $(this).data("id");    
      crud("admin/atraso", "GET", idEditar+['/edit'], null, function (error, response) {        

        // Verificamos que no haya un error o que todos los campos sean llenados
        if (response.tipo === "errores") {
            mensajeAlerta(response.mensaje, "errores");
            return;
        }
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        // //si todo esta correcto muestra el mensaje de correcto    
        $('#atraso_id').val(response.mensaje.id);
        $('#tiempo_extra').val(response.mensaje.tiempo_extra);
        $('#monto').val(response.mensaje.monto);
    });
});


//<<<<<<<<<<<<<------------------------------------>>>>>>>>>>>>>.
//FUNCIONES DE APOYO
//estas funciones nos ayuadaran para trabajar el nuevo y el editar   en un mismo modal



//mostrar el modal para editar un formulario
$(document).on("click", ".editar_atraso", function () {
    // le asignas el data-id al botón guardar
    $("#btn_guardar_atraso").attr("data-id", "editar");
    vaciar_errores("form_atraso");
    // luego muestras el modal
    $("#modal_atraso").modal("show");
    // editamos el titulo del modal
    $("#titulo_modal").html('<i class="fas fa-car me-1"></i>CONFIGURAR ATRASO');
});

$(document).on("click", ".limpiar_modal", function () {
    vaciar_formulario('form_atrasos');
});