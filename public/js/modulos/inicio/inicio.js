import { mensajeAlerta } from "../../../funciones_helper/notificaciones/mensajes.js";
import { crud } from "../../../funciones_helper/operaciones_crud/crud.js";
import {
    vaciar_errores,
    vaciar_formulario,
} from "../../../funciones_helper/vistas/formulario.js";

$(document).ready(function () {
    // Definimos una función para el manejo de la alerta
    function mostrarAlertaBoletas(response) {
        // Si la consulta fue exitosa y los datos son válidos
        if (response.tipo === "exito" && response.mensaje && response.mensaje.cantidad > 0) {
            const cantidad = response.mensaje.cantidad;
            const boletas = response.mensaje.boletas;

            // 1. Construir la tabla HTML con los nuevos estilos de color
            let tabla = `
                <style>
                    /* Estilo para las filas rayadas dentro del SweetAlert (mejora la visibilidad) */
                    .custom-alert-table tbody tr:nth-child(even) {
                        background-color: #f7f9fc; /* Rayado muy sutil (Gris claro) */
                    }
                    .custom-alert-table tbody tr:nth-child(odd) {
                        background-color: #fff; /* Fondo blanco */
                    }
                    .custom-alert-table tbody tr:hover {
                        background-color: #e6f2ff !important; /* Resaltado Azul Suave al pasar el ratón */
                    }
                </style>
                <div class="table-responsive" style="max-height: 250px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 6px;">
                    <table class='table table-sm table-hover align-middle text-start mb-0 custom-alert-table'>
                        <thead style=" color: white; position: sticky; top: 0; z-index: 10;">
                            <tr>
                                <th style="width: 40%; padding-left: 15px; border-bottom: 2px solid #851a1a;">CI del Propietario</th>
                                <th style="width: 60%; border-bottom: 2px solid #851a1a;"><i class="fas fa-id-card me-1"></i> Placa del Vehículo</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            boletas.forEach((b) => {
                tabla += `
                    <tr>
                        <td style="padding-left: 15px; font-weight: 500; color: #333;">
                            ${b.ci || '<span class="text-danger"><i class="fas fa-exclamation-triangle"></i> N/A</span>'}
                        </td>
                        <td style="color: #333;">
                            ${b.placa || 'N/A'}
                        </td>
                    </tr>
                `;
            });

            tabla += "</tbody></table></div>";

            // 2. Elementos de diseño UX para el HTML interno del SweetAlert
            Swal.fire({
                title: "<span style='color: #851a1a; font-weight: 800;'>¡ALERTA CRÍTICA DE PARQUEO!</span>", 
                
                html: `
                    <div style="padding: 10px; background-color: #fcebeb; border: 1px solid #dc3545; border-radius: 8px; margin-bottom: 20px;">
                        <p style="font-size: 1.05em; color: #dc3545; margin-bottom: 5px; font-weight: bold;">
                           <i class="fas fa-shield-alt me-2"></i> REGISTROS PENDIENTES
                        </p>
                        <h2 style="font-size: 1.2em; color: #851a1a; margin-top: 5px; margin-bottom: 0;">
                            ${cantidad} Vehículo(s) Vencido(s)
                        </h2>
                    </div>

                    <p style="font-size: 0.95em; color: #343a40; margin-bottom: 15px;">
                        Estos vehículos tienen boletas con más de 30 días de antigüedad y requieren una acción inmediata.
                    </p>
                    
                    ${tabla}
                `,
                
                icon: "warning", 
                width: "700px", 
                showCancelButton: true,
                focusConfirm: false, 
                
                confirmButtonColor: "#851a1a", 
                cancelButtonColor: "#6c757d",
                confirmButtonText: '<i class="fas fa-list-ol me-1"></i> Ir al Listado Completo',
                cancelButtonText: '<i class="fas fa-check me-1"></i> Entendido, Cerrar',
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "/admin/boletas"; 
                }
            });
        }
    }

    // Llamada original al CRUD (sin cambios)
    crud(
        "admin/VerificarRegistrosPasados",
        "GET",
        null,
        null,
        function (error, response) {
            if (error) {
                console.error("Error al verificar registros:", error);
                return;
            }
            if (response.tipo === "error") {
                mensajeAlerta(response.mensaje, "error");
                return;
            }
            mostrarAlertaBoletas(response);
        }
    );
});