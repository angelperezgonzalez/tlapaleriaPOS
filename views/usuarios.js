document.addEventListener('DOMContentLoaded', cargarUsuarios);

function cargarUsuarios() {
  fetch('../controllers/usuarios.php')
    .then(res => res.json())
    .then(data => renderTabla(data))
    .catch(console.error);
}

function renderTabla(usuarios) {
  const tbody = document.querySelector('#tablaUsuarios tbody');
  tbody.innerHTML = '';

  usuarios.forEach(u => {
    tbody.innerHTML += `
      <tr>
        <td>${u.id}</td>
        <td><input type="text" value="${u.nombre}" id="nombre-${u.id}"></td>
        <td><input type="email" value="${u.email}" id="email-${u.id}"></td>
        <td>
          <select id="rol-${u.id}">
            <option value="ADMIN" ${u.rol === 'ADMIN' ? 'selected' : ''}>ADMIN</option>
            <option value="CAJERO" ${u.rol === 'CAJERO' ? 'selected' : ''}>CAJERO</option>
          </select>
        </td>
        <td>
          <button onclick="actualizarUsuario(${u.id})">💾 Guardar</button>
          <button onclick="eliminarUsuario(${u.id})">🗑️ Eliminar</button>
        </td>
      </tr>
    `;
  });
}

function crearUsuario() {
  const nombre = document.getElementById('nombre').value;
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  const rol = document.getElementById('rol').value;

  if (!nombre || !email || !password) {
    alert('⚠️ Completa todos los campos');
    return;
  }

  const formData = new FormData();
  formData.append('nombre', nombre);
  formData.append('email', email);
  formData.append('password', password);
  formData.append('rol', rol);

  fetch('../controllers/usuarios.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'ok') {
      alert('✅ Usuario creado');
      document.getElementById('nombre').value = '';
      document.getElementById('email').value = '';
      document.getElementById('password').value = '';
      cargarUsuarios();
    } else {
      alert('❌ Error: ' + (data.message || 'Desconocido'));
    }
  })
  .catch(console.error);
}

function actualizarUsuario(id) {
  const nombre = document.getElementById(`nombre-${id}`).value;
  const email = document.getElementById(`email-${id}`).value;
  const rol = document.getElementById(`rol-${id}`).value;

  const params = new URLSearchParams();
  params.append('id', id);
  params.append('nombre', nombre);
  params.append('email', email);
  params.append('rol', rol);

  fetch(`../controllers/usuarios.php`, {
    method: 'PUT',
    body: params
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'ok') {
      alert('✅ Usuario actualizado');
      cargarUsuarios();
    } else {
      alert('❌ Error: ' + (data.message || 'Desconocido'));
    }
  })
  .catch(console.error);
}

function eliminarUsuario(id) {
  if (!confirm('⚠️ ¿Seguro que deseas eliminar este usuario?')) return;

  const params = new URLSearchParams();
  params.append('id', id);

  fetch(`../controllers/usuarios.php`, {
    method: 'DELETE',
    body: params
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'ok') {
      alert('🗑️ Usuario eliminado');
      cargarUsuarios();
    } else {
      alert('❌ Error: ' + (data.message || 'Desconocido'));
    }
  })
  .catch(console.error);
}
