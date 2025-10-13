import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";
let permisosGlobal;
let tabla_vehiculo;

$(document).ready(function () {
    listar_afiliado();
});

function listar_afiliado() {
    tabla_vehiculo = $("#tabla_vehiculo").DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: "listarVehiculos", // Ruta que recibe la solicitud en el servidor
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
                    let start = $('#tabla_vehiculo').DataTable().page.info().start;
                    return start + meta.row + 1;
                }
            },
            {
                data: "nombre",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
                    `;
                },
            },
            {
                data: "descripcion",
                className: "table-td text-uppercase",
                render: function (data) {
                    return `                            
                        ${data}
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
                        permisosGlobal["estado"] == true
                            ? `
                            <a class="cambiar_estado_vehiculo" data-id="${row.id},${row.estado}">
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
                data: "tarifa",
                className: "table-td ",
                render: function (data) {
                    return `                            
                        <span  class='p-1 pe-3 ps-3 bg-warning rounded-pill text-light fs-15'>${data} <b>Bs</b></span>
                    `;
                },
            },


            {
                data: null,
                className: "table-td text-end",
                render: function (data, type, row) {
                    return ` <div class="d-flex justify-content-center">

                         ${permisosGlobal.eliminar
                            ? `
                        <a class="btn btn-sm btn-outline-danger px-2 d-inline-flex align-items-center eliminar_vehiculo me-1" data-id="${row.id}" title="Eliminar Sede">
                            <i class="fas fa-window-close fs-16"></i>
                        </a>
                            `
                            : ``
                        }
                      
                             ${permisosGlobal.editar
                            ? ` <a class="btn btn-sm btn-outline-warning px-2 d-inline-flex align-items-center editar_vehiculo me-1" data-id="${row.id}" title="Editar Sede">
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
    tabla_vehiculo.ajax.reload(null, false); // Recarga los datos sin resetear el paginado
}

// Agregar nuevos vehiculos y sus tarifas;
$("#vehiculos").on("submit", function (e) {

    e.preventDefault();
    let ruta = $("#btn_guardar_tipoVehiculo").attr("data-id");
    let idEditar = null;
    let metodo = null;
    let formData = new FormData(this);
    if (ruta == 'nuevo') {
        ruta = 'admin/vehiculos';
        metodo = 'POST';
    }

    if (ruta == 'editar') {
        ruta = 'admin/vehiculos';
        metodo = 'PUT';
        idEditar = $("#vehiculo_id").val();      
        formData = Object.fromEntries(new FormData(this));  
    }
    
    vaciar_errores("vehiculos");
    // Opcional: Desactivar botón para evitar doble clic    

    const btn = $("#btn_guardar_tipoVehiculo");
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
        $("#modal_tipoVehiculo").modal("hide");

        mensajeAlerta(response.mensaje, response.tipo);
        vaciar_formulario('vehiculos');
        actualizarTabla();
    });
});

//Eliminar vehiculo
$(document).on("click", ".eliminar_vehiculo", function () {
    const idVehiculo = $(this).data("id");    
    Swal.fire({
        title: "¿Eliminar?",
        text: "Esta acción no se puede deshacer.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            crud("admin/vehiculos", "DELETE", idVehiculo, null, function (error, response) {
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

// obtener valores de los vehiculos para editar
$(document).on("click", ".editar_vehiculo", function () {
    const idVehiculo = $(this).data("id");    
      crud("admin/vehiculos", "GET", idVehiculo+['/edit'], null, function (error, response) {        

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
        $("#modal_tipoVehiculo").modal("hide");

        $('#vehiculo_id').val(response.mensaje.id);
        $('#nombre').val(response.mensaje.nombre);
        $('#descripcion_vehiculo').val(response.mensaje.descripcion);
        $('#tarifa').val(response.mensaje.tarifa);
    });
});


// cambiar estado vehiculo
$("#tabla_vehiculo").on("click", ".cambiar_estado_vehiculo", function (e) {
    e.preventDefault(); // Evitar que el enlace recargue la página

    // Obtener el valor de data-id
    var dataId = $(this).data("id");

    // Separar el id y el estado
    var values = dataId.split(",");

    console.log(dataId);
    let datos = {
        estado: values[1],
    };



    crud("admin/cambiarEstadoVehiculos", "PUT", values[0], datos, function (error, response) {
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
//<<<<<<<<<<<<<------------------------------------>>>>>>>>>>>>>.
//FUNCIONES DE APOYO
//estas funciones nos ayuadaran para trabajar el nuevo y el editar   en un mismo modal


//mostrar el modal para ingresar nuevo vehiculo
$(document).on("click", ".nuevo_vehiculo", function () {
    // le asignas el data-id al botón guardar
    $("#btn_guardar_tipoVehiculo").attr("data-id", "nuevo");
    vaciar_errores("vehiculos");
    // luego muestras el modal
    $("#modal_tipoVehiculo").modal("show");

    // editamos el titulo del modal
    $("#titulo_modal").html('<i class="fas fa-car me-1"></i> NUEVO VEHICULO');
});


//mostrar el modal para editar un vehiculo
$(document).on("click", ".editar_vehiculo", function () {
    // le asignas el data-id al botón guardar
    $("#btn_guardar_tipoVehiculo").attr("data-id", "editar");
    vaciar_errores("vehiculos");
    // luego muestras el modal
    $("#modal_tipoVehiculo").modal("show");
    // editamos el titulo del modal
    $("#titulo_modal").html('<i class="fas fa-car me-1"></i> EDITAR VEHICULO');

});

$(document).on("click", ".limpiar_modal", function () {
    vaciar_formulario('vehiculos');
});