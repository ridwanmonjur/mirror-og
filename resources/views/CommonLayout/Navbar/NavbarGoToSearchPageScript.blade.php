
<script>
    function toggleDropdown() {
        document.querySelector("#myDropdown").classList.toggle("d-none")
    }

    function goToSearchPage() {
        let ENDPOINT = "{{ route('public.search.view') }}";
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

     document.getElementById('search-bar').addEventListener(
        "keydown",
        debounce((e) => {
            goToSearchPage();
        }, 1000)
    );

    document.getElementById('search-bar-mobile').addEventListener(
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
      if (!event.target.matches('.oceans-gaming-default-button')) {
        var dropdowns = document.getElementsByClassName("team-dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
          var openDropdown = dropdowns[i];
          if (openDropdown.style.display === "block") {
            openDropdown.style.display = "none";
          }
        }
      }
    }

</script>

