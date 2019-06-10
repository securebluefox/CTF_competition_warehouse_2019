function makeFormResult(req, confirmPage) {
    return function () {
        if (req.readyState === XMLHttpRequest.DONE) {
            if (req.status === 200) {
                let success = JSON.parse(req.responseText)['success'];
                if (success) {
                    console.log('form submit: success');
                    window.location = confirmPage;
                    return;
                }
            }
            let reg_button = document.querySelector('.js-button-submit');
            console.log('form submit: failed');
            reg_button.innerHTML = 'Error';
            reg_button.classList.add('error');
        }
    }
}

function ajaxSubmit(event, confirmPage) {
    console.log('form submit');
    event.preventDefault();
    let req = new XMLHttpRequest();
    let form = document.querySelector('form.js-ajaxform');
    req.open('post', form.action);
    req.onreadystatechange = makeFormResult(req, confirmPage);
    req.send(new FormData(form));
    let reg_button = document.querySelector('.js-button-submit');
    reg_button.innerHTML = 'Submitting...';
}