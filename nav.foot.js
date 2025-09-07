// navbar-footer.js IMPORTA ESTO EL NAV Y EL FOOTER EN HTML COMO UN IBLOCK COMO LO VIMOS EN CLASES!!!1
function includeHTML(id, file) {
    fetch(file)
      .then(res => res.text())
      .then(html => {
        document.getElementById(id).innerHTML = html;
      })
  }
  
  document.addEventListener("DOMContentLoaded", () => {
    includeHTML("navbar", "nav.html");
    includeHTML("footer", "footer.html");
  });
  