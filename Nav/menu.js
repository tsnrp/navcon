/* When the user clicks on the SYSTEM button,
 * cycle which of the menu sectors are viewed
 * there probably is a cleverer way to do this
 * but it (probably) wont be as simple I advise
 * if this needs to go to toggling more elements
 * that a cleaner soultion is looked into*/
function toggleSystemView() {
    if (document.getElementById("menuSectorsPart1").classList.contains("show")) {
      if (document.getElementById("menuSectorsPart2").classList.contains("show")) {
        document.getElementById("menuSectorsPart2").classList.toggle("show");
        document.getElementById("systemButton").innerHTML = "CANCEL SYSTEMS";
      } else {
        document.getElementById("menuSectorsPart1").classList.toggle("show");
        document.getElementById("systemButton").innerHTML = "SYSTEMS";
      }
    } else {
      document.getElementById("menuSectorsPart1").classList.toggle("show");
      document.getElementById("menuSectorsPart2").classList.toggle("show");
      document.getElementById("systemButton").innerHTML = "MORE SYSTEMS";
    }
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {
    document.getElementById("systemButton").innerHTML = "SYSTEMS";

    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
