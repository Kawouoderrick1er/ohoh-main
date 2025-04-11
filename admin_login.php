<?php
session_start();

// Connexion à la base de données
try {
    $conn = new PDO("mysql:host=localhost;dbname=formation_professionnelle", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérification du nombre d'administrateurs
    $sql = "SELECT COUNT(*) FROM administrateurs";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $admin_count = $stmt->fetchColumn();

    // Si aucun administrateur n'est enregistré, redirigez vers le tableau de bord
    if ($admin_count == 0) {
        $_SESSION['admin_id'] = 0; // Identifiant fictif pour accès libre
        header("Location: admin_dashboard.php");
        exit();
    }
} catch (PDOException $e) {
    $message = "Erreur de connexion à la base de données: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT * FROM administrateurs WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['mot_de_passe'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['nom'];
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $message = "Email ou mot de passe incorrect.";
        }
    } catch (PDOException $e) {
        $message = "Erreur de connexion à la base de données: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Administrateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f0f0f0;
        }
        .login-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
        .login-container input, .login-container button {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .login-container button {
            background-color: #007bff;
            color: white;
            border: none;
            transition: background-color 0.3s ease-in-out;
        }
        .login-container button:hover {
            background-color: #0056b3;
        }
        .message {
            color: red;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Connexion Administrateur</h2>
        <?php if (isset($message)): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>
        <form action="admin_login.php" method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Connexion</button>
        </form>
    </div>
</body>
</html>