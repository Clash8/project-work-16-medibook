<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MediBook &middot; Documentazione API</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui.css">
    <style>
        body { margin: 0; background: #fafafa; }
        .topbar { display: none; }
        header.medibook {
            background: #0f766e;
            color: #fff;
            padding: 1.25rem 2rem;
            font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
        }
        header.medibook h1 { margin: 0; font-size: 1.25rem; }
        header.medibook p { margin: 0.35rem 0 0; opacity: 0.85; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header class="medibook">
        <h1>MediBook &mdash; Documentazione delle API</h1>
        <p>Specifica OpenAPI 3.0 degli endpoint REST per la prenotazione di visite e la gestione dei referti.</p>
    </header>

    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist@5.17.14/swagger-ui-bundle.js"></script>
    <script>
        window.onload = () => {
            SwaggerUIBundle({
                url: '{{ asset('docs/openapi.yaml') }}',
                dom_id: '#swagger-ui',
                deepLinking: true,
                persistAuthorization: true,
                docExpansion: 'list',
            });
        };
    </script>
</body>
</html>
