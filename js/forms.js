jQuery(function ($) {

    var formHasFiles = function (frm) {
        var hasFile = false;
        $(frm).find('input[type="file"]').each(function () {
            if ($(this).val()) hasFile = true;
        });
        return hasFile;
    };

    $(document).on('submit', '.js-stereo-cf', function (e) {
        e.preventDefault();
        var form = $(this);
        if (window.recaptcha_v3) {
            grecaptcha.ready(function () {
                grecaptcha.execute(window.recaptcha_v3, { action: 'submit' }).then(function (token) {
                    submitStereoForm(form, token);
                });
            });
        } else {
            submitStereoForm(form);
        }
        return false;
    });

    var submitStereoForm = function(form, token) {
        var frm = form.get(0);
        if (!frm.checkValidity()) {
            alert('Veuillez compléter tous les champs requis !');
            return false;
        }
        var $div = $('<div class="js-extra-form-data" />');
        var additions = {
            "action": "st_post_contact",
            "Page actuelle": location.href,
            "Page précédente": document.referrer,
            "_subject": form.data('subject') || 'Formulaire de contact',
            "_title_field": form.data('title') || form.find('input:first').attr('name'),
            "_category": form.data('category') || 'Contact',
            "_nobot": "1"
        }

        if (token) additions['_token'] = token;

        $.each(additions, function (k, v) {
            var $input = $('<input type="hidden" />');
            $input.attr('name', k);
            $input.attr('value', v);
            $input.appendTo($div);
        });

        var callback = form.data('callback');
        if (callback && window[callback]) {
            try {
                window[callback](form[0]);
            } catch (error) {
                // skip
            }
        }

        form.find('.js-extra-form-data').remove();
        $div.appendTo(form);

        if (formHasFiles(frm)) {
            $('#stFrmToPost').remove();
            $('<iframe style="height:1px;width:1px;border:0;opacity:0;position:absolute;" id="stFrmToPost" name="stFrmToPost" />').appendTo($('body'));
            form.attr('target', 'stFrmToPost');
            form.attr('action', stereo_cf.ajax_url);
            form.addClass('is-submitting')

            if (!form.data('reset-only')) {
                form[0].reset();
            } else {
                form.hide();
            }

            setTimeout(function () {
                form.find('.js-extra-form-data').remove();
                form.get(0).reset();
            }, 500)

            if (form.data('redirect')) {
                window.location.href = form.data('redirect');
            } else {
                form.removeClass('is-submitting').addClass('is-submitted').next().show();
            }
        } else {
            $.post(stereo_cf.ajax_url, form.serializeArray())
                .then(function () {
                    form.get(0).reset();

                    if (form.data('redirect')) {
                        window.location.href = form.data('redirect');
                    } else {
                        form.removeClass('is-submitting').addClass('is-submitted').next().show();
                    }
                })
                .fail(function () {
                    form.removeClass('is-submitting').show();
                    alert('Une erreur est survenue, veuillez réessayer!');
                });
            form.addClass('is-submitting');

            if (form.data('reset-only')) {
                form[0].reset();
            } else {
                form.hide();
            }

            form.find('.js-extra-form-data').remove();
            return false;
        }
    }
});
