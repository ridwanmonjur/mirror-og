function goToUrl(event, element) {
    event.stopPropagation();
    event.preventDefault();
    const url = element.getAttribute('data-url');
    window.location.href = url;

}

async function onFollowSubmit(event) {
    event.preventDefault();
    event.stopPropagation();
    let form = event.currentTarget;
    let dataset = form.dataset;
    let followButtons = document.getElementsByClassName(
        'followButton' + dataset.joinEventUser
    );
    let followCounts = document.getElementsByClassName(
        'followCounts' + dataset.joinEventUser
    );


    let formData = new FormData(form);
    [...followButtons].forEach((button) => {
        button.style.setProperty('pointer-events', 'none');
    });

    try {
        let jsonObject = {}
        for (let [key, value] of formData.entries()) {
            jsonObject[key] = value;
        }

        let response = await fetch(form.action, {
            method: form.method,
            body: JSON.stringify(jsonObject),
            headers: {
                ...window.loadBearerHeader(),
                'Accept': 'application/json',
                "Content-Type": "application/json",
            }
        });

        let data = await response.json();
        [...followButtons].forEach((followButton) => {
            followButton.style.setProperty('pointer-events', 'none');

            if (data.isFollowing) {
                followButton.innerText = 'Following';
                followButton.style.backgroundColor = '#8CCD39';
                followButton.style.color = 'black';
            } else {
                followButton.innerText = 'Follow';
                followButton.style.backgroundColor = '#43A4D7';
                followButton.style.color = 'white';
            }

            followButton.style.setProperty('pointer-events', 'auto');
        });

        let count = Number(followCounts[0].dataset.count);
        if (data.isFollowing) {
            count++;
        } else {
            count--;
        }

        [...followCounts].forEach((followCount) => {
            followCount.dataset.count = count;
            if (count == 1) {
                followCount.innerHTML = '1 follower';
            } else if (count == 0) {
                followCount.innerHTML = `0 followers`;
            } else {
                followCount.innerHTML = `${followCount.dataset.count} followers`;
            }
        })
    } catch (error) {
        [...followButtons].forEach(function (followButton) {
            followButton.style.setProperty('pointer-events', 'auto');
        });

        console.error('Error:', error);
    }
}

(function applyRandomColorsAndShapes() {
    const circles = document.querySelectorAll('.random-color-circle');

    circles.forEach(circle => {
        const randomColor = getRandomColor();
        circle.style.borderColor = randomColor;
        circle.style.borderWidth = '2px';
        circle.style.borderStyle = 'solid';
        circle.style.borderRadius = '50%';
    });
})();

function getRandomColor() {
    const letters = '0123456789ABCDEF';
    let color = '#';
    for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}