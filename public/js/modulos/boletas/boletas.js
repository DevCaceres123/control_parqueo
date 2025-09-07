import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";

let tipoSeleccionado = null;
let precioSeleccionado = null;
let idVehiculoSeleccionado = null;

$("#btn-generar").on("click", function () {
    // validamos vehículo
    if (!idVehiculoSeleccionado) {
        mensajeAlerta("Selecciona una tarifa",'error');
        return;
    }
    // 2️⃣ valores de los inputs según lo seleccionado
    let datos={
        'modo':$('input[name="modo"]:checked').val(), // "cliente" o "placa"
        'nombre': $("#nombre_cliente").val().trim(),
        'ci': $("#ci_cliente").val().trim(),
        'placa': $("#placa").val().trim(),
        'id_vehiculo':idVehiculoSeleccionado,
    };

    const btn = $("#btn-generar");
    btn.prop("disabled", true).html('<i class="ri-loader-4-line spin"></i> Subiendo...');
    crud("admin/boletas", "POST", null, datos, function (error, response) {
    btn.prop("disabled", false).html('<i class="ri-upload-cloud-line me-1"></i>Generar');
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }
        mensajeAlerta(response.mensaje, response.tipo);
    });




    // 4️⃣ aquí podrías hacer el iframe preview o un $.post/AJAX al servidor
});

// FUNCION PARA PONER EL VALOR Y MARCAR LA CASILLA DE VEHICULO
$(function () {
    // Selección de tipo (solo uno activo)
    $("#tipos-vehiculo .tipo-card").on("click", function () {
        $("#tipos-vehiculo .tipo-card")
            .removeClass("bg-primary text-white shadow-lg")
            .addClass("bg-white text-dark");

        $(this)
            .removeClass("bg-white text-dark")
            .addClass("bg-success text-white shadow-lg");

        tipoSeleccionado = $(this).data("vehiculo");
        precioSeleccionado = $(this).data("precio");
        idVehiculoSeleccionado = $(this).data("id"); // <- Aquí obtienes el ID
    });

    // Mostrar/Ocultar campos según radio
    $('input[name="modo"]').on("change", function () {
        if ($("#modo_cliente").is(":checked")) {
            $("#grupo_cliente").removeClass("d-none");
            $("#grupo_placa").addClass("d-none");
        } else {
            $("#grupo_cliente").addClass("d-none");
            $("#grupo_placa").removeClass("d-none");
        }
    });

    // // Imprimir iframe
    // $("#btn-imprimir").on("click", function () {
    //     document.getElementById("iframe-boleta").contentWindow.print();
    // });
});

function  generarVista(){
    
        const modo = $('input[name="modo"]:checked').val();
        const nombre = $("#nombre_cliente").val();
        const placa = $("#placa").val();

        $("#iframe-boleta").attr(
            "srcdoc",
            `
            <html>
            <head>
                <style>
                    body {font-family:sans-serif; padding:15px; font-size:14px;}
                    h3 {color:#0d6efd; margin-top:0;}
                    p {margin:3px 0;}
                </style>
            </head>
            <body>
                <h3>VERIFICAR DATOS....</h3>
                <p><strong>Vehículo:</strong> ${tipoSeleccionado}</p>
                <p><strong>Precio:</strong> Bs. ${precioSeleccionado}</p>
                <p>${
                    modo === "cliente"
                        ? "<strong>Cliente:</strong> " + nombre
                        : "<strong>Placa:</strong> " + placa
                }</p>
            </body>
            </html>
        `
        );
}
