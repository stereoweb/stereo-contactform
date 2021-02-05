export default class Form {
    constructor(element) {
        this.el = element
        this.init();
    }

    init() {
        this.el.addEventListener('submit', e => this.submit(e));
    }

    submit(e) {
        e.preventDefault()

        if (!this.el.checkValidity()) {
            alert("Veuillez compléter tous les champs requis !");
            return false;
        }

        if (window.recaptcha_v3) {
            grecaptcha.ready(() => {
                grecaptcha.execute(window.recaptcha_v3, { action: 'submit' }).then(token => {
                    this.send(token);
                });
            });
        } else {
            this.send();
        }
    }

    send(token) {
        let div = document.createElement("div");
        div.classList.add("js-extra-form-data");

        let additions = {
            action: "st_post_contact",
            "Page actuelle": location.href,
            "Page précédente": document.referrer,
            _subject: this.el.dataset.subject || "Formulaire de contact",
            _title_field:
                this.el.dataset.title ||
                this.el.querySelectorAll("input:first").getAttribute("name"),
            _category: this.el.dataset.category || "Contact",
            _nobot: "1",
        }

        if (token) additions['_token'] = token;

        for (let name in additions) {
            let input = document.createElement("input");
            input.setAttribute("type", "hidden");
            input.setAttribute("name", name);
            input.setAttribute("value", additions[name]);
            div.appendChild(input);
        }

        let callback = this.el.dataset.callback;
        if (callback && window[callback]) {
            try {
                window[callback](this);
            } catch (error) {
                // skip
            }
        }

        this.el
            .querySelectorAll(".js-extra-form-data")
            .forEach((e) => e.parentNode.removeChild(e));

        this.el.appendChild(div);

        if (this.formHasFiles()) {
            if (document.getElementById("stFrmToPost").length>0) document.getElementById("stFrmToPost").remove()
            let iframe = document.createElement("iframe");
            iframe.setAttribute('style', 'height:1px;width:1px;border:0;opacity:0;position:absolute;');
            iframe.setAttribute("id", "stFrmToPost");
            iframe.setAttribute("name", "stFrmToPost");
            document.body.appendChild(iframe);

            this.el.setAttribute("target", "stFrmToPost");
            this.el.setAttribute("action", stereo_cf.ajax_url);
            this.el.classList.add("is-submitting");

            if (this.el.getAttribute('data-reset-only')) {
                this.el.reset()
            } else {
                this.el.style.display = 'none';
            }

            setTimeout(() => {
                this.el
                    .querySelectorAll(".js-extra-form-data")
                    .forEach((e) => e.parentNode.removeChild(e));
                this.el.reset();
            }, 500);

            if (this.el.dataset.redirect) {
                window.location.href = this.el.dataset.redirect;
            } else {
                this.el.classList.remove("is-submitting")
                this.el.classList.add("is-submitted");
                this.el.nextElementSibling.style.display = "block";
            }
        } else {
            let formData = new FormData(this.el);
            fetch(stereo_cf.ajax_url, { method: 'POST', body: formData })
                .then(response => {
                    this.el.reset();

                    if (this.el.dataset.redirect) {
                        window.location.href = this.el.dataset.redirect;
                    } else {
                        this.el.classList.remove("is-submitting");
                        this.el.classList.add("is-submitted");
                        this.el.nextElementSibling.style.display = "block";
                    }
                    return response
                })
                .catch((error) => {
                    console.log('error', error)
                    this.el.classList.remove("is-submitting");
                    this.el.style.display = 'block';
                    alert("Une erreur est survenue, veuillez réessayer!");
                })

            this.el.classList.add("is-submitting");

            if (this.el.getAttribute('data-reset-only')) {
                this.el.reset()
            } else {
                this.el.style.display = 'none';
            }

            this.el
                .querySelectorAll(".js-extra-form-data")
                .forEach((e) => e.parentNode.removeChild(e));
        }
    }

    formHasFiles() {
        let hasFile = false;
        this.el.querySelectorAll("input[type=file]").forEach(i => {
            if (i.files.length != 0) hasFile = true;
        })
        return hasFile;
    }
}

export { Form };
