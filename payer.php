<?php
// 1. Vos identifiants marchands (à récupérer sur votre tableau de bord CinetPay)
$apiKey = "VOTRE_API_KEY_CINETPAY";
$siteId = "VOTRE_SITE_ID_CINETPAY";

// 2. Récupération et calcul sécurisé du montant côté serveur
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nom = htmlspecialchars($_POST['clientName']);
    $email = htmlspecialchars($_POST['clientEmail']);
    $telephone = htmlspecialchars($_POST['clientPhone']);
    $adresse = htmlspecialchars($_POST['clientAddress']);
    $prixUnitaire = floatval($_POST['productPrice']);
    $quantite = intval($_POST['quantity']);
    $montantTotal = $prixUnitaire * $quantite;

    // Génération d'un numéro de commande unique
    $transactionId = "CMD-" . time() . "-" . rand(100, 999);

    // 3. Préparation des données pour l'API CinetPay v2
    $data = [
        "apikey" => $apiKey,
        "site_id" => $siteId,
        "transaction_id" => $transactionId,
        "amount" => $montantTotal,
        "currency" => "USD", // Utilisez "CDF" si vous facturez en Francs Congolais
        "description" => "Achat sur KinStyle & Beauté ($quantite article(s))",
        "return_url" => "https://votre-domaine.com", // Page après paiement
        "notify_url" => "https://votre-domaine.com", // URL de confirmation serveur
        "channels" => "ALL", // Active Mobile Money (M-Pesa, Airtel, Orange) ET Cartes bancaires
        "lang" => "fr",
        // Informations requises pour le déblocage des cartes Visa/Mastercard
        "customer_name" => $nom,
        "customer_surname" => $nom,
        "customer_email" => $email,
        "customer_phone_number" => $telephone,
        "customer_address" => $adresse,
        "customer_city" => "Kinshasa",
        "customer_country" => "CD" // Code ISO de la RDC
    ];
    // 4. Envoi de la requête HTTP POST à CinetPay
    $ch = curl_init('https://api-checkout.cinetpay.com/v2/payment');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    // 5. Redirection du client vers le guichet de paiement sécurisé
    if (isset($result['code']) && $result['code'] === '201') {
        $paymentUrl = $result['data']['payment_url'];
        header("Location: " . $paymentUrl);
        exit();
    } else {
        echo "Erreur lors de l'initialisation du paiement : " . ($result['message'] ?? 'Erreur inconnue');
    }
}
?>
