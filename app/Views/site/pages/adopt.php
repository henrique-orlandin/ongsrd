<?= $this->extend('site/layouts/main') ?>

<?= $this->section('head') ?>
<link rel="stylesheet" href="<?= base_url('assets/site/adopt.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
helper('pet');
$pets    = $pets    ?? [];
$filters = $filters ?? [];
$f       = static fn(string $k) => $filters[$k] ?? '';
$total   = count($pets);
?>

<section class="adopt-page">
    <div class="container">
        <header class="section-header">
            <div>
                <p class="section-tag">Adoção</p>
                <h2>Pets para Adoção</h2>
            </div>
        </header>
    </div>

    <form method="get" id="filter-form" action="">
        <div class="container adopt-layout">

            <!-- ─── Sidebar ─── -->
            <aside class="filters-sidebar">
                <div class="filters-header">
                    <h3><i class="fa-solid fa-sliders" aria-hidden="true"></i> Filtros</h3>
                    <?php if ($f('type') !== '' || $f('size') !== '' || $f('gender') !== '' || $f('age') !== ''): ?>
                        <a href="<?= base_url('adotar') ?>" class="clear-all">Limpar</a>
                    <?php endif; ?>
                </div>

                <!-- Type -->
                <details class="filter-group" <?= $f('type') !== '' ? 'open' : '' ?>>
                    <summary>
                        <span><i class="fa-solid fa-paw" aria-hidden="true"></i> Tipo</span>
                        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </summary>
                    <div class="filter-options">
                        <?php foreach ([''=>'Todos', 'Cachorro'=>'Cachorros', 'Gato'=>'Gatos'] as $val => $label): ?>
                            <label>
                                <input type="radio" name="type" value="<?= esc($val, 'attr') ?>"
                                       <?= $f('type') === $val ? 'checked' : '' ?>
                                       onchange="document.getElementById('filter-form').submit()">
                                <?= esc($label) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </details>

                <!-- Age -->
                <details class="filter-group" <?= $f('age') !== '' ? 'open' : '' ?>>
                    <summary>
                        <span><i class="fa-regular fa-calendar" aria-hidden="true"></i> Idade</span>
                        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </summary>
                    <div class="filter-options">
                        <?php foreach ([''=>'Todas as idades', 'filhote'=>'Filhote (até 1 ano)', 'jovem'=>'Jovem (1–3 anos)', 'adulto'=>'Adulto (3–8 anos)', 'idoso'=>'Idoso (8+ anos)'] as $val => $label): ?>
                            <label>
                                <input type="radio" name="age" value="<?= esc($val, 'attr') ?>"
                                       <?= $f('age') === $val ? 'checked' : '' ?>
                                       onchange="document.getElementById('filter-form').submit()">
                                <?= esc($label) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </details>

                <!-- Size -->
                <details class="filter-group" <?= $f('size') !== '' ? 'open' : '' ?>>
                    <summary>
                        <span><i class="fa-solid fa-ruler-horizontal" aria-hidden="true"></i> Porte</span>
                        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </summary>
                    <div class="filter-options">
                        <?php foreach ([''=>'Todos', 'P'=>'Pequeno (P)', 'M'=>'Médio (M)', 'G'=>'Grande (G)', 'GG'=>'Extra grande (GG)'] as $val => $label): ?>
                            <label>
                                <input type="radio" name="size" value="<?= esc($val, 'attr') ?>"
                                       <?= $f('size') === $val ? 'checked' : '' ?>
                                       onchange="document.getElementById('filter-form').submit()">
                                <?= esc($label) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </details>

                <!-- Gender -->
                <details class="filter-group" <?= $f('gender') !== '' ? 'open' : '' ?>>
                    <summary>
                        <span><i class="fa-solid fa-venus-mars" aria-hidden="true"></i> Sexo</span>
                        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
                    </summary>
                    <div class="filter-options">
                        <?php foreach ([''=>'Todos', 'M'=>'Macho', 'F'=>'Fêmea'] as $val => $label): ?>
                            <label>
                                <input type="radio" name="gender" value="<?= esc($val, 'attr') ?>"
                                       <?= $f('gender') === $val ? 'checked' : '' ?>
                                       onchange="document.getElementById('filter-form').submit()">
                                <?= esc($label) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </details>

                <button type="submit" class="apply-filters-btn">
                    <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> Buscar
                </button>
            </aside>

            <!-- ─── Results ─── -->
            <div class="pets-results">
                <div class="results-header">
                    <p class="results-count">
                        <strong><?= $total ?></strong> <?= $total === 1 ? 'pet disponível' : 'pets disponíveis' ?>
                    </p>
                    <label class="sort-control">
                        Ordenar por
                        <select name="sort" onchange="document.getElementById('filter-form').submit()">
                            <option value="recent"  <?= $f('sort') === 'recent'  ? 'selected' : '' ?>>Mais recentes</option>
                            <option value="name"    <?= $f('sort') === 'name'    ? 'selected' : '' ?>>Nome A–Z</option>
                            <option value="younger" <?= $f('sort') === 'younger' ? 'selected' : '' ?>>Mais novos</option>
                            <option value="older"   <?= $f('sort') === 'older'   ? 'selected' : '' ?>>Mais velhos</option>
                        </select>
                    </label>
                </div>

                <?php if ($pets !== []): ?>
                    <div class="pets-grid">
                        <?php foreach ($pets as $pet): ?>
                            <?php
                                $slug      = $pet['slug'] ?? '';
                                $detailUrl = base_url($slug !== '' ? 'adotar/' . $slug : 'adotar');
                                $genderLabel = $pet['gender'] === 'M' ? 'Macho' : 'Fêmea';
                                $ageLabel    = pet_age_label((int) $pet['age'], (string) ($pet['age_unit'] ?? 'years'));
                                $sizeLabels  = ['P'=>'Pequeno','M'=>'Médio','G'=>'Grande','GG'=>'Extra grande'];
                                $sizeLabel   = $sizeLabels[$pet['size']] ?? $pet['size'];
                            ?>
                            <article class="pet-card">
                                <a href="<?= esc($detailUrl, 'attr') ?>" class="pet-card-image" tabindex="-1" aria-hidden="true">
                                    <?php if (! empty($pet['thumbnail'])): ?>
                                        <img src="<?= base_url(esc((string) $pet['thumbnail'], 'attr')) ?>"
                                             alt="<?= esc((string) $pet['name']) ?>" loading="lazy">
                                    <?php else: ?>
                                        <div class="img-placeholder" aria-hidden="true"></div>
                                    <?php endif; ?>
                                    <span class="pet-card-name-overlay"><?= esc((string) $pet['name']) ?></span>
                                </a>
                                <div class="pet-card-body">
                                    <div class="pet-card-tags">
                                        <span class="pet-tag"><?= esc((string) $pet['type']) ?></span>
                                        <span class="pet-tag"><?= esc($genderLabel) ?></span>
                                        <span class="pet-tag"><?= esc($ageLabel) ?></span>
                                        <span class="pet-tag"><?= esc($sizeLabel) ?></span>
                                    </div>
                                    <a href="<?= esc($detailUrl, 'attr') ?>" class="pet-view-btn">Ver Perfil</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="adopt-empty">
                        <i class="fa-solid fa-heart-crack" aria-hidden="true"></i>
                        <p>Nenhum pet encontrado com esses filtros.</p>
                        <a href="<?= base_url('adotar') ?>" class="btn-primary" data-text="Ver todos">Ver todos</a>
                    </div>
                <?php endif; ?>
            </div>

        </div><!-- .adopt-layout -->
    </form>
</section>
<?= $this->endSection() ?>
