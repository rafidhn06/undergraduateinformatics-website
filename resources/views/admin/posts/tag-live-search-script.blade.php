<script>
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    const viewGenerated = true;
    var selected = [];

    let results = document.getElementsByClassName("checkbox-option");
    for (let index = 0; index < results.length; index++) {
        if (results[index].checked) {
            selected.push(results[index].value);
        }
    }

    function saveSelection(target) {
        if (target.checked) {
            selected.push(target.value);
        } else {
            const index = selected.indexOf(target.value);
            if (index > -1) {
                selected.splice(index, 1);
            }
        }
    }

    async function performSearch() {
        const query = searchInput.value;
        var urlString = ''
        selected.forEach(element => {
            urlString += '&selected[]=' + element
        });

        if (query.length >= 0) {
            await fetch('{{ route('admin.posts.create') }}?query=' + query + '&viewGenerated=' + viewGenerated +
                    urlString)
                .then(response => response.text())
                .then(data => {
                    searchResults.innerHTML = data;
                });
        }

        let results = document.getElementsByClassName("checkbox-option");
        for (let index = 0; index < results.length; index++) {
            if (selected.includes(results[index].value)) {
                results[index].checked = true;
            }
        }
    }

    performSearch();
    // setInterval(performSearch, 500); // Refresh setiap 0.5 detik
</script>
