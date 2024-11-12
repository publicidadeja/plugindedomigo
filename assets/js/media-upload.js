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
        title: 'Escolha ou faça upload de um vídeo',
        button: {
            text: 'Usar este vídeo'
        },
        multiple: false,
        library: {
            type: 'video'
        }
    });

    mediaUploader.on('select', function() {
        var attachment = mediaUploader.state().get('selection').first().toJSON();
        $('#gma-video-url').val(attachment.url);
        $('#gma-video-preview').html(
            '<video controls width="300" preload="metadata">' +
            '<source src="' + attachment.url + '" type="video/mp4">' +
            '</video>'
        );
    });

    mediaUploader.open();
});
