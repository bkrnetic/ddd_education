<?php

namespace UserIdentity\Infrastructure\Http\Controller\Api;

use Common\Infrastructure\Http\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use UserIdentity\Application\Formatter\UserFormatter;
use UserIdentity\Application\Manager\TokenManager;
use UserIdentity\Application\Model\User;
use UserIdentity\Application\Repository\UserRepository;
use UserIdentity\Application\Validation\LoginValidator;

class SecurityController extends BaseController
{
    private LoginValidator $loginValidator;
    private UserFormatter $userFormatter;
    private UserRepository $userRepository;
    private TokenManager $tokenManager;

    public function __construct(
        LoginValidator $loginValidator,
        TokenManager $tokenManager,
        UserFormatter $userFormatter,
        UserRepository $userRepository,
        TranslatorInterface $translator
    ) {
        parent::__construct($translator);
        $this->loginValidator = $loginValidator;
        $this->tokenManager = $tokenManager;
        $this->userFormatter = $userFormatter;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route(
     *     "/api/login", name="userIdentity.api.login",
     *     priority=10
     * )
     *
     * @return JsonResponse
     */
    public function apiLoginAction(Request $request)
    {
        $data = $this->extractJSONPayload($request);

        $validation = $this->loginValidator->validate($data);
        if (!$validation->isValid()) {
            return $this->formatValidationError($validation);
        }

        $email = $data['email'];
        $token = $this->tokenManager->createToken($email);
        /** @var User $user */
        $user = $this->userRepository->getByEmail($email);

        return new JsonResponse([
            'token' => $token,
            'user' => $this->userFormatter->format($user),
        ]);
    }
}
