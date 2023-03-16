<?php

namespace Slimfony\HttpFoundation\Bag;

use Slimfony\HttpFoundation\Utils\HeaderUtils;

/**
 * @template K as string
 * @template V as list<string|null>
 *
 * @extends AbstractBag<K, V>
 */
class HeaderBag extends AbstractBag
{
    /**
     * @var array<string, list<string|null>>
     */
    protected array $cacheControl;

    /**
     * @param array<string, list<string|null>> $data
     */
    public function __construct(array $data = [])
    {
        $this->cacheControl = [];
        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        if (!$headers = $this->all()) { return ''; }

        ksort($headers);
        $max = max(array_map('strlen', array_keys($headers))) + 1;
        $content = '';

        foreach ($headers as $name => $values)
        {
            $name = ucwords($name, '-');
            foreach ($values as $value)
            {
                $content .= sprintf("%-{$max}s %s\r\n", $name.":", $value);
            }
        }

        return $content;
    }

    /**
     * @param string $key
     * @param list<string|null> $value
     *
     * @return void
     */
    public function set($key, $value): void
    {
        $key = strtolower($key);

        if ('cache-control' === $key) {
            $this->cacheControl = $this->parseCacheControl(implode(', ', $this->data[$key]));
            return;
        }

        if (!\is_array($value)) {
            $value = [$value];
        }

        parent::set($key, $value);
    }

    /**
     * Removes a header.
     *
     * @param string $key
     */
    public function remove($key): void
    {
        $key = strtolower($key);

        unset($this->data[$key]);
        if ('cache-control' === $key) {
            $this->cacheControl = [];
        }
    }

    /**
     * @param string $key
     * @param \DateTime|null $default
     * @return \DateTimeInterface|null
     */
    public function getDate(string $key, \DateTime $default = null): ?\DateTimeInterface
    {
        if (null === $value = $this->get($key)) { return $default; }
        if (false === $date = \DateTime::createFromFormat(\DATE_RFC822, $value)) {
            throw new \RuntimeException(sprintf('The "%s" HTTP header is not parseable (%s).', $key, $value));
        }

        return $date;
    }

    /**
     * @param string $key
     * @param bool|string $value
     * @return void
     */
    public function addCacheControlDirective(string $key, bool|string $value = true): void
    {
        $this->cacheControl[$key] = $value;
        $this->set('Cache-Control', $this->getCacheControlHeader());
    }

    /**
     * @param string $key
     * @return void
     */
    public function removeCacheControlDirective(string $key): void
    {
        unset($this->cacheControl[$key]);
        $this->set('Cache-Control', $this->getCacheControlHeader());
    }

    /**
     * @param string $key
     * @return bool|string|null
     */
    public function getCacheControlDirective(string $key): bool|string|null
    {
        return $this->cacheControl[$key] ?? null;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function hasCacheControlDirective(string $key): bool
    {
        return \array_key_exists($key, $this->cacheControl);
    }

    /**
     * @return string
     */
    protected function getCacheControlHeader(): string
    {
        ksort($this->cacheControl);
        return HeaderUtils::toString($this->cacheControl, ',');
    }

    /**
     * @param string $header
     * @return array
     */
    protected function parseCacheControl(string $header): array
    {
        $parts = HeaderUtils::split($header, ',=');
        return HeaderUtils::combine($parts);
    }

    // TODO: Until "@template" fixed
    /**
     * @inheritDoc
     *
     * @param string $key
     * @param list<string|null>|null $default
     *
     * @return list<string|null>|null
     */
    public function get($key, $default = null)
    {
        return parent::get($key, $default);
    }

    /**
     * @inheritDoc
     *
     * @param string|null $key
     *
     * @return list<string|null>
     */
    public function all($key = null): array
    {
        return parent::all($key);
    }

    /**
     * @inheritDoc
     *
     * @return string[]
     */
    public function keys(): array
    {
        return parent::keys();
    }

    /**
     * @inheritDoc
     *
     * @param string $key
     */
    public function has($key): bool
    {
        return parent::has($key);
    }

    /**
     * @inheritDoc
     *
     * @param list<string|null> $data
     */
    public function add(array $data = []): void
    {
        parent::add($data);
    }

    /**
     * @inheritDoc
     *
     * @param list<string|null> $data
     */
    public function replace(array $data = []): void
    {
        parent::replace($data);
    }

    /**
     * @inheritDoc
     *
     * @param string $key
     * @param list<string|null> $value
     */
    public function contains($key, $value): bool
    {
        return parent::contains($key, $value);
    }
}
