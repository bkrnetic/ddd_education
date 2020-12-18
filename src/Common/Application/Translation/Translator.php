<?php

declare(strict_types=1);

namespace Common\Application\Translation;

use Common\Application\Exception\TranslationException;
use Common\Application\ValueObject\TranslationTuple;
use Symfony\Contracts\Translation\TranslatorInterface;

class Translator
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Translate message given as string, TranslationTuple or array value. If array is provided, apply recursion.
     *
     * @param mixed $error
     * @param array<mixed, mixed> $parameters
     *
     * @return string|array<string, mixed>
     *
     * @throws TranslationException
     */
    public function translateMessage($error, array $parameters = [], string $domain = null, string $locale = null)
    {
        if (\is_string($error)) {
            return $this->translator->trans($error, $parameters, $domain, $locale);
        }
        if ($error instanceof TranslationTuple) {
            return $this->translator->trans($error->getMessage(), $error->getParams(), $domain, $locale);
        }
        if (!\is_array($error)) {
            throw TranslationException::becauseUnexpectedTypeProvided();
        }

        $translatedErrors = [];
        foreach ($error as $key => $value) {
            $translatedErrors[$key] = $this->translateMessage($value);
        }

        return $translatedErrors;
    }
}
