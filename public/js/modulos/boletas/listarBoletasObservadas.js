import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";
let permissions;
let tabla_historialRegistro;

$(document).ready(function () {
    listar_registros();
});

function listar_registros() {
    // Inicializa la tabla con DataTables
    tabla_historialRegistro = $("#tabla_boletas_observadas").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "listarBoletasObservadas", // Ruta que recibe la solicitud en el servidor
            type: "GET", // Método de la solicitud (GET o POST)
            // data: function (d) {
            //     d.fecha = $("#filterFecha").val(); // Agrega la fecha al request
            //     d.encargado = valorSeleccionado;
            // },
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
                    let start = $("#tabla_boletas_observadas")
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
                data: "dias_estadia",
                className: "table-td",
                render: function (data) {
                    return `                    
                    <span class="badge text-capitalize ${
                        data != null ? "bg-danger" : "bg-danger"
                    } fs-6">
                        ${
                            data != null
                                ? `${data} ${data == 1 ? "día" : "días"}`
                                : "N/A"
                        }
                    </span>
                    `;
                },
            },

            {
                data: null,
                className: "table-td",
                render: function (data, type, row) {
                    return `

                    <div class="d-flex justify-content-center">

                    
                    ${
                        permissions["entrada"]
                            ? `<button type="button" class="btn btn-sm btn-outline-success px-2 d-inline-flex align-items-center ms-1" 
                                    title='Escribir al Whatsapp' 
                                    onclick="window.open('https://wa.me/${row.contacto}?text=Hola%2C%20le%20escribimos%20del%20Parqueo%20Municipal%20de%20Caranavi', '_blank')">
                                    <i class="fab fa-whatsapp  fs-16"></i>
                                </button>`
                            : ``
                    }           
                    
                    
                    ${
                        permissions["entrada"]
                            ? ` <button type="button" class="btn btn-sm btn-outline-success px-2 d-inline-flex align-items-center generar_tiket_entrada ms-1 btn-reporteEntrada" title='Genear Ticket de entrada' data-id="${row.id}">
                                 <i class="fas fa-file-pdf  fs-16"></i>
                            </button>`
                            : ``
                    }
                 
                 </div> `;
                },
            },
        ],
    });

    // // Permite filtrar por una fecha diferente
    // $("#filterFecha").on("change", function () {
    //     tabla_historialRegistro.ajax.reload();
    // });

    // // Listar todos los registros al presionar el botón
    // $("#btnListarTodo").on("click", function () {
    //     $("#filterFecha").val(""); // Limpia el valor del campo de fecha
    //     $("#encargados").val(""); // Limpia el valor del campo de fecha
    //     tabla_historialRegistro.ajax.reload(); // Recarga la tabla sin filtrar
    // });

    // // Escuchar el evento change en el select
    // $("#encargados").on("change", function () {
    //     // Obtener el valor seleccionado
    //     valorSeleccionado = $(this).val(); // Obtiene el valor (attribute value)
    //     tabla_historialRegistro.ajax.reload();
    //     // Mostrar en la consola los valores obtenidos
    // });
}

function actualizarTabla() {
    tabla_historialRegistro.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}


// Genear ticket de entrada
$(document).on("click", ".generar_tiket_entrada", function () {
    const idRegistro = $(this).data("id");
    $(".btn-reporteEntrada").prop("disabled", true);
    crud(
        "admin/generarTicketEntrada",
        "GET",
        idRegistro,
        null,
        function (error, response) {
            $(".btn-reporteEntrada").prop("disabled", false);
            if (response.tipo != "exito") {
                mensajeAlerta(response.mensaje, response.tipo);
                return;
            }
            mensajeAlerta("Generando Reporte.....", "exito");

            const blobUrl = generarURlBlob(response.mensaje); // Genera la URL del Blob

            // espera un segundo para abrir la nueva ventana
            setTimeout(() => {
                window.open(blobUrl, "_blank"); // Abre en una nueva pestaña
            }, 1500);
        }
    );
});

// PARA GENERAR UN BLOB PARA GENERAR EL REPORTE

function generarURlBlob(pdfbase64) {
    // Convertir Base64 a un Blob
    const byteCharacters = atob(pdfbase64); // Decodifica el Base64
    const byteNumbers = Array.from(byteCharacters).map((c) => c.charCodeAt(0));
    const byteArray = new Uint8Array(byteNumbers);
    const blob = new Blob([byteArray], { type: "application/pdf" });

    // Crear una URL para el Blob
    return URL.createObjectURL(blob);
}
