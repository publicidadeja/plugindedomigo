<?php
if (!defined('ABSPATH')) {
  // Adicione esta função no arquivo pagina-listar-materiais.php logo após a verificação de ABSPATH

function gma_enqueue_material_scripts() {
    // Swiper JS
    wp_enqueue_style('swiper-bundle', 'https://unpkg.com/swiper/swiper-bundle.min.css', array(), '7.0.0');
    wp_enqueue_script('swiper-bundle', 'https://unpkg.com/swiper/swiper-bundle.min.js', array('jquery'), '7.0.0', true);
    
    // Custom scripts
    wp_enqueue_script('gma-material-list', plugins_url('/assets/js/material-list.js', dirname(__FILE__)), array('jquery', 'swiper-bundle'), '1.0.0', true);
    
    // Localize script
    wp_localize_script('gma-material-list', 'gmaVars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('gma_material_nonce')
    ));
}
add_action('admin_enqueue_scripts', 'gma_enqueue_material_scripts');
    exit;
}

// Enqueue necessary files
wp_enqueue_style('gma-admin-style', plugins_url('/assets/css/admin-style.css', dirname(__FILE__)));
wp_enqueue_script('gma-admin-script', plugins_url('/assets/js/admin-script.js', dirname(__FILE__)), array('jquery'), '1.0', true);
wp_enqueue_style('swiper-style', 'https://unpkg.com/swiper/swiper-bundle.min.css');
wp_enqueue_script('swiper-script', 'https://unpkg.com/swiper/swiper-bundle.min.js', array('jquery'), null, true);
wp_enqueue_style('dashicons');

// Debug mode
if (WP_DEBUG) {
    error_log('Materiais recebidos: ' . print_r($materiais, true));
}
?>

<div class="wrap">
    <center><h1 class="gma-header">Listar Materiais</h1></center>

    <!-- Filters -->
    <div class="gma-filter">
        <select id="filter-status" class="gma-filter-select">
            <option value="todos">Todos os Status</option>
            <option value="aprovado">Aprovados</option>
            <option value="reprovado">Reprovados</option>
            <option value="pendente">Pendentes</option>
        </select>

        <select id="filter-tipo" class="gma-filter-select">
            <option value="todos">Todos os Tipos</option>
            <option value="aprovacao">Aprovação</option>
            <option value="marketing">Marketing</option>
        </select>

        <input type="text" id="filter-campanha-nome" class="gma-filter-input" placeholder="Nome da Campanha">
    </div>

    <div class="gma-grid">
        <!-- Approved Materials -->
        <div class="gma-card" data-status="aprovado">
            <h2 class="column-header approved">Aprovados</h2>
            <div class="materials-list">
                <?php
                if (!empty($materiais)) {
                    foreach ($materiais as $material) {
                        if ($material->status_aprovacao === 'aprovado') {
                            echo gma_render_material_card($material);
                        }
                    }
                }
                ?>
            </div>
        </div>

        <!-- Rejected Materials -->
        <div class="gma-card" data-status="reprovado">
            <h2 class="column-header rejected">Reprovados</h2>
            <div class="materials-list">
                <?php
                if (!empty($materiais)) {
                    foreach ($materiais as $material) {
                        if ($material->status_aprovacao === 'reprovado') {
                            echo gma_render_material_card($material);
                        }
                    }
                }
                ?>
            </div>
        </div>

        <!-- Pending Materials -->
        <div class="gma-card" data-status="pendente">
            <h2 class="column-header pending">Para Edição</h2>
            <div class="materials-list">
                <?php
                if (!empty($materiais)) {
                    foreach ($materiais as $material) {
                        if ($material->status_aprovacao === 'pendente') {
                            echo gma_render_material_card($material);
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<!-- CSS -->
<style>
:root {
    --primary-color: #6e8efb;
    --secondary-color: #a777e3;
    --success-color: #46b450;
    --error-color: #dc3232;
    --border-radius: 8px;
    --box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.wrap {
    max-width: 1200px;
    margin: 20px auto;
    font-family: 'Roboto', Arial, sans-serif;
}

.gma-filter {
    display: flex;
    gap: 15px;
    margin: 20px 0;
}

.gma-filter-select,
.gma-filter-input {
    padding: 8px;
    border-radius: var(--border-radius);
    border: 1px solid #ddd;
    min-width: 200px;
}

.gma-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.gma-card {
    background: #fff;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
}

.column-header {
    padding: 15px;
    color: white;
    text-align: center;
    font-weight: bold;
    text-transform: uppercase;
}

.approved { background-color: #4CAF50; }
.rejected { background-color: #f44336; }
.pending { background-color: #ff9800; }

.materials-list {
    padding: 15px;
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.material-card {
    background: #fff;
    border-radius: var(--border-radius);
    box-shadow: var(--box-shadow);
    overflow: hidden;
}

.material-image {
    width: 100%;
    max-width: 300px;
    height: auto;
}

.material-image img {
    width: 100%;
    height: auto;
    display: block;
}

.material-info {
    padding: 15px;
}

.campaign-type {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 12px;
    color: white;
    margin: 5px 0;
}

.campaign-type.aprovacao {
    background: linear-gradient(135deg, #6e8efb, #4a6cf7);
}

.campaign-type.marketing {
    background: linear-gradient(135deg, #a777e3, #8854d0);
}

.material-actions {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
}

.button {
    padding: 8px 15px;
    border-radius: var(--border-radius);
    border: none;
    color: white;
    font-weight: bold;
    text-transform: uppercase;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.delete-button {
    background-color: #f44336;
}

.campaign-name {
    background: #f5f5f5;
    padding: 10px;
    font-weight: bold;
    text-align: center;
    border-bottom: 1px solid #ddd;
    color: #333;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.material-carousel {
    width: 100%;
    height: 300px;
}

.swiper-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-container {
    width: 100%;
    position: relative;
    padding-bottom: 56.25%;
    height: 0;
}

.material-video {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
}

.swiper-button-next,
.swiper-button-prev {
    color: #fff;
    background: rgba(0,0,0,0.5);
    padding: 30px;
    border-radius: 50%;
}

.swiper-pagination-bullet-active {
    background: var(--primary-color);
}

@media screen and (max-width: 782px) {
    .gma-filter {
        flex-direction: column;
    }
    
    .gma-filter-select,
    .gma-filter-input {
        width: 100%;
    }
}

@media screen and (max-width: 480px) {
    .material-carousel {
        height: 200px;
    }
    
    .material-actions {
        flex-direction: column;
        gap: 10px;
    }
    
    .button {
        width: 100%;
        text-align: center;
    }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializa todos os carrosséis
    document.querySelectorAll('.material-carousel').forEach(function(element) {
        new Swiper(element, {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            pagination: {
                el: element.querySelector('.swiper-pagination'),
                clickable: true,
            },
            navigation: {
                nextEl: element.querySelector('.swiper-button-next'),
                prevEl: element.querySelector('.swiper-button-prev'),
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
        });
    });

    // Inicializa os players de vídeo
    document.querySelectorAll('.video-container').forEach(function(container) {
        const video = container.querySelector('video');
        if (video) {
            video.addEventListener('loadedmetadata', function() {
                container.style.paddingBottom = (video.videoHeight / video.videoWidth * 100) + '%';
            });
        }
    });
});

// Filter functionality
jQuery(document).ready(function($) {
    function filterMaterials() {
        const statusFilter = $('#filter-status').val();
        const tipoFilter = $('#filter-tipo').val();
        const campanhaNomeFilter = $('#filter-campanha-nome').val().toLowerCase();

        $('.material-card').each(function() {
            const card = $(this);
            const status = card.data('status');
            const tipo = card.data('tipo');
            const campanhaNome = card.find('.campaign-name').text().toLowerCase();

            const statusMatch = statusFilter === 'todos' || status === statusFilter;
            const tipoMatch = tipoFilter === 'todos' || tipo === tipoFilter;
            const campanhaNomeMatch = campanhaNomeFilter === '' || campanhaNome.includes(campanhaNomeFilter);

            if (statusMatch && tipoMatch && campanhaNomeMatch) {
                card.show();
            } else {
                card.hide();
            }
        });
    }

    $('#filter-status, #filter-tipo, #filter-campanha-nome').on('change keyup', filterMaterials);
});
</script>

<?php
// The rest of your PHP functions (gma_render_material_card and gma_render_action_buttons) 
// remain the same as they are working correctly
function gma_render_material_card($material) {
    if (!is_object($material)) {
        return '';
    }

    // Debug log
    if (WP_DEBUG) {
        error_log('Material data: ' . print_r($material, true));
    }

    $is_aprovacao = isset($material->tipo_campanha) ? $material->tipo_campanha === 'aprovacao' : false;
    $campanha = isset($material->campanha_id) ? gma_obter_campanha($material->campanha_id) : null;
    $nome_campanha = $campanha ? $campanha->nome : 'Campanha não encontrada';

    ob_start();
    ?>
    <div class="material-card <?php echo esc_attr($is_aprovacao ? 'aprovacao' : 'marketing'); ?>"
         data-status="<?php echo esc_attr($material->status_aprovacao); ?>"
         data-tipo="<?php echo esc_attr($material->tipo_campanha); ?>"
         data-tipo-midia="<?php echo esc_attr($material->tipo_midia); ?>"
         data-campanha="<?php echo esc_attr($material->campanha_id); ?>">
        
        <div class="campaign-name">
            <?php echo esc_html($nome_campanha); ?>
            <span class="campaign-status <?php echo esc_attr($material->status_aprovacao); ?>">
                <?php echo esc_html(ucfirst($material->status_aprovacao)); ?>
            </span>
        </div>

        <div class="material-media">
            <?php if ($material->tipo_midia === 'video' && !empty($material->video_url)): ?>
                <div class="video-container">
                    <video controls width="100%" preload="metadata">
                        <source src="<?php echo esc_url($material->video_url); ?>" type="video/mp4">
                        Seu navegador não suporta o elemento de vídeo.
                    </video>
                </div>
            <?php elseif (!empty($material->imagem_url)): ?>
                <div class="image-container">
                    <img src="<?php echo esc_url($material->imagem_url); ?>" 
                         alt="<?php echo esc_attr($nome_campanha); ?>" 
                         class="material-image"
                         loading="lazy">
                </div>
            <?php endif; ?>
        </div>

        <div class="material-info">
            <span class="campaign-type <?php echo esc_attr($material->tipo_campanha); ?>">
                <?php echo esc_html($is_aprovacao ? 'Aprovação' : 'Marketing'); ?>
            </span>
            <?php if (!empty($material->copy)) : ?>
                <div class="material-copy">
                    <p><?php echo wp_kses_post(wp_trim_words($material->copy, 10)); ?></p>
                    <?php if (strlen($material->copy) > 55) : ?>
                        <button class="show-more-btn">Ver mais</button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            <div class="material-actions">
                <?php echo gma_render_action_buttons($material, $is_aprovacao); ?>
            </div>
        </div>
    </div>

    <style>
      
      .gma-video-preview {
    width: 100%;
    max-width: 300px;
    margin: 0 auto;
}

.gma-video-preview video {
    width: 100%;
    height: auto;
    border-radius: var(--border-radius);
    background: #000;
}

.material-media {
    width: 100%;
    min-height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    border-radius: var(--border-radius);
    overflow: hidden;
}
    .material-card {
        margin-bottom: 20px;
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        overflow: hidden;
        transition: transform 0.2s ease;
    }

    .material-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .campaign-name {
        padding: 12px;
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .campaign-status {
        font-size: 12px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 500;
    }

    .campaign-status.aprovado { background: #28a745; color: white; }
    .campaign-status.reprovado { background: #dc3545; color: white; }
    .campaign-status.pendente { background: #ffc107; color: #000; }

    .material-image {
        position: relative;
        width: 100%;
        min-height: 200px;
        background: #f5f5f5;
    }

    .material-carousel {
        height: 300px;
    }

    .video-container {
        position: relative;
        padding-bottom: 56.25%;
        height: 0;
        overflow: hidden;
        background: #000;
    }

    .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(0,0,0,0.3);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .video-container:hover .video-overlay {
        opacity: 1;
    }

    .play-button {
        background: rgba(255,255,255,0.9);
        border: none;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: transform 0.2s ease;
    }

    .play-button:hover {
        transform: scale(1.1);
    }

    .material-video {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .single-image-container {
        position: relative;
        overflow: hidden;
        padding-bottom: 75%;
    }

    .single-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .carousel-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .material-info {
        padding: 15px;
    }

    .material-copy {
        margin: 10px 0;
        position: relative;
    }

    .show-more-btn {
        background: none;
        border: none;
        color: #007bff;
        padding: 0;
        font-size: 12px;
        cursor: pointer;
        text-decoration: underline;
    }

    .material-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    @media (max-width: 768px) {
        .material-carousel {
            height: 200px;
        }
    }
    </style>
    <?php
    return ob_get_clean();
}
function gma_render_action_buttons($material, $is_aprovacao) {
    $edit_url = esc_url(admin_url('admin.php?page=gma-editar-material&id=' . $material->id . '&tipo=' . ($is_aprovacao ? 'aprovacao' : 'marketing')));
    $delete_url = wp_nonce_url(
        admin_url("admin-post.php?action=gma_excluir_material&id={$material->id}"),
        'gma_excluir_material_' . $material->id,
        'gma_nonce'
    );

    ob_start();
    ?>
    <a href="<?php echo $edit_url; ?>" class="button">Editar</a>
    <a href="<?php echo $delete_url; ?>" class="button delete-button">Excluir</a>
    <?php
    return ob_get_clean();
}
?>
