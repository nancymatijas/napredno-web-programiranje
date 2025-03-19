<?php
session_start();
function encrypt_file($inputFile, $outputFile) {
    $encryption_key = md5('jed4n j4k0 v3l1k1 kljuc'); 
    $cipher = "AES-128-CTR";
    $iv_length = openssl_cipher_iv_length($cipher);
    $options = 0; 
    $iv = openssl_random_pseudo_bytes($iv_length);

    $data = file_get_contents($inputFile);
    $encrypted_data = openssl_encrypt($data, $cipher, $encryption_key, $options, $iv);

    file_put_contents('zad2_files/' . $outputFile, $iv . $encrypted_data);
}

function decrypt_file($inputFile, $outputFile) {
    $decryption_key = md5('jed4n j4k0 v3l1k1 kljuc'); 
    $cipher = "AES-128-CTR";
    $options = 0; 

    $data = file_get_contents('zad2_files/' . $inputFile);
    $iv_length = openssl_cipher_iv_length($cipher);
    $iv = substr($data, 0, $iv_length);
    $encrypted_data = substr($data, $iv_length);
    
    $decrypted_data = openssl_decrypt($encrypted_data, $cipher, $decryption_key, $options, $iv);
    file_put_contents('zad2_files/' . $outputFile, $decrypted_data);
}

// Enkripcija
if(isset($_POST['submit']) && isset($_FILES['file'])) {
    $_SESSION['latest_file'] = [
        'encrypted' => $enc = 'encrypted_'.bin2hex(random_bytes(4)).'.enc',
        'original' => $_FILES['file']['name']
    ];
    encrypt_file($_FILES['file']['tmp_name'], $enc);
    echo "Datoteka šifrirana.";
}

// Dekripcija
if(isset($_POST['decrypt'])) {
    if($file = $_SESSION['latest_file'] ?? null) {
        decrypt_file($file['encrypted'], 'decrypted_'.$file['original']);
        echo "Datoteka uspješno dešifrirana i preuzeta.";
    } else {
        echo "Nema dostupne datoteke!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zad2</title>
</head>
<body>

    <h2>Šifriranje i dešifriranje datoteka</h2>

    <form method="post" enctype="multipart/form-data">
        <h4>Odaberite datoteku:</h4>
        <input type="file" name="file" id="file"><br><br>
        <input type="submit" value="Prenesi i šifriraj" name="submit"><br><br>
    </form>

    <form method="post">
        <input type="submit" value="Dešifriraj i preuzmi" name="decrypt">
    </form>

</body>
</html>
