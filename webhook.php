<?php
// Ce fichier est appelé automatiquement par les serveurs de CinetPay (pas par le client)
$apiKey = "VOTRE_API_KEY_CINETPAY";
$siteId = "VOTRE_SITE_ID_CINETPAY";

if (isset($_POST['cpm_trans_id'])) {
    $transactionId = $_POST['cpm_trans_id'];

    // Vérification officielle du statut auprès de CinetPay (sécurité anti-fraude)
    $checkData = [
        "apikey" => $apiKey, "site_id" => $siteId,
        "transaction_id" => $transactionId
    ];

    $ch = curl_init('https://cinetpay.com');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($checkData));

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result['code']) && $result['code'] === '00') {

        // PAIEMENT RÉUSSI : L'argent est crédité sur votre compte CinetPay !
        $montantPaye = $result['data']['amount'];
        $devise = $result['data']['currency'];
        $moyenPaiement = $result['data']['payment_method'];

        // Enregistrement dans un fichier journal (ou votre base de données MySQL)
        $log = date('Y-m-d H:i:s') . " | SUCCÈS | Cmd: $transactionId | $montantPaye $devise via $moyenPaiement\n";
        file_put_contents("commandes_payees.txt", $log, FILE_APPEND);
    }
}
?>
