<?php declare(strict_types=1);

namespace danjam\GmailMatcher;

use danjam\GmailMatcher\Exception\InvalidEmailException;
use InvalidArgumentException;

/**
 * Class GmailMatcher
 */
class GmailMatcher
{
    /** @var string[] */
    private const NORMALIZED_DOMAINS = [
        'gmail.com',
        'googlemail.com',
    ];

    /** @var string */
    public const ERROR_INVALID_NORMALIZED_DOMAIN = '$normalizedDomain must be one of ';

    /** @var string */
    public const ERROR_INVALID_EMAIL =  ' is not a valid address';

    /** @var string  */
    public const ERROR_INVALID_EMAILS_COUNT = 'You must provide at last two email addresses to compare';

    /** @var string */
    private $normalizedDomain = self::NORMALIZED_DOMAINS[0];

    /**
     * @param string|null $normalizedDomain
     */
    public function __construct(?string $normalizedDomain = null)
    {
        if (!is_null($normalizedDomain)) {
            $this->setNormalizedDomain($normalizedDomain);
        }
    }

    /**
     * @param string $normalizedDomain
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    private function setNormalizedDomain(string $normalizedDomain): void
    {
        if (!in_array($normalizedDomain, self::NORMALIZED_DOMAINS, true)) {
            throw new InvalidArgumentException(
                self::ERROR_INVALID_NORMALIZED_DOMAIN . implode(', ', self::NORMALIZED_DOMAINS)
            );
        }

        $this->normalizedDomain = $normalizedDomain;
    }

    /**
     * @return string
     */
    private function normalizedDomainsRegexString(): string
    {
        return '/@(' . str_replace('.', '\.', implode('|', self::NORMALIZED_DOMAINS)) . ')$/i';
    }

    /**
     * @param string ...$emails
     *
     * @return void
     *
     * @throws InvalidEmailException
     */
    private function validate(string ...$emails): void
    {
        $matches = preg_grep($this->normalizedDomainsRegexString(), $emails);

        if ($matches !== $emails) {
            throw new InvalidEmailException(
                array_values(array_diff($emails, $matches))[0] . self::ERROR_INVALID_EMAIL
            );
        }
    }

    /**
     * Normalizes an email to lowercase with dots removed, and uses default domain
     *
     * @param string $email
     *
     * @see GmailMatcher::setNormalizedDomain()
     *
     * @return string
     */
    public function normalize(string $email): string
    {
        return str_replace(
            '.',
            '',
            explode('@', strtolower($email))[0]
        ) . '@' . $this->normalizedDomain;
    }

    /**
     * @param string ...$emails
     *
     * @return bool
     *
     * @throws InvalidArgumentException
     */
    public function match(string ...$emails): bool
    {
        if (count($emails) === 1) {
            throw new InvalidArgumentException(self::ERROR_INVALID_EMAILS_COUNT);
        }

        $this->validate(...$emails);

        return count(array_unique(
            array_map([$this, 'normalize'], $emails)
        )) === 1;
    }
}
