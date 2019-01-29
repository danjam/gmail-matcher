<?php declare(strict_types=1);

namespace danjam\GmailMatcher\tests;

use danjam\GmailMatcher\GmailMatcher;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * Class GmailMatcherTest
 *
 * @package danjam\GmailMatcher\tests
 *
 * @SuppressWarnings(TooManyPublicMethods)
 */
class GmailMatcherTest extends TestCase
{
    /**
     * @return array
     */
    public function normalizeDataProvider(): array
    {
        return [
            'Removes dots' => ['f.o.o@gmail.com', 'foo@gmail.com'],
            'Lowercase'    => ['FOO@GMAIL.COM', 'foo@gmail.com'],
            'Domain'       => ['foo@googlemail.com', 'foo@gmail.com'],
        ];
    }

    /**
     * @dataProvider normalizeDataProvider
     *
     * @param string $email
     * @param string $expected
     */
    public function testNormalize(string $email, string $expected): void
    {
        self::assertSame(
            $expected,
            (new GmailMatcher())->normalize($email)
        );
    }

    /**
     * @depends testNormalize
     */
    public function testInstantiateWithNormalizedDomain(): void
    {
        self::assertSame(
            'foo@googlemail.com',
            (new GmailMatcher('googlemail.com'))->normalize('foo@googlemail.com')
        );
    }

    /**
     * @depends testNormalize
     */
    public function testInstantiateWithInvalidNormalizedDomainThrowsException(): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage(GmailMatcher::ERROR_INVALID_NORMALIZED_DOMAIN);

        new GmailMatcher('INVALID');
    }

    /**
     * @return array
     */
    public function matchDataProvider(): array
    {
        return [
            'Valid match'                       => ['foo@gmail.com', 'foo@gmail.com', true],
            'Valid match with different domain' => ['foo@gmail.com', 'foo@googlemail.com', true],
            'Should not match'                  => ['foo@gmail.com', 'bar@gmail.com', false],
        ];
    }

    /**
     * @dataProvider matchDataProvider
     *
     * @param string $emailOne
     * @param string $emailTwo
     * @param mixed  $expected
     */
    public function testMatch(string $emailOne, string $emailTwo, $expected): void
    {
        self::assertSame(
            $expected,
            (new GmailMatcher())->match($emailOne, $emailTwo)
        );
    }

    /**
     * @return array
     */
    public function matchHandlesVariadicParametersDataProvider(): array
    {
        return [
            'Two valid'     => [['foo@gmail.com', 'foo@gmail.com'], true],
            'Three valid'   => [['foo@gmail.com', 'foo@gmail.com', 'foo@gmail.com'], true],
            'Two invalid'   => [['foo@gmail.com', 'bar@gmail.com'], false],
            'Three invalid' => [['foo@gmail.com', 'foo@gmail.com', 'bar@gmail.com'], false],
        ];
    }

    /**
     * @dataProvider matchHandlesVariadicParametersDataProvider
     *
     * @param array $parameters
     * @param mixed $expected
     */
    public function testMatchHandlesVariadicParameters(array $parameters, $expected): void
    {
        self::assertSame(
            $expected,
            (new GmailMatcher())->match(...$parameters)
        );
    }

    /**
     * @return array
     */
    public function matchThrowsExceptionDataProvider(): array
    {
        return [
            'Single email'      => [['foo@gmail.com'], GmailMatcher::ERROR_INVALID_EMAILS_COUNT],
            'Empty emails'      => [['', ''], GmailMatcher::ERROR_INVALID_EMAILS_EMPTY],
            'Invalid email'     => [['INVALID', 'INVALID'], GmailMatcher::ERROR_INVALID_EMAIL],
            'non Gmail address' => [['foo@bar.com', 'foo@bar.com'], GmailMatcher::ERROR_INVALID_GMAIL],
        ];
    }

    /**
     * @dataProvider matchThrowsExceptionDataProvider
     *
     * @param array  $emails
     * @param string $exceptionMessage
     */
    public function testMatchThrowsException(array $emails, string $exceptionMessage): void
    {
        self::expectException(InvalidArgumentException::class);
        self::expectExceptionMessage($exceptionMessage);

        (new Gmailmatcher())->match(...$emails);
    }
}
