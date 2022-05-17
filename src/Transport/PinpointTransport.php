<?php

namespace Codaptive\LaravelPinpoint\Transport;

use Aws\Pinpoint\PinpointClient;
use Illuminate\Mail\Transport\Transport;
use Illuminate\Support\Facades\Log;
use Swift_Mime_SimpleMessage;

class PinpointTransport extends Transport
{
    /**
     * The Amazon Pinpoint instance.
     *
     * @var \Aws\Pinpoint\PinpointClient
     */
    protected $pinpoint;

    /**
     * The Amazon Pinpoint transmission options.
     *
     * @var array
     */
    protected $options = [];

    /**
     * Create a new Pinpoint transport instance.
     *
     * @param \Aws\Pinpoint\PinpointClient $pinpoint
     * @param array $options
     * @return void
     */
    public function __construct(PinpointClient $pinpoint, $options = [])
    {
        $this->pinpoint = $pinpoint;
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $toAddresses = $message->getTo();

        $addresses = [];

        foreach ($toAddresses as $emailAddress => $name) {
            $addresses[$emailAddress] = [
                'ChannelType' => 'EMAIL'
            ];
        }

        logger('Sending Pinpoint Email', [
            'Addresses' => $addresses,
            'Headers' => $message->getHeaders(),
        ]);
        
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');

        try {
            $result = $this->pinpoint->sendMessages(
                array_merge($this->options, [
                    'ApplicationId' => config('pinpoint.application_id'),
                    'MessageRequest' => [
                        'Addresses' => $addresses,
                        'MessageConfiguration' => [
                            'EmailMessage' => [
                                'FromAddress' => "${fromName} <${fromAddress}>",
                                'RawEmail' => [
                                    'Data' => $message->toString()
                                ]
                            ]
                        ]
                    ]
                ])
            );

            $resultData = $result->get('MessageResponse');

            Log::debug('Pinpoint Message Result', $resultData['Result']);

            $message->getHeaders()->addTextHeader('X-Pinpoint-Request-ID', $resultData['RequestId']);

            $this->sendPerformed($message);

            return $this->numberOfRecipients($message);
        } catch (\Exception $e) {
            report($e);

            return 0;
        }
    }

    /**
     * Get the Amazon Pinpoint client for the PinpointTransport instance.
     *
     * @return \Aws\Pinpoint\PinpointClient
     */
    public function Pinpoint()
    {
        return $this->pinpoint;
    }

    /**
     * Get the transmission options being used by the transport.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set the transmission options being used by the transport.
     *
     * @param  array  $options
     * @return array
     */
    public function setOptions(array $options)
    {
        return $this->options = $options;
    }
}
