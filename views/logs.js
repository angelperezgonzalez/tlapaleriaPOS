document.addEventListener('DOMContentLoaded', cargarLogs);

function cargarLogs() {
  const usuarioId = document.getElementById('filtroUsuario').value;
  const desde = document.getElementById('filtroDesde').value;
  const hasta = document.getElementById('filtroHasta').value;

  let url = '../controllers/logs.php';
  const params = new URLSearchParams();

  if (usuarioId) params.append('usuario_id', usuarioId);
  if (desde) params.append('desde', desde);
  if (hasta) params.append('hasta', hasta);

  if ([...params].length) url += '?' + params.toString();

  fetch(url)
    .then(res => res.json())
    .then(data => renderLogs(data))
    .catch(err => console.error(err));
}

function renderLogs(logs) {
  const tbody = document.getElementById('tablaLogs');
  tbody.innerHTML = '';

  if (!logs.length) {
    tbody.innerHTML = '<tr><td colspan="5">No hay registros</td></tr>';
    return;
  }

  logs.forEach(log => {
    tbody.innerHTML += `
      <tr>
        <td>${log.id}</td>
        <td>${log.usuario_id ?? 'N/A'}</td>
        <td>${log.usuario_nombre}</td>
        <td>${log.accion}</td>
        <td>${log.descripcion}</td>
        <td>${log.fecha}</td>
      </tr>
    `;
  });
}
