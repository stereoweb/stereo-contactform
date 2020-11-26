import Form from './forms'

const forms = document.querySelectorAll('.js-stereo-cf');

forms.forEach(e => {
    new Form(e)
})
