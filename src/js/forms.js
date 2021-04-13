export default class Form {
    constructor(element) {
        this.el = element;
        this.init();
    }

    init() {
        this.el.addEventListener("submit", (e) => this.submit(e));
    }

    submit(e) {
        e.preventDefault();

        if (!this.el.checkValidity()) {
            alert("Veuillez compléter tous les champs requis !");
            return false;
        }

        if (window.recaptcha_v3) {
            grecaptcha.ready(() => {
                grecaptcha
                    .execute(window.recaptcha_v3, { action: "submit" })
                    .then((token) => {
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
                this.el.querySelector("input:first-child").getAttribute("name"),
            _category: this.el.dataset.category || "Contact",
            _nobot: "1",
        };

        if (token) additions["_token"] = token;

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

        let extras = this.el.querySelectorAll(".js-extra-form-data");
        if ($extras) {
            for (i = 0; i < extras.length; ++i) {
                extras[i].parentNode.removeChild(e);
            }
        }

        this.el.appendChild(div);

        let formData = new FormData(this.el);
        fetch(stereo_cf.ajax_url, {
            method: "POST",
            credentials: "same-origin",
            body: formData,
        })
            .then((response) => {
                this.el.reset();

                if (this.el.dataset.redirect) {
                    window.location.href = this.el.dataset.redirect;
                } else {
                    this.el.classList.remove("is-submitting");
                    this.el.classList.add("is-submitted");
                    this.el.nextElementSibling.style.display = "block";
                }
                return response;
            })
            .catch((error) => {
                console.log("error", error);
                this.el.classList.remove("is-submitting");
                this.el.style.display = "block";
                alert("Une erreur est survenue, veuillez réessayer!");
            });

        this.el.classList.add("is-submitting");

        if (this.el.getAttribute("data-reset-only")) {
            this.el.reset();
        } else {
            this.el.style.display = "none";
        }

        extras = this.el.querySelectorAll(".js-extra-form-data");
        if ($extras) {
            for (i = 0; i < extras.length; ++i) {
                extras[i].parentNode.removeChild(e);
            }
        }
    }
}

export { Form };
