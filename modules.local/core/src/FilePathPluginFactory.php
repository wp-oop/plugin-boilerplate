<?php
declare(strict_types=1);

namespace Me\Plugin\Core;

use Dhii\Package\Version\StringVersionFactoryInterface;
use Dhii\Package\Version\VersionInterface;
use DomainException;
use Exception;
use RuntimeException;
use UnexpectedValueException;
use WpOop\WordPress\Plugin\FilePathPluginFactoryInterface;
use WpOop\WordPress\Plugin\PluginInterface;

/**
 * Creates a plugin from plugin basename.
 */
class FilePathPluginFactory implements FilePathPluginFactoryInterface
{
    /**
     * @var StringVersionFactoryInterface
     */
    protected $versionFactory;

    /**
     * FilePathPluginFactory constructor.
     *
     * @param StringVersionFactoryInterface $versionFactory
     */
    public function __construct(StringVersionFactoryInterface $versionFactory)
    {
        $this->versionFactory = $versionFactory;
    }

    /**
     * @inheritDoc
     */
    public function createPluginFromFilePath(string $filePath): PluginInterface
    {
        if (!is_readable($filePath)) {
            throw new RuntimeException(sprintf('Plugin file "%1$s" does not exist or is not readable', $filePath));
        }

        $pluginData = get_plugin_data($filePath);
        if (empty($pluginData)) {
            throw new UnexpectedValueException(sprintf('Plugin file "%1$s" does not have a valid plugin header', $filePath));
        }

        $pluginData = array_merge([
            'Name' => '',
            'Version' => '0.1.0-alpha1+default',
            'Title' => '',
            'Description' => '',
            'TextDomain' => '',
            'RequiresWP' => '5.0',
            'RequiresPHP' => '5.6',
        ], $pluginData);

        $baseDir = dirname($filePath);
        $baseName = plugin_basename($filePath);
        $slug = $this->getPluginSlug($baseName);
        $textDomain = !empty($pluginData['TextDomain']) ? $pluginData['TextDomain'] : $slug;

        return new Plugin(
            $pluginData['Name'],
            $this->createVersion($pluginData['Version']),
            $baseDir,
            $baseName,
            $pluginData['Title'],
            $pluginData['Description'],
            $textDomain,
            $this->createVersion($pluginData['RequiresPHP']),
            $this->createVersion($pluginData['RequiresWP'])
        );
    }

    /**
     * Creates a new version from a version string.
     *
     * @param string $versionString The SemVer-compliant version string.
     *
     * @return VersionInterface The new version.
     *
     * @throws DomainException If version string is malformed.
     */
    protected function createVersion(string $versionString): VersionInterface
    {
        return $this->versionFactory->createVersionFromString($versionString);
    }

    /**
     * Retrieves a plugin slug from its basename.
     *
     * @param string $baseName The plugin's basename.
     *
     * @return string The plugin's slug.
     *
     * @throws UnexpectedValueException If cannot deduce slug from basename that includes a directory.
     * @throws Exception If cannot determine slug.
     */
    protected function getPluginSlug(string $baseName): string
    {
        $directorySeparator = '/';

        // If plugin is in a directory, use directory name
        if (strstr($baseName, $directorySeparator) !== false) {
            $parts = explode($directorySeparator, $baseName);

            // This isn't actually ever going to happen, because it only happens when the separator is an empty string
            if ($parts === false) {
                throw new UnexpectedValueException(sprintf('Could not deduce plugin slug from basename "%1$s"', $baseName));
            }

            return $parts[0];
        }

        // If plugin is not in a directory, return plugin file basename
        return basename($baseName);
    }
}
