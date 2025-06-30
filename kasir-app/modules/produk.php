<?php
session_start();
include("../config/db.php");

// Cek login
if (!isset($_SESSION['id_kasir'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil data produk
$produk = $koneksi->query("SELECT * FROM produk");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Data Produk</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: rgb(233, 204, 166);
        }

        h2 {
            margin-bottom: 20px;
        }

        a.btn {
            position: absolute;
            top: 15px;
            left: 20px;
            background: rgb(90, 57, 0);
            color: white;
            padding: 8px 11px;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        a.btn:hover {
            background: rgb(122, 79, 0);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        thead {
            background-color: #5A3900;
            color: white;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

    <a href="../home.php" class="btn">← Kembali</a>

    <h2>🍞 Data Produk</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Produk</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($p = $produk->fetch_assoc()): ?>
            <tr>
                <td><?= $p['id_produk'] ?></td>
                <td><?= htmlspecialchars($p['nama_produk']) ?></td>
                <td>Rp <?= number_format($p['harga']) ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

</body>
</html>
