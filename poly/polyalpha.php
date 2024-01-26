
<?php
class Polyalphabetic {
    private $alph = 'abcdefghijklmnopqrstuvwxyz';

    public function encrypt($sourcetext, $key) {
        $code = '';
        $key_id = array();
        $t = 0;

        // mencari indeks huruf kunci
        for ($i = 0; $i < strlen($key); $i++) {
            $key_id[$i] = strpos($this->alph, $key[$i]);
        }

        for ($i = 0; $i < strlen($sourcetext); $i++) {
            // mencari simbol dalam alfabet
            if ($j = strpos($this->alph, $sourcetext[$i])) {
                if ($t > strlen($key) - 1) {
                    $t = 0;
                }
                $code .= $this->alph[($j + $key_id[$t]) % strlen($this->alph)];
                $t++;
            } else {
                $code .= $sourcetext[$i];
                $t++;
            }
        }

        return $code;
    }

    public function decrypt($sourcetext, $key) {
        $code = '';
        $key_id = array();
        $t = 0;

        // mencari indeks huruf kunci
        for ($i = 0; $i < strlen($key); $i++) {
            $key_id[$i] = strpos($this->alph, $key[$i]);
        }

        for ($i = 0; $i < strlen($sourcetext); $i++) {
            // mencari simbol dalam alfabet
            if ($j = strpos($this->alph, $sourcetext[$i])) {
                if ($t > strlen($key) - 1) {
                    $t = 0;
                }
                $code .= $this->alph[($j + strlen($this->alph) - $key_id[$t]) % strlen($this->alph)];
                $t++;
            } else {
                $code .= $sourcetext[$i];
                $t++;
            }
        }

        return $code;
    }
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "poly";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$cipher = new Polyalphabetic();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $text = $_POST["text"];
    $key = $_POST["key"];

    if (isset($_POST['encrypt'])) {
        $encrypted = $cipher->encrypt($text, $key);
        $decrypted = $cipher->decrypt($encrypted, $key);

        $sql = "INSERT INTO enkrip (encrypted_text, decrypted_text)
        VALUES ('$encrypted', '$decrypted')";

        if ($conn->query($sql) === TRUE) {
          echo "New record created successfully";
        } else {
          echo "Error: " . $sql . "<br>" . $conn->error;
        }

        echo "Encrypted text: " . $encrypted . "<br>";
        echo "Decrypted text: " . $decrypted . "<br>";
    }
     
}

$sql = "SELECT * FROM enkrip";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "id: " . $row["id"]. " - Encrypted Text: " . $row["encrypted_text"]. "<br>";
    }
} else {
    echo "0 results";
}

$conn->close();
?>
