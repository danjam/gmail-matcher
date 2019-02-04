<?php declare(strict_types=1);

namespace danjam\GmailMatcher;

use danjam\GmailMatcher\Exception\InvalidEmailException;
use InvalidArgumentException;

/**
 * Class GmailMatcher
 *
 * @todo document class
 */
class GmailMatcher
{
    /** @var string[] */
    private const VALID_NORMALIZED_DOMAINS = [
        'gmail.com',
        'googlemail.com',
    ];

    /** @var string */
    public const ERROR_INVALID_NORMALIZED_DOMAIN = '$normalizedDomain must be one of ';

    /** @var string */
    public const ERROR_INVALID_EMAIL =  ' is not a valid email address';

    /** @var string */
    public const ERROR_INVALID_GMAIL =  ' is not a valid Gmail address';

    /** @var string  */
    public const ERROR_INVALID_EMAILS_COUNT = 'You must provide at last two email addresses to compare';

    /** @var string */
    public const ERROR_INVALID_EMAILS_EMPTY = 'One or more email addresses are empty';

    /** @var string */
    private $normalizedDomain = self::VALID_NORMALIZED_DOMAINS[0];

    /**
     * GmailMatcher constructor
     *
     * @param string|null $normalizedDomain The domain to use when normalizing emails. Defaults to "gmail.com".
     */
    public function __construct(?string $normalizedDomain = null)
    {
        if (!is_null($normalizedDomain)) {
            $this->setNormalizedDomain($normalizedDomain);
        }
    }

    /**
     * Sets the normalized domain for use with email normalization.
     *
     * @param string $normalizedDomain
     *
     * @see normalize()
     *
     * @throws InvalidArgumentException
     */
    private function setNormalizedDomain(string $normalizedDomain): void
    {
        if (!in_array($normalizedDomain, self::VALID_NORMALIZED_DOMAINS, true)) {
            throw new InvalidArgumentException(
                self::ERROR_INVALID_NORMALIZED_DOMAIN . implode(', ', self::VALID_NORMALIZED_DOMAINS)
            );
        }

        $this->normalizedDomain = $normalizedDomain;
    }

    /**
     * Generates the regex string used for Gmail address validation
     *
     * @see validate()
     *
     * @return string
     */
    private function normalizedDomainsRegexString(): string
    {
        return '/@(' . str_replace('.', '\.', implode('|', self::VALID_NORMALIZED_DOMAINS)) . ')$/i';
    }

    /**
     * Naively Validates an email address. Checks for empty address / invalid address / non Gmail address.
     *
     * @param string ...$emails
     *
     * @return void
     *
     * @throws InvalidEmailException
     * @throws InvalidArgumentException
     */
    private function validate(string ...$emails): void
    {
        if (array_filter($emails) !== $emails) {
            throw new InvalidArgumentException(self::ERROR_INVALID_EMAILS_EMPTY);
        }

        $validatedEmails = filter_var_array($emails, FILTER_VALIDATE_EMAIL);

        // invalid email address
        if ($validatedEmails !== $emails) {
            throw new InvalidEmailException(
                array_values(array_diff($emails, $validatedEmails))[0] . self::ERROR_INVALID_EMAIL
            );
        }

        $matches = preg_grep($this->normalizedDomainsRegexString(), $emails);

        // invalid gmail address
        if ($matches !== $emails) {
            throw new InvalidEmailException(
                array_values(array_diff($emails, $matches))[0] . self::ERROR_INVALID_GMAIL
            );
        }
    }

    /**
     * Normalizes an email to lowercase with dots removed, and uses default domain
     *
     * @param string $email
     *
     * @see setNormalizedDomain()
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
     * Checks that all supplied Gmail addresses match each other
     *
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

    /**
     * Checks to see if an email address is a Gmail address
     *
     * @param string $email
     *
     * @return bool
     */
    public function isGmailAddress(string $email): bool
    {
        return (bool) preg_match($this->normalizedDomainsRegexString(), $email);
    }
}
