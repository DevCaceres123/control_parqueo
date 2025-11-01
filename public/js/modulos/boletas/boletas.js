import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";

let tipoSeleccionado = null;
let precioSeleccionado = null;
let idPrecio = null;

$("#btn-generar").on("click", function () {
    // validamos vehículo
    if (!idPrecio) {
        mensajeAlerta("Selecciona una tarifa", "error");
        return;
    }
    // 2️⃣ valores de los inputs según lo seleccionado
    let datos = {
        modo: $('input[name="modo"]:checked').val(), // "cliente" o "placa"
        nombre: $("#nombre_cliente").val().trim(),
        ci: $("#ci_cliente").val().replace(/\s+/g, ''),
        placa: $("#placa").val().replace(/\s+/g, ''),
        precio_id: idPrecio,
        precio: precioSeleccionado,
        color_id: $("#color_id").val(),
        vehiculo_id: $("#vehiculo_id").val(),
        contacto: $("#contacto").val().trim(),
    };

    const btn = $("#btn-generar");
    btn.prop("disabled", true).html(
        '<i class="ri-loader-4-line spin"></i> Subiendo...'
    );
    crud("admin/boletas", "POST", null, datos, function (error, response) {
        btn.prop("disabled", false).html(
            '<i class="ri-upload-cloud-line me-1"></i>Generar'
        );
        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }
        document.getElementById("contendor_cobrar").classList.add("d-none");
        document.getElementById("contenedor_boleta").classList.remove("d-none");
        mensajeAlerta("Boleta Generada Correctamente", response.tipo);

        // generamos un blob url para mostrar la boleta en el iframe
        let pdfUrl = generarURlBlob(response.mensaje.boleta);
        $("#nombre_cliente").val("");
        $("#ci_cliente").val("");
        $("#placa").val("");
        // $("#color_id").prop("selectedIndex", 0).trigger("change");
        $("#contacto").val("");

        // deseleccionar tarifa
        idPrecio = null;
        precioSeleccionado = null;
        $("#precios .tipo-precios").removeClass(
            "bg-success text-white shadow-lg"
        );

        const iframe = document.getElementById("iframe-boleta");
        iframe.src = pdfUrl;

        iframe.onload = () => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print(); // Disparar impresión automática desde el iframe
            marcarBoletaImpresa(response.mensaje.codigoUnico);
        };
    });
});

function marcarBoletaImpresa(codigo) {
    fetch("marcarBoletaImpresa/" + codigo, {
        method: "PUT",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute("content"),
        },
        body: JSON.stringify({}),
    });
}

//Cuando vuelve la conexión
window.addEventListener("online", async () => {
    let pendientes = JSON.parse(localStorage.getItem("pendientes") || "[]");
    if (pendientes.length === 0) return;

    console.log("Reintentando boletas pendientes:", pendientes);

    const reenviados = [];
    for (const codigo of pendientes) {
        try {
            const resp = await fetch(
                "marcarBoletaImpresa/" + encodeURIComponent(codigo),
                {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute("content"),
                    },
                    body: JSON.stringify({}),
                }
            );
            if (!resp.ok) throw new Error("Respuesta no OK");
            console.log(`✅ reenviado ${codigo}`);
            reenviados.push(codigo);
        } catch (e) {
            console.warn(`❌ sigue fallando ${codigo}`);
        }
    }

    // eliminar solo los que sí se reenviaron
    if (reenviados.length > 0) {
        pendientes = pendientes.filter((c) => !reenviados.includes(c));
        localStorage.setItem("pendientes", JSON.stringify(pendientes));
    }
});

// FUNCION PARA PONER EL VALOR DEL PRECIO
$(function () {
    // Selección de tipo (solo uno activo)
    $("#precios .tipo-precios").on("click", function () {
        $("#precios .tipo-precios")
            .removeClass("bg-primary text-white shadow-lg")
            .addClass("bg-white text-dark");

        $(this)
            .removeClass("bg-white text-dark")
            .addClass("bg-success text-white shadow-lg");

        tipoSeleccionado = $(this).data("precios");
        precioSeleccionado = $(this).data("precio");
        idPrecio = $(this).data("id"); // <- Aquí obtienes el ID
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

function llenarBoletaPrint(
    boleta,
    total,
    salida_vehiculo,
    vehiculo,
    montoRetraso,
    montoVehiculo,
    tiempoEstadia,
    
) {
    // Mostrar el div completo de la boleta
    document.getElementById("contendor_cobrar").classList.remove("d-none");
    document.getElementById("contenedor_boleta").classList.add("d-none");

    // Llenar campos obligatorios
    document.getElementById("print-num_boleta").innerText = boleta.num_boleta;
    document.getElementById("print-vehiculo").innerText = vehiculo.nombre;
    document.getElementById("print-placa").innerText = boleta.placa ?? "N/A";
    document.getElementById("print-entrada").innerText = boleta.entrada_veh;
    document.getElementById("print-salida").innerText = boleta.salidaMax;
    document.getElementById("print-tiempo_estadia").innerText = tiempoEstadia;
    

    document.getElementById(
        "print-monto_inicial"
    ).innerText = `Bs. ${montoVehiculo}`;
    document.getElementById(
        "print-monto_retraso"
    ).innerText = `Bs. ${montoRetraso}`;
    document.getElementById("print-total").innerText = `${total}`;
    document.getElementById(
        "print-salida-hora"
    ).innerText = `${salida_vehiculo}`;

    // Mostrar y llenar campos opcionales solo si hay persona
    if (boleta.ci) {
        document
            .getElementById("print-nombre-container")
            .classList.remove("d-none");

        document
            .getElementById("print-ci-container")
            .classList.remove("d-none");

        document.getElementById("print-nombre").innerText = boleta.persona;

        document.getElementById("print-ci").innerText = boleta.ci || "N/A";
    } else {
        // Si no hay persona, ocultar los contenedores opcionales
        document
            .getElementById("print-nombre-container")
            .classList.add("d-none");
        document.getElementById("print-ci-container").classList.add("d-none");
        document.getElementById("print-ci-container").classList.add("d-none");
    }
}

//Buscar Boleta y obtener precio

$("#btn-buscar").on("click", function () {
    // valor del radio seleccionado
    const filtro = $('input[name="filtro"]:checked').val();
    // valor que escribió el usuario
    const valor = $("#filtro_valor").val().replace(/\s+/g, "");

    if (!valor) {
        mensajeAlerta("Ingrese un valor para buscar", "warning");
        return;
    }

    let datos = {
        filtro: filtro,
        valor: valor,
    };

    const btn = $("#btn-buscar");
    btn.prop("disabled", true).html('<i class=""></i> Buscando...');
    crud("admin/buscarBoleta", "POST", null, datos, function (error, response) {
        btn.prop("disabled", false).html(
            '<i class="fas fa-search me-1"></i>Buscar'
        );

        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        mensajeAlerta("Generando datos...", response.tipo);
        let datos_boleta = response.mensaje.datos_boleta;
        let total = response.mensaje.total;
        let salida_vehiculo = response.mensaje.salida_vehiculo;
        let datosVehiculo = response.mensaje.datos_vehiculo;
        let montoRetraso = response.mensaje.montoRetraso;
        let montoVehiculo = response.mensaje.montoVehiculo;
        let tiempoEstadia = response.mensaje.tiempoEstadia;
        
        llenarBoletaPrint(
            datos_boleta,
            total,
            salida_vehiculo,
            datosVehiculo,
            montoRetraso,
            montoVehiculo,
            tiempoEstadia,
            
        );
    });
});

// boton para imprimir pago
$("#btn-imprimir_pago").on("click", function () {
    let datos = {
        numeroBoleta: $("#print-num_boleta").text(),
        total: $("#print-total").text(),
        horaSalida: $("#print-salida-hora").text(),
        estadia: $("#print-tiempo_estadia").text(),
        retraso: $("#print-tiempo_retraso").text(),        
    };

    const btn = $("#btn-imprimir_pago");
    btn.prop("disabled", true).html('<i class=""></i> Imprimiendo...');
    crud("admin/boletaPagada", "POST", null, datos, function (error, response) {
        btn.prop("disabled", false).html(
            '<i class="fas fa-search me-1"></i>Imprimir Pago'
        );

        if (response.tipo != "exito") {
            mensajeAlerta(response.mensaje, response.tipo);
            return;
        }

        document.getElementById("contendor_cobrar").classList.add("d-none");
        document.getElementById("contenedor_boleta").classList.remove("d-none");

        mensajeAlerta("Boleta Generada Correctamente", response.tipo);

        // generamos un blob url para mostrar la boleta en el iframe
        let pdfUrl = generarURlBlob(response.mensaje);
        

        const iframe = document.getElementById("iframe-boleta");
        iframe.src = pdfUrl;

        iframe.onload = () => {
            iframe.contentWindow.focus();
            iframe.contentWindow.print(); // Disparar impresión automática desde el iframe            
        };
    });
});

// nos servira para crear una url para poder visualizar nuestro pdf

function generarURlBlob(pdfbase64) {
    // Convertir Base64 a un Blob
    const byteCharacters = atob(pdfbase64); // Decodifica el Base64
    const byteNumbers = Array.from(byteCharacters).map((c) => c.charCodeAt(0));
    const byteArray = new Uint8Array(byteNumbers);
    const blob = new Blob([byteArray], { type: "application/pdf" });

    // Crear una URL para el Blob
    return URL.createObjectURL(blob);
}
