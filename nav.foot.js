// navbar-footer.js
function includeHTML(id, file) {
    fetch(file)
      .then(res => res.text())
      .then(html => {
        document.getElementById(id).innerHTML = html;
      })
      .catch(err => console.error(`Error cargando ${file}:`, err));
  }
  
  document.addEventListener("DOMContentLoaded", () => {
    includeHTML("navbar", "nav.html");
    includeHTML("footer", "footer.html");
  });
  