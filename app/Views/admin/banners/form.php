<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $item = $item ?? null; ?>
<?php $requiresImage = $item === null; ?>
<section class="panel form-panel">
    <h2><i class="fa-solid fa-images"></i><?= $item ? 'Editar Banner' : 'Novo Banner' ?></h2>
    <form class="dropzone upload-image" method="post" enctype="multipart/form-data" action="<?= $item ? base_url('cms/banners/update/' . $item['id']) : base_url('cms/banners/create') ?>">
        <?= csrf_field() ?>
        <label>Titulo (opcional)
            <input type="text" name="title" maxlength="180" value="<?= esc(old('title', $item['title'] ?? '')) ?>">
        </label>
        <label>Link (opcional)
            <input type="text" name="link_url" maxlength="255" value="<?= esc(old('link_url', $item['link_url'] ?? '')) ?>" placeholder="https://example.com ou /adopt">
        </label>
        <label>Ordem
            <input type="number" name="sort_order" value="<?= esc((string) old('sort_order', $item['sort_order'] ?? 0)) ?>">
        </label>
        <label class="checkbox-inline">
            <input type="checkbox" name="is_active" value="1" <?= (int) old('is_active', $item['is_active'] ?? 1) === 1 ? 'checked' : '' ?>>
            Banner ativo
        </label>
        <label>Imagem do Banner
            <div id="banner-dropzone" class="upload-dropzone my-dropzone">
                <div class="dz-message"><i class="fa-solid fa-cloud-arrow-up"></i> Solte a imagem do banner aqui ou clique para enviar.</div>
            </div>
        </label>
        <?php if (! empty($item['image'])): ?>
            <img src="<?= base_url((string) $item['image']) ?>" class="preview" alt="Banner atual">
        <?php endif; ?>
        <div class="actions"><button class="btn" type="submit"><i class="fa-solid fa-floppy-disk"></i>Salvar</button></div>
    </form>
</section>
<script>
    Dropzone.autoDiscover = false;

    const dropzoneSelector = '#banner-dropzone';
    const form = document.querySelector(dropzoneSelector).closest('form');
    const requiresImage = <?= $requiresImage ? 'true' : 'false' ?>;
    let skipDropzoneSubmit = false;

    new Dropzone(dropzoneSelector, {
        url: form.getAttribute('action'),
        autoProcessQueue: false,
        maxFiles: 1,
        acceptedFiles: 'image/*',
        paramName: 'image',
        addRemoveLinks: true,
        init: function() {
            form.addEventListener('submit', (e) => {
                if (skipDropzoneSubmit) {
                    return;
                }

                e.preventDefault();
                e.stopPropagation();

                if (this.getQueuedFiles().length <= 0) {
                    if (requiresImage) {
                        alert('Selecione uma imagem para enviar.');
                        return;
                    }

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

            this.on('addedfile', function() {
                if (this.files.length > 1) {
                    this.removeFile(this.files[0]);
                }
                document.querySelector(dropzoneSelector).querySelector('.dz-message').style.display = 'none';
            });

            this.on('removedfile', function() {
                if (this.files.length === 0) {
                    document.querySelector(dropzoneSelector).querySelector('.dz-message').style.display = 'block';
                }
            });

            this.on('success', function(file, response) {
                if (response.success) {
                    window.location.href = "<?= base_url('cms/banners') ?>";
                    return;
                }

                alert('Erro ao enviar imagem: ' + (response.message || 'Erro desconhecido'));
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
