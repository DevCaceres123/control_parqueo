<!DOCTYPE html>
<html lang="en" data-startbar="dark" data-bs-theme="light">

<head>
    <meta charset="utf-8" />
    <title>LOGIN</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('admin_template/images/favicon.ico') }}">

    <!-- App css -->
    <link href="{{ asset('admin_template/css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_template/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('admin_template/css/app.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        body {
            height: 100vh;
            margin: 0;
            background: url('{{ asset('assets/gampCaranavi.webp') }}') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        /* Fondo más oscuro para resaltar la tarjeta */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.4);
            /* Incrementa la oscuridad del fondo general */
            z-index: -1;
        }

        .login-card {
            width: 100%;
            max-width: 400px;
            /* Cambiado de blanco transparente a un gris oscuro muy transparente */
            background: rgba(30, 30, 45, 0.8);
            /* Gris oscuro azulado transparente */
            backdrop-filter: blur(12px);
            /* Un poco más de blur para el efecto vidrioso */
            border-radius: 20px;
            box-shadow: 0 0 50px rgba(0, 0, 0, 0.9);
            /* Sombra más intensa */
            /* Borde sutil en tono azulado/morado */
            border: 1px solid rgba(100, 100, 150, 0.3);
            color: #e0e0e0;
            /* Color de texto general más claro pero no blanco puro */
            padding: 2.5rem 2rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .login-card:hover {
            transform: translateY(-8px);
            /* Más movimiento al pasar el ratón */
            box-shadow: 0 10px 60px rgba(0, 0, 0, 1);
            /* Sombra aún más profunda al pasar el ratón */
        }

        .auth-logo {
            height: 80px;
            transition: all 0.3s ease;
        }

        /* Estilo del botón: gradiente vibrante azul/morado */
        .btn-primary {
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            /* Gradiente de morado a azul vibrante */
            border: none;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            padding: 0.75rem 1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            /* Sombra al botón */
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2575fc, #6a11cb);
            /* Invertir gradiente al pasar el ratón */
            box-shadow: 0 8px 20px rgba(37, 117, 252, 0.6);
            /* Sombra de color del gradiente */
            transform: scale(1.03);
            /* Más énfasis en el hover */
        }

        /* Estilo de los Inputs: fondo más oscuro, texto claro */
        input.form-control {
            background-color: rgba(50, 50, 70, 0.7);
            /* Fondo de input más oscuro y sólido */
            border: 1px solid rgba(120, 120, 180, 0.4);
            /* Borde sutil y oscuro */
            color: #e0e0e0;
            /* Texto claro */
            transition: all 0.3s ease;
        }

        input.form-control:focus {
            background-color: rgba(60, 60, 80, 0.9);
            /* Más oscuro y opaco al enfocar */
            border-color: #6a11cb;
            /* Borde de enfoque morado */
            box-shadow: 0 0 0 0.25rem rgba(106, 17, 203, 0.3);
            /* Sombra de enfoque morada */
            color: #fff;
        }

        input.form-control::placeholder {
            color: rgba(180, 180, 200, 0.7);
            /* Placeholder más visible */
        }

        label {
            font-weight: 500;
            color: rgba(220, 220, 230, 0.9);
            /* Color de labels más claro */
        }

        .text-shadow {
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.9);
            /* Sombra de texto más pronunciada */
        }

        #mensaje_error {
            color: #ffda6a;
            /* Amarillo/naranja más suave para el error */
            min-height: 25px;
        }
    </style>

</head>

<!-- Top Bar Start -->

<body>
    <div class="login-card text-center">
        <div class="mb-4">
            <img src="{{ asset('assets/logo-caranavi.webp') }}" alt="logo" class="auth-logo">
        </div>
        <h4 class="fw-bold text-shadow mb-1">¡Bienvenido!</h4>
        <p class="text-shadow system-title mb-4">SISTEMA DE PARQUEO</p>

        <div id="mensaje_error" class="text-warning fw-semibold mb-2"></div>

        <form id="formulario_login" autocomplete="off">
            @csrf
            <div class="form-group mb-3 text-start">
                <label for="usuario">Usuario</label>
                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Ingrese usuario">
            </div>
            <div class="form-group mb-4 text-start">
                <label for="password">Contraseña</label>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Ingrese la contraseña">
            </div>
            <div class="d-grid">
                <button type="submit" id="btn_ingresar_usuario" class="btn btn-primary">
                    INGRESAR <i class="fas fa-sign-in-alt ms-1"></i>
                </button>
            </div>
        </form>
    </div>
</body>

</html>

<script>
    // Seleccionar elementos del DOM
    let loginBtn = document.getElementById('btn_ingresar_usuario');
    let formularioLogin = document.getElementById('formulario_login');
    let mensajeError = document.getElementById('mensaje_error');

    // Función para crear y mostrar alertas
    function mostrarAlerta(tipo, mensaje) {
        let iconoClase = tipo === 'success' ? 'fa-check' : 'fa-xmark';
        let color = tipo === 'success' ? 'success' : 'danger';
        mensajeError.innerHTML = `
        <div class="alert alert-${color} shadow-sm border-theme-white-2" role="alert">
            <div class="d-inline-flex justify-content-center align-items-center thumb-xs bg-${color} rounded-circle mx-auto me-1">
                <i class="fas ${iconoClase} align-self-center mb-0 text-white"></i>
            </div>
            <strong class='text-light'>${mensaje}</strong>
        </div>
        `;
        // Configurar el temporizador para ocultar la alerta después de 5 segundos
        setTimeout(() => {
            mensajeError.innerHTML = '';
        }, 4000);
    }

    // Función para validar el botón
    function validarBoton(estaDeshabilitado, mensaje) {
        loginBtn.textContent = mensaje;
        loginBtn.disabled = estaDeshabilitado;
    }

    // Manejar el envío del formulario
    loginBtn.addEventListener('click', async (e) => {
        let datos = Object.fromEntries(new FormData(formularioLogin).entries());
        validarBoton(true, "Verificando datos...");
        try {
            let respuesta = await fetch("{{ route('log_ingresar') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(datos)
            });
            if (!respuesta.ok) {
                throw new Error(`HTTP error! status: ${respuesta.status}`);
            }
            let data = await respuesta.json();
            mostrarAlerta(data.tipo, data.mensaje);
            if (data.tipo === 'success') {
                validarBoton(true, 'Datos correctos...');
                setTimeout(() => window.location.reload(), 1500);
            } else {
                validarBoton(false, 'INGRESAR');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarAlerta('error', 'Ocurrió un error al procesar la solicitud');
            validarBoton(false, 'INGRESAR');
        }
    });
</script>
