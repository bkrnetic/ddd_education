<?php

declare(strict_types=1);

namespace Common\Infrastructure\Http\Controller;

use Common\Application\Validation\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class BaseController extends AbstractController
{
    protected TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /** @return array<string|array|null> */
    protected function extractFilter(Request $request): array
    {
        $filterQueryParam = $request->get('filter');
        $orderByQueryParams = $request->get('orderBy');

        $filter = \is_string($filterQueryParam) ? $filterQueryParam : null;
        $orderBy = \is_array($orderByQueryParams) ? $orderByQueryParams : null;

        return [$filter, $orderBy];
    }

    protected function formatValidationError(Result $validationResult): JsonResponse
    {
        return new JsonResponse([
            'error' => $this->translator->trans('There are some validation errors'),
            'validations' => $validationResult->getErrorMap(),
        ], 422);
    }

    /** @return array<string, mixed> */
    public function extractJSONPayload(Request $request): array
    {
        $content = $request->getContent();
        if (\is_resource($content)) {
            throw new BadRequestHttpException('Unexpected request contents received');
        }

        return json_decode((string) $request->getContent(), true, 512, JSON_THROW_ON_ERROR);
    }
}
