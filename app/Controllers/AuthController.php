<?php

require_once __DIR__ . '/../../core/Controller.php';

class AuthController extends Controller {

    // Menampilkan halaman login
    public function index() {
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . 'index.php?url=account');
            exit;
        }

        $data = [
            'title' => 'Login',
            'error' => '',
            'success' => ''
        ];

        if (isset($_SESSION['login_error'])) {
            $data['error'] = $_SESSION['login_error'];
            unset($_SESSION['login_error']);
        }

        if (isset($_SESSION['login_success'])) {
            $data['success'] = $_SESSION['login_success'];
            unset($_SESSION['login_success']);
        }

        $this->viewOnly('auth/login', $data);
    }



    // Memproses login
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . 'index.php?url=auth');
            exit;
        }

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        // 1. Ambil input captcha dari form
        $kodecaptcha = trim($_POST['kodecaptcha'] ?? '');

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Email dan password wajib diisi.';
            header('Location: ' . BASE_URL . 'index.php?url=auth');
            exit;
        }

        // 2. VALIDASI CAPTCHA
        // Cek apakah session captcha ada dan apakah input user sama dengan yang di session
        if (!isset($_SESSION['code']) || $kodecaptcha !== $_SESSION['code']) {
            $_SESSION['login_error'] = 'Kode Captcha salah atau kadaluarsa.';
            header('Location: ' . BASE_URL . 'index.php?url=auth');
            exit;
        }

        // Hapus session captcha setelah dicek agar tidak bisa dipakai 2 kali (opsional tapi disarankan)
        unset($_SESSION['code']);

        // 3. JIKA CAPTCHA BENAR, LANJUT KE DATABASE
        $accountModel = $this->model('Account');
        $account = $accountModel->findByEmail($email);

        if (!$account) {
            $_SESSION['login_error'] = 'Email atau password salah.';
            header('Location: ' . BASE_URL . 'index.php?url=auth');
            exit;
        }

        // Verifikasi password (bcrypt hash)
        $isPasswordValid = false;
        if (password_verify($password, $account['password'])) {
            $isPasswordValid = true;
        } elseif ($password === $account['password']) {
            $isPasswordValid = true;
        }

        if (!$isPasswordValid) {
            $_SESSION['login_error'] = 'Email atau password salah.';
            header('Location: ' . BASE_URL . 'index.php?url=auth');
            exit;
        }

        // Simpan sesi user
        $_SESSION['user_id'] = $account['id'];
        $_SESSION['user_name'] = $account['name'];
        $_SESSION['user_email'] = $account['email'];
        $_SESSION['user_account_type'] = $account['account_type_name'] ?? 'User';

        header('Location: ' . BASE_URL . 'index.php?url=account');
        exit;
    }

    // Memproses logout
    public function logout() {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_account_type']);
        session_destroy();

        header('Location: ' . BASE_URL . 'index.php?url=auth');
        exit;
    }
}