# 🟦✨ AACOP – Sistema de Gestión de Capacitaciones (TFI UTN FSA) ✨🟦

Sistema web desarrollado como Trabajo Final Integrador para la Tecnicatura Universitaria en Programación – UTN FSA.  
Permite gestionar capacitaciones, docentes, participantes, asistencias, notas finales y certificados mediante flujos académicos completos.

---

## 📝 Descripción General

AACOP centraliza la administración de capacitaciones internas, simplificando tareas administrativas y académicas.

### 🚀 Funcionalidades principales

- 🔐 Autenticación de usuarios  
- 👥 Roles diferenciados:
  - 🛡️ Administrador
  - 🧑‍🏫 Docente
  - 🎓 Participante  
- 📚 CRUD de capacitaciones  
- 📝 Inscripciones con validación de cupos  
- 📅 Gestión de asistencias  
- 🧮 Carga de notas finales  
- 🏅 Emisión automática de certificados  
- 📊 Panel administrativo con métricas  
- ⚡ Componentes Livewire (acciones en tiempo real)  
- 📱 Interfaz responsive con TailwindCSS  
- 💾 Migraciones y Seeders  

---

## 📂 Módulos Principales

### 🛡️ Administrador
- Crear / editar / eliminar capacitaciones  
- Asignar docentes  
- Ver inscriptos  
- Gestionar asistencia  
- Administrar notas finales  
- Emitir certificados  
- Ver estadísticas del sistema  

### 🧑‍🏫 Docente
- Visualizar capacitaciones asignadas  
- Gestionar asistencias  
- Cargar notas finales  
- Ver listado de alumnos  

### 🎓 Participante
- Ver capacitaciones disponibles  
- Inscribirse  
- Descargar certificados aprobados  

---

## 🛠️ Tecnologías Utilizadas

### Backend
- 🐘 PHP 8.2+  
- 🎯 Laravel 12  
- ⚡ Livewire 3  
- 🔐 Laravel Breeze  
- 📦 Composer  

### Frontend
- 🎨 TailwindCSS  
- 🧩 Blade Templates  
- ⚡ Livewire Components  
- 🚀 Vite + npm  

### Base de datos
- 🐬 MySQL  
- 🧱 SQLite (para testing)  

---

## 🧱 Modelo de Datos (Simplificado)

### 👤 Tabla: `users`
- id
- name
- email
- password
- role (admin/docente/participante)
- timestamps


### 📚 Tabla: `capacitaciones`
- id
- titulo
- descripcion
- fecha_inicio
- fecha_fin
- cupos_maximos
- docente_id (FK → users)
- timestamps


### 📝 Tabla: `inscripciones`
- id
- user_id (FK)
- capacitaciones_id (FK)
- estado
- comentario
- timestamps
- UNIQUE (user_id, capacitaciones_id)


### 📅 Tabla: `asistencias`
- id
- inscripcion_id (FK)
- fecha
- asistio (boolean)
- timestamps


### 🧮 Tabla: `notas_finales`
- id
- inscripcion_id (FK)
- nota
- estado
- timestamps


---

## 🔄 Flujo Completo de una Capacitación

1. 🛡️ Administrador crea capacitación y asigna docente.  
2. 🎓 Participante se inscribe.  
3. 🔎 El sistema valida cupos y evita inscripciones duplicadas.  
4. 🧑‍🏫 Docente registra asistencia en cada clase.  
5. 🧮 Docente carga nota final.  
6. 🏅 Si aprueba → certificado disponible para descargar.  
7. 📊 Administrador revisa métricas y estados generales.  

---

## 💻 Instalación y Ejecución

```bash
git clone https://github.com/FabioArias23/AACOP.git
cd AACOP

composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed

npm run dev
php artisan serve



## 💻 Estructura del proyecto

app/
  Http/
  Models/
  Livewire/
resources/
  views/
  css/
  js/
database/
  migrations/
routes/
  web.php

  
  
  
  
  
  
## 👨‍💻 Equipo de Desarrollo

🎨 María Teresa Zamboni — Frontend · UI/UX · Livewire

💻 Fabio Arias — Backend · Arquitectura

🗄️ Leonardo Arce — Base de Datos · Integraciones

📜 Licencia

MIT — Uso académico.
