<?php
declare(strict_types=1);

namespace Tale\Test\DevTool;

use Tale\DevTool\Application;

class WindowsApplicationTest extends Application
{
    public function isWindows(): bool
    {
        return true;
    }

    public function getPhpcsPath(): string
    {
        return $this->getShellCommandPath('vendor/bin/phpcs');
    }
}
