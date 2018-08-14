jQuery(function($) {
    $(document).on('submit','.js-stereo-cf',function() {
        var frm = $(this).get(0);
        if (!frm.checkValidity()) {
            alert('Veuillez compléter tous les champs requis !');
            return false;
        }
        var $div = $('<div class="js-extra-form-data" />');
        var additions = {
            "action": "st_post_contact",
            "Page actuelle": location.href,
            "Page précédente" : document.referrer,
            "_subject": $(this).data('subject') || 'Formulaire de contact',
            "_title_field": $(this).data('title') || $(this).find('input:first').attr('name'),
            "_nobot": "1"
        }
        $.each(additions,function(k,v) {
            var $input = $('<input type="hidden" />');
            $input.attr('name',k);
            $input.attr('value',v);
            $input.appendTo($div);
        });
        $(this).find('.js-extra-form-data').remove();
        $div.appendTo($(this));
        var $this = $(this);
        $.post(stereo_cf.ajax_url,$(this).serializeArray())
        .then(function() {
            $this.get(0).reset();
            $this.next().show();
        })
        .fail(function() {
            $this.show();
            alert('Une erreur est survenue, veuillez réessayer!');
        });
        $this.hide()
        $this.find('.js-extra-form-data').remove();
        return false;
    });
});
