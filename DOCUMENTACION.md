# 📋 GUÍA COMPLETA - Sistema de Reservas Observatorio UMSA

## ✅ Problemas Resueltos

### Error de Migración (Columna 'role' duplicada)
**Problema:** La migración fallaba porque dos migraciones intentaban agregar la columna 'role'
**Solución:** Se actualizó la migración `2026_03_31_020139_add_custom_fields_to_users_table.php` para solo agregar campos nuevos (telefono, departamento) si no existen.

---

## 🎯 Funcionalidades Implementadas

### 1. **Dashboard Administrador** /admin/dashboard
- estadísticas de reservas (hoy, total, pendientes)
- Capacidad total de cupos
- Ocupación del día
- Listado de reservas recientes
- Estadísticas por estado

### 2. **Dashboard Secretaria** /secretaria/dashboard
- Listado de asistentes confirmados para hoy
- Reservas pendientes de confirmación
- Total de visitantes esperados
- Ocupación por turno con indicador visual

### 3. **Dashboard Usuario** /usuario/dashboard
- Mis reservas (todas)
- Próximas visitas confirmadas
- Estadísticas personales (total, confirmadas, pendientes, canceladas)
- Opción para crear nueva reserva
- Acciones rápidas (ver, editar, cancelar)

### 4. **Sistema de Reservas Completo**
- ✅ Crear reserva
- ✅ Ver detalles
- ✅ Editar reserva
- ✅ Cancelar/eliminar reserva
- ✅ Aprobar reserva (admin/secretaria)
- ✅ Rechazar reserva (admin/secretaria)
- ✅ Validación de disponibilidad de cupos
- ✅ Listado de todas las reservas

### 5. **Reportes y Estadísticas**
- Reportes de reservas
- Ocupación por turno
- Estadísticas generales
- Disponibilidad de cupos

---

## 👥 Usuarios de Prueba

### Admin
- **Email:** admin@observatorio.com
- **Contraseña:** password
- **Rol:** admin
- **Acceso:** Dashboard admin completo

### Secretaria  
- **Email:** secretaria@observatorio.com
- **Contraseña:** password
- **Rol:** secretaria
- **Acceso:** Dashboard secretaria, gestión de asistentes

### Usuarios Regulares
- **usuario1@example.com** hasta **usuario5@example.com**
- **Contraseña:** password para todos
- **Rol:** usuario
- **Acceso:** Solo su dashboard y crear/editar sus propias reservas

---

## 📅 Turnos de Ejemplo

1. **Turno Mañana** - 09:00 a 12:00 (30 personas)
2. **Turno Tarde** - 14:00 a 17:00 (30 personas)
3. **Turno Noche** - 18:00 a 21:00 (25 personas)

---

## 🗄️ Estructura de Base de Datos

### Tabla: users
- id
- name
- email
- password
- role (admin, secretaria, usuario)
- id_acceso (matrícula)
- telefono
- departamento
- timestamps

### Tabla: turnos
- id
- nombre
- hora_inicio
- hora_fin
- capacidad_maxima
- descripcion
- activo
- timestamps

### Tabla: reservas
- id
- user_id → users.id
- turno_id → turnos.id
- fecha
- hora_inicio / hora_fin (opcional)
- cantidad_personas
- estado (Pendiente, Confirmado, Cancelada, Rechazada)
- descripcion
- timestamps

---

## 🛣️ Rutas Disponibles

### Públicas
- `/` - Página de inicio

### Autenticadas
- `/user/dashboard` - Dashboard usuario
- `/admin/dashboard` - Dashboard admin
- `/secretaria/dashboard` - Dashboard secretaria
- `/reservas` - Listado de reservas
- `/reservas/crear` - Formulario nueva reserva
- `/reservas/{id}` - Ver detalles
- `/reservas/{id}/editar` - Editar reserva
- `/reservas/{id}/aprobar` - Aprobar (admin/secretaria)
- `/reservas/{id}/rechazar` - Rechazar (admin/secretaria)
- `/reservas/reportes/index` - Ver reportes

---

## 📝 Comandos Útiles Laravel

```bash
# Resetear base de datos y cargar datos de prueba
php artisan migrate:fresh --seed

# Solo ejecutar migraciones
php artisan migrate

# Solo cargar datos de prueba
php artisan db:seed

# Ver rutas registradas
php artisan route:list

# Iniciar servidor
php artisan serve
```

---

## 🔒 Control de Acceso

### Niveles de Acceso
- **Admin:** Acceso total a todo el sistema, crear/editar/eliminar/aprobar cualquier cosa
- **Secretaria:** Ver asistentes, confirmar/rechazar reservas, crear reservas, ver reportes
- **Usuario:** Solo crear y ver sus propias reservas, editar si aún no se confirman

### Protección
- Las rutas están protegidas con `auth()` middleware
- Los métodos de controlador validan permisos antes de ejecutarse
- Los formularios muestran solo acciones permitidas

---

## 🚀 Próximas Mejoras Sugeridas

1. Agregar campo de email de confirmación
2. Enviar notificaciones por email cuando se aprueba/rechaza
3. Sistema de recordatorios antes de la fecha
4. Integración con calendario visual
5. Reportes en PDF/Excel
6. Fotografías de perfil de usuarios
7. Sistema de comentarios en reservas
8. Historial de cambios
9. Importación masiva de turnos
10. API REST para integración externa

---

## 📞 Información de Contacto

Para dudas o problemas, contactar al administrador del sistema.

**Última actualización:** 5 de abril de 2026
