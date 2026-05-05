<?php

/**
 * MongoDB Stubs for IDE IntelliSense
 * 
 * This file provides stub classes for the MongoDB PHP extension to resolve 
 * "unknown class" warnings in IDEs when the extension is not installed locally.
 * This file is not used at runtime.
 */

namespace MongoDB\Driver {
    class ReadPreference {
        const PRIMARY = 'primary';
        const PRIMARY_PREFERRED = 'primaryPreferred';
        const SECONDARY = 'secondary';
        const SECONDARY_PREFERRED = 'secondaryPreferred';
        const NEAREST = 'nearest';

        public function __construct(string $mode, ?array $tagSets = null, ?array $options = null) {}
    }
}

namespace MongoDB\Driver\Exception {
    class ConnectionException extends \RuntimeException {}
    class AuthenticationException extends \RuntimeException {}
}

namespace {
    // Global scope stubs if needed
}
