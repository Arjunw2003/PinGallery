<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinterest Gallery</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .masonry-grid {
            column-count: 4;
            column-gap: 1.25rem;
        }
        .masonry-item {
            display: inline-block;
            width: 100%;
            break-inside: avoid;
            margin-bottom: 1.25rem;
        }
        .pin-card img {
            width: 100%;
            height: auto;
            display: block;
            object-fit: cover;
        }
        @media (max-width: 1199px) {
            .masonry-grid { column-count: 3; }
        }
        @media (max-width: 767px) {
            .masonry-grid { column-count: 2; }
        }
        @media (max-width: 575px) {
            .masonry-grid { column-count: 1; }
        }
    </style>
</head>
<body>