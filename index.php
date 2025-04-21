<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

// Konfigurasi database MySQL
$servername  = "localhost";
$dbUsername  = "zaens";      
$dbPassword  = "zaens";      
$dbName      = "bukuDatabase"; 

// Buat koneksi ke database
$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbName);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// Tangani preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Ambil path dan id (jika ada)
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath   = str_replace(basename($scriptName), '', $scriptName);
$requestUri = $_SERVER['REQUEST_URI'];
if (($pos = strpos($requestUri, '?')) !== false) {
    $requestUri = substr($requestUri, 0, $pos);
}
if (strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}
$segments = array_values(array_filter(explode('/', trim($requestUri, '/'))));
$resource = $segments[0] ?? '';
$id       = isset($segments[1]) ? intval($segments[1]) : null;

// Helper: kirim JSON error
function send_error($code, $msg) {
    http_response_code($code);
    echo json_encode(["message" => $msg]);
    exit;
}

if ($resource === 'doc') {
    header("Content-Type: text/html");
    ?>
  <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="SwaggerUI" />
        <title>SwaggerUI</title>
        <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui.css" />
    </head>

    <body>
        <div id="swagger-ui"></div>
        <script src="https://unpkg.com/swagger-ui-dist@5.11.0/swagger-ui-bundle.js" crossorigin></script>
        <script>
        window.onload = () => {
            window.ui = SwaggerUIBundle({
            url: 'swagger.json',
            dom_id: '#swagger-ui',
            });
        };
        </script>
    </body>

    </html>
    <?php
    exit;
}

switch ($resource) {
    // =====================================================
    // ENTITAS BUKU
    // POST   /buku        → Insert
    // GET    /buku?id=   → Search by ID (via query param atau /buku/{id})
    // PUT    /buku/{id}   → Update
    // =====================================================
    case 'buku':
        // var_dump('buku');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            foreach (['judul','penulis','isbn','tahun_terbit','jumlah_stok'] as $f) {
                if (!isset($data[$f])) send_error(400, "Missing field $f");
            }
            $stmt = $conn->prepare("
                INSERT INTO buku (judul, penulis, isbn, tahun_terbit, jumlah_stok)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "sssii",
                $data['judul'],
                $data['penulis'],
                $data['isbn'],
                $data['tahun_terbit'],
                $data['jumlah_stok']
            );
            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(["message"=>"Buku added","id"=>$stmt->insert_id]);
            } else {
                send_error(500, "Failed to insert buku");
            }
            $stmt->close();

        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // id via query param atau path
            $q = $_GET['id'] ?? $id;
            // var_dump($q);
            if ($q) {
                $stmt = $conn->prepare("SELECT * FROM buku WHERE id = ?");
                $stmt->bind_param("i", $q);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    echo json_encode($row);
                } else {
                    send_error(404, "Buku not found");
                }
                $stmt->close();
            } else {
                // semua buku
                $res = $conn->query("SELECT * FROM buku");
                $out = [];
                while ($r = $res->fetch_assoc()) $out[] = $r;
                echo json_encode($out);
            }

        } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            if (!$id) send_error(400, "Parameter ID dibutuhkan");
            $data = json_decode(file_get_contents("php://input"), true);
            $fields = [];
            $types  = "";
            $values = [];
            foreach (['judul','penulis','isbn','tahun_terbit','jumlah_stok'] as $f) {
                if (isset($data[$f])) {
                    $fields[] = "$f = ?";
                    $values[] = $data[$f];
                    $types .= in_array($f, ['tahun_terbit','jumlah_stok']) ? "i" : "s";
                }
            }
            if (empty($fields)) send_error(400, "Tidak ada field untuk diupdate");
            $types .= "i";
            $values[] = $id;
            $sql = "UPDATE buku SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$values);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(["message"=>"Buku updated"]);
                } else {
                    send_error(404, "Buku tidak ditemukan atau data sama");
                }
            } else {
                send_error(500, "Update gagal");
            }
            $stmt->close();

        } else {
            send_error(405, "Method not allowed");
        }
        break;

    // =====================================================
    // ENTITAS ANGGOTA
    // POST   /anggota
    // GET    /anggota?id=
    // PUT    /anggota/{id}
    // =====================================================
    case 'anggota':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            foreach (['nama','email','alamat','tanggal_daftar'] as $f) {
                if (!isset($data[$f])) send_error(400, "Missing field $f");
            }
            $stmt = $conn->prepare("
                INSERT INTO anggota (nama, email, alamat, tanggal_daftar)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssss",
                $data['nama'],
                $data['email'],
                $data['alamat'],
                $data['tanggal_daftar']
            );
            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(["message"=>"Anggota added","id"=>$stmt->insert_id]);
            } else {
                send_error(500, "Failed to insert anggota");
            }
            $stmt->close();

        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $q = $_GET['id'] ?? $id;
            if ($q) {
                $stmt = $conn->prepare("SELECT * FROM anggota WHERE id = ?");
                $stmt->bind_param("i", $q);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    echo json_encode($row);
                } else {
                    send_error(404, "Anggota not found");
                }
                $stmt->close();
            } else {
                $res = $conn->query("SELECT * FROM anggota");
                $out = [];
                while ($r = $res->fetch_assoc()) $out[] = $r;
                echo json_encode($out);
            }

        } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            if (!$id) send_error(400, "Parameter ID dibutuhkan");
            $data = json_decode(file_get_contents("php://input"), true);
            $fields = []; $types=""; $values=[];
            foreach (['nama','email','alamat','tanggal_daftar'] as $f) {
                if (isset($data[$f])) {
                    $fields[] = "$f = ?";
                    $values[] = $data[$f];
                    $types .= "s";
                }
            }
            if (empty($fields)) send_error(400, "Tidak ada field untuk diupdate");
            $types .= "i"; $values[] = $id;
            $sql = "UPDATE anggota SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$values);
            if ($stmt->execute()) {
                if ($stmt->affected_rows>0) {
                    echo json_encode(["message"=>"Anggota updated"]);
                } else {
                    send_error(404, "Anggota tidak ditemukan atau data sama");
                }
            } else {
                send_error(500, "Update gagal");
            }
            $stmt->close();

        } else {
            send_error(405, "Method not allowed");
        }
        break;

    // =====================================================
    // ENTITAS PEMINJAMAN
    // POST   /peminjaman
    // GET    /peminjaman?id=
    // PUT    /peminjaman/{id}
    // =====================================================
    case 'peminjaman':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);
            foreach (['buku_id','anggota_id','tanggal_pinjam'] as $f) {
                if (!isset($data[$f])) send_error(400, "Missing field $f");
            }
            $stmt = $conn->prepare("
                INSERT INTO peminjaman (buku_id, anggota_id, tanggal_pinjam, status)
                VALUES (?, ?, ?, 'dipinjam')
            ");
            $stmt->bind_param(
                "iis",
                $data['buku_id'],
                $data['anggota_id'],
                $data['tanggal_pinjam']
            );
            if ($stmt->execute()) {
                http_response_code(201);
                echo json_encode(["message"=>"Peminjaman created","id"=>$stmt->insert_id]);
            } else {
                send_error(500, "Failed to insert peminjaman");
            }
            $stmt->close();

        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            $q = $_GET['id'] ?? $id;
            if ($q) {
                $stmt = $conn->prepare("SELECT * FROM peminjaman WHERE id = ?");
                $stmt->bind_param("i", $q);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($row = $res->fetch_assoc()) {
                    echo json_encode($row);
                } else {
                    send_error(404, "Peminjaman not found");
                }
                $stmt->close();
            } else {
                $res = $conn->query("SELECT * FROM peminjaman");
                $out = [];
                while ($r = $res->fetch_assoc()) $out[] = $r;
                echo json_encode($out);
            }

        } elseif ($_SERVER['REQUEST_METHOD'] === 'PUT') {
            if (!$id) send_error(400, "Parameter ID dibutuhkan");
            $data = json_decode(file_get_contents("php://input"), true);
            // Bisa update tanggal_kembali dan status
            $fields = []; $types=""; $values=[];
            if (isset($data['tanggal_kembali'])) {
                $fields[] = "tanggal_kembali = ?";
                $values[] = $data['tanggal_kembali'];
                $types .= "s";
            }
            if (isset($data['status'])) {
                $fields[] = "status = ?";
                $values[] = $data['status'];
                $types .= "s";
            }
            if (empty($fields)) send_error(400, "Tidak ada field untuk diupdate");
            $types .= "i"; $values[] = $id;
            $sql = "UPDATE peminjaman SET " . implode(", ", $fields) . " WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($types, ...$values);
            if ($stmt->execute()) {
                if ($stmt->affected_rows>0) {
                    echo json_encode(["message"=>"Peminjaman updated"]);
                } else {
                    send_error(404, "Peminjaman tidak ditemukan atau data sama");
                }
            } else {
                send_error(500, "Update gagal");
            }
            $stmt->close();

        } else {
            send_error(405, "Method not allowed");
        }
        break;
    default:
        send_error(404, "Resource tidak ditemukan");
}
