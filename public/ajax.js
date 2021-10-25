window.onload = function () {
    console.log(123);
    let form = document.getElementsByTagName('form')[0];
    console.log(form);

    form.addEventListener('submit', sendData);
}

async function sendData(e) {
    e.preventDefault();

    let response = await fetch(this.getAttribute('action'), {
        body: new FormData(this),
        method: this.getAttribute('method'),
    });

    let result = await response.json();

    if (result.url) {
        document.location.href = result.url;
        return;
    }

    alert(`${result.status} - ${result.body}`);
}