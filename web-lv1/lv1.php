<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diplomski Radovi</title>
</head>
<body>

<?php
require 'vendor/autoload.php'; 
use simplehtmldom\HtmlWeb;

ini_set('max_execution_time', 300);
set_time_limit(300);

interface iRadovi {
    public function create($data); 
    public function save();
    public function read();
}

class DiplomskiRad implements iRadovi {
    private $_naziv_rada = NULL;
    private $_tekst_rada = NULL;
    private $_link_rada = NULL;
    private $_oib_tvrtke = NULL;

    public function __construct($data) {
        $this->_naziv_rada = $data['naziv_rada'] ?? null;
        $this->_tekst_rada = $data['tekst_rada'] ?? null;
        $this->_link_rada = $data['link_rada'] ?? null;
        $this->_oib_tvrtke = $data['oib_tvrtke'] ?? null;
    }

    public function create($data) {
        self::__construct($data);
    }

    public function save() {
        $servername = "localhost";
        $username = "root";
        $password = ""; 
        $dbname = "radovi";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            error_log("Connection failed: " . $conn->connect_error);
            return false;
        }

        $sql = "INSERT INTO `diplomski_radovi` (`naziv_rada`, `tekst_rada`, `link_rada`, `oib_tvrtke`) 
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", 
            $this->_naziv_rada, 
            $this->_tekst_rada, 
            $this->_link_rada, 
            $this->_oib_tvrtke
        );

        $result = $stmt->execute();
        if (!$result) {
            error_log("Error saving: " . $stmt->error);
        }

        $stmt->close();
        $conn->close();

        return $result;
    }

    public function read() {
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "radovi";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . htmlspecialchars($conn->connect_error));
        }

        $sql = "SELECT * FROM `diplomski_radovi`";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<p>
                        <strong>Naziv rada:</strong> {$row['naziv_rada']}<br>
                        <strong>Tekst rada:</strong> {$row['tekst_rada']}<br>
                        <strong>Link rada:</strong> <a href='{$row['link_rada']}' target='_blank'>{$row['link_rada']}</a><br>
                        <strong>OIB tvrtke:</strong> {$row['oib_tvrtke']}
                      </p>";
            }
        } else {
            echo "<p>Nema podataka u bazi.</p>";
        }

        $conn->close();
    }
}

$htmlWeb = new HtmlWeb();
for ($redni_broj = 2; $redni_broj <= 6; $redni_broj++) {
    $html = @$htmlWeb->load("https://stup.ferit.hr/index.php/zavrsni-radovi/page/{$redni_broj}/");
    
    if (!$html) continue;

    foreach ($html->find('article') as $article) {
        $link = $article->find('h2.entry-title a', 0);
        if ($link && !empty(trim($link->href))) {
            $postHtml = @$htmlWeb->load(trim($link->href));
            if (!$postHtml) continue;

            $textContent = trim($postHtml->find('.post-content', 0)->plaintext ?? "Tekst nije pronađen.");
            $imgElement = $article->find('ul.slides img', 0);
            $oib_tvrtke = '';
            if ($imgElement && $imgElement->src) {
                preg_match('/(\d{11})/', $imgElement->src, $matches);
                $oib_tvrtke = $matches[1] ?? '';
            }

            (new DiplomskiRad([
                'naziv_rada' => trim($link->plaintext),
                'tekst_rada' => $textContent,
                'link_rada' => trim($link->href),
                'oib_tvrtke' => $oib_tvrtke
            ]))->save();
        }
    }
}

echo "<h3>Podaci iz baze:</h3>";
(new DiplomskiRad([]))->read();
?>

</body>
</html>