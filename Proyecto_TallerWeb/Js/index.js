document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById("modal-publicidad");
  const cerrarBtn = document.getElementById("cerrar-modal");
  const horasEl = document.getElementById("horas");
  const minutosEl = document.getElementById("minutos");
  const segundosEl = document.getElementById("segundos");

  modal.classList.add("activo");

  cerrarBtn.onclick = () => {
    modal.classList.remove("activo");
  };

  const fechaLimite = new Date();
  fechaLimite.setDate(fechaLimite.getDate() + 3);

  function actualizarContador() {
    const ahora = new Date();
    const diferencia = fechaLimite - ahora;

    if (diferencia <= 0) {
      horasEl.textContent = "00";
      minutosEl.textContent = "00";
      segundosEl.textContent = "00";
      clearInterval(intervalo);
      return;
    }

    const horas = Math.floor((diferencia / (1000 * 60 * 60)) % 24);
    const minutos = Math.floor((diferencia / (1000 * 60)) % 60);
    const segundos = Math.floor((diferencia / 1000) % 60);

    horasEl.textContent = String(horas).padStart(2, '0');
    minutosEl.textContent = String(minutos).padStart(2, '0');
    segundosEl.textContent = String(segundos).padStart(2, '0');
  }

  const intervalo = setInterval(actualizarContador, 1000);
  actualizarContador(); 
});
