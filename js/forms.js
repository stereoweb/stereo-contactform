jQuery(function ($) {

    var formHasFiles = function (frm) {
        var hasFile = false;
        $(frm).find('input[type="file"]').each(function () {
            if ($(this).val()) hasFile = true;
        });
        return hasFile;
    };

    $(document).on('submit', '.js-stereo-cf', function () {
        var frm = $(this).get(0);
        if (!frm.checkValidity()) {
            alert('Veuillez compléter tous les champs requis !');
            return false;
        }
        var $div = $('<div class="js-extra-form-data" />');
        var additions = {
            "action": "st_post_contact",
            "Page actuelle": location.href,
            "Page précédente": document.referrer,
            "_subject": $(this).data('subject') || 'Formulaire de contact',
            "_title_field": $(this).data('title') || $(this).find('input:first').attr('name'),
            "_category": $(this).data('category') || 'Contact',
            "_nobot": "1"
        }
        $.each(additions, function (k, v) {
            var $input = $('<input type="hidden" />');
            $input.attr('name', k);
            $input.attr('value', v);
            $input.appendTo($div);
        });

        $(this).find('.js-extra-form-data').remove();
        $div.appendTo($(this));
        var $this = $(this);

        if (formHasFiles(frm)) {
            $('#stFrmToPost').remove();
            $('<iframe style="height:1px;width:1px;border:0;opacity:0;position:absolute;" id="stFrmToPost" name="stFrmToPost" />').appendTo($('body'));
            $this.attr('target', 'stFrmToPost');
            $this.attr('action', stereo_cf.ajax_url);
            $this.addClass('is-submitting').hide()
            setTimeout(function () {
                $this.find('.js-extra-form-data').remove();
                $this.get(0).reset();
            }, 500)

            if ($this.data('redirect')) {
                window.location.href = $this.data('redirect');
            } else {
                $this.removeClass('is-submitting').addClass('is-submitted').next().show();
            }
        } else {
            $.post(stereo_cf.ajax_url, $(this).serializeArray())
                .then(function () {
                    $this.get(0).reset();

                    if ($this.data('redirect')) {
                        window.location.href = $this.data('redirect');
                    } else {
                        $this.removeClass('is-submitting').addClass('is-submitted').next().show();
                    }
                })
                .fail(function () {
                    $this.removeClass('is-submitting').show();
                    alert('Une erreur est survenue, veuillez réessayer!');
                });
            $this.addClass('is-submitting').hide()
            $this.find('.js-extra-form-data').remove();
            return false;
        }
    });
});
