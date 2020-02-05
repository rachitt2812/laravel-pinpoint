<?php

namespace Codaptive\LaravelPinpoint;

use Aws\Pinpoint\PinpointClient;
use Codaptive\LaravelPinpoint\Transport\PinpointTransport;
use Illuminate\Mail\TransportManager;
use Illuminate\Support\Arr;

class PinpointTransportManager extends TransportManager
{
    protected function createPinpointDriver() {
        $config = array_merge($this->config->get('pinpoint', []), [
            'version' => 'latest',
        ]);

        return new PinpointTransport(
            new PinpointClient($this->addPinpointCredentials($config)),
            $config['options'] ?? []
        );
    }

    /**
     * Add the Pinpoint credentials to the configuration array.
     *
     * @param  array  $config
     * @return array
     */
    protected function addPinpointCredentials(array $config)
    {
        if (! empty($config['key']) && ! empty($config['secret'])) {
            $config['credentials'] = Arr::only($config, ['key', 'secret', 'token']);
        }

        return $config;
    }
}
