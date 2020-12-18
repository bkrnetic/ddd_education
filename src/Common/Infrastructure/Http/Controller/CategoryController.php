<?php

declare(strict_types=1);

namespace Common\Infrastructure\Http\Controller;

use Common\Application\Formatter\CategoryFormatter;
use Common\Application\Validation\CategoryValidator;
use Common\Domain\Exception\NotFoundException;
use Common\Domain\Exception\StorageException;
use Common\Domain\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CategoryController extends BaseController
{
    private CategoryRepository $categoryRepository;
    private CategoryFormatter $formatter;
    private CategoryValidator $validator;

    public function __construct(
        CategoryRepository $categoryRepository,
        CategoryFormatter $formatter,
        CategoryValidator $validator,
        TranslatorInterface $translator
    ) {
        parent::__construct($translator);
        $this->categoryRepository = $categoryRepository;
        $this->formatter = $formatter;
        $this->validator = $validator;
    }

    /**
     * @Route(
     *     "/api/categories",
     *     name="common.categories.list",
     *     methods={"GET"},
     *     priority=10
     * )
     */
    public function list(Request $request): JsonResponse
    {
        $categories = $this->categoryRepository->findAll();

        return new JsonResponse(array_map([$this->formatter, 'format'], $categories), Response::HTTP_OK);
    }

    /**
     * @Route(
     *     "/api/categories/{uuid}",
     *     name="common.categories.fetch",
     *     methods={"GET"},
     *     priority=10,
     *     requirements={"uuid": "[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}"}
     * )
     */
    public function fetch(string $uuid): JsonResponse
    {
        $category = $this->categoryRepository->findById($uuid);

        if (null === $category) {
            throw new NotFoundHttpException($this->translator->trans('Category with ID = %id% not found', ['%id%' => $uuid]));
        }

        return new JsonResponse($this->formatter->format($category), Response::HTTP_OK);
    }

    /**
     * @Route(
     *     "/api/categories",
     *     name="common.categories.create",
     *     methods={"POST"},
     *     priority=10
     * )
     */
    public function create(Request $request): JsonResponse
    {
        $validationResult = $this->validator->validateOnCreate($this->extractJSONPayload($request));
        if ($validationResult->isValid() === false) {
            return $this->formatValidationError($validationResult);
        }
        try {
            $category = $this->categoryRepository->create($this->extractJSONPayload($request));
        } catch (StorageException $e) {
            throw new HttpException($e->getCode(), $e->getMessage());
        }

        return new JsonResponse($this->formatter->format($category), Response::HTTP_OK);
    }

    /**
     * @Route(
     *     "/api/categories/{uuid}",
     *     name="common.categories.update",
     *     methods={"PUT"},
     *     priority=10,
     *     requirements={"uuid": "[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}"}
     * )
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        $category = $this->categoryRepository->findById($uuid);

        if (null === $category) {
            throw new NotFoundHttpException($this->translator->trans('Category with ID = %id% not found', ['%id%' => $uuid]));
        }

        $validationResult = $this->validator->validateOnUpdate($this->extractJSONPayload($request), $category);
        if ($validationResult->isValid() === false) {
            return $this->formatValidationError($validationResult);
        }

        try {
            $category = $this->categoryRepository->update($category, $this->extractJSONPayload($request));
        } catch (StorageException $e) {
            throw new HttpException($e->getCode(), $e->getMessage());
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return new JsonResponse($this->formatter->format($category), Response::HTTP_OK);
    }

    /**
     * @Route(
     *     "/api/categories/{uuid}",
     *     name="common.categories.delete",
     *     methods={"DELETE"},
     *     priority=10,
     *     requirements={"uuid": "[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}"}
     * )
     */
    public function delete(string $uuid): JsonResponse
    {
        $category = $this->categoryRepository->findById($uuid);

        if (null === $category) {
            throw new NotFoundHttpException($this->translator->trans('Category with ID = %id% not found', ['%id%' => $uuid]));
        }

        try {
            $this->categoryRepository->delete($category);
        } catch (StorageException $e) {
            throw new HttpException($e->getCode(), $e->getMessage());
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
