<?php
if (!defined('ABSPATH')) exit;

// Handler do formulário
if (isset($_POST['criar_material'])) {
    if (check_admin_referer('gma_novo_material', 'gma_novo_material_nonce')) {
        $campanha_id = intval($_POST['campanha_id']);
        $copy = sanitize_textarea_field($_POST['copy']);
        $link_canva = esc_url_raw($_POST['link_canva']);
        $tipo_midia = sanitize_text_field($_POST['tipo_midia']);
        
        if ($tipo_midia === 'video') {
            $video_url = esc_url_raw($_POST['video_url']);
            $material_id = gma_criar_material(
                $campanha_id,
                '',
                $copy,
                $link_canva,
                null,
                'video',
                $video_url
            );
        } else {
            $imagem_url = esc_url_raw($_POST['imagem_url']);
            $arquivo_id = intval($_POST['arquivo_id']);
            $material_id = gma_criar_material(
                $campanha_id,
                $imagem_url,
                $copy,
                $link_canva,
                $arquivo_id,
                'imagem'
            );
        }

        if ($material_id) {
            wp_redirect(admin_url('admin.php?page=gma-materiais&message=created'));
            exit;
        }
    }
}

// Enqueue scripts
wp_enqueue_media();
wp_enqueue_script('jquery');

// Localize script
wp_localize_script('jquery', 'gma_ajax', array(
    'ajaxurl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('gma_novo_material')
));
?>

<!-- HTML Structure -->
<div class="gma-create-wrap">
    <div class="gma-create-container">
        <h1 class="gma-create-title">🎨 Criar Novo Material</h1>
        <div class="gma-create-card">
            <form method="post" class="gma-create-form" id="gma-material-form">
                <?php wp_nonce_field('gma_novo_material', 'gma_novo_material_nonce'); ?>
                
                <div class="gma-form-grid">
                    <!-- Campanha -->
                    <div class="gma-form-group">
                        <label for="campanha_id">
                            <i class="dashicons dashicons-megaphone"></i> Campanha
                        </label>
                        <select name="campanha_id" id="campanha_id" required>
                            <option value="">Selecione uma campanha</option>
                            <?php 
                            $campanhas = gma_listar_campanhas();
                            foreach ($campanhas as $campanha): 
                                $tipo = esc_attr($campanha->tipo_campanha);
                            ?>
                                <option value="<?php echo esc_attr($campanha->id); ?>" 
                                        data-tipo="<?php echo $tipo; ?>">
                                    <?php echo esc_html($campanha->nome); ?> 
                                    (<?php echo ucfirst($tipo); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Tipo de Mídia -->
                    <div class="gma-form-group">
                        <label for="tipo_midia">
                            <i class="dashicons dashicons-admin-media"></i> Tipo de Mídia
                        </label>
                        <select name="tipo_midia" id="tipo_midia" required>
                            <option value="imagem">Imagem</option>
                            <option value="video">Vídeo</option>
                        </select>
                    </div>

                    <!-- Upload de Imagem -->
                    <div class="gma-form-group" id="imagem-upload-group">
                        <label for="gma-imagem-url">
                            <i class="dashicons dashicons-format-image"></i> Imagem
                        </label>
                        <button type="button" class="button" id="gma-upload-btn">Escolher Imagem</button>
                        <input type="hidden" name="imagem_url" id="gma-imagem-url">
                        <input type="hidden" name="arquivo_id" id="gma-arquivo-id">
                        <div id="imagem-preview" class="gma-image-preview"></div>
                    </div>

                    <!-- Upload de Vídeo -->
                    <div class="gma-form-group" id="video-upload-group" style="display: none;">
                        <label for="gma-video-url">
                            <i class="dashicons dashicons-video-alt3"></i> Vídeo
                        </label>
                        <div class="gma-upload-container">
                            <input type="text" name="video_url" id="gma-video-url" class="gma-input" readonly>
                            <button type="button" id="gma-video-upload-btn" class="gma-button secondary">
                                <i class="dashicons dashicons-upload"></i> Selecionar Vídeo
                            </button>
                        </div>
                        <div id="gma-video-preview" class="gma-video-preview"></div>
                    </div>

                    <!-- Copy -->
                    <div class="gma-form-group full-width">
                        <label for="copy">
                            <i class="dashicons dashicons-editor-paste-text"></i> Copy
                        </label>
                        <textarea name="copy" id="copy" rows="5" required></textarea>
                    </div>

                    <!-- Link Canva -->
                    <div class="gma-form-group full-width" id="canva-group" style="display: none;">
                        <label for="link_canva">
                            <i class="dashicons dashicons-art"></i> Link do Canva
                        </label>
                        <input type="url" name="link_canva" id="link_canva" class="gma-input">
                    </div>
                </div>

                <div class="gma-form-actions">
                    <button type="submit" name="criar_material" class="gma-button primary">
                        <i class="dashicons dashicons-saved"></i> Criar Material
                    </button>
                    <a href="<?php echo admin_url('admin.php?page=gma-materiais'); ?>" class="gma-button secondary">
                        <i class="dashicons dashicons-arrow-left-alt"></i> Voltar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Controle de tipo de mídia
    $('#tipo_midia').on('change', function() {
        var tipoMidia = $(this).val();
        if (tipoMidia === 'video') {
            $('#imagem-upload-group').hide();
            $('#video-upload-group').show();
        } else {
            $('#imagem-upload-group').show();
            $('#video-upload-group').hide();
        }
    });

    // Upload de imagem
    $('#gma-upload-btn').click(function(e) {
        e.preventDefault();
        
        var image_frame = wp.media({
            title: 'Selecionar Imagem',
            multiple: false,
            library: {
                type: 'image'
            }
        });

        image_frame.on('select', function() {
            var attachment = image_frame.state().get('selection').first().toJSON();
            $('#gma-imagem-url').val(attachment.url);
            $('#gma-arquivo-id').val(attachment.id);
            $('#imagem-preview').html('<img src="' + attachment.url + '" alt="Preview">');
        });

        image_frame.open();
    });

    // Upload de vídeo
    $('#gma-video-upload-btn').click(function(e) {
        e.preventDefault();
        
        var video_frame = wp.media({
            title: 'Selecionar Vídeo',
            multiple: false,
            library: {
                type: 'video'
            }
        });

        video_frame.on('select', function() {
            var attachment = video_frame.state().get('selection').first().toJSON();
            $('#gma-video-url').val(attachment.url);
            $('#gma-video-preview').html(
                '<video width="300" controls>' +
                '<source src="' + attachment.url + '" type="' + attachment.mime + '">' +
                'Seu navegador não suporta o elemento de vídeo.' +
                '</video>'
            );
        });

        video_frame.open();
    });

    // Controle do campo Canva
    $('#campanha_id').on('change', function() {
        var tipoCampanha = $(this).find('option:selected').data('tipo');
        if (tipoCampanha === 'marketing') {
            $('#canva-group').show();
        } else {
            $('#canva-group').hide();
        }
    });

    // Validação do formulário
    $('#gma-material-form').on('submit', function(e) {
        e.preventDefault();
        
        var tipoMidia = $('#tipo_midia').val();
        var isValid = true;

        if (!$('#campanha_id').val() || !$('#copy').val()) {
            alert('Por favor, preencha todos os campos obrigatórios.');
            return false;
        }

        if (tipoMidia === 'video' && !$('#gma-video-url').val()) {
            alert('Por favor, selecione um vídeo.');
            return false;
        }

        if (tipoMidia === 'imagem' && !$('#gma-imagem-url').val()) {
            alert('Por favor, selecione uma imagem.');
            return false;
        }

        this.submit();
    });
});
</script>

<style>
/* Seus estilos CSS aqui - mantido o mesmo que você já tem */
</style>
<style>
:root {
    --primary-color: #4a90e2;
    --secondary-color: #2ecc71;
    --danger-color: #e74c3c;
    --text-color: #2c3e50;
    --background-color: #f5f6fa;
    --card-background: #ffffff;
    --border-radius: 10px;
    --transition: all 0.3s ease;
}

.gma-create-wrap {
    padding: 20px;
    background: var(--background-color);
    min-height: 100vh;
}

.gma-create-container {
    max-width: 800px;
    margin: 0 auto;
}

.gma-create-title {
    font-size: 2.5em;
    color: var(--text-color);
    text-align: center;
    margin-bottom: 30px;
    font-weight: 700;
}

.gma-create-card {
    background: var(--card-background);
    border-radius: var(--border-radius);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    padding: 30px;
    animation: slideIn 0.5s ease;
}

.gma-form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.gma-form-group {
    margin-bottom: 20px;
}

.gma-form-group.full-width {
    grid-column: 1 / -1;
}

.gma-form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-color);
}

.gma-input, select, textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e1e1e1;
    border-radius: var(--border-radius);
    font-size: 1em;
    transition: var(--transition);
}

.gma-input:focus, select:focus, textarea:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
}

.gma-upload-container {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.gma-image-preview {
    margin-top: 10px;
    max-width: 300px;
    border-radius: var(--border-radius);
    overflow: hidden;
}

.gma-image-preview img,
.gma-video-preview video {
    width: 100%;
    height: auto;
    display: block;
    border-radius: var(--border-radius);
}

.gma-image-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.carrossel-item {
    position: relative;
}

.carrossel-image {
    width: 100%;
    height: auto;
    border-radius: var(--border-radius);
}

.gma-button {
    padding: 12px 24px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition);
    text-decoration: none;
}

.gma-button.primary {
    background: var(--primary-color);
    color: white;
}

.gma-button.secondary {
    background: var(--secondary-color);
    color: white;
}

.gma-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.gma-form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    justify-content: flex-end;
}

@keyframes slideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .gma-form-grid {
        grid-template-columns: 1fr;
    }
    
    .gma-form-actions {
        flex-direction: column;
    }
    
    .gma-button {
        width: 100%;
        justify-content: center;
    }
}

.error {
    border-color: var(--danger-color) !important;
}

.shake {
    animation: shake 0.5s linear;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

.midia-uploads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.midia-item {
    position: relative;
}

.midia-item img {
    width: 100%;
    height: auto;
    border-radius: var(--border-radius);
}

.remove-midia {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    font-size: 1.2em;
    color: #e74c3c;
    cursor: pointer;
}

#suggestions-container {
    margin-top: 15px;
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: var(--border-radius);
}

#suggestions-content {
    margin-top: 10px;
}
  
  .gma-video-preview {
    margin-top: 10px;
    max-width: 300px;
    border-radius: var(--border-radius);
    overflow: hidden;
}

.gma-video-preview video {
    width: 100%;
    height: auto;
    display: block;
}
</style>

