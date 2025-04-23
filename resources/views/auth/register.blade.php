<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register | OranjeGarden</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #fff6f0;
    }
    .register-box {
      max-width: 400px;
      margin: auto;
      margin-top: 10vh;
      background-color: white;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 4px 20px rgba(255, 102, 0, 0.1);
    }
    .brand {
      color: #ff6600;
      font-weight: 700;
      font-size: 1.8rem;
      text-align: center;
      margin-bottom: 20px;
    }
    .btn-orange {
      background-color: #ff6600;
      color: white;
    }
    .btn-orange:hover {
      background-color: #e65c00;
    }
  </style>
</head>
<body>

  <div class="container">
    <div class="register-box">
      <div class="brand">OranjeGarden</div>
      <form action="{{ route('register.submit') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label for="name" class="form-label">Nama Lengkap</label>
          <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Kata Sandi</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
          <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>
        <div class="mb-3">
          <label for="role" class="form-label">Pilih Role</label>
          <select class="form-select" id="role" name="role" required>
            <option value="" disabled selected>Pilih Role</option>
            <option value="admin">Admin</option>
            <option value="delivery">Delivery</option>
          </select>
        </div>
        @if ($errors->any())
          <div class="alert alert-danger" role="alert">
            <ul>
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        <div class="d-grid">
          <button type="submit" class="btn btn-orange">Daftar</button>
        </div>
        <div class="text-center mt-3">
          <small>Sudah punya akun? <a href="/login" style="color:#ff6600;">Masuk di sini</a></small>
        </div>
      </form>
    </div>
  </div>

</body>
</html>