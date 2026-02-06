<?php
include 'config.php';

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password']; // ในงานจริงควรใช้ password_hash
    $role = $_POST['role'];

    // 1. ตรวจสอบ Email ซ้ำ
    $check_email = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check_email) > 0) {
        echo "<script>alert('Email นี้มีในระบบแล้ว');</script>";
    } else {
        // 2. บันทึกลงตาราง users
        $sql_user = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')";
        
        if (mysqli_query($conn, $sql_user)) {
            $user_id = mysqli_insert_id($conn); // ดึง ID ล่าสุดที่เพิ่งสร้าง
            
            // 3. บันทึกลงตารางแยกตาม Role ตาม Schema
            if ($role == 'student') {
                mysqli_query($conn, "INSERT INTO students (user_id, class_level) VALUES ('$user_id', 'Unassigned')");
            } else if ($role == 'teacher') {
                mysqli_query($conn, "INSERT INTO teachers (user_id, department) VALUES ('$user_id', 'General')");
            }
            
            echo "<script>alert('ลงทะเบียนสำเร็จ'); window.location='login.php';</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก - Education Platform</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            width: 100%;
            max-width: 480px;
        }

        .register-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 45px 40px;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .logo {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-icon {
            font-size: 48px;
            margin-bottom: 10px;
        }

        h2 {
            font-size: 28px;
            font-weight: 600;
            color: #667eea;
            text-align: center;
            margin-bottom: 10px;
        }

        .subtitle {
            text-align: center;
            color: #999;
            font-size: 14px;
            margin-bottom: 35px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 14px;
            font-weight: 500;
            color: #5a67d8;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0d9f0;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: white;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        select {
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg width='12' height='8' viewBox='0 0 12 8' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1.5L6 6.5L11 1.5' stroke='%235a67d8' stroke-width='2' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px;
        }

        input::placeholder {
            color: #bbb;
        }

        .role-info {
            font-size: 12px;
            color: #999;
            margin-top: -4px;
        }

        button[type="submit"] {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            margin-top: 10px;
        }

        button[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }

        button[type="submit"]:active {
            transform: translateY(0);
        }

        .divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e0d9f0;
        }

        .divider span {
            background: rgba(255, 255, 255, 0.95);
            padding: 0 15px;
            color: #999;
            font-size: 13px;
            position: relative;
        }

        .login-link {
            text-align: center;
            font-size: 14px;
            color: #666;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .required {
            color: #f5576c;
            margin-left: 4px;
        }

        .success-message,
        .error-message {
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            display: none;
        }

        .success-message {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .error-message {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .success-message.show,
        .error-message.show {
            display: block;
        }

        @media (max-width: 480px) {
            .register-card {
                padding: 35px 25px;
            }

            h2 {
                font-size: 24px;
            }

            .logo-icon {
                font-size: 40px;
            }
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .register-card {
            animation: fadeInUp 0.6s ease;
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="register-card">
            <div class="logo">
                <div class="logo-icon">📚</div>
                <h2>สมัครสมาชิก</h2>
                <p class="subtitle">Education Platform</p>
            </div>

            <!-- Success/Error message placeholder -->
            <div class="success-message" id="successMessage">
                ✅ สมัครสมาชิกสำเร็จ! กำลังพาไปหน้าเข้าสู่ระบบ...
            </div>
            <div class="error-message" id="errorMessage">
                ⚠️ อีเมลนี้ถูกใช้งานแล้ว
            </div>

            <form method="post">
                <div class="form-group">
                    <label>ชื่อ-นามสกุล <span class="required">*</span></label>
                    <input type="text" name="name" placeholder="กรอกชื่อ-นามสกุล" required>
                </div>

                <div class="form-group">
                    <label>อีเมล <span class="required">*</span></label>
                    <input type="email" name="email" placeholder="example@email.com" required>
                </div>

                <div class="form-group">
                    <label>รหัสผ่าน <span class="required">*</span></label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label>บทบาท <span class="required">*</span></label>
                    <select name="role" required>
                        <option value="student">👨‍🎓 นักเรียน</option>
                        <option value="teacher">👨‍🏫 ครู</option>
                        <option value="admin">🔧 ผู้ดูแลระบบ (Admin)</option>
                    </select>
                    <p class="role-info">เลือกบทบาทที่เหมาะสมกับคุณ</p>
                </div>

                <button type="submit" name="register">สมัครสมาชิก</button>
            </form>

            <div class="divider">
                <span>หรือ</span>
            </div>

            <p class="login-link">
                มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a>
            </p>
        </div>
    </div>
</body>

</html>