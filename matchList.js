(function() {

    const quueryString = window.location.search;
    const params = new URLSearchParams(quueryString);
    const league = params.get('league');

    fetch(`eplFeed.php?league=${league}`).then(response => {
        if(!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`)
        }
        return response.json();
    })
    .then(data => {
        const container = document.getElementById('matchList');
        Object.entries(data).forEach(([key,value]) => {
            const fragment = document.createDocumentFragment();
            const div = document.createElement('div');
            div.textContent = key;
            div.setAttribute('id', value);
            div.setAttribute('class', 'match')

            div.addEventListener('click', (event) => matchId(event, league));

            fragment.appendChild(div);
            container.appendChild(fragment);
        })
    })
    .catch(error => {
        console.error('Error:', error);
    });
})();

function matchId(event, league) {
    const selectedMatch = event.currentTarget.getAttribute('id');
    const matchName = event.currentTarget.textContent;
    window.location.href = `match.html?id=${selectedMatch}&match=${matchName}&league=${league}`;
}