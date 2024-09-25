// Función para mostrar el menú principal al hacer clic en el icono del menú hamburguesa
document.getElementById('menus').addEventListener('click', function() {
    var menu = document.getElementById('menu');
    menu.classList.toggle('active');
});

// Funciones para manejar los mini-menús
function openMenu(menuId) {
    var menu = document.getElementById(menuId);
    menu.style.display = 'block';
}

function closeMenu(menuId) {
    var menu = document.getElementById(menuId);
    menu.style.display = 'none';
}

// Event listeners para los mini-menús
document.querySelectorAll('.mini-menu-item').forEach(item => {
    item.addEventListener('click', function() {
        var menuId = this.getAttribute('data-menu-id');
        if (this.classList.contains('open')) {
            closeMenu(menuId);
            this.classList.remove('open');
        } else {
            openMenu(menuId);
            this.classList.add('open');
        }
    });
});

// Función para mostrar el modal de reservas
function openReservationModal() {
    var modal = document.getElementById('reservationModal');
    var modalContent = document.querySelector('.modal-content');
    var reservationBody = document.getElementById('reservationBody');

    // Llenar el contenido de la tabla con datos de ejemplo
    var reservations = [
        {id: 1, tipo: "Alquiler", hora: "10:00 AM", disponibilidad: "Disponible", opciones: "<button>Cancelar</button>"}
    ];

    reservations.forEach(reservation => {
        var row = document.createElement('tr');
        row.innerHTML = `
            <td>${reservation.id}</td>
            <td>${reservation.tipo}</td>
            <td>${reservation.hora}</td>
            <td>${reservation.disponibilidad}</td>
            <td>${reservation.opciones}</td>
        `;
        reservationBody.appendChild(row);
    });

    modal.style.display = 'block';
    modalContent.style.display = 'block';

    // Event listener para cerrar el modal
    var closeBtn = document.querySelector('.close');
    closeBtn.onclick = function() {
        modal.style.display = 'none';
        modalContent.style.display = 'none';
    }

    // Event listener para cerrar el modal al hacer clic en cualquier lugar fuera del modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
            modalContent.style.display = 'none';
        }
    }
}

// Función para mostrar el modal de agregar vehículo
function openAddVehicleModal() {
    var modal = document.getElementById('addVehicleModal');
    var modalContent = document.querySelector('.modal-content');

    modal.style.display = 'block';
    modalContent.style.display = 'block';

    // Event listener para cerrar el modal
    var closeBtn = document.querySelector('.close');
    closeBtn.onclick = function() {
        modal.style.display = 'none';
        modalContent.style.display = 'none';
    }

    // Event listener para cerrar el modal al hacer clic en cualquier lugar fuera del modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
            modalContent.style.display = 'none';
        }
    }
}

// Función para agregar un nuevo vehículo
function addVehicle() {
    var vehiclePlate = document.getElementById('vehiclePlate').value;
    var vehicleBrand = document.getElementById('vehicleBrand').value;
    var vehicleModel = document.getElementById('vehicleModel').value;

    console.log(`Nuevo vehículo agregado: ${vehiclePlate}, ${vehicleBrand}, ${vehicleModel}`);

    // Aquí iría el código para agregar realmente el vehículo a la base de datos
    // Por ahora, solo se muestra un mensaje en la consola

    // Limpiar los campos del formulario
    document.getElementById('vehiclePlate').value = '';
    document.getElementById('vehicleBrand').value = '';
    document.getElementById('vehicleModel').value = '';

    // Cerrar el modal
    closeAddVehicleModal();
}

// Función para cerrar el modal de agregar vehículo
function closeAddVehicleModal() {
    var modal = document.getElementById('addVehicleModal');
    var modalContent = document.querySelector('.modal-content');

    modal.style.display = 'none';
    modalContent.style.display = 'none';
}

// Función para actualizar la información del usuario
function updateUserInformation() {
    alert("Actualizando información del usuario...");
}

// Función para mostrar la alerta de cierre de sesión
function showLogoutAlert() {
    var alerta = document.querySelector('.alerta-cierre-sesion');
    alerta.classList.add('show');
    
    setTimeout(function() {
        alerta.classList.remove('show');
    }, 5000);
}

// Event listener para el botón de cierre de sesión
document.querySelector('.cerrar-sesion').addEventListener('click', function() {
    showLogoutAlert();
});

// Funciones para manejar los mini-menús
function openMenu(menuId) {
    var menu = document.getElementById(menuId);
    menu.style.display = 'block';
}

function closeMenu(menuId) {
    var menu = document.getElementById(menuId);
    menu.style.display = 'none';
}

// Event listeners para los mini-menús
document.querySelectorAll('.mini-menu-item').forEach(item => {
    item.addEventListener('click', function() {
        var menuId = this.getAttribute('data-menu-id');
        if (this.classList.contains('open')) {
            closeMenu(menuId);
            this.classList.remove('open');
        } else {
            openMenu(menuId);
            this.classList.add('open');
        }
    });
});

// Función para mostrar el modal de reservas
function openReservationModal() {
    var modal = document.getElementById('reservationModal');
    var modalContent = document.querySelector('.modal-content');
    var reservationBody = document.getElementById('reservationBody');

    // Llenar el contenido de la tabla con datos de ejemplo
    var reservations = [
        {id: 1, tipo: "Alquiler", hora: "10:00 AM", disponibilidad: "Disponible", opciones: "<button>Cancelar</button>"}
    ];

    reservations.forEach(reservation => {
        var row = document.createElement('tr');
        row.innerHTML = `
            <td>${reservation.id}</td>
            <td>${reservation.tipo}</td>
            <td>${reservation.hora}</td>
            <td>${reservation.disponibilidad}</td>
            <td>${reservation.opciones}</td>
        `;
        reservationBody.appendChild(row);
    });

    modal.style.display = 'block';
    modalContent.style.display = 'block';

    // Event listener para cerrar el modal
    var closeBtn = document.querySelector('.close');
    closeBtn.onclick = function() {
        modal.style.display = 'none';
        modalContent.style.display = 'none';
    }

    // Event listener para cerrar el modal al hacer clic en cualquier lugar fuera del modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
            modalContent.style.display = 'none';
        }
    }
}

// Función para mostrar el modal de agregar vehículo
function openAddVehicleModal() {
    var modal = document.getElementById('addVehicleModal');
    var modalContent = document.querySelector('.modal-content');

    modal.style.display = 'block';
    modalContent.style.display = 'block';

    // Event listener para cerrar el modal
    var closeBtn = document.querySelector('.close');
    closeBtn.onclick = function() {
        modal.style.display = 'none';
        modalContent.style.display = 'none';
    }

    // Event listener para cerrar el modal al hacer clic en cualquier lugar fuera del modal
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
            modalContent.style.display = 'none';
        }
    }
}

// Función para agregar un nuevo vehículo
function addVehicle() {
    var vehiclePlate = document.getElementById('vehiclePlate').value;
    var vehicleBrand = document.getElementById('vehicleBrand').value;
    var vehicleModel = document.getElementById('vehicleModel').value;

    console.log(`Nuevo vehículo agregado: ${vehiclePlate}, ${vehicleBrand}, ${vehicleModel}`);

    // Aquí iría el código para agregar realmente el vehículo a la base de datos
    // Por ahora, solo se muestra un mensaje en la consola

    // Limpiar los campos del formulario
    document.getElementById('vehiclePlate').value = '';
    document.getElementById('vehicleBrand').value = '';
    document.getElementById('vehicleModel').value = '';

    // Cerrar el modal
    closeAddVehicleModal();
}

// Función para cerrar el modal de agregar vehículo
function closeAddVehicleModal() {
    var modal = document.getElementById('addVehicleModal');
    var modalContent = document.querySelector('.modal-content');

    modal.style.display = 'none';
    modalContent.style.display = 'none';
}

// Función para actualizar la información del usuario
function updateUserInformation() {
    alert("Actualizando información del usuario...");
}

// Función para mostrar la alerta de cierre de sesión
function showLogoutAlert() {
    var alerta = document.querySelector('.alerta-cierre-sesion');
    alerta.classList.add('show');
    
    setTimeout(function() {
        alerta.classList.remove('show');
    }, 5000);
}

// Event listener para el botón de cierre de sesión
document.querySelector('.cerrar-sesion').addEventListener('click', function() {
    showLogoutAlert();
});