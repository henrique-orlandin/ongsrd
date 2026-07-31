<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $item = $item ?? null; ?>
<?php $images = $images ?? []; ?>
<?php $existingImageCount = is_array($images) ? count($images) : 0; ?>
<?php $remainingSlots = max(0, 10 - $existingImageCount); ?>
<?php
$publishedAtSource = old('published_at', $item['published_at'] ?? '');
$publishedAtValue = '';
if (is_string($publishedAtSource) && trim($publishedAtSource) !== '') {
    $timestamp = strtotime($publishedAtSource);
    if ($timestamp !== false) {
        $publishedAtValue = date('Y-m-d H:i', $timestamp);
    }
}

if ($publishedAtValue === '') {
    $publishedAtValue = date('Y-m-d H:i');
}
?>
<section class="panel form-panel">
    <h2><i class="fa-solid fa-newspaper"></i><?= $item ? 'Editar Noticia' : 'Nova Noticia' ?></h2>
    <form class="dropzone upload-image" method="post" enctype="multipart/form-data" action="<?= $item ? base_url('cms/noticias/update/' . $item['id']) : base_url('cms/noticias/create') ?>">
        <?= csrf_field() ?>
        <div>
            <label>Titulo <input type="text" name="title" value="<?= esc(old('title', $item['title'] ?? '')) ?>" required></label>
        </div>
        <div>
            <label>Data de Publicacao
                <input type="text" name="published_at" class="js-flatpickr" value="<?= esc($publishedAtValue) ?>" placeholder="dd/mm/aaaa hh:mm" required>
            </label>
        </div>
        <div class="editor-field">
            <label class="editor-field">Descricao <textarea class="editor" name="description" rows="7" required><?= esc(old('description', $item['description'] ?? '')) ?></textarea></label>
        </div>
        <div>
            <label>Imagens da Noticia (max. 10 no total)
                <div id="news-dropzone" class="upload-dropzone my-dropzone gallery-dropzone">
                    <div class="dz-message"><i class="fa-solid fa-images"></i> Solte imagens da noticia aqui ou clique para enviar.</div>
                </div>
                <?php if ($remainingSlots === 0): ?>
                    <small>Todos os 10 espacos de imagem ja estao em uso. Remova uma imagem para enviar outra.</small>
                <?php else: ?>
                    <small><?= esc((string) $remainingSlots) ?> espacos de imagem disponiveis.</small>
                <?php endif; ?>
            </label>
        </div>
        <?php if (! empty($images) && is_array($item) && isset($item['id'])): ?>
            <div class="gallery">
                <?php foreach ($images as $img): ?>
                    <figure class="<?= (int) $img['is_main'] === 1 ? 'is-thumbnail' : '' ?>">
                        <img src="<?= base_url($img['image_path']) ?>" alt="">
                        <div class="icons">
                            <?php if ((int) $img['is_main'] === 1): ?>
                                <span class="thumbnail-badge"><i class="fa-solid fa-star"></i></span>
                            <?php else: ?>
                                <button class="link" type="button"
                                    data-main-url="<?= base_url('cms/noticias/' . $item['id'] . '/images/' . $img['id'] . '/main') ?>"
                                    data-csrf-name="<?= csrf_token() ?>"
                                    data-csrf-value="<?= csrf_hash() ?>">
                                    <i class="fa-regular fa-star"></i>
                                </button>
                            <?php endif; ?>
                            <button class="link danger" type="button"
                                data-delete-url="<?= base_url('cms/noticias/' . $item['id'] . '/images/' . $img['id'] . '/delete') ?>"
                                data-csrf-name="<?= csrf_token() ?>"
                                data-csrf-value="<?= csrf_hash() ?>">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </figure>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="actions"><button class="btn" type="submit"><i class="fa-solid fa-floppy-disk"></i>Salvar</button></div>
    </form>
</section>
<script>
    if (typeof flatpickr !== 'undefined') {
        flatpickr('.js-flatpickr', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            altInput: true,
            altFormat: 'd/m/Y H:i',
            locale: flatpickr.l10ns.pt,
            time_24hr: true,
            minuteIncrement: 1,
            allowInput: true,
            disableMobile: true
        });
    }

    document.querySelectorAll('button[data-main-url]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const url = btn.dataset.mainUrl;
            const csrfName = btn.dataset.csrfName;
            const csrfValue = btn.dataset.csrfValue;
            const body = new URLSearchParams();
            body.append(csrfName, csrfValue);
            fetch(url, {
                    method: 'POST',
                    body: body,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(res) {
                    if (res.ok) {
                        window.location.reload();
                    } else {
                        alert('Falha ao definir imagem principal.');
                    }
                })
                .catch(function() {
                    alert('Falha ao definir imagem principal.');
                });
        });
    });

    document.querySelectorAll('button[data-delete-url]').forEach(function(btn) {
        btn.addEventListener('click', function() {
            if (!confirm('Remover esta imagem?')) return;
            const url = btn.dataset.deleteUrl;
            const csrfName = btn.dataset.csrfName;
            const csrfValue = btn.dataset.csrfValue;
            const body = new URLSearchParams();
            body.append(csrfName, csrfValue);
            fetch(url, {
                    method: 'POST',
                    body: body,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(res) {
                    if (res.ok || res.redirected) {
                        window.location.reload();
                    } else {
                        alert('Falha ao remover imagem.');
                    }
                })
                .catch(function() {
                    alert('Falha ao remover imagem.');
                });
        });
    });

    Dropzone.autoDiscover = false;

    const dropzoneSelector = '#news-dropzone';
    const form = document.querySelector(dropzoneSelector).closest('form');
    const maxFiles = <?= esc((string) $remainingSlots) ?>;
    let skipDropzoneSubmit = false;

    new Dropzone(dropzoneSelector, {
        url: form.getAttribute('action'),
        autoProcessQueue: false,
        maxFiles,
        acceptedFiles: 'image/*',
        paramName: 'images',
        uploadMultiple: true,
        parallelUploads: Math.max(1, maxFiles),
        addRemoveLinks: true,
        init: function() {
            form.addEventListener('submit', (e) => {
                if (skipDropzoneSubmit) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                if (this.getQueuedFiles().length <= 0) {
                    skipDropzoneSubmit = true;
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                    } else {
                        form.submit();
                    }
                    return;
                }

                this.processQueue();
            });

            this.on('maxfilesexceeded', function(file) {
                this.removeFile(file);
            });

            this.on('addedfile', function() {
                document.querySelector(dropzoneSelector).querySelector('.dz-message').style.display = 'none';
            });

            this.on('removedfile', function() {
                if (this.files.length === 0) {
                    document.querySelector(dropzoneSelector).querySelector('.dz-message').style.display = 'block';
                }
            });

            this.on('successmultiple', function(files, response) {
                if (response.success) {
                    window.location.href = "<?= base_url('cms/noticias') ?>";
                    return;
                }

                alert('Erro ao enviar imagens: ' + (response.message || 'Erro desconhecido'));
            });

            this.on('success', function(file, response) {
                if (response.success) {
                    window.location.href = "<?= base_url('cms/noticias') ?>";
                }
            });

            this.on('error', function(file, errorMessage, xhr) {
                const responseMessage = xhr && xhr.responseText ? xhr.responseText : errorMessage;
                alert('Erro ao enviar arquivo: ' + responseMessage);
            });

            this.on('sending', function(file, xhr, formData) {
                const data = new FormData(form);
                for (const pair of data.entries()) {
                    formData.append(pair[0], pair[1]);
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>