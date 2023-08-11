<?php

declare(strict_types=1);

namespace Me\Plugin;

use Dhii\Package\Version\VersionInterface;
use WpOop\WordPress\Plugin\PluginInterface;

class Plugin implements PluginInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var VersionInterface
     */
    protected $version;

    /**
     * @var string
     */
    protected $baseDir;

    /**
     * @var string
     */
    protected $baseName;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $textDomain;

    /**
     * @var VersionInterface
     */
    protected $minPhpVersion;

    /**
     * @var VersionInterface
     */
    protected $minWpVersion;

    public function __construct(
        string $name,
        VersionInterface $version,
        string $baseDir,
        string $baseName,
        string $title,
        string $description,
        string $textDomain,
        VersionInterface $minPhpVersion,
        VersionInterface $minWpVersion
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->version = $version;
        $this->baseDir = $baseDir;
        $this->baseName = $baseName;
        $this->title = $title;
        $this->textDomain = $textDomain;
        $this->minPhpVersion = $minPhpVersion;
        $this->minWpVersion = $minWpVersion;
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @inheritDoc
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @inheritDoc
     */
    public function getVersion(): VersionInterface
    {
        return $this->version;
    }

    /**
     * @inheritDoc
     */
    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    /**
     * @inheritDoc
     */
    public function getBaseName(): string
    {
        return $this->baseName;
    }

    /**
     * @inheritDoc
     */
    public function getTextDomain(): string
    {
        return $this->textDomain;
    }

    /**
     * @inheritDoc
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @inheritDoc
     */
    public function getMinPhpVersion(): VersionInterface
    {
        return $this->minPhpVersion;
    }

    /**
     * @inheritDoc
     */
    public function getMinWpVersion(): VersionInterface
    {
        return $this->minWpVersion;
    }
}
