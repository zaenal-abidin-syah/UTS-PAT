# Library Management API

API ini menyediakan endpoint untuk mengelola buku, anggota, dan peminjaman di perpustakaan.

## Daftar Endpoint

| Method | URL                   | Deskripsi                                                  |
| ------ | --------------------- | ---------------------------------------------------------- |
| POST   | `/buku`               | Menambahkan buku baru                                      |
| GET    | `/buku?id={id}`       | Mengambil detail buku berdasarkan `id`                     |
| PUT    | `/buku/{id}`          | Memperbarui data buku                                      |
| POST   | `/anggota`            | Menambahkan anggota baru                                   |
| GET    | `/anggota?id={id}`    | Mengambil detail anggota berdasarkan `id`                  |
| PUT    | `/anggota/{id}`       | Memperbarui data anggota                                   |
| POST   | `/peminjaman`         | Membuat transaksi peminjaman (status awal `'dipinjam'`)    |
| GET    | `/peminjaman?id={id}` | Mengambil detail transaksi peminjaman                      |
| PUT    | `/peminjaman/{id}`    | Memperbarui transaksi (misal: `status`, `tanggal_kembali`) |

## Contoh Request & Response

### 1. Tambah Buku

Request

```
POST /buku
Content-Type: application/json

{
  "judul": "Laskar Pelangi",
  "penulis": "Andrea Hirata",
  "isbn": "9786020243808",
  "tahun_terbit": 2005,
  "jumlah_stok": 5
}
```

Response

```json
HTTP/1.1 201 Created
{
  "message": "Buku added",
  "id": 1
}
```

### 2. Cari Buku

Request

```
GET /buku?id=1
```

Response

```json
HTTP/1.1 200 OK
{
  "id": 1,
  "judul": "Laskar Pelangi",
  "penulis": "Andrea Hirata",
  "isbn": "9786020243808",
  "tahun_terbit": 2005,
  "jumlah_stok": 5
}
```

### 3. Update Buku

Request

```
PUT /buku/1
Content-Type: application/json

{
  "jumlah_stok": 6
}
```

Response

```json
HTTP/1.1 200 OK
{
  "message": "Buku updated"
}
```
