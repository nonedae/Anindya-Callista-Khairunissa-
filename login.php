<?php
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $conn = koneksiDB(); // Menggunakan fungsi asli milikmu
    
    // Cari user
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // ✅ PERBAIKAN: Gunakan password_verify() untuk cek hash
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'] ?? $user['username'];
            
            // Redirect berdasarkan role
            switch ($user['role']) {
                case 'admin':
                    redirect('admin/dashboard.php');
                    break;
                case 'wali_kelas':
                    redirect('wali/dashboard.php');
                    break;
                case 'siswa':
                    redirect('siswa/dashboard.php');
                    break;
                default:
                    redirect('index.php');
            }
        } else {
            $error = 'Password salah!';
        }
    } else {
        $error = 'Username tidak ditemukan!';
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
        }
        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .login-container {
            max-width: 420px;
            margin: 0 auto;
            width: 100%;
            padding: 15px;
        }
        .login-card {
            background: white;
            border-radius: 25px;
            padding: 35px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .school-header {
            text-align: center;
            margin-bottom: -25px;
        }
        .school-logo-container {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            padding: 5px;
        }
        .school-logo-container img {
            width: 85%;
            height: 85%;
            object-fit: contain;
        }
        .btn-login {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: transform 0.2s;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            color: white;
            opacity: 0.9;
        }
        .input-group-text {
            background-color: #f8f9fa;
            color: #6c757d;
            border-right: none;
        }
        .form-control {
            border-left: none;
            padding: 11px;
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #ced4da;
        }
        .info-text {
            margin-top: 25px;
            text-align: center;
            font-size: 0.85rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-card">
                <div class="school-header">
                    <div class="school-logo-container">
                        <img src="assets/images/logo.png" alt="Logo SMK" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'bi bi-building fs-1 text-white\'></i>';">
                    </div>
                    <h3 class="fw-bold mb-1">SMKN 1 DLANGGU</h3>
                    <p class="text-muted mb-3">Sistem Pembayaran SOPP</p>
                    <p class="small text-muted mb-4">Jl. A. Yani No 01, Ds. Pohkecik, Mojokerto</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle-fill me-2"></i><?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <!-- FORM LOGIN -->
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold">
                            <i class="bi bi-person"></i> Username
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person-badge"></i></span>
                            <input type="text" class="form-control" id="username" name="username" 
                                   placeholder="Masukkan username/nisn" required autofocus>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label fw-bold">
                            <i class="bi bi-lock"></i> Password
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Masukkan password" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Login
                    </button>
                </form>

                <div class="info-text">
                    <i class="bi bi-info-circle me-1"></i> 
                    SOPP: Sumbangan Operasional Pengembangan Pendidikan
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>