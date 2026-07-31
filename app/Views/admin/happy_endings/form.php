<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>
<?php $item = $item ?? null; ?>
<?php $requiresImage = $item === null; ?>
<section class="panel form-panel">
    <h2><i class="fa-solid fa-heart"></i><?= $item ? 'Editar Final Feliz' : 'Novo Final Feliz' ?></h2>
    <form class="dropzone upload-image" method="post" enctype="multipart/form-data" action="<?= $item ? base_url('cms/happy-endings/update/' . $item['id']) : base_url('cms/happy-endings/create') ?>">
        <?= csrf_field() ?>
        <label> Título <input type="text" name="title" value="<?= esc(old('title', $item['title'] ?? '')) ?>" required></label>
        <label> Descrição <textarea name="description" rows="6" required><?= esc(old('description', $item['description'] ?? '')) ?></textarea></label>
        <label>Imagem
            <div id="happy-dropzone" class="upload-dropzone my-dropzone">
                <div class="dz-message"><i class="fa-solid fa-cloud-arrow-up"></i> Solte uma imagem aqui ou clique para enviar.</div>
            </div>
        </label>
        <?php if (! empty($item['image'])): ?><img src="<?= base_url($item['image']) ?>" class="preview" alt=""><?php endif; ?>
        <div class="actions"><button class="btn" type="submit"><i class="fa-solid fa-floppy-disk"></i>Salvar</button></div>
    </form>
</section>
<script>
    Dropzone.autoDiscover = false;

    const dropzoneSelector = '#happy-dropzone';
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
                    window.location.href = "<?= base_url('cms/happy-endings') ?>";
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
