<?php
// app/services/MomoPaymentService.php
namespace App\Services;

class MomoPaymentService
{
    private $partnerCode;
    private $accessKey;
    private $secretKey;
    private $endPoint = "https://test-payment.momo.vn/gw_payment/transactionProcessor";
    private $redirectUrl;
    private $ipnUrl;

    public function __construct()
    {
        // Thông tin tài khoản test MoMo
        $this->partnerCode = "MOMO_PARTNER_CODE"; // Thay thế bằng partner code thật
        $this->accessKey = "MOMO_ACCESS_KEY"; // Thay thế bằng access key thật
        $this->secretKey = "MOMO_SECRET_KEY"; // Thay thế bằng secret key thật
        $this->redirectUrl = APP_URL . "/payments/momo-redirect";
        $this->ipnUrl = APP_URL . "/payments/momo-ipn";
    }

    public function createPayment($orderId, $amount, $orderInfo, $extraData = "")
    {
        $requestId = time() . "";
        $rawHash = "partnerCode=" . $this->partnerCode .
            "&accessKey=" . $this->accessKey .
            "&requestId=" . $requestId .
            "&amount=" . $amount .
            "&orderId=" . $orderId .
            "&orderInfo=" . $orderInfo .
            "&returnUrl=" . $this->redirectUrl .
            "&notifyUrl=" . $this->ipnUrl .
            "&extraData=" . $extraData;

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'accessKey' => $this->accessKey,
            'requestId' => $requestId,
            'amount' => (string)$amount,
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'returnUrl' => $this->redirectUrl,
            'notifyUrl' => $this->ipnUrl,
            'extraData' => $extraData,
            'requestType' => 'captureMoMoWallet',
            'signature' => $signature
        ];

        $result = $this->execPostRequest($this->endPoint, json_encode($data));
        return json_decode($result, true);
    }

    public function verifyPayment($requestId, $orderId, $amount, $transId, $requestType)
    {
        $rawHash = "partnerCode=" . $this->partnerCode .
            "&accessKey=" . $this->accessKey .
            "&requestId=" . $requestId .
            "&orderId=" . $orderId .
            "&amount=" . $amount .
            "&transId=" . $transId .
            "&requestType=" . $requestType;

        $signature = hash_hmac("sha256", $rawHash, $this->secretKey);

        $data = [
            'partnerCode' => $this->partnerCode,
            'accessKey' => $this->accessKey,
            'requestId' => $requestId,
            'orderId' => $orderId,
            'amount' => $amount,
            'transId' => $transId,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        $result = $this->execPostRequest($this->endPoint, json_encode($data));
        return json_decode($result, true);
    }

    private function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $ch,
            CURLOPT_HTTPHEADER,
            array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            )
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        $result = curl_exec($ch);

        curl_close($ch);
        return $result;
    }
}
