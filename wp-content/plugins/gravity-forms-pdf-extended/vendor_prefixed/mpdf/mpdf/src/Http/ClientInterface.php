<?php

namespace GFPDF_Vendor\Mpdf\Http;

use Psr\Http\Message\RequestInterface;
interface ClientInterface
{
    public function sendRequest(\Psr\Http\Message\RequestInterface $request);
}
