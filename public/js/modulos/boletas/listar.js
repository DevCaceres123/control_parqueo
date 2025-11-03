import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";

let permissions;
let tabla_historialRegistro;
let valorSeleccionado;
let colorSelect;
let vehiculoSelect;
let selectrColor;
let selectrVehiculo;
$(document).ready(function () {
    listar_registros();
    // iniciamos el selectr para poder usarlo
    colorSelect = document.getElementById("color");
    vehiculoSelect = document.getElementById("tipo_vehiculo");

    selectrColor = new Selectr(colorSelect, {
        searchable: true,
        placeholder: "Busca o selecciona un color...",
    });

    selectrVehiculo = new Selectr(vehiculoSelect, {
        searchable: true,
        placeholder: "Busca o selecciona un tipo de vehículo...",
    });
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
                data: "dias_cobrados",
                className: "table-td",
                render: function (data) {
                    return `                    
                    <span class="badge text-capitalize ${
                        data != null ? "bg-primary" : "bg-danger"
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
                        permissions["editar"]
                            ? ` <button type="button" class="btn btn-sm btn-outline-warning px-2 d-inline-flex align-items-center  ms-1 btn-editar" title='Editar'data-id="${row.id}">
                                 <i class="las la-pen fs-18 fs-16"></i>
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

                     ${
                         permissions["salida"]
                             ? ` <button type="button" class="btn btn-sm btn-outline-info px-2 d-inline-flex align-items-center generar_tiket_salida ms-1" title='Genera Ticket de salida'data-id="${row.id}">
                                 <i class="fas fa-money-bill   fs-16"></i>
                            </button>`
                             : ``
                     }

                    ${
                        permissions["contacto"]
                            ? `<button type="button" class="btn btn-sm btn-outline-success px-2 d-inline-flex align-items-center ms-1" 
                                    title='Escribir al Whatsapp' 
                                    onclick="window.open('https://wa.me/${row.contacto}?text=Hola%2C%20le%20escribimos%20del%20Parqueo%20Municipal%20de%20Caranavi', '_blank')">
                                    <i class="fab fa-whatsapp  fs-16"></i>
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
            crud(
                "admin/listarBoletas",
                "DELETE",
                idRegistro,
                null,
                function (error, response) {
                    if (response.tipo != "exito") {
                        mensajeAlerta(response.mensaje, response.tipo);
                        return;
                    }
                    mensajeAlerta(response.mensaje, response.tipo);
                    actualizarTabla();
                }
            );
        }
    });
});

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

// Generar ticket de salida
$(document).on("click", ".generar_tiket_salida", function () {
    const idRegistro = $(this).data("id");
    $(".btn-reporteSalida").prop("disabled", true);
    crud(
        "admin/generarTicketSalida",
        "GET",
        idRegistro,
        null,
        function (error, response) {
            $(".btn-reporteSalida").prop("disabled", false);
            // console.log(response.mensaje);
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

// obtener valores de los vehiculos para editar
$(document).on("click", ".btn-editar", function () {
    const idRegistro = $(this).data("id");
    crud(
        "admin/listarBoletas",
        "GET",
        idRegistro + ["/edit"],
        null,
        function (error, response) {
            // Verificamos que no haya un error o que todos los campos sean llenados
            if (response.tipo === "errores") {
                mensajeAlerta(response.mensaje, "errores");
                return;
            }
            if (response.tipo != "exito") {
                mensajeAlerta(response.mensaje, response.tipo);
                return;
            }
            $("#editar_registro").modal("show");
            // //si todo esta correcto muestra el mensaje de correcto
            $("#registro_id").val(response.mensaje.id);
            $("#placa").val(response.mensaje.placa);
            $("#ci").val(response.mensaje.ci);
            $("#contacto").val(response.mensaje.contacto.telefono);

            // Si ya tienes los selectr inicializados:
            selectrColor.setValue(response.mensaje.color_id);
            selectrVehiculo.setValue(response.mensaje.vehiculo_id);
        }
    );
});

$("#form_editar_datos").on("submit", function (e) {
    e.preventDefault();
    $("#btn_guardar_datos").prop("disabled", true);
    let id_registro = $("#registro_id").val();
    let datos = {
        placa: $("#placa").val(),
        ci: $("#ci").val(),
        color_id: $("#color").val(),
        vehiculo_id: $("#tipo_vehiculo").val(),
        contacto: $("#contacto").val(),
    };

    vaciar_errores("form_editar_datos");

    crud(
        "admin/listarBoletas",
        "PUT",
        id_registro,
        datos,
        function (error, response) {
            $("#btn_guardar_datos").prop("disabled", false);
            // console.log(response);

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
            $("#editar_registro").modal("hide");
            vaciar_formulario("form_editar_datos");
            mensajeAlerta(response.mensaje, response.tipo);
            actualizarTabla();
        }
    );
});

// Deshabilitar un input si el otro tiene valor y viceversa
document.addEventListener("DOMContentLoaded", function () {
    const placaInput = document.getElementById("placa");
    const ciInput = document.getElementById("ci");

    function toggleInputs() {
        if (placaInput.value.trim() !== "") {
            ciInput.disabled = true;
            ciInput.value = "";
        } else {
            ciInput.disabled = false;
        }

        if (ciInput.value.trim() !== "") {
            placaInput.disabled = true;
            placaInput.value = "";
        } else {
            placaInput.disabled = false;
        }
    }

    placaInput.addEventListener("input", toggleInputs);
    ciInput.addEventListener("input", toggleInputs);
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

// generar reporte diario

// obtener valores de los vehiculos para editar
$(document).on("click", "#reporte_diario", function (e) {
    
    e.preventDefault();
   
    let datos = {
        fecha:$("#filterFecha").val(), // Agrega la fecha al request      
    };
    crud(
        "admin/reporteDiario",
        "POST",
        null,
        datos,
        function (error, response) {
            // Verificamos que no haya un error o que todos los campos sean llenados
            if (response.tipo === "errores") {
                mensajeAlerta(response.mensaje, "errores");
                return;
            }
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
