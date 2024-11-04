# ParkEase - Sistema de Gestión de Parqueaderos

**ParkEase** es una solución de software diseñada para gestionar de manera eficiente los parqueaderos de la Universidad del Rosario. Este sistema busca facilitar la administración y el uso de los espacios de estacionamiento a través de una plataforma intuitiva que ofrece funcionalidades específicas para tres tipos de usuarios: estudiantes, personal de la universidad y administradores. 

## Objetivo del Proyecto

El objetivo principal de **ParkEase** es optimizar el uso de los espacios de parqueadero en la universidad, permitiendo una administración clara y precisa de las reservas y el estado de los espacios disponibles. El sistema también busca mejorar la experiencia de los usuarios al proporcionar una herramienta fácil de usar para gestionar sus reservas de estacionamiento.

## Principales Funcionalidades

### Gestión de Parqueaderos por Piso y Tipo de Vehículo

- El sistema organiza los espacios de estacionamiento por piso y tipo de vehículo (carro o moto).
- Cada espacio de estacionamiento está identificado con un formato específico (por ejemplo, A1, B1, etc.) y tiene un estado que indica si está **disponible**, **reservado** o **ocupado**.
- Los administradores pueden visualizar el estado de cada espacio de estacionamiento por piso y tipo de vehículo.

### Lógicas de Negocio Principales

#### Reservas Activas con Temporizador

- **ParkEase** permite a los usuarios reservar espacios de estacionamiento.
- Una reserva activa inicia un temporizador de 15 minutos. Si el usuario no ocupa el espacio dentro de este periodo, el estado de la reserva cambia automáticamente a **perdido**.
- El temporizador está implementado como un contador regresivo en la interfaz del usuario, que actualiza el estado en tiempo real y cambia a **ocupado** si el usuario llega dentro del tiempo límite.

#### Restricciones de Reserva

- El sistema impone restricciones para evitar múltiples reservas activas por usuario. Si un usuario tiene una reserva activa, no puede crear una nueva hasta que la reserva actual finalice o sea cancelada.
- Se gestiona un límite de tres vehículos por usuario, con validaciones para verificar que el usuario no supere este límite al intentar registrar un nuevo vehículo.

#### Cambio de Estado de Espacios

- Los espacios de estacionamiento tienen tres posibles estados: **disponible**, **reservado**, y **ocupado**. El estado cambia automáticamente según las acciones del usuario o la expiración de la reserva.
- Los administradores pueden consultar el estado de todos los espacios de parqueo en tiempo real.

### Interfaces del Sistema

#### Menú Usuario

- Los estudiantes y el personal tienen acceso a una interfaz donde pueden seleccionar su vehículo, visualizar sus reservas activas y realizar nuevas reservas si cumplen con los requisitos.
- La interfaz también muestra el estado de los parqueaderos por piso y el tipo de vehículo, permitiendo a los usuarios saber qué espacios están disponibles o reservados.

#### Menú Personal

- El personal de seguridad puede consultar las reservas activas a través de una interfaz específica, facilitando la verificación de reservas en tiempo real y ayudando en la administración general de los espacios de parqueo.
- También pueden actualizar el estado de una reserva según las necesidades.

#### Menú Administrador

- Los administradores pueden visualizar todas las reservas activas y el estado general de los parqueaderos.
- Cuentan con la opción de generar reportes detallados sobre la ocupación de los parqueaderos y el uso de los espacios, permitiendo una supervisión completa y la generación de estadísticas para la toma de decisiones.

## Futuras Mejoras

1. **Notificaciones**: Implementación de alertas automáticas para notificar a los usuarios cuando su reserva esté a punto de expirar.
2. **Reportes avanzados**: Integración de reportes detallados sobre la ocupación diaria, semanal y mensual de los espacios.
3. **Optimización de uso de espacios**: Uso de algoritmos que sugieran el mejor espacio disponible basado en el historial de uso y el tipo de vehículo.

---

Este README puede servir como referencia inicial para los nuevos colaboradores y como guía para comprender la arquitectura y el flujo de trabajo general de **ParkEase**.
