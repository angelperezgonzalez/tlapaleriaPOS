let productosDisponibles = [];
let carrito = [];

// Al cargar
document.addEventListener('DOMContentLoaded', cargarProductos);

function cargarProductos() {
  fetch('../controllers/productos.php')
    .then(res => res.json())
    .then(data => {
        console.log('Data de productos.php:', data);
      productosDisponibles = data.map(p=>({
        ...p,
        id: parseInt(p.id),
        precio: parseFloat(p.precio),
        stock: parseInt(p.stock)
      }));
      renderTablaProductos();
    })
    .catch(console.error);
}

function renderTablaProductos() {
  const tbody = document.querySelector('#tablaProductos tbody');
  tbody.innerHTML = '';
  console.log('Renderizando:', productosDisponibles);
  productosDisponibles.forEach(p => {
    tbody.innerHTML += `
      <tr>
        <td>${p.id}</td>
        <td>${p.nombre}</td>
        <td>$${parseFloat(p.precio).toFixed(2)}</td>
        <td>${p.stock}</td>
        <td>
          <button onclick="agregarAlCarrito(${p.id})">🛒 Agregar</button>
        </td>
      </tr>
    `;
  });
}

function agregarProducto() {
  const nombre = document.getElementById('nombre').value;
  const precio = document.getElementById('precio').value;
  const stock = document.getElementById('stock').value;

  if (!nombre || !precio || !stock) {
    alert('⚠️ Completa todos los campos');
    return;
  }

  const formData = new FormData();
  formData.append('nombre', nombre);
  formData.append('precio', precio);
  formData.append('stock', stock);

  fetch('../controllers/productos.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'ok') {
      alert('✅ Producto agregado');
      document.getElementById('nombre').value = '';
      document.getElementById('precio').value = '';
      document.getElementById('stock').value = '';
      cargarProductos();
    } else {
      alert('❌ Error: ' + (data.message || 'Desconocido'));
    }
  })
  .catch(console.error);
}

function agregarAlCarrito(id) {
  const producto = productosDisponibles.find(p => p.id === id);
  console.log('Intentando agregar:', producto);
  if (!producto || producto.stock <= 0) {
    alert('❌ Stock insuficiente');
    return;
  }

  const existente = carrito.find(item => item.id === id);
  if (existente) {
    if (existente.cantidad + 1 > producto.stock) {
      alert('⚠️ No hay suficiente stock');
      return;
    }
    existente.cantidad += 1;
  } else {
    carrito.push({
      id: producto.id,
      nombre: producto.nombre,
      precio: producto.precio,
      cantidad: 1
    });
  }
  renderCarrito();
}

function renderCarrito() {
  const tbody = document.querySelector('#tablaCarrito tbody');
  tbody.innerHTML = '';

  let total = 0;
  carrito.forEach((item, index) => {
    const subtotal = item.precio * item.cantidad;
    total += subtotal;

    tbody.innerHTML += `
      <tr>
        <td>${item.nombre}</td>
        <td>$${parseFloat(item.precio).toFixed(2)}</td>
        <td>
          <input type="number" value="${item.cantidad}" min="1" max="99" style="width:50px;" onchange="cambiarCantidad(${index}, this.value)">
        </td>
        <td>$${subtotal.toFixed(2)}</td>
        <td><button onclick="quitarDelCarrito(${index})">❌ Quitar</button></td>
      </tr>
    `;
  });

  document.getElementById('totalVenta').innerText = `Total: $${total.toFixed(2)}`;
}

function cambiarCantidad(index, nuevaCantidad) {
  nuevaCantidad = parseInt(nuevaCantidad);
  if (nuevaCantidad < 1) nuevaCantidad = 1;

  const producto = productosDisponibles.find(p => p.id === carrito[index].id);
  if (nuevaCantidad > producto.stock) {
    alert('⚠️ No hay suficiente stock');
    return;
  }

  carrito[index].cantidad = nuevaCantidad;
  renderCarrito();
}

function quitarDelCarrito(index) {
  carrito.splice(index, 1);
  renderCarrito();
}

function registrarVenta() {
  if (carrito.length === 0) {
    alert('⚠️ Agrega productos al carrito');
    return;
  }

  const detalles = carrito.map(item => ({
    producto_id: item.id,
    cantidad: item.cantidad,
    precio_unitario: item.precio
  }));

  fetch('../controllers/ventas.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ detalles })
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'ok') {
      alert(`✅ Venta registrada con ID #${data.venta_id}`);
      carrito = [];
      renderCarrito();
      cargarProductos();
    } else {
      alert('❌ Error al registrar: ' + (data.message || 'Desconocido'));
    }
  })
  .catch(console.error);
}


function cargarVentas() {
    fetch('../controllers/consultas.php?action=listar')
      .then(res => res.json())
      .then(data => renderTablaVentas(data))
      .catch(console.error);
  }
  
  function renderTablaVentas(ventas) {
    const tbody = document.querySelector('#tablaVentas tbody');
    tbody.innerHTML = '';
    ventas.forEach(v => {
      tbody.innerHTML += `
        <tr>
          <td>${v.id}</td>
          <td>${v.fecha}</td>
          <td>$${parseFloat(v.total).toFixed(2)}</td>
          <td>
            <button onclick="verDetalleVenta(${v.id})">📄 Ver detalle</button>
            <button onclick="generarTicket(${v.id})">🧾 Ticket</button>
            <button onclick="generarTicketPDF(${v.id})">🧾 PDF</button>
          </td>
        </tr>
      `;
    });
  }
  
  function verDetalleVenta(id) {
    fetch(`../controllers/consultas.php?action=detalle&id=${id}`)
      .then(res => res.json())
      .then(data => renderDetalleVenta(data))
      .catch(console.error);
  }
  
  function renderDetalleVenta(detalles) {
    const tbody = document.querySelector('#tablaDetalleVenta tbody');
    tbody.innerHTML = '';
    detalles.forEach(d => {
      const subtotal = d.cantidad * d.precio_unitario;
      tbody.innerHTML += `
        <tr>
          <td>${d.nombre}</td>
          <td>${d.cantidad}</td>
          <td>$${parseFloat(d.precio_unitario).toFixed(2)}</td>
          <td>$${subtotal.toFixed(2)}</td>
        </tr>
      `;
    });
  }
  
  function generarTicket(id) {
    fetch(`../controllers/consultas.php?action=ticket&id=${id}`)
      .then(res => res.json())
      .then(data => renderTicket(data))
      .catch(console.error);
  }
  
  function renderTicket(data) {
    const { venta, detalles } = data;
    const contenedor = document.getElementById('contenedorTicket');
    contenedor.innerHTML = `
      <div id="ticket">
        <h3>🧾 Tlapalería Mi Negocio</h3>
        <p>Venta ID: ${venta.id}</p>
        <p>Fecha: ${venta.fecha}</p>
        <hr>
        <table border="3" cellspacing="3" cellpadding="3" width="100%">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cant.</th>
              <th>PU</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            ${detalles.map(item => `
              <tr>
                <td>${item.nombre}</td>
                <td>${item.cantidad}</td>
                <td>$${parseFloat(item.precio_unitario).toFixed(2)}</td>
                <td>$${(item.cantidad * item.precio_unitario).toFixed(2)}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>
        <hr>
        <h3>Total: $${parseFloat(venta.total).toFixed(2)}</h3>
        <button onclick="window.print()">🖨️ Imprimir</button>
      </div>
    `;
  }

  function generarTicketPDF(id) {
    fetch(`../controllers/consultas.php?action=ticket&id=${id}`)
      .then(res => res.json())
      .then(data => renderTicketPDF(data))
      .catch(console.error);
  }

  function renderTicketPDF(data) {
    const { venta, detalles } = data;
    const contenedor = document.getElementById('contenedorTicketPDF');
    contenedor.innerHTML = `
      <div id="ticketParaPDF">
        <h2>🧾 Tlapalería Mi Negocio</h2>
        <p>Venta ID: ${venta.id}</p>
        <p>Fecha: ${venta.fecha}</p>
        <hr>
        <table border="1" cellspacing="0" cellpadding="5" width="100%">
          <thead>
            <tr>
              <th>Producto</th>
              <th>Cant.</th>
              <th>PU</th>
              <th>Subtotal</th>
            </tr>
          </thead>
          <tbody>
            ${detalles.map(item => `
              <tr>
                <td>${item.nombre}</td>
                <td>${item.cantidad}</td>
                <td>$${parseFloat(item.precio_unitario).toFixed(2)}</td>
                <td>$${(item.cantidad * item.precio_unitario).toFixed(2)}</td>
              </tr>
            `).join('')}
          </tbody>
        </table>
        <hr>
        <h3>Total: $${parseFloat(venta.total).toFixed(2)}</h3>
        <button onclick="enviarTicketPorCorreo(${venta.id})">📧 Enviar por correo</button>
      </div>
    `;
  
    // Mostrar botón de PDF
    document.getElementById('btnDescargarPDF').style.display = 'inline';
    document.getElementById('btnDescargarPDF').onclick = () => descargarPDF(venta.id);
  }

  function descargarPDF(ventaId) {
    const elemento = document.getElementById('ticketParaPDF');
    const opciones = {
      margin:       5,
      filename:     `ticket_venta_${ventaId}.pdf`,
      image:        { type: 'jpeg', quality: 0.98 },
      html2canvas:  { scale: 2 },
      jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
  
    html2pdf().set(opciones).from(elemento).save();
  }

  function enviarTicketPorCorreo(ventaId) {
    const ticket = document.getElementById('ticketParaPDF');
    const opciones = {
      margin: 5,
      image: { type: 'jpeg', quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
  
    html2pdf().set(opciones).from(ticket).outputPdf('blob').then(blob => {
      const formData = new FormData();
      formData.append('archivo', blob, `ticket_venta_${ventaId}.pdf`);
      formData.append('venta_id', ventaId);
      formData.append('correo', prompt("Introduce el correo del cliente:"));
  
      fetch('../controllers/enviar_ticket.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'ok') {
          alert('📧 Ticket enviado correctamente');
        } else {
          alert('❌ Error al enviar: ' + (data.message || 'desconocido'));
        }
      })
      .catch(console.error);
    });
  }