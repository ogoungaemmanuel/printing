<?php

declare(strict_types=1);

namespace Xslain\Printing\Enums;

use Xslain\Printing\Exceptions\InvalidDriverConfig;

/**
 * Printing drivers supported by the package.
 */
enum PrintDriver: string
{
    case PrintNode = 'printnode';
    case Cups = 'cups';
    case Network = 'network';
    case Usb = 'usb';
    case Raw = 'raw';

    public function ensureConfigIsValid(array $config): void
    {
        $method = 'validate' . ucfirst($this->value) . 'Config';

        $this->{$method}($config);
    }

    protected function validatePrintnodeConfig(array $config): void
    {
        $key = data_get($config, 'key');

        // We'll attempt to fall back on the static PrintNode::$apiKey value later.
        if ($key === null) {
            return;
        }

        throw_if(
            blank($key),
            InvalidDriverConfig::invalid('You must provide an api key for the PrintNode driver.'),
        );
    }

    protected function validateCupsConfig(array $config): void
    {
        $ip = data_get($config, 'ip');
        throw_if(
            $ip !== null && blank($ip),
            InvalidDriverConfig::invalid('An IP address is required for the CUPS driver.'),
        );

        $secure = data_get($config, 'secure');
        throw_if(
            $secure !== null && (! is_bool($secure)),
            InvalidDriverConfig::invalid('A boolean value must be provided for the secure option for the CUPS driver.'),
        );

        $port = data_get($config, 'port');
        throw_if(
            $port !== null && blank($port),
            InvalidDriverConfig::invalid('A port must be provided for the CUPS driver.'),
        );

        throw_if(
            $port !== null &&
            ((! is_int($port)) || $port < 1),
            InvalidDriverConfig::invalid('A valid port number was not provided for the CUPS driver.'),
        );
    }

    protected function validateNetworkConfig(array $config): void
    {
        $ip = data_get($config, 'ip');
        throw_if(
            blank($ip),
            InvalidDriverConfig::invalid('An IP address is required for the Network driver.'),
        );

        throw_if(
            ! filter_var($ip, FILTER_VALIDATE_IP),
            InvalidDriverConfig::invalid('A valid IP address must be provided for the Network driver.'),
        );

        $port = data_get($config, 'port');
        throw_if(
            $port !== null && ((! is_int($port)) || $port < 1 || $port > 65535),
            InvalidDriverConfig::invalid('A valid port number (1-65535) must be provided for the Network driver.'),
        );
    }

    protected function validateUsbConfig(array $config): void
    {
        $device = data_get($config, 'device');
        
        // For USB, we can either have a specific device path or use auto-detection
        if ($device !== null) {
            throw_if(
                blank($device),
                InvalidDriverConfig::invalid('A device path is required for the USB driver when specified.'),
            );
        }

        $vendor_id = data_get($config, 'vendor_id');
        $product_id = data_get($config, 'product_id');
        
        // If vendor_id or product_id is provided, both must be provided
        if ($vendor_id !== null || $product_id !== null) {
            throw_if(
                blank($vendor_id) || blank($product_id),
                InvalidDriverConfig::invalid('Both vendor_id and product_id must be provided for USB device identification.'),
            );
        }
    }

    protected function validateRawConfig(array $config): void
    {
        $connection_type = data_get($config, 'connection_type');
        throw_if(
            blank($connection_type),
            InvalidDriverConfig::invalid('A connection type (network, usb, parallel, serial) is required for the Raw driver.'),
        );

        throw_if(
            ! in_array($connection_type, ['network', 'usb', 'parallel', 'serial']),
            InvalidDriverConfig::invalid('Connection type must be one of: network, usb, parallel, serial.'),
        );

        // Validate based on connection type
        if ($connection_type === 'network') {
            $this->validateNetworkConfig($config);
        } elseif ($connection_type === 'usb') {
            $this->validateUsbConfig($config);
        } elseif ($connection_type === 'serial') {
            $port = data_get($config, 'serial_port');
            throw_if(
                blank($port),
                InvalidDriverConfig::invalid('A serial port is required for serial connections.'),
            );
        } elseif ($connection_type === 'parallel') {
            $port = data_get($config, 'parallel_port');
            throw_if(
                blank($port),
                InvalidDriverConfig::invalid('A parallel port is required for parallel connections.'),
            );
        }
    }
}
