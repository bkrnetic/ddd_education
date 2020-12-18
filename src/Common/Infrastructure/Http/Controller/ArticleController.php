<?php

declare(strict_types=1);

namespace Common\Infrastructure\Http\Controller;

use Common\Application\Exception\TranslationException;
use Common\Application\Formatter\ArticleFormatter;
use Common\Application\Validation\ArticleValidator;
use Common\Domain\Exception\JsonException;
use Common\Domain\Exception\NotFoundException;
use Common\Domain\Exception\StorageException;
use Common\Domain\Repository\ArticleRepository;
use ReflectionException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ArticleController extends BaseController
{
    private ArticleRepository $articleRepository;
    private ArticleFormatter $formatter;
    private ArticleValidator $validator;

    public function __construct(
        ArticleRepository $articleRepository,
        ArticleFormatter $formatter,
        TranslatorInterface $translator,
        ArticleValidator $validator
    ) {
        parent::__construct($translator);
        $this->articleRepository = $articleRepository;
        $this->formatter = $formatter;
        $this->validator = $validator;
    }

    /**
     * @Route(
     *     "/api/articles",
     *     name="common.articles.list",
     *     methods={"GET"},
     *     priority=10
     * )
     */
    public function list(Request $request): JsonResponse
    {
        $articles = $this->articleRepository->findAll();

        return new JsonResponse(array_map([$this->formatter, 'format'], $articles), Response::HTTP_OK);
    }

    /**
     * @Route(
     *     "/api/articles/{uuid}",
     *     name="common.articles.fetch",
     *     methods={"GET"},
     *     priority=10,
     *     requirements={"uuid": "[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}"}
     * )
     *
     * @throws ReflectionException
     */
    public function fetch(string $uuid): JsonResponse
    {
        $article = $this->articleRepository->findById($uuid);

        if (null === $article) {
            throw new NotFoundHttpException($this->translator->trans('Article with ID = %id% not found', ['%id%' => $uuid]));
        }

        return new JsonResponse($this->formatter->format($article), Response::HTTP_OK);
    }

    /**
     * @Route(
     *     "/api/articles",
     *     name="common.articles.create",
     *     methods={"POST"},
     *     priority=10
     * )
     */
    public function create(Request $request): JsonResponse
    {
        try {
            $validationResult = $this->validator->validateOnCreate($this->extractJSONPayload($request));
            if ($validationResult->isValid() === false) {
                return $this->formatValidationError($validationResult);
            }

            $article = $this->articleRepository->create($this->extractJSONPayload($request));
        } catch (TranslationException | StorageException $e) {
            throw new HttpException($e->getCode(), $e->getMessage());
        } catch (JsonException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return new JsonResponse($this->formatter->format($article), Response::HTTP_OK);
    }

    /**
     * @Route(
     *     "/api/articles/{uuid}",
     *     name="common.articles.update",
     *     methods={"PUT"},
     *     priority=10,
     *     requirements={"uuid": "[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}"}
     * )
     *
     * @throws ReflectionException
     */
    public function update(Request $request, string $uuid): JsonResponse
    {
        $article = $this->articleRepository->findById($uuid);

        if (null === $article) {
            throw new NotFoundHttpException($this->translator->trans('Article with ID = %id% not found', ['%id%' => $uuid]));
        }

        try {
            $validationResult = $this->validator->validateOnUpdate($this->extractJSONPayload($request), $article);
            if ($validationResult->isValid() === false) {
                return $this->formatValidationError($validationResult);
            }

            $article = $this->articleRepository->update($article, $this->extractJSONPayload($request));
        } catch (TranslationException | StorageException $e) {
            throw new HttpException($e->getCode(), $e->getMessage());
        } catch (JsonException $e) {
            throw new BadRequestHttpException($e->getMessage());
        } catch (NotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }

        return new JsonResponse($this->formatter->format($article), Response::HTTP_OK);
    }

    /**
     * @Route(
     *     "/api/articles/{uuid}",
     *     name="common.articles.delete",
     *     methods={"DELETE"},
     *     priority=10,
     *     requirements={"uuid": "[0-9a-f]{8}(-[0-9a-f]{4}){3}-[0-9a-f]{12}"}
     * )
     *
     * @throws ReflectionException
     */
    public function delete(string $uuid): JsonResponse
    {
        $article = $this->articleRepository->findById($uuid);

        if (null === $article) {
            throw new NotFoundHttpException($this->translator->trans('Article with ID = %id% not found', ['%id%' => $uuid]));
        }

        try {
            $this->articleRepository->delete($article);
        } catch (StorageException $e) {
            throw new HttpException($e->getCode(), $e->getMessage());
        }

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }
}
