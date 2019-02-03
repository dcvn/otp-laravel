<?php

namespace Dcvn\Otp;

use RobThree\Auth\Providers\Qr\IQRCodeProvider;

/**
 * Local generated QRCode, without the need for an online service.
 */
class QrcodeProvider implements IQRCodeProvider
{
    public function getQRCodeImage(/* string */ $qrtext, /* int */ $size = null)
    {
        // Outer margin of the QR.
        $margin = 3;

        // For phpqrcode, $size means pixels per point.
        if (is_null($size)) {
            $size = (int) config('otp.pixelspp');
        }

        // The image call sends to output, catching that in the output buffer.
        ob_start();

        \QRCode::png($qrtext, null, \QR_ECLEVEL_L, $size, $margin);

        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }

    public function getMimeType()
    {
        return 'image/png';
    }
}
