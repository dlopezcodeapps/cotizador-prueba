# Cotizador - Prueba Técnica

## Descripción
Cotizador es una aplicación web desarrollada como parte de una prueba técnica. Permite la gestión de clientes, productos, cotizaciones y generación de PDFs para las cotizaciones realizadas. Está construido con tecnologías modernas como Laravel, Vue 3 e Inertia.js.

---

## Tecnologías Utilizadas

### Backend:
- **Laravel 10**: Framework PHP para el backend y API REST.

### Frontend:
- **Vue 3**: Framework JavaScript para la interfaz de usuario.
- **Inertia.js**: Comunicación fluida entre el frontend y backend.
- **Tailwind CSS**: Framework CSS para estilos rápidos y responsivos.

### Otros:
- **MySQL**: Base de datos relacional.
- **Vite**: Empaquetador rápido para frontend.
- **Node.js**: Gestión de dependencias y herramientas del proyecto.

---

## Características Principales

### Clientes
- Crear y listar clientes.
- Seleccionar clientes en la creación de cotizaciones.

### Productos
- Gestión de productos con precios y stock dinámicos.
- Búsqueda e integración de precios por cantidad.

### Cotizaciones
- Crear cotizaciones asignadas a un cliente.
- Listar y visualizar detalles de cotizaciones.
- Generar PDF con los detalles de la cotización.

---

## Despliegue
El proyecto está desplegado y accesible en:
[http://cotizador.codeapps.cl/](http://cotizador.codeapps.cl/)

---

## Instalación Local
Siga estos pasos para correr el proyecto en su máquina local:

### Pre-requisitos
- PHP >= 8.1
- Composer
- Node.js y npm
- MySQL
- Servidor web (Apache o Nginx)

### Pasos
1. **Clonar el repositorio:**
   ```bash
   git clone https://github.com/dlopezcodeapps/cotizador-prueba.git
   cd cotizador-prueba
   ```

2. **Instalar dependencias de PHP:**
   ```bash
   composer install
   ```

3. **Instalar dependencias de Node.js:**
   ```bash
   npm install
   ```

4. **Configurar variables de entorno:**
   Copiar el archivo `.env.example` como `.env` y editar las variables de conexión a la base de datos.
   ```bash
   cp .env.example .env
   ```
   Actualizar los campos relevantes como `DB_DATABASE`, `DB_USERNAME` y `DB_PASSWORD`.

5. **Generar la clave de aplicación:**
   ```bash
   php artisan key:generate
   ```

6. **Migrar y poblar la base de datos:**
   ```bash
   php artisan migrate --seed
   ```

7. **Compilar recursos:**
   ```bash
   npm run build
   ```

8. **Levantar el servidor local:**
   ```bash
   php artisan serve
   ```
   Acceder en: `http://localhost:8000`

---

## Estructura del Proyecto

### Backend
- **`app/Models`**: Modelos para Clientes, Productos, Precios y Cotizaciones.
- **`app/Http/Controllers`**: Controladores para manejar las solicitudes HTTP.
- **`routes/web.php`**: Definición de rutas.

### Frontend
- **`resources/js/Pages`**: Vistas y componentes principales para cada página.
- **`resources/js/Components`**: Componentes reutilizables como tablas, formularios y diálogos.

---

## Contacto
Creador: **Diego López**
- **Correo:** [diego.lopez2000@hotmail.com](mailto:diego.lopez2000@hotmail.com)
- **Teléfono:** +56992048648
- **GitHub:** [https://github.com/dlopezcodeapps](https://github.com/dlopezcodeapps)

---

## Licencia
Este proyecto es solo para fines de evaluación técnica y no está licenciado para su uso comercial.

