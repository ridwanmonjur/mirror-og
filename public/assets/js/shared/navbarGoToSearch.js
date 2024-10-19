function toggleDropdown() {
    document.querySelector("#myDropdown").classList.toggle("d-none")
}

const searchEndpointInput = document.getElementById('searchEndpointInput')?.value;

function goToSearchPage() {
    let ENDPOINT = searchEndpointInput;
    let page = 1;
    let search = null;
    let searchBar = document.querySelector('input#search-bar');
    if (searchBar.style.display != 'none') {
        search = searchBar.value;
    } else {
        searchBar = document.querySelector('input#search-bar-mobile');
        search = searchBar.value;
    }
    if (!search || String(search).trim() == "") {
        search = null;
        ENDPOINT += "?page=" + page;
    } else {
        ENDPOINT += "?search=" + search + "&page=" + page;
    }
    window.location = ENDPOINT;
}

 document.getElementById('search-bar')?.addEventListener(
    "keydown",
    debounce((e) => {
        goToSearchPage();
    }, 1000)
);

document.getElementById('search-bar-mobile')?.addEventListener(
    "keydown",
    debounce((e) => {
        goToSearchPage();
    }, 1000)
);

function toggleTeamList() {
  var teamList = document.getElementById("teamList");
  if (teamList.style.display === "block") {
    teamList.style.display = "none";
  } else {
    teamList.style.display = "block";
  }
}

window.onclick = function(event) {
  if (!event.target?.matches('.oceans-gaming-default-button')) {
    var dropdowns = document.getElementsByClassName("team-dropdown-content");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.style.display === "block") {
        openDropdown.style.display = "none";
      }
    }
  }
}

function applyRandomColorsAndShapes() {
  const circles = document.querySelectorAll('.random-bg-circle');
  circles.forEach(circle => {
    if (!circle.style.backgroundColor) {
      const randomColor = getRandomColorBg();
      circle.style.backgroundColor = randomColor;
    }
  });
}

function getRandomColorBg() {
  const letters = '0123456789ABCDEF';
  let color = '#';
  for (let i = 0; i < 6; i++) {
      color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}

applyRandomColorsAndShapes();