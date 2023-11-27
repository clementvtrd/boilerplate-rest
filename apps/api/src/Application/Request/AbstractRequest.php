<?php

declare(strict_types=1);

namespace App\Request;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractRequest
{
    public function __construct(
        private readonly ValidatorInterface $validator
    ) {
        $this->populate();
        $this->validate();
    }

    protected function validate(): void
    {
        $errors = $this->validator->validate($this);

        $messages = [
            'message' => 'validation_failed',
            'errors' => array_map(fn (ConstraintViolation $violation) => [
                'property' => $violation->getPropertyPath(),
                'value' => $violation->getInvalidValue(),
                'message' => $violation->getMessage(),
            ], iterator_to_array($errors))
        ];

        if (count($messages['errors']) > 0) {
            $response = new JsonResponse($messages, JsonResponse::HTTP_BAD_REQUEST);
            $response->send();

            exit;
        }
    }

    protected function populate(): void
    {
        foreach ($this->getRequest()->toArray() as $property => $value) {
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            }
        }
    }

    private function getRequest(): Request
    {
        return Request::createFromGlobals();
    }
}
