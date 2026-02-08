// Placeholder for AJAX requests (future integration with API)

function ajaxRequest(url, method = 'GET', data = null, callback) {

    const xhr = new XMLHttpRequest();

    xhr.open(method, url, true);

    xhr.onload = function() {

        if (xhr.status === 200) {

            callback(xhr.responseText);

        } else {

            console.error('AJAX Error:', xhr.status);

        }

    };

    if (method === 'POST') {

        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    }

    xhr.send(data);

}

