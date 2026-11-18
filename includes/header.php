<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - Pinterest Style Gallery</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        /* Pinterest Masonry Grid */
        .masonry-grid {
            column-count: 4;
            column-gap: 16px;
        }
        @media (max-width: 1200px) { .masonry-grid { column-count: 3; } }
        @media (max-width: 900px) { .masonry-grid { column-count: 2; } }
        @media (max-width: 600px) { .masonry-grid { column-count: 1; } }

        .masonry-item {
            break-inside: avoid;
            margin-bottom: 16px;
        }
        .pin-card {
            border-radius: 16px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .pin-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }
        .pin-card img {
            width: 100%;
            height: auto;
            display: block;
        }
        .pin-body {
            padding: 12px 16px;
        }
        .pin-title {
            font-weight: 600;
            font-size: 0.95rem;
            color: #111;
        }
        .pin-desc {
            color: #555;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>