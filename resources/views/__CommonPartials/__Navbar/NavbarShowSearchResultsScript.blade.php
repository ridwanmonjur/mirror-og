
<script>
    function toggleDropdown() {
        document.querySelector("#myDropdown").classList.toggle("d-none")
    }

    var ENDPOINT = "{{ route('landing.view') }}";
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
        ENDPOINT = "{{ route('landing.view') }}";
        if (!search || String(search).trim() === "") {
            search = null;
            ENDPOINT += "?page=" + page;
            infinteLoadMore(null, ENDPOINT);
        } else {
            ENDPOINT = "{{ route('landing.view') }}";
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
