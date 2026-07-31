<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= base_url('assets/icon.png') ?>" type="image/png">
    <title><?= esc($seo['title'] ?? 'ONG SRD') ?></title>
    <meta name="description" content="<?= esc($seo['description'] ?? 'Site oficial da ONG SRD.') ?>">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="<?= current_url() ?>">

    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= esc($seo['title'] ?? 'ONG SRD') ?>">
    <meta property="og:description" content="<?= esc($seo['description'] ?? 'Site oficial da ONG SRD.') ?>">
    <meta property="og:url" content="<?= current_url() ?>">
    <meta property="og:image" content="<?= base_url('assets/logo.png') ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= esc($seo['title'] ?? 'ONG SRD') ?>">
    <meta name="twitter:description" content="<?= esc($seo['description'] ?? 'Site oficial da ONG SRD.') ?>">
    <meta name="twitter:image" content="<?= base_url('assets/logo.png') ?>">

    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "NGO",
            "name": "ONG SRD",
            "url": "<?= base_url('/') ?>",
            "logo": "<?= base_url('assets/logo.png') ?>",
            "address": {
                "@type": "PostalAddress",
                "streetAddress": "Sarmento Leite, 3090",
                "postalCode": "95084-000",
                "addressLocality": "Caxias do Sul",
                "addressRegion": "RS",
                "addressCountry": "BR"
            },
            "sameAs": [
                "https://www.instagram.com/ongsrdoficial/",
                "https://www.facebook.com/ongsemracadefinidacxs/?locale=pt_BR"
            ]
        }
    </script>

    <script src="https://kit.fontawesome.com/b0a281f7df.js" async="true" crossorigin="anonymous"></script>

    <link rel="stylesheet" href="<?= base_url('assets/site/site.css') ?>">
    <?= $this->renderSection('head') ?>
</head>

<body>
    <?= $this->include('site/partials/header') ?>

    <main id="conteudo-principal">
        <?= $this->renderSection('content') ?>
    </main>

    <?= $this->include('site/partials/footer') ?>
    <script src="<?= base_url('assets/site/site.js') ?>" defer></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>