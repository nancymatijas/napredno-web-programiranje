<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zad3</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        h1 {
            font-size: 2rem;
            font-weight: bold;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .card img {
            height: 100px; 
            object-fit: cover;
            margin: 10px auto;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Profili osoba</h1>
        <div class="row justify-content-center">
            
            <?php
            $xml = simplexml_load_file('LV2.xml');

            foreach ($xml->record as $record) {
                echo "<div class='col-md-4 mb-4'>";
                echo "<div class='card h-100 text-center'>";

                if (isset($record->slika)) {
                    echo "<img src='{$record->slika}' alt='Profilna slika' class='img-fluid'>";
                }

                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>{$record->ime} {$record->prezime}</h5>";
                echo "<p class='card-text'><strong>Email:</strong> {$record->email}</p>";
                echo "<p class='card-text'><strong>Spol:</strong> {$record->spol}</p>";

                if (isset($record->zivotopis)) {
                    echo "<h6>Životopis</h6>";
                    echo "<p style='font-size: 0.8em;'>{$record->zivotopis}</p>";
                }
                echo "</div>";
                echo "</div>"; 
                echo "</div>"; 
            }
            ?>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
