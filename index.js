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

// Cerrar ventana emergente de reservas
closeBtn.onclick = function() {
    reservationModal.style.display = 'none';
};

// Rellenar tabla de reservas
function populateReservationTable() {
    reservationBody.innerHTML = '';
    reservations.forEach(reservation => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${reservation.id}</td>
            <td>${reservation.type}</td>
            <td>${reservation.time}</td>
            <td>${reservation.availability}</td>
            <td><button onclick="cancelReservation('${reservation.id}')">Cancelar</button></td>
        `;
        reservationBody.appendChild(row);
    });
}

// Cancelar reserva
function cancelReservation(id) {
    const index = reservations.findIndex(res => res.id === id);
    if (index !== -1) {
        reservations.splice(index, 1);
        populateReservationTable();
    }
}

// Agregar vehículo
function addVehicle() {
    const plate = document.getElementById('vehiclePlate').value;
    const brand = document.getElementById('vehicleBrand').value;
    const model = document.getElementById('vehicleModel').value;

    if (plate && brand && model) {
        vehicles.push({plate, brand, model});
        document.getElementById('addVehicleForm').reset();
        updateReservedVehiclesList();
    }
}

// Actualizar lista de vehículos reservados
function updateReservedVehiclesList() {
    const userInfo = document.getElementById('userInfo');
    const reservedVehiclesList = document.getElementById('reservedVehiclesList');

    userInfo.textContent = `Bienvenido/a, usuario!`;
    reservedVehiclesList.innerHTML = '';

    vehicles.forEach(vehicle => {
        const li = document.createElement('li');
        li.textContent = `${vehicle.brand} ${vehicle.model} (${vehicle.plate})`;
        reservedVehiclesList.appendChild(li);
    });
}

// Simular inicio de sesión
updateReservedVehiclesList();

// Escuchar eventos de cerrar sesión
document.querySelector('.usuario li:last-child').addEventListener('click', () => {
    localStorage.clear();
    window.location.reload();
});

// Verificar si hay datos guardados en localStorage
window.onload = function() {
    if (localStorage.getItem('vehicles')) {
        vehicles = JSON.parse(localStorage.getItem('vehicles'));
        updateReservedVehiclesList();
    }
};

// Guardar datos en localStorage cuando se cierra la página
window.onbeforeunload = function() {
    localStorage.setItem('vehicles', JSON.stringify(vehicles));
};
