<?php

namespace App\Models;

// Put this inside an `if (false) {}` or similar condition to ensure it never
// actually loads in production. The IDE and static analyzers will still see it.
if (!class_exists(User::class)) {
    // This dummy class is only for IDEs / static analysis.
    class User
    {
        public function updateQuietly(array $attributes = [], array $options = []): bool
        {
            return true;
        }
    }
}
