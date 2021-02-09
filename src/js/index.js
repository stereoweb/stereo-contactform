import Form from './forms'

window.initStereoForm = () => {
    const forms = document.querySelectorAll('.js-stereo-cf');

    forms.forEach(e => {
        new Form(e)
    })
}

window.initStereoForm();
