import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";

let permissions;
let tabla_historialRegistro;
let valorSeleccionado;
$(document).ready(function () {
    listar_registros();
});

function listar_registros() {
    // Inicializa la tabla con DataTables
    tabla_historialRegistro = $("#tabla_boletas").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "listarTodasBoletas", // Ruta que recibe la solicitud en el servidor
            type: "GET", // Método de la solicitud (GET o POST)
            data: function (d) {
                d.fecha = $("#filterFecha").val(); // Agrega la fecha al request
                d.encargado = valorSeleccionado;
            },
            dataSrc: function (json) {
                permissions = json.permissions; // Guardar los permisos si es necesario
                return json.data; // Data que se pasará al DataTable
            },
        },
        columns: [
            {
                data: null,
                className: "table-td",
                render: function (data, type, row, meta) {
                    // Calcula el índice global usando el start actual
                    let start = $("#tabla_boletas")
                        .DataTable()
                        .page.info().start;
                    return start + meta.row + 1;
                },
            },
            {
                data: "vehiculo.nombre",
                className: "table-td text-capitalize",
                render: function (data) {
                    return `${data}`;
                },
            },
            {
                data: "placa",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `<span class="badge ${
                        data != null ? "bg-success" : "bg-danger"
                    } fs-6">${data != null ? data : "N/A"}</span>`;
                },
            },

            {
                data: "ci",
                className: "table-td",
                render: function (data) {
                    return `${data ?? "N/A"}`;
                },
            },

            {
                data: "entrada_veh",
                className: "table-td",
                render: function (data) {
                    return `${data} `;
                },
            },

            {
                data: "salida_veh",
                className: "table-td",
                render: function (data) {
                    return `${data ?? "N/A"} `;
                },
            },

            {
                data: "retraso",
                className: "table-td",
                render: function (data) {
                    return `${data ?? "N/A"} `;
                },
            },
            {
                data: "estado_parqueo",
                className: "table-td",
                render: function (data) {
                    return `<span class="badge text-capitalize ${
                        data != "salida" ? "bg-primary" : "bg-success"
                    } fs-6">${data != null ? data : "N/A"}</span>`;
                },
            },

            {
                data: "total",
                className: "table-td",
                render: function (data) {
                    return `<span class="p-1 pe-2 ps-2 bg-warning rounded-pill text-light ${
                        data != null ? "bg-success" : "bg-danger"
                    } ">${data != null ? data : "."} Bs</span>`;
                },
            },

            {
                data: null,
                className: "table-td",
                render: function (data, type, row) {
                    return `

                    <div class="d-flex justify-content-center">

                   ${
                       permissions["eliminar"]
                           ? ` <a  class="btn btn-sm btn-outline-danger px-2 d-inline-flex align-items-center eliminar_registro" data-id="${row.id}">
                                 <i class="fas fa-window-close fs-16"></i>
                            </a>`
                           : ``
                   }

                     ${
                         permissions["eliminar"]
                             ? ` <button type="button" class="btn btn-sm btn-outline-success px-2 d-inline-flex align-items-center generar_reporte ms-1 btn-reporte" data-id="${row.id}">
                                 <i class="fas fa-file-pdf  fs-16"></i>
                            </button>`
                             : ``
                     }

                     ${
                         permissions["eliminar"]
                             ? ` <button type="button" class="btn btn-sm btn-outline-info px-2 d-inline-flex align-items-center generar_reporte ms-1 btn-reporte" data-id="${row.id}">
                                 <i class="fas fa-money-bill   fs-16"></i>
                            </button>`
                             : ``
                     }

                        
                    
                 
                 </div> `;
                },
            },
        ],
    });

    // Permite filtrar por una fecha diferente
    $("#filterFecha").on("change", function () {
        tabla_historialRegistro.ajax.reload();
    });

    // Listar todos los registros al presionar el botón
    $("#btnListarTodo").on("click", function () {
        $("#filterFecha").val(""); // Limpia el valor del campo de fecha
        $("#encargados").val(""); // Limpia el valor del campo de fecha
        tabla_historialRegistro.ajax.reload(); // Recarga la tabla sin filtrar
    });

    // Escuchar el evento change en el select
    $("#encargados").on("change", function () {
        // Obtener el valor seleccionado
        valorSeleccionado = $(this).val(); // Obtiene el valor (attribute value)
        tabla_historialRegistro.ajax.reload();
        // Mostrar en la consola los valores obtenidos
    });
}

function actualizarTabla() {
    tabla_historialRegistro.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}

// eliminar boleta
$(document).on("click", ".eliminar_registro", function () {
    
    const idRegistro = $(this).data("id");    
    Swal.fire({
        title: "¿Eliminar?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            crud("admin/listarBoletas", "DELETE", idRegistro, null, function (error, response) {
                if (response.tipo != "exito") {
                    mensajeAlerta(response.mensaje, response.tipo);
                    return;
                }
                mensajeAlerta(response.mensaje, response.tipo);
                actualizarTabla();
            });
        }
    });
});