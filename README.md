# **Tinder de Mascotas** 🐾

### Descripción

Tinder de Mascotas es una aplicación móvil desarrollada con **Flutter** para el frontend y **Laravel** para el backend. La aplicación permite a los usuarios encontrar compañeros para sus mascotas mediante un sistema de "deslizar para emparejar". Los dueños de mascotas pueden crear perfiles para sus mascotas, buscar otras mascotas en su área, y establecer conexiones basadas en intereses comunes.

### Funcionalidades principales

- **Registro e inicio de sesión** de usuarios con autenticación (Laravel Sanctum).
- **Creación de perfiles** para mascotas con detalles como edad, raza, y fotos.
- **Búsqueda de mascotas** en función de la ubicación y otros filtros.
- **Deslizar para emparejar**: Desliza a la derecha para "me gusta" y a la izquierda para pasar.
- **Sistema de mensajes** para las coincidencias.
- **Gestión de perfil**: Los usuarios pueden editar los detalles y fotos de sus mascotas.

### Tecnologías utilizadas

#### Frontend
- **Flutter**: Framework para desarrollo de aplicaciones móviles multiplataforma.
- **Dart**: Lenguaje de programación utilizado por Flutter.

#### Backend
- **Laravel**: Framework PHP para el backend, API y base de datos.
- **Laravel Sanctum**: Para la autenticación de API.
- **MySQL/PostgreSQL**: Base de datos utilizada para almacenar usuarios y perfiles de mascotas.

### Instalación

#### Requisitos previos

- **PHP 8.x** o superior
- **Composer**
- **MySQL/PostgreSQL**
- **Node.js** y **npm**
- **Flutter SDK**

#### Instalación del Backend (Laravel)

1. Clona este repositorio:

   ```bash
   cd tinder-mascotas-backend
