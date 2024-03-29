<?php

namespace App\Domain\Users\Service;

use App\Domain\Users\Data\UserData;
use App\Domain\Users\Repository\UserRepository;
use App\LugCE\Definition;

/**
 * Service.
 */
final class UserCreator
{
    private UserRepository $repository;

    private UserValidator $userValidator;

    /**
     * The constructor.
     *
     * @param UserRepository $repository The repository
     * @param UserValidator $userValidator The validator
     */
    public function __construct(
        UserRepository $repository,
        UserValidator $userValidator
    ) {
        $this->repository = $repository;
        $this->userValidator = $userValidator;
    }

    /**
     * Create a new user.
     *
     * @param array<mixed> $data The form data
     *
     * @return int The new user ID
     */
    public function createUser(array $data): int
    {
        // Input validation
        $this->userValidator->validateUser($data);

        // Map form data to user DTO (model)
        $user = new UserData($data);

        // Hash password
        if ($user->password) {
            $user->password = (string)hash('sha512', $user->password);
        }

        if (!$user->locale) {
            $user->locale = 'en_EN';
        }

        if (!$user->user_type) {
            $user->user_type = Definition::REPORTER;
        }

        // Insert user and get new user ID
        return $this->repository->insertUser($user);
    }
}
