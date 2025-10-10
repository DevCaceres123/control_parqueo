import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";


// GENERAR REPORTE POR USUARIO
$('#form-reporte_usuario').submit(function (e) {    
    e.preventDefault();

    const usuariosSeleccionados = $('input[name="encargados_puesto[]"]:checked').map(function () {
        return $(this).val();
    }).get();


    $("#btn-reporte_usuario").prop("disabled", true);
    const datosFormulario = {
        fecha_inicio: $('#fecha_inicio_usuario').val(),
        fecha_final: $('#fecha_final_usuario').val(),
        usuario: usuariosSeleccionados
    };



    const btn = $("#btn-reporte_usuario");
    btn.prop("disabled", true).html('<i class="ri-loader-4-line spin"></i> Generando...');
    // envia a la funcion de store
    crud("admin/generar_reporte", "POST", null, datosFormulario, function (error, response) {
        btn.prop("disabled", false).html('<i class="ri-upload-cloud-line me-1"></i> Generar Reporte');
        //console.log(response);
        if (response.tipo == "errores") {
            $("#btn-reporte").prop("disabled", false);
            mensajeAlerta(response.mensaje, "errores");
            return;
        }

        if (response.tipo != "exito") {
            $("#btn-reporte").prop("disabled", false);
            mensajeAlerta(response.mensaje, response.tipo);
            $("#btn-reporte_usuario").prop("disabled", false);
            return;
        }


        mensajeAlerta("Generando Reporte.....", "exito");
        const blobUrl = generarURlBlob(response.mensaje); // Genera la URL del Blob


        setTimeout(() => {
            window.open(blobUrl, '_blank'); // Abre en una nueva pestaña
            $("#btn-reporte_usuario").prop("disabled", false);
        }, 1500);

    });
})




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


// MARCAR DESMARCAR CHECK
$('#select_all_user').on('change', function () {
    // Cambia el estado de todos los checkboxes dentro de #form_rol
    $('#form-reporte_usuario input[type="checkbox"]').prop('checked', this.checked);
});
