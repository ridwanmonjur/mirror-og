function toggleDropdown() {
    document.querySelector("#myDropdown").classList.toggle("d-none")
}
const landingEndpointInput = document.getElementById('landingEndpointInput').value;

var ENDPOINT = landingEndpointInput;
var page = 1;
var search = null;

document.getElementById('search-bar').addEventListener(
    "keydown",
    debounce((e) => {
        searchPart(e);
    }, 1000)
);

  document.getElementById('search-bar-mobile').addEventListener(
    "keydown",
    debounce((e) => {
        searchPart(e);
    }, 1000)
);

function searchPart(e) {
    page = 1;
    let noMoreDataElement = document.querySelector('.no-more-data');
    noMoreDataElement.classList.add('d-none');
    document.querySelector('.scrolling-pagination').innerHTML = '';
    search = e.target.value;
    ENDPOINT = landingEndpointInput;
    if (!search || String(search).trim() == "") {
        search = null;
        ENDPOINT += "?page=" + page;
        infinteLoadMore(null, ENDPOINT);
    } else {
        ENDPOINT = landingEndpointInput;
        ENDPOINT += "?search=" + e.target.value + "&page=" + page;
        window.location.href = ENDPOINT;
    }
}

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
  console.log({circles})

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