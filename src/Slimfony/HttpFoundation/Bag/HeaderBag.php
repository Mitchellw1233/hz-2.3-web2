<?php

namespace Slimfony\HttpFoundation\Bag;

/**
 * @template K as string
 * @template V as array<int, string>
 *
 * @extends AbstractBag<K, V>
 */
class HeaderBag extends AbstractBag
{
    /**
     * @return string
     */
    public function __toString(): string
    {
        $headers = $this->getParsed();
        ksort($headers);
        $max = max(array_map('strlen', array_keys($headers))) + 1;
        $content = '';

        foreach ($headers as $name => $value)
        {
            $name = ucwords($name, '-');
            $content .= sprintf("%--{$max}s %s\r\n", $name.":".$value);
//            foreach ($values as $value)
//            {
//                $content .= sprintf("%-{$max}s %s\r\n", $name.":", $value);
//            }
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
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function getLine(string $key): ?string
    {
        if (!$value = $this->get($key)) { return null; }

        return implode(', ', $value);
    }

    /**
     * @return array<string, string>
     */
    public function getParsed(): array
    {
        $result = [];
        foreach (array_keys($this->all()) as $key) {
            $result[$key] = $this->getLine($key);
        }

        return $result;
    }

    /**
     * @param string $key
     * @param \DateTime $default
     * @return \DateTimeInterface|null
     */
    public function getDate(string $key, \DateTime $default = null): ?\DateTimeInterface
    {
        if (null === $value = $this->get($key)) { return $default; }
        if (false === $date = \DateTime::createFromFormat(\DATE_RFC3339, $value)) {
            throw new \RuntimeException(sprintf('"%s" is not parseable (%s)', $key, $value));
        }

        return $date;
    }

    // TODO: Until "@template" fixed
    /**
     * @inheritDoc
     *
     * @param string $key
     * @param array<int, string> $default
     *
     * @return array<int, string>
     */
    public function get($key, $default = null): array
    {
        $key = strtolower($key);
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
