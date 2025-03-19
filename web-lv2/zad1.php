<?php 

$db_name = 'crud';

$dir = "backup/$db_name";

if (!is_dir($dir)) {
    if (!@mkdir($dir)) {
        die("<p>Ne možemo stvoriti direktorij $dir.</p></body></html>");
    }
}

$time = time();

$dbc = @mysqli_connect('localhost', 'root', '', $db_name) OR die("<p>Ne možemo se spojiti na bazu $db_name.</p></body></html>");

$r = mysqli_query($dbc, 'SHOW TABLES');

if (mysqli_num_rows(result: $r) > 0) {

    echo "<p>Backup za bazu podataka '$db_name'.</p>";
    
    while (list($table) = mysqli_fetch_array($r, MYSQLI_NUM)) {

        $q = "SELECT * FROM $table";
        $r2 = mysqli_query($dbc, $q);
        
        if (mysqli_num_rows($r2) > 0) {

            if ($fp = gzopen("$dir/{$table}_{$time}.sql.gz", 'w9')) {
                
                $columns = array();
                $fields = mysqli_fetch_fields($r2);
                foreach ($fields as $field) {
                    if ($field->flags & MYSQLI_PRI_KEY_FLAG) continue;
                    $columns[] = $field->name;
                }
                $columns_str = implode(", ", $columns);
                
                while ($row = mysqli_fetch_array($r2, MYSQLI_NUM)) {
                    $values = array();
                    
                    foreach ($row as $key => $value) {
                        if ($fields[$key]->flags & MYSQLI_PRI_KEY_FLAG) continue; 
                        $value = addslashes($value);
                        $values[] = "'$value'";
                    }
                    $values_str = implode(", ", $values);
                    
                    gzwrite($fp, "INSERT INTO $table ($columns_str)\nVALUES ($values_str);\n");
                }

                gzclose($fp);
                
                echo "<p>Tablica '$table' je pohranjena.</p>";
            } else {
                echo "<p>Datoteka $dir/{$table}_{$time}.sql.gz se ne može otvoriti.</p>";
                break; 
            } 
        } 
    }
} else {
    echo "<p>Baza $db_name ne sadrži tablice.</p>";
}

mysqli_close($dbc);
?>