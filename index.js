// Elementos del DOM
const menus = document.getElementById('menus');
const menu = document.getElementById('menu');
const miniMenus = document.querySelectorAll('.mini-menu');
const reservationModal = document.getElementById('reservationModal');
const closeBtn = document.querySelector('.close');
const reservationBody = document.getElementById('reservationBody');

// Datos de reservas y vehículos
let reservations = [
    {id: 'R001', type: 'Parqueadero', time: '08:00 AM', availability: 'Disponible'},
    {id: 'R002', type: 'Salón de Eventos', time: '12:00 PM', availability: 'Ocupado'},
    {id: 'R003', type: 'Sala de Reuniones', time: '02:00 PM', availability: 'Disponible'},
];

let vehicles = [];
let reservedVehicles = [];

// Función para abrir el menú hamburguesa
menus.addEventListener('click', () => {
    menus.classList.toggle('active');
    menu.classList.toggle('active');
});

// Manejo de los elementos del menú principal
document.querySelectorAll('#menu li').forEach((item, index) => {
    item.addEventListener('click', () => {
        miniMenus.forEach(miniMenu => miniMenu.style.display = 'none');

        if (index >= 1 && index <= 5) {
            miniMenus[index - 1].style.display = 'block';
        }
    });
});

// Ocultar mini-menus al hacer clic fuera
document.addEventListener('click', (e) => {
    if (!e.target.closest('.menu') && !e.target.closest('.mini-menus')) {
        miniMenus.forEach(miniMenu => miniMenu.style.display = 'none');
    }
});

// Abrir ventana emergente de reservas
function openReservationModal() {
    reservationModal.style.display = 'block';
    populateReservationTable();
}

// Cerrar ventana emergente al hacer clic en el botón cerrar
closeBtn.onclick = function() {
    reservationModal.style.display = 'none';
};

// Cerrar ventana emergente al hacer clic fuera
window.onclick = function(event) {
    if (event.target == reservationModal) {
        reservationModal.style.display = 'none';
    }
};

// Poblar la tabla de reservas
function populateReservationTable() {
    reservationBody.innerHTML = '';
    reservations.forEach(reservation => {
        let row = document.createElement('tr');
        row.innerHTML = `
            <td>${reservation.id}</td>
            <td>${reservation.type}</td>
            <td>${reservation.time}</td>
            <td>${reservation.availability}</td>
            <td><button onclick="reserve(${reservation.id})">Reservar</button></td>
        `;
        reservationBody.appendChild(row);
    });
}

// Función para realizar una reserva
function reserve(id) {
    const reservation = reservations.find(r => r.id === id);
    if (reservation && reservation.availability === 'Disponible' && vehicles.length > 0) {
        const vehicleSelect = prompt("Seleccione un vehículo:\n" + vehicles.map((v, i) => `${i+1}. ${v}`).join("\n"));
        
        if (vehicleSelect && !isNaN(vehicleSelect) && vehicleSelect > 0 && vehicleSelect <= vehicles.length) {
            reservation.availability = 'Reservado';
            reservedVehicles.push({id: id, vehicle: vehicles[vehicleSelect - 1]});
            populateReservationTable();
            updateReservedVehiclesList();
            alert("Reserva realizada con éxito.");
        } else {
            alert("Selección inválida. Por favor, inténtelo nuevamente.");
        }
    } else {
        alert("No hay vehículos disponibles o la reserva ya está ocupada.");
    }
}

// Función para agregar un vehículo
function addVehicle() {
    const plate = document.getElementById('vehiclePlate').value;
    const brand = document.getElementById('vehicleBrand').value;
    const model = document.getElementById('vehicleModel').value;

    if (plate && brand && model) {
        vehicles.push(`${brand} ${model} (${plate})`);
        alert("Vehículo agregado con éxito.");
        document.getElementById('addVehicleForm').reset();
    } else {
        alert("Por favor, complete todos los campos.");
    }
}

// Actualizar la lista de vehículos reservados en el menú de usuario
function updateReservedVehiclesList() {
    const reservedVehiclesList = document.getElementById('reservedVehiclesList');
    reservedVehiclesList.innerHTML = '';
    reservedVehicles.forEach((rv, index) => {
        const li = document.createElement('li');
        li.textContent = `${index + 1}. Reserva ${rv.id}: ${rv.vehicle}`;
        reservedVehiclesList.appendChild(li);
    });
}

// Actualizar información del usuario
function updateUserInformation() {
    const userInfo = document.getElementById('userInfo');
    userInfo.innerHTML = `
        <h4>Usuario: Juan Pérez</h4>
        <p>Vehículos registrados: ${vehicles.length}</p>
        <p>Reservas activas: ${reservedVehicles.length}</p>
    `;
}

// Inicialización
updateUserInformation();

// Evento para actualizar la información del usuario cada vez que se abre el menú
document.querySelector('.mini-menu.usuario').addEventListener('click', updateUserInformation);