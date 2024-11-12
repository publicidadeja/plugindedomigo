jQuery(document).ready(function($) {
    var mediaUploader;

    $('#gma-upload-btn').on('click', function(e) {
        e.preventDefault();

        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media({
            title: 'Escolha ou faça upload de uma imagem',
            button: {
                text: 'Usar esta imagem'
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#gma-imagem-url').val(attachment.url);
            $('#gma-image-preview').html('<img src="' + attachment.url + '" alt="Pré-visualização da imagem" style="max-width: 300px;">');
        });

        mediaUploader.open();
    });
});

$('#tipo_midia').on('change', function() {
    if ($(this).val() === 'video') {
        $('#imagem-upload').hide();
        $('#video-upload').show();
    } else {
        $('#imagem-upload').show();
        $('#video-upload').hide();
    }
});

$('#gma-video-upload-btn').on('click', function(e) {
    e.preventDefault();
    
    if (mediaUploader) {
        mediaUploader.open();
        return;
    }

    mediaUploader = wp.media({
        title: gmaData.wpMediaTitle,
        button: {
            text: gmaData.wpMediaButton
        },
        library: {
            type: 'video'
        },
        multiple: false
    });

    mediaUploader.on('select', function() {
        var attachment = mediaUploader.state().get('selection').first().toJSON();
        $('#video_url').val(attachment.url);
        $('#video-preview').html('<video width="320" height="240" controls><source src="' + attachment.url + '" type="' + attachment.mime + '"></video>');
    });

    mediaUploader.open();
});
