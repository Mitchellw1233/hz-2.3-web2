<?php

namespace Slimfony\HttpFoundation\Bag;

// TODO: HeaderUtils
// TODO: Checken of alle abstract functies correct zijn (hier ben ik niet doorheen gekomen)

/**
 * @template-extends AbstractBag<string, list<string|null>
 */
class HeaderBag extends AbstractBag
{
    /**
     * @var array<string, list<string|null>>
     */
    protected array $cacheControl;

    /**
     * @param array $headers
     */
    public function __construct(array $headers = [])
    {
        parent::__construct($headers);
        $this->cacheControl = [];
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
    public function addCacheControlDirective(string $key, bool|string $value = true)
    {
        $this->cacheControl[$key] = $value;
        $this->set('Cache-Control', $this->getCacheControlHeader());
    }

    /**
     * @param string $key
     * @return void
     */
    public function removeCacheControlDirective(string $key)
    {
        unset($this->cacheControl[$key]);
        $this->set('Cache-Control', $this->getCacheControlHeader());
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
}