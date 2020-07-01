<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    protected const METHOD_PREFIX = 'when';

    public function handle(Request $request)
    {
        $payload = $request->all();
        var_dump($payload);
        return response("Unexpected value exception", 400);
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );
        } catch (\UnexpectedValueException $exception) {
            // Invalid payload
            return response("Unexpected value exception", 400);
            exit();
        } catch (SignatureVerificationException $exception) {
            // Invalid signature
            return response("Signature verification exception", 400);
            exit();
        }

        $method = $this->eventToMethod($payload['type']);
        if (method_exists($this, $method)) {
            $this->$method($payload);
        }

        return response("Webhook received");
    }

    public function whenInvoicePaid($payload)
    {
        var_dump($payload);
    }

    /**
     * transform stripe event into a method name.
     * invoice.paid => whenInvoicePaid
     */
    protected function eventToMethod($stripeEvent)
    {
        return static::METHOD_PREFIX . studly_case(str_replace('.', '_', $stripeEvent));
    }
}
